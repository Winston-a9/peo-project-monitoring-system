<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="app-page-title">
                <span class="app-icon-badge"><i class="fas fa-folder"></i></span>
                {{ $project->project_title }}
            </h2>
            <p class="app-page-subtitle">
                <i class="fas fa-map-marker-alt" style="color:#f97316; font-size:0.7rem; margin-right:0.3rem;"></i>
                {{ $project->location }} · <i class="fas fa-building" style="font-size:0.7rem; margin:0 0.3rem;"></i> {{ $project->contractor }}
            </p>
        </div>
        <div class="app-header-actions">
            <a href="{{ route('user.projects.index') }}" class="app-btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</x-slot>

@php
    $today    = now();
    $expiry   = $project->revised_contract_expiry ?? $project->original_contract_expiry;
    $daysLeft = (int) $today->diffInDays($expiry, false);

    $issuances        = $project->issuances        ?? [];
    $documentsPressed = $project->documents_pressed ?? [];
    $extensionDays    = $project->extension_days    ?? [];
    $issuances        = is_array($issuances)        ? array_values(array_filter($issuances))        : [];
    $documentsPressed = is_array($documentsPressed) ? array_values(array_filter($documentsPressed)) : [];
    $extensionDays    = is_array($extensionDays)    ? $extensionDays                                : [];
    $totalDays = (int) array_sum(array_map(fn($d) => (int) $d, array_filter((array) $extensionDays)));
@endphp

