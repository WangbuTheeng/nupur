<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Register') }} - {{ config('app.name', 'BookNGo') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .register-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .floating-animation {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .slide-in-left {
            animation: slideInLeft 0.8s ease-out;
        }
        .slide-in-right {
            animation: slideInRight 0.8s ease-out;
        }
        @keyframes slideInLeft {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Left Side - Project Theme -->
        <div class="hidden lg:flex lg:w-1/2 register-gradient relative overflow-hidden slide-in-left">
            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
            <div class="relative z-10 flex flex-col justify-center items-center p-12 text-white">
                <!-- Logo -->
                <div class="mb-8">
                    <div class="w-20 h-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm mb-4">
                        <i class="fas fa-bus text-white text-3xl"></i>
                    </div>
                    <h1 class="text-4xl font-bold text-center">BookNGo</h1>
                    <p class="text-blue-100 text-center mt-2">Your Journey Starts Here</p>
                </div>
                
                <!-- Customer Benefits -->
                <div class="max-w-md space-y-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-user-plus text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Easy Registration</h3>
                            <p class="text-blue-100 text-sm">Quick and simple customer account creation</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-ticket-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Instant Booking</h3>
                            <p class="text-blue-100 text-sm">Book tickets immediately after registration</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-history text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Booking History</h3>
                            <p class="text-blue-100 text-sm">Track all your travel bookings in one place</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-shield-check text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Verified Operators</h3>
                            <p class="text-blue-100 text-sm">All bus operators are verified by our admin team</p>
                        </div>
                    </div>
                </div>
                
                <!-- Floating Bus Animation -->
                <div class="mt-12 floating-animation">
                    <div class="w-32 h-32 bg-white bg-opacity-10 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-route text-white text-4xl"></i>
                    </div>
                </div>
                
                <!-- Join Stats -->
                <div class="mt-8 text-center">
                    <div class="text-3xl font-bold mb-2">Join 10,000+ Travelers</div>
                    <div class="text-blue-200">Who trust BookNGo for their journeys</div>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-10 right-10 w-20 h-20 bg-white bg-opacity-10 rounded-full"></div>
            <div class="absolute bottom-20 left-10 w-16 h-16 bg-white bg-opacity-10 rounded-full"></div>
            <div class="absolute top-1/3 left-20 w-8 h-8 bg-white bg-opacity-10 rounded-full"></div>
        </div>
        
        <!-- Right Side - Register Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 slide-in-right">
            <div class="max-w-md w-full">
                <!-- Back to Home Link -->
                <div class="mb-8">
                    <a href="{{ url('/') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Home
                    </a>
                </div>
                
                <!-- Register Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-plus text-white text-2xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Create Account</h2>
                    <p class="text-gray-600 mt-2">Join BookNGo and start your journey today</p>
                </div>
                
                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Hidden Role Field (Customer Only) -->
                    <input type="hidden" name="role" value="user">

                    <!-- Account Type Info -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Customer Account</div>
                                <div class="text-sm text-gray-600">Book bus tickets and manage your travel</div>
                            </div>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Bus operators are created by administrators only. Contact support if you're a bus operator.
                        </div>
                    </div>
                    
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="name" 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                   type="text" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required 
                                   autofocus 
                                   autocomplete="name" 
                                   placeholder="Enter your full name" />
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                   type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autocomplete="username" 
                                   placeholder="Enter your email address" />
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input id="phone" 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" 
                                   type="tel" 
                                   name="phone" 
                                   value="{{ old('phone') }}" 
                                   required 
                                   autocomplete="tel" 
                                   placeholder="Enter your phone number" />
                        </div>
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    

                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   type="password"
                                   name="password"
                                   required 
                                   autocomplete="new-password" 
                                   placeholder="Create a strong password" />
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <div class="relative mt-2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password_confirmation" 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                   type="password"
                                   name="password_confirmation"
                                   required 
                                   autocomplete="new-password" 
                                   placeholder="Confirm your password" />
                        </div>
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Terms and Conditions -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" required>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="text-gray-600">
                                I agree to the 
                                <a href="#" class="text-blue-600 hover:text-blue-800 transition duration-200">Terms of Service</a> 
                                and 
                                <a href="#" class="text-blue-600 hover:text-blue-800 transition duration-200">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Register Button -->
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-user-plus mr-2"></i>
                            Create Account
                        </button>
                    </div>
                    
                    <!-- Login Link -->
                    <div class="text-center">
                        <p class="text-gray-600">
                            Already have an account? 
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium transition duration-200">
                                Sign in here
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
