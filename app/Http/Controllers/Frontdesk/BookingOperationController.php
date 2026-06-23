<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingOperationController extends Controller
{
    /**
     * Check in a guest
     */
    public function checkIn(Request $request): JsonResponse
    {
        $request->validate([
            'booking_id' => ['required', 'exists:bookings,booking_id'],
        ]);

        $booking = Booking::with('room')->findOrFail($request->booking_id);

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

        if ($booking->room->status !== 'AVAILABLE') {
            return response()->json([
                'success' => false,
                'message' => 'Room must be available before check-in.',
            ], 422);
        }

        $booking->update([
            'actual_check_in' => Carbon::now(),
            'status' => 'CHECKED_IN',
            'checked_in_by' => auth()->id(),
        ]);

        $booking->room->update(['status' => 'OCCUPIED']);

        $booking->load('folio.guest');
        $guestName = $booking->folio?->guest ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name) : 'Guest';
        $roomNumber = $booking->room?->room_number ?? 'N/A';
        ActivityLog::log(
            'CHECK_IN',
            "Checked in guest {$guestName} to Room {$roomNumber} (Booking #{$booking->booking_id})."
        );

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

        $booking = Booking::with('room')->findOrFail($request->booking_id);

        if ($booking->status !== 'CHECKED_IN') {
            return response()->json([
                'success' => false,
                'message' => 'Only checked-in guests can be checked out.',
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

        $booking->update([
            'actual_check_in' => $booking->actual_check_in, // preserve check-in time
            'actual_check_out' => $actualCheckOut,
            'status' => 'CHECKED_OUT',
        ]);

        $booking->room->update(['status' => 'CLEANING']);

        $booking->load('folio.guest');
        $guestName = $booking->folio?->guest ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name) : 'Guest';
        $roomNumber = $booking->room?->room_number ?? 'N/A';
        ActivityLog::log(
            'CHECK_IN',
            "Checked out guest {$guestName} from Room {$roomNumber} (Booking #{$booking->booking_id})."
        );

        return response()->json([
            'success' => true,
            'message' => 'Guest checked out! Room sent to housekeeping for cleaning.',
            'booking' => $booking,
        ]);
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

        $room->update(['status' => 'AVAILABLE']);

        ActivityLog::log(
            'ROOM_MODIFIED',
            "Room {$room->room_number} status updated to AVAILABLE (Housekeeping Cleaned)."
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
