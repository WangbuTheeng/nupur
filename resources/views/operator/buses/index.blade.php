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
    <div class="row mb-4">
        <!-- Total Buses -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Buses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Buses -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Buses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inactive Buses -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Inactive Buses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Schedules -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Today's Schedules
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['scheduled_today'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operator.buses.index') }}">
                <div class="row">
                    <!-- Search -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                   placeholder="Bus number, license plate..."
                                   class="form-control">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All Statuses</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bus Type Filter -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="bus_type">Bus Type</label>
                            <select name="bus_type" id="bus_type" class="form-control">
                                <option value="">All Types</option>
                                @foreach($busTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('bus_type') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('operator.buses.index') }}" class="btn btn-secondary">
                                    Clear
                                </a>
                            </div>
                        </div>
                    </div>
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
