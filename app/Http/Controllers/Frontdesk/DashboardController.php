<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::now()->toDateString();
        $userId = auth()->id();

        // Active shift
        $activeShift = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->with('schedule')
            ->first();

        // Available schedules for today that haven't started yet
        $todaySchedules = ShiftSchedule::where('user_id', $userId)
            ->whereDate('shift_date', $today)
            ->where('status', 'SCHEDULED')
            ->get();

        // Shift sales totals
        $shiftSales = [
            'charges' => 0.00,
            'payments' => 0.00,
            'cash' => 0.00,
            'card' => 0.00,
        ];

        if ($activeShift) {
            $shiftSales['charges'] = Transaction::where('shift_id', $activeShift->shift_id)
                ->sum('charge_amount');
            $shiftSales['payments'] = Transaction::where('shift_id', $activeShift->shift_id)
                ->sum('credit_amount');
            $shiftSales['cash'] = Transaction::where('shift_id', $activeShift->shift_id)
                ->where('payment_method', 'CASH')
                ->sum('credit_amount');
            $shiftSales['card'] = Transaction::where('shift_id', $activeShift->shift_id)
                ->where('payment_method', 'CREDIT_CARD')
                ->sum('credit_amount');
        }

        // Get today's arrivals
        $todayArrivals = Booking::whereDate('arrival_date', $today)
            ->whereIn('status', ['RESERVED'])
            ->count();

        // Get today's departures
        $todayDepartures = Booking::whereDate('departure_date', $today)
            ->whereIn('status', ['CHECKED_IN'])
            ->count();

        // Get room status counts
        $occupiedRooms = Room::where('is_active', true)->where('status', 'OCCUPIED')->count();
        $availableRooms = Room::where('is_active', true)->where('status', 'AVAILABLE')->count();
        $needsCleaningRooms = Room::where('is_active', true)->where('status', 'CLEANING')->count();
        $maintenanceRooms = Room::where('is_active', true)->where('status', 'MAINTENANCE')->count();

        // Get all rooms grouped by room type with active booking info
        $roomsByType = Room::where('is_active', true)->with(['bookings' => function ($query) {
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

        $guestBookingsQuery = fn () => Booking::with(['folio.guest', 'room'])
            ->whereHas('folio', fn ($query) => $query->whereNotNull('guest_id'));

        // Today's check-ins and reservations (arrivals only)
        $todayCheckIns = $guestBookingsQuery()
            ->whereDate('arrival_date', $today)
            ->whereIn('status', ['RESERVED', 'CHECKED_IN'])
            ->orderBy('arrival_time')
            ->get();

        // Today's check-outs (departures — pending and completed)
        $todayCheckOuts = $guestBookingsQuery()
            ->whereDate('departure_date', $today)
            ->whereIn('status', ['CHECKED_IN', 'CHECKED_OUT'])
            ->orderBy('departure_time')
            ->get();

        // Rooms ready for guests: available status with no active reservation or occupancy
        $vacantRooms = Room::query()
            ->where('is_active', true)
            ->where('status', 'AVAILABLE')
            ->whereDoesntHave('bookings', function ($query) use ($today) {
                $query->whereIn('status', ['RESERVED', 'CHECKED_IN'])
                    ->whereDate('departure_date', '>=', $today);
            })
            ->orderBy('room_type')
            ->orderBy('room_number')
            ->get();

        $occupiedRoomList = Room::query()
            ->where('is_active', true)
            ->where('status', 'OCCUPIED')
            ->with(['bookings' => function ($query) {
                $query->where('status', 'CHECKED_IN')
                    ->with(['folio.guest']);
            }])
            ->orderBy('room_type')
            ->orderBy('room_number')
            ->get()
            ->map(function (Room $room) {
                $activeBooking = $room->bookings->first();

                return [
                    'room_id' => $room->room_id,
                    'room_number' => $room->room_number,
                    'room_type' => $room->room_type,
                    'guest_name' => $activeBooking
                        ? trim(
                            ($activeBooking->folio?->guest?->first_name ?? '').' '.
                            ($activeBooking->folio?->guest?->last_name ?? '')
                        )
                        : null,
                    'folio_number' => $activeBooking?->folio?->folio_number,
                    'departure_date' => $activeBooking?->departure_date,
                ];
            });

        return view('frontdesk.dashboard.index', [
            'todayArrivals' => $todayArrivals,
            'todayDepartures' => $todayDepartures,
            'occupiedRooms' => $occupiedRooms,
            'availableRooms' => $availableRooms,
            'needsCleaningRooms' => $needsCleaningRooms,
            'maintenanceRooms' => $maintenanceRooms,
            'roomsByType' => $roomsByType,
            'todayCheckIns' => $todayCheckIns,
            'todayCheckOuts' => $todayCheckOuts,
            'vacantRooms' => $vacantRooms,
            'occupiedRoomList' => $occupiedRoomList,
            'totalRooms' => Room::where('is_active', true)->count(),
            'activeShift' => $activeShift,
            'todaySchedules' => $todaySchedules,
            'shiftSales' => $shiftSales,
        ]);
    }
}
