<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard.
     */
    public function index()
    {
        // Overall statistics
        $stats = [
            'total_bookings' => Booking::count(),
            'total_revenue' => Booking::where('status', 'confirmed')->sum('total_amount'),
            'total_operators' => User::role('operator')->count(),
            'total_routes' => Route::count(),
            'monthly_bookings' => Booking::whereMonth('created_at', Carbon::now()->month)->count(),
            'monthly_revenue' => Booking::where('status', 'confirmed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('total_amount'),
        ];

        // Monthly booking trends (last 12 months)
        $monthlyTrends = Booking::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as bookings'),
                DB::raw('SUM(CASE WHEN status = "confirmed" THEN total_amount ELSE 0 END) as revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Top performing routes
        $topRoutes = Route::withCount(['schedules as total_bookings' => function($query) {
                $query->join('bookings', 'schedules.id', '=', 'bookings.schedule_id')
                      ->where('bookings.status', 'confirmed');
            }])
            ->orderBy('total_bookings', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact('stats', 'monthlyTrends', 'topRoutes'));
    }

    /**
     * Generate booking reports.
     */
    public function bookings(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $operator = $request->get('operator');
        $status = $request->get('status');

        $query = Booking::with(['user', 'schedule.route', 'schedule.operator'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        if ($operator) {
            $query->whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(50);

        // Summary statistics
        $summary = [
            'total_bookings' => $query->count(),
            'confirmed_bookings' => $query->where('status', 'confirmed')->count(),
            'cancelled_bookings' => $query->where('status', 'cancelled')->count(),
            'total_revenue' => $query->where('status', 'confirmed')->sum('total_amount'),
        ];

        $operators = User::role('operator')->get();

        return view('admin.reports.bookings', compact(
            'bookings',
            'summary',
            'operators',
            'dateFrom',
            'dateTo',
            'operator',
            'status'
        ));
    }

    /**
     * Generate revenue reports.
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', 'month');
        $year = $request->get('year', Carbon::now()->year);
        $month = $request->get('month', Carbon::now()->month);

        // Revenue by period
        $revenueData = [];

        if ($period === 'month') {
            // Daily revenue for the month
            $revenueData = Booking::select(
                    DB::raw('DAY(created_at) as day'),
                    DB::raw('SUM(CASE WHEN status = "confirmed" THEN total_amount ELSE 0 END) as revenue'),
                    DB::raw('COUNT(*) as bookings')
                )
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->groupBy('day')
                ->orderBy('day')
                ->get();
        } else {
            // Monthly revenue for the year
            $revenueData = Booking::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(CASE WHEN status = "confirmed" THEN total_amount ELSE 0 END) as revenue'),
                    DB::raw('COUNT(*) as bookings')
                )
                ->whereYear('created_at', $year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        // Revenue by operator
        $operatorRevenue = User::role('operator')
            ->with(['operatorBookings' => function($query) use ($year, $month, $period) {
                $query->where('status', 'confirmed');
                if ($period === 'month') {
                    $query->whereYear('created_at', $year)
                          ->whereMonth('created_at', $month);
                } else {
                    $query->whereYear('created_at', $year);
                }
            }])
            ->get()
            ->map(function($operator) {
                return [
                    'name' => $operator->name,
                    'company' => $operator->company_name,
                    'revenue' => $operator->operatorBookings->sum('total_amount'),
                    'bookings' => $operator->operatorBookings->count(),
                ];
            })
            ->sortByDesc('revenue');

        return view('admin.reports.revenue', compact(
            'revenueData',
            'operatorRevenue',
            'period',
            'year',
            'month'
        ));
    }

    /**
     * Generate operator performance reports.
     */
    public function operators(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $operators = User::role('operator')
            ->withCount([
                'buses',
                'schedules',
                'operatorBookings as total_bookings',
                'operatorBookings as confirmed_bookings' => function($query) {
                    $query->where('status', 'confirmed');
                }
            ])
            ->with(['operatorBookings' => function($query) use ($dateFrom, $dateTo) {
                $query->where('status', 'confirmed')
                      ->whereBetween('created_at', [$dateFrom, $dateTo]);
            }])
            ->get()
            ->map(function($operator) {
                $revenue = $operator->operatorBookings->sum('total_amount');
                $bookings = $operator->operatorBookings->count();

                return [
                    'id' => $operator->id,
                    'name' => $operator->name,
                    'company_name' => $operator->company_name,
                    'email' => $operator->email,
                    'phone' => $operator->phone,
                    'buses_count' => $operator->buses_count,
                    'schedules_count' => $operator->schedules_count,
                    'total_bookings' => $operator->total_bookings,
                    'confirmed_bookings' => $operator->confirmed_bookings,
                    'period_revenue' => $revenue,
                    'period_bookings' => $bookings,
                    'average_booking_value' => $bookings > 0 ? $revenue / $bookings : 0,
                    'is_active' => $operator->is_active,
                ];
            });

        return view('admin.reports.operators', compact('operators', 'dateFrom', 'dateTo'));
    }

    /**
     * Export booking reports to PDF.
     */
    public function exportBookings(Request $request)
    {
        try {
            $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $dateTo = $request->get('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));
            $operator = $request->get('operator');
            $status = $request->get('status');

            \Log::info('Admin booking export started', [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'operator' => $operator,
                'status' => $status
            ]);

            $query = Booking::with(['user', 'schedule.route.sourceCity', 'schedule.route.destinationCity', 'schedule.bus', 'schedule.operator'])
                ->whereBetween('created_at', [$dateFrom, $dateTo]);

            if ($operator) {
                $query->whereHas('schedule', function($q) use ($operator) {
                    $q->where('operator_id', $operator);
                });
            }

            if ($status) {
                $query->where('status', $status);
            }

            $bookings = $query->orderBy('created_at', 'desc')->get();

            \Log::info('Bookings retrieved for admin export', [
                'booking_count' => $bookings->count()
            ]);

            // Generate PDF
            $pdf = PDF::loadView('admin.reports.export-bookings-pdf', [
                'bookings' => $bookings,
                'filters' => [
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'operator' => $operator,
                    'status' => $status
                ],
                'exportDate' => now(),
                'summary' => [
                    'total_bookings' => $bookings->count(),
                    'confirmed_bookings' => $bookings->where('status', 'confirmed')->count(),
                    'cancelled_bookings' => $bookings->where('status', 'cancelled')->count(),
                    'total_revenue' => $bookings->where('status', 'confirmed')->sum('total_amount'),
                ]
            ]);

            $filename = 'admin_bookings_export_' . $dateFrom . '_to_' . $dateTo . '_' . now()->format('Y_m_d_H_i_s') . '.pdf';

            \Log::info('Admin booking export completed', [
                'filename' => $filename,
                'bookings_exported' => $bookings->count()
            ]);

            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Admin booking export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to export booking reports. Please try again.');
        }
    }

    /**
     * Export revenue reports to PDF.
     */
    public function exportRevenue(Request $request)
    {
        try {
            $period = $request->get('period', 'month');
            $year = $request->get('year', Carbon::now()->year);
            $month = $request->get('month', Carbon::now()->month);

            \Log::info('Admin revenue export started', [
                'period' => $period,
                'year' => $year,
                'month' => $month
            ]);

            // Get revenue data based on period
            $query = Booking::where('status', 'confirmed');

            if ($period === 'month') {
                $query->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month);
            } else {
                $query->whereYear('created_at', $year);
            }

            $bookings = $query->with(['schedule.operator', 'schedule.route.sourceCity', 'schedule.route.destinationCity'])
                             ->orderBy('created_at', 'desc')
                             ->get();

            // Revenue by operator
            $operatorRevenue = $bookings->groupBy('schedule.operator.company_name')
                                      ->map(function($operatorBookings) {
                                          return [
                                              'bookings' => $operatorBookings->count(),
                                              'revenue' => $operatorBookings->sum('total_amount')
                                          ];
                                      })
                                      ->sortByDesc('revenue');

            // Generate PDF
            $pdf = PDF::loadView('admin.reports.export-revenue-pdf', [
                'bookings' => $bookings,
                'operatorRevenue' => $operatorRevenue,
                'period' => $period,
                'year' => $year,
                'month' => $month,
                'exportDate' => now(),
                'summary' => [
                    'total_revenue' => $bookings->sum('total_amount'),
                    'total_bookings' => $bookings->count(),
                    'average_booking_value' => $bookings->count() > 0 ? $bookings->sum('total_amount') / $bookings->count() : 0,
                    'top_operator' => $operatorRevenue->first()
                ]
            ]);

            $filename = 'admin_revenue_export_' . $period . '_' . $year . ($period === 'month' ? '_' . $month : '') . '_' . now()->format('Y_m_d_H_i_s') . '.pdf';

            \Log::info('Admin revenue export completed', [
                'filename' => $filename,
                'total_revenue' => $bookings->sum('total_amount')
            ]);

            return $pdf->download($filename);

        } catch (\Exception $e) {
            \Log::error('Admin revenue export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to export revenue reports. Please try again.');
        }
    }

    /**
     * Export operator reports to Excel/CSV.
     */
    public function exportOperators(Request $request)
    {
        // Implementation for exporting operator data
        return response()->json(['message' => 'Export functionality will be implemented']);
    }
}
