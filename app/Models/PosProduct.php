<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosProduct extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'image_path',
        'stock_quantity',
        'low_stock_threshold',
        'is_active',
        'stock_tracking',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Stock-tracking helpers
    // -------------------------------------------------------------------------

    /** Physical countable item — stock is tracked manually. */
    public function isManualTracked(): bool
    {
        return $this->stock_tracking === 'manual';
    }

    /** Made-to-order item — no stock numbers at all. */
    public function isNoTracking(): bool
    {
        return $this->stock_tracking === 'none';
    }

    /**
     * Backwards-compatible accessor so any code still reading `is_stockable`
     * continues to work while we finish the migration.
     */
    public function getIsStockableAttribute(): bool
    {
        return $this->stock_tracking === 'manual';
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(PosCategory::class, 'category_id', 'category_id');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(PosInventoryLog::class, 'product_id', 'product_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->active());
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /** Only manual-tracked products that are at or below their threshold. */
    public function scopeLowStock($query)
    {
        $default = PosSetting::defaultLowStockThreshold();

        return $query->where('is_active', true)
            ->where('stock_tracking', 'manual')
            ->where(function ($q) use ($default) {
                $q->where(function ($sub) use ($default) {
                    $sub->whereNull('low_stock_threshold')
                        ->where('stock_quantity', '<=', $default);
                })->orWhere(function ($sub) {
                    $sub->whereNotNull('low_stock_threshold')
                        ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
                });
            });
    }

    /** Only manual-tracked products that are above low-stock threshold but <= 1.4 * threshold. */
    public function scopeSemiLow($query)
    {
        $default = PosSetting::defaultLowStockThreshold();

        return $query->where('is_active', true)
            ->where('stock_tracking', 'manual')
            ->where(function ($q) use ($default) {
                $q->where(function ($sub) use ($default) {
                    $sub->whereNull('low_stock_threshold')
                        ->where('stock_quantity', '>', $default)
                        ->where('stock_quantity', '<=', (int) ($default * 1.4));
                })->orWhere(function ($sub) {
                    $sub->whereNotNull('low_stock_threshold')
                        ->whereRaw('stock_quantity > low_stock_threshold')
                        ->whereRaw('stock_quantity <= (low_stock_threshold * 1.4)');
                });
            });
    }

    // -------------------------------------------------------------------------
    // Instance methods
    // -------------------------------------------------------------------------

    public function effectiveLowStockThreshold(): int
    {
        return $this->low_stock_threshold ?? PosSetting::defaultLowStockThreshold();
    }

    /** Returns true only for manual-tracked products that are below threshold. */
    public function isLowStock(): bool
    {
        if (! $this->isManualTracked()) {
            return false;
        }

        return $this->stock_quantity <= $this->effectiveLowStockThreshold();
    }

    /** Returns true only for manual-tracked products that are semi low (above threshold but <= 1.4 * threshold). */
    public function isSemiLow(): bool
    {
        if (! $this->isManualTracked()) {
            return false;
        }

        $threshold = $this->effectiveLowStockThreshold();

        return $this->stock_quantity > $threshold && $this->stock_quantity <= (int) ($threshold * 1.4);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return asset('storage/'.$this->image_path);
    }
}
