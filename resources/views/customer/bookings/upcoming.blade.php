@extends('layouts.app')

@section('title', 'Upcoming Trips')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-green-600 via-green-700 to-emerald-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Upcoming Trips</h1>
                <p class="text-xl text-green-100 max-w-2xl mx-auto">
                    Your confirmed bookings for future travel dates
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <!-- Navigation Tabs -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mb-8">
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('customer.bookings.index') }}" 
                   class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    All Bookings
                </a>
                <a href="{{ route('customer.bookings.upcoming') }}" 
                   class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 bg-green-600 text-white shadow-lg">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10m6-10v10m-6 0h6"></path>
                    </svg>
                    Upcoming Trips
                </a>
                <a href="{{ route('customer.bookings.history') }}" 
                   class="px-6 py-3 rounded-xl font-semibold transition-all duration-200 bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600">
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Travel History
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
                                                {{ $booking->schedule->route->sourceCity->name }} → {{ $booking->schedule->route->destinationCity->name }}
                                            </h3>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span class="bg-gray-100 px-3 py-1 rounded-lg font-medium">
                                                    {{ $booking->booking_reference }}
                                                </span>
                                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-lg font-medium">
                                                    Confirmed
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Trip Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-blue-100 p-3 rounded-xl">
                                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10m6-10v10m-6 0h6"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Travel Date</p>
                                                <p class="font-semibold text-gray-900">{{ $booking->schedule->travel_date->format('M d, Y') }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-3">
                                            <div class="bg-green-100 p-3 rounded-xl">
                                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Departure</p>
                                                <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('g:i A') }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-3">
                                            <div class="bg-purple-100 p-3 rounded-xl">
                                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500">Passengers</p>
                                                <p class="font-semibold text-gray-900">{{ $booking->passenger_count }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Seat Numbers -->
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-500 mb-2">Seat Numbers</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($booking->seat_numbers as $seatNumber)
                                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-lg text-sm font-medium">
                                                    {{ $seatNumber }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Bus Info -->
                                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                                            </svg>
                                            {{ $booking->schedule->operator->company_name ?? $booking->schedule->operator->name }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            {{ $booking->schedule->bus->bus_number }}
                                        </span>
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            {{ $booking->schedule->bus->busType->name ?? 'Standard' }}
                                        </span>
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
                                        <a href="{{ route('customer.tickets.show', $booking) }}" 
                                           class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 text-sm font-semibold transition-colors text-center">
                                            Download Ticket
                                        </a>
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
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-12 text-center">
                <div class="max-w-md mx-auto">
                    <svg class="w-24 h-24 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10m6-10v10m-6 0h6"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">No Upcoming Trips</h3>
                    <p class="text-gray-600 mb-8">You don't have any confirmed bookings for future travel dates.</p>
                    <a href="{{ route('search.index') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-semibold transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search for Buses
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
