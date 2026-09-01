<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // No soft deletes — use reversal only
    protected $fillable = [
        'school_id', 'learner_id', 'receipt_no', 'amount', 'method',
        'reference', 'mobile_number', 'bank_name', 'received_at',
        'status', 'received_by', 'reversed_by', 'reversed_at', 'reversal_reason', 'notes',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    public function school()      { return $this->belongsTo(School::class); }
    public function learner()     { return $this->belongsTo(Learner::class); }
    public function receivedBy()  { return $this->belongsTo(User::class, 'received_by'); }
    public function reversedBy()  { return $this->belongsTo(User::class, 'reversed_by'); }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'payment_allocations')
                    ->withPivot('amount')
                    ->withTimestamps();
    }
}
