<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khalti Payment Simulator</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Khalti Logo and Header -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-purple-600 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Khalti Payment</h2>
                <p class="mt-2 text-sm text-gray-600">Payment Simulator (Test Mode)</p>
                <div class="mt-2 px-3 py-1 bg-yellow-100 border border-yellow-400 rounded-md">
                    <p class="text-xs text-yellow-800">⚠️ This is a simulator for testing without real Khalti credentials</p>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booking Reference:</span>
                        <span class="font-medium">{{ $booking->booking_reference }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Route:</span>
                        <span class="font-medium">{{ $booking->schedule->route->full_name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Seats:</span>
                        <span class="font-medium">{{ implode(', ', $booking->seat_numbers) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                        <span class="text-lg font-bold text-purple-600">NRs {{ number_format($amount) }}</span>
                    </div>
                </div>
            </div>

            <!-- Simulated Khalti Login Form -->
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Login to Khalti</h3>
                
                <form id="khalti-form" class="space-y-4">
                    <div>
                        <label for="khalti_id" class="block text-sm font-medium text-gray-700">Khalti ID</label>
                        <input type="text" id="khalti_id" name="khalti_id" value="9800000000" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        <p class="mt-1 text-xs text-gray-500">Test ID: 9800000000 to 9800000005</p>
                    </div>
                    
                    <div>
                        <label for="mpin" class="block text-sm font-medium text-gray-700">MPIN</label>
                        <input type="password" id="mpin" name="mpin" value="1111" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        <p class="mt-1 text-xs text-gray-500">Test MPIN: 1111</p>
                    </div>
                    
                    <div>
                        <label for="otp" class="block text-sm font-medium text-gray-700">OTP</label>
                        <input type="text" id="otp" name="otp" value="987654" 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        <p class="mt-1 text-xs text-gray-500">Test OTP: 987654</p>
                    </div>
                </form>
            </div>

            <!-- Payment Actions -->
            <div class="space-y-3">
                <form action="{{ route('khalti.simulator.complete', $payment->id) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Complete Payment (Simulate Success)
                    </button>
                </form>
                
                <button onclick="simulateFailure()" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel Payment (Simulate Failure)
                </button>
            </div>

            <!-- Information -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">Simulator Information</h4>
                        <div class="mt-1 text-sm text-blue-700">
                            <p>• This is a payment simulator for testing</p>
                            <p>• No real money will be charged</p>
                            <p>• Use test credentials provided above</p>
                            <p>• To use real Khalti, get credentials from merchant dashboard</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Get Real Credentials Link -->
            <div class="text-center">
                <a href="/debug/khalti-credentials-guide" target="_blank" 
                   class="text-purple-600 hover:text-purple-500 text-sm font-medium">
                    📋 How to get real Khalti credentials →
                </a>
            </div>
        </div>
    </div>

    <script>
        function simulateFailure() {
            const failureUrl = '{{ route("payment.khalti.failure") }}?payment_id={{ $payment->id }}&status=User canceled';
            window.location.href = failureUrl;
        }

        // Auto-fill form validation
        document.getElementById('khalti-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const khaltiId = document.getElementById('khalti_id').value;
            const mpin = document.getElementById('mpin').value;
            const otp = document.getElementById('otp').value;
            
            if (khaltiId && mpin && otp) {
                // Form is valid, allow payment completion
                return true;
            } else {
                alert('Please fill in all fields with test credentials');
                return false;
            }
        });
    </script>
</body>
</html>
