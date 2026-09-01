<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $fillable = [
        'academic_year_id', 'name', 'term_no', 'start_date', 'end_date', 'status', 'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function assessmentPeriods()
    {
        return $this->hasMany(AssessmentPeriod::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
