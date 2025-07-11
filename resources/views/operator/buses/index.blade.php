@extends('layouts.app')

@section('title', 'My Buses')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">My Buses</h1>
                    <p class="text-green-100">Manage your fleet and track performance</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('operator.buses.create') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Bus
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Buses -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Buses</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Buses -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Buses</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inactive Buses -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Inactive Buses</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['inactive'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Schedules -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Schedules</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ $stats['scheduled_today'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-6">
        <div class="px-6 py-4">
            <form method="GET" action="{{ route('operator.buses.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Bus number, license plate..." 
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Bus Type Filter -->
                <div>
                    <label for="bus_type" class="block text-sm font-medium text-gray-700 mb-1">Bus Type</label>
                    <select name="bus_type" id="bus_type" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">All Types</option>
                        @foreach($busTypes as $type)
                            <option value="{{ $type->id }}" {{ request('bus_type') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Buttons -->
                <div class="flex items-end space-x-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('operator.buses.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Buses Grid -->
    @if($buses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($buses as $bus)
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                    <!-- Bus Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $bus->bus_number }}</h3>
                                <p class="text-sm text-gray-600">{{ $bus->license_plate }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $bus->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $bus->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Bus Details -->
                    <div class="px-6 py-4">
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ $bus->busType->name ?? 'N/A' }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $bus->model }} ({{ $bus->manufacture_year }})
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                {{ $bus->total_seats }} seats
                            </div>
                        </div>

                        <!-- Upcoming Schedules -->
                        @if($bus->schedules->count() > 0)
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Upcoming Schedules</h4>
                                <div class="space-y-1">
                                    @foreach($bus->schedules->take(2) as $schedule)
                                        <div class="text-xs text-gray-600">
                                            {{ $schedule->route->name ?? 'N/A' }} - {{ $schedule->travel_date }} {{ $schedule->departure_time }}
                                        </div>
                                    @endforeach
                                    @if($bus->schedules->count() > 2)
                                        <div class="text-xs text-gray-500">+{{ $bus->schedules->count() - 2 }} more</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Bus Actions -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-2">
                                <a href="{{ route('operator.buses.show', $bus) }}" class="text-green-600 hover:text-green-900 text-sm font-medium">View</a>
                                <a href="{{ route('operator.buses.edit', $bus) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</a>
                                <a href="{{ route('operator.buses.schedules', $bus) }}" class="text-purple-600 hover:text-purple-900 text-sm font-medium">Schedules</a>
                            </div>
                            <div class="flex space-x-2">
                                <form method="POST" action="{{ route('operator.buses.toggle-status', $bus) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm font-medium {{ $bus->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}">
                                        {{ $bus->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="bg-white px-6 py-4 border border-gray-200 rounded-xl">
            {{ $buses->links() }}
        </div>
    @else
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No buses found</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first bus to the fleet.</p>
                <div class="mt-6">
                    <a href="{{ route('operator.buses.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Your First Bus
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
