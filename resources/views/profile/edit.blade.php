<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Provincial Engineering Office — Profile Management">
    <title>Profile Settings — {{ config('app.name', 'PEO Project Monitoring') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap"
        rel="stylesheet">
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
            --orange-50: #fff7ed;
            --amber-400: #fbbf24;
            --amber-300: #fcd34d;

            --dark-950: #0a0a0a;
            --dark-900: #111111;
            --dark-800: #1a1a1a;
            --dark-700: #242424;
            --dark-600: #2e2e2e;
            --dark-400: #525252;
            --dark-300: #737373;
            --dark-200: #a3a3a3;
            --dark-100: #d4d4d4;

            --white: #ffffff;
            --glass-bg: rgba(255, 255, 255, 0.06);
            --glass-border: rgba(255, 255, 255, 0.1);

            --font-display: 'Syne', sans-serif;
            --font-body: 'DM Sans', sans-serif;

            --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
            --ease-smooth: cubic-bezier(0.23, 1, 0.32, 1);

            --nav-h: 72px;
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--font-body);
            background: var(--dark-950);
            color: var(--white);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        ::selection {
            background: var(--orange-500);
            color: #fff;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-900);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--orange-600);
            border-radius: 3px;
        }

        /* ─── NOISE TEXTURE (same as welcome) ───────────────────────────── */
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
            top: 15%;
            right: 10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(234, 88, 12, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            animation: breathe 8s ease-in-out infinite;
            z-index: 0;
            pointer-events: none;
        }

        .hero-orb-2 {
            position: fixed;
            bottom: 5%;
            left: 5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(251, 146, 60, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            animation: breathe 11s ease-in-out infinite reverse;
            z-index: 0;
            pointer-events: none;
        }

        @keyframes breathe {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.15);
                opacity: 0.7;
            }
        }

        /* ─── NAVBAR (exact welcome page structure) ─────────────────────── */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: var(--nav-h);
            display: flex;
            align-items: center;
            transition: all 0.4s var(--ease-smooth);
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
            width: 44px;
            height: 44px;
            position: relative;
            flex-shrink: 0;
        }

        .navbar-logo-inner {
            position: relative;
            width: 100%;
            height: 100%;
            border-radius: 11px;
            background: var(--dark-900);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .navbar-logo-inner img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 11px;
        }

        .navbar-logo-inner .logo-fallback {
            background: linear-gradient(135deg, var(--orange-500), var(--amber-400));
            width: 100%;
            height: 100%;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            box-shadow: 0 8px 16px rgba(249, 115, 22, 0.3);
        }

        .navbar-text-group {
            line-height: 1;
        }

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

        .nav-user-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--dark-200);
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
            cursor: pointer;
            box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.4);
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
            padding: calc(var(--nav-h) + 4.5rem) 2.5rem 5rem;
        }

        .page-inner {
            max-width: 1320px;
            margin: 0 auto;
        }

        /* ─── PAGE HEADER (mirrors section-tag + section-h2 pattern) ────── */
        .page-header {
            margin-bottom: 3.5rem;
            opacity: 0;
            transform: translateY(20px);
            animation: fade-up 0.7s var(--ease-smooth) 0.1s forwards;
        }

        .page-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--orange-400);
            margin-bottom: 1rem;
        }

        .page-eyebrow::before {
            content: '';
            width: 20px;
            height: 1.5px;
            background: var(--orange-500);
            display: block;
        }

        .page-header h1 {
            font-family: var(--font-display);
            font-size: clamp(2.4rem, 3.5vw, 3.5rem);
            font-weight: 800;
            letter-spacing: -1.5px;
            color: var(--white);
            line-height: 1.08;
            margin-bottom: 0.75rem;
        }

        .page-header p {
            font-size: 1.05rem;
            color: var(--dark-300);
            line-height: 1.8;
            font-weight: 300;
            max-width: 520px;
        }

        /* ─── CARDS GRID ─────────────────────────────────────────────────── */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 1.75rem;
        }

        /* ─── PROFILE CARD (feature-card + kpi-panel hybrid) ────────────── */
        .profile-card {
            position: relative;
            background: var(--dark-900);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 18px;
            overflow: hidden;
            transition: all 0.4s var(--ease-smooth);
            opacity: 0;
            transform: translateY(24px);
        }

        .profile-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .profile-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 18px;
            background: radial-gradient(circle at 50% 0%, rgba(249, 115, 22, 0.07) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .profile-card:hover {
            border-color: rgba(249, 115, 22, 0.2);
            transform: translateY(-4px);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(249, 115, 22, 0.08);
        }

        .profile-card:hover::after {
            opacity: 1;
        }

        /* top accent bar — same as kpi-panel .kpi-top-bar */
        .card-top-bar {
            height: 3px;
            background: linear-gradient(90deg, var(--orange-600), var(--amber-400), var(--orange-500));
        }

        .danger-card .card-top-bar {
            background: linear-gradient(90deg, #ef4444, #dc2626, #b91c1c);
        }

        .card-body {
            padding: 2rem 2rem 2.25rem;
        }

        /* ─── CARD HEADER ────────────────────────────────────────────────── */
        .card-header {
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
            margin-bottom: 1.75rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .card-icon {
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            border-radius: 11px;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.15), rgba(234, 88, 12, 0.08));
            border: 1px solid rgba(249, 115, 22, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            color: var(--orange-400);
            transition: all 0.3s var(--ease-spring);
        }

        .profile-card:hover .card-icon {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.25), rgba(234, 88, 12, 0.15));
            border-color: rgba(249, 115, 22, 0.3);
            transform: scale(1.06);
        }

        .danger-card .card-icon {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.15);
            color: #f87171;
        }

        .danger-card:hover .card-icon {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.28);
        }

        .card-header-text h2 {
            font-family: var(--font-display);
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: -0.3px;
            margin-bottom: 0.3rem;
        }

        .card-header-text p {
            font-size: 0.85rem;
            color: var(--dark-300);
            line-height: 1.65;
            font-weight: 300;
        }

        /* ─── ALERTS ─────────────────────────────────────────────────────── */
        .alert-success {
            background: rgba(74, 222, 128, 0.08);
            border: 1px solid rgba(74, 222, 128, 0.2);
            color: #86efac;
            padding: 0.875rem 1.1rem;
            border-radius: 10px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.625rem;
            margin-bottom: 1.5rem;
        }

        .alert-success i {
            font-size: 14px;
            color: #4ade80;
            flex-shrink: 0;
        }

        .alert-warning {
            background: rgba(248, 113, 113, 0.08);
            border: 1px solid rgba(248, 113, 113, 0.2);
            color: #fca5a5;
            padding: 0.875rem 1.1rem;
            border-radius: 10px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.625rem;
            margin-bottom: 1rem;
        }

        .alert-warning i {
            font-size: 14px;
            color: #f87171;
            flex-shrink: 0;
        }

        /* ─── FORM ELEMENTS ──────────────────────────────────────────────── */
        .form-group {
            margin-bottom: 1.375rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--dark-100);
            margin-bottom: 0.55rem;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.875rem 1.1rem;
            border: 1.5px solid rgba(255, 255, 255, 0.07);
            border-radius: 10px;
            font-family: var(--font-body);
            font-size: 0.9rem;
            color: var(--white);
            background: rgba(255, 255, 255, 0.035);
            backdrop-filter: blur(8px);
            transition: all 0.3s var(--ease-smooth);
            outline: none;
            appearance: none;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--dark-400);
        }

        .form-group input:hover,
        .form-group select:hover {
            border-color: rgba(255, 255, 255, 0.12);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
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

        /* ─── BUTTONS ────────────────────────────────────────────────────── */
        .btn {
            padding: 0.8rem 1.6rem;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            transition: all 0.3s var(--ease-smooth);
            border: none;
            cursor: pointer;
            font-family: var(--font-body);
            letter-spacing: 0.2px;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--orange-600), var(--orange-500));
            color: #fff;
            box-shadow: 0 4px 20px rgba(249, 115, 22, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(249, 115, 22, 0.5);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #fff;
            box-shadow: 0 4px 20px rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(239, 68, 68, 0.45);
        }

        .btn-ghost {
            background: rgba(255, 255, 255, 0.05);
            color: var(--dark-200);
            border: 1.5px solid rgba(255, 255, 255, 0.08);
            width: auto;
        }

        .btn-ghost:hover {
            border-color: rgba(255, 255, 255, 0.18);
            background: rgba(255, 255, 255, 0.1);
            color: var(--white);
        }

        /* ─── DANGER ZONE ────────────────────────────────────────────────── */
        .danger-warn {
            background: rgba(239, 68, 68, 0.06);
            border: 1px solid rgba(239, 68, 68, 0.14);
            border-radius: 10px;
            padding: 1rem 1.1rem;
            margin-bottom: 1.75rem;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .danger-warn i {
            font-size: 14px;
            color: #f87171;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .danger-warn p {
            font-size: 0.825rem;
            color: #fca5a5;
            line-height: 1.65;
            font-weight: 300;
        }

        /* ─── UNVERIFIED EMAIL NOTICE ────────────────────────────────────── */
        .verify-notice {
            background: rgba(251, 191, 36, 0.08);
            border: 1px solid rgba(251, 191, 36, 0.2);
            border-radius: 10px;
            padding: 0.875rem 1.1rem;
            margin-bottom: 1.375rem;
            font-size: 0.85rem;
            color: var(--amber-300);
            display: flex;
            align-items: center;
            gap: 0.625rem;
            flex-wrap: wrap;
        }

        .verify-notice i {
            color: var(--amber-400);
            font-size: 13px;
            flex-shrink: 0;
        }

        .verify-notice button {
            background: none;
            border: none;
            color: var(--orange-400);
            cursor: pointer;
            font-family: var(--font-body);
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .verify-notice button:hover {
            color: var(--orange-300);
        }

        /* ─── MODAL ──────────────────────────────────────────────────────── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(6px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-overlay.show {
            display: flex;
            animation: fadeIn 0.2s ease;
        }

        .modal-box {
            background: linear-gradient(135deg, rgba(17, 17, 17, 0.98), rgba(26, 26, 26, 0.98));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            max-width: 440px;
            width: 100%;
            box-shadow: 0 32px 80px rgba(0, 0, 0, 0.6);
            animation: slideUp 0.3s var(--ease-smooth) both;
            overflow: hidden;
        }

        .modal-top-bar {
            height: 3px;
            background: linear-gradient(90deg, #ef4444, #dc2626);
        }

        .modal-body {
            padding: 2rem 2rem 2.25rem;
        }

        .modal-icon {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            color: #f87171;
            margin-bottom: 1.25rem;
        }

        .modal-body h3 {
            font-family: var(--font-display);
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--white);
            letter-spacing: -0.3px;
            margin-bottom: 0.5rem;
        }

        .modal-body>p {
            color: var(--dark-300);
            font-size: 0.875rem;
            line-height: 1.7;
            font-weight: 300;
            margin-bottom: 1.5rem;
        }

        .modal-actions {
            display: flex;
            gap: 0.875rem;
            margin-top: 1.5rem;
        }

        .modal-actions .btn {
            width: auto;
            flex: 1;
        }

        /* ─── ANIMATIONS ─────────────────────────────────────────────────── */
        @keyframes fade-up {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ─── RESPONSIVE ─────────────────────────────────────────────────── */
        @media (max-width: 900px) {
            .navbar-inner {
                padding: 0 1.5rem;
            }

            .page-wrap {
                padding: calc(var(--nav-h) + 2.5rem) 1.5rem 3.5rem;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {

            .navbar-org,
            .navbar-sub {
                display: none;
            }
        }
    </style>
</head>

<body>

    {{-- ── BACKGROUND LAYERS ─────────────────────────────────────────────── --}}
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-orb-1"></div>
    <div class="hero-orb-2"></div>

    {{-- ── NAVBAR ────────────────────────────────────────────────────────── --}}
    <nav class="navbar">
        <div class="navbar-inner">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
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

            <div class="navbar-right">
                <span class="nav-user-name">{{ Auth::user()->name }}</span>
                <a href="{{ route('admin.dashboard') }}"
                    style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.65rem 1.25rem; background:#f97316; color:white; border:none; border-radius:9px; font-size:0.855rem; font-weight:700; cursor:pointer; font-family:'Instrument Sans',sans-serif; box-shadow:0 2px 10px rgba(249,115,22,0.35); transition:all 0.2s;"
                    onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='#f97316'">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </nav>

    {{-- ── PAGE ──────────────────────────────────────────────────────────── --}}
    <div class="page-wrap">
        <div class="page-inner">

            {{-- Page header --}}
            <div class="page-header">
                <div class="page-eyebrow">Account</div>
                <h1>Profile Settings</h1>
                <p>Manage your account information and security credentials.</p>
            </div>

            <div class="cards-grid">

                {{-- ── CARD 1: Personal Information ─────────────────────── --}}
                <div class="profile-card" id="card-profile">
                    <div class="card-top-bar"></div>
                    <div class="card-body">

                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-user"></i></div>
                            <div class="card-header-text">
                                <h2>Personal Information</h2>
                                <p>Update your name and email address.</p>
                            </div>
                        </div>

                        @if (session('status') === 'profile-updated')
                            <div class="alert-success">
                                <i class="fas fa-circle-check"></i>
                                Profile updated successfully.
                            </div>
                        @endif

                        <form method="post" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')

                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input id="name" type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                                    required autofocus autocomplete="name" placeholder="Your full name">
                                @if ($errors->has('name'))
                                    <div class="error-message">
                                        <i class="fas fa-circle-exclamation"></i>
                                        {{ $errors->first('name') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input id="email" type="email" name="email"
                                    value="{{ old('email', Auth::user()->email) }}" required autocomplete="username"
                                    placeholder="your@email.gov.ph">
                                @if ($errors->has('email'))
                                    <div class="error-message">
                                        <i class="fas fa-circle-exclamation"></i>
                                        {{ $errors->first('email') }}
                                    </div>
                                @endif
                            </div>

                            @if (Auth::user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !Auth::user()->hasVerifiedEmail())
                                <div class="verify-notice">
                                    <i class="fas fa-triangle-exclamation"></i>
                                    <span>Your email address is unverified.</span>
                                    <form action="{{ route('verification.send') }}" method="post" style="display:inline;">
                                        @csrf
                                        <button type="submit">Re-send verification email</button>
                                    </form>
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary" style="margin-top:.25rem;">
                                <i class="fas fa-floppy-disk"></i> Save Changes
                            </button>
                        </form>

                    </div>
                </div>

                {{-- ── CARD 2: Change Password ───────────────────────────── --}}
                <div class="profile-card" id="card-password">
                    <div class="card-top-bar"></div>
                    <div class="card-body">

                        <div class="card-header">
                            <div class="card-icon"><i class="fas fa-lock"></i></div>
                            <div class="card-header-text">
                                <h2>Change Password</h2>
                                <p>Update your password to keep your account secure.</p>
                            </div>
                        </div>

                        @if (session('status') === 'password-updated')
                            <div class="alert-success">
                                <i class="fas fa-circle-check"></i>
                                Password updated successfully.
                            </div>
                        @endif

                        <form method="post" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')

                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input id="current_password" type="password" name="current_password"
                                    autocomplete="current-password" placeholder="Enter current password">
                                @if ($errors->updatePassword->has('current_password'))
                                    <div class="error-message">
                                        <i class="fas fa-circle-exclamation"></i>
                                        {{ $errors->updatePassword->first('current_password') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="password">New Password</label>
                                <input id="password" type="password" name="password" autocomplete="new-password"
                                    placeholder="Minimum 8 characters">
                                @if ($errors->updatePassword->has('password'))
                                    <div class="error-message">
                                        <i class="fas fa-circle-exclamation"></i>
                                        {{ $errors->updatePassword->first('password') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                    autocomplete="new-password" placeholder="Re-enter new password">
                                @if ($errors->updatePassword->has('password_confirmation'))
                                    <div class="error-message">
                                        <i class="fas fa-circle-exclamation"></i>
                                        {{ $errors->updatePassword->first('password_confirmation') }}
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary" style="margin-top:.25rem;">
                                <i class="fas fa-shield-halved"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>{{-- /.cards-grid --}}
        </div>{{-- /.page-inner --}}
    </div>{{-- /.page-wrap --}}

    {{-- ── DELETE ACCOUNT MODAL ──────────────────────────────────────────── --}}
    <div id="deleteModal" class="modal-overlay" onclick="if(event.target===this)this.classList.remove('show')">
        <div class="modal-box">
            <div class="modal-top-bar"></div>
            <div class="modal-body">

                <div class="modal-icon"><i class="fas fa-trash-can"></i></div>
                <h3>Delete Account</h3>
                <p>Once your account is deleted, there is no going back. Please be certain before continuing.</p>

                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')

                    <div class="form-group">
                        <label for="modal_password">Confirm your password</label>
                        <input id="modal_password" type="password" name="password"
                            placeholder="Enter your password to confirm" required autocomplete="current-password">
                        @if ($errors->userDeletion->has('password'))
                            <div class="error-message">
                                <i class="fas fa-circle-exclamation"></i>
                                {{ $errors->userDeletion->first('password') }}
                            </div>
                        @endif
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        // Staggered card entrance — same pattern as welcome page reveal system
        document.querySelectorAll('.profile-card').forEach((card, i) => {
            setTimeout(() => card.classList.add('visible'), 150 + i * 120);
        });

        // Re-open delete modal if validation failed (userDeletion errors present)
        @if ($errors->userDeletion->isNotEmpty())
            document.getElementById('deleteModal').classList.add('show');
        @endif
    </script>

</body>

</html>