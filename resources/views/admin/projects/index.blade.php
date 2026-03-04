<x-app-layout>
<x-slot name="header">
    <style>
        :root {
            --orange-50:  #fff7ed;
            --orange-100: #ffedd5;
            --orange-500: #f97316;
            --orange-600: #ea580c;
            --orange-700: #c2410c;
            --ink:        #1a0f00;
            --ink-muted:  #6b4f35;
            --surface:    #fffaf5;
            --border:     rgba(249,115,22,0.14);
            --radius:     12px;
        }
    </style>
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; letter-spacing:-0.03em; color:#1a0f00; display:flex; align-items:center; gap:0.6rem;">
                <span style="background:#f97316; width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(249,115,22,0.35);">
                    <i class="fas fa-folder-open" style="color:white; font-size:0.85rem;"></i>
                </span>
                Projects Management
            </h2>
            <p style="color:#6b4f35; font-size:0.82rem; margin-top:3px;">Manage and monitor all projects</p>
        </div>
        <a href="{{ route('admin.projects.create') }}"
           style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.65rem 1.4rem; background:#f97316; color:white; font-weight:600; font-size:0.875rem; border-radius:9px; text-decoration:none; box-shadow:0 3px 14px rgba(249,115,22,0.38); transition:all 0.2s;"
           onmouseover="this.style.background='#ea580c';this.style.transform='translateY(-1px)'"
           onmouseout="this.style.background='#f97316';this.style.transform='translateY(0)'">
            <i class="fas fa-plus"></i> Create Project
        </a>
    </div>
</x-slot>

