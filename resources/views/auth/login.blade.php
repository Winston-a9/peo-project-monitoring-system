<<<<<<< HEAD
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
=======
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Provincial Engineering Office</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --orange:       #E05A00;
            --orange-dark:  #B84A00;
            --orange-light: #FF8C38;
            --amber:        #C97A00;
            --cream:        #FFF8F0;
            --warm-white:   #FFFCF9;
            --stone:        #2C1E12;
            --stone-mid:    #6B4F3A;
            --stone-light:  #A07858;
            --gray-soft:    #F5EDE4;
            --error:        #D0190F;
        }

        html, body {
            height: 100%;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
            background: var(--cream);
            color: var(--stone);
        }

        /* ══════════════════════════
           LEFT PANEL
        ══════════════════════════ */
        .left-panel {
            background: var(--stone);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        .left-panel .glow {
            position: absolute;
            width: 480px; height: 480px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(224,90,0,0.17) 0%, transparent 70%);
            bottom: -110px; right: -100px;
            pointer-events: none;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 520px; height: 520px;
            border-radius: 50%;
            border: 1px solid rgba(224,90,0,0.14);
            bottom: -130px; right: -130px;
            pointer-events: none;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 360px; height: 360px;
            border-radius: 50%;
            border: 1px solid rgba(224,90,0,0.08);
            bottom: -50px; right: -50px;
            pointer-events: none;
        }

        /* Panel top — logo */
        .panel-top {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
            animation: fade-up 0.50s ease forwards;
            opacity: 0;
        }

        .panel-logo {
            width: 48px; height: 48px;
            object-fit: contain;
            border-radius: 10px;
            filter: drop-shadow(0 4px 12px rgba(224,90,0,0.35));
        }

        .panel-logo-text h2 {
            font-size: 0.95rem;
            font-weight: 800;
            color: rgba(255,255,255,0.90);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .panel-logo-text p {
            font-size: 0.68rem;
            color: rgba(255,255,255,0.38);
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* Panel middle — headline */
        .panel-middle {
            position: relative;
            z-index: 1;
            animation: fade-up 0.50s 0.10s ease forwards;
            opacity: 0;
        }

        .panel-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 12px;
            border-radius: 100px;
            background: rgba(224,90,0,0.14);
            border: 1px solid rgba(224,90,0,0.24);
            color: var(--orange-light);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.10em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .badge-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--orange-light);
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.35; transform: scale(0.72); }
        }

        .panel-headline {
            font-size: clamp(1.9rem, 2.6vw, 2.75rem);
            font-weight: 800;
            color: white;
            line-height: 1.12;
            letter-spacing: -0.035em;
            margin-bottom: 18px;
        }

        .panel-headline span { color: var(--orange-light); }

        .panel-desc {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.46);
            line-height: 1.72;
            max-width: 340px;
            font-weight: 400;
        }

        .panel-stats {
            display: flex;
            gap: 28px;
            margin-top: 36px;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }

        .p-stat-num {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--orange-light);
            letter-spacing: -0.04em;
            line-height: 1;
        }

        .p-stat-label {
            font-size: 0.70rem;
            color: rgba(255,255,255,0.35);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            margin-top: 4px;
        }

        /* Panel bottom — quote */
        .panel-bottom {
            position: relative;
            z-index: 1;
            animation: fade-up 0.50s 0.18s ease forwards;
            opacity: 0;
        }

        .panel-quote {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.28);
            font-style: italic;
            line-height: 1.65;
            border-left: 2px solid rgba(224,90,0,0.28);
            padding-left: 14px;
        }

        /* ══════════════════════════
           RIGHT PANEL
        ══════════════════════════ */
        .right-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        .right-panel::before {
            content: '';
            position: absolute;
            width: 360px; height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(224,90,0,0.06) 0%, transparent 70%);
            top: -80px; right: -80px;
            pointer-events: none;
        }

        .right-panel::after {
            content: '';
            position: absolute;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(201,122,0,0.05) 0%, transparent 70%);
            bottom: -60px; left: -60px;
            pointer-events: none;
        }

        /* ── Form Wrap ── */
        .form-wrap {
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 1;
            animation: fade-up 0.50s 0.08s ease forwards;
            opacity: 0;
        }

        @keyframes fade-up {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .form-header { margin-bottom: 34px; }

        .form-eyebrow {
            font-size: 0.70rem;
            font-weight: 700;
            color: var(--orange);
            letter-spacing: 0.13em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .form-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: var(--stone);
            letter-spacing: -0.035em;
            line-height: 1.15;
            margin-bottom: 8px;
        }

        .form-sub {
            font-size: 0.875rem;
            color: var(--stone-light);
            font-weight: 400;
            line-height: 1.5;
        }

        /* Session status */
        .session-status {
            background: rgba(224,90,0,0.08);
            border: 1px solid rgba(224,90,0,0.20);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.85rem;
            color: var(--orange-dark);
            font-weight: 500;
            margin-bottom: 22px;
        }

        /* Fields */
        .field-group { margin-bottom: 18px; }

        .field-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--stone-mid);
            letter-spacing: 0.06em;
            text-transform: uppercase;
            margin-bottom: 7px;
        }

        .field-input-wrap { position: relative; }

        .field-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #C4A898;
            font-size: 0.82rem;
            pointer-events: none;
            transition: color 0.18s;
        }

        .field-input-wrap:focus-within .field-icon { color: var(--orange); }

        .field-input {
            width: 100%;
            padding: 12px 16px 12px 40px;
            border-radius: 10px;
            border: 1.5px solid #E8D9CC;
            background: var(--warm-white);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.925rem;
            font-weight: 500;
            color: var(--stone);
            outline: none;
            transition: border-color 0.18s, box-shadow 0.18s, background 0.18s;
            -webkit-appearance: none;
        }

        .field-input::placeholder { color: #C4A898; font-weight: 400; }

        .field-input:focus {
            border-color: var(--orange);
            background: white;
            box-shadow: 0 0 0 3px rgba(224,90,0,0.11);
        }

        .field-error {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.80rem;
            color: var(--error);
            font-weight: 500;
            margin-top: 6px;
        }

        /* Meta row */
        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 22px 0 26px;
        }

        .remember-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            user-select: none;
        }

        .remember-check {
            width: 17px; height: 17px;
            border-radius: 5px;
            border: 1.5px solid #D4B9A8;
            background: var(--warm-white);
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            position: relative;
            flex-shrink: 0;
            transition: border-color 0.15s, background 0.15s;
        }

        .remember-check:checked {
            background: var(--orange);
            border-color: var(--orange);
        }

        .remember-check:checked::after {
            content: '';
            position: absolute;
            left: 4px; top: 1px;
            width: 5px; height: 9px;
            border: 2px solid white;
            border-top: none;
            border-left: none;
            transform: rotate(45deg);
        }

        .remember-text {
            font-size: 0.85rem;
            color: var(--stone-mid);
            font-weight: 500;
        }

        .forgot-link {
            font-size: 0.825rem;
            color: var(--orange);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.15s;
            white-space: nowrap;
        }
        .forgot-link:hover { color: var(--orange-dark); text-decoration: underline; }

        /* Submit */
        .btn-login {
            width: 100%;
            padding: 14px;
            border-radius: 11px;
            border: none;
            background: var(--orange);
            color: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.975rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            cursor: pointer;
            transition: background 0.18s, transform 0.12s, box-shadow 0.18s;
            box-shadow: 0 4px 20px rgba(224,90,0,0.30);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            position: relative;
            overflow: hidden;
        }

        .btn-login::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.10) 0%, transparent 55%);
            pointer-events: none;
        }

        .btn-login:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 26px rgba(224,90,0,0.38);
        }

        .btn-login:active { transform: translateY(0); }

        /* Footer */
        .form-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(224,90,0,0.12), transparent);
            margin: 26px 0;
        }

        .form-footer {
            text-align: center;
            font-size: 0.85rem;
            color: var(--stone-light);
            font-weight: 500;
        }

        .form-footer a {
            color: var(--orange);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.15s;
        }
        .form-footer a:hover { color: var(--orange-dark); text-decoration: underline; }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            body { grid-template-columns: 1fr; }
            .left-panel { display: none; }
            .right-panel { padding: 40px 24px; }
        }
    </style>
