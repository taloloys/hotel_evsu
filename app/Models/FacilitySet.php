<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacilitySet extends Model
{
    use HasFactory;

    protected $primaryKey = 'facility_set_id';

    protected $fillable = [
        'name',
        'prefix_code',
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

        if ($this->rate_type === 'hourly' && $this->rate > 0) {
            return (float) $this->rate;
        }

        // Fallback: sum of member facilities if not explicitly configured
        $sum = $this->facilities->sum(fn (Facility $f) => $f->effective_hourly_rate ?? 0);

        return $sum > 0 ? (float) $sum : ($this->rate_type === 'hourly' ? (float) $this->rate : null);
    }

    public function getEffectiveDailyRateAttribute(): ?float
    {
        if ($this->daily_rate !== null) {
            return (float) $this->daily_rate;
        }

        if ($this->rate_type === 'daily' && $this->rate > 0) {
            return (float) $this->rate;
        }

        // Fallback: sum of member facilities if not explicitly configured
        $sum = $this->facilities->sum(fn (Facility $f) => $f->effective_daily_rate ?? 0);

        return $sum > 0 ? (float) $sum : ($this->rate_type === 'daily' ? (float) $this->rate : null);
    }

    public function getResolvedCapacityAttribute(): int
    {
        if ($this->capacity !== null && $this->capacity > 0) {
            return (int) $this->capacity;
        }

        return (int) $this->facilities->sum('capacity');
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(
            Facility::class,
            'facility_set_facility',
            'facility_set_id',
            'facility_id'
        );
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(FacilityReservation::class, 'facility_set_id', 'facility_set_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public static function imageUrl(?string $path): ?string
    {
        return LandingPageShowcase::url($path);
    }
}
