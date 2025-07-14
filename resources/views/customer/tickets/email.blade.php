@extends('layouts.app')

@section('title', 'Email Ticket')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Email E-Ticket</h1>
                    <p class="mt-2 text-gray-600">Send your ticket to any email address</p>
                </div>
                <a href="{{ route('tickets.show', $booking) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Ticket
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Email Form -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Send E-Ticket</h2>

                @if(session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                        <div class="flex">
                            <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif

                <form action="{{ route('tickets.send-email', $booking) }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address *
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $booking->user->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @enderror"
                               placeholder="Enter email address"
                               required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Optional Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Additional Message (Optional)
                        </label>
                        <textarea id="message" 
                                  name="message" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('message') border-red-300 @enderror"
                                  placeholder="Add a personal message (optional)">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Maximum 500 characters</p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            PDF ticket will be attached
                        </div>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Send E-Ticket
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ticket Preview -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Ticket Preview</h2>
                
                <div class="space-y-4">
                    <!-- Booking Reference -->
                    <div class="text-center p-4 bg-gray-50 rounded-lg">
                        <div class="text-sm text-gray-500 mb-1">Booking Reference</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $booking->booking_reference }}</div>
                    </div>

                    <!-- Journey Details -->
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-gray-500">From</div>
                            <div class="font-medium">{{ $booking->schedule->route->sourceCity->name }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">To</div>
                            <div class="font-medium">{{ $booking->schedule->route->destinationCity->name }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Date</div>
                            <div class="font-medium">{{ $booking->schedule->travel_date->format('M d, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Time</div>
                            <div class="font-medium">{{ $booking->schedule->departure_time->format('h:i A') }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Passengers</div>
                            <div class="font-medium">{{ $booking->passenger_count }}</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Seats</div>
                            <div class="font-medium">{{ implode(', ', $booking->seat_numbers) }}</div>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500">Total Amount</span>
                            <span class="text-xl font-bold text-gray-900">NPR {{ number_format($booking->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
