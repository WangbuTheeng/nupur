<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BookNGo') }} - @yield('title', 'Bus Booking System')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ mobileMenuOpen: false }">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <!-- Logo -->
                        <a href="{{ url('/') }}" class="flex items-center">
                            <span class="text-2xl font-bold text-blue-600">BookNGo</span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-4">
                        @auth
                            @if(Auth::user()->isAdmin())
                                <a href="{{ url('/admin/dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    Admin Dashboard
                                </a>
                            @else
                                <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    Dashboard
                                </a>
                                <a href="{{ url('/search') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    Search Buses
                                </a>
                                <a href="{{ url('/bookings') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    My Bookings
                                </a>
                            @endif

                            <!-- User Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium mr-2">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    {{ Auth::user()->name }}
                                    <svg class="ml-1 h-4 w-4 transition-transform" :class="{ 'rotate-180': open }" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">Profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                                Register
                            </a>
                        @endauth
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-700 hover:text-blue-600 p-2">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition class="md:hidden bg-white border-b border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                @auth
                    @if(Auth::user()->isAdmin())
                        <a href="{{ url('/admin/dashboard') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium">
                            Admin Dashboard
                        </a>
                    @else
                        <a href="{{ url('/dashboard') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium">
                            Dashboard
                        </a>
                        <a href="{{ url('/search') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium">
                            Search Buses
                        </a>
                        <a href="{{ url('/bookings') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium">
                            My Bookings
                        </a>
                    @endif
                    <a href="{{ route('profile') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium">
                        Profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 text-gray-700 hover:text-red-600 rounded-md text-base font-medium">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-700 hover:text-blue-600 rounded-md text-base font-medium">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-md text-base font-medium">
                        Register
                    </a>
                @endauth
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Page Content -->
        <main class="py-6">
            @yield('content')
        </main>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} BookNGo. All rights reserved.</p>
                <p class="text-gray-400 text-sm mt-2">Your trusted bus booking partner in Nepal</p>
            </div>
        </div>
    </footer>
</body>
</html>
