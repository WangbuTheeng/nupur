@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Admin Dashboard</h1>
                    <p class="text-blue-100">Welcome back! Here's what's happening with BookNGo today.</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <div class="flex items-center text-blue-100">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                        {{ now()->format('l, F j, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Today's Bookings -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Bookings</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $todayBookings }}</dd>
                            <dd class="text-xs text-green-600 font-medium">+12% from yesterday</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Revenue</dt>
                            <dd class="text-2xl font-bold text-gray-900">NPR {{ number_format($todayRevenue) }}</dd>
                            <dd class="text-xs text-green-600 font-medium">+8% from yesterday</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Bookings -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Monthly Bookings</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $monthlyBookings }}</dd>
                            <dd class="text-xs text-green-600 font-medium">+15% from last month</dd>
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
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Monthly Revenue</dt>
                            <dd class="text-2xl font-bold text-gray-900">NPR {{ number_format($monthlyRevenue) }}</dd>
                            <dd class="text-xs text-green-600 font-medium">+22% from last month</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-6">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            <a href="{{ url('/admin/buses') }}" class="group bg-white hover:bg-blue-50 border border-gray-200 hover:border-blue-300 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-xl flex items-center justify-center mx-auto mb-3 transition-colors">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">Manage Buses</h3>
                    <p class="text-xs text-gray-500 mt-1">Add, edit, delete buses</p>
                </div>
            </a>

            <a href="{{ url('/admin/routes') }}" class="group bg-white hover:bg-green-50 border border-gray-200 hover:border-green-300 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 group-hover:bg-green-200 rounded-xl flex items-center justify-center mx-auto mb-3 transition-colors">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-green-600 transition-colors">Manage Routes</h3>
                    <p class="text-xs text-gray-500 mt-1">Configure bus routes</p>
                </div>
            </a>

            <a href="{{ url('/admin/schedules') }}" class="group bg-white hover:bg-purple-50 border border-gray-200 hover:border-purple-300 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-100 group-hover:bg-purple-200 rounded-xl flex items-center justify-center mx-auto mb-3 transition-colors">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">Manage Schedules</h3>
                    <p class="text-xs text-gray-500 mt-1">Set trip schedules</p>
                </div>
            </a>

            <a href="{{ url('/admin/bookings') }}" class="group bg-white hover:bg-yellow-50 border border-gray-200 hover:border-yellow-300 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="w-12 h-12 bg-yellow-100 group-hover:bg-yellow-200 rounded-xl flex items-center justify-center mx-auto mb-3 transition-colors">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-yellow-600 transition-colors">View Bookings</h3>
                    <p class="text-xs text-gray-500 mt-1">Monitor all bookings</p>
                </div>
            </a>

            <a href="{{ url('/admin/operators') }}" class="group bg-white hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="w-12 h-12 bg-indigo-100 group-hover:bg-indigo-200 rounded-xl flex items-center justify-center mx-auto mb-3 transition-colors">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Manage Operators</h3>
                    <p class="text-xs text-gray-500 mt-1">Bus company accounts</p>
                </div>
            </a>

            <a href="{{ url('/admin/users') }}" class="group bg-white hover:bg-pink-50 border border-gray-200 hover:border-pink-300 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">
                <div class="text-center">
                    <div class="w-12 h-12 bg-pink-100 group-hover:bg-pink-200 rounded-xl flex items-center justify-center mx-auto mb-3 transition-colors">
                        <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 group-hover:text-pink-600 transition-colors">Manage Users</h3>
                    <p class="text-xs text-gray-500 mt-1">Customer accounts</p>
                </div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Recent Bookings -->
        <div class="xl:col-span-2 bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                    <a href="{{ url('/admin/bookings') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View all</a>
                </div>
            </div>
            <div class="p-6">
                @if($recentBookings->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentBookings->take(5) as $booking)
                            <div class="border border-gray-100 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-blue-600 font-semibold text-sm">{{ substr($booking->user->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $booking->booking_reference }}</h4>
                                                <p class="text-sm text-gray-600">{{ $booking->user->name }}</p>
                                            </div>
                                        </div>
                                        <div class="mt-3 ml-13">
                                            <p class="text-sm text-gray-700 font-medium">{{ $booking->schedule->route->full_name }}</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <p class="text-sm text-gray-500">NPR {{ number_format($booking->total_amount) }}</p>
                                                <p class="text-xs text-gray-400">{{ $booking->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
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
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-500 mt-2">No bookings yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Today's Schedules -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Today's Schedules</h3>
                    <a href="{{ url('/admin/schedules') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View all</a>
                </div>
            </div>
            <div class="p-6">
                @if($todaySchedules->count() > 0)
                    <div class="space-y-4">
                        @foreach($todaySchedules->take(5) as $schedule)
                            <div class="border border-gray-100 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="space-y-2">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-semibold text-gray-900 text-sm">{{ $schedule->route->full_name }}</h4>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            @if($schedule->status === 'scheduled') bg-blue-100 text-blue-800
                                            @elseif($schedule->status === 'boarding') bg-yellow-100 text-yellow-800
                                            @elseif($schedule->status === 'departed') bg-green-100 text-green-800
                                            @elseif($schedule->status === 'arrived') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $schedule->bus->display_name }}</p>
                                    <div class="flex justify-between items-center text-xs text-gray-500">
                                        <span>{{ $schedule->departure_time->format('h:i A') }}</span>
                                        <span>{{ $schedule->available_seats }} seats</span>
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
                        <p class="text-gray-500 mt-2">No schedules for today.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
