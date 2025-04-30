<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CollaborationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        App\Models\Collaboration::factory()->count(10)->create();
    }
}
