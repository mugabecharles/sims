<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $fillable = [
        'school_id', 'academic_year_id', 'term_id', 'class_id',
        'study_mode', 'name', 'description', 'active',
    ];

    protected $casts = ['active' => 'boolean'];

    public function school()        { return $this->belongsTo(School::class); }
    public function academicYear()  { return $this->belongsTo(AcademicYear::class); }
    public function term()          { return $this->belongsTo(Term::class); }
    public function schoolClass()   { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function items()         { return $this->hasMany(FeeItem::class)->orderBy('sort_order'); }

    public function getTotalAttribute(): int
    {
        return $this->items->sum('amount');
    }
}
