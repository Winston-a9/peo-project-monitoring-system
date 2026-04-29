<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Provincial Engineering Office — Reset Password">
    <title>Forgot Password — {{ config('app.name', 'PEO Project Monitoring') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        /* ─── DESIGN TOKENS (mirrored from welcome.css) ─────────────────── */
        :root {
            --orange-900: #7c2d12;
            --orange-800: #9a3412;
            --orange-600: #ea580c;
            --orange-500: #f97316;
            --orange-400: #fb923c;
            --orange-300: #fdba74;
            --orange-100: #ffedd5;
            --orange-50:  #fff7ed;
            --amber-400:  #fbbf24;
            --amber-300:  #fcd34d;

            --dark-950: #0a0a0a;
            --dark-900: #111111;
            --dark-800: #1a1a1a;
            --dark-700: #242424;
            --dark-600: #2e2e2e;
            --dark-400: #525252;
            --dark-300: #737373;
            --dark-200: #a3a3a3;
            --dark-100: #d4d4d4;

            --white:        #ffffff;
            --glass-bg:     rgba(255, 255, 255, 0.06);
            --glass-border: rgba(255, 255, 255, 0.1);

            --font-display: 'Syne', sans-serif;
            --font-body:    'DM Sans', sans-serif;

            --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
            --ease-smooth: cubic-bezier(0.23, 1, 0.32, 1);

            --nav-h: 72px;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            background: var(--dark-950);
            color: var(--white);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
        }

        ::selection { background: var(--orange-500); color: #fff; }

        ::-webkit-scrollbar       { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--dark-900); }
        ::-webkit-scrollbar-thumb { background: var(--orange-600); border-radius: 3px; }

        /* ─── NOISE TEXTURE ──────────────────────────────────────────────── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 9999;
            opacity: 0.4;
        }

        /* ─── BACKGROUND LAYERS (hero-bg + hero-grid + orbs) ────────────── */
        .hero-bg {
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 60% 20%, rgba(234, 88, 12, 0.14) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 20% 80%, rgba(251, 146, 60, 0.09) 0%, transparent 50%),
                linear-gradient(170deg, var(--dark-950) 0%, #0f0a06 50%, #0a0a0a 100%);
            z-index: 0;
            pointer-events: none;
        }

        .hero-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(249, 115, 22, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(249, 115, 22, 0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 70% 70% at 50% 40%, black, transparent);
            z-index: 0;
            pointer-events: none;
        }

        .hero-orb-1 {
            position: fixed;
            top: 15%; right: 10%;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(234, 88, 12, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            animation: breathe 8s ease-in-out infinite;
            z-index: 0; pointer-events: none;
        }

        .hero-orb-2 {
            position: fixed;
            bottom: 5%; left: 5%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(251, 146, 60, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            animation: breathe 11s ease-in-out infinite reverse;
            z-index: 0; pointer-events: none;
        }

        /* accent line — from welcome hero */
        .hero-accent-line {
            position: fixed;
            top: 0; right: 25%;
            width: 1px; height: 100%;
            background: linear-gradient(180deg,
                transparent 0%,
                rgba(249, 115, 22, 0.12) 30%,
                rgba(249, 115, 22, 0.06) 70%,
                transparent 100%);
            z-index: 0; pointer-events: none;
        }

        @keyframes breathe {
            0%, 100% { transform: scale(1);    opacity: 1; }
            50%       { transform: scale(1.15); opacity: 0.7; }
        }

        /* ─── NAVBAR (exact welcome page structure) ─────────────────────── */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            height: var(--nav-h);
            display: flex;
            align-items: center;
            background: rgba(10, 10, 10, 0.92);
            backdrop-filter: blur(24px) saturate(180%);
            border-bottom: 1px solid rgba(249, 115, 22, 0.12);
            box-shadow: 0 1px 0 rgba(249, 115, 22, 0.08), 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .navbar-inner {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 2.5rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            text-decoration: none;
            color: inherit;
        }

        .navbar-logo {
            width: 44px; height: 44px;
            position: relative;
            flex-shrink: 0;
        }

        .navbar-logo-inner {
            width: 100%; height: 100%;
            border-radius: 11px;
            background: var(--dark-900);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .navbar-logo-inner img {
            width: 100%; height: 100%;
            object-fit: contain;
            border-radius: 11px;
        }

        .logo-fallback {
            width: 100%; height: 100%;
            border-radius: 11px;
            background: linear-gradient(135deg, var(--orange-500), var(--amber-400));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 8px 16px rgba(249, 115, 22, 0.3);
        }

        .navbar-text-group { line-height: 1; }

        .navbar-org {
            font-family: var(--font-display);
            font-size: 13px;
            font-weight: 700;
            color: var(--orange-400);
            letter-spacing: 0.3px;
        }

        .navbar-sub {
            font-size: 10.5px;
            color: var(--dark-200);
            margin-top: 3px;
            letter-spacing: 0.4px;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }

        .btn-nav-ghost {
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 500;
            color: var(--dark-100);
            text-decoration: none;
            padding: 0.55rem 1.25rem;
            border-radius: 9px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-nav-ghost:hover {
            border-color: var(--orange-500);
            color: var(--orange-400);
            background: rgba(249, 115, 22, 0.06);
        }

        .btn-nav-primary {
            font-family: var(--font-body);
            font-size: 13px;
            font-weight: 600;
            color: var(--white);
            text-decoration: none;
            padding: 0.6rem 1.5rem;
            border-radius: 9px;
            background: var(--orange-600);
            border: 1px solid var(--orange-500);
            transition: all 0.3s var(--ease-spring);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-nav-primary:hover {
            background: var(--orange-500);
            transform: translateY(-1px);
            box-shadow: 0 4px 20px rgba(249, 115, 22, 0.35), 0 0 0 4px rgba(249, 115, 22, 0.1);
        }

        /* ─── PAGE LAYOUT ───────────────────────────────────────────────── */
        .page-wrap {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: calc(var(--nav-h) + 3rem) 1.5rem 4rem;
        }

        /* ─── AUTH CARD ─────────────────────────────────────────────────── */
        .auth-card {
            position: relative;
            width: 100%;
            max-width: 460px;
            background: var(--dark-900);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            overflow: hidden;
            opacity: 0;
            transform: translateY(28px);
            animation: fade-up 0.75s var(--ease-smooth) 0.15s forwards;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.5);
        }

        /* subtle radial glow inside card on hover — same as feature-card ::after */
        .auth-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 20px;
            background: radial-gradient(circle at 50% 0%, rgba(249, 115, 22, 0.07) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .auth-card:hover { border-color: rgba(249, 115, 22, 0.18); }
        .auth-card:hover::after { opacity: 1; }

        /* top accent bar — identical to kpi-top-bar */
        .card-top-bar {
            height: 3px;
            background: linear-gradient(90deg, var(--orange-600), var(--amber-400), var(--orange-500));
        }

        .card-body { padding: 2.5rem; }

        /* ─── CARD HEADER ────────────────────────────────────────────────── */
        .card-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.75rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* icon wrap — same as feature-card .feature-icon-wrap */
        .card-icon {
            width: 56px; height: 56px;
            border-radius: 15px;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.15), rgba(234, 88, 12, 0.08));
            border: 1px solid rgba(249, 115, 22, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--orange-400);
            margin-bottom: 1.25rem;
            transition: all 0.3s var(--ease-spring);
        }

        .auth-card:hover .card-icon {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.25), rgba(234, 88, 12, 0.15));
            border-color: rgba(249, 115, 22, 0.32);
            transform: scale(1.06);
        }

        /* eyebrow tag — section-tag pattern */
        .card-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--orange-400);
            margin-bottom: 0.625rem;
        }

        .card-eyebrow::before {
            content: '';
            width: 16px; height: 1.5px;
            background: var(--orange-500);
            display: block;
        }

        .card-eyebrow::after {
            content: '';
            width: 16px; height: 1.5px;
            background: var(--orange-500);
            display: block;
        }

        .card-header h1 {
            font-family: var(--font-display);
            font-size: 1.7rem;
            font-weight: 800;
            color: var(--white);
            letter-spacing: -0.5px;
            margin-bottom: 0.625rem;
        }

        .card-header p {
            font-size: 0.875rem;
            color: var(--dark-300);
            line-height: 1.75;
            font-weight: 300;
            max-width: 340px;
        }

        /* ─── SESSION STATUS ─────────────────────────────────────────────── */
        .alert-success {
            background: rgba(74, 222, 128, 0.08);
            border: 1px solid rgba(74, 222, 128, 0.2);
            color: #86efac;
            padding: 0.875rem 1.1rem;
            border-radius: 10px;
            font-size: 0.875rem;
            display: flex;
            align-items: flex-start;
            gap: 0.625rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .alert-success i { font-size: 14px; color: #4ade80; flex-shrink: 0; margin-top: 2px; }

        /* ─── FORM ELEMENTS ──────────────────────────────────────────────── */
        .form-group { margin-bottom: 1.375rem; }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--dark-100);
            margin-bottom: 0.55rem;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem 1.1rem;
            border: 1.5px solid rgba(255, 255, 255, 0.07);
            border-radius: 10px;
            font-family: var(--font-body);
            font-size: 0.9rem;
            color: var(--white);
            background: rgba(255, 255, 255, 0.035);
            backdrop-filter: blur(8px);
            transition: all 0.3s var(--ease-smooth);
            outline: none;
        }

        .form-group input::placeholder { color: var(--dark-400); }

        .form-group input:hover { border-color: rgba(255, 255, 255, 0.12); }

        .form-group input:focus {
            border-color: rgba(249, 115, 22, 0.55);
            background: rgba(249, 115, 22, 0.04);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
        }

        .error-message {
            color: #f87171;
            font-size: 0.78rem;
            margin-top: 0.4rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .error-message i { font-size: 10px; flex-shrink: 0; }

        /* ─── SUBMIT BUTTON ──────────────────────────────────────────────── */
        .btn-submit {
            width: 100%;
            padding: 0.9rem 1.6rem;
            border-radius: 10px;
            font-family: var(--font-body);
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--white);
            background: linear-gradient(135deg, var(--orange-600), var(--orange-500));
            border: 1px solid var(--orange-500);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.625rem;
            letter-spacing: 0.2px;
            transition: all 0.35s var(--ease-spring);
            box-shadow: 0 4px 20px rgba(249, 115, 22, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            margin-top: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 40%, rgba(255, 255, 255, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(249, 115, 22, 0.5);
        }

        .btn-submit:hover::before { opacity: 1; }
        .btn-submit:active { transform: translateY(0); }

        /* ─── BACK TO LOGIN LINK ─────────────────────────────────────────── */
        .back-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.75rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 0.875rem;
            color: var(--dark-300);
            text-decoration: none;
            font-weight: 400;
            transition: all 0.25s ease;
        }

        .back-link i {
            font-size: 11px;
            color: var(--orange-400);
            transition: transform 0.25s var(--ease-spring);
        }

        .back-link:hover {
            color: var(--orange-400);
        }

        .back-link:hover i { transform: translateX(-3px); }

        /* ─── TRUST ROW (from hero-trust) ────────────────────────────────── */
        .trust-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.25rem;
            margin-top: 2rem;
            opacity: 0;
            animation: fade-up 0.8s var(--ease-smooth) 0.5s forwards;
        }

        .trust-divider {
            width: 1px; height: 24px;
            background: rgba(255, 255, 255, 0.08);
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 11.5px;
            color: var(--dark-400);
        }

        .trust-item i { color: var(--orange-400); font-size: 10px; }

        /* ─── ANIMATION ──────────────────────────────────────────────────── */
        @keyframes fade-up {
            to { opacity: 1; transform: translateY(0); }
        }

        /* ─── RESPONSIVE ─────────────────────────────────────────────────── */
        @media (max-width: 640px) {
            .navbar-inner  { padding: 0 1.25rem; }
            .navbar-org,
            .navbar-sub    { display: none; }
            .card-body     { padding: 1.75rem; }
            .trust-row     { display: none; }
        }
    </style>
</head>
<body>

    {{-- ── BACKGROUND LAYERS ─────────────────────────────────────────────── --}}
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-orb-1"></div>
    <div class="hero-orb-2"></div>
    <div class="hero-accent-line"></div>

    {{-- ── NAVBAR ────────────────────────────────────────────────────────── --}}
    <nav class="navbar">
        <div class="navbar-inner">
            <a class="navbar-brand" href="/">
                <div class="navbar-logo">
                    <div class="navbar-logo-inner">
                        @php $logoPath = public_path('assets/app_logo.PNG'); @endphp
                        @if(file_exists($logoPath))
                            <img src="{{ asset('assets/app_logo.PNG') }}" alt="PEO Logo">
                        @else
                            <div class="logo-fallback">📊</div>
                        @endif
                    </div>
                </div>
                <div class="navbar-text-group">
                    <div class="navbar-org">Provincial Engineering Office</div>
                    <div class="navbar-sub">Bukidnon · Project Monitoring System</div>
                </div>
            </a>
        </div>
    </nav>

    {{-- ── PAGE ──────────────────────────────────────────────────────────── --}}
    <div class="page-wrap">
        <div style="width: 100%; max-width: 460px;">

            {{-- Auth Card --}}
            <div class="auth-card">
                <div class="card-top-bar"></div>
                <div class="card-body">

                    {{-- Card Header --}}
                    <div class="card-header">
                        <div class="card-icon">
                            <i class="fas fa-envelope-circle-check"></i>
                        </div>
                        <div class="card-eyebrow">Account Recovery</div>
                        <h1>Forgot Password?</h1>
                        <p>
                            {{ __('No problem. Enter your email address and we\'ll send you a password reset link to choose a new one.') }}
                        </p>
                    </div>

                    {{-- Session Status (from x-auth-session-status) --}}
                    @if (session('status'))
                        <div class="alert-success" role="alert">
                            <i class="fas fa-circle-check"></i>
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Form (all original logic intact) --}}
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group">
                            <label for="email">{{ __('Email Address') }}</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="your@email.gov.ph"
                                required
                                autofocus
                                autocomplete="username">
                            @foreach ($errors->get('email') as $message)
                                <div class="error-message">
                                    <i class="fas fa-circle-exclamation"></i>
                                    {{ $message }}
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i>
                            {{ __('Email Password Reset Link') }}
                        </button>
                    </form>

                    {{-- Back to Login --}}
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="back-link">
                            <i class="fas fa-arrow-left"></i>
                            Back to login
                        </a>
                    @endif

                </div>
            </div>

            {{-- Trust row — mirrors hero-trust --}}
            <div class="trust-row">
                <div class="trust-item">
                    <i class="fas fa-shield-halved"></i>
                    <span>Secure Reset</span>
                </div>
                <div class="trust-divider"></div>
                <div class="trust-item">
                    <i class="fas fa-clock"></i>
                    <span>Link expires in 60 min</span>
                </div>
                <div class="trust-divider"></div>
                <div class="trust-item">
                    <i class="fas fa-building-columns"></i>
                    <span>Government Grade</span>
                </div>
            </div>

        </div>
    </div>

</body>
</html>