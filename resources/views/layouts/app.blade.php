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

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebar-overlay');
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }
        </script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex">

            <!-- Sidebar -->
            @include('layouts.navigation')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col">

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