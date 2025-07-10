@extends('layouts.app')

@section('title', 'Complete Payment')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Complete Payment</h1>
        <p class="text-gray-600">Secure your booking by completing the payment</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Booking Summary -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h2>
            
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Booking Reference:</span>
                    <span class="font-medium">{{ $booking->booking_reference }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Route:</span>
                    <span class="font-medium">{{ $booking->schedule->route->full_name }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Bus:</span>
                    <span class="font-medium">{{ $booking->schedule->bus->display_name }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Travel Date:</span>
                    <span class="font-medium">{{ $booking->schedule->travel_date->format('M d, Y') }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Departure Time:</span>
                    <span class="font-medium">{{ $booking->schedule->departure_time->format('h:i A') }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Seats:</span>
                    <span class="font-medium">{{ $booking->seat_numbers_string }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Passengers:</span>
                    <span class="font-medium">{{ $booking->passenger_count }}</span>
                </div>
                
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between text-lg font-semibold">
                        <span>Total Amount:</span>
                        <span class="text-blue-600">NPR {{ number_format($booking->total_amount) }}</span>
                    </div>
                </div>
            </div>

            <!-- Passenger Details -->
            <div class="mt-6">
                <h3 class="font-medium text-gray-900 mb-3">Passenger Details</h3>
                <div class="space-y-2">
                    @foreach($booking->passenger_details as $index => $passenger)
                        <div class="text-sm">
                            <span class="font-medium">{{ $passenger['name'] }}</span>
                            <span class="text-gray-500">
                                ({{ $passenger['age'] }} years, {{ ucfirst($passenger['gender']) }})
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Expiry Timer -->
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                <div class="flex">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-800">
                            <strong>Time Remaining:</strong> 
                            <span id="countdown">{{ $booking->booking_expires_at->diffForHumans() }}</span>
                        </p>
                        <p class="text-xs text-yellow-700 mt-1">
                            Complete payment before {{ $booking->booking_expires_at->format('h:i A') }} to secure your booking.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Options -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h2>
            
            <!-- Khalti Payment -->
            <div class="border border-gray-200 rounded-lg p-4 mb-4">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-8 bg-purple-600 rounded flex items-center justify-center">
                        <span class="text-white text-xs font-bold">K</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-medium text-gray-900">Khalti</h3>
                        <p class="text-sm text-gray-600">Pay securely with Khalti digital wallet</p>
                    </div>
                </div>
                
                <button id="khalti-payment-button" 
                        class="w-full bg-purple-600 text-white hover:bg-purple-700 px-4 py-3 rounded-md font-semibold transition duration-200">
                    Pay NPR {{ number_format($booking->total_amount) }} with Khalti
                </button>
            </div>

            <!-- Demo Payment (for testing) -->
            <div class="border border-gray-200 rounded-lg p-4 mb-4">
                <div class="flex items-center mb-3">
                    <div class="w-12 h-8 bg-green-600 rounded flex items-center justify-center">
                        <span class="text-white text-xs font-bold">Demo</span>
                    </div>
                    <div class="ml-3">
                        <h3 class="font-medium text-gray-900">Demo Payment</h3>
                        <p class="text-sm text-gray-600">For testing purposes only</p>
                    </div>
                </div>
                
                <form action="{{ route('bookings.payment.demo', $booking) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-green-600 text-white hover:bg-green-700 px-4 py-3 rounded-md font-semibold transition duration-200">
                        Complete Demo Payment
                    </button>
                </form>
            </div>

            <!-- Payment Security Info -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">Secure Payment</h4>
                        <p class="text-xs text-blue-700 mt-1">
                            Your payment information is encrypted and secure. We never store your payment details.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Cancel Booking -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <form action="{{ route('bookings.cancel', $booking) }}" method="POST" 
                      onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-gray-600 text-white hover:bg-gray-700 px-4 py-2 rounded-md font-medium transition duration-200">
                        Cancel Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Countdown timer
function updateCountdown() {
    const expiryTime = new Date('{{ $booking->booking_expires_at->toISOString() }}');
    const now = new Date();
    const timeLeft = expiryTime - now;
    
    if (timeLeft <= 0) {
        document.getElementById('countdown').textContent = 'Expired';
        // Optionally redirect or disable payment buttons
        return;
    }
    
    const minutes = Math.floor(timeLeft / (1000 * 60));
    const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
    
    document.getElementById('countdown').textContent = `${minutes}m ${seconds}s`;
}

// Update countdown every second
setInterval(updateCountdown, 1000);
updateCountdown(); // Initial call

// Khalti Payment Integration (placeholder)
document.getElementById('khalti-payment-button').addEventListener('click', function() {
    alert('Khalti payment integration will be implemented here. For now, use the demo payment option.');
});
</script>
@endsection
