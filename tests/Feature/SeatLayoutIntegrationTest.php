<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bus;
use App\Models\BusType;
use App\Models\Schedule;
use App\Models\Route;
use App\Services\SeatLayoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SeatLayoutIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $operator;
    protected $busType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test operator
        $this->operator = User::factory()->create([
            'role' => 'operator',
            'company_name' => 'Test Bus Company'
        ]);
        
        // Create test bus type
        $this->busType = BusType::create([
            'name' => 'AC Deluxe',
            'description' => 'Air conditioned deluxe bus',
            'total_seats' => 31,
            'seat_layout' => [
                'layout_type' => '2x2',
                'rows' => 7,
                'columns' => 5,
                'aisle_position' => 2,
                'has_back_row' => true,
                'back_row_seats' => 5
            ],
            'base_fare_multiplier' => 1.5,
            'is_active' => true
        ]);
    }

    /** @test */
    public function operator_can_create_bus_with_seat_layout()
    {
        $this->actingAs($this->operator);

        $response = $this->post(route('operator.buses.store'), [
            'bus_number' => 'TEST-001',
            'license_plate' => 'TEST-1234',
            'bus_type_id' => $this->busType->id,
            'model' => 'Test Model',
            'color' => 'Blue',
            'manufacture_year' => 2023,
            'total_seats' => 31,
            'layout_type' => '2x2',
            'has_back_row' => true,
            'amenities' => ['WiFi', 'AC'],
            'description' => 'Test bus'
        ]);

        $response->assertRedirect();
        
        $bus = Bus::where('bus_number', 'TEST-001')->first();
        $this->assertNotNull($bus);
        $this->assertEquals(31, $bus->total_seats);
        $this->assertEquals('2x2', $bus->seat_layout['layout_type']);
        $this->assertTrue($bus->seat_layout['has_back_row']);
        $this->assertCount(31, $bus->seat_layout['seats']);
    }

    /** @test */
    public function operator_can_update_bus_seat_layout()
    {
        $bus = Bus::create([
            'bus_number' => 'TEST-002',
            'operator_id' => $this->operator->id,
            'bus_type_id' => $this->busType->id,
            'license_plate' => 'TEST-5678',
            'model' => 'Test Model',
            'color' => 'Red',
            'manufacture_year' => 2023,
            'total_seats' => 31,
            'seat_layout' => (new SeatLayoutService())->generateSeatLayout(31, '2x2', true),
            'is_active' => true
        ]);

        $this->actingAs($this->operator);

        $response = $this->post(route('operator.buses.seat-layout.update', $bus), [
            'layout_type' => '2x1',
            'has_back_row' => false
        ]);

        $response->assertRedirect();
        
        $bus->refresh();
        $this->assertEquals('2x1', $bus->seat_layout['layout_type']);
        $this->assertFalse($bus->seat_layout['has_back_row']);
    }

    /** @test */
    public function seat_layout_preview_works()
    {
        $this->actingAs($this->operator);

        $response = $this->post(route('operator.buses.seat-layout.preview'), [
            'total_seats' => 25,
            'layout_type' => '2x1',
            'has_back_row' => true
        ]);

        $response->assertOk();
        $data = $response->json();
        
        $this->assertTrue($data['success']);
        $this->assertEquals('2x1', $data['layout']['layout_type']);
        $this->assertEquals(25, $data['layout']['total_seats']);
        $this->assertCount(25, $data['layout']['seats']);
    }

    /** @test */
    public function api_returns_seat_map_with_booking_status()
    {
        // Create bus with seat layout
        $bus = Bus::create([
            'bus_number' => 'TEST-003',
            'operator_id' => $this->operator->id,
            'bus_type_id' => $this->busType->id,
            'license_plate' => 'TEST-9999',
            'model' => 'Test Model',
            'color' => 'Green',
            'manufacture_year' => 2023,
            'total_seats' => 31,
            'seat_layout' => (new SeatLayoutService())->generateSeatLayout(31, '2x2', true),
            'is_active' => true
        ]);

        // Create route
        $route = Route::create([
            'name' => 'Test Route',
            'source_city' => 'City A',
            'destination_city' => 'City B',
            'distance_km' => 100,
            'base_fare' => 500,
            'estimated_duration' => '02:00:00',
            'is_active' => true
        ]);

        // Create schedule
        $schedule = Schedule::create([
            'bus_id' => $bus->id,
            'route_id' => $route->id,
            'operator_id' => $this->operator->id,
            'travel_date' => now()->addDay(),
            'departure_time' => '08:00:00',
            'arrival_time' => '10:00:00',
            'fare' => 500,
            'available_seats' => 31,
            'status' => 'scheduled'
        ]);

        $response = $this->get("/api/schedules/{$schedule->id}/seat-map");

        $response->assertOk();
        $data = $response->json();
        
        $this->assertTrue($data['success']);
        $this->assertEquals(31, $data['total_seats']);
        $this->assertEquals(31, $data['available_seats']);
        $this->assertArrayHasKey('seat_map', $data);
        $this->assertArrayHasKey('seats', $data['seat_map']);
    }

    /** @test */
    public function seat_layout_validation_works()
    {
        $service = new SeatLayoutService();

        // Valid configuration
        $errors = $service->validateLayout(31, '2x2', true);
        $this->assertEmpty($errors);

        // Invalid seat count
        $errors = $service->validateLayout(5, '2x2', true);
        $this->assertNotEmpty($errors);

        // Invalid layout type
        $errors = $service->validateLayout(31, 'invalid', true);
        $this->assertNotEmpty($errors);
    }

    /** @test */
    public function bus_show_page_displays_seat_layout()
    {
        $bus = Bus::create([
            'bus_number' => 'TEST-004',
            'operator_id' => $this->operator->id,
            'bus_type_id' => $this->busType->id,
            'license_plate' => 'TEST-1111',
            'model' => 'Test Model',
            'color' => 'Yellow',
            'manufacture_year' => 2023,
            'total_seats' => 31,
            'seat_layout' => (new SeatLayoutService())->generateSeatLayout(31, '2x2', true),
            'is_active' => true
        ]);

        $this->actingAs($this->operator);

        $response = $this->get(route('operator.buses.show', $bus));

        $response->assertOk();
        $response->assertSee('Seat Layout');
        $response->assertSee('2X2');
        $response->assertSee('31');
        $response->assertSee('Yes'); // Back row
    }

    /** @test */
    public function demo_page_loads_successfully()
    {
        $response = $this->get('/demo/seat-layouts');

        $response->assertOk();
        $response->assertSee('BookNGO Dynamic Seat Layout System');
        $response->assertSee('2x2 Standard Layout');
        $response->assertSee('2x1 Compact Layout');
        $response->assertSee('3x2 Large Layout');
    }

    /** @test */
    public function different_layout_types_generate_correctly()
    {
        $service = new SeatLayoutService();

        // Test 2x2 layout
        $layout2x2 = $service->generateSeatLayout(31, '2x2', true);
        $this->assertEquals('2x2', $layout2x2['layout_type']);
        $this->assertEquals(5, $layout2x2['back_row_seats']);
        $this->assertCount(31, $layout2x2['seats']);

        // Test 2x1 layout
        $layout2x1 = $service->generateSeatLayout(25, '2x1', true);
        $this->assertEquals('2x1', $layout2x1['layout_type']);
        $this->assertEquals(4, $layout2x1['back_row_seats']);
        $this->assertCount(25, $layout2x1['seats']);

        // Test 3x2 layout
        $layout3x2 = $service->generateSeatLayout(39, '3x2', true);
        $this->assertEquals('3x2', $layout3x2['layout_type']);
        $this->assertEquals(6, $layout3x2['back_row_seats']);
        $this->assertCount(39, $layout3x2['seats']);
    }

    /** @test */
    public function seat_numbering_is_correct()
    {
        $service = new SeatLayoutService();
        $layout = $service->generateSeatLayout(31, '2x2', true);

        // Check first seat
        $firstSeat = $layout['seats'][0];
        $this->assertEquals('A1', $firstSeat['number']);
        $this->assertEquals(1, $firstSeat['row']);
        $this->assertEquals(1, $firstSeat['column']);
        $this->assertTrue($firstSeat['is_window']);

        // Check that we have proper seat progression
        $seatNumbers = array_column($layout['seats'], 'number');
        $this->assertContains('A1', $seatNumbers);
        $this->assertContains('A2', $seatNumbers);
        $this->assertContains('B1', $seatNumbers);
        $this->assertContains('B2', $seatNumbers);

        // Check back row seats
        $backRowSeats = array_filter($layout['seats'], function($seat) {
            return $seat['type'] === 'back_row';
        });
        $this->assertCount(5, $backRowSeats);
    }
}
