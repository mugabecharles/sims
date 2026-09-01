<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportAssignment extends Model
{
    protected $fillable = [
        'learner_id', 'route_id', 'vehicle_id', 'stop',
        'academic_year_id', 'term_id', 'start_date', 'end_date',
    ];

    protected $casts = ['start_date' => 'date', 'end_date' => 'date'];

    public function learner()      { return $this->belongsTo(Learner::class); }
    public function route()        { return $this->belongsTo(Route::class); }
    public function vehicle()      { return $this->belongsTo(Vehicle::class); }
    public function academicYear() { return $this->belongsTo(AcademicYear::class); }
    public function term()         { return $this->belongsTo(Term::class); }
}
