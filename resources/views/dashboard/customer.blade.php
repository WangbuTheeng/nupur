@extends('layouts.app')

@section('title', 'Customer Dashboard')

@push('meta')
<meta name="user-id" content="{{ auth()->id() }}">
@endpush

@section('content')

<!-- Hero Section with Gradient Background -->
<div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white mt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col lg:flex-row items-center justify-between">
            <div class="text-center lg:text-left mb-8 lg:mb-0">
                <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                    Welcome back, <span class="text-blue-200">{{ auth()->user()->name }}!</span>
                </h1>
                <p class="text-xl text-blue-100 mb-6 max-w-2xl">
                    Your journey starts here. Manage your bookings, discover new destinations, and travel with confidence.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('search.index') }}"
                       class="bg-white text-blue-700 px-8 py-4 rounded-xl font-semibold hover:bg-blue-50 transition duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Book New Trip
                    </a>
                    <a href="{{ route('customer.bookings.index') }}"
                       class="bg-blue-500 bg-opacity-20 backdrop-blur-sm border border-blue-300 text-white px-8 py-4 rounded-xl font-semibold hover:bg-opacity-30 transition duration-300">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        View Bookings
                    </a>
                </div>
            </div>
            <div class="hidden lg:block">
                <div class="relative">
                    <div class="w-64 h-64 bg-white bg-opacity-10 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-32 h-32 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-2a2 2 0 00-2-2H8z"></path>
                        </svg>
                    </div>
                    <div class="absolute -top-4 -right-4 w-8 h-8 bg-yellow-400 rounded-full animate-pulse"></div>
                    <div class="absolute -bottom-4 -left-4 w-6 h-6 bg-green-400 rounded-full animate-pulse delay-1000"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">

    <!-- Quick Search Widget -->
    <div class="mb-12">
        <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900">Quick Search & Book</h3>
                <p class="text-gray-600 ml-4">Find your perfect bus journey in seconds</p>
            </div>

            <!-- Quick Search Form -->
            <form method="POST" action="{{ route('search.results') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4" id="quick-search-form">
                @csrf
                <div>
                    <label for="quick_source_city_id" class="block text-sm font-medium text-gray-700 mb-2">From</label>
                    <select name="source_city_id" id="quick_source_city_id" required
                            class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select departure</option>
                        @foreach(\App\Models\City::active()->withActiveRoutes()->orderBy('name')->get()->unique('name') as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Swap Button -->
                <div class="flex items-end justify-center">
                    <button type="button" id="swap-cities-btn"
                            class="p-2 bg-blue-100 hover:bg-blue-200 rounded-lg transition-colors duration-200 group">
                        <svg class="w-5 h-5 text-blue-600 group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </button>
                </div>

                <div>
                    <label for="quick_destination_city_id" class="block text-sm font-medium text-gray-700 mb-2">To</label>
                    <select name="destination_city_id" id="quick_destination_city_id" required
                            class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">Select destination</option>
                        @foreach(\App\Models\City::active()->withActiveRoutes()->orderBy('name')->get()->unique('name') as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="quick_travel_date" class="block text-sm font-medium text-gray-700 mb-2">Travel Date</label>
                    <input type="date" name="travel_date" id="quick_travel_date" required
                           min="{{ date('Y-m-d') }}"
                           value="{{ date('Y-m-d') }}"
                           class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>

                <div>
                    <label for="quick_passengers" class="block text-sm font-medium text-gray-700 mb-2">Passengers</label>
                    <select name="passengers" id="quick_passengers"
                            class="block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="1">1 Passenger</option>
                        <option value="2">2 Passengers</option>
                        <option value="3">3 Passengers</option>
                        <option value="4">4 Passengers</option>
                        <option value="5">5 Passengers</option>
                        <option value="6">6 Passengers</option>
                        <option value="7">7 Passengers</option>
                        <option value="8">8 Passengers</option>
                        <option value="9">9 Passengers</option>
                        <option value="10">10 Passengers</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards with Modern Design -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <!-- Total Bookings Card -->
        <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Bookings</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_bookings'] }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-green-600 font-medium">+12% from last month</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Confirmed Trips Card -->
        <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Confirmed Trips</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['confirmed_bookings'] }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-green-600 font-medium">{{ $stats['confirmed_bookings'] > 0 ? '100% success rate' : 'Start booking!' }}</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Upcoming Trips Card -->
        <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Upcoming Trips</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['upcoming_trips'] }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-orange-600 font-medium">{{ $stats['upcoming_trips'] > 0 ? 'Ready to travel' : 'Plan your next trip' }}</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Spent Card -->
        <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Spent</h3>
                    <p class="text-3xl font-bold text-gray-900 mt-2">NRs {{ number_format($stats['total_spent']) }}</p>
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-purple-600 font-medium">Travel investment</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Upcoming Trips -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Upcoming Trips</h2>
                    </div>
                    <a href="{{ route('customer.bookings.upcoming') }}" class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-100 text-sm font-semibold transition-colors">View all</a>
                </div>

                @if($upcomingTrips->count() > 0)
                    <div class="space-y-6">
                        @foreach($upcomingTrips->take(3) as $booking)
                            <div class="bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                    <div class="flex-1 mb-4 lg:mb-0">
                                        <div class="flex items-start justify-between mb-4">
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $booking->schedule->route->full_name }}</h3>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                Confirmed
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-xs text-gray-500 font-medium">Date</p>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $booking->schedule->travel_date->format('M d, Y') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-xs text-gray-500 font-medium">Time</p>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $booking->schedule->departure_time->format('h:i A') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-xs text-gray-500 font-medium">Seats</p>
                                                    <p class="text-sm font-semibold text-gray-900">{{ implode(', ', $booking->seat_numbers) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-2a2 2 0 00-2-2H8z"></path>
                                                </svg>
                                                <div>
                                                    <p class="text-xs text-gray-500 font-medium">Bus</p>
                                                    <p class="text-sm font-semibold text-gray-900">{{ $booking->schedule->bus->display_name }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col lg:items-end lg:text-right">
                                        <p class="text-2xl font-bold text-gray-900 mb-4">Rs. {{ number_format($booking->total_amount) }}</p>
                                        <div class="flex space-x-3">
                                            <a href="{{ route('customer.bookings.show', $booking) }}"
                                               class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-semibold transition-colors">
                                                View Details
                                            </a>
                                            <a href="{{ route('customer.tickets.show', $booking) }}"
                                               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 text-sm font-semibold transition-colors">
                                                Download Ticket
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No upcoming trips</h3>
                        <p class="text-gray-500 mb-8 max-w-md mx-auto">Ready for your next adventure? Discover amazing destinations and book your perfect trip today!</p>
                        <a href="{{ route('search.index') }}"
                           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Book Your Next Trip
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions & Popular Routes -->
        <div class="space-y-8">
            <!-- Quick Actions -->
            <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Quick Actions</h3>
                </div>
                <div class="space-y-4">
                    <a href="{{ route('search.index') }}"
                       class="group flex items-center p-4 rounded-xl bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 hover:from-blue-100 hover:to-blue-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 group-hover:text-blue-700">Search Buses</h4>
                            <p class="text-sm text-gray-600">Find and book tickets instantly</p>
                        </div>
                        <svg class="w-5 h-5 text-blue-600 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <a href="{{ route('customer.bookings.index') }}"
                       class="group flex items-center p-4 rounded-xl bg-gradient-to-r from-green-50 to-green-100 border border-green-200 hover:from-green-100 hover:to-green-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 group-hover:text-green-700">My Bookings</h4>
                            <p class="text-sm text-gray-600">View all your reservations</p>
                        </div>
                        <svg class="w-5 h-5 text-green-600 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <a href="{{ route('customer.payments.history') }}"
                       class="group flex items-center p-4 rounded-xl bg-gradient-to-r from-purple-50 to-purple-100 border border-purple-200 hover:from-purple-100 hover:to-purple-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 group-hover:text-purple-700">Payment History</h4>
                            <p class="text-sm text-gray-600">Track your transactions</p>
                        </div>
                        <svg class="w-5 h-5 text-purple-600 ml-auto group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Popular Routes -->
            <div class="bg-white shadow-xl rounded-2xl p-6 border border-gray-100">
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mr-4">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Popular Routes</h3>
                </div>
                @if($popularRoutes->count() > 0)
                    <div class="space-y-4">
                        @foreach($popularRoutes->take(4) as $route)
                            <a href="{{ route('search.route', $route) }}"
                               class="group block p-4 rounded-xl bg-gradient-to-r from-gray-50 to-white border border-gray-200 hover:from-orange-50 hover:to-orange-100 hover:border-orange-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 group-hover:text-orange-700 mb-1">{{ $route->full_name }}</h4>
                                        <div class="flex items-center text-sm text-gray-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $route->schedules_count }} trips available
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold text-orange-600">Rs. {{ number_format($route->base_fare) }}</p>
                                        <p class="text-xs text-gray-500">Starting from</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm">No popular routes available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    @if($recentBookings->count() > 0)
        <div class="mt-12">
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-gray-600 to-gray-700 rounded-xl flex items-center justify-center mr-4">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Recent Bookings</h2>
                    </div>
                    <a href="{{ route('customer.bookings.history') }}" class="bg-gray-50 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 text-sm font-semibold transition-colors">View all</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Route & Reference</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Travel Date</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($recentBookings as $booking)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-6">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 mb-1">{{ $booking->schedule->route->full_name }}</div>
                                            <div class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-md inline-block">{{ $booking->booking_reference }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->schedule->travel_date->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $booking->schedule->departure_time->format('h:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border
                                            @if($booking->status === 'confirmed') bg-green-100 text-green-800 border-green-200
                                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800 border-yellow-200
                                            @elseif($booking->status === 'cancelled') bg-red-100 text-red-800 border-red-200
                                            @else bg-gray-100 text-gray-800 border-gray-200 @endif">
                                            @if($booking->status === 'confirmed')
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            @endif
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-6">
                                        <div class="text-lg font-bold text-gray-900">Rs. {{ number_format($booking->total_amount) }}</div>
                                        <div class="text-xs text-gray-500">{{ count($booking->seat_numbers) }} seat(s)</div>
                                    </td>
                                    <td class="px-6 py-6">
                                        <a href="{{ route('customer.bookings.show', $booking) }}"
                                           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-semibold transition-colors">
                                            View Details
                                        </a>
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

    // Add smooth scroll behavior for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading states for action buttons
    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', function() {
            if (!this.href.includes('#')) {
                this.style.opacity = '0.7';
                this.style.pointerEvents = 'none';
                setTimeout(() => {
                    this.style.opacity = '1';
                    this.style.pointerEvents = 'auto';
                }, 2000);
            }
        });
    });
});
</script>

