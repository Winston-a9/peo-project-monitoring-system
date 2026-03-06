<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; letter-spacing:-0.03em; color:var(--text-primary); display:flex; align-items:center; gap:0.6rem;">
                <span style="background:#f97316; width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(249,115,22,0.35);">
                    <i class="fas fa-folder" style="color:white; font-size:0.85rem;"></i>
                </span>
                {{ $project->project_title }}
            </h2>
            <p style="color:var(--text-secondary); font-size:0.82rem; margin-top:3px;">Project details and information</p>
        </div>
        <div style="display:flex; gap:0.6rem; align-items:center;">
            <a href="{{ route('admin.projects.edit', $project) }}"
               style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.65rem 1.2rem; background:#f97316; color:white; font-weight:600; font-size:0.855rem; border-radius:9px; text-decoration:none; box-shadow:0 3px 14px rgba(249,115,22,0.35); transition:all 0.2s;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.projects.index') }}"
               style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.65rem 1.1rem; border:1.5px solid var(--border); border-radius:9px; font-weight:600; font-size:0.825rem; color:var(--text-secondary); text-decoration:none; background:var(--bg-secondary); transition:all 0.2s;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <!-- Theme Toggle Button -->
            <button id="themeToggle" type="button" aria-label="Toggle dark mode" style="
                background: var(--bg-secondary);
                border: 1.5px solid var(--border);
                border-radius: 10px;
                padding: 0.5rem 0.95rem;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                color: var(--text-primary);
                font-size: 0.9rem;
                font-weight: 500;
                white-space: nowrap;
                position: relative;
                z-index: 50;
                font-family: 'Instrument Sans', sans-serif;
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                transition: all 0.3s ease;
            " onmouseover="this.style.background='rgba(249,115,22,0.12)'; this.style.borderColor='rgba(249,115,22,0.4)'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" 
               onmouseout="this.style.background='var(--bg-secondary)'; this.style.borderColor='var(--border)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'" 
               onclick="toggleTheme()">
                <i class="fas" id="themeIcon" style="color:#f97316; font-size: 0.95rem;"></i>
                <span id="themeLabel" style="font-weight: 600;">Light</span>
            </button>

            <script>
                function initTheme() {
                    const html = document.documentElement;
                    const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
                    updateThemeButton(currentTheme);
                }

                function updateThemeButton(theme) {
                    const icon = document.getElementById('themeIcon');
                    const label = document.getElementById('themeLabel');
                    
                    if (theme === 'dark') {
                        icon.className = 'fas fa-moon';
                        label.textContent = 'Dark';
                    } else {
                        icon.className = 'fas fa-sun';
                        label.textContent = 'Light';
                    }
                }

                function toggleTheme() {
                    const html = document.documentElement;
                    const body = document.body;
                    const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                    // Update DOM
                    html.classList.remove(currentTheme);
                    html.classList.add(newTheme);
                    
                    if (newTheme === 'dark') {
                        body.classList.add('dark');
                    } else {
                        body.classList.remove('dark');
                    }

                    // Save preference
                    localStorage.setItem('theme-mode', newTheme);
                    
                    // Update button
                    updateThemeButton(newTheme);
                }

                // Initialize on page load
                document.addEventListener('DOMContentLoaded', initTheme);
                initTheme();
            </script>
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
    $totalDays = (int) array_sum(array_map(fn($d) => (int) $d, array_filter((array) $extensionDays)));@endphp

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

    body { color:var(--text-primary); transition:background 0.3s, color 0.3s; }

    .view-card { background:var(--bg-primary); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
    .card-header { padding:1rem 1.5rem; border-bottom:1px solid var(--border); background:var(--bg-secondary); display:flex; align-items:center; gap:0.5rem; }
    .card-header span { font-family:'Syne',sans-serif; font-weight:700; font-size:0.875rem; color:var(--ink); }
    .info-row { display:flex; align-items:center; gap:1rem; padding:0.875rem 1.5rem; border-bottom:1px solid var(--border); }
    .info-row:last-child { border-bottom:none; }
    .info-icon { width:34px; height:34px; background:rgba(249,115,22,0.1); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .info-key  { font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); margin-bottom:1px; }
    .info-val  { font-size:0.9rem; font-weight:700; color:var(--text-primary); }
    
    .badge { display:inline-flex; align-items:center; gap:0.35rem; padding:3px 10px; border-radius:99px; font-size:0.72rem; font-weight:700; border:1px solid; }
    @media (prefers-color-scheme: light) {
        .badge-green  { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
        .badge-red    { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
        .badge-blue   { background:#eff6ff; color:#2563eb; border-color:#bfdbfe; }
        .badge-amber  { background:#fffbeb; color:#d97706; border-color:#fde68a; }
    }
    @media (prefers-color-scheme: dark) {
        .badge-green  { background:rgba(34,197,94,0.15); color:#4ade80; border-color:rgba(34,197,94,0.3); }
        .badge-red    { background:rgba(239,68,68,0.15); color:#f87171; border-color:rgba(239,68,68,0.3); }
        .badge-blue   { background:rgba(59,130,246,0.15); color:#60a5fa; border-color:rgba(59,130,246,0.3); }
        .badge-amber  { background:rgba(217,119,6,0.15); color:#fbbf24; border-color:rgba(217,119,6,0.3); }
    }
    
    .prog-track { height:5px; background:rgba(249,115,22,0.1); border-radius:99px; margin-top:0.5rem; overflow:hidden; }
    .prog-fill  { height:100%; border-radius:99px; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(14px);} to{opacity:1;transform:translateY(0);} }
    .fade-up { animation:fadeUp 0.45s ease both; }

    .doc-row { display:flex; align-items:center; justify-content:space-between; gap:0.75rem; padding:0.6rem 0.875rem; background:var(--bg-secondary); border:1px solid var(--border); border-radius:9px; }
    .doc-row-left { display:flex; align-items:center; gap:0.5rem; min-width:0; }
    .doc-row-name { font-size:0.845rem; font-weight:600; color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .days-pill { display:inline-flex; align-items:center; gap:0.3rem; padding:3px 10px; border-radius:99px; white-space:nowrap; flex-shrink:0; font-size:0.72rem; font-weight:700; background:rgba(249,115,22,0.1); color:var(--orange-600); border:1px solid rgba(249,115,22,0.22); }
    .days-pill.na { background:rgba(156,163,175,0.1); color:#9ca3af; border-color:rgba(156,163,175,0.2); }
    .issue-row { display:flex; align-items:center; gap:0.6rem; padding:0.6rem 0.875rem; background:var(--bg-secondary); border:1px solid var(--border); border-radius:9px; }
    .days-total-bar {
        display:flex; align-items:center; justify-content:space-between;
        padding:0.65rem 0.875rem; border-radius:9px; margin-top:0.875rem;
        background:rgba(249,115,22,0.05); border:1px solid rgba(249,115,22,0.14);
    }
</style>

<div class="max-w-5xl mx-auto space-y-5 fade-up">

    {{-- ── Status + Progress ── --}}
    <div style="display:grid; grid-template-columns:1fr 2fr; gap:1.25rem;">

        <div class="view-card" style="padding:1.5rem; display:flex; flex-direction:column; justify-content:space-between; gap:1rem;">
            <p style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); display:flex; align-items:center; gap:0.4rem;">
                <i class="fas fa-briefcase" style="color:var(--orange-500);"></i> Contract Status
            </p>
            @if($daysLeft < 0)
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

        <div class="view-card" style="padding:1.5rem;">
            <p style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); display:flex; align-items:center; gap:0.4rem; margin-bottom:1.1rem;">
                <i class="fas fa-chart-bar" style="color:var(--orange-500);"></i> Progress Overview
            </p>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
                <div style="background:#fffaf5; border:1px solid var(--border); border-radius:10px; padding:1rem; text-align:center;">
                    <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); margin-bottom:0.5rem;">As Planned</p>
                    <p style="font-family:'Syne',sans-serif; font-size:1.8rem; font-weight:800; color:var(--ink); letter-spacing:-0.03em; line-height:1;">
                        {{ $project->as_planned }}<span style="font-size:0.9rem; color:var(--ink-muted);">%</span>
                    </p>
                    <div class="prog-track" style="margin-top:0.75rem;"><div class="prog-fill" style="background:var(--orange-500); width:{{ $project->as_planned }}%;"></div></div>
                </div>
                <div style="background:#fffaf5; border:1px solid var(--border); border-radius:10px; padding:1rem; text-align:center;">
                    <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); margin-bottom:0.5rem;">Work Done</p>
                    <p style="font-family:'Syne',sans-serif; font-size:1.8rem; font-weight:800; color:var(--ink); letter-spacing:-0.03em; line-height:1;">
                        {{ $project->work_done }}<span style="font-size:0.9rem; color:var(--ink-muted);">%</span>
                    </p>
                    <div class="prog-track" style="margin-top:0.75rem;"><div class="prog-fill" style="background:#3b82f6; width:{{ $project->work_done }}%;"></div></div>
                </div>
                <div style="background:#fffaf5; border:1px solid var(--border); border-radius:10px; padding:1rem; text-align:center;">
                    <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.07em; color:var(--ink-muted); margin-bottom:0.5rem;">Slippage</p>
                    <p style="font-family:'Syne',sans-serif; font-size:1.8rem; font-weight:800; letter-spacing:-0.03em; line-height:1;
                       color:{{ $project->slippage < 0 ? '#dc2626' : ($project->slippage > 0 ? '#16a34a' : '#6b7280') }}">
                        {{ $project->slippage > 0 ? '+' : '' }}{{ $project->slippage }}<span style="font-size:0.9rem;">%</span>
                    </p>
                    <p style="font-size:0.72rem; margin-top:0.5rem; font-weight:600; color:{{ $project->slippage < 0 ? '#dc2626' : ($project->slippage > 0 ? '#16a34a' : '#9ca3af') }}">
                        @if($project->slippage < 0) <i class="fas fa-arrow-down"></i> Behind
                        @elseif($project->slippage > 0) <i class="fas fa-arrow-up"></i> Ahead
                        @else <i class="fas fa-minus"></i> On track @endif
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
                    ['fas fa-user-tie','In Charge',$project->in_charge],
                    ['fas fa-folder','Project Title',$project->project_title],
                    ['fas fa-map-marker-alt','Location',$project->location],
                    ['fas fa-building','Contractor',$project->contractor],
                    ['fas fa-peso-sign','Contract Amount','₱'.number_format($project->contract_amount,2)],
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
                        ['fas fa-calendar-check','Date Started',$project->date_started->format('F d, Y'),$project->date_started->format('l')],
                        ['fas fa-calendar-times','Original Expiry',$project->original_contract_expiry->format('F d, Y'),$project->original_contract_expiry->format('l')],
                        ['fas fa-calendar-pen','Revised Expiry',$project->revised_contract_expiry?$project->revised_contract_expiry->format('F d, Y'):null,$project->revised_contract_expiry?$project->revised_contract_expiry->format('l'):null],
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

    {{-- ── Activity Logs ── --}}
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

    {{-- Scrollable container --}}
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

                    {{-- Timeline spine --}}
                    <div style="display:flex; flex-direction:column; align-items:center; padding-top:0.25rem;">
                        <div style="width:11px; height:11px; border-radius:50%; background:var(--orange-500); flex-shrink:0;
                                    box-shadow:0 0 0 3px rgba(249,115,22,0.18);"></div>
                        @if(!$isLast)
                            <div style="width:2px; flex:1; min-height:2rem; background:rgba(249,115,22,0.15); margin:3px 0;"></div>
                        @endif
                    </div>

                    {{-- Log body --}}
                    <div style="padding:0 0 {{ $isLast ? '0' : '1.25rem' }} 0;">

                        {{-- Who + when --}}
                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.35rem;">
                            <span style="font-size:0.875rem; font-weight:700; color:var(--ink);">
                                {{ $log->user?->name ?? 'System' }}
                            </span>
                            <span style="color:#d1d5db; font-size:0.7rem;">•</span>
                            <span style="font-size:0.72rem; color:#9ca3af;">
                                <i class="fas fa-clock" style="font-size:0.6rem;"></i>
                                {{ $log->created_at->format('F d, Y \a\t H:i') }}
                            </span>
                            <span style="color:#d1d5db; font-size:0.7rem;">•</span>
                            <span style="font-size:0.7rem; color:#9ca3af; font-style:italic;">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </div>

                        {{-- Collapsible changes toggle --}}
                        @if($changeCount)
                            <button
                                onclick="toggleLog('log-{{ $idx }}')"
                                style="display:inline-flex; align-items:center; gap:0.4rem; margin-top:0.1rem;
                                    padding:3px 10px; border-radius:99px; border:1px solid rgba(249,115,22,0.22);
                                    background:rgba(249,115,22,0.06); color:var(--orange-600);
                                    font-size:0.72rem; font-weight:700; cursor:pointer;
                                    font-family:'Instrument Sans',sans-serif; transition:all 0.2s;"
                                onmouseover="this.style.background='rgba(249,115,22,0.13)'"
                                onmouseout="this.style.background='rgba(249,115,22,0.06)'">
                                <i class="fas fa-list-ul" style="font-size:0.6rem;"></i>
                                {{ $changeCount }} {{ Str::plural('change', $changeCount) }}
                                <i id="log-{{ $idx }}-chevron" class="fas fa-chevron-down" style="font-size:0.55rem; transition:transform 0.25s;"></i>
                            </button>

                            {{-- Expanded changes --}}
                            <div id="log-{{ $idx }}" style="display:none; flex-direction:column; gap:0.35rem; margin-top:0.5rem;">
                                @foreach($changes as $field => $change)
                                @php
                                        $isNested = is_array($change) && (array_key_exists('old', $change) || array_key_exists('new', $change));

                                        // If values are arrays of history, grab only the last (current) item
                                        $rawOld = $isNested ? ($change['old'] ?? null) : null;
                                        $rawNew = $isNested ? ($change['new'] ?? $change) : $change;

                                        $oldVal = is_array($rawOld) ? last($rawOld) : $rawOld;
                                        $newVal = is_array($rawNew) ? last($rawNew) : $rawNew;

                                   if ($field === 'work_done') continue;

                                    // Skip issuances if the value is empty/blank
                                    if ($field === 'issuances') {
                                        $checkVal = is_array($rawNew) ? last($rawNew) : $rawNew;
                                        if (empty($checkVal) || $checkVal === '[]' || $checkVal === [] || $checkVal === null) continue;
                                    }

                                    $formatVal = function($val) {
                                        if (is_array($val)) {
                                            return implode(', ', array_map(function($v) {
                                                try { return \Carbon\Carbon::parse($v)->format('F d, Y'); } catch(\Exception $e) { return $v; }
                                            }, $val));
                                        }
                                        try { return \Carbon\Carbon::parse($val)->format('F d, Y'); } catch(\Exception $e) { return $val ?? '—'; }
                                    };

                                    $isDate     = $field === 'revised_contract_expiry';
                                    $isSlippage = $field === 'slippage';

                                    if ($isSlippage) {
                                        $displayNew = is_array($newVal)
                                            ? implode(', ', array_map(fn($v) => is_array($v) ? json_encode($v) : $v, $newVal))
                                            : ($newVal ?? '—');
                                        $slippageNum      = (float) $displayNew;
                                        $slippagePositive = $slippageNum >= 0;
                                    }
                                @endphp

                                <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;
                                    padding:0.45rem 0.75rem; border-radius:8px;
                                    background:#fffaf5; border:1px solid rgba(249,115,22,0.1);">

                                    <span style="font-size:0.72rem; font-weight:700; text-transform:uppercase;
                                        letter-spacing:0.05em; color:var(--ink-muted); min-width:110px;">
                                        {{ str_replace('_', ' ', $field) }}
                                    </span>

                                    @if($isSlippage)
                                        <span style="display:inline-flex; align-items:center; gap:0.35rem;
                                            font-size:0.78rem; font-weight:700; padding:2px 10px; border-radius:99px;
                                            background:{{ $slippagePositive ? '#f0fdf4' : '#fef2f2' }};
                                            color:{{ $slippagePositive ? '#16a34a' : '#dc2626' }};
                                            border:1px solid {{ $slippagePositive ? '#bbf7d0' : '#fecaca' }};">
                                            <i class="fas {{ $slippagePositive ? 'fa-arrow-up' : 'fa-arrow-down' }}" style="font-size:0.6rem;"></i>
                                            {{ $slippagePositive ? '+' : '' }}{{ $displayNew }}%
                                            {{ $slippagePositive ? 'Ahead' : 'Behind' }}
                                        </span>

                                    @elseif($isDate)
                                        @if($oldVal !== null)
                                            <span style="font-size:0.78rem; color:#dc2626; font-weight:500;
                                                background:#fef2f2; border:1px solid #fecaca;
                                                padding:1px 8px; border-radius:99px; text-decoration:line-through;">
                                                {{ $formatVal($oldVal) }}
                                            </span>
                                            <i class="fas fa-arrow-right" style="color:#9ca3af; font-size:0.65rem;"></i>
                                        @endif
                                        <span style="font-size:0.78rem; color:#16a34a; font-weight:600;
                                            background:#f0fdf4; border:1px solid #bbf7d0;
                                            padding:1px 8px; border-radius:99px;">
                                            {{ $formatVal($newVal) }}
                                        </span>

                                    @else
                                        @if($oldVal !== null)
                                            <span style="font-size:0.78rem; color:#dc2626; font-weight:500;
                                                background:#fef2f2; border:1px solid #fecaca;
                                                padding:1px 8px; border-radius:99px; text-decoration:line-through;">
                                                {{ is_array($oldVal) ? implode(', ', array_map(fn($v) => is_array($v) ? json_encode($v) : $v, $oldVal)) : $oldVal }}
                                            </span>
                                            <i class="fas fa-arrow-right" style="color:#9ca3af; font-size:0.65rem;"></i>
                                        @endif
                                        @php
                                        $displayVal = is_array($newVal)
                                            ? implode(', ', array_map(fn($v) => is_array($v) ? json_encode($v) : $v, $newVal))
                                            : ($newVal ?? '—');

                                        // Format extension_days as "10 days" instead of '["10"]'
                                        if ($field === 'extension_days') {
                                            $cleaned = preg_replace('/[^0-9,]/', '', $displayVal);
                                            $nums = array_filter(explode(',', $cleaned));
                                            $displayVal = implode(', ', array_map(fn($n) => trim($n) . ' days', $nums));
                                        }
                                    @endphp
                                    <span style="font-size:0.78rem; color:#16a34a; font-weight:600;
                                        background:#f0fdf4; border:1px solid #bbf7d0;
                                        padding:1px 8px; border-radius:99px;">
                                        {{ $displayVal }}
                                    </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>{{-- end expanded --}}
                        @endif

                    </div>{{-- end log body --}}

                    {{-- Action badge --}}
                    <div style="display:flex; align-items:flex-start; padding-top:0.05rem;">
                        <span style="display:inline-flex; align-items:center; gap:0.3rem; padding:3px 10px; border-radius:99px;
                            font-size:0.7rem; font-weight:700; white-space:nowrap;
                            background:{{ $actionStyles['bg'] }}; color:{{ $actionStyles['color'] }};
                            border:1px solid {{ $actionStyles['border'] }};">
                            <i class="fas {{ $actionStyles['icon'] }}" style="font-size:0.6rem;"></i>
                            {{ str_replace('_', ' ', $log->action) }}
                        </span>
                    </div>

                </div>{{-- end grid row --}}
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

<script>
function toggleLog(id) {
    const el      = document.getElementById(id);
    const chevron = document.getElementById(id + '-chevron');
    const isOpen  = el.style.display === 'flex';
    el.style.display        = isOpen ? 'none' : 'flex';
    chevron.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
}
</script>
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

    {{-- ── Danger Zone ── --}}
    <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:14px; padding:1.5rem;">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
            <div>
                <h3 style="font-family:'Syne',sans-serif; font-weight:800; font-size:0.95rem; color:#991b1b; display:flex; align-items:center; gap:0.5rem; margin-bottom:0.3rem;">
                    <i class="fas fa-exclamation-triangle"></i> Danger Zone
                </h3>
                <p style="font-size:0.83rem; color:#b91c1c;">Once you delete this project, there is no going back.</p>
            </div>
            <form action="{{ route('admin.projects.destroy', $project) }}" method="POST"
                  onsubmit="return confirm('Are you absolutely sure you want to delete this project?')">
                @csrf @method('DELETE')
                <button type="submit"
                    style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.65rem 1.25rem; background:#dc2626; color:white; font-weight:600; font-size:0.855rem; border-radius:9px; border:none; cursor:pointer; box-shadow:0 2px 10px rgba(220,38,38,0.3); font-family:'Instrument Sans',sans-serif; transition:all 0.2s;"
                    onmouseover="this.style.background='#b91c1c'"
                    onmouseout="this.style.background='#dc2626'">
                    <i class="fas fa-trash"></i> Delete Project
                </button>
            </form>
        </div>
    </div>

</div>
</x-app-layout>