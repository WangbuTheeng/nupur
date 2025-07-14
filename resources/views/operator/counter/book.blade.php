@extends('layouts.operator')

@section('title', 'Counter Booking - ' . $schedule->route->sourceCity->name . ' to ' . $schedule->route->destinationCity->name)

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Error Messages -->
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <strong>Validation Errors:</strong>
            <ul class="mt-2">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

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

        <!-- TWO COLUMN LAYOUT: Left = Seats, Right = Passenger Details -->
        <div class="booking-container" style="display: flex; flex-direction: row; gap: 2rem; width: 100%; align-items: flex-start;">
            <!-- LEFT COLUMN: Seat Selection -->
            <div class="seat-selection-column" style="flex: 1; width: 50%; background: white; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); overflow: hidden; min-height: 600px;">
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
                            <span>Reserved</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                            <span>Booked/Paid</span>
                        </div>
                    </div>

                    <!-- Seat Map -->
                    <div class="seat-map bg-gray-50 p-4 rounded-lg">
                        <!-- Debug Info -->
                        @if(config('app.debug'))
                            <div class="mb-4 p-2 bg-yellow-100 rounded text-xs">
                                <strong>Debug:</strong> Layout Type: {{ $seatMap['layout_type'] ?? 'N/A' }},
                                Rows: {{ $seatMap['rows'] ?? 'N/A' }},
                                Seats: {{ count($seatMap['seats'] ?? []) }}
                                <br><strong>Seat Map Keys:</strong> {{ implode(', ', array_keys($seatMap ?? [])) }}
                                <br><strong>First Seat:</strong> {{ json_encode($seatMap['seats'][0] ?? 'None') }}
                                <button onclick="console.log('Seat Data:', @json($seatMap))" class="ml-2 px-2 py-1 bg-blue-500 text-white rounded text-xs">Log Data</button>
                                <button onclick="window.handleSeatClickDirect('1', document.querySelector('[data-seat=\'1\']'))" class="ml-2 px-2 py-1 bg-green-500 text-white rounded text-xs">Test Seat 1</button>
                                <button onclick="location.reload(true)" class="ml-2 px-2 py-1 bg-purple-500 text-white rounded text-xs">Hard Refresh</button>
                            </div>
                        @endif

                        <div id="counterSeatLayoutDisplay" class="min-h-[400px]">
                            <!-- IMMEDIATE SEAT RENDERING -->
                            <div class="seat-map-container bg-gray-100 p-6 rounded-lg">
                                <!-- Bus Header -->
                                <div class="flex justify-between items-center mb-4 p-3 bg-gray-600 text-white rounded">
                                    <div class="text-sm">🚪 Door</div>
                                    <div class="text-sm">👨‍✈️ Driver</div>
                                </div>

                                <!-- Seat Grid -->
                                <div class="bus-seats-grid">
                                    @php
                                        $seats = $seatMap['seats'] ?? [];
                                        $seatsByRow = [];

                                        // Group seats by row
                                        foreach ($seats as $seat) {
                                            $row = $seat['row'] ?? ceil(intval($seat['seat_number']) / 4);
                                            if (!isset($seatsByRow[$row])) {
                                                $seatsByRow[$row] = [];
                                            }
                                            $seatsByRow[$row][] = $seat;
                                        }

                                        // Sort rows
                                        ksort($seatsByRow);
                                    @endphp

                                    @foreach($seatsByRow as $rowNum => $rowSeats)
                                        <div class="seat-row flex justify-center gap-2 mb-3" data-row="{{ $rowNum }}">
                                            @php
                                                // Sort seats in row by seat number
                                                usort($rowSeats, function($a, $b) {
                                                    return intval($a['seat_number']) - intval($b['seat_number']);
                                                });
                                            @endphp

                                            @foreach($rowSeats as $index => $seat)
                                                @php
                                                    $seatNumber = $seat['seat_number'];
                                                    $status = $seat['status'] ?? 'available';

                                                    switch ($status) {
                                                        case 'booked':
                                                            $bgColor = 'bg-red-500';
                                                            $textColor = 'text-white';
                                                            $cursor = 'cursor-not-allowed';
                                                            $disabled = 'disabled';
                                                            break;
                                                        case 'reserved':
                                                            $bgColor = 'bg-blue-500';
                                                            $textColor = 'text-white';
                                                            $cursor = 'cursor-not-allowed';
                                                            $disabled = 'disabled';
                                                            break;
                                                        default:
                                                            $bgColor = 'bg-green-500 hover:bg-green-600';
                                                            $textColor = 'text-white';
                                                            $cursor = 'cursor-pointer';
                                                            $disabled = '';
                                                    }
                                                @endphp

                                                <button type="button"
                                                        class="seat-button {{ $bgColor }} {{ $textColor }} {{ $cursor }} w-12 h-12 rounded-lg font-medium text-sm transition-all duration-200 border-2 border-transparent hover:border-gray-300"
                                                        data-seat="{{ $seatNumber }}"
                                                        data-status="{{ $status }}"
                                                        {{ $disabled }}
                                                        title="Seat {{ $seatNumber }} - {{ $status }}">
                                                    {{ $seatNumber }}
                                                </button>

                                                @if($index === 1 && count($rowSeats) > 2)
                                                    <div class="w-6"></div> <!-- Aisle space -->
                                                @endif
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Legend -->
                                <div class="flex justify-center gap-4 mt-6 text-sm">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                                        <span>Available</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                                        <span>Selected</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                                        <span>Booked</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-blue-500 rounded mr-2"></div>
                                        <span>Reserved</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Selected Seats Display -->
                    <div class="mt-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Selected Seats:</span>
                            <span id="selectedSeats" class="text-lg font-semibold text-blue-600">None</span>
                        </div>
                        <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                            <span class="text-sm font-medium text-gray-700">Total Amount:</span>
                            <span id="totalAmount" class="text-lg font-bold text-purple-600">Rs. 0.00</span>
                        </div>
                    </div>
                </div>
            

            <!-- RIGHT COLUMN: Passenger Details -->
            <div class="passenger-details-column" style="flex: 1; width: 50%; background: white; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); overflow: hidden; min-height: 600px;">
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
                        <button type="submit" id="submitBtn"
                                class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors duration-200">
                            <i class="fas fa-ticket-alt mr-2"></i>
                            Create Booking
                        </button>

                        <!-- Debug: Manual seat input fallback -->
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg" id="debugSection" style="display: block;">
                            <p class="text-sm text-yellow-800 mb-2">Debug: Test seat selection or manually enter seat numbers:</p>
                            <div class="flex gap-2 mb-2">
                                <button type="button" onclick="testSeatSelection()" class="px-3 py-1 bg-blue-500 text-white text-sm rounded">Test Seat Click</button>
                                <button type="button" onclick="showSelectedSeats()" class="px-3 py-1 bg-green-500 text-white text-sm rounded">Show Selected</button>
                                <button type="button" onclick="clearSelectedSeats()" class="px-3 py-1 bg-red-500 text-white text-sm rounded">Clear All</button>
                                <button type="button" onclick="testDirectSeatClick()" class="px-3 py-1 bg-orange-500 text-white text-sm rounded">Direct Test</button>
                                <button type="button" onclick="testFormSubmission()" class="px-3 py-1 bg-purple-500 text-white text-sm rounded">Test Submit</button>
                            </div>
                            <input type="text" id="manualSeats" placeholder="e.g., 1,2,3" class="w-full px-3 py-2 border border-yellow-300 rounded-md text-sm">
                            <button type="button" onclick="applyManualSeats()" class="mt-2 px-3 py-1 bg-yellow-500 text-white text-sm rounded">Apply Seats</button>
                        </div>
                    </div>
             
                </div>
               </div>

            </div>
        </div>

        <!-- Hidden inputs for selected seats -->
        <div id="seatNumbersContainer"></div>
    </form>
