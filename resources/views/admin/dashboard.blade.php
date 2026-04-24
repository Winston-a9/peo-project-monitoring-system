<x-app-layout>
    <x-slot name="header">
        {{-- Ambient orbs — mirrors hero-orb-1 / hero-orb-2 from welcome --}}
        <div class="dashboard-orb"></div>
        <div class="dashboard-orb-2"></div>

        <div style="display:flex; flex-col md:flex-row; align-items:center; justify-content:space-between; gap:1rem;">
            <div>
                <p class="page-subtitle">Administration</p>
                <h2 class="page-title">
                    @php $logoPath = public_path('assets/app_logo.PNG'); @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ asset('assets/app_logo.PNG') }}" alt="PEO Logo" style="height:28px;width:auto;object-fit:contain;margin-right:0.5rem;vertical-align:middle;">
                    @else
                        <i class="fas fa-building-columns" style="color:var(--orange-400);margin-right:0.5rem;"></i>
                    @endif
                    Admin Dashboard
                </h2>
            </div>
            <div class="date-chip" style="animation:fade-up 0.55s var(--ease-smooth) 0.15s both;">
                <div class="date-chip-dot"></div>
                <span>{{ now()->format('l, F j, Y') }}</span>
            </div>
        </div>
    </x-slot>

    @push('styles')
        @vite('resources/css/admin/dashboard.css')
    @endpush

    @php
        $userDivision = auth()->user()->division;
        $baseQ = fn() => \App\Models\Project::when(
            $userDivision,
            fn($q, $div) => $q->where('division', $div)
        );

        $total     = $baseQ()->count();

        $ongoing   = $baseQ()->where('status','ongoing')
                        ->where(function($q){
                            $q->whereNull('revised_contract_expiry')
                              ->where('original_contract_expiry','>=',now())
                              ->orWhere('revised_contract_expiry','>=',now());
                        })->count();

        $completed = $baseQ()->where('status','completed')->count();

        $active    = $baseQ()->where('status','ongoing')
                        ->where(function($q){
                            $q->whereNull('revised_contract_expiry')
                              ->where('original_contract_expiry','>',now()->addDays(30))
                              ->orWhere('revised_contract_expiry','>',now()->addDays(30));
                        })->count();

        $expiring  = $baseQ()->where('status','ongoing')
                        ->where(function($q){
                            $q->whereNull('revised_contract_expiry')
                              ->whereBetween('original_contract_expiry',[now(),now()->addDays(30)])
                              ->orWhereBetween('revised_contract_expiry',[now(),now()->addDays(30)]);
                        })->count();

        $expired   = $baseQ()->where('status','!=','completed')
                        ->where(function($q){
                            $q->whereNull('revised_contract_expiry')
                              ->where('original_contract_expiry','<',now())
                              ->orWhere('revised_contract_expiry','<',now());
                        })->count();

        $segments = [
            ['label'=>'Active',    'count'=>$active,    'color'=>'#06b6d4'],
            ['label'=>'Completed', 'count'=>$completed, 'color'=>'#22c55e'],
            ['label'=>'Expired',   'count'=>$expired,   'color'=>'#ef4444'],
            ['label'=>'Expiring',  'count'=>$expiring,  'color'=>'#eab308'],
        ];

        $recent         = $baseQ()->orderByDesc('updated_at')->limit(5)->get();
        $avgSlippage    = $baseQ()->avg('slippage') ?? 0;

        $expiringProjects = $baseQ()->where('status','ongoing')
            ->where(function($q){
                $q->whereNull('revised_contract_expiry')
                  ->whereBetween('original_contract_expiry',[now(),now()->addDays(30)])
                  ->orWhereBetween('revised_contract_expiry',[now(),now()->addDays(30)]);
            })
            ->orderByRaw('COALESCE(revised_contract_expiry, original_contract_expiry) ASC')
            ->limit(5)->get();
    @endphp

    <div class="space-y-5">

        {{-- ══ ROW 1: Stat cards ══ --}}
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem;" class="fade-up">
            @foreach([
                ['Total Projects', $total,     'fa-folder',       '#f97316', 'rgba(249,115,22,0.1)',  route('admin.projects.index')],
                ['Ongoing',        $ongoing,   'fa-spinner',      '#3b82f6', 'rgba(59,130,246,0.1)',  route('admin.projects.index',['status'=>'ongoing'])],
                ['Completed',      $completed, 'fa-check-circle', '#22c55e', 'rgba(34,197,94,0.1)',   route('admin.projects.index',['status'=>'completed'])],
                ['Expiring Soon',  $expiring,  'fa-clock',        '#eab308', 'rgba(234,179,8,0.1)',   route('admin.projects.index',['status'=>'expiring'])],
            ] as [$label, $val, $icon, $color, $bg, $link])
            <a href="{{ $link }}" class="stat-card">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:0.5rem;">
                    <div>
                        <p class="stat-label">{{ $label }}</p>
                        <p class="stat-count">{{ $val }}</p>
                    </div>
                    <div style="width:40px; height:40px; background:{{ $bg }}; border:1px solid {{ $color }}26; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:all 0.3s var(--ease-spring);"
                         onmouseover="this.style.transform='scale(1.1) rotate(6deg)'"
                         onmouseout="this.style.transform='scale(1) rotate(0deg)'">
                        <i class="fas {{ $icon }}" style="color:{{ $color }}; font-size:1rem;"></i>
                    </div>
                </div>
                <div class="stat-bar">
                    <div class="stat-bar-fill" style="background:{{ $color }}; width:{{ $total > 0 ? round(($val/$total)*100) : 0 }}%;"></div>
                </div>
                <p class="stat-pct">{{ $total > 0 ? round(($val/$total)*100) : 0 }}% of total</p>
            </a>
            @endforeach
        </div>

        {{-- ══ ROW 2: Donut + Slippage + Quick Access ══ --}}
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;" class="fade-up-2">

            {{-- Donut --}}
            <div class="card">
                <div class="card-header" style="justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <div class="card-header-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <span class="card-header-title">Status Breakdown</span>
                    </div>
                    <div class="live-dot">
                        <div class="live-dot-pulse"></div>
                        Live
                    </div>
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
                    <div style="position:relative; width:148px; height:148px;">
                        <svg width="148" height="148" viewBox="0 0 148 148">
                            <circle cx="74" cy="74" r="{{ $r }}" fill="none" stroke="rgba(249,115,22,0.07)" stroke-width="18"/>
                            @if($total > 0)
                                @foreach($donutSegs as $seg)
                                    @if($seg['count'] > 0)
                                    <circle class="donut-segment"
                                        cx="74" cy="74" r="{{ $r }}" fill="none"
                                        stroke="{{ $seg['color'] }}" stroke-width="18"
                                        stroke-dasharray="{{ $seg['dash'] }} {{ $seg['gap'] }}"
                                        stroke-dashoffset="{{ -$seg['offset'] + $circ/4 }}"
                                        data-label="{{ $seg['label'] }}" data-count="{{ $seg['count'] }}" data-pct="{{ $seg['pct'] }}">
                                        <title>{{ $seg['label'] }}: {{ $seg['count'] }} ({{ $seg['pct'] }}%)</title>
                                    </circle>
                                    @endif
                                @endforeach
                            @endif
                        </svg>
                        <div id="donut-center" style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; transition:all 0.25s var(--ease-smooth);">
                            <span style="font-family:var(--font-display); font-size:1.9rem; font-weight:800; color:var(--white); line-height:1;">{{ $total }}</span>
                            <span style="font-size:0.65rem; color:var(--dark-400); font-weight:600; text-transform:uppercase; letter-spacing:0.06em;">Total</span>
                        </div>
                    </div>

                    <div style="width:100%; display:flex; flex-direction:column; gap:0.5rem;">
                        @foreach($segments as $seg)
                        <div class="legend-item">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div class="legend-dot" style="background:{{ $seg['color'] }};"></div>
                                <span class="legend-label">{{ $seg['label'] }}</span>
                            </div>
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <span class="legend-count">{{ $seg['count'] }}</span>
                                <span class="legend-pct">{{ $total > 0 ? round(($seg['count']/$total)*100) : 0 }}%</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Slippage --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-header-icon">
                        <i class="fas fa-wave-square"></i>
                    </div>
                    <span class="card-header-title">Slippage Health</span>
                </div>
                <div class="card-pad" style="display:flex; flex-direction:column; gap:1.25rem;">

                    @php
                        $slipPositive = $avgSlippage >= 0;
                        $slipColor    = $slipPositive ? '#16a34a' : '#dc2626';
                        $slipBg       = $slipPositive ? 'rgba(34,197,94,0.06)'  : 'rgba(239,68,68,0.06)';
                        $slipBorder   = $slipPositive ? 'rgba(34,197,94,0.18)'  : 'rgba(239,68,68,0.18)';
                        $slipHoverShadow = $slipPositive ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)';
                    @endphp

                    <div class="slippage-card"
                         style="background:{{ $slipBg }}; border:1px solid {{ $slipBorder }};"
                         onmouseover="this.style.boxShadow='0 8px 24px {{ $slipHoverShadow }}'"
                         onmouseout="this.style.boxShadow='none'">
                        <p class="slippage-card-label" style="color:{{ $slipColor }};">Avg. Slippage</p>
                        <p class="slippage-card-num" style="color:{{ $slipColor }};">
                            {{ $slipPositive ? '+' : '' }}{{ round($avgSlippage,1) }}<span>%</span>
                        </p>
                        <p class="slippage-card-status" style="color:{{ $slipColor }};">
                            <i class="fas {{ $slipPositive ? 'fa-arrow-up' : 'fa-arrow-down' }}" style="font-size:0.58rem;"></i>
                            {{ $slipPositive ? 'Ahead of schedule' : 'Behind schedule' }}
                        </p>
                    </div>

                    @if($expired > 0)
                    <a href="{{ route('admin.projects.index', ['status'=>'expired']) }}" class="expired-alert">
                        <div class="expired-alert-icon">
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

            {{-- Quick Access --}}
            <div class="card">
                <div class="card-header">
                    <div class="card-header-icon">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <span class="card-header-title">Quick Access</span>
                </div>
                <div style="overflow:hidden;">
                    @foreach([
                        [route('admin.projects.index'),                        'fa-folder-open',  'rgba(249,115,22,0.1)', '#f97316', 'All Projects',    'Browse & manage'],
                        [route('admin.projects.index',['status'=>'ongoing']), 'fa-spinner',      'rgba(59,130,246,0.1)', '#3b82f6', 'Ongoing',          $ongoing.' active'],
                        [route('admin.projects.index',['status'=>'expiring']),'fa-clock',        'rgba(234,179,8,0.1)',  '#ca8a04', 'Expiring Soon',    $expiring.' at risk'],
                        [route('admin.projects.create'),                       'fa-plus-circle',  'rgba(34,197,94,0.1)',  '#22c55e', 'New Project',      'Create entry'],
                        [route('admin.reports.index'),                         'fa-file-pdf',     'rgba(139,92,246,0.1)', '#8b5cf6', 'Generate Report',  'Export data'],
                    ] as [$url, $icon, $bg, $color, $title, $sub])
                    <a href="{{ $url }}" class="quick-link">
                        <div class="quick-link-icon" style="background:{{ $bg }}; border:1px solid {{ $color }}26;">
                            <i class="fas {{ $icon }}" style="color:{{ $color }};"></i>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <p class="quick-link-title">{{ $title }}</p>
                            <p class="quick-link-sub">{{ $sub }}</p>
                        </div>
                        <i class="fas fa-chevron-right quick-link-arrow"></i>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ══ ROW 3: Recently Updated + Expiring Soon ══ --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;" class="fade-up-3">

            {{-- Recently Updated --}}
            <div class="card">
                <div class="card-header" style="justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <div class="card-header-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <span class="card-header-title">Recently Updated</span>
                    </div>
                    <a href="{{ route('admin.projects.index') }}" class="view-all">View all <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i></a>
                </div>

                @forelse($recent as $project)
                @php
                    $expiry    = $project->revised_contract_expiry ?? $project->original_contract_expiry;
                    $isExpired = $expiry->isPast() && $project->status !== 'completed';
                    $isExpiring= !$isExpired && $expiry->diffInDays(now()) <= 30 && $project->status !== 'completed';
                    $sk = $project->status==='completed' ? 'completed' : ($isExpired ? 'expired' : ($isExpiring ? 'expiring' : 'ongoing'));
                    $sl = (float)($project->slippage ?? 0);
                    $rowIcons  = ['completed'=>'fa-check-circle','expired'=>'fa-times-circle','expiring'=>'fa-clock','ongoing'=>'fa-spinner'];
                    $rowColors = ['completed'=>'#22c55e','expired'=>'#dc2626','expiring'=>'#ca8a04','ongoing'=>'#3b82f6'];
                    $rowBgs    = ['completed'=>'rgba(34,197,94,0.08)','expired'=>'rgba(220,38,38,0.08)','expiring'=>'rgba(202,138,4,0.08)','ongoing'=>'rgba(59,130,246,0.08)'];
                @endphp
                <a href="{{ route('admin.projects.show', $project) }}" class="recent-row" style="animation:slideInRight 0.4s ease-out both;">
                    <div style="display:flex; align-items:center; gap:0.75rem; min-width:0; flex:1;">
                        <div class="recent-row-icon" style="background:{{ $rowBgs[$sk] }};">
                            <i class="fas {{ $rowIcons[$sk] }}" style="color:{{ $rowColors[$sk] }};"></i>
                        </div>
                        <div style="min-width:0;">
                            <p class="recent-row-title">{{ $project->project_title }}</p>
                            <p class="recent-row-sub">{{ $project->updated_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:0.5rem; flex-shrink:0;">
                        @if($sl > 0)
                            <span style="font-size:0.69rem; font-weight:700; color:#16a34a;">+{{ $sl }}%</span>
                        @elseif($sl < 0)
                            <span style="font-size:0.69rem; font-weight:700; color:#dc2626;">{{ $sl }}%</span>
                        @endif
                        <span class="badge badge-{{ $sk }}">{{ ucfirst($sk) }}</span>
                    </div>
                </a>
                @empty
                <div class="all-clear">
                    <div class="all-clear-icon" style="background:rgba(249,115,22,0.07); color:var(--orange-400);">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <p class="all-clear-title" style="color:var(--dark-300);">No projects yet</p>
                    <p class="all-clear-sub">Projects will appear here once added</p>
                </div>
                @endforelse
            </div>

            {{-- Expiring Soon --}}
            <div class="card">
                <div class="card-header" style="justify-content:space-between;">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <div class="card-header-icon" style="background:rgba(234,179,8,0.1); border-color:rgba(234,179,8,0.18); color:#eab308;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <span class="card-header-title">Expiring Soon</span>
                        @if($expiring > 0)
                        <span class="badge-count" style="animation:scaleIn 0.4s ease-out both;">{{ $expiring }}</span>
                        @endif
                    </div>
                    @if($expiring > 0)
                    <a href="{{ route('admin.projects.index', ['status'=>'expiring']) }}" class="view-all">
                        View all <i class="fas fa-arrow-right" style="font-size:0.6rem;"></i>
                    </a>
                    @endif
                </div>

                @forelse($expiringProjects as $project)
                @php
                    $exp      = $project->revised_contract_expiry ?? $project->original_contract_expiry;
                    $dLeft    = (int) now()->diffInDays($exp, false);
                    $urgColor = $dLeft <= 7 ? '#dc2626' : ($dLeft <= 14 ? '#b45309' : '#ca8a04');
                    $urgBg    = $dLeft <= 7 ? 'rgba(220,38,38,0.07)' : 'rgba(234,179,8,0.07)';
                @endphp
                <a href="{{ route('admin.projects.show', $project) }}" class="recent-row" style="animation:slideInRight 0.4s ease-out both;">
                    <div style="display:flex; align-items:center; gap:0.75rem; min-width:0; flex:1;">
                        <div class="recent-row-icon" style="background:{{ $urgBg }};">
                            <i class="fas fa-calendar-times" style="color:{{ $urgColor }};"></i>
                        </div>
                        <div style="min-width:0;">
                            <p class="recent-row-title">{{ $project->project_title }}</p>
                            <p class="recent-row-sub">Expires {{ $exp->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <p style="font-family:var(--font-display); font-size:1.15rem; font-weight:800; color:{{ $urgColor }}; line-height:1; animation:counterUp 0.6s ease-out both;">{{ $dLeft }}</p>
                        <p style="font-size:0.62rem; color:{{ $urgColor }}; font-weight:600; margin-top:1px;">days left</p>
                    </div>
                </a>
                @empty
                <div class="all-clear">
                    <div class="all-clear-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <p class="all-clear-title">All clear!</p>
                    <p class="all-clear-sub">No projects expiring in the next 30 days</p>
                </div>
                @endforelse
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        window.dashboardTotal = {{ $total }};
    </script>
    @vite('resources/js/admin/dashboard.js')
    @endpush
</x-app-layout>