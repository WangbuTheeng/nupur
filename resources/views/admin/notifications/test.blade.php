@extends('layouts.admin')

@section('title', 'Notification System Test')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 to-teal-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Notification System Test</h1>
                    <p class="text-green-100">Test and verify the admin notification system functionality</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('admin.notifications.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white bg-opacity-20 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-opacity-30 focus:bg-opacity-30 transition ease-in-out duration-150">
                        <i class="fas fa-list mr-2"></i>All Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Status -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow-xl rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-bell text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Unread Notifications</p>
                        <p class="text-2xl font-bold text-gray-900" id="unread-count">{{ $unreadCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">System Status</p>
                        <p class="text-lg font-bold text-green-600">Working</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl rounded-xl">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-purple-600"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Last Update</p>
                        <p class="text-sm font-bold text-gray-900" id="last-update">{{ now()->format('H:i:s') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Actions -->
    <div class="bg-white overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Test Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <button onclick="createTestNotification()" 
                        class="inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i>Create Test Notification
                </button>

                <button onclick="refreshNotifications()" 
                        class="inline-flex items-center justify-center px-4 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 transition ease-in-out duration-150">
                    <i class="fas fa-sync mr-2"></i>Refresh Count
                </button>

                <button onclick="markAllAsRead()" 
                        class="inline-flex items-center justify-center px-4 py-3 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700 focus:bg-yellow-700 transition ease-in-out duration-150">
                    <i class="fas fa-check-double mr-2"></i>Mark All Read
                </button>

                <button onclick="generateSystemNotifications()" 
                        class="inline-flex items-center justify-center px-4 py-3 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 transition ease-in-out duration-150">
                    <i class="fas fa-cogs mr-2"></i>System Notifications
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Notifications -->
    <div class="bg-white overflow-hidden shadow-xl rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Notifications (Last 5)</h3>
        </div>
        <div id="recent-notifications">
            @if($recentNotifications->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($recentNotifications as $notification)
                        <div class="px-6 py-4 {{ $notification->read_at ? '' : 'bg-blue-50' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h4>
                                        @if(!$notification->read_at)
                                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        @endif
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                            @if($notification->priority === 'high') bg-red-100 text-red-800
                                            @elseif($notification->priority === 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($notification->priority) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <i class="fas fa-bell-slash text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No notifications yet</h3>
                    <p class="text-gray-500 mb-4">Create a test notification to see how it works.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Test Results -->
    <div class="mt-8 bg-gray-50 overflow-hidden shadow-xl rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Test Results</h3>
        </div>
        <div class="p-6">
            <div id="test-results" class="space-y-2">
                <p class="text-sm text-gray-600">Click the test buttons above to see results here...</p>
            </div>
        </div>
    </div>
</div>

<script>
    let testResults = [];

    function addTestResult(message, type = 'info') {
        const timestamp = new Date().toLocaleTimeString();
        testResults.unshift(`[${timestamp}] ${message}`);
        
        const resultsDiv = document.getElementById('test-results');
        const colorClass = type === 'success' ? 'text-green-600' : type === 'error' ? 'text-red-600' : 'text-blue-600';
        
        resultsDiv.innerHTML = testResults.map(result => 
            `<p class="text-sm ${colorClass}">${result}</p>`
        ).join('');
    }

    async function createTestNotification() {
        try {
            addTestResult('Creating test notification...', 'info');
            
            const response = await fetch('/admin/notifications/create-test', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                addTestResult('✅ Test notification created successfully!', 'success');
                document.getElementById('unread-count').textContent = data.unread_count;
                document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
                
                // Refresh the page to show new notification
                setTimeout(() => location.reload(), 1000);
            } else {
                addTestResult('❌ Failed to create test notification', 'error');
            }
        } catch (error) {
            addTestResult('❌ Error: ' + error.message, 'error');
        }
    }

    async function refreshNotifications() {
        try {
            addTestResult('Refreshing notification count...', 'info');
            
            const response = await fetch('/admin/notifications/unread-count', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            document.getElementById('unread-count').textContent = data.unread_count;
            document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
            
            addTestResult(`✅ Refreshed! Unread count: ${data.unread_count}`, 'success');
        } catch (error) {
            addTestResult('❌ Error refreshing: ' + error.message, 'error');
        }
    }

    async function markAllAsRead() {
        try {
            addTestResult('Marking all notifications as read...', 'info');
            
            const response = await fetch('/admin/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                addTestResult('✅ All notifications marked as read!', 'success');
                document.getElementById('unread-count').textContent = '0';
                document.getElementById('last-update').textContent = new Date().toLocaleTimeString();
                
                // Refresh the page to update UI
                setTimeout(() => location.reload(), 1000);
            } else {
                addTestResult('❌ Failed to mark notifications as read', 'error');
            }
        } catch (error) {
            addTestResult('❌ Error: ' + error.message, 'error');
        }
    }

    function generateSystemNotifications() {
        addTestResult('System notifications would be generated by background processes...', 'info');
        addTestResult('💡 Tip: Run "php artisan notifications:generate-system" in terminal', 'info');
    }

    // Auto-refresh every 30 seconds
    setInterval(refreshNotifications, 30000);
</script>
@endsection
