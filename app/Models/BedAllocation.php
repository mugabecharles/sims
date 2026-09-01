<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BedAllocation extends Model
{
    protected $fillable = ['learner_id', 'bed_id', 'academic_year_id', 'term_id', 'start_date', 'end_date'];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function learner()      { return $this->belongsTo(Learner::class); }
    public function bed()          { return $this->belongsTo(Bed::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function term()         { return $this->belongsTo(Term::class); }
}
