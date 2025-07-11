<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\EsewaPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $esewaService;

    public function __construct(EsewaPaymentService $esewaService)
    {
        $this->esewaService = $esewaService;
    }

    /**
     * Show payment options for a booking
     */
    public function showPaymentOptions(Booking $booking)
    {
        // Ensure user owns the booking
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking');
        }

        // Check if booking is already paid
        if ($booking->payment_status === 'paid') {
            return redirect()->route('booking.show', $booking)
                ->with('info', 'This booking has already been paid.');
        }

        return view('payments.options', compact('booking'));
    }

    /**
     * Initiate eSewa payment
     */
    public function initiateEsewaPayment(Request $request, Booking $booking)
    {
        try {
            // Ensure user owns the booking
            if ($booking->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to booking');
            }

            // Check if booking is already paid
            if ($booking->payment_status === 'paid') {
                return redirect()->route('booking.show', $booking)
                    ->with('info', 'This booking has already been paid.');
            }

            // Initiate payment with eSewa
            $result = $this->esewaService->initiatePayment($booking);

            if ($result['success']) {
                return view('payments.esewa-redirect', [
                    'booking' => $booking,
                    'payment' => $result['payment'],
                    'form_html' => $result['form_html']
                ]);
            } else {
                return back()->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('eSewa payment initiation error', [
                'booking_id' => $booking->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Payment initiation failed. Please try again.');
        }
    }

    /**
     * Handle eSewa payment success callback
     */
    public function esewaSuccess(Request $request)
    {
        try {
            $paymentId = $request->get('payment_id');
            $refId = $request->get('refId');
            $oid = $request->get('oid');
            $amt = $request->get('amt');

            if (!$paymentId || !$refId || !$oid || !$amt) {
                return redirect()->route('dashboard')
                    ->with('error', 'Invalid payment response from eSewa.');
            }

            // Verify payment with eSewa
            $result = $this->esewaService->verifyPayment($paymentId, $refId, $oid, $amt);

            if ($result['success']) {
                $payment = $result['payment'];
                $booking = $payment->booking;

                return redirect()->route('booking.show', $booking)
                    ->with('success', 'Payment completed successfully! Your booking is confirmed.');
            } else {
                return redirect()->route('dashboard')
                    ->with('error', 'Payment verification failed: ' . $result['message']);
            }

        } catch (\Exception $e) {
            Log::error('eSewa payment success handling error', [
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Payment processing failed. Please contact support.');
        }
    }

    /**
     * Handle eSewa payment failure callback
     */
    public function esewaFailure(Request $request)
    {
        try {
            $paymentId = $request->get('payment_id');

            if ($paymentId) {
                $payment = Payment::find($paymentId);
                if ($payment) {
                    $payment->update([
                        'status' => 'failed',
                        'failure_reason' => 'Payment cancelled by user',
                        'failed_at' => now()
                    ]);

                    $booking = $payment->booking;
                    return redirect()->route('booking.show', $booking)
                        ->with('error', 'Payment was cancelled. You can try again.');
                }
            }

            return redirect()->route('dashboard')
                ->with('error', 'Payment was cancelled or failed.');

        } catch (\Exception $e) {
            Log::error('eSewa payment failure handling error', [
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Payment processing failed.');
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Payment $payment)
    {
        // Ensure user owns the payment
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to payment');
        }

        return response()->json([
            'success' => true,
            'payment' => $payment,
            'booking' => $payment->booking
        ]);
    }

    /**
     * Show payment history
     */
    public function paymentHistory()
    {
        $payments = Payment::where('user_id', Auth::id())
            ->with('booking.schedule.route')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payments.history', compact('payments'));
    }
}
