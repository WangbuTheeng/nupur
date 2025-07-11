<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EsewaPaymentService
{
    private $merchantId;
    private $secretKey;
    private $baseUrl;
    private $successUrl;
    private $failureUrl;

    public function __construct()
    {
        $this->merchantId = config('services.esewa.merchant_id');
        $this->secretKey = config('services.esewa.secret_key');
        $this->baseUrl = config('services.esewa.base_url');
        $this->successUrl = config('services.esewa.success_url');
        $this->failureUrl = config('services.esewa.failure_url');
    }

    /**
     * Initiate payment with eSewa
     */
    public function initiatePayment(Booking $booking, array $additionalData = [])
    {
        try {
            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'payment_method' => 'esewa',
                'amount' => $booking->total_amount,
                'currency' => 'NPR',
                'status' => 'pending',
                'transaction_id' => $this->generateTransactionId(),
                'gateway_data' => $additionalData,
            ]);

            // Prepare eSewa payment data
            $paymentData = [
                'amt' => $booking->total_amount,
                'pdc' => 0, // Delivery charge
                'psc' => 0, // Service charge
                'txAmt' => 0, // Tax amount
                'tAmt' => $booking->total_amount, // Total amount
                'pid' => $payment->transaction_id,
                'scd' => $this->merchantId,
                'su' => $this->successUrl . '?payment_id=' . $payment->id,
                'fu' => $this->failureUrl . '?payment_id=' . $payment->id,
            ];

            // Generate form HTML for eSewa
            $formHtml = $this->generatePaymentForm($paymentData);

            return [
                'success' => true,
                'payment' => $payment,
                'form_html' => $formHtml,
                'payment_data' => $paymentData,
            ];

        } catch (\Exception $e) {
            Log::error('eSewa payment initiation failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Payment initiation failed. Please try again.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify payment with eSewa
     */
    public function verifyPayment($paymentId, $refId, $oid, $amt)
    {
        try {
            $payment = Payment::findOrFail($paymentId);
            
            // Prepare verification data
            $verificationData = [
                'amt' => $amt,
                'rid' => $refId,
                'pid' => $oid,
                'scd' => $this->merchantId,
            ];

            // Make verification request to eSewa
            $response = Http::asForm()->post($this->baseUrl . '/epay/transrec', $verificationData);

            if ($response->successful()) {
                $responseBody = $response->body();
                
                if (strpos($responseBody, 'Success') !== false) {
                    // Payment verified successfully
                    $this->handleSuccessfulPayment($payment, $refId, $responseBody);
                    
                    return [
                        'success' => true,
                        'message' => 'Payment verified successfully',
                        'payment' => $payment->fresh(),
                    ];
                } else {
                    // Payment verification failed
                    $this->handleFailedPayment($payment, 'Verification failed: ' . $responseBody);
                    
                    return [
                        'success' => false,
                        'message' => 'Payment verification failed',
                        'error' => $responseBody
                    ];
                }
            } else {
                throw new \Exception('eSewa API request failed: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error('eSewa payment verification failed', [
                'payment_id' => $paymentId,
                'ref_id' => $refId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Payment verification failed. Please contact support.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessfulPayment(Payment $payment, $refId, $responseData)
    {
        // Update payment status
        $payment->update([
            'status' => 'completed',
            'gateway_transaction_id' => $refId,
            'gateway_response' => $responseData,
            'paid_at' => now(),
        ]);

        // Update booking status
        $booking = $payment->booking;
        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        // Send confirmation notifications
        app(NotificationService::class)->sendBookingConfirmation($booking);
        app(NotificationService::class)->sendPaymentReceived($booking);

        Log::info('eSewa payment completed successfully', [
            'payment_id' => $payment->id,
            'booking_id' => $booking->id,
            'amount' => $payment->amount,
            'ref_id' => $refId
        ]);
    }

    /**
     * Handle failed payment
     */
    private function handleFailedPayment(Payment $payment, $reason)
    {
        // Update payment status
        $payment->update([
            'status' => 'failed',
            'gateway_response' => $reason,
            'failed_at' => now(),
        ]);

        // Update booking status
        $booking = $payment->booking;
        $booking->update([
            'status' => 'cancelled',
            'payment_status' => 'failed',
        ]);

        Log::warning('eSewa payment failed', [
            'payment_id' => $payment->id,
            'booking_id' => $booking->id,
            'reason' => $reason
        ]);
    }

    /**
     * Generate payment form HTML
     */
    private function generatePaymentForm($paymentData)
    {
        $formFields = '';
        foreach ($paymentData as $key => $value) {
            $formFields .= '<input type="hidden" name="' . $key . '" value="' . $value . '">' . "\n";
        }

        return '
        <form id="esewa-payment-form" action="' . $this->baseUrl . '/epay/main" method="POST">
            ' . $formFields . '
            <button type="submit" class="btn btn-primary">Pay with eSewa</button>
        </form>
        <script>
            document.getElementById("esewa-payment-form").submit();
        </script>';
    }

    /**
     * Generate unique transaction ID
     */
    private function generateTransactionId()
    {
        return 'BNG_' . time() . '_' . Str::random(8);
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($paymentId)
    {
        $payment = Payment::find($paymentId);
        
        if (!$payment) {
            return [
                'success' => false,
                'message' => 'Payment not found'
            ];
        }

        return [
            'success' => true,
            'payment' => $payment,
            'status' => $payment->status,
            'booking' => $payment->booking
        ];
    }

    /**
     * Process refund (if supported by eSewa)
     */
    public function processRefund(Payment $payment, $amount = null)
    {
        try {
            $refundAmount = $amount ?? $payment->amount;
            
            // Note: eSewa doesn't have direct refund API
            // This would need to be handled manually or through eSewa support
            
            Log::info('Refund request initiated', [
                'payment_id' => $payment->id,
                'original_amount' => $payment->amount,
                'refund_amount' => $refundAmount,
                'gateway_transaction_id' => $payment->gateway_transaction_id
            ]);

            return [
                'success' => true,
                'message' => 'Refund request has been submitted. It will be processed manually.',
                'refund_amount' => $refundAmount
            ];

        } catch (\Exception $e) {
            Log::error('Refund processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Refund processing failed',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStatistics($startDate = null, $endDate = null)
    {
        $query = Payment::where('payment_method', 'esewa');
        
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return [
            'total_payments' => $query->count(),
            'successful_payments' => $query->where('status', 'completed')->count(),
            'failed_payments' => $query->where('status', 'failed')->count(),
            'pending_payments' => $query->where('status', 'pending')->count(),
            'total_amount' => $query->where('status', 'completed')->sum('amount'),
            'average_amount' => $query->where('status', 'completed')->avg('amount'),
        ];
    }
}
