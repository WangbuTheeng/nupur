@extends('layouts.app')

@section('title', 'Verify Ticket')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Ticket Verification</h1>
        <p class="text-gray-600">Scan QR code or enter booking reference to verify ticket</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <!-- QR Scanner Section -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">QR Code Scanner</h2>
            <div class="text-center">
                <div id="qr-scanner" class="mx-auto mb-4" style="width: 300px; height: 300px; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center;">
                    <p class="text-gray-500">QR Scanner will appear here</p>
                </div>
                <button id="start-scanner" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md font-semibold transition duration-200">
                    Start QR Scanner
                </button>
                <button id="stop-scanner" class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md font-semibold transition duration-200 hidden">
                    Stop Scanner
                </button>
            </div>
        </div>

        <!-- Manual Entry Section -->
        <div class="border-t border-gray-200 pt-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Manual Verification</h2>
            <form id="manual-verify-form" class="space-y-4">
                <div>
                    <label for="booking_reference" class="block text-sm font-medium text-gray-700 mb-2">Booking Reference</label>
                    <input type="text" id="booking_reference" name="booking_reference" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter booking reference (e.g., BNG-ABC12345)">
                </div>
                <button type="submit" 
                        class="w-full bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-md font-semibold transition duration-200">
                    Verify Ticket
                </button>
            </form>
        </div>

        <!-- Verification Result -->
        <div id="verification-result" class="mt-8 hidden">
            <!-- Results will be displayed here -->
        </div>
    </div>
</div>

<!-- QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let html5QrcodeScanner = null;

// Start QR Scanner
document.getElementById('start-scanner').addEventListener('click', function() {
    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        console.log(`Code matched = ${decodedText}`, decodedResult);
        verifyQRCode(decodedText);
        stopScanner();
    };

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    html5QrcodeScanner = new Html5Qrcode("qr-scanner");
    html5QrcodeScanner.start(
        { facingMode: "environment" },
        config,
        qrCodeSuccessCallback
    ).catch(err => {
        console.error(`Unable to start scanning, error: ${err}`);
        alert('Unable to start camera. Please check permissions or use manual entry.');
    });

    document.getElementById('start-scanner').classList.add('hidden');
    document.getElementById('stop-scanner').classList.remove('hidden');
});

// Stop QR Scanner
document.getElementById('stop-scanner').addEventListener('click', stopScanner);

function stopScanner() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.stop().then(() => {
            html5QrcodeScanner.clear();
            document.getElementById('qr-scanner').innerHTML = '<p class="text-gray-500">QR Scanner stopped</p>';
        }).catch(err => {
            console.error(`Unable to stop scanning, error: ${err}`);
        });
    }
    
    document.getElementById('start-scanner').classList.remove('hidden');
    document.getElementById('stop-scanner').classList.add('hidden');
}

// Manual verification form
document.getElementById('manual-verify-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const bookingRef = document.getElementById('booking_reference').value.trim();
    if (bookingRef) {
        verifyBookingReference(bookingRef);
    }
});

// Verify QR Code
function verifyQRCode(qrData) {
    fetch('{{ route("tickets.verify") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ qr_data: qrData })
    })
    .then(response => response.json())
    .then(data => {
        displayVerificationResult(data);
    })
    .catch(error => {
        console.error('Error:', error);
        displayVerificationResult({ valid: false, message: 'Verification failed' });
    });
}

// Verify Booking Reference
function verifyBookingReference(bookingRef) {
    fetch('{{ route("tickets.verify.manual") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ booking_reference: bookingRef })
    })
    .then(response => response.json())
    .then(data => {
        displayVerificationResult(data);
    })
    .catch(error => {
        console.error('Error:', error);
        displayVerificationResult({ valid: false, message: 'Verification failed' });
    });
}

// Display verification result
function displayVerificationResult(data) {
    const resultDiv = document.getElementById('verification-result');
    resultDiv.classList.remove('hidden');
    
    if (data.valid) {
        resultDiv.innerHTML = `
            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">Valid Ticket</h3>
                        <div class="mt-2 text-sm text-green-700">
                            <p><strong>Reference:</strong> ${data.booking.reference}</p>
                            <p><strong>Passenger:</strong> ${data.booking.passenger_name}</p>
                            <p><strong>Route:</strong> ${data.booking.route}</p>
                            <p><strong>Bus:</strong> ${data.booking.bus}</p>
                            <p><strong>Date:</strong> ${data.booking.travel_date}</p>
                            <p><strong>Departure:</strong> ${data.booking.departure_time}</p>
                            <p><strong>Seats:</strong> ${data.booking.seats}</p>
                            <p><strong>Passengers:</strong> ${data.booking.passenger_count}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else {
        resultDiv.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Invalid Ticket</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>${data.message}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
}
</script>
@endsection
