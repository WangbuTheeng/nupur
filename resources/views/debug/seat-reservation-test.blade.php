<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Seat Reservation Debug Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Seat Reservation Debug Test</h1>
        
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Test Seat Reservation for Schedule 285</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Seat Numbers to Reserve:</label>
                <input type="text" id="seatNumbers" value="11" class="border border-gray-300 rounded px-3 py-2 w-full" placeholder="Enter seat numbers separated by commas">
            </div>
            
            <button onclick="testSeatReservation()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Test Seat Reservation
            </button>
            
            <button onclick="testReserveSeatsOnly()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 ml-2">
                Test Reserve Seats Only
            </button>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Test Results:</h3>
            <pre id="results" class="bg-gray-100 p-4 rounded text-sm overflow-auto max-h-96"></pre>
        </div>
    </div>

    <script>
        function testSeatReservation() {
            const seatNumbers = document.getElementById('seatNumbers').value.split(',').map(s => s.trim());
            const resultsDiv = document.getElementById('results');
            
            resultsDiv.textContent = 'Testing seat reservation...';
            
            fetch('/debug/test-seat-reservation/285', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    seat_numbers: seatNumbers
                })
            })
            .then(response => response.json())
            .then(data => {
                resultsDiv.textContent = JSON.stringify(data, null, 2);
            })
            .catch(error => {
                resultsDiv.textContent = 'Error: ' + error.message;
                console.error('Error:', error);
            });
        }
        
        function testReserveSeatsOnly() {
            const seatNumbers = document.getElementById('seatNumbers').value.split(',').map(s => s.trim());
            const resultsDiv = document.getElementById('results');
            
            resultsDiv.textContent = 'Testing reserve seats only...';
            
            fetch('/booking/reserve-seats-only', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    schedule_id: 285,
                    seat_numbers: seatNumbers
                })
            })
            .then(response => response.json())
            .then(data => {
                resultsDiv.textContent = JSON.stringify(data, null, 2);
            })
            .catch(error => {
                resultsDiv.textContent = 'Error: ' + error.message;
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>
