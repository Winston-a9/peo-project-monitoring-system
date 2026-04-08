<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; letter-spacing:-0.03em; color:var(--text-primary); display:flex; align-items:center; gap:0.6rem;">
                <span style="background:#f97316; width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(249,115,22,0.35);">
                    <i class="fas fa-layer-group" style="color:white; font-size:0.85rem;"></i>
                </span>
                Projects
            </h2>
            <p style="color:var(--text-secondary); font-size:0.82rem; margin-top:3px;">Manage and monitor all projects</p>
        </div>
        <div style="display:flex; align-items:center; gap:1.25rem;">
            <a href="{{ route('admin.projects.create') }}"
               style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.6rem 1rem; border:1.5px solid var(--border); border-radius:9px; font-weight:600; font-size:0.825rem; color:var(--text-secondary); text-decoration:none; background:var(--bg-secondary); transition:all 0.2s;"
               onmouseover="this.style.borderColor='#f97316';this.style.color='#ea580c'"
               onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-secondary)'">
                <i class="fas fa-plus"></i> New Project
            </a>
        </div>
    </div>
</x-slot>

@push('styles')
    @vite('resources/css/admin/projects/index.css')
@endpush

@php
    $today      = now();
    $perPage    = in_array((int)request('per_page',10), [10,25,50]) ? (int)request('per_page',10) : 10;
    $search     = request('search','');
    $inCharge   = request('in_charge','');
    $slipFilter = request('slip','');
    $dateFrom   = request('date_from','');
    $dateTo     = request('date_to','');
    $status     = request('status','all');
    $sortCol    = request('sort','updated_at');
    $sortDir    = request('dir','desc') === 'asc' ? 'asc' : 'desc';

    $allowed = ['project_title','contract_id','in_charge','contractor','location','work_done','slippage','original_contract_expiry','updated_at'];
    $sortCol = in_array($sortCol, $allowed) ? $sortCol : 'updated_at';

    $q = \App\Models\Project::query();
    if ($search)   $q->where(fn($x) => $x->where('project_title','like',"%$search%")->orWhere('contract_id','like',"%$search%")->orWhere('contractor','like',"%$search%")->orWhere('location','like',"%$search%"));
    if ($inCharge) $q->where('in_charge', $inCharge);
    if ($slipFilter === 'ahead')  $q->where('slippage','>',0);
    if ($slipFilter === 'behind') $q->where('slippage','<',0);
    if ($slipFilter === 'on')     $q->where('slippage',0);
    if ($dateFrom) $q->where('date_started', '>=', $dateFrom);
    if ($dateTo)   $q->where('date_started', '<=', $dateTo);

    if ($status === 'completed') {
        $q->where('status','completed');
    } elseif ($status === 'active') {
        $q->where('status','ongoing')->where(fn($x) => $x->whereNull('revised_contract_expiry')->where('original_contract_expiry','>',now()->addDays(30))->orWhere('revised_contract_expiry','>',now()->addDays(30)));
    } elseif ($status === 'expiring') {
        $q->where('status','!=','completed')->where(fn($x) => $x->whereNull('revised_contract_expiry')->whereBetween('original_contract_expiry',[now(),now()->addDays(30)])->orWhereBetween('revised_contract_expiry',[now(),now()->addDays(30)]));
    } elseif ($status === 'expired') {
        $q->where(fn($x) => $x
            ->where('status', 'expired')
            ->orWhere(fn($y) => $y
                ->where('status', '!=', 'completed')
                ->where('status', '!=', 'expired')
                ->where(fn($z) => $z
                    ->whereNull('revised_contract_expiry')->where('original_contract_expiry', '<', now())
                    ->orWhere('revised_contract_expiry', '<', now())
                )
            )
        );
    }
    elseif ($status === 'ongoing') {
        $q->where('status','ongoing')->where(fn($x) => $x->whereNull('revised_contract_expiry')->where('original_contract_expiry','>=',now())->orWhere('revised_contract_expiry','>=',now()));
    }

    $projects = $q->orderBy($sortCol,$sortDir)->paginate($perPage)->withQueryString();

    $base = \App\Models\Project::query();
    if ($search)   $base->where(fn($x) => $x->where('project_title','like',"%$search%")->orWhere('contractor','like',"%$search%")->orWhere('location','like',"%$search%"));
    if ($inCharge) $base->where('in_charge',$inCharge);
    if ($slipFilter === 'ahead')  $base->where('slippage','>',0);
    if ($slipFilter === 'behind') $base->where('slippage','<',0);
    if ($slipFilter === 'on')     $base->where('slippage',0);
    if ($dateFrom) $base->where('date_started', '>=', $dateFrom);
    if ($dateTo)   $base->where('date_started', '<=', $dateTo);

    $counts = [
    'all'       => (clone $base)->count(),

    'active'    => (clone $base)
                    ->where('status', 'ongoing')
                    ->where(fn($x) => $x
                        ->whereNull('revised_contract_expiry')->where('original_contract_expiry', '>', now()->addDays(30))
                        ->orWhere('revised_contract_expiry', '>', now()->addDays(30))
                    )->count(),

    'completed' => (clone $base)->where('status', 'completed')->count(),

    'expiring'  => (clone $base)
                    ->where('status', '!=', 'completed')
                    ->where('status', '!=', 'expired')
                    ->where(fn($x) => $x
                        ->whereNull('revised_contract_expiry')->whereBetween('original_contract_expiry', [now(), now()->addDays(30)])
                        ->orWhereBetween('revised_contract_expiry', [now(), now()->addDays(30)])
                    )->count(),

    'expired'   => (clone $base)
                    ->where(fn($x) => $x
                        ->where('status', 'expired')
                        ->orWhere(fn($y) => $y
                            ->whereNotIn('status', ['completed', 'expired'])
                            ->where(fn($z) => $z
                                ->whereNull('revised_contract_expiry')->where('original_contract_expiry', '<', now())
                                ->orWhereNotNull('revised_contract_expiry')->where('revised_contract_expiry', '<', now())
                            )
                        )
                    )->count(),
];

    $allInCharge = \App\Models\Project::pluck('in_charge')->unique()->filter()->sort()->values();
    $sortUrl = fn($col) => request()->fullUrlWithQuery(['sort'=>$col,'dir'=>($sortCol===$col && $sortDir==='asc'?'desc':'asc'),'page'=>1]);
    $chipUrl = fn($val) => request()->fullUrlWithQuery(['status'=>$val,'page'=>1]);
