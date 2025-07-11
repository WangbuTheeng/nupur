@extends('layouts.app')

@section('title', 'Customer Dashboard')

@push('meta')
<meta name="user-id" content="{{ auth()->id() }}">
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h1>
                <p class="text-gray-600 mt-2">Manage your bookings and discover new destinations</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('search.index') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                    Book New Trip
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Bookings</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Confirmed Trips</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['confirmed_bookings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Upcoming Trips</h3>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['upcoming_trips'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-500">Total Spent</h3>
                    <p class="text-2xl font-bold text-gray-900">Rs. {{ number_format($stats['total_spent']) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Upcoming Trips -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Upcoming Trips</h2>
                    <a href="{{ route('bookings.upcoming') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View all</a>
                </div>
                
                @if($upcomingTrips->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingTrips->take(3) as $booking)
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">{{ $booking->schedule->route->full_name }}</h3>
                                        <div class="mt-2 space-y-1">
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Date:</span> {{ $booking->schedule->travel_date->format('M d, Y') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Time:</span> {{ $booking->schedule->departure_time->format('h:i A') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Seats:</span> {{ implode(', ', $booking->seat_numbers) }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Bus:</span> {{ $booking->schedule->bus->display_name }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Confirmed
                                        </span>
                                        <p class="text-lg font-bold text-gray-900 mt-2">Rs. {{ number_format($booking->total_amount) }}</p>
                                        <div class="mt-2 space-x-2">
                                            <a href="{{ route('bookings.show', $booking) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                            <a href="{{ route('tickets.show', $booking) }}" 
                                               class="text-green-600 hover:text-green-800 text-sm">Ticket</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No upcoming trips</h3>
                        <p class="mt-1 text-sm text-gray-500">Book your next adventure!</p>
                        <div class="mt-6">
                            <a href="{{ route('search.index') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Book a Trip
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions & Popular Routes -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="{{ route('search.index') }}" 
                       class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-blue-50 hover:border-blue-300 transition-colors">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Search Buses</h4>
                            <p class="text-sm text-gray-500">Find and book tickets</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('bookings.index') }}" 
                       class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-green-50 hover:border-green-300 transition-colors">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">My Bookings</h4>
                            <p class="text-sm text-gray-500">View all bookings</p>
                        </div>
                    </a>
                    
                    <a href="{{ route('payments.history') }}" 
                       class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-purple-50 hover:border-purple-300 transition-colors">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Payment History</h4>
                            <p class="text-sm text-gray-500">View transactions</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Popular Routes -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Popular Routes</h3>
                @if($popularRoutes->count() > 0)
                    <div class="space-y-3">
                        @foreach($popularRoutes->take(4) as $route)
                            <a href="{{ route('search.route', $route) }}" 
                               class="block p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                                <h4 class="font-medium text-gray-900">{{ $route->full_name }}</h4>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-sm text-gray-500">{{ $route->schedules_count }} trips available</p>
                                    <p class="text-sm font-medium text-blue-600">From Rs. {{ number_format($route->base_fare) }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">No popular routes available</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    @if($recentBookings->count() > 0)
        <div class="mt-8">
            <div class="bg-white shadow-lg rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Bookings</h2>
                    <a href="{{ route('bookings.history') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View all</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentBookings as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->schedule->route->full_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->booking_reference }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $booking->schedule->travel_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Rs. {{ number_format($booking->total_amount) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize real-time dashboard if available
    if (typeof window.RealtimeDashboard !== 'undefined') {
        window.customerDashboard = new RealtimeDashboard({
            updateInterval: 60000, // 1 minute for customer dashboard
            chartUpdateInterval: 300000, // 5 minutes
            notificationCheckInterval: 30000 // 30 seconds
        });
    }
});
</script>
@endpush

@endsection
