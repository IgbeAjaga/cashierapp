<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\InvoiceReportHistory;

class InvoiceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'customer',
        'payment_note',
        'total_amount',
        'cash',
        'awaiting_invoice',
        'cheque',
        'credit',
        'pos',
        'bank_transfer',
        'return_outward',
        'invoice_to_invoice',
        'cheque_details',
        'pos_details',
        'bank_transfer_details',
        'return_inward',
        'less_credit',
        'balance_to_use',
        'difference',
        'is_balanced',
        'date',
        'shift',
        'recorded_by',
        'edited_by',
        'last_edited_at',
    ];

    protected $casts = [
        'date' => 'date',
        'last_edited_at' => 'datetime',
        'is_balanced' => 'boolean',
    ];

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function editedBy()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty();
    }

    public function histories()
{
    return $this->hasMany(
        InvoiceReportHistory::class
    )->latest();
}
}