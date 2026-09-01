<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamCandidate extends Model
{
    protected $fillable = [
        'school_id', 'learner_id', 'exam_type', 'exam_year',
        'centre_no', 'index_no', 'status', 'eligibility_checklist', 'notes',
    ];

    protected $casts = ['eligibility_checklist' => 'array'];

    public function school()    { return $this->belongsTo(School::class); }
    public function learner()   { return $this->belongsTo(Learner::class); }
    public function subjects()  { return $this->hasMany(ExamSubject::class, 'candidate_id'); }
    public function results()   { return $this->hasMany(ExamResult::class, 'candidate_id'); }
}
