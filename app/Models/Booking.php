<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'user_id',
        'schedule_id',
        'seat_numbers',
        'passenger_count',
        'total_amount',
        'status',
        'passenger_details',
        'contact_phone',
        'contact_email',
        'booking_expires_at',
        'special_requests'
    ];

    protected $casts = [
        'seat_numbers' => 'array',
        'passenger_details' => 'array',
        'total_amount' => 'decimal:2',
        'booking_expires_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = 'BNG-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the schedule that owns the booking.
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the payments for this booking.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the successful payment for this booking.
     */
    public function successfulPayment()
    {
        return $this->hasOne(Payment::class)->where('status', 'completed');
    }

    /**
     * Check if booking is expired.
     */
    public function isExpired()
    {
        return $this->booking_expires_at && $this->booking_expires_at->isPast();
    }

    /**
     * Get seat numbers as comma-separated string.
     */
    public function getSeatNumbersStringAttribute()
    {
        return implode(', ', $this->seat_numbers);
    }
}
