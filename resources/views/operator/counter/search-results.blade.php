@extends('layouts.operator')

@section('title', 'Search Results - Counter Booking')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Available Schedules</h1>
                    <p class="text-purple-100">{{ $schedules->count() }} schedules found for your search</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('operator.counter.search') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        New Search
                    </a>
                    <a href="{{ route('operator.counter.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        </svg>
                        Counter Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Summary -->
    <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Search Details</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Route</div>
                    <div class="text-lg font-semibold text-gray-900">
                        @if($schedules->count() > 0)
                            {{ $schedules->first()->route->sourceCity->name }} → {{ $schedules->first()->route->destinationCity->name }}
                        @else
                            Search Route
                        @endif
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Travel Date</div>
                    <div class="text-lg font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($searchParams['travel_date'])->format('l, F j, Y') }}
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Available Schedules</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $schedules->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedules List -->
    @if($schedules->count() > 0)
        <div class="space-y-6">
            @foreach($schedules as $schedule)
                <div class="bg-white overflow-hidden shadow-lg rounded-xl hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-center">
                            <!-- Schedule Info -->
                            <div class="lg:col-span-2">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mr-4">
                                        <i class="fas fa-bus text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $schedule->bus->bus_number }}</h3>
                                        <p class="text-sm text-gray-500">{{ $schedule->bus->busType->name ?? 'Standard Bus' }}</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="text-gray-500">Departure</div>
                                        <div class="font-medium text-gray-900">{{ $schedule->departure_time }}</div>
                                    </div>
                                    <div>
                                        <div class="text-gray-500">Arrival</div>
                                        <div class="font-medium text-gray-900">{{ $schedule->arrival_time ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Availability & Pricing -->
                            <div class="text-center">
                                <div class="mb-4">
                                    <div class="text-sm text-gray-500">Available Seats</div>
                                    <div class="text-2xl font-bold text-green-600">{{ $schedule->available_seats }}</div>
                                    <div class="text-xs text-gray-500">of {{ $schedule->bus->total_seats }} total</div>
                                </div>
                                
                                <div>
                                    <div class="text-sm text-gray-500">Fare per Seat</div>
                                    <div class="text-xl font-bold text-gray-900">Rs. {{ number_format($schedule->fare, 2) }}</div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="text-center">
                                @if($schedule->available_seats > 0)
                                    <a href="{{ route('operator.counter.book', $schedule) }}" 
                                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                                        <i class="fas fa-ticket-alt mr-2"></i>
                                        Book Now
                                    </a>
                                @else
                                    <button disabled class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-400 bg-gray-100 cursor-not-allowed">
                                        <i class="fas fa-times mr-2"></i>
                                        Fully Booked
                                    </button>
                                @endif
                                
                                <div class="mt-2">
                                    <button onclick="viewScheduleDetails({{ $schedule->id }})" 
                                            class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info (collapsible) -->
                        <div id="schedule-details-{{ $schedule->id }}" class="hidden mt-6 pt-6 border-t border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Bus Details</h4>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <div>Model: {{ $schedule->bus->model ?? 'N/A' }}</div>
                                        <div>License: {{ $schedule->bus->license_plate }}</div>
                                        @if($schedule->bus->amenities && count($schedule->bus->amenities) > 0)
                                            <div>Amenities: {{ implode(', ', $schedule->bus->amenities) }}</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Schedule Info</h4>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <div>Status: <span class="text-green-600 font-medium">{{ ucfirst($schedule->status) }}</span></div>
                                        <div>Created: {{ $schedule->created_at->format('M d, Y') }}</div>
                                    </div>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Booking Stats</h4>
                                    <div class="text-sm text-gray-600 space-y-1">
                                        <div>Booked: {{ $schedule->bus->total_seats - $schedule->available_seats }} seats</div>
                                        <div>Occupancy: {{ round((($schedule->bus->total_seats - $schedule->available_seats) / $schedule->bus->total_seats) * 100, 1) }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- No Results -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Schedules Found</h3>
                <p class="text-gray-500 mb-6">No available schedules found for your search criteria.</p>
                
                <div class="space-y-2 text-sm text-gray-600 mb-6">
                    <p>• Make sure you have schedules set up for this route</p>
                    <p>• Check if the selected date has any scheduled trips</p>
                    <p>• Verify that schedules have available seats</p>
                </div>
                
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('operator.counter.search') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <i class="fas fa-search mr-2"></i>
                        Try Different Search
                    </a>
                    <a href="{{ route('operator.schedules.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create Schedule
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function viewScheduleDetails(scheduleId) {
    const detailsDiv = document.getElementById(`schedule-details-${scheduleId}`);
    const button = event.target;
    
    if (detailsDiv.classList.contains('hidden')) {
        detailsDiv.classList.remove('hidden');
        button.textContent = 'Hide Details';
    } else {
        detailsDiv.classList.add('hidden');
        button.textContent = 'View Details';
    }
}

// Store search in session for recent searches
@if($schedules->count() > 0)
    // This would typically be handled server-side, but we can add client-side storage if needed
    const searchData = {
        source_city_id: '{{ $searchParams["source_city_id"] }}',
        destination_city_id: '{{ $searchParams["destination_city_id"] }}',
        travel_date: '{{ $searchParams["travel_date"] }}',
        source_city: '{{ $schedules->first()->route->sourceCity->name }}',
        destination_city: '{{ $schedules->first()->route->destinationCity->name }}'
    };
    
    // Store in localStorage for client-side recent searches
    let recentSearches = JSON.parse(localStorage.getItem('counter_recent_searches') || '[]');
    recentSearches = recentSearches.filter(search => 
        !(search.source_city_id === searchData.source_city_id && 
          search.destination_city_id === searchData.destination_city_id && 
          search.travel_date === searchData.travel_date)
    );
    recentSearches.unshift(searchData);
    recentSearches = recentSearches.slice(0, 5); // Keep only 5 recent searches
    localStorage.setItem('counter_recent_searches', JSON.stringify(recentSearches));
@endif
</script>
@endpush
@endsection
