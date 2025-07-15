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
    public function esewaSuccess(Request $request, $payment = null)
    {
        try {
            // Try to get data from query parameter first
            $encodedData = $request->get('data');

            // If not found in query, check if it's in the URL path (common eSewa callback issue)
            if (!$encodedData) {
                $fullUrl = $request->fullUrl();
                if (preg_match('/data=([^&]+)/', $fullUrl, $matches)) {
                    $encodedData = $matches[1];
                }
            }

            // Also check if payment_id contains the data (another common issue)
            if (!$encodedData) {
                $paymentId = $request->get('payment_id');
                if ($paymentId && strpos($paymentId, '?data=') !== false) {
                    $parts = explode('?data=', $paymentId);
                    if (count($parts) > 1) {
                        $encodedData = $parts[1];
                    }
                }
            }

            Log::info('eSewa Success Callback', [
                'encoded_data' => $encodedData,
                'all_request_data' => $request->all(),
                'full_url' => $request->fullUrl()
            ]);

            if (!$encodedData) {
                throw new \Exception('No payment data received from eSewa');
            }

            // Decode Base64 response
            $decodedData = base64_decode($encodedData);
            $responseData = json_decode($decodedData, true);

            if (!$responseData) {
                throw new \Exception('Invalid response data from eSewa');
            }

            Log::info('eSewa Response Data', [
                'decoded_data' => $responseData
            ]);

            // Find payment by transaction UUID
            $transactionUuid = $responseData['transaction_uuid'] ?? null;
            if (!$transactionUuid) {
                throw new \Exception('Transaction UUID not found in response');
            }

            $payment = Payment::where('transaction_id', $transactionUuid)->first();
            if (!$payment) {
                throw new \Exception('Payment record not found for transaction: ' . $transactionUuid);
            }

            // Verify payment with eSewa service
            $verificationResult = $this->esewaService->verifyPayment($payment->id, $encodedData);

            if ($verificationResult['success']) {
                $booking = $payment->booking;

                // Redirect to ticket page for successful payment
                return redirect()->route('customer.tickets.show', $booking)
                    ->with('success', 'Payment completed successfully! Your ticket is ready.');
            } else {
                // Payment verification failed
                return view('payment.simple-failure', [
                    'error_message' => $verificationResult['message'] ?? 'Payment verification failed.'
                ]);
            }

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
    public function esewaFailure(Request $request, $payment = null)
    {
        try {
            $paymentId = $payment ?: $request->get('payment_id');

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
