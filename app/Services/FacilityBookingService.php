<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\FacilityReservation;
use Carbon\Carbon;

class FacilityBookingService
{
    /**
     * Calculate estimated rental amount.
     * Supports hourly and daily billing types, date ranges (multi-day), and agreed rate override.
     */
    public function calculateAmount(
        Facility $facility,
        string $start,
        string $end,
        ?string $billingType = null,
        ?string $startDate = null,
        ?string $endDate = null,
        ?float $agreedRate = null
    ): float {
        $type = $billingType ?: ($facility->rate_type ?? 'hourly');

        // Resolve rate: agreed rate override takes precedence
        if ($agreedRate !== null && $agreedRate >= 0) {
            $rate = (float) $agreedRate;
        } elseif ($type === 'daily') {
            $rate = (float) ($facility->effective_daily_rate ?? $facility->rate);
        } else {
            $rate = (float) ($facility->effective_hourly_rate ?? $facility->rate);
        }

        // Calculate days
        $days = 1;
        if ($startDate) {
            $startCarbon = Carbon::parse($startDate);
            $endCarbon = $endDate ? Carbon::parse($endDate) : $startCarbon;
            $days = max(1, $startCarbon->diffInDays($endCarbon) + 1);
        }

        if ($type === 'daily') {
            return max(0.0, round($rate * $days, 2));
        }

        // Hourly calculation (per day hours * days)
        $hoursPerDay = $this->computeDurationHours($start, $end);

        return max(0.0, round($hoursPerDay * $rate * $days, 2));
    }

    /**
     * Compute the duration in hours between two time strings.
     */
    public function computeDurationHours(string $start, string $end): float
    {
        $startCarbon = Carbon::parse($start);
        $endCarbon = Carbon::parse($end);

        $minutes = abs($startCarbon->diffInMinutes($endCarbon));

        return round($minutes / 60, 2);
    }

    /**
     * Check if the given time slot overlaps with existing reservations.
     * Supports date ranges (multi-day) and single-day slots.
     *
     * @param  array<string>  $statuses  Defaults to ['approved', 'active']
     */
    public function hasConflict(
        int $facilityId,
        string $date,
        string $start,
        string $end,
        ?int $excludeId = null,
        array $statuses = ['approved'],
        ?string $endDate = null
    ): bool {
        $targetStartDate = $date;
        $targetEndDate = $endDate ?: $date;

        $query = FacilityReservation::where('facility_id', $facilityId)
            ->whereIn('status', $statuses)
            ->where(function ($q) use ($targetStartDate, $targetEndDate) {
                $q->where('reservation_date', '<=', $targetEndDate)
                    ->where(function ($sub) use ($targetStartDate) {
                        $sub->whereNull('end_date')
                            ->where('reservation_date', '>=', $targetStartDate)
                            ->orWhere('end_date', '>=', $targetStartDate);
                    });
            })
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start);

        if ($excludeId) {
            $query->where('reservation_id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Compute excess usage charge upon checkout if actual end time exceeds scheduled end time.
     */
    public function computeExcessCharge(FacilityReservation $reservation, Carbon $actualEndTime): array
    {
        $scheduledDate = $reservation->end_date ?? $reservation->reservation_date;
        $scheduledEnd = Carbon::parse($scheduledDate->format('Y-m-d').' '.$reservation->end_time);

        if ($actualEndTime->lte($scheduledEnd)) {
            return [
                'excess_minutes' => 0,
                'excess_hours' => 0.0,
                'excess_charge' => 0.0,
                'final_amount' => (float) $reservation->estimated_amount,
            ];
        }

        $excessMinutes = $actualEndTime->diffInMinutes($scheduledEnd);
        $excessHours = round($excessMinutes / 60, 2);

        $hourlyRate = $reservation->agreed_rate !== null
            ? (float) $reservation->agreed_rate
            : ($reservation->facility?->effective_hourly_rate ?? (float) ($reservation->facility?->rate ?? 0));

        $excessCharge = round($excessHours * $hourlyRate, 2);
        $finalAmount = (float) $reservation->estimated_amount + $excessCharge;

        return [
            'excess_minutes' => $excessMinutes,
            'excess_hours' => $excessHours,
            'excess_charge' => $excessCharge,
            'final_amount' => $finalAmount,
        ];
    }
}
