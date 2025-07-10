@extends('layouts.app')

@section('title', 'Create New Operator')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="bg-white shadow-lg rounded-xl mb-8 border border-gray-100">
        <div class="px-6 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Operator</h1>
                    <p class="text-gray-600">Add a new bus operator to the system</p>
                </div>
                <a href="{{ route('admin.operators.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Operators
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-100">
        <form method="POST" action="{{ route('admin.operators.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">Contact Person *</label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('contact_person') border-red-500 @enderror">
                        @error('contact_person')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Company Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Company Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Company Name *</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('company_name') border-red-500 @enderror">
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="company_license" class="block text-sm font-medium text-gray-700 mb-2">Company License *</label>
                        <input type="text" name="company_license" id="company_license" value="{{ old('company_license') }}" required
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('company_license') border-red-500 @enderror">
                        @error('company_license')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="company_address" class="block text-sm font-medium text-gray-700 mb-2">Company Address *</label>
                        <textarea name="company_address" id="company_address" rows="3" required
                                  class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('company_address') border-red-500 @enderror">{{ old('company_address') }}</textarea>
                        @error('company_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Security</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                        <input type="password" name="password" id="password" required
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Minimum 8 characters</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
            </div>

            <!-- Permissions -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Permissions</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($permissions as $key => $label)
                        <div class="flex items-center">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}" id="permission_{{ $key }}"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
                            <label for="permission_{{ $key }}" class="ml-2 block text-sm text-gray-900">
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('permissions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.operators.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Operator
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');
    
    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords don't match");
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    password.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
});
</script>
@endsection