@endphp

<div class="space-y-4">

    {{-- Status chips --}}
    <div class="fade-up" style="display:flex; align-items:center; gap:0.45rem; flex-wrap:wrap;">
        @foreach([
            ['all','All',null,null],
            ['active','On Going','fa-hourglass-start','#06b6d4'],
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
        @if($search || $inCharge || $slipFilter || $dateFrom || $dateTo)
            <span style="font-size:0.72rem; color:#9ca3af; padding:0 0.25rem;">·</span>
            <a href="{{ request()->fullUrlWithQuery(['search'=>'','in_charge'=>'','slip'=>'','date_from'=>'','date_to'=>'','page'=>1]) }}"
               style="display:inline-flex; align-items:center; gap:0.3rem; font-size:0.72rem; font-weight:600; color:#dc2626; text-decoration:none;">
                <i class="fas fa-times" style="font-size:0.6rem;"></i> Clear filters
            </a>
        @endif
    </div>

    {{-- Main card --}}
    <div class="main-card fade-up-2">

        {{-- Filter bar --}}
        <div class="filter-bar">
            <form method="GET" action="{{ route('admin.projects.index') }}">
                <input type="hidden" name="status"   value="{{ $status }}">
                <input type="hidden" name="sort"     value="{{ $sortCol }}">
                <input type="hidden" name="dir"      value="{{ $sortDir }}">
                <input type="hidden" name="per_page" value="{{ $perPage }}">
                <div style="display:grid; grid-template-columns:1fr auto auto auto auto auto; gap:0.65rem; align-items:end;">
                    <div style="position:relative;">
                        <i class="fas fa-search" style="position:absolute; left:0.72rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.7rem; pointer-events:none;"></i>
                        <input type="text" name="search" id="searchInput" value="{{ $search }}" class="filter-input" placeholder="Search title, contract ID, contractor, location…">
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
                    <div style="display:flex; align-items:center; gap:0.3rem;">
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="filter-input" style="width:140px;">
                        <span style="color:#9ca3af; font-size:0.75rem; font-weight:600;">to</span>
                        <input type="date" name="date_to"   value="{{ $dateTo }}"   class="filter-input" style="width:140px;">
                    </div>
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
                                ['contract_id','Contract ID',true],
                                ['in_charge','In Charge',true],
                                ['contractor','Contractor',true],
                                ['location','Location',false],
                                ['original_contract_expiry','Expiry',true],
                                [null,'Progress',false],
                                ['slippage','Slippage',true],
                                [null,'Status',false],
                                [null,'',false],
                            ];
                        @endphp
                        @foreach($cols as [$c,$lbl,$sort])
                        <th class="{{ $sort?'sortable':'' }} {{ $sort&&$sortCol===$c?'sort-active':'' }}"
                            @if($sort) onclick="window.location='{{ $sortUrl($c) }}'" @endif>
                            {{ $lbl }}
                            @if($sort)<i class="fas sort-icon {{ $sortCol===$c?($sortDir==='asc'?'fa-sort-up':'fa-sort-down'):'fa-sort' }}"></i>@endif
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                    @php
                        $expiry   = $project->revised_contract_expiry ?? $project->original_contract_expiry;
                        $daysLeft = (int)$today->diffInDays($expiry->copy()->endOfDay(), false);
                        $sl       = (float)($project->slippage ?? 0);
                        $sk = $project->status==='completed' ? 'completed'
                            : ($project->status==='expired' || $daysLeft < 0 ? 'expired'
                            : ($daysLeft < 30 ? 'expiring'
                            : 'ongoing'));                    @endphp
                    <tr onclick="window.location='{{ route('admin.projects.show', $project) }}'">
                        <td>
                            <div style="font-weight:700; color:var(--ink); max-width:190px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $project->project_title }}</div>
                            <div style="font-size:0.68rem; color:#9ca3af; margin-top:2px;">{{ $project->updated_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center; gap:0.45rem;">
                                <div style="font-family:'Syne',sans-serif; font-weight:800; font-size:0.68rem; color:var(--orange-600);">#</div>
                                <span style="color:var(--ink); font-weight:600; white-space:nowrap;">{{ $project->contract_id }}</span>
                            </div>
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
                            <div style="font-size:0.82rem; font-weight:600; color:{{
                                $sk === 'completed' ? '#16a34a' :
                                ($daysLeft < 0 ? '#dc2626' :
                                ($daysLeft < 30 ? '#b45309' : 'var(--ink)'))
                            }};">
                                <i class="fas {{
                                    $sk === 'completed' ? 'fa-check-circle' :
                                    ($daysLeft < 0 ? 'fa-times-circle' :
                                    ($daysLeft < 30 ? 'fa-clock' : 'fa-calendar-check'))
                                }}" style="font-size:0.58rem; margin-right:2px;"></i>
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
                            @if($sl > 0)     <span class="slip-pill slip-ahead"><i class="fas fa-arrow-up" style="font-size:0.55rem;"></i> +{{ $sl }}%</span>
                            @elseif($sl < 0) <span class="slip-pill slip-behind"><i class="fas fa-arrow-down" style="font-size:0.55rem;"></i> {{ $sl }}%</span>
                            @else            <span class="slip-pill slip-on"><i class="fas fa-minus" style="font-size:0.55rem;"></i> 0%</span>
                            @endif
                        </td>
                        <td>
                            @if($sk==='completed') <span class="badge badge-completed"><i class="fas fa-check-circle" style="font-size:0.55rem;"></i> Completed</span>
                            @elseif($sk==='expired') <span class="badge badge-expired"><i class="fas fa-times-circle" style="font-size:0.55rem;"></i> Expired</span>
                            @elseif($sk==='expiring') <span class="badge badge-expiring"><i class="fas fa-clock" style="font-size:0.55rem;"></i> Expiring</span>
                            @else <span class="badge badge-ongoing"><i class="fas fa-spinner" style="font-size:0.55rem;"></i> Ongoing</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <div style="width:56px; height:56px; background:rgba(249,115,22,0.07); border-radius:14px; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
                                    <i class="fas fa-folder-open" style="font-size:1.4rem; color:rgba(249,115,22,0.35);"></i>
                                </div>
                                <p style="font-family:'Syne',sans-serif; font-weight:800; font-size:1rem; color:var(--ink-muted); margin-bottom:0.3rem;">No projects found</p>
                                <p style="font-size:0.82rem; color:#9ca3af;">Try adjusting your search or filters</p>
                                @if($search || $inCharge || $slipFilter || $status !== 'all')
                                <a href="{{ route('admin.projects.index') }}"
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
                    @else No results @endif
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
                @if($projects->onFirstPage())
                    <span class="page-btn disabled"><i class="fas fa-chevron-left" style="font-size:0.62rem;"></i></span>
                @else
                    <a href="{{ $projects->previousPageUrl() }}" class="page-btn"><i class="fas fa-chevron-left" style="font-size:0.62rem;"></i></a>
                @endif
                @php
                    $cur=$projects->currentPage(); $last=$projects->lastPage();
                    $pages=[]; $prev=null; $raw=[];
                    for($i=1;$i<=$last;$i++){if($i===1||$i===$last||abs($i-$cur)<=2)$raw[]=$i;}
                    foreach($raw as $pg){if($prev!==null&&$pg-$prev>1)$pages[]='…';$pages[]=$pg;$prev=$pg;}
                @endphp
                @foreach($pages as $pg)
                    @if($pg==='…') <span class="page-btn ellipsis">…</span>
                    @elseif($pg==$cur) <span class="page-btn active">{{ $pg }}</span>
                    @else <a href="{{ $projects->url($pg) }}" class="page-btn">{{ $pg }}</a>
                    @endif
                @endforeach
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
@push('scripts')
    @vite('resources/js/admin/projects/index.js')
@endpush
</x-app-layout>