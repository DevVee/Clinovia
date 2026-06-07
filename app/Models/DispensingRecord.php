<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispensingRecord extends Model
{
    protected $fillable = [
        'patient_id', 'consultation_id', 'medicine_id',
        'quantity', 'dispensed_by', 'dispensed_at', 'remarks',
    ];

    protected function casts(): array
    {
        return [
            'dispensed_at' => 'datetime',
            'quantity'     => 'integer',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function dispensedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispensed_by');
    }
}
