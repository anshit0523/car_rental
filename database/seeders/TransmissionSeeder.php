<?php

namespace Database\Seeders;

use App\Models\Transmission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $types = ['Automatic', 'Manual','DCT','CVT'];
        foreach ($types as $type) {
            Transmission::firstOrCreate(['type' => $type]);
        }
    }
}
