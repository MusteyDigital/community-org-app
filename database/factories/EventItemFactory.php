<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'event_date' => fake()->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
            'event_time' => fake()->time('H:i'),
            'location' => fake()->streetAddress(),
            'created_by' => User::factory(),
            'organization_id' => Organization::factory(),
        ];
    }
}
