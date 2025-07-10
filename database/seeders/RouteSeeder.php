<?php

namespace Database\Seeders;

use App\Models\Route;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $routes = [
            [
                'name' => 'Kathmandu - Pokhara',
                'source_city' => 'Kathmandu',
                'destination_city' => 'Pokhara',
                'distance_km' => 200.5,
                'base_fare' => 800.00,
                'estimated_duration' => '06:00:00',
                'stops' => ['Mugling', 'Dumre'],
                'is_active' => true
            ],
            [
                'name' => 'Kathmandu - Chitwan',
                'source_city' => 'Kathmandu',
                'destination_city' => 'Chitwan',
                'distance_km' => 146.2,
                'base_fare' => 600.00,
                'estimated_duration' => '04:30:00',
                'stops' => ['Mugling'],
                'is_active' => true
            ],
            [
                'name' => 'Kathmandu - Butwal',
                'source_city' => 'Kathmandu',
                'destination_city' => 'Butwal',
                'distance_km' => 290.8,
                'base_fare' => 1200.00,
                'estimated_duration' => '08:00:00',
                'stops' => ['Mugling', 'Narayanghat', 'Bharatpur'],
                'is_active' => true
            ],
            [
                'name' => 'Pokhara - Chitwan',
                'source_city' => 'Pokhara',
                'destination_city' => 'Chitwan',
                'distance_km' => 120.3,
                'base_fare' => 500.00,
                'estimated_duration' => '03:30:00',
                'stops' => ['Dumre'],
                'is_active' => true
            ],
            [
                'name' => 'Kathmandu - Dharan',
                'source_city' => 'Kathmandu',
                'destination_city' => 'Dharan',
                'distance_km' => 385.7,
                'base_fare' => 1500.00,
                'estimated_duration' => '10:00:00',
                'stops' => ['Dhulikhel', 'Sindhuli', 'Gaighat'],
                'is_active' => true
            ]
        ];

        foreach ($routes as $route) {
            Route::create($route);
        }
    }
}
