<?php

/**
 * BookNGO Seat Layout Setup Script
 * 
 * This script helps set up the new dynamic seat layout system
 * Run this after installing the seat layout updates
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\Bus;
use App\Models\BusType;
use App\Services\SeatLayoutService;

class SeatLayoutSetup
{
    private $seatLayoutService;
    
    public function __construct()
    {
        $this->seatLayoutService = new SeatLayoutService();
    }
    
    /**
     * Run the complete setup process
     */
    public function run()
    {
        echo "🚌 BookNGO Seat Layout Setup\n";
        echo "============================\n\n";
        
        $this->checkRequirements();
        $this->updateBusTypes();
        $this->updateExistingBuses();
        $this->generateSampleLayouts();
        $this->runValidation();
        
        echo "\n✅ Setup completed successfully!\n";
        echo "📖 Check docs/SEAT_LAYOUT_SYSTEM.md for detailed documentation\n";
        echo "🎯 Visit /demo/seat-layouts to see the system in action\n\n";
    }
    
    /**
     * Check system requirements
     */
    private function checkRequirements()
    {
        echo "🔍 Checking requirements...\n";
        
        // Check if migration has run
        try {
            $bus = Bus::first();
            if ($bus && isset($bus->seat_layout['layout_type'])) {
                echo "   ✅ Migration completed\n";
            } else {
                echo "   ⚠️  Migration may not have run completely\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Database connection issue: " . $e->getMessage() . "\n";
            exit(1);
        }
        
        // Check if service class exists
        if (class_exists('App\Services\SeatLayoutService')) {
            echo "   ✅ SeatLayoutService available\n";
        } else {
            echo "   ❌ SeatLayoutService not found\n";
            exit(1);
        }
        
        echo "\n";
    }
    
    /**
     * Update bus types with new layout configurations
     */
    private function updateBusTypes()
    {
        echo "🚌 Updating bus types...\n";
        
        $busTypes = BusType::all();
        
        foreach ($busTypes as $busType) {
            $currentLayout = $busType->seat_layout;
            
            // Determine best layout type based on total seats
            $layoutType = $this->determineLayoutType($busType->total_seats);
            
            // Generate new layout configuration
            $newLayout = [
                'layout_type' => $layoutType,
                'rows' => $this->calculateRows($busType->total_seats, $layoutType),
                'columns' => $this->getColumnsForLayout($layoutType),
                'aisle_position' => $this->getAislePosition($layoutType),
                'has_back_row' => true,
                'back_row_seats' => $this->getBackRowSeats($layoutType)
            ];
            
            $busType->seat_layout = $newLayout;
            $busType->save();
            
            echo "   ✅ Updated {$busType->name} ({$layoutType} layout)\n";
        }
        
        echo "\n";
    }
    
    /**
     * Update existing buses with new layouts
     */
    private function updateExistingBuses()
    {
        echo "🔧 Updating existing buses...\n";
        
        $buses = Bus::all();
        $updated = 0;
        $skipped = 0;
        
        foreach ($buses as $bus) {
            $currentLayout = $bus->seat_layout;
            
            // Check if already in new format
            if (is_array($currentLayout) && isset($currentLayout['layout_type'])) {
                $skipped++;
                continue;
            }
            
            // Generate new layout
            $layoutType = $this->determineLayoutType($bus->total_seats);
            $newLayout = $this->seatLayoutService->generateSeatLayout(
                $bus->total_seats,
                $layoutType,
                true
            );
            
            $bus->seat_layout = $newLayout;
            $bus->save();
            
            $updated++;
            echo "   ✅ Updated {$bus->bus_number} ({$layoutType}, {$bus->total_seats} seats)\n";
        }
        
        echo "   📊 Updated: {$updated}, Skipped: {$skipped}\n\n";
    }
    
    /**
     * Generate sample layouts for demonstration
     */
    private function generateSampleLayouts()
    {
        echo "🎨 Generating sample layouts...\n";
        
        $samples = [
            ['seats' => 27, 'type' => '2x2', 'name' => 'Standard City Bus'],
            ['seats' => 25, 'type' => '2x1', 'name' => 'Compact Rural Bus'],
            ['seats' => 39, 'type' => '3x2', 'name' => 'Large Highway Bus'],
            ['seats' => 31, 'type' => '2x2', 'name' => 'Popular Configuration'],
        ];
        
        foreach ($samples as $sample) {
            $layout = $this->seatLayoutService->generateSeatLayout(
                $sample['seats'],
                $sample['type'],
                true
            );
            
            echo "   ✅ {$sample['name']}: {$sample['seats']} seats, {$sample['type']} layout\n";
            echo "      - Regular rows: " . ($layout['rows'] - 1) . "\n";
            echo "      - Back row seats: {$layout['back_row_seats']}\n";
        }
        
        echo "\n";
    }
    
    /**
     * Run validation on all layouts
     */
    private function runValidation()
    {
        echo "✅ Running validation...\n";
        
        $buses = Bus::all();
        $valid = 0;
        $invalid = 0;
        
        foreach ($buses as $bus) {
            $layout = $bus->seat_layout;
            
            if (!is_array($layout) || !isset($layout['layout_type'])) {
                echo "   ❌ {$bus->bus_number}: Invalid layout format\n";
                $invalid++;
                continue;
            }
            
            // Validate seat count
            $expectedSeats = count($layout['seats'] ?? []);
            if ($expectedSeats !== $bus->total_seats) {
                echo "   ⚠️  {$bus->bus_number}: Seat count mismatch ({$expectedSeats} vs {$bus->total_seats})\n";
                $invalid++;
                continue;
            }
            
            // Validate layout structure
            $errors = $this->seatLayoutService->validateLayout(
                $bus->total_seats,
                $layout['layout_type'],
                $layout['has_back_row'] ?? true
            );
            
            if (!empty($errors)) {
                echo "   ❌ {$bus->bus_number}: " . implode(', ', $errors) . "\n";
                $invalid++;
                continue;
            }
            
            $valid++;
        }
        
        echo "   📊 Valid: {$valid}, Invalid: {$invalid}\n";
        
        if ($invalid > 0) {
            echo "   ⚠️  Some buses have validation issues. Please review and fix.\n";
        }
        
        echo "\n";
    }
    
    /**
     * Determine optimal layout type based on seat count
     */
    private function determineLayoutType($totalSeats)
    {
        if ($totalSeats <= 29) {
            return '2x1';
        } elseif ($totalSeats >= 35) {
            return '3x2';
        } else {
            return '2x2';
        }
    }
    
    /**
     * Calculate number of rows for layout
     */
    private function calculateRows($totalSeats, $layoutType)
    {
        $configs = [
            '2x2' => ['per_row' => 4, 'back_row' => 5],
            '2x1' => ['per_row' => 3, 'back_row' => 4],
            '3x2' => ['per_row' => 5, 'back_row' => 6],
        ];
        
        $config = $configs[$layoutType];
        $regularSeats = $totalSeats - $config['back_row'];
        $regularRows = ceil($regularSeats / $config['per_row']);
        
        return $regularRows + 1; // +1 for back row
    }
    
    /**
     * Get column count for layout type
     */
    private function getColumnsForLayout($layoutType)
    {
        $columns = [
            '2x2' => 5, // 2 + aisle + 2
            '2x1' => 4, // 2 + aisle + 1
            '3x2' => 6, // 3 + aisle + 2
        ];
        
        return $columns[$layoutType] ?? 5;
    }
    
    /**
     * Get aisle position for layout type
     */
    private function getAislePosition($layoutType)
    {
        $positions = [
            '2x2' => 2,
            '2x1' => 2,
            '3x2' => 3,
        ];
        
        return $positions[$layoutType] ?? 2;
    }
    
    /**
     * Get back row seat count for layout type
     */
    private function getBackRowSeats($layoutType)
    {
        $backRowSeats = [
            '2x2' => 5,
            '2x1' => 4,
            '3x2' => 6,
        ];
        
        return $backRowSeats[$layoutType] ?? 5;
    }
}

// Run the setup if called directly
if (php_sapi_name() === 'cli') {
    $setup = new SeatLayoutSetup();
    $setup->run();
}
