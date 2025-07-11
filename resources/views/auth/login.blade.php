<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Log in') }} - {{ config('app.name', 'BookNGo') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .login-gradient {
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
        <div class="hidden lg:flex lg:w-1/2 login-gradient relative overflow-hidden slide-in-left">
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
                
                <!-- Project Features -->
                <div class="max-w-md space-y-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-clock text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Real-time Booking</h3>
                            <p class="text-blue-100 text-sm">Book tickets instantly with live seat availability</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-shield-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Secure Payments</h3>
                            <p class="text-blue-100 text-sm">Safe transactions with eSewa integration</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-route text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Multiple Routes</h3>
                            <p class="text-blue-100 text-sm">Extensive network across Nepal</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-mobile-alt text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold">Mobile Friendly</h3>
                            <p class="text-blue-100 text-sm">Responsive design for all devices</p>
                        </div>
                    </div>
                </div>
                
                <!-- Floating Bus Animation -->
                <div class="mt-12 floating-animation">
                    <div class="w-32 h-32 bg-white bg-opacity-10 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-bus text-white text-4xl"></i>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="mt-8 grid grid-cols-3 gap-6 text-center">
                    <div>
                        <div class="text-2xl font-bold">10K+</div>
                        <div class="text-blue-200 text-sm">Happy Users</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">50+</div>
                        <div class="text-blue-200 text-sm">Bus Operators</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold">100+</div>
                        <div class="text-blue-200 text-sm">Routes</div>
                    </div>
                </div>
            </div>
            
            <!-- Decorative Elements -->
            <div class="absolute top-10 right-10 w-20 h-20 bg-white bg-opacity-10 rounded-full"></div>
            <div class="absolute bottom-20 left-10 w-16 h-16 bg-white bg-opacity-10 rounded-full"></div>
            <div class="absolute top-1/3 left-20 w-8 h-8 bg-white bg-opacity-10 rounded-full"></div>
        </div>
        
        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 slide-in-right">
            <div class="max-w-md w-full">
                <!-- Back to Home Link -->
                <div class="mb-8">
                    <a href="{{ url('/') }}" class="inline-flex items-center text-gray-600 hover:text-blue-600 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Home
                    </a>
                </div>
                
                <!-- Login Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user text-white text-2xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Welcome Back!</h2>
                    <p class="text-gray-600 mt-2">Sign in to your account to continue</p>
                </div>
                
                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif
                
                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    
                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
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
                                   autofocus 
                                   autocomplete="username" 
                                   placeholder="Enter your email" />
                        </div>
                        @error('email')
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
                                   autocomplete="current-password" 
                                   placeholder="Enter your password" />
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        
                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-600 hover:text-blue-800 transition duration-200" href="{{ route('password.request') }}">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    
                    <!-- Login Button -->
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In
                        </button>
                    </div>
                    
                    <!-- Register Link -->
                    <div class="text-center">
                        <p class="text-gray-600">
                            Don't have an account? 
                            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium transition duration-200">
                                Create one here
                            </a>
                        </p>
                    </div>
                </form>
                
                <!-- Social Login (Optional) -->
                <div class="mt-8">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-gray-50 text-gray-500">Or continue with</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition duration-200">
                            <i class="fab fa-google text-red-500"></i>
                            <span class="ml-2">Google</span>
                        </button>
                        <button class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition duration-200">
                            <i class="fab fa-facebook text-blue-600"></i>
                            <span class="ml-2">Facebook</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
