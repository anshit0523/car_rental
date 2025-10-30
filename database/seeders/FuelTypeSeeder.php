<?php

namespace Database\Seeders;


use App\Models\FuelType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FuelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $types = ['Gasoline', 'Diesel', 'Electric', 'Hybrid'];
        foreach ($types as $type) {
            FuelType::firstOrCreate(['type' => $type]);
        }
    }
}
