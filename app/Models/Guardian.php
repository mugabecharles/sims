<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'other_names', 'phone', 'phone2', 'email',
        'address', 'occupation', 'employer', 'national_id', 'gender',
        'user_id', 'communication_pref', 'sms_opt_in', 'email_opt_in',
    ];

    protected $casts = [
        'sms_opt_in'   => 'boolean',
        'email_opt_in' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function learners()
    {
        return $this->belongsToMany(Learner::class, 'learner_guardians')
                    ->withPivot('relationship', 'is_primary', 'is_fee_payer', 'can_pickup')
                    ->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->other_names} {$this->last_name}");
    }
}
