<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        App\Models\UserProgress::factory()->count(10)->create();
    }
}
