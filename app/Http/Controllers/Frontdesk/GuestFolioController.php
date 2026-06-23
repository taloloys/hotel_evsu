<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ChargeCode;
use App\Models\Folio;
use App\Models\Room;
use App\Models\Shift;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GuestFolioController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $folioType = $request->input('folio_type', 'ALL');
        $statusFilter = $request->input('status', 'ALL');

        $query = Folio::with([
            'guest',
            'bookings.room',
            'transactions.chargeCode',
        ]);

        // Search: folio number, guest name, or room number
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('folio_number', 'like', "%{$search}%")
                    ->orWhereHas('guest', function ($gq) use ($search) {
                        $gq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('bookings.room', function ($rq) use ($search) {
                        $rq->where('room_number', 'like', "%{$search}%");
                    });
            });
        }

        // Folio type filter
        if ($folioType !== 'ALL') {
            $query->where('folio_type', $folioType);
        }

        // Status filter
        if ($statusFilter !== 'ALL') {
            $query->where('status', $statusFilter);
        }

        $folios = $query->orderByDesc('folio_id')->paginate(20)->withQueryString();

        // Load available rooms and active charge codes for the controls in the modals
        $availableRooms = Room::where('is_active', true)
            ->where('status', 'AVAILABLE')
            ->orderBy('room_number')
            ->get();

        $chargeCodes = ChargeCode::where('is_active', true)
            ->orderBy('charge_code')
            ->get();

        return view('frontdesk.guest-folio.index', [
            'folios' => $folios,
            'search' => $search,
            'folioType' => $folioType,
            'statusFilter' => $statusFilter,
            'availableRooms' => $availableRooms,
            'chargeCodes' => $chargeCodes,
        ]);
    }

    /**
     * Post a charge or payment transaction to a guest folio
     */
    public function postTransaction(Request $request, Folio $folio): RedirectResponse
    {
        $validated = $request->validate([
            'charge_code' => ['required', 'exists:chargecodes,charge_code'],
            'type' => ['required', 'in:CHARGE,PAYMENT'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_notes' => ['nullable', 'string', 'max:255'],
        ]);

        $userId = auth()->id() ?? 1;

        // Retrieve or fallback active shift
        $activeShift = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        if (! $activeShift) {
            $activeShift = Shift::orderBy('shift_id', 'desc')->first();
            if (! $activeShift) {
                $activeShift = Shift::create([
                    'user_id' => $userId,
                    'start_time' => Carbon::now(),
                ]);
            }
        }

        $chargeCode = ChargeCode::findOrFail($validated['charge_code']);

        $chargeAmount = 0.00;
        $creditAmount = 0.00;
        $paymentMethod = 'NONE';

        if ($validated['type'] === 'CHARGE') {
            $chargeAmount = $validated['amount'];
        } else {
            $creditAmount = $validated['amount'];
            if (in_array((int) $validated['charge_code'], [401, 402])) {
                $paymentMethod = 'CREDIT_CARD';
            } elseif ((int) $validated['charge_code'] === 403) {
                $paymentMethod = 'CASH';
            } else {
                $paymentMethod = 'CASH';
            }
        }

        $chargeNo = 'TXN-'.time();

        Transaction::create([
            'folio_id' => $folio->folio_id,
            'charge_code' => $validated['charge_code'],
            'shift_id' => $activeShift->shift_id,
            'user_id' => $userId,
            'transaction_date' => Carbon::now()->toDateString(),
            'charge_number' => $validated['reference_notes'] ?? $chargeNo,
            'payment_method' => $paymentMethod,
            'reference_notes' => $validated['reference_notes'] ?? 'Manual posting',
            'charge_amount' => $chargeAmount,
            'credit_amount' => $creditAmount,
        ]);

        $typeName = $validated['type'] === 'CHARGE' ? 'charge' : 'payment';
        ActivityLog::log(
            'ADD_CHARGE',
            "Posted manual {$typeName} of ₱".number_format($validated['amount'], 2)." [{$chargeCode->description}] on Folio #{$folio->folio_number}."
        );

        return back()->with('success', 'Transaction posted successfully!');
    }

    /**
     * Check in a reserved booking from the guest folio page
     */
    public function checkInBooking(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->status !== 'RESERVED') {
            return back()->withErrors(['checkin' => 'Only reserved bookings can be checked in.']);
        }

        $room = $booking->room;

        if (! $room) {
            return back()->withErrors(['checkin' => 'Booking has no assigned room.']);
        }

        if ($room->status !== 'AVAILABLE') {
            return back()->withErrors(['checkin' => "Room {$room->room_number} is not available for check-in (status: {$room->status})."]);
        }

        DB::transaction(function () use ($booking, $room) {
            $booking->update([
                'actual_check_in' => Carbon::now(),
                'status' => 'CHECKED_IN',
                'checked_in_by' => auth()->id(),
            ]);

            $room->update(['status' => 'OCCUPIED']);

            $booking->load('folio.guest');
            $guestName = $booking->folio?->guest
                ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name)
                : 'Guest';

            ActivityLog::log(
                'CHECK_IN',
                "Checked in guest {$guestName} to Room {$room->room_number} (Booking #{$booking->booking_id}) via Folio."
            );
        });

        return back()->with('success', 'Guest checked in successfully!');
    }

    /**
     * Transfer a guest to another available room
     */
    public function transferRoom(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'new_room_id' => ['required', 'exists:rooms,room_id'],
            'net_rate' => ['nullable', 'numeric', 'min:0.00'],
        ]);

        $newRoom = Room::where('is_active', true)
            ->where('status', 'AVAILABLE')
            ->findOrFail($validated['new_room_id']);

        $booking->load(['room', 'folio']);
        $oldRoom = $booking->room;

        if ($booking->status !== 'CHECKED_IN') {
            return back()->withErrors(['room' => 'Only checked-in guests can be transferred to another room.']);
        }

        DB::transaction(function () use ($booking, $oldRoom, $newRoom, $validated) {
            // Update old room to CLEANING
            if ($oldRoom) {
                $oldRoom->update(['status' => 'CLEANING']);
            }

            // Update new room to OCCUPIED
            $newRoom->update(['status' => 'OCCUPIED']);

            // Update booking room
            $booking->update([
                'room_id' => $newRoom->room_id,
            ]);

            // Update folio net_rate — use provided rate or fall back to new room's base rate
            if ($booking->folio) {
                $rate = (isset($validated['net_rate']) && $validated['net_rate'] !== null && $validated['net_rate'] !== '')
                    ? (float) $validated['net_rate']
                    : (float) $newRoom->base_rate;

                $booking->folio->update([
                    'net_rate' => $rate,
                ]);
            }

            $guestName = $booking->folio?->guest
                ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name)
                : 'Guest';

            ActivityLog::log(
                'ROOM_MODIFIED',
                "Room Transfer: Transferred {$guestName} from Room ".($oldRoom ? $oldRoom->room_number : 'N/A')." to Room {$newRoom->room_number}."
            );
        });

        return back()->with('success', 'Room transfer completed successfully!');
    }

    /**
     * Reconcile and check out a guest from the guest folio
     */
    public function checkOutBooking(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'checkout_time' => ['required', 'regex:/^(0?[1-9]|1[0-2]):[0-5][0-9]$/'],
            'checkout_period' => ['required', 'in:AM,PM'],
        ]);

        // Normalize time to always have leading zero for Carbon parsing
        $checkoutTimePadded = str_pad(explode(':', $validated['checkout_time'])[0], 2, '0', STR_PAD_LEFT)
            .':'.explode(':', $validated['checkout_time'])[1];

        if ($booking->status !== 'CHECKED_IN') {
            return back()->withErrors(['checkout' => 'Only checked-in guests can be checked out.']);
        }

        $room = $booking->room;

        $actualCheckOut = Carbon::createFromFormat(
            'Y-m-d h:i A',
            Carbon::today()->format('Y-m-d').' '.$checkoutTimePadded.' '.$validated['checkout_period']
        );

        DB::transaction(function () use ($booking, $room, $actualCheckOut) {
            $booking->update([
                'actual_check_in' => $booking->actual_check_in, // preserve check-in time
                'actual_check_out' => $actualCheckOut,
                'status' => 'CHECKED_OUT',
            ]);

            if ($room) {
                $room->update(['status' => 'CLEANING']);
            }

            $booking->load('folio.guest');
            $guestName = $booking->folio?->guest
                ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name)
                : 'Guest';
            $roomNumber = $room?->room_number ?? 'N/A';

            ActivityLog::log(
                'CHECK_IN', // Matches BookingOperationController's action type for consistency
                "Checked out guest {$guestName} from Room {$roomNumber} (Booking #{$booking->booking_id}) via Folio."
            );
        });

        return back()->with('success', 'Guest checked out successfully! Room status updated to CLEANING.');
    }

    /**
     * Close a guest folio
     */
    public function closeFolio(Request $request, Folio $folio): RedirectResponse
    {
        $folio->update(['status' => 'CLOSED']);

        ActivityLog::log(
            'ROOM_MODIFIED',
            "Closed Folio #{$folio->folio_number}."
        );

        return back()->with('success', "Folio #{$folio->folio_number} has been closed.");
    }

    /**
     * Reopen a guest folio
     */
    public function reopenFolio(Request $request, Folio $folio): RedirectResponse
    {
        $folio->update(['status' => 'OPEN']);

        ActivityLog::log(
            'ROOM_MODIFIED',
            "Reopened Folio #{$folio->folio_number}."
        );

        return back()->with('success', "Folio #{$folio->folio_number} has been reopened.");
    }
}
