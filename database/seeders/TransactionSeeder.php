<?php

namespace Database\Seeders;

use App\Models\Folio;
use App\Models\Shift;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = Shift::with('user')->get();
        $folios = Folio::all();

        if ($shifts->isEmpty() || $folios->isEmpty()) {
            return;
        }

        foreach ($shifts as $index => $shift) {
            $shiftDate = Carbon::parse($shift->start_time)->toDateString();

            // Pick a couple of folios to generate transactions for this shift
            $assignedFolios = $folios->shuffle()->take(2);

            foreach ($assignedFolios as $fIndex => $folio) {
                // 1. Create a charge (e.g., Room Charge or Food & Beverage)
                $isRoom = ($index + $fIndex) % 2 === 0;
                $chargeCode = $isRoom ? 100 : 200;
                $amount = $isRoom ? rand(1500, 3500) : rand(150, 750);

                Transaction::create([
                    'folio_id' => $folio->folio_id,
                    'charge_code' => $chargeCode,
                    'shift_id' => $shift->shift_id,
                    'user_id' => $shift->user_id,
                    'transaction_date' => $shiftDate,
                    'charge_number' => 'CHG-'.rand(100000, 999999),
                    'payment_method' => 'NONE',
                    'reference_notes' => $isRoom ? 'Daily Room Charge' : 'Coffee shop order',
                    'charge_amount' => $amount,
                    'credit_amount' => 0.00,
                ]);

                // 2. Create a matching or partial payment
                $payMethod = ($fIndex % 2 === 0) ? 'CASH' : 'CREDIT_CARD';
                $payCode = ($payMethod === 'CASH') ? 403 : 401;
                $payAmount = $amount - (rand(0, 1) * rand(50, 100)); // sometimes partial payment

                Transaction::create([
                    'folio_id' => $folio->folio_id,
                    'charge_code' => $payCode,
                    'shift_id' => $shift->shift_id,
                    'user_id' => $shift->user_id,
                    'transaction_date' => $shiftDate,
                    'charge_number' => 'PAY-'.rand(100000, 999999),
                    'payment_method' => $payMethod,
                    'reference_notes' => 'Received payment',
                    'charge_amount' => 0.00,
                    'credit_amount' => $payAmount,
                ]);
            }
        }
    }
}
