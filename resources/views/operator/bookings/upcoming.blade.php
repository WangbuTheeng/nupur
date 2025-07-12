@extends('layouts.operator')

@section('title', 'Upcoming Bookings')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Upcoming Bookings</h1>
            <p class="text-muted">Bookings for future travel dates</p>
        </div>
        <div>
            <a href="{{ route('operator.bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> All Bookings
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operator.bookings.upcoming') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="route" class="form-label">Route</label>
                        <select name="route" id="route" class="form-select">
                            <option value="">All Routes</option>
                            @foreach($routes as $route)
                                <option value="{{ $route->id }}" {{ request('route') == $route->id ? 'selected' : '' }}>
                                    {{ $route->sourceCity->name }} → {{ $route->destinationCity->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" 
                               value="{{ request('date_from', \Carbon\Carbon::today()->format('Y-m-d')) }}" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" 
                               value="{{ request('date_to') }}" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Booking ref, name..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('operator.bookings.upcoming') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Upcoming Bookings ({{ $bookings->total() }} total)</h5>
        </div>
        <div class="card-body p-0">
            @if($bookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Booking Ref</th>
                                <th>Passenger</th>
                                <th>Route</th>
                                <th>Travel Date</th>
                                <th>Departure</th>
                                <th>Seats</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr>
                                    <td>
                                        <strong>{{ $booking->booking_reference }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $booking->user->name }}</strong><br>
                                            <small class="text-muted">{{ $booking->contact_phone }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $booking->schedule->route->sourceCity->name }} →<br>
                                            {{ $booking->schedule->route->destinationCity->name }}
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $booking->schedule->travel_date->format('M d, Y') }}<br>
                                            <small class="text-muted">{{ $booking->schedule->travel_date->format('l') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $booking->schedule->departure_time }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $booking->passenger_count }} seats</span><br>
                                        <small class="text-muted">{{ implode(', ', $booking->seat_numbers) }}</small>
                                    </td>
                                    <td>
                                        <strong>Rs. {{ number_format($booking->total_amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge {{ $booking->booking_type == 'counter' ? 'bg-warning' : 'bg-primary' }}">
                                            {{ ucfirst($booking->booking_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            @if($booking->status == 'confirmed') bg-success
                                            @elseif($booking->status == 'pending') bg-warning
                                            @elseif($booking->status == 'cancelled') bg-danger
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('operator.bookings.show', $booking) }}" class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($booking->status == 'pending')
                                                <button type="button" class="btn btn-outline-success" onclick="confirmBooking({{ $booking->id }})" title="Confirm">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            @if(in_array($booking->status, ['pending', 'confirmed']))
                                                <button type="button" class="btn btn-outline-danger" onclick="cancelBooking({{ $booking->id }})" title="Cancel">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $bookings->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                    <h5>No upcoming bookings found</h5>
                    <p class="text-muted">No bookings match your current filters for future travel dates.</p>
                    @if(!request()->hasAny(['route', 'date_from', 'date_to', 'search']))
                        <a href="{{ route('operator.counter.index') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Counter Booking
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Confirm Booking Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to confirm this booking?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmBtn">Confirm Booking</button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Booking Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this booking?</p>
                <div class="mb-3">
                    <label for="cancelReason" class="form-label">Cancellation Reason</label>
                    <textarea class="form-control" id="cancelReason" rows="3" placeholder="Enter reason for cancellation..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="cancelBtn">Cancel Booking</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentBookingId = null;

function confirmBooking(bookingId) {
    currentBookingId = bookingId;
    new bootstrap.Modal(document.getElementById('confirmModal')).show();
}

function cancelBooking(bookingId) {
    currentBookingId = bookingId;
    new bootstrap.Modal(document.getElementById('cancelModal')).show();
}

document.getElementById('confirmBtn').addEventListener('click', function() {
    if (currentBookingId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/operator/bookings/${currentBookingId}/confirm`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
});

document.getElementById('cancelBtn').addEventListener('click', function() {
    if (currentBookingId) {
        const reason = document.getElementById('cancelReason').value;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/operator/bookings/${currentBookingId}/cancel`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'reason';
        reasonInput.value = reason;
        form.appendChild(reasonInput);
        
        document.body.appendChild(form);
        form.submit();
    }
});

// Set minimum date for date inputs
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('date_from').min = today;
    document.getElementById('date_to').min = today;
});
</script>
@endpush
@endsection
