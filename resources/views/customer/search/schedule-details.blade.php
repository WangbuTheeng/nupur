@extends('layouts.app')

@section('title', 'Schedule Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-4">Schedule Details</h1>
                    <p class="text-xl text-indigo-100">
                        {{ $schedule->route->sourceCity->name }} → {{ $schedule->route->destinationCity->name }}
                    </p>
                    <p class="text-indigo-200">
                        {{ $schedule->travel_date->format('l, M d, Y') }} • {{ \Carbon\Carbon::parse($schedule->departure_time)->format('g:i A') }}
                    </p>
                </div>
                <div class="mt-6 lg:mt-0 text-right">
                    <div class="text-3xl font-bold">Rs. {{ number_format($schedule->fare) }}</div>
                    <div class="text-indigo-200">per seat</div>
                    <div class="text-green-300 font-medium">
                        {{ $schedule->available_seats }} seats available
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Schedule Information -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Trip Details -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Trip Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Departure -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Departure</h3>
                                    <p class="text-gray-600">{{ $schedule->route->sourceCity->name }}</p>
                                </div>
                            </div>
                            <div class="ml-15">
                                <p class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('g:i A') }}</p>
                                <p class="text-gray-600">{{ $schedule->travel_date->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <!-- Arrival -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Arrival</h3>
                                    <p class="text-gray-600">{{ $schedule->route->destinationCity->name }}</p>
                                </div>
                            </div>
                            <div class="ml-15">
                                <p class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($schedule->arrival_time)->format('g:i A') }}</p>
                                <p class="text-gray-600">{{ $schedule->travel_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Journey Duration -->
                    <div class="mt-8 p-4 bg-blue-50 rounded-xl">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span class="text-blue-800 font-medium">
                                Journey Duration: 
                                @php
                                    $departure = \Carbon\Carbon::parse($schedule->departure_time);
                                    $arrival = \Carbon\Carbon::parse($schedule->arrival_time);
                                    $duration = $departure->diff($arrival);
                                @endphp
                                {{ $duration->format('%h hours %i minutes') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Bus Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Bus Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Bus Number:</span>
                                <span class="font-semibold">{{ $schedule->bus->bus_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Bus Type:</span>
                                <span class="font-semibold">{{ $schedule->bus->busType->name ?? 'Standard' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Seats:</span>
                                <span class="font-semibold">{{ $schedule->bus->total_seats }}</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Operator:</span>
                                <span class="font-semibold">{{ $schedule->operator->company_name ?? $schedule->operator->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Available Seats:</span>
                                <span class="font-semibold text-green-600">{{ $schedule->available_seats }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($schedule->status === 'scheduled') bg-green-100 text-green-800
                                    @elseif($schedule->status === 'boarding') bg-yellow-100 text-yellow-800
                                    @elseif($schedule->status === 'departed') bg-blue-100 text-blue-800
                                    @elseif($schedule->status === 'arrived') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($schedule->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Amenities -->
                    @if($schedule->bus->amenities && count($schedule->bus->amenities) > 0)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Amenities</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($schedule->bus->amenities as $amenity)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                        {{ $amenity }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Seat Map Preview -->
                @if(isset($seatMap['seats']) && is_array($seatMap['seats']))
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Seat Availability</h2>

                        <!-- Seat Legend -->
                        <div class="flex flex-wrap gap-4 mb-6">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-green-500 rounded"></div>
                                <span class="text-sm text-gray-700">Available</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-red-500 rounded"></div>
                                <span class="text-sm text-gray-700">Booked</span>
                            </div>
                        </div>

                        <!-- Proper Bus Seat Layout -->
                        <div id="seat-map-container" class="mb-6">
                            <div class="seat-map-container">
                                <div class="bus-layout-container">
                                    <div class="bus-frame">
                                        <!-- Top section with door and driver -->
                                        <div class="bus-top-section">
                                            <div class="bus-door" title="Front Door">🚪 Door</div>
                                            <div class="bus-front-space"></div>
                                            <div class="driver-seat" title="Driver">👨‍✈️ Driver</div>
                                        </div>

                                        <!-- Main seating area -->
                                        <div class="main-seating-area">
                                            @php
                                                // Group seats by row
                                                $seatsByRow = [];
                                                foreach($seatMap['seats'] as $seat) {
                                                    $row = $seat['row'] ?? 1;
                                                    if (!isset($seatsByRow[$row])) {
                                                        $seatsByRow[$row] = [];
                                                    }
                                                    $seatsByRow[$row][] = $seat;
                                                }

                                                $maxRow = !empty($seatsByRow) ? max(array_keys($seatsByRow)) : 1;
                                                $hasBackRow = $seatMap['has_back_row'] ?? true;
                                                $aislePosition = $seatMap['aisle_position'] ?? 2;
                                            @endphp

                                            @for($rowNum = 1; $rowNum <= $maxRow; $rowNum++)
                                                @php
                                                    $rowSeats = $seatsByRow[$rowNum] ?? [];
                                                    $isBackRow = $hasBackRow && $rowNum === $maxRow;

                                                    // Sort seats by position
                                                    usort($rowSeats, function($a, $b) {
                                                        return ($a['position'] ?? 0) <=> ($b['position'] ?? 0);
                                                    });
                                                @endphp

                                                <div class="seat-row {{ $isBackRow ? 'back-row' : 'regular-row' }}" data-row="{{ $rowNum }}">
                                                    @if($isBackRow)
                                                        <!-- Back row - continuous seats -->
                                                        <div class="back-row-seats">
                                                            @foreach($rowSeats as $seat)
                                                                @php
                                                                    $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? 'N/A';
                                                                    $isBooked = $seat['is_booked'] ?? false;
                                                                @endphp
                                                                <div class="seat back-seat {{ $isBooked ? 'seat-booked' : 'seat-available' }}"
                                                                     data-seat-number="{{ $seatNumber }}"
                                                                     title="Seat {{ $seatNumber }}">
                                                                    {{ $seatNumber }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <!-- Regular row with aisle -->
                                                        <div class="regular-row-seats">
                                                            @php $seatIndex = 0; @endphp
                                                            @foreach($rowSeats as $seat)
                                                                @php
                                                                    $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? 'N/A';
                                                                    $isBooked = $seat['is_booked'] ?? false;
                                                                    $seatIndex++;
                                                                @endphp

                                                                <div class="seat regular-seat {{ $isBooked ? 'seat-booked' : 'seat-available' }}"
                                                                     data-seat-number="{{ $seatNumber }}"
                                                                     title="Seat {{ $seatNumber }}">
                                                                    {{ $seatNumber }}
                                                                </div>

                                                                @if($seatIndex == $aislePosition && $seatIndex < count($rowSeats))
                                                                    <div class="aisle"></div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Booking Actions -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Book This Trip</h3>
                    
                    <!-- Price Summary -->
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Base Fare:</span>
                            <span class="font-semibold">Rs. {{ number_format($schedule->fare) }}</span>
                        </div>
                        <div class="border-t pt-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Price per seat:</span>
                                <span class="text-indigo-600">Rs. {{ number_format($schedule->fare) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Button -->
                    @if($schedule->available_seats > 0 && $schedule->status === 'scheduled')
                        <a href="{{ route('booking.seat-selection', $schedule) }}" 
                           class="w-full bg-indigo-600 text-white px-6 py-4 rounded-xl hover:bg-indigo-700 font-semibold transition-colors text-center block">
                            Select Seats & Book
                        </a>
                    @else
                        <button class="w-full bg-gray-300 text-gray-500 px-6 py-4 rounded-xl font-semibold cursor-not-allowed" disabled>
                            @if($schedule->available_seats <= 0)
                                Sold Out
                            @else
                                Not Available
                            @endif
                        </button>
                    @endif

                    <!-- Additional Info -->
                    <div class="mt-6 space-y-3 text-sm text-gray-600">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Instant confirmation</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Mobile ticket</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Free cancellation</span>
                        </div>
                    </div>
                </div>

                <!-- Similar Schedules -->
                @if($similarSchedules->count() > 0)
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 mt-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Similar Schedules</h3>
                        <div class="space-y-3">
                            @foreach($similarSchedules->take(3) as $similar)
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-medium text-sm">{{ \Carbon\Carbon::parse($similar->departure_time)->format('g:i A') }}</p>
                                            <p class="text-xs text-gray-500">{{ $similar->travel_date->format('M d') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-sm">Rs. {{ number_format($similar->fare) }}</p>
                                            <a href="{{ route('search.schedule', $similar) }}" 
                                               class="text-xs text-indigo-600 hover:text-indigo-800">View</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Bus Seat Layout Styles */
.seat-map-container {
    max-width: 400px;
    margin: 0 auto;
}

.bus-layout-container {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.bus-frame {
    background: #ffffff;
    border-radius: 15px;
    border: 3px solid #cbd5e1;
    overflow: hidden;
    box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.05);
}

.bus-top-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 12px 16px;
    font-size: 12px;
    font-weight: 600;
}

.bus-door {
    background: rgba(255, 255, 255, 0.2);
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 10px;
}

.driver-seat {
    background: rgba(255, 255, 255, 0.2);
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 10px;
}

.main-seating-area {
    padding: 16px;
    background: #f8fafc;
}

.seat-row {
    margin-bottom: 12px;
    display: flex;
    justify-content: center;
}

.regular-row-seats, .back-row-seats {
    display: flex;
    align-items: center;
    gap: 8px;
}

.seat {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 600;
    border: 2px solid;
    cursor: default;
    transition: all 0.2s ease;
}

.seat-available {
    background: #10b981;
    border-color: #059669;
    color: white;
}

.seat-booked {
    background: #ef4444;
    border-color: #dc2626;
    color: white;
}

.back-seat {
    width: 40px;
    height: 32px;
}

.aisle {
    width: 16px;
    height: 32px;
    background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%);
    position: relative;
}

.aisle::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 2px;
    height: 20px;
    background: #cbd5e1;
    border-radius: 1px;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .seat-map-container {
        max-width: 100%;
    }

    .bus-layout-container {
        padding: 15px;
    }

    .main-seating-area {
        padding: 12px;
    }

    .seat {
        width: 28px;
        height: 28px;
        font-size: 9px;
    }

    .back-seat {
        width: 32px;
        height: 28px;
    }
}
</style>
@endpush
