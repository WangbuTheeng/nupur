<?php

namespace Database\Seeders;

use App\Models\Route;
use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get city IDs for route creation
        $kathmandu = City::where('name', 'Kathmandu')->first();
        $pokhara = City::where('name', 'Pokhara')->first();
        $bharatpur = City::where('name', 'Bharatpur')->first();
        $butwal = City::where('name', 'Butwal')->first();
        $dharan = City::where('name', 'Dharan')->first();
        $biratnagar = City::where('name', 'Biratnagar')->first();
        $janakpur = City::where('name', 'Janakpur')->first();
        $birgunj = City::where('name', 'Birgunj')->first();
        $hetauda = City::where('name', 'Hetauda')->first();
        $dhangadhi = City::where('name', 'Dhangadhi')->first();
        $birendranagar = City::where('name', 'Birendranagar')->first();

        $routes = [
            [
                'name' => 'Kathmandu - Pokhara',
                'source_city_id' => $kathmandu->id,
                'destination_city_id' => $pokhara->id,
                'distance_km' => 200.5,
                'base_fare' => 800.00,
                'estimated_duration' => '06:00:00',
                'stops' => ['Mugling', 'Dumre'],
                'is_active' => true
            ],
            [
                'name' => 'Kathmandu - Chitwan',
                'source_city_id' => $kathmandu->id,
                'destination_city_id' => $bharatpur->id,
                'distance_km' => 146.2,
                'base_fare' => 600.00,
                'estimated_duration' => '04:30:00',
                'stops' => ['Mugling'],
                'is_active' => true
            ],
            [
                'name' => 'Kathmandu - Butwal',
                'source_city_id' => $kathmandu->id,
                'destination_city_id' => $butwal->id,
                'distance_km' => 290.8,
                'base_fare' => 1200.00,
                'estimated_duration' => '08:00:00',
                'stops' => ['Mugling', 'Narayanghat', 'Bharatpur'],
                'is_active' => true
            ],
            [
                'name' => 'Pokhara - Chitwan',
                'source_city_id' => $pokhara->id,
                'destination_city_id' => $bharatpur->id,
                'distance_km' => 120.3,
                'base_fare' => 500.00,
                'estimated_duration' => '03:30:00',
                'stops' => ['Dumre'],
                'is_active' => true
            ],
            [
                'name' => 'Kathmandu - Dharan',
                'source_city_id' => $kathmandu->id,
                'destination_city_id' => $dharan->id,
                'distance_km' => 385.7,
                'base_fare' => 1500.00,
                'estimated_duration' => '10:00:00',
                'stops' => ['Dhulikhel', 'Sindhuli', 'Gaighat'],
                'is_active' => true
            ],
            [
                'name' => 'Kathmandu - Biratnagar',
                'source_city_id' => $kathmandu->id,
                'destination_city_id' => $biratnagar->id,
                'distance_km' => 543.2,
                'base_fare' => 2000.00,
                'estimated_duration' => '12:00:00',
                'stops' => ['Dhulikhel', 'Sindhuli', 'Gaighat', 'Dharan'],
                'is_active' => true
            ],
            [
                'name' => 'Kathmandu - Janakpur',
                'source_city_id' => $kathmandu->id,
                'destination_city_id' => $janakpur->id,
                'distance_km' => 225.4,
                'base_fare' => 900.00,
                'estimated_duration' => '07:00:00',
                'stops' => ['Dhulikhel', 'Sindhuli'],
                'is_active' => true
            ],
            [
                'name' => 'Kathmandu - Birgunj',
                'source_city_id' => $kathmandu->id,
                'destination_city_id' => $birgunj->id,
                'distance_km' => 135.8,
                'base_fare' => 550.00,
                'estimated_duration' => '04:00:00',
                'stops' => ['Hetauda'],
                'is_active' => true
            ],
            [
                'name' => 'Pokhara - Butwal',
                'source_city_id' => $pokhara->id,
                'destination_city_id' => $butwal->id,
                'distance_km' => 125.6,
                'base_fare' => 500.00,
                'estimated_duration' => '03:30:00',
                'stops' => ['Tansen'],
                'is_active' => true
            ],
            [
                'name' => 'Kathmandu - Dhangadhi',
                'source_city_id' => $kathmandu->id,
                'destination_city_id' => $dhangadhi->id,
                'distance_km' => 618.5,
                'base_fare' => 2500.00,
                'estimated_duration' => '15:00:00',
                'stops' => ['Mugling', 'Narayanghat', 'Butwal', 'Nepalgunj'],
                'is_active' => true
            ]
        ];

        foreach ($routes as $route) {
            Route::create($route);
        }
    }
}
