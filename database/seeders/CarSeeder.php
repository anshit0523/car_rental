<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Brand;
use App\Models\FuelType;
use App\Models\Transmission;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = Brand::pluck('id')->toArray();
        $transmissions = Transmission::pluck('id')->toArray();
        $fuelTypes = FuelType::pluck('id')->toArray();

        $cars = [
            ['model' => 'Civic', 'price_per_day' => 2500, 'seats' => 5, 'description' => 'Reliable and efficient city car.'],
            ['model' => 'Fortuner', 'price_per_day' => 4500, 'seats' => 7, 'description' => 'Spacious SUV for family travel.'],
            ['model' => 'Mustang', 'price_per_day' => 7000, 'seats' => 4, 'description' => 'High-performance sports car.'],
            ['model' => 'Vios', 'price_per_day' => 2000, 'seats' => 5, 'description' => 'Economical and comfortable sedan.'],
            ['model' => 'Hilux', 'price_per_day' => 4000, 'seats' => 5, 'description' => 'Durable pickup for tough terrain.'],
            ['model' => 'Accord', 'price_per_day' => 3500, 'seats' => 5, 'description' => 'Elegant executive car.'],
            ['model' => 'Ranger', 'price_per_day' => 4200, 'seats' => 5, 'description' => 'Powerful and rugged pickup.'],
            ['model' => 'Almera', 'price_per_day' => 2200, 'seats' => 5, 'description' => 'Compact sedan for daily use.'],
            ['model' => 'Xtrail', 'price_per_day' => 3800, 'seats' => 7, 'description' => 'Comfortable crossover SUV.'],
            ['model' => 'Camry', 'price_per_day' => 4500, 'seats' => 5, 'description' => 'Premium sedan with luxury interior.'],
        ];

        foreach ($cars as $car) {
            Car::create([
                'brand_id' => $brands[array_rand($brands)],
                'transmission_id' => $transmissions[array_rand($transmissions)],
                'fuel_type_id' => $fuelTypes[array_rand($fuelTypes)],
                'model' => $car['model'],
                'seats' => $car['seats'],
                'price_per_day' => $car['price_per_day'],
                'description' => $car['description'],
                'images' => json_encode([
                    'https://via.placeholder.com/600x400?text=' . urlencode($car['model'])
                ]),
                'active' => true,
            ]);
        }
    }
}
