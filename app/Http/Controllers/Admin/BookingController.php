<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of all bookings.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'schedule.route', 'schedule.bus', 'schedule.operator']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Operator filter
        if ($request->filled('operator')) {
            $query->whereHas('schedule', function($scheduleQuery) use ($request) {
                $scheduleQuery->where('operator_id', $request->operator);
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get operators for filter
        $operators = User::role('operator')->get();

        return view('admin.bookings.index', compact('bookings', 'operators'));
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        $booking->load(['user', 'schedule.route', 'schedule.bus', 'schedule.operator']);
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking.
     */
    public function edit(Booking $booking)
    {
        $booking->load(['user', 'schedule.route', 'schedule.bus']);
        return view('admin.bookings.edit', compact('booking'));
    }

    /**
     * Update the specified booking in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'passenger_details' => 'required|array',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'special_requests' => 'nullable|string|max:500',
        ]);

        $booking->update([
            'status' => $request->status,
            'passenger_details' => $request->passenger_details,
            'contact_phone' => $request->contact_phone,
            'contact_email' => $request->contact_email,
            'special_requests' => $request->special_requests,
        ]);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Cancel the specified booking.
     */
    public function cancel(Booking $booking)
    {
        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Booking is already cancelled.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Confirm the specified booking.
     */
    public function confirm(Booking $booking)
    {
        if ($booking->status === 'confirmed') {
            return back()->with('error', 'Booking is already confirmed.');
        }

        $booking->update(['status' => 'confirmed']);

        return back()->with('success', 'Booking confirmed successfully.');
    }

    /**
     * Show today's bookings.
     */
    public function today()
    {
        $bookings = Booking::with(['user', 'schedule.route', 'schedule.bus', 'schedule.operator'])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.bookings.today', compact('bookings'));
    }

    /**
     * Show revenue dashboard.
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', 'today');

        $query = Booking::where('status', 'confirmed');

        switch ($period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
        }

        $totalRevenue = $query->sum('total_amount');
        $totalBookings = $query->count();
        $averageBookingValue = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;

        // Revenue by operator
        $revenueByOperator = Booking::with('schedule.operator')
            ->where('status', 'confirmed')
            ->when($period === 'today', function($q) {
                $q->whereDate('created_at', Carbon::today());
            })
            ->when($period === 'month', function($q) {
                $q->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
            })
            ->get()
            ->groupBy('schedule.operator.name')
            ->map(function($bookings) {
                return $bookings->sum('total_amount');
            });

        return view('admin.bookings.revenue', compact(
            'totalRevenue',
            'totalBookings',
            'averageBookingValue',
            'revenueByOperator',
            'period'
        ));
    }
}
