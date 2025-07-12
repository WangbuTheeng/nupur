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
        'operator_id',
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
     * Get the operator that owns the schedule.
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
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

    /**
     * Get the arrival datetime.
     */
    public function getArrivalDatetimeAttribute()
    {
        $arrivalDate = $this->travel_date;

        // If arrival time is earlier than departure time, it's next day
        if ($this->arrival_time < $this->departure_time) {
            $arrivalDate = $arrivalDate->addDay();
        }

        return Carbon::parse($arrivalDate->format('Y-m-d') . ' ' . $this->arrival_time);
    }

    /**
     * Get the journey duration in hours.
     */
    public function getJourneyDurationAttribute()
    {
        return $this->departure_datetime->diffInHours($this->arrival_datetime);
    }

    /**
     * Get booked seats count.
     */
    public function getBookedSeatsCountAttribute()
    {
        return $this->bookings()
                   ->where('status', '!=', 'cancelled')
                   ->sum('passenger_count');
    }

    /**
     * Update available seats based on bookings.
     */
    public function updateAvailableSeats()
    {
        $bookedSeats = $this->booked_seats_count;
        $this->available_seats = $this->bus->total_seats - $bookedSeats;
        $this->save();
    }

    /**
     * Scope to get schedules for a specific route.
     */
    public function scopeForRoute($query, $routeId)
    {
        return $query->where('route_id', $routeId);
    }

    /**
     * Scope to get schedules for a specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where('travel_date', $date);
    }

    /**
     * Scope to get bookable schedules.
     */
    public function scopeBookable($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('available_seats', '>', 0)
                    ->where('travel_date', '>=', Carbon::today());
    }

    /**
     * Scope to search schedules between cities.
     */
    public function scopeBetweenCities($query, $sourceCityId, $destinationCityId)
    {
        return $query->whereHas('route', function($q) use ($sourceCityId, $destinationCityId) {
            $q->where('source_city_id', $sourceCityId)
              ->where('destination_city_id', $destinationCityId);
        });
    }

    /**
     * Get the calculated fare based on bus type multiplier.
     */
    public function getCalculatedFareAttribute()
    {
        $baseFare = $this->route->base_fare;
        $multiplier = $this->bus->busType->base_fare_multiplier;
        return $baseFare * $multiplier;
    }
}
