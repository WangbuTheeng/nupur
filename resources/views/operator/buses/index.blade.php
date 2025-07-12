@extends('layouts.operator')

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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Buses -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-blue-500">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-bus text-blue-600 text-xl"></i>
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
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-green-500">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
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
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-red-500">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
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
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border-l-4 border-yellow-500">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-calendar-check text-yellow-600 text-xl"></i>
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
    <div class="bg-white overflow-hidden shadow-lg rounded-xl mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Filter Buses</h3>
        </div>
        <div class="p-6">
            <form method="GET" action="{{ route('operator.buses.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search Buses</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                           placeholder="Bus number, license plate..."
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Bus Type Filter -->
                <div>
                    <label for="bus_type" class="block text-sm font-medium text-gray-700 mb-2">Bus Type</label>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
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
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-200 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <!-- Bus Header -->
                    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $bus->bus_number }}</h3>
                                <p class="text-sm text-gray-600">{{ $bus->license_plate }}</p>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $bus->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full {{ $bus->is_active ? 'bg-green-400' : 'bg-red-400' }}"></span>
                                    {{ $bus->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Bus Details -->
                    <div class="p-6">
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-tag text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Bus Type</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $bus->busType->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-car text-purple-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Model & Year</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $bus->model }} ({{ $bus->manufacture_year }})</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Capacity</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $bus->total_seats }} seats</p>
                                    @if($bus->seat_layout && isset($bus->seat_layout['layout_type']))
                                        <p class="text-xs text-blue-600 font-medium">{{ strtoupper($bus->seat_layout['layout_type']) }} Layout</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Upcoming Schedules -->
                        @if($bus->schedules->count() > 0)
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                    Upcoming Schedules
                                </h4>
                                <div class="space-y-2">
                                    @foreach($bus->schedules->take(2) as $schedule)
                                        <div class="flex items-center justify-between text-xs bg-blue-50 rounded-lg p-3">
                                            <div class="flex-1">
                                                <div class="font-medium text-blue-900">{{ $schedule->route->name ?? 'N/A' }}</div>
                                                <div class="text-blue-600 mt-1">{{ $schedule->travel_date }} • {{ $schedule->departure_time }}</div>
                                            </div>
                                            <div class="ml-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($bus->schedules->count() > 2)
                                        <div class="text-xs text-gray-500 text-center py-2">
                                            <i class="fas fa-plus-circle mr-1"></i>
                                            {{ $bus->schedules->count() - 2 }} more schedules
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="border-t border-gray-200 pt-4">
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times text-gray-300 text-2xl mb-2"></i>
                                    <p class="text-sm text-gray-500">No upcoming schedules</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Bus Actions -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-2">
                                <a href="{{ route('operator.buses.show', $bus) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                    <i class="fas fa-eye mr-1.5"></i>
                                    View
                                </a>
                                <a href="{{ route('operator.buses.edit', $bus) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                    <i class="fas fa-edit mr-1.5"></i>
                                    Edit
                                </a>
                            </div>
                            <div>
                                <form method="POST" action="{{ route('operator.buses.toggle-status', $bus) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white {{ $bus->is_active ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500' }} focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200">
                                        <i class="fas fa-{{ $bus->is_active ? 'times' : 'check' }} mr-1.5"></i>
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
        <div class="flex justify-center">
            {{ $buses->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white shadow-lg rounded-xl border border-gray-200">
            <div class="text-center py-12 px-6">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-bus text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No buses found</h3>
                <p class="text-gray-500 mb-6 max-w-sm mx-auto">Get started by adding your first bus to the fleet and begin managing your transportation services.</p>
                <div>
                    <a href="{{ route('operator.buses.create') }}"
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Add Your First Bus
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
