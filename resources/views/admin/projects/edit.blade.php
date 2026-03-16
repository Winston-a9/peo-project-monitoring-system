<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="app-page-title">
                <span class="app-icon-badge"><i class="fas fa-edit"></i></span>
                Edit Project
            </h2>
            <p class="app-page-subtitle">
                Editing: <span style="color:#f97316;">{{ $project->project_title }}</span>
            </p>
        </div>
        <div class="app-header-actions">
            <a href="{{ route('admin.projects.show', $project) }}" class="app-btn-secondary">
                <i class="fas fa-eye"></i> View
            </a>
            <a href="{{ route('admin.projects.index') }}" class="app-btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</x-slot>

<style>
    :root {
        --orange-500:#f97316; --orange-600:#ea580c;
        --ink:#1a0f00; --ink-muted:#6b4f35;
        --border:rgba(249,115,22,0.14);
        --bg-primary:#ffffff; --bg-secondary:#fffaf5;
        --text-primary:#1a0f00; --text-secondary:#6b4f35;
        --indigo-500:#6366f1; --indigo-600:#4f46e5;
    }
    .dark, html.dark {
        --bg-primary:#0f0f0f; --bg-secondary:#1a1a1a;
        --text-primary:#f5f5f0; --text-secondary:#9ca3af;
        --ink:#f5f5f0; --ink-muted:#9ca3af;
        --border:rgba(249,115,22,0.25);
    }
    body { color:var(--text-primary); transition:background 0.3s,color 0.3s; }

    .app-page-title { font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; letter-spacing:-0.03em; color:var(--text-primary); display:flex; align-items:center; gap:0.6rem; margin:0; }
    .app-icon-badge { background:#f97316; width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(249,115,22,0.35); color:white; }
    .app-page-subtitle { color:var(--text-secondary); font-size:0.82rem; margin:3px 0 0 0; }
    .app-header-actions { display:flex; gap:0.6rem; align-items:center; }
    .app-btn-secondary { display:inline-flex; align-items:center; gap:0.4rem; padding:0.6rem 1rem; border:1.5px solid var(--border); border-radius:9px; font-weight:600; font-size:0.825rem; color:var(--text-secondary); text-decoration:none; background:var(--bg-secondary); transition:all 0.2s; cursor:pointer; font-family:'Instrument Sans',sans-serif; }
    .app-btn-secondary:hover { border-color:var(--orange-500); background:rgba(249,115,22,0.08); color:var(--orange-600); }

    .form-card { background:var(--bg-primary); border:1px solid var(--border); border-radius:12px; overflow:hidden; box-shadow:0 2px 4px rgba(0,0,0,0.02); }
    .section-header { padding:1.1rem 1.5rem; border-bottom:1px solid var(--border); background:var(--bg-secondary); display:flex; align-items:center; gap:0.5rem; }
    .section-header span { font-family:'Syne',sans-serif; font-weight:700; font-size:0.875rem; color:var(--ink); }
    .section-header i { color:var(--orange-500); font-size:0.85rem; }
    .section-body { padding:1.5rem; }

    .field-group { margin-bottom:1.25rem; }
    .field-group:last-child { margin-bottom:0; }
    .field-label { display:block; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); margin-bottom:0.5rem; }
    .field-input { width:100%; padding:0.75rem 1rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; color:var(--text-primary); background:var(--bg-primary); outline:none; font-family:'Instrument Sans',sans-serif; transition:border-color 0.2s,box-shadow 0.2s; }
    .field-input:focus { border-color:var(--orange-500); box-shadow:0 0 0 3px rgba(249,115,22,0.1); }
    .readonly-field { background:var(--bg-secondary) !important; cursor:not-allowed; color:var(--ink-muted); }
    .field-error { font-size:0.775rem; color:#ef4444; margin-top:0.35rem; display:flex; align-items:center; gap:0.3rem; }
    .field-hint { font-size:0.7rem; color:#9ca3af; margin-top:0.35rem; }

    .prog-bar-track { height:5px; background:rgba(249,115,22,0.1); border-radius:99px; margin-top:0.6rem; overflow:hidden; }
    .prog-bar-fill { height:100%; border-radius:99px; transition:width 0.4s ease; }

    .info-box { padding:0.75rem 1rem; border-radius:10px; border:1px solid var(--border); background:var(--bg-secondary); display:flex; align-items:center; gap:0.6rem; font-size:0.825rem; }
    .info-box i { color:var(--orange-500); min-width:20px; }

    .dynamic-row { display:flex; align-items:center; gap:0.5rem; }
    .dynamic-select { flex:1; min-width:0; padding:0.65rem 2.2rem 0.65rem 0.9rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.855rem; color:var(--text-primary); background:var(--bg-primary); outline:none; font-family:'Instrument Sans',sans-serif; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-position:right 0.6rem center; background-repeat:no-repeat; background-size:1.1em; cursor:pointer; transition:border-color 0.2s,box-shadow 0.2s; }
    .dynamic-select:focus { border-color:var(--orange-500); box-shadow:0 0 0 3px rgba(249,115,22,0.1); }
    .remove-btn { width:36px; height:36px; border-radius:8px; border:1.5px solid #fecaca; background:#fef2f2; color:#dc2626; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:0.75rem; transition:all 0.18s; }
    .remove-btn:hover { background:#dc2626; color:white; border-color:#dc2626; }
    .add-row-btn { margin-top:0.875rem; display:inline-flex; align-items:center; gap:0.4rem; padding:0.5rem 1rem; border:1.5px dashed rgba(249,115,22,0.35); border-radius:8px; font-size:0.775rem; font-weight:600; color:var(--orange-600); background:rgba(249,115,22,0.04); cursor:pointer; transition:all 0.2s; font-family:'Instrument Sans',sans-serif; }
    .add-row-btn:hover { border-color:var(--orange-500); background:rgba(249,115,22,0.09); }
    .tag-chip { display:inline-flex; align-items:center; padding:4px 12px; border-radius:99px; font-size:0.68rem; font-weight:700; background:rgba(249,115,22,0.1); color:var(--orange-600); border:1px solid rgba(249,115,22,0.2); }
    .tag-chip-indigo { background:rgba(99,102,241,0.1); color:#6366f1; border:1px solid rgba(99,102,241,0.2); }

    /* History timeline */
    .history-timeline { display:flex; flex-direction:column; gap:0; }
    .history-entry { display:grid; gap:0 1rem; align-items:stretch; }
    .history-entry.te-entry { grid-template-columns:18px 1fr 5rem 5rem 5rem; }
    .history-entry.vo-entry { grid-template-columns:18px 1fr 5rem 5rem 5rem; }
    .h-spine { display:flex; flex-direction:column; align-items:center; padding-top:3px; }
    .h-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
    .h-dot-orange { background:#f97316; box-shadow:0 0 0 3px rgba(249,115,22,0.18); }
    .h-dot-indigo { background:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,0.18); }
    .h-line { width:2px; flex:1; min-height:1.5rem; margin:3px 0; }
    .h-line-orange { background:rgba(249,115,22,0.18); }
    .h-line-indigo { background:rgba(99,102,241,0.18); }
    .h-label { font-size:0.855rem; font-weight:700; color:var(--text-primary); padding:0.1rem 0 1rem; }
    .h-label.last { padding-bottom:0; }
    .h-pill { display:inline-flex; align-items:center; gap:0.3rem; padding:3px 10px; border-radius:99px; font-size:0.72rem; font-weight:700; white-space:nowrap; }
    .h-pill-orange { background:rgba(249,115,22,0.1); color:#ea580c; border:1px solid rgba(249,115,22,0.22); }
    .h-pill-indigo { background:rgba(99,102,241,0.1); color:#6366f1; border:1px solid rgba(99,102,241,0.22); }
    .h-pill-gray   { background:rgba(0,0,0,0.04); color:#9ca3af; border:1px solid rgba(0,0,0,0.07); }
    .h-pill-cum-last { background:rgba(249,115,22,0.12); color:#ea580c; border:1px solid rgba(249,115,22,0.22); }
    .h-col-hdr { font-size:0.63rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#9ca3af; }
    .h-summary { display:flex; align-items:center; justify-content:space-between; margin-top:1rem; padding:0.65rem 0.875rem; border-radius:9px; border:1px solid var(--border); background:var(--bg-secondary); }

    .form-actions { display:flex; gap:0.875rem; margin-top:2rem; }
    .btn-submit { display:inline-flex; align-items:center; gap:0.5rem; padding:0.85rem 1.75rem; background:var(--orange-500); color:white; font-weight:700; font-size:0.9rem; border-radius:10px; border:none; cursor:pointer; box-shadow:0 3px 14px rgba(249,115,22,0.38); font-family:'Instrument Sans',sans-serif; transition:all 0.2s; }
    .btn-submit:hover { background:#ea580c; transform:translateY(-1px); }
    .btn-cancel { display:inline-flex; align-items:center; gap:0.5rem; padding:0.85rem 1.5rem; border:1.5px solid var(--border); border-radius:10px; font-weight:600; font-size:0.875rem; color:var(--ink-muted); text-decoration:none; background:var(--bg-primary); transition:all 0.2s; cursor:pointer; font-family:'Instrument Sans',sans-serif; }
    .btn-cancel:hover { border-color:var(--orange-500); background:rgba(249,115,22,0.05); }

    .grid-2col { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(14px);} to{opacity:1;transform:translateY(0);} }
    .fade-up { animation:fadeUp 0.45s ease both; }
    @media (max-width:768px) { .tab-btn { padding:0.75rem 1rem; font-size:0.8rem; } }
    @media (max-width:640px) {
        .grid-2col { grid-template-columns:1fr; }
        .tab-btn { padding:0.65rem 0.8rem; font-size:0.75rem; }
        .tab-btn span { display:none; }
    }

    /* ══ ACCORDION STYLES ══ */
    .acc-header {
        padding: 1.1rem 1.5rem;
        background: var(--bg-secondary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        user-select: none;
        transition: background 0.2s;
        border-bottom: 1px solid transparent;
    }
    .acc-header:hover { background: rgba(249,115,22,0.06); }
    .acc-header.acc-indigo:hover { background: rgba(99,102,241,0.06); }
    .acc-header.acc-yellow:hover { background: rgba(234,179,8,0.06); }
    .acc-header.is-open { border-bottom-color: var(--border); }
    .acc-header .acc-title {
        font-family: 'Syne', sans-serif;
        font-weight: 700;
        font-size: 0.875rem;
        color: var(--ink);
    }
    .acc-chevron {
        width: 28px; height: 28px;
        border-radius: 7px;
        border: 1.5px solid var(--border);
        background: var(--bg-primary);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: transform 0.3s ease, background 0.2s, border-color 0.2s, color 0.2s;
        color: var(--text-secondary);
        font-size: 0.65rem;
    }
    .acc-header.is-open .acc-chevron {
        transform: rotate(180deg);
        background: rgba(249,115,22,0.08);
        border-color: rgba(249,115,22,0.3);
        color: var(--orange-500);
    }
    .acc-header.acc-indigo.is-open .acc-chevron {
        background: rgba(99,102,241,0.08);
        border-color: rgba(99,102,241,0.3);
        color: #6366f1;
    }
    .acc-header.acc-yellow.is-open .acc-chevron {
        background: rgba(234,179,8,0.08);
        border-color: rgba(234,179,8,0.3);
        color: #d97706;
    }
    .acc-body {
        display: grid;
        grid-template-rows: 1fr;
        transition: grid-template-rows 0.35s cubic-bezier(0.4,0,0.2,1);
    }
    .acc-body.is-collapsed {
        grid-template-rows: 0fr;
    }
    .acc-body-inner {
        overflow: hidden;
    }
    .acc-status-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
        margin-left: auto;
        margin-right: 0.4rem;
    }
</style>

@php
    $existingDocs  = is_array($project->documents_pressed) ? $project->documents_pressed : [];
    $existingDays  = is_array($project->extension_days)    ? array_map('intval', $project->extension_days) : [];
    $existingCosts = is_array($project->cost_involved)     ? $project->cost_involved : [];
    $existingVoDays  = is_array($project->vo_days) ? array_map('intval', array_filter((array)$project->vo_days)) : [];
    $existingVoCosts = is_array($project->vo_cost) ? $project->vo_cost : [];
    $existingDates   = is_array($project->date_requested ?? null) ? $project->date_requested : [];

    $teHistory = [];
    $teIndex   = 0;
    foreach ($existingDocs as $doc) {
        if (str_starts_with((string) $doc, 'Time Extension')) {
            $teHistory[] = [
                'label'          => $doc,
                'days'           => $existingDays[$teIndex]  ?? 0,
                'cost'           => $existingCosts[$teIndex] ?? null,
                'date_requested' => $existingDates[$teIndex] ?? null,
            ];
            $teIndex++;
        }
    }
    $teCount      = count($teHistory);
    $nextTeNumber = $teCount + 1;

    $voHistory    = [];
    $voIndex      = 0;
    $voDateOffset = $teCount;
    foreach ($existingDocs as $doc) {
        if (str_starts_with((string) $doc, 'Variation Order')) {
            $voHistory[] = [
                'label'          => $doc,
                'days'           => $existingVoDays[$voIndex]  ?? 0,
                'cost'           => $existingVoCosts[$voIndex] ?? null,
                'date_requested' => $existingDates[$voDateOffset + $voIndex] ?? null,
            ];
            $voIndex++;
        }
    }
    $voCount      = count($voHistory);
    $nextVoNumber = $voCount + 1;

    $hasSO = collect($existingDocs)->contains('Suspension Order');

    $existingTETotal = collect($teHistory)->sum('days');
    $existingVOTotal = collect($voHistory)->sum('days');
@endphp

<div class="max-w-5xl mx-auto fade-up" style="padding:0 1rem;">

    @if(session('success'))
    <div style="background:rgba(22,163,74,0.08); border:1px solid rgba(22,163,74,0.25); border-radius:10px; padding:1rem; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.6rem;">
        <i class="fas fa-check-circle" style="color:#16a34a; font-size:1rem;"></i>
        <span style="font-size:0.875rem; font-weight:600; color:#15803d;">{{ session('success') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.projects.update', $project) }}">
        @csrf
        @method('PATCH')

        <!-- TAB NAVIGATION -->
        <div style="display:flex; gap:0.5rem; margin-bottom:1.5rem; border-bottom:1px solid var(--border); padding-bottom:0;">
            <button type="button" class="tab-btn tab-active" data-tab="tab-overview" onclick="switchTab('tab-overview', this)">
                <i class="fas fa-info-circle" style="font-size:0.8rem;"></i>
                <span>Overview</span>
            </button>
            <button type="button" class="tab-btn" data-tab="tab-progress" onclick="switchTab('tab-progress', this)">
                <i class="fas fa-chart-line" style="font-size:0.8rem;"></i>
                <span>Performance</span>
            </button>
            <button type="button" class="tab-btn" data-tab="tab-extensions" onclick="switchTab('tab-extensions', this)">
                <i class="fas fa-file-alt" style="font-size:0.8rem;"></i>
                <span>Extensions</span>
            </button>
            <button type="button" class="tab-btn" data-tab="tab-admin" onclick="switchTab('tab-admin', this)">
                <i class="fas fa-cog" style="font-size:0.8rem;"></i>
                <span>Admin</span>
            </button>
        </div>

        <style>
            .tab-btn {
                padding:0.85rem 1.25rem; border:none; background:transparent; cursor:pointer;
                display:flex; align-items:center; gap:0.5rem; color:var(--text-secondary);
                font-weight:600; font-size:0.85rem; font-family:'Instrument Sans',sans-serif;
                border-bottom:3px solid transparent; transition:all 0.2s;
                position:relative; bottom:-1.5px;
            }
            .tab-btn:hover { color:var(--text-primary); }
            .tab-btn.tab-active { color:var(--orange-500); border-bottom-color:var(--orange-500); }
            .tab-content { display:none; }
            .tab-content.active { display:block; }
        </style>

        {{-- TAB 1: OVERVIEW --}}
        <div id="tab-overview" class="tab-content active">
            <div class="info-box" style="margin-bottom:1.5rem;">
                <i class="fas fa-clock"></i>
                <p style="margin:0; color:var(--text-secondary);">
                    Last updated: <span style="font-weight:700; color:var(--text-primary);">{{ $project->updated_at->format('F d, Y \a\t h:i A') }}</span>
                </p>
            </div>

            {{-- PROJECT INFORMATION — read-only fields shown as plain text --}}
            <div class="form-card" style="margin-bottom:1.5rem;">
                <div class="section-header">
                    <i class="fas fa-box"></i>
                    <span>Project Information</span>
                    <span style="margin-left:auto; font-size:0.7rem; color:#9ca3af; font-weight:400;">Read-only</span>
                </div>
                {{-- Hidden inputs carry all values for form submission --}}
                <input type="hidden" name="in_charge"       value="{{ $project->in_charge }}">
                <input type="hidden" name="project_title"   value="{{ $project->project_title }}">
                <input type="hidden" name="location"        value="{{ $project->location }}">
                <input type="hidden" name="contractor"      value="{{ $project->contractor }}">
                <input type="hidden" name="contract_amount" id="contract_amount" value="{{ $project->contract_amount }}">
                <div class="section-body">
                    <div class="grid-2col">
                        <div class="field-group">
                            <label class="field-label">In Charge</label>
                            <p style="font-size:0.9rem;font-weight:600;color:var(--text-primary);margin:0;padding:0.1rem 0;">{{ $project->in_charge }}</p>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Project Title</label>
                            <p style="font-size:0.9rem;font-weight:600;color:var(--text-primary);margin:0;padding:0.1rem 0;">{{ $project->project_title }}</p>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Location</label>
                            <p style="font-size:0.9rem;font-weight:600;color:var(--text-primary);margin:0;padding:0.1rem 0;">{{ $project->location }}</p>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Contractor</label>
                            <p style="font-size:0.9rem;font-weight:600;color:var(--text-primary);margin:0;padding:0.1rem 0;">{{ $project->contractor }}</p>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Contract Amount</label>
                            <p style="font-size:0.9rem;font-weight:700;color:var(--text-primary);margin:0;padding:0.1rem 0;font-family:'Syne',sans-serif;letter-spacing:-0.01em;">
                                ₱{{ number_format($project->contract_amount, 2) }}
                            </p>
                            <p class="field-hint">Adjusted by TE / VO cost entries</p>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Status</label>
                            <select name="status" id="status_sel" class="field-input" onchange="toggleCompletedAt()" style="appearance:none; padding-right:2rem; background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b4f35%22 stroke-width=%222%22%3E%3Cpath d=%22M6 9l6 6 6-6%22/%3E%3C/svg%3E'); background-position:right 0.6rem center; background-repeat:no-repeat; background-size:1.1em; cursor:pointer;">
                                <option value="ongoing"   {{ old('status',$project->status)=='ongoing'   ? 'selected':'' }}>Ongoing</option>
                                <option value="completed" {{ old('status',$project->status)=='completed' ? 'selected':'' }}>Completed</option>
                                <option value="expired"   {{ old('status',$project->status)=='expired'   ? 'selected':'' }}>Expired</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CONTRACT DATES — read-only fields shown as plain text --}}
            <div class="form-card" style="margin-bottom:1.5rem;">
                <div class="section-header">
                    <i class="fas fa-calendar-days"></i>
                    <span>Contract Dates</span>
                    <span style="margin-left:auto; font-size:0.7rem; color:#9ca3af; font-weight:400;">Key milestones</span>
                </div>
                {{-- Hidden inputs carry values for form submission --}}
                <input type="hidden" name="date_started"             value="{{ $project->date_started->format('Y-m-d') }}">
                <input type="hidden" name="original_contract_expiry" value="{{ $project->original_contract_expiry->format('Y-m-d') }}">
                <div class="section-body">
                    <div class="grid-2col">
                        <div class="field-group">
                            <label class="field-label">Date Started</label>
                            <p style="font-size:0.9rem;font-weight:600;color:var(--text-primary);margin:0;padding:0.1rem 0;">{{ $project->date_started->format('F d, Y') }}</p>
                            <p class="field-hint">{{ $project->date_started->format('l') }}</p>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Original Expiry</label>
                            <p style="font-size:0.9rem;font-weight:600;color:var(--text-primary);margin:0;padding:0.1rem 0;">{{ $project->original_contract_expiry->format('F d, Y') }}</p>
                            <p class="field-hint">{{ $project->original_contract_expiry->format('l') }}</p>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Revised Expiry</label>
                            @if($project->revised_contract_expiry)
                                <p style="font-size:0.9rem;font-weight:700;color:#f97316;margin:0;padding:0.1rem 0;font-family:'Syne',sans-serif;">{{ $project->revised_contract_expiry->format('F d, Y') }}</p>
                                <p class="field-hint">{{ $project->revised_contract_expiry->format('l') }}</p>
                            @else
                                <p style="font-size:0.875rem;color:#9ca3af;margin:0;padding:0.1rem 0;font-style:italic;">Not yet set</p>
                            @endif
                        </div>
                        <div id="completed_at_field" class="field-group {{ old('status',$project->status)=='completed' ? '' : 'hidden' }}">
                            <label class="field-label">Date Completed</label>
                            <input type="date" name="completed_at" class="field-input {{ $errors->has('completed_at') ? 'has-error':'' }}" value="{{ old('completed_at', $project->completed_at?->format('Y-m-d')) }}">
                            @error('completed_at')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: PERFORMANCE --}}
        <div id="tab-progress" class="tab-content">
        <div class="form-card" style="margin-bottom:1.5rem;">
            <div class="section-header">
                <i class="fas fa-chart-bar"></i>
                <span>Work Progress</span>
            </div>
            <div class="section-body">
                <div class="grid-2col">
                    <div class="field-group">
                        <label class="field-label">As Planned (%)</label>
                        <div style="position:relative;">
                            <input type="number" name="as_planned" id="as_planned" class="field-input" value="{{ old('as_planned', $project->as_planned) }}" min="0" max="100" step="0.01" oninput="computeSlippage()" required style="padding-right:2.5rem;">
                            <span style="position:absolute; right:1rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-weight:600;">%</span>
                        </div>
                        <div class="prog-bar-track"><div class="prog-bar-fill" id="ap_bar" style="background:var(--orange-500); width:{{ $project->as_planned }}%;"></div></div>
                        @error('as_planned')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                    <div class="field-group">
                        <label class="field-label">Work Done (%)</label>
                        <div style="position:relative;">
                            <input type="number" name="work_done" id="work_done" class="field-input" value="{{ old('work_done', $project->work_done) }}" min="0" max="100" step="0.01" oninput="computeSlippage()" required style="padding-right:2.5rem;">
                            <span style="position:absolute; right:1rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-weight:600;">%</span>
                        </div>
                        <div class="prog-bar-track"><div class="prog-bar-fill" id="wd_bar" style="background:#3b82f6; width:{{ $project->work_done }}%;"></div></div>
                        @error('work_done')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="field-group">
                    <label class="field-label">Schedule Slippage <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(automatic)</span></label>
                    <input type="hidden" name="slippage" id="slippage" value="{{ old('slippage', $project->slippage) }}">
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:0.85rem 1rem; border:1.5px solid rgba(26,15,0,0.08); border-radius:9px; background:var(--bg-secondary); min-height:48px;">
                        <p id="slippage_label" style="font-size:0.85rem; font-weight:600; color:#9ca3af; margin:0;"><i class="fas fa-minus"></i> On schedule</p>
                        <span id="slippage-value" style="font-family:'Syne',sans-serif; font-size:1.25rem; font-weight:800; color:#9ca3af;">—</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- LIQUIDATED DAMAGES — auto fields shown as plain text with comma formatting --}}
        <div class="form-card" style="margin-bottom:1.5rem;">
            <div class="section-header">
                <i class="fas fa-calculator"></i>
                <span>Liquidated Damages Assessment</span>
            </div>
            <div class="section-body">
                <div class="grid-2col">
                    <div class="field-group">
                        <label class="field-label">Accomplished (%)</label>
                        <div style="position:relative;">
                            <input type="number" id="ld_accomplished" name="ld_accomplished" class="field-input" value="{{ old('ld_accomplished', $project->ld_accomplished ?? '') }}" min="0" max="100" step="0.001" oninput="calculateLDPerDay()" style="padding-right:2.5rem;">
                            <span style="position:absolute; right:1rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-weight:600;">%</span>
                        </div>
                        <p class="field-hint">Percentage of work completed</p>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Unworked (%) <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto)</span></label>
                        <input type="hidden" id="ld_unworked" name="ld_unworked" value="{{ old('ld_unworked', $project->ld_unworked ?? '') }}">
                        <p style="font-size:0.9rem;font-weight:600;color:var(--text-primary);margin:0;padding:0.1rem 0;">
                            <span id="ld_unworked_display">{{ old('ld_unworked', $project->ld_unworked ?? '—') }}</span>
                            <span style="color:var(--ink-muted);font-weight:500;margin-left:2px;">%</span>
                        </p>
                        <p class="field-hint">Automatically calculated</p>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Days Overdue</label>
                        <input type="number" id="ld_days_overdue_input" name="ld_days_overdue" class="field-input" value="{{ old('ld_days_overdue', $project->ld_days_overdue ?? '') }}" min="0" step="1" oninput="calculateLDTotal()" placeholder="e.g. 30">
                        <p class="field-hint">Number of overdue days</p>
                    </div>
                    <div class="field-group">
                        <label class="field-label">LD per Day (₱) <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto)</span></label>
                        <input type="hidden" id="ld_per_day" name="ld_per_day" value="{{ old('ld_per_day', $project->ld_per_day ?? '0') }}">
                        <p style="font-size:0.9rem;font-weight:700;color:var(--text-primary);margin:0;padding:0.1rem 0;font-family:'Syne',sans-serif;letter-spacing:-0.01em;">
                            ₱<span id="ld_per_day_display">{{ number_format((float) old('ld_per_day', $project->ld_per_day ?? 0), 2) }}</span>
                        </p>
                        <p class="field-hint">Formula: (Unworked % ÷ 100) × Contract Amount × 0.001</p>
                    </div>
                    <div class="field-group" style="grid-column:1/-1;">
                        <label class="field-label">Total LD (₱) <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto)</span></label>
                        <input type="hidden" id="total_ld" name="total_ld" value="{{ old('total_ld', $project->total_ld ?? '0') }}">
                        <p style="font-size:1.35rem;font-weight:800;color:#dc2626;margin:0;padding:0.1rem 0;font-family:'Syne',sans-serif;letter-spacing:-0.02em;">
                            ₱<span id="total_ld_display">{{ number_format((float) old('total_ld', $project->total_ld ?? 0), 2) }}</span>
                        </p>
                        <p class="field-hint">Formula: LD per Day × Days Overdue</p>
                    </div>
                </div>
            </div>
        </div>
        </div>

        {{-- TAB 3: EXTENSIONS — with accordion panels --}}
        <div id="tab-extensions" class="tab-content">

        {{-- ══ TIME EXTENSIONS ACCORDION ══ --}}
        <div class="form-card" style="margin-bottom:1.5rem;">
            <div class="acc-header is-open" id="acc-te-hdr" onclick="toggleAcc('te')" role="button" aria-expanded="true" aria-controls="acc-te-bdy">
                <i class="fas fa-clock" style="color:var(--orange-500); font-size:0.85rem; flex-shrink:0;"></i>
                <span class="acc-title">Time Extensions</span>
                @if($teCount > 0)
                    <span class="tag-chip" style="margin-left:0.4rem;">{{ $teCount }} recorded</span>
                @else
                    <span style="font-size:0.7rem; color:#9ca3af; margin-left:0.4rem; font-weight:400;">No entries yet</span>
                @endif
                <div class="acc-status-dot" style="background:{{ $teCount > 0 ? '#16a34a' : '#d1d5db' }};"></div>
                <div class="acc-chevron"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="acc-body" id="acc-te-bdy">
            <div class="acc-body-inner">
            <div class="section-body">
                @if($teCount > 0)
                <div style="margin-bottom:1.5rem;">
                    <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#9ca3af; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem;">
                        <i class="fas fa-history" style="color:var(--orange-500);"></i> Existing Time Extensions
                    </p>
                    <div style="display:grid; grid-template-columns:18px 1fr 5rem 5rem 5rem; gap:0 0.75rem; padding:0 0.25rem; margin-bottom:0.4rem;">
                        <span></span>
                        <p class="h-col-hdr">Extension</p>
                        <p class="h-col-hdr" style="text-align:center;">Action</p>
                        <p class="h-col-hdr" style="text-align:center;">Days Added</p>
                        <p class="h-col-hdr" style="text-align:center;">Cumulative</p>
                    </div>
                    <div class="history-timeline">
                        @php $teRunning = 0; @endphp
                        @foreach($teHistory as $ti => $entry)
                        @php $teRunning += $entry['days']; $teIsLast = $ti === count($teHistory)-1; @endphp
                        <div class="history-entry te-entry" style="align-items:start;">
                            <div class="h-spine">
                                <div class="h-dot h-dot-orange"></div>
                                @if(!$teIsLast)<div class="h-line h-line-orange"></div>@endif
                            </div>
                            <div class="h-label {{ $teIsLast ? 'last' : '' }}" style="display:flex; align-items:flex-start; flex-direction:column; gap:0.2rem;">
                                <span>{{ $entry['label'] }}</span>
                                <span style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                    @if($entry['date_requested']) <span style="font-size:0.72rem; font-weight:500; color:#9ca3af;">{{ \Carbon\Carbon::parse($entry['date_requested'])->format('M d, Y') }}</span>@endif
                                    @if($entry['cost']) <span style="font-size:0.72rem; font-weight:500; color:#16a34a;">₱{{ number_format($entry['cost'],2) }}</span>@endif
                                </span>
                            </div>
                            <div style="display:flex; align-items:flex-start; justify-content:center; padding-top:2px;">
                                <button type="button"
                                    onclick="openEditModal('te', {{ $ti }}, '{{ addslashes($entry['label']) }}', {{ $entry['days'] }}, '{{ $entry['cost'] ?? '' }}', '{{ $entry['date_requested'] ?? '' }}')"
                                    style="display:inline-flex; align-items:center; gap:0.3rem; padding:3px 10px; border-radius:6px; border:1.5px solid rgba(249,115,22,0.25); background:rgba(249,115,22,0.06); color:#ea580c; font-size:0.68rem; font-weight:700; cursor:pointer; font-family:'Instrument Sans',sans-serif; transition:all 0.15s; white-space:nowrap;"
                                    onmouseover="this.style.background='rgba(249,115,22,0.15)';this.style.borderColor='rgba(249,115,22,0.45)'"
                                    onmouseout="this.style.background='rgba(249,115,22,0.06)';this.style.borderColor='rgba(249,115,22,0.25)'">
                                    <i class="fas fa-pen" style="font-size:0.55rem;"></i> Edit
                                </button>
                            </div>
                            <div style="display:flex; align-items:flex-start; justify-content:center; padding-top:2px;">
                                <span class="h-pill {{ $entry['days'] > 0 ? 'h-pill-orange' : 'h-pill-gray' }}">+{{ $entry['days'] }}d</span>
                            </div>
                            <div style="display:flex; align-items:flex-start; justify-content:center; padding-top:2px;">
                                <span class="h-pill {{ $teIsLast ? 'h-pill-cum-last' : 'h-pill-gray' }}">{{ $teRunning }}d</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="h-summary">
                        <span style="font-size:0.8rem; font-weight:600; color:var(--ink-muted); display:flex; align-items:center; gap:0.4rem;">
                            <i class="fas fa-sigma" style="color:var(--orange-500); font-size:0.75rem;"></i>
                            Total across {{ $teCount }} {{ $teCount === 1 ? 'extension' : 'extensions' }}
                        </span>
                        <span style="font-family:'Syne',sans-serif; font-size:1.2rem; font-weight:800; color:var(--orange-600);">{{ $teRunning }} days</span>
                    </div>
                </div>
                <div style="border-top:1px dashed var(--border); margin-bottom:1.5rem;"></div>
                @endif

                <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#9ca3af; margin-bottom:0.875rem; display:flex; align-items:center; gap:0.5rem;">
                    <i class="fas fa-plus-circle" style="color:var(--orange-500);"></i>
                    Add Time Extension {{ $nextTeNumber }}
                    <span style="font-weight:500; color:#9ca3af; text-transform:none; letter-spacing:0;">— will be saved as <strong style="color:var(--orange-600);">TE {{ $nextTeNumber }}</strong></span>
                </p>
                <div class="grid-2col">
                    <div class="field-group">
                        <label class="field-label">Extension Days <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="new_te_days" id="new_te_days" class="field-input" min="1" step="1" placeholder="e.g. 60" oninput="updateTEPreview()">
                        <p class="field-hint">Number of days to extend the contract deadline</p>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Cost Involved (₱)</label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-weight:600; pointer-events:none;">₱</span>
                            <input type="number" name="new_te_cost" class="field-input" min="-9999999999" step="0.01" placeholder="0.00" style="padding-left:1.75rem;">
                        </div>
                        <p class="field-hint">Optional cost for this extension</p>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Date Requested</label>
                        <input type="date" name="new_te_date" class="field-input" style="border-color:rgba(249,115,22,0.25);">
                        <p class="field-hint">When this time extension was requested</p>
                    </div>
                    <div class="field-group" style="grid-column:1/-1;">
                        <label class="field-label">Projected New Expiry (Preview)</label>
                        <div style="padding:0.875rem 1rem; border:1.5px solid rgba(249,115,22,0.2); border-radius:9px; background:rgba(249,115,22,0.04); display:flex; align-items:center; gap:0.6rem; min-height:46px;">
                            <i class="fas fa-calendar-check" style="color:var(--orange-500);"></i>
                            <span id="te_revised_preview" style="font-weight:600; color:var(--text-primary);">Enter days above to preview</span>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div>
        </div>

        {{-- ══ VARIATION ORDERS ACCORDION ══ --}}
        <div class="form-card" style="margin-bottom:1.5rem; border-color:rgba(99,102,241,0.18);">
            <div class="acc-header acc-indigo is-open" id="acc-vo-hdr" onclick="toggleAcc('vo')" role="button" aria-expanded="true" aria-controls="acc-vo-bdy" style="border-bottom-color:rgba(99,102,241,0.15);">
                <i class="fas fa-file-signature" style="color:#6366f1; font-size:0.85rem; flex-shrink:0;"></i>
                <span class="acc-title">Variation Orders</span>
                @if($voCount > 0)
                    <span class="tag-chip tag-chip-indigo" style="margin-left:0.4rem;">{{ $voCount }} recorded</span>
                @else
                    <span style="font-size:0.7rem; color:#9ca3af; margin-left:0.4rem; font-weight:400;">No entries yet</span>
                @endif
                <div class="acc-status-dot" style="background:{{ $voCount > 0 ? '#6366f1' : '#d1d5db' }};"></div>
                <div class="acc-chevron" style="border-color:rgba(99,102,241,0.2);"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="acc-body" id="acc-vo-bdy">
            <div class="acc-body-inner">
            <div class="section-body">
                @if($voCount > 0)
                <div style="margin-bottom:1.5rem;">
                    <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#9ca3af; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem;">
                        <i class="fas fa-history" style="color:#6366f1;"></i> Existing Variation Orders
                    </p>
                    <div style="display:grid; grid-template-columns:18px 1fr 5rem 5rem 5rem; gap:0 0.75rem; padding:0 0.25rem; margin-bottom:0.4rem;">
                        <span></span>
                        <p class="h-col-hdr">Order</p>
                        <p class="h-col-hdr" style="text-align:center;">Action</p>
                        <p class="h-col-hdr" style="text-align:center;">Days Added</p>
                        <p class="h-col-hdr" style="text-align:center;">VO Cumulative</p>
                    </div>
                    <div class="history-timeline">
                        @php $voRunning = 0; @endphp
                        @foreach($voHistory as $vi => $entry)
                        @php $voRunning += $entry['days']; $voIsLast = $vi === count($voHistory)-1; @endphp
                        <div class="history-entry vo-entry" style="align-items:start;">
                            <div class="h-spine">
                                <div class="h-dot h-dot-indigo"></div>
                                @if(!$voIsLast)<div class="h-line h-line-indigo"></div>@endif
                            </div>
                            <div class="h-label {{ $voIsLast ? 'last' : '' }}" style="display:flex; align-items:flex-start; flex-direction:column; gap:0.2rem;">
                                <span>{{ $entry['label'] }}</span>
                                <span style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                    @if($entry['date_requested']) <span style="font-size:0.72rem; font-weight:500; color:#9ca3af;">{{ \Carbon\Carbon::parse($entry['date_requested'])->format('M d, Y') }}</span>@endif
                                    @if($entry['cost']) <span style="font-size:0.72rem; font-weight:500; color:#16a34a;">₱{{ number_format($entry['cost'],2) }}</span>@endif
                                </span>
                            </div>
                            <div style="display:flex; align-items:flex-start; justify-content:center; padding-top:2px;">
                                <button type="button"
                                    onclick="openEditModal('vo', {{ $vi }}, '{{ addslashes($entry['label']) }}', {{ $entry['days'] }}, '{{ $entry['cost'] ?? '' }}', '{{ $entry['date_requested'] ?? '' }}')"
                                    style="display:inline-flex; align-items:center; gap:0.3rem; padding:3px 10px; border-radius:6px; border:1.5px solid rgba(99,102,241,0.25); background:rgba(99,102,241,0.06); color:#6366f1; font-size:0.68rem; font-weight:700; cursor:pointer; font-family:'Instrument Sans',sans-serif; transition:all 0.15s; white-space:nowrap;"
                                    onmouseover="this.style.background='rgba(99,102,241,0.15)';this.style.borderColor='rgba(99,102,241,0.45)'"
                                    onmouseout="this.style.background='rgba(99,102,241,0.06)';this.style.borderColor='rgba(99,102,241,0.25)'">
                                    <i class="fas fa-pen" style="font-size:0.55rem;"></i> Edit
                                </button>
                            </div>
                            <div style="display:flex; align-items:flex-start; justify-content:center; padding-top:2px;">
                                <span class="h-pill {{ $entry['days'] > 0 ? 'h-pill-indigo' : 'h-pill-gray' }}">+{{ $entry['days'] }}d</span>
                            </div>
                            <div style="display:flex; align-items:flex-start; justify-content:center; padding-top:2px;">
                                <span class="h-pill {{ $voIsLast ? '' : 'h-pill-gray' }}" style="{{ $voIsLast ? 'background:rgba(99,102,241,0.12);color:#6366f1;border:1px solid rgba(99,102,241,0.22);' : '' }}">{{ $voRunning }}d</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @php
                        $existingTETotal = collect($teHistory)->sum('days');
                        $voStackedTotal  = $existingTETotal + $voRunning;
                    @endphp
                    <div class="h-summary">
                        <span style="font-size:0.8rem; font-weight:600; color:var(--ink-muted); display:flex; align-items:center; gap:0.4rem;">
                            <i class="fas fa-sigma" style="color:#6366f1; font-size:0.75rem;"></i>
                            VO total · stacks on TE ({{ $existingTETotal }}d + {{ $voRunning }}d)
                        </span>
                        <span style="font-family:'Syne',sans-serif; font-size:1.2rem; font-weight:800; color:#6366f1;">{{ $voStackedTotal }} days</span>
                    </div>
                </div>
                <div style="border-top:1px dashed rgba(99,102,241,0.2); margin-bottom:1.5rem;"></div>
                @endif

                <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#9ca3af; margin-bottom:0.875rem; display:flex; align-items:center; gap:0.5rem;">
                    <i class="fas fa-plus-circle" style="color:#6366f1;"></i>
                    Add Variation Order {{ $nextVoNumber }}
                    <span style="font-weight:500; color:#9ca3af; text-transform:none; letter-spacing:0;">— will be saved as <strong style="color:#6366f1;">VO {{ $nextVoNumber }}</strong></span>
                </p>
                <div class="grid-2col">
                    <div class="field-group">
                        <label class="field-label">VO Days <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="new_vo_days" id="new_vo_days" class="field-input" min="1" step="1" placeholder="e.g. 45" oninput="updateVOPreview()" style="border-color:rgba(99,102,241,0.25);">
                        <p class="field-hint">Days added by this variation order</p>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Cost Involved (₱)</label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-weight:600; pointer-events:none;">₱</span>
                            <input type="number" name="new_vo_cost" class="field-input" min="-9999999999" step="0.01" placeholder="0.00" style="padding-left:1.75rem; border-color:rgba(99,102,241,0.25);">
                        </div>
                        <p class="field-hint">Additional cost for this variation</p>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Date Requested</label>
                        <input type="date" name="new_vo_date" class="field-input" style="border-color:rgba(99,102,241,0.25);">
                        <p class="field-hint">When this variation order was requested</p>
                    </div>
                    <div class="field-group" style="grid-column:1/-1;">
                        <label class="field-label">Projected New Expiry (Preview)</label>
                        <div style="padding:0.875rem 1rem; border:1.5px solid rgba(99,102,241,0.2); border-radius:9px; background:rgba(99,102,241,0.04); display:flex; align-items:center; gap:0.6rem; min-height:46px;">
                            <i class="fas fa-calendar-check" style="color:#6366f1;"></i>
                            <span id="vo_revised_preview" style="font-weight:600; color:var(--text-primary);">Enter days above to preview</span>
                        </div>
                        <p class="field-hint" style="margin-top:0.4rem;">
                            <i class="fas fa-info-circle" style="color:#6366f1;"></i>
                            VO stacks on top of the current revised expiry
                            @if($existingTETotal > 0 || $existingVOTotal > 0)
                                (Original{{ $existingTETotal > 0 ? ' +'.$existingTETotal.'d TE' : '' }}{{ $existingVOTotal > 0 ? ' +'.$existingVOTotal.'d VO' : '' }} + new VO days)
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            </div>
            </div>
        </div>

        {{-- ══ SUSPENSION ORDER ACCORDION ══ --}}
        <div class="form-card" style="margin-bottom:1.5rem; border-color:rgba(234,179,8,0.2);">
            <div class="acc-header acc-yellow is-open" id="acc-so-hdr" onclick="toggleAcc('so')" role="button" aria-expanded="true" aria-controls="acc-so-bdy" style="border-bottom-color:rgba(234,179,8,0.18);">
                <i class="fas fa-pause-circle" style="color:#d97706; font-size:0.85rem; flex-shrink:0;"></i>
                <span class="acc-title">{{ $hasSO ? 'Suspension Order' : 'Add Suspension' }}</span>
                @if($hasSO)
                    <span style="margin-left:0.4rem; font-size:0.7rem; color:#9ca3af; font-weight:400;">
                        Currently <strong style="color:#d97706;">{{ $project->suspension_days }} days</strong> suspended
                    </span>
                @endif
                <div class="acc-status-dot" style="background:{{ $hasSO ? '#d97706' : '#d1d5db' }};"></div>
                <div class="acc-chevron" style="border-color:rgba(234,179,8,0.25);"><i class="fas fa-chevron-down"></i></div>
            </div>
            <div class="acc-body" id="acc-so-bdy">
            <div class="acc-body-inner">
            <div class="section-body">
                <div class="grid-2col">
                    <div class="field-group">
                        <label class="field-label">{{ $hasSO ? 'Additional' : '' }} Suspension Days <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="new_so_days" id="new_so_days" class="field-input" min="1" step="1" placeholder="e.g. 30" oninput="updateSOPreview()">
                        <p class="field-hint">{{ $hasSO ? 'Added to existing '.$project->suspension_days.' days' : 'Days that work was suspended' }}</p>
                    </div>
                    <div class="field-group">
                        <label class="field-label">Projected New Expiry (Preview)</label>
                        <div style="padding:0.875rem 1rem; border:1.5px solid rgba(234,179,8,0.25); border-radius:9px; background:rgba(234,179,8,0.04); display:flex; align-items:center; gap:0.6rem; min-height:46px;">
                            <i class="fas fa-calendar-check" style="color:#d97706;"></i>
                            <span id="so_revised_preview" style="font-weight:600; color:var(--text-primary);">Enter days to preview</span>
                        </div>
                    </div>
                    <div style="grid-column:1/-1; padding:0.875rem 1rem; border-left:3px solid #d97706; background:rgba(234,179,8,0.04); border-radius:6px;">
                        <p style="font-size:0.8rem; color:var(--text-primary); margin:0 0 0.4rem 0; font-weight:600;">
                            <i class="fas fa-info-circle" style="color:#d97706; margin-right:0.4rem;"></i> How SO affects dates
                        </p>
                        <p style="font-size:0.75rem; color:var(--text-secondary); margin:0; line-height:1.6;">
                            SO days stack on top of any existing TE and VO days, pushing the Revised Expiry further.
                            @if($hasSO)
                                <br>Current SO total: <strong style="color:#d97706;">{{ $project->suspension_days }} days</strong>. New days will be added.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            </div>
            </div>
        </div>

        </div>{{-- end tab-extensions --}}

        {{-- TAB 4: ADMIN --}}
        <div id="tab-admin" class="tab-content">
        @php
            $issuanceOptions = ['1st Notice of Negative Slippage','2nd Notice of Negative Slippage','3rd Notice of Negative Slippage','Liquidated Damages','Notice to Terminate','Notice of Expiry','Performance Bond'];
            $savedIssuances  = old('issuances', $project->issuances ?? []);
            if (is_string($savedIssuances)) $savedIssuances = json_decode($savedIssuances, true) ?? [];
            if (empty($savedIssuances)) $savedIssuances = [''];
        @endphp
        <div class="form-card" style="margin-bottom:1.5rem;">
            <div class="section-header">
                <i class="fas fa-bell"></i>
                <span>Contractor Notifications</span>
                <span class="tag-chip" id="issuance-count" style="margin-left:auto;">0</span>
            </div>
            <div class="section-body">
                <p class="field-hint" style="margin-bottom:1rem;">Notices and formal documents issued to the contractor</p>
                <div id="issuances-list" style="display:flex; flex-direction:column; gap:0.75rem;">
                    @foreach($savedIssuances as $val)
                    <div class="dynamic-row">
                        <select name="issuances[]" class="dynamic-select" onchange="updateCount('issuances-list','issuance-count'); checkPerformanceBond()">
                            <option value="">— Select Notification —</option>
                            @foreach($issuanceOptions as $opt)
                                <option value="{{ $opt }}" {{ $val===$opt ? 'selected':'' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="remove-btn" onclick="removeIssuanceRow(this)"><i class="fas fa-trash-alt"></i></button>
                    </div>
                    @endforeach
                </div>

                {{-- Performance Bond date — only visible when Performance Bond is selected --}}
                <div id="performance-bond-date-field" style="display:none; margin-top:1.25rem; padding:1rem 1.15rem; border-radius:10px; border:1.5px solid rgba(249,115,22,0.2); background:rgba(249,115,22,0.04);">
                    <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.75rem;">
                        <div style="width:26px; height:26px; background:rgba(249,115,22,0.1); border-radius:7px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-shield-halved" style="color:#f97316; font-size:0.7rem;"></i>
                        </div>
                        <label class="field-label" style="margin:0;">Performance Bond Expiry Date</label>
                    </div>
                    <input type="date" name="performance_bond_date" class="field-input"
                        value="{{ old('performance_bond_date', $project->performance_bond_date?->format('Y-m-d') ?? '') }}">
                    <p class="field-hint" style="margin-top:0.4rem;">Date when the performance bond expires</p>
                </div>
            </div>
        </div>

        <div class="form-card" style="margin-bottom:2rem;">
            <div class="section-header">
                <i class="fas fa-sticky-note"></i>
                <span>Remarks & Observations</span>
                <span style="margin-left:auto; font-size:0.7rem; color:#9ca3af; font-weight:400;">(Optional)</span>
            </div>
            <div class="section-body">
                <textarea name="remarks_recommendation" rows="5" class="field-input" style="resize:none; font-family:'Instrument Sans',sans-serif;" placeholder="Add any remarks, notes, or observations about this project…">{{ old('remarks_recommendation', $project->remarks_recommendation) }}</textarea>
                <p class="field-hint">This field is for project notes and observations</p>
            </div>
        </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <a href="{{ route('admin.projects.index') }}" class="btn-cancel">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
// ── Tab switching ──
function switchTab(tabId, btnElement) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('tab-active'));
    document.getElementById(tabId).classList.add('active');
    btnElement.classList.add('tab-active');
}

// ── Accordion toggle ──
function toggleAcc(id) {
    const hdr  = document.getElementById('acc-' + id + '-hdr');
    const body = document.getElementById('acc-' + id + '-bdy');
    const open = hdr.classList.contains('is-open');
    if (open) {
        hdr.classList.remove('is-open');
        body.classList.add('is-collapsed');
        hdr.setAttribute('aria-expanded', 'false');
    } else {
        hdr.classList.add('is-open');
        body.classList.remove('is-collapsed');
        hdr.setAttribute('aria-expanded', 'true');
    }
}

function toggleCompletedAt() {
    document.getElementById('completed_at_field').classList.toggle('hidden', document.getElementById('status_sel').value !== 'completed');
}

function computeSlippage() {
    const ap   = parseFloat(document.getElementById('as_planned').value);
    const wd   = parseFloat(document.getElementById('work_done').value);
    document.getElementById('ap_bar').style.width = Math.min(ap || 0, 100) + '%';
    document.getElementById('wd_bar').style.width = Math.min(wd || 0, 100) + '%';
    const lbl  = document.getElementById('slippage_label');
    const valEl = document.getElementById('slippage-value');
    if (isNaN(ap) || isNaN(wd)) { valEl.textContent = '—'; return; }
    const sl = (wd - ap).toFixed(2);
    document.getElementById('slippage').value = sl;
    if (+sl > 0)      { lbl.style.color='#16a34a'; lbl.innerHTML='<i class="fas fa-arrow-up"></i> Ahead';    valEl.style.color='#16a34a'; }
    else if (+sl < 0) { lbl.style.color='#dc2626'; lbl.innerHTML='<i class="fas fa-arrow-down"></i> Behind'; valEl.style.color='#dc2626'; }
    else              { lbl.style.color='#9ca3af'; lbl.innerHTML='<i class="fas fa-minus"></i> On schedule'; valEl.style.color='#9ca3af'; }
    valEl.textContent = (+sl > 0 ? '+' : '') + sl + '%';
}

// ── LD — uses unworked % not accomplished % ──
function fmtNum(n, decimals) {
    return n.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
}
function calculateLDPerDay() {
    const acc      = parseFloat(document.getElementById('ld_accomplished').value) || 0;
    const amt      = parseFloat(document.getElementById('contract_amount').value.replace(/,/g, '')) || 0;
    const unworked = Math.max(0, 100 - acc);
    const perDay   = (unworked / 100) * amt * 0.001;

    document.getElementById('ld_unworked').value = unworked.toFixed(2);
    document.getElementById('ld_per_day').value  = perDay.toFixed(2);

    document.getElementById('ld_unworked_display').textContent = fmtNum(unworked, 2);
    document.getElementById('ld_per_day_display').textContent  = fmtNum(perDay, 2);

    calculateLDTotal();
}
function calculateLDTotal() {
    const perDay  = parseFloat(document.getElementById('ld_per_day').value)            || 0;
    const overdue = parseFloat(document.getElementById('ld_days_overdue_input').value) || 0;
    const total   = perDay * overdue;

    document.getElementById('total_ld').value = total.toFixed(2);
    document.getElementById('total_ld_display').textContent = fmtNum(total, 2);
}

// ── Date preview helpers ──
const originalExpiry = '{{ $project->original_contract_expiry->format("Y-m-d") }}';
const existingTEDays = {{ (int) collect($project->extension_days ?? [])->filter(fn($v) => is_numeric($v))->sum() }};
const existingVODays = {{ (int) collect($project->vo_days ?? [])->filter(fn($v) => is_numeric($v))->sum() }};
const existingSODays = {{ (int) ($project->suspension_days ?? 0) }};

function addDaysToDate(dateStr, days) {
    const d = new Date(dateStr + 'T00:00:00');
    d.setDate(d.getDate() + days);
    return d;
}
function formatDate(d) {
    return d.toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric' });
}
function updateTEPreview() {
    const newDays = parseInt(document.getElementById('new_te_days').value) || 0;
    const preview = document.getElementById('te_revised_preview');
    if (newDays < 1) { preview.textContent = 'Enter days above to preview'; return; }
    preview.textContent = formatDate(addDaysToDate(originalExpiry, existingTEDays + existingVODays + existingSODays + newDays));
}
function updateVOPreview() {
    const newDays = parseInt(document.getElementById('new_vo_days').value) || 0;
    const preview = document.getElementById('vo_revised_preview');
    if (newDays < 1) { preview.textContent = 'Enter days above to preview'; return; }
    preview.textContent = formatDate(addDaysToDate(originalExpiry, existingTEDays + existingVODays + existingSODays + newDays));
}
function updateSOPreview() {
    const newDays = parseInt(document.getElementById('new_so_days').value) || 0;
    const preview = document.getElementById('so_revised_preview');
    if (newDays < 1) { preview.textContent = 'Enter days to preview'; return; }
    preview.textContent = formatDate(addDaysToDate(originalExpiry, existingTEDays + existingVODays + existingSODays + newDays));
}

// ── Issuances ──
function checkPerformanceBond() {
    const hasPerformanceBond = [...document.querySelectorAll('#issuances-list select')]
        .some(s => s.value === 'Performance Bond');
    const field = document.getElementById('performance-bond-date-field');
    field.style.display = hasPerformanceBond ? 'block' : 'none';
}

const ISSUANCE_OPTS = ['1st Notice of Negative Slippage','2nd Notice of Negative Slippage','3rd Notice of Negative Slippage','Liquidated Damages','Notice to Terminate','Notice of Expiry'];
function issuanceRowHTML(val = '') {
    let opts = '<option value="">— Select Issuance —</option>';
    ISSUANCE_OPTS.forEach(o => opts += `<option value="${o}" ${o===val?'selected':''}>${o}</option>`);
    return `<div class="dynamic-row">
        <select name="issuances[]" class="dynamic-select" onchange="updateCount('issuances-list','issuance-count'); checkPerformanceBond()">${opts}</select>
        <button type="button" class="remove-btn" onclick="removeIssuanceRow(this)"><i class="fas fa-times"></i></button>
    </div>`;
}
function addIssuanceRow() {
    document.getElementById('issuances-list').insertAdjacentHTML('beforeend', issuanceRowHTML());
    updateCount('issuances-list', 'issuance-count');
}
function removeIssuanceRow(btn) {
    const list = document.getElementById('issuances-list');
    if (list.querySelectorAll('.dynamic-row').length <= 1) {
        list.querySelector('select').value = '';
        updateCount('issuances-list','issuance-count');
        checkPerformanceBond();
        return;
    }
    btn.closest('.dynamic-row').remove();
    updateCount('issuances-list', 'issuance-count');
    checkPerformanceBond();
}
function updateCount(listId, countId) {
    const filled = [...document.getElementById(listId).querySelectorAll('select')].filter(s => s.value !== '').length;
    const chip   = document.getElementById(countId);
    chip.textContent       = filled;
    chip.style.background  = filled > 0 ? 'rgba(249,115,22,0.15)' : 'rgba(249,115,22,0.07)';
    chip.style.color       = filled > 0 ? '#ea580c' : '#9ca3af';
    chip.style.borderColor = filled > 0 ? 'rgba(249,115,22,0.3)' : 'rgba(249,115,22,0.15)';
}

document.addEventListener('DOMContentLoaded', () => {
    computeSlippage();
    updateCount('issuances-list', 'issuance-count');
    calculateLDPerDay();
    checkPerformanceBond();
});
</script>

{{-- EDIT ENTRY MODAL --}}
<x-modal id="edit-entry-modal" title="Edit Entry" type="default" icon="fa-pen" size="md">
    <form id="edit-entry-form" method="POST" action="" style="display:contents;">
        @csrf
        @method('PATCH')
        <input type="hidden" id="edit_entry_type"  name="edit_entry_type"  value="">
        <input type="hidden" id="edit_entry_index" name="edit_entry_index" value="">
        <div style="display:flex; flex-direction:column; gap:1.1rem;">
            <div>
                <label style="display:block; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); margin-bottom:0.4rem;">Entry</label>
                <div id="edit_entry_label_display" style="padding:0.65rem 1rem; border-radius:9px; background:var(--bg-secondary); border:1.5px solid var(--border); font-size:0.875rem; font-weight:700; color:var(--text-primary);">—</div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div>
                    <label for="edit_days" style="display:block; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); margin-bottom:0.4rem;">Days <span style="color:#ef4444;">*</span></label>
                    <input type="number" id="edit_days" name="edit_days" min="1" step="1" required
                           style="width:100%; padding:0.72rem 1rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; color:var(--text-primary); background:var(--bg-primary); outline:none; font-family:'Instrument Sans',sans-serif; transition:border-color 0.2s,box-shadow 0.2s; box-sizing:border-box;"
                           onfocus="this.style.borderColor='var(--orange-500)';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                           onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                    <p style="font-size:0.68rem; color:#9ca3af; margin-top:0.3rem;">Number of days</p>
                </div>
                <div>
                    <label for="edit_date_requested" style="display:block; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); margin-bottom:0.4rem;">Date Requested</label>
                    <input type="date" id="edit_date_requested" name="edit_date_requested"
                           style="width:100%; padding:0.72rem 1rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; color:var(--text-primary); background:var(--bg-primary); outline:none; font-family:'Instrument Sans',sans-serif; transition:border-color 0.2s,box-shadow 0.2s; box-sizing:border-box;"
                           onfocus="this.style.borderColor='var(--orange-500)';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                           onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                    <p style="font-size:0.68rem; color:#9ca3af; margin-top:0.3rem;">Optional</p>
                </div>
            </div>
            <div>
                <label for="edit_cost" style="display:block; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); margin-bottom:0.4rem;">Cost Involved (₱)</label>
                <div style="position:relative;">
                    <span style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-weight:600; pointer-events:none; font-size:0.9rem;">₱</span>
                    <input type="number" id="edit_cost" name="edit_cost" min="-9999999999" step="0.01" placeholder="0.00"
                           style="width:100%; padding:0.72rem 1rem 0.72rem 1.75rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; color:var(--text-primary); background:var(--bg-primary); outline:none; font-family:'Instrument Sans',sans-serif; transition:border-color 0.2s,box-shadow 0.2s; box-sizing:border-box;"
                           onfocus="this.style.borderColor='var(--orange-500)';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                           onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                </div>
                <p style="font-size:0.68rem; color:#9ca3af; margin-top:0.3rem;">Optional</p>
            </div>
        </div>
    </form>
    <x-slot name="footer">
        <button type="button" onclick="closeModal('edit-entry-modal')"
            style="padding:0.6rem 1.2rem; border:1.5px solid var(--border); border-radius:9px; background:var(--bg-primary); color:var(--text-secondary); font-weight:600; font-size:0.85rem; cursor:pointer; font-family:'Instrument Sans',sans-serif; transition:all 0.15s;"
            onmouseover="this.style.borderColor='var(--orange-500)'" onmouseout="this.style.borderColor='var(--border)'">Cancel</button>
        <button type="button" id="edit-entry-save-btn" onclick="submitEditEntry()"
            style="padding:0.6rem 1.4rem; background:var(--orange-500); color:white; border:none; border-radius:9px; font-weight:700; font-size:0.85rem; cursor:pointer; font-family:'Instrument Sans',sans-serif; box-shadow:0 2px 8px rgba(249,115,22,0.3); transition:all 0.15s;"
            onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='var(--orange-500)'">
            <i class="fas fa-save" style="margin-right:0.35rem; font-size:0.75rem;"></i> Save Changes
        </button>
    </x-slot>
</x-modal>

<script>
function openEditModal(type, index, label, days, cost, dateRequested) {
    const panel  = document.getElementById('edit-entry-modal-panel');
    const isVO   = type === 'vo';
    const accent = isVO ? '#6366f1' : '#f97316';
    const iconBadge = panel.querySelector('.fas.fa-pen')?.closest('div');
    if (iconBadge) {
        iconBadge.style.background  = isVO ? 'rgba(99,102,241,0.1)'  : 'rgba(249,115,22,0.1)';
        iconBadge.style.borderColor = isVO ? 'rgba(99,102,241,0.22)' : 'rgba(249,115,22,0.22)';
        iconBadge.querySelector('i').style.color = accent;
    }
    const titleEl = document.getElementById('edit-entry-modal-title');
    if (titleEl) titleEl.textContent = 'Edit ' + label;
    document.getElementById('edit_entry_type').value  = type;
    document.getElementById('edit_entry_index').value = index;
    document.getElementById('edit_entry_label_display').textContent = label;
    document.getElementById('edit_days').value           = days || '';
    document.getElementById('edit_cost').value           = cost || '';
    document.getElementById('edit_date_requested').value = dateRequested || '';
    document.getElementById('edit_days').style.borderColor = isVO ? 'rgba(99,102,241,0.3)' : 'rgba(249,115,22,0.25)';
    const saveBtn = document.getElementById('edit-entry-save-btn');
    saveBtn.style.background = isVO ? '#6366f1' : 'var(--orange-500)';
    saveBtn.style.boxShadow  = isVO ? '0 2px 8px rgba(99,102,241,0.3)' : '0 2px 8px rgba(249,115,22,0.3)';
    saveBtn.onmouseover = () => saveBtn.style.background = isVO ? '#4f46e5' : '#ea580c';
    saveBtn.onmouseout  = () => saveBtn.style.background = isVO ? '#6366f1' : 'var(--orange-500)';
    openModal('edit-entry-modal');
}
function submitEditEntry() {
    const type  = document.getElementById('edit_entry_type').value;
    const index = document.getElementById('edit_entry_index').value;
    const days  = document.getElementById('edit_days').value;
    if (!days || parseInt(days) < 1) {
        document.getElementById('edit_days').style.borderColor = '#ef4444';
        document.getElementById('edit_days').focus();
        return;
    }
    const form  = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.projects.updateEntry", $project) }}';
    const fields = {
        '_token': '{{ csrf_token() }}', '_method': 'PATCH',
        'edit_entry_type': type, 'edit_entry_index': index, 'edit_days': days,
        'edit_cost': document.getElementById('edit_cost').value,
        'edit_date_requested': document.getElementById('edit_date_requested').value,
    };
    Object.entries(fields).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = name; input.value = value;
        form.appendChild(input);
    });
    document.body.appendChild(form);
    form.submit();
}
</script>

{{-- DELETE ENTRY MODAL --}}
<x-modal id="delete-entry-modal" title="Delete Entry" type="danger" icon="fa-trash-alt" size="md">
    <div style="display:flex; flex-direction:column; gap:1.1rem;">
        <div style="display:flex; align-items:flex-start; gap:0.875rem; padding:1rem; background:rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.15); border-radius:10px;">
            <div style="width:34px; height:34px; border-radius:9px; flex-shrink:0; background:rgba(239,68,68,0.1); border:1.5px solid rgba(239,68,68,0.2); display:flex; align-items:center; justify-content:center;">
                <i class="fas fa-exclamation-triangle" style="color:#dc2626; font-size:0.8rem;"></i>
            </div>
            <div>
                <p style="font-size:0.875rem; font-weight:700; color:var(--text-primary); margin:0 0 3px;">This cannot be undone</p>
                <p style="font-size:0.78rem; color:var(--text-secondary); margin:0; line-height:1.5;">The entry will be permanently removed and remaining entries renumbered. Your reason will be saved to the project's remarks.</p>
            </div>
        </div>
        <div>
            <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); margin:0 0 0.4rem;">Entry to delete</p>
            <div style="display:flex; align-items:center; justify-content:space-between; padding:0.65rem 1rem; border-radius:9px; background:var(--bg-secondary); border:1.5px solid rgba(239,68,68,0.2);">
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <i id="del-entry-icon" class="fas fa-clock" style="color:#dc2626; font-size:0.75rem;"></i>
                    <span id="del-entry-label" style="font-size:0.875rem; font-weight:700; color:var(--text-primary);">—</span>
                </div>
                <span id="del-entry-days" style="font-size:0.75rem; font-weight:700; padding:2px 10px; border-radius:99px; background:rgba(239,68,68,0.1); color:#dc2626; border:1px solid rgba(239,68,68,0.2);">—</span>
            </div>
        </div>
        <div>
            <label for="del-reason-input" style="display:block; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); margin-bottom:0.4rem;">
                Reason for deletion <span style="color:#ef4444;">*</span>
            </label>
            <textarea id="del-reason-input" rows="3" maxlength="1000" placeholder="Explain why this entry is being deleted…" oninput="delClearError()"
                      style="width:100%; padding:0.75rem 1rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.855rem; color:var(--text-primary); background:var(--bg-primary); outline:none; resize:none; font-family:'Instrument Sans',sans-serif; transition:border-color 0.2s,box-shadow 0.2s; box-sizing:border-box;"
                      onfocus="this.style.borderColor='#ef4444';this.style.boxShadow='0 0 0 3px rgba(239,68,68,0.1)'"
                      onblur="this.style.borderColor=this.value?'rgba(239,68,68,0.4)':'var(--border)';this.style.boxShadow='none'"></textarea>
            <p id="del-reason-error" style="display:none; font-size:0.75rem; color:#ef4444; margin-top:0.35rem; align-items:center; gap:0.3rem;">
                <i class="fas fa-exclamation-circle"></i> Please provide a reason before deleting.
            </p>
            <p style="font-size:0.68rem; color:#9ca3af; margin-top:0.3rem; text-align:right;"><span id="del-reason-count">0</span>/1000</p>
        </div>
        <div id="del-renumber-notice" style="display:none; align-items:center; gap:0.5rem; padding:0.6rem 0.875rem; border-radius:8px; background:rgba(249,115,22,0.05); border:1px solid rgba(249,115,22,0.15);">
            <i class="fas fa-sort-numeric-down" style="color:#f97316; font-size:0.75rem; flex-shrink:0;"></i>
            <p style="font-size:0.75rem; color:var(--text-secondary); margin:0;" id="del-renumber-text">Remaining entries will be renumbered from <strong style="color:var(--text-primary);">1</strong>.</p>
        </div>
    </div>
    <x-slot name="footer">
        <button type="button" onclick="closeModal('delete-entry-modal')"
                style="padding:0.65rem 1.25rem; border:1.5px solid var(--border); border-radius:9px; background:var(--bg-primary); color:var(--text-secondary); font-weight:600; font-size:0.835rem; cursor:pointer; font-family:'Instrument Sans',sans-serif; transition:all 0.15s; display:inline-flex; align-items:center; gap:0.4rem;"
                onmouseover="this.style.borderColor='var(--orange-500)'" onmouseout="this.style.borderColor='var(--border)'">
            <i class="fas fa-times" style="font-size:0.75rem;"></i> Cancel
        </button>
        <button type="button" id="del-confirm-btn" onclick="submitDeleteEntry()"
                style="padding:0.65rem 1.4rem; background:#dc2626; color:white; border:none; border-radius:9px; font-weight:700; font-size:0.835rem; cursor:pointer; font-family:'Instrument Sans',sans-serif; box-shadow:0 3px 12px rgba(220,38,38,0.3); display:inline-flex; align-items:center; gap:0.45rem; transition:all 0.15s;"
                onmouseover="this.style.background='#b91c1c';this.style.transform='translateY(-1px)'"
                onmouseout="this.style.background='#dc2626';this.style.transform='translateY(0)'">
            <i class="fas fa-trash-alt" style="font-size:0.75rem;"></i> Delete Entry
        </button>
    </x-slot>
