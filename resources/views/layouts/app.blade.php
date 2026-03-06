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

        <!-- Theme Initialization Script (runs before page renders) -->
        <script>
            (function() {
                const html = document.documentElement;
                const savedTheme = localStorage.getItem('theme-mode');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                let theme = savedTheme || (prefersDark ? 'dark' : 'light');
                html.classList.add(theme);
                if (theme === 'dark') {
                    document.body?.classList.add('dark');
                }
                document.documentElement.setAttribute('data-theme', theme);
            })();
        </script>

        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }

            :root {
                --orange-500: #f97316;
                --orange-600: #ea580c;
                --ink:        #1a0f00;
                --ink-muted:  #6b4f35;
                --border:     rgba(249,115,22,0.14);
                --bg-primary: #ffffff;
                --bg-secondary: #fffaf5;
                --text-primary: #1a0f00;
                --text-secondary: #6b4f35;
            }

            html.dark {
                --bg-primary: #0f0f0f;
                --bg-secondary: #1a1a1a;
                --text-primary: #f5f5f0;
                --text-secondary: #9ca3af;
                --ink: #f5f5f0;
                --ink-muted: #9ca3af;
                --border: rgba(249,115,22,0.25);
            }

            html.light {
                --bg-primary: #ffffff;
                --bg-secondary: #fffaf5;
                --text-primary: #1a0f00;
                --text-secondary: #6b4f35;
                --ink: #1a0f00;
                --ink-muted: #6b4f35;
                --border: rgba(249,115,22,0.14);
            }

            @media (prefers-color-scheme: dark) {
                :root:not(.light) {
                    --bg-primary: #0f0f0f;
                    --bg-secondary: #1a1a1a;
                    --text-primary: #f5f5f0;
                    --text-secondary: #9ca3af;
                    --ink: #f5f5f0;
                    --ink-muted: #9ca3af;
                    --border: rgba(249,115,22,0.25);
                }
            }

            * { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }

            body {
                font-family: 'Figtree', ui-sans-serif, system-ui, sans-serif;
                background-color: #fffaf5;
                background-image:
                    radial-gradient(at 0% 0%, rgba(251,146,60,0.15) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(253,186,116,0.12) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(251,146,60,0.10) 0px, transparent 50%),
                    radial-gradient(at 0% 100%, rgba(254,215,170,0.15) 0px, transparent 50%);
                color: var(--text-primary);
            }

            html, body { 
                color: var(--text-primary); 
                background-color: var(--bg-primary);
            }

            html.light body {
                background-color: #fffaf5;
                background-image:
                    radial-gradient(at 0% 0%, rgba(251,146,60,0.15) 0px, transparent 50%),
                    radial-gradient(at 100% 0%, rgba(253,186,116,0.12) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(251,146,60,0.10) 0px, transparent 50%),
                    radial-gradient(at 0% 100%, rgba(254,215,170,0.15) 0px, transparent 50%);
            }

            html.dark body {
                background-color: #0f0f0f;
                background-image:
                    radial-gradient(at 0% 0%, rgba(249,115,22,0.08) 0px, transparent 50%),
                    radial-gradient(at 100% 100%, rgba(249,115,22,0.05) 0px, transparent 50%);
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
                    <header style="background: var(--bg-primary); backdrop-filter: blur(4px); border-bottom: 1px solid var(--border);">
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