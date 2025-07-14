@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-purple-600 via-purple-700 to-indigo-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Payment History</h1>
                <p class="text-xl text-purple-100 max-w-2xl mx-auto">
                    Track all your payment transactions and booking confirmations
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
        <!-- Payment History -->
        @if($payments->count() > 0)
            <div class="space-y-6">
                @foreach($payments as $payment)
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <div class="p-8">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                <!-- Payment Info -->
                                <div class="flex-1 mb-6 lg:mb-0">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                                {{ $payment->schedule->route->full_name }}
                                            </h3>
                                            <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                <span class="bg-gray-100 px-3 py-1 rounded-lg font-medium">
                                                    {{ $payment->booking_reference }}
                                                </span>
                                                <span>{{ $payment->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Payment Confirmed
                                        </span>
                                    </div>

                                    <!-- Payment Details Grid -->
                                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Travel Date</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $payment->schedule->travel_date->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Payment Date</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $payment->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Seats</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ implode(', ', $payment->seat_numbers) }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-xs text-gray-500 font-medium">Payment Method</p>
                                                <p class="text-sm font-semibold text-gray-900">{{ $payment->payment_method ?? 'Online Payment' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Amount and Actions -->
                                <div class="flex flex-col lg:items-end lg:text-right">
                                    <p class="text-3xl font-bold text-gray-900 mb-6">Rs. {{ number_format($payment->total_amount) }}</p>
                                    <div class="flex flex-col sm:flex-row lg:flex-col space-y-3 sm:space-y-0 sm:space-x-3 lg:space-x-0 lg:space-y-3">
                                        <a href="{{ route('customer.bookings.show', $payment) }}" 
                                           class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 text-sm font-semibold transition-colors text-center">
                                            View Booking
                                        </a>
                                        <a href="{{ route('customer.tickets.show', $payment) }}" 
                                           class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 text-sm font-semibold transition-colors text-center">
                                            Download Ticket
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($payments->hasPages())
                <div class="mt-12">
                    {{ $payments->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-16 text-center">
                <div class="w-24 h-24 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center mx-auto mb-8">
                    <svg class="h-12 w-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">No payment history</h3>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">
                    You haven't made any payments yet. Start booking your trips to see your payment history here.
                </p>
                <a href="{{ route('search.index') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Book Your First Trip
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
