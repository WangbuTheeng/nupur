<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Join BookNGO</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Create your account to start booking bus tickets</p>
    </div>

    <form method="POST" action="{{ route('register') }}" x-data="{ role: '{{ old('role', 'user') }}' }">
        @csrf

        <!-- Account Type Selection -->
        <div class="mb-6">
            <x-input-label for="role" :value="__('Account Type')" />
            <div class="mt-2 grid grid-cols-2 gap-3">
                <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-gray-800 p-4 shadow-sm focus:outline-none"
                       :class="role === 'user' ? 'border-indigo-600 ring-2 ring-indigo-600' : 'border-gray-300 dark:border-gray-600'">
                    <input type="radio" name="role" value="user" class="sr-only" x-model="role" />
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900 dark:text-white">🎫 Customer</span>
                            <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">Book bus tickets online</span>
                        </span>
                    </span>
                    <svg class="h-5 w-5 text-indigo-600" :class="role === 'user' ? 'block' : 'hidden'" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </label>

                <label class="relative flex cursor-pointer rounded-lg border bg-white dark:bg-gray-800 p-4 shadow-sm focus:outline-none"
                       :class="role === 'operator' ? 'border-indigo-600 ring-2 ring-indigo-600' : 'border-gray-300 dark:border-gray-600'">
                    <input type="radio" name="role" value="operator" class="sr-only" x-model="role" />
                    <span class="flex flex-1">
                        <span class="flex flex-col">
                            <span class="block text-sm font-medium text-gray-900 dark:text-white">🚌 Bus Operator</span>
                            <span class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">Manage buses & schedules</span>
                        </span>
                    </span>
                    <svg class="h-5 w-5 text-indigo-600" :class="role === 'operator' ? 'block' : 'hidden'" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Personal Information -->
        <div class="space-y-4">
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your full name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email Address')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="your@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Phone Number -->
            <div>
                <x-input-label for="phone" :value="__('Phone Number')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required placeholder="+977-98XXXXXXXX" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <!-- Company Information (Only for Operators) -->
        <div x-show="role === 'operator'" x-transition class="mt-6 space-y-4 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Company Information</h3>

            <div>
                <x-input-label for="company_name" :value="__('Company Name')" />
                <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" placeholder="Your Bus Company Name" />
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="company_address" :value="__('Company Address')" />
                <textarea id="company_address" name="company_address" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Complete company address">{{ old('company_address') }}</textarea>
                <x-input-error :messages="$errors->get('company_address')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="company_license" :value="__('Business License Number')" />
                <x-text-input id="company_license" class="block mt-1 w-full" type="text" name="company_license" :value="old('company_license')" placeholder="Business registration/license number" />
                <x-input-error :messages="$errors->get('company_license')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="contact_person" :value="__('Contact Person')" />
                <x-text-input id="contact_person" class="block mt-1 w-full" type="text" name="contact_person" :value="old('contact_person')" placeholder="Primary contact person name" />
                <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
            </div>
        </div>

        <!-- Password Section -->
        <div class="mt-6 space-y-4">
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" placeholder="Create a strong password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already have an account?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
