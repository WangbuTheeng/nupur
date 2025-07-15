<?php

namespace App\Services;

use App\Models\SeatReservation;
use App\Models\Schedule;
use App\Models\Booking;
use App\Events\SeatUpdated;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SeatReservationService
{
    /**
     * Reserve seats for a user.
     */
    public function reserveSeats($userId, $scheduleId, $seatNumbers, $duration = 60)
    {
        try {
            $schedule = Schedule::findOrFail($scheduleId);
            $expiresAt = now()->addMinutes($duration);

            // Check if seats are available
            $unavailableSeats = $this->getUnavailableSeats($scheduleId, $seatNumbers, $userId);
            if (!empty($unavailableSeats)) {
                return [
                    'success' => false,
                    'message' => 'Some seats are no longer available: ' . implode(', ', $unavailableSeats)
                ];
            }

            // Create or update reservation
            $reservation = SeatReservation::createOrUpdate($userId, $scheduleId, $seatNumbers, $expiresAt);

            // Update cache for real-time updates
            $this->updateSeatCache($scheduleId);

            // Fire seat update events
            foreach ($seatNumbers as $seatNumber) {
                event(new SeatUpdated($schedule, $seatNumber, 'reserved', $userId));
            }

            Log::info('Seats reserved successfully', [
                'user_id' => $userId,
                'schedule_id' => $scheduleId,
                'seat_numbers' => $seatNumbers,
                'expires_at' => $expiresAt
            ]);

            return [
                'success' => true,
                'message' => 'Seats reserved successfully.',
                'reservation' => $reservation,
                'expires_at' => $expiresAt
            ];

        } catch (\Exception $e) {
            Log::error('Seat reservation failed', [
                'user_id' => $userId,
                'schedule_id' => $scheduleId,
                'seat_numbers' => $seatNumbers,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to reserve seats. Please try again.'
            ];
        }
    }

    /**
     * Release seats for a user.
     */
    public function releaseSeats($userId, $scheduleId)
    {
        try {
            $reservation = SeatReservation::where('user_id', $userId)
                                        ->where('schedule_id', $scheduleId)
                                        ->first();

            if (!$reservation) {
                return [
                    'success' => false,
                    'message' => 'No reservation found.'
                ];
            }

            $seatNumbers = $reservation->seat_numbers;
            $schedule = Schedule::findOrFail($scheduleId);

            // Delete reservation
            $reservation->delete();

            // Update cache
            $this->updateSeatCache($scheduleId);

            // Fire seat update events
            foreach ($seatNumbers as $seatNumber) {
                event(new SeatUpdated($schedule, $seatNumber, 'available', $userId));
            }

            Log::info('Seats released successfully', [
                'user_id' => $userId,
                'schedule_id' => $scheduleId,
                'seat_numbers' => $seatNumbers
            ]);

            return [
                'success' => true,
                'message' => 'Seats released successfully.'
            ];

        } catch (\Exception $e) {
            Log::error('Seat release failed', [
                'user_id' => $userId,
                'schedule_id' => $scheduleId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to release seats.'
            ];
        }
    }

    /**
     * Get all reserved seats for a schedule.
     */
    public function getReservedSeats($scheduleId)
    {
        return SeatReservation::getReservedSeatsForSchedule($scheduleId);
    }

    /**
     * Get unavailable seats (booked or reserved by others).
     */
    public function getUnavailableSeats($scheduleId, $requestedSeats, $excludeUserId = null)
    {
        $unavailable = [];

        // Check booked seats
        $bookedSeats = Booking::where('schedule_id', $scheduleId)
                             ->whereIn('status', ['confirmed', 'pending'])
                             ->get()
                             ->pluck('seat_numbers')
                             ->flatten()
                             ->unique()
                             ->toArray();

        // Check reserved seats (excluding current user)
        $reservedSeats = SeatReservation::where('schedule_id', $scheduleId)
                                      ->active();
        
        if ($excludeUserId) {
            $reservedSeats->where('user_id', '!=', $excludeUserId);
        }

        $reservedSeats = $reservedSeats->get()
                                     ->pluck('seat_numbers')
                                     ->flatten()
                                     ->unique()
                                     ->toArray();

        $allUnavailable = array_merge($bookedSeats, $reservedSeats);

        foreach ($requestedSeats as $seat) {
            if (in_array($seat, $allUnavailable)) {
                $unavailable[] = $seat;
            }
        }

        return $unavailable;
    }

    /**
     * Clean up expired reservations.
     */
    public function cleanupExpiredReservations()
    {
        $expiredReservations = SeatReservation::expired()->get();
        $cleanedCount = 0;

        foreach ($expiredReservations as $reservation) {
            $schedule = $reservation->schedule;
            $seatNumbers = $reservation->seat_numbers;

            // Mark as expired
            $reservation->markAsExpired();

            // Update cache
            $this->updateSeatCache($reservation->schedule_id);

            // Fire seat update events
            foreach ($seatNumbers as $seatNumber) {
                event(new SeatUpdated($schedule, $seatNumber, 'available', null));
            }

            $cleanedCount++;
        }

        if ($cleanedCount > 0) {
            Log::info('Cleaned up expired seat reservations', [
                'count' => $cleanedCount
            ]);
        }

        return $cleanedCount;
    }

    /**
     * Send expiry notifications for reservations about to expire.
     */
    public function sendExpiryNotifications()
    {
        $reservations = SeatReservation::needsExpiryNotification()->get();
        $notificationService = app(NotificationService::class);
        $sentCount = 0;

        foreach ($reservations as $reservation) {
            try {
                $notificationService->sendSeatReservationExpiry($reservation);
                $reservation->markAsNotified();
                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Failed to send reservation expiry notification', [
                    'reservation_id' => $reservation->id,
                    'user_id' => $reservation->user_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        if ($sentCount > 0) {
            Log::info('Sent seat reservation expiry notifications', [
                'count' => $sentCount
            ]);
        }

        return $sentCount;
    }

    /**
     * Convert reservation to booking.
     */
    public function convertToBooking($userId, $scheduleId)
    {
        $reservation = SeatReservation::where('user_id', $userId)
                                    ->where('schedule_id', $scheduleId)
                                    ->active()
                                    ->first();

        if ($reservation) {
            $reservation->markAsConverted();
            return $reservation;
        }

        return null;
    }

    /**
     * Update seat cache for real-time updates.
     */
    private function updateSeatCache($scheduleId)
    {
        $cacheKey = "schedule_seats_{$scheduleId}";
        Cache::forget($cacheKey);
        
        // Optionally rebuild cache immediately
        $this->buildSeatCache($scheduleId);
    }

    /**
     * Build seat cache for a schedule.
     */
    private function buildSeatCache($scheduleId)
    {
        $cacheKey = "schedule_seats_{$scheduleId}";
        
        $seatData = [
            'booked' => Booking::where('schedule_id', $scheduleId)
                              ->whereIn('status', ['confirmed', 'pending'])
                              ->get()
                              ->pluck('seat_numbers')
                              ->flatten()
                              ->unique()
                              ->values()
                              ->toArray(),
            'reserved' => $this->getReservedSeats($scheduleId),
            'updated_at' => now()
        ];

        Cache::put($cacheKey, $seatData, 300); // Cache for 5 minutes
        
        return $seatData;
    }
}
