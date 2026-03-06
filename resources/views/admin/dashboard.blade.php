<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p style="font-size:0.68rem; font-weight:700; letter-spacing:0.12em; text-transform:uppercase; color:#9a6030; margin-bottom:0.3rem;">
                    Administration
                </p>
                <h2 style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.75rem; letter-spacing:-0.03em; color:#1a0f00; display:flex; align-items:center; gap:0.65rem; line-height:1.1;">
                    <span style="background:linear-gradient(135deg,#f97316,#ea580c); width:36px; height:36px; border-radius:10px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 4px 12px rgba(249,115,22,0.4); flex-shrink:0;">
                        <i class="fas fa-chart-pie" style="color:white; font-size:0.8rem;"></i>
                    </span>
                    Admin Dashboard
                </h2>
            </div>
            <div style="display:flex; align-items:center; gap:0.65rem; background:white; border:1px solid rgba(249,115,22,0.15); border-radius:10px; padding:0.5rem 0.9rem;">
                <div style="width:8px; height:8px; background:#22c55e; border-radius:50%; animation:pulse-dot 2s infinite; flex-shrink:0;"></div>
                <span style="font-size:0.8rem; font-weight:600; color:#6b4f35; font-family:'Instrument Sans',sans-serif;">
                    {{ now()->format('l, F j, Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <style>
        * { font-family: 'Instrument Sans', sans-serif; }

        :root {
            --o50:  #fff7ed;
            --o100: #ffedd5;
            --o500: #f97316;
            --o600: #ea580c;
            --ink:  #1a0f00;
            --muted:#6b4f35;
            --border: rgba(249,115,22,0.12);
        }

        @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1);} 50%{opacity:0.5;transform:scale(0.85);} }
        @keyframes fadeUp { from{opacity:0;transform:translateY(16px);} to{opacity:1;transform:translateY(0);} }
        @keyframes countUp { from{opacity:0;transform:translateY(8px);} to{opacity:1;transform:translateY(0);} }
        @keyframes barGrow { from{width:0;} to{width:var(--target-width);} }
        @keyframes shimmer { 0%{background-position:-200% center;} 100%{background-position:200% center;} }
        @keyframes spin-slow { from{transform:rotate(0deg);} to{transform:rotate(360deg);} }

        .dash-section { animation: fadeUp 0.45s ease both; }
        .dash-section:nth-child(1) { animation-delay: 0s; }
        .dash-section:nth-child(2) { animation-delay: 0.08s; }
        .dash-section:nth-child(3) { animation-delay: 0.16s; }
        .dash-section:nth-child(4) { animation-delay: 0.24s; }

        /* Metric cards */
        .metric-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.4rem 1.5rem;
            position: relative;
            overflow: hidden;
            cursor: default;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .metric-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(249,115,22,0.03) 0%, transparent 60%);
            opacity: 0;
            transition: opacity 0.3s;
        }
        .metric-card:hover { transform: translateY(-5px); box-shadow: 0 16px 40px rgba(249,115,22,0.12), 0 4px 12px rgba(0,0,0,0.06); border-color: rgba(249,115,22,0.28); }
        .metric-card:hover::before { opacity: 1; }
        .metric-card:hover .metric-icon { transform: scale(1.12) rotate(-4deg); }
        .metric-card:hover .metric-bar-fill { filter: brightness(1.08); }

        .metric-icon {
            width: 46px; height: 46px; border-radius: 13px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; flex-shrink: 0;
            transition: transform 0.3s cubic-bezier(0.34,1.56,0.64,1);
        }
        .metric-val {
            font-family: 'Syne', sans-serif;
            font-size: 2.6rem; font-weight: 800;
            color: var(--ink); line-height: 1;
            letter-spacing: -0.04em;
            animation: countUp 0.5s ease both;
        }
        .metric-label {
            font-size: 0.68rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em;
            color: var(--muted); margin-bottom: 0.6rem;
        }
        .metric-sub { font-size: 0.73rem; color: #9ca3af; margin-top: 0.35rem; }
        .metric-bar {
            height: 4px; background: rgba(249,115,22,0.08);
            border-radius: 99px; margin-top: 1.1rem; overflow: hidden;
        }
        .metric-bar-fill {
            height: 100%; border-radius: 99px;
            animation: barGrow 1s cubic-bezier(0.22,1,0.36,1) both;
            animation-delay: 0.4s;
            width: var(--target-width);
        }

        /* Trend badges */
        .trend-badge {
            display: inline-flex; align-items: center; gap: 0.25rem;
            padding: 2px 7px; border-radius: 99px;
            font-size: 0.65rem; font-weight: 700;
        }

        /* Welcome banner */
        .welcome-banner {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 50%, #fff7ed 100%);
            background-size: 200% 100%;
            border: 1px solid rgba(249,115,22,0.2);
            border-radius: 16px;
            padding: 1.35rem 1.6rem;
            position: relative;
            overflow: hidden;
            animation: shimmer 4s ease infinite;
        }
        .welcome-banner::after {
            content: '';
            position: absolute;
            right: -30px; top: -30px;
            width: 120px; height: 120px;
            background: radial-gradient(circle, rgba(249,115,22,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Action cards */
        .action-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem;
            text-decoration: none;
            display: flex; align-items: center; gap: 1rem;
            transition: all 0.22s ease;
            position: relative; overflow: hidden;
        }
        .action-card::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent, rgba(249,115,22,0.04));
            opacity: 0; transition: opacity 0.2s;
        }
        .action-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(249,115,22,0.15); border-color: rgba(249,115,22,0.3); }
        .action-card:hover::after { opacity: 1; }
        .action-card:hover .action-arrow { transform: translateX(4px); opacity: 1; }
        .action-card:active { transform: translateY(-1px); }

        .action-icon-wrap {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; font-size: 1rem;
            transition: transform 0.25s cubic-bezier(0.34,1.56,0.64,1);
        }
        .action-card:hover .action-icon-wrap { transform: scale(1.1) rotate(-5deg); }

        .action-arrow {
            margin-left: auto; font-size: 0.7rem;
            color: #c4956a; opacity: 0;
            transition: all 0.2s ease; flex-shrink: 0;
        }

        /* Status panel */
        .status-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.65rem 0.8rem;
            border-radius: 10px;
            background: rgba(249,115,22,0.03);
            border: 1px solid rgba(249,115,22,0.08);
            transition: background 0.2s, border-color 0.2s;
            cursor: default;
        }
        .status-row:hover { background: rgba(249,115,22,0.07); border-color: rgba(249,115,22,0.15); }

        /* Progress ring */
        .ring-wrap { position:relative; display:inline-flex; align-items:center; justify-content:center; }
        .ring-wrap svg { transform: rotate(-90deg); }
        .ring-track { fill: none; stroke: rgba(249,115,22,0.1); }
        .ring-fill {
            fill: none;
            stroke-linecap: round;
            transition: stroke-dashoffset 1.2s cubic-bezier(0.22,1,0.36,1);
        }
        .ring-label {
            position: absolute;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            font-size: 0.8rem;
            color: var(--ink);
            text-align: center;
            line-height: 1.1;
        }

        /* Section headers */
        .section-head {
            font-family: 'Syne', sans-serif;
            font-weight: 700; font-size: 0.9rem;
            color: var(--ink);
            display: flex; align-items: center; gap: 0.5rem;
            margin-bottom: 1rem; letter-spacing: -0.01em;
        }

        /* Recent projects mini table */
        .recent-row {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.7rem 0;
            border-bottom: 1px solid rgba(249,115,22,0.06);
            transition: background 0.15s;
        }
        .recent-row:last-child { border-bottom: none; }
        .recent-row:hover { background: rgba(249,115,22,0.025); border-radius: 8px; padding-left: 0.4rem; }

        /* Panel card */
        .panel {
            background: white;
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
        }
        .panel-head {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(249,115,22,0.08);
            background: #fffaf5;
        }
    </style>

    @php
        $totalProjects  = \App\Models\Project::count();
        $activeCount    = \App\Models\Project::where('status','ongoing')->where(function($q){ $q->whereNull('revised_contract_expiry')->where('original_contract_expiry','>=',now())->orWhere('revised_contract_expiry','>=',now()); })->count();
        $expiringCount  = \App\Models\Project::where('status','!=','completed')->where(function($q){ $q->whereNull('revised_contract_expiry')->whereBetween('original_contract_expiry',[now(),now()->addDays(30)])->orWhereBetween('revised_contract_expiry',[now(),now()->addDays(30)]); })->count();
        $expiredCount   = \App\Models\Project::where(function($q){ $q->whereNull('revised_contract_expiry')->where('original_contract_expiry','<',now())->orWhere('revised_contract_expiry','<',now()); })->where('status','!=','completed')->count();
        $completedCount = \App\Models\Project::where('status','completed')->count();

        $activePercent    = $totalProjects ? round($activeCount / $totalProjects * 100) : 0;
        $completedPercent = $totalProjects ? round($completedCount / $totalProjects * 100) : 0;
        $expiredPercent   = $totalProjects ? round($expiredCount / $totalProjects * 100) : 0;

        $recentProjects = \App\Models\Project::latest()->take(5)->get();
        $avgSlippage    = \App\Models\Project::avg('slippage') ?? 0;
        $aheadCount     = \App\Models\Project::where('slippage','>',0)->count();
        $behindCount    = \App\Models\Project::where('slippage','<',0)->count();
    @endphp

    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        {{-- ── WELCOME ── --}}
        <div class="welcome-banner dash-section">
            <div style="display:flex; align-items:center; gap:1rem; flex-wrap:wrap;">
                <div style="width:42px; height:42px; background:rgba(249,115,22,0.15); border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1px solid rgba(249,115,22,0.2);">
                    <i class="fas fa-shield-alt" style="color:#ea580c; font-size:1rem;"></i>
                </div>
                <div style="flex:1;">
                    <p style="font-family:'Syne',sans-serif; font-weight:800; font-size:1rem; color:#92400e; letter-spacing:-0.01em;">
                        Welcome back, {{ Auth::user()->name }} 👋
                    </p>
                    <p style="color:#b45309; font-size:0.82rem; margin-top:2px;">
                        You have full administrative access.
                        @if($expiringCount > 0)
                            <span style="background:rgba(234,179,8,0.15); color:#92400e; padding:1px 8px; border-radius:99px; font-weight:700; font-size:0.75rem; margin-left:4px;">
                                <i class="fas fa-exclamation-triangle" style="font-size:0.6rem;"></i>
                                {{ $expiringCount }} contract{{ $expiringCount > 1 ? 's' : '' }} expiring soon
                            </span>
                        @endif
                    </p>
                </div>
                <a href="{{ route('admin.projects.index') }}"
                   style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.55rem 1.1rem; background:white; border:1.5px solid rgba(249,115,22,0.25); border-radius:9px; font-size:0.8rem; font-weight:600; color:#ea580c; text-decoration:none; transition:all 0.2s; white-space:nowrap;"
                   onmouseover="this.style.background='#fff7ed';this.style.borderColor='#f97316'"
                   onmouseout="this.style.background='white';this.style.borderColor='rgba(249,115,22,0.25)'">
                    View All <i class="fas fa-arrow-right" style="font-size:0.65rem;"></i>
                </a>
            </div>
        </div>

        {{-- ── METRIC CARDS ── --}}
        <div class="dash-section" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem;">

            {{-- Total --}}
            <div class="metric-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:0.75rem;">
                    <div style="flex:1;">
                        <p class="metric-label">Total Projects</p>
                        <p class="metric-val">{{ $totalProjects }}</p>
                        <p class="metric-sub">All-time registered</p>
                    </div>
                    <div class="metric-icon" style="background:rgba(249,115,22,0.1);">
                        <i class="fas fa-layer-group" style="color:#f97316;"></i>
                    </div>
                </div>
                <div class="metric-bar"><div class="metric-bar-fill" style="background:linear-gradient(90deg,#f97316,#fb923c); --target-width:100%;"></div></div>
            </div>

            {{-- Active --}}
            <div class="metric-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:0.75rem;">
                    <div style="flex:1;">
                        <p class="metric-label">Active Contracts</p>
                        <p class="metric-val">{{ $activeCount }}</p>
                        <p class="metric-sub">{{ $activePercent }}% of total</p>
                    </div>
                    <div class="metric-icon" style="background:rgba(34,197,94,0.1);">
                        <i class="fas fa-circle-check" style="color:#22c55e;"></i>
                    </div>
                </div>
                <div class="metric-bar"><div class="metric-bar-fill" style="background:linear-gradient(90deg,#22c55e,#4ade80); --target-width:{{ $activePercent }}%;"></div></div>
            </div>

            {{-- Expiring --}}
            <div class="metric-card" style="{{ $expiringCount > 0 ? 'border-color:rgba(234,179,8,0.3);' : '' }}">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:0.75rem;">
                    <div style="flex:1;">
                        <p class="metric-label">Expiring Soon</p>
                        <p class="metric-val" style="{{ $expiringCount > 0 ? 'color:#b45309;' : '' }}">{{ $expiringCount }}</p>
                        <p class="metric-sub">Within 30 days</p>
                    </div>
                    <div class="metric-icon" style="background:rgba(234,179,8,0.1);">
                        <i class="fas fa-clock" style="color:#eab308; {{ $expiringCount > 0 ? 'animation:spin-slow 4s linear infinite;' : '' }}"></i>
                    </div>
                </div>
                <div class="metric-bar"><div class="metric-bar-fill" style="background:linear-gradient(90deg,#eab308,#facc15); --target-width:{{ $totalProjects ? min(100, $expiringCount/$totalProjects*100) : 0 }}%;"></div></div>
            </div>

            {{-- Expired --}}
            <div class="metric-card" style="{{ $expiredCount > 0 ? 'border-color:rgba(239,68,68,0.2);' : '' }}">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:0.75rem;">
                    <div style="flex:1;">
                        <p class="metric-label">Expired</p>
                        <p class="metric-val" style="{{ $expiredCount > 0 ? 'color:#dc2626;' : '' }}">{{ $expiredCount }}</p>
                        <p class="metric-sub">Needs attention</p>
                    </div>
                    <div class="metric-icon" style="background:rgba(239,68,68,0.1);">
                        <i class="fas fa-circle-xmark" style="color:#ef4444;"></i>
                    </div>
                </div>
                <div class="metric-bar"><div class="metric-bar-fill" style="background:linear-gradient(90deg,#ef4444,#f87171); --target-width:{{ $expiredPercent }}%;"></div></div>
            </div>

        </div>

        {{-- ── MIDDLE ROW: Quick Actions + Progress Ring + Status ── --}}
        <div class="dash-section" style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">

            {{-- Quick Actions --}}
            <div class="panel">
                <div class="panel-head">
                    <p class="section-head" style="margin-bottom:0;">
                        <i class="fas fa-bolt" style="color:#f97316; font-size:0.75rem;"></i> Quick Actions
                    </p>
                </div>
                <div style="padding:0.875rem; display:flex; flex-direction:column; gap:0.6rem;">

                    <a href="{{ route('admin.projects.create') }}" class="action-card"
                       style="background:linear-gradient(135deg,#fff7ed,white);">
                        <div class="action-icon-wrap" style="background:rgba(249,115,22,0.12);">
                            <i class="fas fa-plus" style="color:#f97316;"></i>
                        </div>
                        <div>
                            <p style="font-weight:700; font-size:0.855rem; color:var(--ink);">New Project</p>
                            <p style="font-size:0.72rem; color:#9ca3af; margin-top:1px;">Register a new entry</p>
                        </div>
                        <i class="fas fa-chevron-right action-arrow"></i>
                    </a>

                    <a href="{{ route('admin.projects.index') }}" class="action-card">
                        <div class="action-icon-wrap" style="background:rgba(59,130,246,0.1);">
                            <i class="fas fa-list-ul" style="color:#3b82f6;"></i>
                        </div>
                        <div>
                            <p style="font-weight:700; font-size:0.855rem; color:var(--ink);">All Projects</p>
                            <p style="font-size:0.72rem; color:#9ca3af; margin-top:1px;">Browse & manage</p>
                        </div>
                        <i class="fas fa-chevron-right action-arrow"></i>
                    </a>

                    <a href="{{ route('admin.projects.index') }}?status=expiring" class="action-card"
                       style="{{ $expiringCount > 0 ? 'border-color:rgba(234,179,8,0.3); background:linear-gradient(135deg,#fffbeb,white);' : '' }}">
                        <div class="action-icon-wrap" style="background:rgba(234,179,8,0.1);">
                            <i class="fas fa-clock" style="color:#eab308;"></i>
                        </div>
                        <div>
                            <p style="font-weight:700; font-size:0.855rem; color:var(--ink);">Expiring Soon</p>
                            <p style="font-size:0.72rem; color:#9ca3af; margin-top:1px;">
                                {{ $expiringCount }} contract{{ $expiringCount != 1 ? 's' : '' }} at risk
                            </p>
                        </div>
                        <i class="fas fa-chevron-right action-arrow"></i>
                    </a>

                    <a href="#" class="action-card">
                        <div class="action-icon-wrap" style="background:rgba(139,92,246,0.1);">
                            <i class="fas fa-file-pdf" style="color:#8b5cf6;"></i>
                        </div>
                        <div>
                            <p style="font-weight:700; font-size:0.855rem; color:var(--ink);">Generate Report</p>
                            <p style="font-size:0.72rem; color:#9ca3af; margin-top:1px;">Export analytics</p>
                        </div>
                        <i class="fas fa-chevron-right action-arrow"></i>
                    </a>

                </div>
            </div>

            {{-- Project Breakdown (donut-style rings) --}}
            <div class="panel" style="display:flex; flex-direction:column;">
                <div class="panel-head">
                    <p class="section-head" style="margin-bottom:0;">
                        <i class="fas fa-chart-donut" style="color:#f97316; font-size:0.75rem;"></i> Project Breakdown
                    </p>
                </div>
                <div style="padding:1.25rem; flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:1.1rem;">

                    {{-- Main ring --}}
                    <div style="position:relative; display:flex; align-items:center; justify-content:center;">
                        <svg width="120" height="120" style="transform:rotate(-90deg);">
                            <circle class="ring-track" cx="60" cy="60" r="50" stroke-width="10"/>
                            {{-- Completed arc --}}
                            <circle class="ring-fill" cx="60" cy="60" r="50" stroke-width="10"
                                stroke="#22c55e"
                                stroke-dasharray="{{ round($completedPercent * 3.14159) }} 314"
                                stroke-dashoffset="0"/>
                            {{-- Active arc --}}
                            <circle class="ring-fill" cx="60" cy="60" r="50" stroke-width="10"
                                stroke="#f97316"
                                stroke-dasharray="{{ round($activePercent * 3.14159) }} 314"
                                stroke-dashoffset="-{{ round($completedPercent * 3.14159) }}"/>
                            {{-- Expired arc --}}
                            <circle class="ring-fill" cx="60" cy="60" r="50" stroke-width="10"
                                stroke="#ef4444"
                                stroke-dasharray="{{ round($expiredPercent * 3.14159) }} 314"
                                stroke-dashoffset="-{{ round(($completedPercent + $activePercent) * 3.14159) }}"/>
                        </svg>
                        <div style="position:absolute; text-align:center;">
                            <p style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.5rem; color:var(--ink); line-height:1;">{{ $totalProjects }}</p>
                            <p style="font-size:0.6rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#9ca3af;">Total</p>
                        </div>
                    </div>

                    {{-- Legend --}}
                    <div style="width:100%; display:flex; flex-direction:column; gap:0.45rem;">
                        @foreach([
                            ['#f97316','Active',$activeCount,$activePercent],
                            ['#22c55e','Completed',$completedCount,$completedPercent],
                            ['#ef4444','Expired',$expiredCount,$expiredPercent],
                        ] as [$color,$label,$count,$pct])
                        <div style="display:flex; align-items:center; gap:0.6rem;">
                            <div style="width:8px; height:8px; border-radius:2px; background:{{ $color }}; flex-shrink:0;"></div>
                            <span style="font-size:0.78rem; color:var(--muted); flex:1;">{{ $label }}</span>
                            <span style="font-family:'Syne',sans-serif; font-weight:700; font-size:0.78rem; color:var(--ink);">{{ $count }}</span>
                            <span style="font-size:0.7rem; color:#9ca3af; min-width:28px; text-align:right;">{{ $pct }}%</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Slippage Health + System Status --}}
            <div style="display:flex; flex-direction:column; gap:1rem;">

                {{-- Slippage Health --}}
                <div class="panel" style="flex:1;">
                    <div class="panel-head">
                        <p class="section-head" style="margin-bottom:0;">
                            <i class="fas fa-wave-square" style="color:#f97316; font-size:0.75rem;"></i> Slippage Health
                        </p>
                    </div>
                    <div style="padding:1rem; display:flex; flex-direction:column; gap:0.5rem;">
                        @php $totalSlip = $aheadCount + $behindCount + (\App\Models\Project::where('slippage',0)->count()); @endphp

                        <div style="display:flex; align-items:center; justify-content:space-between; padding:0.5rem 0.65rem; background:rgba(34,197,94,0.06); border:1px solid rgba(34,197,94,0.15); border-radius:9px;">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <i class="fas fa-arrow-up" style="color:#22c55e; font-size:0.7rem;"></i>
                                <span style="font-size:0.8rem; color:#15803d; font-weight:600;">Ahead</span>
                            </div>
                            <span style="font-family:'Syne',sans-serif; font-weight:800; font-size:1rem; color:#15803d;">{{ $aheadCount }}</span>
                        </div>

                        <div style="display:flex; align-items:center; justify-content:space-between; padding:0.5rem 0.65rem; background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.15); border-radius:9px;">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <i class="fas fa-arrow-down" style="color:#ef4444; font-size:0.7rem;"></i>
                                <span style="font-size:0.8rem; color:#dc2626; font-weight:600;">Behind</span>
                            </div>
                            <span style="font-family:'Syne',sans-serif; font-weight:800; font-size:1rem; color:#dc2626;">{{ $behindCount }}</span>
                        </div>

                        <div style="display:flex; align-items:center; justify-content:space-between; padding:0.5rem 0.65rem; background:rgba(59,130,246,0.06); border:1px solid rgba(59,130,246,0.15); border-radius:9px;">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <i class="fas fa-equals" style="color:#3b82f6; font-size:0.7rem;"></i>
                                <span style="font-size:0.8rem; color:#1d4ed8; font-weight:600;">On Track</span>
                            </div>
                            <span style="font-family:'Syne',sans-serif; font-weight:800; font-size:1rem; color:#1d4ed8;">{{ \App\Models\Project::where('slippage',0)->count() }}</span>
                        </div>

                        <div style="margin-top:0.25rem; padding:0.5rem 0.65rem; background:var(--o50); border-radius:9px; text-align:center;">
                            <p style="font-size:0.68rem; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.06em;">Avg Slippage</p>
                            <p style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.1rem; color:{{ $avgSlippage >= 0 ? '#15803d' : '#dc2626' }}; margin-top:1px;">
                                {{ $avgSlippage >= 0 ? '+' : '' }}{{ number_format($avgSlippage, 2) }}%
                            </p>
                        </div>
                    </div>
                </div>

                {{-- System Status --}}
                <div class="panel">
                    <div class="panel-head">
                        <p class="section-head" style="margin-bottom:0;">
                            <i class="fas fa-server" style="color:#f97316; font-size:0.75rem;"></i> System
                        </p>
                    </div>
                    <div style="padding:0.75rem; display:flex; flex-direction:column; gap:0.4rem;">
                        @foreach([['System','Operational'],['Database','Connected'],['API','Healthy']] as [$lbl,$val])
                        <div class="status-row">
                            <div style="display:flex; align-items:center; gap:0.55rem;">
                                <div style="width:7px; height:7px; background:#22c55e; border-radius:50%; animation:pulse-dot 2s infinite; flex-shrink:0;"></div>
                                <div>
                                    <p style="font-size:0.63rem; color:var(--muted); text-transform:uppercase; letter-spacing:0.05em; font-weight:700;">{{ $lbl }}</p>
                                    <p style="font-size:0.8rem; font-weight:600; color:var(--ink);">{{ $val }}</p>
                                </div>
                            </div>
                            <span style="font-size:0.65rem; background:rgba(34,197,94,0.1); color:#16a34a; padding:2px 8px; border-radius:99px; font-weight:700; border:1px solid rgba(34,197,94,0.2);">Online</span>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- ── RECENT PROJECTS ── --}}
        <div class="dash-section panel">
            <div class="panel-head" style="display:flex; align-items:center; justify-content:space-between;">
                <p class="section-head" style="margin-bottom:0;">
                    <i class="fas fa-clock-rotate-left" style="color:#f97316; font-size:0.75rem;"></i> Recent Projects
                </p>
                <a href="{{ route('admin.projects.index') }}"
                   style="font-size:0.75rem; font-weight:600; color:#ea580c; text-decoration:none; display:flex; align-items:center; gap:0.3rem;"
                   onmouseover="this.style.color='#c2410c'" onmouseout="this.style.color='#ea580c'">
                    View all <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i>
                </a>
            </div>
            <div style="padding:0.25rem 1.25rem 0.5rem;">
                @forelse($recentProjects as $rp)
                @php
                    $rSlip = (float)($rp->slippage ?? 0);
                    $rExpiry = $rp->revised_contract_expiry ?? $rp->original_contract_expiry;
                    $rDays = (int) now()->diffInDays($rExpiry, false);
                @endphp
                <div class="recent-row">
                    <div style="width:32px; height:32px; border-radius:9px; background:var(--o100); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-family:'Syne',sans-serif; font-weight:800; font-size:0.7rem; color:#c2410c;">
                        {{ strtoupper(substr($rp->project_title, 0, 2)) }}
                    </div>
                    <div style="flex:1; min-width:0;">
                        <p style="font-weight:700; font-size:0.845rem; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $rp->project_title }}</p>
                        <p style="font-size:0.7rem; color:#9ca3af; margin-top:1px;">{{ $rp->in_charge }} · {{ $rp->location }}</p>
                    </div>
                    <div style="flex-shrink:0; text-align:right;">
                        @if($rSlip > 0)
                            <span style="display:inline-flex; align-items:center; gap:0.2rem; font-size:0.7rem; font-weight:700; color:#15803d; background:rgba(34,197,94,0.08); padding:2px 7px; border-radius:99px;">
                                <i class="fas fa-arrow-up" style="font-size:0.55rem;"></i>+{{ $rSlip }}%
                            </span>
                        @elseif($rSlip < 0)
                            <span style="display:inline-flex; align-items:center; gap:0.2rem; font-size:0.7rem; font-weight:700; color:#dc2626; background:rgba(239,68,68,0.08); padding:2px 7px; border-radius:99px;">
                                <i class="fas fa-arrow-down" style="font-size:0.55rem;"></i>{{ $rSlip }}%
                            </span>
                        @else
                            <span style="font-size:0.7rem; font-weight:700; color:#6b7280; background:rgba(107,114,128,0.08); padding:2px 7px; border-radius:99px;">0%</span>
                        @endif
                    </div>
                    <div style="flex-shrink:0; min-width:70px; text-align:right;">
                        @if($rp->status === 'completed')
                            <span style="font-size:0.68rem; font-weight:700; color:#15803d; background:rgba(34,197,94,0.08); padding:2px 8px; border-radius:99px; border:1px solid rgba(34,197,94,0.2);">
                                <i class="fas fa-check" style="font-size:0.5rem;"></i> Done
                            </span>
                        @elseif($rDays < 0)
                            <span style="font-size:0.68rem; font-weight:700; color:#dc2626; background:rgba(239,68,68,0.08); padding:2px 8px; border-radius:99px; border:1px solid rgba(239,68,68,0.2);">Expired</span>
                        @elseif($rDays <= 30)
                            <span style="font-size:0.68rem; font-weight:700; color:#b45309; background:rgba(234,179,8,0.1); padding:2px 8px; border-radius:99px; border:1px solid rgba(234,179,8,0.2);">{{ $rDays }}d left</span>
                        @else
                            <span style="font-size:0.68rem; font-weight:700; color:#2563eb; background:rgba(59,130,246,0.08); padding:2px 8px; border-radius:99px; border:1px solid rgba(59,130,246,0.2);">Active</span>
                        @endif
                    </div>
                    <a href="{{ route('admin.projects.show', $rp) }}"
                       style="flex-shrink:0; width:28px; height:28px; border-radius:7px; background:var(--o50); border:1px solid var(--border); display:flex; align-items:center; justify-content:center; text-decoration:none; transition:all 0.15s;"
                       onmouseover="this.style.background='#f97316';this.style.borderColor='#f97316';this.querySelector('i').style.color='white'"
                       onmouseout="this.style.background='var(--o50)';this.style.borderColor='var(--border)';this.querySelector('i').style.color='#c4956a'">
                        <i class="fas fa-eye" style="font-size:0.6rem; color:#c4956a;"></i>
                    </a>
                </div>
                @empty
                <div style="padding:2rem; text-align:center; color:#9ca3af; font-size:0.845rem;">
                    No projects yet — <a href="{{ route('admin.projects.create') }}" style="color:#f97316; font-weight:600; text-decoration:none;">create one</a>
                </div>
                @endforelse
            </div>
        </div>

    </div>

    <script>
        // Animate metric values counting up
        document.querySelectorAll('.metric-val').forEach(el => {
            const target = parseInt(el.textContent.trim());
            if (isNaN(target) || target === 0) return;
            let current = 0;
            const duration = 800;
            const step = target / (duration / 16);
            const timer = setInterval(() => {
                current = Math.min(current + step, target);
                el.textContent = Math.round(current);
                if (current >= target) clearInterval(timer);
            }, 16);
        });
    </script>
</x-app-layout>