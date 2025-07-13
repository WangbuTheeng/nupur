<?php

namespace App\Services;

class SeatLayoutService
{
    // Layout type constants
    const LAYOUT_2X2 = '2x2';
    const LAYOUT_2X1 = '2x1';
    const LAYOUT_3X2 = '3x2';

    // Default configurations for different layouts with specific seat numbering patterns
    const LAYOUT_CONFIGS = [
        self::LAYOUT_2X2 => [
            'left_seats' => 2,
            'right_seats' => 2,
            'total_per_row' => 4,
            'aisle_position' => 3, // Between columns 2 and 4
            'back_row_seats' => 5, // Always 5 seats in back row
            'back_row_config' => ['left' => 2, 'right' => 3],
        ],
        self::LAYOUT_2X1 => [
            'left_seats' => 2,
            'right_seats' => 1,
            'total_per_row' => 3,
            'aisle_position' => 3, // Between columns 2 and 4
            'back_row_seats' => 4, // Always 4 seats in back row
            'back_row_config' => ['left' => 2, 'right' => 2],
        ],
        self::LAYOUT_3X2 => [
            'left_seats' => 3,
            'right_seats' => 2,
            'total_per_row' => 5,
            'aisle_position' => 4, // Between columns 3 and 5
            'back_row_seats' => 5, // Always 5 seats in back row (not 6)
            'back_row_config' => ['left' => 2, 'right' => 3],
        ],
    ];

    /**
     * Generate complete seat layout for a bus.
     * Uses the same logic as the bus creation preview.
     */
    public function generateSeatLayout($totalSeats, $layoutType = self::LAYOUT_2X2, $hasBackRow = true)
    {
        $config = self::LAYOUT_CONFIGS[$layoutType] ?? self::LAYOUT_CONFIGS[self::LAYOUT_2X2];

        // Calculate seats distribution
        $seatsPerRow = $config['total_per_row'];

        if ($hasBackRow) {
            // Use fixed back row seat count based on layout type
            $backRowSeats = $config['back_row_seats'];
            $regularSeats = $totalSeats - $backRowSeats;
            $regularRows = ceil($regularSeats / $seatsPerRow);
        } else {
            $backRowSeats = 0;
            $regularSeats = $totalSeats;
            $regularRows = ceil($regularSeats / $seatsPerRow);
        }

        // Generate layout structure
        $layout = [
            'layout_type' => $layoutType,
            'total_seats' => $totalSeats,
            'rows' => $regularRows + ($hasBackRow ? 1 : 0),
            'columns' => $this->getMaxColumns($layoutType),
            'aisle_position' => $config['aisle_position'],
            'has_back_row' => $hasBackRow,
            'back_row_seats' => $backRowSeats,
            'driver_seat' => [
                'position' => 'top-right',
                'row' => 0,
                'column' => $this->getMaxColumns($layoutType),
            ],
            'door' => [
                'position' => 'top-left',
                'row' => 0,
                'column' => 0,
            ],
            'seats' => [],
        ];

        // Generate seats using side-grouped numbering approach
        $seatNumber = 1;

        // Generate regular row seats - group by side for proper numbering
        for ($row = 1; $row <= $regularRows; $row++) {
            // First, generate left side seats (1, 2)
            for ($col = 1; $col <= $config['left_seats']; $col++) {
                // Stop if we've generated enough regular seats
                if ($seatNumber > $regularSeats) {
                    break 2; // Break out of both loops
                }

                $layout['seats'][] = [
                    'number' => $seatNumber,
                    'row' => $row,
                    'column' => $col,
                    'type' => 'regular',
                    'is_window' => ($col == 1),
                    'is_aisle' => ($col == $config['left_seats']),
                    'is_available' => true,
                    'side' => 'left'
                ];
                $seatNumber++;
            }

            // Then, generate right side seats (3, 4)
            for ($col = 1; $col <= $config['right_seats']; $col++) {
                // Stop if we've generated enough regular seats
                if ($seatNumber > $regularSeats) {
                    break 2; // Break out of both loops
                }

                $actualColumn = $config['left_seats'] + 1 + $col; // Skip aisle position

                $layout['seats'][] = [
                    'number' => $seatNumber,
                    'row' => $row,
                    'column' => $actualColumn,
                    'type' => 'regular',
                    'is_window' => ($col == $config['right_seats']),
                    'is_aisle' => ($col == 1),
                    'is_available' => true,
                    'side' => 'right'
                ];
                $seatNumber++;
            }
        }

        // Generate back row seats if enabled (continuous across full width)
        if ($hasBackRow && $backRowSeats > 0) {
            $backRowNumber = $regularRows + 1;

            // Generate back row seats continuously across the available width
            for ($col = 1; $col <= $backRowSeats; $col++) {
                $layout['seats'][] = [
                    'number' => $seatNumber,
                    'row' => $backRowNumber,
                    'column' => $col,
                    'type' => 'back_row',
                    'is_window' => ($col == 1 || $col == $backRowSeats),
                    'is_aisle' => false,
                    'is_available' => true,
                    'side' => 'back'
                ];
                $seatNumber++;
            }
        }

        return $layout;
    }



