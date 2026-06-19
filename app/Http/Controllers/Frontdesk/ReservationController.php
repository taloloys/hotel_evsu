<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::query()
            ->with(['folio.guest', 'room'])
            ->whereHas('folio.guest');

        if ($request->filled('date_from')) {
            $query->whereDate('departure_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('arrival_date', '<=', $request->date_to);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($bookingQuery) use ($search) {
                $bookingQuery->whereHas('folio', fn ($folioQuery) => $folioQuery->where('folio_number', 'like', "%{$search}%"))
                    ->orWhereHas('folio.guest', function ($guestQuery) use ($search) {
                        $guestQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $reservations = $query
            ->orderByDesc('arrival_date')
            ->orderByDesc('booking_id')
            ->get();

        $roomTypes = Room::query()
            ->select('room_type')
            ->distinct()
            ->orderBy('room_type')
            ->pluck('room_type');

        $assignableRooms = Room::query()
            ->where('is_active', true)
            ->where('status', 'AVAILABLE')
            ->orderBy('room_number')
            ->get(['room_id', 'room_number', 'room_type', 'base_rate']);

        return view('frontdesk.reservation.index', [
            'reservations' => $reservations,
            'roomTypes' => $roomTypes,
            'assignableRooms' => $assignableRooms,
            'suggestedFolioNumber' => $this->generateFolioNumber(),
            'filters' => [
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
                'status' => $request->status ?? 'all',
                'search' => $request->search,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'address_line1' => ['nullable', 'string', 'max:100'],
            'address_line2' => ['nullable', 'string', 'max:100'],
            'folio_number' => ['nullable', 'string', 'max:20', 'unique:folios,folio_number'],
            'registration_number' => ['nullable', 'string', 'max:20', 'unique:folios,registration_number'],
            'account_number' => ['nullable', 'string', 'max:20'],
            'num_pax' => ['nullable', 'integer', 'min:1', 'max:20'],
            'room_id' => ['required', 'integer', 'exists:rooms,room_id'],
            'arrival_date' => ['required', 'date', 'after_or_equal:today'],
            'arrival_time' => ['required', 'date_format:H:i'],
            'departure_date' => ['required', 'date', 'after:arrival_date'],
            'departure_time' => ['required', 'date_format:H:i'],
        ]);

        $room = Room::where('is_active', true)->findOrFail($validated['room_id']);

        if ($room->status !== 'AVAILABLE') {
            return back()
                ->withInput()
                ->withErrors(['room_id' => 'Selected room is not available for reservation.']);
        }

        if ($this->roomHasConflict($room->room_id, $validated['arrival_date'], $validated['departure_date'])) {
            return back()
                ->withInput()
                ->withErrors(['room_id' => 'Selected room already has a reservation for these dates.']);
        }

        DB::transaction(function () use ($validated, $room) {
            $guest = Guest::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'contact_number' => $validated['contact_number'] ?? null,
                'address_line1' => $validated['address_line1'] ?? null,
                'address_line2' => $validated['address_line2'] ?? null,
            ]);

            $folio = Folio::create([
                'folio_number' => ! empty($validated['folio_number'])
                    ? $validated['folio_number']
                    : $this->generateFolioNumber(),
                'registration_number' => $validated['registration_number'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'guest_id' => $guest->guest_id,
                'market_segment' => 'NONE',
                'num_pax' => $validated['num_pax'] ?? 1,
                'has_joiner' => false,
                'num_free_breakfasts' => 0,
                'symbol' => 'CBO',
                'folio_type' => 'GUEST',
                'status' => 'OPEN',
            ]);

            Booking::create([
                'folio_id' => $folio->folio_id,
                'room_id' => $room->room_id,
                'arrival_date' => $validated['arrival_date'],
                'arrival_time' => $validated['arrival_time'],
                'departure_date' => $validated['departure_date'],
                'departure_time' => $validated['departure_time'],
                'status' => 'RESERVED',
            ]);

            $room->update(['status' => 'RESERVED']);

            ActivityLog::log(
                'RESERVATION_CREATE',
                "Created reservation for guest {$validated['first_name']} {$validated['last_name']} with Folio #{$folio->folio_number} (Room {$room->room_number})."
            );
        });

        return redirect()
            ->route('frontdesk.reservation')
            ->with('success', 'Reservation saved successfully. It will appear on the dashboard for today\'s arrivals.');
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        if (! in_array($booking->status, ['RESERVED', 'CHECKED_IN'], true)) {
            return redirect()
                ->route('frontdesk.reservation')
                ->withErrors(['cancel' => 'Only active reservations can be cancelled.']);
        }

        DB::transaction(function () use ($booking) {
            $booking->load(['room', 'folio.guest']);

            $booking->update(['status' => 'CANCELLED']);

            if ($booking->room && in_array($booking->room->status, ['RESERVED', 'OCCUPIED'], true)) {
                $hasOtherActiveBookings = Booking::query()
                    ->where('room_id', $booking->room_id)
                    ->where('booking_id', '!=', $booking->booking_id)
                    ->whereIn('status', ['RESERVED', 'CHECKED_IN'])
                    ->exists();

                if (! $hasOtherActiveBookings) {
                    $booking->room->update(['status' => 'AVAILABLE']);
                }
            }

            $guestName = $booking->folio?->guest ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name) : 'Guest';
            $roomNumber = $booking->room?->room_number ?? 'N/A';
            ActivityLog::log(
                'RESERVATION_CREATE',
                "Cancelled reservation #{$booking->booking_id} for {$guestName} (Room {$roomNumber})."
            );
        });

        return redirect()
            ->route('frontdesk.reservation')
            ->with('success', 'Reservation cancelled successfully.');
    }

    private function roomHasConflict(int $roomId, string $arrivalDate, string $departureDate): bool
    {
        return Booking::query()
            ->where('room_id', $roomId)
            ->whereIn('status', ['RESERVED', 'CHECKED_IN'])
            ->whereDate('arrival_date', '<', $departureDate)
            ->whereDate('departure_date', '>', $arrivalDate)
            ->exists();
    }

    private function generateFolioNumber(): string
    {
        $year = now()->year;
        $prefix = "RSV-{$year}";

        $latest = Folio::query()
            ->where('folio_number', 'like', "{$prefix}%")
            ->orderByDesc('folio_id')
            ->value('folio_number');

        if (! $latest) {
            return $prefix.'001';
        }

        $sequence = (int) substr($latest, strlen($prefix)) + 1;

        return $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }
}
