<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class GroupMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        App\Models\GroupMember::factory()->count(10)->create();
    }
}
