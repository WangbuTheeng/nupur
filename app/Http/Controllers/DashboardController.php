<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show the user dashboard.
     */
    public function userDashboard()
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
            ->limit(6)
            ->get();

        return view('dashboard.user', compact('recentBookings', 'upcomingTrips', 'popularRoutes'));
    }

    /**
     * Show the admin dashboard.
     */
    public function adminDashboard()
    {
        // Get today's statistics
        $todayBookings = Booking::whereDate('created_at', Carbon::today())->count();
        $todayRevenue = Booking::whereDate('created_at', Carbon::today())
            ->where('status', 'confirmed')
            ->sum('total_amount');

        // Get this month's statistics
        $monthlyBookings = Booking::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        $monthlyRevenue = Booking::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('status', 'confirmed')
            ->sum('total_amount');

        // Get recent bookings
        $recentBookings = Booking::with(['user', 'schedule.route', 'schedule.bus'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get today's schedules
        $todaySchedules = Schedule::with(['route', 'bus'])
            ->where('travel_date', Carbon::today())
            ->orderBy('departure_time')
            ->get();

        return view('dashboard.admin', compact(
            'todayBookings', 
            'todayRevenue', 
            'monthlyBookings', 
            'monthlyRevenue',
            'recentBookings',
            'todaySchedules'
        ));
    }
}