</x-modal>

<script>
window._delType  = 'te';
window._delIndex = 0;
const _totalTECount = {{ $teCount }};
const _totalVOCount = {{ $voCount }};

document.getElementById('del-reason-input').addEventListener('input', function () {
    document.getElementById('del-reason-count').textContent = this.value.length;
});

function openDeleteModal(type, index, label, days) {
    window._delType  = type;
    window._delIndex = index;
    const isVO = type === 'vo';
    document.getElementById('del-entry-icon').className = 'fas ' + (isVO ? 'fa-file-signature' : 'fa-clock');
    document.getElementById('del-entry-icon').style.color = '#dc2626';
    document.getElementById('del-entry-label').textContent = label;
    document.getElementById('del-entry-days').textContent  = '+' + days + 'd';
    const titleEl = document.getElementById('delete-entry-modal-title');
    if (titleEl) titleEl.textContent = 'Delete ' + label;
    const totalOfType = isVO ? _totalVOCount : _totalTECount;
    const notice = document.getElementById('del-renumber-notice');
    if (totalOfType > 1) {
        notice.style.display = 'flex';
        document.getElementById('del-renumber-text').innerHTML =
            'Remaining <strong style="color:var(--text-primary);">' + (isVO ? 'Variation Orders' : 'Time Extensions') +
            '</strong> will be renumbered from <strong style="color:var(--text-primary);">1</strong>.';
    } else {
        notice.style.display = 'none';
    }
    const textarea = document.getElementById('del-reason-input');
    textarea.value = '';
    textarea.style.borderColor = 'var(--border)';
    textarea.style.boxShadow   = 'none';
    document.getElementById('del-reason-count').textContent = '0';
    document.getElementById('del-reason-error').style.display = 'none';
    const btn = document.getElementById('del-confirm-btn');
    btn.innerHTML = '<i class="fas fa-trash-alt" style="font-size:0.75rem;"></i> Delete Entry';
    btn.disabled  = false;
    btn.style.opacity = '1';
    openModal('delete-entry-modal');
}

function delClearError() {
    document.getElementById('del-reason-error').style.display = 'none';
    document.getElementById('del-reason-input').style.borderColor = 'var(--border)';
}

function submitDeleteEntry() {
    const reason = document.getElementById('del-reason-input').value.trim();
    if (!reason) {
        const textarea = document.getElementById('del-reason-input');
        textarea.style.borderColor = '#ef4444';
        textarea.style.boxShadow   = '0 0 0 3px rgba(239,68,68,0.12)';
        textarea.focus();
        document.getElementById('del-reason-error').style.display = 'flex';
        return;
    }
    const btn = document.getElementById('del-confirm-btn');
    btn.innerHTML  = '<i class="fas fa-spinner fa-spin" style="font-size:0.75rem;"></i> Deleting…';
    btn.disabled   = true;
    btn.style.opacity = '0.75';
    const form  = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.projects.destroyEntry", $project) }}';
    const fields = {
        '_token': '{{ csrf_token() }}', '_method': 'DELETE',
        'entry_type': window._delType, 'entry_index': window._delIndex,
        'delete_reason': reason,
    };
    Object.entries(fields).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = name; input.value = value;
        form.appendChild(input);
    });
    document.body.appendChild(form);
    form.submit();
}
</script>
</x-app-layout>