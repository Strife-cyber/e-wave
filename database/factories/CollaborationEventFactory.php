<?php

namespace Database\Factories;

use App\Models\Collaboration;
use App\Models\CollaborationEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CollaborationEvent>
 */
class CollaborationEventFactory extends Factory
{
    protected $model = CollaborationEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collaboration_id' => Collaboration::factory(),
            'user_id' => User::factory(),
            'joined_at' => now(),
            'left_at' => now()->addHours(),
        ];
    }
}
