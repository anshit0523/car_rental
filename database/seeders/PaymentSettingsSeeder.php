<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_settings')->upsert(
            [
                [
                    'provider' => 'paypal',
                    'environment' => 'sandbox',
                    'client_id' => 'YOUR_SANDBOX_CLIENT_ID',
                    'client_secret' => 'YOUR_SANDBOX_SECRET',
                    'business_email' => 'business@example.com',
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ],
            ['provider', 'environment'], // UNIQUE keys
            ['client_id', 'client_secret', 'business_email', 'active', 'updated_at']
        );
    }
}
