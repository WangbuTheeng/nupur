@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Booking Details</h1>
        <p class="text-gray-600">Reference: {{ $booking->booking_reference }}</p>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">{{ $booking->schedule->route->full_name }}</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($booking->status === 'confirmed') bg-green-100 text-green-800
                    @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800 @endif">
                    {{ ucfirst($booking->status) }}
                </span>
            </div>
        </div>

        <div class="p-6">
            <!-- Trip Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Trip Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Bus</dt>
                            <dd class="text-sm text-gray-900">{{ $booking->schedule->bus->display_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Bus Type</dt>
                            <dd class="text-sm text-gray-900">{{ $booking->schedule->bus->busType->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Travel Date</dt>
                            <dd class="text-sm text-gray-900">{{ $booking->schedule->travel_date->format('l, M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Departure Time</dt>
                            <dd class="text-sm text-gray-900">{{ $booking->schedule->departure_time->format('h:i A') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Arrival Time</dt>
                            <dd class="text-sm text-gray-900">{{ $booking->schedule->arrival_time->format('h:i A') }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Information</h3>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Seats</dt>
                            <dd class="text-sm text-gray-900">{{ $booking->seat_numbers_string }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Passengers</dt>
                            <dd class="text-sm text-gray-900">{{ $booking->passenger_count }} passenger(s)</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                            <dd class="text-lg font-semibold text-gray-900">NPR {{ number_format($booking->total_amount) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Booked On</dt>
                            <dd class="text-sm text-gray-900">{{ $booking->created_at->format('M d, Y h:i A') }}</dd>
                        </div>
                        @if($booking->booking_expires_at && $booking->status === 'pending')
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Payment Deadline</dt>
                                <dd class="text-sm text-gray-900">{{ $booking->booking_expires_at->format('M d, Y h:i A') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Passenger Details -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Passenger Details</h3>
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

            <!-- Contact Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="text-sm text-gray-900">{{ $booking->contact_phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="text-sm text-gray-900">{{ $booking->contact_email }}</dd>
                    </div>
                </dl>
                @if($booking->special_requests)
                    <div class="mt-4">
                        <dt class="text-sm font-medium text-gray-500">Special Requests</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $booking->special_requests }}</dd>
                    </div>
                @endif
            </div>

            <!-- Payment Information -->
            @if($booking->payments->count() > 0)
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                    @foreach($booking->payments as $payment)
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        Payment Method: {{ ucfirst($payment->payment_method) }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Amount: NPR {{ number_format($payment->amount) }}
                                    </p>
                                    @if($payment->transaction_id)
                                        <p class="text-sm text-gray-600">
                                            Transaction ID: {{ $payment->transaction_id }}
                                        </p>
                                    @endif
                                    @if($payment->paid_at)
                                        <p class="text-sm text-gray-600">
                                            Paid on: {{ $payment->paid_at->format('M d, Y h:i A') }}
                                        </p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                    @if($payment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($payment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($payment->status === 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Actions -->
            <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
                @if($booking->status === 'pending' && !$booking->isExpired())
                    <a href="{{ route('bookings.payment', $booking) }}" 
                       class="bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                        Complete Payment
                    </a>
                @endif

                @if($booking->status === 'confirmed')
                    <a href="{{ route('tickets.view', $booking) }}"
                       class="bg-purple-600 text-white hover:bg-purple-700 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                        View Ticket
                    </a>
                    <a href="{{ route('tickets.download', $booking) }}"
                       class="bg-indigo-600 text-white hover:bg-indigo-700 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                        Download Ticket
                    </a>
                @endif

                @if(in_array($booking->status, ['pending', 'confirmed']))
                    <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                        @csrf
                        <button type="submit" 
                                class="bg-red-600 text-white hover:bg-red-700 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                            Cancel Booking
                        </button>
                    </form>
                @endif

                <a href="{{ route('bookings.index') }}" 
                   class="bg-gray-600 text-white hover:bg-gray-700 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                    Back to Bookings
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
