<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Sedan'],
            ['name' => 'SUV'],
            ['name' => 'Hatchback'],
            ['name' => 'Pickup'],
            ['name' => 'Van'],
        ];

        foreach ($types as $type) {
            DB::table('car_types')->updateOrInsert(
                ['name' => $type['name']],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}