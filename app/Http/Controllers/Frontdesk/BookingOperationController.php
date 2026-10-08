<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Mail\CheckInConfirmationMail;
use App\Mail\FolioBillingMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Transaction;
use App\Services\ChargeCodeResolver;
use App\Services\EmailRecipientResolver;
use App\Services\RoomChargeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingOperationController extends Controller
{
    /**
     * Check in a guest
     */
    public function checkIn(Request $request): JsonResponse
    {
        $request->validate([
            'booking_id' => ['required', 'exists:bookings,booking_id'],
            'net_rate' => ['nullable', 'numeric', 'min:0'],
        ]);

        $booking = Booking::with(['room', 'folio'])->findOrFail($request->booking_id);

        if ($booking->status !== 'RESERVED') {
            return response()->json([
                'success' => false,
                'message' => 'Only reserved bookings can be checked in.',
            ], 422);
        }

        if (! $booking->room) {
            return response()->json([
                'success' => false,
                'message' => 'Booking has no assigned room.',
            ], 422);
        }

        if (! in_array($booking->room->status, ['AVAILABLE', 'RESERVED'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Room must be available or reserved before check-in.',
            ], 422);
        }

        DB::transaction(function () use ($booking, $request) {
            $booking->update([
                'actual_check_in' => Carbon::now(),
                'status' => 'CHECKED_IN',
            ]);

            if ($booking->room) {
                $booking->room->update(['status' => 'OCCUPIED']);
            }

            if ($booking->folio) {
                $rate = $request->filled('net_rate')
                    ? (float) $request->net_rate
                    : ($booking->folio->net_rate ?? $booking->room?->base_rate);

                $booking->folio->update(['net_rate' => $rate]);
            }
        });

        $booking->load('folio.guest');
        $guestName = $booking->folio?->guest ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name) : 'Guest';
        $roomNumber = $booking->room?->room_number ?? 'N/A';

        // Post room charges night-by-night automatically
        app(RoomChargeService::class)->processCatchUpCharges($booking->booking_id);

        ActivityLog::log(
            'CHECK_IN',
            "Checked in guest {$guestName} to Room {$roomNumber} (Booking #{$booking->booking_id})."
        );

        try {
            $booking->load(['folio.guest', 'room']);
            $recipients = app(EmailRecipientResolver::class)->resolve('checkin', $booking);
            if (! empty($recipients)) {
                Mail::to($recipients)->queue(new CheckInConfirmationMail($booking));
            }
        } catch (\Throwable $e) {
            // Log or ignore email dispatch failures gracefully
        }

        return response()->json([
            'success' => true,
            'message' => 'Guest checked in successfully!',
            'booking' => $booking,
        ]);
    }

    /**
     * Check out a guest
     */
    public function checkOut(Request $request): JsonResponse
    {
        $request->validate([
            'booking_id' => ['required', 'exists:bookings,booking_id'],
            'checkout_time' => ['required', 'regex:/^(0?[1-9]|1[0-2]):[0-5][0-9]$/'],
            'checkout_period' => ['required', 'in:AM,PM'],
        ]);

        $booking = Booking::with(['room', 'folio'])->findOrFail($request->booking_id);

        if ($booking->status !== 'CHECKED_IN') {
            return response()->json([
                'success' => false,
                'message' => 'Only checked-in guests can be checked out.',
            ], 422);
        }

        if ($booking->folio && ! $booking->folio->isSettled()) {
            $balance = $booking->folio->balance;
            $message = $balance > 0
                ? 'Cannot check out guest. Folio has an outstanding balance of ₱'.number_format($balance, 2).'.'
                : 'Cannot check out guest. Folio has an overpayment of ₱'.number_format(abs($balance), 2).'. Please refund it first.';

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        if (! $booking->room) {
            return response()->json([
                'success' => false,
                'message' => 'Booking has no assigned room.',
            ], 422);
        }

        $actualCheckOut = Carbon::createFromFormat(
            'Y-m-d g:i A',
            Carbon::today()->format('Y-m-d').' '.$request->checkout_time.' '.$request->checkout_period
        );

        DB::transaction(function () use ($booking, $actualCheckOut) {
            $booking->update([
                'actual_check_in' => $booking->actual_check_in, // preserve check-in time
                'actual_check_out' => $actualCheckOut,
                'status' => 'CHECKED_OUT',
            ]);

            if ($booking->room) {
                $booking->room->update(['status' => 'CLEANING']);
            }

            if ($booking->folio) {
                $booking->folio->update(['status' => 'CLOSED']);
            }
        });

        $booking->load('folio.guest');
        $guestName = $booking->folio?->guest ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name) : 'Guest';
        $roomNumber = $booking->room?->room_number ?? 'N/A';
        ActivityLog::log(
            'CHECK_OUT',
            "Checked out guest {$guestName} from Room {$roomNumber} (Booking #{$booking->booking_id})."
        );

        try {
            if ($booking->folio) {
                $recipients = app(EmailRecipientResolver::class)->resolve('folio', $booking->folio);
                if (! empty($recipients)) {
                    Mail::to($recipients)->queue(new FolioBillingMail($booking->folio));
                }
            }
        } catch (\Throwable $e) {
            // Log or ignore email dispatch failures gracefully
        }

        return response()->json([
            'success' => true,
            'message' => 'Guest checked out! Room sent to housekeeping for cleaning.',
            'booking' => $booking,
        ]);
    }

    /**
     * Extend a booking's departure date and time (for reserved or checked-in guests).
     */
    public function extend(Request $request): JsonResponse
    {
        $request->validate([
            'booking_id' => ['required', 'exists:bookings,booking_id'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'departure_time' => ['nullable', 'date_format:H:i'],
            'net_rate' => ['nullable', 'numeric', 'min:0'],
        ]);

        $booking = Booking::with(['room', 'folio.guest'])->findOrFail($request->booking_id);

        if (! in_array($booking->status, ['RESERVED', 'CHECKED_IN'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Only reserved or checked-in bookings can be extended.',
            ], 422);
        }

        $newDepartureDate = Carbon::parse($request->departure_date)->startOfDay();
        $arrivalDate = $booking->arrival_date ? $booking->arrival_date->copy()->startOfDay() : null;

        if ($arrivalDate && $newDepartureDate->lte($arrivalDate)) {
            return response()->json([
                'success' => false,
                'message' => 'New departure date must be after the arrival date ('.$booking->arrival_date->format('m/d/Y').').',
            ], 422);
        }

        $today = Carbon::today();
        if ($booking->departure_date) {
            $currentDeparture = $booking->departure_date->copy()->startOfDay();
            if ($currentDeparture->gte($today) && $newDepartureDate->lte($currentDeparture)) {
                return response()->json([
                    'success' => false,
                    'message' => 'New departure date must be after the current departure date ('.$booking->departure_date->format('m/d/Y').').',
                ], 422);
            }
        }

        $extensionStart = $booking->departure_date
            ? $booking->departure_date->toDateString()
            : ($booking->arrival_date ? $booking->arrival_date->toDateString() : $today->toDateString());

        $newDepartureString = $newDepartureDate->toDateString();

        if ($booking->room_id && $this->roomHasConflictExcluding($booking->room_id, $booking->booking_id, $extensionStart, $newDepartureString)) {
            $roomNumber = $booking->room?->room_number ?? 'assigned room';

            return response()->json([
                'success' => false,
                'message' => "Room {$roomNumber} is not available for the requested extension period.",
            ], 422);
        }

        $departureTime = $request->departure_time ?? $booking->departure_time ?? '12:00';

        DB::transaction(function () use ($booking, $newDepartureString, $departureTime, $request) {
            $booking->update([
                'departure_date' => $newDepartureString,
                'departure_time' => $departureTime,
            ]);

            if ($booking->folio && $request->filled('net_rate')) {
                $rate = (float) $request->net_rate;
                $booking->folio->update(['net_rate' => $rate]);

                if ($booking->status === 'CHECKED_IN') {
                    $roomChargeCode = ChargeCodeResolver::resolve(ChargeCodeResolver::ROOM_CHARGE);
                    if ($roomChargeCode) {
                        Transaction::where('folio_id', $booking->folio_id)
                            ->where('charge_code', $roomChargeCode)
                            ->where('charge_number', 'like', 'RM-'.$booking->booking_id.'-%')
                            ->update(['charge_amount' => $rate]);
                    }
                }
            }

            if ($booking->status === 'CHECKED_IN') {
                app(RoomChargeService::class)->processCatchUpCharges($booking->booking_id);
            }
        });

        $booking->load(['room', 'folio.guest']);
        $guestName = $booking->folio?->guest
            ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name)
            : 'Guest';
        $roomNumber = $booking->room?->room_number ?? 'N/A';
        $formattedDate = $newDepartureDate->format('m/d/Y');
        $formattedTime = Carbon::parse($departureTime)->format('g:i A');

        ActivityLog::log(
            'STAY_EXTENDED',
            "Extended departure for {$guestName} (Room {$roomNumber}) to {$formattedDate} {$formattedTime} (Booking #{$booking->booking_id})."
        );

        return response()->json([
            'success' => true,
            'message' => "Departure extended successfully to {$formattedDate} at {$formattedTime}!",
            'booking' => $booking,
        ]);
    }

    /**
     * Move a booking's arrival and departure dates.
     */
    public function moveDate(Request $request): JsonResponse
    {
        $request->validate([
            'booking_id' => ['required', 'exists:bookings,booking_id'],
            'arrival_date' => ['required', 'date', 'after_or_equal:today'],
            'arrival_time' => ['nullable', 'date_format:H:i'],
            'departure_date' => ['required', 'date', 'after:arrival_date'],
            'departure_time' => ['nullable', 'date_format:H:i'],
        ]);

        $booking = Booking::with(['room', 'folio.guest'])->findOrFail($request->booking_id);

        if (! in_array($booking->status, ['RESERVED', 'CHECKED_IN'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Only reserved or checked-in bookings can be moved.',
            ], 422);
        }

        if ($booking->status === 'CHECKED_IN') {
            $newArrivalDate = Carbon::parse($request->arrival_date)->startOfDay();
            $originalArrivalDate = $booking->arrival_date ? $booking->arrival_date->copy()->startOfDay() : null;

            if ($originalArrivalDate && $newArrivalDate->notEqualTo($originalArrivalDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot change the arrival date of a guest who is already checked in.',
                ], 422);
            }
        }

        $newArrivalString = Carbon::parse($request->arrival_date)->toDateString();
        $newDepartureString = Carbon::parse($request->departure_date)->toDateString();

        if ($booking->room_id && $this->roomHasConflictExcluding($booking->room_id, $booking->booking_id, $newArrivalString, $newDepartureString)) {
            $roomNumber = $booking->room?->room_number ?? 'assigned room';

            return response()->json([
                'success' => false,
                'message' => "Room {$roomNumber} is not available for the requested dates.",
            ], 422);
        }

        $arrivalTime = $request->arrival_time ?? $booking->arrival_time ?? '14:00';
        $departureTime = $request->departure_time ?? $booking->departure_time ?? '12:00';

        DB::transaction(function () use ($booking, $newArrivalString, $newDepartureString, $arrivalTime, $departureTime) {
            $booking->update([
                'arrival_date' => $newArrivalString,
                'arrival_time' => $arrivalTime,
                'departure_date' => $newDepartureString,
                'departure_time' => $departureTime,
            ]);

            if ($booking->status === 'CHECKED_IN') {
                app(RoomChargeService::class)->processCatchUpCharges($booking->booking_id);
            }
        });

        $booking->load(['room', 'folio.guest']);
        $guestName = $booking->folio?->guest
            ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name)
            : 'Guest';

        return response()->json([
            'success' => true,
            'message' => "Reservation for {$guestName} successfully moved to {$newArrivalString} - {$newDepartureString}.",
            'booking' => $booking,
        ]);
    }

    /**
     * Check room availability for an extension period, excluding the current booking.
     */
    private function roomHasConflictExcluding(
        int $roomId,
        int $excludeBookingId,
        string $fromDate,
        string $toDate
    ): bool {
        return Booking::query()
            ->where('room_id', $roomId)
            ->where('booking_id', '!=', $excludeBookingId)
            ->whereIn('status', ['RESERVED', 'CHECKED_IN'])
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->where(function ($specificStayQuery) use ($fromDate, $toDate) {
                    $specificStayQuery->whereNotNull('departure_date')
                        ->whereDate('arrival_date', '<', $toDate)
                        ->whereDate('departure_date', '>', $fromDate);
                })->orWhere(function ($openStayQuery) use ($toDate) {
                    $openStayQuery->whereNull('departure_date')
                        ->whereDate('arrival_date', '<', $toDate);
                });
            })
            ->exists();
    }

    /**
     * Mark a room as cleaned after housekeeping
     */
    public function markCleaned(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => ['required', 'exists:rooms,room_id'],
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($room->status !== 'CLEANING') {
            return response()->json([
                'success' => false,
                'message' => 'Only rooms awaiting cleaning can be marked as cleaned.',
            ], 422);
        }

        $hasPendingReservationToday = $room->bookings()
            ->where('status', 'RESERVED')
            ->whereDate('arrival_date', '<=', Carbon::today())
            ->exists();

        $targetStatus = $hasPendingReservationToday ? 'RESERVED' : 'AVAILABLE';
        $room->update(['status' => $targetStatus]);

        ActivityLog::log(
            'ROOM_MODIFIED',
            "Room {$room->room_number} status updated to {$targetStatus} (Housekeeping Cleaned)."
        );

        return response()->json([
            'success' => true,
            'message' => 'Room cleaned and is now available!',
            'room' => $room,
        ]);
    }

    /**
     * Mark an available room as needing cleaning
     */
    public function markForCleaning(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => ['required', 'exists:rooms,room_id'],
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($room->status !== 'AVAILABLE') {
            return response()->json([
                'success' => false,
                'message' => 'Only available rooms can be sent for cleaning.',
            ], 422);
        }

        $room->update(['status' => 'CLEANING']);

        ActivityLog::log(
            'ROOM_MODIFIED',
            "Room {$room->room_number} status updated to CLEANING."
        );

        return response()->json([
            'success' => true,
            'message' => 'Room marked for cleaning.',
            'room' => $room,
        ]);
    }

    /**
     * Mark an available room as under maintenance (repairs)
     */
    public function markForMaintenance(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => ['required', 'exists:rooms,room_id'],
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($room->status !== 'AVAILABLE') {
            return response()->json([
                'success' => false,
                'message' => 'Only available rooms can be put under maintenance.',
            ], 422);
        }

        $room->update(['status' => 'MAINTENANCE']);

        ActivityLog::log(
            'ROOM_MODIFIED',
            "Room {$room->room_number} status updated to MAINTENANCE (Out of Order)."
        );

        return response()->json([
            'success' => true,
            'message' => 'Room marked as under maintenance (out of order).',
            'room' => $room,
        ]);
    }

    /**
     * Mark maintenance work as complete
     */
    public function markMaintenanceComplete(Request $request): JsonResponse
    {
        $request->validate([
            'room_id' => ['required', 'exists:rooms,room_id'],
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($room->status !== 'MAINTENANCE') {
            return response()->json([
                'success' => false,
                'message' => 'Only rooms under maintenance can be marked as ready.',
            ], 422);
        }

        $room->update(['status' => 'AVAILABLE']);

        ActivityLog::log(
            'ROOM_MODIFIED',
            "Room {$room->room_number} status updated to AVAILABLE (Maintenance Complete)."
        );

        return response()->json([
            'success' => true,
            'message' => 'Maintenance complete! Room is now available.',
            'room' => $room,
        ]);
    }
}
