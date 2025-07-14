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

        // Find available schedules for this operator (counter booking allowed until departure)
        $schedules = Auth::user()->schedules()
            ->with(['route.sourceCity', 'route.destinationCity', 'bus.busType'])
            ->whereIn('route_id', $routes)
            ->whereDate('travel_date', $request->travel_date)
            ->bookableViaCounter() // Allow counter booking until departure time
            ->orderBy('departure_time')
            ->get();

        $searchParams = $request->only(['source_city_id', 'destination_city_id', 'travel_date']);

        return view('operator.counter.search-results', compact('schedules', 'searchParams'));
    }

    /**
     * Show booking form for selected schedule.
     * Allow viewing even after departure time, but prevent booking.
     */
    public function book(Schedule $schedule)
    {
        // Ensure operator can only book their own schedules
        if ($schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to schedule.');
        }

        // Allow viewing even if no seats available or schedule has departed
        // The view will handle showing appropriate messages

        $schedule->load(['route.sourceCity', 'route.destinationCity', 'bus.busType']);

        // Get seat map
        $seatMap = $this->generateSeatMapWithBookings($schedule);

        // Debug: Log the seat map structure for troubleshooting
        \Log::info('=== SEAT MAP DEBUG FOR SCHEDULE ' . $schedule->id . ' ===', [
            'seatMap_keys' => array_keys($seatMap),
            'has_seats' => isset($seatMap['seats']),
            'seat_count' => isset($seatMap['seats']) ? count($seatMap['seats']) : 0,
            'layout_type' => $seatMap['layout_type'] ?? 'N/A',
            'rows' => $seatMap['rows'] ?? 'N/A',
            'columns' => $seatMap['columns'] ?? 'N/A',
            'first_seat' => isset($seatMap['seats'][0]) ? $seatMap['seats'][0] : null,
            'last_seat' => isset($seatMap['seats']) && count($seatMap['seats']) > 0 ? $seatMap['seats'][count($seatMap['seats']) - 1] : null,
            'sample_seats' => isset($seatMap['seats']) ? array_slice($seatMap['seats'], 0, 5) : [],
            'bus_id' => $schedule->bus->id,
            'bus_total_seats' => $schedule->bus->total_seats,
            'bus_seat_layout_exists' => isset($schedule->bus->seat_layout),
            'bus_seat_layout_keys' => isset($schedule->bus->seat_layout) ? array_keys($schedule->bus->seat_layout) : [],
            'is_bookable' => $schedule->isBookableViaCounter(),
            'has_departed' => !$schedule->isBookableViaCounter(),
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

        // Check if schedule is still bookable via counter
        if (!$schedule->isBookableViaCounter()) {
            return back()->with('error', 'This schedule has already departed and is no longer available for booking.');
        }

        // Enhanced logging for debugging
        \Log::info('=== Counter booking request received ===', [
            'request_data' => $request->all(),
            'schedule_id' => $schedule->id,
            'operator_id' => Auth::id(),
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'has_seat_numbers' => $request->has('seat_numbers'),
            'seat_numbers_raw' => $request->input('seat_numbers'),
            'all_inputs' => $request->input(),
        ]);

        // Check if seat_numbers is present and log it
        if ($request->has('seat_numbers')) {
            \Log::info('Seat numbers received:', [
                'seat_numbers' => $request->seat_numbers,
                'seat_numbers_type' => gettype($request->seat_numbers),
                'seat_numbers_count' => is_array($request->seat_numbers) ? count($request->seat_numbers) : 0,
            ]);
        } else {
            \Log::error('No seat_numbers in request!', [
                'all_request_keys' => array_keys($request->all()),
                'request_input_method' => $request->input('seat_numbers'),
            ]);

            // Return with error immediately if no seat numbers
            return back()->withInput()
                ->with('error', 'No seats selected. Please select at least one seat before booking.');
        }

        try {
            $validatedData = $request->validate([
                'passenger_name' => 'required|string|max:255',
                'passenger_phone' => 'required|string|max:20',
                'passenger_email' => 'nullable|email|max:255',
                'passenger_age' => 'required|integer|min:1|max:120',
                'passenger_gender' => 'required|in:male,female,other',
                'seat_numbers' => 'required|array|min:1',
                'seat_numbers.*' => 'required|string|max:10', // Changed from integer to string for seat numbers like "A1", "B2"
                'contact_phone' => 'required|string|max:20',
                'contact_email' => 'nullable|email|max:255',
                'special_requests' => 'nullable|string|max:500',
                'payment_method' => 'required|in:cash,card,digital',
            ]);

            \Log::info('Validation passed successfully', ['validated_data' => $validatedData]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);
            throw $e;
        }

        // Validate seat availability
        $requestedSeats = array_map('strval', $request->seat_numbers); // Ensure all seat numbers are strings
        $bookedSeats = $schedule->bookings()
            ->where('status', '!=', 'cancelled')
            ->pluck('seat_numbers')
            ->flatten()
            ->map(function($seat) {
                return is_string($seat) ? $seat : (string)$seat;
            })
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
        // Get booked seats for this schedule
        $bookedSeats = $schedule->bookings()
            ->where('status', '!=', 'cancelled')
            ->pluck('seat_numbers')
            ->flatten()
            ->map(function($seat) {
                return is_string($seat) ? $seat : (string)$seat;
            })
            ->toArray();

        // Try to get existing seat layout from bus
        $seatLayout = $schedule->bus->seat_layout;

        // If no seat layout exists, generate one using the same service as bus creation
        if (!isset($seatLayout['seats']) || empty($seatLayout['seats'])) {
            \Log::info('=== GENERATING SEAT LAYOUT FOR SCHEDULE ' . $schedule->id . ' ===');
            \Log::info('Bus details:', [
                'bus_id' => $schedule->bus->id,
                'total_seats' => $schedule->bus->total_seats,
                'existing_layout' => $schedule->bus->seat_layout,
            ]);

            $seatLayoutService = new \App\Services\SeatLayoutService();
            $totalSeats = $schedule->bus->total_seats;

            // Determine layout type based on total seats (same logic as bus creation)
            $layoutType = '2x2'; // Default
            if ($totalSeats <= 20) {
                $layoutType = '2x1';
            } elseif ($totalSeats >= 35) {
                $layoutType = '3x2';
            }

            // Always use back row for consistency
            $hasBackRow = true;

            // Generate layout using the same service as bus creation
            $seatLayout = $seatLayoutService->generateSeatLayout($totalSeats, $layoutType, $hasBackRow);

            \Log::info('=== GENERATED LAYOUT RESULT ===', [
                'total_seats' => $totalSeats,
                'layout_type' => $layoutType,
                'has_back_row' => $hasBackRow,
                'generated_seats' => count($seatLayout['seats'] ?? []),
                'layout_keys' => array_keys($seatLayout),
                'first_generated_seat' => $seatLayout['seats'][0] ?? null,
                'last_generated_seat' => isset($seatLayout['seats']) && count($seatLayout['seats']) > 0 ? $seatLayout['seats'][count($seatLayout['seats']) - 1] : null,
            ]);
        } else {
            \Log::info('Using existing seat layout for schedule ' . $schedule->id, [
                'existing_seat_count' => count($seatLayout['seats']),
                'layout_keys' => array_keys($seatLayout),
            ]);
        }

        // Update booking status for each seat in existing layout
        if (isset($seatLayout['seats'])) {
            $columns = $seatLayout['columns'] ?? 4;

            foreach ($seatLayout['seats'] as $index => &$seat) {
                $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? null;
                if ($seatNumber) {
                    $seatNumberStr = (string)$seatNumber;
                    $isBooked = in_array($seatNumberStr, $bookedSeats);

                    $seat['is_booked'] = $isBooked;
                    $seat['is_available'] = !$isBooked;
                    $seat['number'] = $seatNumberStr; // Normalize the key
                    $seat['seat_number'] = $seatNumberStr; // Keep both for compatibility

                    // Set status for frontend
                    $seat['status'] = $isBooked ? 'booked' : 'available';

                    // Ensure row and column are set
                    if (!isset($seat['row']) || !isset($seat['column'])) {
                        // Calculate row and column based on index if missing
                        $seat['row'] = floor($index / $columns) + 1;
                        $seat['column'] = ($index % $columns) + 1;
                    }

                    // Ensure is_window is set
                    if (!isset($seat['is_window'])) {
                        $seat['is_window'] = ($seat['column'] === 1 || $seat['column'] === $columns);
                    }
                }
            }

            \Log::info('Updated existing seat layout for schedule ' . $schedule->id, [
                'seat_count' => count($seatLayout['seats']),
                'booked_seats' => count($bookedSeats),
                'first_seat' => $seatLayout['seats'][0] ?? null
            ]);
        }

        // Ensure all required fields are present
        $seatLayout['layout_type'] = $seatLayout['layout_type'] ?? '2x2';
        $seatLayout['rows'] = $seatLayout['rows'] ?? ceil(count($seatLayout['seats'] ?? []) / 4);
        $seatLayout['columns'] = $seatLayout['columns'] ?? 4;
        $seatLayout['aisle_position'] = $seatLayout['aisle_position'] ?? 2;
        $seatLayout['has_back_row'] = $seatLayout['has_back_row'] ?? false;

        // Return the exact same structure as bus details page
        return $seatLayout;
    }
}
