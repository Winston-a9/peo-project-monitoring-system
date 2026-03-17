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
                {{ $project->location }} &middot;
                <i class="fas fa-building" style="font-size:0.7rem;margin:0 0.3rem;"></i>
                {{ $project->contractor }}
            </p>
        </div>
        <div class="app-header-actions">
            <a href="{{ route('admin.projects.edit', $project) }}" class="app-btn-secondary"><i class="fas fa-edit"></i> Update</a>
            <a href="{{ route('admin.projects.index') }}" class="app-btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
        </div>
    </div>
</x-slot>
@push('styles')
    @vite('resources/css/admin/projects/show.css')
@endpush

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
    $dateRequested = is_array($dateRequested) ? $dateRequested : [];

    $totalDaysAdded = $totalTEDays + $totalVODays + $totalSODays;

    $slip      = (float)($project->slippage ?? 0);
    $slipColor = $slip < 0 ? '#ef4444' : ($slip > 0 ? '#22c55e' : '#9ca3af');
    $slipBg    = $slip < 0 ? 'rgba(239,68,68,0.1)' : ($slip > 0 ? 'rgba(34,197,94,0.1)' : 'rgba(156,163,175,0.1)');
    $slipIcon  = $slip < 0 ? 'fa-arrow-trend-down' : ($slip > 0 ? 'fa-arrow-trend-up' : 'fa-minus');
    $slipLabel = $slip < 0 ? 'Behind Schedule' : ($slip > 0 ? 'Ahead of Schedule' : 'On Schedule');

    $hasLD        = !empty($project->ld_accomplished) || !empty($project->ld_unworked)
                 || !empty($project->ld_per_day)      || !empty($project->total_ld)
                 || !empty($project->ld_days_overdue);
    $hasIssuances = !empty($issuances);
    $hasRemarks   = !empty($project->remarks_recommendation);

    $revisedBreakdown = '';
    if ($totalTEDays > 0 || $voCount > 0 || $hasSO) {
        $revisedBreakdown = 'Original';
        if ($totalTEDays > 0) $revisedBreakdown .= ' +' . $totalTEDays . 'd TE';
        if ($voCount > 0)     $revisedBreakdown .= ' +' . $voCount . ' VO' . ($totalVODays > 0 ? ' (' . $totalVODays . 'd)' : '');
        if ($hasSO)           $revisedBreakdown .= ' +' . $totalSODays . 'd SO';
    }

    $contractDaysTotal = max((int)($project->contract_days ?? 1), 1);
    $elapsed  = max(0, (int)$today->diffInDays($project->date_started));
    $pct      = min(100, round(($elapsed / $contractDaysTotal) * 100));
    $barColor = $project->status === 'completed' ? '#22c55e' : ($daysLeft < 0 ? '#ef4444' : ($daysLeft <= 30 ? '#f59e0b' : '#f97316'));

    $heroCiAll = array_merge(
        is_array($project->cost_involved ?? null) ? $project->cost_involved : [],
        is_array($project->vo_cost ?? null)        ? $project->vo_cost : []
    );
    $heroCiAll = array_values(array_filter($heroCiAll, fn($c) => $c !== null && (float)$c != 0));
    $lastCost  = !empty($heroCiAll) ? (float) end($heroCiAll) : null;

    $billingAmounts = is_array($project->billing_amounts) ? array_map('floatval', $project->billing_amounts) : [];
    $billingDates   = is_array($project->billing_dates)   ? $project->billing_dates : [];
    $billingCount   = count($billingAmounts);
    $totalBilled    = array_sum($billingAmounts);
    $remainingBal   = (float)$project->contract_amount - $totalBilled;
    $hasBilling     = $billingCount > 0;

    $extCount = $teCount + $voCount + ($hasSO ? 1 : 0);
    $logs     = $project->logs()->with('user')->latest()->get();
@endphp

<div style="max-width:1100px;margin:0 auto;display:flex;flex-direction:column;gap:0.875rem;">

