<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'period_id', 'subject_id', 'class_id', 'stream_id',
        'max_score', 'weight', 'grading_scheme_id', 'status',
    ];

    protected $casts = [
        'max_score' => 'decimal:2',
        'weight'    => 'decimal:2',
    ];

    public function period()         { return $this->belongsTo(AssessmentPeriod::class, 'period_id'); }
    public function subject()        { return $this->belongsTo(Subject::class); }
    public function schoolClass()    { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function stream()         { return $this->belongsTo(Stream::class); }
    public function gradingScheme()  { return $this->belongsTo(GradingScheme::class); }
    public function scores()         { return $this->hasMany(AssessmentScore::class); }
}
