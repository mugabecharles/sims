<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthRecord extends Model
{
    protected $fillable = [
        'school_id', 'learner_id', 'blood_group',
        'allergies', 'medical_alerts', 'chronic_conditions',
    ];

    public function school()       { return $this->belongsTo(School::class); }
    public function learner()      { return $this->belongsTo(Learner::class); }
    public function clinicVisits() { return $this->hasMany(ClinicVisit::class, 'learner_id', 'learner_id'); }
}
