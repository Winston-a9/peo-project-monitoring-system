<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Register — {{ config('app.name', 'PEO Document Monitoring') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|syne:700,800" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root {
                --orange-50:  #fff7ed;
                --orange-100: #ffedd5;
                --orange-500: #f97316;
                --orange-600: #ea580c;
                --orange-700: #c2410c;
                --ink:        #1a0f00;
                --ink-muted:  #6b4f35;
                --surface:    #fffaf5;
                --card-bg:    #ffffff;
                --border:     rgba(249,115,22,0.15);
                --radius:     14px;
            }

            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
            html { scroll-behavior: smooth; }

            body {
                font-family: 'Instrument Sans', sans-serif;
                background-color: var(--surface);
                color: var(--ink);
                line-height: 1.6;
                overflow-x: hidden;
            }

            body::before {
                content: '';
                position: fixed;
                inset: 0;
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
                background-repeat: repeat;
                background-size: 200px;
                pointer-events: none;
                z-index: 0;
            }

            .bg-blob {
                position: fixed;
                border-radius: 50%;
                filter: blur(80px);
                opacity: 0.25;
                pointer-events: none;
                z-index: 0;
            }
            .bg-blob-1 {
                width: 600px; height: 600px;
                background: var(--orange-500);
                top: -200px; right: -150px;
            }
            .bg-blob-2 {
                width: 400px; height: 400px;
                background: var(--orange-400);
                bottom: 100px; left: -100px;
            }

            nav {
                position: fixed;
                top: 0; width: 100%;
                z-index: 100;
                padding: 0 2.5rem;
                height: 68px;
                display: flex;
                align-items: center;
                background: rgba(255, 250, 245, 0.85);
                backdrop-filter: blur(14px);
                border-bottom: 1px solid var(--border);
            }

            .nav-inner {
                max-width: 1200px;
                margin: 0 auto;
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .logo {
                display: flex;
                align-items: center;
                gap: 0.6rem;
                font-family: 'Syne', sans-serif;
                font-weight: 800;
                font-size: 1.25rem;
                color: var(--ink);
                text-decoration: none;
                letter-spacing: -0.02em;
            }

            .logo-icon {
                width: 34px; height: 34px;
                background: var(--orange-500);
                border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                font-size: 1rem;
                box-shadow: 0 2px 10px rgba(249,115,22,0.35);
            }

            .nav-links {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .nav-links a {
                text-decoration: none;
                color: var(--ink-muted);
                font-weight: 500;
                font-size: 0.9rem;
                transition: color 0.2s;
            }

            .nav-links a:hover { color: var(--orange-600); }

            .btn-nav {
                padding: 8px 22px;
                background: var(--orange-500);
                color: #fff !important;
                border-radius: 8px;
                font-weight: 600 !important;
                font-size: 0.875rem !important;
                box-shadow: 0 2px 12px rgba(249,115,22,0.4);
                transition: background 0.2s, transform 0.2s !important;
            }

            .btn-nav:hover {
                background: var(--orange-600) !important;
                transform: translateY(-1px);
            }

            .auth-container {
                position: relative;
                z-index: 1;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2.5rem;
                padding-top: 120px;
            }

            .auth-box {
                background: var(--card-bg);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 3rem;
                width: 100%;
                max-width: 420px;
                box-shadow: 0 16px 48px rgba(249,115,22,0.08);
            }

            .auth-box h2 {
                font-family: 'Syne', sans-serif;
                font-size: 1.75rem;
                font-weight: 800;
                color: var(--ink);
                margin-bottom: 0.5rem;
                letter-spacing: -0.02em;
            }

            .auth-box p {
                color: var(--ink-muted);
                font-size: 0.95rem;
                margin-bottom: 2rem;
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-group label {
                display: block;
                font-weight: 600;
                font-size: 0.9rem;
                color: var(--ink);
                margin-bottom: 0.5rem;
            }

            .form-group input {
                width: 100%;
                padding: 0.85rem 1rem;
                border: 1.5px solid rgba(26,15,0,0.12);
                border-radius: 10px;
                font-family: 'Instrument Sans', sans-serif;
                font-size: 0.95rem;
                color: var(--ink);
                background: #fafaf9;
                transition: border-color 0.2s, box-shadow 0.2s;
            }

            .form-group input:focus {
                outline: none;
                border-color: var(--orange-500);
                background: white;
                box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
            }

            .form-group input::placeholder {
                color: #a89968;
            }

            .error-message {
                color: #dc2626;
                font-size: 0.8rem;
                margin-top: 0.35rem;
            }

            .form-actions {
                display: flex;
                gap: 1rem;
                margin-top: 2rem;
            }

            .btn {
                padding: 11px 24px;
                border-radius: 10px;
                font-size: 0.95rem;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                transition: all 0.25s;
                border: none;
                cursor: pointer;
                font-family: 'Instrument Sans', sans-serif;
                flex: 1;
            }

            .btn-primary {
                background: var(--orange-500);
                color: #fff;
                box-shadow: 0 4px 18px rgba(249,115,22,0.4);
            }

            .btn-primary:hover {
                background: var(--orange-600);
                transform: translateY(-1px);
                box-shadow: 0 6px 24px rgba(249,115,22,0.5);
            }

            .btn-secondary {
                background: transparent;
                color: var(--ink-muted);
                border: 1.5px solid rgba(26,15,0,0.15);
                flex: unset;
            }

            .btn-secondary:hover {
                border-color: var(--orange-500);
                color: var(--orange-600);
                background: var(--orange-50);
            }

            .auth-link {
                text-align: center;
                margin-top: 1.5rem;
                font-size: 0.9rem;
                color: var(--ink-muted);
            }

            .auth-link a {
                color: var(--orange-600);
                text-decoration: none;
                font-weight: 600;
                transition: color 0.2s;
            }

            .auth-link a:hover { color: var(--orange-700); }

            /* Dark Mode Toggle Sidebar */
            .theme-sidebar {
                position:fixed; right:0; top:50%; transform:translateY(-50%);
                background:var(--card-bg); border:1px solid rgba(249,115,22,0.15);
                border-right:none; border-radius:16px 0 0 16px;
                padding:1.2rem 0.8rem; z-index:999;
                display:flex; flex-direction:column; gap:0.8rem;
                box-shadow:-4px 0 16px rgba(0,0,0,0.1);
                transition:all 0.3s;
            }

            @media (prefers-color-scheme: dark) {
                .theme-sidebar {
                    background:#1a1a1a;
                    border-color:rgba(249,115,22,0.3);
                    box-shadow:-4px 0 16px rgba(0,0,0,0.3);
                }
            }

            .theme-btn {
                width:48px; height:48px;
                border-radius:12px; border:1.5px solid rgba(249,115,22,0.15);
                background:white; color:#1a0f00;
                display:flex; align-items:center; justify-content:center;
                cursor:pointer; transition:all 0.2s; font-size:1.1rem;
            }

            @media (prefers-color-scheme: dark) {
                .theme-btn {
                    background:#0f0f0f; color:#f5f5f0;
                    border-color:rgba(249,115,22,0.3);
                }
            }

            .theme-btn:hover {
                border-color:var(--orange-500);
                background:rgba(249,115,22,0.1);
                color:var(--orange-600);
            }

            .theme-btn.active {
                background:var(--orange-500); color:white; border-color:var(--orange-500);
                box-shadow:0 4px 12px rgba(249,115,22,0.3);
            }


            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(24px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            .auth-box { animation: fadeUp 0.5s ease both; }

            @media (max-width: 768px) {
                nav { padding: 0 1.25rem; }
                .auth-box { padding: 2rem; }
                .auth-container { padding: 1.25rem; }
            }
        </style>
    </head>
    <body>
        <div class="bg-blob bg-blob-1"></div>
        <div class="bg-blob bg-blob-2"></div>

        <nav>
            <div class="nav-inner">
                <a href="/" class="logo">
                    <div class="logo-icon">📊</div>
                    PEO Monitor
                </a>
                <div class="nav-links">
                    <a href="{{ route('login') }}">Sign In</a>
                </div>
            </div>
        </nav>

        <div class="auth-container">
            <div class="auth-box">
                <h2>Join Us</h2>
                <p>Create your account to get started with PEO Document Monitoring.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Enter your full name">
                        @if ($errors->has('name'))
                            <div class="error-message">{{ $errors->first('name') }}</div>
                        @endif
                    </div>

                    <!-- Email Address -->
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="name@company.com">
                        @if ($errors->has('email'))
                            <div class="error-message">{{ $errors->first('email') }}</div>
                        @endif
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Create a strong password">
                        @if ($errors->has('password'))
                            <div class="error-message">{{ $errors->first('password') }}</div>
                        @endif
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password *</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password">
                        @if ($errors->has('password_confirmation'))
                            <div class="error-message">{{ $errors->first('password_confirmation') }}</div>
                        @endif
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            Create Account
                        </button>
                    </div>
                </form>

                <div class="auth-link">
                    Already have an account? <a href="{{ route('login') }}">Sign in here</a>
                </div>
            </div>
        </div>

        <script>
        // Dark Mode Toggle
        const htmlElement = document.documentElement;
        const lightBtn = document.getElementById('themeLight');
        const darkBtn = document.getElementById('themeDark');

        function initializeTheme() {
            const saved = localStorage.getItem('theme');
            if (saved) {
                htmlElement.style.colorScheme = saved;
                updateButtons(saved);
            } else {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = prefersDark ? 'dark' : 'light';
                updateButtons(theme);
            }
        }

        function updateButtons(theme) {
            if (theme === 'dark') {
                darkBtn?.classList.add('active');
                lightBtn?.classList.remove('active');
            } else {
                lightBtn?.classList.add('active');
                darkBtn?.classList.remove('active');
            }
        }

        lightBtn?.addEventListener('click', () => {
            htmlElement.style.colorScheme = 'light';
            localStorage.setItem('theme', 'light');
            updateButtons('light');
        });

        darkBtn?.addEventListener('click', () => {
            htmlElement.style.colorScheme = 'dark';
            localStorage.setItem('theme', 'dark');
            updateButtons('dark');
        });

        initializeTheme();
        </script>

        <div class="theme-sidebar">
            <button id="themeLight" class="theme-btn" title="Light Mode">☀️</button>
            <button id="themeDark" class="theme-btn" title="Dark Mode">🌙</button>
        </div>
    </body>
</html>
