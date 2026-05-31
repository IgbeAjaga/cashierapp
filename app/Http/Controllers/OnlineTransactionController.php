<?php

namespace App\Http\Controllers;

use App\Models\OnlineTransaction;
use App\Models\InvoiceRegistry;
use App\Models\OnlineTransactionHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\BalanceService;
use App\Http\Requests\StoreOnlineTransactionRequest;
use App\Http\Requests\UpdateOnlineTransactionRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OnlineTransactionExport;
use App\Imports\OnlineTransactionImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\InvoiceRegistryService;
use Illuminate\Support\Facades\Log;

class OnlineTransactionController extends Controller
{
    /**
     * Display listing
     */
    public function index()
    {
        $query = OnlineTransaction::with([

    'recordedBy',

    'editedBy',

    'histories.editor'

])->withCount('histories');

        /* =========================
         | FILTERS
         |========================= */

        if (request('start_date')) {
            $query->whereDate(
                'date',
                '>=',
                request('start_date')
            );
        }

        if (request('end_date')) {
            $query->whereDate(
                'date',
                '<=',
                request('end_date')
            );
        }

        if (request('shift')) {
            $query->where(
                'shift',
                request('shift')
            );
        }

        if (request('search')) {

            $search = request('search');

            $query->where(function ($q) use ($search) {

                $q->where(
                    'invoice_number',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'customer',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'payment_note',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'cheque_details',
                    'like',
                    "%{$search}%"
                );
            });
        }

        /* =========================
         | DATA
         |========================= */

        $onlineTransactions = $query
            ->latest()
            ->paginate(
                request('per_page', 15)
            )
            ->withQueryString();

        /* =========================
         | TOTALS
         |========================= */

        $totals = [
            'total_amount' => $query->sum('total_amount'),
            'cash' => $query->sum('cash'),
            'cheque' => $query->sum('cheque'),
            'pos' => $query->sum('pos'),
            'transfer' => $query->sum('transfer'),
            'return_inward' => $query->sum('return_inward'),
            'less_credit' => $query->sum('less_credit'),
            'balance_to_use' => $query->sum('balance_to_use'),
            'difference' => $query->sum('difference'),
        ];

        return view(
            'online_transactions.index',
            compact(
                'onlineTransactions',
                'totals'
            )
        );
    }

    /**
     * Show create form
     */
    public function create()
    {
        $nextInvoice =
            InvoiceRegistryService::nextInvoiceNumber();

        return view(
            'online_transactions.create',
            compact('nextInvoice')
        );
    }

