<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Events\SeatUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SeatMapController extends Controller
{
    /**
     * Get real-time seat map for a schedule.
     */
    public function getSeatMap(Schedule $schedule)
    {
        $schedule->load(['bus', 'bookings']);

        $seatLayout = $schedule->bus->seat_layout;
        $bookedSeats = $schedule->bookings()
            ->where('status', '!=', 'cancelled')
            ->pluck('seat_numbers')
            ->flatten()
            ->toArray();

        // Get temporarily reserved seats
        $reservedSeats = $this->getReservedSeats($schedule->id);

        if (isset($seatLayout['seats']) && is_array($seatLayout['seats'])) {
            foreach ($seatLayout['seats'] as &$seat) {
                // Handle both 'number' and 'seat_number' keys for backward compatibility
                $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? null;
                $seat['is_booked'] = $seatNumber ? in_array($seatNumber, $bookedSeats) : false;
                $seat['is_reserved'] = $seatNumber ? in_array($seatNumber, $reservedSeats) : false;
                $seat['is_available'] = !$seat['is_booked'] && !$seat['is_reserved'];

                // Ensure seat number is in 'number' field for frontend compatibility
                if (!isset($seat['number']) && isset($seat['seat_number'])) {
                    $seat['number'] = $seat['seat_number'];
                }
            }
        }

        return response()->json([
            'success' => true,
            'seat_map' => $seatLayout,
            'available_seats' => $schedule->available_seats,
            'total_seats' => $schedule->bus->total_seats,
            'fare_per_seat' => $schedule->fare,
        ]);
    }

    /**
     * Reserve seats temporarily.
     */
    public function reserveSeats(Request $request, Schedule $schedule)
    {
        $request->validate([
            'seat_numbers' => 'required|array|min:1',
            'seat_numbers.*' => 'required|string',
        ]);

        $seatNumbers = $request->seat_numbers;
        $userId = Auth::id();

        // Check if seats are available
        $bookedSeats = $schedule->bookings()
            ->where('status', '!=', 'cancelled')
            ->pluck('seat_numbers')
            ->flatten()
            ->toArray();

        $reservedSeats = $this->getReservedSeats($schedule->id);

        foreach ($seatNumbers as $seatNumber) {
            if (in_array($seatNumber, $bookedSeats) || in_array($seatNumber, $reservedSeats)) {
                return response()->json([
                    'success' => false,
                    'message' => "Seat {$seatNumber} is not available.",
                ], 400);
            }
        }

        // Reserve seats for 10 minutes
        $reservationKey = 'seat_reservation_' . $schedule->id . '_' . $userId;
        $reservationData = [
            'seat_numbers' => $seatNumbers,
            'user_id' => $userId,
            'schedule_id' => $schedule->id,
            'expires_at' => now()->addMinutes(10),
        ];

        Cache::put($reservationKey, $reservationData, 600); // 10 minutes

        // Fire seat update events
        foreach ($seatNumbers as $seatNumber) {
            event(new SeatUpdated($schedule, $seatNumber, 'reserved', $userId));
        }

        return response()->json([
            'success' => true,
            'message' => 'Seats reserved successfully.',
            'reservation_expires_at' => $reservationData['expires_at'],
        ]);
    }

    /**
     * Release reserved seats.
     */
    public function releaseSeats(Request $request, Schedule $schedule)
    {
        $userId = Auth::id();
        $reservationKey = 'seat_reservation_' . $schedule->id . '_' . $userId;

        $reservation = Cache::get($reservationKey);

        if ($reservation) {
            // Fire seat update events
            foreach ($reservation['seat_numbers'] as $seatNumber) {
                event(new SeatUpdated($schedule, $seatNumber, 'available', $userId));
            }

            Cache::forget($reservationKey);

            return response()->json([
                'success' => true,
                'message' => 'Seats released successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No reservation found.',
        ], 404);
    }

    /**
     * Get all reserved seats for a schedule.
     */
    private function getReservedSeats($scheduleId)
    {
        $reservedSeats = [];

        // In a real application, you might want to use Redis for better pattern matching
        // For now, we'll use a simple approach
        $cacheKeys = Cache::getStore()->getRedis()->keys('seat_reservation_' . $scheduleId . '_*');

        foreach ($cacheKeys as $key) {
            $reservation = Cache::get($key);
            if ($reservation && isset($reservation['seat_numbers'])) {
                $reservedSeats = array_merge($reservedSeats, $reservation['seat_numbers']);
            }
        }

        return array_unique($reservedSeats);
    }
}
