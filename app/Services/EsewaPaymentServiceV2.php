<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EsewaPaymentServiceV2
{
    private $merchantId;
    private $secretKey;
    private $successUrl;
    private $failureUrl;
    
    // Multiple URLs to try (production is more stable than test)
    private $paymentUrls = [
        'production' => 'https://epay.esewa.com.np/api/epay/main/v2/form',
        'test' => 'https://rc-epay.esewa.com.np/api/epay/main/v2/form',
    ];
    
    private $statusCheckUrls = [
        'production' => 'https://epay.esewa.com.np/api/epay/transaction/status/',
        'test' => 'https://rc.esewa.com.np/api/epay/transaction/status/',
    ];

    public function __construct()
    {
        $this->merchantId = config('services.esewa.merchant_id');
        $this->secretKey = config('services.esewa.secret_key');
        $this->successUrl = config('services.esewa.success_url');
        $this->failureUrl = config('services.esewa.failure_url');
    }

    /**
     * Initiate payment with eSewa v2 API (robust implementation)
     */
    public function initiatePayment(Booking $booking, array $additionalData = [])
    {
        try {
            Log::info('eSewa Payment Initiation Started', [
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount
            ]);

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

            // Calculate amounts
            $amount = $booking->total_amount;
            $taxAmount = 0; // No tax for now
            $serviceCharge = 0;
            $deliveryCharge = 0;
            $totalAmount = $amount + $taxAmount + $serviceCharge + $deliveryCharge;

            // eSewa v2 API format
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

            // Log payment data for debugging
            Log::info('eSewa Payment Data Generated', [
                'booking_id' => $booking->id,
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'amount' => $amount,
                'total_amount' => $totalAmount,
                'signature_generated' => !empty($paymentData['signature'])
            ]);

            // Generate robust form HTML
            $formHtml = $this->generateRobustPaymentForm($paymentData);

            return [
                'success' => true,
                'payment' => $payment,
                'form_html' => $formHtml,
                'payment_data' => $paymentData,
            ];

        } catch (\Exception $e) {
            Log::error('eSewa Payment Initiation Failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate transaction ID
     */
    private function generateTransactionId()
    {
        return 'TXN-' . time() . '-' . rand(1000, 9999);
    }

    /**
     * Generate HMAC SHA256 signature for eSewa v2 API
     */
    private function generateSignature($paymentData)
    {
        try {
            // eSewa v2 signature format: total_amount=value,transaction_uuid=value,product_code=value
            $message = sprintf(
                'total_amount=%s,transaction_uuid=%s,product_code=%s',
                $paymentData['total_amount'],
                $paymentData['transaction_uuid'],
                $paymentData['product_code']
            );

            Log::info('eSewa Signature Generation', [
                'message' => $message,
                'secret_key_length' => strlen($this->secretKey)
            ]);

            // Generate HMAC SHA256 signature
            $signature = hash_hmac('sha256', $message, $this->secretKey, true);
            $signatureBase64 = base64_encode($signature);

            Log::info('eSewa Signature Generated', [
                'signature' => $signatureBase64,
                'message' => $message
            ]);

            return $signatureBase64;

        } catch (\Exception $e) {
            Log::error('eSewa Signature Generation Failed', [
                'error' => $e->getMessage(),
                'payment_data' => $paymentData
            ]);
            throw $e;
        }
    }

    /**
     * Generate robust payment form with multiple URL fallbacks
     */
    private function generateRobustPaymentForm($paymentData)
    {
        $formFields = '';
        foreach ($paymentData as $key => $value) {
            $formFields .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">' . "\n";
        }

        $primaryUrl = $this->paymentUrls['production']; // Use production as primary
        $fallbackUrl = $this->paymentUrls['test'];

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
                    <p class="text-sm text-gray-600 mb-4">You will be redirected to eSewa payment gateway</p>
                </div>
                
                <form id="esewa-payment-form" action="' . $primaryUrl . '" method="POST">
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
                let formSubmitted = false;
                
                // Auto-submit after 2 seconds
                setTimeout(function() {
                    if (!formSubmitted) {
                        submitForm();
                    }
                }, 2000);
                
                function submitForm() {
                    if (formSubmitted) return;
                    formSubmitted = true;
                    
                    const form = document.getElementById("esewa-payment-form");
                    const button = form.querySelector("button");
                    
                    // Update button state
                    button.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Redirecting to eSewa...
                    `;
                    button.disabled = true;
                    
                    // Try primary URL first
                    try {
                        form.submit();
                    } catch (error) {
                        console.log("Primary URL failed, trying fallback...");
                        // Try fallback URL
                        form.action = "' . $fallbackUrl . '";
                        setTimeout(() => {
                            try {
                                form.submit();
                            } catch (fallbackError) {
                                alert("eSewa payment gateway is currently unavailable. Please try again later.");
                                button.innerHTML = "Pay NPR ' . $paymentData['total_amount'] . ' with eSewa";
                                button.disabled = false;
                                formSubmitted = false;
                            }
                        }, 1000);
                    }
                }
                
                // Manual form submission
                document.getElementById("esewa-payment-form").addEventListener("submit", function(e) {
                    e.preventDefault();
                    submitForm();
                });
            </script>
        </div>';
    }
}
