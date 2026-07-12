<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Room;
use App\Services\RoomChargeService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(): View
    {
        // Catch up on any missed room charges when initializing the dashboard
        app(RoomChargeService::class)->processCatchUpCharges();

        $today = Carbon::now()->toDateString();

        $assignableRooms = $this->assignableRooms($today);

        $roomTypes = Room::query()
            ->select('room_type')
            ->distinct()
            ->orderBy('room_type')
            ->pluck('room_type');

        return view('frontdesk.registration.index', [
            'assignableRooms' => $assignableRooms,
            'roomTypes' => $roomTypes,
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

    public function store(Request $request): RedirectResponse
    {
        $isOpenStay = (bool) $request->input('open_stay', false);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'address_line1' => ['nullable', 'string', 'max:100'],
            'address_line2' => ['nullable', 'string', 'max:100'],
            'folio_number' => ['nullable', 'string', 'max:20', 'unique:folios,folio_number'],
            'registration_number' => ['nullable', 'string', 'max:20', 'unique:folios,registration_number'],
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
            'net_rate' => ['nullable', 'numeric', 'min:0'],
            'open_stay' => ['nullable', 'boolean'],
            'room_id' => ['required', 'integer', 'exists:rooms,room_id'],
            'arrival_date' => ['required', 'date', 'after_or_equal:today'],
            'arrival_time' => ['required', 'date_format:H:i'],
            'departure_date' => $isOpenStay ? ['nullable'] : ['required', 'date', 'after:arrival_date'],
            'departure_time' => $isOpenStay ? ['nullable'] : ['required', 'date_format:H:i'],
        ]);

        $today = Carbon::now()->toDateString();
        $room = Room::where('is_active', true)->findOrFail($validated['room_id']);

        if (! $this->isRoomAssignable($room, $today)) {
            return back()
                ->withInput()
                ->withErrors(['room_id' => 'Selected room is not available. It may be occupied, reserved, or under maintenance.']);
        }

        if (! $isOpenStay && $this->roomHasConflict($room->room_id, $validated['arrival_date'], $validated['departure_date'])) {
            return back()
                ->withInput()
                ->withErrors(['room_id' => 'Selected room already has a booking for these dates.']);
        }

        $guestName = trim($validated['first_name'].' '.$validated['last_name']);

        DB::transaction(function () use ($validated, $room, $isOpenStay) {
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
                'net_rate' => (isset($validated['net_rate']) && $validated['net_rate'] !== null && $validated['net_rate'] !== '')
                    ? (float) $validated['net_rate']
                    : $room->base_rate,
            ]);

            $booking = Booking::create([
                'folio_id' => $folio->folio_id,
                'room_id' => $room->room_id,
                'arrival_date' => $validated['arrival_date'],
                'arrival_time' => $validated['arrival_time'],
                'departure_date' => $isOpenStay ? null : ($validated['departure_date'] ?? null),
                'departure_time' => $isOpenStay ? null : ($validated['departure_time'] ?? null),
                'actual_check_in' => Carbon::now(),
                'status' => 'CHECKED_IN',
            ]);

            $room->update(['status' => 'OCCUPIED']);

            // Post room charges using the new service
            app(RoomChargeService::class)->processCatchUpCharges($booking->booking_id);
        });

        return redirect()
            ->route('frontdesk.dashboard')
            ->with('success', "{$guestName} registered successfully. Room {$room->room_number} is now occupied.");
    }

    /**
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
        $date = now()->format('Ymd');
        $prefix = "REG-{$date}-";

        $latest = Folio::query()
            ->where('folio_number', 'like', "{$prefix}%")
            ->orderByDesc('folio_id')
            ->value('folio_number');

        if (! $latest) {
            return $prefix.'0001';
        }

        $sequence = (int) substr($latest, strlen($prefix)) + 1;

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
