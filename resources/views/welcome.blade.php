<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Provincial Engineering Office — Project Monitoring & Management System">
    <title>{{ config('app.name', 'PEO Project Monitoring') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">



    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/css/welcome.css', 'resources/js/app.js', 'resources/js/welcome.js'])
    @endif
</head>
<body>

<!-- ═══════════════════════ NAVBAR ════════════════════════════════════ -->
<nav class="navbar" id="mainNav">
    <div class="navbar-inner">
        <a class="navbar-brand" href="/">
            <div class="navbar-logo">
                <div class="navbar-logo-ring"></div>
                <div class="navbar-logo-inner">
                    @php $logoPath = public_path('assets/app_logo.PNG'); @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ asset('assets/app_logo.PNG') }}" alt="PEO Logo">
                    @else
                        <i class="fas fa-building-columns" style="color:var(--orange-400);font-size:18px;"></i>
                    @endif
                </div>
            </div>
            <div class="navbar-text-group">
                <div class="navbar-org">Provincial Engineering Office</div>
                <div class="navbar-sub">Bukidnon · Project Monitoring System</div>
            </div>
        </a>

        <div class="navbar-center">
            <a href="#features"  class="nav-link">Features</a>
            <a href="#overview"  class="nav-link">Overview</a>
            <a href="#stats"     class="nav-link">Statistics</a>
        </div>

        <div class="navbar-right">
            @auth
                @php
                    $role = Auth::user()->role ?? 'user';
                    $dashRoute = $role === 'admin' ? 'admin.dashboard' : 'user.dashboard';
                @endphp
                <a href="{{ route($dashRoute) }}" class="btn-nav-primary">
                    <i class="fas fa-gauge-high"></i> Dashboard
                </a>
            @else
                @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn-nav-ghost">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </a>
                    <a href="{{ route('login') }}" class="btn-nav-primary">
                        <i class="fas fa-arrow-right-to-bracket"></i> Access System
                    </a>
                @endif
            @endauth
        </div>
    </div>
</nav>


