<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Customer Search & Booking Flow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">🧪 Test Customer Search & Booking Flow</h1>
            
            <!-- Test Results -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Cities Test -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">📍 Cities Available</h2>
                    <div class="space-y-2">
                        @if($cities->count() > 0)
                            <div class="text-green-600 font-medium">✅ {{ $cities->count() }} cities found</div>
                            <div class="text-sm text-gray-600">
                                @foreach($cities->take(5) as $city)
                                    <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded mr-2 mb-1">{{ $city->name }}</span>
                                @endforeach
                                @if($cities->count() > 5)
                                    <span class="text-gray-500">... and {{ $cities->count() - 5 }} more</span>
                                @endif
                            </div>
                        @else
                            <div class="text-red-600 font-medium">❌ No cities found</div>
                        @endif
                    </div>
                </div>

                <!-- Schedules Test -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">🚌 Available Schedules</h2>
                    <div class="space-y-2">
                        @if($schedules->count() > 0)
                            <div class="text-green-600 font-medium">✅ {{ $schedules->count() }} schedules found</div>
                            <div class="text-sm text-gray-600">
                                @foreach($schedules->take(3) as $schedule)
                                    <div class="border-l-4 border-blue-500 pl-3 mb-2">
                                        <div class="font-medium">{{ $schedule->route->sourceCity->name }} → {{ $schedule->route->destinationCity->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $schedule->travel_date->format('M d, Y') }} • {{ $schedule->departure_time->format('H:i') }} • NRs {{ number_format($schedule->fare) }}</div>
                                    </div>
                                @endforeach
                                @if($schedules->count() > 3)
                                    <div class="text-gray-500 text-xs">... and {{ $schedules->count() - 3 }} more</div>
                                @endif
                            </div>
                        @else
                            <div class="text-red-600 font-medium">❌ No schedules found</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Search Test -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">🔍 Quick Search Test</h2>
                <form method="POST" action="{{ route('search.results') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                        <select name="source_city_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select departure</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                        <select name="destination_city_id" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select destination</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Travel Date</label>
                        <input type="date" name="travel_date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" 
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            🔍 Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Direct Seat Selection Test -->
            @if($schedules->count() > 0)
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">🪑 Direct Seat Selection Test</h2>
                <div class="space-y-3">
                    @foreach($schedules->take(3) as $schedule)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div>
                                <div class="font-medium">{{ $schedule->route->sourceCity->name }} → {{ $schedule->route->destinationCity->name }}</div>
                                <div class="text-sm text-gray-600">{{ $schedule->travel_date->format('M d, Y') }} • {{ $schedule->departure_time->format('H:i') }} • Rs. {{ number_format($schedule->fare) }}</div>
                                <div class="text-xs text-green-600">{{ $schedule->available_seats }} seats available</div>
                            </div>
                            <a href="{{ route('booking.seat-selection', $schedule) }}" 
                               class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                🪑 Select Seats
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Navigation Links -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">🔗 Navigation Links</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <a href="{{ route('search.index') }}" class="block p-4 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors text-center">
                        🔍 Main Search Page
                    </a>
                    <a href="{{ route('customer.dashboard') }}" class="block p-4 bg-green-100 text-green-800 rounded-lg hover:bg-green-200 transition-colors text-center">
                        📊 Customer Dashboard
                    </a>
                    <a href="{{ route('customer.bookings.index') }}" class="block p-4 bg-purple-100 text-purple-800 rounded-lg hover:bg-purple-200 transition-colors text-center">
                        📋 My Bookings
                    </a>
                </div>
            </div>

            <!-- Debug Information -->
            <div class="bg-gray-50 rounded-lg p-6 mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">🐛 Debug Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <strong>Environment:</strong> {{ app()->environment() }}<br>
                        <strong>Laravel Version:</strong> {{ app()->version() }}<br>
                        <strong>PHP Version:</strong> {{ phpversion() }}<br>
                        <strong>Current Time:</strong> {{ now()->format('Y-m-d H:i:s') }}
                    </div>
                    <div>
                        <strong>Total Cities:</strong> {{ $cities->count() }}<br>
                        <strong>Total Schedules:</strong> {{ $schedules->count() }}<br>
                        <strong>User Authenticated:</strong> {{ auth()->check() ? 'Yes' : 'No' }}<br>
                        @if(auth()->check())
                            <strong>User:</strong> {{ auth()->user()->name }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        console.log('🧪 [TEST-PAGE] Customer search test page loaded');
        console.log('🧪 [TEST-PAGE] Cities available:', {{ $cities->count() }});
        console.log('🧪 [TEST-PAGE] Schedules available:', {{ $schedules->count() }});
    </script>
</body>
</html>
