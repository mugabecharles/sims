<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentPeriod extends Model
{
    protected $fillable = [
        'school_id', 'academic_year_id', 'term_id', 'name',
        'type', 'start_date', 'end_date', 'status', 'sort_order',
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function school()         { return $this->belongsTo(School::class); }
    public function academicYear()   { return $this->belongsTo(AcademicYear::class); }
    public function term()           { return $this->belongsTo(Term::class); }
    public function assessments()    { return $this->hasMany(Assessment::class, 'period_id'); }
}
