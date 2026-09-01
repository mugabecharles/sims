<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSubject extends Model
{
    protected $fillable = [
        'candidate_id', 'subject_id', 'subject_code',
        'ca_status', 'ca_score', 'project_status', 'project_score',
    ];

    protected $casts = ['ca_score' => 'decimal:2', 'project_score' => 'decimal:2'];

    public function candidate() { return $this->belongsTo(ExamCandidate::class, 'candidate_id'); }
    public function subject()   { return $this->belongsTo(Subject::class); }
}
