@extends('layouts.operator')

@section('title', 'Bus Details - ' . $bus->bus_number)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Bus Details - {{ $bus->bus_number }}</h1>
            <p class="text-muted">{{ $bus->license_plate }}</p>
        </div>
        <div>
            <a href="{{ route('operator.buses.edit', $bus) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Bus
            </a>
            <a href="{{ route('operator.buses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Buses
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Bus Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Bus Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Bus Number:</strong></td>
                                    <td>{{ $bus->bus_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>License Plate:</strong></td>
                                    <td>{{ $bus->license_plate }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Bus Type:</strong></td>
                                    <td>{{ $bus->busType->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Model:</strong></td>
                                    <td>{{ $bus->model }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Color:</strong></td>
                                    <td>{{ $bus->color }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Manufacture Year:</strong></td>
                                    <td>{{ $bus->manufacture_year }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Seats:</strong></td>
                                    <td>{{ $bus->total_seats }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $bus->is_active ? 'success' : 'danger' }}">
                                            {{ $bus->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $bus->created_at->format('M j, Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($bus->amenities && count($bus->amenities) > 0)
                        <div class="mt-3">
                            <strong>Amenities:</strong>
                            <div class="mt-2">
                                @foreach($bus->amenities as $amenity)
                                    <span class="badge badge-info mr-1">{{ $amenity }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($bus->description)
                        <div class="mt-3">
                            <strong>Description:</strong>
                            <p class="mt-2">{{ $bus->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upcoming Schedules -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Upcoming Schedules</h6>
                </div>
                <div class="card-body">
                    @if($upcomingSchedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Route</th>
                                        <th>Date</th>
                                        <th>Departure</th>
                                        <th>Arrival</th>
                                        <th>Fare</th>
                                        <th>Available Seats</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingSchedules as $schedule)
                                        <tr>
                                            <td>{{ $schedule->route->name ?? 'N/A' }}</td>
                                            <td>{{ $schedule->travel_date }}</td>
                                            <td>{{ $schedule->departure_time }}</td>
                                            <td>{{ $schedule->arrival_time }}</td>
                                            <td>Rs. {{ number_format($schedule->fare, 2) }}</td>
                                            <td>{{ $schedule->available_seats }}</td>
                                            <td>
                                                <span class="badge badge-{{ $schedule->status === 'scheduled' ? 'primary' : 'secondary' }}">
                                                    {{ ucfirst($schedule->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5>No Upcoming Schedules</h5>
                            <p class="text-muted">Create schedules for this bus to start accepting bookings.</p>
                            <a href="{{ route('operator.schedules.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Schedule
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                </div>
                <div class="card-body">
                    @if($recentBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Route</th>
                                        <th>Date</th>
                                        <th>Seats</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                        <tr>
                                            <td>{{ $booking->user->name }}</td>
                                            <td>{{ $booking->schedule->route->name ?? 'N/A' }}</td>
                                            <td>{{ $booking->schedule->travel_date }}</td>
                                            <td>{{ $booking->seats_count }}</td>
                                            <td>Rs. {{ number_format($booking->total_amount, 2) }}</td>
                                            <td>
                                                <span class="badge badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                            <h5>No Recent Bookings</h5>
                            <p class="text-muted">No bookings have been made for this bus yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics & Actions -->
        <div class="col-lg-4">
            <!-- Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $stats['total_schedules'] }}</h4>
                            <small class="text-muted">Total Schedules</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $stats['upcoming_schedules'] }}</h4>
                            <small class="text-muted">Upcoming</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-info">{{ $stats['total_bookings'] }}</h4>
                            <small class="text-muted">Total Bookings</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning">Rs. {{ number_format($stats['monthly_revenue'], 2) }}</h4>
                            <small class="text-muted">Monthly Revenue</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('operator.schedules.create') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-calendar-plus"></i> Create Schedule
                        </a>
                        <a href="{{ route('operator.buses.edit', $bus) }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-edit"></i> Edit Bus Details
                        </a>
                        <form method="POST" action="{{ route('operator.buses.toggle-status', $bus) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-{{ $bus->is_active ? 'warning' : 'success' }} btn-block">
                                <i class="fas fa-{{ $bus->is_active ? 'pause' : 'play' }}"></i>
                                {{ $bus->is_active ? 'Deactivate Bus' : 'Activate Bus' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Bus Type Info -->
            @if($bus->busType)
                <div class="card shadow">
                    <div class="card-header">
                        <h6 class="m-0 font-weight-bold text-info">Bus Type Details</h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Type:</strong> {{ $bus->busType->name }}</p>
                        <p><strong>Default Seats:</strong> {{ $bus->busType->default_seats }}</p>
                        <p><strong>Fare Multiplier:</strong> {{ $bus->busType->fare_multiplier }}x</p>
                        @if($bus->busType->description)
                            <p><strong>Description:</strong> {{ $bus->busType->description }}</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
