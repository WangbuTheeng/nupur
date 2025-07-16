<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class EsewaSimulatorService
{
    /**
     * Simulate eSewa payment when test environment is unavailable
     */
    public function simulatePayment(Booking $booking)
    {
        try {
            Log::info('eSewa Simulator: Payment simulation started', [
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount
            ]);

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'payment_method' => 'esewa_simulator',
                'amount' => $booking->total_amount,
                'currency' => 'NPR',
                'status' => 'pending',
                'transaction_id' => $this->generateTransactionId(),
                'gateway_data' => [
                    'simulator' => true,
                    'note' => 'eSewa test environment unavailable - using simulator'
                ],
            ]);

            // Generate simulation form
            $formHtml = $this->generateSimulationForm($payment, $booking);

            return [
                'success' => true,
                'payment' => $payment,
                'form_html' => $formHtml,
                'is_simulation' => true,
            ];

        } catch (\Exception $e) {
            Log::error('eSewa Simulator: Payment simulation failed', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Payment simulation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate transaction ID for simulation
     */
    private function generateTransactionId()
    {
        return 'SIM-' . time() . '-' . rand(1000, 9999);
    }

    /**
     * Generate simulation form HTML
     */
    private function generateSimulationForm($payment, $booking)
    {
        $successUrl = config('services.esewa.success_url') . '/' . $payment->id;
        $failureUrl = config('services.esewa.failure_url') . '/' . $payment->id;

        return '
        <div class="esewa-simulator-container">
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 shadow-lg">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.314 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-yellow-800 mb-2">eSewa Test Environment</h3>
                    <p class="text-sm text-yellow-700 mb-4">eSewa test environment is currently unavailable. Using simulator for testing.</p>
                </div>
                
                <div class="bg-white rounded-lg p-4 mb-6 border border-yellow-200">
                    <h4 class="font-semibold text-gray-800 mb-3">Payment Details</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Amount:</span>
                            <span class="font-semibold">NPR ' . number_format($booking->total_amount, 2) . '</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Transaction ID:</span>
                            <span class="font-mono text-xs">' . $payment->transaction_id . '</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method:</span>
                            <span>eSewa (Simulator)</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <button onclick="simulateSuccess()" class="w-full bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 font-semibold transition-colors">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simulate Successful Payment
                    </button>
                    
                    <button onclick="simulateFailure()" class="w-full bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 font-semibold transition-colors">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Simulate Failed Payment
                    </button>
                </div>
                
                <div class="mt-6 text-xs text-gray-500 text-center">
                    <p>This is a simulation for testing purposes only</p>
                    <p class="mt-1">Real payments will use actual eSewa gateway</p>
                </div>
            </div>
            
            <script>
                function simulateSuccess() {
                    // Show loading state
                    showLoading("Processing successful payment...");
                    
                    // Simulate processing time
                    setTimeout(function() {
                        // Create success data (simulating eSewa response)
                        const successData = {
                            transaction_code: "SIM" + Math.random().toString(36).substr(2, 6).toUpperCase(),
                            status: "COMPLETE",
                            total_amount: ' . $booking->total_amount . ',
                            transaction_uuid: "' . $payment->transaction_id . '",
                            product_code: "EPAYTEST",
                            signed_field_names: "transaction_code,status,total_amount,transaction_uuid,product_code",
                            signature: "simulated_signature_" + Date.now()
                        };
                        
                        // Encode data like eSewa does
                        const encodedData = btoa(JSON.stringify(successData));
                        
                        // Redirect to success URL with data
                        window.location.href = "' . $successUrl . '?data=" + encodedData;
                    }, 2000);
                }
                
                function simulateFailure() {
                    showLoading("Processing failed payment...");
                    
                    setTimeout(function() {
                        window.location.href = "' . $failureUrl . '?error=user_cancelled";
                    }, 1500);
                }
                
                function showLoading(message) {
                    const container = document.querySelector(".esewa-simulator-container");
                    container.innerHTML = `
                        <div class="bg-white rounded-lg p-8 shadow-lg text-center">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="animate-spin w-8 h-8 text-blue-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">${message}</h3>
                            <p class="text-sm text-gray-600">Please wait...</p>
                        </div>
                    `;
                }
            </script>
        </div>';
    }

    /**
     * Verify simulated payment (for success callback)
     */
    public function verifySimulatedPayment($paymentId, $data)
    {
        try {
            $payment = Payment::findOrFail($paymentId);
            
            if (isset($data['data'])) {
                // Decode the simulated response
                $decodedData = json_decode(base64_decode($data['data']), true);
                
                if ($decodedData && $decodedData['status'] === 'COMPLETE') {
                    // Update payment status
                    $payment->update([
                        'status' => 'completed',
                        'gateway_response' => $decodedData,
                        'completed_at' => now(),
                    ]);
                    
                    // Update booking status
                    $payment->booking->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed',
                    ]);
                    
                    Log::info('eSewa Simulator: Payment completed successfully', [
                        'payment_id' => $payment->id,
                        'booking_id' => $payment->booking_id,
                        'transaction_code' => $decodedData['transaction_code']
                    ]);
                    
                    return [
                        'success' => true,
                        'payment' => $payment,
                        'message' => 'Payment completed successfully (simulated)',
                    ];
                }
            }
            
            return [
                'success' => false,
                'message' => 'Invalid payment data',
            ];
            
        } catch (\Exception $e) {
            Log::error('eSewa Simulator: Payment verification failed', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Payment verification failed',
            ];
        }
    }
}
