@extends('layouts.operator')

@section('title', 'Suggest New Route')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Suggest New Route</h1>
                    <p class="text-purple-100">Suggest a new route for admin review</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('operator.routes.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Routes
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Route Suggestion Form</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('operator.routes.suggest.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="source_city_id" class="block text-sm font-medium text-gray-700">Source City <span class="text-red-500">*</span></label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('source_city_id') border-red-300 @enderror"
                                        id="source_city_id" name="source_city_id" required>
                                    <option value="">Select Source City</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('source_city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}, {{ $city->district }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('source_city_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="destination_city_id" class="block text-sm font-medium text-gray-700">Destination City <span class="text-red-500">*</span></label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('destination_city_id') border-red-300 @enderror"
                                        id="destination_city_id" name="destination_city_id" required>
                                    <option value="">Select Destination City</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('destination_city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}, {{ $city->district }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('destination_city_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label for="distance_km" class="block text-sm font-medium text-gray-700">Distance (KM) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.1" min="0"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('distance_km') border-red-300 @enderror"
                                       id="distance_km" name="distance_km" value="{{ old('distance_km') }}" required>
                                @error('distance_km')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="estimated_duration" class="block text-sm font-medium text-gray-700">Estimated Duration <span class="text-red-500">*</span></label>
                                <input type="time"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('estimated_duration') border-red-300 @enderror"
                                       id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration') }}" required>
                                @error('estimated_duration')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="suggested_fare" class="block text-sm font-medium text-gray-700">Suggested Base Fare (Rs.) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('suggested_fare') border-red-300 @enderror"
                                       id="suggested_fare" name="suggested_fare" value="{{ old('suggested_fare') }}" required>
                                @error('suggested_fare')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="stops" class="block text-sm font-medium text-gray-700">Intermediate Stops (Optional)</label>
                            <div id="stops-container" class="mt-1">
                                <div class="flex mb-2">
                                    <input type="text" class="flex-1 border-gray-300 rounded-l-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
                                           name="stops[]" placeholder="Enter stop name">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-green-50 text-green-700 hover:bg-green-100" onclick="addStop()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Add intermediate stops along the route</p>
                        </div>

                        <div class="mt-6">
                            <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Suggestion <span class="text-red-500">*</span></label>
                            <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm @error('reason') border-red-300 @enderror"
                                      id="reason" name="reason" rows="4" required
                                      placeholder="Explain why this route should be added...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8 flex items-center justify-end space-x-3">
                            <a href="{{ route('operator.routes.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Submit Suggestion
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-blue-900">Guidelines</h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Ensure the route doesn't already exist</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Provide accurate distance and duration</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Suggest competitive fare pricing</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Include major stops along the route</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Provide clear justification for the route</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function addStop() {
    const container = document.getElementById('stops-container');
    const div = document.createElement('div');
    div.className = 'flex mb-2';
    div.innerHTML = `
        <input type="text" class="flex-1 border-gray-300 rounded-l-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
               name="stops[]" placeholder="Enter stop name">
        <button type="button" class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 rounded-r-md bg-red-50 text-red-700 hover:bg-red-100" onclick="removeStop(this)">
            <i class="fas fa-minus"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeStop(button) {
    button.closest('.flex').remove();
}
</script>
@endsection
