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
            <a href="{{ route('admin.projects.edit', $project) }}" class="app-btn-secondary"><i class="fas fa-edit"></i> Edit</a>
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
@endphp

<div style="max-width:1100px;margin:0 auto;display:flex;flex-direction:column;gap:0.875rem;">

{{-- ══════════ ROW 1 · STATUS + PROGRESS + AMOUNT ══════════ --}}
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
        @elseif($daysLeft < 0)
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <div style="width:8px;height:8px;border-radius:50%;background:#ef4444;flex-shrink:0;"></div>
                <span style="font-family:'Syne',sans-serif;font-size:1.15rem;font-weight:800;color:#dc2626;">Expired</span>
            </div>
            <p style="font-size:0.75rem;color:#ef4444;">{{ abs($daysLeft) }} days ago</p>
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

{{-- ══════════ ROW 2 · PROJECT INFO + CONTRACT DATES ══════════ --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.875rem;" class="fu d1">

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
                @elseif($project->status === 'expired')
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

{{-- ══════════ ROW 3 · TE/VO/SO ══════════ --}}
@if($teCount > 0 || $voCount > 0 || $hasSO)
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:0.875rem;" class="fu d2">
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
<div class="card fu d2" style="overflow:hidden;">
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
                    $teDateOffset = 0;
                    foreach ($teEntries as $idx => $label) {
                        $days = (int)($extensionDays[$idx] ?? 0);
                        $cost = $costInvolved[$idx] ?? null;
                        $date = $dateRequested[$teDateOffset + $idx] ?? null;
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
@endif

{{-- ══════════ ROW 4 · ISSUANCES + LD ══════════ --}}
@if($hasIssuances || $hasLD)
<div style="display:grid;grid-template-columns:{{ ($hasIssuances && $hasLD) ? '1fr 1fr' : '1fr' }};gap:0.875rem;" class="fu d3">

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

    @if($hasLD)
    <div class="card" style="overflow:hidden;">
        <div style="display:flex;align-items:center;border-bottom:1px solid var(--bd);background:var(--bg2);">
            <button onclick="toggleLDTab('view')" id="ld-tab-view"
                style="flex:1;padding:0.875rem 1.25rem;background:transparent;border:none;cursor:pointer;font-size:0.825rem;font-weight:700;color:var(--tx);border-bottom:2px solid #f97316;transition:all 0.2s;display:flex;align-items:center;gap:0.5rem;font-family:'Instrument Sans',sans-serif;">
                <i class="fas fa-eye" style="font-size:0.75rem;"></i> View Assessment
            </button>
            <button onclick="toggleLDTab('update')" id="ld-tab-update"
                style="flex:1;padding:0.875rem 1.25rem;background:transparent;border:none;cursor:pointer;font-size:0.825rem;font-weight:700;color:var(--tx2);border-bottom:2px solid transparent;transition:all 0.2s;display:flex;align-items:center;gap:0.5rem;font-family:'Instrument Sans',sans-serif;">
                <i class="fas fa-edit" style="font-size:0.75rem;"></i> Update Assessment
            </button>
            <div style="padding:0.875rem 1.25rem;display:flex;align-items:center;gap:0.5rem;border-left:1px solid var(--bd);flex-shrink:0;">
                <i class="fas fa-calculator" style="color:#dc2626;font-size:0.8rem;"></i>
                @if($project->total_ld)
                    <span class="pill p-re">₱{{ number_format($project->total_ld, 2) }}</span>
                @else
                    <span class="pill p-gy">No Data</span>
                @endif
            </div>
        </div>

        {{-- VIEW TAB --}}
        <div id="ld-tab-view-content" style="display:block;">
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
                    <span class="dl"><i class="fas fa-calendar-xmark"></i> Days Overdue From</span>
                    <span class="dv">
                        @if($project->ld_days_overdue)
                            {{ \Carbon\Carbon::parse($project->ld_days_overdue)->format('M d, Y') }}
                        @else —
                        @endif
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
            <div style="padding:0.875rem 1.25rem;background:rgba(59,130,246,0.04);border-top:1px solid var(--bd);display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                <div>
                    <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink2);margin-bottom:0.35rem;">Days Overdue (Calculated)</p>
                    <p style="font-size:0.9rem;font-weight:600;color:#3b82f6;">
                        @if($project->ld_days_overdue)
                            @php $daysOverdue = \Carbon\Carbon::parse($project->ld_days_overdue)->diffInDays(now()); @endphp
                            {{ $daysOverdue }} {{ Str::plural('day', $daysOverdue) }}
                        @else Not Set
                        @endif
                    </p>
                </div>
                <div>
                    <p style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink2);margin-bottom:0.35rem;">Total Liability</p>
                    <p style="font-size:0.9rem;font-weight:600;color:#16a34a;">
                        @if($project->ld_per_day && $project->ld_days_overdue)
                            @php $calculatedTotal = $project->ld_per_day * \Carbon\Carbon::parse($project->ld_days_overdue)->diffInDays(now()); @endphp
                            ₱{{ number_format($calculatedTotal, 2) }}
                        @else Pending
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- UPDATE TAB --}}
        <div id="ld-tab-update-content" style="display:none;">
            <form action="{{ route('admin.projects.update', $project) }}" method="POST">
                @csrf @method('PUT')
                <div style="padding:1.25rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    @php
                        $inStyle = "width:100%;padding:0.7rem 0.875rem;border:1.5px solid var(--bd);border-radius:8px;background:var(--bg);color:var(--tx);font-size:0.875rem;font-family:'Instrument Sans',sans-serif;box-sizing:border-box;outline:none;transition:border-color 0.2s,box-shadow 0.2s;";
                        $lblStyle = "display:block;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:var(--ink2);margin-bottom:0.45rem;";
                        $hintStyle = "font-size:0.68rem;color:#9ca3af;margin-top:0.3rem;";
                    @endphp
                    <div>
                        <label style="{{ $lblStyle }}"><i class="fas fa-percent" style="color:var(--or5);width:14px;"></i> Accomplished (%)</label>
                        <input type="number" name="ld_accomplished" min="0" max="100" step="0.00001"
                            value="{{ $project->ld_accomplished ?? '' }}" placeholder="e.g. 84.70200"
                            style="{{ $inStyle }}"
                            onfocus="this.style.borderColor='rgba(249,115,22,0.5)';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                            onblur="this.style.borderColor='var(--bd)';this.style.boxShadow='none'">
                        <p style="{{ $hintStyle }}">Up to 5 decimal places (e.g. 84.70200)</p>
                    </div>
                    <div>
                        <label style="{{ $lblStyle }}"><i class="fas fa-percent" style="color:var(--or5);width:14px;"></i> Unworked (%)</label>
                        <input type="number" name="ld_unworked" min="0" max="100" step="0.00001"
                            value="{{ $project->ld_unworked ?? '' }}" placeholder="e.g. 15.29800"
                            style="{{ $inStyle }}"
                            onfocus="this.style.borderColor='rgba(249,115,22,0.5)';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                            onblur="this.style.borderColor='var(--bd)';this.style.boxShadow='none'">
                        <p style="{{ $hintStyle }}">100% − Accomplished</p>
                    </div>
                    <div>
                        <label style="{{ $lblStyle }}"><i class="fas fa-calendar" style="color:var(--or5);width:14px;"></i> Days Overdue From</label>
                        <input type="date" name="ld_days_overdue"
                            value="{{ $project->ld_days_overdue ? \Carbon\Carbon::parse($project->ld_days_overdue)->format('Y-m-d') : '' }}"
                            style="{{ $inStyle }}cursor:pointer;"
                            onfocus="this.style.borderColor='rgba(249,115,22,0.5)';this.style.boxShadow='0 0 0 3px rgba(249,115,22,0.1)'"
                            onblur="this.style.borderColor='var(--bd)';this.style.boxShadow='none'">
                        <p style="{{ $hintStyle }}">Date the project fell behind</p>
                    </div>
                    <div>
                        <label style="{{ $lblStyle }}"><i class="fas fa-peso-sign" style="color:var(--or5);width:14px;"></i> LD / Day (₱) <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;font-size:0.65rem;">(auto)</span></label>
                        <div style="padding:0.7rem 0.875rem;border:1.5px solid rgba(249,115,22,0.1);border-radius:8px;background:rgba(249,115,22,0.03);color:var(--tx2);font-size:0.875rem;font-family:'Instrument Sans',sans-serif;">
                            ₱{{ $project->ld_per_day ? number_format($project->ld_per_day, 2) : '0.00' }}
                        </div>
                        <p style="{{ $hintStyle }}">(Accomplished ÷ 100) × Contract Amount × 0.001</p>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label style="{{ $lblStyle }}"><i class="fas fa-peso-sign" style="color:#dc2626;width:14px;"></i> Total LD (₱) <span style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;font-size:0.65rem;">(auto)</span></label>
                        <div style="padding:0.7rem 0.875rem;border:1.5px solid rgba(239,68,68,0.15);border-radius:8px;background:rgba(239,68,68,0.04);font-size:0.95rem;font-family:'Syne',sans-serif;font-weight:800;color:#dc2626;">
                            ₱{{ $project->total_ld ? number_format($project->total_ld, 2) : '0.00' }}
                        </div>
                        <p style="{{ $hintStyle }}">Recalculated when you save changes in the Edit page</p>
                    </div>
                </div>
                <div style="padding:0 1.25rem 1.25rem;display:flex;align-items:center;gap:0.75rem;">
                    <button type="submit"
                        style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.65rem 1.15rem;background:#f97316;color:#fff;font-weight:700;font-size:0.825rem;border-radius:9px;border:none;cursor:pointer;box-shadow:0 2px 10px rgba(249,115,22,0.25);font-family:'Instrument Sans',sans-serif;transition:all 0.2s;"
                        onmouseover="this.style.background='#ea580c';this.style.transform='translateY(-1px)'"
                        onmouseout="this.style.background='#f97316';this.style.transform='translateY(0)'">
                        <i class="fas fa-check-circle" style="font-size:0.75rem;"></i> Save Assessment
                    </button>
                    <button type="button" onclick="toggleLDTab('view')"
                        style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.65rem 1.15rem;background:transparent;color:var(--tx2);font-weight:700;font-size:0.825rem;border-radius:9px;border:1.5px solid var(--bd);cursor:pointer;font-family:'Instrument Sans',sans-serif;transition:all 0.2s;"
                        onmouseover="this.style.borderColor='#9ca3af'"
                        onmouseout="this.style.borderColor='var(--bd)'">
                        <i class="fas fa-times-circle" style="font-size:0.75rem;"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endif

