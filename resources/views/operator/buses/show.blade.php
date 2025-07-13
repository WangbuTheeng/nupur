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
                                        <button onclick="regenerateSeatLayout({{ $bus->id }})" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm ml-2">
                                            🔧 Regenerate Layout
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
    .bus-frame {
        background: #f0f4f8;
        border: 2px solid #a0aec0;
        border-radius: 20px;
        padding: 20px;
        max-width: 400px;
        margin: auto;
    }
    .bus-top-section {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .bus-door, .driver-seat {
        padding: 10px;
        border-radius: 5px;
        font-weight: bold;
        color: white;
    }
    .bus-door { background-color: #4299e1; }
    .driver-seat { background-color: #48bb78; }
    .main-seating-area {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .seat-row {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }
    .seat {
        width: 40px;
        height: 40px;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
    }
    .seat.available { background-color: #68d391; }
    .seat.window-seat { background-color: #63b3ed; }
    .seat.back-row-seat { background-color: #a78bfa; }
    .aisle-space { width: 20px; }
</style>
@endpush

@push('scripts')
<script>
    function renderSeatLayout(seatData, containerId) {
        console.log('🚌 Rendering seat layout for:', containerId);
        console.log('📊 Seat data:', seatData);

        const container = document.getElementById(containerId);
        if (!container) {
            console.error('❌ Container not found:', containerId);
            return;
        }

        const { seats, has_back_row, layout_type, aisle_position } = seatData;

        let html = `
            <div class="seat-map-container">
                <div class="bus-layout-container">
                    <div class="bus-frame">
                        <div class="bus-top-section">
                            <div class="bus-door" title="Front Door">🚪</div>
                            <div class="bus-front-space"></div>
                            <div class="driver-seat" title="Driver">👨‍✈️</div>
                        </div>
                        <div class="main-seating-area">
        `;

        const seatsByRow = {};
        if (seats) {
            seats.forEach(seat => {
                if (!seatsByRow[seat.row]) {
                    seatsByRow[seat.row] = [];
                }
                seatsByRow[seat.row].push(seat);
            });
        }

        const maxRow = Object.keys(seatsByRow).length > 0 ? Math.max(...Object.keys(seatsByRow).map(r => parseInt(r, 10))) : 0;

        for (let r = 1; r <= maxRow; r++) {
            const rowSeats = seatsByRow[r] || [];
            const isBackRow = has_back_row && r === maxRow;

            html += `<div class="seat-row ${isBackRow ? 'back-row' : 'regular-row'}" data-row="${r}">`;

            if (isBackRow) {
                html += '<div class="back-row-container">';
                rowSeats.sort((a,b) => a.column - b.column).forEach(seat => {
                    html += `<div class="seat available back-row-seat" title="Seat ${seat.number}">${seat.number}</div>`;
                });
                html += '</div>';
            } else {
                // Use the fixed column-based rendering
                const seatsByColumn = {};
                rowSeats.forEach(seat => {
                    seatsByColumn[seat.column] = seat;
                });

                const maxColumns = Math.max(...rowSeats.map(seat => seat.column));

                for (let col = 1; col <= maxColumns; col++) {
                    if (seatsByColumn[col]) {
                        const seat = seatsByColumn[col];
                        const isWindow = seat.is_window ? 'window-seat' : '';
                        html += `<div class="seat available ${isWindow}" title="Seat ${seat.number}">${seat.number}</div>`;
                    } else {
                        html += '<div class="aisle-space"></div>';
                    }
                }
            }

            html += `</div>`;
        }

        html += `</div></div></div></div>`;
        container.innerHTML = html;

        console.log('✅ Seat layout rendered successfully');
    }

    // Simple debug function for the reload button
    function renderSimpleSeatLayout(seatData, containerId) {
        console.log('🔄 Debug reload triggered');
        renderSeatLayout(seatData, containerId);
    }

    // Function to regenerate seat layout
    function regenerateSeatLayout(busId) {
        console.log('🔧 Regenerating seat layout for bus:', busId);

        fetch(`/fix/bus-seat-layout/${busId}`)
            .then(response => response.json())
            .then(data => {
                console.log('✅ Layout regenerated:', data);
                if (data.success) {
                    alert('Seat layout regenerated successfully! Refreshing page...');
                    window.location.reload();
                } else {
                    alert('Failed to regenerate seat layout: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('❌ Error regenerating layout:', error);
                alert('Error regenerating seat layout. Check console for details.');
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        console.log('🚀 DOM loaded, initializing seat layout...');
        @if($bus->seat_layout)
            console.log('📋 Bus seat layout data:', @json($bus->seat_layout));
            renderSeatLayout(@json($bus->seat_layout), 'seatLayoutDisplay');
        @else
            console.log('⚠️ No seat layout data available');
        @endif
    });
</script>
@endpush

@endsection
