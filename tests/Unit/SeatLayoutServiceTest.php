<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\SeatLayoutService;

class SeatLayoutServiceTest extends TestCase
{
    protected $seatLayoutService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seatLayoutService = new SeatLayoutService();
    }

    /** @test */
    public function it_generates_2x2_layout_correctly()
    {
        $layout = $this->seatLayoutService->generateSeatLayout(29, '2x2', true);

        $this->assertEquals('2x2', $layout['layout_type']);
        $this->assertEquals(29, $layout['total_seats']);
        $this->assertTrue($layout['has_back_row']);
        $this->assertEquals(5, $layout['back_row_seats']);
        $this->assertCount(29, $layout['seats']);

        // Check driver seat and door positions
        $this->assertEquals('top-right', $layout['driver_seat']['position']);
        $this->assertEquals('top-left', $layout['door']['position']);

        // Verify seat numbering
        $firstSeat = $layout['seats'][0];
        $this->assertEquals('A1', $firstSeat['number']);
        $this->assertEquals(1, $firstSeat['row']);
        $this->assertEquals(1, $firstSeat['column']);
        $this->assertTrue($firstSeat['is_window']);
    }

    /** @test */
    public function it_generates_2x1_layout_correctly()
    {
        $layout = $this->seatLayoutService->generateSeatLayout(25, '2x1', true);

        $this->assertEquals('2x1', $layout['layout_type']);
        $this->assertEquals(25, $layout['total_seats']);
        $this->assertEquals(4, $layout['back_row_seats']);
        $this->assertCount(25, $layout['seats']);
    }

    /** @test */
    public function it_generates_3x2_layout_correctly()
    {
        $layout = $this->seatLayoutService->generateSeatLayout(36, '3x2', true);

        $this->assertEquals('3x2', $layout['layout_type']);
        $this->assertEquals(36, $layout['total_seats']);
        $this->assertEquals(6, $layout['back_row_seats']);
        $this->assertCount(36, $layout['seats']);
    }

    /** @test */
    public function it_generates_layout_without_back_row()
    {
        // For 2x2 without back row, we need to use a valid count
        // Let's use 25 total seats, which would be 25 regular seats (no back row)
        $layout = $this->seatLayoutService->generateSeatLayout(25, '2x2', false);

        $this->assertFalse($layout['has_back_row']);
        $this->assertEquals(0, $layout['back_row_seats']);
        $this->assertCount(25, $layout['seats']);
    }

    /** @test */
    public function it_validates_layout_configuration()
    {
        // Valid configuration - use valid seat count for 2x2 layout
        $errors = $this->seatLayoutService->validateLayout(29, '2x2', true);
        $this->assertEmpty($errors);

        // Invalid seat count (too low)
        $errors = $this->seatLayoutService->validateLayout(5, '2x2', true);
        $this->assertNotEmpty($errors);

        // Invalid seat count (not matching pattern)
        $errors = $this->seatLayoutService->validateLayout(31, '2x2', true);
        $this->assertNotEmpty($errors);

        // Invalid layout type
        $errors = $this->seatLayoutService->validateLayout(29, 'invalid', true);
        $this->assertNotEmpty($errors);
    }

    /** @test */
    public function it_identifies_window_and_aisle_seats_correctly()
    {
        $layout = $this->seatLayoutService->generateSeatLayout(29, '2x2', true);

        // Find first row seats
        $firstRowSeats = array_filter($layout['seats'], function($seat) {
            return $seat['row'] === 1;
        });

        $leftWindowSeat = array_filter($firstRowSeats, function($seat) {
            return $seat['column'] === 1;
        });
        $leftWindowSeat = array_values($leftWindowSeat)[0];

        $this->assertTrue($leftWindowSeat['is_window']);
        $this->assertEquals('left', $leftWindowSeat['side']);

        // Check aisle seats
        $leftAisleSeat = array_filter($firstRowSeats, function($seat) {
            return $seat['column'] === 2;
        });
        $leftAisleSeat = array_values($leftAisleSeat)[0];

        $this->assertTrue($leftAisleSeat['is_aisle']);
    }

    /** @test */
    public function it_generates_back_row_correctly()
    {
        $layout = $this->seatLayoutService->generateSeatLayout(29, '2x2', true);

        // Find back row seats
        $backRowSeats = array_filter($layout['seats'], function($seat) {
            return $seat['type'] === 'back_row';
        });

        $this->assertCount(5, $backRowSeats);

        foreach ($backRowSeats as $seat) {
            $this->assertEquals('back_row', $seat['type']);
            $this->assertEquals('back', $seat['side']);
            $this->assertEquals($layout['rows'], $seat['row']);
        }
    }

    /** @test */
    public function it_provides_correct_layout_types()
    {
        $layoutTypes = SeatLayoutService::getLayoutTypes();

        $this->assertArrayHasKey('2x2', $layoutTypes);
        $this->assertArrayHasKey('2x1', $layoutTypes);
        $this->assertArrayHasKey('3x2', $layoutTypes);
        $this->assertEquals('2x2 (Standard)', $layoutTypes['2x2']);
    }

    /** @test */
    public function it_provides_recommended_seat_counts()
    {
        $recommendations = SeatLayoutService::getRecommendedSeatCounts();

        $this->assertArrayHasKey('2x2', $recommendations);
        $this->assertArrayHasKey('2x1', $recommendations);
        $this->assertArrayHasKey('3x2', $recommendations);
        $this->assertContains(25, $recommendations['2x2']);
        $this->assertContains(22, $recommendations['2x1']);
        $this->assertContains(26, $recommendations['3x2']);
    }

    /** @test */
    public function it_validates_2x2_layout_seat_counts()
    {
        // Valid seat counts for 2x2 layout: 25, 29, 33, 37, 41, etc.
        $validCounts = [25, 29, 33, 37, 41, 45, 49, 53, 57];
        $invalidCounts = [24, 26, 27, 28, 30, 31, 32, 34, 35, 36, 38, 39, 40, 42, 43, 44];

        foreach ($validCounts as $count) {
            $this->assertTrue(
                SeatLayoutService::isValidSeatCount($count, '2x2'),
                "Seat count {$count} should be valid for 2x2 layout"
            );
        }

        foreach ($invalidCounts as $count) {
            $this->assertFalse(
                SeatLayoutService::isValidSeatCount($count, '2x2'),
                "Seat count {$count} should be invalid for 2x2 layout"
            );
        }
    }

    /** @test */
    public function it_validates_2x1_layout_seat_counts()
    {
        // Valid seat counts for 2x1 layout: 22, 25, 28, 31, 34, etc.
        $validCounts = [22, 25, 28, 31, 34, 37, 40, 43, 46, 49];
        $invalidCounts = [21, 23, 24, 26, 27, 29, 30, 32, 33, 35, 36, 38, 39, 41, 42];

        foreach ($validCounts as $count) {
            $this->assertTrue(
                SeatLayoutService::isValidSeatCount($count, '2x1'),
                "Seat count {$count} should be valid for 2x1 layout"
            );
        }

        foreach ($invalidCounts as $count) {
            $this->assertFalse(
                SeatLayoutService::isValidSeatCount($count, '2x1'),
                "Seat count {$count} should be invalid for 2x1 layout"
            );
        }
    }

    /** @test */
    public function it_validates_3x2_layout_seat_counts()
    {
        // Valid seat counts for 3x2 layout: 26, 31, 36, 41, 46, etc.
        $validCounts = [26, 31, 36, 41, 46, 51, 56];
        $invalidCounts = [25, 27, 28, 29, 30, 32, 33, 34, 35, 37, 38, 39, 40, 42, 43, 44, 45];

        foreach ($validCounts as $count) {
            $this->assertTrue(
                SeatLayoutService::isValidSeatCount($count, '3x2'),
                "Seat count {$count} should be valid for 3x2 layout"
            );
        }

        foreach ($invalidCounts as $count) {
            $this->assertFalse(
                SeatLayoutService::isValidSeatCount($count, '3x2'),
                "Seat count {$count} should be invalid for 3x2 layout"
            );
        }
    }

    /** @test */
    public function it_generates_seat_layout_with_gaps()
    {
        $layout = $this->seatLayoutService->generateSeatLayout(25, '2x2', true);

        $this->assertEquals('2x2', $layout['layout_type']);
        $this->assertEquals(25, $layout['total_seats']);
        $this->assertTrue($layout['has_back_row']);

        // Check that regular seats have gap information
        $regularSeats = array_filter($layout['seats'], function($seat) {
            return $seat['type'] === 'regular';
        });

        foreach ($regularSeats as $seat) {
            $this->assertArrayHasKey('has_gap_after', $seat);
        }

        // Check that back row seats don't have gaps
        $backRowSeats = array_filter($layout['seats'], function($seat) {
            return $seat['type'] === 'back_row';
        });

        foreach ($backRowSeats as $seat) {
            $this->assertArrayHasKey('has_gap_after', $seat);
            $this->assertFalse($seat['has_gap_after'], 'Back row seats should not have gaps');
        }
    }
}