{{-- ══════════ REMARKS ══════════ --}}
@if($hasRemarks)
<div class="card fu d3">
    <div class="ch">
        <i class="fas fa-comment-dots" style="color:var(--or5);font-size:0.8rem;"></i>
        <span class="ct">Remarks / Recommendation</span>
    </div>
    <div style="padding:1.1rem 1.25rem;">
        <p style="font-size:0.875rem;color:var(--tx);line-height:1.8;white-space:pre-line;">{{ $project->remarks_recommendation }}</p>
    </div>
</div>
@endif

{{-- ══════════ ACTIVITY LOG ══════════ --}}
@php $logs = $project->logs()->with('user')->latest()->get(); @endphp
<div class="card fu d4">
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
    <div style="max-height:500px;overflow-y:auto;padding:1.1rem 1.25rem;">
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
                foreach ($changes as $field => $change) {
                    if (in_array($field, ['work_done', 'updated_at'])) continue;
                    if (in_array($field, ['issuances'])) {
                        $newVal = $change['to'] ?? $change['new'] ?? $change;
                        $flat = is_array($newVal) ? array_filter($newVal) : $newVal;
                        if (empty($flat)) continue;
                    }
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
                            $label   = ucwords(str_replace('_', ' ', $field));
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

{{-- ══════════ DANGER ZONE ══════════ --}}
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