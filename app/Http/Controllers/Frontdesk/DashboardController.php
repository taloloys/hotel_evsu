<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Expense;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\Room;
use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        $dayOfWeek = strtolower(now()->englishDayOfWeek); // e.g., 'monday', 'tuesday'
        $columnName = 'is_'.$dayOfWeek;

        // Available schedules for today
        $todaySchedules = ShiftSchedule::where('user_id', $userId)
            ->where('is_active', true)
            ->where($columnName, true)
            ->get();

        // Shift sales totals
        $shiftSales = [
            'charges' => 0.00,
            'payments' => 0.00,
            'cash' => 0.00,
            'card' => 0.00,
            'expenses' => 0.00,
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
            $shiftSales['expenses'] = Expense::where('user_id', $userId)
                ->where('funding_source', 'FRONT DESK')
                ->where('created_at', '>=', $activeShift->start_time)
                ->sum('amount');
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
                ->with(['folio' => fn ($q) => $q->withBalances()->with('guest')]);
        }])
            ->orderBy('room_number')
            ->get()
            ->groupBy('room_type')
            ->map(function ($rooms) {
                return $rooms->map(function (Room $room) {
                    $activeBooking = $room->bookings->first();
                    $folio = $activeBooking?->folio;
                    $balance = $folio ? (float) $folio->balance : 0.00;
                    $hasUnpaidBalance = $balance > 0.009;

                    return [
                        'room_id' => $room->room_id,
                        'room_number' => $room->room_number,
                        'room_type' => $room->room_type,
                        'status' => $room->status,
                        'active_booking' => $activeBooking ? [
                            'booking_id' => $activeBooking->booking_id,
                            'folio_id' => $activeBooking->folio_id,
                            'balance' => $balance,
                            'has_unpaid_balance' => $hasUnpaidBalance,
                            'guest_name' => trim(
                                ($folio?->guest?->first_name ?? '').' '.
                                ($folio?->guest?->last_name ?? '')
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
            ->limit(30)
            ->get();

        // Today's check-outs (departures — pending and completed)
        $todayCheckOuts = $guestBookingsQuery()
            ->whereDate('departure_date', $today)
            ->whereIn('status', ['CHECKED_IN', 'CHECKED_OUT'])
            ->orderBy('departure_time')
            ->limit(30)
            ->get();

        // Overdue guests: still checked in but departure date has already passed
        $overdueGuests = $guestBookingsQuery()
            ->where('status', 'CHECKED_IN')
            ->whereNotNull('departure_date')
            ->whereDate('departure_date', '<', $today)
            ->orderBy('departure_date')
            ->limit(30)
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
            ->map(function (Room $room) use ($today) {
                $activeBooking = $room->bookings->first();
                $isOverdue = $activeBooking
                    && $activeBooking->departure_date !== null
                    && $activeBooking->departure_date->lt(Carbon::parse($today));

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
                    'is_overdue' => $isOverdue,
                ];
            });

        // Active and upcoming facility reservations for dashboard overview
        $todayFacilityReservations = FacilityReservation::with(['facility', 'facilitySet', 'reservedFacilities'])
            ->whereDate('reservation_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereDate('end_date', '>=', $today)
                    ->orWhere(function ($sub) use ($today) {
                        $sub->whereNull('end_date')
                            ->whereDate('reservation_date', '>=', $today);
                    });
            })
            ->whereIn('status', ['approved', 'active', 'pending'])
            ->orderBy('start_time')
            ->get();

        $upcomingFacilityReservations = FacilityReservation::with(['facility', 'facilitySet'])
            ->whereDate('reservation_date', '>', $today)
            ->where('status', 'approved')
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        $pendingFacilityCount = FacilityReservation::where('status', 'pending')->count();
        $totalFacilities = Facility::where('is_active', true)->count();

        $bookedFacilityIds = collect();
        foreach ($todayFacilityReservations as $res) {
            if ($res->status === 'approved' || $res->status === 'active') {
                if ($res->facility_id) {
                    $bookedFacilityIds->push($res->facility_id);
                }
                foreach ($res->reservedFacilities as $rf) {
                    $bookedFacilityIds->push($rf->facility_id);
                }
            }
        }
        $bookedFacilitiesToday = $bookedFacilityIds->unique()->count();

        $facilitiesList = Facility::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

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
            'overdueGuests' => $overdueGuests,
            'vacantRooms' => $vacantRooms,
            'occupiedRoomList' => $occupiedRoomList,
            'totalRooms' => Room::where('is_active', true)->count(),
            'activeShift' => $activeShift,
            'todaySchedules' => $todaySchedules,
            'shiftSales' => $shiftSales,
            // Facilities
            'facilitiesList' => $facilitiesList,
            'todayFacilityReservations' => $todayFacilityReservations,
            'upcomingFacilityReservations' => $upcomingFacilityReservations,
            'pendingFacilityCount' => $pendingFacilityCount,
            'totalFacilities' => $totalFacilities,
            'bookedFacilitiesToday' => $bookedFacilitiesToday,
        ]);
    }

    /**
     * Get timeline calendar data for front desk room monitoring.
     */
    public function calendarData(Request $request): JsonResponse
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfDay();

        $days = max(3, min(31, (int) $request->input('days', 14)));
        $endDate = $startDate->copy()->addDays($days - 1)->endOfDay();
        $today = Carbon::now()->toDateString();

        // Build list of dates
        $dates = [];
        for ($i = 0; $i < $days; $i++) {
            $current = $startDate->copy()->addDays($i);
            $dateString = $current->toDateString();
            $dates[] = [
                'date' => $dateString,
                'day_name' => $current->format('D'),
                'day_number' => $current->format('j'),
                'month_name' => $current->format('M'),
                'full_formatted' => $current->format('M j, Y'),
                'is_today' => $dateString === $today,
                'is_weekend' => $current->isWeekend(),
            ];
        }

        // Room query
        $roomQuery = Room::where('is_active', true);
        if ($request->filled('room_type') && $request->room_type !== 'ALL') {
            $roomQuery->where('room_type', $request->room_type);
        }

        $rooms = $roomQuery->with(['bookings' => function ($query) use ($startDate, $endDate) {
            $query->whereIn('status', ['RESERVED', 'CHECKED_IN', 'OVERDUE_CHECKIN'])
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->where(function ($sub) use ($startDate, $endDate) {
                        $sub->whereDate('arrival_date', '<=', $endDate->toDateString())
                            ->where(function ($d) use ($startDate) {
                                $d->whereDate('departure_date', '>=', $startDate->toDateString())
                                    ->orWhereNull('departure_date');
                            });
                    })
                        ->orWhere(function ($sub) use ($endDate) {
                            $sub->where('status', 'CHECKED_IN')
                                ->whereDate('arrival_date', '<=', $endDate->toDateString());
                        })
                        ->orWhere(function ($sub) use ($endDate) {
                            $sub->whereIn('status', ['RESERVED', 'OVERDUE_CHECKIN'])
                                ->whereDate('arrival_date', '<=', $endDate->toDateString());
                        });
                })
                ->with(['folio' => fn ($q) => $q->withBalances()->with('guest')]);
        }])
            ->orderBy('room_type')
            ->orderBy('room_number')
            ->get()
            ->map(function (Room $room) use ($today, $endDate) {
                $bookingsData = $room->bookings->map(function (Booking $booking) use ($today, $endDate) {
                    $guest = $booking->folio?->guest;
                    $guestName = $guest ? trim(($guest->first_name ?? '').' '.($guest->last_name ?? '')) : 'Guest';
                    $folio = $booking->folio;
                    $balance = $folio ? (float) $folio->balance : 0.00;
                    $hasUnpaidBalance = $balance > 0.009;

                    $arrivalDateStr = $booking->arrival_date ? $booking->arrival_date->toDateString() : $today;
                    $departureDateStr = $booking->departure_date ? $booking->departure_date->toDateString() : null;
                    $isOverdue = false;
                    $isOpenStay = false;

                    if ($booking->status === 'CHECKED_IN') {
                        if (empty($departureDateStr)) {
                            $isOpenStay = true;
                            $departureDateStr = max($today, $endDate->toDateString());
                        } elseif (Carbon::parse($departureDateStr)->lt(Carbon::parse($today))) {
                            $isOverdue = true;
                            $departureDateStr = $today;
                        }
                    } elseif (in_array($booking->status, ['RESERVED', 'OVERDUE_CHECKIN'], true)) {
                        if (empty($departureDateStr)) {
                            $departureDateStr = $arrivalDateStr;
                        }
                        if (Carbon::parse($arrivalDateStr)->lt(Carbon::parse($today))) {
                            $isOverdue = true;
                        }
                    }

                    return [
                        'booking_id' => $booking->booking_id,
                        'folio_id' => $booking->folio_id,
                        'folio_number' => $booking->folio?->folio_number ?? ('FOL-'.$booking->folio_id),
                        'balance' => $balance,
                        'has_unpaid_balance' => $hasUnpaidBalance,
                        'status' => $booking->status === 'OVERDUE_CHECKIN' ? 'RESERVED' : $booking->status,
                        'arrival_date' => $arrivalDateStr,
                        'departure_date' => $departureDateStr ?: $arrivalDateStr,
                        'original_departure_date' => $booking->departure_date?->toDateString(),
                        'arrival_time' => $booking->arrival_time ? Carbon::parse($booking->arrival_time)->format('g:i A') : '14:00',
                        'departure_time' => $booking->departure_time ? Carbon::parse($booking->departure_time)->format('g:i A') : '12:00',
                        'guest_name' => $guestName ?: 'Guest',
                        'guest_phone' => $guest?->contact_number,
                        'guest_email' => $guest?->email,
                        'net_rate' => $booking->folio?->net_rate ?? $booking->room?->base_rate,
                        'is_overdue' => $isOverdue,
                        'is_open_stay' => $isOpenStay,
                    ];
                });

                if ($bookingsData->isEmpty()) {
                    if ($room->status === 'OCCUPIED') {
                        $bookingsData->push([
                            'booking_id' => null,
                            'folio_id' => null,
                            'folio_number' => '—',
                            'balance' => 0.00,
                            'has_unpaid_balance' => false,
                            'status' => 'CHECKED_IN',
                            'arrival_date' => $today,
                            'departure_date' => $today,
                            'original_departure_date' => null,
                            'arrival_time' => '12:00 PM',
                            'departure_time' => '12:00 PM',
                            'guest_name' => 'In-House Guest',
                            'guest_phone' => null,
                            'guest_email' => null,
                            'net_rate' => $room->base_rate,
                            'is_overdue' => false,
                            'is_open_stay' => true,
                        ]);
                    } elseif ($room->status === 'RESERVED') {
                        $bookingsData->push([
                            'booking_id' => null,
                            'folio_id' => null,
                            'folio_number' => '—',
                            'balance' => 0.00,
                            'has_unpaid_balance' => false,
                            'status' => 'RESERVED',
                            'arrival_date' => $today,
                            'departure_date' => $today,
                            'original_departure_date' => null,
                            'arrival_time' => '12:00 PM',
                            'departure_time' => '12:00 PM',
                            'guest_name' => 'Reserved Guest',
                            'guest_phone' => null,
                            'guest_email' => null,
                            'net_rate' => $room->base_rate,
                            'is_overdue' => false,
                            'is_open_stay' => false,
                        ]);
                    }
                }

                return [
                    'room_id' => $room->room_id,
                    'room_number' => $room->room_number,
                    'room_type' => $room->room_type,
                    'floor' => $room->floor,
                    'current_status' => $room->status,
                    'base_rate' => (float) $room->base_rate,
                    'bookings' => $bookingsData->values()->all(),
                ];
            });

        $allRoomTypes = Room::where('is_active', true)->distinct()->pluck('room_type')->values();

        // Summary counts for today
        $summary = [
            'total' => Room::where('is_active', true)->count(),
            'available' => Room::where('is_active', true)->where('status', 'AVAILABLE')->count(),
            'occupied' => Room::where('is_active', true)->where('status', 'OCCUPIED')->count(),
            'reserved' => Room::where('is_active', true)->where('status', 'RESERVED')->count(),
            'cleaning' => Room::where('is_active', true)->where('status', 'CLEANING')->count(),
            'maintenance' => Room::where('is_active', true)->where('status', 'MAINTENANCE')->count(),
        ];

        return response()->json([
            'range' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'days' => $days,
                'display' => $startDate->format('M j, Y').' — '.$endDate->format('M j, Y'),
            ],
            'dates' => $dates,
            'rooms' => $rooms,
            'room_types' => $allRoomTypes,
            'summary' => $summary,
        ]);
    }
}
