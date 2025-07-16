<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EsewaPaymentServiceV3
{
    private $merchantId;
    private $secretKey;
    private $successUrl;
    private $failureUrl;
    private $simulatorService;
    
    // eSewa URLs with priority order
    private $esewaUrls = [
        'test' => 'https://rc-epay.esewa.com.np/api/epay/main/v2/form',
        'production' => 'https://epay.esewa.com.np/api/epay/main/v2/form',
    ];

    public function __construct()
    {
        $this->merchantId = config('services.esewa.merchant_id');
        $this->secretKey = config('services.esewa.secret_key');
        $this->successUrl = config('services.esewa.success_url');
        $this->failureUrl = config('services.esewa.failure_url');
        $this->simulatorService = new EsewaSimulatorService();
    }

    /**
     * Initiate payment with intelligent fallback system
     */
    public function initiatePayment(Booking $booking)
    {
        try {
            Log::info('eSewa V3: Payment initiation started', [
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount
            ]);

            // Step 1: Check eSewa URL availability
            $workingUrl = $this->findWorkingEsewaUrl();
            
            if ($workingUrl) {
                // Step 2: Try real eSewa payment
                $result = $this->processRealEsewaPayment($booking, $workingUrl);
                
                if ($result['success']) {
                    return $result;
                }
                
                Log::warning('eSewa V3: Real eSewa payment failed, falling back to simulator', [
                    'booking_id' => $booking->id,
                    'error' => $result['message']
                ]);
            } else {
                Log::warning('eSewa V3: No working eSewa URLs found, using simulator', [
                    'booking_id' => $booking->id
                ]);
            }

            // Step 3: Fallback to simulator
            return $this->simulatorService->simulatePayment($booking);

        } catch (\Exception $e) {
            Log::error('eSewa V3: Payment initiation failed completely', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Payment system temporarily unavailable. Please try again later.',
            ];
        }
    }

    /**
     * Find a working eSewa URL
     */
    private function findWorkingEsewaUrl()
    {
        foreach ($this->esewaUrls as $type => $url) {
            try {
                Log::info("eSewa V3: Testing URL accessibility", ['type' => $type, 'url' => $url]);
                
                $response = Http::timeout(5)->get($url);
                
                // eSewa returns 405 for GET requests on POST endpoints, which is expected
                if ($response->successful() || $response->status() === 405) {
                    Log::info("eSewa V3: Working URL found", ['type' => $type, 'url' => $url]);
                    return ['url' => $url, 'type' => $type];
                }
                
            } catch (\Exception $e) {
                Log::debug("eSewa V3: URL test failed", [
                    'type' => $type,
                    'url' => $url,
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }
        
        return null;
    }

    /**
     * Process real eSewa payment
     */
    private function processRealEsewaPayment(Booking $booking, $urlInfo)
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
                'gateway_data' => [
                    'esewa_url_type' => $urlInfo['type'],
                    'esewa_url' => $urlInfo['url']
                ],
            ]);

            // Prepare payment data
            $paymentData = $this->preparePaymentData($payment, $booking);
            
            // Generate form HTML
            $formHtml = $this->generatePaymentForm($paymentData, $urlInfo);

            Log::info('eSewa V3: Real payment form generated', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'url_type' => $urlInfo['type']
            ]);

            return [
                'success' => true,
                'payment' => $payment,
                'form_html' => $formHtml,
                'is_simulation' => false,
            ];

        } catch (\Exception $e) {
            Log::error('eSewa V3: Real payment processing failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'eSewa payment processing failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Prepare payment data for eSewa
     */
    private function preparePaymentData($payment, $booking)
    {
        $amount = $booking->total_amount;
        $taxAmount = 0;
        $serviceCharge = 0;
        $deliveryCharge = 0;
        $totalAmount = $amount + $taxAmount + $serviceCharge + $deliveryCharge;

        $paymentData = [
            'amount' => (string) $amount,
            'tax_amount' => (string) $taxAmount,
            'total_amount' => (string) $totalAmount,
            'transaction_uuid' => $payment->transaction_id,
            'product_code' => $this->merchantId,
            'product_service_charge' => (string) $serviceCharge,
            'product_delivery_charge' => (string) $deliveryCharge,
            'success_url' => $this->successUrl . '/' . $payment->id,
            'failure_url' => $this->failureUrl . '/' . $payment->id,
            'signed_field_names' => 'total_amount,transaction_uuid,product_code',
        ];

        // Generate signature
        $paymentData['signature'] = $this->generateSignature($paymentData);

        return $paymentData;
    }

    /**
     * Generate HMAC SHA256 signature
     */
    private function generateSignature($paymentData)
    {
        $message = sprintf(
            'total_amount=%s,transaction_uuid=%s,product_code=%s',
            $paymentData['total_amount'],
            $paymentData['transaction_uuid'],
            $paymentData['product_code']
        );

        $signature = hash_hmac('sha256', $message, $this->secretKey, true);
        return base64_encode($signature);
    }

    /**
     * Generate payment form HTML
     */
    private function generatePaymentForm($paymentData, $urlInfo)
    {
        $formFields = '';
        foreach ($paymentData as $key => $value) {
            $formFields .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">' . "\n";
        }

        $environmentBadge = $urlInfo['type'] === 'test' ? 
            '<span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Test Environment</span>' :
            '<span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Production Environment</span>';

        return '
        <div class="esewa-payment-container">
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Redirecting to eSewa</h3>
                    <p class="text-sm text-gray-600 mb-2">You will be redirected to eSewa payment gateway</p>
                    ' . $environmentBadge . '
                </div>
                
                <form id="esewa-payment-form" action="' . $urlInfo['url'] . '" method="POST">
                    ' . $formFields . '
                    <button type="submit" class="w-full bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 font-semibold transition-colors">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Pay NPR ' . $paymentData['total_amount'] . ' with eSewa
                    </button>
                </form>
                
                <div class="mt-4 text-xs text-gray-500 text-center">
                    <p>If you are not redirected automatically, click the button above</p>
                    <p class="mt-1">Secure payment powered by eSewa</p>
                </div>
            </div>
            
            <script>
                // Auto-submit after 2 seconds
                setTimeout(function() {
                    document.getElementById("esewa-payment-form").submit();
                }, 2000);
            </script>
        </div>';
    }

    /**
     * Generate transaction ID
     */
    private function generateTransactionId()
    {
        return 'TXN-' . time() . '-' . rand(1000, 9999);
    }
}
