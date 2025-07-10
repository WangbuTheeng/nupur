@extends('layouts.app')

@section('title', 'Add New Route')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add New Route</h1>
        <p class="text-gray-600">Create a new bus route</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.routes.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Route Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Route Name</label>
                    <input type="text" name="name" id="name" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           value="{{ old('name') }}" placeholder="e.g., Kathmandu - Pokhara" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Source City -->
                <div>
                    <label for="source_city" class="block text-sm font-medium text-gray-700 mb-2">Source City</label>
                    <input type="text" name="source_city" id="source_city" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('source_city') border-red-500 @enderror"
                           value="{{ old('source_city') }}" placeholder="e.g., Kathmandu" required>
                    @error('source_city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Destination City -->
                <div>
                    <label for="destination_city" class="block text-sm font-medium text-gray-700 mb-2">Destination City</label>
                    <input type="text" name="destination_city" id="destination_city" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('destination_city') border-red-500 @enderror"
                           value="{{ old('destination_city') }}" placeholder="e.g., Pokhara" required>
                    @error('destination_city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Distance -->
                <div>
                    <label for="distance_km" class="block text-sm font-medium text-gray-700 mb-2">Distance (KM)</label>
                    <input type="number" name="distance_km" id="distance_km" step="0.1" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('distance_km') border-red-500 @enderror"
                           value="{{ old('distance_km') }}" placeholder="e.g., 200.5" required>
                    @error('distance_km')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Base Fare -->
                <div>
                    <label for="base_fare" class="block text-sm font-medium text-gray-700 mb-2">Base Fare (NPR)</label>
                    <input type="number" name="base_fare" id="base_fare" step="0.01" min="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('base_fare') border-red-500 @enderror"
                           value="{{ old('base_fare') }}" placeholder="e.g., 800.00" required>
                    @error('base_fare')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estimated Duration -->
                <div>
                    <label for="estimated_duration" class="block text-sm font-medium text-gray-700 mb-2">Estimated Duration (HH:MM)</label>
                    <input type="time" name="estimated_duration" id="estimated_duration"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('estimated_duration') border-red-500 @enderror"
                           value="{{ old('estimated_duration') }}" required>
                    @error('estimated_duration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Stops -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Intermediate Stops (Optional)</label>
                <div id="stops-container" class="space-y-2">
                    @if(old('stops'))
                        @foreach(old('stops') as $index => $stop)
                            <div class="flex items-center space-x-2 stop-input">
                                <input type="text" name="stops[]" 
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                       value="{{ $stop }}" placeholder="Enter stop name">
                                <button type="button" onclick="removeStop(this)" class="text-red-600 hover:text-red-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    @else
                        <div class="flex items-center space-x-2 stop-input">
                            <input type="text" name="stops[]" 
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter stop name">
                            <button type="button" onclick="removeStop(this)" class="text-red-600 hover:text-red-800">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                </div>
                <button type="button" onclick="addStop()" class="mt-2 text-blue-600 hover:text-blue-800 text-sm font-medium">
                    + Add Another Stop
                </button>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.routes.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                    Add Route
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addStop() {
    const container = document.getElementById('stops-container');
    const stopDiv = document.createElement('div');
    stopDiv.className = 'flex items-center space-x-2 stop-input';
    stopDiv.innerHTML = `
        <input type="text" name="stops[]" 
               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
               placeholder="Enter stop name">
        <button type="button" onclick="removeStop(this)" class="text-red-600 hover:text-red-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    `;
    container.appendChild(stopDiv);
}

function removeStop(button) {
    const container = document.getElementById('stops-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    }
}

// Auto-generate route name when source and destination are filled
document.getElementById('source_city').addEventListener('input', updateRouteName);
document.getElementById('destination_city').addEventListener('input', updateRouteName);

function updateRouteName() {
    const source = document.getElementById('source_city').value;
    const destination = document.getElementById('destination_city').value;
    const nameField = document.getElementById('name');
    
    if (source && destination && !nameField.value) {
        nameField.value = `${source} - ${destination}`;
    }
}
</script>
@endsection
