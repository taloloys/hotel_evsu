<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
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
        ]);

        $booking->room->update(['status' => 'OCCUPIED']);

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

        $booking->update([
            'actual_check_out' => Carbon::now(),
            'status' => 'CHECKED_OUT',
        ]);

        $booking->room->update(['status' => 'CLEANING']);

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

        return response()->json([
            'success' => true,
            'message' => 'Maintenance complete! Room is now available.',
            'room' => $room,
        ]);
    }
}
