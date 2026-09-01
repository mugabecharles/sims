<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    protected $fillable = ['school_id', 'name', 'event', 'body', 'active'];
    protected $casts    = ['active' => 'boolean'];

    public function school()   { return $this->belongsTo(School::class); }
    public function messages() { return $this->hasMany(SmsMessage::class, 'template_id'); }

    /** Replace template variables with actual values */
    public function render(array $vars): string
    {
        $body = $this->body;
        foreach ($vars as $key => $value) {
            $body = str_replace('{' . $key . '}', $value, $body);
        }
        return $body;
    }
}
