<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'emis_no', 'name', 'short_name', 'level', 'ownership', 'school_type',
        'registration_no', 'licence_no', 'district', 'subcounty', 'village',
        'address', 'phone', 'email', 'website', 'logo_url', 'motto',
        'head_teacher_name', 'head_teacher_signature_url', 'proprietor_name',
        'stamp_url', 'admission_no_prefix', 'admission_no_next',
        'invoice_no_prefix', 'invoice_no_next', 'receipt_no_prefix',
        'receipt_no_next', 'learner_id_prefix', 'learner_id_next',
        'sms_sender_id', 'sms_provider', 'sms_api_key',
        'currency', 'timezone', 'status',
    ];

    protected $hidden = ['sms_api_key'];

    // ── Relationships ─────────────────────────────────────────────

    public function users()
    {
        return $this->belongsToMany(User::class, 'school_users')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function currentAcademicYear()
    {
        return $this->hasOne(AcademicYear::class)->where('is_current', true);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class, 'school_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function learners()
    {
        return $this->hasMany(Learner::class);
    }

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function hostels()
    {
        return $this->hasMany(Hostel::class);
    }

    // ── Number generators ─────────────────────────────────────────

    public function generateAdmissionNo(): string
    {
        $no = str_pad($this->admission_no_next, 4, '0', STR_PAD_LEFT);
        $this->increment('admission_no_next');
        return "{$this->admission_no_prefix}-{$no}";
    }

    public function generateInvoiceNo(): string
    {
        $no = str_pad($this->invoice_no_next, 6, '0', STR_PAD_LEFT);
        $this->increment('invoice_no_next');
        return "{$this->invoice_no_prefix}-{$no}";
    }

    public function generateReceiptNo(): string
    {
        $no = str_pad($this->receipt_no_next, 6, '0', STR_PAD_LEFT);
        $this->increment('receipt_no_next');
        return "{$this->receipt_no_prefix}-{$no}";
    }

    public function generateLearnerNo(): string
    {
        $no = str_pad($this->learner_id_next, 5, '0', STR_PAD_LEFT);
        $this->increment('learner_id_next');
        return "{$this->learner_id_prefix}-{$no}";
    }
}
