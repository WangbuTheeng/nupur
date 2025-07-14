@extends('layouts.app')

@section('title', 'Passenger Details')

@section('content')
@php
    // Ensure seatNumbers is always an array to prevent count() errors
    $seatNumbers = $seatNumbers ?? [];
    $seatCount = count($seatNumbers);
@endphp
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-green-600 via-green-700 to-emerald-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Passenger Details</h1>
                    <p class="text-xl text-green-100">
                        {{ $schedule->route->sourceCity->name }} → {{ $schedule->route->destinationCity->name }}
                    </p>
                    <p class="text-green-200">
                        {{ $schedule->travel_date->format('M d, Y') }} • Departure: {{ \Carbon\Carbon::parse($schedule->departure_time)->format('g:i A') }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <div class="text-2xl font-bold">{{ $seatCount }} Seat(s)</div>
                    <div class="text-green-200">{{ implode(', ', $seatNumbers) }}</div>
                    <div class="text-green-300 font-medium">
                        Total: Rs. {{ number_format($schedule->fare * $seatCount) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 relative z-10">
        <form action="{{ route('booking.store-details') }}" method="POST" id="passenger-form">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Passenger Details Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Passenger Information</h2>
                        
                        <!-- Primary Passenger Details -->
                        <div class="border border-gray-200 rounded-xl p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Primary Passenger Details
                                </h3>
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $seatCount }} Seat(s): {{ implode(', ', $seatNumbers) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Full Name -->
                                <div class="md:col-span-2">
                                    <label for="primary_passenger_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Full Name *
                                    </label>
                                    <input type="text"
                                           name="primary_passenger_name"
                                           id="primary_passenger_name"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                           placeholder="Enter full name of primary passenger"
                                           value="{{ old('primary_passenger_name', auth()->user()->name) }}"
                                           required>
                                    @error('primary_passenger_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Age -->
                                <div>
                                    <label for="primary_passenger_age" class="block text-sm font-medium text-gray-700 mb-2">
                                        Age *
                                    </label>
                                    <input type="number"
                                           name="primary_passenger_age"
                                           id="primary_passenger_age"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                           placeholder="Age"
                                           min="1" max="120"
                                           value="{{ old('primary_passenger_age') }}"
                                           required>
                                    @error('primary_passenger_age')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Gender -->
                                <div>
                                    <label for="primary_passenger_gender" class="block text-sm font-medium text-gray-700 mb-2">
                                        Gender *
                                    </label>
                                    <select name="primary_passenger_gender"
                                            id="primary_passenger_gender"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                            required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('primary_passenger_gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('primary_passenger_gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('primary_passenger_gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('primary_passenger_gender')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Phone (Optional) -->
                                <div class="md:col-span-2">
                                    <label for="primary_passenger_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number (Optional)
                                    </label>
                                    <input type="tel"
                                           name="primary_passenger_phone"
                                           id="primary_passenger_phone"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                           placeholder="Phone number"
                                           value="{{ old('primary_passenger_phone', auth()->user()->phone) }}">
                                    @error('primary_passenger_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Group Booking Notice -->
                            @if($seatCount > 1)
                                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-start space-x-3">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-blue-900">Group Booking</h4>
                                            <p class="text-sm text-blue-800 mt-1">
                                                You are booking {{ $seatCount }} seats. The primary passenger details will be used for all seats.
                                                You can provide individual passenger names at the time of travel if required by the operator.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Contact Information -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Contact Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Contact Phone -->
                                <div>
                                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contact Phone *
                                    </label>
                                    <input type="tel" 
                                           name="contact_phone" 
                                           id="contact_phone"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                           placeholder="Primary contact number"
                                           value="{{ old('contact_phone', auth()->user()->phone) }}"
                                           required>
                                    @error('contact_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Contact Email -->
                                <div>
                                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contact Email *
                                    </label>
                                    <input type="email" 
                                           name="contact_email" 
                                           id="contact_email"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                           placeholder="Email address"
                                           value="{{ old('contact_email', auth()->user()->email) }}"
                                           required>
                                    @error('contact_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Special Requests -->
                            <div class="mt-6">
                                <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-2">
                                    Special Requests (Optional)
                                </label>
                                <textarea name="special_requests" 
                                          id="special_requests"
                                          rows="3"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                          placeholder="Any special requirements or requests..."
                                          maxlength="500">{{ old('special_requests') }}</textarea>
                                @error('special_requests')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-sm text-gray-500">Maximum 500 characters</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Booking Summary</h3>
                        
                        <!-- Trip Details -->
                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Route:</span>
                                <span class="font-medium text-right">{{ $schedule->route->sourceCity->name }} → {{ $schedule->route->destinationCity->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-medium">{{ $schedule->travel_date->format('M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Time:</span>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('g:i A') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Bus:</span>
                                <span class="font-medium">{{ $schedule->bus->bus_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Operator:</span>
                                <span class="font-medium text-right">{{ $schedule->operator->company_name ?? $schedule->operator->name }}</span>
                            </div>
                        </div>

                        <!-- Selected Seats -->
                        <div class="border-t pt-4 mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Selected Seats</h4>
                            <div class="space-y-2">
                                @foreach($seatNumbers as $seatNumber)
                                    <div class="flex justify-between items-center p-2 bg-green-50 rounded">
                                        <span>Seat {{ $seatNumber }}</span>
                                        <span class="font-medium">Rs. {{ number_format($schedule->fare) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Calculation -->
                        <div class="border-t pt-4 mb-6">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Seats ({{ $seatCount }}):</span>
                                    <span class="font-medium">Rs. {{ number_format($schedule->fare * $seatCount) }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total:</span>
                                    <span class="text-green-600">Rs. {{ number_format($schedule->fare * $seatCount) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full bg-green-600 text-white px-6 py-4 rounded-xl hover:bg-green-700 font-semibold transition-colors">
                            Continue to Review
                        </button>

                        <!-- Reservation Timer -->
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-yellow-800">
                                    Seats reserved for <span id="timer" class="font-bold">15:00</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Reservation timer
    let timeLeft = 15 * 60; // 15 minutes in seconds
    const timerElement = document.getElementById('timer');
    
    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            alert('Seat reservation has expired. You will be redirected to seat selection.');
            window.location.href = '{{ route("booking.seat-selection", $schedule) }}';
            return;
        }
        
        timeLeft--;
    }
    
    // Update timer every second
    updateTimer();
    setInterval(updateTimer, 1000);
    
    // Form validation
    document.getElementById('passenger-form').addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('input[required], select[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
            } else {
                field.classList.remove('border-red-500');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
});
</script>
@endsection
