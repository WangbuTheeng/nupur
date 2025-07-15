@extends('layouts.app')

@section('title', 'Select Your Seats')

@push('meta')
<meta name="user-id" content="{{ auth()->id() }}">
<meta name="schedule-id" content="{{ $schedule->id }}">
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Select Your Seats</h1>
                    <p class="text-xl text-blue-100">
                        {{ $schedule->route->sourceCity->name }} → {{ $schedule->route->destinationCity->name }}
                    </p>
                    <p class="text-blue-200">
                        {{ $schedule->travel_date->format('M d, Y') }} • Departure: {{ \Carbon\Carbon::parse($schedule->departure_time)->format('g:i A') }}
                    </p>
                </div>
                <div class="mt-4 md:mt-0 text-right">
                    <div class="text-2xl font-bold">Rs. {{ number_format($schedule->fare) }}</div>
                    <div class="text-blue-200">per seat</div>
                    <div class="text-green-300 font-medium">
                        <span id="available-seats-counter">{{ $schedule->available_seats }}</span> seats available
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4 relative z-10">
        <!-- Existing Reservation Notice -->
        @php
            $existingReservation = \App\Models\SeatReservation::where('user_id', auth()->id())
                ->where('schedule_id', $schedule->id)
                ->active()
                ->first();
        @endphp

        @if($existingReservation)
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-medium text-blue-900">
                            You have seats reserved for this journey
                        </h3>
                        <div class="mt-2 text-blue-800">
                            <p>Reserved seats: <strong>{{ implode(', ', $existingReservation->seat_numbers) }}</strong></p>
                            <p class="text-sm">Expires: <strong>{{ $existingReservation->expires_at->format('M d, Y g:i A') }}</strong>
                               (<span class="countdown" data-expires="{{ $existingReservation->expires_at->toISOString() }}">{{ $existingReservation->expires_at->diffForHumans() }}</span>)</p>
                        </div>
                        <div class="mt-4 flex space-x-3">
                            <a href="{{ route('booking.passenger-details', $schedule) }}"
                               class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                Complete Booking
                            </a>
                            <button onclick="cancelExistingReservation({{ $existingReservation->id }})"
                                    class="bg-white text-blue-600 border border-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors">
                                Cancel & Select New Seats
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Seat Map -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Choose Your Seats</h2>
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span>Live Updates</span>
                        </div>
                    </div>
                    
                    <!-- Seat Legend -->
                    <div class="flex flex-wrap gap-6 mb-8 p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-green-500 rounded border-2 border-green-600"></div>
                            <span class="text-sm text-gray-700">Available</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-yellow-500 rounded border-2 border-yellow-600"></div>
                            <span class="text-sm text-gray-700">Selected</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-blue-500 rounded border-2 border-blue-600"></div>
                            <span class="text-sm text-gray-700">Reserved</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-red-500 rounded border-2 border-red-600"></div>
                            <span class="text-sm text-gray-700">Booked</span>
                        </div>
                    </div>
                    
                    <!-- Seat Map Container -->
                    <div id="seat-map-container" class="mb-6">
                        <div class="text-center py-8">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                            <p class="text-gray-600">Loading seat map...</p>
                        </div>
                        <!-- Real-time seat map will be loaded here -->
                    </div>

                    <!-- Hidden fallback for when JavaScript is disabled -->
                    <noscript>
                        @if(isset($seatMap['seats']) && is_array($seatMap['seats']))
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

                                                    // Sort seats by column
                                                    usort($rowSeats, function($a, $b) {
                                                        return ($a['column'] ?? 1) - ($b['column'] ?? 1);
                                                    });
                                                @endphp

                                                @if(!empty($rowSeats))
                                                    <div class="seat-row {{ $isBackRow ? 'back-row' : 'regular-row' }}" data-row="{{ $rowNum }}">
                                                        @if($isBackRow)
                                                            <!-- Back row - continuous seats -->
                                                            <div class="back-row-container">
                                                                @foreach($rowSeats as $seat)
                                                                    @php
                                                                        $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? 'N/A';
                                                                        $isBooked = $seat['is_booked'] ?? false;
                                                                        $seatClass = $isBooked ? 'seat-booked' : 'seat-available';
                                                                    @endphp
                                                                    <div class="seat {{ $seatClass }}"
                                                                         data-seat-number="{{ $seatNumber }}"
                                                                         data-is-available="{{ !$isBooked ? 'true' : 'false' }}"
                                                                         title="Seat {{ $seatNumber }}">
                                                                        {{ $seatNumber }}
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <!-- Regular row with aisle -->
                                                            @php
                                                                $maxColumn = !empty($rowSeats) ? max(array_column($rowSeats, 'column')) : 4;
                                                                $seatsByColumn = [];
                                                                foreach($rowSeats as $seat) {
                                                                    $seatsByColumn[$seat['column'] ?? 1] = $seat;
                                                                }
                                                            @endphp

                                                            @for($col = 1; $col <= $maxColumn; $col++)
                                                                @if(isset($seatsByColumn[$col]))
                                                                    @php
                                                                        $seat = $seatsByColumn[$col];
                                                                        $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? 'N/A';
                                                                        $isBooked = $seat['is_booked'] ?? false;
                                                                        $seatClass = $isBooked ? 'seat-booked' : 'seat-available';
                                                                    @endphp
                                                                    <div class="seat {{ $seatClass }}"
                                                                         data-seat-number="{{ $seatNumber }}"
                                                                         data-is-available="{{ !$isBooked ? 'true' : 'false' }}"
                                                                         title="Seat {{ $seatNumber }}">
                                                                        {{ $seatNumber }}
                                                                    </div>
                                                                @else
                                                                    <!-- Aisle space -->
                                                                    <div class="aisle-space">|</div>
                                                                @endif
                                                            @endfor
                                                        @endif
                                                    </div>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8">
                                <p class="text-gray-500">Seat map not available</p>
                            </div>
                        @endif
                    </noscript>

                    <!-- Selected Seats Info -->
                    <div class="p-4 bg-blue-50 rounded-xl">
                        <div id="selected-seats-display" class="text-blue-800 font-medium">
                            No seats selected
                        </div>
                        <div class="text-sm text-blue-600 mt-1">
                            Click on available seats to select them. Selected seats are reserved for 1 hour.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Booking Summary</h3>
                    
                    <!-- Trip Details -->
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Route:</span>
                            <span class="font-medium text-right">{{ $schedule->route->sourceCity->name }} → {{ $schedule->route->destinationCity->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date:</span>
                            <span class="font-medium">{{ $schedule->travel_date->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Time:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('g:i A') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Bus:</span>
                            <span class="font-medium">{{ $schedule->bus->bus_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Operator:</span>
                            <span class="font-medium text-right">{{ $schedule->operator->company_name ?? $schedule->operator->name }}</span>
                        </div>
                    </div>

                    <!-- Selected Seats -->
                    <div class="border-t pt-4 mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Selected Seats</h4>
                        <div id="selected-seats-list" class="space-y-2">
                            <div class="text-gray-500 text-sm">No seats selected</div>
                        </div>
                    </div>

                    <!-- Price Calculation -->
                    <div class="border-t pt-4 mb-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Seats (<span id="seat-count">0</span>):</span>
                                <span id="subtotal" class="font-medium">Rs. 0</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span id="total-amount" class="text-blue-600">Rs. 0</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <!-- Reserve Seats Button -->
                        <button id="reserve-button"
                                class="w-full bg-blue-600 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-200 hover:bg-blue-700 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed"
                                disabled>
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Reserve Seats (1 hour)
                        </button>

                        <!-- Proceed to Book Button -->
                        <button id="proceed-button"
                                class="w-full bg-green-600 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-200 hover:bg-green-700 disabled:bg-gray-300 disabled:text-gray-500 disabled:cursor-not-allowed"
                                disabled>
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Book Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Bus Layout Styles */
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
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.bus-top-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px 15px;
    background: linear-gradient(to right, #64748b, #475569);
    border-radius: 15px;
    color: white;
}

.bus-door {
    font-size: 16px;
    padding: 8px 12px;
    background: rgba(34, 197, 94, 0.2);
    border-radius: 8px;
    border: 2px solid #22c55e;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 80px;
    height: 40px;
    font-weight: bold;
}

.bus-front-space {
    flex: 1;
    text-align: center;
    font-weight: 600;
    font-size: 14px;
    color: #e2e8f0;
}

.bus-front-space::after {
    content: "FRONT";
    opacity: 0.7;
}

.driver-seat {
    font-size: 16px;
    padding: 8px 12px;
    background: rgba(59, 130, 246, 0.2);
    border-radius: 8px;
    border: 2px solid #3b82f6;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 80px;
    height: 40px;
    font-weight: bold;
}

.main-seating-area {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.seat-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    padding: 4px 0;
}

.regular-row {
    border-bottom: 1px solid rgba(203, 213, 225, 0.3);
}

.back-row {
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid #64748b;
    position: relative;
}

.back-row::before {
    content: "BACK ROW";
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: #64748b;
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: bold;
}

.back-row-container {
    display: flex;
    justify-content: center;
    gap: 4px;
    background: rgba(34, 197, 94, 0.1);
    padding: 8px;
    border-radius: 12px;
    border: 2px dashed #22c55e;
}

.seat {
    width: 40px;
    height: 40px;
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

.seat-available {
    background: #22c55e;
    color: white;
    border-color: #16a34a;
}

.seat-available:hover {
    background: #16a34a;
    transform: scale(1.05);
}

.seat-selected {
    background: #f59e0b;
    color: white;
    border-color: #d97706;
    transform: scale(1.05);
}

.seat-booked {
    background: #ef4444;
    color: white;
    border-color: #dc2626;
    cursor: not-allowed;
}

.seat-reserved {
    background: #3b82f6;
    color: white;
    border-color: #1d4ed8;
    cursor: not-allowed;
}

.aisle-space {
    width: 20px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 18px;
    font-weight: bold;
}
</style>

<!-- Include real-time seat map CSS and script -->
<link rel="stylesheet" href="{{ asset('css/seat-map.css') }}">
<script src="{{ asset('js/realtime-seat-map.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scheduleId = {{ $schedule->id }};
    const farePerSeat = {{ $schedule->fare }};
    let selectedSeats = [];
    let realtimeSeatMap;

    console.log('🚌 [SEAT-SELECTION] Page loaded for schedule:', scheduleId);
    console.log('🚌 [SEAT-SELECTION] Fare per seat:', farePerSeat);

    // Initialize real-time seat map
    initializeRealtimeSeatMap();

    // Function to initialize real-time seat map
    function initializeRealtimeSeatMap() {
        // Create a custom real-time seat map for seat selection
        realtimeSeatMap = new RealtimeSeatMap(scheduleId, 'seat-map-container');

        // Override the seat click handler to integrate with our booking flow
        realtimeSeatMap.handleSeatClick = function(seatElement) {
            const seatNumber = seatElement.dataset.seat || seatElement.dataset.seatNumber;

            // Don't allow selection of booked seats
            if (seatElement.classList.contains('booked')) {
                return;
            }

            // Allow selection of seats reserved by current user, but not by others
            if (seatElement.classList.contains('reserved')) {
                // Check if this seat is reserved by current user
                @if($existingReservation)
                    const userReservedSeats = @json($existingReservation->seat_numbers);
                    if (!userReservedSeats.includes(parseInt(seatNumber))) {
                        // This seat is reserved by someone else
                        return;
                    }
                @else
                    // No existing reservation, so this must be reserved by someone else
                    return;
                @endif
            }

            if (selectedSeats.includes(seatNumber)) {
                // Deselect seat
                selectedSeats = selectedSeats.filter(s => s !== seatNumber);
                seatElement.classList.remove('selected');

                // If this seat was originally reserved by user, mark it as reserved again
                @if($existingReservation)
                    const userReservedSeats = @json($existingReservation->seat_numbers);
                    if (userReservedSeats.includes(parseInt(seatNumber))) {
                        seatElement.classList.add('reserved');
                    } else {
                        seatElement.classList.add('available');
                    }
                @else
                    seatElement.classList.add('available');
                @endif

                console.log('🪑 [SEAT-DESELECT] Seat deselected:', seatNumber);
            } else {
                // Select seat (max 10 seats total including existing reservations)
                @if($existingReservation)
                    const userReservedSeats = @json($existingReservation->seat_numbers);
                    const totalSeats = selectedSeats.length + userReservedSeats.length;
                @else
                    const totalSeats = selectedSeats.length;
                @endif

                if (totalSeats < 10) {
                    selectedSeats.push(seatNumber);
                    seatElement.classList.remove('available', 'reserved');
                    seatElement.classList.add('selected');
                    console.log('🪑 [SEAT-SELECT] Seat selected:', seatNumber);
                } else {
                    alert('You can reserve maximum 10 seats total.');
                    return;
                }
            }

            console.log('🪑 [SEAT-SELECTION] Current selection:', selectedSeats);
            updateBookingSummary();
        };

        // Set up seat click handlers after seat map is loaded
        setTimeout(() => {
            setupSeatClickHandlers();
            console.log('🚌 [REALTIME-SEAT-MAP] Seat map loaded and click handlers set up');
        }, 1000);

        // Refresh seat map every 30 seconds to ensure we have latest data
        setInterval(() => {
            if (realtimeSeatMap) {
                realtimeSeatMap.loadSeatMap();
            }
        }, 30000);
    }

    // Initialize booking summary on page load
    updateBookingSummary();

    function setupSeatClickHandlers() {
        document.querySelectorAll('.seat').forEach(seat => {
            // Only add click handler to available seats
            if (!seat.classList.contains('booked') && !seat.classList.contains('reserved')) {
                seat.addEventListener('click', function() {
                    if (realtimeSeatMap && realtimeSeatMap.handleSeatClick) {
                        realtimeSeatMap.handleSeatClick(this);
                    } else {
                        // Fallback to original seat click handling
                        handleSeatClickFallback(this);
                    }
                });
            }
        });
    }

    function handleSeatClickFallback(seatElement) {
        const seatNumber = seatElement.dataset.seat || seatElement.dataset.seatNumber;

        if (!seatNumber) return;

        if (selectedSeats.includes(seatNumber)) {
            // Deselect seat
            selectedSeats = selectedSeats.filter(s => s !== seatNumber);
            seatElement.classList.remove('seat-selected', 'selected');
            seatElement.classList.add('seat-available', 'available');
        } else {
            // Select seat (max 10 seats)
            if (selectedSeats.length < 10) {
                selectedSeats.push(seatNumber);
                seatElement.classList.remove('seat-available', 'available');
                seatElement.classList.add('seat-selected', 'selected');
            } else {
                alert('You can select maximum 10 seats at a time.');
                return;
            }
        }

        updateBookingSummary();
    }

    // Handle seat selection for all available seats (fallback for static seats)
    document.querySelectorAll('.seat').forEach(seat => {
        // Only add click handler to available seats
        if (seat.dataset.isAvailable === 'true' && !seat.classList.contains('seat-booked')) {
            seat.addEventListener('click', function() {
                const seatNumber = this.dataset.seatNumber;
                console.log('🪑 [SEAT-CLICK] Seat clicked:', seatNumber);

                if (this.classList.contains('seat-selected')) {
                    // Deselect seat
                    this.classList.remove('seat-selected');
                    this.classList.add('seat-available');
                    selectedSeats = selectedSeats.filter(s => s !== seatNumber);
                    console.log('🪑 [SEAT-DESELECT] Seat deselected:', seatNumber);
                } else {
                    // Select seat (max 10 seats)
                    if (selectedSeats.length < 10) {
                        this.classList.remove('seat-available');
                        this.classList.add('seat-selected');
                        selectedSeats.push(seatNumber);
                        console.log('🪑 [SEAT-SELECT] Seat selected:', seatNumber);
                    } else {
                        alert('You can select maximum 10 seats at a time.');
                        return;
                    }
                }

                console.log('🪑 [SEAT-SELECTION] Current selection:', selectedSeats);
                updateBookingSummary();
            });
        }
    });

    function updateBookingSummary() {
        @if($existingReservation)
            const existingSeats = @json($existingReservation->seat_numbers);
            const existingCount = existingSeats.length;
        @else
            const existingSeats = [];
            const existingCount = 0;
        @endif

        const newCount = selectedSeats.length;
        const totalCount = existingCount + newCount;
        const newSubtotal = newCount * farePerSeat;
        const existingSubtotal = existingCount * farePerSeat;
        const totalSubtotal = totalCount * farePerSeat;

        // Update displays
        document.getElementById('seat-count').textContent = totalCount;
        document.getElementById('subtotal').textContent = 'Rs. ' + totalSubtotal.toLocaleString();
        document.getElementById('total-amount').textContent = 'Rs. ' + totalSubtotal.toLocaleString();

        // Update selected seats display
        const selectedSeatsDisplay = document.getElementById('selected-seats-display');
        const selectedSeatsList = document.getElementById('selected-seats-list');

        let displayText = '';
        let listHTML = '';

        if (existingCount > 0 && newCount > 0) {
            displayText = `${totalCount} seat(s) total: ${existingCount} reserved + ${newCount} selected`;

            // Show existing reservations
            listHTML += existingSeats.map(seat =>
                `<div class="flex justify-between items-center p-2 bg-blue-50 rounded">
                    <span>Seat ${seat} <span class="text-xs text-blue-600">(Reserved)</span></span>
                    <span class="font-medium">Rs. ${farePerSeat.toLocaleString()}</span>
                </div>`
            ).join('');

            // Show new selections
            listHTML += selectedSeats.map(seat =>
                `<div class="flex justify-between items-center p-2 bg-yellow-50 rounded">
                    <span>Seat ${seat} <span class="text-xs text-yellow-600">(Selected)</span></span>
                    <span class="font-medium">Rs. ${farePerSeat.toLocaleString()}</span>
                </div>`
            ).join('');
        } else if (existingCount > 0) {
            displayText = `${existingCount} seat(s) reserved: ${existingSeats.join(', ')}`;
            listHTML = existingSeats.map(seat =>
                `<div class="flex justify-between items-center p-2 bg-blue-50 rounded">
                    <span>Seat ${seat} <span class="text-xs text-blue-600">(Reserved)</span></span>
                    <span class="font-medium">Rs. ${farePerSeat.toLocaleString()}</span>
                </div>`
            ).join('');
        } else if (newCount > 0) {
            displayText = `${newCount} seat(s) selected: ${selectedSeats.join(', ')}`;
            listHTML = selectedSeats.map(seat =>
                `<div class="flex justify-between items-center p-2 bg-yellow-50 rounded">
                    <span>Seat ${seat}</span>
                    <span class="font-medium">Rs. ${farePerSeat.toLocaleString()}</span>
                </div>`
            ).join('');
        } else {
            displayText = 'No seats selected';
            listHTML = '<div class="text-gray-500 text-sm">No seats selected</div>';
        }

        selectedSeatsDisplay.textContent = displayText;
        selectedSeatsList.innerHTML = listHTML;
        
        // Update buttons
        const reserveButton = document.getElementById('reserve-button');
        const proceedButton = document.getElementById('proceed-button');

        // Reserve button: Enable if there are new seats to reserve
        if (newCount > 0) {
            reserveButton.disabled = false;
            reserveButton.className = 'w-full bg-blue-600 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-200 hover:bg-blue-700';
            reserveButton.onclick = reserveSeats;
            reserveButton.innerHTML = `<svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Reserve ${newCount} More Seat${newCount > 1 ? 's' : ''} (1 hour)`;
        } else {
            reserveButton.disabled = true;
            reserveButton.className = 'w-full bg-gray-300 text-gray-500 px-6 py-4 rounded-xl font-semibold transition-all duration-200 cursor-not-allowed';
            reserveButton.onclick = null;
            reserveButton.innerHTML = `<svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Reserve Seats (1 hour)`;
        }

        // Proceed button: Enable if there are any seats (existing or new)
        if (totalCount > 0) {
            proceedButton.disabled = false;
            proceedButton.className = 'w-full bg-green-600 text-white px-6 py-4 rounded-xl font-semibold transition-all duration-200 hover:bg-green-700';
            proceedButton.onclick = proceedToPassengerDetails;
        } else {
            proceedButton.disabled = true;
            proceedButton.className = 'w-full bg-gray-300 text-gray-500 px-6 py-4 rounded-xl font-semibold transition-all duration-200 cursor-not-allowed';
            proceedButton.onclick = null;
        }
    }

    function reserveSeats() {
        if (selectedSeats.length === 0) return;

        // Show loading state
        const reserveButton = document.getElementById('reserve-button');
        const originalText = reserveButton.innerHTML;
        reserveButton.disabled = true;
        reserveButton.innerHTML = '<svg class="w-5 h-5 inline-block mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Reserving...';

        // Reserve seats via AJAX
        console.log('🔄 [RESERVE-ONLY] Starting seat reservation for:', selectedSeats);
        fetch('{{ route("booking.reserve-seats-only") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                schedule_id: scheduleId,
                seat_numbers: selectedSeats
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`Seats ${selectedSeats.join(', ')} have been reserved for 1 hour. You can proceed to book them anytime within this period.`);

                // Refresh the real-time seat map to show updated reservations
                if (realtimeSeatMap) {
                    realtimeSeatMap.loadSeatMap();
                } else {
                    // Fallback: Update seat colors manually for static seat map
                    selectedSeats.forEach(seatNumber => {
                        const seatElement = document.querySelector(`[data-seat-number="${seatNumber}"], [data-seat="${seatNumber}"]`);
                        if (seatElement) {
                            seatElement.classList.remove('seat-selected', 'selected');
                            seatElement.classList.add('seat-reserved', 'reserved');
                            seatElement.style.background = '#3b82f6';
                            seatElement.style.borderColor = '#1d4ed8';
                            seatElement.style.cursor = 'not-allowed';
                            seatElement.onclick = null;
                        }
                    });
                }

                // Clear selection
                selectedSeats = [];
                updateBookingSummary();
            } else {
                alert(data.message || 'Failed to reserve seats. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        })
        .finally(() => {
            // Restore button state
            reserveButton.disabled = false;
            reserveButton.innerHTML = originalText;
        });
    }

    function proceedToPassengerDetails() {
        @if($existingReservation)
            const existingSeats = @json($existingReservation->seat_numbers);
            const hasExistingReservation = true;
        @else
            const existingSeats = [];
            const hasExistingReservation = false;
        @endif

        // If user has existing reservation and no new seats selected, proceed directly
        if (hasExistingReservation && selectedSeats.length === 0) {
            console.log('🚀 [PROCEED] Proceeding with existing reservation:', existingSeats);
            window.location.href = '{{ route("booking.passenger-details", $schedule) }}';
            return;
        }

        // If user has new seats selected, reserve them first
        if (selectedSeats.length === 0) {
            console.log('❌ [PROCEED] No seats selected');
            return;
        }

        console.log('🚀 [PROCEED] Proceeding to passenger details with seats:', selectedSeats);

        // Reserve new seats via AJAX
        fetch('{{ route("booking.reserve-seats") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                schedule_id: scheduleId,
                seat_numbers: selectedSeats
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Failed to reserve seats. Please try again.');
            }
        })
        .catch(error => {
            console.error('Seat reservation error:', error);
            alert('An error occurred while reserving seats. Please try again.');
        });
    }

    // Update countdown timer for existing reservation
    function updateCountdown() {
        const countdownElement = document.querySelector('.countdown');
        if (countdownElement) {
            const expiresAt = new Date(countdownElement.dataset.expires);
            const now = new Date();
            const diff = expiresAt - now;

            if (diff <= 0) {
                countdownElement.textContent = 'Expired';
                // Reload page to refresh expired reservation
                setTimeout(() => location.reload(), 2000);
            } else {
                const minutes = Math.floor(diff / 60000);
                const seconds = Math.floor((diff % 60000) / 1000);
                countdownElement.textContent = `${minutes}m ${seconds}s remaining`;
            }
        }
    }

    // Update countdown every second if there's an existing reservation
    if (document.querySelector('.countdown')) {
        setInterval(updateCountdown, 1000);
        updateCountdown(); // Initial call
    }
});

// Cancel existing reservation function (outside DOMContentLoaded)
function cancelExistingReservation(reservationId) {
    if (!confirm('Are you sure you want to cancel your current reservation? Your seats will be released and you can select new ones.')) {
        return;
    }

    fetch('{{ route("customer.bookings.cancel-reservation") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reservation_id: reservationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload to show updated seat map
        } else {
            alert(data.message || 'Failed to cancel reservation. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to cancel reservation. Please try again.');
    });
}
</script>
@endsection
