<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\User;
use App\Models\BusType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the test operator
        $operator = User::where('email', 'operator@ktmexpress.com')->first();

        if (!$operator) {
            $this->command->error('Test operator not found. Please run AdminUserSeeder first.');
            return;
        }

        // Get bus types
        $acDeluxe = BusType::where('name', 'AC Deluxe')->first();
        $vipSleeper = BusType::where('name', 'VIP Sleeper')->first();
        $nonAcRegular = BusType::where('name', 'Non-AC Regular')->first();
        $touristBus = BusType::where('name', 'Tourist Bus')->first();

        $buses = [
            [
                'bus_number' => 'KTM-001',
                'operator_id' => $operator->id,
                'bus_type_id' => $acDeluxe->id,
                'license_plate' => 'BA 1 KHA 1234',
                'model' => 'Tata Ultra',
                'color' => 'Blue',
                'manufacture_year' => 2022,
                'total_seats' => 32,
                'seat_layout' => [
                    'rows' => 8,
                    'columns' => 4,
                    'aisle_position' => 2,
                    'seats' => $this->generateSeatLayout(8, 4, 2)
                ],
                'amenities' => ['WiFi', 'AC', 'Charging Port', 'Reading Light'],
                'description' => 'Comfortable AC deluxe bus with modern amenities',
                'is_active' => true
            ],
            [
                'bus_number' => 'KTM-002',
                'operator_id' => $operator->id,
                'bus_type_id' => $vipSleeper->id,
                'license_plate' => 'BA 1 KHA 5678',
                'model' => 'Ashok Leyland',
                'color' => 'White',
                'manufacture_year' => 2023,
                'total_seats' => 24,
                'seat_layout' => [
                    'rows' => 6,
                    'columns' => 4,
                    'aisle_position' => 2,
                    'seats' => $this->generateSeatLayout(6, 4, 2)
                ],
                'amenities' => ['WiFi', 'AC', 'Charging Port', 'Reading Light', 'Entertainment System', 'Blanket', 'Sleeper Beds'],
                'description' => 'Premium VIP sleeper bus with luxury features',
                'is_active' => true
            ],
            [
                'bus_number' => 'KTM-003',
                'operator_id' => $operator->id,
                'bus_type_id' => $nonAcRegular->id,
                'license_plate' => 'BA 1 KHA 9012',
                'model' => 'Mahindra Tourister',
                'color' => 'Red',
                'manufacture_year' => 2021,
                'total_seats' => 40,
                'seat_layout' => [
                    'rows' => 10,
                    'columns' => 4,
                    'aisle_position' => 2,
                    'seats' => $this->generateSeatLayout(10, 4, 2)
                ],
                'amenities' => ['Charging Port'],
                'description' => 'Standard non-AC bus for budget travel',
                'is_active' => true
            ],
            [
                'bus_number' => 'KTM-004',
                'operator_id' => $operator->id,
                'bus_type_id' => $touristBus->id,
                'license_plate' => 'BA 1 KHA 3456',
                'model' => 'Volvo B11R',
                'color' => 'Green',
                'manufacture_year' => 2023,
                'total_seats' => 28,
                'seat_layout' => [
                    'rows' => 7,
                    'columns' => 4,
                    'aisle_position' => 2,
                    'seats' => $this->generateSeatLayout(7, 4, 2)
                ],
                'amenities' => ['WiFi', 'AC', 'Charging Port', 'Reading Light', 'Large Windows', 'Panoramic View'],
                'description' => 'Tourist-friendly bus with large windows and scenic views',
                'is_active' => true
            ]
        ];

        foreach ($buses as $bus) {
            Bus::create($bus);
        }
    }

    /**
     * Generate seat layout for a bus.
     */
    private function generateSeatLayout($rows, $columns, $aislePosition)
    {
        $seats = [];
        $seatNumber = 1;

        for ($row = 1; $row <= $rows; $row++) {
            for ($col = 1; $col <= $columns; $col++) {
                // Skip aisle position
                if ($col == $aislePosition) {
                    continue;
                }

                $seats[] = [
                    'seat_number' => $seatNumber,
                    'row' => $row,
                    'column' => $col,
                    'is_available' => true,
                    'is_window' => $col == 1 || $col == $columns,
                    'is_aisle' => $col == $aislePosition - 1 || $col == $aislePosition + 1
                ];
                $seatNumber++;
            }
        }

        return $seats;
    }
}
