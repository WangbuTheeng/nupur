<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Show payment options for booking.
     */
    public function index(Booking $booking)
    {
        // Ensure user can only pay for their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        if ($booking->payment_status === 'paid') {
            return redirect()->route('booking.success', $booking)
                ->with('info', 'This booking has already been paid.');
        }

        $booking->load(['schedule.route.sourceCity', 'schedule.route.destinationCity', 'schedule.bus', 'schedule.operator']);

        // Available payment methods
        $paymentMethods = [
            'esewa' => [
                'name' => 'eSewa',
                'logo' => '/images/payment/esewa.png',
                'description' => 'Pay securely with eSewa digital wallet',
                'enabled' => true,
            ],
            'khalti' => [
                'name' => 'Khalti',
                'logo' => '/images/payment/khalti.png',
                'description' => 'Pay with Khalti digital wallet',
                'enabled' => true,
            ],
            'ime_pay' => [
                'name' => 'IME Pay',
                'logo' => '/images/payment/ime-pay.png',
                'description' => 'Pay with IME Pay digital wallet',
                'enabled' => true,
            ],
            'bank_transfer' => [
                'name' => 'Bank Transfer',
                'logo' => '/images/payment/bank.png',
                'description' => 'Direct bank transfer',
                'enabled' => false, // Disabled for demo
            ],
        ];

        return view('customer.payment.index', compact('booking', 'paymentMethods'));
    }

    /**
     * Process payment request.
     */
    public function process(Request $request, Booking $booking)
    {
        // Ensure user can only pay for their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        $request->validate([
            'payment_method' => 'required|in:esewa,khalti,ime_pay,bank_transfer',
        ]);

        if ($booking->payment_status === 'paid') {
            return redirect()->route('booking.success', $booking)
                ->with('info', 'This booking has already been paid.');
        }

        $paymentMethod = $request->payment_method;

        try {
            switch ($paymentMethod) {
                case 'esewa':
                    return $this->processEsewaPayment($booking);
                case 'khalti':
                    return $this->processKhaltiPayment($booking);
                case 'ime_pay':
                    return $this->processImePayment($booking);
                case 'bank_transfer':
                    return $this->processBankTransfer($booking);
                default:
                    return back()->with('error', 'Invalid payment method selected.');
            }
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage(), [
                'booking_id' => $booking->id,
                'payment_method' => $paymentMethod,
                'user_id' => Auth::id(),
            ]);

            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * Process eSewa payment.
     */
    private function processEsewaPayment(Booking $booking)
    {
        // For demo purposes, we'll simulate the eSewa payment process
        // In production, you would integrate with actual eSewa API

        $esewaConfig = [
            'merchant_code' => config('payment.esewa.merchant_code', 'EPAYTEST'),
            'success_url' => route('payment.success', $booking),
            'failure_url' => route('payment.failed', $booking),
        ];

        // Update booking with payment method
        $booking->update([
            'payment_method' => 'esewa',
            'payment_gateway_reference' => 'ESW-' . time() . '-' . $booking->id,
        ]);

        // For demo, redirect directly to success
        // In production, redirect to eSewa payment gateway
        return $this->simulatePaymentSuccess($booking, 'esewa');
    }

    /**
     * Process Khalti payment.
     */
    private function processKhaltiPayment(Booking $booking)
    {
        // For demo purposes, we'll simulate the Khalti payment process
        // In production, you would integrate with actual Khalti API

        $khaltiConfig = [
            'public_key' => config('payment.khalti.public_key'),
            'secret_key' => config('payment.khalti.secret_key'),
        ];

        // Update booking with payment method
        $booking->update([
            'payment_method' => 'khalti',
            'payment_gateway_reference' => 'KHL-' . time() . '-' . $booking->id,
        ]);

        // For demo, redirect directly to success
        return $this->simulatePaymentSuccess($booking, 'khalti');
    }

    /**
     * Process IME Pay payment.
     */
    private function processImePayment(Booking $booking)
    {
        // For demo purposes, we'll simulate the IME Pay payment process
        // In production, you would integrate with actual IME Pay API

        // Update booking with payment method
        $booking->update([
            'payment_method' => 'ime_pay',
            'payment_gateway_reference' => 'IME-' . time() . '-' . $booking->id,
        ]);

        // For demo, redirect directly to success
        return $this->simulatePaymentSuccess($booking, 'ime_pay');
    }

    /**
     * Simulate payment success for demo.
     */
    private function simulatePaymentSuccess(Booking $booking, string $method)
    {
        DB::beginTransaction();
        try {
            // Update booking status
            $booking->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'payment_completed_at' => now(),
                'payment_gateway_response' => [
                    'status' => 'success',
                    'transaction_id' => strtoupper($method) . '-' . time(),
                    'amount' => $booking->total_amount,
                    'currency' => 'NPR',
                    'paid_at' => now(),
                ],
            ]);

            DB::commit();

            return redirect()->route('payment.success', $booking);

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Show payment success page.
     */
    public function success(Booking $booking)
    {
        // Ensure user can only view their own booking success
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        $booking->load(['schedule.route.sourceCity', 'schedule.route.destinationCity', 'schedule.bus', 'schedule.operator']);

        return view('customer.payment.success', compact('booking'));
    }

    /**
     * Show payment failed page.
     */
    public function failed(Booking $booking)
    {
        // Ensure user can only view their own booking failure
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        $booking->load(['schedule.route.sourceCity', 'schedule.route.destinationCity', 'schedule.bus', 'schedule.operator']);

        return view('customer.payment.failed', compact('booking'));
    }

    /**
     * Verify payment status.
     */
    public function verify(Request $request, Booking $booking)
    {
        // Ensure user can only verify their own bookings
        if ($booking->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to booking.');
        }

        // In production, verify payment with the respective gateway
        $verified = true; // Simulate verification success

        if ($verified && $booking->payment_status !== 'paid') {
            DB::beginTransaction();
            try {
                $booking->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'payment_completed_at' => now(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully.',
                    'redirect_url' => route('payment.success', $booking),
                ]);

            } catch (\Exception $e) {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed.',
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment verification failed.',
        ], 422);
    }

    /**
     * Handle eSewa callback.
     */
    public function esewaCallback(Request $request)
    {
        // Handle eSewa payment callback
        // In production, verify the callback signature and update booking status

        Log::info('eSewa callback received', $request->all());

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle Khalti callback.
     */
    public function khaltiCallback(Request $request)
    {
        // Handle Khalti payment callback
        // In production, verify the callback signature and update booking status

        Log::info('Khalti callback received', $request->all());

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle IME Pay callback.
     */
    public function imeCallback(Request $request)
    {
        // Handle IME Pay callback
        // In production, verify the callback signature and update booking status

        Log::info('IME Pay callback received', $request->all());

        return response()->json(['status' => 'success']);
    }
}
