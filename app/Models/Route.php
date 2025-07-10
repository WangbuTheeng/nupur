<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'source_city_id',
        'destination_city_id',
        'distance_km',
        'base_fare',
        'estimated_duration',
        'stops',
        'is_active'
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'base_fare' => 'decimal:2',
        'estimated_duration' => 'datetime:H:i:s',
        'stops' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the source city for this route.
     */
    public function sourceCity()
    {
        return $this->belongsTo(City::class, 'source_city_id');
    }

    /**
     * Get the destination city for this route.
     */
    public function destinationCity()
    {
        return $this->belongsTo(City::class, 'destination_city_id');
    }

    /**
     * Get the schedules for this route.
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Get the full route name.
     */
    public function getFullNameAttribute()
    {
        return $this->sourceCity->name . ' → ' . $this->destinationCity->name;
    }

    /**
     * Scope to get only active routes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter routes by source city.
     */
    public function scopeFromCity($query, $cityId)
    {
        return $query->where('source_city_id', $cityId);
    }

    /**
     * Scope to filter routes by destination city.
     */
    public function scopeToCity($query, $cityId)
    {
        return $query->where('destination_city_id', $cityId);
    }

    /**
     * Scope to search routes between two cities.
     */
    public function scopeBetweenCities($query, $sourceCityId, $destinationCityId)
    {
        return $query->where('source_city_id', $sourceCityId)
                    ->where('destination_city_id', $destinationCityId);
    }
}
