<?php

namespace Database\Seeders;

use App\Models\BusType;
use Illuminate\Database\Seeder;

class BusTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $busTypes = [
            [
                'name' => 'AC Deluxe',
                'description' => 'Air-conditioned deluxe bus with comfortable seating',
                'total_seats' => 32,
                'seat_layout' => [
                    'rows' => 8,
                    'columns' => 4,
                    'aisle_position' => 2
                ],
                'base_fare_multiplier' => 1.5,
                'is_active' => true
            ],
            [
                'name' => 'Non-AC Regular',
                'description' => 'Standard non-air-conditioned bus',
                'total_seats' => 40,
                'seat_layout' => [
                    'rows' => 10,
                    'columns' => 4,
                    'aisle_position' => 2
                ],
                'base_fare_multiplier' => 1.0,
                'is_active' => true
            ],
            [
                'name' => 'VIP Sleeper',
                'description' => 'Premium sleeper bus with bed facilities',
                'total_seats' => 24,
                'seat_layout' => [
                    'rows' => 6,
                    'columns' => 4,
                    'aisle_position' => 2
                ],
                'base_fare_multiplier' => 2.0,
                'is_active' => true
            ],
            [
                'name' => 'Tourist Bus',
                'description' => 'Tourist-friendly bus with large windows',
                'total_seats' => 28,
                'seat_layout' => [
                    'rows' => 7,
                    'columns' => 4,
                    'aisle_position' => 2
                ],
                'base_fare_multiplier' => 1.3,
                'is_active' => true
            ]
        ];

        foreach ($busTypes as $busType) {
            BusType::create($busType);
        }
    }
}
