<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hostel extends Model
{
    protected $fillable = ['school_id', 'name', 'gender', 'capacity', 'warden_name', 'active'];
    protected $casts    = ['active' => 'boolean'];

    public function school()  { return $this->belongsTo(School::class); }
    public function rooms()   { return $this->hasMany(Room::class); }
}