</div>

@push('styles')
<style>
/* OPERATOR BUS DESIGN: Exact same styling as operator bus pages */
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
    gap: 8px;
    align-items: center;
}

.seat-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
}

.aisle-space {
    width: 20px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 18px;
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

.main-seating-area {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.seat-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0px;
    margin-bottom: 10px;
    padding: 2px 0;
}

.aisle-space {
    width: 32px;
    min-height: 45px;
    margin: 4px;
    padding: 8px 0;
    box-sizing: border-box;
}

/* OPERATOR BUS DESIGN: Exact seat styling */
.seat, .seat-button, button.seat-button {
    width: 36px !important;
    height: 36px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    border: 2px solid transparent !important;
    color: white !important;
    text-decoration: none !important;
    outline: none !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    position: relative !important;
    user-select: none !important;
}

/* Seat state styling - matching operator bus design */
.seat.available {
    background-color: #68d391;
}

.seat.window-seat {
    background-color: #63b3ed;
}

.seat.back-row-seat {
    background-color: #a78bfa;
}

/* OPERATOR BUS DESIGN: Available seats */
.seat.available, .seat-button.available {
    background: #22c55e !important;
    color: white !important;
}

.seat.available:hover, .seat-button.available:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
}

/* Window seat styling */
.seat-button.window-seat:not(.selected):not(.booked):not(.reserved) {
    background: #63b3ed !important;
}

