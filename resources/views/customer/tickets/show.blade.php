@extends('layouts.app')

@section('title', 'E-Ticket - ' . $booking->booking_reference)

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-t-xl shadow-lg border border-gray-200 px-6 py-8">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">BookNGO E-Ticket</h1>
                </div>
                <p class="text-lg text-gray-600 mb-2">Your booking is confirmed!</p>
                <p class="text-xl font-bold text-blue-600">{{ $booking->booking_reference }}</p>
                
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('tickets.download', $booking) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download PDF
                    </a>
                    <a href="{{ route('tickets.email', $booking) }}" 
                       class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email Ticket
                    </a>
                    <button onclick="window.print()" 
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Ticket Content -->
        <div class="bg-white shadow-lg border-l border-r border-gray-200">
            <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-gray-200">
                <!-- Journey Information -->
                <div class="lg:col-span-2 p-6">
                    <!-- Route Display -->
                    <div class="mb-8">
                        <div class="flex items-center justify-center mb-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $booking->schedule->route->sourceCity->name }}</div>
                                <div class="text-sm text-gray-500">Departure</div>
                            </div>
                            <div class="mx-8 flex items-center">
                                <div class="w-16 border-t-2 border-blue-300"></div>
                                <svg class="w-6 h-6 mx-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                </svg>
                                <div class="w-16 border-t-2 border-blue-300"></div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $booking->schedule->route->destinationCity->name }}</div>
                                <div class="text-sm text-gray-500">Arrival</div>
                            </div>
                        </div>

                        <!-- Journey Details -->
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="text-sm text-gray-500 mb-1">Departure Time</div>
                                <div class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('g:i A') }}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="text-sm text-gray-500 mb-1">Travel Date</div>
                                <div class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->schedule->travel_date)->format('M j, Y') }}</div>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="text-sm text-gray-500 mb-1">Arrival Time</div>
                                <div class="text-lg font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->schedule->arrival_time)->format('g:i A') }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Bus & Operator Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bus & Operator Details</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-500">Operator</div>
                                <div class="font-semibold text-gray-900">{{ $booking->schedule->operator->company_name ?? $booking->schedule->operator->name }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Bus Number</div>
                                <div class="font-semibold text-gray-900">{{ $booking->schedule->bus->bus_number }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Bus Type</div>
                                <div class="font-semibold text-gray-900">{{ $booking->schedule->bus->busType->name }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Status</div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Passenger Details</h3>
                        <div class="space-y-3">
                            @foreach($booking->passenger_details as $index => $passenger)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $passenger['name'] }}</div>
                                            <div class="text-sm text-gray-600">
                                                Age: {{ $passenger['age'] }} | Gender: {{ ucfirst($passenger['gender']) }}
                                                @if(isset($passenger['phone']) && $passenger['phone'])
                                                    | Phone: {{ $passenger['phone'] }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-gray-500">Seat</div>
                                            <div class="font-bold text-blue-600">{{ $booking->seat_numbers[$index] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Booking Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-500">Booking Date</div>
                                <div class="font-semibold text-gray-900">{{ $booking->created_at->format('M j, Y g:i A') }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Contact Phone</div>
                                <div class="font-semibold text-gray-900">{{ $booking->contact_phone }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Contact Email</div>
                                <div class="font-semibold text-gray-900">{{ $booking->contact_email }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500">Payment Method</div>
                                <div class="font-semibold text-gray-900">{{ ucfirst($booking->payment_method ?? 'N/A') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="p-6 bg-gray-50">
                    <!-- Seat Numbers -->
                    <div class="text-center mb-6">
                        <div class="text-sm text-gray-500 mb-2">Your Seat(s)</div>
                        <div class="bg-blue-600 text-white px-4 py-3 rounded-lg font-bold text-lg">
                            {{ implode(', ', $booking->seat_numbers) }}
                        </div>
                    </div>

                    <!-- QR Code -->
                    <div class="text-center mb-6">
                        <div class="text-sm text-gray-500 mb-3">Verification QR Code</div>
                        <div class="bg-white p-4 rounded-lg shadow-sm inline-block">
                            <img src="data:image/png;base64,{{ $qrCodeImage }}" alt="QR Code" class="w-32 h-32 mx-auto">
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Scan for ticket verification</p>
                    </div>

                    <!-- Amount -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                        <div class="text-sm text-green-600 mb-1">Total Amount Paid</div>
                        <div class="text-2xl font-bold text-green-700">Rs. {{ number_format($booking->total_amount, 2) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-white rounded-b-xl shadow-lg border border-gray-200 px-6 py-6">
            <div class="text-center">
                <h4 class="font-semibold text-gray-900 mb-3">Important Instructions</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div class="flex items-start">
                        <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Arrive at departure point 30 minutes before departure time</span>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                        </svg>
                        <span>Carry a valid photo ID along with this e-ticket</span>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>This ticket is non-transferable and non-refundable after departure</span>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span>For support, call +977-1-4444444 or email support@bookngo.com</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Bookings -->
        <div class="text-center mt-8">
            <a href="{{ route('bookings.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path>
                </svg>
                Back to My Bookings
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
    }
    
    .bg-gray-50 {
        background: white !important;
    }
}
</style>
@endsection
