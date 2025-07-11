@extends('layouts.admin')

@section('title', 'Manage Buses')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Manage Buses</h1>
        <a href="{{ route('admin.buses.create') }}" class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-lg font-semibold transition duration-200">
            Add New Bus
        </a>
    </div>

    @if($buses->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @foreach($buses as $bus)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <p class="text-lg font-medium text-gray-900">{{ $bus->display_name }}</p>
                                            @if($bus->is_active)
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
                                            <p class="text-sm text-gray-600">{{ $bus->busType->name }} • {{ $bus->license_plate }}</p>
                                            <p class="text-sm text-gray-500">{{ $bus->total_seats }} seats • {{ $bus->manufacture_year }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.buses.show', $bus) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        View
                                    </a>
                                    <a href="{{ route('admin.buses.edit', $bus) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.buses.destroy', $bus) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this bus?')">
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
            {{ $buses->links() }}
        </div>
    @else
        <div class="bg-white shadow rounded-lg p-6 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No buses</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding a new bus to your fleet.</p>
            <div class="mt-6">
                <a href="{{ route('admin.buses.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Add New Bus
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
