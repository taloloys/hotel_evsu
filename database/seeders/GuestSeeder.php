<?php

namespace Database\Seeders;

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $guests = [
            ['first_name' => 'John', 'last_name' => 'Doe', 'address_line1' => '123 Main St', 'address_line2' => 'Apt 4', 'contact_number' => '+1234567890'],
            ['first_name' => 'Jane', 'last_name' => 'Smith', 'address_line1' => '456 Oak Ave', 'address_line2' => null, 'contact_number' => '+1987654321'],
            ['first_name' => 'Robert', 'last_name' => 'Johnson', 'address_line1' => '789 Pine Rd', 'address_line2' => 'Suite 100', 'contact_number' => '+1555123456'],
            ['first_name' => 'Emily', 'last_name' => 'Williams', 'address_line1' => '321 Elm St', 'address_line2' => null, 'contact_number' => '+1666789012'],
            ['first_name' => 'Michael', 'last_name' => 'Brown', 'address_line1' => '654 Maple Dr', 'address_line2' => 'Unit B', 'contact_number' => '+1777456789'],
            ['first_name' => 'Sarah', 'last_name' => 'Davis', 'address_line1' => '987 Cedar Ln', 'address_line2' => null, 'contact_number' => '+1888012345'],
            ['first_name' => 'David', 'last_name' => 'Miller', 'address_line1' => '147 Birch St', 'address_line2' => 'Floor 2', 'contact_number' => '+1999345678'],
            ['first_name' => 'Lisa', 'last_name' => 'Wilson', 'address_line1' => '258 Spruce Ave', 'address_line2' => null, 'contact_number' => '+1444678901'],
            ['first_name' => 'James', 'last_name' => 'Moore', 'address_line1' => '369 Willow St', 'address_line2' => 'Apt 1', 'contact_number' => '+1333901234'],
            ['first_name' => 'Jennifer', 'last_name' => 'Taylor', 'address_line1' => '741 Ash Rd', 'address_line2' => null, 'contact_number' => '+1222234567'],
        ];

        for ($i = 0; $i < 150; $i++) {
            $guests[] = [
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'address_line1' => $faker->streetAddress,
                'address_line2' => $faker->optional(0.3)->secondaryAddress,
                'contact_number' => $faker->phoneNumber,
            ];
        }

        DB::table('guests')->insert($guests);
    }
}
