<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of bookings for the operator.
     */
    public function index(Request $request)
    {
        $operator = Auth::user();
        
        $query = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->with(['user', 'schedule.route', 'schedule.bus', 'payments']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->whereDate('travel_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->whereDate('travel_date', '<=', $request->date_to);
            });
        }

        if ($request->filled('route')) {
            $query->whereHas('schedule.route', function($q) use ($request) {
                $q->where('id', $request->route);
            });
        }

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

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $routes = $operator->routes()->where('is_active', true)->get();
        $statuses = ['pending', 'confirmed', 'cancelled', 'completed'];

        // Calculate statistics
        $stats = [
            'total' => Booking::whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->count(),

            'confirmed' => Booking::whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('status', 'confirmed')->count(),

            'pending' => Booking::whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('status', 'pending')->count(),

            'today_revenue' => Booking::whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('status', 'confirmed')
              ->whereDate('created_at', Carbon::today())
              ->sum('total_amount'),
        ];

        return view('operator.bookings.index', compact('bookings', 'routes', 'statuses', 'stats'));
    }

    /**
     * Display the specified booking.
     */
    public function show(Booking $booking)
    {
        // Ensure operator owns this booking
        if ($booking->schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        $booking->load(['user', 'schedule.route', 'schedule.bus', 'payments']);

        return view('operator.bookings.show', compact('booking'));
    }

    /**
     * Update booking status.
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        // Ensure operator owns this booking
        if ($booking->schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'reason' => 'nullable|string|max:500',
        ]);

        $oldStatus = $booking->status;
        $newStatus = $request->status;

        // Update booking status
        $booking->update([
            'status' => $newStatus,
            'status_updated_by' => Auth::id(),
            'status_updated_at' => now(),
            'cancellation_reason' => $request->reason,
        ]);

        // Handle status-specific logic
        switch ($newStatus) {
            case 'confirmed':
                $this->handleBookingConfirmation($booking);
                break;
            case 'cancelled':
                $this->handleBookingCancellation($booking, $request->reason);
                break;
            case 'completed':
                $this->handleBookingCompletion($booking);
                break;
        }

        // Send notification to customer
        $this->notificationService->sendNotification(
            $booking->user,
            'booking_status_updated',
            'Booking Status Updated',
            "Your booking {$booking->booking_reference} status has been updated to {$newStatus}.",
            [
                'booking_id' => $booking->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'reason' => $request->reason,
            ],
            route('customer.bookings.show', $booking),
            'View Booking',
            'medium',
            ['database', 'realtime', 'email']
        );

        return redirect()->back()->with('success', 'Booking status updated successfully.');
    }

    /**
     * Handle booking confirmation.
     */
    private function handleBookingConfirmation(Booking $booking)
    {
        // Update payment status if needed
        if ($booking->payment_status === 'pending') {
            $booking->update(['payment_status' => 'paid']);
        }

        // Send confirmation notification
        $this->notificationService->sendBookingConfirmation($booking);
    }

    /**
     * Handle booking cancellation.
     */
    private function handleBookingCancellation(Booking $booking, $reason = null)
    {
        // Release seats back to schedule
        $schedule = $booking->schedule;
        $schedule->increment('available_seats', $booking->passenger_count);

        // Process refund if payment was made
        if ($booking->payment_status === 'paid') {
            $this->processRefund($booking, $reason);
        }

        // Update booking
        $booking->update([
            'payment_status' => 'refunded',
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
            'cancelled_by' => Auth::id(),
        ]);
    }

    /**
     * Handle booking completion.
     */
    private function handleBookingCompletion(Booking $booking)
    {
        $booking->update([
            'completed_at' => now(),
        ]);

        // Send completion notification
        $this->notificationService->sendNotification(
            $booking->user,
            'trip_completed',
            'Trip Completed',
            "Your trip {$booking->booking_reference} has been completed successfully.",
            ['booking_id' => $booking->id],
            route('customer.bookings.show', $booking),
            'Rate Trip',
            'low',
            ['database', 'realtime']
        );
    }

    /**
     * Process refund for cancelled booking.
     */
    private function processRefund(Booking $booking, $reason = null)
    {
        // Create refund record
        $payment = $booking->payments()->where('status', 'completed')->first();
        
        if ($payment) {
            // For now, just log the refund request
            // In production, integrate with payment gateway refund API
            \Log::info('Refund requested', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'reason' => $reason,
                'requested_by' => Auth::id(),
            ]);

            // Send refund notification
            $this->notificationService->sendNotification(
                $booking->user,
                'refund_processed',
                'Refund Processed',
                "A refund of Rs. {$payment->amount} has been processed for booking {$booking->booking_reference}.",
                [
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    'amount' => $payment->amount,
                ],
                route('customer.bookings.show', $booking),
                'View Booking',
                'medium',
                ['database', 'realtime', 'email']
            );
        }
    }

    /**
     * Export bookings to CSV.
     */
    public function export(Request $request)
    {
        $operator = Auth::user();
        
        $query = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->with(['user', 'schedule.route', 'schedule.bus']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->whereDate('travel_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->whereDate('travel_date', '<=', $request->date_to);
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $filename = 'bookings_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Booking Reference',
                'Customer Name',
                'Customer Email',
                'Route',
                'Travel Date',
                'Departure Time',
                'Bus',
                'Seats',
                'Passengers',
                'Total Amount',
                'Status',
                'Payment Status',
                'Booking Date',
            ]);

            // CSV data
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_reference,
                    $booking->user->name,
                    $booking->user->email,
                    $booking->schedule->route->full_name,
                    $booking->schedule->travel_date->format('Y-m-d'),
                    $booking->schedule->departure_time->format('H:i'),
                    $booking->schedule->bus->display_name,
                    implode(', ', $booking->seat_numbers),
                    $booking->passenger_count,
                    $booking->total_amount,
                    $booking->status,
                    $booking->payment_status,
                    $booking->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show today's bookings.
     */
    public function today()
    {
        $operator = Auth::user();
        $today = Carbon::today();

        // Get today's schedules with bookings
        $schedules = $operator->schedules()
            ->with(['route.sourceCity', 'route.destinationCity', 'bus', 'bookings.user'])
            ->whereDate('travel_date', $today)
            ->orderBy('departure_time')
            ->get();

        // Calculate statistics
        $stats = [
            'total_bookings' => $schedules->sum(function($schedule) {
                return $schedule->bookings->count();
            }),
            'confirmed_bookings' => $schedules->sum(function($schedule) {
                return $schedule->bookings->where('status', 'confirmed')->count();
            }),
            'pending_bookings' => $schedules->sum(function($schedule) {
                return $schedule->bookings->where('status', 'pending')->count();
            }),
            'total_revenue' => $schedules->sum(function($schedule) {
                return $schedule->bookings->where('status', 'confirmed')->sum('total_amount');
            }),
        ];

        return view('operator.bookings.today', compact('schedules', 'stats'));
    }

    /**
     * Show upcoming bookings.
     */
    public function upcoming(Request $request)
    {
        $operator = Auth::user();

        $query = Booking::whereHas('schedule', function($q) use ($operator) {
            $q->where('operator_id', $operator->id)
              ->where('travel_date', '>', Carbon::today());
        })->with(['user', 'schedule.route.sourceCity', 'schedule.route.destinationCity', 'schedule.bus']);

        // Apply filters
        if ($request->filled('route')) {
            $query->whereHas('schedule.route', function($q) use ($request) {
                $q->where('id', $request->route);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->whereDate('travel_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->whereDate('travel_date', '<=', $request->date_to);
            });
        }

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

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $routes = $operator->routes()->where('is_active', true)->get();

        return view('operator.bookings.upcoming', compact('bookings', 'routes'));
    }

    /**
     * Confirm a booking.
     */
    public function confirm(Booking $booking)
    {
        // Ensure operator owns this booking
        if ($booking->schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        if ($booking->status === 'confirmed') {
            return back()->with('error', 'Booking is already confirmed.');
        }

        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);

        return back()->with('success', 'Booking confirmed successfully.');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking)
    {
        // Ensure operator owns this booking
        if ($booking->schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Booking is already cancelled.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500'
        ]);

        DB::beginTransaction();
        try {
            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $request->reason
            ]);

            // Restore available seats
            $booking->schedule->increment('available_seats', $booking->passenger_count);

            DB::commit();

            return back()->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to cancel booking. Please try again.');
        }
    }

    /**
     * Generate and display compact ticket.
     */
    public function ticket(Booking $booking)
    {
        // Ensure operator owns this booking
        if ($booking->schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Ticket can only be generated for confirmed bookings.');
        }

        $booking->load(['user', 'schedule.route.sourceCity', 'schedule.route.destinationCity', 'schedule.bus.busType', 'schedule.operator']);

        return view('operator.bookings.compact-ticket', compact('booking'));
    }

    /**
     * Download compact ticket as PDF.
     */
    public function downloadCompactTicket(Booking $booking)
    {
        // Ensure operator owns this booking
        if ($booking->schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Ticket can only be downloaded for confirmed bookings.');
        }

        $booking->load([
            'user',
            'schedule.route.sourceCity',
            'schedule.route.destinationCity',
            'schedule.bus.busType',
            'schedule.operator'
        ]);

        // Generate Compact PDF
        $pdf = Pdf::loadView('tickets.compact-pdf', compact('booking'));
        $pdf->setPaper([0, 0, 288, 432], 'portrait'); // 4x6 inches in points for compact size

        $filename = 'BookNGO-Operator-Compact-Ticket-' . $booking->booking_reference . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Get booking statistics for dashboard.
     */
    public function statistics()
    {
        $operator = Auth::user();
        $today = Carbon::today();

        $stats = [
            'today_bookings' => Booking::whereHas('schedule', function($q) use ($operator, $today) {
                $q->where('operator_id', $operator->id)
                  ->whereDate('travel_date', $today);
            })->count(),

            'pending_bookings' => Booking::whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('status', 'pending')->count(),

            'confirmed_bookings' => Booking::whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('status', 'confirmed')->count(),

            'cancelled_bookings' => Booking::whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('status', 'cancelled')->count(),

            'total_revenue' => Booking::whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('status', 'confirmed')->sum('total_amount'),

            'monthly_revenue' => Booking::whereHas('schedule', function($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->where('status', 'confirmed')
              ->whereMonth('created_at', $today->month)
              ->whereYear('created_at', $today->year)
              ->sum('total_amount'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }
}