    /**
     * Store new record
     */
    public function store(StoreOnlineTransactionRequest $request)
    {
        $validated = $request->validated();

        $invoiceNumber = trim($validated['invoice_number']);

        // === DUPLICATE CHECK ===
        $existing = InvoiceRegistryService::invoiceExists((int) $invoiceNumber);

        if ($existing) {
            return redirect()
                ->route('online-transactions.create')
                ->withInput()
                ->with('error', "Invoice number {$invoiceNumber} already exists in {$existing->source_table} table.");
        }

        // If no duplicate, proceed with normal save
        DB::beginTransaction();
        try {
            $balance = BalanceService::calculate($validated);

            $validated['difference']      = $balance['difference'];
            $validated['balance_to_use']  = $balance['balance_to_use'];
            $validated['is_balanced']     = $balance['is_balanced'];
            $validated['recorded_by']     = auth()->id();

            $onlineTransaction = OnlineTransaction::create($validated);

            InvoiceRegistryService::register(
                invoiceNumber: (int) $invoiceNumber,
                sourceTable:   'online_transactions',
                sourceId:      $onlineTransaction->id,
                userId:        auth()->id()
            );

            DB::commit();

            return redirect()
                ->route('online-transactions.create')
                ->with('success', 'Record saved successfully. Ready for next entry.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('online-transactions.create')
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display single record
     */
    public function show(
        OnlineTransaction $onlineTransaction
    ) {

        $onlineTransaction->load([
            'recordedBy',
            'editedBy'
        ]);

        return view(
            'online_transactions.show',
            compact('onlineTransaction')
        );
    }

    /**
     * Show edit form
     */
    public function edit(
        OnlineTransaction $onlineTransaction
    ) {

        return view(
            'online_transactions.edit',
            compact('onlineTransaction')
        );
    }

    /**
     * Update record
     */
       public function update(
        UpdateOnlineTransactionRequest $request,
        OnlineTransaction $onlineTransaction
    ) {

        DB::beginTransaction();

        try {

            $validated = $request->validated();

            /*
            |--------------------------------------------------------------------------
            | Check Duplicate Invoice (if changed)
            |--------------------------------------------------------------------------
            */
            if ($validated['invoice_number'] != $onlineTransaction->invoice_number) {
                $existingInvoice = InvoiceRegistry::where('invoice_number', $validated['invoice_number'])
                    ->where(function ($q) use ($onlineTransaction) {
                        $q->where('source_table', '!=', 'online_transactions')
                          ->orWhere('source_id', '!=', $onlineTransaction->id);
                    })
                    ->first();

                if ($existingInvoice) {
                    return back()
                        ->withInput()
                        ->with('error', "Invoice number already exists in {$existingInvoice->source_table} table.");
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Suspicious Invoice Detection
            |--------------------------------------------------------------------------
            */
            $suspicious = InvoiceRegistryService::detectSuspicious((int) $validated['invoice_number']);

            if ($suspicious['is_suspicious'] && !$request->boolean('force_save')) {
                return back()
                    ->withInput()
                    ->with('warning', $suspicious['reason'])
                    ->with('requires_confirmation', true);
            }

            /*
            |--------------------------------------------------------------------------
            | Recalculate Balance
            |--------------------------------------------------------------------------
            */
            $balance = BalanceService::calculate($validated);

            $validated['difference']      = $balance['difference'];
            $validated['balance_to_use']  = $balance['balance_to_use'];
            $validated['is_balanced']     = $balance['is_balanced'];

            $validated['edited_by']       = auth()->id();
            $validated['last_edited_at']  = now();

            /*
            |--------------------------------------------------------------------------
            | Save History + Update
            |--------------------------------------------------------------------------
            */
            $oldValues = $onlineTransaction->getOriginal();

            $onlineTransaction->update($validated);

            // Save History
            OnlineTransactionHistory::create([
                'online_transaction_id' => $onlineTransaction->id,
                'edited_by'             => auth()->id(),
                'old_values'            => $oldValues,
                'new_values'            => $onlineTransaction->fresh()->toArray(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update Invoice Registry
            |--------------------------------------------------------------------------
            */
            InvoiceRegistry::where('source_table', 'online_transactions')
                ->where('source_id', $onlineTransaction->id)
                ->delete();

            InvoiceRegistryService::register(
                invoiceNumber: (int) $validated['invoice_number'],
                sourceTable:   'online_transactions',
                sourceId:      $onlineTransaction->id,
                userId:        auth()->id()
            );

            DB::commit();

            return redirect()
                ->route('online-transactions.index')
                ->with('success', 'Record updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Online Transaction Update Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'id'      => $onlineTransaction->id,
            ]);

            return back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while updating the record.');
        }
    }

    /**
     * Soft delete record
     */
    public function destroy(
        OnlineTransaction $onlineTransaction
    ) {

        try {
            /*
            |--------------------------------------------------------------------------
            | Delete Registry Entry
            |--------------------------------------------------------------------------
            */

            InvoiceRegistry::query()
                ->where(
                    'source_table',
                    'online_transactions'
                )
                ->where(
                    'source_id',
                    $onlineTransaction->id
                )
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Delete Record
            |--------------------------------------------------------------------------
            */

            $onlineTransaction->delete();

            DB::commit();

            return redirect()
                ->route('online-transactions.index')
                ->with(
                    'success',
                    'Record deleted successfully.'
                );

        } catch (Exception $e) {

            DB::rollBack();

            Log::error(
                'Invoice Report Delete Error',
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'user_id' => auth()->id(),
                    'online_transaction_id' => $onlineTransaction->id,
                ]
            );

            return back()->with(
                'error',
                'Unable to delete record.'
            );
        }
    }

        public function export(Request $request, string $format)
    {
        $query = OnlineTransaction::with([
            'recordedBy',
            'editedBy'
        ]);

        // Filters
        $query->when($request->filled('start_date'), fn($q) =>
            $q->whereDate('date', '>=', $request->start_date)
        );

        $query->when($request->filled('end_date'), fn($q) =>
            $q->whereDate('date', '<=', $request->end_date)
        );

        $query->when($request->filled('shift'), fn($q) =>
            $q->where('shift', $request->shift)
        );

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->search;
            $q->where('invoice_number', 'like', "%{$search}%")
              ->orWhere('customer', 'like', "%{$search}%");
        });

        $records = $query->latest()->get();

        if ($format === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\OnlineTransactionExport($records),
                'online-transactions-' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'online_transactions.export_pdf',
                compact('records')
            )->setPaper('a4', 'landscape');

            return $pdf->download(
                'online-transactions-' . now()->format('Y-m-d') . '.pdf'
            );
        }

        abort(404, 'Invalid export format');
    }

        public function importForm()
    {
        return view('online_transactions.import');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:5120']);

        try {
            $import = new \App\Imports\OnlineTransactionImport();
            Excel::import($import, $request->file('file'));

            $summary = "{$import->imported} record(s) imported successfully.";
            if ($import->skipped > 0) $summary .= " {$import->skipped} row(s) skipped.";

            if (!empty($import->errors)) session()->flash('import_errors', $import->errors);

            return redirect()->route('online-transactions.index')->with('success', $summary);

        } catch (\Throwable $e) {
            Log::error('Online Transaction import failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function importTemplate()
    {
        $headers = ['invoice_number', 'customer', 'payment_note', 'total_amount', 'cash', 'cheque', 'pos', 'transfer', 'return_inward', 'less_credit', 'balance_to_use', 'date', 'shift'];
        $example = ['394713', 'John Doe Pharmacy', 'Online payment', '1500000', '1000000', '0', '500000', '0', '0', '0', '0', '2026-05-29', 'morning'];

        $csv = implode(',', $headers) . "\n" . implode(',', $example) . "\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="online-transactions-import-template.csv"',
        ]);
    }
}