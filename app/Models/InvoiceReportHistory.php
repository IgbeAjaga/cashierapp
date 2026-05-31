<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceReportHistory extends Model
{
    protected $fillable = [
        'invoice_report_id',
        'edited_by',
        'old_values',
        'new_values'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function invoiceReport()
    {
        return $this->belongsTo(InvoiceReport::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}