<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Counter Booking Debug Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Counter Booking Debug Test</h1>
        
        <!-- Test Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-blue-600 mb-4">Test Booking Form</h2>
            
            <form id="testForm" action="{{ route('test.booking.submit') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Passenger Name *</label>
                        <input type="text" name="passenger_name" value="Test User" required
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <input type="tel" name="passenger_phone" value="9876543210" required
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Age *</label>
                        <input type="number" name="passenger_age" value="25" min="1" max="120" required
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender *</label>
                        <select name="passenger_gender" required
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Gender</option>
                            <option value="male" selected>Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone *</label>
                        <input type="tel" name="contact_phone" value="9876543210" required
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Email</label>
                        <input type="email" name="contact_email" value="test@example.com"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                
                <!-- Seat Selection Simulation -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Seat Selection Test</h3>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <button type="button" class="seat-btn px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600" 
                                data-seat="A1" onclick="toggleSeat(this)">A1</button>
                        <button type="button" class="seat-btn px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600" 
                                data-seat="A2" onclick="toggleSeat(this)">A2</button>
                        <button type="button" class="seat-btn px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600" 
                                data-seat="B1" onclick="toggleSeat(this)">B1</button>
                        <button type="button" class="seat-btn px-3 py-2 bg-green-500 text-white rounded hover:bg-green-600" 
                                data-seat="B2" onclick="toggleSeat(this)">B2</button>
                    </div>
                    
                    <div class="mb-4">
                        <strong>Selected Seats:</strong> <span id="selectedSeatsDisplay">None</span>
                    </div>
                    
                    <!-- Hidden seat inputs -->
                    <div id="seatInputs"></div>
                </div>
                
                <!-- Payment Method -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="cash" checked class="mr-2">
                            Cash
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="card" class="mr-2">
                            Card
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="payment_method" value="digital" class="mr-2">
                            Digital
                        </label>
                    </div>
                </div>
                
                <button type="submit" id="submitBtn" 
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors">
                    Test Submit
                </button>
            </form>
        </div>
        
        <!-- Debug Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Debug Information</h2>
            <div id="debugInfo" class="space-y-2 text-sm font-mono bg-gray-100 p-4 rounded">
                <div>Loading debug information...</div>
            </div>
        </div>
    </div>

    <script>
        let selectedSeats = [];
        
        function toggleSeat(button) {
            const seatNumber = button.dataset.seat;
            const isSelected = selectedSeats.includes(seatNumber);
            
            if (isSelected) {
                // Deselect
                selectedSeats = selectedSeats.filter(seat => seat !== seatNumber);
                button.classList.remove('bg-blue-500');
                button.classList.add('bg-green-500');
            } else {
                // Select
                selectedSeats.push(seatNumber);
                button.classList.remove('bg-green-500');
                button.classList.add('bg-blue-500');
            }
            
            updateDisplay();
        }
        
        function updateDisplay() {
            const display = document.getElementById('selectedSeatsDisplay');
            const seatInputs = document.getElementById('seatInputs');
            
            if (selectedSeats.length === 0) {
                display.textContent = 'None';
                seatInputs.innerHTML = '';
            } else {
                display.textContent = selectedSeats.join(', ');
                
                // Create hidden inputs
                seatInputs.innerHTML = '';
                selectedSeats.forEach(seat => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'seat_numbers[]';
                    input.value = seat;
                    seatInputs.appendChild(input);
                });
            }
            
            updateDebugInfo();
        }
        
        function updateDebugInfo() {
            const debugInfo = document.getElementById('debugInfo');
            const form = document.getElementById('testForm');
            const formData = new FormData(form);
            
            let info = [];
            info.push(`Selected Seats: ${selectedSeats.length} (${selectedSeats.join(', ')})`);
            info.push(`Form Action: ${form.action}`);
            info.push(`Form Method: ${form.method}`);
            info.push(`CSRF Token: ${document.querySelector('meta[name="csrf-token"]').content}`);
            info.push(`Hidden Inputs: ${document.querySelectorAll('input[name="seat_numbers[]"]').length}`);
            
            info.push('Form Data:');
            for (let [key, value] of formData.entries()) {
                info.push(`  ${key}: ${value}`);
            }
            
            debugInfo.innerHTML = info.map(line => `<div>${line}</div>`).join('');
        }
        
        // Form submission handling
        document.getElementById('testForm').addEventListener('submit', function(e) {
            console.log('Form submitted!');
            console.log('Selected seats:', selectedSeats);
            console.log('Form data:', new FormData(this));
            
            if (selectedSeats.length === 0) {
                e.preventDefault();
                alert('Please select at least one seat');
                return;
            }
            
            // Let the form submit normally for testing
        });
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateDebugInfo();
            
            // Auto-select A1 for testing
            setTimeout(() => {
                const a1Button = document.querySelector('[data-seat="A1"]');
                if (a1Button) {
                    toggleSeat(a1Button);
                }
            }, 500);
        });
    </script>
</body>
</html>
