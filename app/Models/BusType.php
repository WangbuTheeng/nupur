<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'total_seats',
        'seat_layout',
        'base_fare_multiplier',
        'is_active'
    ];

    protected $casts = [
        'seat_layout' => 'array',
        'base_fare_multiplier' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get the buses for this bus type.
     */
    public function buses()
    {
        return $this->hasMany(Bus::class);
    }
}
