<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'school_id', 'copy_id', 'learner_id', 'borrowed_at',
        'due_at', 'returned_at', 'status', 'fine_amount', 'fine_paid',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at'      => 'date',
        'returned_at' => 'datetime',
        'fine_paid'   => 'boolean',
    ];

    public function school()   { return $this->belongsTo(School::class); }
    public function copy()     { return $this->belongsTo(BookCopy::class, 'copy_id'); }
    public function learner()  { return $this->belongsTo(Learner::class); }
}
