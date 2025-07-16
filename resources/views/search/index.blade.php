@extends('layouts.app')

@section('title', 'Search Buses')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Search Form -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Search Buses</h1>
        
        <form action="{{ route('search.results') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Source -->
                <div>
                    <label for="source" class="block text-sm font-medium text-gray-700 mb-2">From</label>
                    <input type="text" name="source" id="source" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('source') border-red-500 @enderror"
                           value="{{ old('source', request('source')) }}" placeholder="Enter source city" required>
                    @error('source')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Destination -->
                <div>
                    <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">To</label>
                    <input type="text" name="destination" id="destination" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('destination') border-red-500 @enderror"
                           value="{{ old('destination', request('destination')) }}" placeholder="Enter destination city" required>
                    @error('destination')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Travel Date -->
                <div>
                    <label for="travel_date" class="block text-sm font-medium text-gray-700 mb-2">Travel Date</label>
                    <input type="date" name="travel_date" id="travel_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('travel_date') border-red-500 @enderror"
                           value="{{ old('travel_date', request('travel_date', date('Y-m-d'))) }}" min="{{ date('Y-m-d') }}" required>
                    @error('travel_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Search Button -->
                <div class="flex items-end">
                    <button type="submit" 
                            class="w-full bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md font-semibold transition duration-200">
                        Search Buses
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Popular Routes -->
    @if($routes->count() > 0 && !$searchResults)
        <div class="bg-white shadow rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Popular Routes</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($routes->take(6) as $route)
                    <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 hover:shadow-md transition duration-200 cursor-pointer"
                         onclick="fillRoute('{{ $route->source_city }}', '{{ $route->destination_city }}')">
                        <h3 class="font-semibold text-gray-900">{{ $route->full_name }}</h3>
                        <p class="text-sm text-gray-600">Starting from NPR {{ number_format($route->base_fare) }}</p>
                        <p class="text-xs text-gray-500">{{ $route->distance_km }} km • {{ $route->estimated_duration->format('H:i') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Search Results -->
    @if($searchResults !== null)
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">
                Search Results 
                @if($searchResults->count() > 0)
                    <span class="text-sm font-normal text-gray-600">({{ $searchResults->count() }} buses found)</span>
                @endif
            </h2>

            @if($searchResults->count() > 0)
                <div class="space-y-4">
                    @foreach($searchResults as $schedule)
                        <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition duration-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $schedule->bus->display_name }}</h3>
                                        <span class="text-2xl font-bold text-blue-600">NRs {{ number_format($schedule->fare) }}</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <p class="text-sm text-gray-500">Bus Type</p>
                                            <p class="font-medium">{{ $schedule->bus->busType->name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Departure</p>
                                            <p class="font-medium">{{ $schedule->departure_time->format('h:i A') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-500">Available Seats</p>
                                            <p class="font-medium">{{ $schedule->available_seats }} seats</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-gray-600">{{ $schedule->route->full_name }}</p>
                                            @if($schedule->bus->amenities && count($schedule->bus->amenities) > 0)
                                                <div class="flex flex-wrap gap-1 mt-1">
                                                    @foreach(array_slice($schedule->bus->amenities, 0, 4) as $amenity)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $amenity }}
                                                        </span>
                                                    @endforeach
                                                    @if(count($schedule->bus->amenities) > 4)
                                                        <span class="text-xs text-gray-500">+{{ count($schedule->bus->amenities) - 4 }} more</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('schedule.show', $schedule) }}" 
                                               class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                                View Details
                                            </a>
                                            <a href="{{ route('bookings.create', $schedule) }}" 
                                               class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                                Book Now
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.562M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No buses found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search criteria or check different dates.</p>
                </div>
            @endif
        </div>
    @endif
</div>

<script>
function fillRoute(source, destination) {
    document.getElementById('source').value = source;
    document.getElementById('destination').value = destination;
}
</script>
@endsection
