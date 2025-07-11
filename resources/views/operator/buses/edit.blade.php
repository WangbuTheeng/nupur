@extends('layouts.operator')

@section('title', 'Edit Bus - ' . $bus->bus_number)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Edit Bus - {{ $bus->bus_number }}</h1>
            <p class="text-muted">Update bus information</p>
        </div>
        <div>
            <a href="{{ route('operator.buses.show', $bus) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Bus
            </a>
            <a href="{{ route('operator.buses.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Buses
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Bus Information</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('operator.buses.update', $bus) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bus_number">Bus Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('bus_number') is-invalid @enderror" 
                                           id="bus_number" name="bus_number" value="{{ old('bus_number', $bus->bus_number) }}" required>
                                    @error('bus_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="license_plate">License Plate <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('license_plate') is-invalid @enderror" 
                                           id="license_plate" name="license_plate" value="{{ old('license_plate', $bus->license_plate) }}" required>
                                    @error('license_plate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bus_type_id">Bus Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('bus_type_id') is-invalid @enderror" 
                                            id="bus_type_id" name="bus_type_id" required>
                                        <option value="">Select Bus Type</option>
                                        @foreach($busTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('bus_type_id', $bus->bus_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bus_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="total_seats">Total Seats <span class="text-danger">*</span></label>
                                    <input type="number" min="10" max="100" 
                                           class="form-control @error('total_seats') is-invalid @enderror" 
                                           id="total_seats" name="total_seats" value="{{ old('total_seats', $bus->total_seats) }}" required>
                                    @error('total_seats')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Changing seat count will regenerate seat layout</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="model">Model <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('model') is-invalid @enderror" 
                                           id="model" name="model" value="{{ old('model', $bus->model) }}" required>
                                    @error('model')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="color">Color <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('color') is-invalid @enderror" 
                                           id="color" name="color" value="{{ old('color', $bus->color) }}" required>
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="manufacture_year">Manufacture Year <span class="text-danger">*</span></label>
                            <input type="number" min="1990" max="{{ date('Y') + 1 }}" 
                                   class="form-control @error('manufacture_year') is-invalid @enderror" 
                                   id="manufacture_year" name="manufacture_year" value="{{ old('manufacture_year', $bus->manufacture_year) }}" required>
                            @error('manufacture_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="amenities">Amenities</label>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]" value="WiFi" id="wifi"
                                               {{ in_array('WiFi', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="wifi">WiFi</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]" value="AC" id="ac"
                                               {{ in_array('AC', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="ac">Air Conditioning</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]" value="TV" id="tv"
                                               {{ in_array('TV', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="tv">TV/Entertainment</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]" value="USB" id="usb"
                                               {{ in_array('USB', old('amenities', $bus->amenities ?? [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="usb">USB Charging</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" 
                                      placeholder="Additional information about the bus...">{{ old('description', $bus->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Bus
                            </button>
                            <a href="{{ route('operator.buses.show', $bus) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-info">Current Information</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Bus Number:</strong></td>
                            <td>{{ $bus->bus_number }}</td>
                        </tr>
                        <tr>
                            <td><strong>License Plate:</strong></td>
                            <td>{{ $bus->license_plate }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current Type:</strong></td>
                            <td>{{ $bus->busType->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Current Seats:</strong></td>
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
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-warning">Important Notes</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled text-sm">
                        <li class="mb-2">
                            <i class="fas fa-exclamation-triangle text-warning"></i>
                            Changing seat count will regenerate the seat layout
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-info"></i>
                            Bus number and license plate must be unique
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            Changes won't affect existing bookings
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-danger">Danger Zone</h6>
                </div>
                <div class="card-body">
                    <p class="text-sm text-muted">Permanently delete this bus. This action cannot be undone.</p>
                    <form method="POST" action="{{ route('operator.buses.destroy', $bus) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this bus? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Delete Bus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
