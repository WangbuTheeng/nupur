<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_number',
        'operator_id',
        'bus_type_id',
        'license_plate',
        'model',
        'color',
        'manufacture_year',
        'total_seats',
        'seat_layout',
        'amenities',
        'description',
        'is_active',
        'status'
    ];

    protected $casts = [
        'seat_layout' => 'array',
        'amenities' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Scope to get buses by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get buses needing maintenance.
     */
    public function scopeNeedsMaintenance($query)
    {
        return $query->whereIn('status', ['maintenance', 'inspection']);
    }

    /**
     * Get the operator that owns the bus.
     */
    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * Get the bus type that owns the bus.
     */
    public function busType()
    {
        return $this->belongsTo(BusType::class);
    }

    /**
     * Get the seats for this bus.
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Get the schedules for this bus.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Get the bookings for this bus through schedules.
     */
    public function bookings()
    {
        return $this->hasManyThrough(Booking::class, Schedule::class);
    }

    /**
     * Get the bus display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->operator->company_name . ' - ' . $this->bus_number;
    }

    /**
     * Get the full bus name with type.
     */
    public function getFullNameAttribute()
    {
        return $this->operator->company_name . ' - ' . $this->bus_number . ' (' . $this->busType->name . ')';
    }

    /**
     * Scope to get only active buses.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope to filter buses by operator.
     */
    public function scopeByOperator($query, $operatorId)
    {
        return $query->where('operator_id', $operatorId);
    }

    /**
     * Scope to filter buses by bus type.
     */
    public function scopeByType($query, $busTypeId)
    {
        return $query->where('bus_type_id', $busTypeId);
    }

    /**
     * Get available seats count for a specific schedule.
     */
    public function getAvailableSeatsForSchedule($scheduleId)
    {
        $bookedSeatsCount = Booking::where('schedule_id', $scheduleId)
            ->where('status', '!=', 'cancelled')
            ->sum('passenger_count');

        return $this->total_seats - $bookedSeatsCount;
    }

    /**
     * Generate default seat layout based on bus type.
     */
    public function generateDefaultSeatLayout()
    {
        $busType = $this->busType;
        if (!$busType || !$busType->seat_layout) {
            return null;
        }

        $layout = $busType->seat_layout;
        $seats = [];
        $seatNumber = 1;

        for ($row = 1; $row <= $layout['rows']; $row++) {
            for ($col = 1; $col <= $layout['columns']; $col++) {
                // Skip aisle position
                if ($col == $layout['aisle_position']) {
                    continue;
                }

                $seats[] = [
                    'seat_number' => $seatNumber,
                    'row' => $row,
                    'column' => $col,
                    'is_available' => true,
                    'is_window' => $col == 1 || $col == $layout['columns'],
                    'is_aisle' => $col == $layout['aisle_position'] - 1 || $col == $layout['aisle_position'] + 1
                ];
                $seatNumber++;
            }
        }

        return $seats;
    }

    /**
     * Generate dynamic seat layout using SeatLayoutService.
     */
    public function generateDynamicSeatLayout($layoutType = '2x2', $hasBackRow = true)
    {
        $seatLayoutService = new \App\Services\SeatLayoutService();
        return $seatLayoutService->generateSeatLayout($this->total_seats, $layoutType, $hasBackRow);
    }

    /**
     * Update seat layout with new configuration.
     */
    public function updateSeatLayout($layoutType = '2x2', $hasBackRow = true)
    {
        $newLayout = $this->generateDynamicSeatLayout($layoutType, $hasBackRow);
        $this->seat_layout = $newLayout;
        $this->save();

        return $newLayout;
    }

    /**
     * Get seat layout with real-time booking status.
     */
    public function getSeatLayoutWithBookings($scheduleId = null)
    {
        $layout = $this->seat_layout;

        if (!$layout || !isset($layout['seats'])) {
            return $layout;
        }

        // Get booked seats for specific schedule if provided
        if ($scheduleId) {
            $bookedSeats = \App\Models\Schedule::find($scheduleId)
                ?->bookings()
                ->where('status', '!=', 'cancelled')
                ->pluck('seat_numbers')
                ->flatten()
                ->toArray() ?? [];

            // Update seat availability
            foreach ($layout['seats'] as &$seat) {
                $seatNumber = $seat['number'] ?? null;
                $seat['is_booked'] = $seatNumber ? in_array($seatNumber, $bookedSeats) : false;
                $seat['is_available'] = !$seat['is_booked'];
            }
        }

        return $layout;
    }

    /**
     * Validate seat layout configuration.
     */
    public function validateSeatLayout($layoutType, $hasBackRow = true)
    {
        $seatLayoutService = new \App\Services\SeatLayoutService();
        return $seatLayoutService->validateLayout($this->total_seats, $layoutType, $hasBackRow);
    }
}
