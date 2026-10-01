<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    use HasFactory;

    protected $primaryKey = 'facility_id';

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'hourly_rate',
        'daily_rate',
        'rate',
        'rate_type',
        'images',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'images' => 'array',
            'is_active' => 'boolean',
            'hourly_rate' => 'decimal:2',
            'daily_rate' => 'decimal:2',
            'rate' => 'decimal:2',
            'capacity' => 'integer',
        ];
    }

    public function getEffectiveHourlyRateAttribute(): ?float
    {
        if ($this->hourly_rate !== null) {
            return (float) $this->hourly_rate;
        }

        return $this->rate_type === 'hourly' ? (float) $this->rate : null;
    }

    public function getEffectiveDailyRateAttribute(): ?float
    {
        if ($this->daily_rate !== null) {
            return (float) $this->daily_rate;
        }

        return $this->rate_type === 'daily' ? (float) $this->rate : null;
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(FacilityReservation::class, 'facility_id', 'facility_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Resolve image URL — delegates to LandingPageShowcase for consistent disk logic.
     */
    public static function imageUrl(?string $path): ?string
    {
        return LandingPageShowcase::url($path);
    }
}
