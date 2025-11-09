<?php

namespace Database\Seeders;

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
        DB::table('payment_statuses')->insert([
            ['name' => 'Pending'],
            ['name' => 'Processing'],
            ['name' => 'Completed'],
            ['name' => 'Failed'],
            ['name' => 'Cancelled'],
            ['name' => 'Refunded'],
            
        ]);
    }
}
