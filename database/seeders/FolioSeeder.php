<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guestIds = DB::table('guests')->pluck('guest_id')->toArray();
        $folios = [];

        for ($i = 1; $i <= count($guestIds); $i++) {
            $folios[] = [
                'folio_number' => 'F'.str_pad($i, 5, '0', STR_PAD_LEFT),
                'registration_number' => 'REG'.str_pad($i, 4, '0', STR_PAD_LEFT),
                'account_number' => 'ACC'.str_pad($i, 5, '0', STR_PAD_LEFT),
                'guest_id' => $guestIds[$i - 1],
                'market_segment' => 'LEISURE',
                'billing_arrangements' => 'DIRECT',
                'special_arrangements' => null,
                'num_pax' => rand(1, 4),
                'has_joiner' => false,
                'num_free_breakfasts' => 0,
                'breakfast_code' => 'NO',
                'symbol' => 'USD',
                'folio_type' => 'GUEST',
                'status' => 'OPEN',
            ];
        }

        DB::table('folios')->insert($folios);
    }
}
