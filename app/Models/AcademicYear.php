<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    protected $fillable = [
        'school_id', 'year', 'start_date', 'end_date', 'status', 'is_current',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function terms()
    {
        return $this->hasMany(Term::class)->orderBy('term_no');
    }

    public function currentTerm()
    {
        return $this->hasOne(Term::class)->where('is_current', true);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function gradingSchemes()
    {
        return $this->hasMany(GradingScheme::class);
    }
}
