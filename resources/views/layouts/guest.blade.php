<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

<<<<<<< HEAD
        <title>{{ config('app.name', 'PEO Document Monitoring') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Font Awesome for Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                color: #2d3748;
            }
            body.dark {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                color: #e2e8f0;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Header -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center gap-2 text-3xl font-bold">
                    <span class="bg-gradient-to-br from-purple-600 to-violet-600 p-2 rounded-lg text-white">
                        <i class="fas fa-shield-alt"></i>
                    </span>
                    <span class="bg-gradient-to-r from-purple-600 to-violet-600 -webkit-background-clip-text -webkit-text-fill-color-transparent bg-clip-text">
                        PEO Monitor
                    </span>
                </a>
                <p class="text-gray-600 dark:text-gray-400 mt-2">Professional Document Management System</p>
            </div>

            <!-- Auth Card -->
            <div class="w-full sm:max-w-md">
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-filter backdrop-blur-lg border border-white/20 dark:border-gray-700/20 rounded-2xl shadow-2xl overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-600/10 to-violet-600/10 dark:from-purple-600/5 dark:to-violet-600/5 px-6 sm:px-8 py-8 border-b border-white/20 dark:border-gray-700/20">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white text-center">
                            {{ isset($header) ? $header : 'Welcome' }}
                        </h2>
                    </div>
                    <div class="px-6 sm:px-8 py-8">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Additional Links -->
                <div class="mt-6 text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        @if (Route::has('login') && !request()->routeIs('login'))
                            <a href="{{ route('login') }}" class="font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">
                                Sign in
                            </a>
                        @endif

                        @if (Route::has('register') && !request()->routeIs('register'))
                            <span class="text-gray-500 dark:text-gray-500 mx-2">•</span>
                            <a href="{{ route('register') }}" class="font-semibold text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">
                                Create account
                            </a>
                        @endif
                    </p>
=======
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gradient-to-br from-orange-50 via-white to-blue-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="mb-6 sm:mb-8">
                <a href="/" class="group transition-transform duration-300 hover:scale-110 inline-block">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all duration-300">
                        <span class="text-white font-bold text-3xl">SIS</span>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md">
                <div class="px-6 py-8 bg-white dark:bg-gray-800 shadow-xl rounded-2xl border-2 border-orange-100 dark:border-orange-700/30 overflow-hidden hover:shadow-2xl transition-all duration-300">
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-center bg-gradient-to-r from-orange-600 to-orange-400 bg-clip-text text-transparent dark:from-orange-400 dark:to-orange-300">
                            Student Information System
                        </h1>
                    </div>
                    {{ $slot }}
                </div>

                <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                    <p>© {{ date('Y') }} SIS. All rights reserved.</p>
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
                </div>
            </div>
        </div>
    </body>
</html>
