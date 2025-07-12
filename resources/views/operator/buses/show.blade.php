@extends('layouts.operator')

@section('title', 'Bus Details - ' . $bus->bus_number)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Bus Details - {{ $bus->bus_number }}</h1>
                    <p class="text-blue-100">{{ $bus->license_plate }}</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('operator.buses.edit', $bus) }}"
                       class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i> Edit Bus
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
        <!-- Bus Information -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        Bus Information
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
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
                                    <p class="text-xs text-gray-500">Bus Type</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $bus->busType->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-car text-yellow-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Model</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $bus->model }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-pink-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-palette text-pink-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Color</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $bus->color }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-calendar text-indigo-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Manufacture Year</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $bus->manufacture_year }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-red-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Total Seats</p>
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
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-clock text-teal-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Created</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $bus->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($bus->amenities && count($bus->amenities) > 0)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-star text-yellow-500 mr-2"></i>
                                Amenities
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($bus->amenities as $amenity)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-check mr-1"></i>
                                        {{ $amenity }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($bus->description)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                <i class="fas fa-file-alt text-gray-500 mr-2"></i>
                                Description
                            </h4>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $bus->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Seat Layout -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-th text-blue-500 mr-2"></i>
                            Seat Layout
                        </h3>
                        <a href="{{ route('operator.buses.edit', $bus) }}"
                           class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-edit mr-1"></i>
                            Configure Layout
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if($bus->seat_layout)
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Layout Info -->
                            <div class="lg:col-span-1">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium text-gray-900 mb-3">Layout Information</h4>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Layout Type:</span>
                                            <span class="font-medium">{{ strtoupper($bus->seat_layout['layout_type'] ?? '2x2') }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Total Seats:</span>
                                            <span class="font-medium">{{ $bus->seat_layout['total_seats'] ?? $bus->total_seats }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Rows:</span>
                                            <span class="font-medium">{{ $bus->seat_layout['rows'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Back Row:</span>
                                            <span class="font-medium">
                                                {{ ($bus->seat_layout['has_back_row'] ?? false) ? 'Yes' : 'No' }}
                                                @if($bus->seat_layout['has_back_row'] ?? false)
                                                    ({{ $bus->seat_layout['back_row_seats'] ?? 0 }} seats)
                                                @endif
                                            </span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-600">Seats Count:</span>
                                            <span class="font-medium">{{ count($bus->seat_layout['seats'] ?? []) }}</span>
                                        </div>
                                    </div>

                                    <!-- Debug info (remove in production) -->
                                    @if(config('app.debug'))
                                        <div class="mt-4 p-2 bg-gray-100 rounded text-xs">
                                            <strong>Debug:</strong> Layout has {{ isset($bus->seat_layout['layout_type']) ? 'new' : 'old' }} format<br>
                                            <strong>Layout Type:</strong> {{ $bus->seat_layout['layout_type'] ?? 'N/A' }}<br>
                                            <strong>Has Seats:</strong> {{ isset($bus->seat_layout['seats']) ? 'Yes (' . count($bus->seat_layout['seats']) . ')' : 'No' }}<br>
                                            <strong>CSS File:</strong> <a href="{{ asset('css/seat-map.css') }}" target="_blank" class="text-blue-600">Check CSS</a><br>
                                            <strong>JS File:</strong> <a href="{{ asset('js/realtime-seat-map.js') }}" target="_blank" class="text-blue-600">Check JS</a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Seat Map Preview -->
                            <div class="lg:col-span-2">
                                @if(config('app.debug'))
                                    <div class="mb-4 text-center">
                                        <button onclick="renderSimpleSeatLayout(@json($bus->seat_layout), 'seatLayoutDisplay')" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                            🔄 Reload Seat Layout
                                        </button>
                                        <button onclick="console.clear()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 text-sm ml-2">
                                            🗑️ Clear Console
                                        </button>
                                    </div>
                                @endif

                                <div id="seatLayoutDisplay" class="min-h-64 border-2 border-dashed border-gray-300 rounded-lg">
                                    <!-- Loading placeholder - will be replaced by JavaScript -->
                                    <div class="flex items-center justify-center h-64 bg-gray-50 rounded-lg">
                                        <div class="text-center">
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                                            <p class="text-gray-500">Loading seat layout...</p>
                                            <p class="text-xs text-gray-400 mt-2">If this doesn't change, check browser console (F12)</p>

                                            @if(config('app.debug'))
                                                <!-- CSS Test Elements -->
                                                <div class="mt-4 p-2 bg-white rounded border">
                                                    <p class="text-xs text-gray-600 mb-2">CSS Test:</p>
                                                    <div class="seat available" style="display: inline-block; margin: 2px;">A1</div>
                                                    <div class="seat booked" style="display: inline-block; margin: 2px;">A2</div>
                                                    <div class="seat selected" style="display: inline-block; margin: 2px;">A3</div>
                                                </div>

                                                <!-- Seat Data Debug -->
                                                <div class="mt-2 p-2 bg-yellow-50 rounded border text-xs">
                                                    <p class="font-semibold mb-1">Seat Data Debug:</p>
                                                    <p><strong>Layout:</strong> {{ $bus->seat_layout['layout_type'] ?? 'N/A' }}</p>
                                                    <p><strong>Rows:</strong> {{ $bus->seat_layout['rows'] ?? 'N/A' }} | <strong>Columns:</strong> {{ $bus->seat_layout['columns'] ?? 'N/A' }}</p>
                                                    <p><strong>Aisle Position:</strong> {{ $bus->seat_layout['aisle_position'] ?? 'N/A' }}</p>
                                                    <p><strong>Total Seats:</strong> {{ count($bus->seat_layout['seats'] ?? []) }}</p>
                                                    @if(isset($bus->seat_layout['seats']) && count($bus->seat_layout['seats']) > 0)
                                                        <p><strong>First 5 seats:</strong>
                                                        @foreach(array_slice($bus->seat_layout['seats'], 0, 5) as $seat)
                                                            {{ $seat['number'] }}({{ $seat['row'] }},{{ $seat['column'] }}){{ $seat['is_window'] ? 'W' : '' }}
                                                        @endforeach
                                                        </p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-3xl mb-4"></i>
                            <p class="text-gray-600 mb-4">No seat layout configured for this bus.</p>
                            <a href="{{ route('operator.buses.seat-layout.preview') }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition">
                                <i class="fas fa-plus mr-2"></i>
                                Configure Seat Layout
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Schedules -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                        Upcoming Schedules
                    </h3>
                </div>
                <div class="p-6">
                    @if($upcomingSchedules->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departure</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrival</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fare</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available Seats</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($upcomingSchedules as $schedule)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $schedule->route->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($schedule->travel_date)->format('M j, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($schedule->arrival_time)->format('H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                Rs. {{ number_format($schedule->fare, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $schedule->available_seats }} seats
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $schedule->status === 'scheduled' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Upcoming Schedules</h3>
                            <p class="text-gray-500 mb-6">Create schedules for this bus to start accepting bookings.</p>
                            <a href="{{ route('operator.schedules.create') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-2"></i> Create Schedule
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-ticket-alt text-purple-500 mr-2"></i>
                        Recent Bookings
                    </h3>
                </div>
                <div class="p-6">
                    @if($recentBookings->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($recentBookings as $booking)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $booking->user->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $booking->schedule->route->name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($booking->schedule->travel_date)->format('M j, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $booking->passenger_count }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                Rs. {{ number_format($booking->total_amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusClasses = [
                                                        'confirmed' => 'bg-green-100 text-green-800',
                                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                                        'cancelled' => 'bg-red-100 text-red-800',
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-ticket-alt text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">No Recent Bookings</h3>
                            <p class="text-gray-500">No bookings have been made for this bus yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics & Actions -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>
                        Statistics
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_schedules'] }}</div>
                            <div class="text-xs text-blue-500 font-medium">Total Schedules</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $stats['upcoming_schedules'] }}</div>
                            <div class="text-xs text-green-500 font-medium">Upcoming</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_bookings'] }}</div>
                            <div class="text-xs text-purple-500 font-medium">Total Bookings</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-lg font-bold text-yellow-600">Rs. {{ number_format($stats['monthly_revenue'], 0) }}</div>
                            <div class="text-xs text-yellow-500 font-medium">Monthly Revenue</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-bolt text-yellow-500 mr-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('operator.schedules.create') }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-calendar-plus mr-2"></i> Create Schedule
                    </a>
                    <a href="{{ route('operator.buses.edit', $bus) }}"
                       class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i> Edit Bus Details
                    </a>
                    <form method="POST" action="{{ route('operator.buses.toggle-status', $bus) }}" class="w-full">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white {{ $bus->is_active ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500' }} focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200">
                            <i class="fas fa-{{ $bus->is_active ? 'pause' : 'play' }} mr-2"></i>
                            {{ $bus->is_active ? 'Deactivate Bus' : 'Activate Bus' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Bus Type Info -->
            @if($bus->busType)
                <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-info-circle text-teal-500 mr-2"></i>
                            Bus Type Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-tag text-teal-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Type</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $bus->busType->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Total Seats</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $bus->busType->total_seats ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-calculator text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Fare Multiplier</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $bus->busType->base_fare_multiplier ?? 'N/A' }}x</p>
                            </div>
                        </div>
                        @if($bus->busType->description)
                            <div class="pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-500 mb-2">Description</p>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ $bus->busType->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
/* Inline seat map styles to ensure they always load */
.seat-map-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.bus-layout-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 15px;
}

.bus-frame {
    background: linear-gradient(to bottom, #f8fafc, #e2e8f0);
    border: 3px solid #475569;
    border-radius: 25px;
    padding: 15px;
    position: relative;
    min-height: 300px;
}

.bus-top-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 8px 15px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 15px;
    border: 2px solid #cbd5e1;
}

.bus-door {
    background: #3b82f6;
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.driver-seat {
    background: #10b981;
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.main-seating-area {
    display: flex;
    flex-direction: column;
    gap: 12px;
    align-items: center;
    width: 100%;
}

.seat-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    width: 100%;
    min-height: 40px;
}

.seat {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.seat.available {
    background: #22c55e;
    color: white;
}

.seat.window-seat {
    background: #3b82f6;
    color: white;
}

.seat.back-row-seat {
    background: #8b5cf6;
    color: white;
}

.aisle-space {
    width: 24px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 16px;
    font-weight: bold;
    background: rgba(107, 114, 128, 0.1);
    border-radius: 4px;
}

.back-row-container {
    display: flex;
    justify-content: center;
    gap: 4px;
    background: rgba(139, 92, 246, 0.1);
    padding: 8px;
    border-radius: 12px;
    border: 2px dashed #8b5cf6;
}
</style>
@endpush

@push('scripts')
<script>
console.log('🚀 Seat Layout Script Loading...');

// Simple and robust seat layout renderer
function renderBusLayout(seatData, containerId) {
    console.log('🚀 Starting renderBusLayout function...');
    console.log('🚌 Rendering bus layout for container:', containerId);
    console.log('📊 Seat data:', seatData);

    // Debug: Show seat arrangement
    console.log('🔍 Layout details:');
    console.log('  - Layout type:', seatData.layout_type);
    console.log('  - Total seats:', seatData.total_seats);
    console.log('  - Rows:', seatData.rows);
    console.log('  - Columns:', seatData.columns);
    console.log('  - Aisle position:', seatData.aisle_position);
    console.log('  - Has back row:', seatData.has_back_row);

    // Debug: Show first few seats to understand data format
    if (seats.length > 0) {
        console.log('🔍 First 3 seats (raw data):');
        seats.slice(0, 3).forEach((seat, i) => {
            console.log(`  Seat ${i + 1}:`, {
                number: seat.number,
                seat_number: seat.seat_number,
                row: seat.row,
                column: seat.column,
                is_window: seat.is_window
            });
        });
    }

    const container = document.getElementById(containerId);
    if (!container) {
        console.error('❌ Container not found:', containerId);
        return false;
    }

    const seats = seatData.seats || [];
    const layoutType = seatData.layout_type || '2x2';
    const hasBackRow = seatData.has_back_row || false;
    const aislePosition = seatData.aisle_position || 2;

    // Create the complete layout HTML
    let html = `
        <div class="seat-map-container">
            <div class="bus-layout-container">
                <div class="bus-frame">
                    <!-- Top section with door and driver -->
                    <div class="bus-top-section">
                        <div class="bus-door" title="Front Door">🚪 Door</div>
                        <div class="driver-seat" title="Driver">👨‍✈️ Driver</div>
                    </div>

                    <!-- Main seating area -->
                    <div class="main-seating-area">
    `;

    // Normalize seat data format (handle both old and new formats)
    seats.forEach(seat => {
        // Ensure 'number' property exists (handle both 'number' and 'seat_number')
        if (!seat.number && seat.seat_number) {
            seat.number = seat.seat_number;
        }
        // Ensure row and column are numbers
        seat.row = parseInt(seat.row) || 1;
        seat.column = parseInt(seat.column) || 1;
    });

    // Group seats by row
    const seatsByRow = {};
    seats.forEach(seat => {
        if (!seatsByRow[seat.row]) seatsByRow[seat.row] = [];
        seatsByRow[seat.row].push(seat);
    });

    let maxRow = Math.max(...seats.map(s => s.row));

    // Debug: Show seat arrangement
    console.log('🗺️ Seat arrangement by row:');
    console.log('Total rows found:', maxRow);
    console.log('Seats by row object:', seatsByRow);

    // Validate maxRow
    if (!maxRow || maxRow < 1) {
        console.error('❌ Invalid maxRow:', maxRow);
        maxRow = 8; // fallback
    }

    for (let r = 1; r <= maxRow; r++) {
        const rowSeats = seatsByRow[r] || [];
        console.log(`  Row ${r} (${rowSeats.length} seats):`, rowSeats.map(s => `${s.number}(R${s.row},C${s.column}${s.is_window ? 'W' : ''})`).join(', '));
    }

    // Check if all seats are in the same row (common issue)
    const allRows = seats.map(s => s.row);
    const uniqueRows = [...new Set(allRows)];
    console.log('All seat rows:', allRows);
    console.log('Unique rows:', uniqueRows);

    if (uniqueRows.length === 1) {
        console.warn('⚠️ WARNING: All seats are in the same row! Fixing layout...');

        // Fix the layout by redistributing seats across rows
        const seatsPerRow = layoutType === '2x1' ? 3 : (layoutType === '2x2' ? 4 : 5);
        const totalRegularSeats = hasBackRow ? seats.length - (seatData.back_row_seats || 5) : seats.length;
        const numberOfRows = Math.ceil(totalRegularSeats / seatsPerRow);

        console.log(`Redistributing ${totalRegularSeats} regular seats across ${numberOfRows} rows (${seatsPerRow} seats per row)`);

        // Redistribute seats
        seats.forEach((seat, index) => {
            if (index < totalRegularSeats) {
                // Regular seats
                const newRow = Math.floor(index / seatsPerRow) + 1;
                const newCol = (index % seatsPerRow) + 1;

                // Adjust column for aisle
                const adjustedCol = newCol > aislePosition ? newCol + 1 : newCol;

                seat.row = newRow;
                seat.column = adjustedCol;
                seat.is_window = (adjustedCol === 1 || adjustedCol === (seatData.columns || 5));
            } else {
                // Back row seats
                seat.row = numberOfRows + 1;
                seat.column = (index - totalRegularSeats) + 1;
                seat.type = 'back_row';
            }
        });

        // Rebuild seatsByRow with fixed data
        const fixedSeatsByRow = {};
        seats.forEach(seat => {
            if (!fixedSeatsByRow[seat.row]) fixedSeatsByRow[seat.row] = [];
            fixedSeatsByRow[seat.row].push(seat);
        });

        // Update variables
        Object.assign(seatsByRow, fixedSeatsByRow);
        const newMaxRow = Math.max(...seats.map(s => s.row));

        console.log('✅ Layout fixed! New arrangement:');
        for (let r = 1; r <= newMaxRow; r++) {
            const rowSeats = seatsByRow[r] || [];
            console.log(`  Row ${r}:`, rowSeats.map(s => `${s.number}(R${s.row},C${s.column})`).join(', '));
        }

        // Update maxRow for rendering
        maxRow = newMaxRow;
    }

    // Render each row
    for (let rowNum = 1; rowNum <= maxRow; rowNum++) {
        const rowSeats = (seatsByRow[rowNum] || []).sort((a, b) => a.column - b.column);
        const isBackRow = hasBackRow && rowNum === maxRow;

        if (rowSeats.length === 0) continue; // Skip empty rows

        html += `<div class="seat-row" data-row="${rowNum}">`;

        if (isBackRow) {
            // Back row - continuous seats across full width
            html += '<div class="back-row-container">';
            rowSeats.forEach(seat => {
                const seatNumber = seat.number || seat.seat_number || `BR${seat.column}`;
                html += `<div class="seat back-row-seat" title="Seat ${seatNumber}">${seatNumber}</div>`;
            });
            html += '</div>';
        } else {
            // Regular row - arrange seats by column position
            const maxColumn = Math.max(...rowSeats.map(s => s.column));

            for (let col = 1; col <= maxColumn; col++) {
                // Add aisle space before right side seats
                if (col === aislePosition + 1) {
                    html += '<div class="aisle-space">|</div>';
                }

                // Find seat for this column
                const seat = rowSeats.find(s => s.column === col);
                if (seat) {
                    const seatClass = seat.is_window ? 'seat window-seat' : 'seat available';
                    const windowText = seat.is_window ? ' (Window)' : '';
                    const seatNumber = seat.number || seat.seat_number || `${col}`;

                    html += `<div class="${seatClass}" title="Seat ${seatNumber}${windowText}">${seatNumber}</div>`;
                } else {
                    // Empty space for missing seats
                    html += '<div style="width: 36px; height: 36px;"></div>';
                }
            }
        }

        html += '</div>';
    }

    html += `
                    </div>

                    <!-- Layout info -->
                    <div style="text-align: center; margin-top: 15px; padding: 8px; background: rgba(255,255,255,0.8); border-radius: 8px; font-size: 12px; color: #6b7280;">
                        <strong>${layoutType.toUpperCase()}</strong> Layout • <strong>${seats.length}</strong> Seats • ${hasBackRow ? 'With' : 'Without'} Back Row
                    </div>
                </div>
            </div>
        </div>
    `;

    try {
        container.innerHTML = html;
        console.log('✅ Bus layout rendered successfully!');
        console.log('📏 Final HTML length:', html.length);
        console.log('🎯 Rendered rows:', maxRow);
        return true;
    } catch (error) {
        console.error('❌ Error setting innerHTML:', error);
        console.log('🔄 Trying simple fallback renderer...');
        return renderSimpleSeatLayout(seatData, containerId);
    }
}

// Simple fallback renderer
function renderSimpleSeatLayout(seatData, containerId) {
    console.log('🔧 Using simple fallback renderer...');

    const container = document.getElementById(containerId);
    if (!container) {
        console.error('❌ Container not found:', containerId);
        return false;
    }

    const seats = seatData.seats || [];
    const layoutType = seatData.layout_type || '2x2';

    let html = `
        <div style="max-width: 600px; margin: 0 auto; padding: 20px; background: #f8fafc; border-radius: 12px;">
            <div style="background: #e2e8f0; border: 3px solid #475569; border-radius: 25px; padding: 15px;">
                <!-- Top section -->
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding: 8px; background: rgba(255,255,255,0.7); border-radius: 15px;">
                    <div style="background: #3b82f6; color: white; padding: 8px 12px; border-radius: 8px;">🚪 Door</div>
                    <div style="background: #10b981; color: white; padding: 8px 12px; border-radius: 8px;">👨‍✈️ Driver</div>
                </div>

                <!-- Simple seat grid -->
                <div style="display: flex; flex-direction: column; gap: 8px; align-items: center;">
    `;

    // Group seats by row
    const seatsByRow = {};
    seats.forEach(seat => {
        const row = parseInt(seat.row) || 1;
        if (!seatsByRow[row]) seatsByRow[row] = [];
        seatsByRow[row].push(seat);
    });

    const maxRow = Math.max(...Object.keys(seatsByRow).map(r => parseInt(r)));

    // Render each row
    for (let row = 1; row <= maxRow; row++) {
        const rowSeats = (seatsByRow[row] || []).sort((a, b) => (a.column || 1) - (b.column || 1));

        html += '<div style="display: flex; gap: 6px; justify-content: center;">';

        rowSeats.forEach((seat, index) => {
            const seatNumber = seat.number || seat.seat_number || `${row}-${index + 1}`;
            const isWindow = seat.is_window;
            const bgColor = isWindow ? '#3b82f6' : '#22c55e';

            // Add aisle space after 2nd seat for 2x2 layout
            if (index === 2 && layoutType === '2x2') {
                html += '<div style="width: 20px; display: flex; align-items: center; justify-content: center; color: #6b7280;">|</div>';
            }

            html += `<div style="
                width: 36px;
                height: 36px;
                background: ${bgColor};
                color: white;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                font-size: 11px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            " title="Seat ${seatNumber}">${seatNumber}</div>`;
        });

        html += '</div>';
    }

    html += `
                </div>

                <!-- Info -->
                <div style="text-align: center; margin-top: 15px; padding: 8px; background: rgba(255,255,255,0.8); border-radius: 8px; font-size: 12px; color: #6b7280;">
                    <strong>${layoutType.toUpperCase()}</strong> Layout • <strong>${seats.length}</strong> Seats
                </div>
            </div>
        </div>
    `;

    container.innerHTML = html;
    console.log('✅ Simple layout rendered successfully!');
    return true;
}
</script>
<script>
// Force immediate rendering - try simple approach first
console.log('🚀 Script loaded, starting immediate rendering...');

@if($bus->seat_layout)
    const seatData = @json($bus->seat_layout);
    console.log('📊 Seat data loaded:', seatData);

    // Try simple renderer immediately
    function trySimpleRender() {
        console.log('🎯 Trying simple renderer...');
        const container = document.getElementById('seatLayoutDisplay');
        if (!container) {
            console.log('❌ Container not found yet');
            return false;
        }

        return renderSimpleSeatLayout(seatData, 'seatLayoutDisplay');
    }

    // Try immediately
    if (!trySimpleRender()) {
        // Try on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            console.log('📄 DOM ready, trying again...');
            if (!trySimpleRender()) {
                // Final attempt after delay
                setTimeout(function() {
                    console.log('🔄 Final attempt...');
                    trySimpleRender();
                }, 1000);
            }
        });
    }
@else
    console.log('ℹ️ No seat layout data available');
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('seatLayoutDisplay');
        if (container) {
            container.innerHTML = '<div style="text-align: center; padding: 2rem; color: #6b7280;">No seat layout configured</div>';
        }
    });
@endif

// Backup execution for window load
window.addEventListener('load', function() {
    console.log('🌐 Window fully loaded - checking seat layout...');
    const container = document.getElementById('seatLayoutDisplay');

    if (container && container.innerHTML.includes('Loading seat layout')) {
        console.log('🔄 Still loading, attempting final render...');
        @if($bus->seat_layout)
            const seatData = @json($bus->seat_layout);
            renderBusLayout(seatData, 'seatLayoutDisplay');
        @endif
    }
});

// Simplified seat layout preview class for show page
class SeatLayoutPreview {
    constructor(layout, container) {
        this.layout = layout;
        this.container = container;
    }

    render() {
        const { layout_type, rows, seats, driver_seat, door, has_back_row } = this.layout;

        let html = '<div class="seat-map-container">';

        // Bus layout container
        html += '<div class="bus-layout-container">';
        html += '<div class="bus-frame">';

        // Top section with driver seat and door
        html += '<div class="bus-top-section">';
        html += '<div class="bus-door" title="Front Door">🚪</div>';
        html += '<div class="bus-front-space"></div>';
        html += '<div class="driver-seat" title="Driver">👨‍✈️</div>';
        html += '</div>';

        // Main seating area
        html += this.renderMainSeatingArea();

        html += '</div></div></div>';

        this.container.innerHTML = html;
    }

    renderMainSeatingArea() {
        const { rows, seats, has_back_row, aisle_position } = this.layout;

        let html = '<div class="main-seating-area">';

        // Group seats by row
        const seatsByRow = {};
        seats.forEach(seat => {
            if (!seatsByRow[seat.row]) {
                seatsByRow[seat.row] = [];
            }
            seatsByRow[seat.row].push(seat);
        });

        // Render each row
        for (let rowNum = 1; rowNum <= rows; rowNum++) {
            const rowSeats = seatsByRow[rowNum] || [];
            const isBackRow = has_back_row && rowNum === rows;

            html += `<div class="seat-row ${isBackRow ? 'back-row' : 'regular-row'}" data-row="${rowNum}">`;

            if (isBackRow) {
                html += this.renderBackRow(rowSeats);
            } else {
                html += this.renderRegularRow(rowSeats, aisle_position);
            }

            html += '</div>';
        }

        html += '</div>';
        return html;
    }

    renderRegularRow(rowSeats, aislePosition) {
        let html = '';

        rowSeats.sort((a, b) => a.column - b.column);

        let currentColumn = 1;

        rowSeats.forEach(seat => {
            if (currentColumn === aislePosition + 1) {
                html += '<div class="aisle-space"></div>';
            }

            const isWindow = seat.is_window ? 'window-seat' : '';

            html += `<div class="seat available ${isWindow}" title="Seat ${seat.number}">
                        ${seat.number}
                     </div>`;

            currentColumn = seat.column + 1;
        });

        return html;
    }

    renderBackRow(rowSeats) {
        let html = '<div class="back-row-container">';

        rowSeats.sort((a, b) => a.column - b.column);

        rowSeats.forEach(seat => {
            html += `<div class="seat available back-row-seat" title="Seat ${seat.number}">
                        ${seat.number}
                     </div>`;
        });

        html += '</div>';
        return html;
    }
}
</script>
@endpush

@endsection
