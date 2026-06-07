<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id', 'logged_by',
        'log_date', 'time_in', 'time_out',
        'chief_complaint', 'vital_signs',
        'assessment', 'treatment',
        'disposition',
        'sms_guardian', 'sms_sent',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'log_date'     => 'date',
            'vital_signs'  => 'array',
            'sms_guardian' => 'boolean',
            'sms_sent'     => 'boolean',
        ];
    }

    // ─── Disposition options ──────────────────────────────────────────────────

    public static function dispositions(): array
    {
        return [
            'rest_in_clinic'       => 'Rest in Clinic',
            'returned_to_class'    => 'Returned to Class / Work',
            'sent_home'            => 'Sent Home',
            'referred_to_hospital' => 'Referred to Hospital',
            'further_observation'  => 'Under Observation',
        ];
    }

    public static function dispositionIcons(): array
    {
        return [
            'rest_in_clinic'       => 'bi-hospital',
            'returned_to_class'    => 'bi-mortarboard',
            'sent_home'            => 'bi-house-heart',
            'referred_to_hospital' => 'bi-ambulance',
            'further_observation'  => 'bi-eye',
        ];
    }

    public static function dispositionColors(): array
    {
        return [
            'rest_in_clinic'       => 'info',
            'returned_to_class'    => 'success',
            'sent_home'            => 'warning',
            'referred_to_hospital' => 'danger',
            'further_observation'  => 'secondary',
        ];
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getDispositionLabelAttribute(): string
    {
        return static::dispositions()[$this->disposition] ?? ucwords(str_replace('_', ' ', $this->disposition));
    }

    public function getDispositionColorAttribute(): string
    {
        return static::dispositionColors()[$this->disposition] ?? 'secondary';
    }

    public function getDispositionIconAttribute(): string
    {
        return static::dispositionIcons()[$this->disposition] ?? 'bi-check-circle';
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function loggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeToday($query)
    {
        return $query->whereDate('log_date', today());
    }

    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('log_date', $date);
    }
}
