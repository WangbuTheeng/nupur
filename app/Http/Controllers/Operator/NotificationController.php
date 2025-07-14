<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get notifications for the operator.
     */
    public function index(Request $request)
    {
        $operator = Auth::user();
        
        $notifications = Notification::where('notifiable_type', get_class($operator))
            ->where('notifiable_id', $operator->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'notifications' => $notifications->items(),
                'unread_count' => $this->getUnreadCount(),
                'has_more' => $notifications->hasMorePages(),
            ]);
        }

        return view('operator.notifications.index', compact('notifications'));
    }

    /**
     * Get unread notifications for AJAX requests.
     */
    public function unread()
    {
        $operator = Auth::user();
        
        $notifications = Notification::where('notifiable_type', get_class($operator))
            ->where('notifiable_id', $operator->id)
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
     * Mark notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        // Ensure the notification belongs to the current operator
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
        $operator = Auth::user();
        
        Notification::where('notifiable_type', get_class($operator))
            ->where('notifiable_id', $operator->id)
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
        // Ensure the notification belongs to the current operator
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
     * Get unread notification count.
     */
    private function getUnreadCount()
    {
        $operator = Auth::user();
        
        return Notification::where('notifiable_type', get_class($operator))
            ->where('notifiable_id', $operator->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get notification count for navbar badge.
     */
    public function getCount()
    {
        return response()->json([
            'unread_count' => $this->getUnreadCount(),
        ]);
    }
}
