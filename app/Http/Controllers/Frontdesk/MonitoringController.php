<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Display the Hotel Room & Facility Monitoring dashboard.
     */
    public function index(Request $request): View
    {
        $today = Carbon::now()->toDateString();

        // Room status counts
        $occupiedRooms = Room::where('is_active', true)->where('status', 'OCCUPIED')->count();
        $availableRooms = Room::where('is_active', true)->where('status', 'AVAILABLE')->count();
        $needsCleaningRooms = Room::where('is_active', true)->where('status', 'CLEANING')->count();
        $maintenanceRooms = Room::where('is_active', true)->where('status', 'MAINTENANCE')->count();
        $todayArrivals = Booking::whereDate('arrival_date', $today)
            ->whereIn('status', ['RESERVED'])
            ->count();
        $totalRooms = Room::where('is_active', true)->count();

        // Rooms grouped by room type with active booking info for Grid view
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

        // Facility counts and overview
        $totalFacilities = Facility::where('is_active', true)->count();
        $pendingFacilityCount = FacilityReservation::where('status', 'pending')->count();

        $todayFacilityReservations = FacilityReservation::with(['facility', 'reservedFacilities', 'facilitySet'])
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
            ->with(['facilitySets'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('frontdesk.monitoring.index', [
            'availableRooms' => $availableRooms,
            'occupiedRooms' => $occupiedRooms,
            'todayArrivals' => $todayArrivals,
            'needsCleaningRooms' => $needsCleaningRooms,
            'maintenanceRooms' => $maintenanceRooms,
            'totalRooms' => $totalRooms,
            'roomsByType' => $roomsByType,
            // Facilities
            'facilitiesList' => $facilitiesList,
            'todayFacilityReservations' => $todayFacilityReservations,
            'pendingFacilityCount' => $pendingFacilityCount,
            'totalFacilities' => $totalFacilities,
            'bookedFacilitiesToday' => $bookedFacilitiesToday,
            'initialPanel' => $request->query('panel'),
        ]);
    }

    /**
     * Get room timeline calendar data for front desk monitoring.
     */
    public function roomTimelineData(Request $request): JsonResponse
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfDay();

        $days = max(1, min(31, (int) $request->input('days', 14)));
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

    /**
     * Get facility timeline calendar data for front desk monitoring.
     */
    public function facilityTimelineData(Request $request): JsonResponse
    {
        $startDate = $request->filled('start_date')
            ? Carbon::parse($request->start_date)->startOfDay()
            : Carbon::now()->startOfDay();

        $days = max(1, min(31, (int) $request->input('days', 14)));
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

        // Fetch facilities
        $facilityQuery = Facility::query();
        if ($request->filled('facility_id') && $request->facility_id !== 'ALL') {
            $facilityQuery->where('facility_id', $request->facility_id);
        }

        $allFacilities = $facilityQuery
            ->with(['facilitySets'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        // Query overlapping reservations in range
        $reservations = FacilityReservation::with(['facility', 'facilitySet', 'reservedFacilities'])
            ->whereIn('status', ['pending', 'approved', 'active', 'completed'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereDate('reservation_date', '<=', $endDate->toDateString())
                    ->where(function ($sub) use ($startDate) {
                        $sub->whereDate('end_date', '>=', $startDate->toDateString())
                            ->orWhere(function ($sd) use ($startDate) {
                                $sd->whereNull('end_date')
                                    ->whereDate('reservation_date', '>=', $startDate->toDateString());
                            });
                    });
            })
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->get();

        $facilitiesData = $allFacilities->map(function (Facility $fac) use ($reservations, $today) {
            // Find reservations belonging to this facility (direct or consolidated)
            $facReservations = $reservations->filter(function (FacilityReservation $r) use ($fac) {
                if ($r->facility_id === $fac->facility_id) {
                    return true;
                }

                return $r->reservedFacilities->contains('facility_id', $fac->facility_id);
            });

            // Map reservations
            $mappedReservations = $facReservations->map(function (FacilityReservation $res) use ($today) {
                $startDateStr = $res->reservation_date ? $res->reservation_date->toDateString() : $today;
                $endDateStr = $res->end_date ? $res->end_date->toDateString() : $startDateStr;

                $startTimeStr = $res->start_time ? substr($res->start_time, 0, 5) : '08:00';
                $endTimeStr = $res->end_time ? substr($res->end_time, 0, 5) : '17:00';

                $timeFormatted = ($res->start_time && $res->end_time)
                    ? Carbon::parse($res->start_time)->format('g:i A').' – '.Carbon::parse($res->end_time)->format('g:i A')
                    : 'Full Day';

                return [
                    'reservation_id' => $res->reservation_id,
                    'reference_number' => $res->reference_number,
                    'booker_name' => $res->booker_name,
                    'booker_email' => $res->booker_email,
                    'booker_contact' => $res->booker_contact,
                    'event_name' => $res->event_name ?: 'Facility Reservation',
                    'event_details' => $res->event_details,
                    'billing_type' => $res->billing_type ?: $res->facility?->rate_type,
                    'start_date' => $startDateStr,
                    'end_date' => $endDateStr,
                    'start_time' => $startTimeStr,
                    'end_time' => $endTimeStr,
                    'time_formatted' => $timeFormatted,
                    'status' => $res->status,
                    'estimated_amount' => (float) $res->estimated_amount,
                    'final_amount' => (float) ($res->final_amount ?? $res->estimated_amount),
                    'is_consolidated' => $res->isConsolidated(),
                    'facility_set_name' => $res->facilitySet?->name,
                ];
            })->values()->all();

            // Determine live status for today
            $todayRes = $facReservations->first(function (FacilityReservation $r) use ($today) {
                $start = $r->reservation_date ? $r->reservation_date->toDateString() : null;
                $end = $r->end_date ? $r->end_date->toDateString() : $start;

                return $start && $end && $today >= $start && $today <= $end;
            });

            $currentStatus = 'AVAILABLE';
            if (! $fac->is_active) {
                $currentStatus = 'MAINTENANCE';
            } elseif ($todayRes) {
                if ($todayRes->status === 'active') {
                    $currentStatus = 'IN_USE';
                } elseif ($todayRes->status === 'approved') {
                    $currentStatus = 'BOOKED';
                } elseif ($todayRes->status === 'pending') {
                    $currentStatus = 'PENDING';
                }
            }

            return [
                'facility_id' => $fac->facility_id,
                'name' => $fac->name,
                'capacity' => $fac->capacity,
                'rate' => (float) $fac->rate,
                'rate_type' => $fac->rate_type,
                'hourly_rate' => $fac->effective_hourly_rate,
                'daily_rate' => $fac->effective_daily_rate,
                'is_active' => (bool) $fac->is_active,
                'images' => $fac->images ?? [],
                'image_url' => (! empty($fac->images) && count($fac->images) > 0) ? Facility::imageUrl($fac->images[0]) : null,
                'current_status' => $currentStatus,
                'reservations' => $mappedReservations,
            ];
        });

        // Summary counts for facilities
        $allActiveFacilities = Facility::where('is_active', true)->get();
        $totalFac = $allActiveFacilities->count();

        $bookedTodayCount = 0;
        $inUseTodayCount = 0;
        $pendingCount = FacilityReservation::where('status', 'pending')->count();
        $reservedUpcomingCount = FacilityReservation::where('status', 'approved')
            ->whereDate('reservation_date', '>=', $today)
            ->count();
        $inactiveCount = Facility::where('is_active', false)->count();

        foreach ($facilitiesData as $fd) {
            if ($fd['current_status'] === 'IN_USE') {
                $inUseTodayCount++;
                $bookedTodayCount++;
            } elseif ($fd['current_status'] === 'BOOKED') {
                $bookedTodayCount++;
            }
        }

        $availableTodayCount = max(0, $totalFac - $bookedTodayCount);

        $summary = [
            'total' => $totalFac,
            'available' => $availableTodayCount,
            'booked' => $bookedTodayCount,
            'in_use' => $inUseTodayCount,
            'reserved' => $reservedUpcomingCount,
            'pending' => $pendingCount,
            'maintenance' => $inactiveCount,
        ];

        $facilityOptions = Facility::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['facility_id', 'name'])
            ->map(fn ($f) => ['id' => $f->facility_id, 'name' => $f->name]);

        return response()->json([
            'range' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'days' => $days,
                'display' => $startDate->format('M j, Y').' — '.$endDate->format('M j, Y'),
            ],
            'dates' => $dates,
            'facilities' => $facilitiesData->values()->all(),
            'facility_filters' => $facilityOptions,
            'summary' => $summary,
        ]);
    }
}
