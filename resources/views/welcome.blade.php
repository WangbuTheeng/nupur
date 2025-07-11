<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased">
        <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-red-500 selection:text-white">
            @if (Route::has('login'))
                <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <div class="flex justify-center">
                    <svg class="w-16 h-16 text-gray-800 dark:text-gray-200" viewBox="0 0 62 65" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8898 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0351V49.1337C49.9104 49.4902 49.7209 49.8192 49.4118 49.9884L25.4519 63.7861C25.3971 63.8133 25.3372 63.8427 25.2774 63.8649C25.255 63.8734 25.2333 63.8851 25.2101 63.8913C25.0426 63.9354 24.8666 63.9354 24.6991 63.8913C24.6759 63.8851 24.6542 63.8734 24.6318 63.8649C24.5719 63.8427 24.5121 63.8133 24.4573 63.7861L0.497311 49.9884C0.188235 49.8192 -0.00123596 49.4902 -0.00123596 49.1337V14.8858C-0.00123596 14.7102 0.0452632 14.5377 0.133212 14.3859C0.221161 14.234 0.347751 14.108 0.499931 14.0207L12.4456 7.05445C12.4456 7.05445 12.4456 7.05445 12.4456 7.05445L24.9004 0.342604C25.0529 0.254004 25.2266 0.208374 25.4034 0.208374C25.5802 0.208374 25.7538 0.254004 25.9064 0.342604L49.2518 13.4056C49.5609 13.5748 49.7504 13.9038 49.7504 14.2603V27.8615L61.3887 21.2529C61.5409 21.1656 61.6675 21.0396 61.7554 20.8878C61.8434 20.7359 61.8898 20.5634 61.8897 20.3879V14.8858C61.8898 14.7978 61.8781 14.7102 61.8548 14.6253Z" fill="currentColor"/><path d="M25.4034 0.208374C25.2266 0.208374 25.0529 0.254004 24.9004 0.342604L12.4456 7.05445C12.4456 7.05445 12.4456 7.05445 12.4456 7.05445L0.499931 14.0207C0.347751 14.108 0.221161 14.234 0.133212 14.3859C0.0452632 14.5377 -0.00123596 14.7102 -0.00123596 14.8858V49.1337C-0.00123596 49.4902 0.188235 49.8192 0.497311 49.9884L24.4573 63.7861C24.5121 63.8133 24.5719 63.8427 24.6318 63.8649C24.6542 63.8734 24.6759 63.8851 24.6991 63.8913C24.8666 63.9354 25.0426 63.9354 25.2101 63.8913C25.2333 63.8851 25.255 63.8734 25.2774 63.8649C25.3372 63.8427 25.3971 63.8133 25.4519 63.7861L49.4118 49.9884C49.7209 49.8192 49.9104 49.4902 49.9104 49.1337V36.0351L61.3887 29.4265C61.5409 29.3392 61.6675 29.2132 61.7554 29.0614C61.8434 28.9095 61.8898 28.737 61.8897 28.5615V14.8858C61.8895 14.7978 61.8778 14.7102 61.8548 14.6253C61.8548 14.6253 61.8548 14.6253 61.8548 14.6253Z" fill="currentColor"/></svg>
                </div>

                <div class="mt-16">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                        <a href="https://laravel.com/docs" class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                            <div>
                                <div class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full">
                                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0118 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                                    </svg>
                                </div>

                                <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">Documentation</h2>

                                <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                    Laravel has wonderful documentation covering every aspect of the framework. Whether you are a newcomer or have prior experience with Laravel, we recommend reading our documentation from beginning to end.
                                </p>
                            </div>

                            <svg class="self-center shrink-0 stroke-red-500 w-6 h-6 mx-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"></path>
                            </svg>
                        </a>

                        <a href="https://laracasts.com" class="scale-100 p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent dark:ring-1 dark:ring-inset dark:ring-white/5 rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none flex motion-safe:hover:scale-[1.01] transition-all duration-250 focus:outline focus:outline-2 focus:outline-red-500">
                            <div>
                                <div class="h-16 w-16 bg-red-50 dark:bg-red-800/20 flex items-center justify-center rounded-full">
                                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"></path>
                                    </svg>
                                </div>

                                <h2 class="mt-6 text-xl font-semibold text-gray-900 dark:text-white">Laracasts</h2>

                                <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                                    Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript development. Check them out, see for yourself, and massively level up your development skills in the process.
                                </p>
                            </div>

                            <svg class="self-center shrink-0 stroke-red-500 w-6 h-6 mx-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="flex justify-center mt-16 px-0 sm:items-center sm:justify-between">
                    <div class="text-center text-sm text-gray-500 dark:text-gray-400 sm:text-left">
                        <div class="flex items-center gap-4">
                            <a href="https://github.com/sponsors/taylorotwell" class="group inline-flex items-center hover:text-gray-700 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                                <svg class="w-5 h-5 fill-gray-400 group-hover:fill-gray-600 dark:group-hover:fill-gray-300" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 2C5.58 2 2 5.58 2 10c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0018 10c0-4.42-3.58-8-8-8z"></path></svg>
                                Sponsor
                            </a>
                        </div>
                    </div>

                    <div class="ml-4 text-center text-sm text-gray-500 dark:text-gray-400 sm:text-right sm:ml-0">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
