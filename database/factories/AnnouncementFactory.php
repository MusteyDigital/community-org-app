<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'is_pinned' => false,
            'published_at' => now(),
            'type' => fake()->randomElement(['general', 'burial']),
            'organization_id' => \App\Models\Organization::factory(),
        ];
    }
}