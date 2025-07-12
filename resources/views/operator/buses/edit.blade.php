@extends('layouts.operator')

@section('title', 'Edit Bus - ' . $bus->bus_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Edit Bus - {{ $bus->bus_number }}</h1>
                    <p class="text-blue-100">Update bus information</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('operator.buses.show', $bus) }}"
                       class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <i class="fas fa-eye mr-2"></i> View Bus
                    </a>
                    <a href="{{ route('operator.buses.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-white bg-opacity-10 border border-white border-opacity-30 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-20 focus:bg-opacity-20 transition ease-in-out duration-150">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Buses
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-edit text-blue-500 mr-2"></i>
                        Bus Information
                    </h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('operator.buses.update', $bus) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="bus_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bus Number <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('bus_number') border-red-300 @enderror"
                                       id="bus_number" name="bus_number" value="{{ old('bus_number', $bus->bus_number) }}" required>
                                @error('bus_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="license_plate" class="block text-sm font-medium text-gray-700 mb-2">
                                    License Plate <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('license_plate') border-red-300 @enderror"
                                       id="license_plate" name="license_plate" value="{{ old('license_plate', $bus->license_plate) }}" required>
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
                                <select class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('bus_type_id') border-red-300 @enderror"
                                        id="bus_type_id" name="bus_type_id" required>
                                    <option value="">Select Bus Type</option>
                                    @foreach($busTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('bus_type_id', $bus->bus_type_id) == $type->id ? 'selected' : '' }}>
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
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('total_seats') border-red-300 @enderror"
                                       id="total_seats" name="total_seats" value="{{ old('total_seats', $bus->total_seats) }}" required>
                                @error('total_seats')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Changing seat count will regenerate seat layout</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                                    Model <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('model') border-red-300 @enderror"
                                       id="model" name="model" value="{{ old('model', $bus->model) }}" required>
                                @error('model')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                                    Color <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('color') border-red-300 @enderror"
                                       id="color" name="color" value="{{ old('color', $bus->color) }}" required>
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
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('manufacture_year') border-red-300 @enderror"
                                   id="manufacture_year" name="manufacture_year" value="{{ old('manufacture_year', $bus->manufacture_year) }}" required>
                            @error('manufacture_year')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Amenities</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           name="amenities[]" value="WiFi" id="wifi"
                                           {{ in_array('WiFi', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                    <label for="wifi" class="ml-2 text-sm text-gray-700">WiFi</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           name="amenities[]" value="AC" id="ac"
                                           {{ in_array('AC', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                    <label for="ac" class="ml-2 text-sm text-gray-700">Air Conditioning</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           name="amenities[]" value="Charging Port" id="charging"
                                           {{ in_array('Charging Port', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                    <label for="charging" class="ml-2 text-sm text-gray-700">Charging Port</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           name="amenities[]" value="Reading Light" id="reading_light"
                                           {{ in_array('Reading Light', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                    <label for="reading_light" class="ml-2 text-sm text-gray-700">Reading Light</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           name="amenities[]" value="Entertainment System" id="entertainment"
                                           {{ in_array('Entertainment System', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                    <label for="entertainment" class="ml-2 text-sm text-gray-700">Entertainment System</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           name="amenities[]" value="Large Windows" id="windows"
                                           {{ in_array('Large Windows', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                    <label for="windows" class="ml-2 text-sm text-gray-700">Large Windows</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           name="amenities[]" value="Panoramic View" id="panoramic"
                                           {{ in_array('Panoramic View', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                    <label for="panoramic" class="ml-2 text-sm text-gray-700">Panoramic View</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           name="amenities[]" value="Blanket" id="blanket"
                                           {{ in_array('Blanket', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                    <label for="blanket" class="ml-2 text-sm text-gray-700">Blanket</label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('description') border-red-300 @enderror"
                                      id="description" name="description" rows="4"
                                      placeholder="Additional information about the bus...">{{ old('description', $bus->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('operator.buses.show', $bus) }}"
                               class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i> Update Bus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Current Information
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-hashtag text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Bus Number</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $bus->bus_number }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-id-card text-green-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">License Plate</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $bus->license_plate }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-tag text-purple-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Current Type</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $bus->busType->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-users text-red-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Current Seats</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $bus->total_seats }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-{{ $bus->is_active ? 'check-circle' : 'times-circle' }} text-{{ $bus->is_active ? 'green' : 'red' }}-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bus->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $bus->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                {{ $bus->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 px-6 py-4 border-b border-yellow-200">
                    <h3 class="text-lg font-semibold text-yellow-900 flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        Important Notes
                    </h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Changing seat count will regenerate the seat layout</span>
                        </li>
                        <li class="flex items-start">
                            <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                <i class="fas fa-info-circle text-blue-600 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Bus number and license plate must be unique</span>
                        </li>
                        <li class="flex items-start">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                <i class="fas fa-calendar-alt text-green-600 text-xs"></i>
                            </div>
                            <span class="text-sm text-gray-700">Changes won't affect existing bookings</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-red-200">
                <div class="bg-gradient-to-r from-red-50 to-red-100 px-6 py-4 border-b border-red-200">
                    <h3 class="text-lg font-semibold text-red-900 flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                        Danger Zone
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">Permanently delete this bus. This action cannot be undone.</p>
                    <form method="POST" action="{{ route('operator.buses.destroy', $bus) }}"
                          onsubmit="return confirm('Are you sure you want to delete this bus? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i> Delete Bus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
