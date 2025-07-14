@extends('layouts.app')

@section('title', $route->full_name . ' - Route Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl lg:text-5xl font-bold mb-4">{{ $route->full_name }}</h1>
                <p class="text-xl text-blue-100 mb-6">
                    {{ $route->distance_km }} km • {{ $route->estimated_duration->format('H:i') }} hours journey
                </p>
                <div class="flex flex-wrap justify-center gap-4 text-sm">
                    <span class="bg-blue-500 bg-opacity-30 px-4 py-2 rounded-full">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ $stats['total_schedules'] }} Total Schedules
                    </span>
                    <span class="bg-blue-500 bg-opacity-30 px-4 py-2 rounded-full">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        {{ $stats['active_operators'] }} Operators
                    </span>
                    <span class="bg-blue-500 bg-opacity-30 px-4 py-2 rounded-full">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                        From Rs. {{ number_format($stats['min_fare']) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <!-- Quick Search -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Book This Route</h2>
            <form action="{{ route('search.results') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="source_city_id" value="{{ $route->source_city_id }}">
                <input type="hidden" name="destination_city_id" value="{{ $route->destination_city_id }}">
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Travel Date</label>
                    <input type="date" 
                           name="travel_date" 
                           value="{{ date('Y-m-d') }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Passengers</label>
                    <select name="passengers" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }} Passenger{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                
                <div class="md:col-span-2 flex items-end">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-blue-800 font-semibold transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search Available Buses
                    </button>
                </div>
            </form>
        </div>

        <!-- Route Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Total Schedules</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_schedules'] }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Active Operators</h3>
                <p class="text-3xl font-bold text-green-600">{{ $stats['active_operators'] }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Starting From</h3>
                <p class="text-3xl font-bold text-orange-600">Rs. {{ number_format($stats['min_fare']) }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Average Fare</h3>
                <p class="text-3xl font-bold text-purple-600">Rs. {{ number_format($stats['average_fare']) }}</p>
            </div>
        </div>

        <!-- Available Schedules -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Upcoming Schedules</h2>
                </div>
                <span class="text-sm text-gray-500">{{ $schedules->total() }} schedules found</span>
            </div>

            @if($schedules->count() > 0)
                <div class="space-y-6">
                    @foreach($schedules as $schedule)
                        <div class="border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                <div class="flex-1 mb-4 lg:mb-0">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $schedule->bus->display_name }}</h3>
                                            <p class="text-gray-600">{{ $schedule->operator->company_name ?? $schedule->operator->name }}</p>
                                        </div>
                                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            {{ $schedule->bus->busType->name ?? 'Standard' }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Date</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $schedule->travel_date->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Departure</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $schedule->departure_time->format('h:i A') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Available Seats</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $schedule->available_seats }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Fare</p>
                                                <p class="text-sm font-semibold text-gray-900">Rs. {{ number_format($schedule->fare) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col lg:items-end lg:text-right">
                                    <p class="text-2xl font-bold text-gray-900 mb-4">Rs. {{ number_format($schedule->fare) }}</p>
                                    <div class="flex space-x-3">
                                        <a href="{{ route('search.schedule', $schedule) }}" 
                                           class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 text-sm font-semibold transition-colors">
                                            View Details
                                        </a>
                                        @if($schedule->available_seats > 0)
                                            <a href="{{ route('booking.seat-selection', $schedule) }}" 
                                               class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 text-sm font-semibold transition-colors">
                                                Book Now
                                            </a>
                                        @else
                                            <span class="bg-gray-300 text-gray-500 px-6 py-3 rounded-xl text-sm font-semibold cursor-not-allowed">
                                                Sold Out
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($schedules->hasPages())
                    <div class="mt-8">
                        {{ $schedules->links() }}
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-8">
                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No schedules available</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto">
                        There are currently no upcoming schedules for this route. Please check back later or try a different date.
                    </p>
                    <a href="{{ route('search.index') }}" 
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search Other Routes
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
