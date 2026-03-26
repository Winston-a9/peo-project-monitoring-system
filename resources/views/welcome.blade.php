<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'PEO Document Monitoring') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Force Light Mode on Welcome Page -->
    <script>
        (function() {
            const html = document.documentElement;
            html.classList.remove('dark');
            html.classList.add('light');
            document.body?.classList.remove('dark');
            document.documentElement.setAttribute('data-page', 'welcome');
        })();
    </script>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --orange: #E05A00;
            --orange-dark: #B84A00;
            --orange-light: #FF8C38;
            --amber: #C97A00;
            --cream: #FFF8F0;
            --warm-white: #FFFCF9;
            --stone: #3D2B1A;
            --stone-mid: #6B4F3A;
            --stone-light: #A07858;
            --gray-soft: #F5EDE4;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--warm-white);
            color: var(--stone);
            overflow-x: hidden;
        }

        /* ─── HEADER ─── */
        header {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            background: rgba(255, 252, 249, 0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid rgba(224, 90, 0, 0.12);
            transition: box-shadow 0.3s;
        }

        header.scrolled {
            box-shadow: 0 4px 32px rgba(224, 90, 0, 0.10);
        }

        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
        }

        .logo-group {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-img {
            width: 56px;
            height: 56px;
            object-fit: contain;
            border-radius: 12px;
        }

        .logo-text h1 {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--stone);
            line-height: 1.2;
            letter-spacing: -0.01em;
        }

        .logo-text p {
            font-size: 0.72rem;
            color: var(--stone-light);
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-ghost {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--stone-mid);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: color 0.2s, background 0.2s;
            text-decoration: none;
        }

        .btn-ghost:hover { color: var(--orange); background: rgba(224,90,0,0.06); }

        .btn-primary {
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: white;
            background: var(--orange);
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 2px 12px rgba(224,90,0,0.28);
        }

        .btn-primary:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 18px rgba(224,90,0,0.36);
        }

        .btn-dashboard {
            padding: 10px 22px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, var(--orange), var(--orange-light));
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: opacity 0.2s, transform 0.15s;
            box-shadow: 0 2px 16px rgba(224,90,0,0.30);
        }

        .btn-dashboard:hover { opacity: 0.9; transform: translateY(-1px); }

        /* ─── HERO ─── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 100px 2rem 60px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 70% 50%, rgba(224, 90, 0, 0.08) 0%, transparent 70%),
                radial-gradient(ellipse 50% 80% at 10% 90%, rgba(201, 122, 0, 0.06) 0%, transparent 60%);
            pointer-events: none;
        }

        /* Decorative geometric accent */
        .hero-geo {
            position: absolute;
            right: -80px;
            top: 50%;
            transform: translateY(-50%);
            width: 600px;
            height: 600px;
            border-radius: 50%;
            border: 1px solid rgba(224, 90, 0, 0.10);
            pointer-events: none;
        }

        .hero-geo::before {
            content: '';
            position: absolute;
            inset: 40px;
            border-radius: 50%;
            border: 1px solid rgba(224, 90, 0, 0.08);
        }

        .hero-geo::after {
            content: '';
            position: absolute;
            inset: 100px;
            border-radius: 50%;
            background: radial-gradient(ellipse, rgba(224,90,0,0.06) 0%, transparent 70%);
        }

        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 100px;
            background: rgba(224, 90, 0, 0.10);
            border: 1px solid rgba(224, 90, 0, 0.20);
            color: var(--orange);
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .hero-badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--orange);
            animation: pulse-dot 2s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.8); }
        }

        .hero h2 {
            font-size: clamp(2.4rem, 4vw, 3.8rem);
            font-weight: 900;
            line-height: 1.08;
            letter-spacing: -0.03em;
            color: var(--stone);
            margin-bottom: 24px;
        }

        .hero h2 em {
            font-style: italic;
            color: var(--orange);
        }

        .hero-desc {
            font-size: 1.05rem;
            line-height: 1.7;
            color: var(--stone-mid);
            max-width: 480px;
            margin-bottom: 40px;
            font-weight: 300;
        }

        .hero-cta {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            padding: 14px 32px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            color: white;
            background: var(--orange);
            text-decoration: none;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow: 0 4px 20px rgba(224,90,0,0.32);
        }

        .btn-hero-primary:hover {
            background: var(--orange-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 28px rgba(224,90,0,0.42);
        }

        .btn-hero-outline {
            padding: 13px 32px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--orange);
            background: transparent;
            border: 1.5px solid var(--orange);
            text-decoration: none;
            transition: background 0.2s, color 0.2s, transform 0.15s;
        }

        .btn-hero-outline:hover {
            background: rgba(224,90,0,0.08);
            transform: translateY(-2px);
        }

        /* Hero right - visual card */
        .hero-visual {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px 28px;
            box-shadow: 0 4px 32px rgba(61, 43, 26, 0.08);
            border: 1px solid rgba(224, 90, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
            animation: float-in 0.6s ease forwards;
            opacity: 0;
        }

        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 40px rgba(61,43,26,0.12); }
        .stat-card:nth-child(1) { animation-delay: 0.2s; }
        .stat-card:nth-child(2) { animation-delay: 0.35s; margin-left: 32px; }
        .stat-card:nth-child(3) { animation-delay: 0.5s; }

        @keyframes float-in {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--orange), var(--orange-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .stat-icon.green { background: linear-gradient(135deg, #2D9E6B, #42C882); }
        .stat-icon.amber { background: linear-gradient(135deg, var(--amber), #F0A500); }

        .stat-num {
            font-size: 2rem;
            font-weight: 900;
            color: var(--stone);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.82rem;
            color: var(--stone-light);
            font-weight: 500;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        /* ─── DIVIDER ─── */
        .section-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(224,90,0,0.15), transparent);
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ─── SERVICES ─── */
        .services {
            padding: 100px 2rem;
            background: var(--warm-white);
        }

        .section-header {
            text-align: center;
            margin-bottom: 64px;
        }

        .section-label {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--orange);
            margin-bottom: 16px;
        }

        .section-title {
            font-size: clamp(2rem, 3.5vw, 2.8rem);
            font-weight: 900;
            letter-spacing: -0.025em;
            color: var(--stone);
            margin-bottom: 16px;
            line-height: 1.15;
        }

        .section-sub {
            font-size: 1rem;
            color: var(--stone-mid);
            max-width: 520px;
            margin: 0 auto;
            line-height: 1.65;
            font-weight: 300;
        }

        .services-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .service-card {
            background: white;
            border-radius: 20px;
            padding: 36px 28px;
            border: 1px solid rgba(224, 90, 0, 0.08);
            box-shadow: 0 2px 16px rgba(61, 43, 26, 0.05);
            transition: transform 0.25s, box-shadow 0.25s, border-color 0.25s;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--orange), var(--orange-light));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 48px rgba(61, 43, 26, 0.12);
            border-color: rgba(224, 90, 0, 0.15);
        }

        .service-card:hover::before { transform: scaleX(1); }

        .service-icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: var(--gray-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 22px;
            transition: background 0.25s;
        }

        .service-card:hover .service-icon-wrap {
            background: linear-gradient(135deg, var(--orange), var(--orange-light));
        }

        .service-icon-wrap svg {
            width: 26px;
            height: 26px;
            color: var(--orange);
            transition: color 0.25s;
        }

        .service-card:hover .service-icon-wrap svg { color: white; }

        .service-card h4 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--stone);
            margin-bottom: 10px;
        }

        .service-card p {
            font-size: 0.88rem;
            color: var(--stone-mid);
            line-height: 1.65;
            font-weight: 300;
        }

        /* ─── STATS BAND ─── */
        .stats-band {
            background: var(--stone);
            padding: 80px 2rem;
            position: relative;
            overflow: hidden;
        }

        .stats-band::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 80% 100% at 50% 50%, rgba(224,90,0,0.15) 0%, transparent 70%);
        }

        .stats-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2px;
            position: relative;
            z-index: 1;
        }

        .stat-item {
            text-align: center;
            padding: 40px 20px;
            border-right: 1px solid rgba(255,255,255,0.08);
        }

        .stat-item:last-child { border-right: none; }

        .stat-big {
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--orange-light);
            line-height: 1;
            margin-bottom: 8px;
            letter-spacing: -0.04em;
        }

        .stat-text {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.60);
            font-weight: 400;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        /* ─── CONTACT ─── */
        .contact {
            padding: 100px 2rem;
            background: var(--cream);
        }

        .contact-inner {
            max-width: 900px;
            margin: 0 auto;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 56px;
        }

        .contact-card {
            background: white;
            border-radius: 20px;
            padding: 36px 32px;
            border: 1px solid rgba(224, 90, 0, 0.08);
            box-shadow: 0 2px 16px rgba(61,43,26,0.05);
            display: flex;
            align-items: flex-start;
            gap: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .contact-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 36px rgba(61,43,26,0.10);
        }

        .contact-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--orange), var(--orange-light));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .contact-card h4 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--stone);
            margin-bottom: 6px;
        }

        .contact-card p {
            font-size: 0.875rem;
            color: var(--stone-mid);
            line-height: 1.6;
            font-weight: 300;
        }

        /* ─── FOOTER ─── */
        footer {
            background: var(--stone);
            padding: 48px 2rem;
            text-align: center;
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-logo {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .footer-logo img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 8px;
            opacity: 0.9;
        }

        .footer-logo span {
            font-size: 1rem;
            font-weight: 700;
            color: rgba(255,255,255,0.90);
        }

        .footer-sub {
            font-size: 0.82rem;
            color: rgba(255,255,255,0.45);
            margin-bottom: 24px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .footer-divider {
            height: 1px;
            background: rgba(255,255,255,0.08);
            margin: 24px 0;
        }

        .footer-copy {
            font-size: 0.80rem;
            color: rgba(255,255,255,0.35);
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 900px) {
            .hero-inner { grid-template-columns: 1fr; gap: 40px; }
            .hero-visual { display: none; }
            .services-grid { grid-template-columns: 1fr 1fr; }
            .stats-inner { grid-template-columns: 1fr; }
            .stat-item { border-right: none; border-bottom: 1px solid rgba(255,255,255,0.08); }
            .stat-item:last-child { border-bottom: none; }
            .contact-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 600px) {
            .services-grid { grid-template-columns: 1fr; }
            .header-inner { padding: 0 1rem; }
            .hero { padding: 90px 1rem 50px; }
        }
    </style>
</head>
<body>

    <!-- HEADER -->
    <header id="main-header">
        <div class="header-inner">
            <div class="logo-group">
                <img src="{{ asset('assets/app_logo.PNG') }}" alt="PEO Logo" class="logo-img">
                <div class="logo-text">
                    <h1>Provincial Engineering Office</h1>
                    <p>Malaybalay City, Bukidnon</p>
                </div>
            </div>

            @if (Route::has('login'))
                <nav class="nav-links">
                    @auth
                        @php
                            $role = Auth::user()->role ?? null;
                            $dashboardRoute = match($role) {
                                'admin'                                                                                                          => 'admin.dashboard',
                                'contractor'                                                                                                     => 'user.dashboard',
                                'site_inspector', 'surveyor', 'resident_engineer','mtqa', 'engineeriv', 'engineeriii', 'provincial_engineer'           => 'reviewer.dashboard',
                                default                                                                                                          => 'dashboard',
                            };
                        @endphp
                        <a href="{{ route($dashboardRoute) }}" class="btn-dashboard">
                            <i class="fas fa-tachometer-alt" style="margin-right:7px;"></i>Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-ghost">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">Register</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </div>
    </header>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-geo"></div>
        <div class="hero-inner">
            <div class="hero-content">
                <div class="hero-badge">
                    <div class="hero-badge-dot"></div>
                    Serving Malaybalay City
                </div>
                <h2>
                    Building Better<br>
                    <em>Infrastructure</em><br>
                    For Our Community
                </h2>
                <p class="hero-desc">
                    The Provincial Engineering Office is committed to delivering quality engineering services and sustainable infrastructure development for the people of Bukidnon.
                </p>
                <div class="hero-cta">
                    @if (Route::has('login'))
                        @auth
                            @php
                                $role = Auth::user()->role ?? null;
                                $dashboardRoute = match($role) {
                                    'admin'                                                                                                          => 'admin.dashboard',
                                    'contractor'                                                                                                     => 'user.dashboard',
                                    'site_inspector', 'surveyor', 'resident_engineer','mtqa', 'engineeriv', 'engineeriii', 'provincial_engineer'           => 'reviewer.dashboard',
                                    default                                                                                                          => 'dashboard',
                                };
                            @endphp
                            <a href="{{ route($dashboardRoute) }}" class="btn-hero-primary">
                                Open Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-hero-primary">Get Started</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-hero-outline">Request Access</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>

            <div class="hero-visual">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-hard-hat"></i>
                    </div>
                    <div>
                        <div class="stat-num">500+</div>
                        <div class="stat-label">Projects Completed</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="stat-num">24/7</div>
                        <div class="stat-label">Service Availability</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon amber">
                        <i class="fas fa-award"></i>
                    </div>
                    <div>
                        <div class="stat-num">15+</div>
                        <div class="stat-label">Years of Experience</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <!-- SERVICES -->
    <section class="services" id="services">
        <div class="section-header">
            <span class="section-label">What We Offer</span>
            <h3 class="section-title">Our Services</h3>
            <p class="section-sub">Comprehensive engineering solutions for infrastructure development and public works across Bukidnon.</p>
        </div>

        <div class="services-grid">

            <!-- Card 1 -->
            <div class="service-card">
                <div class="service-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                </div>
                <h4>Infrastructure Planning</h4>
                <p>Strategic planning and design of roads, bridges, and public facilities for sustainable development.</p>
            </div>

            <!-- Card 2 -->
            <div class="service-card">
                <div class="service-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h4>Project Management</h4>
                <p>Professional oversight and management of engineering projects from conception to completion.</p>
            </div>

            <!-- Card 3 -->
            <div class="service-card">
                <div class="service-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
                    </svg>
                </div>
                <h4>Technical Assistance</h4>
                <p>Expert technical support and consultation for construction and engineering concerns.</p>
            </div>

            <!-- Card 4 -->
            <div class="service-card">
                <div class="service-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h4>Quality Assurance</h4>
                <p>Rigorous inspection and quality control to ensure compliance with engineering standards.</p>
            </div>

            <!-- Card 5 -->
            <div class="service-card">
                <div class="service-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h4>Building Permits</h4>
                <p>Streamlined processing of building permits and compliance certifications.</p>
            </div>

            <!-- Card 6 -->
            <div class="service-card">
                <div class="service-icon-wrap">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h4>Documentation Services</h4>
                <p>Comprehensive engineering documentation and archival services for public records.</p>
            </div>

        </div>
    </section>

    <!-- STATS BAND -->
    <section class="stats-band">
        <div class="stats-inner">
            <div class="stat-item">
                <div class="stat-big">500+</div>
                <div class="stat-text">Projects Completed</div>
            </div>
            <div class="stat-item">
                <div class="stat-big">24/7</div>
                <div class="stat-text">Service Availability</div>
            </div>
            <div class="stat-item">
                <div class="stat-big">15+</div>
                <div class="stat-text">Years of Experience</div>
            </div>
        </div>
    </section>

    <!-- CONTACT -->
    <section class="contact">
        <div class="contact-inner">
            <div class="section-header">
                <span class="section-label">Reach Out</span>
                <h3 class="section-title">Get in Touch</h3>
                <p class="section-sub">Have questions or need assistance? Our team is ready to help with your engineering needs.</p>
            </div>
            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <h4>Visit Us</h4>
                        <p>Provincial Capitol, Malaybalay City, Bukidnon</p>
                    </div>
                </div>
                <div class="contact-card">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <h4>Email Us</h4>
                        <p>peo.malaybalay@bukidnon.gov.ph</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-inner">
            <div class="footer-logo">
                <img src="{{ asset('assets/app_logo.PNG') }}" alt="PEO Logo">
                <span>Provincial Engineering Office</span>
            </div>
            <p class="footer-sub">Malaybalay City, Bukidnon</p>
            <div class="footer-divider"></div>
            <p class="footer-copy">&copy; {{ date('Y') }} Provincial Engineering Office. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Sticky header shadow on scroll
        const header = document.getElementById('main-header');
        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 10);
        });
    </script>

</body>
</html>