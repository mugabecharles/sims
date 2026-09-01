<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsMessage extends Model
{
    protected $fillable = [
        'school_id', 'recipient', 'learner_id', 'guardian_id', 'template_id',
        'message', 'status', 'provider_message_id', 'provider_response',
        'retry_count', 'sent_at', 'delivered_at', 'sent_by', 'event',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'sent_at'           => 'datetime',
        'delivered_at'      => 'datetime',
    ];

    public function school()    { return $this->belongsTo(School::class); }
    public function learner()   { return $this->belongsTo(Learner::class); }
    public function guardian()  { return $this->belongsTo(Guardian::class); }
    public function template()  { return $this->belongsTo(SmsTemplate::class, 'template_id'); }
    public function sentBy()    { return $this->belongsTo(User::class, 'sent_by'); }
}
