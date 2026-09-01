<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherAssignment extends Model
{
    protected $fillable = [
        'staff_id', 'class_id', 'subject_id', 'academic_year_id',
        'term_id', 'stream_id', 'is_class_teacher',
    ];

    protected $casts = ['is_class_teacher' => 'boolean'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }
}
