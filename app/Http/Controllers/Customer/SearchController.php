<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\BusType;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Display the search form.
     */
    public function index()
    {
        // Get only active cities that have routes (either as source or destination)
        // and deduplicate by name to avoid showing duplicate city names
        $cities = City::active()
            ->withActiveRoutes()
            ->orderBy('name')
            ->get()
            ->unique('name') // Remove duplicates by name
            ->values(); // Reset array keys

        $popularRoutes = Route::withCount(['schedules as booking_count' => function($query) {
                $query->join('bookings', 'schedules.id', '=', 'bookings.schedule_id')
                      ->where('bookings.status', 'confirmed');
            }])
            ->orderBy('booking_count', 'desc')
            ->limit(6)
            ->get();

        // Get user's recent searches if authenticated
        $recentSearches = [];
        if (Auth::check()) {
            $recentSearches = session()->get('recent_searches', []);
        }

        return view('customer.search.index', compact('cities', 'popularRoutes', 'recentSearches'));
    }

    /**
     * Process search request and redirect to results.
     */
    public function search(Request $request)
    {
        $request->validate([
            'source_city_id' => 'required|exists:cities,id',
            'destination_city_id' => 'required|exists:cities,id|different:source_city_id',
            'travel_date' => 'required|date|after_or_equal:today',
            'passengers' => 'nullable|integer|min:1|max:10',
        ]);

        // Store search in session for recent searches
        if (Auth::check()) {
            $this->storeRecentSearch($request);
        }

        // Redirect to results with search parameters
        return redirect()->route('search.show', $request->only([
            'source_city_id',
            'destination_city_id',
            'travel_date',
            'passengers'
        ]));
    }

    /**
     * Display search results.
     */
    public function results(Request $request)
    {
        $request->validate([
            'source_city_id' => 'required|exists:cities,id',
            'destination_city_id' => 'required|exists:cities,id',
            'travel_date' => 'required|date',
            'passengers' => 'nullable|integer|min:1|max:10',
        ]);

        $passengers = $request->get('passengers', 1);

        // Find routes matching the cities
        $routes = Route::where(function($query) use ($request) {
            $query->where('source_city_id', $request->source_city_id)
                  ->where('destination_city_id', $request->destination_city_id);
        })->pluck('id');

        if ($routes->isEmpty()) {
            return back()->withInput()
                ->with('error', 'No routes available for the selected cities.');
        }

        // Find available schedules (only show schedules that haven't finished)
        $query = Schedule::with([
                'route.sourceCity',
                'route.destinationCity',
                'bus.busType',
                'operator'
            ])
            ->whereIn('route_id', $routes)
            ->whereDate('travel_date', $request->travel_date)
            ->bookableOnline() // Only show schedules bookable online for customers
            ->where('available_seats', '>=', $passengers);

        // Apply filters
        if ($request->filled('bus_type')) {
            $query->whereHas('bus', function($busQuery) use ($request) {
                $busQuery->where('bus_type_id', $request->bus_type);
            });
        }

        if ($request->filled('operator')) {
            $query->where('operator_id', $request->operator);
        }

        if ($request->filled('departure_time')) {
            switch ($request->departure_time) {
                case 'morning':
                    $query->whereBetween('departure_time', ['06:00', '12:00']);
                    break;
                case 'afternoon':
                    $query->whereBetween('departure_time', ['12:00', '18:00']);
                    break;
                case 'evening':
                    $query->whereBetween('departure_time', ['18:00', '23:59']);
                    break;
                case 'night':
                    $query->where(function($timeQuery) {
                        $timeQuery->whereBetween('departure_time', ['00:00', '06:00'])
                                 ->orWhereBetween('departure_time', ['22:00', '23:59']);
                    });
                    break;
            }
        }

        if ($request->filled('price_range')) {
            switch ($request->price_range) {
                case 'budget':
                    $query->where('fare', '<=', 1000);
                    break;
                case 'standard':
                    $query->whereBetween('fare', [1000, 2000]);
                    break;
                case 'premium':
                    $query->where('fare', '>=', 2000);
                    break;
            }
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'departure_time');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('fare', 'asc');
                break;
            case 'price_high':
                $query->orderBy('fare', 'desc');
                break;
            case 'duration':
                $query->orderByRaw('TIME_TO_SEC(arrival_time) - TIME_TO_SEC(departure_time)');
                break;
            case 'rating':
                // TODO: Implement operator rating
                $query->orderBy('departure_time');
                break;
            default:
                $query->orderBy('departure_time');
        }

        $schedules = $query->paginate(10)->withQueryString();

        // Get filter options
        $busTypes = BusType::where('is_active', true)->get();
        $operators = User::role('operator')
            ->whereHas('schedules', function($scheduleQuery) use ($routes, $request) {
                $scheduleQuery->whereIn('route_id', $routes)
                             ->whereDate('travel_date', $request->travel_date);
            })
            ->get();

        // Get search parameters for display
        $searchParams = [
            'source_city' => City::find($request->source_city_id),
            'destination_city' => City::find($request->destination_city_id),
            'travel_date' => $request->travel_date,
            'passengers' => $passengers,
        ];

        return view('customer.search.results', compact(
            'schedules',
            'busTypes',
            'operators',
            'searchParams'
        ));
    }

    /**
     * Show schedule details.
     */
    public function scheduleDetails(Schedule $schedule)
    {
        $schedule->load([
            'route.sourceCity',
            'route.destinationCity',
            'bus.busType',
            'operator',
            'bookings' => function($query) {
                $query->where('status', '!=', 'cancelled');
            }
        ]);

        // Generate seat map
        $seatMap = $this->generateSeatMapWithBookings($schedule);

        // Get similar schedules
        $similarSchedules = Schedule::with(['route', 'bus.busType', 'operator'])
            ->where('route_id', $schedule->route_id)
            ->where('id', '!=', $schedule->id)
            ->where('travel_date', '>=', Carbon::today())
            ->where('status', 'scheduled')
            ->orderBy('travel_date')
            ->limit(5)
            ->get();

        return view('customer.search.schedule-details', compact(
            'schedule',
            'seatMap',
            'similarSchedules'
        ));
    }

    /**
     * Show route details and available schedules.
     */
    public function routeDetails(Route $route)
    {
        $route->load(['sourceCity', 'destinationCity']);

        // Get upcoming schedules for this route
        $schedules = Schedule::with(['bus.busType', 'operator'])
            ->where('route_id', $route->id)
            ->where('travel_date', '>=', Carbon::today())
            ->where('status', 'scheduled')
            ->orderBy('travel_date')
            ->orderBy('departure_time')
            ->paginate(10);

        // Get route statistics
        $stats = [
            'total_schedules' => $route->schedules()->count(),
            'active_operators' => $route->schedules()->distinct('operator_id')->count(),
            'average_fare' => $route->schedules()->avg('fare'),
            'min_fare' => $route->schedules()->min('fare'),
            'max_fare' => $route->schedules()->max('fare'),
        ];

        return view('customer.search.route-details', compact('route', 'schedules', 'stats'));
    }

    /**
     * Store recent search in session.
     */
    private function storeRecentSearch(Request $request)
    {
        $search = [
            'source_city_id' => $request->source_city_id,
            'destination_city_id' => $request->destination_city_id,
            'source_city_name' => City::find($request->source_city_id)->name,
            'destination_city_name' => City::find($request->destination_city_id)->name,
            'travel_date' => $request->travel_date,
            'passengers' => $request->get('passengers', 1),
            'searched_at' => now(),
        ];

        $recentSearches = session()->get('recent_searches', []);

        // Remove duplicate searches
        $recentSearches = array_filter($recentSearches, function($item) use ($search) {
            return !($item['source_city_id'] == $search['source_city_id'] &&
                    $item['destination_city_id'] == $search['destination_city_id']);
        });

        // Add new search to beginning
        array_unshift($recentSearches, $search);

        // Keep only last 5 searches
        $recentSearches = array_slice($recentSearches, 0, 5);

        session()->put('recent_searches', $recentSearches);
    }

    /**
     * Generate seat map with booking information.
     */
    private function generateSeatMapWithBookings(Schedule $schedule)
    {
        $seatLayout = $schedule->bus->seat_layout;
        $bookedSeats = $schedule->bookings()
            ->where('status', '!=', 'cancelled')
            ->pluck('seat_numbers')
            ->flatten()
            ->toArray();

        if (isset($seatLayout['seats']) && is_array($seatLayout['seats'])) {
            foreach ($seatLayout['seats'] as &$seat) {
                // Handle both 'number' and 'seat_number' keys for backward compatibility
                $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? null;
                $seat['is_booked'] = $seatNumber ? in_array($seatNumber, $bookedSeats) : false;
                $seat['is_available'] = !$seat['is_booked'];
            }
        }

        return $seatLayout;
    }
}
