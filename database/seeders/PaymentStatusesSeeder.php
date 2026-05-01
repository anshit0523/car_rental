<?php

namespace Database\Seeders;

use App\Models\PaymentStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Awaiting Payment', 'code' => 'awaiting_payment'],
            ['name' => 'Pending', 'code' => 'pending'],
            ['name' => 'Completed', 'code' => 'completed'],
            ['name' => 'Failed', 'code' => 'failed'],
            ['name' => 'Refunded', 'code' => 'refunded'],
        ];

        foreach ($statuses as $status) {
            PaymentStatus::firstOrCreate(
                ['code' => $status['code']],
                $status
            );
        }
    }
}
