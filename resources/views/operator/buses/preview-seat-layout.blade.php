@extends('layouts.operator')

@section('title', 'Seat Layout Preview')

@push('styles')
<style>
/* Inline seat map styles */
.seat-map-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.bus-layout-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 15px;
}

.bus-frame {
    background: linear-gradient(to bottom, #f8fafc, #e2e8f0);
    border: 3px solid #475569;
    border-radius: 25px;
    padding: 15px;
    position: relative;
    min-height: 300px;
}

.bus-top-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 8px 15px;
    background: rgba(255, 255, 255, 0.7);
    border-radius: 15px;
    border: 2px solid #cbd5e1;
}

.bus-door {
    background: #3b82f6;
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.driver-seat {
    background: #10b981;
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.main-seating-area {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: center;
}

.seat-row {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
}

.seat {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 2px solid transparent;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.seat.available {
    background: #22c55e;
    color: white;
}

.seat.window-seat {
    background: #3b82f6;
    color: white;
}

.seat.back-row-seat {
    background: #8b5cf6;
    color: white;
}

.aisle-space {
    width: 20px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    font-size: 18px;
}

.back-row-container {
    display: flex;
    justify-content: center;
    gap: 4px;
    background: rgba(139, 92, 246, 0.1);
    padding: 8px;
    border-radius: 12px;
    border: 2px dashed #8b5cf6;
}
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Seat Layout Preview</h1>
                <p class="text-gray-600 mt-1">Interactive seat layout demonstration</p>
            </div>
            <a href="{{ route('operator.buses.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                ← Back to Buses
            </a>
        </div>
    </div>

    <!-- Layout Preview -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-semibold mb-4">{{ $bus->bus_number ?? 'Demo Bus' }} - Seat Layout</h2>
        
        <!-- Seat Layout Display -->
        <div id="seatLayoutDisplay" class="min-h-64">
            <div class="flex items-center justify-center h-64 bg-gray-50 rounded-lg">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                    <p class="text-gray-500">Loading seat layout...</p>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-6 flex justify-center gap-6 text-sm">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-500 rounded"></div>
                <span>Window Seat</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-green-500 rounded"></div>
                <span>Regular Seat</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-purple-500 rounded"></div>
                <span>Back Row</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Simple and robust seat layout renderer
function renderBusLayout(seatData, containerId) {
    console.log('🚌 Rendering bus layout for container:', containerId);
    console.log('📊 Seat data:', seatData);
    
    const container = document.getElementById(containerId);
    if (!container) {
        console.error('❌ Container not found:', containerId);
        return false;
    }
    
    const seats = seatData.seats || [];
    const layoutType = seatData.layout_type || '2x2';
    const hasBackRow = seatData.has_back_row || false;
    const aislePosition = seatData.aisle_position || 2;
    
    // Create the complete layout HTML
    let html = `
        <div class="seat-map-container">
            <div class="bus-layout-container">
                <div class="bus-frame">
                    <!-- Top section with door and driver -->
                    <div class="bus-top-section">
                        <div class="bus-door" title="Front Door">🚪 Door</div>
                        <div class="driver-seat" title="Driver">👨‍✈️ Driver</div>
                    </div>
                    
                    <!-- Main seating area -->
                    <div class="main-seating-area">
    `;
    
    // Group seats by row
    const seatsByRow = {};
    seats.forEach(seat => {
        if (!seatsByRow[seat.row]) seatsByRow[seat.row] = [];
        seatsByRow[seat.row].push(seat);
    });
    
    const maxRow = Math.max(...seats.map(s => s.row));
    
    // Render each row
    for (let rowNum = 1; rowNum <= maxRow; rowNum++) {
        const rowSeats = (seatsByRow[rowNum] || []).sort((a, b) => a.column - b.column);
        const isBackRow = hasBackRow && rowNum === maxRow;
        
        if (rowSeats.length === 0) continue;
        
        html += `<div class="seat-row" data-row="${rowNum}">`;
        
        if (isBackRow) {
            // Back row - continuous seats across full width
            html += '<div class="back-row-container">';
            rowSeats.forEach(seat => {
                html += `<div class="seat back-row-seat" title="Seat ${seat.number}">${seat.number}</div>`;
            });
            html += '</div>';
        } else {
            // Regular row - arrange seats by column position
            const maxColumn = Math.max(...rowSeats.map(s => s.column));
            
            for (let col = 1; col <= maxColumn; col++) {
                // Find seat for this column
                const seat = rowSeats.find(s => s.column === col);
                if (seat) {
                    const seatClass = seat.is_window ? 'seat window-seat' : 'seat available';
                    const windowText = seat.is_window ? ' (Window)' : '';

                    html += `<div class="${seatClass}" title="Seat ${seat.number}${windowText}">${seat.number}</div>`;
                } else {
                    // This is an aisle position - render aisle space
                    html += '<div class="aisle-space">|</div>';
                }
            }
        }
        
        html += '</div>';
    }
    
    html += `
                    </div>
                    
                    <!-- Layout info -->
                    <div style="text-align: center; margin-top: 15px; padding: 8px; background: rgba(255,255,255,0.8); border-radius: 8px; font-size: 12px; color: #6b7280;">
                        <strong>${layoutType.toUpperCase()}</strong> Layout • <strong>${seats.length}</strong> Seats • ${hasBackRow ? 'With' : 'Without'} Back Row
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML = html;
    console.log('✅ Bus layout rendered successfully!');
    return true;
}

// Main execution
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM Content Loaded - Starting seat layout rendering...');
    
    @if(isset($bus->seat_layout))
        const seatData = @json($bus->seat_layout);
        
        // Try to render immediately
        if (renderBusLayout(seatData, 'seatLayoutDisplay')) {
            console.log('✅ Seat layout rendered on DOM ready');
        } else {
            console.log('❌ Failed to render on DOM ready, will retry...');
            
            // Retry after a short delay
            setTimeout(function() {
                console.log('🔄 Retrying seat layout rendering...');
                renderBusLayout(seatData, 'seatLayoutDisplay');
            }, 500);
        }
    @else
        console.log('ℹ️ No seat layout data available');
        const container = document.getElementById('seatLayoutDisplay');
        if (container) {
            container.innerHTML = '<div style="text-align: center; padding: 2rem; color: #6b7280;">No seat layout configured</div>';
        }
    @endif
});
</script>
@endpush
