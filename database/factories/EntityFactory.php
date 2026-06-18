<?php

namespace Insynnia\Badges\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Insynnia\Badges\Models\Entity;

/**
 * @extends Factory<Entity>
 */
class EntityFactory extends Factory
{
    protected $model = Entity::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_id' => 'user_'.fake()->unique()->numerify('######'),
            'display_name' => fake()->name(),
            'avatar_url' => fake()->imageUrl(),
        ];
    }
}
