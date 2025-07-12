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
            ->where('bookings.booking_type', 'counter')
            ->orderBy('bookings.created_at', 'desc')
            ->limit(10)
            ->get();

        // Statistics
        $stats = [
            'today_schedules' => $todaySchedules->count(),
            'today_bookings' => Auth::user()->operatorBookings()
                ->whereDate('bookings.created_at', Carbon::today())
                ->where('bookings.booking_type', 'counter')
                ->count(),
            'today_revenue' => Auth::user()->operatorBookings()
                ->whereDate('bookings.created_at', Carbon::today())
                ->where('bookings.booking_type', 'counter')
                ->where('bookings.status', 'confirmed')
                ->sum('bookings.total_amount'),
            'pending_bookings' => Auth::user()->operatorBookings()
                ->where('bookings.status', 'pending')
                ->where('bookings.booking_type', 'counter')
                ->count(),
        ];

        return view('operator.counter.index', compact('todaySchedules', 'recentBookings', 'stats'));
    }

    /**
     * Show the search form for counter booking.
     */
    public function search()
    {
        // Get unique cities that are used in routes for this operator
        // Use a direct query to avoid ambiguous column issues
        $operatorRoutes = Route::whereHas('schedules', function($query) {
            $query->where('operator_id', Auth::id());
        })->pluck('routes.id');

        $cityIds = Route::whereIn('id', $operatorRoutes)
            ->select('source_city_id', 'destination_city_id')
            ->get()
            ->flatMap(function ($route) {
                return [$route->source_city_id, $route->destination_city_id];
            })
            ->unique();

        $cities = City::whereIn('id', $cityIds)->orderBy('name')->get();

        // Fallback to all cities if operator has no routes
        if ($cities->isEmpty()) {
            $cities = City::orderBy('name')->get();
        }

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

        // Debug: Log the seat map structure for troubleshooting
        \Log::info('Seat map structure for schedule ' . $schedule->id, [
            'has_layout' => isset($seatMap['layout']),
            'has_seats' => isset($seatMap['seats']),
            'layout_keys' => isset($seatMap['layout']) ? array_keys($seatMap['layout']) : [],
            'seat_count' => isset($seatMap['seats']) ? count($seatMap['seats']) : 0,
            'first_seat' => isset($seatMap['seats'][0]) ? $seatMap['seats'][0] : null,
        ]);

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

        // Log request data for debugging
        \Log::info('Counter booking request received', [
            'request_data' => $request->all(),
            'schedule_id' => $schedule->id,
            'operator_id' => Auth::id(),
        ]);

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

            // Determine the best email to use for contact
            $contactEmail = $request->contact_email ?: $request->passenger_email;

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
                'contact_email' => $contactEmail,
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

            // Log the error for debugging
            \Log::error('Counter booking failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
                'schedule_id' => $schedule->id,
                'operator_id' => Auth::id(),
            ]);

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

        // If the seat layout doesn't have the expected structure, create a default one
        if (!isset($seatLayout['seats']) || !isset($seatLayout['rows']) || !isset($seatLayout['columns'])) {
            // Generate a default layout based on total seats
            $totalSeats = $schedule->bus->total_seats;
            $columns = 4; // Default 4 columns
            $rows = ceil($totalSeats / $columns);

            $seats = [];
            for ($i = 1; $i <= $totalSeats; $i++) {
                $seats[] = [
                    'number' => $i,
                    'type' => 'seat',
                    'is_booked' => in_array($i, $bookedSeats),
                    'is_available' => !in_array($i, $bookedSeats),
                ];
            }

            return [
                'layout' => [
                    'rows' => $rows,
                    'columns' => $columns,
                    'aisle_position' => 2,
                ],
                'seats' => $seats,
            ];
        }

        // Update booking status for each seat
        foreach ($seatLayout['seats'] as &$seat) {
            $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? null;
            if ($seatNumber) {
                $seat['is_booked'] = in_array($seatNumber, $bookedSeats);
                $seat['is_available'] = !$seat['is_booked'];
                $seat['type'] = 'seat'; // Ensure type is set
                $seat['number'] = $seatNumber; // Normalize the key

                // Remove the old key if it exists
                if (isset($seat['seat_number']) && $seat['seat_number'] !== $seat['number']) {
                    unset($seat['seat_number']);
                }
            }
        }

        // Return the structure expected by the view
        return [
            'layout' => [
                'rows' => $seatLayout['rows'] ?? ceil($schedule->bus->total_seats / 4),
                'columns' => $seatLayout['columns'] ?? 4,
                'aisle_position' => $seatLayout['aisle_position'] ?? 2,
            ],
            'seats' => $seatLayout['seats'],
        ];
    }
}
