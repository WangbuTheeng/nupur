<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        // Get key statistics
        $stats = [
            // User Statistics
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'new_users_today' => User::whereDate('created_at', $today)->count(),
            'new_users_this_month' => User::whereDate('created_at', '>=', $thisMonth)->count(),
            
            // Operator Statistics
            'total_operators' => User::role('operator')->count(),
            'active_operators' => User::role('operator')->where('is_active', true)->count(),
            
            // Bus Statistics
            'total_buses' => Bus::count(),
            'active_buses' => Bus::where('is_active', true)->count(),
            
            // Route Statistics
            'total_routes' => Route::count(),
            'active_routes' => Route::where('is_active', true)->count(),
            
            // Booking Statistics
            'total_bookings' => Booking::count(),
            'today_bookings' => Booking::whereDate('created_at', $today)->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            
            // Revenue Statistics
            'total_revenue' => Booking::where('status', 'confirmed')->sum('total_amount') ?? 0,
            'today_revenue' => Booking::where('status', 'confirmed')
                ->whereDate('created_at', $today)->sum('total_amount') ?? 0,
            'this_month_revenue' => Booking::where('status', 'confirmed')
                ->whereDate('created_at', '>=', $thisMonth)->sum('total_amount') ?? 0,
            
            // Schedule Statistics
            'total_schedules' => Schedule::count(),
            'today_schedules' => Schedule::whereDate('travel_date', $today)->count(),
            'upcoming_schedules' => Schedule::where('travel_date', '>=', $today)
                ->where('status', 'scheduled')->count(),
        ];

        // Recent activities
        $recentBookings = Booking::with(['user', 'schedule.route', 'schedule.bus'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentUsers = User::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Top operators by revenue
        $topOperators = User::role('operator')
            ->withCount(['buses'])
            ->with(['buses' => function($query) {
                $query->withCount(['schedules']);
            }])
            ->get()
            ->map(function($operator) {
                $revenue = Booking::whereHas('schedule.bus', function($query) use ($operator) {
                    $query->where('operator_id', $operator->id);
                })->where('status', 'confirmed')->sum('total_amount') ?? 0;
                
                $operator->total_revenue = $revenue;
                $operator->total_schedules = $operator->buses->sum('schedules_count');
                return $operator;
            })
            ->sortByDesc('total_revenue')
            ->take(5);

        // Monthly revenue chart data (last 12 months)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Booking::where('status', 'confirmed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_amount') ?? 0;
            
            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue,
                'bookings' => Booking::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        }

        // Daily bookings for the last 7 days
        $dailyBookings = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $bookings = Booking::whereDate('created_at', $date)->count();
            $revenue = Booking::where('status', 'confirmed')
                ->whereDate('created_at', $date)
                ->sum('total_amount') ?? 0;
            
            $dailyBookings[] = [
                'date' => $date->format('M d'),
                'bookings' => $bookings,
                'revenue' => $revenue,
            ];
        }

        // Popular routes
        $popularRoutes = Route::withCount(['schedules as total_schedules'])
            ->with(['schedules' => function($query) {
                $query->withCount(['bookings']);
            }])
            ->get()
            ->map(function($route) {
                $route->total_bookings = $route->schedules->sum('bookings_count');
                return $route;
            })
            ->sortByDesc('total_bookings')
            ->take(5);

        // System alerts
        $alerts = [];
        
        // Check for pending bookings
        $pendingCount = Booking::where('status', 'pending')->count();
        if ($pendingCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$pendingCount} pending bookings require attention",
                'action' => route('admin.bookings.index', ['status' => 'pending']),
                'action_text' => 'View Pending Bookings'
            ];
        }

        // Check for inactive operators
        $inactiveOperators = User::role('operator')->where('is_active', false)->count();
        if ($inactiveOperators > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$inactiveOperators} operators are currently inactive",
                'action' => route('admin.operators.index', ['status' => 'inactive']),
                'action_text' => 'View Inactive Operators'
            ];
        }

        // Check for buses needing maintenance
        $maintenanceBuses = Bus::where('status', 'maintenance')->count();
        if ($maintenanceBuses > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$maintenanceBuses} buses are under maintenance",
                'action' => route('admin.buses.index', ['status' => 'maintenance']),
                'action_text' => 'View Maintenance Buses'
            ];
        }

        // Check for buses needing inspection
        $inspectionBuses = Bus::where('status', 'inspection')->count();
        if ($inspectionBuses > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$inspectionBuses} buses are due for inspection",
                'action' => route('admin.buses.index', ['status' => 'inspection']),
                'action_text' => 'View Inspection Buses'
            ];
        }

        return view('admin.dashboard.index', compact(
            'stats',
            'recentBookings',
            'recentUsers',
            'topOperators',
            'monthlyRevenue',
            'dailyBookings',
            'popularRoutes',
            'alerts'
        ));
    }

    /**
     * Get dashboard statistics for AJAX requests.
     */
    public function stats()
    {
        $today = Carbon::today();
        
        $stats = [
            'today_bookings' => Booking::whereDate('created_at', $today)->count(),
            'today_revenue' => Booking::where('status', 'confirmed')
                ->whereDate('created_at', $today)->sum('total_amount') ?? 0,
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'active_schedules' => Schedule::where('travel_date', '>=', $today)
                ->where('status', 'scheduled')->count(),
            'last_updated' => now()->format('H:i:s'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get chart data for dashboard.
     */
    public function chartData(Request $request)
    {
        $type = $request->get('type', 'revenue');
        $period = $request->get('period', '7days');

        switch ($type) {
            case 'revenue':
                return $this->getRevenueChartData($period);
            case 'bookings':
                return $this->getBookingsChartData($period);
            case 'users':
                return $this->getUsersChartData($period);
            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    private function getRevenueChartData($period)
    {
        $data = [];
        $days = $period === '30days' ? 30 : 7;

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = Booking::where('status', 'confirmed')
                ->whereDate('created_at', $date)
                ->sum('total_amount') ?? 0;
            
            $data[] = [
                'date' => $date->format('M d'),
                'value' => $revenue,
            ];
        }

        return response()->json(['data' => $data]);
    }

    private function getBookingsChartData($period)
    {
        $data = [];
        $days = $period === '30days' ? 30 : 7;

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $bookings = Booking::whereDate('created_at', $date)->count();
            
            $data[] = [
                'date' => $date->format('M d'),
                'value' => $bookings,
            ];
        }

        return response()->json(['data' => $data]);
    }

    private function getUsersChartData($period)
    {
        $data = [];
        $days = $period === '30days' ? 30 : 7;

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $users = User::whereDate('created_at', $date)->count();
            
            $data[] = [
                'date' => $date->format('M d'),
                'value' => $users,
            ];
        }

        return response()->json(['data' => $data]);
    }
}
