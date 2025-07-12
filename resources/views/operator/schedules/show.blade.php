@extends('layouts.operator')

@section('title', 'Schedule Details')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Schedule Details</h1>
                    <p class="text-indigo-100">{{ $schedule->route->name ?? 'N/A' }} - {{ $schedule->travel_date ?? 'N/A' }}</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('operator.schedules.edit', $schedule) }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Schedule
                    </a>
                    <a href="{{ route('operator.schedules.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Schedules
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Schedule Information -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Schedule Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Route</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->route->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $schedule->route->sourceCity->name ?? '' }} → {{ $schedule->route->destinationCity->name ?? '' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Bus</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->bus->bus_number ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $schedule->bus->model ?? '' }} ({{ $schedule->bus->total_seats ?? 0 }} seats)</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Travel Date</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->travel_date ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                {{ ($schedule->status ?? 'scheduled') === 'scheduled' ? 'bg-blue-100 text-blue-800' : (($schedule->status ?? '') === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($schedule->status ?? 'scheduled') }}
                            </span>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Departure Time</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->departure_time ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Arrival Time</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->arrival_time ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Fare</h4>
                            <p class="mt-1 text-sm text-gray-900">Rs. {{ number_format($schedule->fare ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Available Seats</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->available_seats ?? 0 }}</p>
                        </div>
                    </div>

                    @if($schedule->notes ?? false)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500">Notes</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $schedule->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Bookings -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Bookings ({{ $bookings->count() ?? 0 }})</h3>
                </div>
                <div class="p-6">
                    @if(($bookings ?? collect())->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booked At</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($bookings ?? [] as $booking)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $booking->user->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $booking->user->email ?? 'N/A' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->user->phone ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->passenger_count ?? 0 }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rs. {{ number_format($booking->total_amount ?? 0, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                    {{ ($booking->status ?? 'pending') === 'confirmed' ? 'bg-green-100 text-green-800' : (($booking->status ?? '') === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    {{ ucfirst($booking->status ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->created_at->format('M j, Y H:i') ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Bookings Yet</h3>
                            <p class="mt-1 text-sm text-gray-500">No bookings have been made for this schedule.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics & Actions -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Statistics -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Statistics</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $stats['total_bookings'] ?? 0 }}</div>
                            <p class="text-xs text-gray-600">Total Bookings</p>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ $stats['confirmed_bookings'] ?? 0 }}</div>
                            <p class="text-xs text-gray-600">Confirmed</p>
                        </div>
                        <div class="pt-4 border-t border-gray-200">
                            <div class="text-2xl font-bold text-yellow-600">{{ $stats['seats_booked'] ?? 0 }}</div>
                            <p class="text-xs text-gray-600">Seats Booked</p>
                        </div>
                        <div class="pt-4 border-t border-gray-200">
                            <div class="text-2xl font-bold text-purple-600">Rs. {{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                            <p class="text-xs text-gray-600">Revenue</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('operator.schedules.edit', $schedule) }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Schedule
                    </a>
                    
                    @if(($schedule->status ?? 'scheduled') === 'scheduled')
                        <form method="POST" action="{{ route('operator.schedules.update-status', $schedule) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="completed">
                            <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fas fa-check mr-2"></i>
                                Mark as Completed
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('operator.schedules.create') }}" class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i>
                        Create New Schedule
                    </a>
                </div>
            </div>

            <!-- Route Information -->
            @if($schedule->route ?? false)
                <div class="bg-white overflow-hidden shadow-lg rounded-xl">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Route Details</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Distance</h4>
                                <p class="text-sm text-gray-900">{{ number_format($schedule->route->distance_km ?? 0, 1) }} km</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Estimated Duration</h4>
                                <p class="text-sm text-gray-900">{{ $schedule->route->estimated_duration ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Base Fare</h4>
                                <p class="text-sm text-gray-900">Rs. {{ number_format($schedule->route->base_fare ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
