<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = ['school_id', 'academic_year_id', 'term_id', 'name', 'version', 'status'];

    public function school()       { return $this->belongsTo(School::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function term()         { return $this->belongsTo(Term::class); }
    public function entries()      { return $this->hasMany(TimetableEntry::class); }
}
