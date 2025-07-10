<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class OperatorController extends Controller
{
    /**
     * Display a listing of operators.
     */
    public function index(Request $request)
    {
        $query = User::role('operator')
            ->with(['roles', 'permissions'])
            ->withCount(['buses', 'schedules', 'operatorBookings']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $operators = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => User::role('operator')->count(),
            'active' => User::role('operator')->where('is_active', true)->count(),
            'inactive' => User::role('operator')->where('is_active', false)->count(),
            'this_month' => User::role('operator')->whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        return view('admin.operators.index', compact('operators', 'stats'));
    }

    /**
     * Show the form for creating a new operator.
     */
    public function create()
    {
        $permissions = [
            'bus_management' => 'Manage Buses',
            'schedule_management' => 'Manage Schedules',
            'booking_management' => 'Manage Bookings',
            'counter_booking' => 'Counter Booking',
            'revenue_reports' => 'View Revenue Reports',
            'seat_management' => 'Seat Management',
        ];

        return view('admin.operators.create', compact('permissions'));
    }

    /**
     * Store a newly created operator in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'company_license' => 'required|string|max:100',
            'contact_person' => 'required|string|max:255',
            'permissions' => 'array',
        ]);

        DB::beginTransaction();
        try {
            // Create operator user
            $operator = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'company_name' => $request->company_name,
                'company_address' => $request->company_address,
                'company_license' => $request->company_license,
                'contact_person' => $request->contact_person,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Assign operator role
            $operator->assignRole('operator');

            // Assign permissions
            if ($request->filled('permissions')) {
                foreach ($request->permissions as $permission) {
                    $operator->givePermissionTo($permission);
                }
            }

            DB::commit();

            return redirect()->route('admin.operators.index')
                ->with('success', 'Operator created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to create operator: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified operator.
     */
    public function show(User $operator)
    {
        $operator->load(['roles', 'permissions', 'buses', 'schedules']);
        
        // Get operator statistics
        $stats = [
            'total_buses' => $operator->buses()->count(),
            'active_schedules' => $operator->schedules()->where('status', 'scheduled')->count(),
            'total_bookings' => $operator->operatorBookings()->count(),
            'monthly_revenue' => $operator->operatorBookings()
                ->whereMonth('created_at', Carbon::now()->month)
                ->where('status', 'confirmed')
                ->sum('total_amount'),
        ];

        // Recent activity
        $recentBookings = $operator->operatorBookings()
            ->with(['user', 'schedule.route'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.operators.show', compact('operator', 'stats', 'recentBookings'));
    }

    /**
     * Show the form for editing the specified operator.
     */
    public function edit(User $operator)
    {
        $permissions = [
            'bus_management' => 'Manage Buses',
            'schedule_management' => 'Manage Schedules',
            'booking_management' => 'Manage Bookings',
            'counter_booking' => 'Counter Booking',
            'revenue_reports' => 'View Revenue Reports',
            'seat_management' => 'Seat Management',
        ];

        $operatorPermissions = $operator->permissions->pluck('name')->toArray();

        return view('admin.operators.edit', compact('operator', 'permissions', 'operatorPermissions'));
    }

    /**
     * Update the specified operator in storage.
     */
    public function update(Request $request, User $operator)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $operator->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $operator->id,
            'password' => 'nullable|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:500',
            'company_license' => 'required|string|max:100',
            'contact_person' => 'required|string|max:255',
            'is_active' => 'boolean',
            'permissions' => 'array',
        ]);

        DB::beginTransaction();
        try {
            // Update operator details
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company_name' => $request->company_name,
                'company_address' => $request->company_address,
                'company_license' => $request->company_license,
                'contact_person' => $request->contact_person,
                'is_active' => $request->boolean('is_active'),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $operator->update($updateData);

            // Update permissions
            $operator->syncPermissions($request->permissions ?? []);

            DB::commit();

            return redirect()->route('admin.operators.index')
                ->with('success', 'Operator updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Failed to update operator: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified operator from storage.
     */
    public function destroy(User $operator)
    {
        try {
            // Check if operator has active schedules or bookings
            $activeSchedules = $operator->schedules()->where('status', 'scheduled')->count();
            $pendingBookings = $operator->operatorBookings()->where('status', 'pending')->count();

            if ($activeSchedules > 0 || $pendingBookings > 0) {
                return back()->withErrors(['error' => 'Cannot delete operator with active schedules or pending bookings.']);
            }

            $operator->delete();

            return redirect()->route('admin.operators.index')
                ->with('success', 'Operator deleted successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete operator: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle operator status.
     */
    public function toggleStatus(User $operator)
    {
        $operator->update(['is_active' => !$operator->is_active]);

        $status = $operator->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Operator {$status} successfully.");
    }
}