/* Back row seat styling */
.seat-button.back-row-seat:not(.selected):not(.booked):not(.reserved) {
    background: #a78bfa !important;
}

/* OPERATOR BUS DESIGN: Selected seats */
.seat.selected, .seat-button.selected {
    background: #eab308 !important;
    color: white !important;
    border-color: #ca8a04 !important;
    animation: pulse 1.5s infinite !important;
}

.seat.selected:hover, .seat-button.selected:hover {
    background: #ca8a04 !important;
    border-color: #a16207 !important;
}

/* OPERATOR BUS DESIGN: Reserved seats (for counter booking) */
.seat.reserved, .seat-button.reserved {
    background: #3b82f6 !important; /* Blue for reserved/selected */
    color: white !important;
    border-color: #1d4ed8 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3) !important;
    z-index: 2 !important;
}

/* Force blue color for selected seats - override any other styles */
button.seat-button.selected {
    background-color: #3b82f6 !important;
    background: #3b82f6 !important;
    animation: seatSelected 0.3s ease-in-out;
}

/* Animation for seat selection */
@keyframes seatSelected {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1) translateY(-1px); }
}

/* OPERATOR BUS DESIGN: Window seats */
.seat.window-seat, .seat-button.window-seat {
    background: #3b82f6 !important;
    color: white !important;
}

/* OPERATOR BUS DESIGN: Back row seats */
.seat.back-row-seat, .seat-button.back-row-seat {
    background: #8b5cf6 !important;
    color: white !important;
    width: 35px !important;
    height: 35px !important;
    font-size: 10px !important;
    margin: 0 1px !important;
}

/* OPERATOR BUS DESIGN: Booked seats */
.seat.booked, .seat-button.booked,
.seat[disabled], .seat-button[disabled] {
    background: #ef4444 !important;
    color: white !important;
    cursor: not-allowed !important;
    opacity: 0.9 !important;
    border-color: #dc2626 !important;
}

.seat.booked:hover, .seat-button.booked:hover,
.seat[disabled]:hover, .seat-button[disabled]:hover {
    background: #ef4444 !important;
    transform: none !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
}

/* OPERATOR BUS DESIGN: Pulse animation for selected seats */
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* FIXED: SIDE-BY-SIDE LAYOUT */
.booking-container {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    width: 100%;
}

/* Desktop: Force side-by-side layout */
@media (min-width: 1200px) {
    .booking-container {
        display: flex !important;
        flex-direction: row !important;
        gap: 2rem !important;
        align-items: flex-start !important;
        width: 100% !important;
    }

    .seat-selection-column {
        flex: 1 !important;
        width: 50% !important;
        max-width: 50% !important;
    }

    .passenger-details-column {
        flex: 1 !important;
        width: 50% !important;
        max-width: 50% !important;
    }
}

/* Column styling */
.seat-selection-column,
.passenger-details-column {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    width: 100%;
    min-height: 600px;
}

/* Ensure seat map fits properly */
.seat-selection-column .seat-map-container {
    max-width: 100% !important;
    margin: 0 !important;
    padding: 15px !important;
}

