@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Notifications</h1>
                    <p class="text-purple-100">Manage your system notifications and alerts</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button onclick="markAllAsRead()" 
                            class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <i class="fas fa-check-double mr-2"></i>Mark All Read
                    </button>
                    <button onclick="createTestNotification()" 
                            class="inline-flex items-center px-4 py-2 bg-green-500 bg-opacity-80 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-100 focus:bg-opacity-100 transition ease-in-out duration-150">
                        <i class="fas fa-plus mr-2"></i>Test Notification
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notifications List -->
    <div class="bg-white overflow-hidden shadow-xl rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">All Notifications</h3>
        </div>

        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    <div class="px-6 py-4 hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        @switch($notification->type)
                                            @case('booking_alert')
                                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-ticket-alt text-blue-600"></i>
                                                </div>
                                                @break
                                            @case('operator_registration')
                                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user-plus text-green-600"></i>
                                                </div>
                                                @break
                                            @case('system_alert')
                                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                                </div>
                                                @break
                                            @case('revenue_milestone')
                                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-chart-line text-yellow-600"></i>
                                                </div>
                                                @break
                                            @default
                                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-bell text-gray-600"></i>
                                                </div>
                                        @endswitch
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h4>
                                            @if(!$notification->read_at)
                                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                            @endif
                                            @switch($notification->priority)
                                                @case('high')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        High Priority
                                                    </span>
                                                    @break
                                                @case('medium')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Medium Priority
                                                    </span>
                                                    @break
                                                @case('low')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                        Low Priority
                                                    </span>
                                                    @break
                                            @endswitch
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                        <div class="flex items-center space-x-4 mt-2">
                                            <span class="text-xs text-gray-400">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                            @if($notification->action_url)
                                                <a href="{{ $notification->action_url }}" 
                                                   class="text-xs text-blue-600 hover:text-blue-800"
                                                   onclick="markAsRead({{ $notification->id }})">
                                                    {{ $notification->action_text ?? 'View' }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                @if(!$notification->read_at)
                                    <button onclick="markAsRead({{ $notification->id }})" 
                                            class="text-sm text-blue-600 hover:text-blue-800">
                                        Mark as read
                                    </button>
                                @endif
                                <button onclick="deleteNotification({{ $notification->id }})" 
                                        class="text-sm text-red-600 hover:text-red-800">
                                    Delete
                                </button>
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
            <div class="px-6 py-12 text-center">
                <i class="fas fa-bell-slash text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications</h3>
                <p class="text-gray-500 mb-4">You don't have any notifications yet.</p>
                <button onclick="createTestNotification()" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i>Create Test Notification
                </button>
            </div>
        @endif
    </div>
</div>

<script>
    async function markAsRead(notificationId) {
        try {
            const response = await fetch(`/admin/notifications/${notificationId}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    async function markAllAsRead() {
        try {
            const response = await fetch('/admin/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
        }
    }

    async function deleteNotification(notificationId) {
        if (!confirm('Are you sure you want to delete this notification?')) {
            return;
        }

        try {
            const response = await fetch(`/admin/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error deleting notification:', error);
        }
    }

    async function createTestNotification() {
        try {
            const response = await fetch('/admin/notifications/create-test', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error creating test notification:', error);
        }
    }
</script>
@endsection
