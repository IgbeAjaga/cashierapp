<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InvoiceReport;
use App\Models\InvoiceRegistry;
use App\Models\InvoiceReportHistory;
use App\Services\BalanceService;
use App\Services\InvoiceRegistryService;
use App\Http\Requests\StoreInvoiceReportRequest;
use App\Http\Requests\UpdateInvoiceReportRequest;
use App\Exports\InvoiceReportExport;
use App\Imports\InvoiceReportImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceReportController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->middleware([
            'auth',
            'approved',
        ]);
    }

    /**
     * Display listing
     */
        public function index(Request $request)
    {
        $query = InvoiceReport::with([
            'recordedBy',
            'editedBy',
            'histories.editor'

])->withCount('histories');

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
        $query->when($request->filled('start_date'), fn ($q) =>
            $q->whereDate('date', '>=', $request->start_date)
        );

        $query->when($request->filled('end_date'), fn ($q) =>
            $q->whereDate('date', '<=', $request->end_date)
        );

        $query->when($request->filled('shift'), fn ($q) =>
            $q->where('shift', $request->shift)
        );

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = trim($request->search);
            $q->where(function ($sub) use ($search) {
                $sub->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('customer', 'like', "%{$search}%")
                    ->orWhere('payment_note', 'like', "%{$search}%");
            });
        });

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */
        $invoiceReports = $query
            ->latest('id')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Totals (Simple & Reliable)
        |--------------------------------------------------------------------------
        */
        $totals = [
            'total_amount'     => $query->sum('total_amount'),
            'cash'             => $query->sum('cash'),
            'awaiting_invoice' => $query->sum('awaiting_invoice'),
            'cheque'           => $query->sum('cheque'),
            'credit'           => $query->sum('credit'),
            'pos'              => $query->sum('pos'),
            'bank_transfer'    => $query->sum('bank_transfer'),
            'balance_to_use'   => $query->sum('balance_to_use'),
            'less_credit'    => $query->sum('less_credit'),
            'difference'       => $query->sum('difference'),
            'return_outward'    => $query->sum('return_outward'),
            'invoice_to_invoice'   => $query->sum('invoice_to_invoice'),
            'add_return_inward'    => $query->sum('return_inward'),
        ];

        return view('invoice_reports.index', compact('invoiceReports', 'totals'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        $nextInvoice =
            InvoiceRegistryService::nextInvoiceNumber();

        return view(
            'invoice_reports.create',
            compact('nextInvoice')
        );
    }

    /**
     * Store new record
     */
    public function store(StoreInvoiceReportRequest $request)
    {
        $validated = $request->validated();

        $invoiceNumber = trim($validated['invoice_number']);

        // === DUPLICATE CHECK ===
        $existing = InvoiceRegistryService::invoiceExists((int) $invoiceNumber);

        if ($existing) {
            return redirect()
                ->route('invoice-reports.create')
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

            $invoiceReport = InvoiceReport::create($validated);

            InvoiceRegistryService::register(
                invoiceNumber: (int) $invoiceNumber,
                sourceTable:   'invoice_reports',
                sourceId:      $invoiceReport->id,
                userId:        auth()->id()
            );

            DB::commit();

            return redirect()
                ->route('invoice-reports.create')
                ->with('success', 'Record saved successfully. Ready for next entry.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('invoice-reports.create')
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display single record
     */
    public function show(
        InvoiceReport $invoiceReport
    ) {

        $invoiceReport->load([
            'recordedBy',
            'editedBy',
        ]);

        return view(
            'invoice_reports.show',
            compact('invoiceReport')
        );
    }

    /**
     * Show edit form
     */
    public function edit(
        InvoiceReport $invoiceReport
    ) {

        return view(
            'invoice_reports.edit',
            compact('invoiceReport')
        );
    }

    /**
     * Update record
     */
        public function update(
        UpdateInvoiceReportRequest $request,
        InvoiceReport $invoiceReport
    ) {

        DB::beginTransaction();

        try {

            $validated = $request->validated();

            /*
            |--------------------------------------------------------------------------
            | Check Duplicate Invoice (if changed)
            |--------------------------------------------------------------------------
            */
            if ($validated['invoice_number'] != $invoiceReport->invoice_number) {
                $existingInvoice = InvoiceRegistry::where('invoice_number', $validated['invoice_number'])
                    ->where(function ($q) use ($invoiceReport) {
                        $q->where('source_table', '!=', 'invoice_reports')
                          ->orWhere('source_id', '!=', $invoiceReport->id);
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
            | Suspicious Detection
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
            $oldValues = $invoiceReport->getOriginal();

            $invoiceReport->update($validated);

            // Save History
            InvoiceReportHistory::create([
                'invoice_report_id' => $invoiceReport->id,
                'edited_by'         => auth()->id(),
                'old_values'        => $oldValues,
                'new_values'        => $invoiceReport->fresh()->toArray(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update Invoice Registry
            |--------------------------------------------------------------------------
            */
            InvoiceRegistry::where('source_table', 'invoice_reports')
                ->where('source_id', $invoiceReport->id)
                ->delete();

            InvoiceRegistryService::register(
                invoiceNumber: (int) $validated['invoice_number'],
                sourceTable:   'invoice_reports',
                sourceId:      $invoiceReport->id,
                userId:        auth()->id()
            );

            DB::commit();

            return redirect()
                ->route('invoice-reports.index')
                ->with('success', 'Invoice report updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Invoice Report Update Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'id'      => $invoiceReport->id,
            ]);

            return back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while updating the record.');
        }
    }

    /**
     * Delete record
     */
    public function destroy(
        InvoiceReport $invoiceReport
    ) {

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Delete Registry Entry
            |--------------------------------------------------------------------------
            */

            InvoiceRegistry::query()
                ->where(
                    'source_table',
                    'invoice_reports'
                )
                ->where(
                    'source_id',
                    $invoiceReport->id
                )
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Delete Record
            |--------------------------------------------------------------------------
            */

            $invoiceReport->delete();

            DB::commit();

            return redirect()
                ->route('invoice-reports.index')
                ->with(
                    'success',
                    'Invoice report deleted successfully.'
                );

        } catch (Exception $e) {

            DB::rollBack();

            Log::error(
                'Invoice Report Delete Error',
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'user_id' => auth()->id(),
                    'invoice_report_id' => $invoiceReport->id,
                ]
            );

            return back()->with(
                'error',
                'Unable to delete record.'
            );
        }
    }

    /**
     * Export records
     */
    public function export(
        Request $request,
        string $format
    ) {

        $query = InvoiceReport::query()
            ->with([
                'recordedBy',
                'editedBy',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        $query->when(
            $request->filled('start_date'),
            fn ($q) =>
            $q->whereDate(
                'date',
                '>=',
                $request->start_date
            )
        );

        $query->when(
            $request->filled('end_date'),
            fn ($q) =>
            $q->whereDate(
                'date',
                '<=',
                $request->end_date
            )
        );

        $query->when(
            $request->filled('shift'),
            fn ($q) =>
            $q->where(
                'shift',
                $request->shift
            )
        );

        $query->when(
            $request->filled('search'),
            function ($q) use ($request) {

                $search = trim($request->search);

                $q->where(function ($sub) use ($search) {

                    $sub->where(
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
                        );
                });
            }
        );

        $records = $query
            ->latest('id')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Excel Export
        |--------------------------------------------------------------------------
        */

        if ($format === 'excel') {

            return Excel::download(

                new InvoiceReportExport($records),

                'invoice-reports-' .
                now()->format('Y-m-d-His') .
                '.xlsx'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PDF Export
        |--------------------------------------------------------------------------
        */

        if ($format === 'pdf') {

            $pdf = Pdf::loadView(
                'invoice_reports.export_pdf',
                compact('records')
            )
                ->setPaper(
                    'a4',
                    'landscape'
                );

            return $pdf->download(
                'invoice-reports-' .
                now()->format('Y-m-d-His') .
                '.pdf'
            );
        }

        abort(
            404,
            'Invalid export format.'
        );
    }

        public function importForm()
    {
        return view('invoice_reports.import');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:5120']);

        try {
            $import = new \App\Imports\InvoiceReportImport();
            Excel::import($import, $request->file('file'));

            $summary = "{$import->imported} record(s) imported successfully.";
            if ($import->skipped > 0) $summary .= " {$import->skipped} row(s) skipped.";

            if (!empty($import->errors)) session()->flash('import_errors', $import->errors);

            return redirect()->route('invoice-reports.index')->with('success', $summary);

        } catch (\Throwable $e) {
            Log::error('Invoice Report import failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function importTemplate()
    {
        $headers = ['invoice_number', 'customer', 'payment_note', 'total_amount', 'cash', 'awaiting_invoice', 'cheque', 'credit', 'pos', 'bank_transfer', 'balance_to_use', 'date', 'shift'];
        $example = ['394713', 'John Doe Pharmacy', 'Cash payment', '1500000', '1000000', '0', '0', '0', '500000', '0', '0', '2026-05-29', 'morning'];

        $csv = implode(',', $headers) . "\n" . implode(',', $example) . "\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="invoice-reports-import-template.csv"',
        ]);
    }
}