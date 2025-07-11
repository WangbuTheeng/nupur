@extends('layouts.admin')

@section('title', 'Manage Schedules')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manage Schedules</h1>
        <a href="{{ route('admin.schedules.create') }}" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg font-semibold transition duration-200">
            Add New Schedule
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" id="date" 
                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       value="{{ request('date', date('Y-m-d')) }}">
            </div>
            <div>
                <label for="route_id" class="block text-sm font-medium text-gray-700 mb-1">Route</label>
                <select name="route_id" id="route_id" 
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Routes</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}" {{ request('route_id') == $route->id ? 'selected' : '' }}>
                            {{ $route->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md transition duration-200">
                    Filter
                </button>
            </div>
        </form>
    </div>

    @if($schedules->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @foreach($schedules as $schedule)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-lg font-medium text-gray-900">{{ $schedule->route->full_name }}</p>
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($schedule->status === 'scheduled') bg-blue-100 text-blue-800
                                                @elseif($schedule->status === 'boarding') bg-yellow-100 text-yellow-800
                                                @elseif($schedule->status === 'departed') bg-green-100 text-green-800
                                                @elseif($schedule->status === 'arrived') bg-gray-100 text-gray-800
                                                @else bg-red-100 text-red-800 @endif">
                                                {{ ucfirst($schedule->status) }}
                                            </span>
                                        </div>
                                        <div class="mt-1">
                                            <p class="text-sm text-gray-600">{{ $schedule->bus->display_name }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $schedule->travel_date->format('M d, Y') }} • 
                                                {{ $schedule->departure_time->format('h:i A') }} - {{ $schedule->arrival_time->format('h:i A') }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Fare: NPR {{ number_format($schedule->fare) }} • 
                                                Available: {{ $schedule->available_seats }} seats
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.schedules.show', $schedule) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        View
                                    </a>
                                    <a href="{{ route('admin.schedules.edit', $schedule) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this schedule?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $schedules->appends(request()->query())->links() }}
        </div>
    @else
        <div class="bg-white shadow rounded-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No schedules</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding a new schedule for your buses.</p>
            <div class="mt-6">
                <a href="{{ route('admin.schedules.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Add New Schedule
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
