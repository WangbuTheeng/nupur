<?php

namespace App\Services;

class SeatLayoutService
{
    // Layout type constants
    const LAYOUT_2X2 = '2x2';
    const LAYOUT_2X1 = '2x1';
    const LAYOUT_3X2 = '3x2';

    // Default configurations for different layouts
    const LAYOUT_CONFIGS = [
        self::LAYOUT_2X2 => [
            'left_seats' => 2,
            'right_seats' => 2,
            'total_per_row' => 4,
            'aisle_position' => 2,
            'back_row_seats' => 5,
        ],
        self::LAYOUT_2X1 => [
            'left_seats' => 2,
            'right_seats' => 1,
            'total_per_row' => 3,
            'aisle_position' => 2,
            'back_row_seats' => 4,
        ],
        self::LAYOUT_3X2 => [
            'left_seats' => 3,
            'right_seats' => 2,
            'total_per_row' => 5,
            'aisle_position' => 3,
            'back_row_seats' => 6,
        ],
    ];

    /**
     * Generate complete seat layout for a bus.
     */
    public function generateSeatLayout($totalSeats, $layoutType = self::LAYOUT_2X2, $hasBackRow = true)
    {
        $config = self::LAYOUT_CONFIGS[$layoutType] ?? self::LAYOUT_CONFIGS[self::LAYOUT_2X2];
        
        // Calculate seats distribution
        $backRowSeats = $hasBackRow ? $config['back_row_seats'] : 0;
        $regularSeats = $totalSeats - $backRowSeats;
        $seatsPerRow = $config['total_per_row'];
        $regularRows = ceil($regularSeats / $seatsPerRow);
        
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

        // Generate regular row seats
        $seatNumber = 1;
        for ($row = 1; $row <= $regularRows; $row++) {
            $rowSeats = $this->generateRowSeats($row, $config, $seatNumber, $regularSeats);
            $layout['seats'] = array_merge($layout['seats'], $rowSeats);
        }

        // Generate back row seats if enabled
        if ($hasBackRow && $backRowSeats > 0) {
            $backRowSeats = $this->generateBackRowSeats($regularRows + 1, $config['back_row_seats'], $seatNumber);
            $layout['seats'] = array_merge($layout['seats'], $backRowSeats);
        }

        return $layout;
    }

    /**
     * Generate seats for a regular row.
     */
    private function generateRowSeats($rowNumber, $config, &$seatNumber, $remainingSeats)
    {
        $seats = [];
        $rowLetter = chr(64 + $rowNumber); // A, B, C, etc.
        $seatsInThisRow = min($config['total_per_row'], $remainingSeats - ($seatNumber - 1));
        
        $leftSeats = min($config['left_seats'], $seatsInThisRow);
        $rightSeats = max(0, $seatsInThisRow - $leftSeats);

        // Generate left side seats
        for ($i = 1; $i <= $leftSeats; $i++) {
            $seats[] = [
                'number' => $rowLetter . $i,
                'row' => $rowNumber,
                'column' => $i,
                'type' => 'regular',
                'is_window' => $i === 1,
                'is_aisle' => $i === $leftSeats,
                'is_available' => true,
                'side' => 'left',
            ];
            $seatNumber++;
        }

        // Generate right side seats
        $rightStartColumn = $config['aisle_position'] + 1;
        for ($i = 1; $i <= $rightSeats; $i++) {
            $seats[] = [
                'number' => $rowLetter . ($leftSeats + $i),
                'row' => $rowNumber,
                'column' => $rightStartColumn + $i - 1,
                'type' => 'regular',
                'is_window' => $i === $rightSeats,
                'is_aisle' => $i === 1,
                'is_available' => true,
                'side' => 'right',
            ];
            $seatNumber++;
        }

        return $seats;
    }

    /**
     * Generate back row seats.
     */
    private function generateBackRowSeats($rowNumber, $backRowSeats, &$seatNumber)
    {
        $seats = [];
        $rowLetter = chr(64 + $rowNumber);

        for ($i = 1; $i <= $backRowSeats; $i++) {
            $seats[] = [
                'number' => $rowLetter . $i,
                'row' => $rowNumber,
                'column' => $i,
                'type' => 'back_row',
                'is_window' => $i === 1 || $i === $backRowSeats,
                'is_aisle' => false,
                'is_available' => true,
                'side' => 'back',
            ];
            $seatNumber++;
        }

        return $seats;
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

        // Check if seat count is feasible for layout
        $config = self::LAYOUT_CONFIGS[$layoutType] ?? self::LAYOUT_CONFIGS[self::LAYOUT_2X2];
        $minSeats = $config['total_per_row'] + ($hasBackRow ? $config['back_row_seats'] : 0);
        
        if ($totalSeats < $minSeats) {
            $errors[] = "Minimum {$minSeats} seats required for {$layoutType} layout";
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
        return [
            self::LAYOUT_2X2 => [27, 31, 35],
            self::LAYOUT_2X1 => [21, 25, 29],
            self::LAYOUT_3X2 => [35, 39, 45],
        ];
    }
}
