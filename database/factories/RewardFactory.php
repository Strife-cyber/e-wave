<?php

namespace Database\Factories;

use App\Models\Badge;
use App\Models\Reward;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reward>
 */
class RewardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'points' => fake()->numberBetween(10, 100),
            'badge_id' => Badge::factory(),
        ];
    }
}
