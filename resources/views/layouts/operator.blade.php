<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Operator Dashboard') - {{ config('app.name', 'BookNGO') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Seat Map CSS -->
    <link rel="stylesheet" href="{{ asset('css/seat-map.css') }}">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('meta')

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>

    <script>
        function notificationDropdown() {
            return {
                open: false,
                loading: false,
                notifications: [],
                unreadCount: 0,

                init() {
                    this.loadNotifications();
                    this.loadUnreadCount();
                    // Refresh notifications every 30 seconds
                    setInterval(() => {
                        this.loadUnreadCount();
                        if (this.open) {
                            this.loadNotifications();
                        }
                    }, 30000);
                },

                toggleDropdown() {
                    this.open = !this.open;
                    if (this.open) {
                        this.loadNotifications();
                    }
                },

                async loadNotifications() {
                    this.loading = true;
                    try {
                        const response = await fetch('{{ route("operator.notifications.unread") }}', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await response.json();
                        this.notifications = data.notifications;
                        this.unreadCount = data.unread_count;
                    } catch (error) {
                        console.error('Failed to load notifications:', error);
                    } finally {
                        this.loading = false;
                    }
                },

                async loadUnreadCount() {
                    try {
                        const response = await fetch('{{ route("operator.notifications.count") }}', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        const data = await response.json();
                        this.unreadCount = data.unread_count;
                    } catch (error) {
                        console.error('Failed to load notification count:', error);
                    }
                },

                async markAsRead(notification) {
                    if (notification.read_at) return;

                    try {
                        const response = await fetch(`/operator/notifications/${notification.id}/read`, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            notification.read_at = new Date().toISOString();
                            this.unreadCount = data.unread_count;

                            // Navigate to action URL if available
                            if (notification.action_url) {
                                window.location.href = notification.action_url;
                            }
                        }
                    } catch (error) {
                        console.error('Failed to mark notification as read:', error);
                    }
                },

                async markAllAsRead() {
                    try {
                        const response = await fetch('{{ route("operator.notifications.mark-all-read") }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.notifications.forEach(n => n.read_at = new Date().toISOString());
                            this.unreadCount = 0;
                        }
                    } catch (error) {
                        console.error('Failed to mark all notifications as read:', error);
                    }
                },

                getNotificationIcon(type) {
                    const icons = {
                        'new_booking': 'fas fa-ticket-alt text-blue-500',
                        'booking_cancelled': 'fas fa-times-circle text-red-500',
                        'booking_confirmed': 'fas fa-check-circle text-green-500',
                        'schedule_reminder': 'fas fa-calendar text-yellow-500',
                        'payment_received': 'fas fa-money-bill text-green-500',
                        'system_alert': 'fas fa-exclamation-triangle text-orange-500',
                        'default': 'fas fa-bell text-gray-500'
                    };
                    return icons[type] || icons.default;
                },

                formatTime(dateString) {
                    const date = new Date(dateString);
                    const now = new Date();
                    const diffInMinutes = Math.floor((now - date) / (1000 * 60));

                    if (diffInMinutes < 1) return 'Just now';
                    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
                    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
                    return `${Math.floor(diffInMinutes / 1440)}d ago`;
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-64 md:flex-col">
            <div class="flex flex-col flex-grow pt-5 bg-gradient-to-b from-blue-600 to-blue-800 overflow-y-auto">
                <!-- Logo -->
                <div class="flex items-center flex-shrink-0 px-4">
                    <a href="{{ route('operator.dashboard') }}" class="flex items-center">
                        <i class="fas fa-bus text-white text-2xl mr-3"></i>
                        <div>
                            <h1 class="text-xl font-bold text-white">BookNGO</h1>
                            <p class="text-blue-200 text-sm">Operator Panel</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <div class="mt-8 flex-grow flex flex-col">
                    <nav class="flex-1 px-2 space-y-1">
                        <a href="{{ route('operator.dashboard') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('operator.dashboard*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                            <i class="fas fa-tachometer-alt mr-3 flex-shrink-0 h-6 w-6"></i>
                            Dashboard
                        </a>

                        <a href="{{ route('operator.buses.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('operator.buses.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                            <i class="fas fa-bus mr-3 flex-shrink-0 h-6 w-6"></i>
                            My Buses
                        </a>

                        <a href="{{ route('operator.schedules.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('operator.schedules.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                            <i class="fas fa-calendar-alt mr-3 flex-shrink-0 h-6 w-6"></i>
                            Schedules
                        </a>

                        <a href="{{ route('operator.routes.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('operator.routes.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                            <i class="fas fa-route mr-3 flex-shrink-0 h-6 w-6"></i>
                            Routes
                        </a>

                        <a href="{{ route('operator.bookings.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('operator.bookings.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                            <i class="fas fa-ticket-alt mr-3 flex-shrink-0 h-6 w-6"></i>
                            Bookings
                        </a>

                        <a href="{{ route('operator.counter.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('operator.counter.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                            <i class="fas fa-cash-register mr-3 flex-shrink-0 h-6 w-6"></i>
                            Counter Booking
                        </a>

                        <a href="{{ route('operator.reports.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('operator.reports.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                            <i class="fas fa-chart-bar mr-3 flex-shrink-0 h-6 w-6"></i>
                            Reports
                        </a>

                        <a href="{{ route('operator.notifications.index') }}"
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('operator.notifications.*') ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-700 hover:text-white' }}">
                            <i class="fas fa-bell mr-3 flex-shrink-0 h-6 w-6"></i>
                            Notifications
                        </a>
                    </nav>

                    <!-- User Section -->
                    <div class="flex-shrink-0 flex border-t border-blue-700 p-4">
                        <div class="flex-shrink-0 w-full group block" x-data="{ open: false }">
                            <div class="flex items-center">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                    <div class="relative">
                                        <button @click="open = !open" class="text-xs text-blue-200 hover:text-white focus:outline-none">
                                            Account Settings
                                        </button>
                                        <div x-show="open" @click.away="open = false"
                                             class="absolute bottom-full left-0 mb-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                            <a href="{{ route('operator.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-user mr-2"></i>Profile
                                            </a>
                                            <a href="{{ route('operator.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-cog mr-2"></i>Settings
                                            </a>
                                            <div class="border-t border-gray-100"></div>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <!-- Mobile menu button -->
                            <button type="button" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>

                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <div class="relative" x-data="notificationDropdown()" x-init="init()">
                                <button @click="toggleDropdown()" class="p-2 text-gray-400 hover:text-gray-500 relative focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg">
                                    <i class="fas fa-bell text-lg"></i>
                                    <span x-show="unreadCount > 0" x-text="unreadCount > 99 ? '99+' : unreadCount"
                                          class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-[18px] h-[18px]"></span>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200 max-h-96 overflow-hidden">

                                    <!-- Header -->
                                    <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                                        <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                                        <div class="flex items-center space-x-2">
                                            <button @click="markAllAsRead()" x-show="unreadCount > 0" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                Mark all read
                                            </button>
                                            <a href="{{ route('operator.notifications.index') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                View all
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Notifications List -->
                                    <div class="max-h-80 overflow-y-auto">
                                        <div x-show="loading" class="p-4 text-center">
                                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                                            <p class="text-sm text-gray-500 mt-2">Loading notifications...</p>
                                        </div>

                                        <div x-show="!loading && notifications.length === 0" class="p-6 text-center">
                                            <i class="fas fa-bell-slash text-gray-300 text-2xl mb-2"></i>
                                            <p class="text-sm text-gray-500">No notifications yet</p>
                                        </div>

                                        <template x-for="notification in notifications" :key="notification.id">
                                            <div class="border-b border-gray-50 last:border-b-0">
                                                <div @click="markAsRead(notification)"
                                                     :class="notification.read_at ? 'bg-white' : 'bg-blue-50'"
                                                     class="block px-4 py-3 hover:bg-gray-50 cursor-pointer transition-colors">
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0 mt-1">
                                                            <i :class="getNotificationIcon(notification.type)" class="text-sm"></i>
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <p class="text-sm font-medium text-gray-900" x-text="notification.title"></p>
                                                            <p class="text-sm text-gray-600 mt-1" x-text="notification.message"></p>
                                                            <p class="text-xs text-gray-500 mt-1" x-text="formatTime(notification.created_at)"></p>
                                                        </div>
                                                        <div x-show="!notification.read_at" class="flex-shrink-0">
                                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- User Info with Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" type="button" class="flex items-center space-x-3 text-sm rounded-lg hover:bg-gray-50 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out">
                                    <span class="text-sm text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center shadow-sm">
                                        <span class="text-sm font-medium text-white">
                                            {{ substr(Auth::user()->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-150" :class="{ 'rotate-180': open }"></i>
                                </button>

                                <!-- Dropdown Menu -->
                                <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5 border border-gray-200">
                                    <div class="px-4 py-3 border-b border-gray-100 bg-gray-50">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ substr(Auth::user()->name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                                @if(Auth::user()->company_name)
                                                    <p class="text-xs text-blue-600 truncate font-medium">{{ Auth::user()->company_name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <a href="{{ route('operator.profile') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition duration-150 ease-in-out">
                                        <i class="fas fa-user mr-3 text-gray-400 w-4"></i>
                                        <span>Profile</span>
                                    </a>

                                    <a href="{{ route('operator.settings') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition duration-150 ease-in-out">
                                        <i class="fas fa-cog mr-3 text-gray-400 w-4"></i>
                                        <span>Settings</span>
                                    </a>

                                    <div class="border-t border-gray-100 my-1"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 transition duration-150 ease-in-out">
                                            <i class="fas fa-sign-out-alt mr-3 text-gray-400 w-4"></i>
                                            <span>Logout</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <div class="px-4 sm:px-6 lg:px-8 mt-4">
                @if(session('success'))
                    <div class="rounded-md bg-green-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="rounded-md bg-red-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="rounded-md bg-yellow-50 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" class="inline-flex bg-yellow-50 rounded-md p-1.5 text-yellow-500 hover:bg-yellow-100 focus:outline-none">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