{{-- ══════════ HERO ROW · STATUS + PROGRESS + AMOUNT (always visible) ══════════ --}}
<div style="display:grid;grid-template-columns:200px 1fr 190px;gap:0.875rem;" class="fu">

    {{-- Status --}}
    <div class="card" style="padding:1.25rem;display:flex;flex-direction:column;gap:0.65rem;justify-content:center;">
        <p class="ey" style="margin:0;">Contract Status</p>
        @if($project->status === 'completed')
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <div style="width:8px;height:8px;border-radius:50%;background:#22c55e;flex-shrink:0;"></div>
                <span style="font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:#16a34a;">Completed</span>
            </div>
            @if($project->completed_at)<p style="font-size:0.75rem;color:var(--tx2);">{{ $project->completed_at->format('M d, Y') }}</p>@endif
        @elseif($project->status === 'expired' || $daysLeft < 0)
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <div style="width:8px;height:8px;border-radius:50%;background:#ef4444;flex-shrink:0;"></div>
                <span style="font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:#dc2626;">Expired</span>
            </div>
            <p style="font-size:0.75rem;color:#ef4444;">{{ $daysLeft < 0 ? abs($daysLeft).' days ago' : 'Marked as expired' }}</p>
        @elseif($daysLeft <= 30)
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <div style="width:8px;height:8px;border-radius:50%;background:#f59e0b;flex-shrink:0;animation:pulse 1.5s ease infinite;"></div>
                <span style="font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:#d97706;">Expiring</span>
            </div>
            <p style="font-size:0.75rem;color:#f59e0b;">{{ $daysLeft }} days left</p>
        @else
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <div style="width:8px;height:8px;border-radius:50%;background:#22c55e;flex-shrink:0;"></div>
                <span style="font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:#16a34a;">Active</span>
            </div>
            <p style="font-size:0.75rem;color:var(--tx2);">{{ $daysLeft }} days remaining</p>
        @endif
        <div style="height:3px;background:rgba(249,115,22,0.1);border-radius:99px;overflow:hidden;">
            <div style="height:100%;width:{{ $pct }}%;background:{{ $barColor }};border-radius:99px;"></div>
        </div>
    </div>

    {{-- Progress --}}
    <div class="card" style="display:grid;grid-template-columns:1fr 1fr 1fr;overflow:hidden;">
        <div class="sb" style="border-right:1px solid var(--bd);">
            <p class="ey">As Planned</p>
            <p class="sn" style="font-size:2.4rem;color:var(--or5);">{{ $project->as_planned }}<span style="font-size:1rem;color:var(--ink2);">%</span></p>
            <div style="height:3px;background:rgba(249,115,22,0.1);border-radius:99px;margin-top:0.75rem;overflow:hidden;">
                <div style="height:100%;width:{{ $project->as_planned }}%;background:#f97316;border-radius:99px;"></div>
            </div>
        </div>
        <div class="sb" style="border-right:1px solid var(--bd);">
            <p class="ey">Work Done</p>
            <p class="sn" style="font-size:2.4rem;color:#3b82f6;">{{ $project->work_done }}<span style="font-size:1rem;color:var(--ink2);">%</span></p>
            <div style="height:3px;background:rgba(59,130,246,0.1);border-radius:99px;margin-top:0.75rem;overflow:hidden;">
                <div style="height:100%;width:{{ $project->work_done }}%;background:#3b82f6;border-radius:99px;"></div>
            </div>
        </div>
        <div class="sb">
            <p class="ey">Slippage</p>
            <p class="sn" style="font-size:2.4rem;color:{{ $slipColor }};">{{ $slip > 0 ? '+' : '' }}{{ $project->slippage }}<span style="font-size:1rem;">%</span></p>
            <div style="display:flex;align-items:center;gap:0.35rem;margin-top:0.75rem;">
                <div style="width:20px;height:20px;border-radius:6px;background:{{ $slipBg }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas {{ $slipIcon }}" style="font-size:0.65rem;color:{{ $slipColor }};"></i>
                </div>
                <span style="font-size:0.7rem;font-weight:600;color:{{ $slipColor }};">{{ $slipLabel }}</span>
            </div>
        </div>
    </div>

    {{-- Contract Amount --}}
    <div class="card" style="padding:1.25rem;display:flex;flex-direction:column;justify-content:center;gap:0.4rem;">
        <p class="ey" style="margin:0;">Contract Amount</p>
        <p style="font-family:'Syne',sans-serif;font-weight:800;font-size:1.25rem;color:var(--tx);letter-spacing:-0.03em;line-height:1.25;word-break:break-word;">
            ₱{{ number_format($project->contract_amount, 2) }}
        </p>
        @if($lastCost !== null)
            <p style="font-size:0.72rem;font-weight:700;color:{{ $lastCost > 0 ? '#16a34a' : '#dc2626' }};">
                <i class="fas fa-arrow-{{ $lastCost > 0 ? 'up' : 'down' }}" style="font-size:0.55rem;"></i>
                Last entry: {{ $lastCost > 0 ? '+' : '' }}₱{{ number_format($lastCost, 2) }}
            </p>
        @endif
        <p style="font-size:0.72rem;color:var(--tx2);">{{ $project->contract_days ?? '—' }} contract days</p>
    </div>
</div>

{{-- ══════════ TAB NAV ══════════ --}}
<div class="show-tab-nav fu d1">
    <button class="show-tab-btn active" onclick="switchShowTab('overview', this)" data-tab="overview">
        <i class="fas fa-circle-info"></i>
        <span>Overview</span>
    </button>
    <button class="show-tab-btn" onclick="switchShowTab('extensions', this)" data-tab="extensions">
        <i class="fas fa-clock"></i>
        <span>Extensions</span>
        @if($extCount > 0)
            <span class="show-tab-badge">{{ $extCount }}</span>
        @endif
    </button>
    <button class="show-tab-btn" onclick="switchShowTab('financials', this)" data-tab="financials">
        <i class="fas fa-peso-sign"></i>
        <span>Financials</span>
        @if($hasBilling)
            <span class="show-tab-badge">{{ $billingCount }}</span>
        @endif
    </button>
    <button class="show-tab-btn" onclick="switchShowTab('admin', this)" data-tab="admin">
        <i class="fas fa-bell"></i>
        <span>Admin</span>
        @if($hasIssuances)
            <span class="show-tab-badge show-tab-badge-warn">{{ count($issuances) }}</span>
        @endif
    </button>
    <button class="show-tab-btn" onclick="switchShowTab('activity', this)" data-tab="activity">
        <i class="fas fa-clock-rotate-left"></i>
        <span>Activity</span>
        @if($logs->count())
            <span class="show-tab-badge show-tab-badge-muted">{{ $logs->count() }}</span>
        @endif
    </button>
</div>

