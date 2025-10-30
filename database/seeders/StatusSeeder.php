<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('statuses')->insert([
            ['name' => 'reserved', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'completed', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'cancelled', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
