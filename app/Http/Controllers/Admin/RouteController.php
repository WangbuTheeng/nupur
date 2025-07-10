<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RouteController extends Controller
{
    /**
     * Display a listing of routes.
     */
    public function index()
    {
        $routes = Route::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new route.
     */
    public function create()
    {
        return view('admin.routes.create');
    }

    /**
     * Store a newly created route in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'source_city' => 'required|string|max:255',
            'destination_city' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:1',
            'base_fare' => 'required|numeric|min:1',
            'estimated_duration' => 'required|date_format:H:i',
            'stops' => 'nullable|array',
            'stops.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Convert estimated_duration to time format
        $estimatedDuration = $request->estimated_duration . ':00';

        Route::create([
            'name' => $request->name,
            'source_city' => $request->source_city,
            'destination_city' => $request->destination_city,
            'distance_km' => $request->distance_km,
            'base_fare' => $request->base_fare,
            'estimated_duration' => $estimatedDuration,
            'stops' => array_filter($request->stops ?? []),
            'is_active' => true,
        ]);

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route created successfully!');
    }

    /**
     * Display the specified route.
     */
    public function show(Route $route)
    {
        $route->load('schedules.bus');
        return view('admin.routes.show', compact('route'));
    }

    /**
     * Show the form for editing the specified route.
     */
    public function edit(Route $route)
    {
        return view('admin.routes.edit', compact('route'));
    }

    /**
     * Update the specified route in storage.
     */
    public function update(Request $request, Route $route)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'source_city' => 'required|string|max:255',
            'destination_city' => 'required|string|max:255',
            'distance_km' => 'required|numeric|min:1',
            'base_fare' => 'required|numeric|min:1',
            'estimated_duration' => 'required|date_format:H:i',
            'stops' => 'nullable|array',
            'stops.*' => 'string|max:255',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Convert estimated_duration to time format
        $estimatedDuration = $request->estimated_duration . ':00';

        $route->update([
            'name' => $request->name,
            'source_city' => $request->source_city,
            'destination_city' => $request->destination_city,
            'distance_km' => $request->distance_km,
            'base_fare' => $request->base_fare,
            'estimated_duration' => $estimatedDuration,
            'stops' => array_filter($request->stops ?? []),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.routes.index')
            ->with('success', 'Route updated successfully!');
    }

    /**
     * Remove the specified route from storage.
     */
    public function destroy(Route $route)
    {
        $route->delete();
        return redirect()->route('admin.routes.index')
            ->with('success', 'Route deleted successfully!');
    }
}
