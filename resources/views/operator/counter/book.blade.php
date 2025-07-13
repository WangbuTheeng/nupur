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
                                <button onclick="forceRenderLayout()" class="ml-2 px-2 py-1 bg-green-500 text-white rounded text-xs">Force Render</button>
                                <button onclick="debugSeatRendering()" class="ml-2 px-2 py-1 bg-red-500 text-white rounded text-xs">Debug Seats</button>
                            </div>
                        @endif

                        <div id="counterSeatLayoutDisplay" class="min-h-[400px]">
                            <!-- Direct PHP-rendered bus layout - No JavaScript dependency -->
                            <div class="php-seat-layout">
                                <!-- Bus Frame -->
                                <div style="max-width: 500px; margin: 0 auto; padding: 20px; background: #f8fafc; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                    <div style="background: linear-gradient(to bottom, #f8fafc, #e2e8f0); border: 3px solid #475569; border-radius: 25px; padding: 15px;">

                                        <!-- Top section with door and driver -->
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding: 8px 15px; background: rgba(255,255,255,0.7); border-radius: 15px; border: 2px solid #cbd5e1;">
                                            <div style="background: #3b82f6; color: white; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">🚪 Door</div>
                                            <div style="background: #10b981; color: white; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">👨‍✈️ Driver</div>
                                        </div>

                                        <!-- Seating area -->
                                        <div style="display: flex; flex-direction: column; gap: 8px; align-items: center;">
                                            @if(isset($seatMap['seats']) && is_array($seatMap['seats']))
                                                @php
                                                    $seats = $seatMap['seats'];
                                                    $rows = $seatMap['rows'] ?? 8;
                                                    $columns = $seatMap['columns'] ?? 4;
                                                    $aislePosition = $seatMap['aisle_position'] ?? 2;

                                                    // Group seats by row
                                                    $seatsByRow = [];
                                                    foreach ($seats as $seat) {
                                                        $row = $seat['row'] ?? 1;
                                                        if (!isset($seatsByRow[$row])) {
                                                            $seatsByRow[$row] = [];
                                                        }
                                                        $seatsByRow[$row][] = $seat;
                                                    }

                                                    // Sort seats within each row by column
                                                    foreach ($seatsByRow as &$rowSeats) {
                                                        usort($rowSeats, function($a, $b) {
                                                            return ($a['column'] ?? 1) - ($b['column'] ?? 1);
                                                        });
                                                    }
                                                @endphp

                                                @for($row = 1; $row <= $rows; $row++)
                                                    @if(isset($seatsByRow[$row]) && count($seatsByRow[$row]) > 0)
                                                        @php
                                                            $rowSeats = $seatsByRow[$row];
                                                            $isBackRow = ($seatMap['has_back_row'] ?? false) && $row == $rows;
                                                        @endphp

                                                        @if($isBackRow)
                                                            {{-- Back Row - Continuous full-width seats --}}
                                                            <div style="display: flex; gap: 4px; justify-content: center; align-items: center; background: rgba(255,255,255,0.3); padding: 4px 8px; border-radius: 8px;">
                                                                @foreach($rowSeats as $seat)
                                                                    @php
                                                                        $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? 'N/A';
                                                                        $isBooked = $seat['is_booked'] ?? false;
                                                                        $isWindow = $seat['is_window'] ?? false;
                                                                    @endphp

                                                                    <button type="button"
                                                                            class="seat-button back-row-seat {{ $isBooked ? 'booked' : 'available' }} {{ $isWindow ? 'window-seat' : '' }}"
                                                                            data-seat="{{ $seatNumber }}"
                                                                            data-booked="{{ $isBooked ? 'true' : 'false' }}"
                                                                            data-window="{{ $isWindow ? 'true' : 'false' }}"
                                                                            @if($isBooked) disabled @endif
                                                                            onclick="toggleSeatSelection(this, '{{ $seatNumber }}')"
                                                                            title="Seat {{ $seatNumber }}{{ $isWindow ? ' (Window)' : '' }} - Back Row"
                                                                            style="width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; transition: all 0.2s ease; border: 2px solid #374151; position: relative; z-index: 1; background: {{ $isBooked ? '#ef4444' : '#22c55e' }}; color: white; text-decoration: none; outline: none; margin: 2px;">
                                                                        {{ $seatNumber }}
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            {{-- Regular Row - With aisle spacing --}}
                                                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                                @php
                                                                    // Sort seats by column for proper left-to-right order
                                                                    usort($rowSeats, function($a, $b) {
                                                                        return ($a['column'] ?? 1) - ($b['column'] ?? 1);
                                                                    });
                                                                @endphp

                                                                @foreach($rowSeats as $seat)
                                                                    @php
                                                                        $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? 'N/A';
                                                                        $isBooked = $seat['is_booked'] ?? false;
                                                                        $isWindow = $seat['is_window'] ?? false;
                                                                        $column = $seat['column'] ?? 1;
                                                                    @endphp

                                                                    {{-- Add aisle space after position 2 for 2x2 layout --}}
                                                                    @if($column == $aislePosition + 1)
                                                                        <div style="width: 20px; display: flex; align-items: center; justify-content: center; color: #6b7280; font-size: 14px;">|</div>
                                                                    @endif

                                                                    <button type="button"
                                                                            class="seat-button regular-seat {{ $isBooked ? 'booked' : 'available' }} {{ $isWindow ? 'window-seat' : '' }}"
                                                                            data-seat="{{ $seatNumber }}"
                                                                            data-booked="{{ $isBooked ? 'true' : 'false' }}"
                                                                            data-window="{{ $isWindow ? 'true' : 'false' }}"
                                                                            @if($isBooked) disabled @endif
                                                                            onclick="toggleSeatSelection(this, '{{ $seatNumber }}')"
                                                                            title="Seat {{ $seatNumber }}{{ $isWindow ? ' (Window)' : '' }}"
                                                                            style="width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 11px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; transition: all 0.2s ease; border: 2px solid #374151; position: relative; z-index: 1; background: {{ $isBooked ? '#ef4444' : '#22c55e' }}; color: white; text-decoration: none; outline: none; margin: 2px;">
                                                                        {{ $seatNumber }}
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endfor
                                            @else
                                                <div style="text-align: center; padding: 2rem; color: #ef4444;">
                                                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                                                    <p>No seat data available</p>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Layout info -->
                                        <div style="text-align: center; margin-top: 15px; padding: 8px; background: rgba(255,255,255,0.8); border-radius: 8px; font-size: 12px; color: #6b7280;">
                                            <strong>{{ strtoupper($seatMap['layout_type'] ?? '2X2') }}</strong> Layout •
                                            <strong>{{ count($seatMap['seats'] ?? []) }}</strong> Seats •
                                            {{ ($seatMap['has_back_row'] ?? false) ? 'With' : 'Without' }} Back Row
                                        </div>
                                    </div>
                                </div>
                            </div>
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

@push('styles')
<style>
/* Force seat button styles to override any conflicts */
.seat-button, button.seat-button {
    width: 36px !important;
    height: 36px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    border: 2px solid #374151 !important;
    position: relative !important;
    z-index: 1 !important;
    color: white !important;
    text-decoration: none !important;
    outline: none !important;
    margin: 2px !important;
    background: #22c55e !important; /* Default green */
}

.seat-button.back-row-seat {
    width: 32px !important;
    height: 32px !important;
    font-size: 10px !important;
}
/* Counter booking seat styles - ensuring proper selection colors */
.seat-button {
    width: 36px !important;
    height: 36px !important;
    border-radius: 8px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
    border: 2px solid #374151 !important;
    position: relative !important;
    z-index: 1 !important;
    background: #22c55e !important; /* Default green for available */
    color: white !important;
    text-decoration: none !important;
    outline: none !important;
    margin: 2px !important;
}

/* Back row seats are smaller */
.seat-button.back-row-seat {
    width: 32px;
    height: 32px;
    font-size: 10px !important;
}

/* Available seat styling - Green color */
.seat-button.available {
    background: #22c55e !important; /* Green for available */
    color: white !important;
    border-color: #16a34a !important;
}

.seat-button.available:hover {
    background: #16a34a !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
}

/* Selected/Reserved seat styling - Blue color - HIGHEST PRIORITY */
.seat-button.selected,
.seat-button.reserved {
    background: #3b82f6 !important; /* Blue for reserved/selected */
    color: white !important;
    border-color: #1d4ed8 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3) !important;
    z-index: 2 !important;
}

.seat-button.selected:hover,
.seat-button.reserved:hover {
    background: #2563eb !important;
    border-color: #1e40af !important;
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

/* Window seat border enhancement */
.seat-button.window-seat:not(.selected):not(.booked):not(.reserved) {
    border-color: #0ea5e9 !important;
}

/* Booked/Paid seats - Red color */
.seat-button.booked,
.seat-button[disabled] {
    background: #ef4444 !important; /* Red for booked/paid */
    color: white !important;
    cursor: not-allowed !important;
    opacity: 0.9 !important;
    border-color: #dc2626 !important;
}

.seat-button.booked:hover,
.seat-button[disabled]:hover {
    background: #ef4444 !important;
    transform: none !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
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
// Simple, guaranteed-to-work seat selection system
let selectedSeats = [];
const farePerSeat = {{ $schedule->fare }};

// Debug function to check seat rendering
function debugSeatRendering() {
    console.log('🔍 Debugging seat rendering...');

    const seatButtons = document.querySelectorAll('.seat-button');
    const seatContainer = document.getElementById('counterSeatLayoutDisplay');

    console.log('Seat container:', seatContainer);
    console.log('Seat buttons found:', seatButtons.length);

    if (seatButtons.length === 0) {
        console.log('❌ No seat buttons found! Checking HTML structure...');
        console.log('Container HTML:', seatContainer ? seatContainer.innerHTML : 'Container not found');
    } else {
        console.log('✅ Seat buttons found:', Array.from(seatButtons).map(btn => ({
            text: btn.textContent,
            classes: btn.className,
            dataset: btn.dataset,
            disabled: btn.disabled
        })));
    }

    // Check if seats are being rendered as text instead of buttons
    const allSeats = seatContainer ? seatContainer.querySelectorAll('*') : [];
    console.log('All elements in seat container:', Array.from(allSeats).map(el => ({
        tag: el.tagName,
        text: el.textContent.trim(),
        classes: el.className
    })));
}

// Initialize seat colors based on their current state
function initializeSeatColors() {
    const seatButtons = document.querySelectorAll('.seat-button');
    console.log('🎨 Initializing seat colors for', seatButtons.length, 'seats');

    seatButtons.forEach(button => {
        const isBooked = button.dataset.booked === 'true' || button.disabled;
        const isSelected = selectedSeats.includes(button.dataset.seat);

        // Remove all state classes first
        button.classList.remove('available', 'selected', 'booked', 'reserved');

        if (isBooked) {
            button.classList.add('booked');
            button.style.background = '#ef4444'; // Red for booked
            button.style.borderColor = '#dc2626';
            button.style.cursor = 'not-allowed';
            console.log('🔴 Seat', button.dataset.seat, 'marked as booked');
        } else if (isSelected) {
            button.classList.add('selected');
            button.style.background = '#3b82f6'; // Blue for selected
            button.style.borderColor = '#1d4ed8';
            button.style.cursor = 'pointer';
            console.log('🔵 Seat', button.dataset.seat, 'marked as selected');
        } else {
            button.classList.add('available');
            button.style.background = '#22c55e'; // Green for available
            button.style.borderColor = '#16a34a';
            button.style.cursor = 'pointer';
            console.log('🟢 Seat', button.dataset.seat, 'marked as available');
        }
    });
}

// Toggle seat selection - called directly from PHP-rendered buttons
function toggleSeatSelection(button, seatNumber) {
    console.log('🪑 Seat clicked:', seatNumber, 'Button classes:', button.className);

    const isBooked = button.dataset.booked === 'true';
    if (isBooked || button.disabled) {
        console.log('❌ Seat is booked/disabled, cannot select');
        return;
    }

    const isSelected = button.classList.contains('selected');
    const isWindow = button.dataset.window === 'true';

    console.log('Current state:', { isSelected, isWindow, classList: button.classList.toString() });

    if (isSelected) {
        // Deselect seat - return to available state
        button.classList.remove('selected');
        button.classList.add('available');
        button.style.background = '#22c55e'; // Green for available
        button.style.borderColor = '#16a34a';
        selectedSeats = selectedSeats.filter(seat => seat !== seatNumber);
        console.log('❌ Deselected seat:', seatNumber, 'New classes:', button.className);
    } else {
        // Select seat - make it selected
        button.classList.remove('available');
        button.classList.add('selected');
        button.style.background = '#3b82f6'; // Blue for selected
        button.style.borderColor = '#1d4ed8';
        selectedSeats.push(seatNumber);
        console.log('✅ Selected seat:', seatNumber, 'New classes:', button.className);
    }

    console.log('Selected seats array:', selectedSeats);
    updateSelectedSeatsDisplay();
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Counter booking setup complete - using PHP-rendered layout');

    // Debug: Check authentication and CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]') || document.querySelector('input[name="_token"]');
    console.log('🔐 Auth & Security check:', {
        csrfTokenExists: !!csrfToken,
        csrfTokenValue: csrfToken ? csrfToken.content || csrfToken.value : null,
        currentUrl: window.location.href,
        userAgent: navigator.userAgent
    });

    // Get display elements
    const selectedSeatsDisplay = document.getElementById('selectedSeats');
    const totalAmountDisplay = document.getElementById('totalAmount');
    const seatNumbersContainer = document.getElementById('seatNumbersContainer');
    const submitBtn = document.getElementById('submitBtn');

    console.log('📋 Found elements:', {
        selectedSeatsDisplay: !!selectedSeatsDisplay,
        totalAmountDisplay: !!totalAmountDisplay,
        seatNumbersContainer: !!seatNumbersContainer,
        submitBtn: !!submitBtn
    });

    // Debug: Check all seat buttons
    const seatButtons = document.querySelectorAll('.seat-button');
    console.log('🪑 Found seat buttons:', seatButtons.length);

    // Initialize seat colors on page load
    initializeSeatColors();

    // Auto-run debug function
    setTimeout(() => {
        debugSeatRendering();
    }, 1000);

    // Add click event listeners as backup
    seatButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            console.log('🖱️ Button clicked via event listener:', this.dataset.seat);
        });
    });

    // Initialize display update function
    window.updateSelectedSeatsDisplay = function() {
        const selectedSeatsDisplay = document.getElementById('selectedSeats');
        const totalAmountDisplay = document.getElementById('totalAmount');
        const seatNumbersContainer = document.getElementById('seatNumbersContainer');
        const submitBtn = document.getElementById('submitBtn');

        console.log('🔄 Updating display, selected seats:', selectedSeats);

        if (selectedSeats.length === 0) {
            if (selectedSeatsDisplay) selectedSeatsDisplay.textContent = 'None';
            if (totalAmountDisplay) totalAmountDisplay.textContent = 'Rs. 0.00';
            if (submitBtn) submitBtn.disabled = true;
            if (seatNumbersContainer) seatNumbersContainer.innerHTML = '';
        } else {
            selectedSeats.sort((a, b) => {
                // Sort alphanumerically (A1, A2, B1, B2, etc.)
                return a.localeCompare(b, undefined, { numeric: true, sensitivity: 'base' });
            });

            if (selectedSeatsDisplay) selectedSeatsDisplay.textContent = selectedSeats.join(', ');

            const totalAmount = selectedSeats.length * farePerSeat;
            if (totalAmountDisplay) totalAmountDisplay.textContent = `Rs. ${totalAmount.toFixed(2)}`;

            if (submitBtn) submitBtn.disabled = false;

            // Update seat numbers in the form
            if (seatNumbersContainer) {
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

        // Ensure seat colors are properly applied
        initializeSeatColors();

        console.log('✅ Display updated:', {
            selectedCount: selectedSeats.length,
            totalAmount: selectedSeats.length * farePerSeat,
            seats: selectedSeats
        });
    };

    // Initialize the display
    updateSelectedSeatsDisplay();

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

        if (selectedSeats.length === 0) {
            e.preventDefault();
            alert('Please select at least one seat.');
            console.log('❌ Form blocked: No seats selected');
            return false;
        }

        if (!paymentMethod) {
            e.preventDefault();
            alert('Please select a payment method.');
            console.log('❌ Form blocked: No payment method selected');
            return false;
        }

        if (!passengerName || !passengerPhone || !passengerAge || !passengerGender || !contactPhone) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            console.log('❌ Form blocked: Missing required fields');
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
        console.log('Form details:', {
            action: form.action,
            method: form.method,
            formData: new FormData(form)
        });

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
                submitBtn.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Book Ticket';
                console.log('⚠️ Form submission timeout - button reset');
            }
        }, 10000); // 10 second timeout

        // Alternative: Try fetch-based submission for debugging
        /*
        e.preventDefault();
        const formData = new FormData(form);

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
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
        */
    });
});
</script>
@endpush
@endsection
