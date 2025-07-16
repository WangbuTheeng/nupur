<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khalti Payment - BookNGo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .khalti-gradient {
            background: linear-gradient(135deg, #5C2D91 0%, #8B5CF6 100%);
        }
        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #8B5CF6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .countdown {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full">
            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden fade-in">
                <!-- Header -->
                <div class="khalti-gradient text-white p-6 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold mb-2">Khalti Payment</h1>
                    <p class="text-purple-100">Secure Digital Wallet Payment</p>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <!-- Booking Details -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Details</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Booking Reference:</span>
                                <span class="font-medium">{{ $booking->booking_reference }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount:</span>
                                <span class="font-bold text-purple-600">Rs. {{ number_format($booking->total_amount) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Method:</span>
                                <span class="font-medium">Khalti Digital Wallet</span>
                            </div>
                            @if($is_simulator)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Environment:</span>
                                    <span class="text-orange-600 font-medium">Test/Simulator</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status Section -->
                    <div id="status-section" class="mb-6">
                        <!-- Initial Status -->
                        <div id="preparing-status" class="text-center">
                            <div class="spinner mx-auto mb-4"></div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Preparing Payment</h4>
                            <p class="text-gray-600">Setting up secure connection to Khalti...</p>
                            <div class="mt-4">
                                <div class="countdown text-2xl text-purple-600" id="countdown">3</div>
                                <p class="text-sm text-gray-500">Redirecting in <span id="countdown-text">3</span> seconds</p>
                            </div>
                        </div>

                        <!-- Ready Status (hidden initially) -->
                        <div id="ready-status" class="text-center hidden">
                            <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Ready to Pay</h4>
                            <p class="text-gray-600 mb-4">Click the button below to proceed to Khalti</p>
                            <button onclick="proceedToKhalti()" class="w-full bg-purple-600 text-white py-3 px-6 rounded-lg hover:bg-purple-700 transition-colors font-semibold">
                                Proceed to Khalti Payment
                            </button>
                        </div>
                    </div>

                    @if($is_simulator)
                        <!-- Test Credentials -->
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold text-orange-900 mb-2">Test Credentials</h4>
                            <div class="text-sm text-orange-800 space-y-1">
                                <p><strong>Khalti ID:</strong> 9800000000 to 9800000005</p>
                                <p><strong>MPIN:</strong> 1111</p>
                                <p><strong>OTP:</strong> 987654</p>
                                <p class="text-xs text-orange-600 mt-2">Use these credentials in the simulator</p>
                            </div>
                        </div>
                    @endif

                    <!-- Security Notice -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-blue-900 mb-1">Secure Payment</h4>
                                <p class="text-sm text-blue-700">Your payment is secured by Khalti's encryption technology. Never share your payment credentials with anyone.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-6 py-4">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>Powered by Khalti</span>
                        <a href="{{ route('customer.bookings.index') }}" class="text-purple-600 hover:text-purple-700">Cancel Payment</a>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Having trouble? <a href="#" class="text-purple-600 hover:text-purple-700">Contact Support</a>
                </p>
            </div>
        </div>
    </div>

    <!-- Hidden form for redirection -->
    <form id="khalti-form" action="{{ $payment_url }}" method="GET" style="display: none;">
        <!-- Khalti payment URL will be used for redirection -->
    </form>

    <script>
        let countdown = 3;
        const paymentUrl = @json($payment_url);
        
        // Countdown timer
        const countdownInterval = setInterval(() => {
            countdown--;
            document.getElementById('countdown').textContent = countdown;
            document.getElementById('countdown-text').textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(countdownInterval);
                showReadyStatus();
            }
        }, 1000);

        function showReadyStatus() {
            document.getElementById('preparing-status').classList.add('hidden');
            document.getElementById('ready-status').classList.remove('hidden');
            
            // Auto-redirect after 2 more seconds
            setTimeout(() => {
                proceedToKhalti();
            }, 2000);
        }

        function proceedToKhalti() {
            // Show loading state
            const button = document.querySelector('button');
            if (button) {
                button.innerHTML = '<div class="spinner mx-auto" style="width: 20px; height: 20px; border-width: 2px;"></div>';
                button.disabled = true;
            }
            
            // Redirect to Khalti
            setTimeout(() => {
                window.location.href = paymentUrl;
            }, 500);
        }

        // Handle page visibility change (when user comes back from Khalti)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // User came back to this page, might want to check payment status
                console.log('User returned from Khalti');
            }
        });
    </script>
</body>
</html>
