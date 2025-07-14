<nav id="main-navbar" class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50 backdrop-blur-sm bg-white/95">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group logo-container">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300 transform group-hover:scale-105">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-2a2 2 0 00-2-2H8z"></path>
                            </svg>
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent">BookNGO</h1>
                            <p class="text-xs text-gray-500 -mt-1">Travel Smart</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden lg:flex lg:items-center lg:space-x-1 lg:ml-10">
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700 shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v6m8-6v6"></path>
                        </svg>
                        Dashboard
                    </a>

                    @if(Auth::user()->hasRole('admin'))
                        <a href="{{ url('/admin/users') }}"
                           class="flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('admin/users*') ? 'bg-blue-100 text-blue-700 shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Users
                        </a>
                        <a href="{{ url('/admin/operators') }}"
                           class="flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('admin/operators*') ? 'bg-blue-100 text-blue-700 shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Operators
                        </a>
                        <a href="{{ url('/admin/buses') }}"
                           class="flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('admin/buses*') ? 'bg-blue-100 text-blue-700 shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-2a2 2 0 00-2-2H8z"></path>
                            </svg>
                            Buses
                        </a>
                        <a href="{{ url('/admin/routes') }}"
                           class="flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('admin/routes*') ? 'bg-blue-100 text-blue-700 shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Routes
                        </a>
                    @elseif(Auth::user()->hasRole('operator'))
                        <a href="{{ url('/operator/buses') }}"
                           class="flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('operator/buses*') ? 'bg-blue-100 text-blue-700 shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-2a2 2 0 00-2-2H8z"></path>
                            </svg>
                            My Buses
                        </a>
                        <a href="{{ url('/operator/schedules') }}"
                           class="flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('operator/schedules*') ? 'bg-blue-100 text-blue-700 shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Schedules
                        </a>
                        <a href="{{ url('/operator/bookings') }}"
                           class="flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('operator/bookings*') ? 'bg-blue-100 text-blue-700 shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Bookings
                        </a>
                    @else
                        <a href="{{ route('search.index') }}"
                           class="flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('search*') ? 'bg-blue-100 text-blue-700 shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Search Buses
                        </a>
                        <a href="{{ route('customer.bookings.index') }}"
                           class="flex items-center px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('bookings*') ? 'bg-blue-100 text-blue-700 shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            My Bookings
                        </a>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden lg:flex lg:items-center lg:space-x-4">
                <!-- Notifications -->
                <div class="relative">
                    <button id="notification-button"
                            class="relative p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all duration-200 nav-button">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.5a6 6 0 0 1 6 6v2l1.5 3h-15l1.5-3v-2a6 6 0 0 1 6-6z"></path>
                        </svg>
                        <span class="notification-badge absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div id="notification-dropdown"
                         class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl dropdown-shadow border border-gray-200 py-2 z-50 max-w-screen-sm hidden opacity-0 scale-95 transform translate-y-1 transition-all duration-200">

                        <div class="px-4 py-3 border-b border-gray-100">
                            <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                        </div>

                        <div class="max-h-64 overflow-y-auto">
                            <div class="px-4 py-3 text-center text-gray-500 text-sm">
                                No new notifications
                            </div>
                        </div>

                        <div class="border-t border-gray-100 px-4 py-2">
                            <a href="#" class="text-xs text-blue-600 hover:text-blue-700">View all notifications</a>
                        </div>
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <div class="relative">
                    <button id="user-dropdown-button"
                            class="flex items-center space-x-3 px-4 py-2 rounded-xl bg-gray-50 hover:bg-gray-100 transition-all duration-200 group">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="hidden sm:block text-left">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ ucfirst(Auth::user()->role ?? 'Customer') }}</p>
                        </div>
                        <svg id="user-dropdown-arrow" class="w-4 h-4 text-gray-500 group-hover:text-gray-700 transition-transform duration-200"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="user-dropdown"
                         class="absolute right-0 mt-2 w-64 sm:w-72 bg-white rounded-2xl dropdown-shadow border border-gray-200 py-2 z-50 hidden opacity-0 scale-95 transform translate-y-1 transition-all duration-200">

                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                                    <span class="text-white font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                    <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                                        {{ ucfirst(Auth::user()->role ?? 'Customer') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-2">
                            <a href="{{ route('profile.edit') }}"
                               @click="closeAllDropdowns()"
                               class="dropdown-item flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile Settings
                            </a>

                            @if(!Auth::user()->hasRole('admin'))
                            <a href="#"
                               @click="closeAllDropdowns()"
                               class="dropdown-item flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.5 3.5a6 6 0 0 1 6 6v2l1.5 3h-15l1.5-3v-2a6 6 0 0 1 6-6z"></path>
                                </svg>
                                Notifications
                            </a>
                            @endif

                            <a href="#"
                               @click="closeAllDropdowns()"
                               class="dropdown-item flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Settings
                            </a>
                        </div>

                        <!-- Logout -->
                        <div class="border-t border-gray-100 pt-2">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center lg:hidden">
                <!-- Mobile User Avatar (visible on small screens) -->
                <div class="flex items-center space-x-3 mr-3 sm:hidden">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                        <span class="text-white text-sm font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                </div>

                <button id="mobile-menu-button"
                        class="inline-flex items-center justify-center p-2 rounded-xl text-gray-600 hover:text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                    <svg id="mobile-menu-icon" class="h-6 w-6 transition-transform duration-200" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path id="hamburger-icon" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Backdrop -->
    <div id="mobile-backdrop"
         class="fixed inset-0 bg-black bg-opacity-25 z-40 lg:hidden hidden opacity-0 transition-opacity duration-300"></div>

    <!-- Mobile Navigation Menu -->
    <div id="mobile-menu"
         class="lg:hidden bg-white border-t border-gray-200 shadow-lg relative z-50 hidden opacity-0 transform -translate-y-2 transition-all duration-300">
        <div class="px-4 py-6 space-y-2">
            <a href="{{ route('dashboard') }}"
               class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v6m8-6v6"></path>
                </svg>
                Dashboard
            </a>

            @if(Auth::user()->hasRole('admin'))
                <a href="{{ url('/admin/users') }}"
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('admin/users*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Users
                </a>
                <a href="{{ url('/admin/operators') }}"
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('admin/operators*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Operators
                </a>
                <a href="{{ url('/admin/buses') }}"
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('admin/buses*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-2a2 2 0 00-2-2H8z"></path>
                    </svg>
                    Buses
                </a>
                <a href="{{ url('/admin/routes') }}"
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('admin/routes*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Routes
                </a>
            @elseif(Auth::user()->hasRole('operator'))
                <a href="{{ url('/operator/buses') }}"
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('operator/buses*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-2a2 2 0 00-2-2H8z"></path>
                    </svg>
                    My Buses
                </a>
                <a href="{{ url('/operator/schedules') }}"
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('operator/schedules*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Schedules
                </a>
                <a href="{{ url('/operator/bookings') }}"
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('operator/bookings*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Bookings
                </a>
            @else
                <a href="{{ route('search.index') }}"
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('search*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search Buses
                </a>
                <a href="{{ route('customer.bookings.index') }}"
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ request()->is('bookings*') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    My Bookings
                </a>
            @endif
        </div>

        <!-- Mobile User Profile -->
        <div class="border-t border-gray-200 px-4 py-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <span class="text-white font-semibold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                    <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <div class="space-y-2">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center px-4 py-3 rounded-xl text-sm font-semibold text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-all duration-200">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile Settings
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center w-full px-4 py-3 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 transition-all duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Vanilla JavaScript Navbar Implementation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 [NAVBAR] Initializing vanilla JavaScript navbar');

    // State management
    let state = {
        userDropdownOpen: false,
        notificationOpen: false,
        mobileOpen: false
    };

    // Get DOM elements
    const elements = {
        userButton: document.getElementById('user-dropdown-button'),
        userDropdown: document.getElementById('user-dropdown'),
        userArrow: document.getElementById('user-dropdown-arrow'),

        notificationButton: document.getElementById('notification-button'),
        notificationDropdown: document.getElementById('notification-dropdown'),

        mobileButton: document.getElementById('mobile-menu-button'),
        mobileMenu: document.getElementById('mobile-menu'),
        mobileBackdrop: document.getElementById('mobile-backdrop'),
        mobileIcon: document.getElementById('mobile-menu-icon'),
        hamburgerIcon: document.getElementById('hamburger-icon'),
        closeIcon: document.getElementById('close-icon')
    };

    // Utility functions
    function closeAllDropdowns() {
        console.log('🔒 Closing all dropdowns');
        state.userDropdownOpen = false;
        state.notificationOpen = false;
        updateUI();
    }

    function toggleUserDropdown() {
        console.log('👤 Toggling user dropdown');
        state.notificationOpen = false;
        state.userDropdownOpen = !state.userDropdownOpen;
        console.log('👤 User dropdown is now:', state.userDropdownOpen ? 'OPEN' : 'CLOSED');
        updateUI();
    }

    function toggleNotificationDropdown() {
        console.log('🔔 Toggling notification dropdown');
        state.userDropdownOpen = false;
        state.notificationOpen = !state.notificationOpen;
        console.log('🔔 Notification dropdown is now:', state.notificationOpen ? 'OPEN' : 'CLOSED');
        updateUI();
    }

    function toggleMobileMenu() {
        console.log('📱 Toggling mobile menu');
        state.mobileOpen = !state.mobileOpen;
        if (state.mobileOpen) {
            closeAllDropdowns();
        }
        console.log('📱 Mobile menu is now:', state.mobileOpen ? 'OPEN' : 'CLOSED');
        updateUI();
    }

    function updateUI() {
        // User dropdown
        if (elements.userDropdown) {
            if (state.userDropdownOpen) {
                elements.userDropdown.classList.remove('hidden', 'opacity-0', 'scale-95', 'translate-y-1');
                elements.userDropdown.classList.add('opacity-100', 'scale-100', 'translate-y-0');
            } else {
                elements.userDropdown.classList.add('hidden', 'opacity-0', 'scale-95', 'translate-y-1');
                elements.userDropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
            }
        }

        // User arrow rotation
        if (elements.userArrow) {
            if (state.userDropdownOpen) {
                elements.userArrow.classList.add('rotate-180');
            } else {
                elements.userArrow.classList.remove('rotate-180');
            }
        }

        // Notification dropdown
        if (elements.notificationDropdown) {
            if (state.notificationOpen) {
                elements.notificationDropdown.classList.remove('hidden', 'opacity-0', 'scale-95', 'translate-y-1');
                elements.notificationDropdown.classList.add('opacity-100', 'scale-100', 'translate-y-0');
            } else {
                elements.notificationDropdown.classList.add('hidden', 'opacity-0', 'scale-95', 'translate-y-1');
                elements.notificationDropdown.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
            }
        }

        // Mobile menu
        if (elements.mobileMenu && elements.mobileBackdrop) {
            if (state.mobileOpen) {
                elements.mobileMenu.classList.remove('hidden', 'opacity-0', '-translate-y-2');
                elements.mobileMenu.classList.add('opacity-100', 'translate-y-0');
                elements.mobileBackdrop.classList.remove('hidden', 'opacity-0');
                elements.mobileBackdrop.classList.add('opacity-100');
                elements.mobileButton.classList.add('bg-blue-50', 'text-blue-600');
            } else {
                elements.mobileMenu.classList.add('hidden', 'opacity-0', '-translate-y-2');
                elements.mobileMenu.classList.remove('opacity-100', 'translate-y-0');
                elements.mobileBackdrop.classList.add('hidden', 'opacity-0');
                elements.mobileBackdrop.classList.remove('opacity-100');
                elements.mobileButton.classList.remove('bg-blue-50', 'text-blue-600');
            }
        }

        // Mobile menu icons
        if (elements.hamburgerIcon && elements.closeIcon) {
            if (state.mobileOpen) {
                elements.hamburgerIcon.classList.add('hidden');
                elements.hamburgerIcon.classList.remove('inline-flex');
                elements.closeIcon.classList.remove('hidden');
                elements.closeIcon.classList.add('inline-flex');
                elements.mobileIcon.classList.add('rotate-90');
            } else {
                elements.hamburgerIcon.classList.remove('hidden');
                elements.hamburgerIcon.classList.add('inline-flex');
                elements.closeIcon.classList.add('hidden');
                elements.closeIcon.classList.remove('inline-flex');
                elements.mobileIcon.classList.remove('rotate-90');
            }
        }
    }

    // Event listeners
    if (elements.userButton) {
        elements.userButton.addEventListener('click', toggleUserDropdown);
    }

    if (elements.notificationButton) {
        elements.notificationButton.addEventListener('click', toggleNotificationDropdown);
    }

    if (elements.mobileButton) {
        elements.mobileButton.addEventListener('click', toggleMobileMenu);
    }

    if (elements.mobileBackdrop) {
        elements.mobileBackdrop.addEventListener('click', () => {
            state.mobileOpen = false;
            updateUI();
        });
    }

    // Click outside to close dropdowns
    document.addEventListener('click', function(event) {
        const isUserDropdown = elements.userButton?.contains(event.target) || elements.userDropdown?.contains(event.target);
        const isNotificationDropdown = elements.notificationButton?.contains(event.target) || elements.notificationDropdown?.contains(event.target);
        const isMobileMenu = elements.mobileButton?.contains(event.target) || elements.mobileMenu?.contains(event.target);

        if (!isUserDropdown && !isNotificationDropdown && !isMobileMenu) {
            if (state.userDropdownOpen || state.notificationOpen) {
                closeAllDropdowns();
            }
        }
    });

    // ESC key to close all
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (state.userDropdownOpen || state.notificationOpen || state.mobileOpen) {
                state.userDropdownOpen = false;
                state.notificationOpen = false;
                state.mobileOpen = false;
                updateUI();
            }
        }
    });

    // Initialize UI
    updateUI();

    console.log('✅ [NAVBAR] Vanilla JavaScript navbar initialized successfully');
    console.log('🔍 Elements found:', {
        userButton: !!elements.userButton,
        userDropdown: !!elements.userDropdown,
        notificationButton: !!elements.notificationButton,
        notificationDropdown: !!elements.notificationDropdown,
        mobileButton: !!elements.mobileButton,
        mobileMenu: !!elements.mobileMenu
    });
});
</script>
