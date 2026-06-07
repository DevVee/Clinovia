<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentTimeSlot extends Model
{
    protected $fillable = ['slot_time', 'max_appointments', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active'        => 'boolean',
            'max_appointments' => 'integer',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('slot_time');
    }

    public function getFormattedTimeAttribute(): string
    {
        return date('h:i A', strtotime($this->slot_time));
    }
}
