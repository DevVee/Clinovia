<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'created_by');
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'nurse_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'created_by');
    }

    public function approvedAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'approved_by');
    }

    public function dispensingRecords(): HasMany
    {
        return $this->hasMany(DispensingRecord::class, 'dispensed_by');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiConversation::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getRoleNameAttribute(): string
    {
        return $this->roles->first()?->name ?? 'No Role';
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('administrator');
    }

    public function isNurse(): bool
    {
        return $this->hasRole('nurse');
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staff');
    }
}
