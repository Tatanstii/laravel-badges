<?php

namespace Insynnia\Badges\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Insynnia\Badges\Models\Badge;

/**
 * @extends Factory<Badge>
 */
class BadgeFactory extends Factory
{
    protected $model = Badge::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['learning', 'achievement', 'social', 'streak', 'special']),
            'tier' => fake()->randomElement(['bronze', 'silver', 'gold', 'platinum']),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
