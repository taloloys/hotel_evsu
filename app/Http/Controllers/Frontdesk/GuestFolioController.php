<?php

namespace App\Http\Controllers\Frontdesk;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ChargeCode;
use App\Models\CreditAccount;
use App\Models\Folio;
use App\Models\Room;
use App\Models\Shift;
use App\Models\Transaction;
use App\Services\CreditBillingService;
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

        $query = Folio::guestFolios()->with([
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

        $creditAccounts = CreditAccount::orderBy('account_name')->get();

        return view('frontdesk.guest-folio.index', [
            'folios' => $folios,
            'search' => $search,
            'folioType' => $folioType,
            'statusFilter' => $statusFilter,
            'availableRooms' => $availableRooms,
            'chargeCodes' => $chargeCodes,
            'creditAccounts' => $creditAccounts,
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

        if ($folio->status !== 'OPEN') {
            return back()->withErrors(['payment' => 'Cannot add transactions to a closed folio.']);
        }

        if ($validated['type'] === 'PAYMENT' && $folio->isSettled()) {
            return back()->withErrors(['payment' => 'This folio has already been settled.']);
        }

        if ($validated['type'] === 'PAYMENT' && round($validated['amount'], 2) > round($folio->balance, 2)) {
            return back()->withErrors(['payment' => 'Payment amount cannot exceed the outstanding balance.']);
        }

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

        $validated = $request->validate([
            'net_rate' => ['nullable', 'numeric', 'min:0'],
        ]);

        $room = $booking->room;

        if (! $room) {
            return back()->withErrors(['checkin' => 'Booking has no assigned room.']);
        }

        if (! in_array($room->status, ['AVAILABLE', 'RESERVED'], true)) {
            return back()->withErrors(['checkin' => "Room {$room->room_number} is not available for check-in (status: {$room->status})."]);
        }

        DB::transaction(function () use ($booking, $room, $validated) {
            $booking->update([
                'actual_check_in' => Carbon::now(),
                'status' => 'CHECKED_IN',
                'checked_in_by' => auth()->id(),
            ]);

            $room->update(['status' => 'OCCUPIED']);

            if ($booking->folio) {
                $rate = (isset($validated['net_rate']) && $validated['net_rate'] !== null && $validated['net_rate'] !== '')
                    ? (float) $validated['net_rate']
                    : ($booking->folio->net_rate ?? $room->base_rate);

                $booking->folio->update(['net_rate' => $rate]);
            }

            $booking->load('folio.guest');
            $guestName = $booking->folio?->guest
                ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name)
                : 'Guest';

            $booking->postRoomCharges();

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

        $today = Carbon::today()->toDateString();
        $isSameDayTransfer = $booking->arrival_date->toDateString() === $today;

        DB::transaction(function () use ($booking, $oldRoom, $newRoom, $validated, $today, $isSameDayTransfer) {
            if ($oldRoom) {
                $oldRoom->update(['status' => 'CLEANING']);
            }

            $newRoom->update(['status' => 'OCCUPIED']);

            $rate = (isset($validated['net_rate']) && $validated['net_rate'] !== null && $validated['net_rate'] !== '')
                ? (float) $validated['net_rate']
                : (float) $newRoom->base_rate;

            if ($booking->folio) {
                $booking->folio->update(['net_rate' => $rate]);
            }

            $guestName = $booking->folio?->guest
                ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name)
                : 'Guest';

            if ($isSameDayTransfer) {
                $booking->update(['room_id' => $newRoom->room_id]);

                Transaction::where('folio_id', $booking->folio_id)
                    ->where('charge_code', 100)
                    ->where('charge_number', 'like', 'RM-'.$booking->booking_id.'-%')
                    ->update(['charge_amount' => $rate]);

                ActivityLog::log(
                    'ROOM_TRANSFER',
                    "Room Transfer (Same-Day): Transferred {$guestName} from Room ".($oldRoom ? $oldRoom->room_number : 'N/A')." to Room {$newRoom->room_number}."
                );
            } else {
                $originalDepartureDate = $booking->departure_date;
                $originalDepartureTime = $booking->departure_time;

                $booking->update([
                    'departure_date' => $today,
                    'departure_time' => Carbon::now()->format('H:i'),
                    'actual_check_out' => Carbon::now(),
                    'status' => 'CHECKED_OUT',
                ]);

                Transaction::where('folio_id', $booking->folio_id)
                    ->where('charge_code', 100)
                    ->where('charge_number', 'like', 'RM-'.$booking->booking_id.'-%')
                    ->whereDate('transaction_date', '>', $today)
                    ->delete();

                $tomorrow = Carbon::tomorrow()->toDateString();

                $newBooking = Booking::create([
                    'folio_id' => $booking->folio_id,
                    'room_id' => $newRoom->room_id,
                    'arrival_date' => $tomorrow,
                    'arrival_time' => Carbon::now()->format('H:i'),
                    'departure_date' => $originalDepartureDate,
                    'departure_time' => $originalDepartureTime,
                    'actual_check_in' => Carbon::now(),
                    'status' => 'CHECKED_IN',
                    'checked_in_by' => auth()->id(),
                ]);

                $newBooking->postRoomCharges();

                ActivityLog::log(
                    'ROOM_TRANSFER',
                    "Room Transfer (Multi-Day): Transferred {$guestName} from Room ".($oldRoom ? $oldRoom->room_number : 'N/A')." to Room {$newRoom->room_number}. New booking #{$newBooking->booking_id} created for remainder of stay."
                );
            }
        });

        return back()->with('success', 'Room transfer completed successfully!');
    }

    /**
     * Extend a checked-in guest's stay with optional rate override.
     */
    public function extendStay(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->status !== 'CHECKED_IN') {
            return back()->withErrors(['extend' => 'Only checked-in guests can extend their stay.']);
        }

        $validated = $request->validate([
            'departure_date' => ['required', 'date', 'after:today'],
            'departure_time' => ['nullable', 'date_format:H:i'],
            'net_rate' => ['nullable', 'numeric', 'min:0'],
        ]);

        $booking->load(['room', 'folio']);

        $newDeparture = $validated['departure_date'];

        if ($booking->departure_date && Carbon::parse($newDeparture)->lte($booking->departure_date)) {
            return back()->withErrors(['extend' => 'New departure date must be after the current departure date.']);
        }

        $extensionStart = $booking->departure_date
            ? $booking->departure_date->toDateString()
            : Carbon::today()->toDateString();

        if ($this->roomHasConflictExcluding(
            $booking->room_id,
            $booking->booking_id,
            $extensionStart,
            $newDeparture
        )) {
            return back()->withErrors(['extend' => 'Room is not available for the extension period.']);
        }

        DB::transaction(function () use ($booking, $validated, $newDeparture) {
            $booking->update([
                'departure_date' => $newDeparture,
                'departure_time' => $validated['departure_time'] ?? $booking->departure_time ?? '12:00',
            ]);

            if ($booking->folio) {
                if (isset($validated['net_rate']) && $validated['net_rate'] !== null && $validated['net_rate'] !== '') {
                    $booking->folio->update(['net_rate' => (float) $validated['net_rate']]);
                }

                if ($booking->folio->net_rate !== null) {
                    Transaction::where('folio_id', $booking->folio_id)
                        ->where('charge_code', 100)
                        ->where('charge_number', 'like', 'RM-'.$booking->booking_id.'-%')
                        ->update(['charge_amount' => $booking->folio->net_rate]);
                }
            }

            $booking->postRoomCharges();

            $guestName = $booking->folio?->guest
                ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name)
                : 'Guest';

            ActivityLog::log(
                'STAY_EXTENDED',
                "Extended stay for {$guestName} to {$newDeparture} (Booking #{$booking->booking_id})."
            );
        });

        return back()->with('success', 'Stay extended successfully!');
    }

    /**
     * Check room availability for an extension period, excluding the current booking.
     */
    private function roomHasConflictExcluding(
        int $roomId,
        int $excludeBookingId,
        string $fromDate,
        string $toDate
    ): bool {
        return Booking::query()
            ->where('room_id', $roomId)
            ->where('booking_id', '!=', $excludeBookingId)
            ->whereIn('status', ['RESERVED', 'CHECKED_IN'])
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->where(function ($specificStayQuery) use ($fromDate, $toDate) {
                    $specificStayQuery->whereNotNull('departure_date')
                        ->whereDate('arrival_date', '<', $toDate)
                        ->whereDate('departure_date', '>', $fromDate);
                })->orWhere(function ($openStayQuery) use ($toDate) {
                    $openStayQuery->whereNull('departure_date')
                        ->whereDate('arrival_date', '<', $toDate);
                });
            })
            ->exists();
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

        $booking->load(['room', 'folio']);

        $today = Carbon::today()->toDateString();
        if ($booking->departure_date && $booking->departure_date->toDateString() > $today) {
            $booking->update(['departure_date' => $today]);

            // Clean up any unstayed future room charges
            Transaction::where('folio_id', $booking->folio_id)
                ->where('charge_code', 100)
                ->where('charge_number', 'like', 'RM-'.$booking->booking_id.'-%')
                ->whereDate('transaction_date', '>', $today)
                ->delete();

            $booking->refresh();
            if ($booking->folio) {
                $booking->folio->refresh();
            }
        }

        if ($booking->folio && ! $booking->folio->isSettled()) {
            $balance = $booking->folio->balance;
            $message = $balance > 0
                ? 'Cannot check out guest. Folio has an outstanding balance of ₱'.number_format($balance, 2).'.'
                : 'Cannot check out guest. Folio has an overpayment of ₱'.number_format(abs($balance), 2).'. Please refund the guest first.';

            return back()->withErrors(['checkout' => $message]);
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

            if ($booking->folio) {
                $booking->folio->update(['status' => 'CLOSED']);
            }

            $booking->load('folio.guest');
            $guestName = $booking->folio?->guest
                ? ($booking->folio->guest->first_name.' '.$booking->folio->guest->last_name)
                : 'Guest';
            $roomNumber = $room?->room_number ?? 'N/A';

            ActivityLog::log(
                'CHECK_OUT',
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
        if (! $folio->isSettled()) {
            $balance = $folio->balance;
            $message = $balance > 0
                ? 'Cannot close folio. Folio has an outstanding balance of ₱'.number_format($balance, 2).'.'
                : 'Cannot close folio. Folio has an overpayment of ₱'.number_format(abs($balance), 2).'. Please refund it first.';

            return back()->withErrors(['close' => $message]);
        }

        $folio->update(['status' => 'CLOSED']);

        ActivityLog::log(
            'FOLIO_CLOSED',
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
            'FOLIO_REOPENED',
            "Reopened Folio #{$folio->folio_number}."
        );

        return back()->with('success', "Folio #{$folio->folio_number} has been reopened.");
    }

    /**
     * Mark a folio as paid — post a clearing payment and close the folio.
     */
    public function markAsPaid(Request $request, Folio $folio, CreditBillingService $creditBillingService): RedirectResponse
    {
        if ($folio->status !== 'OPEN') {
            return back()->withErrors(['payment' => 'Only open folios can be marked as paid.']);
        }

        if ($folio->isSettled()) {
            return back()->withErrors(['payment' => 'This folio has already been settled.']);
        }

        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:Cash,Credit Card'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_notes' => ['nullable', 'string', 'max:255'],
            'close_folio' => ['nullable', 'boolean'],
        ]);

        if (round($validated['amount'], 2) > round($folio->balance, 2)) {
            return back()->withErrors(['payment' => 'Payment amount cannot exceed the outstanding balance.']);
        }

        $closeFolio = isset($validated['close_folio']) && $validated['close_folio'];

        $userId = auth()->id() ?? 1;

        $activeShift = Shift::where('user_id', $userId)->whereNull('end_time')->first();
        if (! $activeShift) {
            $activeShift = Shift::orderBy('shift_id', 'desc')->first();
            if (! $activeShift) {
                $activeShift = Shift::create([
                    'user_id' => $userId,
                    'start_time' => Carbon::now(),
                ]);
            }
        }

        // Determine payment charge code (Cash = 403, Credit Card = 401)
        $chargeCode = $validated['payment_method'] === 'Credit Card' ? '401' : '403';
        $paymentMethod = $validated['payment_method'] === 'Credit Card' ? 'CREDIT_CARD' : 'CASH';

        DB::transaction(function () use ($folio, $validated, $activeShift, $userId, $chargeCode, $paymentMethod, $closeFolio) {
            Transaction::create([
                'folio_id' => $folio->folio_id,
                'charge_code' => $chargeCode,
                'shift_id' => $activeShift->shift_id,
                'user_id' => $userId,
                'transaction_date' => Carbon::now()->toDateString(),
                'charge_number' => 'PAY-'.time(),
                'payment_method' => $paymentMethod,
                'reference_notes' => $validated['reference_notes'] ?? 'Folio payment',
                'charge_amount' => 0.00,
                'credit_amount' => $validated['amount'],
            ]);

            if ($closeFolio) {
                $folio->update([
                    'status' => 'CLOSED',
                    'payment_method' => $validated['payment_method'],
                ]);
            }
        });

        $actionText = $closeFolio ? 'and closed' : 'as partial payment';
        ActivityLog::log(
            'FOLIO_PAID',
            'Recorded payment of ₱'.number_format($validated['amount'], 2)." to Folio #{$folio->folio_number} {$actionText}."
        );

        $successMsg = $closeFolio ? "Folio #{$folio->folio_number} marked as paid and closed." : "Payment recorded successfully to Folio #{$folio->folio_number}.";

        return back()->with('success', $successMsg);
    }

    /**
     * Charge the folio balance to a corporate credit account.
     */
    public function chargeToAccount(Request $request, Folio $folio, CreditBillingService $creditBillingService): RedirectResponse
    {
        if ($folio->status !== 'OPEN') {
            return back()->withErrors(['payment' => 'Only open folios can be charged to an account.']);
        }

        if ($folio->isSettled()) {
            return back()->withErrors(['payment' => 'This folio has already been settled.']);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference_notes' => ['nullable', 'string', 'max:255'],
            'credit_account_id' => ['required', 'exists:credit_accounts,account_id'],
            'close_folio' => ['nullable', 'boolean'],
        ]);

        if (round($validated['amount'], 2) > round($folio->balance, 2)) {
            return back()->withErrors(['payment' => 'Payment amount cannot exceed the outstanding balance.']);
        }

        $closeFolio = isset($validated['close_folio']) && $validated['close_folio'];
        $userId = auth()->id() ?? 1;

        $account = CreditAccount::findOrFail($validated['credit_account_id']);

        $activeShift = Shift::where('user_id', $userId)->whereNull('end_time')->first();
        if (! $activeShift) {
            $activeShift = Shift::orderBy('shift_id', 'desc')->first();
            if (! $activeShift) {
                $activeShift = Shift::create([
                    'user_id' => $userId,
                    'start_time' => Carbon::now(),
                ]);
            }
        }

        DB::transaction(function () use ($creditBillingService, $account, $validated, $folio, $userId, $closeFolio, $activeShift) {
            $creditBillingService->chargeAccount(
                $account,
                $validated['amount'],
                'folio',
                $folio->folio_id,
                $userId,
                $validated['reference_notes'] ?? "Folio #{$folio->folio_number} settlement"
            );

            Transaction::create([
                'folio_id' => $folio->folio_id,
                'charge_code' => 404, // Use 404 for Account Charge (or 403/other if 404 is not defined)
                'shift_id' => $activeShift->shift_id,
                'user_id' => $userId,
                'transaction_date' => Carbon::now()->toDateString(),
                'charge_number' => 'AR-'.time(),
                'payment_method' => 'ACCOUNT_CHARGE',
                'reference_notes' => $validated['reference_notes'] ?? "Account Charge: {$account->account_name}",
                'charge_amount' => 0.00,
                'credit_amount' => $validated['amount'],
            ]);

            if ($closeFolio) {
                $folio->update([
                    'status' => 'CLOSED',
                    'payment_method' => 'Account Charge',
                ]);
            }
        });

        // Transaction handles folio update if closeFolio is true

        $actionText = $closeFolio ? 'and closed' : 'as partial payment';
        ActivityLog::log(
            'FOLIO_PAID',
            "Posted Account Charge ({$account->account_name}) of ₱".number_format($validated['amount'], 2)." to Folio #{$folio->folio_number} {$actionText}."
        );

        $successMsg = $closeFolio ? "Folio #{$folio->folio_number} charged to account and closed." : "Account charge posted successfully to Folio #{$folio->folio_number}.";

        return back()->with('success', $successMsg);
    }
}
