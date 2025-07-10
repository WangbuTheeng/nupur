<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'source_city',
        'destination_city',
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
        return $this->source_city . ' → ' . $this->destination_city;
    }
}
