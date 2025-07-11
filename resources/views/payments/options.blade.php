@extends('layouts.app')

@section('title', 'Payment Options')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Complete Your Payment</h1>
        <p class="text-gray-600 mt-2">Choose your preferred payment method to confirm your booking</p>
    </div>

    <!-- Booking Summary -->
    <div class="bg-white shadow-lg rounded-xl p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Booking Summary</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Booking Reference:</span>
                    <span class="font-medium">{{ $booking->booking_reference }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Route:</span>
                    <span class="font-medium">{{ $booking->schedule->route->full_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Travel Date:</span>
                    <span class="font-medium">{{ $booking->schedule->travel_date->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Departure Time:</span>
                    <span class="font-medium">{{ $booking->schedule->departure_time->format('h:i A') }}</span>
                </div>
            </div>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Bus:</span>
                    <span class="font-medium">{{ $booking->schedule->bus->display_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Seats:</span>
                    <span class="font-medium">{{ implode(', ', $booking->seat_numbers) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Passengers:</span>
                    <span class="font-medium">{{ $booking->passenger_count }}</span>
                </div>
                <div class="flex justify-between border-t pt-3">
                    <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                    <span class="text-lg font-bold text-blue-600">Rs. {{ number_format($booking->total_amount) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Select Payment Method</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- eSewa Payment -->
            <form action="{{ route('payment.esewa.initiate', $booking) }}" method="POST">
                @csrf
                <button type="submit" class="w-full p-6 border-2 border-gray-200 rounded-xl hover:border-green-500 hover:bg-green-50 transition-all duration-200 group">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center group-hover:bg-green-200">
                            <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">eSewa</h3>
                        <p class="text-sm text-gray-600">Pay securely with your eSewa wallet</p>
                        <div class="mt-3 text-xs text-green-600 font-medium">Most Popular</div>
                    </div>
                </button>
            </form>

            <!-- Khalti Payment (Coming Soon) -->
            <div class="w-full p-6 border-2 border-gray-200 rounded-xl opacity-50 cursor-not-allowed">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Khalti</h3>
                    <p class="text-sm text-gray-600">Digital wallet payment</p>
                    <div class="mt-3 text-xs text-gray-500 font-medium">Coming Soon</div>
                </div>
            </div>

            <!-- FonePay (Coming Soon) -->
            <div class="w-full p-6 border-2 border-gray-200 rounded-xl opacity-50 cursor-not-allowed">
                <div class="text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">FonePay</h3>
                    <p class="text-sm text-gray-600">Mobile banking solution</p>
                    <div class="mt-3 text-xs text-gray-500 font-medium">Coming Soon</div>
                </div>
            </div>
        </div>

        <!-- Payment Security Notice -->
        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-blue-900">Secure Payment</h4>
                    <p class="text-sm text-blue-700 mt-1">
                        Your payment information is encrypted and secure. We do not store your payment details.
                    </p>
                </div>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="mt-6 text-sm text-gray-600">
            <p>By proceeding with payment, you agree to our 
                <a href="#" class="text-blue-600 hover:underline">Terms and Conditions</a> and 
                <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>.
            </p>
        </div>
    </div>

    <!-- Back to Booking Button -->
    <div class="mt-8 text-center">
        <a href="{{ route('booking.show', $booking) }}" 
           class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Booking Details
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to payment buttons
    const paymentForms = document.querySelectorAll('form[action*="payment"]');
    
    paymentForms.forEach(form => {
        form.addEventListener('submit', function() {
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.innerHTML = `
                    <div class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </div>
                `;
            }
        });
    });
});
</script>
@endpush

@endsection
