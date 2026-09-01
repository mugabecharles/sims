<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingScheme extends Model
{
    protected $fillable = [
        'school_id', 'name', 'academic_year_id', 'level', 'rules', 'is_default',
    ];

    protected $casts = [
        'rules'      => 'array',
        'is_default' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Given a raw score and max_score, return the matching grade rule.
     */
    public function grade(float $score, float $maxScore = 100): ?array
    {
        $percentage = $maxScore > 0 ? ($score / $maxScore) * 100 : 0;
        foreach ($this->rules as $rule) {
            if ($percentage >= $rule['min'] && $percentage <= $rule['max']) {
                return $rule;
            }
        }
        return null;
    }
}
