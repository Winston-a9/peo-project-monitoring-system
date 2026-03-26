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

@push('styles')
    @vite('resources/css/admin/projects/edit.css')
@endpush

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

<div class="max-w-5xl mx-auto fade-up" style="padding:0 1.5rem;">

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
                <input type="hidden" name="contract_amount" id="contract_amount" value="{{ $project->contract_amount }}">
                <input type="hidden" id="original_contract_amount" value="{{ (float) ($project->original_contract_amount ?? $project->contract_amount) }}">
                <div class="section-body">
                    <div class="grid-2col">
                        <div class="field-group">
                            <label class="field-label">In Charge</label>
                            <input type="text" name="in_charge" class="field-input"
                                value="{{ old('in_charge', $project->in_charge) }}"
                                placeholder="Who is responsible?" required>
                            @error('in_charge')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="field-group">
                            <label class="field-label">Project Title</label>
                            <input type="text" name="project_title" class="field-input"
                                value="{{ old('project_title', $project->project_title) }}"
                                placeholder="Project name" required>
                            @error('project_title')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="field-group">
                            <label class="field-label">Location</label>
                            <input type="text" name="location" class="field-input"
                                value="{{ old('location', $project->location) }}"
                                placeholder="Project location" required>
                            @error('location')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="field-group">
                            <label class="field-label">Contractor</label>
                            <input type="text" name="contractor" class="field-input"
                                value="{{ old('contractor', $project->contractor) }}"
                                placeholder="Contractor company" required>
                            @error('contractor')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                        </div>
                        <div class="field-group">
                            <label class="field-label">Contract Amount <span style="font-weight:400; color:#9ca3af;">(read-only)</span></label>
                            <p style="font-size:0.9rem;font-weight:700;color:var(--text-primary);margin:0;padding:0.1rem 0;font-family:'Syne',sans-serif;letter-spacing:-0.01em;">
                                ₱{{ number_format($project->contract_amount, 2) }}
                            </p>
                            <p class="field-hint">Adjusted by TE / VO cost entries</p>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Status <span style="font-weight:400;color:#9ca3af;">(auto)</span></label>
                            @php
                                $expiry   = $project->revised_contract_expiry ?? $project->original_contract_expiry;
                                $daysLeft = now()->startOfDay()->diffInDays($expiry->startOfDay(), false);
                            @endphp
                            <div style="display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;">
                                @if($project->status === 'completed')
                                    <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 0.85rem;border-radius:99px;background:rgba(34,197,94,0.1);color:#16a34a;font-size:0.8rem;font-weight:700;">
                                        <span style="width:7px;height:7px;border-radius:50%;background:#22c55e;display:inline-block;"></span> Completed
                                    </span>
                                @elseif($daysLeft < 0)
                                    <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 0.85rem;border-radius:99px;background:rgba(239,68,68,0.1);color:#dc2626;font-size:0.8rem;font-weight:700;">
                                        <span style="width:7px;height:7px;border-radius:50%;background:#dc2626;display:inline-block;"></span> Expired
                                    </span>
                                @elseif($daysLeft <= 30)
                                    <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 0.85rem;border-radius:99px;background:rgba(245,158,11,0.1);color:#d97706;font-size:0.8rem;font-weight:700;">
                                        <span style="width:7px;height:7px;border-radius:50%;background:#f59e0b;animation:pulse 1.5s ease infinite;display:inline-block;"></span> Expiring Soon
                                    </span>
                                @else
                                    <span style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 0.85rem;border-radius:99px;background:rgba(34,197,94,0.1);color:#16a34a;font-size:0.8rem;font-weight:700;">
                                        <span style="width:7px;height:7px;border-radius:50%;background:#22c55e;display:inline-block;"></span> Ongoing
                                    </span>
                                @endif

                                @if($project->status !== 'completed')
                                    <button type="button" onclick="toggleCompleteSection()"
                                        style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.35rem 0.85rem;border-radius:99px;border:1.5px solid rgba(22,163,74,0.3);background:rgba(22,163,74,0.06);color:#16a34a;font-size:0.75rem;font-weight:700;cursor:pointer;font-family:'Instrument Sans',sans-serif;transition:all 0.15s;"
                                        onmouseover="this.style.background='rgba(22,163,74,0.14)';this.style.borderColor='rgba(22,163,74,0.5)'"
                                        onmouseout="this.style.background='rgba(22,163,74,0.06)';this.style.borderColor='rgba(22,163,74,0.3)'">
                                        <i class="fas fa-check" style="font-size:0.65rem;"></i> Mark as Completed
                                    </button>
                                @else
                                    <button type="button" onclick="toggleReactivateSection()"
                                        style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.35rem 0.85rem;border-radius:99px;border:1.5px solid rgba(59,130,246,0.3);background:rgba(59,130,246,0.06);color:#2563eb;font-size:0.75rem;font-weight:700;cursor:pointer;font-family:'Instrument Sans',sans-serif;transition:all 0.15s;"
                                        onmouseover="this.style.background='rgba(59,130,246,0.14)';this.style.borderColor='rgba(59,130,246,0.5)'"
                                        onmouseout="this.style.background='rgba(59,130,246,0.06)';this.style.borderColor='rgba(59,130,246,0.3)'">
                                        <i class="fas fa-rotate-left" style="font-size:0.65rem;"></i> Reactivate Project
                                    </button>
                                @endif
                            </div>

                            @if($project->status === 'completed')
                            <div id="reactivate-section" style="display:none; margin-top:0.875rem; padding:1rem; border-radius:10px; border:1.5px solid rgba(59,130,246,0.2); background:rgba(59,130,246,0.04);">
                                <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.6rem;">
                                    <i class="fas fa-rotate-left" style="color:#2563eb; font-size:0.8rem;"></i>
                                    <label class="field-label" style="margin:0; color:#2563eb;">Reactivate this project?</label>
                                </div>
                                <p style="font-size:0.78rem; color:var(--text-secondary); margin:0 0 0.75rem;">
                                    Status will be auto-determined from the expiry date:
                                    <strong style="color:var(--text-primary);">
                                        {{ $project->revised_contract_expiry
                                            ? $project->revised_contract_expiry->format('F d, Y')
                                            : $project->original_contract_expiry->format('F d, Y') }}
                                    </strong>
                                </p>
                                <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
                                    <button type="button" onclick="confirmReactivate()"
                                        style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.4rem 1rem;border-radius:8px;border:none;background:#2563eb;color:white;font-size:0.75rem;font-weight:700;cursor:pointer;font-family:'Instrument Sans',sans-serif;box-shadow:0 2px 6px rgba(37,99,235,0.25);transition:all 0.15s;"
                                        onmouseover="this.style.background='#1d4ed8'"
                                        onmouseout="this.style.background='#2563eb'">
                                        <i class="fas fa-check" style="font-size:0.65rem;"></i> Yes, Reactivate
                                    </button>
                                    <button type="button" onclick="toggleReactivateSection()"
                                        style="font-size:0.72rem; color:#9ca3af; background:none; border:none; cursor:pointer; font-family:'Instrument Sans',sans-serif; padding:0;">
                                        <i class="fas fa-times" style="font-size:0.65rem;"></i> Cancel
                                    </button>
                                </div>
                            </div>
                            @endif

                            <div id="complete-section" style="display:{{ $project->status === 'completed' ? 'block' : 'none' }}; margin-top:0.875rem; padding:1rem; border-radius:10px; border:1.5px solid rgba(22,163,74,0.2); background:rgba(22,163,74,0.04);">
                                <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.75rem;">
                                    <i class="fas fa-calendar-check" style="color:#16a34a; font-size:0.8rem;"></i>
                                    <label class="field-label" style="margin:0; color:#16a34a;">Date Completed</label>
                                </div>
                                <input type="hidden" name="completed_at" id="completed_at_hidden"
                                    value="{{ old('completed_at', $project->completed_at?->format('Y-m-d')) }}">
                                <input type="date" id="completed_at_input" class="field-input"
                                    value="{{ old('completed_at', $project->completed_at?->format('Y-m-d')) }}"
                                    onchange="document.getElementById('completed_at_hidden').value = this.value"
                                    style="border-color:rgba(22,163,74,0.3);"
                                    onfocus="this.style.borderColor='#16a34a';this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.1)'"
                                    onblur="this.style.borderColor='rgba(22,163,74,0.3)';this.style.boxShadow='none'">
                                <p class="field-hint" style="margin-top:0.4rem; color:#16a34a;">
                                    <i class="fas fa-info-circle"></i> Saving with this date will mark the project as completed.
                                </p>
                                @if($project->status !== 'completed')
                                <button type="button" onclick="toggleCompleteSection()"
                                    style="margin-top:0.5rem; font-size:0.72rem; color:#9ca3af; background:none; border:none; cursor:pointer; font-family:'Instrument Sans',sans-serif; padding:0;">
                                    <i class="fas fa-times" style="font-size:0.65rem;"></i> Cancel
                                </button>
                                @endif
                            </div>

                            <input type="hidden" name="status" id="status_hidden" value="{{ $project->status }}">
                            <input type="hidden" name="status" value="{{ $project->status }}">

                            @if($project->status !== 'completed')
                                <p class="field-hint" style="margin-top:0.4rem;">Auto-determined from expiry date · override by marking complete</p>
                            @else
                                <p class="field-hint" style="margin-top:0.4rem; color:#16a34a;">
                                    Completed on {{ $project->completed_at?->format('F d, Y') ?? '—' }}
                                </p>
                            @endif
                        </div>
                    </div>{{-- closes grid-2col --}}
                </div>{{-- closes section-body --}}
            </div>{{-- closes form-card --}}
                {{-- CONTRACT DATES — move this INSIDE tab-overview, before its closing div --}}
            <div class="form-card" style="margin-bottom:1.5rem;">
                <div class="section-header">
                    <i class="fas fa-calendar-days"></i>
                    <span>Contract Dates</span>
                    <span style="margin-left:auto; font-size:0.7rem; color:#9ca3af; font-weight:400;">Key milestones</span>
                </div>
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
                            <input type="number" onkeydown="return event.key !== '-' && event.key !== 'e' " name="as_planned" id="as_planned" class="field-input"
                            value="{{ old('as_planned', $project->as_planned) }}"
                            min="0" max="100" step="0.001"
                            oninput="if(parseFloat(this.value)>100)this.value=100; computeSlippage()"
                            required style="padding-right:2.5rem;">
                        </div>
                        <div class="prog-bar-track"><div class="prog-bar-fill" id="ap_bar" style="background:var(--orange-500); width:{{ $project->as_planned }}%;"></div></div>
                        @error('as_planned')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                    <div class="field-group">
                        <label class="field-label">Work Done (%)</label>
                        <div style="position:relative;">
                            <input type="number" onkeydown="return event.key !== '-' && event.key !== 'e' " name="work_done" id="work_done" class="field-input"
                            value="{{ old('work_done', $project->work_done) }}"
                            min="0" max="100" step="0.001"
                            oninput="if(parseFloat(this.value)>100)this.value=100; computeSlippage()"
                            required style="padding-right:2.5rem;">
                        </div>
                        <div class="prog-bar-track"><div class="prog-bar-fill" id="wd_bar" style="background:#3b82f6; width:{{ $project->work_done }}%;"></div></div>
                        @error('work_done')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="field-group">
                    <label class="field-label">Schedule Slippage <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(automatic)</span></label>
                    <input type="hidden" name="slippage" id="slippage" value="{{ old('slippage', $project->slippage) }}">
                   <div id="slippage-display" style="display:flex; align-items:center; justify-content:space-between; padding:0.85rem 1rem; border:1.5px solid rgba(26,15,0,0.08); border-radius:9px; background:var(--bg-secondary); min-height:48px;">
                        <p id="slippage_label" style="font-size:0.85rem; font-weight:600; color:#9ca3af; margin:0;"><i class="fas fa-minus"></i> On schedule</p>
                        <span id="slippage-value" style="font-family:'Syne',sans-serif; font-size:1.25rem; font-weight:800; color:#9ca3af;">—</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- LIQUIDATED DAMAGES --}}
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
                            <input type="number" onkeydown="return event.key !== '-' && event.key !== 'e' " id="ld_accomplished" name="ld_accomplished" class="field-input"
                        value="{{ old('ld_accomplished', $project->ld_accomplished ?? '') }}"
                        min="0" max="100" step="0.001"
                        oninput="if(parseFloat(this.value)>100)this.value=100; calculateLDPerDay()"
                        style="padding-right:2.5rem;">
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
                    <label class="field-label">Days Overdue <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto)</span></label>
                    {{-- Hidden input submits the value to the controller --}}
                    <input type="hidden" id="ld_days_overdue_input" name="ld_days_overdue" value="{{ old('ld_days_overdue', $project->ld_days_overdue ?? 0) }}">
                    <div style="padding:0.75rem 1rem; border:1.5px solid var(--border); border-radius:9px; background:var(--bg-secondary);">
                        <p style="margin:0; display:flex; align-items:baseline; gap:0.4rem;">
                            <span id="ld_days_overdue_display" style="font-size:1.1rem; font-weight:800; font-family:'Syne',sans-serif; letter-spacing:-0.02em; color:var(--tx2);">—</span>
                            <span id="ld_days_unit" style="font-size:0.8rem; font-weight:600; color:var(--tx2);">calculating…</span>
                        </p>
                        <p style="font-size:0.68rem; color:#9ca3af; margin:3px 0 0;">
                            {{ $project->revised_contract_expiry
                                ? 'Revised Expiry: ' . $project->revised_contract_expiry->format('M d, Y')
                                : 'Original Expiry: ' . $project->original_contract_expiry->format('M d, Y') }}
                        </p>
                    </div>
                    <p class="field-hint" id="ld_overdue_hint">Auto-calculated from {{ $project->revised_contract_expiry ? 'revised' : 'original' }} contract expiry</p>
                </div>
                    <div class="field-group">
                        <label class="field-label">LD per Day (₱) <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto)</span></label>
                        <input type="hidden" id="ld_per_day" name="ld_per_day" value="{{ old('ld_per_day', $project->ld_per_day ?? '0') }}">
                        <p style="font-size:0.9rem;font-weight:700;color:var(--text-primary);margin:0;padding:0.1rem 0;font-family:'Syne',sans-serif;letter-spacing:-0.01em;">
                            ₱<span id="ld_per_day_display">{{ number_format((float) old('ld_per_day', $project->ld_per_day ?? 0), 2) }}</span>
                        </p>
                        <p class="field-hint">Formula: (Unworked % ÷ 100) × Original Contract Amount × 0.001</p>                    </div>
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

        {{-- TAB 3: EXTENSIONS --}}
        <div id="tab-extensions" class="tab-content">

        {{-- TIME EXTENSIONS ACCORDION --}}
        <div class="form-card" style="margin-bottom:1.5rem;">
            <div class="acc-header" id="acc-te-hdr" onclick="toggleAcc('te')" role="button" aria-expanded="true" aria-controls="acc-te-bdy">
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
            <div class="acc-body is-collapsed" id="acc-te-bdy">
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
                        <input type="number" onkeydown="return event.key !== '-' && event.key !== 'e' " name="new_te_days" id="new_te_days" class="field-input" min="1" step="1" placeholder="e.g. 60" oninput="updateTEPreview()">
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

        {{-- VARIATION ORDERS ACCORDION --}}
        <div class="form-card" style="margin-bottom:1.5rem; border-color:rgba(99,102,241,0.18);">
            <div class="acc-header acc-indigo" id="acc-vo-hdr" onclick="toggleAcc('vo')" role="button" aria-expanded="true" aria-controls="acc-vo-bdy" style="border-bottom-color:rgba(99,102,241,0.15);">
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
            <div class="acc-body is-collapsed" id="acc-vo-bdy">
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
                        <input type="number" onkeydown="return event.key !== '-' && event.key !== 'e'"  name="new_vo_days" id="new_vo_days" class="field-input" min="1" step="1" placeholder="e.g. 45" oninput="updateVOPreview()" style="border-color:rgba(99,102,241,0.25);">
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

        {{-- SUSPENSION ORDER ACCORDION --}}
        <div class="form-card" style="margin-bottom:1.5rem; border-color:rgba(234,179,8,0.2);">
            <div class="acc-header acc-yellow" id="acc-so-hdr" onclick="toggleAcc('so')" role="button" aria-expanded="true" aria-controls="acc-so-bdy" style="border-bottom-color:rgba(234,179,8,0.18);">
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
            <div class="acc-body is-collapsed" id="acc-so-bdy">
            <div class="acc-body-inner">
            <div class="section-body">
                <div class="grid-2col">
                    <div class="field-group">
                        <label class="field-label">{{ $hasSO ? 'Additional' : '' }} Suspension Days <span style="color:#ef4444;">*</span></label>
                        <input type="number" onkeydown="return event.key !== '-' && event.key !== 'e'" name="new_so_days" id="new_so_days" class="field-input" min="1" step="1" placeholder="e.g. 30" oninput="updateSOPreview()">
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

        {{-- BILLING UPDATE ACCORDION --}}
