@extends('layouts.operator')

@section('title', 'Counter Booking - ' . $schedule->route->sourceCity->name . ' to ' . $schedule->route->destinationCity->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Counter Booking</h1>
                    <p class="text-purple-100">{{ $schedule->route->sourceCity->name }} → {{ $schedule->route->destinationCity->name }}</p>
                    <div class="flex items-center text-purple-200 text-sm mt-2">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span>{{ $schedule->travel_date->format('l, F j, Y') }}</span>
                        <span class="mx-3">•</span>
                        <i class="fas fa-clock mr-2"></i>
                        <span>{{ $schedule->departure_time }}</span>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('operator.counter.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Counter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Info Card -->
    <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Schedule Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Bus</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $schedule->bus->bus_number }}</div>
                    <div class="text-sm text-gray-500">{{ $schedule->bus->busType->name ?? 'N/A' }}</div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Fare per Seat</div>
                    <div class="text-lg font-semibold text-gray-900">Rs. {{ number_format($schedule->fare, 2) }}</div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Available Seats</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $schedule->available_seats }}</div>
                </div>
                <div class="text-center">
                    <div class="text-sm font-medium text-gray-500">Total Seats</div>
                    <div class="text-lg font-semibold text-gray-900">{{ $schedule->bus->total_seats }}</div>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('operator.counter.book.store', $schedule) }}" id="bookingForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Seat Selection -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Select Seats</h3>
                    <p class="text-sm text-gray-500">Click on available seats to select them</p>
                </div>
                <div class="p-6">
                    <!-- Seat Legend -->
                    <div class="flex flex-wrap gap-4 mb-6 text-sm">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                            <span>Available</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                            <span>Selected</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                            <span>Booked</span>
                        </div>
                    </div>

                    <!-- Seat Map -->
                    <div class="seat-map bg-gray-50 p-4 rounded-lg">
                        <div class="text-center mb-4">
                            <div class="inline-block bg-gray-300 px-4 py-2 rounded-lg text-sm font-medium">Driver</div>
                        </div>
                        
                        <div class="grid gap-2" style="grid-template-columns: repeat({{ $seatMap['layout']['columns'] ?? 4 }}, 1fr);">
                            @if(isset($seatMap['seats']) && is_array($seatMap['seats']))
                                @foreach($seatMap['seats'] as $seat)
                                    @php
                                        $seatType = $seat['type'] ?? 'seat';
                                        $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? 'N/A';
                                        $isBooked = $seat['is_booked'] ?? false;
                                    @endphp

                                    @if($seatType === 'seat')
                                        <button type="button"
                                                class="seat-btn w-10 h-10 rounded text-xs font-medium transition-colors duration-200
                                                       @if($isBooked) bg-red-500 text-white cursor-not-allowed
                                                       @else bg-green-500 text-white hover:bg-green-600 cursor-pointer
                                                       @endif"
                                                data-seat="{{ $seatNumber }}"
                                                @if($isBooked) disabled @endif>
                                            {{ $seatNumber }}
                                        </button>
                                    @else
                                        <div class="w-10 h-10"></div> <!-- Empty space for aisle -->
                                    @endif
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-8">
                                    <div class="text-red-600">
                                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                                        <p>Unable to load seat map. Please contact support.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Selected Seats Display -->
                    <div class="mt-6">
                        <div class="text-sm font-medium text-gray-700 mb-2">Selected Seats:</div>
                        <div id="selectedSeats" class="text-lg font-semibold text-blue-600">None</div>
                        <div class="text-sm text-gray-500 mt-1">
                            Total Amount: <span id="totalAmount" class="font-medium">Rs. 0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Passenger Details -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Passenger Details</h3>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Passenger Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="passenger_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="passenger_name" id="passenger_name" required
                                   value="{{ old('passenger_name') }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            @error('passenger_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="passenger_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                            <input type="tel" name="passenger_phone" id="passenger_phone" required
                                   value="{{ old('passenger_phone') }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            @error('passenger_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="passenger_email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="passenger_email" id="passenger_email"
                                   value="{{ old('passenger_email') }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            @error('passenger_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="passenger_age" class="block text-sm font-medium text-gray-700 mb-2">Age *</label>
                            <input type="number" name="passenger_age" id="passenger_age" required min="1" max="120"
                                   value="{{ old('passenger_age') }}"
                                   class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            @error('passenger_age')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="passenger_gender" class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                            <select name="passenger_gender" id="passenger_gender" required
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                <option value="">Select Gender</option>
                                <option value="male" {{ old('passenger_gender') === 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('passenger_gender') === 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('passenger_gender') === 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('passenger_gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Contact Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">Contact Phone *</label>
                                <input type="tel" name="contact_phone" id="contact_phone" required
                                       value="{{ old('contact_phone') }}"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                @error('contact_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                                <input type="email" name="contact_email" id="contact_email"
                                       value="{{ old('contact_email') }}"
                                       class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                @error('contact_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Payment Method</h4>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="relative">
                                <input type="radio" name="payment_method" value="cash" class="sr-only" {{ old('payment_method') === 'cash' ? 'checked' : '' }}>
                                <div class="payment-option border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-purple-500 transition-colors">
                                    <i class="fas fa-money-bill-wave text-2xl text-gray-600 mb-2"></i>
                                    <div class="text-sm font-medium">Cash</div>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="payment_method" value="card" class="sr-only" {{ old('payment_method') === 'card' ? 'checked' : '' }}>
                                <div class="payment-option border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-purple-500 transition-colors">
                                    <i class="fas fa-credit-card text-2xl text-gray-600 mb-2"></i>
                                    <div class="text-sm font-medium">Card</div>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="payment_method" value="digital" class="sr-only" {{ old('payment_method') === 'digital' ? 'checked' : '' }}>
                                <div class="payment-option border-2 border-gray-300 rounded-lg p-4 text-center cursor-pointer hover:border-purple-500 transition-colors">
                                    <i class="fas fa-mobile-alt text-2xl text-gray-600 mb-2"></i>
                                    <div class="text-sm font-medium">Digital</div>
                                </div>
                            </label>
                        </div>
                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Special Requests -->
                    <div class="border-t border-gray-200 pt-6">
                        <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-2">Special Requests</label>
                        <textarea name="special_requests" id="special_requests" rows="3"
                                  placeholder="Any special requirements or notes..."
                                  class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500 sm:text-sm">{{ old('special_requests') }}</textarea>
                        @error('special_requests')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t border-gray-200 pt-6">
                        <button type="submit" id="submitBtn" disabled
                                class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors duration-200">
                            <i class="fas fa-ticket-alt mr-2"></i>
                            Create Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden inputs for selected seats -->
        <div id="seatNumbersContainer"></div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const seatButtons = document.querySelectorAll('.seat-btn:not([disabled])');
    const selectedSeatsDisplay = document.getElementById('selectedSeats');
    const totalAmountDisplay = document.getElementById('totalAmount');
    const seatNumbersContainer = document.getElementById('seatNumbersContainer');
    const submitBtn = document.getElementById('submitBtn');
    const farePerSeat = {{ $schedule->fare }};

    let selectedSeats = [];

    // Seat selection functionality
    seatButtons.forEach(button => {
        button.addEventListener('click', function() {
            const seatNumber = parseInt(this.dataset.seat);

            if (this.classList.contains('bg-blue-500')) {
                // Deselect seat
                this.classList.remove('bg-blue-500', 'hover:bg-blue-600');
                this.classList.add('bg-green-500', 'hover:bg-green-600');
                selectedSeats = selectedSeats.filter(seat => seat !== seatNumber);
            } else {
                // Select seat
                this.classList.remove('bg-green-500', 'hover:bg-green-600');
                this.classList.add('bg-blue-500', 'hover:bg-blue-600');
                selectedSeats.push(seatNumber);
            }

            updateSelectedSeatsDisplay();
        });
    });

    function updateSelectedSeatsDisplay() {
        if (selectedSeats.length === 0) {
            selectedSeatsDisplay.textContent = 'None';
            totalAmountDisplay.textContent = 'Rs. 0.00';
            submitBtn.disabled = true;
            seatNumbersContainer.innerHTML = '';
        } else {
            selectedSeats.sort((a, b) => a - b);
            selectedSeatsDisplay.textContent = selectedSeats.join(', ');
            const totalAmount = selectedSeats.length * farePerSeat;
            totalAmountDisplay.textContent = `Rs. ${totalAmount.toLocaleString('en-NP', {minimumFractionDigits: 2})}`;
            submitBtn.disabled = false;

            // Create hidden inputs for each selected seat
            seatNumbersContainer.innerHTML = '';
            selectedSeats.forEach(seatNumber => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'seat_numbers[]';
                input.value = seatNumber;
                seatNumbersContainer.appendChild(input);
            });
        }
    }

    // Payment method selection
    const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
    const paymentOptionDivs = document.querySelectorAll('.payment-option');

    paymentOptions.forEach((radio, index) => {
        radio.addEventListener('change', function() {
            paymentOptionDivs.forEach(div => {
                div.classList.remove('border-purple-500', 'bg-purple-50');
                div.classList.add('border-gray-300');
            });

            if (this.checked) {
                paymentOptionDivs[index].classList.remove('border-gray-300');
                paymentOptionDivs[index].classList.add('border-purple-500', 'bg-purple-50');
            }
        });
    });

    // Initialize payment method selection if there's an old value
    const checkedPayment = document.querySelector('input[name="payment_method"]:checked');
    if (checkedPayment) {
        const index = Array.from(paymentOptions).indexOf(checkedPayment);
        paymentOptionDivs[index].classList.remove('border-gray-300');
        paymentOptionDivs[index].classList.add('border-purple-500', 'bg-purple-50');
    }

    // Auto-fill contact phone with passenger phone
    const passengerPhone = document.getElementById('passenger_phone');
    const contactPhone = document.getElementById('contact_phone');

    passengerPhone.addEventListener('blur', function() {
        if (this.value && !contactPhone.value) {
            contactPhone.value = this.value;
        }
    });

    // Auto-fill contact email with passenger email
    const passengerEmail = document.getElementById('passenger_email');
    const contactEmail = document.getElementById('contact_email');

    passengerEmail.addEventListener('blur', function() {
        if (this.value && !contactEmail.value) {
            contactEmail.value = this.value;
        }
    });

    // Form validation
    const form = document.getElementById('bookingForm');
    form.addEventListener('submit', function(e) {
        if (selectedSeats.length === 0) {
            e.preventDefault();
            alert('Please select at least one seat.');
            return false;
        }

        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            e.preventDefault();
            alert('Please select a payment method.');
            return false;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    });
});
</script>
@endpush
@endsection
