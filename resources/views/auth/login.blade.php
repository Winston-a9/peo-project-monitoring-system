<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — {{ config('app.name', 'PEO Monitor') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        /* ── RESET & BASE ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

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

            --glass-bg:     rgba(255,255,255,0.04);
            --glass-border: rgba(255,255,255,0.08);

            --font-display: 'Syne', sans-serif;
            --font-body:    'DM Sans', sans-serif;
            --ease-spring:  cubic-bezier(0.34, 1.56, 0.64, 1);
            --ease-smooth:  cubic-bezier(0.23, 1, 0.32, 1);
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font-body);
            background: var(--dark-950);
            color: #fff;
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            -webkit-font-smoothing: antialiased;
        }

        /* ── NOISE OVERLAY ── */
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none; z-index: 9999; opacity: 0.4;
        }

        /* ──────────────────────────────────────────
           LEFT PANEL — brand / art side
        ────────────────────────────────────────── */
        .panel-left {
            position: relative;
            width: 48%;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
            overflow: hidden;
        }

        /* Same layered bg as hero section */
        .panel-left-bg {
            position: absolute; inset: 0;
            background: radial-gradient(ellipse 80% 60% at 65% 30%, rgba(234,88,12,0.22) 0%, transparent 60%),
                        radial-gradient(ellipse 50% 40% at 15% 80%, rgba(251,146,60,0.12) 0%, transparent 50%),
                        linear-gradient(170deg, var(--dark-950) 0%, #0f0a06 50%, #0a0a0a 100%);
        }

        /* Grid pattern matching hero-grid */
        .panel-left-grid {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(249,115,22,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(249,115,22,0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            mask-image: radial-gradient(ellipse 70% 70% at 60% 40%, black, transparent);
        }

        /* Glow orbs matching hero-orb-1 / 2 */
        .panel-left-orb1 {
            position: absolute; top: 10%; right: 5%;
            width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(234,88,12,0.18) 0%, transparent 70%);
            border-radius: 50%;
            animation: breathe 8s ease-in-out infinite;
        }
        .panel-left-orb2 {
            position: absolute; bottom: 5%; left: -5%;
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(251,146,60,0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: breathe 11s ease-in-out infinite reverse;
        }

        /* Diagonal accent line */
        .panel-left-accent {
            position: absolute; top: 0; right: 20%;
            width: 1px; height: 100%;
            background: linear-gradient(180deg, transparent 0%, rgba(249,115,22,0.12) 30%, rgba(249,115,22,0.06) 70%, transparent 100%);
        }

        @keyframes breathe {
            0%,100% { transform: scale(1); opacity: 1; }
            50%      { transform: scale(1.15); opacity: 0.7; }
        }

        /* Brand logo — same structure as navbar-brand */
        .brand-logo {
            position: relative; z-index: 2;
            display: flex; align-items: center; gap: 0.875rem;
            text-decoration: none; color: inherit;
        }

        .brand-logo-ring-wrap {
            width: 44px; height: 44px; position: relative; flex-shrink: 0;
        }

        .brand-logo-ring {
            position: absolute; inset: -3px;
            border-radius: 13px;
            background: conic-gradient(from 0deg, var(--orange-500), var(--amber-400), var(--orange-500));
            animation: spin-slow 6s linear infinite;
            opacity: 0.7;
        }

        @keyframes spin-slow { to { transform: rotate(360deg); } }

        .brand-logo-inner {
            position: relative; width: 100%; height: 100%;
            border-radius: 11px; background: var(--dark-900);
            display: flex; align-items: center; justify-content: center; overflow: hidden;
        }
        .brand-logo-inner img { width: 100%; height: 100%; object-fit: contain; border-radius: 11px; }

        .brand-text-group { line-height: 1; }

        .brand-org {
            font-family: var(--font-display);
            font-size: 13px; font-weight: 700;
            color: var(--orange-400); letter-spacing: 0.3px;
        }

        .brand-sub {
            font-size: 10.5px; color: var(--dark-200);
            margin-top: 3px; letter-spacing: 0.4px;
        }

        /* Hero content area */
        .panel-hero {
            position: relative; z-index: 2;
        }

        .panel-eyebrow {
            display: inline-flex; align-items: center; gap: 0.5rem;
            margin-bottom: 1.75rem;
            opacity: 0; transform: translateY(16px);
            animation: fade-up 0.7s var(--ease-smooth) 0.15s forwards;
        }

        .eyebrow-badge {
            display: flex; align-items: center; gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: rgba(249,115,22,0.08);
            border: 1px solid rgba(249,115,22,0.2);
            border-radius: 100px;
            font-size: 11px; font-weight: 600; letter-spacing: 0.5px;
            text-transform: uppercase; color: var(--orange-400);
        }

        .eyebrow-pulse {
            width: 6px; height: 6px; border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 0 0 rgba(74,222,128,0.5);
            animation: ring-pulse 2.5s ease-in-out infinite;
        }

        @keyframes ring-pulse {
            0%  { box-shadow: 0 0 0 0   rgba(74,222,128,0.6); }
            70% { box-shadow: 0 0 0 8px rgba(74,222,128,0);   }
            100%{ box-shadow: 0 0 0 0   rgba(74,222,128,0);   }
        }

        .panel-h1 {
            font-family: var(--font-display);
            font-size: clamp(2.8rem, 4.5vw, 4.2rem);
            font-weight: 800; line-height: 1.05; letter-spacing: -2px;
            margin-bottom: 1.5rem;
            opacity: 0; transform: translateY(20px);
            animation: fade-up 0.8s var(--ease-smooth) 0.25s forwards;
        }

        .h1-white { display: block; color: #fff; }

        .h1-gradient {
            background: linear-gradient(90deg, var(--orange-500) 0%, var(--amber-400) 50%, var(--orange-400) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .panel-lead {
            font-size: 0.975rem; color: var(--dark-200);
            line-height: 1.85; max-width: 400px;
            font-weight: 300;
            opacity: 0; transform: translateY(16px);
            animation: fade-up 0.8s var(--ease-smooth) 0.4s forwards;
        }

        .panel-lead strong { color: var(--dark-100); font-weight: 500; }

        @keyframes fade-up { to { opacity: 1; transform: translateY(0); } }

        /* Feature bullets — matching preview-point style */
        .panel-features {
            position: relative; z-index: 2;
            display: flex; flex-direction: column; gap: 0.75rem;
            opacity: 0; transform: translateY(16px);
            animation: fade-up 0.8s var(--ease-smooth) 0.55s forwards;
        }

        .panel-feature {
            display: flex; align-items: center; gap: 0.875rem;
            padding: 0.9rem 1.1rem;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .panel-feature:hover {
            border-color: rgba(249,115,22,0.18);
            background: rgba(249,115,22,0.05);
        }

        .panel-feature-icon {
            width: 32px; height: 32px; flex-shrink: 0;
            background: rgba(249,115,22,0.1);
            border: 1px solid rgba(249,115,22,0.15);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; color: var(--orange-400);
        }

        .panel-feature-text { font-size: 13px; color: var(--dark-100); font-weight: 400; }

        /* ──────────────────────────────────────────
           RIGHT PANEL — form side
        ────────────────────────────────────────── */
        .panel-right {
            flex: 1;
            position: relative;
            display: flex; align-items: center; justify-content: center;
            padding: 3rem 2rem;
            overflow-y: auto;
        }

        /* Subtle border separator */
        .panel-right::before {
            content: '';
            position: absolute; top: 10%; left: 0;
            width: 1px; height: 80%;
            background: linear-gradient(180deg, transparent, rgba(249,115,22,0.15), transparent);
        }

        /* Faint glow echoing hero-orb */
        .panel-right-glow {
            position: absolute; top: 30%; right: 10%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(249,115,22,0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ── FORM BOX — styled like KPI panel ── */
        .form-box {
            position: relative; z-index: 1;
            width: 100%; max-width: 440px;
            opacity: 0; transform: translateY(30px) scale(0.97);
            animation: fade-up 0.9s var(--ease-smooth) 0.3s forwards;
        }

        /* Top accent bar like kpi-top-bar */
        .form-card {
            background: var(--glass-bg);
            backdrop-filter: blur(32px) saturate(150%);
            border: 1px solid rgba(249,115,22,0.15);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 24px 80px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.05);
        }

        .form-top-bar {
            height: 3px;
            background: linear-gradient(90deg, var(--orange-600), var(--amber-400), var(--orange-500));
        }

        .form-body { padding: 2.25rem 2rem; }

        /* Heading */
        .form-heading { margin-bottom: 2rem; }

        .form-heading h1 {
            font-family: var(--font-display);
            font-size: 1.7rem; font-weight: 800;
            letter-spacing: -0.04em; color: #fff;
            margin-bottom: 0.375rem;
        }

        .form-heading p {
            font-size: 13px; color: var(--dark-300); font-weight: 300;
        }

        /* Live badge, matching kpi-live-dot */
        .form-live-badge {
            display: flex; align-items: center; gap: 0.45rem;
            font-size: 11px; font-weight: 600; color: #4ade80;
            padding: 0.3rem 0.75rem;
            background: rgba(74,222,128,0.08);
            border: 1px solid rgba(74,222,128,0.15);
            border-radius: 100px;
            width: fit-content;
            margin-bottom: 1.75rem;
        }

        .live-dot {
            width: 6px; height: 6px; border-radius: 50%; background: #4ade80;
            animation: ring-pulse 2s ease-in-out infinite;
        }

        /* Alert */
        .alert-success {
            display: flex; align-items: center; gap: 0.6rem;
            padding: 0.875rem 1rem;
            background: rgba(74,222,128,0.06);
            border: 1px solid rgba(74,222,128,0.15);
            color: #4ade80;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 1.5rem;
        }

        /* Form groups */
        .form-group { margin-bottom: 1.25rem; }

        .form-label {
            display: block;
            font-size: 11px; font-weight: 600;
            letter-spacing: 1px; text-transform: uppercase;
            color: var(--dark-200);
            margin-bottom: 0.55rem;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
            font-size: 13px; color: var(--dark-400); pointer-events: none;
            transition: color 0.2s;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.75rem;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            font-family: var(--font-body);
            font-size: 14px; color: #fff;
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
        }

        input[type="email"]::placeholder,
        input[type="password"]::placeholder { color: var(--dark-400); }

        input[type="email"]:hover,
        input[type="password"]:hover {
            border-color: rgba(255,255,255,0.14);
            background: rgba(255,255,255,0.06);
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: rgba(249,115,22,0.45);
            background: rgba(249,115,22,0.04);
            box-shadow: 0 0 0 4px rgba(249,115,22,0.1);
        }

        input[type="email"]:focus ~ .input-icon,
        input[type="password"]:focus ~ .input-icon { color: var(--orange-400); }

        /* Flip icon to be after input for the ~ selector trick — use JS instead */
        .input-wrap:focus-within .input-icon { color: var(--orange-400); }

        input.is-error { border-color: rgba(248,113,113,0.45) !important; }
        input.is-error:focus { box-shadow: 0 0 0 4px rgba(248,113,113,0.1) !important; }

        .field-error {
            display: flex; align-items: center; gap: 0.35rem;
            font-size: 12px; color: #f87171;
            margin-top: 0.45rem;
        }

        /* Divider row — remember + forgot */
        .form-row {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.75rem;
        }

        .remember-wrap { display: flex; align-items: center; gap: 0.5rem; }

        input[type="checkbox"] {
            width: 15px; height: 15px;
            accent-color: var(--orange-500); cursor: pointer;
        }

        .remember-label {
            font-size: 13px; color: var(--dark-300);
            font-weight: 400; cursor: pointer;
        }

        .forgot-link {
            font-size: 13px; font-weight: 600;
            color: var(--orange-400); text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: var(--orange-300); }

        /* Submit button — matching btn-cta-primary */
        .btn-submit {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 0.7rem;
            padding: 0.95rem;
            font-family: var(--font-body);
            font-size: 14px; font-weight: 600; color: #fff;
            background: linear-gradient(135deg, var(--orange-600) 0%, var(--orange-500) 100%);
            border: 1px solid var(--orange-500);
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 24px rgba(249,115,22,0.3), inset 0 1px 0 rgba(255,255,255,0.1);
            transition: all 0.35s var(--ease-spring);
            position: relative; overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent 40%, rgba(255,255,255,0.1) 100%);
            opacity: 0; transition: opacity 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px) scale(1.01);
            box-shadow: 0 8px 40px rgba(249,115,22,0.45), inset 0 1px 0 rgba(255,255,255,0.15);
        }

        .btn-submit:hover::before { opacity: 1; }
        .btn-submit:active { transform: translateY(0) scale(1); }

        /* Register row */
        .register-row {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.06);
            font-size: 13px; color: var(--dark-400);
        }

        .register-row a {
            color: var(--orange-400); font-weight: 600;
            text-decoration: none; transition: color 0.2s;
        }
        .register-row a:hover { color: var(--orange-300); }

        /* Timestamp footer — matching kpi-footer-row */
        .form-footer {
            display: flex; align-items: center; justify-content: center;
            gap: 0.4rem;
            margin-top: 2rem;
            font-size: 11px; color: var(--dark-400);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .panel-left { display: none; }
            .panel-right { padding: 2rem 1.25rem; }
            .panel-right::before { display: none; }
        }
    </style>
</head>
<body>

    <!-- ══════════════ LEFT PANEL — brand & hero ══════════════ -->
    <div class="panel-left">
        <div class="panel-left-bg"></div>
        <div class="panel-left-grid"></div>
        <div class="panel-left-orb1"></div>
        <div class="panel-left-orb2"></div>
        <div class="panel-left-accent"></div>

        <!-- Brand — mirrors navbar-brand -->
        <a class="brand-logo" href="/">
            <div class="brand-logo-ring-wrap">
                <div class="brand-logo-inner">
                    @php $logoPath = public_path('assets/app_logo.PNG'); @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ asset('assets/app_logo.PNG') }}" alt="PEO Logo">
                    @else
                        <i class="fas fa-building-columns" style="color:var(--orange-400);font-size:18px;"></i>
                    @endif
                </div>
            </div>
            <div class="brand-text-group">
                <div class="brand-org">Provincial Engineering Office</div>
                <div class="brand-sub">Bukidnon · Project Monitoring System</div>
            </div>
        </a>

        <!-- Hero headline — mirrors hero-content -->
        <div class="panel-hero">
            <div class="panel-eyebrow">
                <div class="eyebrow-badge">
                    <div class="eyebrow-pulse"></div>
                    Secure Access Portal
                </div>
            </div>

            <h1 class="panel-h1">
                <span class="h1-white">Project</span>
                <span class="h1-white"><span class="h1-gradient">Document</span></span>
                <span class="h1-white">Command</span>
            </h1>

            <p class="panel-lead">
                Centralized monitoring for <strong>infrastructure contracts</strong>, time extensions, liquidated damages, and billing across all Bukidnon engineering districts.
            </p>
        </div>

        <!-- Feature bullets — mirrors preview-points -->
        <div class="panel-features">
            <div class="panel-feature">
                <div class="panel-feature-icon"><i class="fas fa-shield-halved"></i></div>
                <span class="panel-feature-text">Enterprise-grade security & role-based access</span>
            </div>
            <div class="panel-feature">
                <div class="panel-feature-icon"><i class="fas fa-bolt"></i></div>
                <span class="panel-feature-text">Real-time contract & deadline tracking</span>
            </div>
            <div class="panel-feature">
                <div class="panel-feature-icon"><i class="fas fa-users"></i></div>
                <span class="panel-feature-text">Multi-district collaboration platform</span>
            </div>
        </div>
    </div>

    <!-- ══════════════ RIGHT PANEL — login form ══════════════ -->
    <div class="panel-right">
        <div class="panel-right-glow"></div>

        <div class="form-box">

            <!-- Card — mirrors kpi-panel -->
            <div class="form-card">
                <div class="form-top-bar"></div>
                <div class="form-body">

                    <div class="form-heading">
                        <h1>Welcome back</h1>
                        <p>Sign in to your PEO Monitor account</p>
                    </div>

                    <!-- Live badge — mirrors kpi-live-dot -->
                    <div class="form-live-badge">
                        <div class="live-dot"></div>
                        System Online · {{ now()->format('M d, Y') }}
                    </div>

                    @if (session('status'))
                        <div class="alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <div class="input-wrap">
                                <i class="fas fa-envelope input-icon"></i>
                                <input
                                    id="email" type="email" name="email"
                                    value="{{ old('email') }}"
                                    class="{{ $errors->has('email') ? 'is-error' : '' }}"
                                    required autofocus autocomplete="username"
                                    placeholder="you@peo.gov.ph"
                                />
                            </div>
                            @error('email')
                                <p class="field-error">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label class="form-label" for="password">Password</label>
                            <div class="input-wrap">
                                <i class="fas fa-lock input-icon"></i>
                                <input
                                    id="password" type="password" name="password"
                                    class="{{ $errors->has('password') ? 'is-error' : '' }}"
                                    required autocomplete="current-password"
                                    placeholder="••••••••"
                                />
                            </div>
                            @error('password')
                                <p class="field-error">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Remember + Forgot -->
                        <div class="form-row">
                            <div class="remember-wrap">
                                <input id="remember_me" type="checkbox" name="remember">
                                <label class="remember-label" for="remember_me">Remember me</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot-link">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-arrow-right-to-bracket"></i>
                            Sign In to System
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

            <!-- Footer timestamp — mirrors kpi-timestamp -->
            <div class="form-footer">
                <i class="fas fa-circle-dot" style="color:#4ade80;font-size:8px;"></i>
                &copy; {{ now()->year }} Provincial Engineering Office — Bukidnon
            </div>

        </div>
    </div>

</body>
</html>