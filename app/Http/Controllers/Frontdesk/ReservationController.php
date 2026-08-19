<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Mail\ReservationConfirmationMail;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\Folio;
use App\Models\Guest;
use App\Models\Room;
use App\Services\EmailRecipientResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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
            ->paginate(20)
            ->withQueryString();

        $roomTypes = Room::query()
            ->select('room_type')
            ->distinct()
            ->orderBy('room_type')
            ->pluck('room_type');

        $assignableRooms = Room::query()
            ->where('is_active', true)
            ->whereIn('status', ['AVAILABLE', 'CLEANING'])
            ->orderBy('room_number')
            ->get(['room_id', 'room_number', 'room_type', 'base_rate', 'status']);

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
        $firstName = $request->input('first_name');
        $lastName = $request->input('last_name');
        $existingGuest = null;

        if ($firstName && $lastName) {
            $existingGuest = Guest::whereRaw('LOWER(first_name) = ?', [strtolower(trim($firstName))])
                ->whereRaw('LOWER(last_name) = ?', [strtolower(trim($lastName))])
                ->first();
        }

        $isOpenStay = $request->boolean('open_stay');

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'address_line1' => ['nullable', 'string', 'max:100'],
            'address_line2' => ['nullable', 'string', 'max:100'],
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
            'num_pax' => ['nullable', 'integer', 'min:1', 'max:20'],
            'room_id' => ['required', 'integer', 'exists:rooms,room_id'],
            'arrival_date' => ['required', 'date', 'after_or_equal:today'],
            'arrival_time' => ['required', 'date_format:H:i'],
            'open_stay' => ['nullable', 'boolean'],
            'departure_date' => [$isOpenStay ? 'nullable' : 'required', 'date', 'after:arrival_date'],
            'departure_time' => [$isOpenStay ? 'nullable' : 'required', 'date_format:H:i'],
        ]);

        $room = Room::where('is_active', true)->findOrFail($validated['room_id']);

        if (! in_array($room->status, ['AVAILABLE', 'CLEANING'], true)) {
            return back()
                ->withInput()
                ->withErrors(['room_id' => 'Selected room is not available for reservation.']);
        }

        $departureDate = $isOpenStay ? null : ($validated['departure_date'] ?? null);

        if ($this->roomHasConflict($room->room_id, $validated['arrival_date'], $departureDate)) {
            return back()
                ->withInput()
                ->withErrors(['room_id' => 'Selected room already has a reservation for these dates.']);
        }

        $booking = DB::transaction(function () use ($validated, $room, $existingGuest, $isOpenStay) {
            if ($existingGuest) {
                $guest = $existingGuest;
                $guest->update([
                    'contact_number' => $validated['contact_number'] ?? $guest->contact_number,
                    'email' => $validated['email'] ?? $guest->email,
                    'address_line1' => $validated['address_line1'] ?? $guest->address_line1,
                    'address_line2' => $validated['address_line2'] ?? $guest->address_line2,
                ]);
            } else {
                $guest = Guest::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'contact_number' => $validated['contact_number'] ?? null,
                    'email' => $validated['email'] ?? null,
                    'address_line1' => $validated['address_line1'] ?? null,
                    'address_line2' => $validated['address_line2'] ?? null,
                ]);
            }

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
                'net_rate' => $room->base_rate,
            ]);

            $booking = Booking::create([
                'folio_id' => $folio->folio_id,
                'room_id' => $room->room_id,
                'arrival_date' => $validated['arrival_date'],
                'arrival_time' => $validated['arrival_time'],
                'departure_date' => $isOpenStay ? null : $validated['departure_date'],
                'departure_time' => $isOpenStay ? null : $validated['departure_time'],
                'status' => 'RESERVED',
            ]);

            if ($room->status === 'AVAILABLE') {
                $room->update(['status' => 'RESERVED']);
            }

            ActivityLog::log(
                'RESERVATION_CREATE',
                "Created reservation for guest {$validated['first_name']} {$validated['last_name']} with Folio #{$folio->folio_number} (Room {$room->room_number})."
            );

            return $booking;
        });

        try {
            $booking->load(['folio.guest', 'room']);
            $recipients = app(EmailRecipientResolver::class)->resolve('reservation', $booking);
            if (! empty($recipients)) {
                Mail::to($recipients)->queue(new ReservationConfirmationMail($booking));
            }
        } catch (\Throwable $e) {
            // Log or ignore email dispatch failures gracefully
        }

        return redirect()
            ->route('frontdesk.reservation')
            ->with('success', 'Reservation saved successfully. It will appear on the dashboard for today\'s arrivals.');
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        if ($booking->status !== 'RESERVED') {
            return redirect()
                ->route('frontdesk.reservation')
                ->withErrors(['cancel' => 'Only reserved bookings can be cancelled.']);
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
                'RESERVATION_CANCEL',
                "Cancelled reservation #{$booking->booking_id} for {$guestName} (Room {$roomNumber})."
            );
        });

        return redirect()
            ->route('frontdesk.reservation')
            ->with('success', 'Reservation cancelled successfully.');
    }

    private function roomHasConflict(int $roomId, string $arrivalDate, ?string $departureDate): bool
    {
        return Booking::query()
            ->where('room_id', $roomId)
            ->whereIn('status', ['RESERVED', 'CHECKED_IN'])
            ->where(function ($query) use ($arrivalDate, $departureDate) {
                $query->where(function ($specificStayQuery) use ($arrivalDate, $departureDate) {
                    $specificStayQuery->whereNotNull('departure_date')
                        ->whereDate('arrival_date', '<', $departureDate ?? '9999-12-31')
                        ->whereDate('departure_date', '>', $arrivalDate);
                })->orWhere(function ($openStayQuery) use ($departureDate) {
                    $openStayQuery->whereNull('departure_date');

                    if ($departureDate !== null) {
                        $openStayQuery->whereDate('arrival_date', '<', $departureDate);
                    }
                });
            })
            ->exists();
    }

    private function generateFolioNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "RSV-{$date}-";

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
