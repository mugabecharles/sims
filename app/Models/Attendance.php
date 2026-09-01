<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'school_id', 'learner_id', 'class_id', 'stream_id',
        'academic_year_id', 'term_id', 'date', 'session',
        'status', 'reason', 'recorded_by', 'corrected_by',
        'corrected_at', 'correction_reason',
    ];

    protected $casts = [
        'date'         => 'date',
        'corrected_at' => 'datetime',
    ];

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function correctedBy()
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
