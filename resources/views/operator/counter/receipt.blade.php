@extends('layouts.operator')

@section('title', 'Booking Receipt - ' . $booking->booking_reference)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Booking Receipt</h1>
                    <p class="text-green-100">Booking successfully created</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('operator.counter.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Counter
                    </a>
                    <a href="{{ route('operator.bookings.ticket', $booking) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Compact Ticket
                    </a>
                    <a href="{{ route('operator.bookings.download-compact-ticket', $booking) }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150 ml-2">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Content -->
    <div class="bg-white overflow-hidden shadow-xl rounded-xl" id="receipt">
        <!-- Receipt Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">BookNGO</h2>
                    <p class="text-sm text-gray-600">Bus Booking Receipt</p>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold text-green-600">{{ $booking->booking_reference }}</div>
                    <div class="text-sm text-gray-500">{{ $booking->created_at->format('M d, Y H:i A') }}</div>
                </div>
            </div>
        </div>

        <!-- Booking Status -->
        <div class="px-6 py-4 bg-green-50 border-b border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">Booking Confirmed</p>
                    <p class="text-sm text-green-600">Payment received and seats reserved</p>
                </div>
            </div>
        </div>

        <!-- Trip Details -->
        <div class="px-6 py-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Trip Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Route</dt>
                        <dd class="text-lg font-semibold text-gray-900">
                            {{ $booking->schedule->route->sourceCity->name }} → {{ $booking->schedule->route->destinationCity->name }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Travel Date</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $booking->schedule->travel_date->format('l, F j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Departure Time</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $booking->schedule->departure_time }}</dd>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Bus</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $booking->schedule->bus->bus_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Seat Numbers</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ implode(', ', $booking->seat_numbers) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Passengers</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ $booking->passenger_count }}</dd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Passenger Details -->
        <div class="px-6 py-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Passenger Information</h3>
            @if($booking->passenger_details && count($booking->passenger_details) > 0)
                @foreach($booking->passenger_details as $index => $passenger)
                    <div class="bg-gray-50 rounded-lg p-4 {{ $index > 0 ? 'mt-4' : '' }}">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $passenger['name'] ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Age</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $passenger['age'] ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gender</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ ucfirst($passenger['gender'] ?? 'N/A') }}</dd>
                            </div>
                        </div>
                        @if(isset($passenger['phone']) || isset($passenger['email']))
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                @if(isset($passenger['phone']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                        <dd class="text-sm font-semibold text-gray-900">{{ $passenger['phone'] }}</dd>
                                    </div>
                                @endif
                                @if(isset($passenger['email']))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="text-sm font-semibold text-gray-900">{{ $passenger['email'] }}</dd>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        <!-- Contact Information -->
        <div class="px-6 py-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                    <dd class="text-sm font-semibold text-gray-900">{{ $booking->contact_phone }}</dd>
                </div>
                @if($booking->contact_email)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Contact Email</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $booking->contact_email }}</dd>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payment Details -->
        <div class="px-6 py-6 border-t border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                    <dd class="text-sm font-semibold text-gray-900">{{ ucfirst($booking->payment_method) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                    <dd class="text-sm font-semibold text-green-600">{{ ucfirst($booking->payment_status) }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                    <dd class="text-lg font-bold text-gray-900">Rs. {{ number_format($booking->total_amount, 2) }}</dd>
                </div>
            </div>
        </div>

        <!-- Special Requests -->
        @if($booking->special_requests)
            <div class="px-6 py-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Special Requests</h3>
                <p class="text-sm text-gray-700">{{ $booking->special_requests }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="text-center">
                <p class="text-sm text-gray-600">Thank you for choosing BookNGO!</p>
                <p class="text-xs text-gray-500 mt-1">Please arrive at the departure point 15 minutes before departure time.</p>
            </div>
        </div>
    </div>

    <!-- Ticket Printing Notice -->
    <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-ticket-alt text-green-600 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">
                    Booking Confirmed - Ready to Print Ticket
                </h3>
                <div class="mt-2 text-sm text-green-700">
                    <p>Your counter booking has been successfully created. Click "Print Compact Ticket" below to print the ticket for your passenger.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 flex justify-center space-x-4 flex-wrap gap-4">
        <!-- Print Compact Ticket Button -->
        <!-- <a href="{{ route('operator.bookings.ticket', $booking) }}" target="_blank" class="inline-flex items-center px-8 py-4 border border-transparent text-lg font-bold rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg transform hover:scale-105 transition-all duration-200">
            <i class="fas fa-print mr-3 text-xl"></i>
            Print Compact Ticket
        </a> -->

        <!-- Download PDF Ticket Button -->
        <!-- <a href="{{ route('operator.bookings.download-compact-ticket', $booking) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg">
            <i class="fas fa-download mr-2"></i>
            Download PDF Ticket
        </a> -->

        <a href="{{ route('operator.counter.search') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
            <i class="fas fa-plus mr-2"></i>
            Create Another Booking
        </a>
        <a href="{{ route('operator.bookings.show', $booking) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
            <i class="fas fa-eye mr-2"></i>
            View in Bookings
        </a>
    </div>
</div>

@push('styles')
<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
    }
    
    #receipt {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>
@endpush
@endsection
