<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'school_id', 'registration_no', 'type', 'capacity',
        'driver_name', 'driver_phone', 'status', 'last_service_date', 'next_service_date',
    ];

    protected $casts = ['last_service_date' => 'date', 'next_service_date' => 'date'];

    public function school()      { return $this->belongsTo(School::class); }
    public function assignments() { return $this->hasMany(TransportAssignment::class); }
}
