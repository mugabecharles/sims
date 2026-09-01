<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Learner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id', 'admission_no', 'learner_no', 'first_name', 'last_name',
        'other_names', 'gender', 'date_of_birth', 'nationality', 'national_id',
        'birth_cert_no', 'lin', 'religion', 'tribe', 'address', 'district',
        'subcounty', 'photo_url', 'previous_school', 'previous_class',
        'special_needs', 'has_disability', 'disability_details', 'study_mode',
        'status', 'user_id', 'application_id',
    ];

    protected $casts = [
        'date_of_birth'  => 'date',
        'has_disability' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function guardians()
    {
        return $this->belongsToMany(Guardian::class, 'learner_guardians')
                    ->withPivot('relationship', 'is_primary', 'is_fee_payer', 'can_pickup')
                    ->withTimestamps();
    }

    public function primaryGuardian()
    {
        return $this->guardians()->wherePivot('is_primary', true)->first();
    }

    public function feePayingGuardian()
    {
        return $this->guardians()->wherePivot('is_fee_payer', true)->first();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function currentEnrollment()
    {
        return $this->hasOne(Enrollment::class)
                    ->where('status', 'active')
                    ->latest();
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function assessmentScores()
    {
        return $this->hasMany(AssessmentScore::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function examCandidates()
    {
        return $this->hasMany(ExamCandidate::class);
    }

    public function disciplineCases()
    {
        return $this->hasMany(DisciplineCase::class);
    }

    public function healthRecord()
    {
        return $this->hasOne(HealthRecord::class);
    }

    public function bedAllocations()
    {
        return $this->hasMany(BedAllocation::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'entity');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->other_names} {$this->last_name}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}
