<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['school_id', 'name', 'description', 'fee', 'active'];
    protected $casts    = ['active' => 'boolean'];

    public function school()      { return $this->belongsTo(School::class); }
    public function assignments() { return $this->hasMany(TransportAssignment::class); }
}
