<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RewardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        App\Models\Reward::factory()->count(10)->create();
    }
}
