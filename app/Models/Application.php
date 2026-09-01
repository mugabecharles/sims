<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id', 'application_no', 'first_name', 'last_name', 'other_names',
        'class_applied_id', 'previous_school', 'previous_class', 'date_of_birth',
        'gender', 'notes', 'status', 'reviewed_by', 'reviewed_at', 'review_notes', 'submitted_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'reviewed_at'   => 'datetime',
        'submitted_at'  => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classApplied()
    {
        return $this->belongsTo(SchoolClass::class, 'class_applied_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function learner()
    {
        return $this->hasOne(Learner::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->other_names} {$this->last_name}");
    }
}