<!-- ═══════════════════════ HERO ══════════════════════════════════════ -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="hero-orb-1"></div>
    <div class="hero-orb-2"></div>
    <div class="hero-accent-line"></div>

    <div class="hero-inner">
        <div class="hero-content">
            <div class="hero-eyebrow">
                <div class="eyebrow-badge">
                    <div class="eyebrow-pulse"></div>
                    Live System · {{ now()->format('Y') }}
                </div>
                <span class="eyebrow-year">FY {{ now()->year }}–{{ now()->addYear()->year }}</span>
            </div>

            <h1 class="hero-h1">
                <span class="h1-line-1">Bukidnon</span>
                <span class="h1-line-2"><span class="h1-gradient">Infrastructure</span></span>
                <span class="h1-line-1">Command Center</span>
            </h1>

            <p class="hero-lead">
                Centralized monitoring for <strong>infrastructure contracts</strong>, time extensions, liquidated damages, and billing across all district engineering offices of Bukidnon Province.
            </p>

            <div class="hero-actions">
                @auth
                    <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'user.dashboard') }}" class="btn-cta-primary">
                        <i class="fas fa-gauge-high"></i> Open Dashboard
                    </a>
                    @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.reports.index') }}" class="btn-cta-ghost">
                        <i class="fas fa-file-pdf"></i> View Reports
                    </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn-cta-primary">
                        <i class="fas fa-arrow-right-to-bracket"></i> Sign In to System
                    </a>
                    <a href="#features" class="btn-cta-ghost">
                        <i class="fas fa-circle-info"></i> Explore Features
                    </a>
                @endauth
            </div>

            <div class="hero-trust">
                <div class="trust-item">
                    <i class="fas fa-shield-halved"></i>
                    <span>Secure Access</span>
                </div>
                <div class="trust-divider"></div>
                <div class="trust-item">
                    <i class="fas fa-clock-rotate-left"></i>
                    <span>Real-Time Data</span>
                </div>
                <div class="trust-divider"></div>
                <div class="trust-item">
                    <i class="fas fa-building-columns"></i>
                    <span>Government Grade</span>
                </div>
            </div>
        </div>

        <!-- KPI Panel -->
        @php
            $sTotal     = \App\Models\Project::count();
            $sOngoing   = \App\Models\Project::where('status','ongoing')->count();
            $sExpiring  = \App\Models\Project::where('status','ongoing')
                ->where(function($q){ $q->whereNull('revised_contract_expiry')->whereBetween('original_contract_expiry',[now(),now()->addDays(30)])->orWhereBetween('revised_contract_expiry',[now(),now()->addDays(30)]); })
                ->count();
            $sExpired   = \App\Models\Project::where(function($q){ $q->where('status','expired')->orWhere(function($x){ $x->where('status','!=','completed')->where(function($y){ $y->whereNull('revised_contract_expiry')->where('original_contract_expiry','<',now())->orWhere('revised_contract_expiry','<',now()); }); }); })->count();
            $sCompleted = \App\Models\Project::where('status','completed')->count();
            $completionRate = $sTotal > 0 ? round(($sCompleted / $sTotal) * 100, 1) : 0;
        @endphp

        <div class="kpi-panel">
            <div class="kpi-top-bar"></div>
            <div class="kpi-body">
                <div class="kpi-header-row">
                    <div class="kpi-header-left">
                        <div class="kpi-icon-wrap">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <div class="kpi-panel-title">Project Overview</div>
                            <div class="kpi-panel-sub">All districts · Real-time</div>
                        </div>
                    </div>
                    <div class="kpi-live-dot">
                        <div class="live-pulse"></div>
                        Live
                    </div>
                </div>

                <div class="kpi-cards-grid">
                    <div class="kpi-stat-card">
                        <div class="kpi-stat-num" data-count="{{ $sTotal }}">{{ $sTotal }}</div>
                        <div class="kpi-stat-label">Total Projects</div>
                        <div class="kpi-badge badge-green">
                            <i class="fas fa-arrow-up" style="font-size:8px;"></i> All districts
                        </div>
                    </div>
                    <div class="kpi-stat-card">
                        <div class="kpi-stat-num" data-count="{{ $sOngoing }}">{{ $sOngoing }}</div>
                        <div class="kpi-stat-label">Active / Ongoing</div>
                        <div class="kpi-badge badge-green">
                            <i class="fas fa-circle-dot" style="font-size:8px;"></i> In progress
                        </div>
                    </div>
                    <div class="kpi-stat-card">
                        <div class="kpi-stat-num" data-count="{{ $sExpired }}">{{ $sExpired }}</div>
                        <div class="kpi-stat-label">Expired Contracts</div>
                        <div class="kpi-badge {{ $sExpired > 0 ? 'badge-red' : 'badge-green' }}">
                            @if($sExpired > 0)
                                <i class="fas fa-triangle-exclamation" style="font-size:8px;"></i> Needs review
                            @else
                                <i class="fas fa-check" style="font-size:8px;"></i> All clear
                            @endif
                        </div>
                    </div>
                    <div class="kpi-stat-card">
                        <div class="kpi-stat-num" data-count="{{ $sExpiring }}">{{ $sExpiring }}</div>
                        <div class="kpi-stat-label">Expiring (30 days)</div>
                        <div class="kpi-badge {{ $sExpiring > 0 ? 'badge-amber' : 'badge-green' }}">
                            @if($sExpiring > 0)
                                <i class="fas fa-clock" style="font-size:8px;"></i> Attention needed
                            @else
                                <i class="fas fa-check" style="font-size:8px;"></i> Clear
                            @endif
                        </div>
                    </div>
                </div>

                <div class="kpi-progress-section">
                    <div class="kpi-progress-header">
                        <span class="kpi-progress-label">Overall Completion Rate</span>
                        <span class="kpi-progress-pct">{{ $completionRate }}%</span>
                    </div>
                    <div class="progress-track">
                        <div class="progress-fill-animated" id="heroProgressBar" style="width: 0%;"></div>
                    </div>
                </div>

                <div class="kpi-footer-row">
                    <span class="kpi-timestamp">
                        <i class="fas fa-circle-dot" style="color:#4ade80;font-size:8px;margin-right:4px;"></i>
                        Updated {{ now()->format('M d · h:i A') }}
                    </span>
                    <div class="kpi-refresh-btn" onclick="this.querySelector('i').style.transform='rotate(360deg)';this.querySelector('i').style.transition='0.5s';">
                        <i class="fas fa-rotate"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════ FEATURES ══════════════════════════════════ -->
