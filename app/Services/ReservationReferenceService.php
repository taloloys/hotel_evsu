<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\FacilitySet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationReferenceService
{
    protected const DEFAULT_BASE_PREFIX = 'EVSUOC';

    /**
     * Generate or resolve a deterministic prefix for a facility or facility set.
     */
    public function generatePrefix(Facility|FacilitySet|string|null $item): string
    {
        if ($item instanceof Facility || $item instanceof FacilitySet) {
            if (! empty($item->prefix_code)) {
                return $this->sanitizePrefix($item->prefix_code);
            }
            $name = $item->name ?? '';
        } else {
            $name = (string) $item;
        }

        return $this->generatePrefixFromName($name);
    }

    /**
     * Generate a prefix from a facility / facility-set name.
     * E.g. "Function Hall" -> "EVSUOCFH"
     * "Grand Function Hall" -> "EVSUOCGFH"
     */
    public function generatePrefixFromName(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return self::DEFAULT_BASE_PREFIX.'FAC';
        }

        // Split into words, ignoring special chars
        $words = preg_split('/[\s\-_]+/', $name, -1, PREG_SPLIT_NO_EMPTY);

        if (count($words) >= 2) {
            $initials = '';
            foreach ($words as $word) {
                // Grab first alphanumeric char of word
                if (preg_match('/[a-zA-Z0-9]/', $word, $matches)) {
                    $initials .= strtoupper($matches[0]);
                }
            }
        } else {
            // Single word: use up to first 4 alphanumeric characters
            $clean = preg_replace('/[^a-zA-Z0-9]/', '', $words[0]);
            $initials = strtoupper(substr($clean, 0, 4));
        }

        if (empty($initials)) {
            $initials = 'FAC';
        }

        return $this->sanitizePrefix(self::DEFAULT_BASE_PREFIX.$initials);
    }

    /**
     * Clean and format prefix string (uppercase alphanumeric only).
     */
    protected function sanitizePrefix(string $prefix): string
    {
        $clean = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $prefix));

        return ! empty($clean) ? $clean : self::DEFAULT_BASE_PREFIX.'FAC';
    }

    /**
     * Concurrency-safe dynamic reservation reference number generation.
     *
     * Format: #[PREFIX]-[YYYYMMDD]-[SEQUENCE]
     * E.g. #EVSUOCFH-20261001-001
     */
    public function generateReference(Facility|FacilitySet|string $facilityOrSet, Carbon|string $reservationDate): string
    {
        $prefix = $this->generatePrefix($facilityOrSet);
        $dateCarbon = $reservationDate instanceof Carbon ? $reservationDate : Carbon::parse($reservationDate);
        $formattedDate = $dateCarbon->format('Y-m-d');
        $dateCode = $dateCarbon->format('Ymd');

        return DB::transaction(function () use ($prefix, $formattedDate, $dateCode) {
            // Ensure sequence tracking record exists
            DB::table('facility_reservation_sequences')->insertOrIgnore([
                'prefix' => $prefix,
                'reservation_date' => $formattedDate,
                'last_sequence' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Lock the sequence row for update to ensure concurrency safety
            $sequenceRow = DB::table('facility_reservation_sequences')
                ->where('prefix', $prefix)
                ->where('reservation_date', $formattedDate)
                ->lockForUpdate()
                ->first();

            $nextSequence = ($sequenceRow?->last_sequence ?? 0) + 1;

            // Collision safety loop against existing records
            do {
                $paddedSeq = str_pad((string) $nextSequence, 3, '0', STR_PAD_LEFT);
                $reference = "#{$prefix}-{$dateCode}-{$paddedSeq}";

                $exists = DB::table('facility_reservations')
                    ->where('reference_number', $reference)
                    ->exists();

                if ($exists) {
                    $nextSequence++;
                }
            } while ($exists);

            // Update stored sequence
            DB::table('facility_reservation_sequences')
                ->where('prefix', $prefix)
                ->where('reservation_date', $formattedDate)
                ->update([
                    'last_sequence' => $nextSequence,
                    'updated_at' => now(),
                ]);

            return $reference;
        });
    }

    /**
     * Generate dynamic reference for a given FacilityReservation instance before creation.
     */
    public function generateReferenceForReservation(FacilityReservation $reservation): string
    {
        $target = $reservation->facilitySet ?? $reservation->facility;
        if (! $target) {
            if ($reservation->facility_set_id) {
                $target = FacilitySet::find($reservation->facility_set_id);
            } elseif ($reservation->facility_id) {
                $target = Facility::find($reservation->facility_id);
            }
        }

        $date = $reservation->reservation_date ?: now();

        return $this->generateReference($target ?? 'FACILITY', $date);
    }
}
