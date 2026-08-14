<?php

namespace Database\Factories;

use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'role' => 'member',
            'status' => 'approved',
            'is_listed' => true,
            'join_date' => now(),
            'user_id' => \App\Models\User::factory(),
            'organization_id' => \App\Models\Organization::factory(),
        ];
    }
}