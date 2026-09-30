<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\FacilityReservation;
use Carbon\Carbon;

class FacilityBookingService
{
    /**
     * Calculate estimated rental amount.
     * Hourly: rate × hours elapsed.  Daily: flat rate regardless of duration.
     */
    public function calculateAmount(Facility $facility, string $start, string $end): float
    {
        if ($facility->rate_type === 'daily') {
            return (float) $facility->rate;
        }

        $hours = $this->computeDurationHours($start, $end);

        return round($hours * (float) $facility->rate, 2);
    }

    /**
     * Compute the duration in hours between two H:i time strings.
     */
    public function computeDurationHours(string $start, string $end): float
    {
        $startCarbon = Carbon::createFromFormat('H:i', $start);
        $endCarbon = Carbon::createFromFormat('H:i', $end);

        return round($endCarbon->diffInMinutes($startCarbon) / 60, 2);
    }

    /**
     * Check if the given time slot overlaps with existing reservations.
     * Uses classic interval overlap: A.start < B.end AND A.end > B.start
     *
     * @param  array<string>  $statuses  Defaults to ['approved'] for public submissions
     */
    public function hasConflict(
        int $facilityId,
        string $date,
        string $start,
        string $end,
        ?int $excludeId = null,
        array $statuses = ['approved']
    ): bool {
        $query = FacilityReservation::where('facility_id', $facilityId)
            ->where('reservation_date', $date)
            ->whereIn('status', $statuses)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start);

        if ($excludeId) {
            $query->where('reservation_id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
