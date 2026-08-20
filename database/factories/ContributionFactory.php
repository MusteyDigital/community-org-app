<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContributionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'member_id' => Member::factory(),
            'amount' => fake()->randomFloat(2, 500, 50000),
            'category' => fake()->randomElement(['general', 'zakat', 'sadaqah']),
            'note' => fake()->optional()->sentence(),
            'contributed_at' => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'payment_reference' => null,
            'source' => 'admin',
            'payment_status' => 'completed',
        ];
    }
}
