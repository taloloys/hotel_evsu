<?php

namespace App\Models;

use App\Services\ReservationReferenceService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FacilityReservation extends Model
{
    use HasFactory;

    protected $primaryKey = 'reservation_id';

    protected $fillable = [
        'facility_id',
        'facility_set_id',
        'booker_name',
        'booker_email',
        'booker_contact',
        'event_name',
        'event_details',
        'billing_type',
        'reservation_date',
        'end_date',
        'total_days',
        'start_time',
        'end_time',
        'duration_hours',
        'agreed_rate',
        'estimated_amount',
        'final_amount',
        'status',
        'reference_number',
        'terms_accepted',
        'terms_accepted_at',
        'admin_notes',
        'processed_by',
        'processed_at',
        'actual_start_time',
        'actual_end_time',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'integer',
            'terms_accepted' => 'boolean',
            'terms_accepted_at' => 'datetime',
            'processed_at' => 'datetime',
            'actual_start_time' => 'datetime',
            'actual_end_time' => 'datetime',
            'estimated_amount' => 'decimal:2',
            'final_amount' => 'decimal:2',
            'agreed_rate' => 'decimal:2',
            'duration_hours' => 'decimal:2',
        ];
    }

    /**
     * Auto-generate a dynamic reference number on creation if not already provided.
     */
    protected static function booted(): void
    {
        static::creating(function (self $reservation): void {
            if (empty($reservation->reference_number)) {
                $reservation->reference_number = app(ReservationReferenceService::class)
                    ->generateReferenceForReservation($reservation);
            }
        });
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }

    public function facilitySet(): BelongsTo
    {
        return $this->belongsTo(FacilitySet::class, 'facility_set_id', 'facility_set_id');
    }

    public function reservedFacilities(): BelongsToMany
    {
        return $this->belongsToMany(
            Facility::class,
            'facility_reservation_facility',
            'reservation_id',
            'facility_id'
        );
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }

    public function isConsolidated(): bool
    {
        return $this->facility_set_id !== null;
    }

    public function getFacilityNameAttribute(): string
    {
        if ($this->facilitySet) {
            return $this->facilitySet->name;
        }

        if ($this->facility) {
            return $this->facility->name;
        }

        return 'Unassigned Facility';
    }

    public function getAllFacilitiesAttribute()
    {
        if ($this->relationLoaded('reservedFacilities') && $this->reservedFacilities->isNotEmpty()) {
            return $this->reservedFacilities;
        }

        $reserved = $this->reservedFacilities()->get();
        if ($reserved->isNotEmpty()) {
            return $reserved;
        }

        return $this->facility ? collect([$this->facility]) : collect();
    }

    public function getDurationLabelAttribute(): string
    {
        $days = $this->total_days ?? 1;
        $activeItem = $this->facilitySet ?? $this->facility;

        if ($this->billing_type === 'daily' || ($activeItem && $activeItem->rate_type === 'daily' && ! $this->billing_type)) {
            return $days === 1 ? '1 Day' : "{$days} Days";
        }

        $hours = (float) ($this->duration_hours ?? 0);
        $hourStr = $hours == 1.0 ? '1 hr' : "{$hours} hrs";

        if ($days > 1) {
            return "{$days} Days ({$hourStr}/day)";
        }

        return $hours == 1.0 ? '1 hour' : "{$hours} hours";
    }

    public function getEffectiveAmountAttribute(): float
    {
        return (float) ($this->final_amount ?? $this->estimated_amount);
    }

    public function isMultiDay(): bool
    {
        return ($this->total_days ?? 1) > 1 || ($this->end_date && $this->end_date->gt($this->reservation_date));
    }
}
