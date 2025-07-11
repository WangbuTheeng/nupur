@extends('layouts.admin')

@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-gray-600 mt-2">User Details and Activity</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('admin.users.edit', $user) }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-edit mr-2"></i>Edit User
                </a>
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- User Information -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Basic Information -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Basic Information</h2>
                    <div class="flex items-center space-x-2">
                        @if($user->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Inactive
                            </span>
                        @endif
                        @foreach($user->roles as $role)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ ucfirst($role->name) }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Full Name</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email Address</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Date of Birth</label>
                        <p class="mt-1 text-lg text-gray-900">
                            {{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'Not provided' }}
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Gender</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->gender ? ucfirst($user->gender) : 'Not specified' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Member Since</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    @if($user->address)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Address</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $user->address }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Emergency Contact -->
            @if($user->emergency_contact_name || $user->emergency_contact_phone)
                <div class="bg-white shadow-lg rounded-xl p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Emergency Contact</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Contact Name</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $user->emergency_contact_name ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Contact Phone</label>
                            <p class="mt-1 text-lg text-gray-900">{{ $user->emergency_contact_phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Bookings -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Bookings</h2>
                    <span class="text-sm text-gray-500">Last 10 bookings</span>
                </div>

                @if($user->bookings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($user->bookings->take(10) as $booking)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $booking->booking_reference }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $booking->schedule->route->full_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $booking->schedule->travel_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                @if($booking->status === 'confirmed') bg-green-100 text-green-800
                                                @elseif($booking->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            Rs. {{ number_format($booking->total_amount) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-ticket-alt text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Bookings Yet</h3>
                        <p class="text-gray-500">This user hasn't made any bookings yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Bookings</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $stats['total_bookings'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Confirmed Bookings</span>
                        <span class="text-lg font-semibold text-green-600">{{ $stats['confirmed_bookings'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Cancelled Bookings</span>
                        <span class="text-lg font-semibold text-red-600">{{ $stats['cancelled_bookings'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Spent</span>
                        <span class="text-lg font-semibold text-blue-600">Rs. {{ number_format($stats['total_spent'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Average Booking</span>
                        <span class="text-lg font-semibold text-purple-600">Rs. {{ number_format($stats['average_booking'] ?? 0) }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200">
                            @if($user->is_active)
                                <i class="fas fa-user-slash mr-2 text-red-500"></i>Deactivate User
                            @else
                                <i class="fas fa-user-check mr-2 text-green-500"></i>Activate User
                            @endif
                        </button>
                    </form>

                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200"
                                onclick="return confirm('Are you sure you want to reset this user\'s password?')">
                            <i class="fas fa-key mr-2 text-blue-500"></i>Reset Password
                        </button>
                    </form>

                    <a href="{{ route('admin.users.edit', $user) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-edit mr-2 text-green-500"></i>Edit User
                    </a>

                    @if($user->bookings->count() === 0)
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full flex items-center justify-center px-4 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 hover:bg-red-50 transition duration-200"
                                    onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                <i class="fas fa-trash mr-2"></i>Delete User
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Email Verified</span>
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check mr-1"></i>Verified
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times mr-1"></i>Not Verified
                            </span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Last Login</span>
                        <span class="text-sm text-gray-900">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Created</span>
                        <span class="text-sm text-gray-900">{{ $user->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Updated</span>
                        <span class="text-sm text-gray-900">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
