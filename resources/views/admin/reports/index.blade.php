<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; letter-spacing:-0.03em; color:var(--text-primary); display:flex; align-items:center; gap:0.6rem;">
                <span style="background:#f97316; width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(249,115,22,0.35);">
                    <i class="fas fa-file-pdf" style="color:white; font-size:0.85rem;"></i>
                </span>
                Reports
            </h2>
            <p style="color:var(--text-secondary); font-size:0.82rem; margin-top:3px;">Generate and view project reports</p>
        </div>
        <div style="display:flex; gap:0.6rem; align-items:center;">
            <form action="{{ route('admin.reports.generate') }}" method="GET" style="display:inline;">
                <input type="hidden" name="search"    value="{{ request('search') }}">
                <input type="hidden" name="in_charge" value="{{ request('in_charge') }}">
                <input type="hidden" name="status"    value="{{ request('status') }}">
                <button type="submit" ...>
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </form>
            <a href="{{ route('admin.projects.index') }}"
               style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.65rem 1.1rem; border:1.5px solid var(--border); border-radius:9px; font-weight:600; font-size:0.825rem; color:var(--text-secondary); text-decoration:none; background:var(--bg-secondary); transition:all 0.2s;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</x-slot>

<style>
    :root {
        --orange-500: #f97316;
        --orange-600: #ea580c;
        --ink:        #1a0f00;
        --ink-muted:  #6b4f35;
        --border:     rgba(249,115,22,0.14);
        --bg-primary: #ffffff;
        --bg-secondary: #fffaf5;
        --text-primary: #1a0f00;
        --text-secondary: #6b4f35;
    }

    @media (prefers-color-scheme: dark) {
        :root {
            --bg-primary: #0f0f0f;
            --bg-secondary: #1a1a1a;
            --text-primary: #f5f5f0;
            --text-secondary: #9ca3af;
            --ink: #f5f5f0;
            --ink-muted: #9ca3af;
            --border: rgba(249,115,22,0.25);
        }
    }

    @keyframes fadeUp { from{opacity:0;transform:translateY(12px);} to{opacity:1;transform:translateY(0);} }
    .fade-up   { animation: fadeUp 0.4s ease both; }
    .fade-up-2 { animation: fadeUp 0.4s 0.06s ease both; }

    body { transition: background-color 0.3s, color 0.3s; }

    .main-card { background:var(--bg-primary); border:1px solid var(--border); border-radius:16px; overflow:hidden; }
    .filter-bar { padding:1.1rem 1.5rem; border-bottom:1px solid var(--border); background:var(--bg-secondary); }

    .summary-cards { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1.2rem; margin-bottom:2rem; }
    .summary-card {
        background:var(--bg-primary); border:1px solid var(--border); border-radius:12px; padding:1.5rem;
        display:flex; align-items:center; gap:1rem; transition:all 0.3s;
    }
    .summary-card:hover { border-color:var(--orange-500); transform:translateY(-2px); box-shadow:0 4px 12px rgba(249,115,22,0.15); }
    .summary-card-icon { width:50px; height:50px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1.4rem; }
    .summary-card-content { flex:1; }
    .summary-card-label { font-size:0.8rem; color:var(--text-secondary); font-weight:600; text-transform:uppercase; }
    .summary-card-value { font-size:1.8rem; font-weight:800; color:var(--text-primary); margin-top:0.25rem; }

    .filter-input {
        padding:0.575rem 1rem 0.575rem 2.25rem; width:100%;
        border:1.5px solid var(--border); border-radius:9px;
        font-size:0.84rem; color:var(--text-primary); background:var(--bg-primary); outline:none;
        font-family:'Instrument Sans',sans-serif; transition:border-color 0.2s, box-shadow 0.2s;
    }
    .filter-input:focus { border-color:var(--orange-500); box-shadow:0 0 0 3px rgba(249,115,22,0.1); }

    .filter-select {
        padding:0.575rem 2.25rem 0.575rem 0.875rem; width:100%;
        border:1.5px solid var(--border); border-radius:9px;
        font-size:0.84rem; color:var(--text-primary); background:var(--bg-primary); outline:none;
        appearance:none; cursor:pointer; font-family:'Instrument Sans',sans-serif;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-position:right 0.55rem center; background-repeat:no-repeat; background-size:1em;
        transition:border-color 0.2s, box-shadow 0.2s;
    }
    .filter-select:focus { border-color:var(--orange-500); box-shadow:0 0 0 3px rgba(249,115,22,0.1); }

    /* Table */
    .proj-table { width:100%; border-collapse:collapse; }
    .proj-table thead th {
        padding:0.7rem 1.25rem; background:var(--bg-secondary);
        font-size:0.63rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em;
        color:var(--text-secondary); border-bottom:1px solid var(--border);
        white-space:nowrap; text-align:left; user-select:none;
    }
    .proj-table thead th.sortable { cursor:pointer; transition:color 0.15s; }
    .proj-table thead th.sortable:hover { color:var(--orange-600); }
    .proj-table thead th .sort-icon { margin-left:0.3rem; opacity:0.35; font-size:0.55rem; }
    .proj-table thead th.sort-active .sort-icon { opacity:1; color:var(--orange-500); }

    .proj-table tbody td {
        padding:0.9rem 1.25rem; font-size:0.845rem; color:var(--text-secondary);
        border-bottom:1px solid var(--border); vertical-align:middle;
    }
    .proj-table tbody tr:last-child td { border-bottom:none; }
    .proj-table tbody tr { transition:background 0.13s; }
    @media (prefers-color-scheme: light) {
        .proj-table tbody tr:hover td { background:rgba(249,115,22,0.025); }
    }
    @media (prefers-color-scheme: dark) {
        .proj-table tbody tr:hover td { background:rgba(249,115,22,0.15); }
    }
    .proj-table tbody tr:hover td:first-child { box-shadow:inset 3px 0 0 var(--orange-500); }

    /* Badges */
    .badge { display:inline-flex; align-items:center; gap:0.3rem; padding:3px 10px; border-radius:99px; font-size:0.68rem; font-weight:700; border:1px solid; white-space:nowrap; }
    @media (prefers-color-scheme: light) {
        .badge-active    { background:rgba(6,182,212,0.08);   color:#0891b2; border-color:rgba(6,182,212,0.22); }
        .badge-ongoing   { background:rgba(59,130,246,0.08);  color:#2563eb; border-color:rgba(59,130,246,0.22); }
        .badge-completed { background:rgba(34,197,94,0.08);   color:#16a34a; border-color:rgba(34,197,94,0.2); }
        .badge-expiring  { background:rgba(234,179,8,0.1);    color:#b45309; border-color:rgba(234,179,8,0.25); }
        .badge-expired   { background:rgba(239,68,68,0.08);   color:#dc2626; border-color:rgba(239,68,68,0.2); }
    }
    @media (prefers-color-scheme: dark) {
        .badge-active    { background:rgba(6,182,212,0.15);   color:#06b6d4; border-color:rgba(6,182,212,0.3); }
        .badge-ongoing   { background:rgba(59,130,246,0.15);  color:#60a5fa; border-color:rgba(59,130,246,0.3); }
        .badge-completed { background:rgba(34,197,94,0.15);   color:#4ade80; border-color:rgba(34,197,94,0.3); }
        .badge-expiring  { background:rgba(234,179,8,0.15);    color:#facc15; border-color:rgba(234,179,8,0.3); }
        .badge-expired   { background:rgba(239,68,68,0.15);   color:#f87171; border-color:rgba(239,68,68,0.3); }
    }

    .slip-pill { display:inline-flex; align-items:center; gap:0.25rem; padding:2px 8px; border-radius:99px; font-size:0.69rem; font-weight:700; }
    @media (prefers-color-scheme: light) {
        .slip-ahead  { background:rgba(34,197,94,0.1);   color:#16a34a; }
        .slip-behind { background:rgba(239,68,68,0.1);   color:#dc2626; }
        .slip-on     { background:rgba(156,163,175,0.1); color:#6b7280; }
    }
    @media (prefers-color-scheme: dark) {
        .slip-ahead  { background:rgba(34,197,94,0.15);   color:#4ade80; }
        .slip-behind { background:rgba(239,68,68,0.15);   color:#f87171; }
        .slip-on     { background:rgba(156,163,175,0.15); color:#9ca3af; }
    }

    .view-btn { display:inline-flex; align-items:center; gap:0.3rem; padding:0.38rem 0.8rem; border-radius:8px; font-size:0.755rem; font-weight:600; color:var(--text-secondary); border:1.5px solid var(--border); background:var(--bg-primary); text-decoration:none; transition:all 0.16s; white-space:nowrap; }
    .view-btn:hover { border-color:var(--orange-500); color:var(--orange-600); background:rgba(249,115,22,0.1); }

    /* Chips */
    .chip { display:inline-flex; align-items:center; gap:0.3rem; padding:0.3rem 0.75rem; border-radius:99px; font-size:0.72rem; font-weight:600; border:1.5px solid; text-decoration:none; transition:all 0.16s; }
    .chip-default { background:var(--bg-primary); color:var(--text-secondary); border-color:var(--border); }
    .chip-default:hover { border-color:rgba(249,115,22,0.35); color:var(--orange-600); }
    .chip-active { background:var(--orange-500); color:white; border-color:var(--orange-500); box-shadow:0 2px 8px rgba(249,115,22,0.28); }

    /* Pagination */
    .pagination-wrap { padding:1rem 1.5rem; border-top:1px solid var(--border); background:var(--bg-secondary); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem; }
    .page-info { font-size:0.78rem; color:var(--text-secondary); }
    .page-links { display:flex; align-items:center; gap:0.3rem; flex-wrap:wrap; }
    .page-btn { display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 0.5rem; border-radius:8px; font-size:0.8rem; font-weight:600; border:1.5px solid var(--border); background:var(--bg-primary); color:var(--text-secondary); text-decoration:none; transition:all 0.16s; font-family:'Instrument Sans',sans-serif; }
    .page-btn:hover:not(.disabled):not(.active) { border-color:var(--orange-500); color:var(--orange-600); background:rgba(249,115,22,0.1); }
    .page-btn.active { background:var(--orange-500); border-color:var(--orange-500); color:white; box-shadow:0 2px 8px rgba(249,115,22,0.3); }
    .page-btn.disabled { opacity:0.35; pointer-events:none; cursor:default; }
    .page-btn.ellipsis { border-color:transparent; background:transparent; pointer-events:none; }

    .per-page-select { padding:0.35rem 1.75rem 0.35rem 0.6rem; border:1.5px solid var(--border); border-radius:8px; font-size:0.78rem; color:var(--text-primary); background:var(--bg-primary); outline:none; appearance:none; cursor:pointer; font-family:'Instrument Sans',sans-serif; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-position:right 0.4rem center; background-repeat:no-repeat; background-size:0.9em; transition:border-color 0.2s; }
    .per-page-select:focus { border-color:var(--orange-500); }

    .empty-state { padding:3.5rem 1.5rem; text-align:center; }
</style>

@php
    $today      = now();
    $perPage    = in_array((int)request('per_page',10), [10,25,50]) ? (int)request('per_page',10) : 10;
    $search     = request('search','');
    $inCharge   = request('in_charge','');
    $slipFilter = request('slip','');
    $status     = request('status','all');
    $sortCol    = request('sort','updated_at');
    $sortDir    = request('dir','desc') === 'asc' ? 'asc' : 'desc';

    $allowed = ['project_title','in_charge','contractor','location','work_done','slippage','original_contract_expiry','updated_at'];
    $sortCol = in_array($sortCol, $allowed) ? $sortCol : 'updated_at';

    $q = \App\Models\Project::query();

    if ($search) {
        $q->where(fn($x) => $x->where('project_title','like',"%$search%")->orWhere('contractor','like',"%$search%")->orWhere('location','like',"%$search%"));
    }
    if ($inCharge) $q->where('in_charge', $inCharge);
    if ($slipFilter === 'ahead')  $q->where('slippage','>',0);
    if ($slipFilter === 'behind') $q->where('slippage','<',0);
    if ($slipFilter === 'on')     $q->where('slippage',0);

    if ($status === 'completed') {
        $q->where('status','completed');
    } elseif ($status === 'active') {
        $q->where('status','ongoing')->where(fn($x) => $x->whereNull('revised_contract_expiry')->where('original_contract_expiry','>',now()->addDays(30))->orWhere('revised_contract_expiry','>',now()->addDays(30)));
    } elseif ($status === 'expiring') {
        $q->where('status','!=','completed')->where(fn($x) => $x->whereNull('revised_contract_expiry')->whereBetween('original_contract_expiry',[now(),now()->addDays(30)])->orWhereBetween('revised_contract_expiry',[now(),now()->addDays(30)]));
    } elseif ($status === 'expired') {
        $q->where('status','!=','completed')->where(fn($x) => $x->whereNull('revised_contract_expiry')->where('original_contract_expiry','<',now())->orWhere('revised_contract_expiry','<',now()));
    } elseif ($status === 'ongoing') {
        $q->where('status','ongoing')->where(fn($x) => $x->whereNull('revised_contract_expiry')->where('original_contract_expiry','>=',now())->orWhere('revised_contract_expiry','>=',now()));
    }

    $projects = $q->orderBy($sortCol,$sortDir)->paginate($perPage)->withQueryString();

    // Chip counts
    $base = \App\Models\Project::query();
    if ($search)   $base->where(fn($x) => $x->where('project_title','like',"%$search%")->orWhere('contractor','like',"%$search%")->orWhere('location','like',"%$search%"));
    if ($inCharge) $base->where('in_charge',$inCharge);
    if ($slipFilter === 'ahead')  $base->where('slippage','>',0);
    if ($slipFilter === 'behind') $base->where('slippage','<',0);
    if ($slipFilter === 'on')     $base->where('slippage',0);

    $counts = [
        'all'       => (clone $base)->count(),
        'active'    => (clone $base)->where('status','ongoing')->where(fn($x) => $x->whereNull('revised_contract_expiry')->where('original_contract_expiry','>',now()->addDays(30))->orWhere('revised_contract_expiry','>',now()->addDays(30)))->count(),
        'completed' => (clone $base)->where('status','completed')->count(),
        'expiring'  => (clone $base)->where('status','!=','completed')->where(fn($x) => $x->whereNull('revised_contract_expiry')->whereBetween('original_contract_expiry',[now(),now()->addDays(30)])->orWhereBetween('revised_contract_expiry',[now(),now()->addDays(30)]))->count(),
        'expired'   => (clone $base)->where('status','!=','completed')->where(fn($x) => $x->whereNull('revised_contract_expiry')->where('original_contract_expiry','<',now())->orWhere('revised_contract_expiry','<',now()))->count(),
        'ongoing'   => (clone $base)->where('status','ongoing')->where(fn($x) => $x->whereNull('revised_contract_expiry')->where('original_contract_expiry','>=',now())->orWhere('revised_contract_expiry','>=',now()))->count(),
    ];

    $allInCharge = \App\Models\Project::pluck('in_charge')->unique()->filter()->sort()->values();

    $sortUrl = fn($col) => request()->fullUrlWithQuery(['sort'=>$col,'dir'=>($sortCol===$col && $sortDir==='asc'?'desc':'asc'),'page'=>1]);
    $chipUrl = fn($val) => request()->fullUrlWithQuery(['status'=>$val,'page'=>1]);
@endphp

<div class="space-y-4" style="padding:2rem; max-width:1400px; margin:0 auto;">

    {{-- Chips --}}
    <div class="fade-up" style="display:flex; align-items:center; gap:0.45rem; flex-wrap:wrap;">
        @foreach([
            ['all','All',null,null],
            ['active','Active','fa-hourglass-start','#06b6d4'],
            ['completed','Completed','fa-check-circle','#22c55e'],
            ['expiring','Expiring','fa-clock','#eab308'],
            ['expired','Expired','fa-times-circle','#ef4444'],
        ] as [$val,$label,$icon,$col])
        <a href="{{ $chipUrl($val) }}" class="chip {{ $status===$val ? 'chip-active':'chip-default' }}">
            @if($icon)<i class="fas {{ $icon }}" style="font-size:0.58rem;{{ $status!==$val&&$col?' color:'.$col.';':'' }}"></i>@endif
            {{ $label }}
            <span style="min-width:18px; height:18px; padding:0 4px; border-radius:99px; font-size:0.62rem; font-weight:800; display:inline-flex; align-items:center; justify-content:center;
                background:{{ $status===$val ? 'rgba(255,255,255,0.25)':'rgba(249,115,22,0.1)' }};
                color:{{ $status===$val ? 'white':'#ea580c' }};">{{ $counts[$val] }}</span>
        </a>
        @endforeach
        @if($search || $inCharge || $slipFilter)
            <span style="font-size:0.72rem; color:#9ca3af; padding:0 0.25rem;">·</span>
            <a href="{{ request()->fullUrlWithQuery(['search'=>'','in_charge'=>'','slip'=>'','page'=>1]) }}"
               style="display:inline-flex; align-items:center; gap:0.3rem; font-size:0.72rem; font-weight:600; color:#dc2626; text-decoration:none;">
                <i class="fas fa-times" style="font-size:0.6rem;"></i> Clear filters
            </a>
        @endif
    </div>

    {{-- Main card --}}
    <div class="main-card fade-up-2">

        {{-- Filter bar --}}
        <div class="filter-bar">
            <form method="GET" action="{{ route('admin.reports.index') }}">
                <input type="hidden" name="status"   value="{{ $status }}">
                <input type="hidden" name="sort"     value="{{ $sortCol }}">
                <input type="hidden" name="dir"      value="{{ $sortDir }}">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div style="display:grid; grid-template-columns:1fr auto auto auto; gap:0.65rem; align-items:end;">

                    <div style="position:relative;">
                        <i class="fas fa-search" style="position:absolute; left:0.72rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.7rem; pointer-events:none;"></i>
                        <input type="text" name="search" id="searchInput" value="{{ $search }}" class="filter-input" placeholder="Search title, contractor, location…">
                    </div>

                    <select name="in_charge" class="filter-select" style="width:auto; min-width:138px;" onchange="this.form.submit()">
                        <option value="">All In Charge</option>
                        @foreach($allInCharge as $ic)
                            <option value="{{ $ic }}" {{ $inCharge===$ic?'selected':'' }}>{{ $ic }}</option>
                        @endforeach
                    </select>

                    <select name="slip" class="filter-select" style="width:auto; min-width:138px;" onchange="this.form.submit()">
                        <option value="">All Slippage</option>
                        <option value="ahead"  {{ $slipFilter==='ahead' ?'selected':'' }}>Ahead</option>
                        <option value="behind" {{ $slipFilter==='behind'?'selected':'' }}>Behind</option>
                        <option value="on"     {{ $slipFilter==='on'    ?'selected':'' }}>On Schedule</option>
                    </select>

                    <button type="submit"
                        style="display:inline-flex; align-items:center; gap:0.35rem; padding:0.575rem 1.1rem; background:var(--orange-500); color:white; border:none; border-radius:9px; font-size:0.83rem; font-weight:600; cursor:pointer; font-family:'Instrument Sans',sans-serif; transition:background 0.18s; white-space:nowrap;"
                        onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='#f97316'">
                        <i class="fas fa-search" style="font-size:0.68rem;"></i> Search
                    </button>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;">
            <table class="proj-table">
                <thead>
                    <tr>
                        @php
                            $cols = [
                                ['project_title','Project Title',true],
                                ['in_charge','In Charge',true],
                                ['contractor','Contractor',true],
                                ['location','Location',false],
                                ['original_contract_expiry','Expiry',true],
                                [null,'Progress',false],
                                ['slippage','Slippage',true],
                                [null,'Status',false],
                            ];
                        @endphp
                        @foreach($cols as [$c,$lbl,$sort])
                        <th class="{{ $sort?'sortable':'' }} {{ $sort&&$sortCol===$c?'sort-active':'' }}"
                            @if($sort) onclick="window.location='{{ $sortUrl($c) }}'" @endif>
                            {{ $lbl }}
                            @if($sort)
                            <i class="fas sort-icon {{ $sortCol===$c?($sortDir==='asc'?'fa-sort-up':'fa-sort-down'):'fa-sort' }}"></i>
                            @endif
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    @php
                        $expiry     = $project->revised_contract_expiry ?? $project->original_contract_expiry;
                        $daysLeft   = (int)$today->diffInDays($expiry, false);
                        $sl         = (float)($project->slippage ?? 0);
                        $sk         = $project->status==='completed'?'completed':($daysLeft<0?'expired':($daysLeft<30?'expiring':($project->status==='ongoing'?'active':'ongoing')));
                    @endphp
                    <tr>
                        <td>
                            <div style="font-weight:700; color:var(--ink); max-width:190px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $project->project_title }}</div>
                            <div style="font-size:0.68rem; color:#9ca3af; margin-top:2px;">{{ $project->updated_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.45rem;">
                                <div style="width:26px; height:26px; border-radius:7px; background:rgba(249,115,22,0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0; font-family:'Syne',sans-serif; font-weight:800; font-size:0.68rem; color:var(--orange-600);">{{ strtoupper(substr($project->in_charge,0,1)) }}</div>
                                <span style="color:var(--ink); font-weight:500; white-space:nowrap;">{{ $project->in_charge }}</span>
                            </div>
                        </td>
                        <td style="max-width:140px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $project->contractor }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.3rem; color:var(--ink-muted);">
                                <i class="fas fa-map-marker-alt" style="color:rgba(249,115,22,0.45); font-size:0.65rem; flex-shrink:0;"></i>
                                {{ $project->location }}
                            </div>
                        </td>
                        <td style="white-space:nowrap;">
                            <div style="font-size:0.82rem; font-weight:600; color:{{ $daysLeft<0?'#dc2626':($daysLeft<30?'#b45309':'var(--ink)') }};">
                                <i class="fas {{ $daysLeft<0?'fa-times-circle':($daysLeft<30?'fa-clock':'fa-calendar-check') }}" style="font-size:0.58rem; margin-right:2px;"></i>
                                {{ $expiry->format('M d, Y') }}
                            </div>
                            @if($project->revised_contract_expiry)
                                <div style="font-size:0.63rem; color:#9ca3af; margin-top:1px;"><i class="fas fa-rotate" style="font-size:0.5rem;"></i> Revised</div>
                            @endif
                        </td>
                        <td style="min-width:120px;">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div style="flex:1; height:6px; background:rgba(249,115,22,0.08); border-radius:99px; overflow:hidden; min-width:60px;">
                                    <div style="height:100%; border-radius:99px; background:#3b82f6; width:{{ $project->work_done }}%;"></div>
                                </div>
                                <span style="font-size:0.72rem; font-weight:700; color:var(--ink); flex-shrink:0; min-width:28px; text-align:right;">{{ $project->work_done }}%</span>
                            </div>
                        </td>
                        <td>
                            @if($sl > 0) <span class="slip-pill slip-ahead"><i class="fas fa-arrow-up" style="font-size:0.55rem;"></i> +{{ $sl }}%</span>
                            @elseif($sl < 0) <span class="slip-pill slip-behind"><i class="fas fa-arrow-down" style="font-size:0.55rem;"></i> {{ $sl }}%</span>
                            @else <span class="slip-pill slip-on"><i class="fas fa-minus" style="font-size:0.55rem;"></i> 0%</span>
                            @endif
                        </td>
                        <td>
                            @if($sk==='completed') <span class="badge badge-completed"><i class="fas fa-check-circle" style="font-size:0.55rem;"></i> Completed</span>
                            @elseif($sk==='expired') <span class="badge badge-expired"><i class="fas fa-times-circle" style="font-size:0.55rem;"></i> Expired</span>
                            @elseif($sk==='expiring') <span class="badge badge-expiring"><i class="fas fa-clock" style="font-size:0.55rem;"></i> Expiring</span>
                            @elseif($sk==='active') <span class="badge badge-active"><i class="fas fa-hourglass-start" style="font-size:0.55rem;"></i> Active</span>
                            @else <span class="badge badge-ongoing"><i class="fas fa-spinner" style="font-size:0.55rem;"></i> Ongoing</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div style="width:56px; height:56px; background:rgba(249,115,22,0.07); border-radius:14px; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
                                    <i class="fas fa-folder-open" style="font-size:1.4rem; color:rgba(249,115,22,0.35);"></i>
                                </div>
                                <p style="font-family:'Syne',sans-serif; font-weight:800; font-size:1rem; color:var(--ink-muted); margin-bottom:0.3rem;">No projects found</p>
                                <p style="font-size:0.82rem; color:#9ca3af;">Try adjusting your search or filters</p>
                                @if($search || $inCharge || $slipFilter || $status !== 'all')
                                <a href="{{ route('admin.reports.index') }}"
                                   style="display:inline-flex; align-items:center; gap:0.4rem; margin-top:1rem; padding:0.55rem 1.1rem; background:var(--orange-500); color:white; border-radius:9px; font-size:0.82rem; font-weight:600; text-decoration:none;">
                                    <i class="fas fa-times"></i> Clear all filters
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrap">
            <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
                <span class="page-info">
                    @if($projects->firstItem())
                        Showing <strong>{{ $projects->firstItem() }}</strong>–<strong>{{ $projects->lastItem() }}</strong> of <strong>{{ $projects->total() }}</strong> projects
                    @else
                        No results
                    @endif
                </span>
                <div style="display:flex; align-items:center; gap:0.4rem;">
                    <span style="font-size:0.72rem; color:#9ca3af;">Per page:</span>
                    <select class="per-page-select" onchange="changePerPage(this.value)">
                        @foreach([10,25,50] as $pp)
                            <option value="{{ $pp }}" {{ $perPage==$pp?'selected':'' }}>{{ $pp }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($projects->hasPages())
            <div class="page-links">
                {{-- Prev --}}
                @if($projects->onFirstPage())
                    <span class="page-btn disabled"><i class="fas fa-chevron-left" style="font-size:0.62rem;"></i></span>
                @else
                    <a href="{{ $projects->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left" style="font-size:0.62rem;"></i></a>
                @endif

                {{-- Numbers --}}
                @php
                    $cur  = $projects->currentPage();
                    $last = $projects->lastPage();
                    $pages = []; $prev = null;
                    $raw = [];
                    for ($i=1;$i<=$last;$i++) { if($i===1||$i===$last||abs($i-$cur)<=2) $raw[]=$i; }
                    foreach($raw as $pg) { if($prev!==null&&$pg-$prev>1) $pages[]='…'; $pages[]=$pg; $prev=$pg; }
                @endphp
                @foreach($pages as $pg)
                    @if($pg==='…')
                        <span class="page-btn ellipsis">…</span>
                    @elseif($pg==$cur)
                        <span class="page-btn active">{{ $pg }}</span>
                    @else
                        <a href="{{ $projects->url($pg) }}" class="page-btn">{{ $pg }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($projects->hasMorePages())
                    <a href="{{ $projects->nextPageUrl() }}" class="page-btn"><i class="fas fa-chevron-right" style="font-size:0.62rem;"></i></a>
                @else
                    <span class="page-btn disabled"><i class="fas fa-chevron-right" style="font-size:0.62rem;"></i></span>
                @endif
            </div>
            @endif
        </div>

    </div>
</div>

<script>
function changePerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}
document.getElementById('searchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); this.closest('form').submit(); }
});
</script>

</x-app-layout>
