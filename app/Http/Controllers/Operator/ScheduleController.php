<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Bus;
use App\Models\Route;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of operator's schedules.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->schedules()->with(['route', 'bus', 'bookings']);

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('travel_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('travel_date', '<=', $request->date_to);
        } else {
            // Default to show schedules from today onwards
            $query->whereDate('travel_date', '>=', Carbon::today());
        }

        // Route filter
        if ($request->filled('route')) {
            $query->where('route_id', $request->route);
        }

        // Bus filter
        if ($request->filled('bus')) {
            $query->where('bus_id', $request->bus);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('travel_date')->orderBy('departure_time')->paginate(15);

        // Get data for filters
        $routes = Route::where('is_active', true)->get();
        $buses = Auth::user()->buses()->where('is_active', true)->get();

        // Statistics
        $stats = [
            'total' => Auth::user()->schedules()->count(),
            'upcoming' => Auth::user()->schedules()->where('travel_date', '>=', Carbon::today())->count(),
            'today' => Auth::user()->schedules()->whereDate('travel_date', Carbon::today())->count(),
            'this_week' => Auth::user()->schedules()->whereBetween('travel_date', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count(),
        ];

        return view('operator.schedules.index', compact('schedules', 'routes', 'buses', 'stats'));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        $routes = Route::where('is_active', true)->get();
        $buses = Auth::user()->buses()->where('is_active', true)->get();

        return view('operator.schedules.create', compact('routes', 'buses'));
    }

    /**
     * Store a newly created schedule.
     */
    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'bus_id' => 'required|exists:buses,id',
            'travel_date' => 'required|date|after_or_equal:today',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i|after:departure_time',
            'fare' => 'required|numeric|min:1',
            'special_notes' => 'nullable|string|max:500',
        ]);

        // Verify bus belongs to operator
        $bus = Bus::where('id', $request->bus_id)
                  ->where('operator_id', Auth::id())
                  ->firstOrFail();

        // Check for conflicting schedules
        $conflictingSchedule = Schedule::where('bus_id', $request->bus_id)
            ->where('travel_date', $request->travel_date)
            ->where(function($query) use ($request) {
                $query->whereBetween('departure_time', [$request->departure_time, $request->arrival_time])
                      ->orWhereBetween('arrival_time', [$request->departure_time, $request->arrival_time])
                      ->orWhere(function($q) use ($request) {
                          $q->where('departure_time', '<=', $request->departure_time)
                            ->where('arrival_time', '>=', $request->arrival_time);
                      });
            })
            ->exists();

        if ($conflictingSchedule) {
            return back()->withInput()
                ->with('error', 'Bus is already scheduled during this time period.');
        }

        DB::beginTransaction();
        try {
            $schedule = Schedule::create([
                'route_id' => $request->route_id,
                'bus_id' => $request->bus_id,
                'operator_id' => Auth::id(),
                'travel_date' => $request->travel_date,
                'departure_time' => $request->departure_time,
                'arrival_time' => $request->arrival_time,
                'fare' => $request->fare,
                'available_seats' => $bus->total_seats,
                'status' => 'scheduled',
                'special_notes' => $request->special_notes,
            ]);

            DB::commit();

            return redirect()->route('operator.schedules.show', $schedule)
                ->with('success', 'Schedule created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create schedule: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified schedule.
     */
    public function show(Schedule $schedule)
    {
        // Ensure operator can only view their own schedules
        if ($schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to schedule.');
        }

        $schedule->load(['route', 'bus', 'bookings.user']);

        // Get bookings for this schedule
        $bookings = $schedule->bookings()->with('user')->orderBy('created_at', 'desc')->get();

        // Get seat map with booking status
        $seatMap = $this->generateSeatMapWithBookings($schedule);

        // Statistics
        $stats = [
            'total_seats' => $schedule->bus->total_seats,
            'booked_seats' => $schedule->bookings()->where('status', '!=', 'cancelled')->sum('passenger_count'),
            'available_seats' => $schedule->available_seats,
            'total_revenue' => $schedule->bookings()->where('status', 'confirmed')->sum('total_amount'),
            'pending_bookings' => $schedule->bookings()->where('status', 'pending')->count(),
        ];

        return view('operator.schedules.show', compact('schedule', 'seatMap', 'stats', 'bookings'));
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit(Schedule $schedule)
    {
        // Ensure operator can only edit their own schedules
        if ($schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to schedule.');
        }

        // Don't allow editing if there are confirmed bookings
        if ($schedule->bookings()->where('status', 'confirmed')->exists()) {
            return back()->with('error', 'Cannot edit schedule with confirmed bookings.');
        }

        $routes = Route::where('is_active', true)->get();
        $buses = Auth::user()->buses()->where('is_active', true)->get();

        return view('operator.schedules.edit', compact('schedule', 'routes', 'buses'));
    }

    /**
     * Update the specified schedule.
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Ensure operator can only update their own schedules
        if ($schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to schedule.');
        }

        // Don't allow editing if there are confirmed bookings
        if ($schedule->bookings()->where('status', 'confirmed')->exists()) {
            return back()->with('error', 'Cannot edit schedule with confirmed bookings.');
        }

        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'bus_id' => 'required|exists:buses,id',
            'travel_date' => 'required|date|after_or_equal:today',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i|after:departure_time',
            'fare' => 'required|numeric|min:1',
            'special_notes' => 'nullable|string|max:500',
        ]);

        // Verify bus belongs to operator
        $bus = Bus::where('id', $request->bus_id)
                  ->where('operator_id', Auth::id())
                  ->firstOrFail();

        // Check for conflicting schedules (excluding current schedule)
        $conflictingSchedule = Schedule::where('bus_id', $request->bus_id)
            ->where('travel_date', $request->travel_date)
            ->where('id', '!=', $schedule->id)
            ->where(function($query) use ($request) {
                $query->whereBetween('departure_time', [$request->departure_time, $request->arrival_time])
                      ->orWhereBetween('arrival_time', [$request->departure_time, $request->arrival_time])
                      ->orWhere(function($q) use ($request) {
                          $q->where('departure_time', '<=', $request->departure_time)
                            ->where('arrival_time', '>=', $request->arrival_time);
                      });
            })
            ->exists();

        if ($conflictingSchedule) {
            return back()->withInput()
                ->with('error', 'Bus is already scheduled during this time period.');
        }

        DB::beginTransaction();
        try {
            $schedule->update([
                'route_id' => $request->route_id,
                'bus_id' => $request->bus_id,
                'travel_date' => $request->travel_date,
                'departure_time' => $request->departure_time,
                'arrival_time' => $request->arrival_time,
                'fare' => $request->fare,
                'available_seats' => $bus->total_seats - $schedule->bookings()->where('status', '!=', 'cancelled')->sum('passenger_count'),
                'special_notes' => $request->special_notes,
            ]);

            DB::commit();

            return redirect()->route('operator.schedules.show', $schedule)
                ->with('success', 'Schedule updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to update schedule: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy(Schedule $schedule)
    {
        // Ensure operator can only delete their own schedules
        if ($schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to schedule.');
        }

        // Don't allow deletion if there are confirmed bookings
        if ($schedule->bookings()->where('status', 'confirmed')->exists()) {
            return back()->with('error', 'Cannot delete schedule with confirmed bookings.');
        }

        // Cancel any pending bookings
        $schedule->bookings()->where('status', 'pending')->update(['status' => 'cancelled']);

        $schedule->delete();

        return redirect()->route('operator.schedules.index')
            ->with('success', 'Schedule deleted successfully!');
    }

    /**
     * Toggle schedule status.
     */
    public function toggleStatus(Schedule $schedule)
    {
        // Ensure operator can only toggle their own schedules
        if ($schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to schedule.');
        }

        $newStatus = $schedule->status === 'scheduled' ? 'cancelled' : 'scheduled';
        $schedule->update(['status' => $newStatus]);

        return back()->with('success', "Schedule {$newStatus} successfully!");
    }

    /**
     * Update schedule status.
     */
    public function updateStatus(Request $request, Schedule $schedule)
    {
        // Ensure operator can only update their own schedules
        if ($schedule->operator_id !== Auth::id()) {
            abort(403, 'Unauthorized access to schedule.');
        }

        $request->validate([
            'status' => 'required|in:scheduled,boarding,departed,arrived,completed,cancelled',
        ]);

        $oldStatus = $schedule->status;
        $newStatus = $request->status;

        // Validate status transitions
        $validTransitions = [
            'scheduled' => ['boarding', 'cancelled', 'completed'],
            'boarding' => ['departed', 'cancelled'],
            'departed' => ['arrived', 'completed'],
            'arrived' => ['completed'],
            'completed' => [], // No transitions from completed
            'cancelled' => [], // No transitions from cancelled
        ];

        if (!in_array($newStatus, $validTransitions[$oldStatus] ?? [])) {
            return back()->with('error', "Cannot change status from {$oldStatus} to {$newStatus}.");
        }

        $schedule->update(['status' => $newStatus]);

        return back()->with('success', "Schedule status updated to {$newStatus} successfully!");
    }

    /**
     * Generate seat map with booking information.
     */
    private function generateSeatMapWithBookings(Schedule $schedule)
    {
        $seatLayout = $schedule->bus->seat_layout;
        $bookedSeats = $schedule->bookings()
            ->where('status', '!=', 'cancelled')
            ->pluck('seat_numbers')
            ->flatten()
            ->toArray();

        if (isset($seatLayout['seats']) && is_array($seatLayout['seats'])) {
            foreach ($seatLayout['seats'] as &$seat) {
                // Handle both 'number' and 'seat_number' keys for backward compatibility
                $seatNumber = $seat['number'] ?? $seat['seat_number'] ?? null;
                $seat['is_booked'] = $seatNumber ? in_array($seatNumber, $bookedSeats) : false;
            }
        }

        return $seatLayout;
    }
}
