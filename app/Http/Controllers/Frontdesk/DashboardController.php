<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::now()->toDateString();

        // Get today's arrivals
        $todayArrivals = Booking::whereDate('arrival_date', $today)
            ->whereIn('status', ['RESERVED'])
            ->count();

        // Get today's departures
        $todayDepartures = Booking::whereDate('departure_date', $today)
            ->whereIn('status', ['CHECKED_IN'])
            ->count();

        // Get room status counts
        $occupiedRooms = Room::where('status', 'OCCUPIED')->count();
        $availableRooms = Room::where('status', 'AVAILABLE')->count();
        $needsCleaningRooms = Room::where('status', 'CLEANING')->count();
        $maintenanceRooms = Room::where('status', 'MAINTENANCE')->count();

        // Get all rooms grouped by room type with active booking info
        $roomsByType = Room::with(['bookings' => function ($query) {
            $query->where('status', 'CHECKED_IN')
                ->with(['folio.guest']);
        }])
            ->orderBy('room_number')
            ->get()
            ->groupBy('room_type')
            ->map(function ($rooms) {
                return $rooms->map(function (Room $room) {
                    $activeBooking = $room->bookings->first();

                    return [
                        'room_id' => $room->room_id,
                        'room_number' => $room->room_number,
                        'room_type' => $room->room_type,
                        'status' => $room->status,
                        'active_booking' => $activeBooking ? [
                            'booking_id' => $activeBooking->booking_id,
                            'guest_name' => trim(
                                ($activeBooking->folio?->guest?->first_name ?? '').' '.
                                ($activeBooking->folio?->guest?->last_name ?? '')
                            ),
                        ] : null,
                    ];
                })->values()->all();
            })
            ->toArray();

        // Get today's bookings (arrivals + departures)
        $todayBookings = Booking::with(['folio.guest', 'room'])
            ->where(function ($query) use ($today) {
                $query->whereDate('arrival_date', $today)
                    ->orWhereDate('departure_date', $today);
            })
            ->whereHas('folio', function ($query) {
                $query->whereNotNull('guest_id');
            })
            ->orderBy('arrival_date')
            ->get();

        return view('frontdesk.dashboard.index', [
            'todayArrivals' => $todayArrivals,
            'todayDepartures' => $todayDepartures,
            'occupiedRooms' => $occupiedRooms,
            'availableRooms' => $availableRooms,
            'needsCleaningRooms' => $needsCleaningRooms,
            'maintenanceRooms' => $maintenanceRooms,
            'roomsByType' => $roomsByType,
            'todayBookings' => $todayBookings,
            'totalRooms' => Room::count(),
        ]);
    }
}