{{-- ══════════ TAB: OVERVIEW ══════════ --}}
<div id="show-tab-overview" class="show-tab-panel">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.875rem;">

        {{-- Project Information --}}
        <div class="card">
            <div class="ch">
                <i class="fas fa-circle-info" style="color:var(--or5);font-size:0.8rem;"></i>
                <span class="ct">Project Information</span>
            </div>
            <div class="dr">
                <span class="dl"><i class="fas fa-user-tie"></i> In Charge</span>
                <span class="dv">{{ $project->in_charge }}</span>
            </div>
            <div class="dr">
                <span class="dl"><i class="fas fa-map-marker-alt"></i> Location</span>
                <span class="dv" style="text-align:right;max-width:60%;">{{ $project->location }}</span>
            </div>
            <div class="dr">
                <span class="dl"><i class="fas fa-building"></i> Contractor</span>
                <span class="dv" style="text-align:right;max-width:60%;">{{ $project->contractor }}</span>
            </div>
            <div class="dr">
                <span class="dl"><i class="fas fa-circle-dot"></i> Status</span>
                <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;justify-content:flex-end;">
                    @if($project->status === 'completed')
                        <span class="pill p-gr"><i class="fas fa-check-circle" style="font-size:0.6rem;"></i> Completed</span>
                        @if($project->completed_at)<span style="font-size:0.72rem;color:var(--tx2);">{{ $project->completed_at->format('M d, Y') }}</span>@endif
                    @elseif($project->status === 'expired' || $daysLeft < 0)
                        <span class="pill p-re"><i class="fas fa-times-circle" style="font-size:0.6rem;"></i> Expired</span>
                    @else
                        <span class="pill p-bl"><i class="fas fa-circle" style="font-size:0.5rem;"></i> Ongoing</span>
                    @endif
                </div>
            </div>
            <div class="dr">
                <span class="dl"><i class="fas fa-peso-sign"></i> Original Amount</span>
                <span class="dv">₱{{ number_format($project->original_contract_amount ?? $project->contract_amount, 2) }}</span>
            </div>
            <div class="dr">
                <span class="dl"><i class="fas fa-clock-rotate-left"></i> Last Updated</span>
                <div>
                    <p class="dv">{{ $project->updated_at->format('M d, Y') }}</p>
                    <p class="ds">{{ $project->updated_at->format('h:i A') }} &middot; {{ $project->updated_at->diffForHumans() }}</p>
                </div>
            </div>
            <div class="dr">
                <span class="dl"><i class="fas fa-calendar-plus"></i> Created</span>
                <div>
                    <p class="dv">{{ $project->created_at->format('M d, Y') }}</p>
                    <p class="ds">{{ $project->created_at->format('h:i A') }}</p>
                </div>
            </div>
        </div>

        {{-- Contract Dates --}}
        <div class="card">
            <div class="ch" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    <i class="fas fa-calendar-days" style="color:var(--or5);font-size:0.8rem;"></i>
                    <span class="ct">Contract Dates</span>
                </div>
                @if($totalDaysAdded > 0)
                    <span class="pill p-or"><i class="fas fa-plus" style="font-size:0.55rem;"></i> +{{ $totalDaysAdded }}d total</span>
                @endif
            </div>
            <div class="dr">
                <span class="dl"><i class="fas fa-play"></i> Date Started</span>
                <div>
                    <p class="dv">{{ $project->date_started->format('F d, Y') }}</p>
                    <p class="ds">{{ $project->date_started->format('l') }}</p>
                </div>
            </div>
            <div class="dr">
                <span class="dl"><i class="fas fa-flag-checkered"></i> Original Expiry</span>
                <div>
                    <p class="dv">{{ $project->original_contract_expiry->format('F d, Y') }}</p>
                    <p class="ds">{{ $project->original_contract_expiry->format('l') }}</p>
                </div>
            </div>
            <div class="dr" style="{{ $totalTEDays > 0 ? '' : 'opacity:0.48;' }}">
                <span class="dl"><i class="fas fa-clock" style="{{ $totalTEDays > 0 ? '' : 'color:#9ca3af;' }}"></i> Time Extension</span>
                @if($totalTEDays > 0)
                    <span class="pill p-or"><i class="fas fa-clock" style="font-size:0.55rem;"></i> +{{ $totalTEDays }}d &middot; {{ $teCount }} {{ $teCount === 1 ? 'entry' : 'entries' }}</span>
                @else
                    <span class="pill p-gy">None</span>
                @endif
            </div>
            <div class="dr" style="{{ $voCount > 0 ? '' : 'opacity:0.48;' }}">
                <span class="dl"><i class="fas fa-file-signature" style="{{ $voCount > 0 ? 'color:#6366f1;' : 'color:#9ca3af;' }}"></i> Variation Order</span>
                @if($voCount > 0)
                    <span class="pill p-vi"><i class="fas fa-file-signature" style="font-size:0.55rem;"></i>{{ $totalVODays > 0 ? ' +'.$totalVODays.'d &middot;' : '' }} {{ $voCount }} {{ $voCount === 1 ? 'entry' : 'entries' }}</span>
                @else
                    <span class="pill p-gy">None</span>
                @endif
            </div>
            <div class="dr" style="{{ $hasSO ? '' : 'opacity:0.48;' }}">
                <span class="dl"><i class="fas fa-pause-circle" style="{{ $hasSO ? 'color:#d97706;' : 'color:#9ca3af;' }}"></i> Suspension Order</span>
                @if($hasSO)
                    <span class="pill p-am"><i class="fas fa-pause" style="font-size:0.55rem;"></i> +{{ $totalSODays }}d</span>
                @else
                    <span class="pill p-gy">None</span>
                @endif
            </div>
            <div style="padding:0.875rem 1.25rem;background:{{ $project->revised_contract_expiry ? 'rgba(249,115,22,0.04)' : 'transparent' }};border-top:1px solid var(--bd);">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
                    <div>
                        <span class="dl"><i class="fas fa-calendar-pen" style="color:var(--or5);"></i> Revised Expiry</span>
                        @if($revisedBreakdown)<p style="font-size:0.68rem;color:#9ca3af;margin-top:3px;">{{ $revisedBreakdown }}</p>@endif
                    </div>
                    @if($project->revised_contract_expiry)
                        <div style="text-align:right;">
                            <p style="font-size:0.9rem;font-weight:800;color:var(--or5);font-family:'Syne',sans-serif;">{{ $project->revised_contract_expiry->format('F d, Y') }}</p>
                            <p class="ds">{{ $project->revised_contract_expiry->format('l') }}</p>
                        </div>
                    @else
                        <span class="pill p-gy">Not set</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════ TAB: EXTENSIONS ══════════ --}}
