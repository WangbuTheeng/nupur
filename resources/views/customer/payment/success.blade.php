@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Success Header -->
    <div class="bg-gradient-to-br from-green-600 via-green-700 to-emerald-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <!-- Success Icon -->
                <div class="w-24 h-24 mx-auto mb-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                
                <h1 class="text-4xl font-bold mb-4">Payment Successful!</h1>
                <p class="text-xl text-green-100 max-w-2xl mx-auto">
                    Your booking has been confirmed. You will receive a confirmation email shortly.
                </p>
                <p class="text-green-200 mt-2">
                    Booking Reference: <span class="font-semibold">{{ $booking->booking_reference }}</span>
                </p>

                @if(session('gateway'))
                    <div class="mt-4 inline-flex items-center px-4 py-2 bg-white bg-opacity-20 rounded-full">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                        <span class="text-sm font-medium">Paid via {{ session('gateway') }}</span>
                        @if(session('transaction_id'))
                            <span class="ml-2 text-xs text-green-100">ID: {{ session('transaction_id') }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Booking Confirmation Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Trip Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Trip Confirmation</h2>
                        <span class="bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-medium">
                            Confirmed
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Departure -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Departure</h3>
                                    <p class="text-gray-600">{{ $booking->schedule->route->sourceCity->name }}</p>
                                </div>
                            </div>
                            <div class="ml-15">
                                <p class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->schedule->departure_time)->format('g:i A') }}</p>
                                <p class="text-gray-600">{{ $booking->schedule->travel_date->format('l, M d, Y') }}</p>
                            </div>
                        </div>

                        <!-- Arrival -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Arrival</h3>
                                    <p class="text-gray-600">{{ $booking->schedule->route->destinationCity->name }}</p>
                                </div>
                            </div>
                            <div class="ml-15">
                                <p class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->schedule->arrival_time)->format('g:i A') }}</p>
                                <p class="text-gray-600">{{ $booking->schedule->travel_date->format('l, M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Journey Duration -->
                    <div class="mt-8 p-4 bg-blue-50 rounded-xl">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <span class="text-blue-800 font-medium">
                                Journey Duration: 
                                @php
                                    $departure = \Carbon\Carbon::parse($booking->schedule->departure_time);
                                    $arrival = \Carbon\Carbon::parse($booking->schedule->arrival_time);
                                    $duration = $departure->diff($arrival);
                                @endphp
                                {{ $duration->format('%h hours %i minutes') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Bus & Passenger Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Booking Details</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Bus Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900">Bus Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Bus Number:</span>
                                    <span class="font-semibold">{{ $booking->schedule->bus->bus_number }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Bus Type:</span>
                                    <span class="font-semibold">{{ $booking->schedule->bus->busType->name ?? 'Standard' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Operator:</span>
                                    <span class="font-semibold">{{ $booking->schedule->operator->company_name ?? $booking->schedule->operator->name }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Passenger Information -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-gray-900">Passenger Details</h3>
                            @php
                                $primaryPassenger = $booking->passenger_details[0] ?? null;
                            @endphp
                            @if($primaryPassenger)
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Primary Passenger:</span>
                                        <span class="font-semibold">{{ $primaryPassenger['name'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Age:</span>
                                        <span class="font-semibold">{{ $primaryPassenger['age'] }} years</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Gender:</span>
                                        <span class="font-semibold">{{ ucfirst($primaryPassenger['gender']) }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Seat Information -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Seat Assignment</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($booking->seat_numbers as $seatNumber)
                                <span class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-semibold">
                                    Seat {{ $seatNumber }}
                                </span>
                            @endforeach
                        </div>
                        @if(count($booking->seat_numbers) > 1)
                            <p class="text-sm text-gray-600 mt-2">
                                Group booking for {{ count($booking->seat_numbers) }} passengers
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Important Travel Information -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Important Travel Information</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Before Travel</h3>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Arrive 30 minutes before departure</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Carry a valid government ID</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Keep your ticket handy</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancellation Policy</h3>
                            <ul class="space-y-2 text-gray-700">
                                <li class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Free cancellation up to 2 hours before departure</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Refund processed within 3-5 business days</span>
                                </li>
                                <li class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>Contact support for assistance</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 sticky top-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Next Steps</h3>
                    
                    <!-- Payment Confirmation -->
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-green-900">Payment Confirmed</p>
                                <p class="text-sm text-green-700">NRs {{ number_format($booking->total_amount) }} paid successfully</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3 mb-6">
                        <a href="{{ route('customer.tickets.show', $booking) }}" 
                           class="w-full bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 font-semibold transition-colors text-center block">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Ticket
                        </a>
                        
                        <a href="{{ route('customer.bookings.show', $booking) }}" 
                           class="w-full bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 font-semibold transition-colors text-center block">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Booking Details
                        </a>
                        
                        <a href="{{ route('customer.bookings.index') }}" 
                           class="w-full bg-gray-100 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-200 font-semibold transition-colors text-center block">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            My Bookings
                        </a>
                        
                        <a href="{{ route('search.index') }}" 
                           class="w-full bg-gray-100 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-200 font-semibold transition-colors text-center block">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Book Another Trip
                        </a>
                    </div>

                    <!-- Contact Support -->
                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-3">Need Help?</h4>
                        <div class="text-sm text-gray-600 space-y-2">
                            <p class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span>Support: +977-1-4444444</span>
                            </p>
                            <p class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span>support@bookngo.com</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
