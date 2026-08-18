<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateFrontdeskStressData extends Command
{
    protected $signature = 'frontdesk:generate-stress-data {--count=5000 : Number of guests to generate} {--cleanup : Remove generated stress data}';

    protected $description = 'Generate thousands of Front Desk records (guests, folios, bookings, transactions) to stress test performance';

    public function handle(): int
    {
        if ($this->option('cleanup')) {
            $this->info('Cleaning up stress test records...');
            $deletedTx = DB::table('transactions')->where('reference_notes', 'LIKE', '%[STRESS_TEST]%')->delete();
            $deletedBk = DB::table('bookings')->whereIn('folio_id', function ($q) {
                $q->select('folio_id')->from('folios')->where('special_arrangements', 'STRESS_TEST');
            })->delete();
            $deletedFolios = DB::table('folios')->where('special_arrangements', 'STRESS_TEST')->delete();
            $deletedGuests = DB::table('guests')->where('address_line2', 'STRESS_TEST')->delete();

            $this->info("Cleaned up: {$deletedTx} transactions, {$deletedBk} bookings, {$deletedFolios} folios, {$deletedGuests} guests.");

            return Command::SUCCESS;
        }

        $count = (int) $this->option('count');
        if ($count < 10) {
            $count = 5000;
        }

        $this->info("Starting stress data generation: target {$count} guests and associated folios/bookings/transactions...");

        $faker = Faker::create();
        $startTime = microtime(true);

        $rooms = DB::table('rooms')->pluck('room_id')->toArray();
        if (empty($rooms)) {
            $this->error('No rooms found in database. Run room seeders first.');

            return Command::FAILURE;
        }

        $user = DB::table('users')->first();
        $userId = $user ? $user->user_id : 1;

        $shift = DB::table('shifts')->first();
        $shiftId = $shift ? $shift->shift_id : null;
        if (! $shiftId) {
            $shiftId = DB::table('shifts')->insertGetId([
                'user_id' => $userId,
                'start_time' => now()->subHours(8),
                'end_time' => null,
                'starting_cash' => 5000.00,
            ]);
        }

        $chargeCodeList = DB::table('chargecodes')->pluck('charge_code')->toArray();
        if (empty($chargeCodeList)) {
            $chargeCodeList = [100, 200, 300, 401, 403];
        }

        $now = Carbon::now();
        $chunkSize = 1000;
        $totalInsertedGuests = 0;
        $totalInsertedFolios = 0;
        $totalInsertedBookings = 0;
        $totalInsertedTx = 0;

        $maxExistingFolio = DB::table('folios')->max('folio_id') ?? 0;

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($batch = 0; $batch < $count; $batch += $chunkSize) {
            $currentChunk = min($chunkSize, $count - $batch);

            $guestsData = [];
            for ($i = 0; $i < $currentChunk; $i++) {
                $guestsData[] = [
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'address_line1' => $faker->streetAddress,
                    'address_line2' => 'STRESS_TEST',
                    'contact_number' => substr($faker->phoneNumber, 0, 20),
                    'created_at' => $now,
                ];
            }

            DB::table('guests')->insert($guestsData);
            $totalInsertedGuests += count($guestsData);

            $recentGuestIds = DB::table('guests')
                ->where('address_line2', 'STRESS_TEST')
                ->orderByDesc('guest_id')
                ->limit($currentChunk)
                ->pluck('guest_id')
                ->reverse()
                ->values()
                ->toArray();

            $foliosData = [];
            $bookingData = [];
            $transactionData = [];

            foreach ($recentGuestIds as $index => $gId) {
                $folioSeq = $maxExistingFolio + $totalInsertedFolios + 1;
                $folioNum = 'STRESS-F'.str_pad($folioSeq, 7, '0', STR_PAD_LEFT);
                $regNum = 'REG'.str_pad($folioSeq, 7, '0', STR_PAD_LEFT);
                $accNum = 'ACC'.str_pad($folioSeq, 7, '0', STR_PAD_LEFT);

                $statusChoice = $faker->randomElement(['OPEN', 'OPEN', 'CLOSED']);
                $marketSeg = $faker->randomElement(['LEISURE', 'CORPORATE', 'WALK_IN', 'GOVERNMENT']);
                $folioType = $faker->randomElement(['GUEST', 'GUEST', 'GUEST', 'HOUSE']);

                $foliosData[] = [
                    'folio_number' => $folioNum,
                    'registration_number' => $regNum,
                    'account_number' => $accNum,
                    'guest_id' => $gId,
                    'market_segment' => $marketSeg,
                    'billing_arrangements' => 'DIRECT',
                    'special_arrangements' => 'STRESS_TEST',
                    'num_pax' => rand(1, 4),
                    'has_joiner' => (bool) rand(0, 1),
                    'num_free_breakfasts' => rand(0, 2),
                    'breakfast_code' => 'YES',
                    'symbol' => 'PHP',
                    'folio_type' => $folioType,
                    'status' => $statusChoice,
                ];

                $totalInsertedFolios++;
            }

            DB::table('folios')->insert($foliosData);

            $recentFolioIds = DB::table('folios')
                ->where('special_arrangements', 'STRESS_TEST')
                ->orderByDesc('folio_id')
                ->limit($currentChunk)
                ->pluck('folio_id')
                ->reverse()
                ->values()
                ->toArray();

            foreach ($recentFolioIds as $fId) {
                $roomId = $rooms[array_rand($rooms)];
                $bookingStatus = $faker->randomElement(['CHECKED_IN', 'CHECKED_OUT', 'RESERVED', 'CANCELLED']);

                $arrDate = $now->copy();
                if ($bookingStatus === 'CHECKED_OUT') {
                    $arrDate->subDays(rand(5, 60));
                    $depDate = $arrDate->copy()->addDays(rand(1, 5));
                    $actIn = $arrDate->copy()->setTime(14, 0)->format('Y-m-d H:i:s');
                    $actOut = $depDate->copy()->setTime(11, 0)->format('Y-m-d H:i:s');
                } elseif ($bookingStatus === 'CHECKED_IN') {
                    $arrDate->subDays(rand(0, 3));
                    $depDate = $arrDate->copy()->addDays(rand(1, 5));
                    $actIn = $arrDate->copy()->setTime(14, 0)->format('Y-m-d H:i:s');
                    $actOut = null;
                } elseif ($bookingStatus === 'RESERVED') {
                    $arrDate->addDays(rand(1, 30));
                    $depDate = $arrDate->copy()->addDays(rand(1, 5));
                    $actIn = null;
                    $actOut = null;
                } else {
                    $arrDate->subDays(rand(1, 15));
                    $depDate = $arrDate->copy()->addDays(rand(1, 3));
                    $actIn = null;
                    $actOut = null;
                }

                $bookingData[] = [
                    'folio_id' => $fId,
                    'room_id' => $roomId,
                    'arrival_date' => $arrDate->toDateString(),
                    'arrival_time' => '14:00',
                    'departure_date' => $depDate->toDateString(),
                    'departure_time' => '11:00',
                    'actual_check_in' => $actIn,
                    'actual_check_out' => $actOut,
                    'status' => $bookingStatus,
                ];

                // Create 1 to 4 transactions per folio
                $txCount = rand(1, 4);
                for ($t = 0; $t < $txCount; $t++) {
                    $isCharge = (bool) rand(0, 1);
                    $cCode = $chargeCodeList[array_rand($chargeCodeList)];
                    $amt = (float) rand(200, 4500);

                    $transactionData[] = [
                        'folio_id' => $fId,
                        'charge_code' => $cCode,
                        'shift_id' => $shiftId,
                        'user_id' => $userId,
                        'transaction_date' => $arrDate->toDateString(),
                        'charge_number' => 'STRESS-CHG-'.rand(100000, 999999),
                        'payment_method' => $isCharge ? 'NONE' : $faker->randomElement(['CASH', 'CREDIT_CARD']),
                        'reference_notes' => '[STRESS_TEST] Generated transaction',
                        'charge_amount' => $isCharge ? $amt : 0.00,
                        'credit_amount' => $isCharge ? 0.00 : $amt,
                        'timestamp' => $now,
                    ];
                }
            }

            DB::table('bookings')->insert($bookingData);
            $totalInsertedBookings += count($bookingData);

            // Chunk transaction inserts
            foreach (array_chunk($transactionData, 1000) as $txChunk) {
                DB::table('transactions')->insert($txChunk);
                $totalInsertedTx += count($txChunk);
            }

            $bar->advance($currentChunk);
        }

        $bar->finish();
        $this->newLine();

        $duration = round(microtime(true) - $startTime, 2);
        $this->info("Successfully generated stress data in {$duration}s:");
        $this->table(
            ['Record Type', 'Count Inserted'],
            [
                ['Guests', number_format($totalInsertedGuests)],
                ['Folios', number_format($totalInsertedFolios)],
                ['Bookings', number_format($totalInsertedBookings)],
                ['Transactions', number_format($totalInsertedTx)],
            ]
        );

        return Command::SUCCESS;
    }
}
