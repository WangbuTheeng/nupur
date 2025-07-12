@extends('layouts.operator')

@section('title', 'Edit Schedule')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Edit Schedule</h1>
                    <p class="text-indigo-100">{{ $schedule->route->name ?? 'N/A' }} - {{ $schedule->travel_date ?? 'N/A' }}</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('operator.schedules.show', $schedule) }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View Schedule
                    </a>
                    <a href="{{ route('operator.schedules.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Schedules
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Schedule Information</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('operator.schedules.update', $schedule) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="route_id" class="block text-sm font-medium text-gray-700">Route <span class="text-red-500">*</span></label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('route_id') border-red-300 @enderror" 
                                        id="route_id" name="route_id" required>
                                    <option value="">Select Route</option>
                                    @foreach($routes ?? [] as $route)
                                        <option value="{{ $route->id }}" {{ old('route_id', $schedule->route_id ?? '') == $route->id ? 'selected' : '' }}>
                                            {{ $route->name }} ({{ $route->sourceCity->name }} → {{ $route->destinationCity->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('route_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="bus_id" class="block text-sm font-medium text-gray-700">Bus <span class="text-red-500">*</span></label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('bus_id') border-red-300 @enderror" 
                                        id="bus_id" name="bus_id" required>
                                    <option value="">Select Bus</option>
                                    @foreach($buses ?? [] as $bus)
                                        <option value="{{ $bus->id }}" {{ old('bus_id', $schedule->bus_id ?? '') == $bus->id ? 'selected' : '' }}>
                                            {{ $bus->bus_number }} - {{ $bus->model }} ({{ $bus->total_seats }} seats)
                                        </option>
                                    @endforeach
                                </select>
                                @error('bus_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label for="travel_date" class="block text-sm font-medium text-gray-700">Travel Date <span class="text-red-500">*</span></label>
                                <input type="date" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('travel_date') border-red-300 @enderror" 
                                       id="travel_date" name="travel_date" value="{{ old('travel_date', $schedule->travel_date ?? '') }}" required>
                                @error('travel_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="departure_time" class="block text-sm font-medium text-gray-700">Departure Time <span class="text-red-500">*</span></label>
                                <input type="time" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('departure_time') border-red-300 @enderror" 
                                       id="departure_time" name="departure_time" value="{{ old('departure_time', $schedule->departure_time ?? '') }}" required>
                                @error('departure_time')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="arrival_time" class="block text-sm font-medium text-gray-700">Arrival Time <span class="text-red-500">*</span></label>
                                <input type="time" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('arrival_time') border-red-300 @enderror" 
                                       id="arrival_time" name="arrival_time" value="{{ old('arrival_time', $schedule->arrival_time ?? '') }}" required>
                                @error('arrival_time')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label for="fare" class="block text-sm font-medium text-gray-700">Fare (Rs.) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('fare') border-red-300 @enderror" 
                                       id="fare" name="fare" value="{{ old('fare', $schedule->fare ?? '') }}" required>
                                @error('fare')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="available_seats" class="block text-sm font-medium text-gray-700">Available Seats <span class="text-red-500">*</span></label>
                                <input type="number" min="0" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('available_seats') border-red-300 @enderror" 
                                       id="available_seats" name="available_seats" value="{{ old('available_seats', $schedule->available_seats ?? '') }}" required>
                                @error('available_seats')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Reducing seats may affect existing bookings</p>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('status') border-red-300 @enderror" 
                                        id="status" name="status" required>
                                    <option value="scheduled" {{ old('status', $schedule->status ?? 'scheduled') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="completed" {{ old('status', $schedule->status ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ old('status', $schedule->status ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                            <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('notes') border-red-300 @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Any special notes or instructions...">{{ old('notes', $schedule->notes ?? '') }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8 flex items-center justify-end space-x-3">
                            <a href="{{ route('operator.schedules.show', $schedule) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-save mr-2"></i>
                                Update Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-blue-900">Current Information</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Current Route</h4>
                            <p class="text-sm text-gray-900">{{ $schedule->route->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Current Bus</h4>
                            <p class="text-sm text-gray-900">{{ $schedule->bus->bus_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Current Status</h4>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ ($schedule->status ?? 'scheduled') === 'scheduled' ? 'bg-blue-100 text-blue-800' : (($schedule->status ?? '') === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($schedule->status ?? 'scheduled') }}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Total Bookings</h4>
                            <p class="text-sm text-gray-900">{{ $schedule->bookings_count ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-yellow-900">Important Notes</h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Changing bus or date may affect existing bookings</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Reducing available seats may cancel some bookings</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-clock text-purple-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Time changes should be communicated to passengers</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-red-900">Danger Zone</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">Permanently delete this schedule. This action cannot be undone.</p>
                    <form method="POST" action="{{ route('operator.schedules.destroy', $schedule) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this schedule? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i>
                            Delete Schedule
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
