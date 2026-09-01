<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id', 'user_id', 'staff_no', 'first_name', 'last_name', 'other_names',
        'gender', 'date_of_birth', 'nationality', 'national_id', 'phone', 'email',
        'address', 'photo_url', 'staff_type', 'designation', 'department',
        'date_joined', 'employment_status', 'qualifications',
    ];

    protected $casts = ['date_of_birth' => 'date', 'date_joined' => 'date'];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignments()
    {
        return $this->hasMany(TeacherAssignment::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->other_names} {$this->last_name}");
    }
}
