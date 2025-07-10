@extends('layouts.app')

@section('title', 'Digital Ticket')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Digital Ticket</h1>
        <p class="text-gray-600">{{ $booking->booking_reference }}</p>
    </div>

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 text-center">
            <h2 class="text-3xl font-bold mb-2">BookNGo</h2>
            <p class="text-blue-100">Digital Bus Ticket</p>
        </div>

        <div class="p-6">
            <!-- Booking Reference -->
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $booking->booking_reference }}</h3>
                <p class="text-gray-600">Booking Reference Number</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 mt-2">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>

            <!-- Trip Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 border-b-2 border-blue-500 pb-2">Trip Information</h4>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Route:</dt>
                            <dd class="font-medium text-gray-900">{{ $booking->schedule->route->full_name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Bus:</dt>
                            <dd class="font-medium text-gray-900">{{ $booking->schedule->bus->display_name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Bus Type:</dt>
                            <dd class="font-medium text-gray-900">{{ $booking->schedule->bus->busType->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Travel Date:</dt>
                            <dd class="font-medium text-gray-900">{{ $booking->schedule->travel_date->format('l, M d, Y') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Departure:</dt>
                            <dd class="font-medium text-gray-900">{{ $booking->schedule->departure_time->format('h:i A') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Arrival:</dt>
                            <dd class="font-medium text-gray-900">{{ $booking->schedule->arrival_time->format('h:i A') }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 border-b-2 border-blue-500 pb-2">Booking Details</h4>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Passenger:</dt>
                            <dd class="font-medium text-gray-900">{{ $booking->user->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Contact:</dt>
                            <dd class="font-medium text-gray-900">{{ $booking->contact_phone }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Email:</dt>
                            <dd class="font-medium text-gray-900">{{ $booking->contact_email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Seats:</dt>
                            <dd class="font-medium text-gray-900">{{ $booking->seat_numbers_string }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Passengers:</dt>
                            <dd class="font-medium text-gray-900">{{ $booking->passenger_count }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Total Amount:</dt>
                            <dd class="font-medium text-gray-900 text-lg">NPR {{ number_format($booking->total_amount) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Passenger Details -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Passenger Information</h4>
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($booking->passenger_details as $index => $passenger)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $booking->seat_numbers[$index] ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $passenger['name'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $passenger['age'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($passenger['gender']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="text-center bg-gray-50 rounded-lg p-6 mb-8">
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Verification QR Code</h4>
                <div class="inline-block p-4 bg-white rounded-lg shadow-md">
                    <img src="{{ $qrCodeDataUri }}" alt="QR Code" class="w-48 h-48 mx-auto">
                </div>
                <p class="text-gray-600 text-sm mt-4">
                    Show this QR code to the bus conductor for verification
                </p>
            </div>

            <!-- Instructions -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-blue-800">Important Instructions:</h4>
                        <ul class="mt-2 text-sm text-blue-700 list-disc list-inside space-y-1">
                            <li>Please arrive at the departure point at least 15 minutes before departure time</li>
                            <li>Show this ticket (digital or printed) to the bus conductor</li>
                            <li>Keep your ID proof ready for verification</li>
                            <li>Contact {{ $booking->contact_phone }} for any queries</li>
                            <li>Cancellation is allowed up to 2 hours before departure</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="{{ route('tickets.download', $booking) }}" 
                   class="bg-blue-600 text-white hover:bg-blue-700 px-6 py-3 rounded-md font-semibold transition duration-200 flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Ticket
                </a>
                
                <button onclick="window.print()" 
                        class="bg-green-600 text-white hover:bg-green-700 px-6 py-3 rounded-md font-semibold transition duration-200 flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Ticket
                </button>
                
                <a href="{{ route('bookings.show', $booking) }}" 
                   class="bg-gray-600 text-white hover:bg-gray-700 px-6 py-3 rounded-md font-semibold transition duration-200">
                    Back to Booking
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 px-6 py-4 text-center text-sm text-gray-600">
            <p>Generated on {{ now()->format('M d, Y h:i A') }} | BookNGo - Your Trusted Travel Partner</p>
        </div>
    </div>
</div>

<style>
@media print {
    .max-w-4xl {
        max-width: none;
    }
    
    nav, .bg-gray-50.py-8, footer {
        display: none !important;
    }
    
    body {
        background: white !important;
    }
}
</style>
@endsection
