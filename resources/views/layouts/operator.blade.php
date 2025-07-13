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
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-500 relative">
                                    <i class="fas fa-bell text-lg"></i>
                                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"></span>
                                </button>
                                <div x-show="open" @click.away="open = false"
                                     class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50 border">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                                    </div>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <div class="flex items-center">
                                            <i class="fas fa-ticket-alt text-blue-500 mr-3"></i>
                                            <div>
                                                <p class="font-medium">New booking received</p>
                                                <p class="text-xs text-gray-500">2 minutes ago</p>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <div class="flex items-center">
                                            <i class="fas fa-calendar text-green-500 mr-3"></i>
                                            <div>
                                                <p class="font-medium">Schedule reminder</p>
                                                <p class="text-xs text-gray-500">1 hour ago</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- User Info -->
                            <div class="flex items-center space-x-3">
                                <span class="text-sm text-gray-700">{{ Auth::user()->name }}</span>
                                <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </span>
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
