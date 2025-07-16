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
    public function index(Request $request)
    {
        $operator = Auth::user();

        // Get date range from request or default to last 30 days
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $routeId = $request->get('route_id');

        $dateFrom = Carbon::parse($startDate);
        $dateTo = Carbon::parse($endDate);

        // Get operator's routes for filter dropdown
        $routes = $operator->routes()->where('is_active', true)->get();

        // Build base query for bookings
        $bookingsQuery = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->whereBetween('created_at', [$dateFrom, $dateTo]);

        // Apply route filter if selected
        if ($routeId) {
            $bookingsQuery->whereHas('schedule', function($q) use ($routeId) {
                $q->where('route_id', $routeId);
            });
        }

        // Calculate key metrics
        $confirmedBookings = $bookingsQuery->where('status', 'confirmed');
        $totalRevenue = $confirmedBookings->sum('total_amount');
        $totalBookings = $bookingsQuery->count();
        $totalPassengers = $confirmedBookings->sum('passenger_count');

        // Calculate average occupancy
        $schedules = $operator->schedules()
            ->with('bus')
            ->whereBetween('travel_date', [$dateFrom, $dateTo])
            ->get();

        $totalSeats = 0;
        $bookedSeats = 0;

        foreach ($schedules as $schedule) {
            $totalSeats += $schedule->bus->total_seats ?? 0;
            $bookedSeats += $schedule->bookings()->where('status', 'confirmed')->sum('passenger_count');
        }

        $avgOccupancy = $totalSeats > 0 ? ($bookedSeats / $totalSeats) * 100 : 0;

        $metrics = [
            'total_revenue' => $totalRevenue,
            'total_bookings' => $totalBookings,
            'total_passengers' => $totalPassengers,
            'avg_occupancy' => $avgOccupancy,
        ];

        // Get booking status distribution
        $bookingStats = [
            'confirmed' => $bookingsQuery->where('status', 'confirmed')->count(),
            'pending' => $bookingsQuery->where('status', 'pending')->count(),
            'cancelled' => $bookingsQuery->where('status', 'cancelled')->count(),
        ];

        // Get top performing routes
        $topRoutes = $operator->schedules()
            ->with(['route.sourceCity', 'route.destinationCity', 'bus'])
            ->whereBetween('travel_date', [$dateFrom, $dateTo])
            ->get()
            ->groupBy('route_id')
            ->map(function($schedules) {
                $route = $schedules->first()->route;

                $totalBookings = 0;
                $totalPassengers = 0;
                $totalRevenue = 0;
                $totalSeats = 0;

                foreach ($schedules as $schedule) {
                    $confirmedBookings = $schedule->bookings()->where('status', 'confirmed')->get();
                    $totalBookings += $confirmedBookings->count();
                    $totalPassengers += $confirmedBookings->sum('passenger_count');
                    $totalRevenue += $confirmedBookings->sum('total_amount');
                    $totalSeats += $schedule->bus->total_seats ?? 0;
                }

                $avgOccupancy = $totalSeats > 0 ? ($totalPassengers / $totalSeats) * 100 : 0;

                return (object) [
                    'name' => $route->sourceCity->name . ' → ' . $route->destinationCity->name,
                    'source_city' => $route->sourceCity->name,
                    'destination_city' => $route->destinationCity->name,
                    'total_bookings' => $totalBookings,
                    'total_passengers' => $totalPassengers,
                    'total_revenue' => $totalRevenue,
                    'avg_occupancy' => $avgOccupancy,
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(5);

        // Get recent transactions
        $recentTransactions = $bookingsQuery
            ->with(['user', 'schedule.route.sourceCity', 'schedule.route.destinationCity'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($booking) {
                return (object) [
                    'created_at' => $booking->created_at,
                    'customer_name' => $booking->user->name ?? 'Guest',
                    'route_name' => $booking->schedule->route->sourceCity->name . ' → ' . $booking->schedule->route->destinationCity->name,
                    'seats_count' => $booking->passenger_count,
                    'amount' => $booking->total_amount,
                    'status' => $booking->status,
                ];
            });

        return view('operator.reports.index', compact(
            'metrics',
            'bookingStats',
            'topRoutes',
            'recentTransactions',
            'routes',
            'startDate',
            'endDate',
            'routeId'
        ));
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

        $routeData = Route::whereHas('schedules', function($query) use ($operator) {
                $query->where('operator_id', $operator->id);
            })
            ->with(['schedules' => function($query) use ($dateFrom, $dateTo, $operator) {
                $query->where('operator_id', $operator->id)
                      ->whereBetween('travel_date', [$dateFrom, $dateTo])
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
     * Generate bookings report.
     */
    public function bookings(Request $request)
    {
        $operator = Auth::user();

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'status' => 'nullable|in:pending,confirmed,cancelled,completed',
            'route_id' => 'nullable|exists:routes,id',
        ]);

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

        $query = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->with(['user', 'schedule.route', 'schedule.bus'])
          ->whereBetween('created_at', [$dateFrom, $dateTo]);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('route_id')) {
            $query->whereHas('schedule.route', function($q) use ($request) {
                $q->where('id', $request->route_id);
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get filter options
        $routes = $operator->routes()->where('is_active', true)->get();
        $statuses = ['pending', 'confirmed', 'cancelled', 'completed'];

        // Calculate statistics
        $stats = [
            'total_bookings' => $query->count(),
            'total_revenue' => $query->where('status', 'confirmed')->sum('total_amount'),
            'average_booking_value' => $query->where('status', 'confirmed')->avg('total_amount') ?? 0,
            'confirmed_bookings' => $query->where('status', 'confirmed')->count(),
            'pending_bookings' => $query->where('status', 'pending')->count(),
            'cancelled_bookings' => $query->where('status', 'cancelled')->count(),
        ];

        return view('operator.reports.bookings', compact('bookings', 'routes', 'statuses', 'stats', 'dateFrom', 'dateTo'));
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
            'report_type' => 'required|in:revenue,route_performance,bus_utilization,customer_analytics,bookings,passengers',
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
                case 'bookings':
                    $this->exportBookingsReport($file, $operator, $dateFrom, $dateTo);
                    break;
                case 'passengers':
                    $this->exportPassengersReport($file, $operator, $dateFrom, $dateTo);
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
        
        $routes = Route::whereHas('schedules', function($query) use ($operator) {
            $query->where('operator_id', $operator->id);
        })->distinct()->get();
        
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

    /**
     * Generate bus performance report.
     */
    public function buses(Request $request)
    {
        $operator = Auth::user();

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

        $buses = $operator->buses()
            ->with(['busType'])
            ->get()
            ->map(function($bus) use ($dateFrom, $dateTo) {
                $schedules = $bus->schedules()
                    ->whereBetween('travel_date', [$dateFrom, $dateTo])
                    ->get();

                $totalSchedules = $schedules->count();
                $totalBookings = 0;
                $totalRevenue = 0;
                $totalPassengers = 0;

                foreach ($schedules as $schedule) {
                    $confirmedBookings = $schedule->bookings()->where('status', 'confirmed')->get();
                    $totalBookings += $confirmedBookings->count();
                    $totalRevenue += $confirmedBookings->sum('total_amount');
                    $totalPassengers += $confirmedBookings->sum('passenger_count');
                }

                $utilizationRate = $totalSchedules > 0 ? ($totalSchedules / $dateFrom->diffInDays($dateTo)) * 100 : 0;
                $occupancyRate = ($bus->total_seats * $totalSchedules) > 0 ?
                    ($totalPassengers / ($bus->total_seats * $totalSchedules)) * 100 : 0;

                return [
                    'bus' => $bus,
                    'total_schedules' => $totalSchedules,
                    'total_bookings' => $totalBookings,
                    'total_revenue' => $totalRevenue,
                    'total_passengers' => $totalPassengers,
                    'utilization_rate' => min($utilizationRate, 100),
                    'occupancy_rate' => $occupancyRate,
                    'revenue_per_trip' => $totalSchedules > 0 ? $totalRevenue / $totalSchedules : 0,
                ];
            })
            ->sortByDesc('total_revenue');

        return view('operator.reports.buses', compact('buses', 'dateFrom', 'dateTo'));
    }

    /**
     * Generate route performance report.
     */
    public function routes(Request $request)
    {
        $operator = Auth::user();

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

        $routes = $operator->schedules()
            ->with(['route.sourceCity', 'route.destinationCity', 'bus'])
            ->whereBetween('travel_date', [$dateFrom, $dateTo])
            ->get()
            ->groupBy('route_id')
            ->map(function($schedules) {
                $route = $schedules->first()->route;

                $totalSchedules = $schedules->count();
                $totalBookings = 0;
                $totalRevenue = 0;
                $totalPassengers = 0;
                $totalSeats = 0;

                foreach ($schedules as $schedule) {
                    $confirmedBookings = $schedule->bookings()->where('status', 'confirmed')->get();
                    $totalBookings += $confirmedBookings->count();
                    $totalRevenue += $confirmedBookings->sum('total_amount');
                    $totalPassengers += $confirmedBookings->sum('passenger_count');
                    $totalSeats += $schedule->bus->total_seats ?? 0;
                }

                $occupancyRate = $totalSeats > 0 ? ($totalPassengers / $totalSeats) * 100 : 0;
                $avgRevenuePerTrip = $totalSchedules > 0 ? $totalRevenue / $totalSchedules : 0;

                return [
                    'route' => $route,
                    'total_schedules' => $totalSchedules,
                    'total_bookings' => $totalBookings,
                    'total_revenue' => $totalRevenue,
                    'total_passengers' => $totalPassengers,
                    'occupancy_rate' => $occupancyRate,
                    'avg_revenue_per_trip' => $avgRevenuePerTrip,
                ];
            })
            ->sortByDesc('total_revenue');

        return view('operator.reports.routes', compact('routes', 'dateFrom', 'dateTo'));
    }

    /**
     * Generate passenger analytics report.
     */
    public function passengers(Request $request)
    {
        $operator = Auth::user();

        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->subMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now();

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
                  'total_passengers' => $bookings->sum('passenger_count'),
                  'average_booking_value' => $bookings->avg('total_amount'),
                  'last_booking' => $bookings->max('created_at'),
              ];
          })
          ->sortByDesc('total_spent')
          ->take(50);

        // Passenger demographics
        $totalPassengers = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'confirmed')
          ->whereBetween('created_at', [$dateFrom, $dateTo])
          ->sum('passenger_count');

        $repeatCustomers = $topCustomers->where('total_bookings', '>', 1)->count();
        $newCustomers = $topCustomers->where('total_bookings', '=', 1)->count();

        $stats = [
            'total_passengers' => $totalPassengers,
            'unique_customers' => $topCustomers->count(),
            'repeat_customers' => $repeatCustomers,
            'new_customers' => $newCustomers,
            'repeat_rate' => $topCustomers->count() > 0 ? ($repeatCustomers / $topCustomers->count()) * 100 : 0,
        ];

        return view('operator.reports.passengers', compact('topCustomers', 'stats', 'dateFrom', 'dateTo'));
    }

    /**
     * Export revenue report.
     */
    public function exportRevenue(Request $request)
    {
        return $this->export($request->merge(['report_type' => 'revenue']));
    }

    /**
     * Export bookings report.
     */
    public function exportBookings(Request $request)
    {
        return $this->export($request->merge(['report_type' => 'bookings']));
    }

    /**
     * Export passengers report.
     */
    public function exportPassengers(Request $request)
    {
        return $this->export($request->merge(['report_type' => 'passengers']));
    }

    private function exportBookingsReport($file, $operator, $dateFrom, $dateTo)
    {
        // CSV headers
        fputcsv($file, [
            'Booking Reference',
            'Date',
            'Customer',
            'Route',
            'Seats',
            'Passengers',
            'Amount',
            'Status',
            'Payment Method',
            'Booking Type'
        ]);

        $bookings = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->with(['user', 'schedule.route.sourceCity', 'schedule.route.destinationCity'])
          ->whereBetween('created_at', [$dateFrom, $dateTo])
          ->orderBy('created_at', 'desc')
          ->get();

        foreach ($bookings as $booking) {
            fputcsv($file, [
                $booking->booking_reference,
                $booking->created_at->format('Y-m-d H:i:s'),
                $booking->user->name ?? 'Guest',
                $booking->schedule->route->sourceCity->name . ' → ' . $booking->schedule->route->destinationCity->name,
                implode(', ', $booking->seat_numbers),
                $booking->passenger_count,
                $booking->total_amount,
                $booking->status,
                $booking->payment_method,
                $booking->booking_type
            ]);
        }
    }

    private function exportPassengersReport($file, $operator, $dateFrom, $dateTo)
    {
        // CSV headers
        fputcsv($file, [
            'Customer Name',
            'Email',
            'Total Bookings',
            'Total Passengers',
            'Total Spent',
            'Average Booking Value',
            'Last Booking',
            'Customer Type'
        ]);

        $customers = Booking::whereHas('schedule', function($q) use ($operator) {
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
                  'total_passengers' => $bookings->sum('passenger_count'),
                  'total_spent' => $bookings->sum('total_amount'),
                  'average_booking_value' => $bookings->avg('total_amount'),
                  'last_booking' => $bookings->max('created_at'),
              ];
          })
          ->sortByDesc('total_spent');

        foreach ($customers as $customerData) {
            fputcsv($file, [
                $customerData['user']->name ?? 'Guest',
                $customerData['user']->email ?? 'N/A',
                $customerData['total_bookings'],
                $customerData['total_passengers'],
                $customerData['total_spent'],
                number_format($customerData['average_booking_value'], 2),
                \Carbon\Carbon::parse($customerData['last_booking'])->format('Y-m-d H:i:s'),
                $customerData['total_bookings'] > 1 ? 'Repeat' : 'New'
            ]);
        }
    }
}
