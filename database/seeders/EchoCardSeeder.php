<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EchoCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        App\Models\EchoCard::factory()->count(10)->create();
    }
}
