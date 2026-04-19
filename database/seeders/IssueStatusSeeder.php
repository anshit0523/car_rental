<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IssueStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'pending',
                'label' => 'Pending',
                'color' => 'yellow',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'reviewing',
                'label' => 'Reviewing',
                'color' => 'blue',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'awaiting_payment',
                'label' => 'Awaiting Payment',
                'color' => 'orange',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'resolved',
                'label' => 'Resolved',
                'color' => 'green',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'rejected',
                'label' => 'Rejected',
                'color' => 'red',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('issue_statuses')->updateOrInsert(
                ['name' => $status['name']],
                $status
            );
        }
    }
}