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
    <body class="font-sans antialiased bg-gradient-to-br from-gray-50 via-white to-orange-50 dark:from-gray-950 dark:via-gray-900 dark:to-gray-800">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 border-b-2 border-orange-100 dark:border-orange-900 shadow-sm">
                    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-4">
                            <div class="h-1 w-1 rounded-full bg-gradient-to-r from-orange-400 to-orange-600"></div>
                            <div>
                                {{ $header }}
                            </div>
                        </div>
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main class="py-8 lg:py-12">
                {{ $slot }}
            </main>

            <!-- Footer Accent -->
            <footer class="mt-12 border-t-2 border-orange-100 dark:border-orange-900/50 bg-white dark:bg-gray-900/50 backdrop-blur-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center">
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Built with <span class="text-orange-600">♥</span> using Laravel & Breeze
                    </p>
                </div>
            </footer>
        </div>
    </body>
</html>
