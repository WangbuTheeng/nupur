<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_number',
        'operator_name',
        'bus_type_id',
        'license_plate',
        'manufacture_year',
        'total_seats',
        'amenities',
        'is_active'
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean'
    ];

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
     * Get the bus display name.
     */
    public function getDisplayNameAttribute()
    {
        return $this->operator_name . ' - ' . $this->bus_number;
    }
}
