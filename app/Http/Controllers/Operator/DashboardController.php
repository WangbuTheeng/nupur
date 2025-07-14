<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the operator dashboard.
     */
    public function index()
    {
        $operator = Auth::user();
        $today = Carbon::today();
        
        // Get today's statistics
        $todayBookings = Booking::whereHas('schedule', function($query) use ($operator, $today) {
            $query->where('operator_id', $operator->id)
                  ->whereDate('travel_date', $today);
        })->count();

        $todayRevenue = Booking::whereHas('schedule', function($query) use ($operator, $today) {
            $query->where('operator_id', $operator->id)
                  ->whereDate('travel_date', $today);
        })->where('status', 'confirmed')->sum('total_amount');

        $pendingBookings = Booking::whereHas('schedule', function($query) use ($operator) {
            $query->where('operator_id', $operator->id);
        })->where('status', 'pending')->count();

        $activeSchedules = Schedule::where('operator_id', $operator->id)
            ->whereDate('travel_date', $today)
            ->where('status', 'scheduled')
            ->count();

        // Get monthly statistics
        $monthlyBookings = Booking::whereHas('schedule', function($query) use ($operator) {
            $query->where('operator_id', $operator->id);
        })->whereMonth('created_at', $today->month)
          ->whereYear('created_at', $today->year)
          ->count();

        $monthlyRevenue = Booking::whereHas('schedule', function($query) use ($operator) {
            $query->where('operator_id', $operator->id);
        })->whereMonth('created_at', $today->month)
          ->whereYear('created_at', $today->year)
          ->where('status', 'confirmed')
          ->sum('total_amount');

        // Get recent bookings (top 5)
        $recentBookings = Booking::whereHas('schedule', function($query) use ($operator) {
            $query->where('operator_id', $operator->id);
        })->with(['user', 'schedule.route', 'schedule.bus'])
          ->orderBy('created_at', 'desc')
          ->limit(5)
          ->get();

        // Get today's schedules (top 5)
        $todaySchedules = Schedule::where('operator_id', $operator->id)
            ->whereDate('travel_date', $today)
            ->with(['route', 'bus', 'bookings'])
            ->orderBy('departure_time')
            ->limit(5)
            ->get();

        // Get fleet statistics
        $totalBuses = Bus::where('operator_id', $operator->id)->count();
        $activeBuses = Bus::where('operator_id', $operator->id)
            ->where('status', 'active')
            ->count();

        // Get route statistics
        $totalRoutes = Route::whereHas('schedules', function($query) use ($operator) {
            $query->where('operator_id', $operator->id);
        })->distinct()->count();
        $activeRoutes = Route::whereHas('schedules', function($query) use ($operator) {
            $query->where('operator_id', $operator->id);
        })->where('is_active', true)->distinct()->count();

        return view('dashboard.operator', compact(
            'todayBookings',
            'todayRevenue',
            'pendingBookings',
            'activeSchedules',
            'monthlyBookings',
            'monthlyRevenue',
            'recentBookings',
            'todaySchedules',
            'totalBuses',
            'activeBuses',
            'totalRoutes',
            'activeRoutes'
        ));
    }

    /**
     * Get dashboard statistics for AJAX requests.
     */
    public function stats()
    {
        $operator = Auth::user();
        $today = Carbon::today();
        
        $stats = [
            'today_bookings' => Booking::whereHas('schedule', function($query) use ($operator, $today) {
                $query->where('operator_id', $operator->id)
                      ->whereDate('travel_date', $today);
            })->count(),
            
            'today_revenue' => Booking::whereHas('schedule', function($query) use ($operator, $today) {
                $query->where('operator_id', $operator->id)
                      ->whereDate('travel_date', $today);
            })->where('status', 'confirmed')->sum('total_amount'),
            
            'pending_bookings' => Booking::whereHas('schedule', function($query) use ($operator) {
                $query->where('operator_id', $operator->id);
            })->where('status', 'pending')->count(),
            
            'active_schedules' => Schedule::where('operator_id', $operator->id)
                ->whereDate('travel_date', $today)
                ->where('status', 'scheduled')
                ->count(),
                
            'monthly_revenue' => Booking::whereHas('schedule', function($query) use ($operator) {
                $query->where('operator_id', $operator->id);
            })->whereMonth('created_at', $today->month)
              ->whereYear('created_at', $today->year)
              ->where('status', 'confirmed')
              ->sum('total_amount'),
              
            'total_buses' => Bus::where('operator_id', $operator->id)->count(),
            'active_buses' => Bus::where('operator_id', $operator->id)
                ->where('status', 'active')
                ->count(),
                
            'last_updated' => now()->format('H:i:s'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get booking analytics data.
     */
    public function bookingAnalytics()
    {
        $operator = Auth::user();
        $today = Carbon::today();
        
        // Get hourly bookings for today
        $hourlyBookings = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $startTime = $today->copy()->addHours($hour);
            $endTime = $startTime->copy()->addHour();
            
            $bookings = Booking::whereHas('schedule', function($query) use ($operator) {
                $query->where('operator_id', $operator->id);
            })->whereBetween('created_at', [$startTime, $endTime])->count();
            
            $revenue = Booking::whereHas('schedule', function($query) use ($operator) {
                $query->where('operator_id', $operator->id);
            })->whereBetween('created_at', [$startTime, $endTime])
              ->where('status', 'confirmed')
              ->sum('total_amount');
            
            $hourlyBookings[] = [
                'hour' => $hour,
                'bookings' => $bookings,
                'revenue' => $revenue,
            ];
        }

        // Get daily bookings for the last 30 days
        $dailyBookings = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            
            $bookings = Booking::whereHas('schedule', function($query) use ($operator) {
                $query->where('operator_id', $operator->id);
            })->whereDate('created_at', $date)->count();
            
            $revenue = Booking::whereHas('schedule', function($query) use ($operator) {
                $query->where('operator_id', $operator->id);
            })->whereDate('created_at', $date)
              ->where('status', 'confirmed')
              ->sum('total_amount');
            
            $dailyBookings[] = [
                'date' => $date->format('Y-m-d'),
                'bookings' => $bookings,
                'revenue' => $revenue,
            ];
        }

        return response()->json([
            'success' => true,
            'hourly_bookings' => $hourlyBookings,
            'daily_bookings' => $dailyBookings,
            'last_updated' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Get route performance data.
     */
    public function routePerformance()
    {
        $operator = Auth::user();
        
        $routeStats = Route::whereHas('schedules', function($query) use ($operator) {
                $query->where('operator_id', $operator->id);
            })
            ->withCount(['schedules as total_schedules' => function($query) use ($operator) {
                $query->where('operator_id', $operator->id);
            }])
            ->withCount(['schedules as completed_schedules' => function($query) use ($operator) {
                $query->where('operator_id', $operator->id)
                      ->where('status', 'completed');
            }])
            ->with(['schedules' => function($query) use ($operator) {
                $query->where('operator_id', $operator->id)
                      ->with('bookings');
            }])
            ->get()
            ->map(function($route) {
                $totalBookings = $route->schedules->sum(function($schedule) {
                    return $schedule->bookings->count();
                });
                
                $totalRevenue = $route->schedules->sum(function($schedule) {
                    return $schedule->bookings->where('status', 'confirmed')->sum('total_amount');
                });
                
                $averageOccupancy = $route->schedules->avg(function($schedule) {
                    $totalSeats = $schedule->bus->total_seats ?? 0;
                    $bookedSeats = $schedule->bookings->where('status', 'confirmed')->sum('passenger_count');
                    return $totalSeats > 0 ? ($bookedSeats / $totalSeats) * 100 : 0;
                });
                
                return [
                    'route' => $route,
                    'total_bookings' => $totalBookings,
                    'total_revenue' => $totalRevenue,
                    'average_occupancy' => round($averageOccupancy, 2),
                    'total_schedules' => $route->total_schedules,
                    'completed_schedules' => $route->completed_schedules,
                ];
            })
            ->sortByDesc('total_revenue');

        return response()->json([
            'success' => true,
            'route_performance' => $routeStats->values(),
        ]);
    }

    /**
     * Get fleet utilization data.
     */
    public function fleetUtilization()
    {
        $operator = Auth::user();
        $today = Carbon::today();
        
        $busStats = Bus::where('operator_id', $operator->id)
            ->with(['schedules' => function($query) use ($today) {
                $query->whereDate('travel_date', $today);
            }])
            ->get()
            ->map(function($bus) use ($today) {
                $todaySchedules = $bus->schedules->where('travel_date', $today->toDateString())->count();
                $totalBookings = $bus->schedules->sum(function($schedule) {
                    return $schedule->bookings->where('status', 'confirmed')->count();
                });
                
                $totalRevenue = $bus->schedules->sum(function($schedule) {
                    return $schedule->bookings->where('status', 'confirmed')->sum('total_amount');
                });
                
                return [
                    'bus' => $bus,
                    'today_schedules' => $todaySchedules,
                    'total_bookings' => $totalBookings,
                    'total_revenue' => $totalRevenue,
                    'utilization_rate' => $todaySchedules > 0 ? 100 : 0, // Simplified calculation
                ];
            })
            ->sortByDesc('total_revenue');

        return response()->json([
            'success' => true,
            'fleet_utilization' => $busStats->values(),
        ]);
    }

    /**
     * Show the operator profile page.
     */
    public function profile()
    {
        $operator = Auth::user();

        // Initialize settings if not set
        if (empty($operator->settings)) {
            $operator->update([
                'settings' => [
                    'notification_email' => true,
                    'notification_sms' => false,
                    'auto_confirm_bookings' => false,
                    'booking_cutoff_minutes' => 10,
                    'default_cancellation_policy' => 'Cancellation allowed up to 24 hours before departure with 10% cancellation fee.',
                ]
            ]);
            $operator->refresh();
        }

        return view('operator.profile.index', compact('operator'));
    }

    /**
     * Update the operator profile.
     */
    public function updateProfile(Request $request)
    {
        $operator = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $operator->id,
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:20',
            'company_email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'current_password' => 'nullable|required_with:password|current_password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Update basic profile information
        $operator->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'company_phone' => $request->company_phone,
            'company_email' => $request->company_email,
            'license_number' => $request->license_number,
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $operator->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Show the operator settings page.
     */
    public function settings()
    {
        $operator = Auth::user();

        // Initialize settings if not set
        if (empty($operator->settings)) {
            $operator->update([
                'settings' => [
                    'notification_email' => true,
                    'notification_sms' => false,
                    'auto_confirm_bookings' => false,
                    'booking_cutoff_minutes' => 10,
                    'default_cancellation_policy' => 'Cancellation allowed up to 24 hours before departure with 10% cancellation fee.',
                ]
            ]);
            $operator->refresh();
        }

        return view('operator.settings.index', compact('operator'));
    }

    /**
     * Update the operator settings.
     */
    public function updateSettings(Request $request)
    {
        $operator = Auth::user();

        $request->validate([
            'notification_email' => 'boolean',
            'notification_sms' => 'boolean',
            'auto_confirm_bookings' => 'boolean',
            'booking_cutoff_minutes' => 'integer|min:5|max:120',
            'default_cancellation_policy' => 'string|max:500',
        ]);

        // Update operator settings (you might want to store these in a separate settings table)
        $settings = [
            'notification_email' => $request->boolean('notification_email'),
            'notification_sms' => $request->boolean('notification_sms'),
            'auto_confirm_bookings' => $request->boolean('auto_confirm_bookings'),
            'booking_cutoff_minutes' => $request->booking_cutoff_minutes ?? 10,
            'default_cancellation_policy' => $request->default_cancellation_policy,
        ];

        // Store settings in user's meta or create a settings table
        $operator->update(['settings' => $settings]);

        return back()->with('success', 'Settings updated successfully!');
    }
}
