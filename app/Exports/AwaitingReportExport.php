<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AwaitingReportExport implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
    WithStyles,
    WithEvents
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        $rows = collect();

        foreach ($this->records as $record) {
            $rows->push([
                $record->invoice_number ?? '',
                $record->customer ?? '',
                optional($record->date)->format('d M Y'),
                ucfirst($record->shift ?? ''),

                (float) ($record->total_amount ?? 0),
                (float) ($record->cash ?? 0),
                (float) ($record->cheque ?? 0),
                (float) ($record->pos ?? 0),
                (float) ($record->bank_transfer ?? 0),
                (float) ($record->return_inward ?? 0),
                (float) ($record->balance_to_use ?? 0),
                (float) ($record->difference ?? 0),
            ]);
        }

        // Total Row
        $rows->push([
            '',
            'TOTAL',
            '',
            '',

            $this->records->sum('total_amount'),
            $this->records->sum('cash'),
            $this->records->sum('cheque'),
            $this->records->sum('pos'),
            $this->records->sum('bank_transfer'),
            $this->records->sum('return_inward'),
            $this->records->sum('balance_to_use'),
            $this->records->sum('difference'),
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Invoice No',
            'Customer',
            'Date',
            'Shift',
            'Total Amount',
            'Cash',
            'Cheque',
            'POS',
            'Transfer',
            'Return Inward',
            'Balance To Use',
            'Difference',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header Style
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F4E78']
                ],
                'alignment' => ['horizontal' => 'center']
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Freeze Header
                $sheet->freezePane('A2');

                // Currency Formatting for amount columns (E to L)
                $sheet->getStyle('E2:L' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                // Total Row Styling
                $sheet->getStyle('A' . $highestRow . ':L' . $highestRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9EAD3']
                    ],
                ]);

                // Auto Filter (excluding total row)
                $sheet->setAutoFilter('A1:L' . ($highestRow - 1));

                // Borders
                $sheet->getStyle('A1:L' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => 'thin']
                    ]
                ]);

                // Right align amount columns
                $sheet->getStyle('E2:L' . $highestRow)
                    ->getAlignment()
                    ->setHorizontal('right');
            }
        ];
    }
}