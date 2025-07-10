@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Welcome Section -->
    <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-gray-600">Ready to book your next journey?</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <a href="{{ url('/search') }}" class="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-lg shadow transition duration-200">
            <div class="flex items-center">
                <svg class="h-8 w-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Search Buses</h3>
                    <p class="text-blue-100">Find your perfect trip</p>
                </div>
            </div>
        </a>

        <a href="{{ url('/bookings') }}" class="bg-green-600 hover:bg-green-700 text-white p-6 rounded-lg shadow transition duration-200">
            <div class="flex items-center">
                <svg class="h-8 w-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">My Bookings</h3>
                    <p class="text-green-100">View your tickets</p>
                </div>
            </div>
        </a>

        <a href="{{ url('/profile') }}" class="bg-purple-600 hover:bg-purple-700 text-white p-6 rounded-lg shadow transition duration-200">
            <div class="flex items-center">
                <svg class="h-8 w-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Profile</h3>
                    <p class="text-purple-100">Manage your account</p>
                </div>
            </div>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Trips -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Upcoming Trips</h3>
                @if($upcomingTrips->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingTrips as $booking)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $booking->schedule->route->full_name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $booking->schedule->bus->display_name }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $booking->schedule->travel_date->format('M d, Y') }} at 
                                            {{ $booking->schedule->departure_time->format('h:i A') }}
                                        </p>
                                        <p class="text-sm text-gray-500">Seats: {{ $booking->seat_numbers_string }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No upcoming trips. <a href="{{ url('/search') }}" class="text-blue-600 hover:text-blue-500">Book your next journey!</a></p>
                @endif
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Bookings</h3>
                @if($recentBookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentBookings as $booking)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $booking->schedule->route->full_name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $booking->booking_reference }}</p>
                                        <p class="text-sm text-gray-500">
                                            Booked on {{ $booking->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">No bookings yet. <a href="{{ url('/search') }}" class="text-blue-600 hover:text-blue-500">Start exploring!</a></p>
                @endif
            </div>
        </div>
    </div>

    <!-- Popular Routes -->
    @if($popularRoutes->count() > 0)
        <div class="bg-white overflow-hidden shadow rounded-lg mt-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Popular Routes</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($popularRoutes as $route)
                        <a href="{{ url('/search?route=' . $route->id) }}" class="block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:shadow-md transition duration-200">
                            <h4 class="font-semibold text-gray-900">{{ $route->full_name }}</h4>
                            <p class="text-sm text-gray-600">Starting from NPR {{ number_format($route->base_fare) }}</p>
                            <p class="text-xs text-gray-500">{{ $route->distance_km }} km</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
