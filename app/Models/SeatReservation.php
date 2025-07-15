<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SeatReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'seat_numbers',
        'status',
        'expires_at',
        'notified_at'
    ];

    protected $casts = [
        'seat_numbers' => 'array',
        'expires_at' => 'datetime',
        'notified_at' => 'datetime'
    ];

    /**
     * Get the user that owns the reservation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the schedule for this reservation.
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Check if the reservation has expired.
     */
    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the reservation is still active.
     */
    public function isActive()
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Mark reservation as expired.
     */
    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);
    }

    /**
     * Mark reservation as converted to booking.
     */
    public function markAsConverted()
    {
        $this->update(['status' => 'converted_to_booking']);
    }

    /**
     * Mark that expiry notification has been sent.
     */
    public function markAsNotified()
    {
        $this->update(['notified_at' => now()]);
    }

    /**
     * Get seat numbers as comma-separated string.
     */
    public function getSeatNumbersStringAttribute()
    {
        return implode(', ', $this->seat_numbers);
    }

    /**
     * Scope to get active reservations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope to get expired reservations.
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere('expires_at', '<=', now());
        });
    }

    /**
     * Scope to get reservations that need expiry notification.
     */
    public function scopeNeedsExpiryNotification($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '<=', now()->addMinutes(10)) // 10 minutes before expiry
                    ->whereNull('notified_at');
    }

    /**
     * Get all reserved seat numbers for a schedule.
     */
    public static function getReservedSeatsForSchedule($scheduleId)
    {
        return static::where('schedule_id', $scheduleId)
                    ->active()
                    ->get()
                    ->pluck('seat_numbers')
                    ->flatten()
                    ->unique()
                    ->values()
                    ->toArray();
    }

    /**
     * Create or update reservation for user and schedule.
     */
    public static function createOrUpdate($userId, $scheduleId, $seatNumbers, $expiresAt = null)
    {
        $expiresAt = $expiresAt ?: now()->addHour(); // Default 1 hour

        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'schedule_id' => $scheduleId
            ],
            [
                'seat_numbers' => $seatNumbers,
                'status' => 'active',
                'expires_at' => $expiresAt,
                'notified_at' => null // Reset notification status
            ]
        );
    }

    /**
     * Release reservation for user and schedule.
     */
    public static function releaseForUser($userId, $scheduleId)
    {
        return static::where('user_id', $userId)
                    ->where('schedule_id', $scheduleId)
                    ->delete();
    }
}
