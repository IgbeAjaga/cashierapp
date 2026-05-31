<?php

namespace App\Http\Controllers;

use App\Models\AwaitingReport;
use App\Models\InvoiceRegistry;
use App\Models\AwaitingReportHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\BalanceService;
use App\Http\Requests\StoreAwaitingReportRequest;
use App\Http\Requests\UpdateAwaitingReportRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AwaitingReportExport;
use App\Imports\AwaitingReportImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\InvoiceRegistryService;
use Illuminate\Support\Facades\Log;

class AwaitingReportController extends Controller
{
    public function index()
    {
        $query = AwaitingReport::with([

    'recordedBy',

    'editedBy',

    'histories.editor'

])->withCount('histories');

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

                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('customer', 'like', "%{$search}%")
                    ->orWhere('payment_note', 'like', "%{$search}%");
            });
        }

        $awaitingReports = $query
            ->latest()
            ->paginate(
                request('per_page', 15)
            );

        $totals = [
            'total_amount' => $query->sum('total_amount'),
            'cash' => $query->sum('cash'),
            'cheque' => $query->sum('cheque'),
            'pos' => $query->sum('pos'),
            'bank_transfer' => $query->sum('bank_transfer'),
            'balance_to_use' => $query->sum('balance_to_use'),
            'difference' => $query->sum('difference'),
        ];

        return view(
            'awaiting_reports.index',
            compact(
                'awaitingReports',
                'totals'
            )
        );
    }

    public function create()
    {
        $nextInvoice =
            InvoiceRegistryService::nextInvoiceNumber();

        return view(
            'awaiting_reports.create',
            compact('nextInvoice')
        );
    }

        public function store(StoreAwaitingReportRequest $request)
    {
        $validated = $request->validated();

        $invoiceNumber = trim($validated['invoice_number']);

        // Simple duplicate check
        $existing = InvoiceRegistryService::invoiceExists((int) $invoiceNumber);

        if ($existing) {
            return redirect()
                ->route('awaiting-reports.create')
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

            $awaitingReport = AwaitingReport::create($validated);

            InvoiceRegistryService::register(
                invoiceNumber: (int) $invoiceNumber,
                sourceTable:   'awaiting_reports',
                sourceId:      $awaitingReport->id,
                userId:        auth()->id()
            );

            DB::commit();

            return redirect()
                ->route('awaiting-reports.create')
                ->with('success', 'Record saved successfully. Ready for next entry.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('awaiting-reports.create')
                ->withInput()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(
        AwaitingReport $awaitingReport
    ) {
        return view(
            'awaiting_reports.show',
            compact('awaitingReport')
        );
    }

    public function edit(
        AwaitingReport $awaitingReport
    ) {
        return view(
            'awaiting_reports.edit',
            compact('awaitingReport')
        );
    }

        public function update(
        UpdateAwaitingReportRequest $request,
        AwaitingReport $awaitingReport
    ) {

        DB::beginTransaction();

        try {

            $validated = $request->validated();

            /*
            |--------------------------------------------------------------------------
            | Check Duplicate Invoice (if changed)
            |--------------------------------------------------------------------------
            */
            if ($validated['invoice_number'] != $awaitingReport->invoice_number) {
                $existingInvoice = InvoiceRegistry::where('invoice_number', $validated['invoice_number'])
                    ->where(function ($q) use ($awaitingReport) {
                        $q->where('source_table', '!=', 'awaiting_reports')
                          ->orWhere('source_id', '!=', $awaitingReport->id);
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
            $oldValues = $awaitingReport->getOriginal();

            $awaitingReport->update($validated);

            // Save History
            AwaitingReportHistory::create([
                'awaiting_report_id' => $awaitingReport->id,
                'edited_by'          => auth()->id(),
                'old_values'         => $oldValues,
                'new_values'         => $awaitingReport->fresh()->toArray(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update Invoice Registry
            |--------------------------------------------------------------------------
            */
            InvoiceRegistry::where('source_table', 'awaiting_reports')
                ->where('source_id', $awaitingReport->id)
                ->delete();

            InvoiceRegistryService::register(
                invoiceNumber: (int) $validated['invoice_number'],
                sourceTable:   'awaiting_reports',
                sourceId:      $awaitingReport->id,
                userId:        auth()->id()
            );

            DB::commit();

            return redirect()
                ->route('awaiting-reports.index')
                ->with('success', 'Record updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error('Awaiting Report Update Error', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'id'      => $awaitingReport->id ?? null,
            ]);

            return back()
                ->withInput()
                ->with('error', 'An unexpected error occurred while updating the record.');
        }
    }

    public function destroy(
        AwaitingReport $awaitingReport
    ) {
        try{ 

    /*
            |--------------------------------------------------------------------------
            | Delete Registry Entry
            |--------------------------------------------------------------------------
            */

            InvoiceRegistry::query()
                ->where(
                    'source_table',
                    'awaiting_reports'
                )
                ->where(
                    'source_id',
                    $awaitingReport->id
                )
                ->delete();

            /*
            |--------------------------------------------------------------------------
            | Delete Record
            |--------------------------------------------------------------------------
            */

        $awaitingReport->delete();

        DB::commit();

        return redirect()
            ->route('awaiting-reports.index')
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
                    'awaiting_report_id' => $awaitingReport->id,
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
        $query = AwaitingReport::with([
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
                new \App\Exports\AwaitingReportExport($records),   // ← Corrected (singular)
                'awaiting-reports-' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
                'awaiting_reports.export_pdf',
                compact('records')
            )->setPaper('a4', 'landscape');

            return $pdf->download(
                'awaiting-reports-' . now()->format('Y-m-d') . '.pdf'
            );
        }

        abort(404, 'Invalid export format');
    }

        // IMPORT FORM
    public function importForm()
    {
        return view('awaiting_reports.import');
    }

    // HANDLE IMPORT
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $import = new \App\Imports\AwaitingReportImport();
            Excel::import($import, $request->file('file'));

            $summary = "{$import->imported} record(s) imported successfully.";

            if ($import->skipped > 0) {
                $summary .= " {$import->skipped} row(s) skipped.";
            }

            if (!empty($import->errors)) {
                session()->flash('import_errors', $import->errors);
            }

            return redirect()
                ->route('awaiting-reports.index')
                ->with('success', $summary);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = collect($e->failures())->map(fn($f) => "Row {$f->row()}: " . implode(', ', $f->errors()))->toArray();
            return back()
                ->with('error', 'Import failed due to validation errors.')
                ->with('import_errors', $failures);
        } catch (\Throwable $e) {
            Log::error('Awaiting Report import failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // DOWNLOAD TEMPLATE
    public function importTemplate()
    {
        $headers = ['invoice_number', 'customer', 'payment_note', 'total_amount', 'cash', 'cheque', 'pos', 'bank_transfer', 'return_inward', 'balance_to_use', 'date', 'shift'];
        $example = ['394713', 'John Doe Pharmacy', 'Cash payment', '1500000', '1000000', '0', '500000', '0', '0', '0', '2026-05-29', 'morning'];

        $csv = implode(',', $headers) . "\n" . implode(',', $example) . "\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="awaiting-reports-import-template.csv"',
        ]);
    }
}