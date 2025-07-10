<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Show user's bookings.
     */
    public function index()
    {
        $user = Auth::user();
        $bookings = $user->bookings()
            ->with(['schedule.route', 'schedule.bus', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    /**
     * Show booking form for a schedule.
     */
    public function create(Schedule $schedule)
    {
        $schedule->load(['bus.busType', 'bus.seats', 'route']);
        
        if (!$schedule->isBookable()) {
            return redirect()->route('search')->with('error', 'This schedule is not available for booking.');
        }

        // Get available seats
        $availableSeats = $schedule->bus->seats()
            ->where('is_available', true)
            ->get()
            ->filter(function ($seat) use ($schedule) {
                return $seat->isBookableForSchedule($schedule->id);
            });

        return view('bookings.create', compact('schedule', 'availableSeats'));
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request, Schedule $schedule)
    {
        $request->validate([
            'seat_numbers' => 'required|array|min:1',
            'seat_numbers.*' => 'string',
            'passenger_details' => 'required|array|min:1',
            'passenger_details.*.name' => 'required|string|max:255',
            'passenger_details.*.age' => 'required|integer|min:1|max:120',
            'passenger_details.*.gender' => 'required|in:male,female,other',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'special_requests' => 'nullable|string|max:500',
        ]);

        if (!$schedule->isBookable()) {
            return back()->with('error', 'This schedule is not available for booking.');
        }

        // Validate seat availability
        $seatNumbers = $request->seat_numbers;
        $passengerCount = count($request->passenger_details);

        if (count($seatNumbers) !== $passengerCount) {
            return back()->with('error', 'Number of seats must match number of passengers.');
        }

        // Check if seats are still available
        foreach ($seatNumbers as $seatNumber) {
            $existingBooking = Booking::where('schedule_id', $schedule->id)
                ->whereJsonContains('seat_numbers', $seatNumber)
                ->whereIn('status', ['confirmed', 'pending'])
                ->exists();

            if ($existingBooking) {
                return back()->with('error', "Seat {$seatNumber} is no longer available.");
            }
        }

        DB::beginTransaction();
        try {
            // Calculate total amount
            $totalAmount = $schedule->fare * $passengerCount;

            // Create booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'schedule_id' => $schedule->id,
                'seat_numbers' => $seatNumbers,
                'passenger_count' => $passengerCount,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'passenger_details' => $request->passenger_details,
                'contact_phone' => $request->contact_phone,
                'contact_email' => $request->contact_email,
                'booking_expires_at' => Carbon::now()->addMinutes(15), // 15 minutes to complete payment
                'special_requests' => $request->special_requests,
            ]);

            // Update available seats count
            $schedule->decrement('available_seats', $passengerCount);

            // Create pending payment record
            Payment::create([
                'booking_id' => $booking->id,
                'payment_method' => 'khalti',
                'amount' => $totalAmount,
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('bookings.payment', $booking)
                ->with('success', 'Booking created successfully! Please complete payment within 15 minutes.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create booking. Please try again.');
        }
    }

    /**
     * Show booking details.
     */
    public function show(Booking $booking)
    {
        // Check if user owns this booking or is admin
        if ($booking->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $booking->load(['schedule.route', 'schedule.bus', 'payments', 'user']);
        return view('bookings.show', compact('booking'));
    }

    /**
     * Show payment page for booking.
     */
    public function payment(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('bookings.show', $booking)
                ->with('info', 'This booking has already been processed.');
        }

        if ($booking->isExpired()) {
            $booking->update(['status' => 'cancelled']);
            return redirect()->route('bookings.index')
                ->with('error', 'Booking has expired. Please create a new booking.');
        }

        $booking->load(['schedule.route', 'schedule.bus']);
        return view('bookings.payment', compact('booking'));
    }

    /**
     * Process demo payment (for testing).
     */
    public function demoPayment(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('bookings.show', $booking)
                ->with('info', 'This booking has already been processed.');
        }

        if ($booking->isExpired()) {
            $booking->update(['status' => 'cancelled']);
            return redirect()->route('bookings.index')
                ->with('error', 'Booking has expired. Please create a new booking.');
        }

        DB::beginTransaction();
        try {
            // Find the pending payment
            $payment = $booking->payments()->where('status', 'pending')->first();

            if ($payment) {
                // Mark payment as completed
                $payment->markAsCompleted('DEMO_' . time(), ['demo' => true, 'processed_at' => now()]);
            }

            DB::commit();

            return redirect()->route('bookings.show', $booking)
                ->with('success', 'Payment completed successfully! Your booking is confirmed.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Booking $booking)
    {
        // Check if user owns this booking
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status === 'confirmed') {
            // Check if cancellation is allowed (e.g., at least 2 hours before departure)
            $departureTime = $booking->schedule->departure_datetime;
            if (Carbon::now()->diffInHours($departureTime) < 2) {
                return back()->with('error', 'Cancellation is not allowed within 2 hours of departure.');
            }
        }

        DB::beginTransaction();
        try {
            $booking->update(['status' => 'cancelled']);

            // Restore available seats
            $booking->schedule->increment('available_seats', $booking->passenger_count);

            DB::commit();

            return redirect()->route('bookings.index')
                ->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to cancel booking. Please try again.');
        }
    }
}
