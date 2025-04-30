<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CollaborationEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        App\Models\CollaborationEvent::factory()->count(10)->create();
    }
}
