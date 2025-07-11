@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Reports & Analytics</h1>
                    <p class="text-purple-100">Comprehensive insights into your bus booking platform</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('admin.reports.export.bookings') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Statistics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Bookings -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Bookings</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_bookings']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-2xl font-bold text-gray-900">Rs. {{ number_format($stats['total_revenue'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Operators -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Operators</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_operators']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Routes -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Routes</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_routes']) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- This Month Stats -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">This Month Performance</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['monthly_bookings']) }}</div>
                        <div class="text-sm text-gray-500">Bookings</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">Rs. {{ number_format($stats['monthly_revenue'], 2) }}</div>
                        <div class="text-sm text-gray-500">Revenue</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-5 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Quick Reports</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('admin.reports.bookings') }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">Booking Reports</div>
                            <div class="text-xs text-gray-500">Detailed booking analysis</div>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports.revenue') }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">Revenue Reports</div>
                            <div class="text-xs text-gray-500">Financial performance analysis</div>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports.operators') }}" class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">Operator Reports</div>
                            <div class="text-xs text-gray-500">Operator performance metrics</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Routes -->
    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top Performing Routes</h3>
        </div>
        <div class="p-6">
            @if($topRoutes->count() > 0)
                <div class="space-y-4">
                    @foreach($topRoutes->take(5) as $route)
                        <div class="flex items-center justify-between p-4 border border-gray-100 rounded-lg">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $route->name }}</div>
                                <div class="text-sm text-gray-500">{{ $route->total_bookings }} bookings</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">Rs. {{ number_format($route->base_fare, 2) }}</div>
                                <div class="text-sm text-gray-500">Base fare</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No route data available</h3>
                    <p class="mt-1 text-sm text-gray-500">Routes will appear here once bookings are made.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
