@extends('layouts.operator')

@section('title', 'Booking Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Enhanced Header -->
    <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-purple-700 rounded-2xl text-white p-8 mb-8 shadow-2xl">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-4">Booking Details</h1>
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-ticket-alt text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">{{ $booking->booking_reference }}</h2>
                        <p class="text-indigo-100">{{ $booking->created_at->format('M d, Y \a\t H:i A') }}</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('operator.bookings.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl font-semibold transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Bookings
                </a>
                @if($booking->status == 'confirmed')
                    <a href="{{ route('operator.bookings.ticket', $booking) }}"
                       class="inline-flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-yellow-900 rounded-xl font-semibold transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                       target="_blank">
                        <i class="fas fa-ticket-alt mr-2"></i>View Ticket
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Booking Information -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-4 border-blue-500 px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-500 text-white rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Booking Information</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-hashtag text-blue-500 mr-2"></i>Booking Reference
                                </span>
                                <span class="font-bold text-gray-900">{{ $booking->booking_reference }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-flag text-blue-500 mr-2"></i>Status
                                </span>
                                <span class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wide
                                    @if($booking->status == 'confirmed') bg-green-100 text-green-800
                                    @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-tag text-blue-500 mr-2"></i>Booking Type
                                </span>
                                <span class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wide flex items-center
                                    {{ $booking->booking_type == 'counter' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    <i class="fas fa-{{ $booking->booking_type == 'counter' ? 'store' : 'globe' }} mr-1"></i>
                                    {{ ucfirst($booking->booking_type) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>Total Amount
                                </span>
                                <span class="font-bold text-green-600 text-lg">Rs. {{ number_format($booking->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-users text-blue-500 mr-2"></i>Passenger Count
                                </span>
                                <span class="text-gray-900 font-medium">{{ $booking->passenger_count }} {{ $booking->passenger_count == 1 ? 'Person' : 'People' }}</span>
                            </div>
                            <div class="flex justify-between items-start py-3">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-chair text-blue-500 mr-2"></i>Seat Numbers
                                </span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($booking->seat_numbers as $seat)
                                        <span class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">{{ $seat }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-calendar-plus text-blue-500 mr-2"></i>Booked On
                                </span>
                                <span class="text-gray-900 font-medium">{{ $booking->created_at->format('M d, Y H:i A') }}</span>
                            </div>
                            @if($booking->booking_type == 'counter' && $booking->bookedBy)
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-user-tie text-blue-500 mr-2"></i>Booked By
                                </span>
                                <span class="text-gray-900 font-medium">{{ $booking->bookedBy->name }}</span>
                            </div>
                            @endif
                            @if($booking->payment_method)
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-credit-card text-blue-500 mr-2"></i>Payment Method
                                </span>
                                <span class="flex items-center text-gray-900 font-medium">
                                    <i class="fas fa-{{ $booking->payment_method == 'cash' ? 'money-bill' : ($booking->payment_method == 'card' ? 'credit-card' : 'mobile-alt') }} mr-1"></i>
                                    {{ ucfirst($booking->payment_method) }}
                                </span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-receipt text-blue-500 mr-2"></i>Payment Status
                                </span>
                                <span class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-wide
                                    @if($booking->payment_status == 'paid') bg-green-100 text-green-800
                                    @elseif($booking->payment_status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($booking->payment_status == 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($booking->payment_status) }}
                                </span>
                            </div>
                            @if($booking->special_requests)
                            <div class="flex justify-between items-start py-3">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-comment-alt text-blue-500 mr-2"></i>Special Requests
                                </span>
                                <span class="text-gray-900 font-medium text-right max-w-xs">{{ $booking->special_requests }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Passenger Information -->
            <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-4 border-cyan-500 px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-cyan-500 text-white rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Passenger Information</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="bg-gradient-to-br from-gray-50 to-white border-2 border-gray-200 hover:border-blue-300 rounded-xl p-5 transition-all duration-300 hover:shadow-lg">
                                <h4 class="text-blue-600 font-bold mb-4 flex items-center">
                                    <i class="fas fa-user-circle mr-2"></i>Primary Contact
                                </h4>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span class="flex items-center text-gray-600 font-semibold text-sm">
                                            <i class="fas fa-user text-blue-500 mr-2"></i>Name
                                        </span>
                                        <span class="text-gray-900 font-medium">{{ $booking->user->name }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                        <span class="flex items-center text-gray-600 font-semibold text-sm">
                                            <i class="fas fa-envelope text-blue-500 mr-2"></i>Email
                                        </span>
                                        <span class="text-gray-900 font-medium">{{ $booking->contact_email }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2">
                                        <span class="flex items-center text-gray-600 font-semibold text-sm">
                                            <i class="fas fa-phone text-blue-500 mr-2"></i>Phone
                                        </span>
                                        <span class="text-gray-900 font-medium">{{ $booking->contact_phone }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-blue-600 font-bold mb-4 flex items-center">
                                <i class="fas fa-id-card mr-2"></i>Passenger Details
                            </h4>
                            @if($booking->passenger_details)
                                <div class="space-y-3">
                                    @foreach($booking->passenger_details as $index => $passenger)
                                        <div class="bg-gradient-to-br from-gray-50 to-white border-2 border-gray-200 hover:border-blue-300 rounded-xl p-4 transition-all duration-300 hover:shadow-lg">
                                            <div class="flex items-center mb-3">
                                                <div class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center mr-3 text-sm font-bold">
                                                    {{ $index + 1 }}
                                                </div>
                                                <h5 class="font-bold text-gray-900">{{ $passenger['name'] ?? 'N/A' }}</h5>
                                            </div>
                                            <div class="ml-11 space-y-1">
                                                @if(isset($passenger['age']))
                                                    <div class="text-sm text-gray-600 flex items-center">
                                                        <i class="fas fa-birthday-cake mr-2 text-pink-500"></i>Age: {{ $passenger['age'] }} years
                                                    </div>
                                                @endif
                                                @if(isset($passenger['gender']))
                                                    <div class="text-sm text-gray-600 flex items-center">
                                                        <i class="fas fa-{{ $passenger['gender'] == 'male' ? 'mars' : ($passenger['gender'] == 'female' ? 'venus' : 'genderless') }} mr-2 text-{{ $passenger['gender'] == 'male' ? 'blue' : ($passenger['gender'] == 'female' ? 'pink' : 'gray') }}-500"></i>{{ ucfirst($passenger['gender']) }}
                                                    </div>
                                                @endif
                                                @if(isset($passenger['phone']))
                                                    <div class="text-sm text-gray-600 flex items-center">
                                                        <i class="fas fa-phone mr-2 text-green-500"></i>{{ $passenger['phone'] }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-user-slash text-gray-400 text-4xl mb-4"></i>
                                    <p class="text-gray-500">No passenger details available</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Information -->
            <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-4 border-green-500 px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-route"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Schedule Information</h3>
                    </div>
                </div>
                <div class="p-6">
                    <!-- Route Display -->
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white p-6 rounded-xl text-center font-bold text-lg mb-6 shadow-lg">
                        <div class="flex items-center justify-center space-x-6">
                            <div class="text-center">
                                <i class="fas fa-map-marker-alt text-2xl mb-2 block"></i>
                                <div class="font-bold">{{ $booking->schedule->route->sourceCity->name }}</div>
                            </div>
                            <div class="text-3xl">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                            <div class="text-center">
                                <i class="fas fa-map-marker-alt text-2xl mb-2 block"></i>
                                <div class="font-bold">{{ $booking->schedule->route->destinationCity->name }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-calendar text-blue-500 mr-2"></i>Travel Date
                                </span>
                                <span class="font-bold text-gray-900">{{ $booking->schedule->travel_date->format('l, M d, Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-clock text-green-500 mr-2"></i>Departure Time
                                </span>
                                <span class="font-bold text-green-600">{{ $booking->schedule->departure_time }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-clock text-orange-500 mr-2"></i>Arrival Time
                                </span>
                                <span class="font-bold text-orange-600">{{ $booking->schedule->arrival_time }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-road text-blue-500 mr-2"></i>Distance
                                </span>
                                <span class="text-gray-900 font-medium">{{ $booking->schedule->route->distance_km }} km</span>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-bus text-blue-500 mr-2"></i>Bus Number
                                </span>
                                <span class="font-bold text-gray-900">{{ $booking->schedule->bus->bus_number }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-cogs text-blue-500 mr-2"></i>Bus Type
                                </span>
                                <span class="text-gray-900 font-medium">{{ $booking->schedule->bus->busType->name }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-money-bill text-green-500 mr-2"></i>Fare per Seat
                                </span>
                                <span class="font-bold text-green-600">Rs. {{ number_format($booking->schedule->fare, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-3">
                                <span class="flex items-center text-gray-600 font-semibold text-sm">
                                    <i class="fas fa-chair text-blue-500 mr-2"></i>Total Seats
                                </span>
                                <span class="text-gray-900 font-medium">{{ $booking->schedule->bus->total_seats }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-4 border-yellow-500 px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-yellow-500 text-white rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Actions</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @if($booking->status == 'pending')
                            <button type="button"
                                    class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex items-center justify-center"
                                    onclick="confirmBooking()">
                                <i class="fas fa-check mr-2"></i>Confirm Booking
                            </button>
                        @endif

                        @if(in_array($booking->status, ['pending', 'confirmed']))
                            <button type="button"
                                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex items-center justify-center"
                                    onclick="cancelBooking()">
                                <i class="fas fa-times mr-2"></i>Cancel Booking
                            </button>
                        @endif

                        @if($booking->status == 'confirmed')
                            <a href="{{ route('operator.bookings.ticket', $booking) }}"
                               class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex items-center justify-center"
                               target="_blank">
                                <i class="fas fa-ticket-alt mr-2"></i>View Ticket
                            </a>
                        @endif

                        <a href="{{ route('operator.bookings.edit', $booking) }}"
                           class="w-full border-2 border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>Edit Booking
                        </a>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <h4 class="text-blue-600 font-bold mb-4 flex items-center">
                        <i class="fas fa-credit-card mr-2"></i>Payment Information
                    </h4>
                    @if($booking->payments->count() > 0)
                        <div class="space-y-3">
                            @foreach($booking->payments as $payment)
                                <div class="bg-gradient-to-br from-gray-50 to-white border-2 border-gray-200 hover:border-blue-300 rounded-xl p-4 transition-all duration-300 hover:shadow-lg">
                                    <div class="flex items-center mb-3">
                                        <div class="w-8 h-8 {{ $payment->status == 'completed' ? 'bg-green-500' : 'bg-yellow-500' }} text-white rounded-full flex items-center justify-center mr-3 text-sm">
                                            <i class="fas fa-{{ $payment->payment_method == 'cash' ? 'money-bill' : ($payment->payment_method == 'card' ? 'credit-card' : 'mobile-alt') }}"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-bold text-gray-900">{{ ucfirst($payment->payment_method) }}</div>
                                            <div class="text-sm text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }}</div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-green-600">Rs. {{ number_format($payment->amount, 2) }}</span>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide
                                            {{ $payment->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-receipt text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">No payment records found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Booking Modal -->
<div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Confirm Booking</h3>
                <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeConfirmModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 mb-4">Are you sure you want to confirm this booking?</p>
                <p class="text-sm"><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
            </div>
            <div class="flex items-center justify-end px-4 py-3 space-x-3">
                <button type="button"
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-lg shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
                        onclick="closeConfirmModal()">
                    Cancel
                </button>
                <form method="POST" action="{{ route('operator.bookings.confirm', $booking) }}" class="inline">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-lg shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        Confirm Booking
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Booking Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
        <form method="POST" action="{{ route('operator.bookings.cancel', $booking) }}">
            @csrf
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Cancel Booking</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeCancelModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-4">Are you sure you want to cancel this booking?</p>
                    <p class="text-sm mb-4"><strong>Booking Reference:</strong> {{ $booking->booking_reference }}</p>
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason</label>
                        <textarea name="reason"
                                  id="reason"
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Enter reason for cancellation..."
                                  required></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end px-4 py-3 space-x-3">
                    <button type="button"
                            class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-lg shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
                            onclick="closeCancelModal()">
                        Close
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-lg shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Cancel Booking
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function confirmBooking() {
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
}

function cancelBooking() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

// Close modals when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});
</script>
@endpush
@endsection
