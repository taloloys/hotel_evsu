<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $folios = DB::table('folios')->pluck('folio_id')->toArray();
        $rooms = DB::table('rooms')->pluck('room_id')->toArray();
        $today = Carbon::now();
        $bookings = [];

        // Today's arrivals (5 bookings)
        for ($i = 0; $i < 5; $i++) {
            $roomId = $rooms[array_rand($rooms)];
            $folioId = $folios[array_rand($folios)];

            $bookings[] = [
                'folio_id' => $folioId,
                'room_id' => $roomId,
                'arrival_date' => $today->toDateString(),
                'arrival_time' => '14:00',
                'departure_date' => $today->copy()->addDays(rand(1, 5))->toDateString(),
                'departure_time' => '11:00',
                'actual_check_in' => null,
                'actual_check_out' => null,
                'status' => 'RESERVED',
            ];
        }

        // Today's departures (5 bookings)
        for ($i = 0; $i < 5; $i++) {
            $roomId = $rooms[array_rand($rooms)];
            $folioId = $folios[array_rand($folios)];

            $bookings[] = [
                'folio_id' => $folioId,
                'room_id' => $roomId,
                'arrival_date' => $today->copy()->subDays(rand(1, 5))->toDateString(),
                'arrival_time' => '14:00',
                'departure_date' => $today->toDateString(),
                'departure_time' => '11:00',
                'actual_check_in' => $today->copy()->subDays(rand(1, 5))->setTime(14, rand(0, 59))->format('Y-m-d H:i:s'),
                'actual_check_out' => null,
                'status' => 'CHECKED_IN',
            ];
        }

        // Past bookings (10 bookings)
        for ($i = 0; $i < 10; $i++) {
            $roomId = $rooms[array_rand($rooms)];
            $folioId = $folios[array_rand($folios)];
            $arrivalDate = $today->copy()->subDays(rand(10, 30));
            $departureDate = $arrivalDate->copy()->addDays(rand(1, 5));

            $bookings[] = [
                'folio_id' => $folioId,
                'room_id' => $roomId,
                'arrival_date' => $arrivalDate->toDateString(),
                'arrival_time' => '14:00',
                'departure_date' => $departureDate->toDateString(),
                'departure_time' => '11:00',
                'actual_check_in' => $arrivalDate->setTime(14, rand(0, 59))->format('Y-m-d H:i:s'),
                'actual_check_out' => $departureDate->setTime(10, rand(0, 59))->format('Y-m-d H:i:s'),
                'status' => 'CHECKED_OUT',
            ];
        }

        DB::table('bookings')->insert($bookings);

        foreach ($bookings as $booking) {
            if ($booking['status'] === 'CHECKED_IN') {
                DB::table('rooms')->where('room_id', $booking['room_id'])->update(['status' => 'OCCUPIED']);
            } elseif ($booking['status'] === 'RESERVED') {
                DB::table('rooms')->where('room_id', $booking['room_id'])->update(['status' => 'RESERVED']);
            }
        }
    }
}
