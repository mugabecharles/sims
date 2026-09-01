<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['hostel_id', 'room_no', 'capacity'];

    public function hostel() { return $this->belongsTo(Hostel::class); }
    public function beds()   { return $this->hasMany(Bed::class); }
}