<div id="show-tab-extensions" class="show-tab-panel" style="display:none;">
    @if($teCount > 0 || $voCount > 0 || $hasSO)
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0.875rem;margin-bottom:0.875rem;">
        <div class="score-card" style="border:1.5px solid rgba(249,115,22,0.18);">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="width:38px;height:38px;background:rgba(249,115,22,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-clock" style="color:#f97316;font-size:0.9rem;"></i>
                </div>
                <div>
                    <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink2);">Time Extensions</p>
                    <p style="font-size:0.72rem;color:#9ca3af;margin-top:2px;">{{ $totalTEDays > 0 ? $totalTEDays.' total days' : 'None applied' }}</p>
                </div>
            </div>
            <span style="font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:800;color:#f97316;line-height:1;">{{ $teCount }}</span>
        </div>
        <div class="score-card" style="border:1.5px solid rgba(99,102,241,0.2);">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="width:38px;height:38px;background:rgba(99,102,241,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-file-signature" style="color:#6366f1;font-size:0.9rem;"></i>
                </div>
                <div>
                    <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink2);">Variation Orders</p>
                    <p style="font-size:0.72rem;color:#9ca3af;margin-top:2px;">{{ $totalVODays > 0 ? $totalVODays.' total days' : 'None applied' }}</p>
                </div>
            </div>
            <span style="font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:800;color:#6366f1;line-height:1;">{{ $voCount }}</span>
        </div>
        <div class="score-card" style="border:1.5px solid rgba(234,179,8,0.22);">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="width:38px;height:38px;background:rgba(234,179,8,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fas fa-pause-circle" style="color:#d97706;font-size:0.9rem;"></i>
                </div>
                <div>
                    <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.07em;color:var(--ink2);">Suspension Order</p>
                    <p style="font-size:0.72rem;color:#9ca3af;margin-top:2px;">{{ $hasSO ? 'Extends revised expiry' : 'None applied' }}</p>
                </div>
            </div>
            <span style="font-family:'Syne',sans-serif;font-size:2.2rem;font-weight:800;color:#d97706;line-height:1;">{{ $totalSODays > 0 ? $totalSODays.'d' : '0' }}</span>
        </div>
    </div>

    @if($teCount > 0 || $voCount > 0)
    <div class="card" style="overflow:hidden;">
        <div class="ch" style="justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <i class="fas fa-table" style="color:var(--or5);font-size:0.8rem;"></i>
                <span class="ct">Approved Time Extensions / Variation Orders</span>
            </div>
            <span style="font-size:0.7rem;color:#9ca3af;font-family:'Instrument Sans',sans-serif;">{{ $teCount + $voCount }} {{ ($teCount + $voCount) === 1 ? 'entry' : 'entries' }}</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="te-tbl">
                <thead>
                    <tr>
                        <th style="text-align:left;">Approved Time Extensions</th>
                        <th style="text-align:center;">No. of Days</th>
                        <th style="text-align:center;">Reasons / Coverage</th>
                        <th style="text-align:center;">Date Requested</th>
                        <th style="text-align:center;">Revised Expiry</th>
                        <th style="text-align:right;">Cost Involved</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $baseDate     = $project->original_contract_expiry;
                        $runningDays  = 0;
                        $allRows      = [];
                        $costInvolved = is_array($project->cost_involved ?? null) ? $project->cost_involved : [];
                        foreach ($teEntries as $idx => $label) {
                            $days = (int)($extensionDays[$idx] ?? 0);
                            $cost = $costInvolved[$idx] ?? null;
                            $date = $dateRequested[$idx] ?? null;
                            $runningDays += $days;
                            $allRows[] = ['type'=>'te','label'=>$label,'days'=>$days,'running'=>$runningDays,'cost'=>$cost,'date_requested'=>$date,'revised'=>(clone $baseDate)->addDays($runningDays)];
                        }
                        $voDateOffset = $teEntries->count();
                        foreach ($voEntries as $vIdx => $label) {
                            $days = (int)($voDays[$vIdx] ?? 0);
                            $cost = $voCosts[$vIdx] ?? null;
                            $date = $dateRequested[$voDateOffset + $vIdx] ?? null;
                            $runningDays += $days;
                            $allRows[] = ['type'=>'vo','label'=>$label,'days'=>$days,'running'=>$runningDays,'cost'=>$cost,'date_requested'=>$date,'revised'=>(clone $baseDate)->addDays($runningDays)];
                        }
                    @endphp
                    @foreach($allRows as $ri => $row)
                    @php $isEven = $ri % 2 === 0; $isLast = $ri === count($allRows) - 1; @endphp
                    <tr style="background:{{ $isEven ? 'var(--bg)' : 'var(--bg2)' }};"
                        onmouseover="this.style.background='rgba(249,115,22,0.04)'"
                        onmouseout="this.style.background='{{ $isEven ? 'var(--bg)' : 'var(--bg2)' }}'">
                        <td style="font-weight:700;color:{{ $row['type']==='te' ? '#ea580c' : '#6366f1' }};white-space:nowrap;">
                            <div style="display:flex;align-items:center;gap:0.45rem;">
                                <i class="fas {{ $row['type']==='te' ? 'fa-clock' : 'fa-file-signature' }}" style="font-size:0.7rem;opacity:0.6;"></i>
                                {{ $row['label'] }}
                                @if($row['type']==='vo')
                                    <span style="font-size:0.6rem;font-weight:700;background:rgba(99,102,241,0.1);color:#6366f1;border:1px solid rgba(99,102,241,0.2);border-radius:99px;padding:1px 6px;">VO</span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:800;color:{{ $row['type']==='te' ? '#f97316' : '#6366f1' }};">{{ $row['days'] }}</span>
                        </td>
                        <td style="text-align:center;color:var(--tx2);font-size:0.8rem;">
                            @if($row['type']==='vo')<span style="font-style:italic;">{{ $row['label'] }}</span>@else<span style="color:#9ca3af;">—</span>@endif
                        </td>
                        <td style="text-align:center;color:var(--tx2);font-size:0.8rem;white-space:nowrap;">
                            @if($row['date_requested']){{ \Carbon\Carbon::parse($row['date_requested'])->format('m/d/y') }}@else<span style="color:#9ca3af;">—</span>@endif
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex;flex-direction:column;align-items:center;gap:1px;">
                                <span style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#9ca3af;">Revised Expiry:</span>
                                <span style="font-weight:700;color:{{ $isLast ? '#f97316' : 'var(--tx)' }};white-space:nowrap;font-size:0.83rem;">{{ $row['revised']->format('m/d/y') }}</span>
                            </div>
                        </td>
                        <td style="text-align:right;">
                            @if($row['cost'])<span style="font-weight:700;color:#16a34a;white-space:nowrap;">₱{{ number_format((float)$row['cost'], 2) }}</span>@else<span style="color:#9ca3af;">—</span>@endif
                        </td>
                    </tr>
                    @endforeach
                    <tr style="background:var(--bg2);border-top:2px solid var(--bd);">
                        <td style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink2);">Total</td>
                        <td style="text-align:center;">
                            <span style="font-family:'Syne',sans-serif;font-size:1.1rem;font-weight:800;color:#f97316;">{{ $totalTEDays + $totalVODays }}</span>
                            <span style="font-size:0.65rem;color:#9ca3af;margin-left:2px;">days</span>
                        </td>
                        <td></td><td></td>
                        <td style="text-align:center;">
                            @if($project->revised_contract_expiry)
                                <div style="display:flex;flex-direction:column;align-items:center;gap:1px;">
                                    <span style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#9ca3af;">Final Revised:</span>
                                    <span style="font-weight:800;color:#f97316;white-space:nowrap;">{{ $project->revised_contract_expiry->format('m/d/y') }}</span>
                                </div>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            @php $totalCost = collect($allRows)->sum(fn($r) => (float)($r['cost'] ?? 0)); @endphp
                            @if($totalCost > 0)<span style="font-weight:800;color:#16a34a;white-space:nowrap;">₱{{ number_format($totalCost, 2) }}</span>@else<span style="color:#9ca3af;">—</span>@endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @else
    <div class="card" style="padding:3rem 1.5rem;text-align:center;">
        <div style="width:52px;height:52px;background:rgba(249,115,22,0.07);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <i class="fas fa-clock" style="font-size:1.3rem;color:rgba(249,115,22,0.3);"></i>
        </div>
        <p style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;color:var(--ink2);margin-bottom:0.25rem;">No Extensions Recorded</p>
        <p style="font-size:0.82rem;color:#9ca3af;">No time extensions, variation orders, or suspension orders have been applied.</p>
    </div>
    @endif
