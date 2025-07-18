@extends('layouts.app')

@section('title', 'Booking Details - ' . $booking->booking_reference)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Booking Details</h1>
                    <p class="text-xl text-blue-100">{{ $booking->booking_reference }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-semibold border-2
                        @if($booking->status === 'confirmed') bg-green-100 text-green-800 border-green-300
                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800 border-yellow-300
                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800 border-red-300
                        @else bg-gray-100 text-gray-800 border-gray-300 @endif">
                        @if($booking->status === 'confirmed')
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        @endif
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Trip Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Trip Information</h2>
                    </div>

                    <div class="space-y-6">
                        <!-- Route -->
                        <div class="flex items-center justify-between p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $booking->schedule->route->full_name }}</h3>
                                <p class="text-gray-600 mt-1">{{ $booking->schedule->route->distance_km }} km • {{ $booking->schedule->route->estimated_duration->format('H:i') }} hours</p>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-blue-600">Rs. {{ number_format($booking->schedule->fare) }}</p>
                                <p class="text-sm text-gray-500">per seat</p>
                            </div>
                        </div>

                        <!-- Travel Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="p-6 bg-gray-50 rounded-xl">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <h4 class="font-semibold text-gray-900">Travel Date</h4>
                                </div>
                                <p class="text-lg font-bold text-gray-900">{{ $booking->schedule->travel_date->format('l, F d, Y') }}</p>
                            </div>

                            <div class="p-6 bg-gray-50 rounded-xl">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h4 class="font-semibold text-gray-900">Departure Time</h4>
                                </div>
                                <p class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('h:i A') }}</p>
                            </div>

                            <div class="p-6 bg-gray-50 rounded-xl">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-2a2 2 0 00-2-2H8z"></path>
                                    </svg>
                                    <h4 class="font-semibold text-gray-900">Bus Details</h4>
                                </div>
                                <p class="text-lg font-bold text-gray-900">{{ $booking->schedule->bus->display_name }}</p>
                                <p class="text-sm text-gray-600">{{ $booking->schedule->bus->busType->name ?? 'Standard' }}</p>
                            </div>

                            <div class="p-6 bg-gray-50 rounded-xl">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <h4 class="font-semibold text-gray-900">Seat Numbers</h4>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($booking->seat_numbers as $seat)
                                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg font-semibold">{{ $seat }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Passenger Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Passenger Information</h2>
                    </div>

                    @if($booking->passenger_details)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($booking->passenger_details as $index => $passenger)
                                <div class="p-6 bg-gray-50 rounded-xl">
                                    <h4 class="font-semibold text-gray-900 mb-3">Passenger {{ $index + 1 }}</h4>
                                    <div class="space-y-2">
                                        <p><span class="font-medium text-gray-600">Name:</span> {{ $passenger['name'] ?? 'N/A' }}</p>
                                        <p><span class="font-medium text-gray-600">Age:</span> {{ $passenger['age'] ?? 'N/A' }}</p>
                                        <p><span class="font-medium text-gray-600">Gender:</span> {{ ucfirst($passenger['gender'] ?? 'N/A') }}</p>
                                        @if(isset($passenger['phone']))
                                            <p><span class="font-medium text-gray-600">Phone:</span> {{ $passenger['phone'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No passenger details available.</p>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Booking Summary -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Booking Summary</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Booking Reference</span>
                            <span class="font-semibold">{{ $booking->booking_reference }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Booking Date</span>
                            <span class="font-semibold">{{ $booking->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Passengers</span>
                            <span class="font-semibold">{{ $booking->passenger_count }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fare per seat</span>
                            <span class="font-semibold">Rs. {{ number_format($booking->schedule->fare) }}</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-lg">
                            <span class="font-semibold text-gray-900">Total Amount</span>
                            <span class="font-bold text-gray-900">Rs. {{ number_format($booking->total_amount) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Actions</h3>
                    
                    <div class="space-y-4">
                        @if($booking->status === 'confirmed')
                            <a href="{{ route('customer.tickets.show', $booking) }}" 
                               class="w-full bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 font-semibold transition-colors text-center block">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Download Ticket
                            </a>
                        @endif
                        
                        <a href="{{ route('customer.bookings.index') }}" 
                           class="w-full bg-gray-100 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-200 font-semibold transition-colors text-center block">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Bookings
                        </a>

                        @if($booking->status === 'pending')
                            <a href="{{ route('payment.index', $booking) }}"
                               class="w-full bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 font-semibold transition-colors text-center block">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Proceed with Payment
                            </a>

                            <!-- Temporary Test Payment (while fixing eSewa) -->
                            <a href="{{ route('payment.test.complete', $booking) }}"
                               class="w-full bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 font-semibold transition-colors text-center block mt-3"
                               onclick="return confirm('This will complete the payment in test mode. Are you sure?')">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Complete Payment (Test Mode)
                            </a>





                            <button id="cancel-booking-btn" class="w-full bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 font-semibold transition-colors">
                                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel Booking
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Contact Information</h3>
                    
                    <div class="space-y-4">
                        @if($booking->contact_phone)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="text-gray-900">{{ $booking->contact_phone }}</span>
                            </div>
                        @endif
                        
                        @if($booking->contact_email)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-gray-900">{{ $booking->contact_email }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Booking Modal -->
@if($booking->status === 'pending')
<div id="cancel-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Cancel Booking</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to cancel this booking? This action cannot be undone.
                </p>
                <p class="text-sm text-gray-700 mt-2 font-medium">
                    Booking Reference: {{ $booking->booking_reference }}
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <div class="flex space-x-3">
                    <button id="confirm-cancel-btn" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Yes, Cancel Booking
                    </button>
                    <button id="close-modal-btn" class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        No, Keep Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
@if($booking->status === 'pending')
document.addEventListener('DOMContentLoaded', function() {
    const cancelBtn = document.getElementById('cancel-booking-btn');
    const modal = document.getElementById('cancel-modal');
    const confirmBtn = document.getElementById('confirm-cancel-btn');
    const closeBtn = document.getElementById('close-modal-btn');

    // Show modal when cancel button is clicked
    cancelBtn.addEventListener('click', function() {
        modal.classList.remove('hidden');
    });

    // Hide modal when close button is clicked
    closeBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    // Hide modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    // Handle booking cancellation
    confirmBtn.addEventListener('click', function() {
        // Show loading state
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<svg class="w-4 h-4 inline-block mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Cancelling...';

        // Send cancellation request
        fetch('{{ route("customer.bookings.cancel", $booking) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                reason: 'Customer requested cancellation'
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Show success message and redirect
                alert('Booking cancelled successfully.');
                window.location.href = '{{ route("customer.bookings.index") }}';
            } else {
                alert(data.message || 'Failed to cancel booking. Please try again.');
                // Restore button state
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = 'Yes, Cancel Booking';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again. Check console for details.');
            // Restore button state
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = 'Yes, Cancel Booking';
        });
    });
});
@endif
</script>

@endsection
