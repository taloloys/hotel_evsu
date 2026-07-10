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
        'is_stockable',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_stockable' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PosCategory::class, 'category_id', 'category_id');
    }

    public function inventoryLogs(): HasMany
    {
        return $this->hasMany(PosInventoryLog::class, 'product_id', 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereHas('category', fn ($query) => $query->active());
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        $default = PosSetting::defaultLowStockThreshold();

        return $query->where('is_active', true)
            ->where('is_stockable', true)
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

    public function effectiveLowStockThreshold(): int
    {
        return $this->low_stock_threshold ?? PosSetting::defaultLowStockThreshold();
    }

    public function isLowStock(): bool
    {
        if (! $this->is_stockable) {
            return false;
        }

        return $this->stock_quantity <= $this->effectiveLowStockThreshold();
    }

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return asset('storage/'.$this->image_path);
    }
}
