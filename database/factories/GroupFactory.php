<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Group>
 */
class GroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word().' Group',
            'course_id' => Course::factory(),
            'created_by' => User::factory(),
        ];
    }
}
