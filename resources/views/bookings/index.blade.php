@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Bookings</h1>
        <p class="text-gray-600">View and manage your bus ticket bookings</p>
    </div>

    @if($bookings->count() > 0)
        <div class="space-y-6">
            @foreach($bookings as $booking)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $booking->schedule->route->full_name }}</h3>
                                <p class="text-sm text-gray-600">Booking Reference: {{ $booking->booking_reference }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Bus</p>
                                <p class="font-medium">{{ $booking->schedule->bus->display_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Travel Date</p>
                                <p class="font-medium">{{ $booking->schedule->travel_date->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Departure Time</p>
                                <p class="font-medium">{{ $booking->schedule->departure_time->format('h:i A') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Seats</p>
                                <p class="font-medium">{{ $booking->seat_numbers_string }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Passengers</p>
                                <p class="font-medium">{{ $booking->passenger_count }} passenger(s)</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Total Amount</p>
                                <p class="font-medium text-lg">NPR {{ number_format($booking->total_amount) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Booked On</p>
                                <p class="font-medium">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        <!-- Payment Status -->
                        @if($booking->payments->count() > 0)
                            <div class="mb-4">
                                <p class="text-sm text-gray-500">Payment Status</p>
                                @php $latestPayment = $booking->payments->last(); @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                    @if($latestPayment->status === 'completed') bg-green-100 text-green-800
                                    @elseif($latestPayment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($latestPayment->status === 'failed') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($latestPayment->status) }}
                                </span>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex flex-wrap gap-2 pt-4 border-t border-gray-200">
                            <a href="{{ route('bookings.show', $booking) }}" 
                               class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                View Details
                            </a>

                            @if($booking->status === 'pending')
                                @if($booking->isExpired())
                                    <span class="bg-gray-400 text-white px-4 py-2 rounded-md text-sm font-medium">
                                        Expired
                                    </span>
                                @else
                                    <a href="{{ route('bookings.payment', $booking) }}" 
                                       class="bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded-md text-sm font-medium transition duration-200">
                                        Complete Payment
                                    </a>
                                @endif
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
                        </div>

                        <!-- Expiry Warning -->
                        @if($booking->status === 'pending' && !$booking->isExpired())
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-800">
                                            <strong>Payment Required:</strong> Complete payment by {{ $booking->booking_expires_at->format('M d, Y h:i A') }} to confirm your booking.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $bookings->links() }}
        </div>
    @else
        <div class="bg-white shadow rounded-lg p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No bookings yet</h3>
            <p class="mt-1 text-sm text-gray-500">Start your journey by booking your first bus ticket.</p>
            <div class="mt-6">
                <a href="{{ route('search') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Search Buses
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
