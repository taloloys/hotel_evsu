<?php

namespace App\Services\Coffeeshop;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\PosOrder;
use App\Models\PosSetting;
use App\Models\Shift;
use App\Models\Transaction;
use Carbon\Carbon;
use RuntimeException;

class PosGuestChargeService
{
    public function resolveActiveShift(int $userId): Shift
    {
        $activeShift = Shift::where('user_id', $userId)
            ->whereNull('end_time')
            ->first();

        if ($activeShift) {
            return $activeShift;
        }

        $activeShift = Shift::orderByDesc('shift_id')->first();

        if ($activeShift) {
            return $activeShift;
        }

        return Shift::create([
            'user_id' => $userId,
            'start_time' => Carbon::now(),
        ]);
    }

    public function postRoomCharge(PosOrder $order, int $folioId, string $itemSummary): Transaction
    {
        $userId = auth()->id() ?? $order->user_id;
        $shift = $this->resolveActiveShift($userId);
        $chargeNumber = 'POS-'.$order->order_id;
        if (strlen($chargeNumber) > 30) {
            $chargeNumber = substr($chargeNumber, 0, 30);
        }

        if (Transaction::where('charge_number', $chargeNumber)->exists()) {
            throw new RuntimeException('This order has already been charged to a folio.');
        }

        $transaction = Transaction::create([
            'folio_id' => $folioId,
            'charge_code' => 200,
            'shift_id' => $shift->shift_id,
            'user_id' => $userId,
            'transaction_date' => Carbon::now()->toDateString(),
            'charge_number' => $chargeNumber,
            'payment_method' => 'NONE',
            'reference_notes' => "POS Order {$order->order_number}: {$itemSummary}",
            'charge_amount' => $order->total,
            'credit_amount' => 0.00,
            'department' => 'COFFEE_SHOP',
        ]);

        ActivityLog::log(
            'POS_SALE',
            'POS room charge of ₱'.number_format((float) $order->total, 2)." posted on Folio via Order {$order->order_number}."
        );

        return $transaction;
    }

    public function postWalkInSale(PosOrder $order, string $paymentMethod, string $itemSummary): Transaction
    {
        $folioId = PosSetting::walkInFolioId();

        $userId = auth()->id() ?? $order->user_id;
        $shift = $this->resolveActiveShift($userId);
        $methodLabel = strtoupper($paymentMethod);
        $chargeNumber = 'POS-'.$methodLabel.'-'.$order->order_id;
        if (strlen($chargeNumber) > 30) {
            $chargeNumber = substr($chargeNumber, 0, 30);
        }
        $payChargeNumber = $chargeNumber.'-PAY';
        if (strlen($payChargeNumber) > 30) {
            $payChargeNumber = substr('POS-'.$order->order_id.'-PAY', 0, 30);
        }
        $today = Carbon::now()->toDateString();
        $notes = "POS {$methodLabel} Order {$order->order_number}: {$itemSummary}";

        Transaction::create([
            'folio_id' => $folioId,
            'charge_code' => 200,
            'shift_id' => $shift->shift_id,
            'user_id' => $userId,
            'transaction_date' => $today,
            'charge_number' => $chargeNumber,
            'payment_method' => 'NONE',
            'reference_notes' => $notes,
            'charge_amount' => $order->total,
            'credit_amount' => 0.00,
            'department' => 'COFFEE_SHOP',
        ]);

        $txPaymentMethod = match ($methodLabel) {
            'CARD', 'CREDIT_CARD' => 'CREDIT_CARD',
            'GCASH' => 'GCASH',
            'MAYA' => 'MAYA',
            'CHECK' => 'CHECK',
            'ACCOUNT_CHARGE' => 'ACCOUNT_CHARGE',
            'NONE' => 'NONE',
            default => 'CASH',
        };

        $payment = Transaction::create([
            'folio_id' => $folioId,
            'charge_code' => 403,
            'shift_id' => $shift->shift_id,
            'user_id' => $userId,
            'transaction_date' => $today,
            'charge_number' => $payChargeNumber,
            'payment_method' => $txPaymentMethod,
            'reference_notes' => "{$methodLabel} payment for {$order->order_number}",
            'charge_amount' => 0.00,
            'credit_amount' => $order->total,
            'department' => 'COFFEE_SHOP',
        ]);

        ActivityLog::log(
            'POS_SALE',
            "POS {$paymentMethod} sale of ₱".number_format((float) $order->total, 2)." recorded via Order {$order->order_number}."
        );

        return $payment;
    }

    public function getCheckedInGuests()
    {
        return Booking::where('status', 'CHECKED_IN')
            ->with(['folio.guest', 'room', 'folio' => fn ($q) => $q->withBalances()])
            ->orderBy('booking_id')
            ->get()
            ->map(function (Booking $booking) {
                $guest = $booking->folio?->guest;

                return [
                    'booking_id' => $booking->booking_id,
                    'folio_id' => $booking->folio_id,
                    'guest_id' => $guest?->guest_id,
                    'guest_name' => $guest
                        ? trim($guest->first_name.' '.$guest->last_name)
                        : 'Unknown Guest',
                    'room_id' => $booking->room_id,
                    'room_number' => $booking->room?->room_number,
                    'balance' => $booking->folio ? (float) $booking->folio->balance : 0.00,
                ];
            });
    }

    public function validateRoomCharge(?int $bookingId, ?int $folioId): Booking
    {
        if (! $bookingId || ! $folioId) {
            throw new RuntimeException('A checked-in guest must be selected for room charge.');
        }

        $booking = Booking::with('folio')
            ->where('booking_id', $bookingId)
            ->where('folio_id', $folioId)
            ->where('status', 'CHECKED_IN')
            ->first();

        if (! $booking) {
            throw new RuntimeException('Selected guest is not currently checked in.');
        }

        if ($booking->folio && $booking->folio->status !== 'OPEN') {
            throw new RuntimeException('Guest folio is not open for charges.');
        }

        return $booking;
    }
}
