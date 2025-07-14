@extends('layouts.app')

@section('title', 'Review Booking')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Review Your Booking</h1>
                    <p class="text-xl text-purple-100">
                        {{ $booking->schedule->route->sourceCity->name }} → {{ $booking->schedule->route->destinationCity->name }}
                    </p>
                    <p class="text-purple-200">
                        Booking Reference: {{ $booking->booking_reference }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <div class="text-2xl font-bold">Rs. {{ number_format($booking->total_amount) }}</div>
                    <div class="text-purple-200">{{ $booking->passenger_count }} passenger(s)</div>
                    <div class="text-purple-300 font-medium">
                        Status: {{ ucfirst($booking->status) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Booking Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Trip Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Trip Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Departure -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Departure</h3>
                                    <p class="text-gray-600">{{ $booking->schedule->route->sourceCity->name }}</p>
                                </div>
                            </div>
                            <div class="ml-15">
                                <p class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('g:i A') }}</p>
                                <p class="text-gray-600">{{ $booking->schedule->travel_date->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <!-- Arrival -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Arrival</h3>
                                    <p class="text-gray-600">{{ $booking->schedule->route->destinationCity->name }}</p>
                                </div>
                            </div>
                            <div class="ml-15">
                                <p class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->schedule->arrival_time)->format('g:i A') }}</p>
                                <p class="text-gray-600">{{ $booking->schedule->travel_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bus Information -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bus Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Bus Number:</span>
                                    <span class="font-semibold">{{ $booking->schedule->bus->bus_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Bus Type:</span>
                                    <span class="font-semibold">{{ $booking->schedule->bus->busType->name ?? 'Standard' }}</span>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Operator:</span>
                                    <span class="font-semibold">{{ $booking->schedule->operator->company_name ?? $booking->schedule->operator->name }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Seats:</span>
                                    <span class="font-semibold">{{ implode(', ', $booking->seat_numbers) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Passenger Details -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Passenger Details</h2>

                    @php
                        $primaryPassenger = $booking->passenger_details[0] ?? null;
                    @endphp

                    @if($primaryPassenger)
                        <div class="border border-gray-200 rounded-xl p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Primary Passenger
                                </h3>
                                <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ count($booking->seat_numbers) }} Seat(s): {{ implode(', ', $booking->seat_numbers) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <span class="text-sm text-gray-500">Name</span>
                                    <p class="font-semibold">{{ $primaryPassenger['name'] }}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Age</span>
                                    <p class="font-semibold">{{ $primaryPassenger['age'] }} years</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Gender</span>
                                    <p class="font-semibold">{{ ucfirst($primaryPassenger['gender']) }}</p>
                                </div>
                                @if(isset($primaryPassenger['phone']) && $primaryPassenger['phone'])
                                    <div class="md:col-span-3">
                                        <span class="text-sm text-gray-500">Phone</span>
                                        <p class="font-semibold">{{ $primaryPassenger['phone'] }}</p>
                                    </div>
                                @endif
                            </div>

                            @if(count($booking->seat_numbers) > 1)
                                <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-start space-x-3">
                                        <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <h4 class="font-medium text-blue-900">Group Booking</h4>
                                            <p class="text-sm text-blue-800 mt-1">
                                                This booking is for {{ count($booking->seat_numbers) }} seats. The primary passenger details apply to all seats.
                                                Individual passenger names can be provided at the time of travel if required.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Contact Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <span class="text-sm text-gray-500">Contact Phone</span>
                            <p class="font-semibold text-lg">{{ $booking->contact_phone }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Contact Email</span>
                            <p class="font-semibold text-lg">{{ $booking->contact_email }}</p>
                        </div>
                    </div>

                    @if($booking->special_requests)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <span class="text-sm text-gray-500">Special Requests</span>
                            <p class="font-semibold mt-1">{{ $booking->special_requests }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Payment Summary</h3>
                    
                    <!-- Price Breakdown -->
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Base Fare ({{ $booking->passenger_count }} seats):</span>
                            <span class="font-medium">Rs. {{ number_format($booking->schedule->fare * $booking->passenger_count) }}</span>
                        </div>
                        <div class="border-t pt-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total Amount:</span>
                                <span class="text-purple-600">Rs. {{ number_format($booking->total_amount) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Status -->
                    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-800">Booking Status: {{ ucfirst($booking->status) }}</p>
                                @if($booking->booking_expires_at)
                                    <p class="text-xs text-yellow-700">
                                        Expires: {{ $booking->booking_expires_at->format('M d, Y g:i A') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    @if($booking->status === 'pending')
                        <div class="space-y-3">
                            <form action="{{ route('booking.confirm', $booking) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full bg-purple-600 text-white px-6 py-4 rounded-xl hover:bg-purple-700 font-semibold transition-colors">
                                    Proceed to Payment
                                </button>
                            </form>
                            
                            <a href="{{ route('booking.passenger-details', $booking->schedule) }}" 
                               class="w-full bg-gray-100 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-200 font-semibold transition-colors text-center block">
                                Edit Details
                            </a>
                        </div>
                    @else
                        <div class="text-center">
                            <p class="text-gray-600 mb-4">This booking has been processed.</p>
                            <a href="{{ route('customer.bookings.show', $booking) }}" 
                               class="w-full bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 font-semibold transition-colors text-center block">
                                View Booking Details
                            </a>
                        </div>
                    @endif

                    <!-- Important Notes -->
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2">Important Notes:</h4>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li>• Please arrive 30 minutes before departure</li>
                            <li>• Carry a valid ID for verification</li>
                            <li>• Booking confirmation will be sent via email</li>
                            <li>• Cancellation allowed up to 2 hours before departure</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($booking->status === 'pending' && $booking->booking_expires_at)
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if booking is expired
    const expiryTime = new Date('{{ $booking->booking_expires_at->toISOString() }}');
    const now = new Date();
    
    if (now >= expiryTime) {
        alert('This booking has expired. You will be redirected to create a new booking.');
        window.location.href = '{{ route("search.index") }}';
    }
});
</script>
@endif
@endsection
