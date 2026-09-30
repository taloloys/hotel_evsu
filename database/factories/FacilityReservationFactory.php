<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\FacilityReservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FacilityReservation>
 */
class FacilityReservationFactory extends Factory
{
    protected $model = FacilityReservation::class;

    public function definition(): array
    {
        return [
            'facility_id' => Facility::factory(),
            'booker_name' => fake()->name(),
            'booker_email' => fake()->safeEmail(),
            'booker_contact' => fake()->numerify('09#########'),
            'reservation_date' => fake()->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'duration_hours' => 2.00,
            'estimated_amount' => 1000.00,
            'status' => 'pending',
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ];
    }

    public function approved(): static
    {
        return $this->state([
            'status' => 'approved',
            'processed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status' => 'rejected',
            'processed_at' => now(),
        ]);
    }
}
