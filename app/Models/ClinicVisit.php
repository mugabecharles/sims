<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicVisit extends Model
{
    protected $fillable = [
        'school_id', 'learner_id', 'visit_date', 'complaint',
        'action_taken', 'referred', 'referral_notes', 'follow_up_date', 'created_by',
    ];

    protected $casts = [
        'visit_date'     => 'date',
        'follow_up_date' => 'date',
        'referred'       => 'boolean',
    ];

    public function school()     { return $this->belongsTo(School::class); }
    public function learner()    { return $this->belongsTo(Learner::class); }
    public function createdBy()  { return $this->belongsTo(User::class, 'created_by'); }
}
