<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'route_id',
        'travel_date',
        'departure_time',
        'arrival_time',
        'fare',
        'available_seats',
        'status',
        'notes'
    ];

    protected $casts = [
        'travel_date' => 'date',
        'departure_time' => 'datetime:H:i:s',
        'arrival_time' => 'datetime:H:i:s',
        'fare' => 'decimal:2'
    ];

    /**
     * Get the bus that owns the schedule.
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    /**
     * Get the route that owns the schedule.
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * Get the bookings for this schedule.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Check if the schedule is bookable.
     */
    public function isBookable()
    {
        return $this->status === 'scheduled' && 
               $this->available_seats > 0 && 
               $this->travel_date >= Carbon::today();
    }

    /**
     * Get the departure datetime.
     */
    public function getDepartureDatetimeAttribute()
    {
        return Carbon::parse($this->travel_date->format('Y-m-d') . ' ' . $this->departure_time);
    }
}
