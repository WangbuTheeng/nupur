@extends('layouts.operator')

@section('title', 'Today\'s Bookings')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Today's Bookings</h1>
            <p class="text-muted">{{ \Carbon\Carbon::today()->format('l, F d, Y') }}</p>
        </div>
        <div>
            <a href="{{ route('operator.bookings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> All Bookings
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_bookings'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Confirmed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['confirmed_bookings'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_bookings'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rs. {{ number_format($stats['total_revenue'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-rupee-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings by Schedule -->
    @if($schedules->count() > 0)
        @foreach($schedules as $schedule)
            <div class="card mb-4">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h5 class="mb-0">
                                {{ $schedule->route->sourceCity->name }} → {{ $schedule->route->destinationCity->name }}
                            </h5>
                            <small class="text-muted">
                                {{ $schedule->bus->bus_number }} | 
                                Departure: {{ $schedule->departure_time }} | 
                                {{ $schedule->bookings->count() }} bookings
                            </small>
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-primary">{{ $schedule->bookings->sum('passenger_count') }} passengers</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($schedule->bookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Booking Ref</th>
                                        <th>Passenger</th>
                                        <th>Seats</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedule->bookings as $booking)
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
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-ticket-alt fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">No bookings for this schedule</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5>No Schedules Today</h5>
                <p class="text-muted">You don't have any schedules for today.</p>
                <a href="{{ route('operator.schedules.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Schedule
                </a>
            </div>
        </div>
    @endif
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
</script>
@endpush
@endsection
