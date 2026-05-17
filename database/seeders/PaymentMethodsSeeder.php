<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\PaymentMethods;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $methods = [
          
            ['name' => 'Cash', 'code' => 'cash'],
            ['name' => 'GCash', 'code' => 'gcash'],
             ['name' => 'Bank Transfer', 'code' => 'bank'],
        ];

        foreach ($methods as $method) {
            PaymentMethods::firstOrCreate(
                ['code' => $method['code']],
                $method
            );
        }
    }
    
}
