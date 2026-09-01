<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'username', 'name', 'email', 'phone', 'password',
        'mfa_enabled', 'mfa_secret', 'status',
        'failed_login_attempts', 'locked_until',
        'last_login_at', 'last_login_ip',
    ];

    protected $hidden = [
        'password', 'remember_token', 'mfa_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'locked_until'      => 'datetime',
        'mfa_enabled'       => 'boolean',
        'password'          => 'hashed',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_users')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function learner()
    {
        return $this->hasOne(Learner::class);
    }

    public function guardian()
    {
        return $this->hasOne(Guardian::class);
    }

    // ── RBAC helpers ──────────────────────────────────────────────

    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermission(string $code): bool
    {
        return $this->roles()
            ->join('role_permissions', 'roles.id', '=', 'role_permissions.role_id')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('permissions.code', $code)
            ->exists();
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked' ||
               ($this->locked_until && $this->locked_until->isFuture());
    }
}
