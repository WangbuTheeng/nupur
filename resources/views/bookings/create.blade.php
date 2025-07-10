@extends('layouts.app')

@section('title', 'Book Your Seats')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Book Your Seats</h1>
        <p class="text-gray-600">{{ $schedule->route->full_name }} • {{ $schedule->travel_date->format('M d, Y') }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Seat Selection -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Seats</h2>
                
                <!-- Bus Layout -->
                <div class="mb-6">
                    <div class="bg-gray-100 rounded-lg p-4">
                        <div class="text-center mb-4">
                            <div class="inline-block bg-gray-300 rounded px-3 py-1 text-sm font-medium">Driver</div>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-2 max-w-md mx-auto">
                            @foreach($availableSeats->groupBy('row_number') as $rowNumber => $rowSeats)
                                @foreach($rowSeats->sortBy('column_number') as $seat)
                                    <div class="text-center">
                                        <input type="checkbox" 
                                               id="seat_{{ $seat->seat_number }}" 
                                               name="selected_seats[]" 
                                               value="{{ $seat->seat_number }}"
                                               class="hidden seat-checkbox"
                                               onchange="updateSeatSelection()">
                                        <label for="seat_{{ $seat->seat_number }}" 
                                               class="seat-label block w-10 h-10 border-2 border-gray-300 rounded cursor-pointer hover:border-blue-400 transition duration-200 flex items-center justify-center text-xs font-medium
                                                      {{ $seat->is_window ? 'bg-blue-50' : 'bg-gray-50' }}">
                                            {{ $seat->seat_number }}
                                        </label>
                                    </div>
                                    @if($seat->column_number == 2)
                                        <div class="w-4"></div> <!-- Aisle space -->
                                    @endif
                                @endforeach
                            @endforeach
                        </div>
                        
                        <!-- Legend -->
                        <div class="flex justify-center space-x-4 mt-4 text-xs">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-gray-50 border-2 border-gray-300 rounded mr-1"></div>
                                <span>Available</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-50 border-2 border-gray-300 rounded mr-1"></div>
                                <span>Window</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-500 border-2 border-blue-500 rounded mr-1"></div>
                                <span>Selected</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Seats Display -->
                <div id="selected-seats-display" class="hidden">
                    <h3 class="font-medium text-gray-900 mb-2">Selected Seats:</h3>
                    <div id="selected-seats-list" class="flex flex-wrap gap-2 mb-4"></div>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <div>
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Trip Details</h2>
                
                <!-- Trip Info -->
                <div class="space-y-3 mb-6 pb-6 border-b border-gray-200">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Bus:</span>
                        <span class="font-medium">{{ $schedule->bus->display_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Type:</span>
                        <span class="font-medium">{{ $schedule->bus->busType->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-medium">{{ $schedule->travel_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Departure:</span>
                        <span class="font-medium">{{ $schedule->departure_time->format('h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fare per seat:</span>
                        <span class="font-medium">NPR {{ number_format($schedule->fare) }}</span>
                    </div>
                </div>

                <!-- Booking Form -->
                <form action="{{ route('bookings.store', $schedule) }}" method="POST" id="booking-form">
                    @csrf
                    <input type="hidden" name="seat_numbers" id="seat_numbers_input">
                    
                    <!-- Passenger Details -->
                    <div id="passenger-details" class="space-y-4 mb-6">
                        <!-- Passenger details will be dynamically added here -->
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-4 mb-6">
                        <h3 class="font-medium text-gray-900">Contact Information</h3>
                        
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="contact_phone" id="contact_phone" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ Auth::user()->phone }}" required>
                        </div>
                        
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="contact_email" id="contact_email" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ Auth::user()->email }}" required>
                        </div>
                        
                        <div>
                            <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-1">Special Requests (Optional)</label>
                            <textarea name="special_requests" id="special_requests" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Any special requirements..."></textarea>
                        </div>
                    </div>

                    <!-- Total Amount -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-medium text-gray-900">Total Amount:</span>
                            <span id="total-amount" class="text-2xl font-bold text-blue-600">NPR 0</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">
                            <span id="seat-count">0</span> seat(s) × NPR {{ number_format($schedule->fare) }}
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="book-button" 
                            class="w-full bg-blue-600 text-white hover:bg-blue-700 px-4 py-3 rounded-md font-semibold transition duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed"
                            disabled>
                        Proceed to Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let selectedSeats = [];
const farePerSeat = {{ $schedule->fare }};

function updateSeatSelection() {
    selectedSeats = [];
    const checkboxes = document.querySelectorAll('.seat-checkbox:checked');
    
    checkboxes.forEach(checkbox => {
        selectedSeats.push(checkbox.value);
    });
    
    updateUI();
    updatePassengerForms();
}

function updateUI() {
    // Update seat visual state
    document.querySelectorAll('.seat-label').forEach(label => {
        const checkbox = document.getElementById(label.getAttribute('for'));
        if (checkbox.checked) {
            label.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
            label.classList.remove('bg-gray-50', 'bg-blue-50', 'border-gray-300');
        } else {
            label.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
            const isWindow = label.classList.contains('bg-blue-50');
            if (isWindow) {
                label.classList.add('bg-blue-50', 'border-gray-300');
            } else {
                label.classList.add('bg-gray-50', 'border-gray-300');
            }
        }
    });
    
    // Update selected seats display
    const display = document.getElementById('selected-seats-display');
    const list = document.getElementById('selected-seats-list');
    
    if (selectedSeats.length > 0) {
        display.classList.remove('hidden');
        list.innerHTML = selectedSeats.map(seat => 
            `<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">${seat}</span>`
        ).join('');
    } else {
        display.classList.add('hidden');
    }
    
    // Update total amount
    const totalAmount = selectedSeats.length * farePerSeat;
    document.getElementById('total-amount').textContent = `NPR ${totalAmount.toLocaleString()}`;
    document.getElementById('seat-count').textContent = selectedSeats.length;
    
    // Update hidden input
    document.getElementById('seat_numbers_input').value = JSON.stringify(selectedSeats);
    
    // Enable/disable book button
    const bookButton = document.getElementById('book-button');
    bookButton.disabled = selectedSeats.length === 0;
}

function updatePassengerForms() {
    const container = document.getElementById('passenger-details');
    container.innerHTML = '';
    
    if (selectedSeats.length > 0) {
        const title = document.createElement('h3');
        title.className = 'font-medium text-gray-900 mb-2';
        title.textContent = 'Passenger Details';
        container.appendChild(title);
        
        selectedSeats.forEach((seat, index) => {
            const passengerDiv = document.createElement('div');
            passengerDiv.className = 'border border-gray-200 rounded-lg p-4 space-y-3';
            passengerDiv.innerHTML = `
                <h4 class="font-medium text-gray-800">Passenger ${index + 1} (Seat ${seat})</h4>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="passenger_details[${index}][name]" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                        <input type="number" name="passenger_details[${index}][age]" min="1" max="120"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select name="passenger_details[${index}][gender]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
            `;
            container.appendChild(passengerDiv);
        });
    }
}

// Initialize
updateUI();
</script>
@endsection
