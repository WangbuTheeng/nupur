<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Bus;
use App\Services\SeatLayoutService;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing buses to use the new seat layout format
        $buses = Bus::all();
        $seatLayoutService = new SeatLayoutService();
        
        foreach ($buses as $bus) {
            $currentLayout = $bus->seat_layout;
            
            // Check if this bus already has the new format
            if (is_array($currentLayout) && isset($currentLayout['layout_type'])) {
                continue; // Already in new format
            }
            
            // Determine layout type based on total seats
            $layoutType = $this->determineLayoutType($bus->total_seats);
            
            // Generate new layout
            $newLayout = $seatLayoutService->generateSeatLayout(
                $bus->total_seats,
                $layoutType,
                true // Include back row by default
            );
            
            // Update the bus
            $bus->seat_layout = $newLayout;
            $bus->save();
            
            echo "Updated bus {$bus->bus_number} with {$layoutType} layout\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old format if needed
        $buses = Bus::all();
        
        foreach ($buses as $bus) {
            $currentLayout = $bus->seat_layout;
            
            if (is_array($currentLayout) && isset($currentLayout['layout_type'])) {
                // Convert back to old format
                $oldLayout = [
                    'rows' => $currentLayout['rows'] ?? 8,
                    'columns' => $currentLayout['columns'] ?? 4,
                    'aisle_position' => $currentLayout['aisle_position'] ?? 2,
                    'seats' => []
                ];
                
                // Convert seats to old format
                if (isset($currentLayout['seats'])) {
                    foreach ($currentLayout['seats'] as $seat) {
                        $oldLayout['seats'][] = [
                            'seat_number' => $seat['number'] ?? $seat['seat_number'] ?? 'N/A',
                            'row' => $seat['row'],
                            'column' => $seat['column'],
                            'is_available' => $seat['is_available'] ?? true,
                            'is_window' => $seat['is_window'] ?? false,
                            'is_aisle' => $seat['is_aisle'] ?? false,
                        ];
                    }
                }
                
                $bus->seat_layout = $oldLayout;
                $bus->save();
            }
        }
    }
    
    /**
     * Determine layout type based on total seats.
     */
    private function determineLayoutType($totalSeats)
    {
        if ($totalSeats <= 29) {
            return '2x1'; // Compact layout for smaller buses
        } elseif ($totalSeats >= 35) {
            return '3x2'; // Large layout for bigger buses
        } else {
            return '2x2'; // Standard layout for most buses
        }
    }
};
