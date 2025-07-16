<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get notifications for the admin.
     */
    public function index(Request $request)
    {
        $admin = Auth::user();
        
        $notifications = Notification::where('notifiable_type', get_class($admin))
            ->where('notifiable_id', $admin->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'unread_count' => $this->getUnreadCount(),
                'has_more' => $notifications->hasMorePages(),
            ]);
        }

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications for AJAX requests.
     */
    public function unread()
    {
        $admin = Auth::user();
        
        $notifications = Notification::where('notifiable_type', get_class($admin))
            ->where('notifiable_id', $admin->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $notifications->count(),
        ]);
    }

    /**
     * Get unread notification count only.
     */
    public function unreadCount()
    {
        return response()->json([
            'unread_count' => $this->getUnreadCount(),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        // Ensure the notification belongs to the current admin
        if ($notification->notifiable_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => $this->getUnreadCount(),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        $admin = Auth::user();
        
        Notification::where('notifiable_type', get_class($admin))
            ->where('notifiable_id', $admin->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    /**
     * Delete a notification.
     */
    public function destroy(Notification $notification)
    {
        // Ensure the notification belongs to the current admin
        if ($notification->notifiable_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'unread_count' => $this->getUnreadCount(),
        ]);
    }

    /**
     * Create a test notification for development.
     */
    public function createTest()
    {
        $admin = Auth::user();
        
        $testNotifications = [
            [
                'type' => 'booking_alert',
                'title' => 'New Booking Alert',
                'message' => 'A new booking has been created and requires your attention.',
                'action_url' => route('admin.bookings.index'),
                'action_text' => 'View Bookings',
                'priority' => 'high'
            ],
            [
                'type' => 'operator_registration',
                'title' => 'New Operator Registration',
                'message' => 'A new operator has registered and is pending approval.',
                'action_url' => route('admin.operators.index'),
                'action_text' => 'Review Operators',
                'priority' => 'medium'
            ],
            [
                'type' => 'system_alert',
                'title' => 'System Maintenance',
                'message' => 'Scheduled maintenance will begin in 2 hours.',
                'priority' => 'low'
            ],
            [
                'type' => 'revenue_milestone',
                'title' => 'Revenue Milestone',
                'message' => 'Monthly revenue target has been achieved!',
                'action_url' => route('admin.reports.revenue'),
                'action_text' => 'View Reports',
                'priority' => 'medium'
            ]
        ];

        $notification = $testNotifications[array_rand($testNotifications)];
        
        Notification::create([
            'type' => $notification['type'],
            'notifiable_type' => get_class($admin),
            'notifiable_id' => $admin->id,
            'data' => [],
            'title' => $notification['title'],
            'message' => $notification['message'],
            'action_url' => $notification['action_url'] ?? null,
            'action_text' => $notification['action_text'] ?? null,
            'priority' => $notification['priority'],
            'channel' => 'database',
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test notification created successfully',
            'unread_count' => $this->getUnreadCount(),
        ]);
    }

    /**
     * Show notification test page.
     */
    public function testPage()
    {
        $admin = Auth::user();
        $unreadCount = $this->getUnreadCount();
        $recentNotifications = Notification::where('notifiable_type', get_class($admin))
            ->where('notifiable_id', $admin->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.notifications.test', compact('unreadCount', 'recentNotifications'));
    }

    /**
     * Get unread notification count.
     */
    private function getUnreadCount()
    {
        $admin = Auth::user();

        return Notification::where('notifiable_type', get_class($admin))
            ->where('notifiable_id', $admin->id)
            ->whereNull('read_at')
            ->count();
    }
}
