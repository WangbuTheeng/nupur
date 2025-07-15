@extends('layouts.app')

@section('title', 'Reserved Seats')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Reserved Seats</h1>
                <p class="text-xl text-purple-100 max-w-2xl mx-auto">
                    Your temporarily reserved seats - complete your booking before they expire
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('customer.bookings.index') }}" 
                   class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    All Bookings
                </a>
                <a href="{{ route('customer.bookings.reservations') }}" 
                   class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 bg-purple-600 text-white shadow-lg">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Reserved Seats ({{ $reservations->count() }})
                </a>
            </div>
        </div>

        <!-- Reservations List -->
        @if($reservations->count() > 0)
            <div class="space-y-6">
                @foreach($reservations as $reservation)
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-8">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                <!-- Reservation Info -->
                                <div class="flex-1 mb-6 lg:mb-0">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                                {{ $reservation->schedule->route->full_name }}
                                            </h3>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span class="bg-purple-100 px-3 py-1 rounded-lg font-medium text-purple-800">
                                                    Reserved
                                                </span>
                                                <span>{{ $reservation->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border bg-orange-100 text-orange-800 border-orange-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="countdown" data-expires="{{ $reservation->expires_at->toISOString() }}">
                                                    Expires in {{ $reservation->expires_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Trip Details Grid -->
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Travel Date</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $reservation->schedule->travel_date->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Departure</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($reservation->schedule->departure_time)->format('h:i A') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Reserved Seats</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ implode(', ', $reservation->seat_numbers) }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Total Amount</p>
                                                <p class="text-sm font-semibold text-gray-900">Rs. {{ number_format($reservation->schedule->fare * count($reservation->seat_numbers)) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex flex-col lg:items-end lg:text-right">
                                    <p class="text-3xl font-bold text-purple-600 mb-6">Rs. {{ number_format($reservation->schedule->fare * count($reservation->seat_numbers)) }}</p>
                                    <div class="flex flex-col sm:flex-row lg:flex-col space-y-3 sm:space-y-0 sm:space-x-3 lg:space-x-0 lg:space-y-3">
                                        <a href="{{ route('customer.bookings.proceed-reservation', $reservation) }}" 
                                           class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 text-sm font-semibold transition-colors text-center">
                                            Complete Booking
                                        </a>
                                        <button onclick="cancelReservation({{ $reservation->id }})" 
                                                class="bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 text-sm font-semibold transition-colors text-center">
                                            Cancel Reservation
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-16 text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center mx-auto mb-8">
                    <svg class="h-12 w-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">No Reserved Seats</h3>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">
                    You don't have any seats reserved at the moment. Reserve seats to hold them temporarily while you complete your booking.
                </p>
                <a href="{{ route('search.index') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search Buses
                </a>
            </div>
        @endif
    </div>
</div>

<script>
// Update countdown timers
function updateCountdowns() {
    document.querySelectorAll('.countdown').forEach(function(element) {
        const expiresAt = new Date(element.dataset.expires);
        const now = new Date();
        const diff = expiresAt - now;
        
        if (diff <= 0) {
            element.textContent = 'Expired';
            element.parentElement.classList.remove('bg-orange-100', 'text-orange-800', 'border-orange-200');
            element.parentElement.classList.add('bg-red-100', 'text-red-800', 'border-red-200');
            // Reload page to refresh expired reservations
            setTimeout(() => location.reload(), 2000);
        } else {
            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);
            element.textContent = `Expires in ${minutes}m ${seconds}s`;
        }
    });
}

// Update every second
setInterval(updateCountdowns, 1000);
updateCountdowns(); // Initial call

// Cancel reservation function
function cancelReservation(reservationId) {
    if (!confirm('Are you sure you want to cancel this reservation? Your seats will be released.')) {
        return;
    }
    
    fetch('{{ route("customer.bookings.cancel-reservation") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reservation_id: reservationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Failed to cancel reservation. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to cancel reservation. Please try again.');
    });
}
</script>
@endsection
