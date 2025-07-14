@extends('layouts.operator')

@section('title', 'Settings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 overflow-hidden shadow-xl rounded-xl mb-8">
        <div class="px-6 py-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Settings</h1>
                    <p class="text-purple-100">Configure your booking and notification preferences</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <div class="flex items-center text-purple-200">
                        <i class="fas fa-cog text-2xl mr-3"></i>
                        <div>
                            <div class="font-medium">{{ $operator->name }}</div>
                            <div class="text-sm">Operator Settings</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Settings Form -->
    <div class="bg-white overflow-hidden shadow-lg rounded-xl">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Operator Settings</h3>
            <p class="text-sm text-gray-500">Configure your booking preferences and notification settings.</p>
        </div>
        
        <form method="POST" action="{{ route('operator.settings.update') }}" class="p-6">
            @csrf
            @method('PUT')

            <!-- Notification Settings -->
            <div class="mb-8">
                <h4 class="text-md font-medium text-gray-900 mb-4">Notification Preferences</h4>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="notification_email" id="notification_email" value="1"
                               {{ old('notification_email', $operator->settings['notification_email'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="notification_email" class="ml-3 block text-sm font-medium text-gray-700">
                            Email Notifications
                        </label>
                    </div>
                    <p class="ml-7 text-sm text-gray-500">Receive email notifications for new bookings, cancellations, and important updates.</p>

                    <div class="flex items-center">
                        <input type="checkbox" name="notification_sms" id="notification_sms" value="1"
                               {{ old('notification_sms', $operator->settings['notification_sms'] ?? false) ? 'checked' : '' }}
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                        <label for="notification_sms" class="ml-3 block text-sm font-medium text-gray-700">
                            SMS Notifications
                        </label>
                    </div>
                    <p class="ml-7 text-sm text-gray-500">Receive SMS notifications for urgent booking updates.</p>
                </div>
            </div>

            <!-- Booking Settings -->
            <div class="mb-8">
                <h4 class="text-md font-medium text-gray-900 mb-4">Booking Settings</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="booking_cutoff_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                            Booking Cutoff (Minutes before departure)
                        </label>
                        <select name="booking_cutoff_minutes" id="booking_cutoff_minutes"
                                class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500">
                            <option value="5" {{ old('booking_cutoff_minutes', $operator->settings['booking_cutoff_minutes'] ?? 10) == 5 ? 'selected' : '' }}>5 minutes</option>
                            <option value="10" {{ old('booking_cutoff_minutes', $operator->settings['booking_cutoff_minutes'] ?? 10) == 10 ? 'selected' : '' }}>10 minutes</option>
                            <option value="15" {{ old('booking_cutoff_minutes', $operator->settings['booking_cutoff_minutes'] ?? 10) == 15 ? 'selected' : '' }}>15 minutes</option>
                            <option value="30" {{ old('booking_cutoff_minutes', $operator->settings['booking_cutoff_minutes'] ?? 10) == 30 ? 'selected' : '' }}>30 minutes</option>
                            <option value="60" {{ old('booking_cutoff_minutes', $operator->settings['booking_cutoff_minutes'] ?? 10) == 60 ? 'selected' : '' }}>1 hour</option>
                            <option value="120" {{ old('booking_cutoff_minutes', $operator->settings['booking_cutoff_minutes'] ?? 10) == 120 ? 'selected' : '' }}>2 hours</option>
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Online booking will close this many minutes before departure.</p>
                    </div>

                    <div>
                        <div class="flex items-center h-full">
                            <div>
                                <input type="checkbox" name="auto_confirm_bookings" id="auto_confirm_bookings" value="1"
                                       {{ old('auto_confirm_bookings', $operator->settings['auto_confirm_bookings'] ?? false) ? 'checked' : '' }}
                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <label for="auto_confirm_bookings" class="ml-3 block text-sm font-medium text-gray-700">
                                    Auto-confirm Bookings
                                </label>
                                <p class="ml-7 text-sm text-gray-500">Automatically confirm bookings upon payment.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancellation Policy -->
            <div class="mb-8">
                <h4 class="text-md font-medium text-gray-900 mb-4">Cancellation Policy</h4>
                <div>
                    <label for="default_cancellation_policy" class="block text-sm font-medium text-gray-700 mb-2">
                        Default Cancellation Policy
                    </label>
                    <textarea name="default_cancellation_policy" id="default_cancellation_policy" rows="4"
                              class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Enter your default cancellation policy...">{{ old('default_cancellation_policy', $operator->settings['default_cancellation_policy'] ?? 'Cancellation allowed up to 24 hours before departure with 10% cancellation fee.') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">This policy will be shown to customers during booking.</p>
                </div>
            </div>

            <!-- Account Information (Read-only) -->
            <div class="mb-8">
                <h4 class="text-md font-medium text-gray-900 mb-4">Account Information</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Account Status</label>
                            <p class="text-sm text-green-600 font-medium">Active</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Member Since</label>
                            <p class="text-sm text-gray-900">{{ $operator->created_at->format('F j, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Buses</label>
                            <p class="text-sm text-gray-900">{{ $operator->buses()->count() }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Schedules</label>
                            <p class="text-sm text-gray-900">{{ $operator->schedules()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('operator.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <i class="fas fa-save mr-2"></i>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
