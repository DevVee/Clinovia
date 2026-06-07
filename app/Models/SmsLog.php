<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SmsLog extends Model
{
    protected $fillable = [
        'recipient_number', 'recipient_name', 'message', 'status',
        'reference_id', 'reference_type',
        'api_response', 'sent_at', 'error_message', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'api_response' => 'array',
            'sent_at'      => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'sent'    => 'success',
            'failed'  => 'danger',
            'pending' => 'warning',
            default   => 'secondary',
        };
    }
}
