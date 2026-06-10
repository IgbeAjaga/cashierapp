<?php

namespace App\Imports;

use App\Models\InvoiceReport;
use App\Services\BalanceService;
use App\Services\InvoiceRegistryService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class InvoiceReportImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $imported = 0;
    public int $skipped = 0;
    public array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $rowIndex => $row) {
            $lineNumber = $rowIndex + 2;

            try {
                $invoiceNumber = trim((string) ($row['invoice_number'] ?? ''));

                if (empty($invoiceNumber)) continue;

                if (InvoiceReport::where('invoice_number', $invoiceNumber)->exists()) {
                    $this->skipped++;
                    $this->errors[] = "Row {$lineNumber}: Invoice {$invoiceNumber} already exists — skipped.";
                    continue;
                }

                $data = [
                    'invoice_number'   => $invoiceNumber,
                    'customer'         => trim((string) ($row['customer'] ?? '')),
                    'payment_note'     => trim((string) ($row['payment_note'] ?? '')),
                    'total_amount'     => (float) str_replace([',', ' '], '', (string) ($row['total_amount'] ?? 0)),
                    'cash'             => (float) str_replace([',', ' '], '', (string) ($row['cash'] ?? 0)),
                    'awaiting_invoice' => (float) str_replace([',', ' '], '', (string) ($row['awaiting_invoice'] ?? 0)),
                    'cheque'           => (float) str_replace([',', ' '], '', (string) ($row['cheque'] ?? 0)),
                    'credit'           => (float) str_replace([',', ' '], '', (string) ($row['credit'] ?? 0)),
                    'pos'              => (float) str_replace([',', ' '], '', (string) ($row['pos'] ?? 0)),
                    'bank_transfer'    => (float) str_replace([',', ' '], '', (string) ($row['bank_transfer'] ?? 0)),
                    'less_credit'    => (float) str_replace([',', ' '], '', (string) ($row['less_credit'] ?? 0)),
                    'balance_to_use'   => (float) str_replace([',', ' '], '', (string) ($row['balance_to_use'] ?? 0)),
                    'date'             => $row['date'] ?? now()->format('Y-m-d'),
                    'shift'            => strtolower(trim((string) ($row['shift'] ?? 'morning'))),
                    'recorded_by'      => Auth::id(),
                ];

                $balance = BalanceService::calculate($data);

                $data['difference']      = $balance['difference'];
                $data['balance_to_use']  = $balance['balance_to_use'];
                $data['is_balanced']     = $balance['is_balanced'];

                DB::transaction(function () use ($data, $invoiceNumber) {
                    $record = InvoiceReport::create($data);

                    InvoiceRegistryService::register(
                        invoiceNumber: (int) $invoiceNumber,
                        sourceTable:   'invoice_reports',
                        sourceId:      $record->id,
                        userId:        Auth::id()
                    );
                });

                $this->imported++;

            } catch (\Throwable $e) {
                $this->skipped++;
                $this->errors[] = "Row {$lineNumber}: {$e->getMessage()}";
            }
        }
    }
}