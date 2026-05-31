<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceRegistry extends Model
{
    use HasFactory;

    protected $table = 'invoice_registries';

    protected $fillable = [
        'invoice_number',
        'source_table',
        'source_id',
        'is_suspicious',
        'suspicious_reason',
        'created_by',
    ];

    protected $casts = [
        'invoice_number'    => 'integer',
        'source_id'         => 'integer',
        'created_by'        => 'integer',
        'is_suspicious'     => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFormattedSourceAttribute(): string
    {
        return str($this->source_table)
            ->replace('_', ' ')
            ->title();
    }

    public function getModuleNameAttribute(): string
    {
        return match ($this->source_table) {

            'cross_checkings'    => 'Cross Checking',

            'awaiting_reports'   => 'Awaiting Report',

            'invoice_reports'    => 'Invoice Report',

            'online_transactions'=> 'Online Transaction',

            default              => ucwords(
                str_replace('_', ' ', $this->source_table)
            ),
        };
    }

    public function getEditUrlAttribute(): ?string
    {
        return match ($this->source_table) {

            'cross_checkings' =>
                route('cross-checkings.edit', $this->source_id),

            'awaiting_reports' =>
                route('awaiting-reports.edit', $this->source_id),

            'invoice_reports' =>
                route('invoice-reports.edit', $this->source_id),

            'online_transactions' =>
                route('online-transactions.edit', $this->source_id),

            default => null,
        };
    }

    public function getButtonTextAttribute(): string
    {
        return match ($this->source_table) {

            'cross_checkings' =>
                'Open Cross Checking',

            'awaiting_reports' =>
                'Open Awaiting Report',

            'invoice_reports' =>
                'Open Invoice Report',

            'online_transactions' =>
                'Open Online Transaction',

            default =>
                'Open Record',
        };
    }
}