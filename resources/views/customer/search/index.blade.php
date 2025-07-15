@extends('layouts.app')

@section('title', 'Search Bus Tickets')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
    <!-- Hero Section with Search -->
    <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-green-600 opacity-10"></div>
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239C92AC" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="4"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-4">
                    Find Your Perfect
                    <span class="bg-gradient-to-r from-blue-600 to-green-600 bg-clip-text text-transparent">Bus Journey</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Book comfortable, reliable bus tickets across Nepal with BookNGO. 
                    Compare prices, choose your seats, and travel with confidence.
                </p>
            </div>

            <!-- Search Form -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
                    <form method="POST" action="{{ route('search.results') }}" x-data="{
                        swapCities() {
                            const source = this.$refs.source.value;
                            const destination = this.$refs.destination.value;
                            this.$refs.source.value = destination;
                            this.$refs.destination.value = source;
                        }
                    }">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- From City -->
                            <div class="space-y-2">
                                <label for="source_city_id" class="block text-sm font-semibold text-gray-700">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    From
                                </label>
                                <select name="source_city_id" id="source_city_id" x-ref="source" required 
                                        class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg py-3">
                                    <option value="">Select departure city</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('source_city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('source_city_id')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Swap Button -->
                            <div class="flex items-end justify-center">
                                <button type="button" @click="swapCities()" 
                                        class="p-3 bg-blue-100 hover:bg-blue-200 rounded-full transition-colors duration-200 group">
                                    <svg class="w-5 h-5 text-blue-600 group-hover:rotate-180 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- To City -->
                            <div class="space-y-2">
                                <label for="destination_city_id" class="block text-sm font-semibold text-gray-700">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    To
                                </label>
                                <select name="destination_city_id" id="destination_city_id" x-ref="destination" required 
                                        class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg py-3">
                                    <option value="">Select destination city</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('destination_city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('destination_city_id')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Travel Date -->
                            <div class="space-y-2">
                                <label for="travel_date" class="block text-sm font-semibold text-gray-700">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10m6-10v10m-6 0h6"></path>
                                    </svg>
                                    Travel Date
                                </label>
                                <input type="date" name="travel_date" id="travel_date" 
                                       value="{{ old('travel_date', date('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d') }}" required
                                       class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg py-3">
                                @error('travel_date')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Passengers and Search Button -->
                        <div class="mt-6 flex flex-col sm:flex-row items-center gap-4">
                            <div class="flex-1">
                                <label for="passengers" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    Passengers
                                </label>
                                <select name="passengers" id="passengers" 
                                        class="block w-full border-gray-300 rounded-xl shadow-sm focus:ring-blue-500 focus:border-blue-500 text-lg py-3">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ old('passengers', 1) == $i ? 'selected' : '' }}>
                                            {{ $i }} {{ $i == 1 ? 'Passenger' : 'Passengers' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            <div class="flex-shrink-0">
                                <button type="submit" 
                                        class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-blue-600 to-green-600 text-white font-bold text-lg rounded-xl hover:from-blue-700 hover:to-green-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Search Buses
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Routes Section -->
    @if($popularRoutes->count() > 0)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Popular Routes</h2>
                <p class="text-lg text-gray-600">Discover the most traveled routes across Nepal</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($popularRoutes as $route)
                    <a href="{{ route('search.route', $route) }}" 
                       class="group bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <span class="font-semibold text-gray-900">{{ $route->sourceCity->name }}</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                                <div class="flex items-center space-x-3">
                                    <span class="font-semibold text-gray-900">{{ $route->destinationCity->name }}</span>
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <span>{{ $route->distance ?? 'N/A' }} km</span>
                                <span>From Rs. {{ number_format($route->base_fare) }}</span>
                            </div>
                            
                            <div class="mt-4 flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $route->estimated_duration ?? 'N/A' }}
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recent Searches (for authenticated users) -->
    @auth
        @if(count($recentSearches) > 0)
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 border-t border-gray-200">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Your Recent Searches</h2>
                    <p class="text-gray-600">Quick access to your previous searches</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($recentSearches as $search)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-4 border border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-gray-900">{{ $search['source_city_name'] }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                                <span class="font-medium text-gray-900">{{ $search['destination_city_name'] }}</span>
                            </div>
                            <div class="text-sm text-gray-600 mb-3">
                                {{ \Carbon\Carbon::parse($search['travel_date'])->format('M j, Y') }} • {{ $search['passengers'] }} passenger(s)
                            </div>
                            <form method="POST" action="{{ route('search.results') }}" class="inline">
                                @csrf
                                <input type="hidden" name="source_city_id" value="{{ $search['source_city_id'] }}">
                                <input type="hidden" name="destination_city_id" value="{{ $search['destination_city_id'] }}">
                                <input type="hidden" name="travel_date" value="{{ $search['travel_date'] }}">
                                <input type="hidden" name="passengers" value="{{ $search['passengers'] }}">
                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Search Again
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endauth

    <!-- Features Section -->
    <div class="bg-gray-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Why Choose BookNGO?</h2>
                <p class="text-lg text-gray-600">Experience the best bus booking platform in Nepal</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Instant Confirmation</h3>
                    <p class="text-gray-600">Get instant booking confirmation with e-tickets</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Secure Payments</h3>
                    <p class="text-gray-600">Multiple secure payment options including eSewa, Khalti</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Choose Your Seat</h3>
                    <p class="text-gray-600">Select your preferred seat with our interactive seat map</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">24/7 Support</h3>
                    <p class="text-gray-600">Round-the-clock customer support for your queries</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
