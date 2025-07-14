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
                            <div class="w-6 h-6 bg-red-500 rounded border-2 border-red-600"></div>
                            <span class="text-sm text-gray-700">Booked</span>
                        </div>
                    </div>
                    
                    <!-- Seat Map Container -->
                    <div id="seat-map-container" class="mb-6">
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
                    </div>

                    <!-- Selected Seats Info -->
                    <div class="p-4 bg-blue-50 rounded-xl">
                        <div id="selected-seats-display" class="text-blue-800 font-medium">
                            No seats selected
                        </div>
                        <div class="text-sm text-blue-600 mt-1">
                            Click on available seats to select them. Selected seats are reserved for 15 minutes.
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

                    <!-- Proceed Button -->
                    <button id="proceed-button" 
                            class="w-full bg-gray-300 text-gray-500 px-6 py-4 rounded-xl font-semibold transition-all duration-200 cursor-not-allowed"
                            disabled>
                        Select seats to continue
                    </button>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const scheduleId = {{ $schedule->id }};
    const farePerSeat = {{ $schedule->fare }};
    let selectedSeats = [];

    // Handle seat selection for all available seats
    document.querySelectorAll('.seat').forEach(seat => {
        // Only add click handler to available seats
        if (seat.dataset.isAvailable === 'true' && !seat.classList.contains('seat-booked')) {
            seat.addEventListener('click', function() {
                const seatNumber = this.dataset.seatNumber;

                if (this.classList.contains('seat-selected')) {
                    // Deselect seat
                    this.classList.remove('seat-selected');
                    this.classList.add('seat-available');
                    selectedSeats = selectedSeats.filter(s => s !== seatNumber);
                } else {
                    // Select seat (max 10 seats)
                    if (selectedSeats.length < 10) {
                        this.classList.remove('seat-available');
                        this.classList.add('seat-selected');
                        selectedSeats.push(seatNumber);
                    } else {
                        alert('You can select maximum 10 seats at a time.');
                        return;
                    }
                }

                updateBookingSummary();
            });
        }
    });

    function updateBookingSummary() {
        const count = selectedSeats.length;
        const subtotal = count * farePerSeat;
        
        // Update displays
        document.getElementById('seat-count').textContent = count;
        document.getElementById('subtotal').textContent = 'Rs. ' + subtotal.toLocaleString();
        document.getElementById('total-amount').textContent = 'Rs. ' + subtotal.toLocaleString();
        
        // Update selected seats display
        const selectedSeatsDisplay = document.getElementById('selected-seats-display');
        const selectedSeatsList = document.getElementById('selected-seats-list');
        
        if (count > 0) {
            selectedSeatsDisplay.textContent = `${count} seat(s) selected: ${selectedSeats.join(', ')}`;
            selectedSeatsList.innerHTML = selectedSeats.map(seat => 
                `<div class="flex justify-between items-center p-2 bg-yellow-50 rounded">
                    <span>Seat ${seat}</span>
                    <span class="font-medium">Rs. ${farePerSeat.toLocaleString()}</span>
                </div>`
            ).join('');
        } else {
            selectedSeatsDisplay.textContent = 'No seats selected';
            selectedSeatsList.innerHTML = '<div class="text-gray-500 text-sm">No seats selected</div>';
        }
        
        // Update proceed button
        const proceedButton = document.getElementById('proceed-button');
        if (count > 0) {
            proceedButton.disabled = false;
            proceedButton.className = 'w-full bg-blue-600 text-white px-6 py-4 rounded-xl font-semibold hover:bg-blue-700 transition-all duration-200';
            proceedButton.textContent = `Continue with ${count} seat(s)`;
            proceedButton.onclick = proceedToPassengerDetails;
        } else {
            proceedButton.disabled = true;
            proceedButton.className = 'w-full bg-gray-300 text-gray-500 px-6 py-4 rounded-xl font-semibold transition-all duration-200 cursor-not-allowed';
            proceedButton.textContent = 'Select seats to continue';
            proceedButton.onclick = null;
        }
    }

    function proceedToPassengerDetails() {
        if (selectedSeats.length === 0) return;
        
        // Reserve seats via AJAX
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
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
});
</script>
@endsection
