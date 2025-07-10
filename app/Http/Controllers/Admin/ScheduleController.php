<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Bus;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules.
     */
    public function index(Request $request)
    {
        $query = Schedule::with(['bus', 'route']);

        // Filter by date if provided
        if ($request->filled('date')) {
            $query->whereDate('travel_date', $request->date);
        } else {
            // Default to today's schedules
            $query->whereDate('travel_date', '>=', Carbon::today());
        }

        // Filter by route if provided
        if ($request->filled('route_id')) {
            $query->where('route_id', $request->route_id);
        }

        $schedules = $query->orderBy('travel_date')
            ->orderBy('departure_time')
            ->paginate(15);

        $routes = Route::where('is_active', true)->get();

        return view('admin.schedules.index', compact('schedules', 'routes'));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        $buses = Bus::where('is_active', true)->with('busType')->get();
        $routes = Route::where('is_active', true)->get();
        
        return view('admin.schedules.create', compact('buses', 'routes'));
    }

    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required|exists:routes,id',
            'travel_date' => 'required|date|after_or_equal:today',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i|after:departure_time',
            'fare' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if bus is already scheduled for this date and time
        $existingSchedule = Schedule::where('bus_id', $request->bus_id)
            ->where('travel_date', $request->travel_date)
            ->where('departure_time', $request->departure_time . ':00')
            ->exists();

        if ($existingSchedule) {
            return back()->withErrors(['departure_time' => 'This bus is already scheduled for this date and time.'])->withInput();
        }

        $bus = Bus::find($request->bus_id);

        Schedule::create([
            'bus_id' => $request->bus_id,
            'route_id' => $request->route_id,
            'travel_date' => $request->travel_date,
            'departure_time' => $request->departure_time . ':00',
            'arrival_time' => $request->arrival_time . ':00',
            'fare' => $request->fare,
            'available_seats' => $bus->total_seats,
            'status' => 'scheduled',
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule created successfully!');
    }

    /**
     * Display the specified schedule.
     */
    public function show(Schedule $schedule)
    {
        $schedule->load(['bus.busType', 'route', 'bookings.user']);
        return view('admin.schedules.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit(Schedule $schedule)
    {
        $buses = Bus::where('is_active', true)->with('busType')->get();
        $routes = Route::where('is_active', true)->get();
        
        return view('admin.schedules.edit', compact('schedule', 'buses', 'routes'));
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $validator = Validator::make($request->all(), [
            'bus_id' => 'required|exists:buses,id',
            'route_id' => 'required|exists:routes,id',
            'travel_date' => 'required|date',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i|after:departure_time',
            'fare' => 'required|numeric|min:1',
            'status' => 'required|in:scheduled,boarding,departed,arrived,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if bus is already scheduled for this date and time (excluding current schedule)
        $existingSchedule = Schedule::where('bus_id', $request->bus_id)
            ->where('travel_date', $request->travel_date)
            ->where('departure_time', $request->departure_time . ':00')
            ->where('id', '!=', $schedule->id)
            ->exists();

        if ($existingSchedule) {
            return back()->withErrors(['departure_time' => 'This bus is already scheduled for this date and time.'])->withInput();
        }

        $schedule->update([
            'bus_id' => $request->bus_id,
            'route_id' => $request->route_id,
            'travel_date' => $request->travel_date,
            'departure_time' => $request->departure_time . ':00',
            'arrival_time' => $request->arrival_time . ':00',
            'fare' => $request->fare,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule updated successfully!');
    }

    /**
     * Remove the specified schedule from storage.
     */
    public function destroy(Schedule $schedule)
    {
        // Check if there are any bookings for this schedule
        if ($schedule->bookings()->whereIn('status', ['confirmed', 'pending'])->exists()) {
            return back()->with('error', 'Cannot delete schedule with existing bookings.');
        }

        $schedule->delete();
        return redirect()->route('admin.schedules.index')
            ->with('success', 'Schedule deleted successfully!');
    }
}