@php
    $billingAmounts = is_array($project->billing_amounts) ? array_map('floatval', $project->billing_amounts) : [];
    $billingDates   = is_array($project->billing_dates)   ? $project->billing_dates : [];
    $billingCount   = count($billingAmounts);
    $nextBillingNo  = $billingCount + 1;
    $totalBilled    = array_sum($billingAmounts);
    $allExtCosts  = array_merge(
    is_array($project->cost_involved ?? null) ? $project->cost_involved : [],
    is_array($project->vo_cost ?? null)       ? $project->vo_cost : []
    );
    $totalCostAdj  = collect($allExtCosts)->filter(fn($c) => $c !== null && (float)$c != 0)->sum();
    $adjustedContractAmt = max(0, (float)($project->original_contract_amount ?? $project->contract_amount) + $totalCostAdj);
    $remainingBal  = $adjustedContractAmt - $totalBilled;
@endphp
<div class="form-card" style="margin-bottom:1.5rem; border-color:rgba(34,197,94,0.18);">
    <div class="acc-header" id="acc-billing-hdr" onclick="toggleAcc('billing')"
         role="button" aria-expanded="true" aria-controls="acc-billing-bdy"
         style="border-bottom-color:rgba(34,197,94,0.15);">
        <i class="fas fa-file-invoice-dollar" style="color:#16a34a; font-size:0.85rem; flex-shrink:0;"></i>
        <span class="acc-title">Billing Updates</span>
        @if($billingCount > 0)
            <span class="tag-chip" style="margin-left:0.4rem; background:rgba(34,197,94,0.1); color:#16a34a; border-color:rgba(34,197,94,0.25);">{{ $billingCount }} recorded</span>
        @else
            <span style="font-size:0.7rem; color:#9ca3af; margin-left:0.4rem; font-weight:400;">No entries yet</span>
        @endif
        <div class="acc-status-dot" style="background:{{ $billingCount > 0 ? '#16a34a' : '#d1d5db' }};"></div>
        <div class="acc-chevron" style="border-color:rgba(34,197,94,0.2);"><i class="fas fa-chevron-down"></i></div>
    </div>
    <div class="acc-body is-collapsed" id="acc-billing-bdy">
    <div class="acc-body-inner">
    <div class="section-body">

        @if($billingCount > 0)
        <div style="margin-bottom:1.5rem;">
            <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#9ca3af; margin-bottom:0.75rem; display:flex; align-items:center; gap:0.5rem;">
                <i class="fas fa-history" style="color:#16a34a;"></i> Billing History
            </p>
            {{-- Column headers --}}
            <div style="display:grid; grid-template-columns:18px 1fr 5rem 5rem; gap:0 0.75rem; padding:0 0.25rem; margin-bottom:0.4rem;">
                <span></span>
                <p class="h-col-hdr">Billing</p>
                <p class="h-col-hdr" style="text-align:center;">Action</p>
                <p class="h-col-hdr" style="text-align:right;">Amount</p>
            </div>
            <div class="history-timeline">
                @foreach($billingAmounts as $bi => $amount)
                @php $bIsLast = $bi === $billingCount - 1; @endphp
                <div style="display:grid; grid-template-columns:18px 1fr 5rem 5rem; gap:0 0.75rem; align-items:start;">
                    <div class="h-spine">
                        <div class="h-dot" style="background:#16a34a; box-shadow:0 0 0 3px rgba(34,197,94,0.18);"></div>
                        @if(!$bIsLast)<div class="h-line" style="background:rgba(34,197,94,0.18);"></div>@endif
                    </div>
                    <div class="h-label {{ $bIsLast ? 'last' : '' }}" style="display:flex; flex-direction:column; gap:0.2rem;">
                        <span>Billing No. {{ $bi + 1 }}</span>
                        @if(!empty($billingDates[$bi]))
                            <span style="font-size:0.72rem; font-weight:500; color:#9ca3af;">{{ \Carbon\Carbon::parse($billingDates[$bi])->format('M d, Y') }}</span>
                        @endif
                    </div>
                    <div style="display:flex; justify-content:center; padding-top:2px;">
                        <button type="button"
                            onclick="openBillingEditModal({{ $bi }}, {{ $amount }}, '{{ $billingDates[$bi] ?? '' }}')"
                            style="display:inline-flex; align-items:center; gap:0.3rem; padding:3px 10px; border-radius:6px; border:1.5px solid rgba(34,197,94,0.25); background:rgba(34,197,94,0.06); color:#16a34a; font-size:0.68rem; font-weight:700; cursor:pointer; font-family:'Instrument Sans',sans-serif; transition:all 0.15s; white-space:nowrap;"
                            onmouseover="this.style.background='rgba(34,197,94,0.15)';this.style.borderColor='rgba(34,197,94,0.45)'"
                            onmouseout="this.style.background='rgba(34,197,94,0.06)';this.style.borderColor='rgba(34,197,94,0.25)'">
                            <i class="fas fa-pen" style="font-size:0.55rem;"></i> Edit
                        </button>
                    </div>
                    <div style="display:flex; justify-content:flex-end; padding-top:2px;">
                        <span style="font-size:0.82rem; font-weight:700; color:#16a34a;">₱{{ number_format($amount, 2) }}</span>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Total billed summary --}}
            <div class="h-summary" style="margin-top:1rem; border-color:rgba(34,197,94,0.2);">
                <span style="font-size:0.8rem; font-weight:600; color:var(--ink-muted); display:flex; align-items:center; gap:0.4rem;">
                    <i class="fas fa-sigma" style="color:#16a34a; font-size:0.75rem;"></i>
                    Total across {{ $billingCount }} {{ $billingCount === 1 ? 'billing' : 'billings' }}
                </span>
                <span style="font-family:'Syne',sans-serif; font-size:1.2rem; font-weight:800; color:#16a34a;">₱{{ number_format($totalBilled, 2) }}</span>
            </div>

            {{-- Remaining balance --}}
            <div style="margin-top:0.75rem; padding:0.875rem 1rem; border-radius:10px; background:{{ $remainingBal >= 0 ? 'rgba(59,130,246,0.04)' : 'rgba(239,68,68,0.04)' }}; border:1px solid {{ $remainingBal >= 0 ? 'rgba(59,130,246,0.18)' : 'rgba(239,68,68,0.18)' }}; display:flex; align-items:center; justify-content:space-between;">
                <span style="font-size:0.8rem; font-weight:600; color:var(--ink-muted); display:flex; align-items:center; gap:0.4rem;">
                    <i class="fas fa-wallet" style="color:{{ $remainingBal >= 0 ? '#3b82f6' : '#dc2626' }};"></i>
                    Remaining Balance
                    <span style="font-size:0.68rem; color:#9ca3af; font-weight:400;">(Contract − Total Billed)</span>

                <span style="font-family:'Syne',sans-serif; font-size:1.2rem; font-weight:800; color:{{ $remainingBal >= 0 ? '#3b82f6' : '#dc2626' }};">
                    ₱{{ number_format($remainingBal, 2) }}
                </span>
            </div>
        </div>
        <div style="border-top:1px dashed rgba(34,197,94,0.2); margin-bottom:1.5rem;"></div>
        @endif

        {{-- Add new billing --}}
        <p style="font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:#9ca3af; margin-bottom:0.875rem; display:flex; align-items:center; gap:0.5rem;">
            <i class="fas fa-plus-circle" style="color:#16a34a;"></i>
            Add Billing No. {{ $nextBillingNo }}
        </p>
        <div class="grid-2col">
            <div class="field-group">
                <label class="field-label">Billing Amount (₱) <span style="color:#ef4444;">*</span></label>
                <div style="position:relative;">
                    <span style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-weight:600; pointer-events:none;">₱</span>
                    <input type="number" onkeydown="return event.key !== '-' && event.key !== 'e'" name="new_billing_amount" id="new_billing_amount" class="field-input"
                           min="0" step="0.01" placeholder="0.00"
                           style="padding-left:1.75rem; border-color:rgba(34,197,94,0.25);"
                           oninput="updateBillingPreview()">
                </div>
                <p class="field-hint">Amount billed in this update</p>
            </div>
            <div class="field-group">
                <label class="field-label">Billing Date</label>
                <input type="date" name="new_billing_date" class="field-input"
                       style="border-color:rgba(34,197,94,0.25);">
                <p class="field-hint">Date of this billing update</p>
            </div>

            {{-- Live preview --}}
            <div class="field-group" style="grid-column:1/-1;">
                <label class="field-label">After This Billing (Preview)</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem;">
                    <div style="padding:0.875rem 1rem; border:1.5px solid rgba(34,197,94,0.2); border-radius:9px; background:rgba(34,197,94,0.04);">
                        <p style="font-size:0.62rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#9ca3af; margin-bottom:0.3rem;">Total Amount Billed</p>
                        <p style="font-family:'Syne',sans-serif; font-size:1rem; font-weight:800; color:#16a34a; margin:0;">
                            ₱<span id="billing_total_preview" data-base="{{ $totalBilled }}">{{ number_format($totalBilled, 2) }}</span>
                        </p>
                    </div>
                    <div style="padding:0.875rem 1rem; border:1.5px solid rgba(59,130,246,0.2); border-radius:9px; background:rgba(59,130,246,0.04);">
                        <p style="font-size:0.62rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#9ca3af; margin-bottom:0.3rem;">Remaining Balance</p>
                        <p id="billing_remaining_preview" style="font-family:'Syne',sans-serif; font-size:1rem; font-weight:800; color:#3b82f6; margin:0;">
                            ₱<span id="billing_remaining_val">{{ number_format($remainingBal, 2) }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
    </div>
</div>
        </div>

        {{-- end tab-extensions --}}

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
                <button type="button" class="add-row-btn" onclick="addIssuanceRow()">
                    <i class="fas fa-plus"></i> Add Notification
                </button>

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

{{-- PHP-rendered runtime variables required by edit.js --}}
{{-- ✅ These MUST be in @push so they render in the same stack, before edit.js --}}
@push('scripts')
<script>
    const originalExpiry = '{{ $project->original_contract_expiry->format("Y-m-d") }}';
    const revisedExpiry  = '{{ $project->revised_contract_expiry?->format("Y-m-d") ?? '' }}';
    const existingTEDays = {{ (int) collect($project->extension_days ?? [])->filter(fn($v) => is_numeric($v))->sum() }};
    const existingVODays = {{ (int) collect($project->vo_days ?? [])->filter(fn($v) => is_numeric($v))->sum() }};
    const existingSODays = {{ (int) ($project->suspension_days ?? 0) }};
    {{-- ✅ FIX: Added missing 'Performance Bond' to match the PHP $issuanceOptions array --}}
    const ISSUANCE_OPTS  = [
        '1st Notice of Negative Slippage',
        '2nd Notice of Negative Slippage',
        '3rd Notice of Negative Slippage',
        'Liquidated Damages',
        'Notice to Terminate',
        'Notice of Expiry',
        'Performance Bond'
    ];
    const _totalTECount  = {{ $teCount }};
    const _totalVOCount  = {{ $voCount }};
</script>
@vite('resources/js/admin/projects/edit.js')
@endpush

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
                    <input type="number" onkeydown="return event.key !== '-' && event.key !== 'e'" id="edit_days" name="edit_days" min="1" step="1" required
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
                    <input type="number" onkeydown="return event.key !== '-' && event.key !== 'e'" id="edit_cost" name="edit_cost" min="-9999999999" step="0.01" placeholder="0.00"
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

{{-- Modal-specific JS (uses blade-rendered routes & tokens — cannot move to edit.js) --}}
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
{{-- BILLING EDIT MODAL --}}
<x-modal id="billing-edit-modal" title="Edit Billing Entry" type="default" icon="fa-pen" size="md">
    <div style="display:flex; flex-direction:column; gap:1.1rem;">
        <div>
            <label style="display:block; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); margin-bottom:0.4rem;">Entry</label>
            <div id="billing-edit-label" style="padding:0.65rem 1rem; border-radius:9px; background:var(--bg-secondary); border:1.5px solid var(--border); font-size:0.875rem; font-weight:700; color:var(--text-primary);">—</div>
        </div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
            <div>
                <label style="display:block; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); margin-bottom:0.4rem;">
                    Billing Amount (₱) <span style="color:#ef4444;">*</span>
                </label>
                <div style="position:relative;">
                    <span style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-weight:600; pointer-events:none;">₱</span>
                    <input type="number" onkeydown="return event.key !== '-' && event.key !== 'e'" id="billing-edit-amount" min="0" step="0.01" placeholder="0.00"
                           style="width:100%; padding:0.72rem 1rem 0.72rem 1.75rem; border:1.5px solid rgba(34,197,94,0.3); border-radius:9px; font-size:0.875rem; color:var(--text-primary); background:var(--bg-primary); outline:none; font-family:'Instrument Sans',sans-serif; box-sizing:border-box; transition:border-color 0.2s, box-shadow 0.2s;"
                           onfocus="this.style.borderColor='#16a34a';this.style.boxShadow='0 0 0 3px rgba(34,197,94,0.1)'"
                           onblur="this.style.borderColor='rgba(34,197,94,0.3)';this.style.boxShadow='none'">
                </div>
                <p style="font-size:0.68rem; color:#9ca3af; margin-top:0.3rem;">Required</p>
            </div>
            <div>
                <label style="display:block; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); margin-bottom:0.4rem;">Billing Date</label>
                <input type="date" id="billing-edit-date"
                       style="width:100%; padding:0.72rem 1rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; color:var(--text-primary); background:var(--bg-primary); outline:none; font-family:'Instrument Sans',sans-serif; box-sizing:border-box; transition:border-color 0.2s, box-shadow 0.2s;"
                       onfocus="this.style.borderColor='#16a34a';this.style.boxShadow='0 0 0 3px rgba(34,197,94,0.1)'"
                       onblur="this.style.borderColor='var(--border)';this.style.boxShadow='none'">
                <p style="font-size:0.68rem; color:#9ca3af; margin-top:0.3rem;">Optional</p>
            </div>
        </div>
        <p id="billing-edit-error" style="display:none; font-size:0.75rem; color:#ef4444; align-items:center; gap:0.3rem;">
            <i class="fas fa-exclamation-circle"></i> Please enter a valid billing amount.
        </p>
    </div>
    <x-slot name="footer">
        <button type="button" onclick="closeModal('billing-edit-modal')"
            style="padding:0.6rem 1.2rem; border:1.5px solid var(--border); border-radius:9px; background:var(--bg-primary); color:var(--text-secondary); font-weight:600; font-size:0.85rem; cursor:pointer; font-family:'Instrument Sans',sans-serif; transition:all 0.15s;"
            onmouseover="this.style.borderColor='#16a34a'" onmouseout="this.style.borderColor='var(--border)'">Cancel</button>
        <button type="button" id="billing-edit-save-btn" onclick="submitBillingEdit()"
            style="padding:0.6rem 1.4rem; background:#16a34a; color:white; border:none; border-radius:9px; font-weight:700; font-size:0.85rem; cursor:pointer; font-family:'Instrument Sans',sans-serif; box-shadow:0 2px 8px rgba(34,197,94,0.3); transition:all 0.15s;"
            onmouseover="this.style.background='#15803d'" onmouseout="this.style.background='#16a34a'">
            <i class="fas fa-save" style="margin-right:0.35rem; font-size:0.75rem;"></i> Save Changes
        </button>
    </x-slot>
</x-modal>

<script>
window._billingEditIndex = 0;

window.openBillingEditModal = function (index, amount, date) {
    window._billingEditIndex = index;
    document.getElementById('billing-edit-label').textContent  = 'Billing No. ' + (index + 1);
    document.getElementById('billing-edit-amount').value       = amount || '';
    document.getElementById('billing-edit-date').value         = date   || '';
    document.getElementById('billing-edit-error').style.display = 'none';
    openModal('billing-edit-modal');
};

window.submitBillingEdit = function () {
    const amount = document.getElementById('billing-edit-amount').value;
    if (!amount || parseFloat(amount) < 0) {
        document.getElementById('billing-edit-error').style.display = 'flex';
        document.getElementById('billing-edit-amount').focus();
        return;
    }
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.projects.updateBilling", $project) }}';
    const fields = {
        '_token':         '{{ csrf_token() }}',
        '_method':        'PATCH',
        'billing_index':  window._billingEditIndex,
        'billing_amount': amount,
        'billing_date':   document.getElementById('billing-edit-date').value,
    };
    Object.entries(fields).forEach(([name, value]) => {
        const input = document.createElement('input');
        input.type = 'hidden'; input.name = name; input.value = value;
        form.appendChild(input);
    });
    document.body.appendChild(form);
    form.submit();
};
</script>
@push('scripts')
    @vite('resources/js/admin/projects/edit.js')
@endpush

{{-- Dedicated reactivation form — separate from the main edit form --}}
@if($project->status === 'completed')
<form id="reactivate-form" method="POST" action="{{ route('admin.projects.reactivate', $project) }}" style="display:none;">
    @csrf
    @method('PATCH')
</form>
@endif
    </x-app-layout>