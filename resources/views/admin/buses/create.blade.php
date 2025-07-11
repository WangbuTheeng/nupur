@extends('layouts.admin')

@section('title', 'Add New Bus')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add New Bus</h1>
        <p class="text-gray-600">Add a new bus to your fleet</p>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form action="{{ route('admin.buses.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bus Number -->
                <div>
                    <label for="bus_number" class="block text-sm font-medium text-gray-700 mb-2">Bus Number</label>
                    <input type="text" name="bus_number" id="bus_number" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('bus_number') border-red-500 @enderror"
                           value="{{ old('bus_number') }}" placeholder="e.g., BUS001" required>
                    @error('bus_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Operator Name -->
                <div>
                    <label for="operator_name" class="block text-sm font-medium text-gray-700 mb-2">Operator Name</label>
                    <input type="text" name="operator_name" id="operator_name" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('operator_name') border-red-500 @enderror"
                           value="{{ old('operator_name') }}" placeholder="e.g., Green Line" required>
                    @error('operator_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bus Type -->
                <div>
                    <label for="bus_type_id" class="block text-sm font-medium text-gray-700 mb-2">Bus Type</label>
                    <select name="bus_type_id" id="bus_type_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('bus_type_id') border-red-500 @enderror" required>
                        <option value="">Select Bus Type</option>
                        @foreach($busTypes as $busType)
                            <option value="{{ $busType->id }}" {{ old('bus_type_id') == $busType->id ? 'selected' : '' }}>
                                {{ $busType->name }} ({{ $busType->total_seats }} seats)
                            </option>
                        @endforeach
                    </select>
                    @error('bus_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- License Plate -->
                <div>
                    <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-2">License Plate</label>
                    <input type="text" name="license_plate" id="license_plate" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('license_plate') border-red-500 @enderror"
                           value="{{ old('license_plate') }}" placeholder="e.g., BA 1 KHA 1234" required>
                    @error('license_plate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Manufacture Year -->
                <div>
                    <label for="manufacture_year" class="block text-sm font-medium text-gray-700 mb-2">Manufacture Year</label>
                    <input type="number" name="manufacture_year" id="manufacture_year" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('manufacture_year') border-red-500 @enderror"
                           value="{{ old('manufacture_year', date('Y')) }}" min="1990" max="{{ date('Y') + 1 }}" required>
                    @error('manufacture_year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Amenities -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @php
                        $amenities = ['AC', 'WiFi', 'TV', 'USB Charging', 'Reclining Seats', 'Reading Light', 'Blanket', 'Water Bottle'];
                        $oldAmenities = old('amenities', []);
                    @endphp
                    @foreach($amenities as $amenity)
                        <label class="flex items-center">
                            <input type="checkbox" name="amenities[]" value="{{ $amenity }}" 
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                   {{ in_array($amenity, $oldAmenities) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">{{ $amenity }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.buses.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                    Add Bus
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
