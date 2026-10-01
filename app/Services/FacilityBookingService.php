<?php

namespace App\Services;

use App\Exceptions\FacilityReservationConflictException;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\FacilitySet;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FacilityBookingService
{
    /**
     * Calculate estimated rental amount.
     * Supports hourly and daily billing types, date ranges (multi-day), and agreed rate override.
     */
    public function calculateAmount(
        Facility|FacilitySet $facility,
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
     * Check if a single facility has an overlapping reservation.
     * Checks both direct bookings and consolidated set bookings affecting this facility.
     *
     * @param  array<string>  $statuses  Defaults to ['approved']
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
        return $this->hasAnyConflict([$facilityId], $date, $start, $end, $excludeId, $statuses, $endDate);
    }

    /**
     * Check if ANY of the specified facility IDs have an overlapping reservation.
     *
     * @param  array<int>  $facilityIds
     * @param  array<string>  $statuses
     */
    public function hasAnyConflict(
        array $facilityIds,
        string $date,
        string $start,
        string $end,
        ?int $excludeId = null,
        array $statuses = ['approved'],
        ?string $endDate = null
    ): bool {
        if (empty($facilityIds)) {
            return false;
        }

        $targetStartDate = Carbon::parse($date)->toDateString();
        $targetEndDate = Carbon::parse($endDate ?: $date)->toDateString();

        $query = FacilityReservation::whereIn('status', $statuses)
            ->where(function ($facQ) use ($facilityIds) {
                $facQ->whereIn('facility_id', $facilityIds)
                    ->orWhereHas('reservedFacilities', function ($sub) use ($facilityIds) {
                        $sub->whereIn('facilities.facility_id', $facilityIds);
                    });
            })
            ->where(function ($q) use ($targetStartDate, $targetEndDate) {
                $q->whereDate('reservation_date', '<=', $targetEndDate)
                    ->where(function ($sub) use ($targetStartDate) {
                        $sub->whereNull('end_date')
                            ->whereDate('reservation_date', '>=', $targetStartDate)
                            ->orWhereDate('end_date', '>=', $targetStartDate);
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
     * Check if any facility belonging to a facility set has an overlapping reservation.
     *
     * @param  array<string>  $statuses
     */
    public function hasSetConflict(
        int $facilitySetId,
        string $date,
        string $start,
        string $end,
        ?int $excludeId = null,
        array $statuses = ['approved'],
        ?string $endDate = null
    ): bool {
        $facilityIds = DB::table('facility_set_facility')
            ->where('facility_set_id', $facilitySetId)
            ->pluck('facility_id')
            ->all();

        if (empty($facilityIds)) {
            return false;
        }

        return $this->hasAnyConflict($facilityIds, $date, $start, $end, $excludeId, $statuses, $endDate);
    }

    /**
     * Retrieve the list of conflicting Facility models for the requested time slot.
     *
     * @param  array<int>  $facilityIds
     * @param  array<string>  $statuses
     * @return Collection<int, Facility>
     */
    public function getConflictedFacilities(
        array $facilityIds,
        string $date,
        string $start,
        string $end,
        ?int $excludeId = null,
        array $statuses = ['approved'],
        ?string $endDate = null
    ): Collection {
        if (empty($facilityIds)) {
            return collect();
        }

        $targetStartDate = Carbon::parse($date)->toDateString();
        $targetEndDate = Carbon::parse($endDate ?: $date)->toDateString();

        $conflictingReservations = FacilityReservation::with(['reservedFacilities', 'facility'])
            ->whereIn('status', $statuses)
            ->where(function ($facQ) use ($facilityIds) {
                $facQ->whereIn('facility_id', $facilityIds)
                    ->orWhereHas('reservedFacilities', function ($sub) use ($facilityIds) {
                        $sub->whereIn('facilities.facility_id', $facilityIds);
                    });
            })
            ->where(function ($q) use ($targetStartDate, $targetEndDate) {
                $q->whereDate('reservation_date', '<=', $targetEndDate)
                    ->where(function ($sub) use ($targetStartDate) {
                        $sub->whereNull('end_date')
                            ->whereDate('reservation_date', '>=', $targetStartDate)
                            ->orWhereDate('end_date', '>=', $targetStartDate);
                    });
            })
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->when($excludeId, fn ($q) => $q->where('reservation_id', '!=', $excludeId))
            ->get();

        if ($conflictingReservations->isEmpty()) {
            return collect();
        }

        $conflictedIds = [];
        foreach ($conflictingReservations as $reservation) {
            $affected = $reservation->all_facilities->pluck('facility_id')->all();
            foreach ($affected as $id) {
                if (in_array($id, $facilityIds, true)) {
                    $conflictedIds[] = $id;
                }
            }
        }

        $conflictedIds = array_unique($conflictedIds);

        return Facility::whereIn('facility_id', $conflictedIds)->get();
    }

    /**
     * Atomically create a facility reservation (individual or consolidated set).
     *
     * Handles:
     * 1. Resolving all member facilities
     * 2. Transaction isolation with row locking on affected facilities
     * 3. Availability verification across ALL member facilities
     * 4. Automatic rollback and exception throwing if any conflict exists
     * 5. Concurrency-safe dynamic reference number generation
     * 6. Associating the reservation with all affected facilities in the pivot table
     *
     * @throws FacilityReservationConflictException
     */
    public function createReservation(
        array $attributes,
        Facility|FacilitySet $item,
        array $statusesToCheck = ['approved']
    ): FacilityReservation {
        return DB::transaction(function () use ($attributes, $item, $statusesToCheck) {
            $isSet = $item instanceof FacilitySet;

            if ($isSet) {
                $facilityIds = $item->facilities()->pluck('facilities.facility_id')->all();
                if (empty($facilityIds)) {
                    throw new \DomainException("The facility set '{$item->name}' does not contain any facilities.");
                }
                $facilityId = null;
                $facilitySetId = $item->facility_set_id;
            } else {
                $facilityIds = [$item->facility_id];
                $facilityId = $item->facility_id;
                $facilitySetId = null;
            }

            // Lock the facility rows to serialize concurrent reservations for the same facility/facilities
            Facility::whereIn('facility_id', $facilityIds)->lockForUpdate()->get();

            $date = $attributes['reservation_date'];
            $endDate = $attributes['end_date'] ?? $date;
            $start = $attributes['start_time'];
            $end = $attributes['end_time'];

            // Check availability across every single affected facility
            $conflicted = $this->getConflictedFacilities(
                $facilityIds,
                $date,
                $start,
                $end,
                null,
                $statusesToCheck,
                $endDate
            );

            if ($conflicted->isNotEmpty()) {
                $message = $isSet
                    ? 'This facility set is unavailable for the selected date and time because one or more included facilities have already been reserved. Please choose another date or time.'
                    : 'The selected facility already has an approved or active reservation that conflicts with this time slot. Please choose another date or time.';

                throw new FacilityReservationConflictException($message, $conflicted);
            }

            // Generate atomic dynamic reference number
            $referenceNumber = app(ReservationReferenceService::class)->generateReference($item, $date);

            $reservationData = array_merge($attributes, [
                'facility_id' => $facilityId,
                'facility_set_id' => $facilitySetId,
                'reference_number' => $referenceNumber,
            ]);

            $reservation = FacilityReservation::create($reservationData);

            // Link all affected facilities to this reservation for traceability and future conflict checking
            $reservation->reservedFacilities()->sync($facilityIds);

            return $reservation;
        });
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

        $activeItem = $reservation->facilitySet ?? $reservation->facility;
        $hourlyRate = $reservation->agreed_rate !== null
            ? (float) $reservation->agreed_rate
            : ($activeItem?->effective_hourly_rate ?? (float) ($activeItem?->rate ?? 0));

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
