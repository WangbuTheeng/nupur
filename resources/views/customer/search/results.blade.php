@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Search Summary Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                        <span class="font-semibold text-lg text-gray-900">{{ $searchParams['source_city']->name }}</span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                    <div class="flex items-center space-x-2">
                        <span class="font-semibold text-lg text-gray-900">{{ $searchParams['destination_city']->name }}</span>
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                    <div class="hidden sm:block text-gray-500">•</div>
                    <div class="text-gray-600">
                        {{ \Carbon\Carbon::parse($searchParams['travel_date'])->format('D, M j, Y') }}
                    </div>
                    <div class="hidden sm:block text-gray-500">•</div>
                    <div class="text-gray-600">
                        {{ $searchParams['passengers'] }} {{ $searchParams['passengers'] == 1 ? 'Passenger' : 'Passengers' }}
                    </div>
                </div>
                
                <div class="mt-4 lg:mt-0">
                    <a href="{{ route('search.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                        </svg>
                        Modify Search
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="lg:grid lg:grid-cols-4 lg:gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
                    
                    <form method="GET" action="{{ route('search.show') }}" id="filterForm">
                        <!-- Preserve search parameters -->
                        <input type="hidden" name="source_city_id" value="{{ request('source_city_id') }}">
                        <input type="hidden" name="destination_city_id" value="{{ request('destination_city_id') }}">
                        <input type="hidden" name="travel_date" value="{{ request('travel_date') }}">
                        <input type="hidden" name="passengers" value="{{ request('passengers') }}">

                        <!-- Bus Type Filter -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Bus Type</h4>
                            <div class="space-y-2">
                                @foreach($busTypes as $busType)
                                    <label class="flex items-center">
                                        <input type="radio" name="bus_type" value="{{ $busType->id }}" 
                                               {{ request('bus_type') == $busType->id ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">{{ $busType->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Operator Filter -->
                        @if($operators->count() > 0)
                            <div class="mb-6">
                                <h4 class="font-medium text-gray-900 mb-3">Operator</h4>
                                <div class="space-y-2">
                                    @foreach($operators as $operator)
                                        <label class="flex items-center">
                                            <input type="radio" name="operator" value="{{ $operator->id }}" 
                                                   {{ request('operator') == $operator->id ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">{{ $operator->company_name ?? $operator->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Departure Time Filter -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Departure Time</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="departure_time" value="morning" 
                                           {{ request('departure_time') == 'morning' ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Morning (6 AM - 12 PM)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="departure_time" value="afternoon" 
                                           {{ request('departure_time') == 'afternoon' ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Afternoon (12 PM - 6 PM)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="departure_time" value="evening" 
                                           {{ request('departure_time') == 'evening' ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Evening (6 PM - 12 AM)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="departure_time" value="night" 
                                           {{ request('departure_time') == 'night' ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Night (12 AM - 6 AM)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="mb-6">
                            <h4 class="font-medium text-gray-900 mb-3">Price Range</h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="price_range" value="budget" 
                                           {{ request('price_range') == 'budget' ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Budget (Up to Rs. 1,000)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="price_range" value="standard" 
                                           {{ request('price_range') == 'standard' ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Standard (Rs. 1,000 - 2,000)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="price_range" value="premium" 
                                           {{ request('price_range') == 'premium' ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Premium (Rs. 2,000+)</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-sm font-medium">
                                Apply Filters
                            </button>
                            <a href="{{ route('search.show', request()->only(['source_city_id', 'destination_city_id', 'travel_date', 'passengers'])) }}" 
                               class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 text-sm font-medium text-center">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Section -->
            <div class="lg:col-span-3 mt-8 lg:mt-0">
                <!-- Sort Options -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-600 mb-4 sm:mb-0">
                            {{ $schedules->total() }} buses found
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <label for="sort_by" class="text-sm font-medium text-gray-700">Sort by:</label>
                            <select name="sort_by" id="sort_by" onchange="updateSort(this.value)" 
                                    class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="departure_time" {{ request('sort_by') == 'departure_time' ? 'selected' : '' }}>Departure Time</option>
                                <option value="price_low" {{ request('sort_by') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort_by') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="duration" {{ request('sort_by') == 'duration' ? 'selected' : '' }}>Duration</option>
                                <option value="rating" {{ request('sort_by') == 'rating' ? 'selected' : '' }}>Rating</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Bus Results -->
                @if($schedules->count() > 0)
                    <div class="space-y-4">
                        @foreach($schedules as $schedule)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                                <div class="p-6">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                        <!-- Bus Info -->
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-4 mb-4">
                                                <div class="flex-shrink-0">
                                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900">{{ $schedule->operator->company_name ?? $schedule->operator->name }}</h3>
                                                    <p class="text-sm text-gray-600">{{ $schedule->bus->busType->name }} • {{ $schedule->bus->bus_number }}</p>
                                                </div>
                                            </div>

                                            <!-- Journey Details -->
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                                                <div>
                                                    <div class="text-sm text-gray-500">Departure</div>
                                                    <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('g:i A') }}</div>
                                                    <div class="text-sm text-gray-600">{{ $schedule->route->sourceCity->name }}</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-sm text-gray-500">Duration</div>
                                                    <div class="flex items-center justify-center">
                                                        <div class="w-8 border-t border-gray-300"></div>
                                                        <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                                        </svg>
                                                        <div class="w-8 border-t border-gray-300"></div>
                                                    </div>
                                                    <div class="text-sm text-gray-600">
                                                        {{ \Carbon\Carbon::parse($schedule->departure_time)->diffInHours(\Carbon\Carbon::parse($schedule->arrival_time)) }}h 
                                                        {{ \Carbon\Carbon::parse($schedule->departure_time)->diffInMinutes(\Carbon\Carbon::parse($schedule->arrival_time)) % 60 }}m
                                                    </div>
                                                </div>
                                                <div class="text-right sm:text-left">
                                                    <div class="text-sm text-gray-500">Arrival</div>
                                                    <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($schedule->arrival_time)->format('g:i A') }}</div>
                                                    <div class="text-sm text-gray-600">{{ $schedule->route->destinationCity->name }}</div>
                                                </div>
                                            </div>

                                            <!-- Amenities -->
                                            @if($schedule->bus->amenities && count($schedule->bus->amenities) > 0)
                                                <div class="flex flex-wrap gap-2 mb-4">
                                                    @foreach(array_slice($schedule->bus->amenities, 0, 4) as $amenity)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ $amenity }}
                                                        </span>
                                                    @endforeach
                                                    @if(count($schedule->bus->amenities) > 4)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            +{{ count($schedule->bus->amenities) - 4 }} more
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Price and Book Button -->
                                        <div class="lg:text-right lg:ml-6">
                                            <div class="mb-4">
                                                <div class="text-2xl font-bold text-gray-900">Rs. {{ number_format($schedule->fare) }}</div>
                                                <div class="text-sm text-gray-600">per person</div>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <div class="text-sm text-gray-600">{{ $schedule->available_seats }} seats available</div>
                                            </div>

                                            <div class="space-y-2">
                                                @auth
                                                    @if($schedule->available_seats >= $searchParams['passengers'])
                                                        <a href="{{ route('booking.seat-selection', $schedule) }}" 
                                                           class="block w-full bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium text-center transition-colors duration-200">
                                                            Select Seats
                                                        </a>
                                                    @else
                                                        <button disabled 
                                                                class="block w-full bg-gray-300 text-gray-500 px-6 py-3 rounded-md font-medium text-center cursor-not-allowed">
                                                            Not Available
                                                        </button>
                                                    @endif
                                                @else
                                                    <a href="{{ route('login') }}" 
                                                       class="block w-full bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium text-center transition-colors duration-200">
                                                        Login to Book
                                                    </a>
                                                @endauth
                                                
                                                <a href="{{ route('search.schedule', $schedule) }}" 
                                                   class="block w-full border border-gray-300 text-gray-700 px-6 py-3 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 font-medium text-center transition-colors duration-200">
                                                    View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $schedules->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No buses found</h3>
                        <p class="text-gray-600 mb-6">We couldn't find any buses for your search criteria. Try adjusting your filters or search for a different date.</p>
                        <a href="{{ route('search.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            New Search
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function updateSort(sortBy) {
    const url = new URL(window.location);
    url.searchParams.set('sort_by', sortBy);
    window.location.href = url.toString();
}

// Auto-submit filter form when radio buttons change
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filterForm');
    const radioInputs = filterForm.querySelectorAll('input[type="radio"]');
    
    radioInputs.forEach(input => {
        input.addEventListener('change', function() {
            filterForm.submit();
        });
    });
});
</script>
@endsection
