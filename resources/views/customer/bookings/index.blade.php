@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">My Bookings</h1>
                <p class="text-xl text-blue-100 max-w-2xl mx-auto">
                    Track and manage all your bus reservations in one place
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <!-- Filter Tabs -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('customer.bookings.index') }}" 
                   class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 {{ !request('status') ? 'bg-blue-600 text-white shadow-lg' : 'bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    All Bookings
                </a>
                <a href="{{ route('customer.bookings.index', ['status' => 'confirmed']) }}" 
                   class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 {{ request('status') === 'confirmed' ? 'bg-green-600 text-white shadow-lg' : 'bg-gray-100 text-gray-600 hover:bg-green-50 hover:text-green-600' }}">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Confirmed
                </a>
                <a href="{{ route('customer.bookings.index', ['status' => 'pending']) }}" 
                   class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 {{ request('status') === 'pending' ? 'bg-yellow-600 text-white shadow-lg' : 'bg-gray-100 text-gray-600 hover:bg-yellow-50 hover:text-yellow-600' }}">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Pending
                </a>
                <a href="{{ route('customer.bookings.index', ['status' => 'cancelled']) }}" 
                   class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 {{ request('status') === 'cancelled' ? 'bg-red-600 text-white shadow-lg' : 'bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelled
                </a>
            </div>
        </div>

        <!-- Bookings List -->
        @if($bookings->count() > 0)
            <div class="space-y-6">
                @foreach($bookings as $booking)
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-8">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                <!-- Booking Info -->
                                <div class="flex-1 mb-6 lg:mb-0">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                                {{ $booking->schedule->route->full_name }}
                                            </h3>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span class="bg-gray-100 px-3 py-1 rounded-lg font-medium">
                                                    {{ $booking->booking_reference }}
                                                </span>
                                                <span>{{ $booking->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border
                                            @if($booking->status === 'confirmed') bg-green-100 text-green-800 border-green-200
                                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800 border-yellow-200
                                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800 border-red-200
                                            @else bg-gray-100 text-gray-800 border-gray-200 @endif">
                                            @if($booking->status === 'confirmed')
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            @elseif($booking->status === 'pending')
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @elseif($booking->status === 'cancelled')
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            @endif
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </div>

                                    <!-- Trip Details Grid -->
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Travel Date</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $booking->schedule->travel_date->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Departure</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('h:i A') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Seats</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ implode(', ', $booking->seat_numbers) }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Passengers</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $booking->passenger_count }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price and Actions -->
                                <div class="flex flex-col lg:items-end lg:text-right">
                                    <p class="text-3xl font-bold text-gray-900 mb-6">Rs. {{ number_format($booking->total_amount) }}</p>
                                    <div class="flex flex-col sm:flex-row lg:flex-col space-y-3 sm:space-y-0 sm:space-x-3 lg:space-x-0 lg:space-y-3">
                                        <a href="{{ route('customer.bookings.show', $booking) }}" 
                                           class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 text-sm font-semibold transition-colors text-center">
                                            View Details
                                        </a>
                                        @if($booking->status === 'confirmed')
                                            <a href="{{ route('customer.tickets.show', $booking) }}" 
                                               class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 text-sm font-semibold transition-colors text-center">
                                                Download Ticket
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($bookings->hasPages())
                <div class="mt-12">
                    {{ $bookings->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-16 text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-8">
                    <svg class="h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">No bookings found</h3>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">
                    @if(request('status'))
                        You don't have any {{ request('status') }} bookings yet.
                    @else
                        You haven't made any bookings yet. Start your journey today!
                    @endif
                </p>
                <a href="{{ route('search.index') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Book Your First Trip
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
