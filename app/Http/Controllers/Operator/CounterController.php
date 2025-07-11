<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Route;
use App\Models\Booking;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CounterController extends Controller
{
    /**
     * Display the counter booking dashboard.
     */
    public function index()
    {
        // Get today's schedules for this operator
        $todaySchedules = Auth::user()->schedules()
            ->with(['route', 'bus'])
            ->whereDate('travel_date', Carbon::today())
            ->orderBy('departure_time')
            ->get();

        // Get recent counter bookings
        $recentBookings = Auth::user()->operatorBookings()
            ->with(['user', 'schedule.route'])
            ->where('booking_type', 'counter')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Statistics
        $stats = [
            'today_schedules' => $todaySchedules->count(),
            'today_bookings' => Auth::user()->operatorBookings()
                ->whereDate('created_at', Carbon::today())
                ->where('booking_type', 'counter')
                ->count(),
            'today_revenue' => Auth::user()->operatorBookings()
                ->whereDate('created_at', Carbon::today())
                ->where('booking_type', 'counter')
                ->where('status', 'confirmed')
                ->sum('total_amount'),
            'pending_bookings' => Auth::user()->operatorBookings()
                ->where('status', 'pending')
                ->where('booking_type', 'counter')
                ->count(),
        ];

        return view('operator.counter.index', compact('todaySchedules', 'recentBookings', 'stats'));
    }

    /**
     * Show the search form for counter booking.
     */
    public function search()
    {
        $cities = City::orderBy('name')->get();
        return view('operator.counter.search', compact('cities'));
    }

    /**
     * Process search and show available schedules.
     */
    public function searchResults(Request $request)
    {
        $request->validate([
            'source_city_id' => 'required|exists:cities,id',
            'destination_city_id' => 'required|exists:cities,id|different:source_city_id',
            'travel_date' => 'required|date|after_or_equal:today',
        ]);

        // Find routes matching the cities
        $routes = Route::where(function($query) use ($request) {
            $query->where('source_city_id', $request->source_city_id)
                  ->where('destination_city_id', $request->destination_city_id);
        })->pluck('id');

        if ($routes->isEmpty()) {
            return back()->withInput()
                ->with('error', 'No routes available for the selected cities.');
        }

        // Find available schedules for this operator
        $schedules = Auth::user()->schedules()
            ->with(['route.sourceCity', 'route.destinationCity', 'bus.busType'])
            ->whereIn('route_id', $routes)
            ->whereDate('travel_date', $request->travel_date)
            ->where('status', 'scheduled')
            ->where('available_seats', '>', 0)
            ->orderBy('departure_time')
            ->get();

        $searchParams = $request->only(['source_city_id', 'destination_city_id', 'travel_date']);

        return view('operator.counter.search-results', compact('schedules', 'searchParams'));
    }

    /**
     * Show booking form for selected schedule.
     */
    public function book(Schedule $schedule)
    {
        // Ensure operator can only book their own schedules
        if ($schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to schedule.');
        }

        if ($schedule->available_seats <= 0) {
            return back()->with('error', 'No seats available for this schedule.');
        }

        $schedule->load(['route.sourceCity', 'route.destinationCity', 'bus.busType']);

        // Get seat map
        $seatMap = $this->generateSeatMapWithBookings($schedule);

        return view('operator.counter.book', compact('schedule', 'seatMap'));
    }

    /**
     * Store the counter booking.
     */
    public function storeBooking(Request $request, Schedule $schedule)
    {
        // Ensure operator can only book their own schedules
        if ($schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to schedule.');
        }

        $request->validate([
            'passenger_name' => 'required|string|max:255',
            'passenger_phone' => 'required|string|max:20',
            'passenger_email' => 'nullable|email|max:255',
            'passenger_age' => 'required|integer|min:1|max:120',
            'passenger_gender' => 'required|in:male,female,other',
            'seat_numbers' => 'required|array|min:1',
            'seat_numbers.*' => 'integer|min:1',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'special_requests' => 'nullable|string|max:500',
            'payment_method' => 'required|in:cash,card,digital',
        ]);

        // Validate seat availability
        $requestedSeats = $request->seat_numbers;
        $bookedSeats = $schedule->bookings()
            ->where('status', '!=', 'cancelled')
            ->pluck('seat_numbers')
            ->flatten()
            ->toArray();

        $unavailableSeats = array_intersect($requestedSeats, $bookedSeats);
        if (!empty($unavailableSeats)) {
            return back()->withInput()
                ->with('error', 'Seats ' . implode(', ', $unavailableSeats) . ' are no longer available.');
        }

        if (count($requestedSeats) > $schedule->available_seats) {
            return back()->withInput()
                ->with('error', 'Not enough seats available.');
        }

        DB::beginTransaction();
        try {
            // Calculate total amount
            $passengerCount = count($requestedSeats);
            $farePerSeat = $schedule->fare;
            $totalAmount = $farePerSeat * $passengerCount;

            // Create booking
            $booking = Booking::create([
                'booking_reference' => 'CTR-' . strtoupper(uniqid()),
                'user_id' => Auth::id(), // Counter booking by operator
                'schedule_id' => $schedule->id,
                'passenger_count' => $passengerCount,
                'seat_numbers' => $requestedSeats,
                'passenger_details' => [
                    [
                        'name' => $request->passenger_name,
                        'phone' => $request->passenger_phone,
                        'email' => $request->passenger_email,
                        'age' => $request->passenger_age,
                        'gender' => $request->passenger_gender,
                    ]
                ],
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'total_amount' => $totalAmount,
                'booking_type' => 'counter',
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid', // Counter bookings are paid immediately
                'status' => 'confirmed',
                'special_requests' => $request->special_requests,
                'booked_by' => Auth::id(),
            ]);

            // Update available seats
            $schedule->decrement('available_seats', $passengerCount);

            DB::commit();

            return redirect()->route('operator.counter.receipt', $booking)
                ->with('success', 'Booking created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create booking: ' . $e->getMessage());
        }
    }

    /**
     * Show seat selection interface.
     */
    public function seatSelection(Schedule $schedule)
    {
        // Ensure operator can only access their own schedules
        if ($schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to schedule.');
        }

        $schedule->load(['route', 'bus']);
        $seatMap = $this->generateSeatMapWithBookings($schedule);

        return response()->json([
            'success' => true,
            'seat_map' => $seatMap,
            'available_seats' => $schedule->available_seats,
            'fare_per_seat' => $schedule->fare,
        ]);
    }

    /**
     * Reserve seats temporarily.
     */
    public function reserveSeats(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seat_numbers' => 'required|array|min:1',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        // Ensure operator can only reserve seats for their own schedules
        if ($schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to schedule.');
        }

        // For counter bookings, we don't need temporary reservation
        // as the booking is completed immediately
        return response()->json([
            'success' => true,
            'message' => 'Seats can be booked immediately.',
        ]);
    }

    /**
     * Show booking receipt.
     */
    public function receipt(Booking $booking)
    {
        // Ensure operator can only view receipts for their bookings
        if ($booking->schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        $booking->load(['schedule.route.sourceCity', 'schedule.route.destinationCity', 'schedule.bus']);

        return view('operator.counter.receipt', compact('booking'));
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

        foreach ($seatLayout['seats'] as &$seat) {
            $seat['is_booked'] = in_array($seat['number'], $bookedSeats);
            $seat['is_available'] = !$seat['is_booked'];
        }

        return $seatLayout;
    }
}
