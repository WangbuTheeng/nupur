<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::role('user')
            ->withCount(['bookings']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
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

        // Booking activity filter
        if ($request->filled('booking_activity')) {
            if ($request->booking_activity === 'active') {
                $query->whereHas('bookings', function($q) {
                    $q->whereDate('created_at', '>=', Carbon::now()->subDays(30));
                });
            } elseif ($request->booking_activity === 'inactive') {
                $query->whereDoesntHave('bookings', function($q) {
                    $q->whereDate('created_at', '>=', Carbon::now()->subDays(30));
                });
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => User::role('user')->count(),
            'active' => User::role('user')->where('is_active', true)->count(),
            'inactive' => User::role('user')->where('is_active', false)->count(),
            'this_month' => User::role('user')->whereMonth('created_at', Carbon::now()->month)->count(),
            'active_bookings' => User::role('user')->whereHas('bookings', function($q) {
                $q->whereDate('created_at', '>=', Carbon::now()->subDays(30));
            })->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['bookings.schedule.route', 'bookings.schedule.bus']);
        
        // Get user statistics
        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'confirmed_bookings' => $user->bookings()->where('status', 'confirmed')->count(),
            'cancelled_bookings' => $user->bookings()->where('status', 'cancelled')->count(),
            'total_spent' => $user->bookings()->where('status', 'confirmed')->sum('total_amount'),
            'this_month_bookings' => $user->bookings()->whereMonth('created_at', Carbon::now()->month)->count(),
            'last_booking' => $user->bookings()->latest()->first()?->created_at,
        ];

        // Recent bookings
        $recentBookings = $user->bookings()
            ->with(['schedule.route', 'schedule.bus'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Booking trends (last 6 months)
        $bookingTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $bookingTrends[] = [
                'month' => $month->format('M Y'),
                'count' => $user->bookings()
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count()
            ];
        }

        return view('admin.users.show', compact('user', 'stats', 'recentBookings', 'bookingTrends'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Check if user has confirmed bookings
            $confirmedBookings = $user->bookings()->where('status', 'confirmed')->count();

            if ($confirmedBookings > 0) {
                return back()->withErrors(['error' => 'Cannot delete user with confirmed bookings.']);
            }

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete user: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully.");
    }

    /**
     * Block user (set as inactive).
     */
    public function block(User $user)
    {
        $user->update(['is_active' => false]);
        return back()->with('success', 'User blocked successfully.');
    }

    /**
     * Unblock user (set as active).
     */
    public function unblock(User $user)
    {
        $user->update(['is_active' => true]);
        return back()->with('success', 'User unblocked successfully.');
    }

    /**
     * Get user booking history for AJAX.
     */
    public function bookingHistory(User $user)
    {
        $bookings = $user->bookings()
            ->with(['schedule.route', 'schedule.bus'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'bookings' => $bookings->items(),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'total' => $bookings->total(),
            ]
        ]);
    }

    /**
     * Export users to CSV.
     */
    public function export(Request $request)
    {
        $query = User::role('user')->withCount(['bookings']);

        // Apply same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->get();

        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Phone', 'Status', 'Total Bookings', 'Joined Date']);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->is_active ? 'Active' : 'Inactive',
                    $user->bookings_count,
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
