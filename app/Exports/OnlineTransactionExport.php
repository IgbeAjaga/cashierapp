<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class OnlineTransactionExport implements FromView
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function view(): View
    {
        return view('online_transactions.export_excel', [
            'records' => $this->records
        ]);
    }
}