<section class="features-section" id="features">
    <div class="section-wrap">
        <div class="features-header">
            <div>
                <div class="section-tag reveal">Core Capabilities</div>
                <h2 class="section-h2 reveal reveal-delay-1">
                    Built for Government<br>Engineering Excellence
                </h2>
            </div>
            <p class="section-lead reveal reveal-delay-2">
                Purpose-built tools for the Provincial Engineering Office — from contract lifecycle management to automated compliance monitoring across all Bukidnon districts.
            </p>
        </div>

        <div class="features-grid">
            @php
                $features = [
                    ['icon'=>'fas fa-chart-line','title'=>'Real-Time Monitoring','desc'=>'Live dashboards tracking project milestones, contract timelines, and compliance status across all engineering districts with instant alerts.','num'=>'01'],
                    ['icon'=>'fas fa-file-contract','title'=>'Contract Management','desc'=>'Centralized repository for all infrastructure contracts with automated expiry detection, renewal workflows, and full document audit trails.','num'=>'02'],
                    ['icon'=>'fas fa-calculator','title'=>'Liquidated Damages','desc'=>'Automated LD computation with configurable penalty structures, billing adjustments, and comprehensive financial audit history.','num'=>'03'],
                    ['icon'=>'fas fa-sitemap','title'=>'Multi-District Control','desc'=>'Seamless coordination across all Bukidnon engineering districts with granular role-based access, delegated views, and district-level analytics.','num'=>'04'],
                    ['icon'=>'fas fa-file-pdf','title'=>'Enterprise Reporting','desc'=>'Generate audit-ready PDF reports with custom metrics, visual graphs, status summaries, and presentation-quality layouts for stakeholders.','num'=>'05'],
                    ['icon'=>'fas fa-shield-halved','title'=>'Security & Compliance','desc'=>'Role-based access control, encrypted data storage, tamper-evident audit logs, and full compliance with government data standards.','num'=>'06'],
                ];
            @endphp

            @foreach($features as $i => $f)
            <div class="feature-card reveal reveal-delay-{{ min($i+1, 5) }}">
                <div class="feature-top-accent"></div>
                <div class="feature-card-num">{{ $f['num'] }}</div>
                <div class="feature-icon-wrap">
                    <i class="{{ $f['icon'] }}"></i>
                </div>
                <h3>{{ $f['title'] }}</h3>
                <p>{{ $f['desc'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>


<!-- ═══════════════════════ STATS STRIP ═══════════════════════════════ -->
<section class="stats-strip" id="stats">
    <div class="stats-strip-bg"></div>
    <div class="stats-strip-pattern"></div>
    <div class="stats-strip-glow"></div>
    <div class="stats-inner">
        <div class="stat-block reveal">
            <div class="stat-block-num"><span id="count-total">0</span><span>+</span></div>
            <div class="stat-block-label">Projects Monitored</div>
            <div class="stat-block-sublabel">Across all districts</div>
        </div>
        <div class="stat-block reveal reveal-delay-1">
            <div class="stat-block-num"><span id="count-ongoing">0</span><span>+</span></div>
            <div class="stat-block-label">Active Projects</div>
            <div class="stat-block-sublabel">Currently in execution</div>
        </div>
        <div class="stat-block reveal reveal-delay-2">
            <div class="stat-block-num"><span id="count-completed">0</span><span>+</span></div>
            <div class="stat-block-label">Completed</div>
            <div class="stat-block-sublabel">Successfully delivered</div>
        </div>
        <div class="stat-block reveal reveal-delay-3">
            <div class="stat-block-num">100<span>%</span></div>
            <div class="stat-block-label">Data Integrity</div>
            <div class="stat-block-sublabel">Verified records</div>
        </div>
    </div>
</section>


<!-- ═══════════════════════ SYSTEM OVERVIEW ═══════════════════════════ -->
<section class="preview-section" id="overview">
    <div class="section-wrap">
        <div class="preview-layout">
            <div class="preview-content">
                <div class="section-tag reveal">System Overview</div>
                <h2 class="section-h2 reveal reveal-delay-1">
                    Everything You Need<br>In One Platform
                </h2>
                <p class="section-lead reveal reveal-delay-2" style="margin-top:0.875rem;">
                    From contract intake to project closeout, the PEO system provides a single source of truth for all infrastructure project data.
                </p>

                <div class="preview-points">
                    @php
                        $points = [
                            ['icon'=>'fas fa-bell','title'=>'Smart Deadline Alerts','desc'=>'Automated notifications for expiring contracts, pending extensions, and critical milestones.'],
                            ['icon'=>'fas fa-table-list','title'=>'Comprehensive Audit Trails','desc'=>'Full change history with timestamps, user attribution, and approval documentation.'],
                            ['icon'=>'fas fa-chart-bar','title'=>'Performance Analytics','desc'=>'District-level and project-level analytics for informed decision making and reporting.'],
                            ['icon'=>'fas fa-users','title'=>'Role-Based Access','desc'=>'Granular permissions for administrators, engineers, and district-level users.'],
                        ];
                    @endphp
                    @foreach($points as $i => $p)
                    <div class="preview-point reveal reveal-delay-{{ $i+1 }}">
                        <div class="preview-point-icon">
                            <i class="{{ $p['icon'] }}"></i>
                        </div>
                        <div class="preview-point-text">
                            <strong>{{ $p['title'] }}</strong>
                            <span>{{ $p['desc'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Dashboard Mockup -->
            <div class="dashboard-mockup reveal reveal-delay-2">
                <div class="mock-titlebar">
                    <div class="mock-dot red"></div>
                    <div class="mock-dot yellow"></div>
                    <div class="mock-dot green"></div>
                    <div class="mock-url-bar">peo.bukidnon.gov.ph/dashboard</div>
                </div>
                <div class="mock-body">
                    <div class="mock-sidebar">
                        <div class="mock-sidebar-logo">
                            <i class="fas fa-building-columns"></i> PEO
                        </div>
                        <div class="mock-nav-item active">
                            <i class="fas fa-gauge-high"></i> Dashboard
                        </div>
                        <div class="mock-nav-item">
                            <i class="fas fa-folder-open"></i> Projects
                        </div>
                        <div class="mock-nav-item">
                            <i class="fas fa-file-contract"></i> Contracts
                        </div>
                        <div class="mock-nav-item">
                            <i class="fas fa-calculator"></i> LD Calculator
                        </div>
                        <div class="mock-nav-item">
                            <i class="fas fa-file-pdf"></i> Reports
                        </div>
                        <div class="mock-nav-item">
                            <i class="fas fa-users-cog"></i> Settings
                        </div>
                    </div>
                    <div class="mock-main">
                        <div class="mock-stats-row">
                            <div class="mock-stat">
                                <div class="mock-stat-n" style="color:var(--orange-400);">{{ $sTotal }}</div>
                                <div class="mock-stat-l">Total</div>
                            </div>
                            <div class="mock-stat">
                                <div class="mock-stat-n" style="color:#4ade80;">{{ $sOngoing }}</div>
                                <div class="mock-stat-l">Active</div>
                            </div>
                            <div class="mock-stat">
                                <div class="mock-stat-n" style="color:var(--amber-400);">{{ $sCompleted }}</div>
                                <div class="mock-stat-l">Done</div>
                            </div>
                        </div>
                        <div class="mock-chart-area">
                            <div class="mock-bar"></div>
                            <div class="mock-bar"></div>
                            <div class="mock-bar"></div>
                            <div class="mock-bar"></div>
                            <div class="mock-bar"></div>
                            <div class="mock-bar"></div>
                            <div class="mock-bar"></div>
                            <div class="mock-bar"></div>
                        </div>
                        <div class="mock-table-rows">
                            <div class="mock-row">
                                <span class="mock-row-text" style="font-weight:600;color:var(--dark-100);">Project Name</span>
                                <span class="mock-row-text">Status</span>
                                <span class="mock-row-text">Progress</span>
                            </div>
                            <div class="mock-row">
                                <span class="mock-row-text">Road Widening – D1</span>
                                <span class="mock-row-badge active">Active</span>
                                <div class="mock-mini-bar-wrap"><div class="mock-mini-bar" style="width:72%;"></div></div>
                            </div>
                            <div class="mock-row">
                                <span class="mock-row-text">Bridge Construction – D3</span>
                                <span class="mock-row-badge expiring">Expiring</span>
                                <div class="mock-mini-bar-wrap"><div class="mock-mini-bar" style="width:88%;background:var(--amber-400);"></div></div>
                            </div>
                            <div class="mock-row">
                                <span class="mock-row-text">Flood Control – D2</span>
                                <span class="mock-row-badge expired">Expired</span>
                                <div class="mock-mini-bar-wrap"><div class="mock-mini-bar" style="width:45%;background:#f87171;"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════ CTA ════════════════════════════════════════ -->
<section class="cta-section">
    <div class="cta-glow"></div>
    <div class="cta-inner">
        <div class="cta-box">
            <div class="section-tag reveal" style="justify-content:center;">Get Started</div>
            <h2 class="reveal reveal-delay-1">
                Ready to Modernize<br><span class="h1-gradient">Project Oversight?</span>
            </h2>
            <p class="reveal reveal-delay-2">
                Join the Provincial Engineering Office's next-generation infrastructure monitoring platform — built for transparency, accountability, and government-grade performance.
            </p>
            <div class="cta-actions reveal reveal-delay-3">
                @auth
                    <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'user.dashboard') }}" class="btn-cta-primary">
                        <i class="fas fa-arrow-right"></i> Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-cta-primary">
                        <i class="fas fa-sign-in-alt"></i> Sign In Now
                    </a>
                    <a href="mailto:support@peo.gov.ph" class="btn-cta-ghost">
                        <i class="fas fa-envelope"></i> Contact Support
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════ FOOTER ════════════════════════════════════ -->
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-top">
            <div>
                <a class="footer-brand-logo" href="/">
                    <div class="footer-brand-icon">
                        @if(file_exists(public_path('assets/app_logo.PNG')))
                            <img src="{{ asset('assets/app_logo.PNG') }}" alt="PEO">
                        @else
                            <i class="fas fa-building-columns"></i>
                        @endif
                    </div>
                    <span class="footer-brand-name">PEO Bukidnon</span>
                </a>
                <p class="footer-col-desc">
                    The Provincial Engineering Office of Bukidnon delivers excellence in infrastructure management through technology-driven transparency and accountability.
                </p>
                <div class="footer-social-row">
                    <a href="#" class="footer-social-btn"><i class="fab fa-facebook-f"></i></a>
                    <a href="mailto:info@peo.gov.ph" class="footer-social-btn"><i class="fas fa-envelope"></i></a>
                    <a href="#" class="footer-social-btn"><i class="fas fa-globe"></i></a>
                </div>
            </div>

            <div>
                <div class="footer-col-title">Navigation</div>
                <div class="footer-links-list">
                    <a href="/" class="footer-link"><i class="fas fa-chevron-right" style="font-size:9px;"></i> Home</a>
                    <a href="#features" class="footer-link"><i class="fas fa-chevron-right" style="font-size:9px;"></i> Features</a>
                    <a href="#overview" class="footer-link"><i class="fas fa-chevron-right" style="font-size:9px;"></i> System Overview</a>
                    @auth
                    <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.dashboard' : 'user.dashboard') }}" class="footer-link">
                        <i class="fas fa-chevron-right" style="font-size:9px;"></i> Dashboard
                    </a>
                    @endauth
                </div>
            </div>

            <div>
                <div class="footer-col-title">Support</div>
                <div class="footer-links-list">
                    <a href="mailto:support@peo.gov.ph" class="footer-link"><i class="fas fa-chevron-right" style="font-size:9px;"></i> Technical Support</a>
                    <a href="mailto:info@peo.gov.ph"    class="footer-link"><i class="fas fa-chevron-right" style="font-size:9px;"></i> General Inquiries</a>
                    <a href="#"                          class="footer-link"><i class="fas fa-chevron-right" style="font-size:9px;"></i> Documentation</a>
                    <a href="#"                          class="footer-link"><i class="fas fa-chevron-right" style="font-size:9px;"></i> Accessibility</a>
                </div>
            </div>

            <div>
                <div class="footer-col-title">System Status</div>
                <div class="footer-status-card">
                    <div class="footer-status-row">
                        <div class="footer-status-dot-row">
                            <div class="status-dot-green"></div>
                            All Systems Operational
                        </div>
                        <div class="footer-status-badge">Live</div>
                    </div>
                    <div class="footer-status-items">
                        <div class="footer-status-item">
                            <span>API Services</span>
                            <span style="display:flex;align-items:center;gap:.4rem;"><div class="footer-status-item-dot"></div><span style="color:#4ade80;font-size:10px;">Online</span></span>
                        </div>
                        <div class="footer-status-item">
                            <span>Database</span>
                            <span style="display:flex;align-items:center;gap:.4rem;"><div class="footer-status-item-dot"></div><span style="color:#4ade80;font-size:10px;">Online</span></span>
                        </div>
                        <div class="footer-status-item">
                            <span>Auth System</span>
                            <span style="display:flex;align-items:center;gap:.4rem;"><div class="footer-status-item-dot"></div><span style="color:#4ade80;font-size:10px;">Online</span></span>
                        </div>
                    </div>
                    <div style="margin-top:1rem;padding-top:0.75rem;border-top:1px solid rgba(255,255,255,0.05);font-size:11px;color:var(--dark-400);">
                        Last checked: {{ now()->format('M d, Y · h:i A') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div>&copy; {{ now()->year }} Provincial Engineering Office — Bukidnon. All rights reserved.</div>
            <div class="footer-bottom-links">
                <a href="#" class="footer-bottom-link">Privacy Policy</a>
                <a href="#" class="footer-bottom-link">Terms of Use</a>
                <a href="#" class="footer-bottom-link">Accessibility</a>
            </div>
        </div>
    </div>
</footer>


@push('scripts')
<script>
    window.statsData = {
        total: {{ $sTotal }},
        ongoing: {{ $sOngoing }},
        completed: {{ $sCompleted }},
        completionRate: {{ $completionRate }}
    };
</script>
@vite(['resources/js/welcome.js'])
@endpush

</body>
</html>