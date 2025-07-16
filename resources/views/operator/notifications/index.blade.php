@extends('layouts.operator')

@section('title', 'Notifications')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Notifications</h1>
                    <p class="text-indigo-100">Stay updated with your booking and schedule alerts</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <div class="flex items-center space-x-3">
                        <button onclick="markAllAsRead()" class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md shadow-sm text-sm font-medium text-indigo-100 bg-indigo-700 hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-check-double mr-2"></i>
                            Mark All Read
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    <div class="p-6 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }} hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $notification->read_at ? 'bg-gray-100' : 'bg-blue-100' }}">
                                    @switch($notification->type)
                                        @case('new_booking')
                                            <i class="fas fa-ticket-alt text-blue-600"></i>
                                            @break
                                        @case('booking_cancelled')
                                            <i class="fas fa-times-circle text-red-600"></i>
                                            @break
                                        @case('booking_confirmed')
                                            <i class="fas fa-check-circle text-green-600"></i>
                                            @break
                                        @case('booking_completed')
                                            <i class="fas fa-flag-checkered text-purple-600"></i>
                                            @break
                                        @case('seat_reserved')
                                            <i class="fas fa-chair text-orange-600"></i>
                                            @break
                                        @case('seat_reservation_expired')
                                            <i class="fas fa-clock text-gray-600"></i>
                                            @break
                                        @case('schedule_reminder')
                                            <i class="fas fa-calendar text-yellow-600"></i>
                                            @break
                                        @case('payment_received')
                                            <i class="fas fa-money-bill text-green-600"></i>
                                            @break
                                        @case('system_alert')
                                            <i class="fas fa-exclamation-triangle text-orange-600"></i>
                                            @break
                                        @default
                                            <i class="fas fa-bell text-gray-600"></i>
                                    @endswitch
                                </div>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="text-xs text-gray-500">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            @if($notification->priority)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    {{ $notification->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                                       ($notification->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                    {{ ucfirst($notification->priority) }} Priority
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-2 ml-4">
                                        @if(!$notification->read_at)
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        @endif
                                        
                                        @if($notification->action_url)
                                            <a href="{{ $notification->action_url }}" 
                                               onclick="markAsRead({{ $notification->id }})"
                                               class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                {{ $notification->action_text ?? 'View' }}
                                                <i class="fas fa-external-link-alt ml-1"></i>
                                            </a>
                                        @endif
                                        
                                        <button onclick="deleteNotification({{ $notification->id }})" 
                                                class="p-1 text-gray-400 hover:text-red-600 focus:outline-none">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-bell-slash text-gray-300 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications yet</h3>
                <p class="text-gray-500">You'll see notifications here when you receive bookings, schedule updates, and other important alerts.</p>
            </div>
        @endif
    </div>
</div>

<script>
async function markAsRead(notificationId) {
    try {
        const response = await fetch(`/operator/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            // Refresh the page to show updated read status
            location.reload();
        }
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
}

async function markAllAsRead() {
    try {
        const response = await fetch('/operator/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error('Failed to mark all notifications as read:', error);
    }
}

async function deleteNotification(notificationId) {
    if (!confirm('Are you sure you want to delete this notification?')) {
        return;
    }
    
    try {
        const response = await fetch(`/operator/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error('Failed to delete notification:', error);
    }
}
</script>
@endsection
