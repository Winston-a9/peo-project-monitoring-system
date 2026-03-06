<x-app-layout>
<x-slot name="header">
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="font-bold text-2xl flex items-center gap-3"
            style="font-family: 'Syne', sans-serif; letter-spacing: -0.03em; color: #2c3e4f;">
            <span style="background: #f97316; padding: 0.45rem 0.6rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(249,115,22,0.35);">
                <i class="fas fa-layer-group text-white text-base"></i>
            </span>
            Project Registry
        </h2>
        <p class="text-sm mt-1" style="color: #6b4f35;">Manage and monitor all projects</p>
    </div>
    <a href="{{ route('admin.projects.create') }}"
       style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.7rem 1.5rem; background:linear-gradient(135deg,#f97316,#ea580c); color:white; font-weight:600; font-size:0.855rem; border-radius:10px; text-decoration:none; box-shadow:0 4px 14px rgba(249,115,22,0.35); transition:all 0.2s;"
       onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 20px rgba(249,115,22,0.5)'"
       onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 14px rgba(249,115,22,0.35)'">
        <i class="fas fa-plus" style="font-size:0.75rem;"></i> New Project
    </a>
</div>
</x-slot>

<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    * { font-family: 'Instrument Sans', sans-serif; }

    :root {
        --o50:  #fff7ed;
        --o100: #ffedd5;
        --o200: #fed7aa;
        --o500: #f97316;
        --o600: #ea580c;
        --o700: #c2410c;
        --ink:  #1a0f00;
        --muted:#6b4f35;
        --border: rgba(249,115,22,0.12);
        --surface: #fffcf9;
    }

    /* Match dashboard card style */
    .pane {
        background: white;
        border: 1px solid rgba(249,115,22,0.12);
        border-radius: 14px;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }

    .stat-chip {
        background: white;
        border: 1px solid rgba(249,115,22,0.12);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        display: flex; align-items: center; gap: 0.9rem;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .stat-chip:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 36px rgba(249,115,22,0.1), 0 4px 10px rgba(0,0,0,0.05);
        border-color: rgba(249,115,22,0.25);
    }
    .stat-icon {
        width: 42px; height: 42px; border-radius: 11px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; flex-shrink: 0;
    }
    .stat-val { font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; color:var(--ink); line-height:1; letter-spacing:-0.03em; }
    .stat-lbl { font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--muted); margin-top:3px; }

    /* Filter inputs */
    .fi {
        padding: 0.6rem 1rem;
        border: 1.5px solid rgba(0,0,0,0.09);
        border-radius: 9px;
        font-size: 0.845rem;
        color: var(--ink);
        background: white;
        outline: none;
        width: 100%;
        font-family: 'Instrument Sans', sans-serif;
        transition: border-color 0.18s, box-shadow 0.18s;
    }
    .fi:focus { border-color: var(--o500); box-shadow: 0 0 0 3px rgba(249,115,22,0.1); }

    /* Table */
    .tbl { width:100%; border-collapse:collapse; }
    .tbl thead tr { background: var(--o50); border-bottom: 1px solid var(--border); }
    .tbl thead th {
        padding: 0.7rem 1.1rem;
        text-align: left;
        font-size: 0.655rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.09em;
        color: var(--muted); white-space: nowrap;
    }
    .tbl tbody tr {
        border-bottom: 1px solid rgba(249,115,22,0.06);
        transition: background 0.15s;
    }
    .tbl tbody tr:last-child { border-bottom: none; }
    .tbl tbody tr:hover { background: #fff8f2; }
    .tbl tbody tr:hover td:first-child { border-left: 3px solid var(--o500); }
    .tbl tbody td:first-child { border-left: 3px solid transparent; transition: border-color 0.15s; }
    .tbl tbody td { padding: 0.85rem 1.1rem; font-size: 0.845rem; color: #5a3a1a; vertical-align: middle; }

    /* Badges */
    .bdg { display:inline-flex; align-items:center; gap:0.3rem; padding:3px 9px; border-radius:99px; font-size:0.695rem; font-weight:700; border:1px solid; letter-spacing:0.02em; }
    .bdg-green { background:#f0fdf4; color:#15803d; border-color:#bbf7d0; }
    .bdg-amber { background:#fffbeb; color:#b45309; border-color:#fde68a; }
    .bdg-blue  { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
    .bdg-red   { background:#fef2f2; color:#b91c1c; border-color:#fecaca; }

    /* Action buttons */
    .abtn { display:inline-flex; align-items:center; gap:0.28rem; padding:4px 9px; border-radius:7px; font-size:0.72rem; font-weight:600; text-decoration:none; border:1px solid; cursor:pointer; background:none; font-family:'Instrument Sans',sans-serif; transition:all 0.15s; }
    .abtn-v { color:#1d4ed8; border-color:#bfdbfe; background:#eff6ff; }
    .abtn-v:hover { background:#1d4ed8; color:white; border-color:#1d4ed8; }
    .abtn-e { color:#c2410c; border-color:#fed7aa; background:#fff7ed; }
    .abtn-e:hover { background:#f97316; color:white; border-color:#f97316; }
    .abtn-d { color:#b91c1c; border-color:#fecaca; background:#fef2f2; }
    .abtn-d:hover { background:#dc2626; color:white; border-color:#dc2626; }

    @keyframes rowIn { from{opacity:0;transform:translateX(-6px);} to{opacity:1;transform:translateX(0);} }
    .project-row { animation: rowIn 0.3s ease both; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

    /* Modal */
    .modal-bg { position:fixed; inset:0; background:rgba(26,15,0,0.5); backdrop-filter:blur(6px); display:flex; align-items:center; justify-content:center; z-index:1000; }
    .modal-box { background:white; border-radius:14px; padding:2rem; max-width:360px; width:90%; box-shadow:0 32px 80px rgba(0,0,0,0.18); animation:rowIn 0.22s ease; border:1px solid rgba(249,115,22,0.12); }
</style>

<div style="display:flex; flex-direction:column; gap:1.25rem;">

@if($projects->count())

    {{-- ── Stats row ── --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:1rem;">
    @php
        $total     = $projects->total();
        $ongoing   = \App\Models\Project::where('status','ongoing')->count();
        $completed = \App\Models\Project::where('status','completed')->count();
        $expired   = \App\Models\Project::where('status','expired')->count();
    @endphp

    <div class="stat-chip">
        <div class="stat-icon" style="background:rgba(249,115,22,0.1);">
            <i class="fas fa-folder" style="color:#f97316;"></i>
        </div>
        <div>
            <div class="stat-val">{{ $total }}</div>
            <div class="stat-lbl">Total Projects</div>
        </div>
    </div>

    <div class="stat-chip">
        <div class="stat-icon" style="background:rgba(59,130,246,0.1);">
            <i class="fas fa-spinner" style="color:#2563eb;"></i>
        </div>
        <div>
            <div class="stat-val">{{ $ongoing }}</div>
            <div class="stat-lbl">Ongoing</div>
        </div>
    </div>

    <div class="stat-chip">
        <div class="stat-icon" style="background:rgba(34,197,94,0.1);">
            <i class="fas fa-check-circle" style="color:#16a34a;"></i>
        </div>
        <div>
            <div class="stat-val">{{ $completed }}</div>
            <div class="stat-lbl">Completed</div>
        </div>
    </div>

    <div class="stat-chip">
        <div class="stat-icon" style="background:rgba(239,68,68,0.1);">
            <i class="fas fa-times-circle" style="color:#dc2626;"></i>
        </div>
        <div>
            <div class="stat-val">{{ $expired }}</div>
            <div class="stat-lbl">Expired</div>
        </div>
    </div>
</div>

    {{-- ── Main Table Pane ── --}}
    <div class="pane">

        {{-- Filter bar --}}
        <div style="padding:1.1rem 1.25rem; background:var(--surface); border-bottom:1px solid var(--border); display:flex; flex-wrap:wrap; gap:0.75rem; align-items:flex-end;">

            {{-- Search --}}
            <div style="flex:2; min-width:180px; position:relative;">
                <i class="fas fa-search" style="position:absolute; left:0.75rem; top:50%; transform:translateY(-50%); color:var(--muted); font-size:0.7rem; pointer-events:none;"></i>
                <input type="text" id="searchInput" class="fi" style="padding-left:2.2rem;" placeholder="Search title, location, contractor…">
            </div>

            {{-- In Charge --}}
            <div style="flex:1; min-width:150px;">
                <select id="inChargeFilter" class="fi" style="appearance:none; background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2378461e' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E\"); background-position:right 0.6rem center; background-repeat:no-repeat; background-size:1em; padding-right:2rem; cursor:pointer;">
                    <option value="">All In Charge</option>
                    @foreach($projects->pluck('in_charge')->unique()->sort() as $ic)
                        <option value="{{ $ic }}">{{ $ic }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div style="flex:1; min-width:130px;">
                <select id="statusFilter" class="fi" style="appearance:none; background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2378461e' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E\"); background-position:right 0.6rem center; background-repeat:no-repeat; background-size:1em; padding-right:2rem; cursor:pointer;">
                    <option value="">All Status</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                    <option value="expired">Expired</option>
                </select>
            </div>

            {{-- Date --}}
            <div style="flex:1; min-width:130px;">
                <select id="dateFilter" class="fi" style="appearance:none; background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2378461e' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E\"); background-position:right 0.6rem center; background-repeat:no-repeat; background-size:1em; padding-right:2rem; cursor:pointer;">
                    <option value="">All Dates</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="older">Older</option>
                </select>
            </div>

            {{-- Reset --}}
            <button onclick="resetFilters()" style="padding:0.6rem 1rem; border:1.5px solid rgba(0,0,0,0.09); border-radius:9px; font-size:0.815rem; font-weight:600; color:var(--muted); background:white; cursor:pointer; display:inline-flex; align-items:center; gap:0.4rem; font-family:'Instrument Sans',sans-serif; transition:all 0.18s; white-space:nowrap;"
                onmouseover="this.style.borderColor='#f97316';this.style.color='#ea580c';"
                onmouseout="this.style.borderColor='rgba(0,0,0,0.09)';this.style.color='var(--muted)';">
                <i class="fas fa-rotate-left" style="font-size:0.7rem;"></i> Reset
            </button>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project Title</th>
                        <th>In Charge</th>
                        <th>Location</th>
                        <th>Contractor</th>
                        <th>Slippage</th>
                        <th>Status</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="projectsTable">
                    @foreach($projects as $i => $project)
                    @php $slip = (float)($project->slippage ?? 0); @endphp
                    <tr class="project-row"
                        data-title="{{ strtolower($project->project_title) }}"
                        data-location="{{ strtolower($project->location) }}"
                        data-contractor="{{ strtolower($project->contractor) }}"
                        data-in-charge="{{ strtolower($project->in_charge) }}"
                        data-status="{{ $project->status }}"
                        data-date="{{ $project->date_started->format('Y-m-d') }}"
                        style="animation-delay:{{ $loop->index * 0.03 }}s">

                        <td style="color:#c4956a; font-weight:600; font-size:0.75rem; width:40px;">
                            {{ $projects->firstItem() + $loop->index }}
                        </td>

                        <td>
                            <p style="font-weight:700; color:var(--ink); font-size:0.855rem; margin-bottom:1px;">{{ $project->project_title }}</p>
                            <p style="font-size:0.72rem; color:#c4956a;">Started {{ $project->date_started->format('M d, Y') }}</p>
                        </td>

                        <td>
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div style="width:26px; height:26px; background:var(--o100); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:0.65rem; font-weight:700; color:var(--o700); flex-shrink:0;">
                                    {{ strtoupper(substr($project->in_charge, 0, 1)) }}
                                </div>
                                <span style="font-size:0.845rem;">{{ $project->in_charge }}</span>
                            </div>
                        </td>

                        <td>
                            <span style="display:inline-flex; align-items:center; gap:0.3rem; font-size:0.835rem;">
                                <i class="fas fa-map-marker-alt" style="color:#c4956a; font-size:0.65rem;"></i>
                                {{ $project->location }}
                            </span>
                        </td>

                        <td style="font-size:0.835rem;">{{ $project->contractor }}</td>

                        <td>
                            @if($slip < 0)
                                <span class="bdg bdg-amber"><i class="fas fa-arrow-down" style="font-size:0.6rem;"></i>{{ number_format($slip,2) }}%</span>
                            @elseif($slip > 0)
                                <span class="bdg bdg-green"><i class="fas fa-arrow-up" style="font-size:0.6rem;"></i>+{{ number_format($slip,2) }}%</span>
                            @else
                                <span class="bdg bdg-blue"><i class="fas fa-equals" style="font-size:0.6rem;"></i>On Track</span>
                            @endif
                        </td>

                        <td>
                            @if($project->status === 'completed')
                                <span class="bdg bdg-green"><i class="fas fa-check-circle" style="font-size:0.6rem;"></i>Completed</span>
                            @elseif($project->status === 'expired')
                                <span class="bdg bdg-red"><i class="fas fa-times-circle" style="font-size:0.6rem;"></i>Expired</span>
                            @else
                                <span class="bdg bdg-blue"><i class="fas fa-circle" style="font-size:0.45rem; animation:pulse 2s infinite;"></i>Ongoing</span>
                            @endif
                        </td>

                        <td>
                            <div style="display:flex; align-items:center; justify-content:center; gap:0.35rem;">
                                <a href="{{ route('admin.projects.show', $project) }}" class="abtn abtn-v" title="View">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('admin.projects.edit', $project) }}" class="abtn abtn-e" title="Edit">
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                                <button onclick="confirmDelete({{ $project->id }}, '{{ addslashes($project->project_title) }}')" class="abtn abtn-d" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
<div style="padding:1rem 1.25rem; border-top:1px solid var(--border); background:var(--surface); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;">

    {{-- Left: info + per page --}}
    <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
        <p style="font-size:0.78rem; color:var(--muted);">
            Showing <strong>{{ $projects->firstItem() }}</strong>–<strong>{{ $projects->lastItem() }}</strong> of <strong>{{ $projects->total() }}</strong> projects
        </p>
        <div style="display:flex; align-items:center; gap:0.4rem;">
            <span style="font-size:0.72rem; color:#9ca3af;">Per page:</span>
            <select onchange="changePerPage(this.value)"
                style="padding:0.3rem 1.6rem 0.3rem 0.6rem; border:1.5px solid rgba(0,0,0,0.09); border-radius:8px; font-size:0.78rem; color:var(--ink); background:white; outline:none; appearance:none; cursor:pointer; font-family:'Instrument Sans',sans-serif;
                background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b4f35' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E\");
                background-position:right 0.35rem center; background-repeat:no-repeat; background-size:0.9em;">
                @foreach([10, 25, 50] as $pp)
                    <option value="{{ $pp }}" {{ request('per_page', 10) == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Right: page buttons --}}
    @if($projects->hasPages())
    <div style="display:flex; align-items:center; gap:0.3rem; flex-wrap:wrap;">

        {{-- Prev --}}
        @if($projects->onFirstPage())
            <span style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 0.5rem; border-radius:8px; font-size:0.8rem; border:1.5px solid rgba(0,0,0,0.08); background:white; color:#d1d5db; pointer-events:none;">
                <i class="fas fa-chevron-left" style="font-size:0.6rem;"></i>
            </span>
        @else
            <a href="{{ $projects->previousPageUrl() }}"
               style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 0.5rem; border-radius:8px; font-size:0.8rem; font-weight:600; border:1.5px solid rgba(0,0,0,0.09); background:white; color:var(--muted); text-decoration:none; transition:all 0.15s;"
               onmouseover="this.style.borderColor='#f97316';this.style.color='#ea580c'"
               onmouseout="this.style.borderColor='rgba(0,0,0,0.09)';this.style.color='var(--muted)'">
                <i class="fas fa-chevron-left" style="font-size:0.6rem;"></i>
            </a>
        @endif

        {{-- Page numbers --}}
        @php
            $cur  = $projects->currentPage();
            $last = $projects->lastPage();
            $pages = [];
            $prev = null;
            $raw = [];
            for ($i = 1; $i <= $last; $i++) {
                if ($i === 1 || $i === $last || abs($i - $cur) <= 2) $raw[] = $i;
            }
            foreach ($raw as $pg) {
                if ($prev !== null && $pg - $prev > 1) $pages[] = '…';
                $pages[] = $pg;
                $prev = $pg;
            }
        @endphp

        @foreach($pages as $pg)
            @if($pg === '…')
                <span style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; font-size:0.8rem; color:#9ca3af;">…</span>
            @elseif($pg == $cur)
                <span style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 0.5rem; border-radius:8px; font-size:0.8rem; font-weight:700; background:#f97316; border:1.5px solid #f97316; color:white; box-shadow:0 2px 8px rgba(249,115,22,0.3);">
                    {{ $pg }}
                </span>
            @else
                <a href="{{ $projects->url($pg) }}"
                   style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 0.5rem; border-radius:8px; font-size:0.8rem; font-weight:600; border:1.5px solid rgba(0,0,0,0.09); background:white; color:var(--muted); text-decoration:none; transition:all 0.15s;"
                   onmouseover="this.style.borderColor='#f97316';this.style.color='#ea580c';this.style.background='#fff7ed'"
                   onmouseout="this.style.borderColor='rgba(0,0,0,0.09)';this.style.color='var(--muted)';this.style.background='white'">
                    {{ $pg }}
                </a>
            @endif
        @endforeach

        {{-- Next --}}
        @if($projects->hasMorePages())
            <a href="{{ $projects->nextPageUrl() }}"
               style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 0.5rem; border-radius:8px; font-size:0.8rem; font-weight:600; border:1.5px solid rgba(0,0,0,0.09); background:white; color:var(--muted); text-decoration:none; transition:all 0.15s;"
               onmouseover="this.style.borderColor='#f97316';this.style.color='#ea580c'"
               onmouseout="this.style.borderColor='rgba(0,0,0,0.09)';this.style.color='var(--muted)'">
                <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i>
            </a>
        @else
            <span style="display:inline-flex; align-items:center; justify-content:center; min-width:32px; height:32px; padding:0 0.5rem; border-radius:8px; font-size:0.8rem; border:1.5px solid rgba(0,0,0,0.08); background:white; color:#d1d5db; pointer-events:none;">
                <i class="fas fa-chevron-right" style="font-size:0.6rem;"></i>
            </span>
        @endif

    </div>
    @endif

    </div>

    </div>

@else
    {{-- Empty state --}}
    <div class="pane" style="padding:5rem 2rem; text-align:center;">
        <div style="width:72px; height:72px; background:var(--o50); border:1.5px dashed var(--o200); border-radius:18px; display:flex; align-items:center; justify-content:center; margin:0 auto 1.5rem;">
            <i class="fas fa-inbox" style="color:var(--o500); font-size:1.6rem;"></i>
        </div>
        <p style="font-family:'Syne',sans-serif;,serif; font-size:1.3rem; color:var(--ink); margin-bottom:0.5rem;">No projects yet</p>
        <p style="color:var(--muted); font-size:0.875rem; margin-bottom:1.75rem;">Start by creating your first project entry.</p>
        <a href="{{ route('admin.projects.create') }}"
           style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.7rem 1.5rem; background:linear-gradient(135deg,#f97316,#ea580c); color:white; font-weight:600; font-size:0.875rem; border-radius:10px; text-decoration:none; box-shadow:0 4px 16px rgba(249,115,22,0.35);">
            <i class="fas fa-plus"></i> Create First Project
        </a>
    </div>
@endif

</div>

{{-- Delete Modal --}}
<div id="deleteModal" class="modal-bg" style="display:none;" onclick="if(event.target===this)closeModal()">
    <div class="modal-box">
        <div style="text-align:center;">
            <div style="width:52px; height:52px; background:#fef2f2; border-radius:13px; display:flex; align-items:center; justify-content:center; margin:0 auto 1.1rem;">
                <i class="fas fa-trash" style="color:#dc2626; font-size:1.1rem;"></i>
            </div>
            <p style="font-family:'Syne',sans-serif;,serif; font-size:1.15rem; color:var(--ink); margin-bottom:0.35rem;">Delete Project?</p>
            <p id="deleteProjectName" style="color:var(--muted); font-size:0.845rem; margin-bottom:0.25rem;"></p>
            <p style="color:#b91c1c; font-size:0.78rem; margin-bottom:1.5rem; background:#fef2f2; padding:0.4rem 0.75rem; border-radius:6px; display:inline-block;">This action cannot be undone.</p>
            <div style="display:flex; gap:0.65rem; justify-content:center;">
                <button onclick="closeModal()" style="padding:0.6rem 1.4rem; border:1.5px solid rgba(0,0,0,0.1); border-radius:9px; font-weight:600; font-size:0.845rem; cursor:pointer; background:white; color:var(--ink); font-family:'Instrument Sans',sans-serif;">
                    Cancel
                </button>
                <button id="confirmDeleteBtn" style="padding:0.6rem 1.4rem; border-radius:9px; background:#dc2626; color:white; font-weight:600; font-size:0.845rem; cursor:pointer; border:none; font-family:'Instrument Sans',sans-serif; display:inline-flex; align-items:center; gap:0.4rem;">
                    <i class="fas fa-trash" style="font-size:0.75rem;"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }
</style>

<script>
    const searchInput    = document.getElementById('searchInput');
    const inChargeFilter = document.getElementById('inChargeFilter');
    const statusFilter   = document.getElementById('statusFilter');
    const dateFilter     = document.getElementById('dateFilter');
    const rows           = document.querySelectorAll('.project-row');

    function filterProjects() {
        const s   = searchInput.value.toLowerCase();
        const ic  = inChargeFilter.value.toLowerCase();
        const st  = statusFilter.value.toLowerCase();
        const dt  = dateFilter.value;
        const now = new Date();

        rows.forEach(row => {
            const ms = !s  || row.dataset.title.includes(s) || row.dataset.location.includes(s) || row.dataset.contractor.includes(s);
            const mi = !ic || row.dataset.inCharge === ic;
            const mst = !st || row.dataset.status === st;
            let   md  = true;
            if (dt) {
                const diff = Math.floor((now - new Date(row.dataset.date)) / 86400000);
                md = dt==='today'?diff===0 : dt==='week'?diff<7 : dt==='month'?diff<30 : diff>=30;
            }
            row.style.display = (ms && mi && mst && md) ? '' : 'none';
        });

        const any = [...rows].some(r => r.style.display !== 'none');
        let noRow = document.getElementById('noResultsRow');
        if (!any && !noRow) {
            const tbody = document.getElementById('projectsTable');
            noRow = document.createElement('tr');
            noRow.id = 'noResultsRow';
            noRow.innerHTML = `<td colspan="8" style="padding:3rem; text-align:center; color:var(--muted);">
                <i class="fas fa-search" style="font-size:1.5rem; display:block; margin-bottom:0.6rem; opacity:0.35;"></i>
                No projects match your filters
            </td>`;
            tbody.appendChild(noRow);
        } else if (any && noRow) { noRow.remove(); }
    }

    function resetFilters() {
        searchInput.value = ''; inChargeFilter.value = '';
        statusFilter.value = ''; dateFilter.value = '';
        filterProjects();
    }

    searchInput.addEventListener('keyup', filterProjects);
    inChargeFilter.addEventListener('change', filterProjects);
    statusFilter.addEventListener('change', filterProjects);
    dateFilter.addEventListener('change', filterProjects);

    let pendingDeleteId = null;

    function confirmDelete(id, name) {
        pendingDeleteId = id;
        document.getElementById('deleteProjectName').textContent = `"${name}"`;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
        pendingDeleteId = null;
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
        if (!pendingDeleteId) return;
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = `/admin/projects/${pendingDeleteId}`;
        f.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(f);
        f.submit();
    });
    function changePerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}
</script>

</x-app-layout>