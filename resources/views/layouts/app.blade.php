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

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }

            body {
                font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif;
                background-color: #fff7ed;
                background-image:
                    radial-gradient(at 0% 0%, rgba(251,146,60,0.15) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(253,186,116,0.12) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(251,146,60,0.10) 0px, transparent 50%),
                    radial-gradient(at 0% 100%, rgba(254,215,170,0.15) 0px, transparent 50%);
            }

            body.dark {
                background-color: #1c1410;
                background-image:
                    radial-gradient(at 0% 0%, rgba(194,65,12,0.15) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(154,52,18,0.10) 0px, transparent 50%);
            }
        </style>

        
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">

            <!-- Sidebar -->
            @include('layouts.navigation')

            <!-- Main Content -->
                <div class="flex-1 flex flex-col" style="margin-left: var(--sb-width-collapsed); transition: margin-left 0.28s cubic-bezier(0.4,0,0.2,1);">

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white/50 backdrop-blur-sm border-b border-orange-100/50">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 max-w-7xl w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>

            </div>
        </div>

        <!-- Sidebar Overlay for Mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 md:hidden hidden z-30" onclick="toggleSidebar()"></div>
    </body>
</html>
=======
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
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