</head>
<body>

    <!-- ═══ LEFT PANEL ═══ -->
    <div class="left-panel">
        <div class="glow"></div>

        <div class="panel-top">
            <img src="{{ asset('assets/app_logo.PNG') }}" alt="PEO Logo" class="panel-logo">
            <div class="panel-logo-text">
                <h2>Provincial Engineering Office</h2>
                <p>Malaybalay City, Bukidnon</p>
            </div>
        </div>

        <div class="panel-middle">
            <div class="panel-badge">
                <div class="badge-dot"></div>
                Government Portal
            </div>
            <h1 class="panel-headline">
                Building Better<br>
                <span>Infrastructure</span><br>
                For Our Community
            </h1>
            <p class="panel-desc">
                Access the Provincial Engineering Office management system. Manage projects, permits, and engineering documentation all in one place.
            </p>
            <div class="panel-stats">
                <div>
                    <div class="p-stat-num">500+</div>
                    <div class="p-stat-label">Projects</div>
                </div>
                <div>
                    <div class="p-stat-num">15+</div>
                    <div class="p-stat-label">Years</div>
                </div>
                <div>
                    <div class="p-stat-num">24/7</div>
                    <div class="p-stat-label">Support</div>
                </div>
            </div>
        </div>

        <div class="panel-bottom">
            <p class="panel-quote">
                "Committed to quality engineering services and sustainable infrastructure development for the people of Bukidnon."
            </p>
        </div>
    </div>

    <!-- ═══ RIGHT PANEL ═══ -->
    <div class="right-panel">
        <div class="form-wrap">

            <div class="form-header">
                <div class="form-eyebrow">Secure Access</div>
                <h2 class="form-title">Welcome back</h2>
                <p class="form-sub">Sign in to your account to continue.</p>
            </div>

            {{-- Session Status --}}
            @if(session('status'))
                <div class="session-status">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="field-group">
                    <label class="field-label" for="email">Email Address</label>
                    <div class="field-input-wrap">
                        <i class="fas fa-envelope field-icon"></i>
                        <input
                            id="email"
                            class="field-input"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="you@example.com"
                        >
                    </div>
                    @error('email')
                        <div class="field-error">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="field-group">
                    <label class="field-label" for="password">Password</label>
                    <div class="field-input-wrap">
                        <i class="fas fa-lock field-icon"></i>
                        <input
                            id="password"
                            class="field-input"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        >
                    </div>
                    @error('password')
                        <div class="field-error">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="meta-row">
                    <label class="remember-label" for="remember_me">
                        <input id="remember_me" class="remember-check" type="checkbox" name="remember">
                        <span class="remember-text">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            Forgot password?
                        </a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                    Sign In
                </button>
            </form>
        </div>
    </div>

</body>
</html>
>>>>>>> 33baeb89651948608801199ef8dceec70f723e41
