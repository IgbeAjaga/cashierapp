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

class InvoiceReportExport implements
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
                $record->invoice_number,
                $record->customer,
                optional($record->date)->format('d M Y'),
                ucfirst($record->shift),

                (float) $record->total_amount,
                (float) $record->cash,
                (float) $record->awaiting_invoice,
                (float) $record->cheque,
                (float) $record->credit,
                (float) $record->pos,
                (float) $record->bank_transfer,
                (float) $record->less_credit,
                (float) $record->return_inward,
                (float) $record->balance_to_use,
                (float) $record->difference,
                (float) $record->return_outward,
                (float) $record->invoice_to_invoice,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Totals Row
        |--------------------------------------------------------------------------
        */

        $rows->push([
            '',
            'TOTAL',
            '',
            '',

            $this->records->sum('total_amount'),
            $this->records->sum('cash'),
            $this->records->sum('awaiting_invoice'),
            $this->records->sum('cheque'),
            $this->records->sum('credit'),
            $this->records->sum('pos'),
            $this->records->sum('bank_transfer'),
            $this->records->sum('less_credit'),
            $this->records->sum('return_inward'),
            $this->records->sum('balance_to_use'),
            $this->records->sum('difference'),
            $this->records->sum('return_outward'),
            $this->records->sum('invoice_to_invoice'),
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
            'Awaiting Invoice',
            'Cheque',
            'Credit',
            'POS',
            'Transfer',
            'Less Credit',
            'Return Inward',
            'Balance To Use',
            'Difference',
            'Return Outward',
            'Invoice To Invoice',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [

            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1F4E78']
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();

                /*
                |--------------------------------------------------------------------------
                | Freeze Header
                |--------------------------------------------------------------------------
                */

                $sheet->freezePane('A2');

                /*
                |--------------------------------------------------------------------------
                | Currency Columns
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('E2:Q' . $highestRow)
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                /*
                |--------------------------------------------------------------------------
                | Total Row Style
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A' . $highestRow . ':Q' . $highestRow)
                    ->getFont()
                    ->setBold(true);

                $sheet->getStyle('A' . $highestRow . ':Q' . $highestRow)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('D9EAD3');

                /*
                |--------------------------------------------------------------------------
                | Filters
                |--------------------------------------------------------------------------
                */

                $sheet->setAutoFilter(
                    'A1:Q' . ($highestRow - 1)
                );

                /*
                |--------------------------------------------------------------------------
                | Center Header
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A1:Q1')
                    ->getAlignment()
                    ->setHorizontal('center');

                /*
                |--------------------------------------------------------------------------
                | Borders
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle(
                    'A1:Q' . $highestRow
                )->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin'
                        ]
                    ]
                ]);
            }
        ];
    }
}