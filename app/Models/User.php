<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
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
        'avatar',
        'bio',
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

    public function avatarUrl(): string
    {
        if ($this->avatar) {
            return url('storage/' . $this->avatar);
        }

        // Fallback: A beautiful gradient person avatar silhouette (blue & cyan)
        return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128"><defs><linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" stop-color="%232563eb"/><stop offset="100%" stop-color="%230ea5e9"/></linearGradient></defs><rect width="128" height="128" fill="url(%23g)"/><circle cx="64" cy="64" r="50" fill="%23ffffff" opacity="0.15"/><path d="M64 76c15.464 0 28-12.536 28-28S79.464 20 64 20s-28 12.536-28 28 12.536 28 28 28zm0 8c-21.375 0-64 10.688-64 32v12h128v-12c0-21.312-42.625-32-64-32z" fill="%23ffffff" opacity="0.95"/></svg>';
    }

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
