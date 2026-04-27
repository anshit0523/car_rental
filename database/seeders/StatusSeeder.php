<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Reserved'],
            ['name' => 'Confirmed'],
            ['name' => 'Active'],
            ['name' => 'Completed'],
            ['name' => 'Cancelled'],
            ['name' => 'Return'],
            ['name' => 'Checkup'],
            ['name' => 'Damage'],
            ['name' => 'Needs Repair'],
            ['name' => 'Failed'],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}