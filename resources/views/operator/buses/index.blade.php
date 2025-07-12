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
        <div class="row">
            @foreach($buses as $bus)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card shadow h-100">
                        <!-- Bus Header -->
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1">{{ $bus->bus_number }}</h5>
                                    <small class="text-muted">{{ $bus->license_plate }}</small>
                                </div>
                                <div>
                                    <span class="badge badge-{{ $bus->is_active ? 'success' : 'danger' }}">
                                        {{ $bus->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Bus Details -->
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-tag text-muted me-2"></i>
                                    <span class="text-sm">{{ $bus->busType->name ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-car text-muted me-2"></i>
                                    <span class="text-sm">{{ $bus->model }} ({{ $bus->manufacture_year }})</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-users text-muted me-2"></i>
                                    <span class="text-sm">{{ $bus->total_seats }} seats</span>
                                </div>
                            </div>

                            <!-- Upcoming Schedules -->
                            @if($bus->schedules->count() > 0)
                                <div class="border-top pt-3">
                                    <h6 class="text-sm font-weight-bold mb-2">Upcoming Schedules</h6>
                                    @foreach($bus->schedules->take(2) as $schedule)
                                        <div class="text-xs text-muted mb-1">
                                            {{ $schedule->route->name ?? 'N/A' }} - {{ $schedule->travel_date }} {{ $schedule->departure_time }}
                                        </div>
                                    @endforeach
                                    @if($bus->schedules->count() > 2)
                                        <div class="text-xs text-muted">+{{ $bus->schedules->count() - 2 }} more</div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Bus Actions -->
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('operator.buses.show', $bus) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('operator.buses.edit', $bus) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                </div>
                                <div>
                                    <form method="POST" action="{{ route('operator.buses.toggle-status', $bus) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $bus->is_active ? 'danger' : 'success' }}">
                                            <i class="fas fa-{{ $bus->is_active ? 'times' : 'check' }}"></i>
                                            {{ $bus->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $buses->appends(request()->query())->links() }}
        </div>
    @else
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-bus fa-3x text-muted mb-3"></i>
                <h5>No buses found</h5>
                <p class="text-muted">Get started by adding your first bus to the fleet.</p>
                <div class="mt-4">
                    <a href="{{ route('operator.buses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Your First Bus
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
