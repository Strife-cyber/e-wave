<?php

namespace Database\Factories;

use App\Models\GroupMessage;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for creating GroupMessage model instances.
 *
 * This factory generates fake data for the GroupMessage model, which represents a message sent within
 * a group in the EduWave platform. It ensures that each message is associated with a group and a user,
 * and has a realistic sent timestamp and content.
 *
 * @extends Factory<GroupMessage>
 */
class GroupMessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = GroupMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sentAt = $this->faker->dateTimeBetween('-1 month');

        return [
            'group_id' => Group::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->sentence(),
            'sent_at' => $sentAt
        ];
    }
}
