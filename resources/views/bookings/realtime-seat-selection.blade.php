@extends('layouts.app')

@section('title', 'Select Your Seats - Real-time')

@push('meta')
<meta name="user-id" content="{{ auth()->id() }}">
<meta name="schedule-id" content="{{ $schedule->id }}">
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Select Your Seats</h1>
                <p class="text-gray-600">{{ $schedule->route->full_name }} • {{ $schedule->travel_date->format('M d, Y') }}</p>
                <p class="text-sm text-gray-500">{{ $schedule->bus->display_name }} • Departure: {{ $schedule->departure_time->format('h:i A') }}</p>
            </div>
            <div class="text-right">
                <div class="text-lg font-semibold text-gray-900">Rs. {{ number_format($schedule->fare) }}</div>
                <div class="text-sm text-gray-500">per seat</div>
                <div class="text-sm text-green-600 font-medium">
                    <span id="available-seats-counter">{{ $schedule->available_seats }}</span> seats available
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Real-time Seat Map -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-lg rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900">Choose Your Seats</h2>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span>Live Updates</span>
                    </div>
                </div>
                
                <!-- Real-time Seat Map Container -->
                <div id="realtime-seat-map" class="mb-6">
                    <!-- Seat map will be loaded here -->
                </div>

                <!-- Seat Selection Info -->
                <div class="seat-selection-info">
                    <div id="selected-seats-display" class="selected-seats-display">
                        No seats selected
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        Click on available seats to select them. Selected seats are reserved for 10 minutes.
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow-lg rounded-xl p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h3>
                
                <!-- Trip Details -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Route:</span>
                        <span class="font-medium">{{ $schedule->route->source_city }} → {{ $schedule->route->destination_city }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-medium">{{ $schedule->travel_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Time:</span>
                        <span class="font-medium">{{ $schedule->departure_time->format('h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bus:</span>
                        <span class="font-medium">{{ $schedule->bus->display_name }}</span>
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
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Seats (<span id="seat-count">0</span>):</span>
                            <span id="subtotal">Rs. 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Service Fee:</span>
                            <span>Rs. 0</span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg border-t pt-2">
                            <span>Total:</span>
                            <span id="total-amount">Rs. 0</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button id="proceed-button" 
                            class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition duration-200 disabled:bg-gray-300 disabled:cursor-not-allowed"
                            disabled>
                        Proceed to Passenger Details
                    </button>
                    <button id="clear-selection-button" 
                            class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg font-medium hover:bg-gray-200 transition duration-200">
                        Clear Selection
                    </button>
                </div>

                <!-- Reservation Timer -->
                <div id="reservation-timer" class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg hidden">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <div class="text-sm font-medium text-yellow-800">Seats Reserved</div>
                            <div class="text-xs text-yellow-600">
                                Time remaining: <span id="timer-countdown">10:00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scheduleId = document.querySelector('meta[name="schedule-id"]').getAttribute('content');
    const farePerSeat = {{ $schedule->fare }};
    
    // Initialize real-time seat map
    const seatMap = new RealtimeSeatMap(scheduleId, 'realtime-seat-map');
    
    // Handle seat selection changes
    document.addEventListener('seatSelectionChanged', function(event) {
        const selectedSeats = event.detail.selectedSeats;
        const count = event.detail.count;
        
        updateBookingSummary(selectedSeats, count, farePerSeat);
        updateProceedButton(count > 0);
        
        // Reserve seats if any are selected
        if (count > 0) {
            reserveSeats(selectedSeats);
        }
    });
    
    // Clear selection button
    document.getElementById('clear-selection-button').addEventListener('click', function() {
        seatMap.clearSelection();
        releaseSeats();
        hideReservationTimer();
    });
    
    // Proceed button
    document.getElementById('proceed-button').addEventListener('click', function() {
        const selectedSeats = seatMap.getSelectedSeats();
        if (selectedSeats.length > 0) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("booking.store-details") }}';
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Add schedule ID
            const scheduleInput = document.createElement('input');
            scheduleInput.type = 'hidden';
            scheduleInput.name = 'schedule_id';
            scheduleInput.value = scheduleId;
            form.appendChild(scheduleInput);
            
            // Add selected seats
            selectedSeats.forEach(seat => {
                const seatInput = document.createElement('input');
                seatInput.type = 'hidden';
                seatInput.name = 'selected_seats[]';
                seatInput.value = seat;
                form.appendChild(seatInput);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    });
    
    function updateBookingSummary(selectedSeats, count, farePerSeat) {
        // Update selected seats list
        const seatsList = document.getElementById('selected-seats-list');
        if (count > 0) {
            seatsList.innerHTML = selectedSeats.map(seat => 
                `<div class="flex justify-between items-center">
                    <span class="text-sm">Seat ${seat}</span>
                    <span class="text-sm font-medium">Rs. ${farePerSeat.toLocaleString()}</span>
                </div>`
            ).join('');
        } else {
            seatsList.innerHTML = '<div class="text-gray-500 text-sm">No seats selected</div>';
        }
        
        // Update price calculation
        const subtotal = count * farePerSeat;
        document.getElementById('seat-count').textContent = count;
        document.getElementById('subtotal').textContent = `NRs ${subtotal.toLocaleString()}`;
        document.getElementById('total-amount').textContent = `NRs ${subtotal.toLocaleString()}`;
    }
    
    function updateProceedButton(enabled) {
        const button = document.getElementById('proceed-button');
        button.disabled = !enabled;
        button.textContent = enabled ? 'Proceed to Passenger Details' : 'Select seats to continue';
    }
    
    async function reserveSeats(seatNumbers) {
        try {
            const response = await fetch(`/api/schedules/${scheduleId}/reserve-seats`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ seat_numbers: seatNumbers })
            });
            
            const data = await response.json();
            if (data.success) {
                showReservationTimer(data.reservation_expires_at);
            }
        } catch (error) {
            console.error('Failed to reserve seats:', error);
        }
    }
    
    async function releaseSeats() {
        try {
            await fetch(`/api/schedules/${scheduleId}/release-seats`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                }
            });
        } catch (error) {
            console.error('Failed to release seats:', error);
        }
    }
    
    function showReservationTimer(expiresAt) {
        const timer = document.getElementById('reservation-timer');
        const countdown = document.getElementById('timer-countdown');
        timer.classList.remove('hidden');
        
        const updateTimer = () => {
            const now = new Date();
            const expires = new Date(expiresAt);
            const timeLeft = expires - now;
            
            if (timeLeft <= 0) {
                hideReservationTimer();
                seatMap.clearSelection();
                return;
            }
            
            const minutes = Math.floor(timeLeft / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
            countdown.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        };
        
        updateTimer();
        const interval = setInterval(updateTimer, 1000);
        
        // Store interval ID for cleanup
        timer.dataset.intervalId = interval;
    }
    
    function hideReservationTimer() {
        const timer = document.getElementById('reservation-timer');
        timer.classList.add('hidden');
        
        if (timer.dataset.intervalId) {
            clearInterval(timer.dataset.intervalId);
            delete timer.dataset.intervalId;
        }
    }
});
</script>
@endpush

@endsection
