@extends('layouts.operator')

@section('title', 'Add New Bus')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Add New Bus</h1>
                    <p class="text-green-100">Add a new bus to your fleet</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('operator.buses.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Buses
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Bus Information</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('operator.buses.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="bus_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bus Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm @error('bus_number') border-red-300 @enderror"
                                       id="bus_number" name="bus_number" value="{{ old('bus_number') }}"
                                       placeholder="e.g., KTM-001" required>
                                @error('bus_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-2">
                                    License Plate <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm @error('license_plate') border-red-300 @enderror"
                                       id="license_plate" name="license_plate" value="{{ old('license_plate') }}"
                                       placeholder="e.g., BA 1 KHA 1234" required>
                                @error('license_plate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="bus_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bus Type <span class="text-red-500">*</span>
                                </label>
                                <select class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm @error('bus_type_id') border-red-300 @enderror"
                                        id="bus_type_id" name="bus_type_id" required>
                                    <option value="">Select Bus Type</option>
                                    @foreach($busTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('bus_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bus_type_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="total_seats" class="block text-sm font-medium text-gray-700 mb-2">
                                    Total Seats <span class="text-red-500">*</span>
                                </label>
                                <input type="number" min="10" max="100"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm @error('total_seats') border-red-300 @enderror"
                                       id="total_seats" name="total_seats" value="{{ old('total_seats', 31) }}"
                                       placeholder="e.g., 32" required>
                                @error('total_seats')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Recommended: 27, 31, 35, or 39 seats</p>
                            </div>
                        </div>

                        <!-- Seat Layout Configuration -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h4 class="font-medium text-blue-900 mb-3">Seat Layout Configuration</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Layout Type <span class="text-red-500">*</span>
                                    </label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="layout_type" value="2x2"
                                                   {{ old('layout_type', '2x2') == '2x2' ? 'checked' : '' }}
                                                   class="text-green-600 focus:ring-green-500">
                                            <span class="ml-2 text-sm">2x2 (Standard) - Most common</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="layout_type" value="2x1"
                                                   {{ old('layout_type') == '2x1' ? 'checked' : '' }}
                                                   class="text-green-600 focus:ring-green-500">
                                            <span class="ml-2 text-sm">2x1 (Compact) - Smaller buses</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="layout_type" value="3x2"
                                                   {{ old('layout_type') == '3x2' ? 'checked' : '' }}
                                                   class="text-green-600 focus:ring-green-500">
                                            <span class="ml-2 text-sm">3x2 (Large) - Larger buses</span>
                                        </label>
                                    </div>
                                    @error('layout_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="has_back_row" value="1"
                                               {{ old('has_back_row', true) ? 'checked' : '' }}
                                               class="text-green-600 focus:ring-green-500 rounded">
                                        <span class="ml-2">
                                            <span class="text-sm font-medium text-gray-700">Include Back Row</span>
                                            <span class="block text-xs text-gray-500">Continuous line of seats at the back</span>
                                        </span>
                                    </label>
                                    @error('has_back_row')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror

                                    <div class="mt-3 text-xs text-gray-600">
                                        <p class="font-medium mb-1">Recommended configurations:</p>
                                        <p>• 2x2: 27, 31, 35 seats</p>
                                        <p>• 2x1: 21, 25, 29 seats</p>
                                        <p>• 3x2: 35, 39, 45 seats</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                                    Model <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm @error('model') border-red-300 @enderror"
                                       id="model" name="model" value="{{ old('model') }}"
                                       placeholder="e.g., Tata Ultra" required>
                                @error('model')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                                    Color <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm @error('color') border-red-300 @enderror"
                                       id="color" name="color" value="{{ old('color') }}"
                                       placeholder="e.g., Blue" required>
                                @error('color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="manufacture_year" class="block text-sm font-medium text-gray-700 mb-2">
                                Manufacture Year <span class="text-red-500">*</span>
                            </label>
                            <input type="number" min="1990" max="{{ date('Y') + 1 }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm @error('manufacture_year') border-red-300 @enderror"
                                   id="manufacture_year" name="manufacture_year" value="{{ old('manufacture_year') }}"
                                   placeholder="e.g., {{ date('Y') }}" required>
                            @error('manufacture_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Amenities</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                           name="amenities[]" value="WiFi" id="wifi"
                                           {{ in_array('WiFi', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="wifi" class="ml-2 text-sm text-gray-700">WiFi</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                           name="amenities[]" value="AC" id="ac"
                                           {{ in_array('AC', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="ac" class="ml-2 text-sm text-gray-700">Air Conditioning</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                           name="amenities[]" value="Charging Port" id="charging"
                                           {{ in_array('Charging Port', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="charging" class="ml-2 text-sm text-gray-700">Charging Port</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                           name="amenities[]" value="Reading Light" id="reading_light"
                                           {{ in_array('Reading Light', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="reading_light" class="ml-2 text-sm text-gray-700">Reading Light</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                           name="amenities[]" value="Entertainment System" id="entertainment"
                                           {{ in_array('Entertainment System', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="entertainment" class="ml-2 text-sm text-gray-700">Entertainment System</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                           name="amenities[]" value="Large Windows" id="windows"
                                           {{ in_array('Large Windows', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="windows" class="ml-2 text-sm text-gray-700">Large Windows</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                           name="amenities[]" value="Panoramic View" id="panoramic"
                                           {{ in_array('Panoramic View', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="panoramic" class="ml-2 text-sm text-gray-700">Panoramic View</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                           name="amenities[]" value="Blanket" id="blanket"
                                           {{ in_array('Blanket', old('amenities', [])) ? 'checked' : '' }}>
                                    <label for="blanket" class="ml-2 text-sm text-gray-700">Blanket</label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm @error('description') border-red-300 @enderror"
                                      id="description" name="description" rows="4"
                                      placeholder="Additional information about the bus...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('operator.buses.index') }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-save mr-2"></i> Add Bus
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
                            <span class="text-sm text-gray-700">Ensure bus number is unique</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">License plate must be valid</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Select appropriate bus type</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Seat count should match bus type</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Add relevant amenities</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-yellow-900">Next Steps</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 mb-3">After adding your bus:</p>
                    <ol class="text-sm text-gray-700 space-y-1">
                        <li>1. Create schedules for routes</li>
                        <li>2. Set up seat layout</li>
                        <li>3. Configure pricing</li>
                        <li>4. Start accepting bookings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
