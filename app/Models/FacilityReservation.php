<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityReservation extends Model
{
    use HasFactory;

    protected $primaryKey = 'reservation_id';

    protected $fillable = [
        'facility_id',
        'booker_name',
        'booker_email',
        'booker_contact',
        'reservation_date',
        'start_time',
        'end_time',
        'duration_hours',
        'estimated_amount',
        'status',
        'reference_number',
        'terms_accepted',
        'terms_accepted_at',
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'terms_accepted' => 'boolean',
            'terms_accepted_at' => 'datetime',
            'processed_at' => 'datetime',
            'estimated_amount' => 'decimal:2',
            'duration_hours' => 'decimal:2',
        ];
    }

    /**
     * Auto-generate a unique reference number on creation.
     */
    protected static function booted(): void
    {
        static::creating(function (self $reservation): void {
            if (empty($reservation->reference_number)) {
                $reservation->reference_number = 'FACIL-'.strtoupper(substr(uniqid(), -8));
            }
        });

        // Finalize reference with the actual ID once created (guaranteed unique)
        static::created(function (self $reservation): void {
            $reservation->updateQuietly([
                'reference_number' => 'FACIL-'.str_pad($reservation->reservation_id, 6, '0', STR_PAD_LEFT),
            ]);
        });
    }

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'facility_id', 'facility_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by', 'user_id');
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->facility?->rate_type === 'daily') {
            return '1 Day';
        }

        $hours = (float) ($this->duration_hours ?? 0);

        return $hours == 1.0 ? '1 hour' : "{$hours} hours";
    }
}