</div>

{{-- ══════════ TAB: FINANCIALS ══════════ --}}
<div id="show-tab-financials" class="show-tab-panel" style="display:none;">
    @if($hasLD)
    <div class="card" style="overflow:hidden;margin-bottom:0.875rem;">
        <div class="ch" style="justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <i class="fas fa-calculator" style="color:#dc2626;font-size:0.8rem;"></i>
                <span class="ct">Liquidated Damages</span>
            </div>
            @if($project->total_ld)
                <span class="pill p-re">Total: ₱{{ number_format($project->total_ld, 2) }}</span>
            @endif
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;">
            <div class="dr" style="border-right:1px solid var(--bd);">
                <span class="dl"><i class="fas fa-percent"></i> Accomplished</span>
                <span class="dv">{{ $project->ld_accomplished !== null ? $project->ld_accomplished.'%' : '—' }}</span>
            </div>
            <div class="dr">
                <span class="dl"><i class="fas fa-percent"></i> Unworked</span>
                <span class="dv">{{ $project->ld_unworked !== null ? $project->ld_unworked.'%' : '—' }}</span>
            </div>
            <div class="dr" style="border-right:1px solid var(--bd);">
                <span class="dl"><i class="fas fa-calendar-xmark"></i> Days Overdue</span>
                <span class="dv" style="{{ $project->ld_days_overdue ? 'color:#dc2626;' : '' }}">
                    {{ $project->ld_days_overdue ? (int)$project->ld_days_overdue . ' days' : '—' }}
                </span>
            </div>
            <div class="dr">
                <span class="dl"><i class="fas fa-peso-sign"></i> LD / Day</span>
                <span class="dv">{{ $project->ld_per_day ? '₱'.number_format($project->ld_per_day, 2) : '—' }}</span>
            </div>
        </div>
        <div style="padding:0.875rem 1.25rem;background:rgba(239,68,68,0.04);border-top:1px solid var(--bd);display:flex;align-items:center;justify-content:space-between;">
            <span class="dl"><i class="fas fa-peso-sign" style="color:#dc2626;"></i> Total LD</span>
            <span style="font-family:'Syne',sans-serif;font-size:1.2rem;font-weight:800;color:#dc2626;">
                ₱{{ $project->total_ld ? number_format($project->total_ld, 2) : '0.00' }}
            </span>
        </div>
    </div>
    @endif

    @if($hasBilling)
    <div class="card" style="overflow:hidden;">
        <div style="display:flex;align-items:center;border-bottom:1px solid var(--bd);background:var(--bg2);">
            <button onclick="toggleBillingTab('summary')" id="billing-tab-summary"
                style="flex:1;padding:0.875rem 1.25rem;background:transparent;border:none;cursor:pointer;font-size:0.825rem;font-weight:700;color:var(--tx);border-bottom:2px solid #16a34a;transition:all 0.2s;display:flex;align-items:center;gap:0.5rem;font-family:'Instrument Sans',sans-serif;">
                <i class="fas fa-chart-bar" style="font-size:0.75rem;color:#16a34a;"></i> Summary
            </button>
            <button onclick="toggleBillingTab('table')" id="billing-tab-table"
                style="flex:1;padding:0.875rem 1.25rem;background:transparent;border:none;cursor:pointer;font-size:0.825rem;font-weight:700;color:var(--tx2);border-bottom:2px solid transparent;transition:all 0.2s;display:flex;align-items:center;gap:0.5rem;font-family:'Instrument Sans',sans-serif;">
                <i class="fas fa-table" style="font-size:0.75rem;"></i> Billing History
            </button>
            <div style="padding:0.875rem 1.25rem;display:flex;align-items:center;gap:0.5rem;border-left:1px solid var(--bd);flex-shrink:0;">
                <i class="fas fa-file-invoice-dollar" style="color:#16a34a;font-size:0.8rem;"></i>
                <span class="pill" style="background:rgba(34,197,94,0.1);color:#16a34a;border:1px solid rgba(34,197,94,0.22);">{{ $billingCount }} {{ $billingCount === 1 ? 'billing' : 'billings' }}</span>
            </div>
        </div>
        <div id="billing-tab-summary-content" style="display:block;">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;border-bottom:1px solid var(--bd);">
                <div style="padding:1.25rem;border-right:1px solid var(--bd);">
                    <p class="ey" style="margin-bottom:0.5rem;">Contract Amount</p>
                    <p style="font-family:'Syne',sans-serif;font-size:1.35rem;font-weight:800;color:var(--tx);line-height:1;letter-spacing:-0.02em;">₱{{ number_format($project->contract_amount, 2) }}</p>
                    <p style="font-size:0.7rem;color:#9ca3af;margin-top:0.4rem;">Original contract value</p>
                </div>
                <div style="padding:1.25rem;border-right:1px solid var(--bd);">
                    <p class="ey" style="margin-bottom:0.5rem;">Total Amount Billed</p>
                    <p style="font-family:'Syne',sans-serif;font-size:1.35rem;font-weight:800;color:#16a34a;line-height:1;letter-spacing:-0.02em;">₱{{ number_format($totalBilled, 2) }}</p>
                    @php $billedPct = $project->contract_amount > 0 ? round(($totalBilled / $project->contract_amount) * 100, 1) : 0; @endphp
                    <div style="height:4px;background:rgba(34,197,94,0.1);border-radius:99px;margin-top:0.6rem;overflow:hidden;">
                        <div style="height:100%;width:{{ min($billedPct, 100) }}%;background:#16a34a;border-radius:99px;"></div>
                    </div>
                    <p style="font-size:0.7rem;color:#16a34a;margin-top:0.3rem;font-weight:600;">{{ $billedPct }}% of contract</p>
                </div>
                <div style="padding:1.25rem;">
                    <p class="ey" style="margin-bottom:0.5rem;">Remaining Balance</p>
                    <p style="font-family:'Syne',sans-serif;font-size:1.35rem;font-weight:800;color:{{ $remainingBal >= 0 ? '#3b82f6' : '#dc2626' }};line-height:1;letter-spacing:-0.02em;">
                        ₱{{ number_format(abs($remainingBal), 2) }}
                        @if($remainingBal < 0)<span style="font-size:0.75rem;font-weight:700;color:#dc2626;"> (over)</span>@endif
                    </p>
                    @php $remainPct = $project->contract_amount > 0 ? round((abs($remainingBal) / $project->contract_amount) * 100, 1) : 0; @endphp
                    <div style="height:4px;background:{{ $remainingBal >= 0 ? 'rgba(59,130,246,0.1)' : 'rgba(239,68,68,0.1)' }};border-radius:99px;margin-top:0.6rem;overflow:hidden;">
                        <div style="height:100%;width:{{ min($remainPct, 100) }}%;background:{{ $remainingBal >= 0 ? '#3b82f6' : '#dc2626' }};border-radius:99px;"></div>
                    </div>
                    <p style="font-size:0.7rem;color:{{ $remainingBal >= 0 ? '#3b82f6' : '#dc2626' }};margin-top:0.3rem;font-weight:600;">{{ $remainPct }}% {{ $remainingBal >= 0 ? 'remaining' : 'exceeded' }}</p>
                </div>
            </div>
            <div style="padding:1rem 1.25rem;background:rgba(34,197,94,0.02);display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;font-size:0.82rem;color:var(--tx2);">
                    <span style="font-weight:700;color:var(--tx);">₱{{ number_format($project->contract_amount, 2) }}</span>
                    <span style="color:#9ca3af;">−</span>
                    <span style="font-weight:700;color:#16a34a;">₱{{ number_format($totalBilled, 2) }}</span>
                    <span style="color:#9ca3af;">=</span>
                    <span style="font-family:'Syne',sans-serif;font-weight:800;font-size:0.95rem;color:{{ $remainingBal >= 0 ? '#3b82f6' : '#dc2626' }};">₱{{ number_format($remainingBal, 2) }}</span>
                </div>
                <span style="font-size:0.68rem;color:#9ca3af;margin-left:auto;">Contract Amount − Total Billed = Remaining Balance</span>
            </div>
        </div>
        <div id="billing-tab-table-content" style="display:none;">
            <div style="overflow-x:auto;">
                <table class="te-tbl">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Billing No.</th>
                            <th style="text-align:center;">Date</th>
                            <th style="text-align:right;">Amount Billed</th>
                            <th style="text-align:right;">Cumulative Total</th>
                            <th style="text-align:right;">Remaining Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $runningTotal = 0; @endphp
                        @foreach($billingAmounts as $bi => $amount)
                        @php
                            $runningTotal += $amount;
                            $runningRemain = (float)$project->contract_amount - $runningTotal;
                            $isEven = $bi % 2 === 0;
                            $isLast = $bi === $billingCount - 1;
                        @endphp
                        <tr style="background:{{ $isEven ? 'var(--bg)' : 'var(--bg2)' }};"
                            onmouseover="this.style.background='rgba(34,197,94,0.03)'"
                            onmouseout="this.style.background='{{ $isEven ? 'var(--bg)' : 'var(--bg2)' }}'">
                            <td>
                                <div style="display:flex;align-items:center;gap:0.5rem;">
                                    <div style="width:26px;height:26px;border-radius:7px;background:rgba(34,197,94,0.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <span style="font-family:'Syne',sans-serif;font-weight:800;font-size:0.68rem;color:#16a34a;">{{ $bi + 1 }}</span>
                                    </div>
                                    <span style="font-weight:700;color:var(--tx);">Billing No. {{ $bi + 1 }}</span>
                                    @if($isLast)<span style="font-size:0.6rem;font-weight:700;background:rgba(34,197,94,0.1);color:#16a34a;border:1px solid rgba(34,197,94,0.22);border-radius:99px;padding:1px 7px;margin-left:4px;">Latest</span>@endif
                                </div>
                            </td>
                            <td style="text-align:center;color:var(--tx2);font-size:0.8rem;">
                                @if(!empty($billingDates[$bi]))
                                    <span style="font-weight:600;color:var(--tx);">{{ \Carbon\Carbon::parse($billingDates[$bi])->format('M d, Y') }}</span>
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                            <td style="text-align:right;"><span style="font-weight:700;color:#16a34a;font-family:'Syne',sans-serif;font-size:0.95rem;">+₱{{ number_format($amount, 2) }}</span></td>
                            <td style="text-align:right;"><span style="font-weight:700;color:{{ $isLast ? '#16a34a' : 'var(--tx)' }};">₱{{ number_format($runningTotal, 2) }}</span></td>
                            <td style="text-align:right;"><span style="font-weight:700;color:{{ $runningRemain >= 0 ? '#3b82f6' : '#dc2626' }};">₱{{ number_format($runningRemain, 2) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="2" style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink2);">Total</td>
                            <td style="text-align:right;"><span style="font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:800;color:#16a34a;">₱{{ number_format($totalBilled, 2) }}</span></td>
                            <td style="text-align:right;"><span style="font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:800;color:#16a34a;">₱{{ number_format($totalBilled, 2) }}</span></td>
                            <td style="text-align:right;"><span style="font-family:'Syne',sans-serif;font-size:1.05rem;font-weight:800;color:{{ $remainingBal >= 0 ? '#3b82f6' : '#dc2626' }};">₱{{ number_format($remainingBal, 2) }}</span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(!$hasLD && !$hasBilling)
    <div class="card" style="padding:3rem 1.5rem;text-align:center;">
        <div style="width:52px;height:52px;background:rgba(34,197,94,0.07);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <i class="fas fa-peso-sign" style="font-size:1.3rem;color:rgba(34,197,94,0.4);"></i>
        </div>
        <p style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;color:var(--ink2);margin-bottom:0.25rem;">No Financial Records</p>
        <p style="font-size:0.82rem;color:#9ca3af;">No billing or liquidated damages data recorded yet.</p>
    </div>
    @endif
</div>

{{-- ══════════ TAB: ADMIN ══════════ --}}
<div id="show-tab-admin" class="show-tab-panel" style="display:none;">
    @if($hasIssuances || $hasRemarks)
    <div style="display:grid;grid-template-columns:{{ ($hasIssuances && $hasRemarks) ? '1fr 1fr' : '1fr' }};gap:0.875rem;">
        @if($hasIssuances)
        <div class="card">
            <div class="ch" style="justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    <i class="fas fa-paper-plane" style="color:var(--or5);font-size:0.8rem;"></i>
                    <span class="ct">Issuances</span>
                </div>
                <span class="pill p-or">{{ count($issuances) }}</span>
            </div>
            <div style="padding:1rem 1.25rem;display:flex;flex-wrap:wrap;gap:0.5rem;">
                @foreach($issuances as $iss)
                @php
                    $issStyle = match(true) {
                        str_contains($iss, '1st Notice') => ['p-am', 'fa-bell'],
                        str_contains($iss, '2nd Notice') => ['p-re', 'fa-bell'],
                        str_contains($iss, '3rd Notice') => ['p-re', 'fa-triangle-exclamation'],
                        str_contains($iss, 'Liquidated') => ['p-re', 'fa-calculator'],
                        str_contains($iss, 'Terminate')  => ['p-re', 'fa-ban'],
                        str_contains($iss, 'Expiry')     => ['p-am', 'fa-hourglass-end'],
                        default                          => ['p-gy', 'fa-circle-dot'],
                    };
                @endphp
                <span class="pill {{ $issStyle[0] }}"><i class="fas {{ $issStyle[1] }}" style="font-size:0.6rem;"></i> {{ $iss }}</span>
                @endforeach
            </div>
        </div>
        @endif
        @if($hasRemarks)
        <div class="card">
            <div class="ch">
                <i class="fas fa-comment-dots" style="color:var(--or5);font-size:0.8rem;"></i>
                <span class="ct">Remarks / Recommendation</span>
            </div>
            <div style="padding:1.1rem 1.25rem;">
                <p style="font-size:0.875rem;color:var(--tx);line-height:1.8;white-space:pre-line;">{{ $project->remarks_recommendation }}</p>
            </div>
        </div>
        @endif
    </div>
    @else
    <div class="card" style="padding:3rem 1.5rem;text-align:center;">
        <div style="width:52px;height:52px;background:rgba(249,115,22,0.07);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
            <i class="fas fa-bell" style="font-size:1.3rem;color:rgba(249,115,22,0.3);"></i>
        </div>
        <p style="font-family:'Syne',sans-serif;font-weight:800;font-size:1rem;color:var(--ink2);margin-bottom:0.25rem;">No Admin Records</p>
        <p style="font-size:0.82rem;color:#9ca3af;">No issuances or remarks have been recorded.</p>
    </div>
    @endif
</div>

{{-- ══════════ TAB: ACTIVITY ══════════ --}}
<div id="show-tab-activity" class="show-tab-panel" style="display:none;">
    <div class="card" style="overflow:hidden;">
        <div class="ch" style="justify-content:space-between;">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <i class="fas fa-clock-rotate-left" style="color:var(--or5);font-size:0.8rem;"></i>
                <span class="ct">Activity Log</span>
            </div>
            @if($logs->count())
                <span class="pill p-or">{{ $logs->count() }} {{ Str::plural('entry', $logs->count()) }}</span>
            @endif
        </div>
        @if($logs->count())
        <div style="max-height:600px;overflow-y:auto;padding:1.1rem 1.25rem;">
            <div style="display:flex;flex-direction:column;">
                @foreach($logs as $idx => $log)
                @php
                    $isLast = $idx === $logs->count() - 1;
                    $aMap = match($log->action) {
                        'created'        => ['#22c55e', '#f0fdf4', '#bbf7d0', 'fa-plus'],
                        'updated'        => ['#3b82f6', '#eff6ff', '#bfdbfe', 'fa-pen'],
                        'status_changed' => ['#f59e0b', '#fffbeb', '#fde68a', 'fa-arrows-rotate'],
                        'deleted'        => ['#ef4444', '#fef2f2', '#fecaca', 'fa-trash'],
                        default          => ['#f97316', 'rgba(249,115,22,0.08)', 'rgba(249,115,22,0.2)', 'fa-circle-dot'],
                    };
                    [$aColor, $aBg, $aBorder, $aIcon] = $aMap;
                    $changes = $log->changes ?? [];
                    $filteredChanges = [];
                    $skipFields = ['updated_at','slippage','revised_contract_expiry','contract_days','time_extension','variation_order','total_amount_billed','remaining_balance','ld_unworked','ld_per_day','total_ld'];
                    $emptyCheckFields = ['issuances','documents_pressed','billing_amounts','billing_dates','extension_days','vo_days','vo_cost','cost_involved','date_requested'];
                    foreach ($changes as $field => $change) {
                        if (in_array($field, $skipFields)) continue;
                        if (in_array($field, $emptyCheckFields)) {
                            $newVal = $change['to'] ?? $change['new'] ?? $change;
                            $flat   = is_array($newVal) ? array_filter($newVal) : $newVal;
                            if (empty($flat)) continue;
                        }
                        $from = $change['from'] ?? $change['old'] ?? null;
                        $to   = $change['to']   ?? $change['new'] ?? $change;
                        if ($from === $to) continue;
                        $filteredChanges[$field] = $change;
                    }
                    $changeCount = count($filteredChanges);
                @endphp
                <div style="display:grid;grid-template-columns:18px 1fr;gap:0 0.875rem;">
                    <div style="display:flex;flex-direction:column;align-items:center;padding-top:4px;">
                        <div class="tl-dot" style="background:{{ $aColor }};box-shadow:0 0 0 3px {{ $aBg }};"></div>
                        @if(!$isLast)<div class="tl-line"></div>@endif
                    </div>
                    <div style="padding-bottom:{{ $isLast ? '0' : '1rem' }};">
                        <div style="display:flex;align-items:center;gap:0.45rem;flex-wrap:wrap;margin-bottom:0.3rem;">
                            <span style="font-size:0.84rem;font-weight:700;color:var(--ink);">{{ $log->user?->name ?? 'System' }}</span>
                            <span style="display:inline-flex;align-items:center;gap:0.25rem;padding:2px 8px;border-radius:99px;font-size:0.67rem;font-weight:700;background:{{ $aBg }};color:{{ $aColor }};border:1px solid {{ $aBorder }};">
                                <i class="fas {{ $aIcon }}" style="font-size:0.55rem;"></i>
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                            <span style="font-size:0.7rem;color:#9ca3af;margin-left:auto;">{{ $log->created_at->format('M d, Y · h:i A') }}</span>
                        </div>
                        @if($log->action === 'created')
                        <div style="display:flex;align-items:center;gap:0.5rem;padding:0.4rem 0.75rem;border-radius:8px;background:rgba(34,197,94,0.05);border:1px solid rgba(34,197,94,0.18);">
                            <i class="fas fa-circle-check" style="color:#16a34a;font-size:0.72rem;"></i>
                            <span style="font-size:0.78rem;color:#15803d;font-weight:600;">Project record was created.</span>
                        </div>
                        @elseif($changeCount > 0)
                        <button onclick="toggleLog('log-{{ $idx }}')"
                            style="display:inline-flex;align-items:center;gap:0.35rem;padding:2px 9px;border-radius:6px;border:1px solid var(--bd);background:var(--bg2);color:var(--tx2);font-size:0.7rem;font-weight:600;cursor:pointer;font-family:'Instrument Sans',sans-serif;transition:all 0.15s;"
                            onmouseover="this.style.borderColor='rgba(249,115,22,0.3)';this.style.color='#ea580c'"
                            onmouseout="this.style.borderColor='var(--bd)';this.style.color='var(--tx2)'">
                            <i class="fas fa-list-ul" style="font-size:0.55rem;"></i>
                            {{ $changeCount }} {{ Str::plural('change', $changeCount) }}
                            <i id="log-{{ $idx }}-chevron" class="fas fa-chevron-down" style="font-size:0.5rem;transition:transform 0.2s;"></i>
                        </button>
                        <div id="log-{{ $idx }}" style="display:none;flex-direction:column;gap:0.3rem;margin-top:0.5rem;">
                            @foreach($filteredChanges as $field => $change)
                            @php
                                $rawFrom     = $change['from'] ?? $change['old'] ?? null;
                                $rawTo       = $change['to']   ?? $change['new'] ?? $change;
                                $displayFrom = is_array($rawFrom) ? implode(', ', array_filter(array_map(fn($v) => is_array($v) ? json_encode($v) : (string)$v, $rawFrom))) : (string)($rawFrom ?? '');
                                $displayTo   = is_array($rawTo)   ? implode(', ', array_filter(array_map(fn($v) => is_array($v) ? json_encode($v) : (string)$v, $rawTo)))   : (string)($rawTo ?? '');
                                if ($field === 'extension_days') {
                                    $nums = array_filter(explode(',', preg_replace('/[^0-9,]/', '', $displayTo)));
                                    $displayTo = implode(', ', array_map(fn($n) => trim($n).'d', $nums));
                                }
                                $isSlip  = $field === 'slippage';
                                $slipNum = $isSlip ? (float)$displayTo : 0;
                                $labelMap = ['as_planned'=>'As Planned (%)','work_done'=>'Work Done (%)','status'=>'Status','completed_at'=>'Date Completed','remarks_recommendation'=>'Remarks','issuances'=>'Notifications','documents_pressed'=>'Documents','extension_days'=>'Extension Days','vo_days'=>'VO Days','vo_cost'=>'VO Cost','cost_involved'=>'Cost Involved','suspension_days'=>'Suspension Days','date_requested'=>'Date Requested','ld_accomplished'=>'LD Accomplished (%)','ld_days_overdue'=>'Days Overdue From','billing_amounts'=>'Billing Amounts','billing_dates'=>'Billing Dates','performance_bond_date'=>'Performance Bond Date'];
                                $label = $labelMap[$field] ?? ucwords(str_replace('_', ' ', $field));
                            @endphp
                            <div style="display:flex;align-items:center;gap:0.45rem;flex-wrap:wrap;padding:0.4rem 0.75rem;border-radius:8px;background:var(--bg2);border:1px solid var(--bd);">
                                <span style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:var(--ink2);min-width:80px;">{{ $label }}</span>
                                @if($displayFrom !== '')
                                    <span style="font-size:0.74rem;color:#9ca3af;text-decoration:line-through;background:rgba(156,163,175,0.1);padding:1px 7px;border-radius:5px;">{{ $displayFrom }}</span>
                                    <i class="fas fa-arrow-right" style="color:#d1d5db;font-size:0.6rem;"></i>
                                @endif
                                @if($isSlip)
                                    <span style="font-size:0.74rem;font-weight:700;color:{{ $slipNum >= 0 ? '#16a34a' : '#dc2626' }};background:{{ $slipNum >= 0 ? 'rgba(34,197,94,0.1)' : 'rgba(239,68,68,0.1)' }};padding:1px 8px;border-radius:5px;">{{ $slipNum >= 0 ? '+' : '' }}{{ $displayTo }}%</span>
                                @else
                                    <span style="font-size:0.74rem;font-weight:600;color:var(--tx);background:rgba(249,115,22,0.07);padding:1px 8px;border-radius:5px;border:1px solid rgba(249,115,22,0.14);">{{ $displayTo }}</span>
                                @endif
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
        <div style="padding:1.5rem 1.25rem;display:flex;align-items:center;gap:0.6rem;color:#9ca3af;">
            <i class="fas fa-inbox"></i>
            <p style="font-size:0.845rem;font-style:italic;">No activity recorded yet.</p>
        </div>
        @endif
    </div>
</div>

{{-- ══════════ DANGER ZONE (always visible) ══════════ --}}
<div style="background:var(--bg);border:1px solid rgba(239,68,68,0.2);border-radius:14px;padding:1.1rem 1.25rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;box-shadow:var(--sh);" class="fu d5">
    <div>
        <p style="font-family:'Syne',sans-serif;font-size:0.85rem;font-weight:800;color:#991b1b;display:flex;align-items:center;gap:0.45rem;margin-bottom:0.2rem;">
            <i class="fas fa-triangle-exclamation" style="font-size:0.8rem;"></i> Danger Zone
        </p>
        <p style="font-size:0.78rem;color:#b91c1c;">Permanently delete this project and all associated records. This cannot be undone.</p>
    </div>
    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST"
          onsubmit="return confirm('Delete this project permanently? This action cannot be reversed.')">
        @csrf @method('DELETE')
        <button type="submit"
            style="display:inline-flex;align-items:center;gap:0.45rem;padding:0.6rem 1.15rem;background:#dc2626;color:#fff;font-weight:700;font-size:0.825rem;border-radius:9px;border:none;cursor:pointer;box-shadow:0 2px 10px rgba(220,38,38,0.2);font-family:'Instrument Sans',sans-serif;transition:all 0.2s;"
            onmouseover="this.style.background='#b91c1c';this.style.transform='translateY(-1px)'"
            onmouseout="this.style.background='#dc2626';this.style.transform='translateY(0)'">
            <i class="fas fa-trash" style="font-size:0.8rem;"></i> Delete Project
        </button>
    </form>
</div>

</div>
@push('scripts')
    @vite('resources/js/admin/projects/show.js')
@endpush
</x-app-layout>
BLADE_EOF
echo "done"