@extends('layouts.admin')

@section('title', 'Booking Details - ' . $booking->booking_reference)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-2xl text-white p-8 mb-8 shadow-2xl">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
            <div class="flex-1">
                <h1 class="text-3xl font-bold mb-4">Booking Details</h1>
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-ticket-alt text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">{{ $booking->booking_reference }}</h2>
                        <p class="text-blue-100">{{ $booking->created_at->format('M d, Y \a\t H:i A') }}</p>
                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('admin.bookings.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-xl font-semibold transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Bookings
                </a>
                @if($booking->status == 'confirmed')
                    <a href="{{ route('admin.bookings.edit', $booking) }}"
                       class="inline-flex items-center px-6 py-3 bg-yellow-500 hover:bg-yellow-400 text-yellow-900 rounded-xl font-semibold transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <i class="fas fa-edit mr-2"></i>Edit Booking
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Booking Status Card -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Booking Status</h3>
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                        @if($booking->status === 'confirmed') bg-green-100 text-green-800
                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        <i class="fas fa-circle mr-2 text-xs"></i>
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Booking Reference</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->booking_reference }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Payment Status</label>
                            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($booking->payment_status ?? 'Pending') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Payment Method</label>
                            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($booking->payment_method ?? 'Not specified') }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Booking Date</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Total Amount</label>
                            <p class="text-2xl font-bold text-green-600">Rs. {{ number_format($booking->total_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Customer Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Customer Name</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->user->name ?? 'Guest Customer' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email</label>
                            <p class="text-lg text-gray-900">{{ $booking->user->email ?? $booking->contact_email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Phone</label>
                            <p class="text-lg text-gray-900">{{ $booking->user->phone ?? $booking->contact_phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Customer Type</label>
                            <p class="text-lg text-gray-900">{{ $booking->user ? 'Registered User' : 'Guest' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Travel Information -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Travel Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Route</label>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $booking->schedule->route->sourceCity->name }} → {{ $booking->schedule->route->destinationCity->name }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Travel Date</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->schedule->travel_date->format('l, M d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Departure Time</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->schedule->departure_time->format('h:i A') }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Bus</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->schedule->bus->bus_number }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Operator</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $booking->schedule->operator->company_name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Seat Numbers</label>
                            <p class="text-lg font-semibold text-gray-900">
                                @if(is_array($booking->seat_numbers))
                                    {{ implode(', ', $booking->seat_numbers) }}
                                @else
                                    {{ $booking->seat_numbers ?? 'N/A' }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Passenger Details -->
            @if($booking->passenger_details && is_array($booking->passenger_details))
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Passenger Details</h3>
                <div class="space-y-4">
                    @foreach($booking->passenger_details as $index => $passenger)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Passenger {{ $index + 1 }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Name</label>
                                <p class="text-sm text-gray-900">{{ $passenger['name'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Age</label>
                                <p class="text-sm text-gray-900">{{ $passenger['age'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Gender</label>
                                <p class="text-sm text-gray-900">{{ ucfirst($passenger['gender'] ?? 'N/A') }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-8">
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h3>
                <div class="space-y-3">
                    @if($booking->status === 'pending')
                        <form method="POST" action="{{ route('admin.bookings.confirm', $booking) }}" class="w-full">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex items-center justify-center"
                                    onclick="return confirm('Are you sure you want to confirm this booking?')">
                                <i class="fas fa-check mr-2"></i>Confirm Booking
                            </button>
                        </form>
                    @endif

                    @if($booking->status !== 'cancelled')
                        <form method="POST" action="{{ route('admin.bookings.cancel', $booking) }}" class="w-full">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex items-center justify-center"
                                    onclick="return confirm('Are you sure you want to cancel this booking?')">
                                <i class="fas fa-times mr-2"></i>Cancel Booking
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.bookings.edit', $booking) }}"
                       class="w-full border-2 border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>Edit Booking
                    </a>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="bg-white rounded-2xl shadow-xl p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Booking Summary</h3>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Passengers:</span>
                        <span class="font-semibold">{{ $booking->passenger_count ?? count($booking->seat_numbers ?? []) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Seats:</span>
                        <span class="font-semibold">{{ count($booking->seat_numbers ?? []) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Fare per seat:</span>
                        <span class="font-semibold">Rs. {{ number_format($booking->schedule->fare, 2) }}</span>
                    </div>
                    <hr class="border-gray-200">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total Amount:</span>
                        <span class="text-green-600">Rs. {{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-gray-50 rounded-2xl shadow-xl p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">System Information</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Booking ID:</span>
                        <span class="font-mono">{{ $booking->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Schedule ID:</span>
                        <span class="font-mono">{{ $booking->schedule_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">User ID:</span>
                        <span class="font-mono">{{ $booking->user_id ?? 'Guest' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created:</span>
                        <span>{{ $booking->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Updated:</span>
                        <span>{{ $booking->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
