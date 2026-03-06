<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; letter-spacing:-0.03em; color:#1a0f00; display:flex; align-items:center; gap:0.6rem;">
                    <span style="background:#f97316; width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(249,115,22,0.35);">
                        <i class="fas fa-chart-pie" style="color:white; font-size:0.85rem;"></i>
                    </span>
                    Project Overview
                </h2>
                <p style="color:#6b4f35; font-size:0.82rem; margin-top:3px;">
                    Welcome back, <span style="font-weight:700; color:#f97316;">{{ Auth::user()->name }}</span> — read-only access
                </p>
            </div>
            <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.82rem; color:#6b4f35;">
                <i class="fas fa-clock" style="color:#f97316;"></i>
                <span>{{ now()->format('l, F j, Y') }}</span>
            </div>
        </div>
    </x-slot>

    @php
        $total     = \App\Models\Project::count();
        $ongoing   = \App\Models\Project::where('status','ongoing')
                        ->where(function($q){ $q->whereNull('revised_contract_expiry')->where('original_contract_expiry','>=',now())->orWhere('revised_contract_expiry','>=',now()); })
                        ->count();
        $completed = \App\Models\Project::where('status','completed')->count();
        $expiring  = \App\Models\Project::where('status','ongoing')
                        ->where(function($q){ $q->whereNull('revised_contract_expiry')->whereBetween('original_contract_expiry',[now(),now()->addDays(30)])->orWhereBetween('revised_contract_expiry',[now(),now()->addDays(30)]); })
                        ->count();
        $expired   = \App\Models\Project::where('status','!=','completed')
                        ->where(function($q){ $q->whereNull('revised_contract_expiry')->where('original_contract_expiry','<',now())->orWhere('revised_contract_expiry','<',now()); })
                        ->count();

        $segments = [
            ['label'=>'Ongoing',   'count'=>$ongoing,   'color'=>'#3b82f6'],
            ['label'=>'Completed', 'count'=>$completed, 'color'=>'#22c55e'],
            ['label'=>'Expired',   'count'=>$expired,   'color'=>'#ef4444'],
            ['label'=>'Expiring',  'count'=>$expiring,  'color'=>'#eab308'],
        ];

        $recent         = \App\Models\Project::orderByDesc('updated_at')->limit(5)->get();
        $avgSlippage    = \App\Models\Project::avg('slippage') ?? 0;
        $avgWorkDone    = \App\Models\Project::avg('work_done') ?? 0;
        $avgAsPlanned   = \App\Models\Project::avg('as_planned') ?? 0;
        $expiringProjects = \App\Models\Project::where('status','ongoing')
            ->where(function($q){ $q->whereNull('revised_contract_expiry')->whereBetween('original_contract_expiry',[now(),now()->addDays(30)])->orWhereBetween('revised_contract_expiry',[now(),now()->addDays(30)]); })
            ->orderByRaw('COALESCE(revised_contract_expiry, original_contract_expiry) ASC')
            ->limit(5)->get();
    @endphp

    <style>
        :root {
            --orange-500: #f97316;
            --orange-600: #ea580c;
            --ink:        #1a0f00;
            --ink-muted:  #6b4f35;
            --border:     rgba(249,115,22,0.14);
        }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(14px); }
            to   { opacity:1; transform:translateY(0); }
        }
        @keyframes barGrow { from { width:0%; } }

        .fade-up   { animation: fadeUp 0.45s ease both; }
        .fade-up-2 { animation: fadeUp 0.45s 0.08s ease both; }
        .fade-up-3 { animation: fadeUp 0.45s 0.16s ease both; }

        .card { background:white; border:1px solid var(--border); border-radius:16px; overflow:hidden; }
        .card-pad { padding:1.5rem; }
        .card-header { padding:1rem 1.5rem; border-bottom:1px solid var(--border); background:#fffaf5; display:flex; align-items:center; gap:0.5rem; }
        .card-header-title { font-family:'Syne',sans-serif; font-weight:700; font-size:0.875rem; color:var(--ink); }

        .stat-card {
            background:white; border:1px solid var(--border); border-radius:14px;
            padding:1.25rem 1.4rem; text-decoration:none; display:block;
            transition:transform 0.2s, box-shadow 0.2s, border-color 0.2s;
            position:relative; overflow:hidden;
        }
        .stat-card:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(249,115,22,0.1); border-color:rgba(249,115,22,0.28); }
        .stat-count { font-family:'Syne',sans-serif; font-size:2.4rem; font-weight:800; letter-spacing:-0.04em; line-height:1; color:var(--ink); }
        .stat-bar { height:3px; background:rgba(249,115,22,0.08); border-radius:99px; margin-top:1rem; overflow:hidden; }
        .stat-bar-fill { height:100%; border-radius:99px; animation:barGrow 1.2s cubic-bezier(.16,1,.3,1) both 0.3s; }

        .prog-track { height:8px; background:rgba(249,115,22,0.08); border-radius:99px; overflow:hidden; }
        .prog-fill { height:100%; border-radius:99px; animation:barGrow 1.2s cubic-bezier(.16,1,.3,1) both 0.5s; }

        .recent-row { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:0.8rem 1.5rem; border-bottom:1px solid rgba(249,115,22,0.06); transition:background 0.15s; cursor:pointer; text-decoration:none; }
        .recent-row:last-child { border-bottom:none; }
        .recent-row:hover { background:rgba(249,115,22,0.025); }

        .badge { display:inline-flex; align-items:center; gap:0.3rem; padding:2px 9px; border-radius:99px; font-size:0.67rem; font-weight:700; border:1px solid; white-space:nowrap; }
        .badge-ongoing   { background:rgba(59,130,246,0.08);  color:#2563eb; border-color:rgba(59,130,246,0.2); }
        .badge-completed { background:rgba(34,197,94,0.08);   color:#16a34a; border-color:rgba(34,197,94,0.18); }
        .badge-expiring  { background:rgba(234,179,8,0.1);    color:#b45309; border-color:rgba(234,179,8,0.22); }
        .badge-expired   { background:rgba(239,68,68,0.08);   color:#dc2626; border-color:rgba(239,68,68,0.18); }

        .quick-link { display:flex; align-items:center; gap:0.75rem; padding:0.75rem 1.25rem; border-bottom:1px solid rgba(249,115,22,0.06); text-decoration:none; transition:background 0.15s, padding-left 0.18s; }
        .quick-link:last-child { border-bottom:none; }
        .quick-link:hover { background:rgba(249,115,22,0.03); padding-left:1.5rem; }

        .donut-segment { transition:opacity 0.2s, transform 0.2s; transform-origin:center; cursor:pointer; }
        .donut-segment:hover { opacity:0.82; transform:scale(1.05); }
    </style>

    <div class="space-y-5">

        {{-- ── ROW 1: Stat cards ── --}}
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem;" class="fade-up">
            @foreach([
                ['Total Projects', $total,     'fa-folder',       '#f97316', 'rgba(249,115,22,0.1)',  route('user.projects.index')],
                ['Ongoing',        $ongoing,   'fa-spinner',      '#3b82f6', 'rgba(59,130,246,0.1)',   route('user.projects.index',['status'=>'ongoing'])],
                ['Completed',      $completed, 'fa-check-circle', '#22c55e', 'rgba(34,197,94,0.1)',    route('user.projects.index',['status'=>'completed'])],
                ['Expiring Soon',  $expiring,  'fa-clock',        '#eab308', 'rgba(234,179,8,0.1)',    route('user.projects.index',['status'=>'expiring'])],
            ] as [$label,$val,$icon,$color,$bg,$link])
            <a href="{{ $link }}" class="stat-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:0.5rem;">
                    <div>
                        <p style="font-size:0.63rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--ink-muted); margin-bottom:0.6rem;">{{ $label }}</p>
                        <p class="stat-count">{{ $val }}</p>
                    </div>
                    <div style="width:38px; height:38px; background:{{ $bg }}; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas {{ $icon }}" style="color:{{ $color }}; font-size:1rem;"></i>
                    </div>
                </div>
                <div class="stat-bar">
                    <div class="stat-bar-fill" style="background:{{ $color }}; width:{{ $total > 0 ? round(($val/$total)*100) : 0 }}%;"></div>
                </div>
                <p style="font-size:0.68rem; color:#9ca3af; margin-top:0.5rem;">{{ $total > 0 ? round(($val/$total)*100) : 0 }}% of total</p>
            </a>
            @endforeach
        </div>

        {{-- ── ROW 2: Donut + Metrics + Quick links ── --}}
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;" class="fade-up-2">

            {{-- Donut --}}
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie" style="color:var(--orange-500); font-size:0.82rem;"></i>
                    <span class="card-header-title">Status Breakdown</span>
                </div>
                <div class="card-pad" style="display:flex; flex-direction:column; align-items:center; gap:1.25rem;">
                    @php
                        $r = 54; $circ = 2 * M_PI * $r; $offset = 0; $donutSegs = [];
                        foreach($segments as $seg) {
                            $pct = $total > 0 ? $seg['count'] / $total : 0;
                            $dash = $pct * $circ; $gap = $circ - $dash;
                            $donutSegs[] = ['dash'=>$dash,'gap'=>$gap,'offset'=>$offset*$circ,'color'=>$seg['color'],'label'=>$seg['label'],'count'=>$seg['count'],'pct'=>round($pct*100)];
                            $offset += $pct;
                        }
                    @endphp
                    <div style="position:relative; width:140px; height:140px;">
                        <svg width="140" height="140" viewBox="0 0 140 140">
                            <circle cx="70" cy="70" r="{{ $r }}" fill="none" stroke="rgba(249,115,22,0.07)" stroke-width="16"/>
                            @if($total > 0)
                                @foreach($donutSegs as $seg)
                                @if($seg['count'] > 0)
                                <circle class="donut-segment"
                                    cx="70" cy="70" r="{{ $r }}" fill="none"
                                    stroke="{{ $seg['color'] }}" stroke-width="16"
                                    stroke-dasharray="{{ $seg['dash'] }} {{ $seg['gap'] }}"
                                    stroke-dashoffset="{{ -$seg['offset'] + $circ/4 }}"
                                    data-label="{{ $seg['label'] }}" data-count="{{ $seg['count'] }}" data-pct="{{ $seg['pct'] }}">
                                    <title>{{ $seg['label'] }}: {{ $seg['count'] }} ({{ $seg['pct'] }}%)</title>
                                </circle>
                                @endif
                                @endforeach
                            @else
                                <circle cx="70" cy="70" r="{{ $r }}" fill="none" stroke="rgba(249,115,22,0.08)" stroke-width="16"/>
                            @endif
                        </svg>
                        <div id="donut-center" style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; transition:all 0.15s;">
                            <span style="font-family:'Syne',sans-serif; font-size:1.8rem; font-weight:800; color:var(--ink); line-height:1;">{{ $total }}</span>
                            <span style="font-size:0.65rem; color:#9ca3af; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Total</span>
                        </div>
                    </div>
                    <div style="width:100%; display:flex; flex-direction:column; gap:0.45rem;">
                        @foreach($segments as $seg)
                        <div style="display:flex; align-items:center; justify-content:space-between;">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div style="width:9px; height:9px; border-radius:3px; background:{{ $seg['color'] }};"></div>
                                <span style="font-size:0.78rem; color:var(--ink-muted);">{{ $seg['label'] }}</span>
                            </div>
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <span style="font-family:'Syne',sans-serif; font-weight:800; font-size:0.85rem; color:var(--ink);">{{ $seg['count'] }}</span>
                                <span style="font-size:0.68rem; color:#9ca3af; min-width:28px; text-align:right;">{{ $total > 0 ? round(($seg['count']/$total)*100) : 0 }}%</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Average Progress --}}
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-bar" style="color:var(--orange-500); font-size:0.82rem;"></i>
                    <span class="card-header-title">Average Progress</span>
                </div>
                <div class="card-pad" style="display:flex; flex-direction:column; gap:1.5rem;">

                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:0.6rem;">
                            <p style="font-size:0.63rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted);">Avg. As Planned</p>
                            <span style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.25rem; color:var(--ink);">{{ round($avgAsPlanned,1) }}<span style="font-size:0.72rem; color:var(--ink-muted);">%</span></span>
                        </div>
                        <div class="prog-track"><div class="prog-fill" style="background:var(--orange-500); width:{{ min($avgAsPlanned,100) }}%;"></div></div>
                    </div>

                    <div>
                        <div style="display:flex; justify-content:space-between; align-items:baseline; margin-bottom:0.6rem;">
                            <p style="font-size:0.63rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted);">Avg. Work Done</p>
                            <span style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.25rem; color:var(--ink);">{{ round($avgWorkDone,1) }}<span style="font-size:0.72rem; color:var(--ink-muted);">%</span></span>
                        </div>
                        <div class="prog-track"><div class="prog-fill" style="background:#3b82f6; width:{{ min($avgWorkDone,100) }}%;"></div></div>
                    </div>

                    {{-- Slippage card --}}
                    <div style="background:{{ $avgSlippage >= 0 ? 'rgba(34,197,94,0.05)' : 'rgba(239,68,68,0.05)' }}; border:1px solid {{ $avgSlippage >= 0 ? 'rgba(34,197,94,0.18)' : 'rgba(239,68,68,0.18)' }}; border-radius:12px; padding:1rem; text-align:center;">
                        <p style="font-size:0.63rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:{{ $avgSlippage >= 0 ? '#16a34a' : '#dc2626' }}; margin-bottom:0.4rem;">Avg. Slippage</p>
                        <p style="font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; color:{{ $avgSlippage >= 0 ? '#16a34a' : '#dc2626' }}; line-height:1;">
                            {{ $avgSlippage >= 0 ? '+' : '' }}{{ round($avgSlippage,1) }}<span style="font-size:0.9rem;">%</span>
                        </p>
                        <p style="font-size:0.72rem; font-weight:600; margin-top:0.35rem; color:{{ $avgSlippage >= 0 ? '#16a34a' : '#dc2626' }};">
                            <i class="fas {{ $avgSlippage >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}" style="font-size:0.58rem;"></i>
                            {{ $avgSlippage >= 0 ? 'Ahead of schedule' : 'Behind schedule' }}
                        </p>
                    </div>

                    @if($expired > 0)
                    <a href="{{ route('user.projects.index', ['status'=>'expired']) }}"
                       style="display:flex; align-items:center; gap:0.75rem; padding:0.75rem 1rem; background:rgba(239,68,68,0.04); border:1px solid rgba(239,68,68,0.16); border-radius:10px; text-decoration:none; transition:background 0.15s;"
                       onmouseover="this.style.background='rgba(239,68,68,0.09)'" onmouseout="this.style.background='rgba(239,68,68,0.04)'">
                        <div style="width:32px; height:32px; background:rgba(239,68,68,0.1); border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-exclamation-triangle" style="color:#dc2626; font-size:0.78rem;"></i>
                        </div>
                        <div>
                            <p style="font-size:0.78rem; font-weight:700; color:#dc2626;">{{ $expired }} Expired {{ Str::plural('Project',$expired) }}</p>
                            <p style="font-size:0.68rem; color:#ef4444; margin-top:1px;">Click to review</p>
                        </div>
                        <i class="fas fa-chevron-right" style="color:#dc2626; font-size:0.58rem; margin-left:auto;"></i>
                    </a>
                    @endif
                </div>
            </div>

            {{-- Quick links --}}
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bolt" style="color:var(--orange-500); font-size:0.82rem;"></i>
                    <span class="card-header-title">Quick Access</span>
                </div>
                <div>
                    @foreach([
                        [route('user.projects.index'),                         'fa-folder-open',  'rgba(249,115,22,0.1)', '#f97316', 'All Projects',   'Browse every project'],
                        [route('user.projects.index',['status'=>'ongoing']),   'fa-spinner',      'rgba(59,130,246,0.1)', '#3b82f6', 'Ongoing',         $ongoing.' active projects'],
                        [route('user.projects.index',['status'=>'expiring']),  'fa-clock',        'rgba(234,179,8,0.1)',  '#ca8a04', 'Expiring Soon',   $expiring.' need attention'],
                        [route('user.projects.index',['status'=>'completed']), 'fa-check-circle', 'rgba(34,197,94,0.1)',  '#16a34a', 'Completed',        $completed.' finished'],
                        [route('user.projects.index',['status'=>'expired']),   'fa-times-circle', 'rgba(239,68,68,0.1)',  '#dc2626', 'Expired',          $expired.' expired'],
                    ] as [$url,$icon,$bg,$color,$title,$sub])
                    <a href="{{ $url }}" class="quick-link">
                        <div style="width:30px; height:30px; border-radius:8px; background:{{ $bg }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas {{ $icon }}" style="color:{{ $color }}; font-size:0.75rem;"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <p style="font-size:0.84rem; font-weight:700; color:var(--ink);">{{ $title }}</p>
                            <p style="font-size:0.7rem; color:#9ca3af; margin-top:1px;">{{ $sub }}</p>
                        </div>
                        <i class="fas fa-chevron-right" style="color:rgba(249,115,22,0.3); font-size:0.58rem; flex-shrink:0;"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── ROW 3: Recently updated + Expiring soon ── --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;" class="fade-up-3">

            {{-- Recently updated --}}
            <div class="card">
                <div class="card-header" style="justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <i class="fas fa-history" style="color:var(--orange-500); font-size:0.82rem;"></i>
                        <span class="card-header-title">Recently Updated</span>
                    </div>
                    <a href="{{ route('user.projects.index') }}" style="font-size:0.72rem; font-weight:600; color:var(--orange-600); text-decoration:none;">View all →</a>
                </div>
                @forelse($recent as $project)
                @php
                    $expiry    = $project->revised_contract_expiry ?? $project->original_contract_expiry;
                    $isExpired = $expiry->isPast() && $project->status !== 'completed';
                    $isExpiring= !$isExpired && $expiry->diffInDays(now()) <= 30 && $project->status !== 'completed';
                    $sk = $project->status==='completed' ? 'completed' : ($isExpired ? 'expired' : ($isExpiring ? 'expiring' : 'ongoing'));
                    $sl = (float)($project->slippage ?? 0);
                    $icons = ['completed'=>'fa-check-circle','expired'=>'fa-times-circle','expiring'=>'fa-clock','ongoing'=>'fa-spinner'];
                    $colors= ['completed'=>'#22c55e','expired'=>'#dc2626','expiring'=>'#ca8a04','ongoing'=>'#3b82f6'];
                @endphp
                <a href="{{ route('user.projects.show', $project) }}" class="recent-row">
                    <div style="display:flex; align-items:center; gap:0.75rem; min-width:0; flex:1;">
                        <div style="width:36px; height:36px; border-radius:9px; background:rgba(249,115,22,0.07); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas {{ $icons[$sk] }}" style="color:{{ $colors[$sk] }}; font-size:0.85rem;"></i>
                        </div>
                        <div style="min-width:0;">
                            <p style="font-size:0.84rem; font-weight:700; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">{{ $project->project_title }}</p>
                            <p style="font-size:0.7rem; color:#9ca3af; margin-top:1px;">{{ $project->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.5rem; flex-shrink:0;">
                        @if($sl > 0)<span style="font-size:0.69rem; font-weight:700; color:#16a34a;">+{{ $sl }}%</span>
                        @elseif($sl < 0)<span style="font-size:0.69rem; font-weight:700; color:#dc2626;">{{ $sl }}%</span>
                        @endif
                        <span class="badge badge-{{ $sk }}">{{ ucfirst($sk) }}</span>
                    </div>
                </a>
                @empty
                <div style="padding:2.5rem 1.5rem; text-align:center;">
                    <i class="fas fa-folder-open" style="font-size:1.5rem; color:rgba(249,115,22,0.2); display:block; margin-bottom:0.5rem;"></i>
                    <p style="font-size:0.84rem; color:#9ca3af;">No projects yet</p>
                </div>
                @endforelse
            </div>

            {{-- Expiring soon --}}
            <div class="card">
                <div class="card-header" style="justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <i class="fas fa-clock" style="color:#eab308; font-size:0.82rem;"></i>
                        <span class="card-header-title">Expiring Soon</span>
                        @if($expiring > 0)
                        <span style="background:rgba(234,179,8,0.1); color:#b45309; border:1px solid rgba(234,179,8,0.22); padding:1px 8px; border-radius:99px; font-size:0.67rem; font-weight:700;">{{ $expiring }}</span>
                        @endif
                    </div>
                    @if($expiring > 0)
                    <a href="{{ route('user.projects.index', ['status'=>'expiring']) }}" style="font-size:0.72rem; font-weight:600; color:var(--orange-600); text-decoration:none;">View all →</a>
                    @endif
                </div>
                @forelse($expiringProjects as $project)
                @php
                    $exp   = $project->revised_contract_expiry ?? $project->original_contract_expiry;
                    $dLeft = (int) now()->diffInDays($exp, false);
                    $urgColor = $dLeft <= 7 ? '#dc2626' : ($dLeft <= 14 ? '#b45309' : '#ca8a04');
                    $urgBg    = $dLeft <= 7 ? 'rgba(239,68,68,0.07)' : 'rgba(234,179,8,0.07)';
                @endphp
                <a href="{{ route('user.projects.show', $project) }}" class="recent-row">
                    <div style="display:flex; align-items:center; gap:0.75rem; min-width:0; flex:1;">
                        <div style="width:36px; height:36px; border-radius:9px; background:{{ $urgBg }}; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-calendar-times" style="color:{{ $urgColor }}; font-size:0.82rem;"></i>
                        </div>
                        <div style="min-width:0;">
                            <p style="font-size:0.84rem; font-weight:700; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">{{ $project->project_title }}</p>
                            <p style="font-size:0.7rem; color:#9ca3af; margin-top:1px;">Expires {{ $exp->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <p style="font-family:'Syne',sans-serif; font-size:1.1rem; font-weight:800; color:{{ $urgColor }}; line-height:1;">{{ $dLeft }}</p>
                        <p style="font-size:0.62rem; color:{{ $urgColor }}; font-weight:600; margin-top:1px;">days left</p>
                    </div>
                </a>
                @empty
                <div style="padding:2.5rem 1.5rem; text-align:center;">
                    <div style="width:48px; height:48px; background:rgba(34,197,94,0.07); border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 0.75rem;">
                        <i class="fas fa-check-circle" style="font-size:1.2rem; color:#22c55e;"></i>
                    </div>
                    <p style="font-size:0.84rem; font-weight:700; color:#16a34a;">All clear!</p>
                    <p style="font-size:0.75rem; color:#9ca3af; margin-top:0.2rem;">No projects expiring in the next 30 days</p>
                </div>
                @endforelse
            </div>

        </div>
    </div>

    <script>
        // Donut hover — update center text
        const center = document.getElementById('donut-center');
        document.querySelectorAll('.donut-segment').forEach(seg => {
            seg.addEventListener('mouseenter', () => {
                center.innerHTML = `
                    <span style="font-family:'Syne',sans-serif;font-size:1.5rem;font-weight:800;color:#1a0f00;line-height:1;">${seg.dataset.count}</span>
                    <span style="font-size:0.6rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;margin-top:2px;">${seg.dataset.label}</span>
                    <span style="font-size:0.65rem;font-weight:700;color:#9ca3af;">${seg.dataset.pct}%</span>`;
            });
            seg.addEventListener('mouseleave', () => {
                center.innerHTML = `
                    <span style="font-family:'Syne',sans-serif;font-size:1.8rem;font-weight:800;color:#1a0f00;line-height:1;">{{ $total }}</span>
                    <span style="font-size:0.65rem;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Total</span>`;
            });
        });
    </script>
</x-app-layout>