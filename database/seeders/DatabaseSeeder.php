<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\CarSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\BrandSeeder;
use Database\Seeders\StatusSeeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\FuelTypeSeeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\TransmissionSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
    
         BrandSeeder::class,
         CarSeeder::class,
        CarTypeSeeder::class,
        FuelTypeSeeder::class,
         IssueStatusSeeder::class,
           PaymentMethodsSeeder::class,
        PaymentStatusesSeeder::class,
        PointsTransactionTypeSeeder::class,
        RoleSeeder::class,
        ServiceTypeSeeder::class,
        StatusSeeder::class,
        TransmissionSeeder::class,
  
        
      
      
        
    ]);

    $this->call(RoleSeeder::class);

     if (!User::where('email', 'admin@example.com')->exists()) {
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin12345'),
                'role_id' => 1, // Admin role
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
      
    }
}
