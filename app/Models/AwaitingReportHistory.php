<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AwaitingReportHistory extends Model
{
    protected $fillable = [
        'awaiting_report_id',
        'edited_by',
        'old_values',
        'new_values'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function awaitingReport()
    {
        return $this->belongsTo(AwaitingReport::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}