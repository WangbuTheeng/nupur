@extends('layouts.admin')

@section('title', 'Add New Schedule')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add New Schedule</h1>
        <p class="text-gray-600">Create a new bus schedule</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.schedules.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bus -->
                <div>
                    <label for="bus_id" class="block text-sm font-medium text-gray-700 mb-2">Bus</label>
                    <select name="bus_id" id="bus_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('bus_id') border-red-500 @enderror" required>
                        <option value="">Select Bus</option>
                        @foreach($buses as $bus)
                            <option value="{{ $bus->id }}" {{ old('bus_id') == $bus->id ? 'selected' : '' }}>
                                {{ $bus->display_name }} ({{ $bus->busType->name }} - {{ $bus->total_seats }} seats)
                            </option>
                        @endforeach
                    </select>
                    @error('bus_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Route -->
                <div>
                    <label for="route_id" class="block text-sm font-medium text-gray-700 mb-2">Route</label>
                    <select name="route_id" id="route_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('route_id') border-red-500 @enderror" required>
                        <option value="">Select Route</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" data-fare="{{ $route->base_fare }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                {{ $route->full_name }} (NPR {{ number_format($route->base_fare) }})
                            </option>
                        @endforeach
                    </select>
                    @error('route_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Travel Date -->
                <div>
                    <label for="travel_date" class="block text-sm font-medium text-gray-700 mb-2">Travel Date</label>
                    <input type="date" name="travel_date" id="travel_date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('travel_date') border-red-500 @enderror"
                           value="{{ old('travel_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                    @error('travel_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Departure Time -->
                <div>
                    <label for="departure_time" class="block text-sm font-medium text-gray-700 mb-2">Departure Time</label>
                    <input type="time" name="departure_time" id="departure_time" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('departure_time') border-red-500 @enderror"
                           value="{{ old('departure_time') }}" required>
                    @error('departure_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Arrival Time -->
                <div>
                    <label for="arrival_time" class="block text-sm font-medium text-gray-700 mb-2">Arrival Time</label>
                    <input type="time" name="arrival_time" id="arrival_time" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('arrival_time') border-red-500 @enderror"
                           value="{{ old('arrival_time') }}" required>
                    @error('arrival_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fare -->
                <div>
                    <label for="fare" class="block text-sm font-medium text-gray-700 mb-2">Fare (NPR)</label>
                    <input type="number" name="fare" id="fare" step="0.01" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('fare') border-red-500 @enderror"
                           value="{{ old('fare') }}" placeholder="e.g., 800.00" required>
                    @error('fare')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                          placeholder="Any special notes about this schedule...">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.schedules.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                    Add Schedule
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-fill fare when route is selected
document.getElementById('route_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const baseFare = selectedOption.getAttribute('data-fare');
    if (baseFare) {
        document.getElementById('fare').value = baseFare;
    }
});

// Auto-calculate arrival time based on departure time and route duration
document.getElementById('departure_time').addEventListener('change', function() {
    // This could be enhanced to automatically calculate arrival time
    // based on the route's estimated duration
});
</script>
@endsection
