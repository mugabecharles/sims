<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $fillable = [
        'candidate_id', 'subject_id', 'grade', 'mark', 'points', 'result_year', 'source', 'notes',
    ];

    protected $casts = ['mark' => 'decimal:2'];

    public function candidate() { return $this->belongsTo(ExamCandidate::class, 'candidate_id'); }
    public function subject()   { return $this->belongsTo(Subject::class); }
}
