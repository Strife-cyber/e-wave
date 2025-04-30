<?php

namespace Database\Factories;

use App\Models\EchoCard;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EchoCard>
 */
class EchoCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->sentence().' A) '.$this->faker->word().' B) '.$this->faker->word(),
            'source_type' => $this->faker->randomElement(['chat', 'whiteboard', 'question']),
            'source_content' => $this->faker->sentence(),
            'lesson_id' => Lesson::factory(),
            'created_by' => User::factory(),
        ];
    }
}
