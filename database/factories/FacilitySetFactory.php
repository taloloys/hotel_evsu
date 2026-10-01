<?php

namespace Database\Factories;

use App\Models\FacilitySet;
use Illuminate\Database\Eloquent\Factories\Factory;

class FacilitySetFactory extends Factory
{
    protected $model = FacilitySet::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true).' Set',
            'prefix_code' => null,
            'description' => fake()->paragraph(),
            'capacity' => fake()->numberBetween(50, 400),
            'rate' => fake()->randomFloat(2, 500, 20000),
            'rate_type' => fake()->randomElement(['hourly', 'daily']),
            'hourly_rate' => 1000.00,
            'daily_rate' => 15000.00,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
