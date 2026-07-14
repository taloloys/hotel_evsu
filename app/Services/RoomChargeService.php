<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ChargeCode;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RoomChargeService
{
    /**
     * Process catch-up room charges for all active check-ins, or a specific booking.
     */
    public function processCatchUpCharges(?int $bookingId = null): void
    {
        $query = Booking::with(['room', 'folio'])->where('status', 'CHECKED_IN');

        if ($bookingId) {
            $query->where('booking_id', $bookingId);
        }

        $bookings = $query->get();
        if ($bookings->isEmpty()) {
            return;
        }

        // Defensive charge code verification/creation
        ChargeCode::firstOrCreate(
            ['charge_code' => 100],
            [
                'description' => 'ROOM CHARGE',
                'category' => 'HOTEL',
                'is_active' => true,
            ]
        );

        $userId = auth()->id() ?: (User::where('username', 'system')->value('user_id') ?: (User::first()?->user_id ?: 1));

        $activeShift = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        if (! $activeShift) {
            $activeShift = Shift::orderBy('shift_id', 'desc')->first()
                ?? Shift::create(['user_id' => $userId, 'start_time' => Carbon::now()]);
        }

        $today = Carbon::today();

        DB::transaction(function () use ($bookings, $userId, $activeShift, $today) {
            foreach ($bookings as $booking) {
                $this->chargeBooking($booking, $userId, $activeShift, $today);
            }
        });
    }

    private function chargeBooking(Booking $booking, int $userId, Shift $activeShift, Carbon $today): void
    {
        $arrival = $booking->arrival_date;
        if (! $arrival) {
            return;
        }

        // Calculate nights between arrival date and today.
        // Add 1 so the current active night is immediately charged up-front.
        $nightsPassed = (int) $arrival->diffInDays($today);
        $nightsToCharge = $nightsPassed + 1;

        // For fixed-stay bookings, charge all nights from arrival to departure up-front.
        if ($booking->departure_date) {
            $totalNights = $arrival->diffInDays($booking->departure_date);
            $nightsToCharge = max($nightsToCharge, $totalNights);
        } elseif ($nightsPassed === 0) {
            // Open stay on arrival day: skip charging; daily command handles it.
            return;
        }

        $folio = $booking->folio;
        $rate = ($folio && $folio->net_rate !== null) ? $folio->net_rate : ($booking->room?->base_rate ?? 0.00);

        $totalCharged = 0.00;
        $newChargesCount = 0;

        for ($i = 0; $i < $nightsToCharge; $i++) {
            $chargeDate = $arrival->copy()->addDays($i)->toDateString();
            $chargeNo = 'RM-'.$booking->booking_id.'-'.($i + 1);

            $exists = Transaction::where('folio_id', $booking->folio_id)
                ->where('charge_number', $chargeNo)
                ->exists();

            if (! $exists) {
                Transaction::create([
                    'folio_id' => $booking->folio_id,
                    'charge_code' => 100, // ROOM CHARGE
                    'shift_id' => $activeShift->shift_id,
                    'user_id' => $userId,
                    'transaction_date' => $chargeDate,
                    'charge_number' => $chargeNo,
                    'payment_method' => 'NONE',
                    'reference_notes' => 'Room charge for Night '.($i + 1)." (Date: {$chargeDate})",
                    'charge_amount' => $rate,
                    'credit_amount' => 0.00,
                ]);
                $totalCharged += $rate;
                $newChargesCount++;
            }
        }

        if ($newChargesCount > 0) {
            ActivityLog::log(
                'ADD_CHARGE',
                "Automatically posted {$newChargesCount} nights of room charges totaling ₱".number_format($totalCharged, 2).' on Folio #'.($folio->folio_number ?? 'N/A')." (Booking #{$booking->booking_id})."
            );
        }
    }
}
