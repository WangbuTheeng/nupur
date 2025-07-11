@extends('layouts.admin')

@section('title', 'Real-time Admin Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Real-time Admin Dashboard</h1>
                    <p class="text-gray-600 mt-1">Welcome back! Here's what's happening with BookNGo in real-time.</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span>{{ now()->format('l, F j, Y') }}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-clock mr-2"></i>
                        <span>Last updated: <span id="last-updated">{{ now()->format('H:i:s') }}</span></span>
                    </div>
                    <button onclick="refreshStats()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200 flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- System Alerts -->
    @if(count($alerts) > 0)
        <div class="mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">System Alerts</h2>
            <div class="space-y-3">
                @foreach($alerts as $alert)
                    <div class="flex items-center justify-between p-4 rounded-lg border-l-4 
                        @if($alert['type'] === 'warning') bg-yellow-50 border-yellow-400
                        @elseif($alert['type'] === 'error') bg-red-50 border-red-400
                        @else bg-blue-50 border-blue-400 @endif">
                        <div class="flex items-center">
                            <i class="fas 
                                @if($alert['type'] === 'warning') fa-exclamation-triangle text-yellow-600
                                @elseif($alert['type'] === 'error') fa-exclamation-circle text-red-600
                                @else fa-info-circle text-blue-600 @endif mr-3"></i>
                            <span class="text-gray-800">{{ $alert['message'] }}</span>
                        </div>
                        <a href="{{ $alert['action'] }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            {{ $alert['action_text'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

        <!-- Key Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Revenue -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">Rs. {{ number_format($stats['total_revenue']) }}</p>
                            <p class="text-sm text-green-600 mt-1">+Rs. {{ number_format($stats['today_revenue']) }} today</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-green-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Bookings -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Bookings</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_bookings']) }}</p>
                            <p class="text-sm text-blue-600 mt-1">{{ $stats['today_bookings'] }} today</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-ticket-alt text-blue-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Active Users</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['active_users']) }}</p>
                            <p class="text-sm text-purple-600 mt-1">{{ $stats['new_users_today'] }} new today</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-users text-purple-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Bookings -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pending Bookings</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['pending_bookings']) }}</p>
                            <p class="text-sm text-yellow-600 mt-1">Require attention</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Revenue Chart -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Revenue Trend</h3>
                    <div class="flex space-x-1">
                        <button onclick="updateChart('revenue', '7days')" class="px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors duration-200">7 Days</button>
                        <button onclick="updateChart('revenue', '30days')" class="px-3 py-1 text-sm bg-gray-50 text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-200">30 Days</button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Bookings Chart -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Daily Bookings</h3>
                    <div class="flex space-x-1">
                        <button onclick="updateChart('bookings', '7days')" class="px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors duration-200">7 Days</button>
                        <button onclick="updateChart('bookings', '30days')" class="px-3 py-1 text-sm bg-gray-50 text-gray-700 rounded-md hover:bg-gray-100 transition-colors duration-200">30 Days</button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="bookingsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Live Activity Feed -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Live Activity Feed</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @if($recentBookings->count() > 0)
                        @foreach($recentBookings->take(5) as $booking)
                            <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-900">
                                        <span class="font-medium">{{ $booking->user->name }}</span>
                                        booked a ticket for
                                        <span class="font-medium">{{ $booking->schedule->route->full_name }}</span>
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $booking->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    Rs. {{ number_format($booking->total_amount) }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-chart-line text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No recent activity to display</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    <!-- Data Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Recent Bookings -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                <a href="{{ route('admin.bookings.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentBookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $booking->booking_reference }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->user->name }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rs. {{ number_format($booking->total_amount) }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Operators -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Top Operators</h3>
                <a href="{{ route('admin.operators.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="space-y-4">
                @foreach($topOperators as $operator)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr($operator->name, 0, 1) }}</span>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $operator->company_name ?? $operator->name }}</p>
                                <p class="text-xs text-gray-500">{{ $operator->buses_count }} buses • {{ $operator->total_schedules }} schedules</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">Rs. {{ number_format($operator->total_revenue) }}</p>
                            <p class="text-xs text-gray-500">Total Revenue</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- System Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- System Stats -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">System Overview</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Operators</span>
                    <span class="text-sm font-medium text-gray-900">{{ $stats['total_operators'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Active Buses</span>
                    <span class="text-sm font-medium text-gray-900">{{ $stats['active_buses'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Active Routes</span>
                    <span class="text-sm font-medium text-gray-900">{{ $stats['active_routes'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Upcoming Schedules</span>
                    <span class="text-sm font-medium text-gray-900">{{ $stats['upcoming_schedules'] }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.users.create') }}" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition duration-200">
                    <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                    <span class="text-sm font-medium text-blue-700">Add New User</span>
                </a>
                <a href="{{ route('admin.operators.create') }}" class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition duration-200">
                    <i class="fas fa-building text-green-600 mr-3"></i>
                    <span class="text-sm font-medium text-green-700">Add New Operator</span>
                </a>
                <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="flex items-center p-3 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition duration-200">
                    <i class="fas fa-clock text-yellow-600 mr-3"></i>
                    <span class="text-sm font-medium text-yellow-700">Review Pending Bookings</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition duration-200">
                    <i class="fas fa-chart-bar text-purple-600 mr-3"></i>
                    <span class="text-sm font-medium text-purple-700">View Reports</span>
                </a>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Users</h3>
            <div class="space-y-3">
                @foreach($recentUsers as $user)
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-gray-600 text-xs font-medium">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            @foreach($user->roles as $role)
                                @if($role->name === 'admin') bg-red-100 text-red-800
                                @elseif($role->name === 'operator') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif
                            @endforeach">
                            @foreach($user->roles as $role)
                                {{ ucfirst($role->name) }}
                            @endforeach
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize charts
let revenueChart, bookingsChart;

document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($dailyBookings, 'date')) !!},
            datasets: [{
                label: 'Revenue (Rs.)',
                data: {!! json_encode(array_column($dailyBookings, 'revenue')) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Bookings Chart
    const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
    bookingsChart = new Chart(bookingsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($dailyBookings, 'date')) !!},
            datasets: [{
                label: 'Bookings',
                data: {!! json_encode(array_column($dailyBookings, 'bookings')) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.8)',
                borderColor: 'rgb(34, 197, 94)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

function refreshStats() {
    fetch('{{ route("admin.dashboard.stats") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('last-updated').textContent = data.stats.last_updated;
                // Update other stats if needed
            }
        })
        .catch(error => console.error('Error refreshing stats:', error));
}

function updateChart(type, period) {
    fetch(`{{ route("admin.dashboard.chart-data") }}?type=${type}&period=${period}`)
        .then(response => response.json())
        .then(data => {
            const chart = type === 'revenue' ? revenueChart : bookingsChart;
            chart.data.labels = data.data.map(item => item.date);
            chart.data.datasets[0].data = data.data.map(item => item.value);
            chart.update();
        })
        .catch(error => console.error('Error updating chart:', error));
}
</script>
@endpush
@endsection