    /**
     * Get maximum columns for layout type.
     */
    private function getMaxColumns($layoutType)
    {
        $config = self::LAYOUT_CONFIGS[$layoutType] ?? self::LAYOUT_CONFIGS[self::LAYOUT_2X2];
        return $config['left_seats'] + $config['right_seats'] + 1; // +1 for aisle
    }

    /**
     * Get valid seat counts for each layout type.
     * Based on seats per row + back row configuration.
     */
    const VALID_SEAT_COUNTS = [
        self::LAYOUT_2X2 => [
            'min' => 9,  // Minimum: 1 row (4 seats) + back row (5 seats)
            'seats_per_row' => 4,
            'back_row_seats' => 5,
            'max' => 60,
        ],
        self::LAYOUT_2X1 => [
            'min' => 7,  // Minimum: 1 row (3 seats) + back row (4 seats)
            'seats_per_row' => 3,
            'back_row_seats' => 4,
            'max' => 60,
        ],
        self::LAYOUT_3X2 => [
            'min' => 11, // Minimum: 1 row (5 seats) + back row (6 seats)
            'seats_per_row' => 5,
            'back_row_seats' => 6,
            'max' => 60,
        ],
    ];

    /**
     * Get valid seat counts for a specific layout type.
     */
    public static function getValidSeatCounts($layoutType)
    {
        if (!array_key_exists($layoutType, self::VALID_SEAT_COUNTS)) {
            return [];
        }

        $config = self::VALID_SEAT_COUNTS[$layoutType];
        $validCounts = [];

        // Generate valid counts: min seats to max seats
        // Valid counts are: (rows * seats_per_row) + back_row_seats
        $seatsPerRow = $config['seats_per_row'];
        $backRowSeats = $config['back_row_seats'];

        for ($rows = 1; $rows <= 15; $rows++) { // Max 15 rows
            $totalSeats = ($rows * $seatsPerRow) + $backRowSeats;
            if ($totalSeats >= $config['min'] && $totalSeats <= $config['max']) {
                $validCounts[] = $totalSeats;
            }
        }

        return $validCounts;
    }

    /**
     * Check if a seat count is valid for a layout type.
     */
    public static function isValidSeatCount($totalSeats, $layoutType)
    {
        if (!array_key_exists($layoutType, self::VALID_SEAT_COUNTS)) {
            return false;
        }

        $config = self::VALID_SEAT_COUNTS[$layoutType];

        // Check basic bounds
        if ($totalSeats < $config['min'] || $totalSeats > $config['max']) {
            return false;
        }

        // Check if the seat count can be achieved with the layout
        // Formula: totalSeats = (rows * seats_per_row) + back_row_seats
        $seatsPerRow = $config['seats_per_row'];
        $backRowSeats = $config['back_row_seats'];

        // Calculate required regular seats (excluding back row)
        $regularSeats = $totalSeats - $backRowSeats;

        // Check if regular seats can be evenly divided into rows
        return $regularSeats > 0 && ($regularSeats % $seatsPerRow) === 0;
    }

    /**
     * Validate seat layout configuration.
     */
    public function validateLayout($totalSeats, $layoutType, $hasBackRow = true)
    {
        $errors = [];

        // Check if layout type is valid
        if (!array_key_exists($layoutType, self::LAYOUT_CONFIGS)) {
            $errors[] = 'Invalid layout type';
        }

        // Check seat count limits
        if ($totalSeats < 10 || $totalSeats > 60) {
            $errors[] = 'Total seats must be between 10 and 60';
        }

        // Check if seat count is valid for the specific layout type
        if (!self::isValidSeatCount($totalSeats, $layoutType)) {
            $validCounts = self::getValidSeatCounts($layoutType);
            $validCountsStr = implode(', ', array_slice($validCounts, 0, 10));
            if (count($validCounts) > 10) {
                $validCountsStr .= '...';
            }
            $errors[] = "Invalid seat count for {$layoutType} layout. Valid counts: {$validCountsStr}";
        }

        return $errors;
    }

    /**
     * Get available layout types.
     */
    public static function getLayoutTypes()
    {
        return [
            self::LAYOUT_2X2 => '2x2 (Standard)',
            self::LAYOUT_2X1 => '2x1 (Compact)',
            self::LAYOUT_3X2 => '3x2 (Large)',
        ];
    }

    /**
     * Get recommended seat counts for layout types.
     */
    public static function getRecommendedSeatCounts()
    {
        $recommendations = [];

        foreach (array_keys(self::LAYOUT_CONFIGS) as $layoutType) {
            $validCounts = self::getValidSeatCounts($layoutType);
            // Get first 5 valid counts as recommendations
            $recommendations[$layoutType] = array_slice($validCounts, 0, 5);
        }

        return $recommendations;
    }
}