/* Force visibility and proper sizing */
.passenger-details-column {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Mobile responsive */
@media (max-width: 1199px) {
    .booking-container {
        flex-direction: column !important;
    }

    .seat-selection-column,
    .passenger-details-column {
        width: 100% !important;
        max-width: 100% !important;
        flex: none !important;
    }
}

/* Force inline styles override */
.booking-container[style] {
    display: flex !important;
}

@media (min-width: 1200px) {
    .booking-container[style] {
        flex-direction: row !important;
    }

    .seat-selection-column[style],
    .passenger-details-column[style] {
        width: 50% !important;
        flex: 1 !important;
    }
}

/* Additional styling for headers and content */
.seat-selection-column .px-6,
.passenger-details-column .px-6 {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.seat-selection-column .py-4,
.passenger-details-column .py-4 {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

.seat-selection-column .p-6,
.passenger-details-column .p-6 {
    padding: 1.5rem;
}

/* Ensure proper border styling */
.seat-selection-column .border-b,
.passenger-details-column .border-b {
    border-bottom-width: 1px;
    border-color: #e5e7eb;
}

/* Force override any conflicting styles for booked seats */
.seat-button.booked.selected,
.seat-button[disabled].selected {
    background: #ef4444 !important;
    color: white !important;
    border-color: #dc2626 !important;
    cursor: not-allowed !important;
}
</style>
@endpush

@push('scripts')
<script>
console.log('🚀 JavaScript loading...');

// SIMPLIFIED SEAT SELECTION SYSTEM - GLOBAL SCOPE
window.selectedSeats = [];
window.farePerSeat = {{ $schedule->fare }};
window.isBookingAllowed = {{ $schedule->isBookableViaCounter() ? 'true' : 'false' }};

console.log('📊 Initial setup:', {
    farePerSeat: window.farePerSeat,
    isBookingAllowed: window.isBookingAllowed,
    selectedSeats: window.selectedSeats
});


// GLOBAL UPDATE DISPLAY FUNCTION - Available immediately
window.updateSelectedSeatsDisplay = function() {
    const selectedSeatsDisplay = document.getElementById('selectedSeats');
    const totalAmountDisplay = document.getElementById('totalAmount');
    const seatNumbersContainer = document.getElementById('seatNumbersContainer');
    const submitBtn = document.getElementById('submitBtn');

    console.log('🔄 Updating display, selected seats:', window.selectedSeats);

    if (window.selectedSeats.length === 0) {
        if (selectedSeatsDisplay) selectedSeatsDisplay.textContent = 'None';
        if (totalAmountDisplay) totalAmountDisplay.textContent = 'Rs. 0.00';
        if (submitBtn) submitBtn.disabled = !window.isBookingAllowed;
        if (seatNumbersContainer) seatNumbersContainer.innerHTML = '';
    } else {
        // Sort seats numerically
        window.selectedSeats.sort((a, b) => {
            return a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' });
        });

        if (selectedSeatsDisplay) selectedSeatsDisplay.textContent = window.selectedSeats.join(', ');

        const totalAmount = window.selectedSeats.length * window.farePerSeat;
        if (totalAmountDisplay) totalAmountDisplay.textContent = `Rs. ${totalAmount.toFixed(2)}`;

        if (submitBtn) submitBtn.disabled = !window.isBookingAllowed;

        // Update seat numbers in the form
        if (seatNumbersContainer) {
            seatNumbersContainer.innerHTML = '';
            window.selectedSeats.forEach(seatNumber => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'seat_numbers[]';
                input.value = seatNumber;
                seatNumbersContainer.appendChild(input);
            });
        }
    }

    console.log('✅ Display updated:', {
        selectedCount: window.selectedSeats.length,
        totalAmount: window.selectedSeats.length * window.farePerSeat,
        seats: window.selectedSeats,
        bookingAllowed: window.isBookingAllowed
    });
};

const farePerSeat = window.farePerSeat;
const isBookingAllowed = window.isBookingAllowed;

// SIMPLE SEAT CLICK HANDLER
function handleSeatClick(seatNumber, button) {
    console.log('🖱️ Seat clicked:', seatNumber, 'Button:', button);

    // Check if booking is allowed
    if (!isBookingAllowed) {
        alert('Booking is no longer available for this schedule. You can only view the seat layout.');
        return;
    }

    // Check if seat is available
    if (button.disabled || button.dataset.status !== 'available') {
        console.log('❌ Seat not available:', seatNumber, 'Status:', button.dataset.status, 'Disabled:', button.disabled);
        return;
    }

    console.log('🔄 Processing seat selection for:', seatNumber);
    console.log('📊 Current selected seats before:', window.selectedSeats);

    // Toggle selection
    if (window.selectedSeats.includes(seatNumber)) {
        // Deselect
        window.selectedSeats = window.selectedSeats.filter(s => s !== seatNumber);
        button.classList.remove('bg-yellow-500');
        button.classList.add('bg-green-500', 'hover:bg-green-600');
        console.log('❌ Deselected seat:', seatNumber);
    } else {
        // Select
        window.selectedSeats.push(seatNumber);
        button.classList.remove('bg-green-500', 'hover:bg-green-600');
        button.classList.add('bg-yellow-500');
        console.log('✅ Selected seat:', seatNumber);
    }

    console.log('📊 Current selected seats after:', window.selectedSeats);
    window.updateSelectedSeatsDisplay();
}



document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Counter booking setup - SIMPLIFIED VERSION');

    // Setup seat click handlers for ALL seat buttons (not just available ones)
    const allSeatButtons = document.querySelectorAll('.seat-button');
    console.log('🔍 Found total seat buttons:', allSeatButtons.length);

    const availableSeats = document.querySelectorAll('.seat-button[data-status="available"]');
    console.log('🔍 Found available seats:', availableSeats.length);

    // Add click handlers to all seat buttons
    allSeatButtons.forEach((button, index) => {
        console.log(`🔧 Setting up seat ${index + 1}:`, {
            seatNumber: button.dataset.seat,
            status: button.dataset.status,
            disabled: button.disabled,
            classes: button.className
        });

        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const seatNumber = this.dataset.seat;
            console.log('🖱️ Seat button clicked:', seatNumber);
            handleSeatClick(seatNumber, this);
        });
    });

    console.log('✅ Seat click handlers setup complete for', allSeatButtons.length, 'seats');

    // Test if seat clicking works immediately
    setTimeout(() => {
        console.log('🧪 Auto-testing seat selection after 2 seconds...');
        const testSeat = document.querySelector('.seat-button[data-status="available"]');
        if (testSeat) {
            console.log('🎯 Found test seat:', testSeat.dataset.seat);
            console.log('🔧 Test seat classes:', testSeat.className);
            console.log('🔧 Test seat onclick:', testSeat.onclick);
        } else {
            console.log('❌ No available seats found for testing');
        }
    }, 2000);

    // Display update function is now global (defined earlier)

    // Initialize the display
    window.updateSelectedSeatsDisplay();

    // Show booking status message
    if (!isBookingAllowed) {
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-clock mr-2"></i>Booking Closed - Schedule Departed';
            submitBtn.classList.remove('bg-purple-600', 'hover:bg-purple-700');
            submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
        }

        // Show message in the booking form
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4';
            messageDiv.innerHTML = '<strong>Notice:</strong> This schedule has departed. You can view the seat layout but cannot create new bookings.';
            bookingForm.insertBefore(messageDiv, bookingForm.firstChild);
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
        console.log('🚀 Form submission started');
        console.log('Selected seats:', selectedSeats);
        console.log('Form data check:');

        // Check all required fields
        const passengerName = document.getElementById('passenger_name').value;
        const passengerPhone = document.getElementById('passenger_phone').value;
        const passengerAge = document.getElementById('passenger_age').value;
        const passengerGender = document.querySelector('select[name="passenger_gender"]').value;
        const contactPhone = document.getElementById('contact_phone').value;
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');

        console.log('Form fields:', {
            passengerName,
            passengerPhone,
            passengerAge,
            passengerGender,
            contactPhone,
            paymentMethod: paymentMethod ? paymentMethod.value : null,
            selectedSeatsCount: selectedSeats.length
        });

        // Check seat selection with better error handling
        if (selectedSeats.length === 0) {
            e.preventDefault();
            alert('❌ Please select at least one seat before booking.\n\nIf seat selection is not working, use the manual input below.');
            console.log('❌ Form blocked: No seats selected');

            // Show debug section
            const debugSection = document.getElementById('debugSection');
            if (debugSection) {
                debugSection.style.display = 'block';
            }
            return false;
        }

        // Check payment method
        if (!paymentMethod) {
            e.preventDefault();
            alert('❌ Please select a payment method (Cash, Card, or Digital).');
            console.log('❌ Form blocked: No payment method selected');
            return false;
        }

        // Check required fields with specific messages
        const missingFields = [];
        if (!passengerName) missingFields.push('Passenger Name');
        if (!passengerPhone) missingFields.push('Passenger Phone');
        if (!passengerAge) missingFields.push('Passenger Age');
        if (!passengerGender) missingFields.push('Passenger Gender');
        if (!contactPhone) missingFields.push('Contact Phone');

        if (missingFields.length > 0) {
            e.preventDefault();
            alert(`❌ Please fill in the following required fields:\n\n• ${missingFields.join('\n• ')}`);
            console.log('❌ Form blocked: Missing required fields:', missingFields);

            // Focus on first missing field
            if (!passengerName) document.getElementById('passenger_name')?.focus();
            else if (!passengerPhone) document.getElementById('passenger_phone')?.focus();
            else if (!passengerAge) document.getElementById('passenger_age')?.focus();
            else if (!passengerGender) document.getElementById('passenger_gender')?.focus();
            else if (!contactPhone) document.getElementById('contact_phone')?.focus();

            return false;
        }

        // Final check: ensure seat numbers are in the form
        const seatNumbersContainer = document.getElementById('seatNumbersContainer');
        const hiddenInputs = seatNumbersContainer ? seatNumbersContainer.querySelectorAll('input[name="seat_numbers[]"]') : [];

        console.log('Hidden seat inputs check:', {
            containerExists: !!seatNumbersContainer,
            hiddenInputsCount: hiddenInputs.length,
            hiddenInputValues: Array.from(hiddenInputs).map(input => input.value)
        });

        if (hiddenInputs.length === 0) {
            e.preventDefault();
            alert('Error: Seat selection data is missing. Please try selecting seats again.');
            console.log('❌ Form blocked: No hidden seat inputs found');
            return false;
        }

        console.log('✅ Form validation passed, submitting...');

        // Debug: Log form action and method
        const formData = new FormData(form);
        console.log('Form details:', {
            action: form.action,
            method: form.method,
            formDataEntries: Array.from(formData.entries())
        });

        // Ensure seat numbers are properly added to form data
        if (selectedSeats.length > 0) {
            // Remove any existing seat_numbers entries
            formData.delete('seat_numbers[]');

            // Add current selected seats
            selectedSeats.forEach(seatNumber => {
                formData.append('seat_numbers[]', seatNumber);
            });

            console.log('✅ Added seat numbers to form data:', selectedSeats);
        }

        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        }

        // Add a timeout to reset the button if something goes wrong
        setTimeout(() => {
            if (submitBtn && submitBtn.disabled) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-ticket-alt mr-2"></i>Create Booking';
                console.log('⚠️ Form submission timeout - button reset');
            }
        }, 10000); // 10 second timeout

        // This ensures seat numbers are always included even if hidden inputs fail
        
        // Remove any existing seat_numbers entries
        formData.delete('seat_numbers[]');

        // Add current selected seats
        selectedSeats.forEach(seatNumber => {
            formData.append('seat_numbers[]', seatNumber);
        });

        console.log('🔧 Final form data entries:', Array.from(formData.entries()));

        // Debug mode: Use fetch to see response
        if (window.location.search.includes('debug=1')) {
            e.preventDefault();
            console.log('🔍 DEBUG MODE: Using fetch for form submission');

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                console.log('Response received:', response.status, response.statusText);
                return response.text();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.includes('error') || data.includes('Error')) {
                    alert('Error in response. Check console for details.');
                } else {
                    alert('Form submitted successfully! Check console for response.');
                }

                // Reset button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-ticket-alt mr-2"></i>Create Booking';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Network error: ' + error.message);

                // Reset button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-ticket-alt mr-2"></i>Create Booking';
                }
            });
        } else {
            // Normal form submission - but we need to ensure seat numbers are included
            // Create a temporary form with the correct data
            e.preventDefault();

            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = form.action;

            // Add all form data to temp form
            for (let [key, value] of formData.entries()) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                tempForm.appendChild(input);
            }

            document.body.appendChild(tempForm);
            tempForm.submit();
        }
    });
});

