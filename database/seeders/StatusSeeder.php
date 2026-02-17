<?php

namespace Database\Seeders;
use App\Models\Status;
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
 $statuses = [
            ['name' => 'Reserved'],
            ['name' => 'Active'],
            ['name' => 'Completed'],
            ['name' => 'Cancelled'],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
