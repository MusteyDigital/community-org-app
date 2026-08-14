<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'type' => fake()->randomElement(['church', 'mosque', 'community']),
            'description' => fake()->sentence(),
            'address' => fake()->address(),
            'created_by' => \App\Models\User::factory(),
            'status' => 'approved',
        ];
    }
}