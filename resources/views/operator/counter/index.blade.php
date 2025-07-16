@extends('layouts.operator')

@section('title', 'Counter Booking')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-cash-register text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">Counter Booking</h1>
                        <p class="text-purple-100">Streamlined booking system for walk-in customers</p>
                        <div class="flex items-center text-purple-200 text-sm mt-2">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>{{ now()->format('l, F j, Y') }}</span>
                            <span class="mx-3">•</span>
                            <i class="fas fa-clock mr-2"></i>
                            <span id="current-time">{{ now()->format('g:i A') }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('operator.counter.search') }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Booking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Today's Schedules -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-blue-500 hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-route text-blue-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Schedules</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['today_schedules'] }}</dd>
                            <dd class="text-xs text-gray-400 mt-1">Active routes today</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Bookings -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-green-500 hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-green-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Bookings</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['today_bookings'] }}</dd>
                            <dd class="text-xs text-gray-400 mt-1">Passengers booked</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-indigo-500 hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-rupee-sign text-indigo-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Revenue</dt>
                            <dd class="text-2xl font-bold text-gray-900">NRs {{ number_format($stats['today_revenue'], 2) }}</dd>
                            <dd class="text-xs text-gray-400 mt-1">Total earnings</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Bookings -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-yellow-500 hover:shadow-xl transition-shadow duration-300">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Bookings</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['pending_bookings'] }}</dd>
                            <dd class="text-xs text-gray-400 mt-1">Awaiting confirmation</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow-lg rounded-xl mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-bolt text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                        <p class="text-sm text-gray-500">Frequently used operations</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    4 Actions
                </span>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Search & Book -->
                <a href="{{ route('operator.counter.search') }}" class="group bg-gradient-to-br from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-xl p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-blue-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-search text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Search & Book</h4>
                        <p class="text-sm text-gray-600 mb-3">Find available schedules and create bookings</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Quick
                        </span>
                    </div>
                </a>

                <!-- Today's Bookings -->
                <a href="{{ route('operator.bookings.today') }}" class="group bg-gradient-to-br from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-xl p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-green-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-calendar-day text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Today's Bookings</h4>
                        <p class="text-sm text-gray-600 mb-3">View and manage today's reservations</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ $stats['today_bookings'] }} Today
                        </span>
                    </div>
                </a>

                <!-- Manage Schedules -->
                <a href="{{ route('operator.schedules.index') }}" class="group bg-gradient-to-br from-indigo-50 to-indigo-100 hover:from-indigo-100 hover:to-indigo-200 rounded-xl p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-indigo-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-route text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Manage Schedules</h4>
                        <p class="text-sm text-gray-600 mb-3">Create and edit bus schedules</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            {{ $stats['today_schedules'] }} Active
                        </span>
                    </div>
                </a>

                <!-- Analytics -->
                <a href="{{ route('operator.reports.index') }}" class="group bg-gradient-to-br from-yellow-50 to-yellow-100 hover:from-yellow-100 hover:to-yellow-200 rounded-xl p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-yellow-500 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">Analytics</h4>
                        <p class="text-sm text-gray-600 mb-3">View detailed reports and insights</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Reports
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Today's Schedules -->
        <div class="bg-white shadow-lg rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-route text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Today's Schedules</h3>
                            <p class="text-sm text-gray-500">Available routes for booking</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $todaySchedules->count() }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                @if($todaySchedules->count() > 0)
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach($todaySchedules as $schedule)
                            <div class="bg-gray-50 hover:bg-gray-100 rounded-lg p-4 transition-colors duration-200 border border-gray-200">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                                            <h4 class="text-lg font-semibold text-gray-900">
                                                {{ $schedule->route->sourceCity->name ?? 'N/A' }}
                                                <i class="fas fa-arrow-right text-gray-400 mx-2"></i>
                                                {{ $schedule->route->destinationCity->name ?? 'N/A' }}
                                            </h4>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 mb-2">
                                            <div class="flex items-center">
                                                <i class="fas fa-bus text-indigo-500 mr-2"></i>
                                                {{ $schedule->bus->bus_number ?? 'N/A' }}
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-clock text-yellow-500 mr-2"></i>
                                                {{ $schedule->departure_time }}
                                            </div>
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <i class="fas fa-users text-green-500 mr-2"></i>
                                            {{ $schedule->available_seats }} seats available
                                        </div>
                                    </div>
                                    <div class="text-right ml-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mb-2
                                            @if($schedule->status === 'scheduled') bg-green-100 text-green-800
                                            @elseif($schedule->status === 'completed') bg-gray-100 text-gray-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                        <div class="text-xl font-bold text-blue-600 mb-2">
                                            Rs. {{ number_format($schedule->fare, 2) }}
                                        </div>
                                        @if($schedule->available_seats > 0)
                                            <a href="{{ route('operator.counter.book', $schedule) }}"
                                               class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-ticket-alt mr-1"></i>Book Now
                                            </a>
                                        @else
                                            <button class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-gray-500 bg-gray-200 cursor-not-allowed" disabled>
                                                <i class="fas fa-times mr-1"></i>Full
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No schedules today</h3>
                        <p class="text-gray-500 mb-4">Create schedules to start accepting bookings.</p>
                        <a href="{{ route('operator.schedules.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-2"></i>Create Schedule
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Counter Bookings -->
        <div class="bg-white shadow-lg rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-ticket-alt text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                            <p class="text-sm text-gray-500">Latest counter reservations</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ $recentBookings->count() }}
                    </span>
                </div>
            </div>
            <div class="p-6">
                @if($recentBookings->count() > 0)
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        @foreach($recentBookings as $booking)
                            <div class="bg-gray-50 hover:bg-gray-100 rounded-lg p-4 transition-colors duration-200 border border-gray-200">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-receipt text-green-500 mr-2"></i>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $booking->booking_reference }}</h4>
                                        </div>
                                        <div class="space-y-1 text-sm text-gray-600">
                                            <div class="flex items-center">
                                                <i class="fas fa-user text-blue-500 mr-2 w-4"></i>
                                                {{ $booking->passenger_details[0]['name'] ?? 'N/A' }}
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-route text-purple-500 mr-2 w-4"></i>
                                                {{ $booking->schedule->route->sourceCity->name ?? 'N/A' }}
                                                <i class="fas fa-arrow-right text-gray-400 mx-2"></i>
                                                {{ $booking->schedule->route->destinationCity->name ?? 'N/A' }}
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-clock text-yellow-500 mr-2 w-4"></i>
                                                {{ $booking->created_at->format('M j, Y g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right ml-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mb-2
                                            @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                        <div class="text-xl font-bold text-green-600 mb-2">
                                            Rs. {{ number_format($booking->total_amount, 2) }}
                                        </div>
                                        <a href="{{ route('operator.counter.receipt', $booking) }}"
                                           class="inline-flex items-center px-3 py-1.5 border border-blue-300 text-xs font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-6 pt-4 border-t border-gray-200">
                            <a href="{{ route('operator.bookings.index') }}" class="inline-flex items-center px-4 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-list mr-2"></i>View All Bookings
                            </a>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-ticket-alt text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No bookings yet</h3>
                            <p class="text-gray-500 mb-4">Start by creating your first counter booking.</p>
                            <a href="{{ route('operator.counter.search') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-plus mr-2"></i>Create Booking
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Real-time clock update
    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeString;
        }
    }

    // Update time every second
    setInterval(updateTime, 1000);

    // Add loading states to action cards
    document.querySelectorAll('a[href*="counter"], a[href*="bookings"], a[href*="schedules"], a[href*="reports"]').forEach(card => {
        card.addEventListener('click', function(e) {
            const icon = this.querySelector('i');
            if (icon && !icon.classList.contains('fa-arrow-right')) {
                const originalClass = icon.className;
                icon.className = 'fas fa-spinner fa-spin text-white';

                setTimeout(() => {
                    icon.className = originalClass;
                }, 800);
            }
        });
    });

    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';

    // Animate cards on page load
    window.addEventListener('load', function() {
        // Animate statistics cards
        const statsCards = document.querySelectorAll('.bg-white.shadow-lg');
        statsCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';

            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });

    // Add hover effects for better interactivity
    document.querySelectorAll('.hover\\:shadow-xl').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
</script>
@endpush