@push('styles')
<style>
    /* Custom animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Custom gradient backgrounds */
    .bg-gradient-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Hover effects */
    .hover-lift:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease-in-out;
    }

    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick search form functionality
    const swapBtn = document.getElementById('swap-cities-btn');
    const sourceSelect = document.getElementById('quick_source_city_id');
    const destinationSelect = document.getElementById('quick_destination_city_id');
    const searchForm = document.getElementById('quick-search-form');

    // Swap cities functionality
    if (swapBtn && sourceSelect && destinationSelect) {
        swapBtn.addEventListener('click', function() {
            const sourceValue = sourceSelect.value;
            const destinationValue = destinationSelect.value;

            sourceSelect.value = destinationValue;
            destinationSelect.value = sourceValue;
        });
    }

    // Form validation
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const source = sourceSelect.value;
            const destination = destinationSelect.value;

            if (source === destination && source !== '') {
                e.preventDefault();
                alert('Please select different cities for departure and destination.');
                return false;
            }

            if (!source || !destination) {
                e.preventDefault();
                alert('Please select both departure and destination cities.');
                return false;
            }
        });
    }

    // Auto-update destination options based on source selection (optional enhancement)
    if (sourceSelect && destinationSelect) {
        sourceSelect.addEventListener('change', function() {
            const selectedSource = this.value;

            // Reset destination if same as source
            if (destinationSelect.value === selectedSource) {
                destinationSelect.value = '';
            }
        });

        destinationSelect.addEventListener('change', function() {
            const selectedDestination = this.value;

            // Reset source if same as destination
            if (sourceSelect.value === selectedDestination) {
                sourceSelect.value = '';
            }
        });
    }
});
</script>

@endsection