// Debug functions for testing
function testSeatSelection() {
    console.log('🧪 Testing seat selection...');

    const allSeats = document.querySelectorAll('.seat-button');
    const availableSeats = document.querySelectorAll('.seat-button[data-status="available"]');

    console.log('📊 Seat analysis:', {
        totalSeats: allSeats.length,
        availableSeats: availableSeats.length,
        selectedSeats: selectedSeats.length
    });

    if (availableSeats.length === 0) {
        alert('❌ No available seats found!\n\nTotal seats: ' + allSeats.length);
        return;
    }

    const firstAvailableSeat = availableSeats[0];
    const seatNumber = firstAvailableSeat.dataset.seat;

    console.log('🎯 Testing seat:', seatNumber, 'Element:', firstAvailableSeat);

    // Simulate click
    firstAvailableSeat.click();

    setTimeout(() => {
        console.log('📊 After click - Selected seats:', selectedSeats);
        alert(`✅ Test clicked seat ${seatNumber}\n\nSelected seats: ${selectedSeats.join(', ') || 'None'}\nTotal selected: ${selectedSeats.length}`);
    }, 100);
}

function testDirectSeatClick() {
    console.log('🧪 Testing DIRECT seat click (bypassing event handlers)...');

    const availableSeats = document.querySelectorAll('.seat-button[data-status="available"]');
    if (availableSeats.length === 0) {
        alert('❌ No available seats found!');
        return;
    }

    const firstSeat = availableSeats[0];
    const seatNumber = firstSeat.dataset.seat;

    console.log('🎯 Direct testing seat:', seatNumber);

    // Call handleSeatClick directly
    handleSeatClick(seatNumber, firstSeat);

    alert(`✅ Direct test completed for seat ${seatNumber}\n\nSelected seats: ${selectedSeats.join(', ') || 'None'}`);
}

