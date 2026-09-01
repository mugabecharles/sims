<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisciplineCase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id', 'learner_id', 'incident_date', 'category', 'description',
        'witnesses', 'action_taken', 'sanction', 'status', 'reported_by',
        'handled_by', 'parent_notified', 'parent_notified_at', 'confidential_notes',
    ];

    protected $casts = [
        'incident_date'       => 'date',
        'parent_notified'     => 'boolean',
        'parent_notified_at'  => 'datetime',
    ];

    public function school()      { return $this->belongsTo(School::class); }
    public function learner()     { return $this->belongsTo(Learner::class); }
    public function reportedBy()  { return $this->belongsTo(User::class, 'reported_by'); }
    public function handledBy()   { return $this->belongsTo(User::class, 'handled_by'); }
}
