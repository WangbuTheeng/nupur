<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Route;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the customer dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's recent bookings
        $recentBookings = $user->bookings()
            ->with(['schedule.route', 'schedule.bus'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get upcoming trips
        $upcomingTrips = $user->bookings()
            ->with(['schedule.route', 'schedule.bus'])
            ->whereHas('schedule', function($query) {
                $query->where('travel_date', '>=', Carbon::today());
            })
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get popular routes for quick booking
        $popularRoutes = Route::where('is_active', true)
            ->withCount(['schedules' => function($query) {
                $query->where('travel_date', '>=', Carbon::today());
            }])
            ->orderBy('schedules_count', 'desc')
            ->limit(6)
            ->get();

        // Get user statistics
        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'confirmed_bookings' => $user->bookings()->where('status', 'confirmed')->count(),
            'pending_bookings' => $user->bookings()->where('status', 'pending')->count(),
            'cancelled_bookings' => $user->bookings()->where('status', 'cancelled')->count(),
            'total_spent' => $user->bookings()->where('status', 'confirmed')->sum('total_amount'),
            'upcoming_trips' => $upcomingTrips->count(),
        ];

        // Get recent notifications
        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.customer', compact(
            'recentBookings', 
            'upcomingTrips', 
            'popularRoutes', 
            'stats',
            'notifications'
        ));
    }

    /**
     * Get dashboard statistics for AJAX requests.
     */
    public function stats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'confirmed_bookings' => $user->bookings()->where('status', 'confirmed')->count(),
            'pending_bookings' => $user->bookings()->where('status', 'pending')->count(),
            'cancelled_bookings' => $user->bookings()->where('status', 'cancelled')->count(),
            'total_spent' => $user->bookings()->where('status', 'confirmed')->sum('total_amount'),
            'upcoming_trips' => $user->bookings()
                ->whereHas('schedule', function($query) {
                    $query->where('travel_date', '>=', Carbon::today());
                })
                ->where('status', 'confirmed')
                ->count(),
            'this_month_bookings' => $user->bookings()
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'this_month_spent' => $user->bookings()
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('status', 'confirmed')
                ->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'last_updated' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Get quick booking suggestions.
     */
    public function quickBookingSuggestions()
    {
        $user = Auth::user();
        
        // Get user's frequently booked routes
        $frequentRoutes = $user->bookings()
            ->with('schedule.route')
            ->get()
            ->groupBy('schedule.route.id')
            ->map(function($bookings) {
                return [
                    'route' => $bookings->first()->schedule->route,
                    'count' => $bookings->count(),
                    'last_booked' => $bookings->max('created_at'),
                ];
            })
            ->sortByDesc('count')
            ->take(3);

        // Get upcoming schedules for frequent routes
        $suggestions = [];
        foreach ($frequentRoutes as $routeData) {
            $upcomingSchedules = Schedule::where('route_id', $routeData['route']->id)
                ->where('travel_date', '>=', Carbon::today())
                ->where('travel_date', '<=', Carbon::today()->addDays(7))
                ->where('status', 'scheduled')
                ->where('available_seats', '>', 0)
                ->with(['route', 'bus'])
                ->orderBy('travel_date')
                ->orderBy('departure_time')
                ->limit(2)
                ->get();

            if ($upcomingSchedules->count() > 0) {
                $suggestions[] = [
                    'route' => $routeData['route'],
                    'schedules' => $upcomingSchedules,
                    'booking_count' => $routeData['count'],
                ];
            }
        }

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions,
        ]);
    }

    /**
     * Get user's travel history summary.
     */
    public function travelHistory()
    {
        $user = Auth::user();
        
        // Get monthly travel data for the last 12 months
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $bookings = $user->bookings()
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->where('status', 'confirmed');
            
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'bookings' => $bookings->count(),
                'amount' => $bookings->sum('total_amount'),
            ];
        }

        // Get most traveled routes
        $topRoutes = $user->bookings()
            ->with('schedule.route')
            ->where('status', 'confirmed')
            ->get()
            ->groupBy('schedule.route.id')
            ->map(function($bookings) {
                return [
                    'route' => $bookings->first()->schedule->route,
                    'count' => $bookings->count(),
                    'total_spent' => $bookings->sum('total_amount'),
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values();

        return response()->json([
            'success' => true,
            'monthly_data' => $monthlyData,
            'top_routes' => $topRoutes,
        ]);
    }

    /**
     * Get user's saved preferences and favorites.
     */
    public function preferences()
    {
        $user = Auth::user();
        
        // Get favorite routes (if you have a favorites system)
        // This would require a favorites table/relationship
        
        // Get preferred travel times based on booking history
        $preferredTimes = $user->bookings()
            ->with('schedule')
            ->where('status', 'confirmed')
            ->get()
            ->groupBy(function($booking) {
                return Carbon::parse($booking->schedule->departure_time)->format('H');
            })
            ->map(function($bookings, $hour) {
                return [
                    'hour' => $hour,
                    'count' => $bookings->count(),
                    'label' => $hour . ':00 - ' . ($hour + 1) . ':00',
                ];
            })
            ->sortByDesc('count')
            ->take(3)
            ->values();

        // Get preferred bus types
        $preferredBusTypes = $user->bookings()
            ->with('schedule.bus.busType')
            ->where('status', 'confirmed')
            ->get()
            ->groupBy('schedule.bus.bus_type_id')
            ->map(function($bookings) {
                return [
                    'bus_type' => $bookings->first()->schedule->bus->busType,
                    'count' => $bookings->count(),
                ];
            })
            ->sortByDesc('count')
            ->take(3)
            ->values();

        return response()->json([
            'success' => true,
            'preferred_times' => $preferredTimes,
            'preferred_bus_types' => $preferredBusTypes,
        ]);
    }
}
