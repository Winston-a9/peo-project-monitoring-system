<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
                </div>
            </div>
        </div>
    </body>
</html>
