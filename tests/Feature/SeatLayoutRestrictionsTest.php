<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\SeatLayoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeatLayoutRestrictionsTest extends TestCase
{
    use RefreshDatabase;

    protected $seatLayoutService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seatLayoutService = new SeatLayoutService();
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
    public function it_generates_valid_seat_counts_list()
    {
        $validCounts2x2 = SeatLayoutService::getValidSeatCounts('2x2');
        $this->assertContains(25, $validCounts2x2);
        $this->assertContains(29, $validCounts2x2);
        $this->assertContains(33, $validCounts2x2);
        $this->assertNotContains(26, $validCounts2x2);
        $this->assertNotContains(27, $validCounts2x2);

        $validCounts2x1 = SeatLayoutService::getValidSeatCounts('2x1');
        $this->assertContains(22, $validCounts2x1);
        $this->assertContains(25, $validCounts2x1);
        $this->assertContains(28, $validCounts2x1);
        $this->assertNotContains(23, $validCounts2x1);
        $this->assertNotContains(24, $validCounts2x1);

        $validCounts3x2 = SeatLayoutService::getValidSeatCounts('3x2');
        $this->assertContains(26, $validCounts3x2);
        $this->assertContains(31, $validCounts3x2);
        $this->assertContains(36, $validCounts3x2);
        $this->assertNotContains(27, $validCounts3x2);
        $this->assertNotContains(28, $validCounts3x2);
    }

    /** @test */
    public function it_validates_layout_with_seat_count_restrictions()
    {
        // Valid configurations
        $errors = $this->seatLayoutService->validateLayout(25, '2x2', true);
        $this->assertEmpty($errors);

        $errors = $this->seatLayoutService->validateLayout(22, '2x1', true);
        $this->assertEmpty($errors);

        $errors = $this->seatLayoutService->validateLayout(26, '3x2', true);
        $this->assertEmpty($errors);

        // Invalid configurations
        $errors = $this->seatLayoutService->validateLayout(26, '2x2', true);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid seat count for 2x2 layout', $errors[0]);

        $errors = $this->seatLayoutService->validateLayout(23, '2x1', true);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid seat count for 2x1 layout', $errors[0]);

        $errors = $this->seatLayoutService->validateLayout(27, '3x2', true);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Invalid seat count for 3x2 layout', $errors[0]);
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

    /** @test */
    public function it_returns_recommended_seat_counts()
    {
        $recommendations = SeatLayoutService::getRecommendedSeatCounts();
        
        $this->assertArrayHasKey('2x2', $recommendations);
        $this->assertArrayHasKey('2x1', $recommendations);
        $this->assertArrayHasKey('3x2', $recommendations);
        
        // Check that recommendations contain valid counts
        foreach ($recommendations['2x2'] as $count) {
            $this->assertTrue(SeatLayoutService::isValidSeatCount($count, '2x2'));
        }
        
        foreach ($recommendations['2x1'] as $count) {
            $this->assertTrue(SeatLayoutService::isValidSeatCount($count, '2x1'));
        }
        
        foreach ($recommendations['3x2'] as $count) {
            $this->assertTrue(SeatLayoutService::isValidSeatCount($count, '3x2'));
        }
    }
}
