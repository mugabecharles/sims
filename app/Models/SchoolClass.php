<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'school_id', 'name', 'display_name', 'level', 'section', 'sort_order', 'active',
    ];

    protected $casts = ['active' => 'boolean'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function streams()
    {
        return $this->hasMany(Stream::class, 'class_id');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id')
                    ->withPivot('compulsory', 'academic_year_id')
                    ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }

    public function teacherAssignments()
    {
        return $this->hasMany(TeacherAssignment::class, 'class_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->display_name ?? $this->name;
    }
}
