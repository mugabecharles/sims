<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetablePeriod extends Model
{
    protected $fillable = ['school_id', 'name', 'start_time', 'end_time', 'is_break', 'sort_order'];
    protected $casts    = ['is_break' => 'boolean'];

    public function school()  { return $this->belongsTo(School::class); }
    public function entries() { return $this->hasMany(TimetableEntry::class, 'period_id'); }
}
