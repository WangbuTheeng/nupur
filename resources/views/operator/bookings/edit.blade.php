@extends('layouts.operator')

@section('title', 'Edit Booking')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Booking</h1>
            <p class="text-muted">{{ $booking->booking_reference }}</p>
        </div>
        <div>
            <a href="{{ route('operator.bookings.show', $booking) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Booking
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('operator.bookings.update', $booking) }}">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Booking Details -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Booking Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ $booking->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="completed" {{ $booking->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_status" class="form-label">Payment Status</label>
                                    <select name="payment_status" id="payment_status" class="form-select">
                                        <option value="pending" {{ $booking->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ $booking->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="failed" {{ $booking->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                        <option value="refunded" {{ $booking->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-select">
                                        <option value="">Select Payment Method</option>
                                        <option value="cash" {{ $booking->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                        <option value="esewa" {{ $booking->payment_method == 'esewa' ? 'selected' : '' }}>eSewa</option>
                                        <option value="khalti" {{ $booking->payment_method == 'khalti' ? 'selected' : '' }}>Khalti</option>
                                        <option value="bank_transfer" {{ $booking->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_amount" class="form-label">Total Amount (Rs.)</label>
                                    <input type="number" name="total_amount" id="total_amount" class="form-control" 
                                           value="{{ $booking->total_amount }}" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_phone" class="form-label">Contact Phone</label>
                                    <input type="tel" name="contact_phone" id="contact_phone" class="form-control" 
                                           value="{{ $booking->contact_phone }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contact_email" class="form-label">Contact Email</label>
                                    <input type="email" name="contact_email" id="contact_email" class="form-control" 
                                           value="{{ $booking->contact_email }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Passenger Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Passenger Details</h5>
                    </div>
                    <div class="card-body">
                        <div id="passenger-details">
                            @if($booking->passenger_details)
                                @foreach($booking->passenger_details as $index => $passenger)
                                    <div class="passenger-detail border rounded p-3 mb-3" data-index="{{ $index }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6>Passenger {{ $index + 1 }}</h6>
                                            @if($index > 0)
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-passenger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" name="passenger_details[{{ $index }}][name]" 
                                                           class="form-control" value="{{ $passenger['name'] ?? '' }}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Age</label>
                                                    <input type="number" name="passenger_details[{{ $index }}][age]" 
                                                           class="form-control" value="{{ $passenger['age'] ?? '' }}" min="1" max="120">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label class="form-label">Gender</label>
                                                    <select name="passenger_details[{{ $index }}][gender]" class="form-select">
                                                        <option value="">Select</option>
                                                        <option value="male" {{ ($passenger['gender'] ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                                        <option value="female" {{ ($passenger['gender'] ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                                        <option value="other" {{ ($passenger['gender'] ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Phone (Optional)</label>
                                                    <input type="tel" name="passenger_details[{{ $index }}][phone]" 
                                                           class="form-control" value="{{ $passenger['phone'] ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Email (Optional)</label>
                                                    <input type="email" name="passenger_details[{{ $index }}][email]" 
                                                           class="form-control" value="{{ $passenger['email'] ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        <button type="button" class="btn btn-outline-primary" id="add-passenger">
                            <i class="fas fa-plus"></i> Add Passenger
                        </button>
                    </div>
                </div>

                <!-- Special Requests -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Special Requests</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <textarea name="special_requests" id="special_requests" class="form-control" rows="3" 
                                      placeholder="Any special requests or notes...">{{ $booking->special_requests }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Summary -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Booking Summary</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Booking Reference:</strong></td>
                                <td>{{ $booking->booking_reference }}</td>
                            </tr>
                            <tr>
                                <td><strong>Route:</strong></td>
                                <td>{{ $booking->schedule->route->sourceCity->name }} → {{ $booking->schedule->route->destinationCity->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Travel Date:</strong></td>
                                <td>{{ $booking->schedule->travel_date->format('M d, Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Departure:</strong></td>
                                <td>{{ $booking->schedule->departure_time }}</td>
                            </tr>
                            <tr>
                                <td><strong>Bus:</strong></td>
                                <td>{{ $booking->schedule->bus->bus_number }}</td>
                            </tr>
                            <tr>
                                <td><strong>Seats:</strong></td>
                                <td>
                                    @foreach($booking->seat_numbers as $seat)
                                        <span class="badge bg-info me-1">{{ $seat }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Passenger Count:</strong></td>
                                <td>{{ $booking->passenger_count }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save"></i> Update Booking
                        </button>
                        <a href="{{ route('operator.bookings.show', $booking) }}" class="btn btn-secondary w-100">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let passengerIndex = {{ count($booking->passenger_details ?? []) }};

document.getElementById('add-passenger').addEventListener('click', function() {
    const container = document.getElementById('passenger-details');
    const passengerHtml = `
        <div class="passenger-detail border rounded p-3 mb-3" data-index="${passengerIndex}">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6>Passenger ${passengerIndex + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-passenger">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="passenger_details[${passengerIndex}][name]" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Age</label>
                        <input type="number" name="passenger_details[${passengerIndex}][age]" class="form-control" min="1" max="120">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <select name="passenger_details[${passengerIndex}][gender]" class="form-select">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Phone (Optional)</label>
                        <input type="tel" name="passenger_details[${passengerIndex}][phone]" class="form-control">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email (Optional)</label>
                        <input type="email" name="passenger_details[${passengerIndex}][email]" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', passengerHtml);
    passengerIndex++;
    updatePassengerNumbers();
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-passenger')) {
        e.target.closest('.passenger-detail').remove();
        updatePassengerNumbers();
    }
});

function updatePassengerNumbers() {
    const passengers = document.querySelectorAll('.passenger-detail');
    passengers.forEach((passenger, index) => {
        passenger.querySelector('h6').textContent = `Passenger ${index + 1}`;
    });
}
</script>
@endpush
@endsection
