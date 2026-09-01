<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id', 'learner_id', 'academic_year_id', 'term_id',
        'invoice_no', 'total_amount', 'total_paid', 'balance',
        'status', 'due_date', 'notes', 'created_by',
    ];

    protected $casts = ['due_date' => 'date'];

    public function school()       { return $this->belongsTo(School::class); }
    public function learner()      { return $this->belongsTo(Learner::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function term()         { return $this->belongsTo(Term::class); }
    public function items()        { return $this->hasMany(InvoiceItem::class); }
    public function createdBy()    { return $this->belongsTo(User::class, 'created_by'); }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function payments()
    {
        return $this->belongsToMany(Payment::class, 'payment_allocations')
                    ->withPivot('amount')
                    ->withTimestamps();
    }

    /** Recalculate and persist balance */
    public function recalculateBalance(): void
    {
        $paid = $this->allocations()->sum('amount');
        $this->update([
            'total_paid' => $paid,
            'balance'    => $this->total_amount - $paid,
            'status'     => match(true) {
                $paid <= 0                       => 'issued',
                $paid >= $this->total_amount     => 'paid',
                default                          => 'partially_paid',
            },
        ]);
    }
}
