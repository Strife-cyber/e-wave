<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        App\Models\Badge::factory()->count(10)->create();
    }
}
