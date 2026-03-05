<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PointsTransactionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['earn', 'redeem', 'refund', 'expire', 'admin_adjust'];

        foreach ($types as $name) {
            DB::table('points_transaction_types')->updateOrInsert(
                ['name' => $name],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}