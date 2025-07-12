@extends('layouts.operator')

@section('title', 'Seat Layout Configuration')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Seat Layout Configuration</h1>
                    <p class="text-blue-100">Configure your bus seat layout for optimal passenger experience</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('operator.buses.index') }}" class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Buses
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Configuration Form -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Layout Configuration</h3>
                <p class="text-sm text-gray-600">Choose your bus layout type and configuration</p>
            </div>
            
            <div class="p-6">
                <form id="seatLayoutForm" class="space-y-6">
                    @csrf
                    
                    <!-- Total Seats -->
                    <div>
                        <label for="total_seats" class="block text-sm font-medium text-gray-700 mb-2">Total Seats</label>
                        <input type="number" 
                               id="total_seats" 
                               name="total_seats" 
                               min="10" 
                               max="60" 
                               value="31"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Recommended: 27, 31, 35, or 39 seats</p>
                    </div>

                    <!-- Layout Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Layout Type</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="layout_type" value="2x2" checked class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-3">
                                    <span class="font-medium">2x2 (Standard)</span>
                                    <span class="block text-sm text-gray-500">2 seats | aisle | 2 seats - Most common layout</span>
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="layout_type" value="2x1" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-3">
                                    <span class="font-medium">2x1 (Compact)</span>
                                    <span class="block text-sm text-gray-500">2 seats | aisle | 1 seat - For smaller buses</span>
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="layout_type" value="3x2" class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-3">
                                    <span class="font-medium">3x2 (Large)</span>
                                    <span class="block text-sm text-gray-500">3 seats | aisle | 2 seats - For larger buses</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Back Row Configuration -->
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="has_back_row" checked class="text-blue-600 focus:ring-blue-500 rounded">
                            <span class="ml-3">
                                <span class="font-medium">Include Back Row</span>
                                <span class="block text-sm text-gray-500">Continuous line of seats at the back</span>
                            </span>
                        </label>
                    </div>

                    <!-- Recommended Configurations -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-medium text-blue-900 mb-2">Recommended Configurations</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-blue-700">2x2 Layout:</span>
                                <span class="text-blue-600">27, 31, 35 seats</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">2x1 Layout:</span>
                                <span class="text-blue-600">21, 25, 29 seats</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-blue-700">3x2 Layout:</span>
                                <span class="text-blue-600">35, 39, 45 seats</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-4">
                        <button type="button" 
                                id="previewBtn"
                                class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            Preview Layout
                        </button>
                        <button type="button" 
                                id="saveBtn"
                                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition"
                                disabled>
                            Save Configuration
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Live Preview -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Live Preview</h3>
                <p class="text-sm text-gray-600">See how your seat layout will look</p>
            </div>
            
            <div class="p-6">
                <div id="seatLayoutPreview" class="min-h-96 flex items-center justify-center bg-gray-50 rounded-lg">
                    <div class="text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                        </svg>
                        <p class="text-lg font-medium">Click "Preview Layout" to see your configuration</p>
                        <p class="text-sm">Adjust settings on the left and preview changes here</p>
                    </div>
                </div>
                
                <!-- Layout Info -->
                <div id="layoutInfo" class="mt-4 hidden">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Layout Information</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Total Seats:</span>
                                <span id="infoTotalSeats" class="font-medium ml-2">-</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Layout Type:</span>
                                <span id="infoLayoutType" class="font-medium ml-2">-</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Regular Rows:</span>
                                <span id="infoRegularRows" class="font-medium ml-2">-</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Back Row Seats:</span>
                                <span id="infoBackRowSeats" class="font-medium ml-2">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/seat-map.css') }}">
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('seatLayoutForm');
    const previewBtn = document.getElementById('previewBtn');
    const saveBtn = document.getElementById('saveBtn');
    const previewContainer = document.getElementById('seatLayoutPreview');
    const layoutInfo = document.getElementById('layoutInfo');
    
    let currentLayout = null;

    // Preview layout
    previewBtn.addEventListener('click', function() {
        const formData = new FormData(form);
        
        previewBtn.disabled = true;
        previewBtn.textContent = 'Loading...';
        
        fetch('{{ route("operator.buses.seat-layout.preview") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentLayout = data.layout;
                renderPreview(data.layout);
                updateLayoutInfo(data.layout);
                saveBtn.disabled = false;
            } else {
                alert('Error generating preview');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error generating preview');
        })
        .finally(() => {
            previewBtn.disabled = false;
            previewBtn.textContent = 'Preview Layout';
        });
    });

    // Auto-preview on form changes
    form.addEventListener('change', function() {
        saveBtn.disabled = true;
    });

    function renderPreview(layout) {
        // Create a simplified seat map renderer
        const seatMap = new SeatLayoutPreview(layout, previewContainer);
        seatMap.render();
        layoutInfo.classList.remove('hidden');
    }

    function updateLayoutInfo(layout) {
        document.getElementById('infoTotalSeats').textContent = layout.total_seats;
        document.getElementById('infoLayoutType').textContent = layout.layout_type.toUpperCase();
        document.getElementById('infoRegularRows').textContent = layout.has_back_row ? layout.rows - 1 : layout.rows;
        document.getElementById('infoBackRowSeats').textContent = layout.has_back_row ? layout.back_row_seats : 'None';
    }
});

