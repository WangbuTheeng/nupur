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
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        $operator = Auth::user();
        
        return view('operator.reports.index');
    }

    /**
     * Generate revenue report.
     */
    public function revenue(Request $request)
    {
        $operator = Auth::user();
        
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'group_by' => 'in:day,week,month',
        ]);

        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);
        $groupBy = $request->group_by ?? 'day';

        $query = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'confirmed')
          ->whereBetween('created_at', [$dateFrom, $dateTo]);

        $revenueData = [];
        $totalRevenue = 0;
        $totalBookings = 0;

        switch ($groupBy) {
            case 'day':
                $period = $dateFrom->copy();
                while ($period->lte($dateTo)) {
                    $dayRevenue = $query->clone()->whereDate('created_at', $period)->sum('total_amount');
                    $dayBookings = $query->clone()->whereDate('created_at', $period)->count();
                    
                    $revenueData[] = [
                        'period' => $period->format('Y-m-d'),
                        'label' => $period->format('M d, Y'),
                        'revenue' => $dayRevenue,
                        'bookings' => $dayBookings,
                    ];
                    
                    $totalRevenue += $dayRevenue;
                    $totalBookings += $dayBookings;
                    $period->addDay();
                }
                break;

            case 'week':
                $period = $dateFrom->copy()->startOfWeek();
                while ($period->lte($dateTo)) {
                    $weekEnd = $period->copy()->endOfWeek();
                    $weekRevenue = $query->clone()->whereBetween('created_at', [$period, $weekEnd])->sum('total_amount');
                    $weekBookings = $query->clone()->whereBetween('created_at', [$period, $weekEnd])->count();
                    
                    $revenueData[] = [
                        'period' => $period->format('Y-m-d'),
                        'label' => $period->format('M d') . ' - ' . $weekEnd->format('M d, Y'),
                        'revenue' => $weekRevenue,
                        'bookings' => $weekBookings,
                    ];
                    
                    $totalRevenue += $weekRevenue;
                    $totalBookings += $weekBookings;
                    $period->addWeek();
                }
                break;

            case 'month':
                $period = $dateFrom->copy()->startOfMonth();
                while ($period->lte($dateTo)) {
                    $monthRevenue = $query->clone()->whereMonth('created_at', $period->month)
                                         ->whereYear('created_at', $period->year)->sum('total_amount');
                    $monthBookings = $query->clone()->whereMonth('created_at', $period->month)
                                          ->whereYear('created_at', $period->year)->count();
                    
                    $revenueData[] = [
                        'period' => $period->format('Y-m'),
                        'label' => $period->format('M Y'),
                        'revenue' => $monthRevenue,
                        'bookings' => $monthBookings,
                    ];
                    
                    $totalRevenue += $monthRevenue;
                    $totalBookings += $monthBookings;
                    $period->addMonth();
                }
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $revenueData,
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_bookings' => $totalBookings,
                'average_booking_value' => $totalBookings > 0 ? $totalRevenue / $totalBookings : 0,
                'period' => $dateFrom->format('M d, Y') . ' - ' . $dateTo->format('M d, Y'),
            ],
        ]);
    }

    /**
     * Generate route performance report.
     */
    public function routePerformance(Request $request)
    {
        $operator = Auth::user();
        
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        $routeData = Route::where('operator_id', $operator->id)
            ->with(['schedules' => function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('travel_date', [$dateFrom, $dateTo])
                      ->with(['bookings' => function($bookingQuery) {
                          $bookingQuery->where('status', 'confirmed');
                      }]);
            }])
            ->get()
            ->map(function($route) {
                $totalSchedules = $route->schedules->count();
                $totalBookings = $route->schedules->sum(function($schedule) {
                    return $schedule->bookings->count();
                });
                $totalRevenue = $route->schedules->sum(function($schedule) {
                    return $schedule->bookings->sum('total_amount');
                });
                $totalSeats = $route->schedules->sum(function($schedule) {
                    return $schedule->bus->total_seats ?? 0;
                });
                $bookedSeats = $route->schedules->sum(function($schedule) {
                    return $schedule->bookings->sum('passenger_count');
                });
                
                return [
                    'route' => $route,
                    'total_schedules' => $totalSchedules,
                    'total_bookings' => $totalBookings,
                    'total_revenue' => $totalRevenue,
                    'occupancy_rate' => $totalSeats > 0 ? ($bookedSeats / $totalSeats) * 100 : 0,
                    'average_revenue_per_trip' => $totalSchedules > 0 ? $totalRevenue / $totalSchedules : 0,
                ];
            })
            ->sortByDesc('total_revenue');

        return response()->json([
            'success' => true,
            'data' => $routeData->values(),
        ]);
    }

    /**
     * Generate bus utilization report.
     */
    public function busUtilization(Request $request)
    {
        $operator = Auth::user();
        
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        $busData = Bus::where('operator_id', $operator->id)
            ->with(['schedules' => function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('travel_date', [$dateFrom, $dateTo])
                      ->with(['bookings' => function($bookingQuery) {
                          $bookingQuery->where('status', 'confirmed');
                      }]);
            }])
            ->get()
            ->map(function($bus) use ($dateFrom, $dateTo) {
                $totalSchedules = $bus->schedules->count();
                $totalBookings = $bus->schedules->sum(function($schedule) {
                    return $schedule->bookings->count();
                });
                $totalRevenue = $bus->schedules->sum(function($schedule) {
                    return $schedule->bookings->sum('total_amount');
                });
                $totalPossibleSeats = $totalSchedules * ($bus->total_seats ?? 0);
                $bookedSeats = $bus->schedules->sum(function($schedule) {
                    return $schedule->bookings->sum('passenger_count');
                });
                
                $daysBetween = $dateFrom->diffInDays($dateTo) + 1;
                $utilizationRate = $daysBetween > 0 ? ($totalSchedules / $daysBetween) * 100 : 0;
                
                return [
                    'bus' => $bus,
                    'total_schedules' => $totalSchedules,
                    'total_bookings' => $totalBookings,
                    'total_revenue' => $totalRevenue,
                    'occupancy_rate' => $totalPossibleSeats > 0 ? ($bookedSeats / $totalPossibleSeats) * 100 : 0,
                    'utilization_rate' => min($utilizationRate, 100),
                    'revenue_per_day' => $daysBetween > 0 ? $totalRevenue / $daysBetween : 0,
                ];
            })
            ->sortByDesc('total_revenue');

        return response()->json([
            'success' => true,
            'data' => $busData->values(),
        ]);
    }

    /**
     * Generate customer analytics report.
     */
    public function customerAnalytics(Request $request)
    {
        $operator = Auth::user();
        
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        // Top customers by revenue
        $topCustomers = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'confirmed')
          ->whereBetween('created_at', [$dateFrom, $dateTo])
          ->with('user')
          ->get()
          ->groupBy('user_id')
          ->map(function($bookings) {
              $user = $bookings->first()->user;
              return [
                  'user' => $user,
                  'total_bookings' => $bookings->count(),
                  'total_spent' => $bookings->sum('total_amount'),
                  'average_booking_value' => $bookings->avg('total_amount'),
                  'last_booking' => $bookings->max('created_at'),
              ];
          })
          ->sortByDesc('total_spent')
          ->take(20);

        // New vs returning customers
        $newCustomers = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->whereBetween('created_at', [$dateFrom, $dateTo])
          ->whereDoesntHave('user.bookings', function($q) use ($dateFrom) {
              $q->where('created_at', '<', $dateFrom);
          })
          ->distinct('user_id')
          ->count();

        $returningCustomers = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->whereBetween('created_at', [$dateFrom, $dateTo])
          ->whereHas('user.bookings', function($q) use ($dateFrom) {
              $q->where('created_at', '<', $dateFrom);
          })
          ->distinct('user_id')
          ->count();

        return response()->json([
            'success' => true,
            'top_customers' => $topCustomers->values(),
            'customer_breakdown' => [
                'new_customers' => $newCustomers,
                'returning_customers' => $returningCustomers,
                'total_customers' => $newCustomers + $returningCustomers,
            ],
        ]);
    }

    /**
     * Export report data to CSV.
     */
    public function export(Request $request)
    {
        $operator = Auth::user();
        
        $request->validate([
            'report_type' => 'required|in:revenue,route_performance,bus_utilization,customer_analytics',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $reportType = $request->report_type;
        $dateFrom = Carbon::parse($request->date_from);
        $dateTo = Carbon::parse($request->date_to);

        $filename = $reportType . '_report_' . $dateFrom->format('Y-m-d') . '_to_' . $dateTo->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportType, $dateFrom, $dateTo, $operator) {
            $file = fopen('php://output', 'w');
            
            switch ($reportType) {
                case 'revenue':
                    $this->exportRevenueReport($file, $operator, $dateFrom, $dateTo);
                    break;
                case 'route_performance':
                    $this->exportRoutePerformanceReport($file, $operator, $dateFrom, $dateTo);
                    break;
                case 'bus_utilization':
                    $this->exportBusUtilizationReport($file, $operator, $dateFrom, $dateTo);
                    break;
                case 'customer_analytics':
                    $this->exportCustomerAnalyticsReport($file, $operator, $dateFrom, $dateTo);
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportRevenueReport($file, $operator, $dateFrom, $dateTo)
    {
        fputcsv($file, ['Date', 'Bookings', 'Revenue']);
        
        $period = $dateFrom->copy();
        while ($period->lte($dateTo)) {
            $dayBookings = Booking::whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('status', 'confirmed')
              ->whereDate('created_at', $period)
              ->count();
              
            $dayRevenue = Booking::whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('status', 'confirmed')
              ->whereDate('created_at', $period)
              ->sum('total_amount');
            
            fputcsv($file, [
                $period->format('Y-m-d'),
                $dayBookings,
                $dayRevenue,
            ]);
            
            $period->addDay();
        }
    }

    private function exportRoutePerformanceReport($file, $operator, $dateFrom, $dateTo)
    {
        fputcsv($file, ['Route', 'Total Schedules', 'Total Bookings', 'Total Revenue', 'Occupancy Rate %']);
        
        $routes = Route::where('operator_id', $operator->id)->get();
        
        foreach ($routes as $route) {
            $schedules = $route->schedules()->whereBetween('travel_date', [$dateFrom, $dateTo])->get();
            $totalSchedules = $schedules->count();
            $totalBookings = $schedules->sum(function($schedule) {
                return $schedule->bookings()->where('status', 'confirmed')->count();
            });
            $totalRevenue = $schedules->sum(function($schedule) {
                return $schedule->bookings()->where('status', 'confirmed')->sum('total_amount');
            });
            
            fputcsv($file, [
                $route->full_name,
                $totalSchedules,
                $totalBookings,
                $totalRevenue,
                0, // Simplified occupancy rate
            ]);
        }
    }

    private function exportBusUtilizationReport($file, $operator, $dateFrom, $dateTo)
    {
        fputcsv($file, ['Bus', 'Total Schedules', 'Total Bookings', 'Total Revenue', 'Utilization Rate %']);
        
        $buses = Bus::where('operator_id', $operator->id)->get();
        
        foreach ($buses as $bus) {
            $schedules = $bus->schedules()->whereBetween('travel_date', [$dateFrom, $dateTo])->get();
            $totalSchedules = $schedules->count();
            $totalBookings = $schedules->sum(function($schedule) {
                return $schedule->bookings()->where('status', 'confirmed')->count();
            });
            $totalRevenue = $schedules->sum(function($schedule) {
                return $schedule->bookings()->where('status', 'confirmed')->sum('total_amount');
            });
            
            fputcsv($file, [
                $bus->display_name,
                $totalSchedules,
                $totalBookings,
                $totalRevenue,
                0, // Simplified utilization rate
            ]);
        }
    }

    private function exportCustomerAnalyticsReport($file, $operator, $dateFrom, $dateTo)
    {
        fputcsv($file, ['Customer Name', 'Email', 'Total Bookings', 'Total Spent', 'Average Booking Value']);
        
        $customers = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'confirmed')
          ->whereBetween('created_at', [$dateFrom, $dateTo])
          ->with('user')
          ->get()
          ->groupBy('user_id');
        
        foreach ($customers as $userBookings) {
            $user = $userBookings->first()->user;
            $totalBookings = $userBookings->count();
            $totalSpent = $userBookings->sum('total_amount');
            $averageValue = $totalSpent / $totalBookings;
            
            fputcsv($file, [
                $user->name,
                $user->email,
                $totalBookings,
                $totalSpent,
                $averageValue,
            ]);
        }
    }
}
