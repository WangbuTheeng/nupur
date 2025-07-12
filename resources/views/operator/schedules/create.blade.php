@extends('layouts.operator')

@section('title', 'Create Schedule')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Create Schedule</h1>
                    <p class="text-indigo-100">Add a new schedule for your bus route</p>
                </div>
                <div class="mt-4 sm:mt-0">
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
                    <form method="POST" action="{{ route('operator.schedules.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="route_id" class="block text-sm font-medium text-gray-700">Route <span class="text-red-500">*</span></label>
                                <select class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('route_id') border-red-300 @enderror" 
                                        id="route_id" name="route_id" required>
                                    <option value="">Select Route</option>
                                    @foreach($routes ?? [] as $route)
                                        <option value="{{ $route->id }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
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
                                        <option value="{{ $bus->id }}" {{ old('bus_id') == $bus->id ? 'selected' : '' }}>
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
                                       id="travel_date" name="travel_date" value="{{ old('travel_date') }}" required>
                                @error('travel_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="departure_time" class="block text-sm font-medium text-gray-700">Departure Time <span class="text-red-500">*</span></label>
                                <input type="time" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('departure_time') border-red-300 @enderror" 
                                       id="departure_time" name="departure_time" value="{{ old('departure_time') }}" required>
                                @error('departure_time')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="arrival_time" class="block text-sm font-medium text-gray-700">Arrival Time <span class="text-red-500">*</span></label>
                                <input type="time" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('arrival_time') border-red-300 @enderror" 
                                       id="arrival_time" name="arrival_time" value="{{ old('arrival_time') }}" required>
                                @error('arrival_time')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="fare" class="block text-sm font-medium text-gray-700">Fare (Rs.) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" min="0" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('fare') border-red-300 @enderror" 
                                       id="fare" name="fare" value="{{ old('fare') }}" required>
                                @error('fare')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="available_seats" class="block text-sm font-medium text-gray-700">Available Seats <span class="text-red-500">*</span></label>
                                <input type="number" min="1" 
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('available_seats') border-red-300 @enderror" 
                                       id="available_seats" name="available_seats" value="{{ old('available_seats') }}" required>
                                @error('available_seats')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                            <textarea class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('notes') border-red-300 @enderror" 
                                      id="notes" name="notes" rows="3" 
                                      placeholder="Any special notes or instructions...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8 flex items-center justify-end space-x-3">
                            <a href="{{ route('operator.schedules.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-times mr-2"></i>
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-save mr-2"></i>
                                Create Schedule
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
                            <span class="text-sm text-gray-700">Select an active route and bus</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Set realistic departure and arrival times</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Price competitively based on route</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Available seats should not exceed bus capacity</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span class="text-sm text-gray-700">Add notes for special instructions</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-yellow-900">Next Steps</h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-700 mb-3">After creating your schedule:</p>
                    <ol class="text-sm text-gray-700 space-y-1">
                        <li>1. Monitor bookings regularly</li>
                        <li>2. Update status as needed</li>
                        <li>3. Manage seat availability</li>
                        <li>4. Communicate with passengers</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
