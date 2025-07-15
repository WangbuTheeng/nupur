<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Booking;
use App\Models\SeatReservation;
use App\Events\SeatUpdated;
use App\Events\BookingStatusUpdated;
use App\Services\SeatReservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $reservationService;

    public function __construct(SeatReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }
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
        // Check if schedule has finished
        if ($schedule->hasFinished()) {
            return back()->with('error', 'This schedule has already departed and is no longer available for booking.');
        }

        // Check if schedule is bookable online
        if (!$schedule->isBookableOnline()) {
            return back()->with('error', 'Online booking is no longer available for this schedule. Please contact the operator for counter booking.');
        }

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

        // Check if schedule is still bookable online
        if (!$schedule->isBookableOnline()) {
            $message = $schedule->hasFinished()
                ? 'This schedule has already departed and is no longer available for booking.'
                : 'Online booking for this schedule has closed. Please visit the counter for booking.';

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

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

        // Reserve seats using the new service (1 hour)
        $result = $this->reservationService->reserveSeats(Auth::id(), $schedule->id, $requestedSeats, 60);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'reservation_expires_at' => $result['expires_at'],
                'redirect_url' => route('booking.passenger-details', $schedule),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }
    }

    /**
     * Reserve selected seats only (without proceeding to booking).
     */
    public function reserveSeatsOnly(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'seat_numbers' => 'required|array|min:1|max:10',
            'seat_numbers.*' => 'integer|min:1',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        // Check if schedule is still bookable online
        if (!$schedule->isBookableOnline()) {
            $message = $schedule->hasFinished()
                ? 'This schedule has already departed and is no longer available for booking.'
                : 'Online booking for this schedule has closed. Please visit the counter for booking.';

            return response()->json([
                'success' => false,
                'message' => $message,
            ], 422);
        }

        $requestedSeats = $request->seat_numbers;

        // Check if seats are available
        $unavailableSeats = $this->reservationService->getUnavailableSeats($schedule->id, $requestedSeats);
        if (!empty($unavailableSeats)) {
            return response()->json([
                'success' => false,
                'message' => 'Some seats are no longer available: ' . implode(', ', $unavailableSeats),
            ], 422);
        }

        // Reserve seats using the new service (1 hour)
        $result = $this->reservationService->reserveSeats(Auth::id(), $schedule->id, $requestedSeats, 60);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Seats reserved successfully for 1 hour.',
                'reservation_expires_at' => $result['expires_at'],
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 422);
        }
    }

    /**
     * Show passenger details form.
     */
    public function passengerDetails(Schedule $schedule)
    {
        // Check if user has reserved seats using new system
        $reservation = SeatReservation::where('user_id', Auth::id())
                                    ->where('schedule_id', $schedule->id)
                                    ->active()
                                    ->first();

        if (!$reservation) {
            return redirect()->route('booking.seat-selection', $schedule)
                ->with('error', 'Seat reservation expired. Please select seats again.');
        }

        $schedule->load(['route.sourceCity', 'route.destinationCity', 'bus.busType', 'operator']);
        $seatNumbers = $reservation->seat_numbers;

        return view('customer.booking.passenger-details', compact('schedule', 'seatNumbers', 'reservation'));
    }

    /**
     * Store passenger details and create booking.
     */
    public function storeDetails(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'primary_passenger_name' => 'required|string|max:255',
            'primary_passenger_age' => 'required|integer|min:1|max:120',
            'primary_passenger_gender' => 'required|in:male,female,other',
            'primary_passenger_phone' => 'nullable|string|max:20',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'special_requests' => 'nullable|string|max:500',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        // Check if schedule has finished
        if ($schedule->hasFinished()) {
            return back()->with('error', 'This schedule has already departed and is no longer available for booking.');
        }

        // Check if schedule is still bookable online
        if (!$schedule->isBookableOnline()) {
            return back()->with('error', 'Online booking is no longer available for this schedule.');
        }

        // Check seat reservation using new system
        $reservation = SeatReservation::where('user_id', Auth::id())
                                    ->where('schedule_id', $schedule->id)
                                    ->active()
                                    ->first();

        if (!$reservation) {
            return back()->with('error', 'Seat reservation expired. Please start booking again.');
        }

        DB::beginTransaction();
        try {
            // Calculate total amount
            $seatNumbers = $reservation->seat_numbers;
            $passengerCount = count($seatNumbers);

            if ($passengerCount === 0) {
                return back()->withInput()
                    ->with('error', 'No seats found in reservation. Please select seats again.');
            }

            $farePerSeat = $schedule->fare;
            $totalAmount = $farePerSeat * $passengerCount;

            // Create passenger details array using primary passenger for all seats
            $primaryPassenger = [
                'name' => $request->primary_passenger_name,
                'age' => $request->primary_passenger_age,
                'gender' => $request->primary_passenger_gender,
                'phone' => $request->primary_passenger_phone,
            ];

            // Create passenger details array - same primary passenger for all seats
            $passengerDetails = [];
            for ($i = 0; $i < $passengerCount; $i++) {
                $passengerDetails[] = $primaryPassenger;
            }

            // Create booking
            $booking = Booking::create([
                'booking_reference' => 'BNG-' . strtoupper(uniqid()),
                'user_id' => Auth::id(),
                'schedule_id' => $schedule->id,
                'passenger_count' => $passengerCount,
                'seat_numbers' => $seatNumbers,
                'passenger_details' => $passengerDetails,
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

            // Fire seat update events for each booked seat
            foreach ($seatNumbers as $seatNumber) {
                event(new SeatUpdated($schedule, $seatNumber, 'booked', Auth::id()));
            }

            // Fire booking status update event
            event(new BookingStatusUpdated($booking));

            // Convert reservation to booking
            $this->reservationService->convertToBooking(Auth::id(), $schedule->id);

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
     * Cancel a customer booking.
     */
    public function cancel(Request $request, Booking $booking)
    {
        // Add logging for debugging
        \Log::info('Cancel booking request received', [
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'booking_user_id' => $booking->user_id,
            'booking_status' => $booking->status
        ]);

        // Ensure user can only cancel their own bookings
        if ($booking->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to booking.',
            ], 403);
        }

        // Check if booking can be cancelled
        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending bookings can be cancelled.',
            ], 422);
        }

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            // Update booking status
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason ?? 'Customer requested cancellation'
            ]);

            // Restore available seats
            $booking->schedule->increment('available_seats', $booking->passenger_count);

            // Release any seat reservations for this user and schedule
            $this->reservationService->releaseSeats(Auth::id(), $booking->schedule_id);

            // Fire seat update events for each cancelled seat
            foreach ($booking->seat_numbers as $seatNumber) {
                event(new SeatUpdated($booking->schedule, $seatNumber, 'available', Auth::id()));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollback();

            \Log::error('Error cancelling booking', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel booking: ' . $e->getMessage(),
            ], 500);
        }
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

        if (isset($seatLayout['seats']) && is_array($seatLayout['seats'])) {
            foreach ($seatLayout['seats'] as &$seat) {
                // Handle both 'number' and 'seat_number' keys for backward compatibility
                $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? null;
                $seat['is_booked'] = $seatNumber ? in_array($seatNumber, $bookedSeats) : false;
                $seat['is_reserved'] = $seatNumber ? in_array($seatNumber, $reservedSeats) : false;
                $seat['is_available'] = !$seat['is_booked'] && !$seat['is_reserved'];
            }
        }

        return $seatLayout;
    }



}
