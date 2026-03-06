<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sign In — {{ config('app.name', 'PEO Monitor') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|syne:700,800" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root {
                --orange-50:  #fff7ed;
                --orange-100: #ffedd5;
                --orange-400: #fb923c;
                --orange-500: #f97316;
                --orange-600: #ea580c;
                --orange-700: #c2410c;
                --ink:        #1a0f00;
                --ink-muted:  #6b4f35;
                --surface:    #fffaf5;
                --border:     rgba(249,115,22,0.18);
                --radius:     14px;
            }

            *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

            body {
                font-family: 'Instrument Sans', sans-serif;
                background-color: var(--surface);
                color: var(--ink);
                min-height: 100vh;
                display: flex;
                overflow: hidden;
            }

            /* ── NOISE ── */
            body::before {
                content: '';
                position: fixed; inset: 0;
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.035'/%3E%3C/svg%3E");
                background-size: 200px;
                pointer-events: none; z-index: 0;
            }

            /* ── LEFT PANEL ── */
            .panel-left {
                position: relative;
                width: 45%;
                background: var(--orange-500);
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                padding: 3rem;
                overflow: hidden;
                flex-shrink: 0;
            }

            .panel-left::before {
                content: '';
                position: absolute;
                top: -120px; right: -120px;
                width: 400px; height: 400px;
                border-radius: 50%;
                background: rgba(255,255,255,0.1);
            }

            .panel-left::after {
                content: '';
                position: absolute;
                bottom: -80px; left: -80px;
                width: 300px; height: 300px;
                border-radius: 50%;
                background: rgba(255,255,255,0.07);
            }

            .panel-logo {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-family: 'Syne', sans-serif;
                font-weight: 800;
                font-size: 1.3rem;
                color: white;
                letter-spacing: -0.02em;
                position: relative; z-index: 1;
            }

            .panel-logo-icon {
                width: 38px; height: 38px;
                background: rgba(255,255,255,0.2);
                border-radius: 10px;
                display: flex; align-items: center; justify-content: center;
                font-size: 1.1rem;
                backdrop-filter: blur(6px);
                border: 1px solid rgba(255,255,255,0.25);
            }

            .panel-content {
                position: relative; z-index: 1;
            }

            .panel-content h2 {
                font-family: 'Syne', sans-serif;
                font-size: clamp(1.8rem, 3vw, 2.6rem);
                font-weight: 800;
                color: white;
                line-height: 1.1;
                letter-spacing: -0.03em;
                margin-bottom: 1.25rem;
            }

            .panel-content p {
                color: rgba(255,255,255,0.8);
                font-size: 0.95rem;
                line-height: 1.75;
                max-width: 300px;
            }

            .panel-features {
                position: relative; z-index: 1;
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .panel-feature {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                color: rgba(255,255,255,0.9);
                font-size: 0.875rem;
                font-weight: 500;
            }

            .panel-feature-dot {
                width: 28px; height: 28px;
                background: rgba(255,255,255,0.15);
                border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                font-size: 0.8rem;
                flex-shrink: 0;
                border: 1px solid rgba(255,255,255,0.2);
            }

            /* ── RIGHT PANEL ── */
            .panel-right {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 3rem 2rem;
                position: relative; z-index: 1;
                overflow-y: auto;
            }

            .form-box {
                width: 100%;
                max-width: 420px;
                animation: fadeUp 0.6s ease both;
            }

            .form-heading {
                margin-bottom: 2rem;
            }

            .form-heading h1 {
                font-family: 'Syne', sans-serif;
                font-size: 1.8rem;
                font-weight: 800;
                color: var(--ink);
                letter-spacing: -0.03em;
                margin-bottom: 0.4rem;
            }

            .form-heading p {
                color: var(--ink-muted);
                font-size: 0.9rem;
            }

            /* Session Status */
            .alert-success {
                padding: 0.875rem 1rem;
                background: #f0fdf4;
                border: 1px solid #bbf7d0;
                color: #166534;
                border-radius: 10px;
                font-size: 0.875rem;
                display: flex;
                align-items: center;
                gap: 0.6rem;
                margin-bottom: 1.5rem;
            }

            /* Form */
            .form-group {
                margin-bottom: 1.25rem;
            }

            label {
                display: block;
                font-size: 0.8rem;
                font-weight: 600;
                color: var(--ink);
                letter-spacing: 0.03em;
                text-transform: uppercase;
                margin-bottom: 0.5rem;
            }

            .input-wrap {
                position: relative;
            }

            .input-icon {
                position: absolute;
                left: 1rem;
                top: 50%;
                transform: translateY(-50%);
                color: var(--ink-muted);
                font-size: 0.85rem;
                pointer-events: none;
            }

            input[type="email"],
            input[type="password"] {
                width: 100%;
                padding: 0.8rem 1rem 0.8rem 2.6rem;
                border: 1.5px solid rgba(26,15,0,0.12);
                border-radius: 10px;
                font-size: 0.9rem;
                font-family: 'Instrument Sans', sans-serif;
                color: var(--ink);
                background: white;
                transition: border-color 0.2s, box-shadow 0.2s;
                outline: none;
            }

            input[type="email"]:focus,
            input[type="password"]:focus {
                border-color: var(--orange-500);
                box-shadow: 0 0 0 3px rgba(249,115,22,0.12);
            }

            input.error {
                border-color: #ef4444;
            }

            .field-error {
                color: #ef4444;
                font-size: 0.8rem;
                margin-top: 0.4rem;
                display: flex;
                align-items: center;
                gap: 0.3rem;
            }

            /* Remember + Forgot */
            .form-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1.5rem;
            }

            .remember-wrap {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            input[type="checkbox"] {
                width: 15px; height: 15px;
                accent-color: var(--orange-500);
                cursor: pointer;
            }

            .remember-wrap label {
                font-size: 0.85rem;
                font-weight: 400;
                text-transform: none;
                letter-spacing: 0;
                color: var(--ink-muted);
                cursor: pointer;
                margin-bottom: 0;
            }

            .forgot-link {
                font-size: 0.85rem;
                font-weight: 600;
                color: var(--orange-600);
                text-decoration: none;
                transition: color 0.2s;
            }

            .forgot-link:hover { color: var(--orange-700); }

            /* Submit */
            .btn-submit {
                width: 100%;
                padding: 0.875rem;
                background: var(--orange-500);
                color: white;
                font-family: 'Instrument Sans', sans-serif;
                font-size: 0.975rem;
                font-weight: 600;
                border: none;
                border-radius: 10px;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.6rem;
                box-shadow: 0 4px 18px rgba(249,115,22,0.4), inset 0 1px 0 rgba(255,255,255,0.2);
                transition: all 0.25s;
                letter-spacing: 0.01em;
            }

            .btn-submit:hover {
                background: var(--orange-600);
                transform: translateY(-1px);
                box-shadow: 0 6px 24px rgba(249,115,22,0.5);
            }

            .btn-submit:active { transform: translateY(0); }

            /* Register link */
            .register-row {
                text-align: center;
                margin-top: 1.5rem;
                padding-top: 1.5rem;
                border-top: 1px solid rgba(26,15,0,0.08);
                font-size: 0.875rem;
                color: var(--ink-muted);
            }

            .register-row a {
                color: var(--orange-600);
                font-weight: 600;
                text-decoration: none;
                transition: color 0.2s;
            }

            .register-row a:hover { color: var(--orange-700); }

            /* Animation */
            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(20px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            /* Responsive */
            @media (max-width: 768px) {
                .panel-left { display: none; }
                .panel-right { padding: 2rem 1.5rem; }
            }
        </style>
    </head>
    <body>

        <!-- Left decorative panel -->
        <div class="panel-left">
            <div class="panel-logo">
                <div class="panel-logo-icon">📊</div>
                PEO Monitor
            </div>

            <div class="panel-content">
                <h2>Project<br>Document<br>Control</h2>
                <p>Track every submittal, transmittal, and project record from a single, secure dashboard built for engineering teams.</p>
            </div>

            <div class="panel-features">
                <div class="panel-feature">
                    <div class="panel-feature-dot"><i class="fas fa-shield-alt"></i></div>
                    Enterprise-grade security
                </div>
                <div class="panel-feature">
                    <div class="panel-feature-dot"><i class="fas fa-bolt"></i></div>
                    Real-time status tracking
                </div>
                <div class="panel-feature">
                    <div class="panel-feature-dot"><i class="fas fa-users"></i></div>
                    Multi-team collaboration
                </div>
            </div>
        </div>

        <!-- Right form panel -->
        <div class="panel-right">
            <div class="form-box">

                <div class="form-heading">
                    <h1>Welcome back</h1>
                    <p>Sign in to your PEO Monitor account</p>
                </div>

                @if (session('status'))
                    <div class="alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrap">
                            <i class="fas fa-envelope input-icon"></i>
                            <input id="email" type="email" name="email"
                                value="{{ old('email') }}"
                                class="{{ $errors->has('email') ? 'error' : '' }}"
                                required autofocus autocomplete="username"
                                placeholder="you@example.com" />
                        </div>
                        @error('email')
                            <p class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <i class="fas fa-lock input-icon"></i>
                            <input id="password" type="password" name="password"
                                class="{{ $errors->has('password') ? 'error' : '' }}"
                                required autocomplete="current-password"
                                placeholder="••••••••" />
                        </div>
                        @error('password')
                            <p class="field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="remember-wrap">
                            <input id="remember_me" type="checkbox" name="remember">
                            <label for="remember_me">Remember me</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </button>

                    @if (Route::has('register'))
                        <div class="register-row">
                            Don't have an account?
                            <a href="{{ route('register') }}">Request access</a>
                        </div>
                    @endif
                </form>

            </div>
        </div>

    </body>
</html>