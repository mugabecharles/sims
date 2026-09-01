<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentScore extends Model
{
    protected $fillable = [
        'assessment_id', 'learner_id', 'score', 'grade', 'points',
        'teacher_comment', 'initials', 'status', 'entered_by', 'approved_by', 'approved_at',
    ];

    protected $casts = [
        'score'       => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function assessment()  { return $this->belongsTo(Assessment::class); }
    public function learner()     { return $this->belongsTo(Learner::class); }
    public function enteredBy()   { return $this->belongsTo(User::class, 'entered_by'); }
    public function approvedBy()  { return $this->belongsTo(User::class, 'approved_by'); }
}
