@extends('layouts.app')

@section('title', 'Manage Routes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manage Routes</h1>
        <a href="{{ route('admin.routes.create') }}" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg font-semibold transition duration-200">
            Add New Route
        </a>
    </div>

    @if($routes->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @foreach($routes as $route)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-lg font-medium text-gray-900">{{ $route->full_name }}</p>
                                            @if($route->is_active)
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Inactive
                                                </span>
                                            @endif
                                        </div>
                                        <div class="mt-1">
                                            <p class="text-sm text-gray-600">{{ $route->distance_km }} km • {{ $route->estimated_duration->format('H:i') }} hours</p>
                                            <p class="text-sm text-gray-500">Base Fare: NPR {{ number_format($route->base_fare) }}</p>
                                            @if($route->stops && count($route->stops) > 0)
                                                <p class="text-sm text-gray-500">Stops: {{ implode(', ', $route->stops) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.routes.show', $route) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        View
                                    </a>
                                    <a href="{{ route('admin.routes.edit', $route) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this route?')">
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
            {{ $routes->links() }}
        </div>
    @else
        <div class="bg-white shadow rounded-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No routes</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding a new route to your system.</p>
            <div class="mt-6">
                <a href="{{ route('admin.routes.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Add New Route
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
