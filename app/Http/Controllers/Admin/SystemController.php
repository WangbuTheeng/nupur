<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusType;
use App\Models\City;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SystemController extends Controller
{
    /**
     * Show system settings.
     */
    public function settings()
    {
        $settings = [
            'festival_mode' => Cache::get('festival_mode', false),
            'festival_price_multiplier' => Cache::get('festival_price_multiplier', 1.5),
            'booking_cancellation_hours' => Cache::get('booking_cancellation_hours', 24),
            'advance_booking_days' => Cache::get('advance_booking_days', 30),
            'seat_hold_minutes' => Cache::get('seat_hold_minutes', 15),
            'system_maintenance' => Cache::get('system_maintenance', false),
        ];

        return view('admin.system.settings', compact('settings'));
    }

    /**
     * Update system settings.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'festival_price_multiplier' => 'required|numeric|min:1|max:5',
            'booking_cancellation_hours' => 'required|integer|min:1|max:168',
            'advance_booking_days' => 'required|integer|min:1|max:365',
            'seat_hold_minutes' => 'required|integer|min:5|max:60',
        ]);

        Cache::put('festival_price_multiplier', $request->festival_price_multiplier);
        Cache::put('booking_cancellation_hours', $request->booking_cancellation_hours);
        Cache::put('advance_booking_days', $request->advance_booking_days);
        Cache::put('seat_hold_minutes', $request->seat_hold_minutes);
        Cache::put('system_maintenance', $request->boolean('system_maintenance'));

        return back()->with('success', 'System settings updated successfully.');
    }

    /**
     * Manage bus types.
     */
    public function busTypes()
    {
        $busTypes = BusType::orderBy('name')->get();
        return view('admin.system.bus-types', compact('busTypes'));
    }

    /**
     * Store a new bus type.
     */
    public function storeBusType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:bus_types',
            'description' => 'nullable|string|max:500',
            'default_seats' => 'required|integer|min:10|max:100',
            'seat_layout' => 'required|array',
            'amenities' => 'nullable|array',
            'fare_multiplier' => 'required|numeric|min:0.5|max:5',
        ]);

        BusType::create([
            'name' => $request->name,
            'description' => $request->description,
            'default_seats' => $request->default_seats,
            'seat_layout' => $request->seat_layout,
            'amenities' => $request->amenities ?? [],
            'fare_multiplier' => $request->fare_multiplier,
        ]);

        return back()->with('success', 'Bus type created successfully.');
    }

    /**
     * Update a bus type.
     */
    public function updateBusType(Request $request, BusType $busType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:bus_types,name,' . $busType->id,
            'description' => 'nullable|string|max:500',
            'default_seats' => 'required|integer|min:10|max:100',
            'seat_layout' => 'required|array',
            'amenities' => 'nullable|array',
            'fare_multiplier' => 'required|numeric|min:0.5|max:5',
        ]);

        $busType->update([
            'name' => $request->name,
            'description' => $request->description,
            'default_seats' => $request->default_seats,
            'seat_layout' => $request->seat_layout,
            'amenities' => $request->amenities ?? [],
            'fare_multiplier' => $request->fare_multiplier,
        ]);

        return back()->with('success', 'Bus type updated successfully.');
    }

    /**
     * Delete a bus type.
     */
    public function destroyBusType(BusType $busType)
    {
        if ($busType->buses()->count() > 0) {
            return back()->with('error', 'Cannot delete bus type that has buses assigned to it.');
        }

        $busType->delete();
        return back()->with('success', 'Bus type deleted successfully.');
    }

    /**
     * Manage cities.
     */
    public function cities()
    {
        $cities = City::orderBy('name')->get();
        return view('admin.system.cities', compact('cities'));
    }

    /**
     * Store a new city.
     */
    public function storeCity(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities',
            'province' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        City::create([
            'name' => $request->name,
            'province' => $request->province,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return back()->with('success', 'City added successfully.');
    }

    /**
     * Update a city.
     */
    public function updateCity(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name,' . $city->id,
            'province' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $city->update([
            'name' => $request->name,
            'province' => $request->province,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return back()->with('success', 'City updated successfully.');
    }

    /**
     * Delete a city.
     */
    public function destroyCity(City $city)
    {
        // Check if city is used in routes
        if ($city->sourceRoutes()->count() > 0 || $city->destinationRoutes()->count() > 0) {
            return back()->with('error', 'Cannot delete city that is used in routes.');
        }

        $city->delete();
        return back()->with('success', 'City deleted successfully.');
    }

    /**
     * Festival mode management.
     */
    public function festivalMode()
    {
        $festivalMode = Cache::get('festival_mode', false);
        $priceMultiplier = Cache::get('festival_price_multiplier', 1.5);

        // Get festival schedules if any
        $festivalSchedules = Schedule::where('is_festival_schedule', true)
            ->with(['route', 'bus', 'operator'])
            ->orderBy('travel_date')
            ->get();

        return view('admin.system.festival', compact('festivalMode', 'priceMultiplier', 'festivalSchedules'));
    }

    /**
     * Toggle festival mode.
     */
    public function toggleFestivalMode(Request $request)
    {
        $festivalMode = $request->boolean('festival_mode');
        Cache::put('festival_mode', $festivalMode);

        $message = $festivalMode ? 'Festival mode enabled.' : 'Festival mode disabled.';
        return back()->with('success', $message);
    }

    /**
     * Get festival schedules.
     */
    public function festivalSchedules()
    {
        $schedules = Schedule::where('is_festival_schedule', true)
            ->with(['route', 'bus', 'operator'])
            ->orderBy('travel_date')
            ->paginate(20);

        return view('admin.system.festival-schedules', compact('schedules'));
    }

    /**
     * Create festival schedule.
     */
    public function createFestivalSchedule(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'bus_id' => 'required|exists:buses,id',
            'travel_date' => 'required|date|after:today',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i|after:departure_time',
            'fare' => 'required|numeric|min:1',
            'special_notes' => 'nullable|string|max:500',
        ]);

        Schedule::create([
            'route_id' => $request->route_id,
            'bus_id' => $request->bus_id,
            'operator_id' => auth()->id(),
            'travel_date' => $request->travel_date,
            'departure_time' => $request->departure_time,
            'arrival_time' => $request->arrival_time,
            'fare' => $request->fare,
            'available_seats' => $request->bus->total_seats ?? 40,
            'status' => 'scheduled',
            'is_festival_schedule' => true,
            'special_notes' => $request->special_notes,
        ]);

        return back()->with('success', 'Festival schedule created successfully.');
    }
}
