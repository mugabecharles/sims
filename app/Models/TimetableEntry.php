<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableEntry extends Model
{
    protected $fillable = [
        'timetable_id', 'period_id', 'day', 'class_id',
        'stream_id', 'subject_id', 'teacher_id', 'room',
    ];

    public function timetable()  { return $this->belongsTo(Timetable::class); }
    public function period()     { return $this->belongsTo(TimetablePeriod::class, 'period_id'); }
    public function schoolClass(){ return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function stream()     { return $this->belongsTo(Stream::class); }
    public function subject()    { return $this->belongsTo(Subject::class); }
    public function teacher()    { return $this->belongsTo(Staff::class, 'teacher_id'); }
}