<style>
    :root {
        --orange-500:#f97316; --orange-600:#ea580c;
        --ink:#1a0f00; --ink-muted:#6b4f35;
        --border:rgba(249,115,22,0.14);
        --bg-primary:#ffffff; --bg-secondary:#fffaf5;
        --text-primary:#1a0f00; --text-secondary:#6b4f35;
        --shadow-sm:0 1px 3px rgba(26,15,0,0.06);
    }
    .dark, html.dark {
        --bg-primary:#0f0f0f; --bg-secondary:#1a1a1a;
        --text-primary:#f5f5f0; --text-secondary:#9ca3af;
        --ink:#f5f5f0; --ink-muted:#9ca3af;
        --border:rgba(249,115,22,0.25);
        --shadow-sm:0 1px 3px rgba(0,0,0,0.25);
    }
    body { color:var(--text-primary); transition:background 0.3s,color 0.3s; }

    /* Header Styles */
    .app-page-title {
        font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem;
        letter-spacing:-0.03em; color:var(--text-primary);
        display:flex; align-items:center; gap:0.6rem; margin:0;
    }
    .app-icon-badge {
        background:#f97316; width:34px; height:34px; border-radius:9px;
        display:inline-flex; align-items:center; justify-content:center;
        box-shadow:0 2px 10px rgba(249,115,22,0.35); color:white;
    }
    .app-page-subtitle {
        color:var(--text-secondary); font-size:0.82rem; margin:3px 0 0 0;
    }
    .app-header-actions {
        display:flex; gap:0.6rem; align-items:center;
    }
    .app-btn-secondary {
        display:inline-flex; align-items:center; gap:0.4rem;
        padding:0.6rem 1rem; border:1.5px solid var(--border);
        border-radius:9px; font-weight:600; font-size:0.825rem;
        color:var(--text-secondary); text-decoration:none;
        background:var(--bg-secondary); transition:all 0.2s;
        cursor:pointer; font-family:'Instrument Sans',sans-serif;
    }
    .app-btn-secondary:hover {
        border-color:var(--orange-500);
        background:rgba(249,115,22,0.08);
        color:var(--orange-600);
    }
    .app-btn-theme {
        background:var(--bg-secondary); border:1.5px solid var(--border);
        border-radius:10px; padding:0.5rem 0.95rem; cursor:pointer;
        display:flex; align-items:center; gap:0.5rem; color:var(--text-primary);
        font-size:0.9rem; font-weight:500; font-family:'Instrument Sans',sans-serif;
        box-shadow:0 2px 8px rgba(0,0,0,0.05); transition:all 0.3s ease;
    }
    .app-btn-theme:hover {
        background:rgba(249,115,22,0.12);
    }
    .app-btn-theme i { color:#f97316; }

    /* Cards & Sections */
    .card {
        background:var(--bg-primary); border:1px solid var(--border);
        border-radius:14px; overflow:hidden; box-shadow:var(--shadow-sm);
    }
    .card-header {
        padding:0.875rem 1.25rem; border-bottom:1px solid var(--border);
        background:var(--bg-secondary); display:flex; align-items:center; gap:0.5rem;
    }
    .card-title {
        font-family:'Syne',sans-serif; font-weight:700; font-size:0.825rem;
        color:var(--ink); letter-spacing:-0.01em;
    }

    /* Section Eyebrow */
    .section-eyebrow {
        font-size:0.65rem; font-weight:700; text-transform:uppercase;
        letter-spacing:0.1em; color:var(--ink-muted); opacity:0.65;
        margin:0 0 0.6rem; display:flex; align-items:center; gap:0.6rem;
    }
    .section-eyebrow::after {
        content:''; flex:1; height:1px; background:var(--border);
    }

    /* Data Display */
    .data-row {
        display:flex; align-items:center; justify-content:space-between;
        padding:0.7rem 1.25rem; border-bottom:1px solid rgba(249,115,22,0.06); gap:1rem;
    }
    .data-row:last-child { border-bottom:none; }
    .data-label {
        font-size:0.72rem; font-weight:700; text-transform:uppercase;
        letter-spacing:0.06em; color:var(--ink-muted); white-space:nowrap;
        display:flex; align-items:center; gap:0.45rem;
    }
    .data-label i {
        color:var(--orange-500); font-size:0.7rem; width:12px;
        text-align:center; opacity:0.8;
    }
    .data-value {
        font-size:0.855rem; font-weight:700; color:var(--text-primary); text-align:right;
    }
    .data-sub {
        font-size:0.7rem; color:var(--text-secondary); margin-top:1px; text-align:right;
    }

    /* Pills */
    .pill {
        display:inline-flex; align-items:center; gap:0.3rem;
        padding:3px 10px; border-radius:99px; font-size:0.7rem; font-weight:700;
    }
    .pill-orange { background:rgba(249,115,22,0.1); color:#ea580c; border:1px solid rgba(249,115,22,0.2); }
    .pill-indigo { background:rgba(99,102,241,0.1); color:#6366f1; border:1px solid rgba(99,102,241,0.2); }
    .pill-amber  { background:rgba(234,179,8,0.1); color:#b45309; border:1px solid rgba(234,179,8,0.22); }
    .pill-green  { background:rgba(22,163,74,0.1); color:#16a34a; border:1px solid rgba(22,163,74,0.22); }
    .pill-red    { background:rgba(239,68,68,0.1); color:#dc2626; border:1px solid rgba(239,68,68,0.2); }
    .pill-blue   { background:rgba(37,99,235,0.1); color:#2563eb; border:1px solid rgba(37,99,235,0.2); }
    .pill-gray   { background:rgba(156,163,175,0.08); color:#9ca3af; border:1px solid rgba(156,163,175,0.15); }

    /* Stats */
    .stat-block { padding:1.1rem 1.25rem; }
    .stat-num {
        font-family:'Syne',sans-serif; font-weight:800;
        letter-spacing:-0.04em; line-height:1;
    }

    /* Badges & Pills Legacy */
    .badge { display:inline-flex; align-items:center; gap:0.35rem; padding:3px 10px; border-radius:99px; font-size:0.72rem; font-weight:700; border:1px solid; }
    .badge-green  { background:rgba(34,197,94,0.1); color:#16a34a; border-color:rgba(22,163,74,0.22); }
    .badge-red    { background:rgba(239,68,68,0.1); color:#dc2626; border-color:rgba(239,68,68,0.2); }
    .badge-blue   { background:rgba(59,130,246,0.1); color:#2563eb; border-color:rgba(37,99,235,0.2); }
    .badge-amber  { background:rgba(234,179,8,0.1); color:#b45309; border-color:rgba(234,179,8,0.22); }

    /* Progress */
    .prog-track { height:5px; background:rgba(249,115,22,0.1); border-radius:99px; margin-top:0.5rem; overflow:hidden; }
    .prog-fill  { height:100%; border-radius:99px; }

    /* Documents & Issues */
    .doc-row { display:flex; align-items:center; justify-content:space-between; gap:0.75rem; padding:0.6rem 0.875rem; background:var(--bg-secondary); border:1px solid var(--border); border-radius:9px; }
    .doc-row-left { display:flex; align-items:center; gap:0.5rem; min-width:0; }
    .doc-row-name { font-size:0.845rem; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .days-pill { display:inline-flex; align-items:center; gap:0.3rem; padding:3px 10px; border-radius:99px; white-space:nowrap; flex-shrink:0; font-size:0.72rem; font-weight:700; background:rgba(249,115,22,0.1); color:var(--orange-600); border:1px solid rgba(249,115,22,0.22); }
    .days-pill.na { background:rgba(156,163,175,0.1); color:#9ca3af; border-color:rgba(156,163,175,0.2); }
    .issue-row { display:flex; align-items:center; gap:0.6rem; padding:0.6rem 0.875rem; background:var(--bg-secondary); border:1px solid var(--border); border-radius:9px; }

    /* Animations */
    @keyframes fadeUp { from{opacity:0;transform:translateY(12px);} to{opacity:1;transform:translateY(0);} }
    @keyframes pulse  { 0%,100%{opacity:1;} 50%{opacity:0.4;} }
    .fade-up  { animation:fadeUp 0.4s ease both; }
    .delay-1  { animation-delay:0.05s; }
    .delay-2  { animation-delay:0.10s; }
    .delay-3  { animation-delay:0.15s; }
    .delay-4  { animation-delay:0.20s; }
    .delay-5  { animation-delay:0.25s; }

    /* Responsive */
    @media (max-width:768px) {
        .app-page-title { font-size:1.35rem; }
        .app-header-actions { flex-wrap:wrap; gap:0.4rem; }
    }
</style>

<div class="max-w-5xl mx-auto space-y-5 fade-up">

    {{-- ── Status + Progress ── --}}
    <div style="display:grid; grid-template-columns:1fr 2fr; gap:1.25rem;">

        <div class="view-card" style="padding:1.5rem; display:flex; flex-direction:column; justify-content:space-between; gap:1rem;">
            <p style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); display:flex; align-items:center; gap:0.4rem;">
                <i class="fas fa-briefcase" style="color:var(--orange-500);"></i> Contract Status
            </p>
            @if($project->status === 'completed')
                <div>
                    <p style="font-family:'Syne',sans-serif; font-size:1.4rem; font-weight:800; color:#16a34a; display:flex; align-items:center; gap:0.4rem;">
                        <i class="fas fa-check-circle"></i> Completed
                    </p>
                    @if($project->completed_at)
                        <p style="font-size:0.8rem; color:#22c55e; margin-top:0.3rem;">on {{ $project->completed_at->format('F d, Y') }}</p>
                    @endif
                </div>
                <div class="prog-track"><div class="prog-fill" style="background:#22c55e; width:100%;"></div></div>
            @elseif($daysLeft < 0)
                <div>
                    <p style="font-family:'Syne',sans-serif; font-size:1.4rem; font-weight:800; color:#dc2626; display:flex; align-items:center; gap:0.4rem;">
                        <i class="fas fa-times-circle"></i> Expired
                    </p>
                    <p style="font-size:0.8rem; color:#ef4444; margin-top:0.3rem;">{{ round(abs($daysLeft)) }} days ago</p>
                </div>
                <div class="prog-track"><div class="prog-fill" style="background:#ef4444; width:100%;"></div></div>
            @elseif($daysLeft < 30)
                <div>
                    <p style="font-family:'Syne',sans-serif; font-size:1.4rem; font-weight:800; color:#d97706; display:flex; align-items:center; gap:0.4rem;">
                        <i class="fas fa-clock"></i> Expiring Soon
                    </p>
                    <p style="font-size:0.8rem; color:#f59e0b; margin-top:0.3rem;">{{ round($daysLeft) }} days remaining</p>
                </div>
                <div class="prog-track"><div class="prog-fill" style="background:#f59e0b; width:{{ ($daysLeft/30)*100 }}%;"></div></div>
            @else
                <div>
                    <p style="font-family:'Syne',sans-serif; font-size:1.4rem; font-weight:800; color:#16a34a; display:flex; align-items:center; gap:0.4rem;">
                        <i class="fas fa-check-circle"></i> Active
                    </p>
                    <p style="font-size:0.8rem; color:#22c55e; margin-top:0.3rem;">{{ $daysLeft }} days remaining</p>
                </div>
                <div class="prog-track"><div class="prog-fill" style="background:#22c55e; width:100%;"></div></div>
            @endif
        </div>

        <div class="view-card" style="padding:1.5rem; background:linear-gradient(135deg, rgba(249,115,22,0.04) 0%, rgba(249,115,22,0.02) 100%);">
            <p style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); display:flex; align-items:center; gap:0.4rem; margin-bottom:1.1rem;">
                <i class="fas fa-chart-bar" style="color:var(--orange-500);"></i> Progress Overview
            </p>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
                <!-- As Planned -->
                <div style="background:var(--bg-primary); border:2px solid transparent; border-image:linear-gradient(135deg, rgba(249,115,22,0.3), rgba(249,115,22,0.1)) 0 1; border-radius:12px; padding:1.25rem; text-align:center; transition:all 0.3s; position:relative; overflow:hidden;" onmouseover="this.style.boxShadow='0 8px 24px rgba(249,115,22,0.15)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)'">
                    <div style="position:absolute; top:-50%; right:-50%; width:200px; height:200px; background:rgba(249,115,22,0.05); border-radius:50%; pointer-events:none;"></div>
                    <div style="display:flex; align-items:center; justify-content:center; margin-bottom:0.75rem; position:relative; z-index:1;">
                        <div style="width:48px; height:48px; background:linear-gradient(135deg, rgba(249,115,22,0.2), rgba(249,115,22,0.1)); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-list-check" style="color:var(--orange-500); font-size:1.2rem;"></i>
                        </div>
                    </div>
                    <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); margin-bottom:0.5rem; position:relative; z-index:1;">As Planned</p>
                    <p style="font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; color:var(--orange-500); letter-spacing:-0.03em; line-height:1; position:relative; z-index:1;">
                        {{ $project->as_planned }}<span style="font-size:1rem; color:var(--ink-muted);">%</span>
                    </p>
                    <div style="margin-top:1rem; position:relative; z-index:1;">
                        <div style="height:8px; background:rgba(249,115,22,0.1); border-radius:99px; overflow:hidden; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                            <div style="height:100%; background:linear-gradient(90deg, var(--orange-500), #f97316); width:{{ $project->as_planned }}%; border-radius:99px; transition:width 0.6s ease; box-shadow: 0 0 10px rgba(249,115,22,0.4);"></div>
                        </div>
                    </div>
                </div>
                <!-- Work Done -->
                <div style="background:var(--bg-primary); border:2px solid transparent; border-image:linear-gradient(135deg, rgba(59,130,246,0.3), rgba(59,130,246,0.1)) 0 1; border-radius:12px; padding:1.25rem; text-align:center; transition:all 0.3s; position:relative; overflow:hidden;" onmouseover="this.style.boxShadow='0 8px 24px rgba(59,130,246,0.15)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)'">
                    <div style="position:absolute; top:-50%; right:-50%; width:200px; height:200px; background:rgba(59,130,246,0.05); border-radius:50%; pointer-events:none;"></div>
                    <div style="display:flex; align-items:center; justify-content:center; margin-bottom:0.75rem; position:relative; z-index:1;">
                        <div style="width:48px; height:48px; background:linear-gradient(135deg, rgba(59,130,246,0.2), rgba(59,130,246,0.1)); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-hammer" style="color:#3b82f6; font-size:1.2rem;"></i>
                        </div>
                    </div>
                    <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); margin-bottom:0.5rem; position:relative; z-index:1;">Work Done</p>
                    <p style="font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; color:#3b82f6; letter-spacing:-0.03em; line-height:1; position:relative; z-index:1;">
                        {{ $project->work_done }}<span style="font-size:1rem; color:var(--ink-muted);">%</span>
                    </p>
                    <div style="margin-top:1rem; position:relative; z-index:1;">
                        <div style="height:8px; background:rgba(59,130,246,0.1); border-radius:99px; overflow:hidden; box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);">
                            <div style="height:100%; background:linear-gradient(90deg, #3b82f6, #60a5fa); width:{{ $project->work_done }}%; border-radius:99px; transition:width 0.6s ease; box-shadow: 0 0 10px rgba(59,130,246,0.4);"></div>
                        </div>
                    </div>
                </div>
                <!-- Slippage -->
                <div style="background:var(--bg-primary); border:2px solid transparent; border-image:linear-gradient(135deg, {{ $project->slippage < 0 ? 'rgba(239,68,68,0.3), rgba(239,68,68,0.1)' : ($project->slippage > 0 ? 'rgba(34,197,94,0.3), rgba(34,197,94,0.1)' : 'rgba(107,79,53,0.3), rgba(107,79,53,0.1)') }}) 0 1; border-radius:12px; padding:1.25rem; text-align:center; transition:all 0.3s; position:relative; overflow:hidden;" onmouseover="this.style.boxShadow='0 8px 24px {{ $project->slippage < 0 ? 'rgba(239,68,68,0.15)' : ($project->slippage > 0 ? 'rgba(34,197,94,0.15)' : 'rgba(107,79,53,0.15)') }}'; this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)'">
                    <div style="position:absolute; top:-50%; right:-50%; width:200px; height:200px; background:{{ $project->slippage < 0 ? 'rgba(239,68,68,0.05)' : ($project->slippage > 0 ? 'rgba(34,197,94,0.05)' : 'rgba(107,79,53,0.05)') }}; border-radius:50%; pointer-events:none;"></div>
                    <div style="display:flex; align-items:center; justify-content:center; margin-bottom:0.75rem; position:relative; z-index:1;">
                        <div style="width:48px; height:48px; background:{{ $project->slippage < 0 ? 'rgba(239,68,68,0.15)' : ($project->slippage > 0 ? 'rgba(34,197,94,0.15)' : 'rgba(107,79,53,0.15)') }}; border-radius:12px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas {{ $project->slippage < 0 ? 'fa-arrow-down' : ($project->slippage > 0 ? 'fa-arrow-up' : 'fa-minus') }}" style="color:{{ $project->slippage < 0 ? '#ef4444' : ($project->slippage > 0 ? '#22c55e' : '#9ca3af') }}; font-size:1.2rem;"></i>
                        </div>
                    </div>
                    <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); margin-bottom:0.5rem; position:relative; z-index:1;">Slippage</p>
                    <p style="font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; color:{{ $project->slippage < 0 ? '#ef4444' : ($project->slippage > 0 ? '#22c55e' : '#9ca3af') }}; letter-spacing:-0.03em; line-height:1; position:relative; z-index:1;">
                        {{ $project->slippage > 0 ? '+' : '' }}{{ $project->slippage }}<span style="font-size:1rem;">%</span>
                    </p>
                    <p style="font-size:0.75rem; margin-top:0.75rem; font-weight:600; color:{{ $project->slippage < 0 ? '#ef4444' : ($project->slippage > 0 ? '#22c55e' : '#9ca3af') }}; position:relative; z-index:1;">
                        @if($project->slippage < 0) <i class="fas fa-triangle-exclamation" style="margin-right:0.3rem;"></i>Behind Schedule
                        @elseif($project->slippage > 0) <i class="fas fa-star" style="margin-right:0.3rem;"></i>Ahead of Schedule
                        @else <i class="fas fa-check" style="margin-right:0.3rem;"></i>On Track @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Project Info + Dates ── --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">

        <div class="view-card">
            <div class="card-header">
                <i class="fas fa-circle-info" style="color:var(--orange-500); font-size:0.85rem;"></i>
                <span>Project Information</span>
            </div>
            <div>
                @php $fields = [
                    ['fas fa-user-tie',       'In Charge',       $project->in_charge],
                    ['fas fa-folder',          'Project Title',   $project->project_title],
                    ['fas fa-map-marker-alt',  'Location',        $project->location],
                    ['fas fa-building',        'Contractor',      $project->contractor],
                    ['fas fa-peso-sign',       'Contract Amount', '₱'.number_format($project->contract_amount,2)],
                ]; @endphp
                @foreach($fields as [$icon,$key,$val])
                <div class="info-row">
                    <div class="info-icon"><i class="{{ $icon }}" style="color:var(--orange-500); font-size:0.8rem;"></i></div>
                    <div><p class="info-key">{{ $key }}</p><p class="info-val">{{ $val }}</p></div>
                </div>
                @endforeach
                <div class="info-row">
                    <div class="info-icon"><i class="fas fa-circle-dot" style="color:var(--orange-500); font-size:0.8rem;"></i></div>
                    <div>
                        <p class="info-key">Status</p>
                        <div style="display:flex; align-items:center; gap:0.5rem; margin-top:2px;">
                            @if($project->status === 'completed')
                                <span class="badge badge-green"><i class="fas fa-check-circle"></i> Completed</span>
                                @if($project->completed_at)
                                    <span style="font-size:0.75rem; color:var(--ink-muted);">on {{ $project->completed_at->format('F d, Y') }}</span>
                                @endif
                            @elseif($project->status === 'expired')
                                <span class="badge badge-red"><i class="fas fa-times-circle"></i> Expired</span>
                            @else
                                <span class="badge badge-blue"><i class="fas fa-spinner fa-spin"></i> Ongoing</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:1.25rem;">
            <div class="view-card">
                <div class="card-header">
                    <i class="fas fa-calendar-days" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Contract Dates</span>
                </div>
                <div>
                    @php $dates = [
                        ['fas fa-calendar-check','Date Started',     $project->date_started->format('F d, Y'),                     $project->date_started->format('l')],
                        ['fas fa-calendar-times','Original Expiry',  $project->original_contract_expiry->format('F d, Y'),          $project->original_contract_expiry->format('l')],
                        ['fas fa-calendar-pen',  'Revised Expiry',   $project->revised_contract_expiry?$project->revised_contract_expiry->format('F d, Y'):null, $project->revised_contract_expiry?$project->revised_contract_expiry->format('l'):null],
                    ]; @endphp
                    @foreach($dates as [$icon,$label,$date,$day])
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:0.75rem 1.5rem; border-bottom:1px solid rgba(249,115,22,0.07);">
                        <div style="display:flex; align-items:center; gap:0.6rem;">
                            <i class="{{ $icon }}" style="color:var(--orange-500); font-size:0.8rem; width:14px; text-align:center;"></i>
                            <p style="font-size:0.82rem; color:var(--ink-muted); font-weight:500;">{{ $label }}</p>
                        </div>
                        @if($date)
                            <div style="text-align:right;">
                                <p style="font-size:0.855rem; font-weight:700; color:var(--ink);">{{ $date }}</p>
                                <p style="font-size:0.7rem; color:#9ca3af;">{{ $day }}</p>
                            </div>
                        @else
                            <p style="font-size:0.82rem; color:#9ca3af; font-style:italic;">Not set</p>
                        @endif
                    </div>
                    @endforeach
                    @if($totalDays > 0)
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:0.7rem 1.5rem; background:rgba(249,115,22,0.03);">
                        <div style="display:flex; align-items:center; gap:0.6rem;">
                            <i class="fas fa-calendar-plus" style="color:var(--orange-500); font-size:0.8rem; width:14px; text-align:center;"></i>
                            <p style="font-size:0.82rem; color:var(--ink-muted); font-weight:500;">Total Extension</p>
                        </div>
                        <span style="display:inline-flex; align-items:center; gap:0.3rem; padding:3px 10px; border-radius:99px; font-size:0.72rem; font-weight:700; background:rgba(249,115,22,0.1); color:var(--orange-600); border:1px solid rgba(249,115,22,0.2);">
                            <i class="fas fa-clock" style="font-size:0.6rem;"></i> {{ $totalDays }} days
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="view-card">
                <div class="card-header">
                    <i class="fas fa-history" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Timeline</span>
                </div>
                <div style="padding:1.25rem 1.5rem; display:flex; flex-direction:column; gap:0;">
                    <div style="display:flex; gap:1rem;">
                        <div style="display:flex; flex-direction:column; align-items:center;">
                            <div style="width:12px; height:12px; background:var(--orange-500); border-radius:50%; flex-shrink:0; margin-top:3px;"></div>
                            <div style="width:2px; height:32px; background:rgba(249,115,22,0.15); margin:3px 0;"></div>
                        </div>
                        <div style="padding-bottom:0.75rem;">
                            <p style="font-size:0.855rem; font-weight:700; color:var(--ink);">Created</p>
                            <p style="font-size:0.75rem; color:var(--ink-muted); margin-top:2px;">{{ $project->created_at->format('F d, Y \a\t H:i') }}</p>
                        </div>
                    </div>
                    <div style="display:flex; gap:1rem;">
                        <div style="width:12px; height:12px; background:#3b82f6; border-radius:50%; flex-shrink:0; margin-top:3px;"></div>
                        <div>
                            <p style="font-size:0.855rem; font-weight:700; color:var(--ink);">Last Updated</p>
                            <p style="font-size:0.75rem; color:var(--ink-muted); margin-top:2px;">{{ $project->updated_at->format('F d, Y \a\t H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Documents Pressed + Issuances ── --}}
    @if(count($documentsPressed) > 0 || count($issuances) > 0)
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">

        {{-- Documents Pressed --}}
        @if(count($documentsPressed) > 0)
        <div class="view-card">
            <div class="card-header" style="justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <i class="fas fa-file-contract" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Documents Pressed</span>
                </div>
                <span style="display:inline-flex; align-items:center; padding:2px 10px; border-radius:99px; font-size:0.68rem; font-weight:700; background:rgba(249,115,22,0.1); color:var(--orange-600); border:1px solid rgba(249,115,22,0.2);">
                    {{ count($documentsPressed) }}
                </span>
            </div>
            <div style="padding:1rem 1.5rem; display:flex; flex-direction:column; gap:0.5rem;">
                @foreach($documentsPressed as $i => $doc)
                @php $days = $extensionDays[$i] ?? null; $isTE = str_starts_with($doc, 'Time Extension'); @endphp
                <div class="doc-row">
                    <div class="doc-row-left">
                        <div style="width:28px; height:28px; background:rgba(249,115,22,0.1); border-radius:7px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="fas fa-file-alt" style="color:var(--orange-500); font-size:0.7rem;"></i>
                        </div>
                        <span class="doc-row-name">{{ $doc }}</span>
                    </div>
                    @if($isTE)
                        <span class="{{ $days ? 'days-pill' : 'days-pill na' }}">
                            <i class="fas fa-clock" style="font-size:0.6rem;"></i>
                            {{ $days ? $days.' days' : 'No days' }}
                        </span>
                    @endif
                </div>
                @endforeach
                @if($totalDays > 0)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:0.65rem 0.875rem; border-radius:9px; margin-top:0.25rem; background:rgba(249,115,22,0.05); border:1px solid rgba(249,115,22,0.14);">
                    <span style="font-size:0.78rem; font-weight:700; color:var(--ink-muted);">Total Extension Days</span>
                    <span style="font-family:'Syne',sans-serif; font-size:1rem; font-weight:800; color:var(--orange-600);">{{ $totalDays }} days</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Issuances --}}
        @if(count($issuances) > 0)
        <div class="view-card">
            <div class="card-header" style="justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:0.5rem;">
                    <i class="fas fa-paper-plane" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Issuances</span>
                </div>
                <span style="display:inline-flex; align-items:center; padding:2px 10px; border-radius:99px; font-size:0.68rem; font-weight:700; background:rgba(239,68,68,0.08); color:#dc2626; border:1px solid rgba(239,68,68,0.18);">
                    {{ count($issuances) }}
                </span>
            </div>
            <div style="padding:1rem 1.5rem; display:flex; flex-direction:column; gap:0.5rem;">
                @foreach($issuances as $iss)
                <div class="issue-row">
                    <div style="width:28px; height:28px; background:rgba(239,68,68,0.08); border-radius:7px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-exclamation-circle" style="color:#dc2626; font-size:0.7rem;"></i>
                    </div>
                    <span style="font-size:0.845rem; font-weight:600; color:var(--ink);">{{ $iss }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
    @endif

    {{-- ── Activity Logs ── --}}
    <div class="view-card">
        <div class="card-header" style="justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:0.5rem;">
                <i class="fas fa-clock-rotate-left" style="color:var(--orange-500); font-size:0.85rem;"></i>
                <span>Activity Logs</span>
            </div>
            @if($project->logs->count())
                <span style="display:inline-flex; align-items:center; gap:0.35rem; padding:3px 12px; border-radius:99px; font-size:0.72rem; font-weight:700; background:rgba(249,115,22,0.1); color:var(--orange-600); border:1px solid rgba(249,115,22,0.2);">
                    {{ $project->logs->count() }} {{ Str::plural('entry', $project->logs->count()) }}
                </span>
            @endif
        </div>
        <div style="padding:1.5rem; max-height:480px; overflow-y:auto;">
            @if($project->logs->count())
                <div style="display:flex; flex-direction:column;">
                    @foreach($project->logs as $idx => $log)
                    @php
                        $isLast = $idx === $project->logs->count() - 1;
                        $actionStyles = match($log->action) {
                            'created'        => ['bg'=>'#f0fdf4','color'=>'#16a34a','border'=>'#bbf7d0','icon'=>'fa-plus-circle'],
                            'updated'        => ['bg'=>'#eff6ff','color'=>'#2563eb','border'=>'#bfdbfe','icon'=>'fa-pen-to-square'],
                            'status_changed' => ['bg'=>'#fffbeb','color'=>'#d97706','border'=>'#fde68a','icon'=>'fa-arrow-right-arrow-left'],
                            'deleted'        => ['bg'=>'#fef2f2','color'=>'#dc2626','border'=>'#fecaca','icon'=>'fa-trash'],
                            default          => ['bg'=>'rgba(249,115,22,0.08)','color'=>'#ea580c','border'=>'rgba(249,115,22,0.22)','icon'=>'fa-circle-dot'],
                        };
                        $changes     = $log->changes ?? [];
                        $changeCount = count($changes);
                    @endphp
                    <div style="display:grid; grid-template-columns:2rem 1fr auto; gap:0 1rem; align-items:stretch;">
                        <div style="display:flex; flex-direction:column; align-items:center; padding-top:0.25rem;">
                            <div style="width:11px; height:11px; border-radius:50%; background:var(--orange-500); flex-shrink:0; box-shadow:0 0 0 3px rgba(249,115,22,0.18);"></div>
                            @if(!$isLast)
                                <div style="width:2px; flex:1; min-height:2rem; background:rgba(249,115,22,0.15); margin:3px 0;"></div>
                            @endif
                        </div>
                        <div style="padding:0 0 {{ $isLast ? '0' : '1.25rem' }} 0;">
                            <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.35rem;">
                                <span style="font-size:0.875rem; font-weight:700; color:var(--ink);">{{ $log->user?->name ?? 'System' }}</span>
                                <span style="color:#d1d5db; font-size:0.7rem;">•</span>
                                <span style="font-size:0.72rem; color:#9ca3af;"><i class="fas fa-clock" style="font-size:0.6rem;"></i> {{ $log->created_at->format('F d, Y \a\t H:i') }}</span>
                                <span style="color:#d1d5db; font-size:0.7rem;">•</span>
                                <span style="font-size:0.7rem; color:#9ca3af; font-style:italic;">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            @if($changeCount)
                                <button onclick="toggleLog('log-{{ $idx }}')"
                                    style="display:inline-flex; align-items:center; gap:0.4rem; margin-top:0.1rem; padding:3px 10px; border-radius:99px; border:1px solid rgba(249,115,22,0.22); background:rgba(249,115,22,0.06); color:var(--orange-600); font-size:0.72rem; font-weight:700; cursor:pointer; font-family:'Instrument Sans',sans-serif; transition:all 0.2s;"
                                    onmouseover="this.style.background='rgba(249,115,22,0.13)'"
                                    onmouseout="this.style.background='rgba(249,115,22,0.06)'">
                                    <i class="fas fa-list-ul" style="font-size:0.6rem;"></i>
                                    {{ $changeCount }} {{ Str::plural('change', $changeCount) }}
                                    <i id="log-{{ $idx }}-chevron" class="fas fa-chevron-down" style="font-size:0.55rem; transition:transform 0.25s;"></i>
                                </button>
                                <div id="log-{{ $idx }}" style="display:none; flex-direction:column; gap:0.35rem; margin-top:0.5rem;">
                                    @foreach($changes as $field => $change)
                                    @php
                                        $isNested = is_array($change) && (array_key_exists('old', $change) || array_key_exists('new', $change));
                                        $rawOld = $isNested ? ($change['old'] ?? null) : null;
                                        $rawNew = $isNested ? ($change['new'] ?? $change) : $change;
                                        $oldVal = is_array($rawOld) ? last($rawOld) : $rawOld;
                                        $newVal = is_array($rawNew) ? last($rawNew) : $rawNew;
                                        if ($field === 'work_done') continue;
                                        if ($field === 'issuances') {
                                            $checkVal = is_array($rawNew) ? last($rawNew) : $rawNew;
                                            if (empty($checkVal) || $checkVal === '[]' || $checkVal === [] || $checkVal === null) continue;
                                        }
                                        $formatVal = function($val) {
                                            if (is_array($val)) { return implode(', ', array_map(function($v) { try { return \Carbon\Carbon::parse($v)->format('F d, Y'); } catch(\Exception $e) { return $v; } }, $val)); }
                                            try { return \Carbon\Carbon::parse($val)->format('F d, Y'); } catch(\Exception $e) { return $val ?? '—'; }
                                        };
                                        $isDate     = $field === 'revised_contract_expiry';
                                        $isSlippage = $field === 'slippage';
                                        if ($isSlippage) {
                                            $displayNew = is_array($newVal) ? implode(', ', array_map(fn($v) => is_array($v) ? json_encode($v) : $v, $newVal)) : ($newVal ?? '—');
                                            $slippageNum = (float) $displayNew;
                                            $slippagePositive = $slippageNum >= 0;
                                        }
                                    @endphp
                                    <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; padding:0.45rem 0.75rem; border-radius:8px; background:#fffaf5; border:1px solid rgba(249,115,22,0.1);">
                                        <span style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--ink-muted); min-width:110px;">{{ str_replace('_', ' ', $field) }}</span>
                                        @if($isSlippage)
                                            <span style="display:inline-flex; align-items:center; gap:0.35rem; font-size:0.78rem; font-weight:700; padding:2px 10px; border-radius:99px; background:{{ $slippagePositive ? '#f0fdf4' : '#fef2f2' }}; color:{{ $slippagePositive ? '#16a34a' : '#dc2626' }}; border:1px solid {{ $slippagePositive ? '#bbf7d0' : '#fecaca' }};">
                                                <i class="fas {{ $slippagePositive ? 'fa-arrow-up' : 'fa-arrow-down' }}" style="font-size:0.6rem;"></i>
                                                {{ $slippagePositive ? '+' : '' }}{{ $displayNew }}% {{ $slippagePositive ? 'Ahead' : 'Behind' }}
                                            </span>
                                        @elseif($isDate)
                                            @if($oldVal !== null)
                                                <span style="font-size:0.78rem; color:#dc2626; font-weight:500; background:#fef2f2; border:1px solid #fecaca; padding:1px 8px; border-radius:99px; text-decoration:line-through;">{{ $formatVal($oldVal) }}</span>
                                                <i class="fas fa-arrow-right" style="color:#9ca3af; font-size:0.65rem;"></i>
                                            @endif
                                            <span style="font-size:0.78rem; color:#16a34a; font-weight:600; background:#f0fdf4; border:1px solid #bbf7d0; padding:1px 8px; border-radius:99px;">{{ $formatVal($newVal) }}</span>
                                        @else
                                            @if($oldVal !== null)
                                                <span style="font-size:0.78rem; color:#dc2626; font-weight:500; background:#fef2f2; border:1px solid #fecaca; padding:1px 8px; border-radius:99px; text-decoration:line-through;">{{ is_array($oldVal) ? implode(', ', array_map(fn($v) => is_array($v) ? json_encode($v) : $v, $oldVal)) : $oldVal }}</span>
                                                <i class="fas fa-arrow-right" style="color:#9ca3af; font-size:0.65rem;"></i>
                                            @endif
                                            @php
                                                $displayVal = is_array($newVal) ? implode(', ', array_map(fn($v) => is_array($v) ? json_encode($v) : $v, $newVal)) : ($newVal ?? '—');
                                                if ($field === 'extension_days') {
                                                    $cleaned = preg_replace('/[^0-9,]/', '', $displayVal);
                                                    $nums = array_filter(explode(',', $cleaned));
                                                    $displayVal = implode(', ', array_map(fn($n) => trim($n).' days', $nums));
                                                }
                                            @endphp
                                            <span style="font-size:0.78rem; color:#16a34a; font-weight:600; background:#f0fdf4; border:1px solid #bbf7d0; padding:1px 8px; border-radius:99px;">{{ $displayVal }}</span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div style="display:flex; align-items:flex-start; padding-top:0.05rem;">
                            <span style="display:inline-flex; align-items:center; gap:0.3rem; padding:3px 10px; border-radius:99px; font-size:0.7rem; font-weight:700; white-space:nowrap; background:{{ $actionStyles['bg'] }}; color:{{ $actionStyles['color'] }}; border:1px solid {{ $actionStyles['border'] }};">
                                <i class="fas {{ $actionStyles['icon'] }}" style="font-size:0.6rem;"></i>
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div style="display:flex; align-items:center; gap:0.6rem; color:#9ca3af; padding:0.25rem 0;">
                    <i class="fas fa-clock-rotate-left" style="font-size:1rem;"></i>
                    <p style="font-size:0.855rem; font-style:italic;">No activity logs recorded for this project.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Remarks ── --}}
    <div class="view-card">
        <div class="card-header">
            <i class="fas fa-comment-dots" style="color:var(--orange-500); font-size:0.85rem;"></i>
            <span>Remarks / Recommendation</span>
        </div>
        <div style="padding:1.25rem 1.5rem;">
            @if($project->remarks_recommendation)
                <p style="font-size:0.875rem; color:var(--ink); line-height:1.75; white-space:pre-line;">{{ $project->remarks_recommendation }}</p>
            @else
                <div style="display:flex; align-items:center; gap:0.6rem; color:#9ca3af;">
                    <i class="fas fa-comment-slash"></i>
                    <p style="font-size:0.855rem; font-style:italic;">No remarks or recommendations added yet.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ── Back Button ── --}}
    <div style="padding-bottom:1rem;">
        <a href="{{ route('user.projects.index') }}"
           style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.7rem 1.5rem; border:1.5px solid rgba(26,15,0,0.1); border-radius:10px; font-weight:600; font-size:0.875rem; color:var(--ink-muted); text-decoration:none; background:white; transition:all 0.2s;"
           onmouseover="this.style.borderColor='#f97316';this.style.color='#ea580c'"
           onmouseout="this.style.borderColor='rgba(26,15,0,0.1)';this.style.color='#6b4f35'">
            <i class="fas fa-arrow-left"></i> Back to Projects
        </a>
    </div>

</div>

<script>
function toggleLog(id) {
    const el      = document.getElementById(id);
    const chevron = document.getElementById(id + '-chevron');
    const isOpen  = el.style.display === 'flex';
    el.style.display        = isOpen ? 'none' : 'flex';
    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}
</script>
</x-app-layout>