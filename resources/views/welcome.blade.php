<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'PEO Document Monitoring') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|syne:700,800" rel="stylesheet" />

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

            /* ─── NOISE TEXTURE OVERLAY ─── */
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

            /* ─── BACKGROUND BLOBS ─── */
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

            /* ─── NAV ─── */
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
                transition: box-shadow 0.3s;
            }

            nav.scrolled {
                box-shadow: 0 4px 30px rgba(249,115,22,0.1);
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
                gap: 2rem;
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
                transition: background 0.2s, transform 0.2s, box-shadow 0.2s !important;
            }

            .btn-nav:hover {
                background: var(--orange-600) !important;
                transform: translateY(-1px);
                box-shadow: 0 4px 18px rgba(249,115,22,0.5) !important;
                color: #fff !important;
            }

            /* ─── HERO ─── */
            .hero {
                position: relative;
                z-index: 1;
                padding: 160px 2.5rem 100px;
                text-align: center;
                max-width: 900px;
                margin: 0 auto;
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 6px 16px;
                background: var(--orange-100);
                border: 1px solid rgba(249,115,22,0.25);
                border-radius: 100px;
                font-size: 0.8rem;
                font-weight: 600;
                color: var(--orange-700);
                letter-spacing: 0.05em;
                text-transform: uppercase;
                margin-bottom: 2rem;
                animation: fadeUp 0.6s ease both;
            }

            .eyebrow::before {
                content: '';
                width: 6px; height: 6px;
                border-radius: 50%;
                background: var(--orange-500);
            }

            .hero h1 {
                font-family: 'Syne', sans-serif;
                font-size: clamp(2.8rem, 6vw, 5rem);
                font-weight: 800;
                line-height: 1.07;
                letter-spacing: -0.03em;
                color: var(--ink);
                margin-bottom: 1.5rem;
                animation: fadeUp 0.6s 0.1s ease both;
            }

            .hero h1 .accent {
                color: var(--orange-500);
                position: relative;
                display: inline-block;
            }

            .hero h1 .accent::after {
                content: '';
                position: absolute;
                bottom: 0; left: 0;
                width: 100%; height: 4px;
                background: var(--orange-500);
                border-radius: 2px;
                opacity: 0.35;
            }

            .hero p {
                font-size: 1.15rem;
                color: var(--ink-muted);
                max-width: 560px;
                margin: 0 auto 2.5rem;
                animation: fadeUp 0.6s 0.2s ease both;
            }

            .cta-row {
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
                animation: fadeUp 0.6s 0.3s ease both;
            }

            .btn {
                padding: 13px 32px;
                border-radius: 10px;
                font-size: 0.975rem;
                font-weight: 600;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                transition: all 0.25s;
                border: none;
                cursor: pointer;
            }

            .btn-primary {
                background: var(--orange-500);
                color: #fff;
                box-shadow: 0 4px 18px rgba(249,115,22,0.4), inset 0 1px 0 rgba(255,255,255,0.2);
            }

            .btn-primary:hover {
                background: var(--orange-600);
                transform: translateY(-2px);
                box-shadow: 0 8px 28px rgba(249,115,22,0.5);
            }

            .btn-ghost {
                background: transparent;
                color: var(--ink);
                border: 1.5px solid rgba(26,15,0,0.15);
            }

            .btn-ghost:hover {
                border-color: var(--orange-500);
                color: var(--orange-600);
                background: var(--orange-50);
            }

            /* ─── DIVIDER STRIP ─── */
            .strip {
                position: relative; z-index: 1;
                background: var(--orange-500);
                padding: 1.25rem 2.5rem;
                overflow: hidden;
                margin: 0;
            }

            .strip-inner {
                max-width: 1200px;
                margin: 0 auto;
                display: flex;
                justify-content: space-around;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .strip-stat {
                text-align: center;
                color: white;
            }

            .strip-stat strong {
                display: block;
                font-family: 'Syne', sans-serif;
                font-size: 1.6rem;
                font-weight: 800;
                letter-spacing: -0.02em;
            }

            .strip-stat span {
                font-size: 0.8rem;
                opacity: 0.85;
                font-weight: 500;
                letter-spacing: 0.04em;
                text-transform: uppercase;
            }

            /* ─── FEATURES ─── */
            .features {
                position: relative; z-index: 1;
                padding: 90px 2.5rem;
                max-width: 1200px;
                margin: 0 auto;
            }

            .section-header {
                text-align: center;
                margin-bottom: 3.5rem;
            }

            .section-header h2 {
                font-family: 'Syne', sans-serif;
                font-size: clamp(1.8rem, 3.5vw, 2.8rem);
                font-weight: 800;
                letter-spacing: -0.03em;
                color: var(--ink);
                margin-bottom: 0.75rem;
            }

            .section-header p {
                color: var(--ink-muted);
                font-size: 1rem;
                max-width: 500px;
                margin: 0 auto;
            }

            .feature-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 1.25rem;
            }

            .feature-card {
                background: var(--card-bg);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 2rem 2.25rem;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }

            .feature-card::before {
                content: '';
                position: absolute;
                top: 0; left: 0; right: 0;
                height: 3px;
                background: var(--orange-500);
                opacity: 0;
                transition: opacity 0.3s;
            }

            .feature-card:hover {
                transform: translateY(-6px);
                border-color: rgba(249,115,22,0.3);
                box-shadow: 0 16px 48px rgba(249,115,22,0.1), 0 4px 12px rgba(0,0,0,0.06);
            }

            .feature-card:hover::before { opacity: 1; }

            .feature-icon-wrap {
                width: 48px; height: 48px;
                background: var(--orange-50);
                border: 1px solid rgba(249,115,22,0.2);
                border-radius: 12px;
                display: flex; align-items: center; justify-content: center;
                font-size: 1.35rem;
                margin-bottom: 1.25rem;
            }

            .feature-card h3 {
                font-family: 'Syne', sans-serif;
                font-size: 1.05rem;
                font-weight: 700;
                color: var(--ink);
                margin-bottom: 0.6rem;
                letter-spacing: -0.01em;
            }

            .feature-card p {
                font-size: 0.9rem;
                color: var(--ink-muted);
                line-height: 1.75;
            }

            /* ─── FOOTER ─── */
            footer {
                position: relative; z-index: 1;
                border-top: 1px solid var(--border);
                padding: 2rem 2.5rem;
                text-align: center;
                color: var(--ink-muted);
                font-size: 0.85rem;
            }

            /* ─── ANIMATIONS ─── */
            @keyframes fadeUp {
                from { opacity: 0; transform: translateY(24px); }
                to   { opacity: 1; transform: translateY(0); }
            }

            .reveal {
                opacity: 0;
                transform: translateY(30px);
                transition: opacity 0.6s ease, transform 0.6s ease;
            }

            .reveal.visible {
                opacity: 1;
                transform: translateY(0);
            }

            /* ─── RESPONSIVE ─── */
            @media (max-width: 768px) {
                nav { padding: 0 1.25rem; }
                .hero { padding: 130px 1.25rem 70px; }
                .strip-inner { gap: 1.5rem; }
                .features { padding: 60px 1.25rem; }
                footer { padding: 1.5rem 1.25rem; }
            }
        </style>
    </head>
    <body>

        <div class="bg-blob bg-blob-1"></div>
        <div class="bg-blob bg-blob-2"></div>

        <!-- Navigation -->
        <nav id="main-nav">
            <div class="nav-inner">
                <a href="#" class="logo">
                    <div class="logo-icon">📊</div>
                    PEO Monitor
                </a>
                <div class="nav-links">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}">Sign In</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-nav">Get Started</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </nav>

        <!-- Hero -->
        <section class="hero">
            <div class="eyebrow">Project Engineering Office</div>

            <h1>
                Monitor Every<br>
                <span class="accent">Project Document</span><br>
                In One Place
            </h1>

            <p>A centralized monitoring system built for engineers. Track submittals, transmittals, and project records with full visibility from start to finish.</p>

            <div class="cta-row">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                            Open Dashboard
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            Get Started
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-ghost">Request Access</a>
                        @endif
                    @endauth
                @endif
            </div>
        </section>

        <!-- Stats Strip -->
        <div class="strip">
            <div class="strip-inner">
                <div class="strip-stat">
                    <strong>99.9%</strong>
                    <span>Uptime SLA</span>
                </div>
                <div class="strip-stat">
                    <strong>24/7</strong>
                    <span>System Availability</span>
                </div>
                <div class="strip-stat">
                    <strong>AES-256</strong>
                    <span>Encryption Standard</span>
                </div>
                <div class="strip-stat">
                    <strong>Real-time</strong>
                    <span>Status Tracking</span>
                </div>
            </div>
        </div>

        <!-- Features -->
        <section class="features">
            <div class="section-header reveal">
                <h2>Everything You Need to Stay on Top</h2>
                <p>Purpose-built tools for engineering project document control and monitoring.</p>
            </div>

            <div class="feature-grid">
                <div class="feature-card reveal">
                    <div class="feature-icon-wrap">📁</div>
                    <h3>Document Organization</h3>
                    <p>Automatically categorize submittals, transmittals, RFIs, and drawings with intelligent tagging and project-based classification.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon-wrap">🔍</div>
                    <h3>Advanced Search</h3>
                    <p>Locate any document instantly across your entire repository using full-text search, filters, and metadata-driven queries.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon-wrap">🔒</div>
                    <h3>Enterprise Security</h3>
                    <p>Role-based access controls and AES-256 encryption ensure that only authorized personnel can view or modify project records.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon-wrap">📊</div>
                    <h3>Status Dashboard</h3>
                    <p>Get a real-time overview of all document statuses — pending, under review, approved, or overdue — at a single glance.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon-wrap">👥</div>
                    <h3>Team Collaboration</h3>
                    <p>Assign reviewers, track approvals, and keep all stakeholders aligned with shared document workflows and notifications.</p>
                </div>

                <div class="feature-card reveal">
                    <div class="feature-icon-wrap">⚙️</div>
                    <h3>Workflow Automation</h3>
                    <p>Automate submission reminders, deadline alerts, and status transitions to reduce manual overhead and human error.</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer>
            <p>&copy; {{ date('Y') }} PEO Document Monitoring System &mdash; All rights reserved.</p>
        </footer>

        <script>
            // Navbar scroll
            const nav = document.getElementById('main-nav');
            window.addEventListener('scroll', () => {
                nav.classList.toggle('scrolled', window.scrollY > 20);
            });

            // Reveal on scroll
            const reveals = document.querySelectorAll('.reveal');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, i) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.classList.add('visible');
                        }, i * 80);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

            reveals.forEach(el => observer.observe(el));
        </script>
    </body>
</html>