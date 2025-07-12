<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Bus;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get buses and routes
        $buses = Bus::all();
        $routes = Route::all();

        if ($buses->isEmpty() || $routes->isEmpty()) {
            $this->command->error('Please run BusSeeder and RouteSeeder first.');
            return;
        }

        // Create schedules for the next 30 days
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays(30);

        $schedules = [];

        // Popular routes with multiple daily schedules
        $popularRoutes = [
            'Kathmandu - Pokhara' => [
                ['departure' => '06:00', 'arrival' => '12:00'],
                ['departure' => '08:00', 'arrival' => '14:00'],
                ['departure' => '14:00', 'arrival' => '20:00'],
                ['departure' => '20:00', 'arrival' => '02:00'], // Next day arrival
            ],
            'Kathmandu - Chitwan' => [
                ['departure' => '07:00', 'arrival' => '11:30'],
                ['departure' => '13:00', 'arrival' => '17:30'],
                ['departure' => '18:00', 'arrival' => '22:30'],
            ],
            'Kathmandu - Butwal' => [
                ['departure' => '06:30', 'arrival' => '14:30'],
                ['departure' => '19:00', 'arrival' => '03:00'], // Next day arrival
            ],
            'Kathmandu - Dharan' => [
                ['departure' => '18:00', 'arrival' => '04:00'], // Next day arrival
                ['departure' => '20:00', 'arrival' => '06:00'], // Next day arrival
            ],
            'Kathmandu - Biratnagar' => [
                ['departure' => '17:00', 'arrival' => '05:00'], // Next day arrival
            ],
            'Kathmandu - Janakpur' => [
                ['departure' => '08:00', 'arrival' => '15:00'],
                ['departure' => '20:00', 'arrival' => '03:00'], // Next day arrival
            ],
            'Kathmandu - Birgunj' => [
                ['departure' => '09:00', 'arrival' => '13:00'],
                ['departure' => '15:00', 'arrival' => '19:00'],
                ['departure' => '21:00', 'arrival' => '01:00'], // Next day arrival
            ],
            'Pokhara - Chitwan' => [
                ['departure' => '08:00', 'arrival' => '11:30'],
                ['departure' => '15:00', 'arrival' => '18:30'],
            ],
            'Pokhara - Butwal' => [
                ['departure' => '09:00', 'arrival' => '12:30'],
                ['departure' => '16:00', 'arrival' => '19:30'],
            ],
            'Kathmandu - Dhangadhi' => [
                ['departure' => '16:00', 'arrival' => '07:00'], // Next day arrival
            ]
        ];

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $usedBuses = []; // Track buses used for this date

            foreach ($routes as $route) {
                $routeName = $route->name;

                if (isset($popularRoutes[$routeName])) {
                    $timeSlots = $popularRoutes[$routeName];

                    foreach ($timeSlots as $timeSlot) {
                        // Get available buses for this time slot
                        $availableBuses = $buses->filter(function($bus) use ($usedBuses, $currentDate, $timeSlot) {
                            $key = $bus->id . '-' . $currentDate->format('Y-m-d') . '-' . $timeSlot['departure'];
                            return !isset($usedBuses[$key]);
                        });

                        if ($availableBuses->isEmpty()) {
                            continue; // Skip if no buses available
                        }

                        $bus = $availableBuses->random();
                        $busKey = $bus->id . '-' . $currentDate->format('Y-m-d') . '-' . $timeSlot['departure'];
                        $usedBuses[$busKey] = true;

                        // Calculate fare based on route base fare and bus type multiplier
                        $baseFare = $route->base_fare;
                        $multiplier = $bus->busType->base_fare_multiplier;
                        $fare = $baseFare * $multiplier;

                        $schedules[] = [
                            'bus_id' => $bus->id,
                            'route_id' => $route->id,
                            'operator_id' => $bus->operator_id,
                            'travel_date' => $currentDate->format('Y-m-d'),
                            'departure_time' => $timeSlot['departure'],
                            'arrival_time' => $timeSlot['arrival'],
                            'fare' => $fare,
                            'available_seats' => $bus->total_seats,
                            'status' => 'scheduled',
                            'notes' => null,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }

            $currentDate->addDay();
        }

        // Insert schedules in batches for better performance
        $chunks = array_chunk($schedules, 100);
        foreach ($chunks as $chunk) {
            Schedule::insert($chunk);
        }

        $this->command->info('Created ' . count($schedules) . ' schedules for the next 30 days.');
    }
}
