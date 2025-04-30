<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Badge>
 */
class BadgeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word().' Badge',
            'image_url' => $this->faker->imageUrl(50, 50, 'badge'),
            'points_required' => $this->faker->numberBetween(50, 500),
        ];
    }
}
