<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display customer's bookings.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->bookings()->with(['schedule.route', 'schedule.bus', 'schedule.operator']);

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

        $bookings = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistics
        $stats = [
            'total' => Auth::user()->bookings()->count(),
            'confirmed' => Auth::user()->bookings()->where('status', 'confirmed')->count(),
            'pending' => Auth::user()->bookings()->where('status', 'pending')->count(),
            'cancelled' => Auth::user()->bookings()->where('status', 'cancelled')->count(),
            'upcoming' => Auth::user()->bookings()
                ->whereHas('schedule', function($q) {
                    $q->where('travel_date', '>=', Carbon::today());
                })
                ->where('status', 'confirmed')
                ->count(),
        ];

        return view('customer.bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Show seat selection interface.
     */
    public function seatSelection(Schedule $schedule)
    {
        if ($schedule->available_seats <= 0) {
            return back()->with('error', 'No seats available for this schedule.');
        }

        $schedule->load(['route.sourceCity', 'route.destinationCity', 'bus.busType', 'operator']);

        // Generate seat map with current bookings
        $seatMap = $this->generateSeatMapWithBookings($schedule);

        return view('customer.booking.seat-selection', compact('schedule', 'seatMap'));
    }

    /**
     * Reserve selected seats temporarily.
     */
    public function reserveSeats(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seat_numbers' => 'required|array|min:1|max:10',
            'seat_numbers.*' => 'integer|min:1',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        // Check seat availability
        $requestedSeats = $request->seat_numbers;
        $bookedSeats = $schedule->bookings()
            ->where('status', '!=', 'cancelled')
            ->pluck('seat_numbers')
            ->flatten()
            ->toArray();

        $unavailableSeats = array_intersect($requestedSeats, $bookedSeats);
        if (!empty($unavailableSeats)) {
            return response()->json([
                'success' => false,
                'message' => 'Seats ' . implode(', ', $unavailableSeats) . ' are no longer available.',
            ], 422);
        }

        if (count($requestedSeats) > $schedule->available_seats) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough seats available.',
            ], 422);
        }

        // Reserve seats temporarily (15 minutes)
        $reservationKey = 'seat_reservation_' . $schedule->id . '_' . Auth::id();
        $reservationData = [
            'user_id' => Auth::id(),
            'schedule_id' => $schedule->id,
            'seat_numbers' => $requestedSeats,
            'reserved_at' => now(),
            'expires_at' => now()->addMinutes(15),
        ];

        Cache::put($reservationKey, $reservationData, 15 * 60); // 15 minutes

        return response()->json([
            'success' => true,
            'message' => 'Seats reserved successfully.',
            'reservation_expires_at' => $reservationData['expires_at'],
            'redirect_url' => route('booking.passenger-details', $schedule),
        ]);
    }

    /**
     * Show passenger details form.
     */
    public function passengerDetails(Schedule $schedule)
    {
        // Check if user has reserved seats
        $reservationKey = 'seat_reservation_' . $schedule->id . '_' . Auth::id();
        $reservation = Cache::get($reservationKey);

        if (!$reservation) {
            return redirect()->route('booking.seat-selection', $schedule)
                ->with('error', 'Seat reservation expired. Please select seats again.');
        }

        $schedule->load(['route.sourceCity', 'route.destinationCity', 'bus.busType', 'operator']);
        $seatNumbers = $reservation['seat_numbers'];

        return view('customer.booking.passenger-details', compact('schedule', 'seatNumbers', 'reservation'));
    }

    /**
     * Store passenger details and create booking.
     */
    public function storeDetails(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'passengers' => 'required|array|min:1',
            'passengers.*.name' => 'required|string|max:255',
            'passengers.*.age' => 'required|integer|min:1|max:120',
            'passengers.*.gender' => 'required|in:male,female,other',
            'passengers.*.phone' => 'nullable|string|max:20',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'special_requests' => 'nullable|string|max:500',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        // Check seat reservation
        $reservationKey = 'seat_reservation_' . $schedule->id . '_' . Auth::id();
        $reservation = Cache::get($reservationKey);

        if (!$reservation) {
            return back()->with('error', 'Seat reservation expired. Please start booking again.');
        }

        // Validate passenger count matches seat count
        if (count($request->passengers) !== count($reservation['seat_numbers'])) {
            return back()->withInput()
                ->with('error', 'Number of passengers must match number of selected seats.');
        }

        DB::beginTransaction();
        try {
            // Calculate total amount
            $passengerCount = count($request->passengers);
            $farePerSeat = $schedule->fare;
            $totalAmount = $farePerSeat * $passengerCount;

            // Create booking
            $booking = Booking::create([
                'booking_reference' => 'BNG-' . strtoupper(uniqid()),
                'user_id' => Auth::id(),
                'schedule_id' => $schedule->id,
                'passenger_count' => $passengerCount,
                'seat_numbers' => $reservation['seat_numbers'],
                'passenger_details' => $request->passengers,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'total_amount' => $totalAmount,
                'booking_type' => 'online',
                'payment_status' => 'pending',
                'status' => 'pending',
                'special_requests' => $request->special_requests,
            ]);

            // Update available seats
            $schedule->decrement('available_seats', $passengerCount);

            // Clear seat reservation
            Cache::forget($reservationKey);

            DB::commit();

            return redirect()->route('booking.review', $booking);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create booking: ' . $e->getMessage());
        }
    }

    /**
     * Show booking review before payment.
     */
    public function review(Booking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        $booking->load(['schedule.route.sourceCity', 'schedule.route.destinationCity', 'schedule.bus.busType', 'schedule.operator']);

        return view('customer.booking.review', compact('booking'));
    }

    /**
     * Confirm booking and redirect to payment.
     */
    public function confirm(Booking $booking)
    {
        // Ensure user can only confirm their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Booking cannot be confirmed.');
        }

        return redirect()->route('payment.index', $booking);
    }

    /**
     * Show booking success page.
     */
    public function success(Booking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        $booking->load(['schedule.route.sourceCity', 'schedule.route.destinationCity', 'schedule.bus', 'schedule.operator']);

        return view('customer.booking.success', compact('booking'));
    }

    /**
     * Show specific booking details.
     */
    public function show(Booking $booking)
    {
        // Ensure user can only view their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        $booking->load(['schedule.route.sourceCity', 'schedule.route.destinationCity', 'schedule.bus.busType', 'schedule.operator']);

        return view('customer.bookings.show', compact('booking'));
    }

    /**
     * Show upcoming bookings.
     */
    public function upcoming()
    {
        $bookings = Auth::user()->bookings()
            ->with(['schedule.route', 'schedule.bus', 'schedule.operator'])
            ->whereHas('schedule', function($q) {
                $q->where('travel_date', '>=', Carbon::today());
            })
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.bookings.upcoming', compact('bookings'));
    }

    /**
     * Show booking history.
     */
    public function history()
    {
        $bookings = Auth::user()->bookings()
            ->with(['schedule.route', 'schedule.bus', 'schedule.operator'])
            ->whereHas('schedule', function($q) {
                $q->where('travel_date', '<', Carbon::today());
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.bookings.history', compact('bookings'));
    }

    /**
     * Show cancelled bookings.
     */
    public function cancelled()
    {
        $bookings = Auth::user()->bookings()
            ->with(['schedule.route', 'schedule.bus', 'schedule.operator'])
            ->where('status', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer.bookings.cancelled', compact('bookings'));
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

        // Get temporarily reserved seats
        $reservedSeats = [];
        $reservationPattern = 'seat_reservation_' . $schedule->id . '_*';
        // Note: In production, you might want to use Redis for better pattern matching

        foreach ($seatLayout['seats'] as &$seat) {
            $seat['is_booked'] = in_array($seat['number'], $bookedSeats);
            $seat['is_reserved'] = in_array($seat['number'], $reservedSeats);
            $seat['is_available'] = !$seat['is_booked'] && !$seat['is_reserved'];
        }

        return $seatLayout;
    }
}
