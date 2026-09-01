<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'learner_id', 'school_id', 'academic_year_id', 'term_id',
        'class_id', 'stream_id', 'study_mode', 'status',
        'enrolled_at', 'left_at', 'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'date',
        'left_at'     => 'date',
    ];

    public function learner()
    {
        return $this->belongsTo(Learner::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }
}
