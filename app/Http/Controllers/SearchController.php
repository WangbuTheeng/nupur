<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SearchController extends Controller
{
    /**
     * Show the search form.
     */
    public function index(Request $request)
    {
        $routes = Route::where('is_active', true)->get();
        $searchResults = null;
        
        // If search parameters are provided, perform search
        if ($request->filled(['source', 'destination', 'travel_date'])) {
            $searchResults = $this->searchBuses($request);
        }
        
        return view('search.index', compact('routes', 'searchResults'));
    }

    /**
     * Search for available buses.
     */
    public function search(Request $request)
    {
        $request->validate([
            'source' => 'required|string',
            'destination' => 'required|string',
            'travel_date' => 'required|date|after_or_equal:today',
        ]);

        $searchResults = $this->searchBuses($request);
        $routes = Route::where('is_active', true)->get();

        return view('search.index', compact('routes', 'searchResults'));
    }

    /**
     * Perform the actual bus search.
     */
    private function searchBuses(Request $request)
    {
        $source = $request->input('source');
        $destination = $request->input('destination');
        $travelDate = $request->input('travel_date');

        // Find routes that match source and destination
        $matchingRoutes = Route::where('is_active', true)
            ->where(function ($query) use ($source, $destination) {
                $query->where('source_city', 'LIKE', "%{$source}%")
                      ->where('destination_city', 'LIKE', "%{$destination}%");
            })
            ->pluck('id');

        if ($matchingRoutes->isEmpty()) {
            return collect();
        }

        // Find schedules for the matching routes on the specified date
        $schedules = Schedule::with(['bus.busType', 'route'])
            ->whereIn('route_id', $matchingRoutes)
            ->where('travel_date', $travelDate)
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0)
            ->orderBy('departure_time')
            ->get();

        return $schedules;
    }

    /**
     * Show schedule details for booking.
     */
    public function showSchedule(Schedule $schedule)
    {
        $schedule->load(['bus.busType', 'bus.seats', 'route']);
        
        // Check if schedule is bookable
        if (!$schedule->isBookable()) {
            return redirect()->route('search')->with('error', 'This schedule is not available for booking.');
        }

        // Get available seats
        $availableSeats = $schedule->bus->seats()
            ->where('is_available', true)
            ->get()
            ->filter(function ($seat) use ($schedule) {
                return $seat->isBookableForSchedule($schedule->id);
            });

        return view('search.schedule', compact('schedule', 'availableSeats'));
    }
}
