<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // Audit logs are immutable — no updates, no soft deletes
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'user_name', 'action', 'entity', 'entity_id',
        'entity_label', 'old_data', 'new_data', 'description',
        'ip_address', 'user_agent', 'module',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}
