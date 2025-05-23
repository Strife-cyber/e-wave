<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GroupMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        App\Models\GroupMessage::factory()->count(10)->create();
    }
}
