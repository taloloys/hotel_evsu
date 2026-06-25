<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckInController extends Controller
{
    /**
     * Display the check-in form for existing guests.
     */
    public function index(): View
    {
        $today = Carbon::now()->toDateString();

        $assignableRooms = $this->assignableRooms($today);

        $roomTypes = Room::query()
            ->select('room_type')
            ->distinct()
            ->orderBy('room_type')
            ->pluck('room_type');

        // Fetch selected guest on validation failure fallback
        $selectedGuest = null;
        if (old('guest_id')) {
            $selectedGuest = Guest::with(['folios'])->find(old('guest_id'));
        }

        return view('frontdesk.check-in.index', [
            'assignableRooms' => $assignableRooms,
            'roomTypes' => $roomTypes,
            'selectedGuest' => $selectedGuest,
            'suggestedFolioNumber' => $this->generateFolioNumber(),
            'defaults' => [
                'arrival_date' => $today,
                'arrival_time' => '12:00',
                'departure_time' => '12:00',
                'market_segment' => 'Walk-in',
                'num_pax' => 1,
                'symbol' => 'CBO',
            ],
        ]);
    }

    /**
     * Store a new check-in booking/folio for an existing guest.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'guest_id' => ['required', 'integer', 'exists:guests,guest_id'],
            'folio_number' => [
                'nullable',
                'string',
                'max:20',
                'unique:folios,folio_number',
            ],
            'registration_number' => [
                'nullable',
                'string',
                'max:20',
                'unique:folios,registration_number',
            ],
            'account_number' => ['nullable', 'string', 'max:20'],
            'market_segment' => ['nullable', 'string', 'max:50'],
            'num_pax' => ['nullable', 'integer', 'min:1', 'max:20'],
            'has_joiner' => ['nullable', 'boolean'],
            'symbol' => ['nullable', 'string', 'max:10'],
            'billing_arrangements' => ['nullable', 'string'],
            'special_arrangements' => ['nullable', 'string'],
            'num_free_breakfasts' => ['nullable', 'integer', 'min:0', 'max:20'],
            'breakfast_code' => ['nullable', 'string', 'max:20'],
            'payment_method' => ['nullable', 'string', 'in:Cash,Credit Card'],
            'room_id' => ['required', 'integer', 'exists:rooms,room_id'],
            'arrival_date' => ['required', 'date', 'after_or_equal:today'],
            'arrival_time' => ['required', 'date_format:H:i'],
            'departure_date' => ['required', 'date', 'after:arrival_date'],
            'departure_time' => ['required', 'date_format:H:i'],
        ]);

        $today = Carbon::now()->toDateString();
        $room = Room::where('is_active', true)->findOrFail($validated['room_id']);

        if (! $this->isRoomAssignable($room, $today)) {
            return back()
                ->withInput()
                ->withErrors(['room_id' => 'Selected room is not available. It may be occupied, reserved, or under maintenance.']);
        }

        if ($this->roomHasConflict($room->room_id, $validated['arrival_date'], $validated['departure_date'])) {
            return back()
                ->withInput()
                ->withErrors(['room_id' => 'Selected room already has a booking for these dates.']);
        }

        $guest = Guest::findOrFail($validated['guest_id']);
        $guestName = trim($guest->first_name.' '.$guest->last_name);

        DB::transaction(function () use ($validated, $room, $guest) {
            $folio = Folio::create([
                'folio_number' => ! empty($validated['folio_number'])
                    ? $validated['folio_number']
                    : $this->generateFolioNumber(),
                'registration_number' => $validated['registration_number'] ?? null,
                'account_number' => $validated['account_number'] ?? null,
                'guest_id' => $guest->guest_id,
                'market_segment' => $validated['market_segment'] ?? 'Walk-in',
                'billing_arrangements' => $validated['billing_arrangements'] ?? null,
                'special_arrangements' => $validated['special_arrangements'] ?? null,
                'num_pax' => $validated['num_pax'] ?? 1,
                'has_joiner' => (bool) ($validated['has_joiner'] ?? false),
                'num_free_breakfasts' => $validated['num_free_breakfasts'] ?? 0,
                'breakfast_code' => $validated['breakfast_code'] ?? null,
                'symbol' => $validated['symbol'] ?? 'CBO',
                'folio_type' => 'GUEST',
                'status' => 'OPEN',
                'payment_method' => $validated['payment_method'] ?? 'Cash',
                'net_rate' => $room->base_rate,
            ]);

            $booking = Booking::create([
                'folio_id' => $folio->folio_id,
                'room_id' => $room->room_id,
                'arrival_date' => $validated['arrival_date'],
                'arrival_time' => $validated['arrival_time'],
                'departure_date' => $validated['departure_date'],
                'departure_time' => $validated['departure_time'],
                'actual_check_in' => Carbon::now(),
                'status' => 'CHECKED_IN',
                'checked_in_by' => auth()->id(),
            ]);

            $room->update(['status' => 'OCCUPIED']);

            // Post room charges night-by-night automatically
            $booking->postRoomCharges();
        });

        return redirect()
            ->route('frontdesk.dashboard')
            ->with('success', "{$guestName} checked in successfully. Room {$room->room_number} is now occupied.");
    }

    /**
     * Get available rooms for assignment.
     *
     * @return Collection<int, Room>
     */
    private function assignableRooms(string $today)
    {
        return Room::query()
            ->where('is_active', true)
            ->where('status', 'AVAILABLE')
            ->whereDoesntHave('bookings', function ($query) use ($today) {
                $query->whereIn('status', ['RESERVED', 'CHECKED_IN'])
                    ->whereDate('departure_date', '>=', $today);
            })
            ->orderBy('room_type')
            ->orderBy('room_number')
            ->get(['room_id', 'room_number', 'room_type', 'base_rate']);
    }

    /**
     * Check if a room can be assigned today.
     */
    private function isRoomAssignable(Room $room, string $today): bool
    {
        if (! $room->is_active) {
            return false;
        }

        if ($room->status !== 'AVAILABLE') {
            return false;
        }

        return ! $room->bookings()
            ->whereIn('status', ['RESERVED', 'CHECKED_IN'])
            ->whereDate('departure_date', '>=', $today)
            ->exists();
    }

    /**
     * Check if room has conflict for selected dates.
     */
    private function roomHasConflict(int $roomId, string $arrivalDate, string $departureDate): bool
    {
        return Booking::query()
            ->where('room_id', $roomId)
            ->whereIn('status', ['RESERVED', 'CHECKED_IN'])
            ->whereDate('arrival_date', '<', $departureDate)
            ->whereDate('departure_date', '>', $arrivalDate)
            ->exists();
    }

    /**
     * Generate standard folio number.
     */
    private function generateFolioNumber(): string
    {
        $year = now()->year;
        $prefix = "REG-{$year}";

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