function showSelectedSeats() {
    alert(`Selected seats: ${selectedSeats.join(', ') || 'None'}\nCount: ${selectedSeats.length}`);
}

function clearSelectedSeats() {
    selectedSeats = [];
    const allSeats = document.querySelectorAll('.seat-button[data-status="available"]');
    allSeats.forEach(seat => {
        seat.classList.remove('bg-yellow-500');
        seat.classList.add('bg-green-500', 'hover:bg-green-600');
    });
    window.updateSelectedSeatsDisplay();
    alert('All seats cleared');
}

// Manual seat selection fallback
function applyManualSeats() {
    const manualSeats = document.getElementById('manualSeats').value;
    if (!manualSeats) return;

    const seatNumbers = manualSeats.split(',').map(s => s.trim()).filter(s => s);

    // Clear existing selection first
    clearSelectedSeats();

    // Add manual seats
    selectedSeats = [...seatNumbers];

    // Update visual state
    seatNumbers.forEach(seatNumber => {
        const seatButton = document.querySelector(`[data-seat="${seatNumber}"]`);
        if (seatButton && seatButton.dataset.status === 'available') {
            seatButton.classList.remove('bg-green-500', 'hover:bg-green-600');
            seatButton.classList.add('bg-yellow-500');
        }
    });

    window.updateSelectedSeatsDisplay();
    alert(`Selected seats: ${selectedSeats.join(', ')}`);
}

// Test form submission function
function testFormSubmission() {
    console.log('🧪 Testing form submission...');

    // Ensure we have some test data
    if (selectedSeats.length === 0) {
        selectedSeats = ['1', '2']; // Add test seats
        window.updateSelectedSeatsDisplay();
    }

    // Fill in required fields with test data
    document.getElementById('passenger_name').value = 'Test Passenger';
    document.getElementById('passenger_phone').value = '9876543210';
    document.getElementById('passenger_age').value = '30';
    document.getElementById('passenger_gender').value = 'male';
    document.getElementById('contact_phone').value = '9876543210';

    // Select payment method
    const cashPayment = document.querySelector('input[name="payment_method"][value="cash"]');
    if (cashPayment) cashPayment.checked = true;

    // Test with debug route
    const form = document.getElementById('bookingForm');
    const originalAction = form.action;

    // Change to debug route temporarily
    form.action = form.action.replace('/book/', '/debug/counter-booking/');

    console.log('🔧 Form action changed to:', form.action);
    console.log('🔧 Selected seats:', selectedSeats);
    console.log('🔧 Form data preview:', new FormData(form));

    // Submit form
    alert('Form will submit to debug route. Check console and logs for results.');
    form.submit();
}
</script>
@endpush
@endsection
