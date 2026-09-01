<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'school_id', 'learner_id', 'invoice_id', 'type',
        'amount', 'reason', 'approved_by', 'approved_at', 'status',
    ];

    protected $casts = ['approved_at' => 'datetime'];

    public function school()     { return $this->belongsTo(School::class); }
    public function learner()    { return $this->belongsTo(Learner::class); }
    public function invoice()    { return $this->belongsTo(Invoice::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
}
