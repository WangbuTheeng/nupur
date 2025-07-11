@extends('layouts.admin')

@section('title', 'Admin Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Settings</h1>
        <p class="text-gray-600 mt-2">Manage system settings and configurations</p>
    </div>

    <!-- Settings Content -->
    <div class="bg-white shadow-lg rounded-xl p-8">
        <div class="text-center py-12">
            <i class="fas fa-cog text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Settings Panel</h3>
            <p class="text-gray-600 mb-6">System settings and configuration options will be available here.</p>
            <a href="{{ route('admin.dashboard') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