<style>
    :root {
        --orange-50:  #fff7ed;
        --orange-100: #ffedd5;
        --orange-500: #f97316;
        --orange-600: #ea580c;
        --ink:        #1a0f00;
        --ink-muted:  #6b4f35;
        --border:     rgba(249,115,22,0.14);
    }

    .proj-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
    }
    .proj-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 32px rgba(249,115,22,0.1);
        border-color: rgba(249,115,22,0.28);
    }

    .filter-input {
        width: 100%;
        padding: 0.625rem 1rem 0.625rem 2.4rem;
        border: 1.5px solid rgba(26,15,0,0.1);
        border-radius: 9px;
        font-size: 0.855rem;
        color: var(--ink);
        background: white;
        outline: none;
        font-family: 'Instrument Sans', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .filter-input:focus {
        border-color: var(--orange-500);
        box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
    }

    .filter-select {
        width: 100%;
        padding: 0.625rem 2.5rem 0.625rem 1rem;
        border: 1.5px solid rgba(26,15,0,0.1);
        border-radius: 9px;
        font-size: 0.855rem;
        color: var(--ink);
        background: white;
        outline: none;
        appearance: none;
        cursor: pointer;
        font-family: 'Instrument Sans', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b4f35' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-position: right 0.6rem center;
        background-repeat: no-repeat;
        background-size: 1.1em;
    }
    .filter-select:focus {
        border-color: var(--orange-500);
        box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
    }

    .table-head th {
        padding: 0.75rem 1.25rem;
        background: #fff7ed;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--ink-muted);
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    .table-head th:first-child { border-radius: 0; }

    .project-row td {
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid rgba(249,115,22,0.07);
        font-size: 0.855rem;
        color: var(--ink-muted);
        vertical-align: middle;
    }

    .project-row:last-child td { border-bottom: none; }

    .project-row:hover td { background: #fff7ed; }
    .project-row:hover td:first-child { border-left: 3px solid var(--orange-500); }
    td:first-child { border-left: 3px solid transparent; transition: border-color 0.2s; }

    .badge {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 3px 10px;
        border-radius: 99px;
        font-size: 0.72rem; font-weight: 700;
        border: 1px solid;
    }
    .badge-green  { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
    .badge-red    { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
    .badge-blue   { background:#eff6ff; color:#2563eb; border-color:#bfdbfe; }
    .badge-amber  { background:#fffbeb; color:#d97706; border-color:#fde68a; }
    .badge-orange { background:#fff7ed; color:#ea580c; border-color:#fed7aa; }

    .action-btn {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 5px 10px; border-radius: 7px;
        font-size: 0.75rem; font-weight: 600;
        text-decoration: none; cursor: pointer;
        border: 1px solid; transition: all 0.18s;
        font-family: 'Instrument Sans', sans-serif;
        background: none;
    }
    .btn-view  { color:#2563eb; border-color:#bfdbfe; background:#eff6ff; }
    .btn-view:hover  { background:#2563eb; color:white; border-color:#2563eb; }
    .btn-edit  { color:#ea580c; border-color:#fed7aa; background:#fff7ed; }
    .btn-edit:hover  { background:#f97316; color:white; border-color:#f97316; }
    .btn-del   { color:#dc2626; border-color:#fecaca; background:#fef2f2; }
    .btn-del:hover   { background:#dc2626; color:white; border-color:#dc2626; }

    .reset-btn {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.575rem 1rem;
        border: 1.5px solid rgba(26,15,0,0.1);
        border-radius: 9px;
        font-size: 0.825rem; font-weight: 600;
        color: var(--ink-muted);
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Instrument Sans', sans-serif;
    }
    .reset-btn:hover { border-color: var(--orange-500); color: var(--orange-600); background: var(--orange-50); }

    @keyframes fadeUp {
        from { opacity:0; transform:translateY(12px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .fade-up { animation: fadeUp 0.45s ease both; }

    /* Delete modal */
    .del-modal-bg {
        position:fixed; inset:0;
        background:rgba(0,0,0,0.45);
        backdrop-filter:blur(4px);
        display:flex; align-items:center; justify-content:center;
        z-index:1000;
        animation: fadeUp 0.2s ease;
    }
    .del-modal {
        background:white;
        border-radius:16px;
        padding:2rem;
        max-width:380px; width:90%;
        box-shadow:0 24px 60px rgba(0,0,0,0.15);
        animation: fadeUp 0.25s ease;
    }
</style>

<div class="space-y-5 fade-up">

    @if($projects->count())

    {{-- Stat card --}}
    <div class="proj-card" style="padding:1.25rem 1.5rem; display:flex; align-items:center; justify-content:space-between;">
        <div>
            <p style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); margin-bottom:0.3rem;">Total Projects</p>
            <p style="font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; color:var(--ink); letter-spacing:-0.03em; line-height:1;">{{ $projects->total() }}</p>
        </div>
        <div style="width:44px; height:44px; background:rgba(249,115,22,0.1); border-radius:11px; display:flex; align-items:center; justify-content:center;">
            <i class="fas fa-folder" style="color:var(--orange-500); font-size:1.15rem;"></i>
        </div>
    </div>

    {{-- Filters + Table --}}
    <div class="proj-card" style="overflow:hidden;">

        {{-- Filter bar --}}
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--border); background:#fffaf5;">
            <p style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); margin-bottom:0.875rem; display:flex; align-items:center; gap:0.4rem;">
                <i class="fas fa-filter" style="color:var(--orange-500);"></i> Filter &amp; Search
            </p>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.875rem;">
                <div style="position:relative;">
                    <i class="fas fa-search" style="position:absolute; left:0.75rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.75rem; pointer-events:none;"></i>
                    <input type="text" id="searchInput" class="filter-input" placeholder="Title, location, contractor…">
                </div>
                <div style="position:relative;">
                    <select id="inChargeFilter" class="filter-select">
                        <option value="">All In Charge</option>
                        @foreach($projects->pluck('in_charge')->unique() as $ic)
                            <option value="{{ $ic }}">{{ $ic }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="position:relative;">
                    <select id="dateFilter" class="filter-select">
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="older">Older</option>
                    </select>
                </div>
            </div>
            <button onclick="resetFilters()" class="reset-btn" style="margin-top:0.75rem;">
                <i class="fas fa-redo" style="font-size:0.7rem;"></i> Reset Filters
            </button>
        </div>

        {{-- Table --}}
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr class="table-head">
                        <th style="text-align:left;">Project Title</th>
                        <th style="text-align:left;">In Charge</th>
                        <th style="text-align:left;">Location</th>
                        <th style="text-align:left;">Contractor</th>
                        <th style="text-align:left;">Slippage</th>
                        <th style="text-align:left;">Status</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody id="projectsTable">
                    @foreach($projects as $project)
                    @php $slippage = (float)($project->slippage ?? 0); @endphp
                    <tr class="project-row"
                        data-title="{{ strtolower($project->project_title) }}"
                        data-location="{{ strtolower($project->location) }}"
                        data-contractor="{{ strtolower($project->contractor) }}"
                        data-in-charge="{{ strtolower($project->in_charge) }}"
                        data-date="{{ $project->date_started->format('Y-m-d') }}">

                        <td>
                            <p style="font-weight:700; color:var(--ink); font-size:0.855rem;">{{ $project->project_title }}</p>
                        </td>
                        <td>{{ $project->in_charge }}</td>
                        <td>{{ $project->location }}</td>
                        <td>{{ $project->contractor }}</td>

                        <td>
                            @if($slippage < 0)
                                <span class="badge badge-amber"><i class="fas fa-arrow-down"></i>{{ number_format($slippage,2) }}%</span>
                            @elseif($slippage > 0)
                                <span class="badge badge-green"><i class="fas fa-arrow-up"></i>+{{ number_format($slippage,2) }}%</span>
                            @else
                                <span class="badge badge-blue"><i class="fas fa-equals"></i>On Track</span>
                            @endif
                        </td>

                        <td>
                            @if($project->status === 'completed')
                                <span class="badge badge-green"><i class="fas fa-check-circle"></i>Completed</span>
                            @elseif($project->status === 'expired')
                                <span class="badge badge-red"><i class="fas fa-times-circle"></i>Expired</span>
                            @else
                                <span class="badge badge-blue"><i class="fas fa-spinner fa-spin"></i>Ongoing</span>
                            @endif
                        </td>

                        <td>
                            <div style="display:flex; align-items:center; justify-content:center; gap:0.4rem;">
                                <a href="{{ route('admin.projects.show', $project) }}" class="action-btn btn-view">
                                    <i class="fas fa-eye"></i><span>View</span>
                                </a>
                                <a href="{{ route('admin.projects.edit', $project) }}" class="action-btn btn-edit">
                                    <i class="fas fa-edit"></i><span>Edit</span>
                                </a>
                                <button onclick="confirmDelete({{ $project->id }})" class="action-btn btn-del">
                                    <i class="fas fa-trash"></i><span>Del</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="padding:1rem 1.5rem; border-top:1px solid var(--border); background:#fffaf5;">
            {{ $projects->links() }}
        </div>
    </div>

    @else
    {{-- Empty state --}}
    <div class="proj-card" style="padding:4rem 2rem; text-align:center;">
        <div style="width:64px; height:64px; background:var(--orange-50); border:1px solid var(--border); border-radius:16px; display:flex; align-items:center; justify-content:center; margin:0 auto 1.25rem;">
            <i class="fas fa-inbox" style="color:var(--orange-500); font-size:1.5rem;"></i>
        </div>
        <p style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.15rem; color:var(--ink); margin-bottom:0.4rem;">No projects yet</p>
        <p style="color:var(--ink-muted); font-size:0.875rem; margin-bottom:1.5rem;">Create your first project to get started</p>
        <a href="{{ route('admin.projects.create') }}"
           style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.7rem 1.5rem; background:var(--orange-500); color:white; font-weight:600; font-size:0.875rem; border-radius:9px; text-decoration:none; box-shadow:0 3px 14px rgba(249,115,22,0.35);">
            <i class="fas fa-plus"></i> Create First Project
        </a>
    </div>
    @endif

</div>

<script>
    const searchInput   = document.getElementById('searchInput');
    const inChargeFilter = document.getElementById('inChargeFilter');
    const dateFilter    = document.getElementById('dateFilter');
    const rows          = document.querySelectorAll('.project-row');

    function filterProjects() {
        const s  = searchInput.value.toLowerCase();
        const ic = inChargeFilter.value.toLowerCase();
        const dt = dateFilter.value;
        const today = new Date();

        rows.forEach(row => {
            const matchSearch   = !s  || row.dataset.title.includes(s) || row.dataset.location.includes(s) || row.dataset.contractor.includes(s);
            const matchInCharge = !ic || row.dataset.inCharge === ic;
            let   matchDate     = true;
            if (dt) {
                const d = new Date(row.dataset.date);
                const diff = Math.floor((today - d) / 86400000);
                matchDate = dt === 'today' ? diff === 0 : dt === 'week' ? diff < 7 : dt === 'month' ? diff < 30 : diff >= 30;
            }
            row.style.display = (matchSearch && matchInCharge && matchDate) ? '' : 'none';
        });

        const any = [...rows].some(r => r.style.display !== 'none');
        let noRow = document.getElementById('noResultsRow');
        if (!any && !noRow) {
            const tbody = document.getElementById('projectsTable');
            noRow = document.createElement('tr');
            noRow.id = 'noResultsRow';
            noRow.innerHTML = '<td colspan="7" style="padding:3rem 1.5rem; text-align:center; color:#6b4f35; font-size:0.875rem;"><i class="fas fa-search" style="font-size:1.5rem; margin-bottom:0.6rem; display:block; opacity:0.4;"></i>No projects match your filters</td>';
            tbody.appendChild(noRow);
        } else if (any && noRow) { noRow.remove(); }
    }

    function resetFilters() {
        searchInput.value = ''; inChargeFilter.value = ''; dateFilter.value = '';
        filterProjects();
    }

    searchInput.addEventListener('keyup', filterProjects);
    inChargeFilter.addEventListener('change', filterProjects);
    dateFilter.addEventListener('change', filterProjects);

    function confirmDelete(id) {
        const modal = document.createElement('div');
        modal.className = 'del-modal-bg';
        modal.innerHTML = `
            <div class="del-modal">
                <div style="text-align:center;">
                    <div style="width:48px; height:48px; background:#fef2f2; border-radius:12px; display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;">
                        <i class="fas fa-trash" style="color:#dc2626; font-size:1.1rem;"></i>
                    </div>
                    <p style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.05rem; color:#1a0f00; margin-bottom:0.5rem;">Delete Project?</p>
                    <p style="color:#6b4f35; font-size:0.855rem; margin-bottom:1.5rem;">This action cannot be undone.</p>
                    <div style="display:flex; gap:0.75rem; justify-content:center;">
                        <button onclick="this.closest('.del-modal-bg').remove()"
                            style="padding:0.6rem 1.4rem; border:1.5px solid rgba(26,15,0,0.12); border-radius:8px; font-weight:600; font-size:0.855rem; cursor:pointer; background:white; color:#1a0f00; font-family:'Instrument Sans',sans-serif;">
                            Cancel
                        </button>
                        <button onclick="deleteProject(${id})"
                            style="padding:0.6rem 1.4rem; border-radius:8px; background:#dc2626; color:white; font-weight:600; font-size:0.855rem; cursor:pointer; border:none; font-family:'Instrument Sans',sans-serif;">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(modal);
        modal.addEventListener('click', e => { if(e.target === modal) modal.remove(); });
    }

    function deleteProject(id) {
        const f = document.createElement('form');
        f.method = 'POST'; f.action = `/admin/projects/${id}`;
        f.innerHTML = `@csrf @method('DELETE')`;
        document.body.appendChild(f); f.submit();
    }
</script>
</x-app-layout>