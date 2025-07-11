@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('meta')
<meta name="user-id" content="{{ auth()->id() }}">
@endpush

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">Real-time Admin Dashboard</h1>
            <p class="text-gray-600">Welcome back! Here's what's happening with BookNGo in real-time.</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center text-gray-500">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                </svg>
                {{ now()->format('l, F j, Y') }}
            </div>
            <div id="last-updated" class="last-updated">
                Last updated: --:--:--
            </div>
            <div class="relative">
                <button class="p-2 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-2H4v2zM4 15h8v-2H4v2zM4 11h10V9H4v2z"></path>
                    </svg>
                    <span id="notification-badge" class="notification-badge">0</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Real-time Statistics -->
    <div id="dashboard-stats" class="loading">
        <!-- Stats will be loaded dynamically -->
    </div>

    <!-- Real-time Charts -->
    <div id="dashboard-chart">
        <!-- Chart will be loaded dynamically -->
    </div>

    <!-- Real-time Activity Feed -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Live Activity Feed</h3>
        <div id="activity-feed" class="space-y-3">
            <!-- Activity items will be loaded dynamically -->
        </div>
    </div>

    <!-- Notification Container -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2">
        <!-- Notifications will appear here -->
    </div>
</div>

@push('scripts')
<script>
    // Initialize real-time dashboard
    document.addEventListener('DOMContentLoaded', function() {
        // Set up CSRF token for API requests
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Initialize real-time dashboard if not already done
        if (!window.realtimeDashboard) {
            window.realtimeDashboard = new RealtimeDashboard({
                updateInterval: 30000, // 30 seconds
                chartUpdateInterval: 60000, // 1 minute
                notificationCheckInterval: 10000 // 10 seconds
            });
        }
    });
</script>
@endpush

@endsection
