<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Route;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Display comprehensive analytics dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $this->getStartDate($period);
        $endDate = Carbon::now();

        // Key Performance Indicators
        $kpis = $this->getKPIs($startDate, $endDate);

        // Revenue Analytics
        $revenueData = $this->getRevenueAnalytics($period, $startDate, $endDate);

        // Booking Analytics
        $bookingData = $this->getBookingAnalytics($period, $startDate, $endDate);

        // Operator Performance
        $operatorPerformance = $this->getOperatorPerformance($startDate, $endDate);

        // Route Performance
        $routePerformance = $this->getRoutePerformance($startDate, $endDate);

        // Customer Analytics
        $customerData = $this->getCustomerAnalytics($startDate, $endDate);

        // Festival Mode Impact (if enabled)
        $festivalData = $this->getFestivalAnalytics($startDate, $endDate);

        return view('admin.analytics.index', compact(
            'kpis',
            'revenueData',
            'bookingData',
            'operatorPerformance',
            'routePerformance',
            'customerData',
            'festivalData',
            'period'
        ));
    }

    /**
     * Get real-time dashboard data via AJAX.
     */
    public function realTimeData()
    {
        $data = [
            'today_bookings' => Booking::whereDate('created_at', Carbon::today())->count(),
            'today_revenue' => Booking::whereDate('created_at', Carbon::today())
                ->where('status', 'confirmed')
                ->sum('total_amount'),
            'active_schedules' => Schedule::whereDate('travel_date', Carbon::today())
                ->where('status', 'scheduled')
                ->count(),
            'online_users' => $this->getOnlineUsersCount(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'last_updated' => now()->format('H:i:s'),
        ];

        return response()->json($data);
    }

    /**
     * Get Key Performance Indicators.
     */
    private function getKPIs($startDate, $endDate)
    {
        $currentPeriod = [
            'total_bookings' => Booking::whereBetween('created_at', [$startDate, $endDate])->count(),
            'total_revenue' => Booking::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'confirmed')
                ->sum('total_amount'),
            'average_booking_value' => Booking::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'confirmed')
                ->avg('total_amount'),
            'conversion_rate' => $this->getConversionRate($startDate, $endDate),
        ];

        // Previous period for comparison
        $periodLength = $endDate->diffInDays($startDate);
        $previousStart = $startDate->copy()->subDays($periodLength);
        $previousEnd = $startDate->copy();

        $previousPeriod = [
            'total_bookings' => Booking::whereBetween('created_at', [$previousStart, $previousEnd])->count(),
            'total_revenue' => Booking::whereBetween('created_at', [$previousStart, $previousEnd])
                ->where('status', 'confirmed')
                ->sum('total_amount'),
            'average_booking_value' => Booking::whereBetween('created_at', [$previousStart, $previousEnd])
                ->where('status', 'confirmed')
                ->avg('total_amount'),
            'conversion_rate' => $this->getConversionRate($previousStart, $previousEnd),
        ];

        // Calculate growth percentages
        $growth = [];
        foreach ($currentPeriod as $key => $value) {
            $previousValue = $previousPeriod[$key] ?? 0;
            $growth[$key] = $previousValue > 0 ? (($value - $previousValue) / $previousValue) * 100 : 0;
        }

        return [
            'current' => $currentPeriod,
            'previous' => $previousPeriod,
            'growth' => $growth,
        ];
    }

    /**
     * Get revenue analytics data.
     */
    private function getRevenueAnalytics($period, $startDate, $endDate)
    {
        $query = Booking::where('status', 'confirmed')
            ->whereBetween('created_at', [$startDate, $endDate]);

        switch ($period) {
            case 'week':
                $groupBy = 'DATE(created_at)';
                $dateFormat = '%Y-%m-%d';
                break;
            case 'month':
                $groupBy = 'DATE(created_at)';
                $dateFormat = '%Y-%m-%d';
                break;
            case 'year':
                $groupBy = 'YEAR(created_at), MONTH(created_at)';
                $dateFormat = '%Y-%m';
                break;
            default:
                $groupBy = 'DATE(created_at)';
                $dateFormat = '%Y-%m-%d';
        }

        $revenueByPeriod = $query->select(
                DB::raw("DATE_FORMAT(created_at, '{$dateFormat}') as period"),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('COUNT(*) as bookings')
            )
            ->groupBy(DB::raw($groupBy))
            ->orderBy('period')
            ->get();

        // Revenue by payment method
        $revenueByPayment = Booking::where('status', 'confirmed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('payment_method')
            ->get();

        return [
            'by_period' => $revenueByPeriod,
            'by_payment_method' => $revenueByPayment,
        ];
    }

    /**
     * Get booking analytics data.
     */
    private function getBookingAnalytics($period, $startDate, $endDate)
    {
        // Booking status distribution
        $statusDistribution = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Booking source (online vs counter)
        $bookingSource = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select('booking_type', DB::raw('COUNT(*) as count'))
            ->groupBy('booking_type')
            ->get();

        // Peak booking hours
        $peakHours = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get();

        return [
            'status_distribution' => $statusDistribution,
            'booking_source' => $bookingSource,
            'peak_hours' => $peakHours,
        ];
    }

    /**
     * Get operator performance data.
     */
    private function getOperatorPerformance($startDate, $endDate)
    {
        return User::role('operator')
            ->with(['operatorBookings' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'confirmed');
            }])
            ->get()
            ->map(function($operator) {
                $bookings = $operator->operatorBookings;
                return [
                    'id' => $operator->id,
                    'name' => $operator->company_name ?? $operator->name,
                    'total_bookings' => $bookings->count(),
                    'total_revenue' => $bookings->sum('total_amount'),
                    'average_booking_value' => $bookings->avg('total_amount') ?? 0,
                    'buses_count' => $operator->buses()->count(),
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10);
    }

    /**
     * Get route performance data.
     */
    private function getRoutePerformance($startDate, $endDate)
    {
        return Route::withCount(['schedules as total_bookings' => function($query) use ($startDate, $endDate) {
                $query->join('bookings', 'schedules.id', '=', 'bookings.schedule_id')
                      ->whereBetween('bookings.created_at', [$startDate, $endDate])
                      ->where('bookings.status', 'confirmed');
            }])
            ->with(['sourceCity', 'destinationCity'])
            ->orderBy('total_bookings', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Get customer analytics data.
     */
    private function getCustomerAnalytics($startDate, $endDate)
    {
        // New vs returning customers
        $newCustomers = User::role('customer')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $returningCustomers = User::role('customer')
            ->whereHas('bookings', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->where('created_at', '<', $startDate)
            ->count();

        // Customer lifetime value
        $customerLTV = User::role('customer')
            ->withSum(['bookings as total_spent' => function($query) {
                $query->where('status', 'confirmed');
            }], 'total_amount')
            ->get()
            ->avg('total_spent');

        return [
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'customer_ltv' => $customerLTV ?? 0,
        ];
    }

    /**
     * Get festival mode analytics.
     */
    private function getFestivalAnalytics($startDate, $endDate)
    {
        $festivalBookings = Booking::whereHas('schedule', function($query) {
                $query->where('is_festival_schedule', true);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'confirmed');

        $regularBookings = Booking::whereHas('schedule', function($query) {
                $query->where('is_festival_schedule', false);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'confirmed');

        return [
            'festival_bookings' => $festivalBookings->count(),
            'festival_revenue' => $festivalBookings->sum('total_amount'),
            'regular_bookings' => $regularBookings->count(),
            'regular_revenue' => $regularBookings->sum('total_amount'),
            'festival_avg_fare' => $festivalBookings->avg('total_amount') ?? 0,
            'regular_avg_fare' => $regularBookings->avg('total_amount') ?? 0,
        ];
    }

    /**
     * Get conversion rate.
     */
    private function getConversionRate($startDate, $endDate)
    {
        // This would require tracking search/view events
        // For now, return a placeholder
        return 0;
    }

    /**
     * Get online users count.
     */
    private function getOnlineUsersCount()
    {
        // This would require session tracking
        // For now, return a placeholder
        return rand(10, 50);
    }

    /**
     * Get start date based on period.
     */
    private function getStartDate($period)
    {
        switch ($period) {
            case 'today':
                return Carbon::today();
            case 'week':
                return Carbon::now()->startOfWeek();
            case 'month':
                return Carbon::now()->startOfMonth();
            case 'quarter':
                return Carbon::now()->startOfQuarter();
            case 'year':
                return Carbon::now()->startOfYear();
            default:
                return Carbon::now()->startOfMonth();
        }
    }
}