// Simplified seat layout preview class
class SeatLayoutPreview {
    constructor(layout, container) {
        this.layout = layout;
        this.container = container;
    }

    render() {
        const { layout_type, rows, seats, driver_seat, door, has_back_row } = this.layout;
        
        let html = '<div class="seat-map-container">';
        
        // Bus layout container
        html += '<div class="bus-layout-container">';
        html += '<div class="bus-frame">';
        
        // Top section with driver seat and door
        html += '<div class="bus-top-section">';
        html += '<div class="bus-door" title="Front Door">🚪</div>';
        html += '<div class="bus-front-space"></div>';
        html += '<div class="driver-seat" title="Driver">👨‍✈️</div>';
        html += '</div>';
        
        // Main seating area
        html += this.renderMainSeatingArea();
        
        html += '</div></div></div>';
        
        this.container.innerHTML = html;
    }

    renderMainSeatingArea() {
        const { rows, seats, has_back_row, aisle_position } = this.layout;
        
        let html = '<div class="main-seating-area">';
        
        // Group seats by row
        const seatsByRow = {};
        seats.forEach(seat => {
            if (!seatsByRow[seat.row]) {
                seatsByRow[seat.row] = [];
            }
            seatsByRow[seat.row].push(seat);
        });
        
        // Render each row
        for (let rowNum = 1; rowNum <= rows; rowNum++) {
            const rowSeats = seatsByRow[rowNum] || [];
            const isBackRow = has_back_row && rowNum === rows;
            
            html += `<div class="seat-row ${isBackRow ? 'back-row' : 'regular-row'}" data-row="${rowNum}">`;
            
            if (isBackRow) {
                html += this.renderBackRow(rowSeats);
            } else {
                html += this.renderRegularRow(rowSeats, aisle_position);
            }
            
            html += '</div>';
        }
        
        html += '</div>';
        return html;
    }

    renderRegularRow(rowSeats, aislePosition) {
        let html = '';
        
        rowSeats.sort((a, b) => a.column - b.column);
        
        let currentColumn = 1;
        
        rowSeats.forEach(seat => {
            if (currentColumn === aislePosition + 1) {
                html += '<div class="aisle-space"></div>';
            }
            
            const isWindow = seat.is_window ? 'window-seat' : '';
            
            html += `<div class="seat available ${isWindow}" title="Seat ${seat.number}">
                        ${seat.number}
                     </div>`;
            
            currentColumn = seat.column + 1;
        });
        
        return html;
    }

    renderBackRow(rowSeats) {
        let html = '<div class="back-row-container">';
        
        rowSeats.sort((a, b) => a.column - b.column);
        
        rowSeats.forEach(seat => {
            html += `<div class="seat available back-row-seat" title="Seat ${seat.number}">
                        ${seat.number}
                     </div>`;
        });
        
        html += '</div>';
        return html;
    }
}
</script>
@endpush
@endsection
