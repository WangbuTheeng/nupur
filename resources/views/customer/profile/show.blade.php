@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
                        <p class="text-sm text-gray-600 mt-1">Manage your personal information and preferences</p>
                    </div>
                    <a href="{{ route('customer.profile.edit') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-green-800 font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Information -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Personal Information</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Full Name</label>
                                <p class="text-gray-900 font-medium">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Email Address</label>
                                <p class="text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Phone Number</label>
                                <p class="text-gray-900">{{ $user->phone ?: 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Date of Birth</label>
                                <p class="text-gray-900">{{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Gender</label>
                                <p class="text-gray-900">{{ $user->gender ? ucfirst($user->gender) : 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Member Since</label>
                                <p class="text-gray-900">{{ $stats['member_since'] }}</p>
                            </div>
                        </div>
                        
                        @if($user->address)
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                                <p class="text-gray-900">{{ $user->address }}</p>
                            </div>
                        @endif

                        @if($user->emergency_contact_name || $user->emergency_contact_phone)
                            <div class="mt-6">
                                <h3 class="text-md font-medium text-gray-900 mb-3">Emergency Contact</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Contact Name</label>
                                        <p class="text-gray-900">{{ $user->emergency_contact_name ?: 'Not provided' }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-500 mb-1">Contact Phone</label>
                                        <p class="text-gray-900">{{ $user->emergency_contact_phone ?: 'Not provided' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="space-y-6">
                <!-- Account Stats -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Account Statistics</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Bookings</span>
                            <span class="text-lg font-semibold text-gray-900">{{ $stats['total_bookings'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Confirmed Trips</span>
                            <span class="text-lg font-semibold text-green-600">{{ $stats['confirmed_bookings'] }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Total Spent</span>
                            <span class="text-lg font-semibold text-blue-600">Rs. {{ number_format($stats['total_spent'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('customer.bookings.index') }}" 
                           class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h5.586a1 1 0 00.707-.293l5.414-5.414a1 1 0 00.293-.707V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">View My Bookings</span>
                        </a>
                        
                        <a href="{{ route('customer.payments.history') }}" 
                           class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Payment History</span>
                        </a>
                        
                        <a href="{{ route('search.index') }}" 
                           class="flex items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Search Buses</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
