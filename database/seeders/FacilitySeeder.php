<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\FacilityReservation;
use App\Models\FacilitySet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        // Default Facilities based on typical hotel requirements
        $facilities = [
            [
                'name' => 'Grand Function Hall',
                'description' => "Our largest event space featuring elegant chandeliers, a built-in stage, and advanced AV equipment. Perfect for weddings, large corporate gatherings, and formal banquets.\n\nIncludes access to the prep kitchen and dedicated restrooms.",
                'capacity' => 300,
                'rate' => 15000,
                'rate_type' => 'daily',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Executive Conference Room',
                'description' => "A professional setting designed for productive meetings and presentations. Equipped with a large conference table, ergonomic seating, a 75-inch smart display, and video conferencing capabilities.\n\nComplimentary coffee and water service included.",
                'capacity' => 20,
                'rate' => 1200,
                'rate_type' => 'hourly',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Indoor Basketball Court',
                'description' => "Full-sized hardwood indoor court suitable for friendly games, tournaments, and sports clinics. Well-ventilated and brightly lit with spectator bleachers on one side.\n\nPlease bring your own sports equipment.",
                'capacity' => 50,
                'rate' => 800,
                'rate_type' => 'hourly',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Rooftop Event Space',
                'description' => 'An open-air venue offering panoramic views of the city skyline. Ideal for sunset cocktails, intimate dinners, and evening socials. Features ambient string lighting and a built-in bar counter.',
                'capacity' => 100,
                'rate' => 8000,
                'rate_type' => 'daily',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Meeting Room A',
                'description' => 'A versatile mid-sized room perfect for workshops, training sessions, and breakout groups. Setup can be customized (classroom, U-shape, or theater style).',
                'capacity' => 40,
                'rate' => 600,
                'rate_type' => 'hourly',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Function Hall A',
                'description' => 'The western wing of our function hall, fully equipped with audio-visual system, ambient lighting, and stage access. Can be booked independently or together as Function Hall.',
                'capacity' => 150,
                'rate' => 8000,
                'rate_type' => 'daily',
                'hourly_rate' => 800,
                'daily_rate' => 8000,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Function Hall B',
                'description' => 'The eastern wing of our function hall, offering flexible seating configurations, banquet buffet area, and dedicated entrance. Can be booked independently or together as Function Hall.',
                'capacity' => 150,
                'rate' => 8000,
                'rate_type' => 'daily',
                'hourly_rate' => 800,
                'daily_rate' => 8000,
                'is_active' => true,
                'sort_order' => 7,
            ],
        ];

        foreach ($facilities as $facilityData) {
            Facility::firstOrCreate(['name' => $facilityData['name']], $facilityData);
        }

        // Seed Consolidated Facility Set: Function Hall
        $hallA = Facility::where('name', 'Function Hall A')->first();
        $hallB = Facility::where('name', 'Function Hall B')->first();

        if ($hallA && $hallB) {
            $functionHallSet = FacilitySet::firstOrCreate(
                ['name' => 'Function Hall'],
                [
                    'prefix_code' => 'EVSUOCFH',
                    'description' => "Our premier combined venue uniting Function Hall A and Function Hall B. Ideal for large conferences, grand wedding receptions, and academic convocations.\n\nReserving this facility set guarantees exclusive use of both Function Hall A and Function Hall B.",
                    'capacity' => 300,
                    'hourly_rate' => 1500,
                    'daily_rate' => 15000,
                    'rate' => 15000,
                    'rate_type' => 'daily',
                    'is_active' => true,
                    'sort_order' => 1,
                ]
            );

            $functionHallSet->facilities()->syncWithoutDetaching([$hallA->facility_id, $hallB->facility_id]);
        }

        // Generate some sample reservations for testing
        // Only if not in production
        if (! app()->environment('production')) {
            $grandHall = Facility::where('name', 'Grand Function Hall')->first();
            $conference = Facility::where('name', 'Executive Conference Room')->first();
            $court = Facility::where('name', 'Indoor Basketball Court')->first();

            // Past approved reservation
            FacilityReservation::factory()->create([
                'facility_id' => $court->facility_id,
                'reservation_date' => Carbon::now()->subDays(2)->toDateString(),
                'status' => 'approved',
                'start_time' => '17:00:00',
                'end_time' => '19:00:00',
                'duration_hours' => 2.0,
                'estimated_amount' => 1600.00,
            ]);

            // Future approved reservation
            FacilityReservation::factory()->create([
                'facility_id' => $grandHall->facility_id,
                'reservation_date' => Carbon::now()->addDays(5)->toDateString(),
                'status' => 'approved',
                'start_time' => '08:00:00',
                'end_time' => '22:00:00',
                'duration_hours' => 14.0,
                'estimated_amount' => 15000.00, // Daily rate
            ]);

            // Pending reservation
            FacilityReservation::factory()->create([
                'facility_id' => $conference->facility_id,
                'reservation_date' => Carbon::now()->addDays(3)->toDateString(),
                'status' => 'pending',
                'start_time' => '13:00:00',
                'end_time' => '16:00:00',
                'duration_hours' => 3.0,
                'estimated_amount' => 3600.00,
            ]);

            // Rejected reservation
            FacilityReservation::factory()->create([
                'facility_id' => $court->facility_id,
                'reservation_date' => Carbon::now()->addDays(1)->toDateString(),
                'status' => 'rejected',
                'admin_notes' => 'Court is closed for maintenance on this date.',
            ]);
        }
    }
}
