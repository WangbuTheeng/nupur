@extends('layouts.app')

@section('title', 'Counter Booking')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Counter Booking</h1>
                    <p class="text-blue-100">Manual booking system for walk-in customers</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('operator.counter.search') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Booking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Today's Schedules -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Schedules</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['today_schedules'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Bookings -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Bookings</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['today_bookings'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Revenue</dt>
                            <dd class="text-2xl font-bold text-gray-900">Rs. {{ number_format($stats['today_revenue'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Bookings -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Bookings</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['pending_bookings'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-8">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('operator.counter.search') }}" class="group bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 border border-blue-200 p-6 rounded-xl transition-all duration-200">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-blue-900">Search & Book</h3>
                        <p class="text-xs text-blue-700 mt-1">Find available schedules</p>
                    </div>
                </a>

                <a href="{{ route('operator.bookings.today') }}" class="group bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 border border-green-200 p-6 rounded-xl transition-all duration-200">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-green-900">Today's Bookings</h3>
                        <p class="text-xs text-green-700 mt-1">View today's bookings</p>
                    </div>
                </a>

                <a href="{{ route('operator.schedules.index') }}" class="group bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 border border-purple-200 p-6 rounded-xl transition-all duration-200">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10m6-10v10m-6 0h6"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-purple-900">Schedules</h3>
                        <p class="text-xs text-purple-700 mt-1">Manage schedules</p>
                    </div>
                </a>

                <a href="{{ route('operator.reports.index') }}" class="group bg-gradient-to-r from-yellow-50 to-yellow-100 hover:from-yellow-100 hover:to-yellow-200 border border-yellow-200 p-6 rounded-xl transition-all duration-200">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-yellow-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-yellow-900">Reports</h3>
                        <p class="text-xs text-yellow-700 mt-1">View analytics</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Today's Schedules & Recent Bookings -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Today's Schedules -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Today's Schedules</h3>
            </div>
            <div class="p-6">
                @if($todaySchedules->count() > 0)
                    <div class="space-y-4">
                        @foreach($todaySchedules as $schedule)
                            <div class="border border-gray-100 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $schedule->route->name ?? 'N/A' }}</h4>
                                        <p class="text-sm text-gray-600">{{ $schedule->bus->bus_number ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">
                                            Departure: {{ $schedule->departure_time }} | 
                                            Available: {{ $schedule->available_seats }} seats
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($schedule->status === 'scheduled') bg-blue-100 text-blue-800
                                            @elseif($schedule->status === 'completed') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                        <p class="text-sm font-medium text-gray-900 mt-1">Rs. {{ number_format($schedule->fare, 2) }}</p>
                                        @if($schedule->available_seats > 0)
                                            <a href="{{ route('operator.counter.book', $schedule) }}" class="text-xs text-blue-600 hover:text-blue-900 mt-1 block">Book Now</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No schedules today</h3>
                        <p class="mt-1 text-sm text-gray-500">Create schedules to start accepting bookings.</p>
                        <div class="mt-4">
                            <a href="{{ route('operator.schedules.create') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Create Schedule
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Counter Bookings -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Counter Bookings</h3>
            </div>
            <div class="p-6">
                @if($recentBookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentBookings as $booking)
                            <div class="border border-gray-100 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $booking->booking_reference }}</h4>
                                        <p class="text-sm text-gray-600">{{ $booking->passenger_details[0]['name'] ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->schedule->route->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->created_at->format('M j, Y g:i A') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                        <p class="text-sm font-medium text-gray-900 mt-1">Rs. {{ number_format($booking->total_amount, 2) }}</p>
                                        <a href="{{ route('operator.counter.receipt', $booking) }}" class="text-xs text-blue-600 hover:text-blue-900 mt-1 block">View Receipt</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No counter bookings yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Start by creating your first counter booking.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
