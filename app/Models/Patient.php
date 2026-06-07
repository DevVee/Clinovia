<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_number', 'category',
        'first_name', 'middle_name', 'last_name', 'suffix',
        'sex', 'birthdate', 'contact_number', 'email', 'address',
        'emergency_contact_name', 'emergency_contact_number',
        'year_level', 'program_strand', 'section',
        'guardian_name', 'guardian_relationship', 'guardian_contact', 'guardian_address',
        'blood_type', 'allergies', 'medical_conditions', 'notes',
        'is_active', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'birthdate'  => 'date',
            'is_active'  => 'boolean',
        ];
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name ? mb_substr($this->middle_name, 0, 1) . '.' : null,
            $this->last_name,
            $this->suffix,
        ]);
        return implode(' ', $parts);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birthdate?->age;
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::categoryLabels()[$this->category] ?? ucfirst($this->category);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function dispensingRecords(): HasMany
    {
        return $this->hasMany(DispensingRecord::class);
    }

    public function patientLogs(): HasMany
    {
        return $this->hasMany(PatientLog::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('patient_number', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    // ─── Static Helpers ───────────────────────────────────────────────────────

    public static function categories(): array
    {
        return [
            'college', 'senior_high', 'junior_high', 'elementary',
            'kinder', 'daycare', 'teacher', 'employee', 'visitor', 'other',
        ];
    }

    public static function categoryLabels(): array
    {
        return [
            'college'     => 'College',
            'senior_high' => 'Senior High School',
            'junior_high' => 'Junior High School',
            'elementary'  => 'Elementary',
            'kinder'      => 'Kinder',
            'daycare'     => 'Daycare',
            'teacher'     => 'Teacher',
            'employee'    => 'Employee',
            'visitor'     => 'Visitor',
            'other'       => 'Other',
        ];
    }

    public static function bloodTypes(): array
    {
        return ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-', 'Unknown'];
    }
}
