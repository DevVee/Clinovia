<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'category_id', 'description',
        'quantity', 'unit', 'expiration_date',
        'batch_number', 'supplier',
        'low_stock_threshold', 'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expiration_date'    => 'date',
            'is_active'          => 'boolean',
            'quantity'           => 'integer',
            'low_stock_threshold'=> 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(MedicineCategory::class, 'category_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function inventoryTransactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function dispensingRecords(): HasMany
    {
        return $this->hasMany(DispensingRecord::class);
    }

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getIsLowStockAttribute(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiration_date) return false;
        return $this->expiration_date->isFuture()
            && $this->expiration_date->diffInDays(now()) <= 30;
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'low_stock_threshold');
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('expiration_date')
                     ->whereDate('expiration_date', '>=', today())
                     ->whereDate('expiration_date', '<=', today()->addDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiration_date')
                     ->whereDate('expiration_date', '<', today());
    }
}
