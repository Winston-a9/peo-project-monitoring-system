<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="app-page-title">
                <span class="app-icon-badge"><i class="fas fa-folder-open"></i></span>
                {{ $project->project_title }}
            </h2>
            <p class="app-page-subtitle">
                <i class="fas fa-map-marker-alt" style="color:#f97316;font-size:0.7rem;margin-right:0.3rem;"></i>
                {{ $project->location }} &middot; <i class="fas fa-building" style="font-size:0.7rem;margin:0 0.3rem;"></i> {{ $project->contractor }}
            </p>
        </div>
        <div class="app-header-actions">
            <a href="{{ route('admin.projects.edit', $project) }}" class="app-btn-secondary"><i class="fas fa-edit"></i> Edit</a>
            <a href="{{ route('admin.projects.index') }}" class="app-btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
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

    $totalTEDays = (int) array_sum(array_map(fn($d) => is_numeric($d) ? (int)$d : 0, $extensionDays));
    $totalSODays = (int) ($project->suspension_days ?? 0);
    $hasSO       = $totalSODays > 0;

    $teEntries = collect($documentsPressed)->filter(fn($d) => str_starts_with((string)$d, 'Time Extension'))->values();
    $teCount   = $teEntries->count();

    $voDays      = $project->vo_days ?? [];
    $voDays      = is_array($voDays) ? array_values(array_filter($voDays, fn($d) => is_numeric($d))) : [];
    $totalVODays = (int) array_sum(array_map('intval', $voDays));
    $voCosts     = $project->vo_cost ?? [];
    $voCosts     = is_array($voCosts) ? $voCosts : [];
    $voEntries   = collect($documentsPressed)->filter(fn($d) => str_starts_with((string)$d, 'Variation Order'))->values();
    $voCount     = $voEntries->count();

    $dateRequested = $project->date_requested ?? [];
    $dateRequested = is_array($dateRequested) ? $dateRequested : (json_decode($dateRequested ?? '[]', true) ?? []);

    $totalDaysAdded = $totalTEDays + $totalVODays + $totalSODays;

    $slip      = (float)($project->slippage ?? 0);
    $slipColor = $slip < 0 ? '#ef4444' : ($slip > 0 ? '#22c55e' : '#9ca3af');
    $slipBg    = $slip < 0 ? 'rgba(239,68,68,0.1)' : ($slip > 0 ? 'rgba(34,197,94,0.1)' : 'rgba(156,163,175,0.1)');
    $slipIcon  = $slip < 0 ? 'fa-arrow-trend-down' : ($slip > 0 ? 'fa-arrow-trend-up' : 'fa-minus');
    $slipLabel = $slip < 0 ? 'Behind Schedule' : ($slip > 0 ? 'Ahead of Schedule' : 'On Schedule');

    $hasLD        = in_array('Liquidated Damages', $issuances);
    $hasIssuances = !empty($issuances);
    $hasRemarks   = !empty($project->remarks_recommendation);

    $revisedBreakdown = '';
    if ($totalTEDays > 0 || $voCount > 0 || $hasSO) {
        $revisedBreakdown = 'Original';
        if ($totalTEDays > 0) $revisedBreakdown .= ' +' . $totalTEDays . 'd TE';
        if ($voCount > 0)     $revisedBreakdown .= ' +' . $voCount . ' VO' . ($totalVODays > 0 ? ' (' . $totalVODays . 'd)' : '');
        if ($hasSO)           $revisedBreakdown .= ' +' . $totalSODays . 'd SO';
    }

    // LD calculations
    $ldAccomplished   = (float)($project->ld_accomplished ?? 0);
    $ldUnworked       = (float)($project->ld_unworked ?? 0);
    $ldPerDay         = (float)($project->ld_per_day ?? 0);
    $totalLd          = (float)($project->total_ld ?? 0);
    $contractAmount   = (float)($project->contract_amount ?? 0);

    $rawOverdue = $project->ld_days_overdue;
    $ldDaysOverdueDate = ($rawOverdue && $rawOverdue !== '0' && $rawOverdue !== '1970-01-01')
        ? \Carbon\Carbon::parse($rawOverdue)
        : null;
    $daysOverdueCount = $expiry ? (int)\Carbon\Carbon::parse($expiry)->diffInDays(now()) : 0;
    $calculatedLdTotal = ($ldPerDay > 0 && $ldDaysOverdueDate) ? round($ldPerDay * $daysOverdueCount, 2) : 0;
    $ldFormulaResult   = $contractAmount > 0 ? round(($ldAccomplished / 100) * $contractAmount * 0.001, 2) : 0;

    $ldSeverity      = 'none';
    $ldSeverityLabel = 'No LD Applied';
    $ldSeverityColor = '#9ca3af';
    $ldSeverityBg    = 'rgba(156,163,175,0.08)';
    $ldPctOfContract = 0;
    if ($totalLd > 0) {
        $ldPct = $contractAmount > 0 ? ($totalLd / $contractAmount) * 100 : 0;
        $ldPctOfContract = round($ldPct, 3);
        if ($ldPct >= 10)     { $ldSeverity='critical'; $ldSeverityLabel='Critical — Max LD Threshold'; $ldSeverityColor='#dc2626'; $ldSeverityBg='rgba(239,68,68,0.08)'; }
        elseif ($ldPct >= 5)  { $ldSeverity='high';     $ldSeverityLabel='High LD Exposure';            $ldSeverityColor='#ea580c'; $ldSeverityBg='rgba(249,115,22,0.08)'; }
        elseif ($ldPct >= 2)  { $ldSeverity='moderate'; $ldSeverityLabel='Moderate LD Exposure';        $ldSeverityColor='#d97706'; $ldSeverityBg='rgba(234,179,8,0.08)'; }
        else                   { $ldSeverity='low';      $ldSeverityLabel='Low LD Exposure';             $ldSeverityColor='#16a34a'; $ldSeverityBg='rgba(34,197,94,0.08)'; }
    }

    $contractDaysTotal = max((int)($project->contract_days ?? 1), 1);
    $elapsed = max(0, (int)$today->diffInDays($project->date_started));
    $pct = min(100, round(($elapsed / $contractDaysTotal) * 100));
    $barColor = $project->status === 'completed' ? '#22c55e' : ($daysLeft < 0 ? '#ef4444' : ($daysLeft <= 30 ? '#f59e0b' : '#f97316'));
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

    .app-page-title { font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; letter-spacing:-0.03em; color:var(--text-primary); display:flex; align-items:center; gap:0.6rem; margin:0; }
    .app-icon-badge { background:#f97316; width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(249,115,22,0.35); color:white; flex-shrink:0; }
    .app-page-subtitle { color:var(--text-secondary); font-size:0.82rem; margin:3px 0 0 0; }
    .app-header-actions { display:flex; gap:0.6rem; align-items:center; }
    .app-btn-secondary { display:inline-flex; align-items:center; gap:0.4rem; padding:0.6rem 1rem; border:1.5px solid var(--border); border-radius:9px; font-weight:600; font-size:0.825rem; color:var(--text-secondary); text-decoration:none; background:var(--bg-secondary); transition:all 0.2s; cursor:pointer; font-family:'Instrument Sans',sans-serif; }
    .app-btn-secondary:hover { border-color:var(--orange-500); background:rgba(249,115,22,0.08); color:var(--orange-600); }

    /* ── Pills ── */
    .pill { display:inline-flex; align-items:center; gap:0.3rem; padding:3px 10px; border-radius:99px; font-size:0.7rem; font-weight:700; }
    .pill-orange { background:rgba(249,115,22,0.1); color:#ea580c; border:1px solid rgba(249,115,22,0.2); }
    .pill-indigo { background:rgba(99,102,241,0.1); color:#6366f1; border:1px solid rgba(99,102,241,0.2); }
    .pill-amber  { background:rgba(234,179,8,0.1); color:#b45309; border:1px solid rgba(234,179,8,0.22); }
    .pill-green  { background:rgba(22,163,74,0.1); color:#16a34a; border:1px solid rgba(22,163,74,0.22); }
    .pill-red    { background:rgba(239,68,68,0.1); color:#dc2626; border:1px solid rgba(239,68,68,0.2); }
    .pill-blue   { background:rgba(37,99,235,0.1); color:#2563eb; border:1px solid rgba(37,99,235,0.2); }
    .pill-gray   { background:rgba(156,163,175,0.08); color:#9ca3af; border:1px solid rgba(156,163,175,0.15); }

    /* ── Hero stat bar ── */
    .stat-block { padding:1.1rem 1.25rem; }
    .stat-num { font-family:'Syne',sans-serif; font-weight:800; letter-spacing:-0.04em; line-height:1; }
    .section-eyebrow { font-size:0.63rem; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--ink-muted); opacity:0.65; margin:0 0 0.55rem; display:flex; align-items:center; gap:0.55rem; }
    .section-eyebrow::after { content:''; flex:1; height:1px; background:var(--border); }

    /* ── Accordion ── */
    .acc-card {
        background:var(--bg-primary);
        border:1px solid var(--border);
        border-radius:14px;
        overflow:hidden;
        box-shadow:var(--shadow-sm);
    }
    .acc-trigger {
        width:100%; display:flex; align-items:center; gap:0.75rem;
        padding:0.95rem 1.25rem;
        background:var(--bg-secondary);
        border:none; cursor:pointer;
        font-family:'Instrument Sans',sans-serif;
        transition:background 0.18s;
        text-align:left;
    }
    .acc-trigger:hover { background:rgba(249,115,22,0.06); }
    .acc-trigger-title { font-family:'Syne',sans-serif; font-weight:700; font-size:0.825rem; color:var(--ink); letter-spacing:-0.01em; flex:1; }
    .acc-chevron { color:var(--ink-muted); font-size:0.7rem; transition:transform 0.25s ease; flex-shrink:0; opacity:0.6; }
    .acc-body { border-top:1px solid var(--border); }

    /* ── LD sub-tabs (inside accordion body) ── */
    .ld-subtab-bar { display:flex; align-items:stretch; border-bottom:1px solid var(--border); background:var(--bg-secondary); }
    .ld-subtab-btn {
        flex:1; padding:0.75rem 0.5rem;
        background:transparent; border:none; border-bottom:2px solid transparent;
        cursor:pointer; font-size:0.775rem; font-weight:700; color:var(--text-secondary);
        display:flex; align-items:center; justify-content:center; gap:0.4rem;
        font-family:'Instrument Sans',sans-serif; transition:all 0.18s; white-space:nowrap;
    }
    .ld-subtab-btn + .ld-subtab-btn { border-left:1px solid var(--border); }
    .ld-subtab-btn:hover { color:var(--text-primary); background:rgba(249,115,22,0.04); }
    .ld-subtab-btn.active { color:#f97316; border-bottom-color:#f97316; background:rgba(249,115,22,0.04); }

    /* ── Data rows ── */
    .data-row { display:flex; align-items:center; justify-content:space-between; padding:0.7rem 1.25rem; border-bottom:1px solid rgba(249,115,22,0.06); gap:1rem; }
    .data-row:last-child { border-bottom:none; }
    .data-label { font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); white-space:nowrap; display:flex; align-items:center; gap:0.45rem; }
    .data-label i { color:var(--orange-500); font-size:0.7rem; width:12px; text-align:center; opacity:0.8; }
    .data-value { font-size:0.855rem; font-weight:700; color:var(--text-primary); text-align:right; }
    .data-sub   { font-size:0.7rem; color:var(--text-secondary); margin-top:1px; text-align:right; }

    /* ── LD formula ── */
    .formula-step { display:flex; flex-direction:column; gap:3px; padding:0.55rem 0.875rem; border-radius:9px; background:var(--bg-secondary); border:1px solid var(--border); font-family:'Instrument Sans',sans-serif; flex:1; min-width:80px; }
    .formula-op   { font-size:0.95rem; font-weight:800; color:#9ca3af; flex-shrink:0; }
    .formula-val  { font-size:0.82rem; font-weight:700; color:var(--text-primary); }
    .formula-lbl  { font-size:0.6rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); }
    .ld-bar-track { height:7px; background:rgba(239,68,68,0.1); border-radius:99px; overflow:hidden; }
    .ld-bar-fill  { height:100%; border-radius:99px; transition:width 0.7s ease; }

    @keyframes fadeUp { from{opacity:0;transform:translateY(10px);} to{opacity:1;transform:translateY(0);} }
    @keyframes pulse  { 0%,100%{opacity:1;} 50%{opacity:0.4;} }
    .fade-up  { animation:fadeUp 0.35s ease both; }
    .delay-1  { animation-delay:.05s; }
    .delay-2  { animation-delay:.10s; }
    .delay-3  { animation-delay:.15s; }
    .delay-4  { animation-delay:.20s; }
    .delay-5  { animation-delay:.25s; }

    input[type="number"]:focus, input[type="date"]:focus, textarea:focus {
        outline:none; border-color:rgba(249,115,22,0.5)!important;
        box-shadow:0 0 0 3px rgba(249,115,22,0.1);
    }
</style>

<div style="max-width:1100px;margin:0 auto;display:flex;flex-direction:column;gap:0.875rem;">

{{-- ══════════════════════════════════════════════════
     HERO BAR — Status / Progress / Amount
══════════════════════════════════════════════════ --}}
<div style="display:grid;grid-template-columns:200px 1fr 185px;gap:0.875rem;" class="fade-up">

    {{-- Status --}}
    <div class="acc-card" style="padding:1.25rem;display:flex;flex-direction:column;gap:0.7rem;justify-content:center;">
        <p class="section-eyebrow" style="margin:0;">Contract Status</p>
        @if($project->status === 'completed')
            <div style="display:flex;align-items:center;gap:0.5rem;"><div style="width:8px;height:8px;border-radius:50%;background:#22c55e;flex-shrink:0;"></div><span style="font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:#16a34a;">Completed</span></div>
            @if($project->completed_at)<p style="font-size:0.75rem;color:var(--text-secondary);">{{ $project->completed_at->format('M d, Y') }}</p>@endif
        @elseif($daysLeft < 0)
            <div style="display:flex;align-items:center;gap:0.5rem;"><div style="width:8px;height:8px;border-radius:50%;background:#ef4444;flex-shrink:0;"></div><span style="font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:#dc2626;">Expired</span></div>
            <p style="font-size:0.75rem;color:#ef4444;">{{ abs($daysLeft) }} days ago</p>
        @elseif($daysLeft <= 30)
            <div style="display:flex;align-items:center;gap:0.5rem;"><div style="width:8px;height:8px;border-radius:50%;background:#f59e0b;flex-shrink:0;animation:pulse 1.5s ease infinite;"></div><span style="font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:#d97706;">Expiring</span></div>
            <p style="font-size:0.75rem;color:#f59e0b;">{{ $daysLeft }} days left</p>
        @else
            <div style="display:flex;align-items:center;gap:0.5rem;"><div style="width:8px;height:8px;border-radius:50%;background:#22c55e;flex-shrink:0;"></div><span style="font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:#16a34a;">Active</span></div>
            <p style="font-size:0.75rem;color:var(--text-secondary);">{{ $daysLeft }} days remaining</p>
        @endif
        <div style="height:3px;background:rgba(249,115,22,0.1);border-radius:99px;overflow:hidden;"><div style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:99px;"></div></div>
    </div>

    {{-- Progress --}}
    <div class="acc-card" style="display:grid;grid-template-columns:1fr 1fr 1fr;overflow:hidden;">
        <div class="stat-block" style="border-right:1px solid var(--border);">
            <p class="section-eyebrow">As Planned</p>
            <p class="stat-num" style="font-size:2.4rem;color:#f97316;">{{ $project->as_planned }}<span style="font-size:1rem;color:var(--ink-muted);">%</span></p>
            <div style="height:3px;background:rgba(249,115,22,0.1);border-radius:99px;margin-top:0.75rem;overflow:hidden;"><div style="height:100%;width:{{ $project->as_planned }}%;background:#f97316;border-radius:99px;"></div></div>
        </div>
        <div class="stat-block" style="border-right:1px solid var(--border);">
            <p class="section-eyebrow">Work Done</p>
            <p class="stat-num" style="font-size:2.4rem;color:#3b82f6;">{{ $project->work_done }}<span style="font-size:1rem;color:var(--ink-muted);">%</span></p>
            <div style="height:3px;background:rgba(59,130,246,0.1);border-radius:99px;margin-top:0.75rem;overflow:hidden;"><div style="height:100%;width:{{ $project->work_done }}%;background:#3b82f6;border-radius:99px;"></div></div>
        </div>
        <div class="stat-block">
            <p class="section-eyebrow">Slippage</p>
            <p class="stat-num" style="font-size:2.4rem;color:{{ $slipColor }};">{{ $slip > 0 ? '+' : '' }}{{ $project->slippage }}<span style="font-size:1rem;">%</span></p>
            <div style="display:flex;align-items:center;gap:0.35rem;margin-top:0.75rem;">
                <div style="width:20px;height:20px;border-radius:6px;background:{{ $slipBg }};display:flex;align-items:center;justify-content:center;"><i class="fas {{ $slipIcon }}" style="font-size:0.65rem;color:{{ $slipColor }};"></i></div>
                <span style="font-size:0.68rem;font-weight:600;color:{{ $slipColor }};">{{ $slipLabel }}</span>
            </div>
        </div>
    </div>

    {{-- Contract Amount --}}
    <div class="acc-card" style="padding:1.25rem;display:flex;flex-direction:column;justify-content:center;gap:0.4rem;">
        <p class="section-eyebrow" style="margin:0;">Contract Amount</p>
        <p style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.2rem;color:var(--text-primary);letter-spacing:-0.03em;line-height:1.3;word-break:break-word;">₱{{ number_format($project->contract_amount,2) }}</p>
        <p style="font-size:0.75rem;color:var(--text-secondary);">{{ $project->contract_days ?? '—' }} contract days</p>
    </div>
</div>


{{-- ══════════════════════════════════════════════════
     ACCORDION SECTIONS
══════════════════════════════════════════════════ --}}

{{-- ① PROJECT INFORMATION --}}
<div class="acc-card fade-up delay-1">
    <button class="acc-trigger" onclick="toggleAcc('acc-info')">
        <div style="width:28px;height:28px;background:rgba(249,115,22,0.1);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-circle-info" style="color:#f97316;font-size:0.75rem;"></i></div>
        <span class="acc-trigger-title">Project Information</span>
        <div style="display:flex;align-items:center;gap:0.5rem;">
            @if($project->status === 'completed')<span class="pill pill-green" style="font-size:0.65rem;">Completed</span>
            @elseif($project->status === 'expired')<span class="pill pill-red" style="font-size:0.65rem;">Expired</span>
            @else<span class="pill pill-blue" style="font-size:0.65rem;">Ongoing</span>@endif
        </div>
        <i class="fas fa-chevron-down acc-chevron" id="acc-info-chevron"></i>
    </button>
    <div class="acc-body" id="acc-info">
        <div style="display:grid;grid-template-columns:1fr 1fr;">
            <div class="data-row" style="border-right:1px solid var(--border);"><span class="data-label"><i class="fas fa-user-tie"></i> In Charge</span><span class="data-value">{{ $project->in_charge }}</span></div>
            <div class="data-row"><span class="data-label"><i class="fas fa-map-marker-alt"></i> Location</span><span class="data-value" style="text-align:right;max-width:65%;">{{ $project->location }}</span></div>
            <div class="data-row" style="border-right:1px solid var(--border);"><span class="data-label"><i class="fas fa-building"></i> Contractor</span><span class="data-value" style="text-align:right;max-width:65%;">{{ $project->contractor }}</span></div>
            <div class="data-row">
                <span class="data-label"><i class="fas fa-circle-dot"></i> Status</span>
                <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;justify-content:flex-end;">
                    @if($project->status === 'completed')<span class="pill pill-green"><i class="fas fa-check-circle" style="font-size:0.6rem;"></i> Completed</span>@if($project->completed_at)<span style="font-size:0.72rem;color:var(--text-secondary);">{{ $project->completed_at->format('M d, Y') }}</span>@endif
                    @elseif($project->status === 'expired')<span class="pill pill-red"><i class="fas fa-times-circle" style="font-size:0.6rem;"></i> Expired</span>
                    @else<span class="pill pill-blue"><i class="fas fa-circle" style="font-size:0.5rem;"></i> Ongoing</span>@endif
                </div>
            </div>
            <div class="data-row" style="border-right:1px solid var(--border);">
                <span class="data-label"><i class="fas fa-clock-rotate-left"></i> Last Updated</span>
                <div><p class="data-value">{{ $project->updated_at->format('M d, Y') }}</p><p class="data-sub">{{ $project->updated_at->format('h:i A') }} &middot; {{ $project->updated_at->diffForHumans() }}</p></div>
            </div>
            <div class="data-row">
                <span class="data-label"><i class="fas fa-calendar-plus"></i> Created</span>
                <div><p class="data-value">{{ $project->created_at->format('M d, Y') }}</p><p class="data-sub">{{ $project->created_at->format('h:i A') }}</p></div>
            </div>
        </div>
    </div>
</div>

{{-- ② CONTRACT DATES --}}
<div class="acc-card fade-up delay-1">
    <button class="acc-trigger" onclick="toggleAcc('acc-dates')">
        <div style="width:28px;height:28px;background:rgba(249,115,22,0.1);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-calendar-days" style="color:#f97316;font-size:0.75rem;"></i></div>
        <span class="acc-trigger-title">Contract Dates</span>
        <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
            @if($totalDaysAdded > 0)<span class="pill pill-orange"><i class="fas fa-plus" style="font-size:0.55rem;"></i> +{{ $totalDaysAdded }}d extended</span>@endif
            @if($project->revised_contract_expiry)<span style="font-size:0.75rem;font-weight:700;color:#f97316;">Revised: {{ $project->revised_contract_expiry->format('M d, Y') }}</span>@endif
        </div>
        <i class="fas fa-chevron-down acc-chevron" id="acc-dates-chevron"></i>
    </button>
    <div class="acc-body" id="acc-dates">
        <div style="display:grid;grid-template-columns:1fr 1fr;">
            <div class="data-row" style="border-right:1px solid var(--border);">
                <span class="data-label"><i class="fas fa-play"></i> Date Started</span>
                <div><p class="data-value">{{ $project->date_started->format('F d, Y') }}</p><p class="data-sub">{{ $project->date_started->format('l') }}</p></div>
            </div>
            <div class="data-row">
                <span class="data-label"><i class="fas fa-flag-checkered"></i> Original Expiry</span>
                <div><p class="data-value">{{ $project->original_contract_expiry->format('F d, Y') }}</p><p class="data-sub">{{ $project->original_contract_expiry->format('l') }}</p></div>
            </div>
            <div class="data-row" style="border-right:1px solid var(--border);{{ $totalTEDays > 0 ? '' : 'opacity:0.45;' }}">
                <span class="data-label"><i class="fas fa-clock" style="{{ $totalTEDays > 0 ? '' : 'color:#9ca3af;' }}"></i> Time Extension</span>
                @if($totalTEDays > 0)<span class="pill pill-orange"><i class="fas fa-clock" style="font-size:0.55rem;"></i> +{{ $totalTEDays }}d &middot; {{ $teCount }} {{ $teCount===1?'entry':'entries' }}</span>@else<span class="pill pill-gray">None</span>@endif
            </div>
            <div class="data-row" style="{{ $voCount > 0 ? '' : 'opacity:0.45;' }}">
                <span class="data-label"><i class="fas fa-file-signature" style="{{ $voCount > 0 ? 'color:#6366f1;' : 'color:#9ca3af;' }}"></i> Variation Order</span>
                @if($voCount > 0)<span class="pill pill-indigo"><i class="fas fa-file-signature" style="font-size:0.55rem;"></i>{{ $totalVODays > 0 ? ' +'.$totalVODays.'d &middot;' : '' }} {{ $voCount }} {{ $voCount===1?'entry':'entries' }}</span>@else<span class="pill pill-gray">None</span>@endif
            </div>
            <div class="data-row" style="border-right:1px solid var(--border);{{ $hasSO ? '' : 'opacity:0.45;' }}">
                <span class="data-label"><i class="fas fa-pause-circle" style="{{ $hasSO ? 'color:#d97706;' : 'color:#9ca3af;' }}"></i> Suspension Order</span>
                @if($hasSO)<span class="pill pill-amber"><i class="fas fa-pause" style="font-size:0.55rem;"></i> +{{ $totalSODays }}d</span>@else<span class="pill pill-gray">None</span>@endif
            </div>
            <div class="data-row">
                <div>
                    <span class="data-label"><i class="fas fa-calendar-pen"></i> Revised Expiry</span>
                    @if($revisedBreakdown)<p style="font-size:0.68rem;color:#9ca3af;margin-top:3px;">{{ $revisedBreakdown }}</p>@endif
                </div>
                @if($project->revised_contract_expiry)
                    <div style="text-align:right;"><p style="font-size:0.9rem;font-weight:800;color:#f97316;font-family:'Syne',sans-serif;">{{ $project->revised_contract_expiry->format('F d, Y') }}</p><p class="data-sub">{{ $project->revised_contract_expiry->format('l') }}</p></div>
                @else<span class="pill pill-gray">Not set</span>@endif
            </div>
        </div>
    </div>
</div>

{{-- ③ TIME EXTENSIONS / VARIATION ORDERS (only when present) --}}
@if($teCount > 0 || $voCount > 0 || $hasSO)
<div class="acc-card fade-up delay-2">
    <button class="acc-trigger" onclick="toggleAcc('acc-te')">
        <div style="width:28px;height:28px;background:rgba(249,115,22,0.1);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-table" style="color:#f97316;font-size:0.75rem;"></i></div>
        <span class="acc-trigger-title">Extensions &amp; Orders</span>
        <div style="display:flex;align-items:center;gap:0.5rem;">
            @if($teCount > 0)<span class="pill pill-orange"><i class="fas fa-clock" style="font-size:0.55rem;"></i> {{ $teCount }} TE</span>@endif
            @if($voCount > 0)<span class="pill pill-indigo"><i class="fas fa-file-signature" style="font-size:0.55rem;"></i> {{ $voCount }} VO</span>@endif
            @if($hasSO)<span class="pill pill-amber"><i class="fas fa-pause" style="font-size:0.55rem;"></i> SO +{{ $totalSODays }}d</span>@endif
        </div>
        <i class="fas fa-chevron-down acc-chevron" id="acc-te-chevron"></i>
    </button>
    <div class="acc-body" id="acc-te">
        {{-- Scoreboard --}}
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;border-bottom:1px solid var(--border);">
            <div style="padding:1rem 1.25rem;border-right:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:0.65rem;">
                    <div style="width:34px;height:34px;background:rgba(249,115,22,0.1);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-clock" style="color:#f97316;font-size:0.85rem;"></i></div>
                    <div><p style="font-size:0.63rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);">Time Extensions</p><p style="font-size:0.7rem;color:#9ca3af;margin-top:2px;">{{ $totalTEDays > 0 ? $totalTEDays.' total days' : 'None applied' }}</p></div>
                </div>
                <span style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;color:#f97316;line-height:1;">{{ $teCount }}</span>
            </div>
            <div style="padding:1rem 1.25rem;border-right:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:0.65rem;">
                    <div style="width:34px;height:34px;background:rgba(99,102,241,0.1);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-file-signature" style="color:#6366f1;font-size:0.85rem;"></i></div>
                    <div><p style="font-size:0.63rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);">Variation Orders</p><p style="font-size:0.7rem;color:#9ca3af;margin-top:2px;">{{ $totalVODays > 0 ? $totalVODays.' total days' : 'None applied' }}</p></div>
                </div>
                <span style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;color:#6366f1;line-height:1;">{{ $voCount }}</span>
            </div>
            <div style="padding:1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:0.65rem;">
                    <div style="width:34px;height:34px;background:rgba(234,179,8,0.1);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-pause-circle" style="color:#d97706;font-size:0.85rem;"></i></div>
                    <div><p style="font-size:0.63rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);">Suspension Order</p><p style="font-size:0.7rem;color:#9ca3af;margin-top:2px;">{{ $hasSO ? 'Extends revised expiry' : 'None applied' }}</p></div>
                </div>
                <span style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;color:#d97706;line-height:1;">{{ $totalSODays > 0 ? $totalSODays.'d' : '0' }}</span>
            </div>
        </div>

        @if($teCount > 0 || $voCount > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-family:'Instrument Sans',sans-serif;font-size:0.82rem;">
                <thead>
                    <tr style="background:var(--bg-secondary);border-bottom:2px solid var(--border);">
                        <th style="padding:0.65rem 1rem;text-align:left;font-size:0.63rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);white-space:nowrap;border-right:1px solid var(--border);">Document</th>
                        <th style="padding:0.65rem 0.75rem;text-align:center;font-size:0.63rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);white-space:nowrap;border-right:1px solid var(--border);">Days</th>
                        <th style="padding:0.65rem 0.75rem;text-align:center;font-size:0.63rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);border-right:1px solid var(--border);">Reasons</th>
                        <th style="padding:0.65rem 0.75rem;text-align:center;font-size:0.63rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);white-space:nowrap;border-right:1px solid var(--border);">Date Requested</th>
                        <th style="padding:0.65rem 0.75rem;text-align:center;font-size:0.63rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);white-space:nowrap;border-right:1px solid var(--border);">Revised Expiry</th>
                        <th style="padding:0.65rem 1rem;text-align:right;font-size:0.63rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);white-space:nowrap;">Cost</th>
                    </tr>
                </thead>
                <tbody>
                @php
                    $baseDate = $project->original_contract_expiry; $runningDays = 0; $allRows = [];
                    $costInvolved = is_array($project->cost_involved ?? null) ? $project->cost_involved : [];
                    foreach ($teEntries as $idx => $label) {
                        $days=$extensionDays[$idx]??0; $runningDays+=(int)$days;
                        $allRows[]=['type'=>'te','label'=>$label,'days'=>(int)$days,'cost'=>$costInvolved[$idx]??null,'date'=>$dateRequested[$idx]??null,'revised'=>(clone $baseDate)->addDays($runningDays)];
                    }
                    foreach ($voEntries as $vIdx => $label) {
                        $days=$voDays[$vIdx]??0; $runningDays+=(int)$days;
                        $allRows[]=['type'=>'vo','label'=>$label,'days'=>(int)$days,'cost'=>$voCosts[$vIdx]??null,'date'=>$dateRequested[$teCount+$vIdx]??null,'revised'=>(clone $baseDate)->addDays($runningDays)];
                    }
                @endphp
                @foreach($allRows as $ri => $row)
                @php $ie=$ri%2===0; $il=$ri===count($allRows)-1; @endphp
                <tr style="background:{{ $ie?'var(--bg-primary)':'var(--bg-secondary)' }};border-bottom:{{ $il?'none':'1px solid var(--border)' }};transition:background 0.15s;"
                    onmouseover="this.style.background='rgba(249,115,22,0.04)'"
                    onmouseout="this.style.background='{{ $ie?'var(--bg-primary)':'var(--bg-secondary)' }}'">
                    <td style="padding:0.7rem 1rem;font-weight:700;color:{{ $row['type']==='te'?'#ea580c':'#6366f1' }};white-space:nowrap;border-right:1px solid var(--border);">
                        <div style="display:flex;align-items:center;gap:0.4rem;"><i class="fas {{ $row['type']==='te'?'fa-clock':'fa-file-signature' }}" style="font-size:0.68rem;opacity:0.6;"></i>{{ $row['label'] }}@if($row['type']==='vo')<span style="font-size:0.58rem;font-weight:700;background:rgba(99,102,241,0.1);color:#6366f1;border:1px solid rgba(99,102,241,0.2);border-radius:99px;padding:1px 5px;margin-left:3px;">VO</span>@endif</div>
                    </td>
                    <td style="padding:0.7rem 0.75rem;text-align:center;border-right:1px solid var(--border);"><span style="font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:800;color:{{ $row['type']==='te'?'#f97316':'#6366f1' }};">{{ $row['days'] }}</span></td>
                    <td style="padding:0.7rem 0.75rem;text-align:center;color:var(--text-secondary);font-size:0.78rem;border-right:1px solid var(--border);">@if($row['type']==='vo')<span style="font-style:italic;">{{ $row['label'] }}</span>@else<span style="color:#9ca3af;">—</span>@endif</td>
                    <td style="padding:0.7rem 0.75rem;text-align:center;font-size:0.78rem;border-right:1px solid var(--border);">@if($row['date'])<span style="font-weight:600;color:var(--text-primary);">{{ \Carbon\Carbon::parse($row['date'])->format('m/d/y') }}</span>@else<span style="color:#9ca3af;">—</span>@endif</td>
                    <td style="padding:0.7rem 0.75rem;text-align:center;border-right:1px solid var(--border);"><div style="display:flex;flex-direction:column;align-items:center;gap:1px;"><span style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#9ca3af;">Revised:</span><span style="font-weight:700;color:{{ $il?'#f97316':'var(--text-primary)' }};white-space:nowrap;font-size:0.82rem;">{{ $row['revised']->format('m/d/y') }}</span></div></td>
                    <td style="padding:0.7rem 1rem;text-align:right;">@if($row['type']==='vo'||!$row['cost'])<span style="color:#9ca3af;">—</span>@else<span style="font-weight:700;color:#16a34a;white-space:nowrap;">₱{{ number_format((float)$row['cost'],2) }}</span>@endif</td>
                </tr>
                @endforeach
                <tr style="background:var(--bg-secondary);border-top:2px solid var(--border);">
                    <td style="padding:0.6rem 1rem;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-muted);border-right:1px solid var(--border);">Total</td>
                    <td style="padding:0.6rem 0.75rem;text-align:center;border-right:1px solid var(--border);"><span style="font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:800;color:#f97316;">{{ $totalTEDays+$totalVODays }}</span><span style="font-size:0.62rem;color:#9ca3af;margin-left:2px;">days</span></td>
                    <td style="border-right:1px solid var(--border);"></td><td style="border-right:1px solid var(--border);"></td>
                    <td style="padding:0.6rem 0.75rem;text-align:center;border-right:1px solid var(--border);">@if($project->revised_contract_expiry)<div style="display:flex;flex-direction:column;align-items:center;gap:1px;"><span style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#9ca3af;">Final:</span><span style="font-weight:800;color:#f97316;white-space:nowrap;">{{ $project->revised_contract_expiry->format('m/d/y') }}</span></div>@endif</td>
                    <td style="padding:0.6rem 1rem;text-align:right;">@php $tc=collect($allRows)->sum(fn($r)=>(float)($r['cost']??0));@endphp @if($tc>0)<span style="font-weight:800;color:#16a34a;white-space:nowrap;">₱{{ number_format($tc,2) }}</span>@else<span style="color:#9ca3af;">—</span>@endif</td>
                </tr>
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endif

{{-- ④ ISSUANCES (only when present) --}}
@if($hasIssuances)
<div class="acc-card fade-up delay-2">
    <button class="acc-trigger" onclick="toggleAcc('acc-iss')">
        <div style="width:28px;height:28px;background:rgba(249,115,22,0.1);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-paper-plane" style="color:#f97316;font-size:0.75rem;"></i></div>
        <span class="acc-trigger-title">Issuances</span>
        <div style="display:flex;align-items:center;gap:0.4rem;flex-wrap:wrap;">
            <span class="pill pill-orange">{{ count($issuances) }}</span>
            @foreach(array_slice($issuances,0,3) as $iss)
            @php $ic=match(true){str_contains($iss,'1st Notice')=>['pill-amber','fa-bell'],str_contains($iss,'2nd Notice')=>['pill-red','fa-bell'],str_contains($iss,'3rd Notice')=>['pill-red','fa-triangle-exclamation'],str_contains($iss,'Liquidated')=>['pill-red','fa-calculator'],str_contains($iss,'Terminate')=>['pill-red','fa-ban'],str_contains($iss,'Expiry')=>['pill-amber','fa-hourglass-end'],default=>['pill-gray','fa-circle-dot']}; @endphp
            <span class="pill {{ $ic[0] }}" style="font-size:0.65rem;"><i class="fas {{ $ic[1] }}" style="font-size:0.55rem;"></i> {{ $iss }}</span>
            @endforeach
            @if(count($issuances) > 3)<span style="font-size:0.7rem;color:#9ca3af;">+{{ count($issuances)-3 }} more</span>@endif
        </div>
        <i class="fas fa-chevron-down acc-chevron" id="acc-iss-chevron"></i>
    </button>
    <div class="acc-body" id="acc-iss">
        <div style="padding:1rem 1.25rem;display:flex;flex-wrap:wrap;gap:0.5rem;">
            @foreach($issuances as $iss)
            @php $ic=match(true){str_contains($iss,'1st Notice')=>['pill-amber','fa-bell'],str_contains($iss,'2nd Notice')=>['pill-red','fa-bell'],str_contains($iss,'3rd Notice')=>['pill-red','fa-triangle-exclamation'],str_contains($iss,'Liquidated')=>['pill-red','fa-calculator'],str_contains($iss,'Terminate')=>['pill-red','fa-ban'],str_contains($iss,'Expiry')=>['pill-amber','fa-hourglass-end'],default=>['pill-gray','fa-circle-dot']}; @endphp
            <span class="pill {{ $ic[0] }}"><i class="fas {{ $ic[1] }}" style="font-size:0.6rem;"></i> {{ $iss }}</span>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- ⑤ LIQUIDATED DAMAGES --}}
@if($hasLD || $ldPerDay > 0 || $totalLd > 0)
<div class="acc-card fade-up delay-3">
    <button class="acc-trigger" onclick="toggleAcc('acc-ld')">
        <div style="width:28px;height:28px;background:rgba(239,68,68,0.1);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-calculator" style="color:#dc2626;font-size:0.75rem;"></i></div>
        <span class="acc-trigger-title">Liquidated Damages</span>
        <div style="display:flex;align-items:center;gap:0.5rem;">
            @if($totalLd > 0)
                <span class="pill pill-red"><i class="fas fa-peso-sign" style="font-size:0.55rem;"></i> ₱{{ number_format($totalLd,2) }}</span>
                <span style="font-size:0.7rem;font-weight:700;color:{{ $ldSeverityColor }};">{{ $ldSeverityLabel }}</span>
            @else
                <span class="pill pill-gray">No assessment yet</span>
            @endif
        </div>
        <i class="fas fa-chevron-up acc-chevron" id="acc-ld-chevron"></i>
    </button>
    <div class="acc-body" id="acc-ld" style="display:block;">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);border-bottom:1px solid var(--border);">
            <div style="padding:1rem 1.25rem;border-right:1px solid var(--border);text-align:center;">
                <p style="font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);margin-bottom:0.4rem;">Accomplished</p>
                <p style="font-family:'Syne',sans-serif;font-size:1.75rem;font-weight:800;color:var(--text-primary);line-height:1;">{{ $project->ld_accomplished ?? '—' }}<span style="font-size:0.9rem;color:var(--ink-muted);">%</span></p>
            </div>
            <div style="padding:1rem 1.25rem;border-right:1px solid var(--border);text-align:center;">
                <p style="font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);margin-bottom:0.4rem;">Unworked</p>
                <p style="font-family:'Syne',sans-serif;font-size:1.75rem;font-weight:800;color:#dc2626;line-height:1;">{{ $project->ld_unworked ?? '—' }}<span style="font-size:0.9rem;color:var(--ink-muted);">%</span></p>
            </div>
            <div style="padding:1rem 1.25rem;border-right:1px solid var(--border);text-align:center;">
                <p style="font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);margin-bottom:0.4rem;">Days Overdue From</p>
                <p style="font-size:0.875rem;font-weight:700;color:var(--text-primary);">{{ $expiry ? \Carbon\Carbon::parse($expiry)->format('M d, Y') : '—' }}</p>
                @if($daysOverdueCount > 0)<p style="font-size:0.7rem;color:#3b82f6;margin-top:3px;font-weight:600;">{{ $daysOverdueCount }} days ago</p>@endif
            </div>
            <div style="padding:1rem 1.25rem;text-align:center;">
                <p style="font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink-muted);margin-bottom:0.4rem;">LD / Day</p>
                <p style="font-family:'Syne',sans-serif;font-size:1.4rem;font-weight:800;color:#d97706;line-height:1;">₱{{ $project->ld_per_day ? number_format($project->ld_per_day,2) : '0.00' }}</p>
            </div>
        </div>
        <div style="padding:1rem 1.5rem;background:rgba(239,68,68,0.03);">
            <p style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink-muted);margin-bottom:0.35rem;">Total LD Assessed</p>
            <p style="font-family:'Syne',sans-serif;font-size:2rem;font-weight:800;color:#dc2626;line-height:1;">₱{{ $project->total_ld ? number_format($project->total_ld,2) : '0.00' }}</p>
        </div>
    </div>
</div>
@endif

{{-- ⑥ REMARKS --}}
@if($hasRemarks)
<div class="acc-card fade-up delay-3">
    <button class="acc-trigger" onclick="toggleAcc('acc-remarks')">
        <div style="width:28px;height:28px;background:rgba(249,115,22,0.1);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-comment-dots" style="color:#f97316;font-size:0.75rem;"></i></div>
        <span class="acc-trigger-title">Remarks / Recommendation</span>
        <i class="fas fa-chevron-down acc-chevron" id="acc-remarks-chevron"></i>
    </button>
    <div class="acc-body" id="acc-remarks">
        <div style="padding:1.1rem 1.25rem;">
            <p style="font-size:0.875rem;color:var(--text-primary);line-height:1.8;margin:0;white-space:pre-wrap;">{!! nl2br(e($project->remarks_recommendation)) !!}</p>
        </div>
    </div>
</div>
@endif

{{-- ⑦ ACTIVITY LOG --}}
@php $logs = $project->logs()->with('user')->latest()->get(); @endphp
<div class="acc-card fade-up delay-4">
    <button class="acc-trigger" onclick="toggleAcc('acc-log')">
        <div style="width:28px;height:28px;background:rgba(249,115,22,0.1);border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="fas fa-clock-rotate-left" style="color:#f97316;font-size:0.75rem;"></i></div>
        <span class="acc-trigger-title">Activity Log</span>
        @if($logs->count())<span class="pill pill-orange">{{ $logs->count() }} {{ Str::plural('entry',$logs->count()) }}</span>@endif
        <i class="fas fa-chevron-down acc-chevron" id="acc-log-chevron"></i>
    </button>
    <div class="acc-body" id="acc-log" style="display:none;">
        @if($logs->count())
        <div style="max-height:520px;overflow-y:auto;padding:1.1rem 1.25rem;">
            <div style="display:flex;flex-direction:column;">
                @foreach($logs as $idx => $log)
                @php
                    $isLast=$idx===$logs->count()-1;
                    $aMap=match($log->action){'created'=>['#22c55e','#f0fdf4','#bbf7d0','fa-plus'],'updated'=>['#3b82f6','#eff6ff','#bfdbfe','fa-pen'],'status_changed'=>['#f59e0b','#fffbeb','#fde68a','fa-arrows-rotate'],'deleted'=>['#ef4444','#fef2f2','#fecaca','fa-trash'],default=>['#f97316','rgba(249,115,22,0.08)','rgba(249,115,22,0.2)','fa-circle-dot']};
                    [$aColor,$aBg,$aBorder,$aIcon]=$aMap;
                    $changes=$log->changes??[];
                    $filteredChanges=[];
                    foreach($changes as $field=>$change){
                        if(in_array($field,['work_done','updated_at']))continue;
                        if(in_array($field,['issuances'])){$nv=$change['to']??$change['new']??$change;$fl=is_array($nv)?array_filter($nv):$nv;if(empty($fl))continue;}
                        $filteredChanges[$field]=$change;
                    }
                    $changeCount=count($filteredChanges);
                @endphp
                <div style="display:grid;grid-template-columns:18px 1fr;gap:0 0.875rem;">
                    <div style="display:flex;flex-direction:column;align-items:center;padding-top:4px;">
                        <div style="width:9px;height:9px;border-radius:50%;background:{{ $aColor }};flex-shrink:0;box-shadow:0 0 0 3px {{ $aBg }};"></div>
                        @if(!$isLast)<div style="width:1.5px;flex:1;min-height:2.5rem;background:var(--border);margin:3px 0;"></div>@endif
                    </div>
                    <div style="padding-bottom:{{ $isLast?'0':'1rem' }};">
                        <div style="display:flex;align-items:center;gap:0.45rem;flex-wrap:wrap;margin-bottom:0.3rem;">
                            <span style="font-size:0.84rem;font-weight:700;color:var(--ink);">{{ $log->user?->name ?? 'System' }}</span>
                            <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:2px 8px;border-radius:99px;font-size:0.67rem;font-weight:700;background:{{ $aBg }};color:{{ $aColor }};border:1px solid {{ $aBorder }};"><i class="fas {{ $aIcon }}" style="font-size:0.55rem;"></i>{{ str_replace('_',' ',$log->action) }}</span>
                            <span style="font-size:0.7rem;color:#9ca3af;margin-left:auto;">{{ $log->created_at->format('M d, Y · h:i A') }}</span>
                        </div>
                        @if($log->action === 'created')
                        <div style="display:flex;align-items:center;gap:0.5rem;padding:0.4rem 0.75rem;border-radius:8px;background:rgba(34,197,94,0.05);border:1px solid rgba(34,197,94,0.18);"><i class="fas fa-circle-check" style="color:#16a34a;font-size:0.72rem;"></i><span style="font-size:0.78rem;color:#15803d;font-weight:600;">Project record was created.</span></div>
                        @elseif($changeCount > 0)
                        <button onclick="toggleLog('log-{{ $idx }}')"
                            style="display:inline-flex;align-items:center;gap:0.35rem;padding:2px 9px;border-radius:6px;border:1px solid var(--border);background:var(--bg-secondary);color:var(--text-secondary);font-size:0.7rem;font-weight:600;cursor:pointer;font-family:'Instrument Sans',sans-serif;transition:all 0.15s;"
                            onmouseover="this.style.borderColor='rgba(249,115,22,0.3)';this.style.color='#ea580c'"
                            onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-secondary)'">
                            <i class="fas fa-list-ul" style="font-size:0.55rem;"></i>
                            {{ $changeCount }} {{ Str::plural('change',$changeCount) }}
                            <i id="log-{{ $idx }}-chevron" class="fas fa-chevron-down" style="font-size:0.5rem;transition:transform 0.2s;"></i>
                        </button>
                        <div id="log-{{ $idx }}" style="display:none;flex-direction:column;gap:0.3rem;margin-top:0.5rem;">
                            @foreach($filteredChanges as $field => $change)
                            @php
                                $rawFrom=$change['from']??$change['old']??null;
                                $rawTo=$change['to']??$change['new']??$change;
                                $displayFrom=is_array($rawFrom)?implode(', ',array_filter(array_map(fn($v)=>is_array($v)?json_encode($v):(string)$v,$rawFrom))):(string)($rawFrom??'');
                                $displayTo=is_array($rawTo)?implode(', ',array_filter(array_map(fn($v)=>is_array($v)?json_encode($v):(string)$v,$rawTo))):(string)($rawTo??'');
                                if($field==='extension_days'){$nums=array_filter(explode(',',preg_replace('/[^0-9,]/','', $displayTo)));$displayTo=implode(', ',array_map(fn($n)=>trim($n).'d',$nums));}
                                $isSlip=$field==='slippage';$slipNum=$isSlip?(float)$displayTo:0;
                                $label=ucwords(str_replace('_',' ',$field));
                            @endphp
                            <div style="display:flex;align-items:center;gap:0.45rem;flex-wrap:wrap;padding:0.4rem 0.75rem;border-radius:8px;background:var(--bg-secondary);border:1px solid var(--border);">
                                <span style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--ink-muted);min-width:80px;">{{ $label }}</span>
                                @if($displayFrom!=='')<span style="font-size:0.74rem;color:#9ca3af;text-decoration:line-through;background:rgba(156,163,175,0.1);padding:1px 7px;border-radius:5px;">{{ $displayFrom }}</span><i class="fas fa-arrow-right" style="color:#d1d5db;font-size:0.6rem;"></i>@endif
                                @if($isSlip)<span style="font-size:0.74rem;font-weight:700;color:{{ $slipNum>=0?'#16a34a':'#dc2626' }};background:{{ $slipNum>=0?'rgba(34,197,94,0.1)':'rgba(239,68,68,0.1)' }};padding:1px 8px;border-radius:5px;">{{ $slipNum>=0?'+':'' }}{{ $displayTo }}%</span>
                                @else<span style="font-size:0.74rem;font-weight:600;color:var(--text-primary);background:rgba(249,115,22,0.07);padding:1px 8px;border-radius:5px;border:1px solid rgba(249,115,22,0.14);">{{ $displayTo }}</span>@endif
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div style="padding:1.5rem 1.25rem;display:flex;align-items:center;gap:0.6rem;color:#9ca3af;"><i class="fas fa-inbox"></i><p style="font-size:0.845rem;font-style:italic;">No activity recorded yet.</p></div>
        @endif
    </div>
</div>

{{-- ⑧ DANGER ZONE --}}
<div style="background:var(--bg-primary);border:1px solid rgba(239,68,68,0.2);border-radius:14px;padding:1.1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;box-shadow:var(--shadow-sm);" class="fade-up delay-5">
    <div>
        <p style="font-family:'Syne',sans-serif;font-size:0.85rem;font-weight:800;color:#991b1b;display:flex;align-items:center;gap:0.45rem;margin-bottom:0.2rem;"><i class="fas fa-triangle-exclamation" style="font-size:0.8rem;"></i> Danger Zone</p>
        <p style="font-size:0.78rem;color:#b91c1c;">Permanently delete this project and all associated records. This cannot be undone.</p>
    </div>
    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this project permanently? This action cannot be reversed.')">
        @csrf @method('DELETE')
        <button type="submit"
            style="display:inline-flex;align-items:center;gap:0.45rem;padding:0.6rem 1.15rem;background:#dc2626;color:white;font-weight:700;font-size:0.825rem;border-radius:9px;border:none;cursor:pointer;box-shadow:0 2px 10px rgba(220,38,38,0.2);font-family:'Instrument Sans',sans-serif;transition:all 0.2s;"
            onmouseover="this.style.background='#b91c1c';this.style.transform='translateY(-1px)'"
            onmouseout="this.style.background='#dc2626';this.style.transform='translateY(0)'">
            <i class="fas fa-trash" style="font-size:0.8rem;"></i> Delete Project
        </button>
    </form>
</div>

</div>{{-- end max-width wrapper --}}

<script>
// ── Accordion toggle ──────────────────────────────────────────
function toggleAcc(id) {
    const body    = document.getElementById(id);
    const chevron = document.getElementById(id + '-chevron');
    if (!body) return;
    const open = body.style.display !== 'none' && body.style.display !== '';
    body.style.display    = open ? 'none' : 'block';
    chevron.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
}

// ── LD sub-tabs ───────────────────────────────────────────────
function switchLDTab(tab) {
    ['view','summary','update'].forEach(t => {
        const btn   = document.getElementById('ld-tab-' + t);
        const panel = document.getElementById('ld-panel-' + t);
        if (!btn || !panel) return;
        const active = t === tab;
        panel.style.display = active ? 'block' : 'none';
        btn.classList.toggle('active', active);
    });
}

// ── Log change rows ───────────────────────────────────────────
function toggleLog(id) {
    const el = document.getElementById(id);
    const ch = document.getElementById(id + '-chevron');
    const open = el.style.display === 'flex';
    el.style.display    = open ? 'none' : 'flex';
    ch.style.transform  = open ? 'rotate(0deg)' : 'rotate(180deg)';
}

// ── Init on load ──────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    // All accordions open by default except Activity Log
    ['acc-info','acc-dates','acc-te','acc-iss','acc-ld','acc-remarks'].forEach(id => {
        const body    = document.getElementById(id);
        const chevron = document.getElementById(id + '-chevron');
        if (body) {
            body.style.display = 'block';
            if (chevron) chevron.style.transform = 'rotate(180deg)';
        }
    });
    // LD summary/update hidden, view shown
    switchLDTab('view');
});
</script>
</x-app-layout>