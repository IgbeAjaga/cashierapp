<?php

namespace App\Models;

use App\Models\InvoiceReport;
use App\Models\AwaitingReport;
use App\Models\OnlineTransaction;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_approved',
        'approved_at',
        'approved_by',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_approved' => 'boolean',
        'password' => 'hashed',
    ];

    /* =========================
     |  ROLE HELPERS
     |========================= */

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    
    /* =========================
     |  APPROVAL RELATIONSHIPS
     |========================= */

    // Superadmin/Admin who approved this user
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Users approved by this superadmin/admin
    public function approvedUsers()
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    /* =========================
     |  INVOICE REPORT AUDIT
     |========================= */

    // Invoice reports created by this user
    public function recordedInvoiceReports()
    {
        return $this->hasMany(
            InvoiceReport::class,
            'recorded_by'
        );
    }

    // Invoice reports edited by this user
    public function editedInvoiceReports()
    {
        return $this->hasMany(
            InvoiceReport::class,
            'edited_by'
        );
    }

    /* =========================
     |  AWAITING REPORT AUDIT
     |========================= */

    // Awaiting reports created by this user
    public function recordedAwaitingReports()
    {
        return $this->hasMany(
            AwaitingReport::class,
            'recorded_by'
        );
    }

    // Awaiting reports edited by this user
    public function editedAwaitingReports()
    {
        return $this->hasMany(
            AwaitingReport::class,
            'edited_by'
        );
    }

    /* =========================
     |  ONLINE TRANSACTION AUDIT
     |========================= */

    // Online transactions created by this user
    public function recordedOnlineTransactions()
    {
        return $this->hasMany(
            OnlineTransaction::class,
            'recorded_by'
        );
    }

    // Online transactions edited by this user
    public function editedOnlineTransactions()
    {
        return $this->hasMany(
            OnlineTransaction::class,
            'edited_by'
        );
    }
}