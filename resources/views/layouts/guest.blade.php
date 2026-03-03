<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

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
                </div>
            </div>
        </div>
    </body>
</html>
