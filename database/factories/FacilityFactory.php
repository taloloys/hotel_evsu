<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    protected $model = Facility::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true).' Hall',
            'description' => fake()->paragraph(),
            'capacity' => fake()->numberBetween(20, 300),
            'rate' => fake()->randomFloat(2, 500, 5000),
            'rate_type' => fake()->randomElement(['hourly', 'daily']),
            'images' => [],
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function hourly(float $rate = 500.00): static
    {
        return $this->state(['rate_type' => 'hourly', 'rate' => $rate]);
    }

    public function daily(float $rate = 3000.00): static
    {
        return $this->state(['rate_type' => 'daily', 'rate' => $rate]);
    }
}
