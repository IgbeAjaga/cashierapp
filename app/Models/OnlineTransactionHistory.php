<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineTransactionHistory extends Model
{
    protected $fillable = [
        'online_transaction_id',
        'edited_by',
        'old_values',
        'new_values'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function onlineTransaction()
    {
        return $this->belongsTo(OnlineTransaction::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}