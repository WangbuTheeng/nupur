<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\BusType;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BusController extends Controller
{
    /**
     * Display a listing of operator's buses.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->buses()->with(['busType', 'schedules' => function($q) {
            $q->where('travel_date', '>=', Carbon::today())->orderBy('travel_date');
        }]);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bus_number', 'like', "%{$search}%")
                  ->orWhere('license_plate', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Bus type filter
        if ($request->filled('bus_type')) {
            $query->where('bus_type_id', $request->bus_type);
        }

        $buses = $query->orderBy('created_at', 'desc')->paginate(12);

        // Get bus types for filter
        $busTypes = BusType::where('is_active', true)->get();

        // Statistics
        $stats = [
            'total' => Auth::user()->buses()->count(),
            'active' => Auth::user()->buses()->where('is_active', true)->count(),
            'inactive' => Auth::user()->buses()->where('is_active', false)->count(),
            'scheduled_today' => Auth::user()->schedules()->whereDate('travel_date', Carbon::today())->count(),
        ];

        return view('operator.buses.index', compact('buses', 'busTypes', 'stats'));
    }

    /**
     * Show the form for creating a new bus.
     */
    public function create()
    {
        $busTypes = BusType::where('is_active', true)->get();
        return view('operator.buses.create', compact('busTypes'));
    }

    /**
     * Store a newly created bus.
     */
    public function store(Request $request)
    {
        $request->validate([
            'bus_number' => 'required|string|max:50|unique:buses',
            'license_plate' => 'required|string|max:50|unique:buses',
            'bus_type_id' => 'required|exists:bus_types,id',
            'model' => 'required|string|max:100',
            'color' => 'required|string|max:50',
            'manufacture_year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'total_seats' => 'required|integer|min:10|max:100',
            'layout_type' => 'required|in:2x2,2x1,3x2',
            'has_back_row' => 'boolean',
            'amenities' => 'nullable|array',
            'description' => 'nullable|string|max:500',
        ]);

        // Validate seat count for the specific layout type
        $layoutType = $request->layout_type;
        $totalSeats = $request->total_seats;

        if (!\App\Services\SeatLayoutService::isValidSeatCount($totalSeats, $layoutType)) {
            $validCounts = \App\Services\SeatLayoutService::getValidSeatCounts($layoutType);
            $validCountsStr = implode(', ', array_slice($validCounts, 0, 10));
            if (count($validCounts) > 10) {
                $validCountsStr .= '...';
            }

            return back()->withErrors([
                'total_seats' => "Invalid seat count for {$layoutType} layout. Valid counts: {$validCountsStr}"
            ])->withInput();
        }

        $busType = BusType::findOrFail($request->bus_type_id);

        DB::beginTransaction();
        try {
            $bus = Bus::create([
                'bus_number' => $request->bus_number,
                'license_plate' => $request->license_plate,
                'operator_id' => Auth::id(),
                'bus_type_id' => $request->bus_type_id,
                'model' => $request->model,
                'color' => $request->color,
                'manufacture_year' => $request->manufacture_year,
                'total_seats' => $request->total_seats,
                'seat_layout' => $this->generateSeatLayout(
                    $request->total_seats,
                    $busType,
                    $request->layout_type,
                    $request->boolean('has_back_row', true)
                ),
                'amenities' => $request->amenities ?? [],
                'description' => $request->description,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('operator.buses.show', $bus)
                ->with('success', 'Bus created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create bus: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified bus.
     */
    public function show(Bus $bus)
    {
        // Ensure operator can only view their own buses
        if ($bus->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to bus.');
        }

        $bus->load(['busType', 'schedules' => function($q) {
            $q->with('route')->orderBy('travel_date', 'desc');
        }]);

        // Get recent bookings for this bus
        $recentBookings = $bus->bookings()
            ->with(['user', 'schedule.route'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get upcoming schedules
        $upcomingSchedules = $bus->schedules()
            ->with('route')
            ->where('travel_date', '>=', Carbon::today())
            ->orderBy('travel_date')
            ->limit(5)
            ->get();

        // Statistics
        $stats = [
            'total_schedules' => $bus->schedules()->count(),
            'upcoming_schedules' => $bus->schedules()->where('travel_date', '>=', Carbon::today())->count(),
            'total_bookings' => $bus->bookings()->count(),
            'monthly_revenue' => $bus->bookings()
                ->where('bookings.status', 'confirmed')
                ->whereMonth('bookings.created_at', Carbon::now()->month)
                ->sum('total_amount'),
        ];

        return view('operator.buses.show', compact('bus', 'recentBookings', 'upcomingSchedules', 'stats'));
    }

    /**
     * Show the form for editing the specified bus.
     */
    public function edit(Bus $bus)
    {
        // Ensure operator can only edit their own buses
        if ($bus->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to bus.');
        }

        $busTypes = BusType::where('is_active', true)->get();
        return view('operator.buses.edit', compact('bus', 'busTypes'));
    }

    /**
     * Update the specified bus.
     */
    public function update(Request $request, Bus $bus)
    {
        // Ensure operator can only update their own buses
        if ($bus->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to bus.');
        }

        $request->validate([
            'bus_number' => 'required|string|max:50|unique:buses,bus_number,' . $bus->id,
            'license_plate' => 'required|string|max:50|unique:buses,license_plate,' . $bus->id,
            'bus_type_id' => 'required|exists:bus_types,id',
            'model' => 'required|string|max:100',
            'color' => 'required|string|max:50',
            'manufacture_year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'total_seats' => 'required|integer|min:10|max:100',
            'layout_type' => 'nullable|in:2x2,2x1,3x2',
            'has_back_row' => 'boolean',
            'amenities' => 'nullable|array',
            'description' => 'nullable|string|max:500',
        ]);

        // Validate seat count for the specific layout type
        $layoutType = $request->layout_type ?? ($bus->seat_layout['layout_type'] ?? '2x2');
        $totalSeats = $request->total_seats;

        if (!\App\Services\SeatLayoutService::isValidSeatCount($totalSeats, $layoutType)) {
            $validCounts = \App\Services\SeatLayoutService::getValidSeatCounts($layoutType);
            $validCountsStr = implode(', ', array_slice($validCounts, 0, 10));
            if (count($validCounts) > 10) {
                $validCountsStr .= '...';
            }

            return back()->withErrors([
                'total_seats' => "Invalid seat count for {$layoutType} layout. Valid counts: {$validCountsStr}"
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            // If seat count or layout changed, regenerate seat layout
            $seatLayout = $bus->seat_layout;
            $hasBackRow = $request->boolean('has_back_row', $bus->seat_layout['has_back_row'] ?? true);

            if ($request->total_seats != $bus->total_seats ||
                $layoutType != ($bus->seat_layout['layout_type'] ?? '2x2') ||
                $hasBackRow != ($bus->seat_layout['has_back_row'] ?? true)) {
                $busType = BusType::findOrFail($request->bus_type_id);
                $seatLayout = $this->generateSeatLayout($request->total_seats, $busType, $layoutType, $hasBackRow);
            }

            $bus->update([
                'bus_number' => $request->bus_number,
                'license_plate' => $request->license_plate,
                'bus_type_id' => $request->bus_type_id,
                'model' => $request->model,
                'color' => $request->color,
                'manufacture_year' => $request->manufacture_year,
                'total_seats' => $request->total_seats,
                'seat_layout' => $seatLayout,
                'amenities' => $request->amenities ?? [],
                'description' => $request->description,
            ]);

            DB::commit();

            return redirect()->route('operator.buses.show', $bus)
                ->with('success', 'Bus updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to update bus: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified bus.
     */
    public function destroy(Bus $bus)
    {
        // Ensure operator can only delete their own buses
        if ($bus->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to bus.');
        }

        // Check if bus has future schedules
        $futureSchedules = $bus->schedules()->where('travel_date', '>=', Carbon::today())->count();
        if ($futureSchedules > 0) {
            return back()->with('error', 'Cannot delete bus with future schedules. Please cancel or complete all schedules first.');
        }

        // Check if bus has confirmed bookings
        $confirmedBookings = $bus->bookings()->where('bookings.status', 'confirmed')->count();
        if ($confirmedBookings > 0) {
            return back()->with('error', 'Cannot delete bus with confirmed bookings.');
        }

        $bus->delete();

        return redirect()->route('operator.buses.index')
            ->with('success', 'Bus deleted successfully!');
    }

    /**
     * Toggle bus status (active/inactive).
     */
    public function toggleStatus(Bus $bus)
    {
        // Ensure operator can only toggle their own buses
        if ($bus->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to bus.');
        }

        $bus->update(['is_active' => !$bus->is_active]);

        $status = $bus->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Bus {$status} successfully!");
    }

    /**
     * Generate seat layout based on total seats and bus type.
     */
    private function generateSeatLayout($totalSeats, $busType, $layoutType = '2x2', $hasBackRow = true)
    {
        $seatLayoutService = new \App\Services\SeatLayoutService();
        return $seatLayoutService->generateSeatLayout($totalSeats, $layoutType, $hasBackRow);
    }

    private function calculateTotalSeats($layoutType, $hasBackRow)
    {
        $configs = \App\Services\SeatLayoutService::LAYOUT_CONFIGS;
        $config = $configs[$layoutType];
        
        // This is a simplified calculation. You might want to make this more robust.
        // For now, we'll use a predefined number of rows for simplicity.
        $rows = 8; 
        
        $totalSeats = $rows * $config['total_per_row'];
        
        if ($hasBackRow) {
            $totalSeats += $config['back_row_seats'] - $config['total_per_row'];
        }
        
        return $totalSeats;
    }

    /**
     * Update bus seat layout configuration.
     */
    public function updateSeatLayout(Request $request, Bus $bus)
    {
        // Ensure operator can only update their own buses
        if ($bus->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to bus.');
        }

        $request->validate([
            'layout_type' => 'required|in:2x2,2x1,3x2',
            'has_back_row' => 'boolean',
        ]);

        $layoutType = $request->layout_type;
        $hasBackRow = $request->boolean('has_back_row', true);

        // Validate layout configuration
        $errors = $bus->validateSeatLayout($layoutType, $hasBackRow);
        if (!empty($errors)) {
            return back()->withErrors(['layout' => $errors]);
        }

        // Update seat layout
        $newLayout = $bus->updateSeatLayout($layoutType, $hasBackRow);

        return back()->with('success', 'Seat layout updated successfully.');
    }

    /**
     * Show seat layout preview page.
     */
    public function showSeatLayoutPreview()
    {
        // Get a sample bus for demonstration
        $bus = Bus::where('operator_id', auth()->id())->first();

        if (!$bus) {
            // Create a sample layout for demonstration
            $sampleLayout = [
                'layout_type' => '2x2',
                'total_seats' => 32,
                'rows' => 8,
                'columns' => 5,
                'aisle_position' => 2,
                'has_back_row' => true,
                'back_row_seats' => 5,
                'seats' => []
            ];

            // Generate sample seats
            $seatNumber = 1;
            for ($row = 1; $row <= 7; $row++) {
                for ($col = 1; $col <= 5; $col++) {
                    if ($col == 3) continue; // Skip aisle position

                    $letter = chr(64 + $row); // A, B, C, etc.
                    $number = $col > 3 ? $col - 1 : $col;

                    $sampleLayout['seats'][] = [
                        'number' => $letter . $number,
                        'row' => $row,
                        'column' => $col,
                        'type' => 'regular',
                        'is_window' => ($col == 1 || $col == 5),
                        'is_aisle' => ($col == 2 || $col == 4),
                        'is_available' => true,
                        'side' => $col <= 2 ? 'left' : 'right'
                    ];
                }
            }

            // Add back row
            for ($col = 1; $col <= 5; $col++) {
                $sampleLayout['seats'][] = [
                    'number' => 'H' . $col,
                    'row' => 8,
                    'column' => $col,
                    'type' => 'back_row',
                    'is_window' => ($col == 1 || $col == 5),
                    'is_aisle' => false,
                    'is_available' => true,
                    'side' => 'back'
                ];
            }

            $bus = (object) [
                'bus_number' => 'DEMO-001',
                'total_seats' => 32,
                'seat_layout' => $sampleLayout
            ];
        }

        return view('operator.buses.preview-seat-layout', compact('bus'));
    }

    /**
     * Get valid seat counts for a layout type.
     */
    public function getValidSeatCounts(Request $request)
    {
        $request->validate([
            'layout_type' => 'required|in:2x2,2x1,3x2',
        ]);

        $validCounts = \App\Services\SeatLayoutService::getValidSeatCounts($request->layout_type);

        return response()->json([
            'success' => true,
            'valid_counts' => $validCounts,
            'layout_type' => $request->layout_type,
        ]);
    }

    /**
     * Preview seat layout configuration.
     */
    public function previewSeatLayout(Request $request)
    {
        $request->validate([
            'total_seats' => 'required|integer|min:10|max:60',
            'layout_type' => 'required|in:2x2,2x1,3x2',
            'has_back_row' => 'boolean',
        ]);

        // Validate seat count for the specific layout type
        $layoutType = $request->layout_type;
        $totalSeats = $request->total_seats;

        if (!\App\Services\SeatLayoutService::isValidSeatCount($totalSeats, $layoutType)) {
            $validCounts = \App\Services\SeatLayoutService::getValidSeatCounts($layoutType);
            $validCountsStr = implode(', ', array_slice($validCounts, 0, 10));
            if (count($validCounts) > 10) {
                $validCountsStr .= '...';
            }

            return response()->json([
                'success' => false,
                'error' => "Invalid seat count for {$layoutType} layout. Valid counts: {$validCountsStr}"
            ], 422);
        }

        $seatLayoutService = new \App\Services\SeatLayoutService();
        $layout = $seatLayoutService->generateSeatLayout(
            $request->total_seats,
            $request->layout_type,
            $request->boolean('has_back_row', true)
        );

        return response()->json([
            'success' => true,
            'layout' => $layout,
        ]);
    }

    /**
     * Display routes that the operator has schedules for.
     */
    public function routes(Request $request)
    {
        $operator = Auth::user();

        // Get routes that this operator has schedules for
        $query = Route::whereHas('schedules', function($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->with(['sourceCity', 'destinationCity']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('sourceCity', function($cityQuery) use ($search) {
                      $cityQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('destinationCity', function($cityQuery) use ($search) {
                      $cityQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $routes = $query->orderBy('name')->paginate(15);

        // Get statistics for each route
        $routes->getCollection()->transform(function($route) use ($operator) {
            $route->total_schedules = $route->schedules()->where('operator_id', $operator->id)->count();
            $route->active_schedules = $route->schedules()
                ->where('operator_id', $operator->id)
                ->where('status', 'scheduled')
                ->where('travel_date', '>=', Carbon::today())
                ->count();
            $route->total_bookings = $route->schedules()
                ->where('operator_id', $operator->id)
                ->withCount('bookings')
                ->get()
                ->sum('bookings_count');
            return $route;
        });

        return view('operator.routes.index', compact('routes'));
    }

    /**
     * Show form to suggest a new route.
     */
    public function suggestRoute()
    {
        $cities = City::where('is_active', true)->orderBy('name')->get();
        return view('operator.routes.suggest', compact('cities'));
    }

    /**
     * Store a route suggestion.
     */
    public function storeSuggestion(Request $request)
    {
        $request->validate([
            'source_city_id' => 'required|exists:cities,id',
            'destination_city_id' => 'required|exists:cities,id|different:source_city_id',
            'suggested_fare' => 'required|numeric|min:0',
            'estimated_duration' => 'required|date_format:H:i',
            'distance_km' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
            'stops' => 'nullable|array',
            'stops.*' => 'string|max:255',
        ]);

        // Check if route already exists
        $existingRoute = Route::where('source_city_id', $request->source_city_id)
            ->where('destination_city_id', $request->destination_city_id)
            ->first();

        if ($existingRoute) {
            return back()->withInput()
                ->with('error', 'A route between these cities already exists.');
        }

        // For now, we'll just show a success message
        // In a real application, you might want to store this in a separate table
        // for admin review before creating the actual route

        return back()->with('success', 'Route suggestion submitted successfully! An admin will review your suggestion.');
    }
}
