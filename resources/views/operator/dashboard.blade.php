@extends('layouts.app')

@section('title', 'Operator Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Operator Dashboard</h1>
                    <p class="text-green-100">Welcome back, {{ Auth::user()->name }}! Manage your buses and bookings.</p>
                    @if(Auth::user()->company_name)
                        <p class="text-green-200 text-sm mt-1">{{ Auth::user()->company_name }}</p>
                    @endif
                </div>
                <div class="mt-4 sm:mt-0">
                    <div class="flex items-center text-green-100">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm">{{ now()->format('l, F j, Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Buses -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Buses</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ Auth::user()->buses()->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Schedules -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Schedules</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ Auth::user()->schedules()->where('status', 'scheduled')->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Bookings -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Bookings</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ Auth::user()->operatorBookings()->whereDate('created_at', today())->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Monthly Revenue</dt>
                            <dd class="text-2xl font-bold text-gray-900">Rs. {{ number_format(Auth::user()->operatorBookings()->whereMonth('created_at', now()->month)->where('status', 'confirmed')->sum('total_amount'), 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="#" class="group bg-white hover:bg-green-50 border border-gray-200 hover:border-green-300 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 group-hover:bg-green-200 rounded-xl flex items-center justify-center mx-auto mb-3 transition-colors">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 group-hover:text-green-900">Add Bus</h3>
                </div>
            </a>

            <a href="#" class="group bg-white hover:bg-blue-50 border border-gray-200 hover:border-blue-300 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-xl flex items-center justify-center mx-auto mb-3 transition-colors">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 group-hover:text-blue-900">Create Schedule</h3>
                </div>
            </a>

            <a href="#" class="group bg-white hover:bg-yellow-50 border border-gray-200 hover:border-yellow-300 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="w-12 h-12 bg-yellow-100 group-hover:bg-yellow-200 rounded-xl flex items-center justify-center mx-auto mb-3 transition-colors">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 group-hover:text-yellow-900">View Bookings</h3>
                </div>
            </a>

            <a href="#" class="group bg-white hover:bg-purple-50 border border-gray-200 hover:border-purple-300 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-100 group-hover:bg-purple-200 rounded-xl flex items-center justify-center mx-auto mb-3 transition-colors">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 group-hover:text-purple-900">Reports</h3>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity & Today's Schedules -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Recent Bookings -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
            </div>
            <div class="p-6">
                @php
                    $recentBookings = Auth::user()->operatorBookings()->with(['user', 'schedule.route'])->orderBy('created_at', 'desc')->limit(5)->get();
                @endphp
                
                @if($recentBookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentBookings as $booking)
                            <div class="border border-gray-100 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $booking->schedule->route->name ?? 'N/A' }}</p>
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
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No bookings yet</h3>
                        <p class="mt-1 text-sm text-gray-500">Start by creating schedules for your buses.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Today's Schedules -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Today's Schedules</h3>
            </div>
            <div class="p-6">
                @php
                    $todaySchedules = Auth::user()->schedules()->with(['route', 'bus'])->where('travel_date', today())->orderBy('departure_time')->get();
                @endphp
                
                @if($todaySchedules->count() > 0)
                    <div class="space-y-4">
                        @foreach($todaySchedules as $schedule)
                            <div class="border border-gray-100 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $schedule->route->name ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-600">{{ $schedule->bus->bus_number ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">Departure: {{ $schedule->departure_time }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($schedule->status === 'scheduled') bg-blue-100 text-blue-800
                                            @elseif($schedule->status === 'completed') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                        <p class="text-sm text-gray-600 mt-1">Rs. {{ number_format($schedule->fare, 2) }}</p>
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
                        <p class="mt-1 text-sm text-gray-500">Create new schedules to start accepting bookings.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
