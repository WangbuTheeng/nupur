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
     * Handle eSewa payment success callback (v2 API)
     */
    public function esewaSuccess(Request $request)
    {
        try {
            $paymentId = $request->get('payment_id');
            $encodedData = $request->get('data');

            Log::info('eSewa Success Callback', [
                'payment_id' => $paymentId,
                'encoded_data' => $encodedData,
                'all_request_data' => $request->all()
            ]);

            // For now, just show a simple success page
            return view('payment.simple-success', [
                'message' => 'Payment completed successfully!',
                'payment_id' => $paymentId,
                'encoded_data' => $encodedData
            ]);

        } catch (\Exception $e) {
            Log::error('eSewa payment success handling error', [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('payment.simple-failure', [
                'error_message' => 'Payment processing failed. Please contact support.'
            ]);
        }
    }

    /**
     * Handle eSewa payment failure callback
     */
    public function esewaFailure(Request $request)
    {
        try {
            $paymentId = $request->get('payment_id');

            Log::info('eSewa Failure Callback', [
                'payment_id' => $paymentId,
                'all_request_data' => $request->all()
            ]);

            return view('payment.simple-failure', [
                'payment_id' => $paymentId,
                'error_message' => 'Payment was cancelled or failed. You can try again.'
            ]);

        } catch (\Exception $e) {
            Log::error('eSewa payment failure handling error', [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('payment.simple-failure', [
                'error_message' => 'Payment processing failed. Please contact support.'
            ]);
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

    /**
     * Check eSewa payment status using status check API
     */
    public function checkEsewaStatus(Payment $payment)
    {
        try {
            // Ensure user owns the payment
            if ($payment->user_id !== Auth::id()) {
                abort(403, 'Unauthorized access to payment');
            }

            if ($payment->payment_method !== 'esewa') {
                return response()->json([
                    'success' => false,
                    'message' => 'This is not an eSewa payment'
                ], 400);
            }

            $result = $this->esewaService->checkPaymentStatus(
                $payment->transaction_id,
                $payment->amount
            );

            // If status check shows payment is complete but our record shows pending,
            // update the payment status
            if ($result['success'] && $result['status'] === 'COMPLETE' && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $result['data']['ref_id'] ?? null,
                    'gateway_response' => $result['data'],
                    'paid_at' => now(),
                ]);

                // Update booking status
                $booking = $payment->booking;
                $booking->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                ]);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('eSewa status check error', [
                'payment_id' => $payment->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check eSewa payment status'
            ], 500);
        }
    }
}
