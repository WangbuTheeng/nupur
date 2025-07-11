<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RealtimeController extends Controller
{
    /**
     * Get real-time dashboard statistics.
     */
    public function getDashboardStats()
    {
        $user = Auth::user();
        $today = Carbon::today();

        if ($user->hasRole('admin')) {
            return $this->getAdminStats($today);
        } elseif ($user->hasRole('operator')) {
            return $this->getOperatorStats($user, $today);
        } else {
            return $this->getUserStats($user, $today);
        }
    }

    /**
     * Get admin dashboard statistics.
     */
    private function getAdminStats($today)
    {
        $stats = [
            'today_bookings' => Booking::whereDate('created_at', $today)->count(),
            'today_revenue' => Booking::whereDate('created_at', $today)
                ->where('status', 'confirmed')
                ->sum('total_amount'),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'active_schedules' => Schedule::whereDate('travel_date', $today)
                ->where('status', 'scheduled')
                ->count(),
            'total_users' => User::where('role', 'user')->count(),
            'total_operators' => User::where('role', 'operator')->count(),
            'monthly_revenue' => Booking::whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->where('status', 'confirmed')
                ->sum('total_amount'),
            'last_updated' => now()->format('H:i:s'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get operator dashboard statistics.
     */
    private function getOperatorStats($user, $today)
    {
        $stats = [
            'today_bookings' => Booking::whereHas('schedule', function($q) use ($user) {
                    $q->where('operator_id', $user->id);
                })
                ->whereDate('created_at', $today)
                ->count(),
            'today_revenue' => Booking::whereHas('schedule', function($q) use ($user) {
                    $q->where('operator_id', $user->id);
                })
                ->whereDate('created_at', $today)
                ->where('status', 'confirmed')
                ->sum('total_amount'),
            'pending_bookings' => Booking::whereHas('schedule', function($q) use ($user) {
                    $q->where('operator_id', $user->id);
                })
                ->where('status', 'pending')
                ->count(),
            'active_schedules' => Schedule::where('operator_id', $user->id)
                ->whereDate('travel_date', $today)
                ->where('status', 'scheduled')
                ->count(),
            'monthly_revenue' => Booking::whereHas('schedule', function($q) use ($user) {
                    $q->where('operator_id', $user->id);
                })
                ->whereMonth('created_at', $today->month)
                ->whereYear('created_at', $today->year)
                ->where('status', 'confirmed')
                ->sum('total_amount'),
            'last_updated' => now()->format('H:i:s'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get user dashboard statistics.
     */
    private function getUserStats($user, $today)
    {
        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'confirmed_bookings' => $user->bookings()->where('status', 'confirmed')->count(),
            'pending_bookings' => $user->bookings()->where('status', 'pending')->count(),
            'upcoming_trips' => $user->bookings()
                ->whereHas('schedule', function($q) {
                    $q->where('travel_date', '>=', Carbon::today());
                })
                ->where('status', 'confirmed')
                ->count(),
            'total_spent' => $user->bookings()
                ->where('status', 'confirmed')
                ->sum('total_amount'),
            'last_updated' => now()->format('H:i:s'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get real-time booking statistics.
     */
    public function getBookingStats()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $hourlyBookings = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $startTime = $today->copy()->addHours($hour);
            $endTime = $startTime->copy()->addHour();

            $query = Booking::whereBetween('created_at', [$startTime, $endTime]);

            if ($user->hasRole('operator')) {
                $query->whereHas('schedule', function($q) use ($user) {
                    $q->where('operator_id', $user->id);
                });
            } elseif (!$user->hasRole('admin')) {
                $query->where('user_id', $user->id);
            }

            $hourlyBookings[] = [
                'hour' => $hour,
                'bookings' => $query->count(),
                'revenue' => $query->where('status', 'confirmed')->sum('total_amount'),
            ];
        }

        return response()->json([
            'success' => true,
            'hourly_bookings' => $hourlyBookings,
            'last_updated' => now()->format('H:i:s'),
        ]);
    }

    /**
     * Get user notifications.
     */
    public function getNotifications()
    {
        $user = Auth::user();

        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $notifications->whereNull('read_at')->count(),
        ]);
    }
}
