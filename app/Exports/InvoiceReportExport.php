<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class InvoiceReportExport implements FromView
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function view(): View
    {
        return view('invoice_reports.export_excel', [
            'records' => $this->records
        ]);
    }
}