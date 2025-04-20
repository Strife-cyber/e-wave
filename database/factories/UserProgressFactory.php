<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProgress>
 */
class UserProgressFactory extends Factory
{
    protected $model = UserProgress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'lesson_id' => Lesson::factory(),
            'status' => fake()->randomElement(['started', 'completed', 'failed']),
            'score' => fake()->numberBetween(0, 100)
        ];
    }
}
