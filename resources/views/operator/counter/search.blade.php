@extends('layouts.operator')

@section('title', 'Search Schedules - Counter Booking')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Search Schedules</h1>
                    <p class="text-purple-100">Find available schedules for counter booking</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('operator.counter.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Counter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="bg-white overflow-hidden shadow-xl rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Search Available Schedules</h3>
            <p class="text-sm text-gray-500">Find schedules from your routes for the selected date</p>
        </div>
        
        <form method="POST" action="{{ route('operator.counter.search.results') }}" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Source City -->
                <div>
                    <label for="source_city_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-green-500 mr-2"></i>From
                    </label>
                    <select name="source_city_id" id="source_city_id" required
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Select departure city</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('source_city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('source_city_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Destination City -->
                <div>
                    <label for="destination_city_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>To
                    </label>
                    <select name="destination_city_id" id="destination_city_id" required
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <option value="">Select destination city</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('destination_city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('destination_city_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Travel Date -->
                <div>
                    <label for="travel_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Travel Date
                    </label>
                    <input type="date" name="travel_date" id="travel_date" required
                           min="{{ date('Y-m-d') }}"
                           value="{{ old('travel_date', date('Y-m-d')) }}"
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                    @error('travel_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Search Button -->
            <div class="mt-6 flex justify-center">
                <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search Schedules
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Tips -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-lightbulb text-blue-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Quick Tips</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc list-inside space-y-1">
                        <li>Only your own schedules will be shown in search results</li>
                        <li>You can only book seats for schedules with available capacity</li>
                        <li>Counter bookings are confirmed immediately upon payment</li>
                        <li>Make sure to collect payment before confirming the booking</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Searches (if any) -->
    @if(session('recent_counter_searches'))
        <div class="mt-8 bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Searches</h3>
                <p class="text-sm text-gray-500">Your recent search queries</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach(session('recent_counter_searches', []) as $search)
                        <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors cursor-pointer" 
                             onclick="fillSearchForm('{{ $search['source_city_id'] }}', '{{ $search['destination_city_id'] }}', '{{ $search['travel_date'] }}')">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $search['source_city'] }} → {{ $search['destination_city'] }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $search['travel_date'] }}</div>
                                </div>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sourceSelect = document.getElementById('source_city_id');
    const destinationSelect = document.getElementById('destination_city_id');

    // Prevent selecting same city for source and destination
    function updateCityOptions() {
        const sourceValue = sourceSelect.value;
        const destinationValue = destinationSelect.value;

        // Reset all options
        Array.from(destinationSelect.options).forEach(option => {
            option.disabled = false;
            option.style.display = '';
        });

        Array.from(sourceSelect.options).forEach(option => {
            option.disabled = false;
            option.style.display = '';
        });

        // Disable selected source in destination
        if (sourceValue) {
            const destOption = destinationSelect.querySelector(`option[value="${sourceValue}"]`);
            if (destOption) {
                destOption.disabled = true;
                destOption.style.display = 'none';
            }
        }

        // Disable selected destination in source
        if (destinationValue) {
            const sourceOption = sourceSelect.querySelector(`option[value="${destinationValue}"]`);
            if (sourceOption) {
                sourceOption.disabled = true;
                sourceOption.style.display = 'none';
            }
        }

        // Clear destination if same as source
        if (sourceValue && sourceValue === destinationValue) {
            destinationSelect.value = '';
        }
    }

    sourceSelect.addEventListener('change', updateCityOptions);
    destinationSelect.addEventListener('change', updateCityOptions);

    // Initialize on page load
    updateCityOptions();

    // Swap cities function
    window.swapCities = function() {
        const sourceValue = sourceSelect.value;
        const destinationValue = destinationSelect.value;
        
        sourceSelect.value = destinationValue;
        destinationSelect.value = sourceValue;
        
        updateCityOptions();
    };

    // Fill search form from recent searches
    window.fillSearchForm = function(sourceId, destinationId, travelDate) {
        sourceSelect.value = sourceId;
        destinationSelect.value = destinationId;
        document.getElementById('travel_date').value = travelDate;
        updateCityOptions();
    };
});
</script>
@endpush
@endsection
