<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\BusType;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusController extends Controller
{
    /**
     * Display a listing of buses.
     */
    public function index()
    {
        $buses = Bus::with('busType')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.buses.index', compact('buses'));
    }

    /**
     * Show the form for creating a new bus.
     */
    public function create()
    {
        $busTypes = BusType::where('is_active', true)->get();
        return view('admin.buses.create', compact('busTypes'));
    }

    /**
     * Store a newly created bus in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bus_number' => 'required|string|max:255|unique:buses',
            'operator_name' => 'required|string|max:255',
            'bus_type_id' => 'required|exists:bus_types,id',
            'license_plate' => 'required|string|max:255|unique:buses',
            'manufacture_year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'amenities' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $busType = BusType::find($request->bus_type_id);
        
        $bus = Bus::create([
            'bus_number' => $request->bus_number,
            'operator_name' => $request->operator_name,
            'bus_type_id' => $request->bus_type_id,
            'license_plate' => $request->license_plate,
            'manufacture_year' => $request->manufacture_year,
            'total_seats' => $busType->total_seats,
            'amenities' => $request->amenities ?? [],
            'is_active' => true,
        ]);

        // Create seats for the bus based on bus type layout
        $this->createSeatsForBus($bus, $busType);

        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus created successfully!');
    }

    /**
     * Display the specified bus.
     */
    public function show(Bus $bus)
    {
        $bus->load(['busType', 'seats', 'schedules.route']);
        return view('admin.buses.show', compact('bus'));
    }

    /**
     * Show the form for editing the specified bus.
     */
    public function edit(Bus $bus)
    {
        $busTypes = BusType::where('is_active', true)->get();
        return view('admin.buses.edit', compact('bus', 'busTypes'));
    }

    /**
     * Update the specified bus in storage.
     */
    public function update(Request $request, Bus $bus)
    {
        $validator = Validator::make($request->all(), [
            'bus_number' => 'required|string|max:255|unique:buses,bus_number,' . $bus->id,
            'operator_name' => 'required|string|max:255',
            'bus_type_id' => 'required|exists:bus_types,id',
            'license_plate' => 'required|string|max:255|unique:buses,license_plate,' . $bus->id,
            'manufacture_year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'amenities' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $bus->update([
            'bus_number' => $request->bus_number,
            'operator_name' => $request->operator_name,
            'bus_type_id' => $request->bus_type_id,
            'license_plate' => $request->license_plate,
            'manufacture_year' => $request->manufacture_year,
            'amenities' => $request->amenities ?? [],
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus updated successfully!');
    }

    /**
     * Remove the specified bus from storage.
     */
    public function destroy(Bus $bus)
    {
        $bus->delete();
        return redirect()->route('admin.buses.index')
            ->with('success', 'Bus deleted successfully!');
    }

    /**
     * Create seats for a bus based on its type layout.
     */
    private function createSeatsForBus(Bus $bus, BusType $busType)
    {
        $layout = $busType->seat_layout;
        $rows = $layout['rows'] ?? 8;
        $columns = $layout['columns'] ?? 4;
        $aislePosition = $layout['aisle_position'] ?? 2;

        $seatNumber = 1;
        
        for ($row = 1; $row <= $rows; $row++) {
            for ($col = 1; $col <= $columns; $col++) {
                $seatLabel = chr(64 + $row) . $col; // A1, A2, B1, B2, etc.
                
                Seat::create([
                    'bus_id' => $bus->id,
                    'seat_number' => $seatLabel,
                    'row_number' => $row,
                    'column_number' => $col,
                    'seat_type' => 'regular',
                    'is_window' => ($col == 1 || $col == $columns),
                    'is_aisle' => ($col == $aislePosition || $col == $aislePosition + 1),
                    'is_available' => true,
                ]);
                
                $seatNumber++;
            }
        }
    }
}
