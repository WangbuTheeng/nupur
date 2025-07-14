@extends('layouts.app')

@section('title', 'Payment Options')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Complete Payment</h1>
                    <p class="text-xl text-blue-100">
                        {{ $booking->schedule->route->sourceCity->name }} → {{ $booking->schedule->route->destinationCity->name }}
                    </p>
                    <p class="text-blue-200">
                        Booking Reference: {{ $booking->booking_reference }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <div class="text-3xl font-bold">Rs. {{ number_format($booking->total_amount) }}</div>
                    <div class="text-blue-200">{{ $booking->passenger_count }} passenger(s)</div>
                    <div class="text-blue-300 font-medium">
                        {{ count($booking->seat_numbers) }} seat(s): {{ implode(', ', $booking->seat_numbers) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Payment Methods -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Choose Payment Method</h2>
                    
                    <form action="{{ route('payment.process', $booking) }}" method="POST" id="payment-form">
                        @csrf
                        
                        <div class="space-y-4">
                            @foreach($paymentMethods as $key => $method)
                                @if($method['enabled'])
                                    <div class="payment-option border-2 border-gray-200 rounded-xl p-6 cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all duration-200"
                                         onclick="selectPaymentMethod('{{ $key }}')">
                                        <div class="flex items-center">
                                            <input type="radio" 
                                                   name="payment_method" 
                                                   value="{{ $key }}" 
                                                   id="payment_{{ $key }}"
                                                   class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                                            
                                            <div class="ml-4 flex-1">
                                                <div class="flex items-center space-x-4">
                                                    <!-- Payment Logo -->
                                                    <div class="w-16 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                                        @if($key === 'esewa')
                                                            <div class="w-12 h-8 bg-green-600 rounded flex items-center justify-center">
                                                                <span class="text-white text-xs font-bold">eSewa</span>
                                                            </div>
                                                        @elseif($key === 'khalti')
                                                            <div class="w-12 h-8 bg-purple-600 rounded flex items-center justify-center">
                                                                <span class="text-white text-xs font-bold">Khalti</span>
                                                            </div>
                                                        @elseif($key === 'ime_pay')
                                                            <div class="w-12 h-8 bg-red-600 rounded flex items-center justify-center">
                                                                <span class="text-white text-xs font-bold">IME</span>
                                                            </div>
                                                        @elseif($key === 'bank_transfer')
                                                            <div class="w-12 h-8 bg-blue-600 rounded flex items-center justify-center">
                                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <!-- Payment Info -->
                                                    <div class="flex-1">
                                                        <h3 class="text-lg font-semibold text-gray-900">{{ $method['name'] }}</h3>
                                                        <p class="text-sm text-gray-600">{{ $method['description'] }}</p>
                                                    </div>
                                                    
                                                    <!-- Security Badge -->
                                                    <div class="flex items-center space-x-1 text-green-600">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                                        </svg>
                                                        <span class="text-xs font-medium">Secure</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="payment-option border-2 border-gray-200 rounded-xl p-6 opacity-50 cursor-not-allowed">
                                        <div class="flex items-center">
                                            <input type="radio" 
                                                   name="payment_method" 
                                                   value="{{ $key }}" 
                                                   id="payment_{{ $key }}"
                                                   class="w-5 h-5 text-gray-400 border-gray-300"
                                                   disabled>
                                            
                                            <div class="ml-4 flex-1">
                                                <div class="flex items-center space-x-4">
                                                    <div class="w-16 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                                        <span class="text-gray-400 text-xs">{{ $method['name'] }}</span>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3 class="text-lg font-semibold text-gray-400">{{ $method['name'] }}</h3>
                                                        <p class="text-sm text-gray-400">{{ $method['description'] }}</p>
                                                        <p class="text-xs text-red-500 mt-1">Currently unavailable</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        @error('payment_method')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <!-- Payment Button -->
                        <div class="mt-8">
                            <button type="submit" 
                                    id="payment-submit-btn"
                                    class="w-full bg-gray-300 text-gray-500 px-6 py-4 rounded-xl font-semibold transition-colors cursor-not-allowed"
                                    disabled>
                                Select a payment method to continue
                            </button>
                        </div>
                    </form>

                    <!-- Security Notice -->
                    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-start space-x-3">
                            <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <div>
                                <h4 class="font-medium text-green-900">Secure Payment</h4>
                                <p class="text-sm text-green-800 mt-1">
                                    Your payment information is encrypted and secure. We do not store your payment details.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Payment Summary</h3>
                    
                    <!-- Trip Details -->
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Route:</span>
                            <span class="font-medium text-right">{{ $booking->schedule->route->sourceCity->name }} → {{ $booking->schedule->route->destinationCity->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium">{{ $booking->schedule->travel_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('g:i A') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Seats:</span>
                            <span class="font-medium">{{ implode(', ', $booking->seat_numbers) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Passengers:</span>
                            <span class="font-medium">{{ $booking->passenger_count }}</span>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="border-t pt-4 mb-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Base Fare ({{ $booking->passenger_count }} seats):</span>
                                <span class="font-medium">Rs. {{ number_format($booking->schedule->fare * $booking->passenger_count) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total Amount:</span>
                                <span class="text-blue-600">Rs. {{ number_format($booking->total_amount) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Timer -->
                    @if($booking->booking_expires_at)
                        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-yellow-800">Payment expires in</p>
                                    <p id="payment-timer" class="text-lg font-bold text-yellow-900">--:--</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Contact Info -->
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-2">Contact Information</h4>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p>Phone: {{ $booking->contact_phone }}</p>
                            <p>Email: {{ $booking->contact_email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('payment-form');
    const submitBtn = document.getElementById('payment-submit-btn');
    const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
    
    // Handle payment method selection
    paymentOptions.forEach(option => {
        option.addEventListener('change', function() {
            if (this.checked) {
                submitBtn.disabled = false;
                submitBtn.className = 'w-full bg-blue-600 text-white px-6 py-4 rounded-xl font-semibold hover:bg-blue-700 transition-colors';
                submitBtn.textContent = `Pay Rs. {{ number_format($booking->total_amount) }} with ${this.closest('.payment-option').querySelector('h3').textContent}`;
            }
        });
    });

    // Payment timer
    @if($booking->booking_expires_at)
        const expiryTime = new Date('{{ $booking->booking_expires_at->toISOString() }}');
        
        function updateTimer() {
            const now = new Date();
            const timeLeft = expiryTime - now;
            
            if (timeLeft <= 0) {
                document.getElementById('payment-timer').textContent = 'EXPIRED';
                submitBtn.disabled = true;
                submitBtn.className = 'w-full bg-red-500 text-white px-6 py-4 rounded-xl font-semibold cursor-not-allowed';
                submitBtn.textContent = 'Payment Expired';
                alert('Payment time has expired. You will be redirected to create a new booking.');
                window.location.href = '{{ route("search.index") }}';
                return;
            }
            
            const minutes = Math.floor(timeLeft / 60000);
            const seconds = Math.floor((timeLeft % 60000) / 1000);
            document.getElementById('payment-timer').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
        
        updateTimer();
        setInterval(updateTimer, 1000);
    @endif
});

function selectPaymentMethod(method) {
    document.getElementById('payment_' + method).checked = true;
    document.getElementById('payment_' + method).dispatchEvent(new Event('change'));
    
    // Update visual selection
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('border-blue-500', 'bg-blue-50');
        option.classList.add('border-gray-200');
    });
    
    event.currentTarget.classList.remove('border-gray-200');
    event.currentTarget.classList.add('border-blue-500', 'bg-blue-50');
}
</script>
@endsection
