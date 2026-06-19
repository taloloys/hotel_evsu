<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = ['Single Room', 'Twin Room', 'Studio Room', 'Deluxe Room', 'Suite', 'President Suite', 'Connecting Room'];
        $baseRates = [50, 60, 75, 100, 150, 250, 200];

        $rooms = [];
        $roomNumber = 101;

        for ($floor = 1; $floor <= 3; $floor++) {
            for ($roomNum = 1; $roomNum <= 20; $roomNum++) {
                $typeIndex = array_rand($roomTypes);
                $rooms[] = [
                    'room_number' => $roomNumber,
                    'room_type' => $roomTypes[$typeIndex],
                    'base_rate' => $baseRates[$typeIndex],
                    'status' => 'AVAILABLE',
                ];
                $roomNumber++;
            }
        }

        DB::table('rooms')->insert($rooms);
    }
}
