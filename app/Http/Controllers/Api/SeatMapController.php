<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\SeatReservation;
use App\Events\SeatUpdated;
use App\Services\SeatReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SeatMapController extends Controller
{
    protected $reservationService;

    public function __construct(SeatReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }
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

        // Use the new reservation service with 1-hour expiration
        $result = $this->reservationService->reserveSeats($userId, $schedule->id, $seatNumbers, 60);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'reservation_expires_at' => $result['expires_at'],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }
    }

    /**
     * Release reserved seats.
     */
    public function releaseSeats(Request $request, Schedule $schedule)
    {
        $userId = Auth::id();

        // Use the new reservation service
        $result = $this->reservationService->releaseSeats($userId, $schedule->id);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }
    }

    /**
     * Get all reserved seats for a schedule.
     */
    private function getReservedSeats($scheduleId)
    {
        return $this->reservationService->getReservedSeats($scheduleId);
    }
}
