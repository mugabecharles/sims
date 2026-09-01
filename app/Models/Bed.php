<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    protected $fillable = ['room_id', 'bed_no', 'status'];

    public function room()        { return $this->belongsTo(Room::class); }
    public function allocations() { return $this->hasMany(BedAllocation::class); }

    public function currentOccupant()
    {
        return $this->allocations()
                    ->whereNull('end_date')
                    ->latest()
                    ->first()
                    ?->learner;
    }
}
