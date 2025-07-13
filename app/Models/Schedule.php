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
     * Check if the schedule is bookable (general check).
     */
    public function isBookable()
    {
        return $this->status === 'scheduled' &&
               $this->available_seats > 0 &&
               !$this->hasFinished();
    }

    /**
     * Check if the schedule is bookable online (customer booking).
     * Online booking closes 10 minutes before departure.
     */
    public function isBookableOnline()
    {
        if (!$this->isBookable()) {
            return false;
        }

        $now = Carbon::now();
        $departureDateTime = $this->departure_datetime;
        $bookingCutoff = $departureDateTime->subMinutes(10);

        return $now <= $bookingCutoff;
    }

    /**
     * Check if the schedule is bookable via counter.
     * Counter booking is allowed until departure time.
     */
    public function isBookableViaCounter()
    {
        if (!$this->isBookable()) {
            return false;
        }

        $now = Carbon::now();
        $departureDateTime = $this->departure_datetime;

        return $now <= $departureDateTime;
    }

    /**
     * Check if the schedule has finished (departed).
     */
    public function hasFinished()
    {
        $now = Carbon::now();
        $departureDateTime = $this->departure_datetime;

        return $now > $departureDateTime;
    }

    /**
     * Check if online booking is closed but counter booking is still available.
     */
    public function isInCounterOnlyPeriod()
    {
        return !$this->isBookableOnline() && $this->isBookableViaCounter();
    }

    /**
     * Get minutes until departure.
     */
    public function getMinutesUntilDepartureAttribute()
    {
        $now = Carbon::now();
        $departureDateTime = $this->departure_datetime;

        if ($now > $departureDateTime) {
            return 0; // Already departed
        }

        return $now->diffInMinutes($departureDateTime);
    }

    /**
     * Get booking status for display.
     */
    public function getBookingStatusAttribute()
    {
        if ($this->hasFinished()) {
            return 'finished';
        }

        if ($this->status !== 'scheduled') {
            return 'not_available';
        }

        if ($this->available_seats <= 0) {
            return 'sold_out';
        }

        if ($this->isBookableOnline()) {
            return 'available_online';
        }

        if ($this->isBookableViaCounter()) {
            return 'counter_only';
        }

        return 'not_available';
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
        if ($this->departure_time && $this->arrival_time && $this->arrival_time < $this->departure_time) {
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
     * Scope to get bookable schedules (general).
     */
    public function scopeBookable($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('available_seats', '>', 0)
                    ->whereRaw("CONCAT(travel_date, ' ', departure_time) > NOW()");
    }

    /**
     * Scope to get schedules bookable online (excludes those within 10 minutes of departure).
     */
    public function scopeBookableOnline($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('available_seats', '>', 0)
                    ->whereRaw("CONCAT(travel_date, ' ', departure_time) > DATE_ADD(NOW(), INTERVAL 10 MINUTE)");
    }

    /**
     * Scope to get schedules bookable via counter (until departure time).
     */
    public function scopeBookableViaCounter($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('available_seats', '>', 0)
                    ->whereRaw("CONCAT(travel_date, ' ', departure_time) > NOW()");
    }

    /**
     * Scope to get schedules that are not finished.
     */
    public function scopeNotFinished($query)
    {
        return $query->whereRaw("CONCAT(travel_date, ' ', departure_time) > NOW()");
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

    /**
     * Auto-update schedule status based on time.
     * This can be called periodically or on access.
     */
    public function updateStatusBasedOnTime()
    {
        if ($this->status === 'scheduled' && $this->hasFinished()) {
            $this->update(['status' => 'departed']);
            return true;
        }
        return false;
    }

    /**
     * Scope to auto-update statuses for finished schedules.
     */
    public function scopeWithUpdatedStatuses($query)
    {
        // Update all scheduled schedules that have finished
        $query->where('status', 'scheduled')
              ->whereRaw("CONCAT(travel_date, ' ', departure_time) <= NOW()")
              ->update(['status' => 'departed']);

        return $query;
    }
}
