<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\BusType;
use App\Models\Route;
use App\Models\Schedule;
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
            'amenities' => 'nullable|array',
            'description' => 'nullable|string|max:500',
        ]);

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
                'seat_layout' => $this->generateSeatLayout($request->total_seats, $busType),
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
                ->where('status', 'confirmed')
                ->whereMonth('created_at', Carbon::now()->month)
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
            'amenities' => 'nullable|array',
            'description' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // If seat count changed, regenerate seat layout
            $seatLayout = $bus->seat_layout;
            if ($request->total_seats != $bus->total_seats) {
                $busType = BusType::findOrFail($request->bus_type_id);
                $seatLayout = $this->generateSeatLayout($request->total_seats, $busType);
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
        $confirmedBookings = $bus->bookings()->where('status', 'confirmed')->count();
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
    private function generateSeatLayout($totalSeats, $busType)
    {
        $layout = $busType->seat_layout ?? ['rows' => 10, 'columns' => 4, 'aisle_position' => 2];

        $rows = ceil($totalSeats / $layout['columns']);
        $seats = [];

        $seatNumber = 1;
        for ($row = 1; $row <= $rows; $row++) {
            for ($col = 1; $col <= $layout['columns']; $col++) {
                if ($seatNumber <= $totalSeats) {
                    $seats[] = [
                        'number' => $seatNumber,
                        'row' => $row,
                        'column' => $col,
                        'type' => $col <= 2 ? 'window' : 'aisle',
                        'is_available' => true,
                    ];
                    $seatNumber++;
                }
            }
        }

        return [
            'rows' => $rows,
            'columns' => $layout['columns'],
            'aisle_position' => $layout['aisle_position'],
            'seats' => $seats,
        ];
    }
}
