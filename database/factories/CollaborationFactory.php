<?php

namespace Database\Factories;

use App\Models\Collaboration;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for creating Collaboration model instances.
 *
 * This factory generates fake data for the Collaboration model, which represents a collaboration session
 * in the EduWave platform. It ensures that each collaboration session is associated with a group and has
 * realistic start and end times, along with a type (e.g., chat, whiteboard, video).
 *
 * @extends Factory<Collaboration>
 */
class CollaborationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Collaboration::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startedAt = $this->faker->dateTimeBetween('-1 month');
        $endedAt = $this->faker->optional(0.7)->dateTimeBetween($startedAt); // 70% chance of having an ended_at

        return [
            'type' => $this->faker->randomElement(['chat', 'whiteboard', 'video']),
            'group_id' => Group::factory(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
        ];
    }
}
