<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'seat_number',
        'row_number',
        'column_number',
        'seat_type',
        'is_window',
        'is_aisle',
        'is_available'
    ];

    protected $casts = [
        'is_window' => 'boolean',
        'is_aisle' => 'boolean',
        'is_available' => 'boolean'
    ];

    /**
     * Get the bus that owns the seat.
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    /**
     * Check if seat is bookable for a specific schedule.
     */
    public function isBookableForSchedule($scheduleId)
    {
        if (!$this->is_available) {
            return false;
        }

        // Check if seat is already booked for this schedule
        $booking = Booking::where('schedule_id', $scheduleId)
            ->whereJsonContains('seat_numbers', $this->seat_number)
            ->whereIn('status', ['confirmed', 'pending'])
            ->exists();

        return !$booking;
    }

    /**
     * Get seat display information.
     */
    public function getDisplayInfoAttribute()
    {
        $info = $this->seat_number;
        if ($this->is_window) $info .= ' (Window)';
        if ($this->seat_type !== 'regular') $info .= ' (' . ucfirst($this->seat_type) . ')';
        return $info;
    }
}
