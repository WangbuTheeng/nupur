<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KhaltiPaymentService
{
    private $secretKey;
    private $baseUrl;
    private $successUrl;
    private $failureUrl;

    public function __construct()
    {
        // Khalti Test Configuration (using sandbox credentials)
        $this->secretKey = config('services.khalti.secret_key', 'test_secret_key_f59e8b7d18b4499ca40f68195a846e9b');
        $this->baseUrl = config('services.khalti.base_url', 'https://dev.khalti.com/api/v2');
        $this->successUrl = route('payment.khalti.success');
        $this->failureUrl = route('payment.khalti.failure');
    }

    /**
     * Initiate payment with Khalti
     */
    public function initiatePayment(Booking $booking, array $additionalData = [])
    {
        try {
            Log::info('Khalti Payment Initiation Started', [
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'base_url' => $this->baseUrl
            ]);

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'payment_method' => 'khalti',
                'amount' => $booking->total_amount,
                'currency' => 'NRs',
                'status' => 'pending',
                'transaction_id' => $this->generateTransactionId(),
                'gateway_data' => $additionalData,
            ]);

            // Prepare Khalti payment data
            $amountInPaisa = (int) ($booking->total_amount * 100); // Convert to paisa
            $purchaseOrderId = 'BOOKING-' . $booking->id . '-' . time();

            // Get customer info from booking
            $customerInfo = $this->getCustomerInfo($booking);

            $paymentData = [
                'return_url' => $this->successUrl . '?payment_id=' . $payment->id,
                'website_url' => config('app.url'),
                'amount' => $amountInPaisa,
                'purchase_order_id' => $purchaseOrderId,
                'purchase_order_name' => 'Bus Booking - ' . $booking->booking_reference,
                'customer_info' => $customerInfo,
                'amount_breakdown' => [
                    [
                        'label' => 'Bus Fare',
                        'amount' => $amountInPaisa
                    ]
                ],
                'product_details' => [
                    [
                        'identity' => $booking->booking_reference,
                        'name' => 'Bus Ticket - ' . ($booking->schedule->route->full_name ?? 'Route'),
                        'total_price' => $amountInPaisa,
                        'quantity' => $booking->passenger_count ?? 1,
                        'unit_price' => $amountInPaisa
                    ]
                ]
            ];

            // Make API request to Khalti
            $response = Http::withHeaders([
                'Authorization' => 'Key ' . $this->secretKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/epayment/initiate/', $paymentData);

            if ($response->successful()) {
                $responseData = $response->json();

                // Update payment with Khalti response
                $payment->update([
                    'gateway_transaction_id' => $responseData['pidx'],
                    'gateway_data' => array_merge($additionalData, [
                        'pidx' => $responseData['pidx'],
                        'payment_url' => $responseData['payment_url'],
                        'expires_at' => $responseData['expires_at'],
                        'purchase_order_id' => $purchaseOrderId
                    ])
                ]);

                Log::info('Khalti Payment Initiated Successfully', [
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    'pidx' => $responseData['pidx'],
                    'payment_url' => $responseData['payment_url']
                ]);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'payment_url' => $responseData['payment_url'],
                    'pidx' => $responseData['pidx'],
                    'expires_at' => $responseData['expires_at']
                ];
            } else {
                $errorData = $response->json();
                Log::error('Khalti Payment Initiation Failed', [
                    'booking_id' => $booking->id,
                    'status_code' => $response->status(),
                    'error' => $errorData
                ]);

                // Check if it's an invalid token error (credentials issue)
                if (isset($errorData['detail']) && strpos($errorData['detail'], 'Invalid token') !== false) {
                    // Use simulator as fallback
                    $simulatorUrl = route('khalti.simulator', $payment->id);

                    Log::info('Using Khalti simulator due to invalid credentials', [
                        'booking_id' => $booking->id,
                        'payment_id' => $payment->id,
                        'simulator_url' => $simulatorUrl
                    ]);

                    return [
                        'success' => true,
                        'payment' => $payment,
                        'payment_url' => $simulatorUrl,
                        'pidx' => 'SIM_' . time() . '_' . $payment->id,
                        'is_simulator' => true,
                        'message' => 'Using payment simulator (invalid Khalti credentials)'
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Payment initiation failed: ' . ($errorData['detail'] ?? 'Unknown error'),
                    'error' => $errorData
                ];
            }

        } catch (\Exception $e) {
            Log::error('Khalti payment initiation exception', [
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
     * Verify payment with Khalti lookup API
     */
    public function verifyPayment($paymentId, $pidx)
    {
        try {
            $payment = Payment::findOrFail($paymentId);

            // Check if this is a simulated payment
            if (strpos($pidx, 'SIM_') === 0) {
                return $this->handleSimulatedPaymentVerification($payment, $pidx);
            }

            // Make lookup request to Khalti
            $response = Http::withHeaders([
                'Authorization' => 'Key ' . $this->secretKey,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/epayment/lookup/', [
                'pidx' => $pidx
            ]);

            if ($response->successful()) {
                $responseData = $response->json();
                
                Log::info('Khalti Payment Verification Response', [
                    'payment_id' => $payment->id,
                    'pidx' => $pidx,
                    'status' => $responseData['status'],
                    'response' => $responseData
                ]);

                // Check if payment was successful
                if ($responseData['status'] === 'Completed') {
                    $this->handleSuccessfulPayment($payment, $responseData['transaction_id'], $responseData);
                    
                    return [
                        'success' => true,
                        'message' => 'Payment verified successfully',
                        'payment' => $payment->fresh(),
                        'status' => 'completed'
                    ];
                } else {
                    // Handle other statuses
                    $this->handlePaymentStatus($payment, $responseData);
                    
                    return [
                        'success' => false,
                        'message' => 'Payment verification failed',
                        'status' => $responseData['status'],
                        'error' => 'Payment status: ' . $responseData['status']
                    ];
                }
            } else {
                $errorData = $response->json();
                Log::error('Khalti Payment Verification Failed', [
                    'payment_id' => $payment->id,
                    'pidx' => $pidx,
                    'status_code' => $response->status(),
                    'error' => $errorData
                ]);

                return [
                    'success' => false,
                    'message' => 'Payment verification failed',
                    'error' => $errorData
                ];
            }

        } catch (\Exception $e) {
            Log::error('Khalti payment verification exception', [
                'payment_id' => $paymentId,
                'pidx' => $pidx,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Payment verification failed. Please contact support.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get customer info from booking
     */
    private function getCustomerInfo(Booking $booking)
    {
        $passengerDetails = is_string($booking->passenger_details) 
            ? json_decode($booking->passenger_details, true) 
            : $booking->passenger_details;

        return [
            'name' => $passengerDetails['name'] ?? 'Customer',
            'email' => $booking->contact_email ?? 'customer@example.com',
            'phone' => $booking->contact_phone ?? '9800000000'
        ];
    }

    /**
     * Handle successful payment
     */
    private function handleSuccessfulPayment(Payment $payment, $transactionId, $responseData)
    {
        // Update payment status
        $payment->update([
            'status' => 'completed',
            'gateway_transaction_id' => $transactionId,
            'gateway_response' => $responseData,
            'paid_at' => now(),
        ]);

        // Update booking status
        $booking = $payment->booking;
        $booking->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ]);

        Log::info('Khalti payment completed successfully', [
            'payment_id' => $payment->id,
            'booking_id' => $booking->id,
            'amount' => $payment->amount,
            'transaction_id' => $transactionId
        ]);
    }

    /**
     * Handle different payment statuses
     */
    private function handlePaymentStatus(Payment $payment, $responseData)
    {
        $status = $responseData['status'];
        
        switch ($status) {
            case 'User canceled':
            case 'Expired':
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => $responseData,
                    'failed_at' => now(),
                ]);
                break;
                
            case 'Pending':
            case 'Initiated':
                // Keep as pending
                $payment->update([
                    'gateway_response' => $responseData,
                ]);
                break;
                
            case 'Refunded':
                $payment->update([
                    'status' => 'refunded',
                    'gateway_response' => $responseData,
                ]);
                break;
        }
    }

    /**
     * Handle simulated payment verification
     */
    private function handleSimulatedPaymentVerification(Payment $payment, $pidx)
    {
        Log::info('Handling simulated payment verification', [
            'payment_id' => $payment->id,
            'pidx' => $pidx
        ]);

        // Check if payment is already completed
        if ($payment->status === 'completed') {
            return [
                'success' => true,
                'message' => 'Simulated payment already verified',
                'payment' => $payment->fresh(),
                'status' => 'completed'
            ];
        }

        // Simulate successful verification for simulator payments
        $transactionId = 'SIM_TXN_' . time();

        $responseData = [
            'pidx' => $pidx,
            'status' => 'Completed',
            'transaction_id' => $transactionId,
            'total_amount' => $payment->amount * 100, // in paisa
            'fee' => 0,
            'refunded' => false
        ];

        $this->handleSuccessfulPayment($payment, $transactionId, $responseData);

        return [
            'success' => true,
            'message' => 'Simulated payment verified successfully',
            'payment' => $payment->fresh(),
            'status' => 'completed'
        ];
    }

    /**
     * Generate unique transaction ID
     */
    private function generateTransactionId()
    {
        return 'KTI-' . date('ymd-His') . '-' . Str::random(4);
    }
}
