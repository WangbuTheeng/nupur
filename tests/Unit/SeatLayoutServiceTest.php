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
        $layout = $this->seatLayoutService->generateSeatLayout(31, '2x2', true);

        $this->assertEquals('2x2', $layout['layout_type']);
        $this->assertEquals(31, $layout['total_seats']);
        $this->assertTrue($layout['has_back_row']);
        $this->assertEquals(5, $layout['back_row_seats']);
        $this->assertCount(31, $layout['seats']);

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
        $layout = $this->seatLayoutService->generateSeatLayout(39, '3x2', true);

        $this->assertEquals('3x2', $layout['layout_type']);
        $this->assertEquals(39, $layout['total_seats']);
        $this->assertEquals(6, $layout['back_row_seats']);
        $this->assertCount(39, $layout['seats']);
    }

    /** @test */
    public function it_generates_layout_without_back_row()
    {
        $layout = $this->seatLayoutService->generateSeatLayout(28, '2x2', false);

        $this->assertFalse($layout['has_back_row']);
        $this->assertEquals(0, $layout['back_row_seats']);
        $this->assertCount(28, $layout['seats']);
    }

    /** @test */
    public function it_validates_layout_configuration()
    {
        // Valid configuration
        $errors = $this->seatLayoutService->validateLayout(31, '2x2', true);
        $this->assertEmpty($errors);

        // Invalid seat count
        $errors = $this->seatLayoutService->validateLayout(5, '2x2', true);
        $this->assertNotEmpty($errors);

        // Invalid layout type
        $errors = $this->seatLayoutService->validateLayout(31, 'invalid', true);
        $this->assertNotEmpty($errors);
    }

    /** @test */
    public function it_identifies_window_and_aisle_seats_correctly()
    {
        $layout = $this->seatLayoutService->generateSeatLayout(31, '2x2', true);

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
        $layout = $this->seatLayoutService->generateSeatLayout(31, '2x2', true);

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
        $this->assertContains(31, $recommendations['2x2']);
        $this->assertContains(39, $recommendations['3x2']);
    }
}
