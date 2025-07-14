<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    /** @use HasFactory<\Database\Factories\CityFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'province',
        'district',
        'is_active',
        'latitude',
        'longitude'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6'
    ];

    /**
     * Get the routes where this city is the source.
     */
    public function sourceRoutes()
    {
        return $this->hasMany(Route::class, 'source_city_id');
    }

    /**
     * Get the routes where this city is the destination.
     */
    public function destinationRoutes()
    {
        return $this->hasMany(Route::class, 'destination_city_id');
    }

    /**
     * Get all routes (source and destination) for this city.
     */
    public function allRoutes()
    {
        return Route::where('source_city_id', $this->id)
                   ->orWhere('destination_city_id', $this->id);
    }

    /**
     * Scope to get only active cities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by province.
     */
    public function scopeByProvince($query, $province)
    {
        return $query->where('province', $province);
    }

    /**
     * Scope to get cities that have active routes.
     */
    public function scopeWithActiveRoutes($query)
    {
        return $query->where(function($query) {
            $query->whereHas('sourceRoutes', function($routeQuery) {
                $routeQuery->where('is_active', true);
            })->orWhereHas('destinationRoutes', function($routeQuery) {
                $routeQuery->where('is_active', true);
            });
        });
    }
}
