<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'school_id', 'code', 'name', 'level', 'subject_type', 'department', 'active',
    ];

    protected $casts = ['active' => 'boolean'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_subjects', 'subject_id', 'class_id')
                    ->withPivot('compulsory', 'academic_year_id')
                    ->withTimestamps();
    }

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }
}
