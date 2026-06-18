<?php

namespace Insynnia\Badges\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Insynnia\Badges\Models\Badge;
use Insynnia\Badges\Models\Trigger;

/**
 * @extends Factory<Trigger>
 */
class TriggerFactory extends Factory
{
    protected $model = Trigger::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'badge_id' => Badge::factory(),
            'slug' => fake()->unique()->slug(2),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
