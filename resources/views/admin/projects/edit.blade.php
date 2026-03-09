<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; letter-spacing:-0.03em; color:var(--text-primary); display:flex; align-items:center; gap:0.6rem;">
                <span style="background:#f97316; width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(249,115,22,0.35);">
                    <i class="fas fa-edit" style="color:white; font-size:0.85rem;"></i>
                </span>
                Edit Project
            </h2>
            <p style="color:var(--text-secondary); font-size:0.82rem; margin-top:3px;">
                Editing: <span style="font-weight:700; color:#f97316;">{{ $project->project_title }}</span>
            </p>
        </div>
        <div style="display:flex; gap:0.6rem; align-items:center;">
            <a href="{{ route('admin.projects.show', $project) }}"
               style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.6rem 1rem; border:1.5px solid var(--border); border-radius:9px; font-weight:600; font-size:0.825rem; color:var(--text-secondary); text-decoration:none; background:var(--bg-secondary); transition:all 0.2s;">
                <i class="fas fa-eye"></i> View
            </a>
            <a href="{{ route('admin.projects.index') }}"
               style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.6rem 1rem; border:1.5px solid var(--border); border-radius:9px; font-weight:600; font-size:0.825rem; color:var(--text-secondary); text-decoration:none; background:var(--bg-secondary); transition:all 0.2s;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <button id="themeToggle" type="button" aria-label="Toggle dark mode" style="
                background:var(--bg-secondary); border:1.5px solid var(--border); border-radius:10px;
                padding:0.5rem 0.95rem; cursor:pointer; display:flex; align-items:center; gap:0.5rem;
                color:var(--text-primary); font-size:0.9rem; font-weight:500; font-family:'Instrument Sans',sans-serif;
                box-shadow:0 2px 8px rgba(0,0,0,0.05); transition:all 0.3s ease;"
               onmouseover="this.style.background='rgba(249,115,22,0.12)';this.style.borderColor='rgba(249,115,22,0.4)'"
               onmouseout="this.style.background='var(--bg-secondary)';this.style.borderColor='var(--border)'"
               onclick="toggleTheme()">
                <i class="fas" id="themeIcon" style="color:#f97316;"></i>
                <span id="themeLabel">Light</span>
            </button>
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
    }
    .dark, html.dark {
        --bg-primary:#0f0f0f; --bg-secondary:#1a1a1a;
        --text-primary:#f5f5f0; --text-secondary:#9ca3af;
        --ink:#f5f5f0; --ink-muted:#9ca3af;
        --border:rgba(249,115,22,0.25);
    }
    body { color:var(--text-primary); transition:background 0.3s,color 0.3s; }
    .form-card { background:var(--bg-primary); border:1px solid var(--border); border-radius:14px; overflow:hidden; }
    .section-header { padding:1rem 1.5rem; border-bottom:1px solid var(--border); background:var(--bg-secondary); display:flex; align-items:center; gap:0.5rem; }
    .section-header span { font-family:'Syne',sans-serif; font-weight:700; font-size:0.875rem; color:var(--ink); }
    .section-body { padding:1.5rem; }
    .field-label { display:block; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); margin-bottom:0.45rem; }
    .field-input { width:100%; padding:0.7rem 1rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.875rem; color:var(--text-primary); background:var(--bg-primary); outline:none; font-family:'Instrument Sans',sans-serif; transition:border-color 0.2s,box-shadow 0.2s; }
    .field-input:focus { border-color:var(--orange-500); box-shadow:0 0 0 3px rgba(249,115,22,0.1); }
    .readonly-field { background:var(--bg-secondary) !important; cursor:not-allowed; color:var(--ink-muted); }
    .field-error { font-size:0.775rem; color:#ef4444; margin-top:0.35rem; display:flex; align-items:center; gap:0.3rem; }
    .prog-bar-track { height:4px; background:rgba(249,115,22,0.1); border-radius:99px; margin-top:0.5rem; overflow:hidden; }
    .prog-bar-fill  { height:100%; border-radius:99px; transition:width 0.4s ease; }
    .last-updated { background:rgba(249,115,22,0.06); border:1px solid rgba(249,115,22,0.18); border-radius:10px; padding:0.65rem 1rem; display:flex; align-items:center; gap:0.6rem; margin-bottom:1.25rem; }
    .dynamic-row { display:flex; align-items:center; gap:0.5rem; }
    .dynamic-select { flex:1; min-width:0; padding:0.65rem 2.2rem 0.65rem 0.9rem; border:1.5px solid var(--border); border-radius:9px; font-size:0.855rem; color:var(--text-primary); background:var(--bg-primary); outline:none; font-family:'Instrument Sans',sans-serif; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-position:right 0.6rem center; background-repeat:no-repeat; background-size:1.1em; cursor:pointer; transition:border-color 0.2s,box-shadow 0.2s; }
    .dynamic-select:focus { border-color:var(--orange-500); box-shadow:0 0 0 3px rgba(249,115,22,0.1); }
    .remove-btn { width:32px; height:32px; border-radius:8px; border:1.5px solid #fecaca; background:#fef2f2; color:#dc2626; cursor:pointer; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:0.72rem; transition:all 0.18s; }
    .remove-btn:hover { background:#dc2626; color:white; border-color:#dc2626; }
    .add-row-btn { margin-top:0.65rem; display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 0.9rem; border:1.5px dashed rgba(249,115,22,0.35); border-radius:8px; font-size:0.775rem; font-weight:600; color:var(--orange-600); background:rgba(249,115,22,0.04); cursor:pointer; transition:all 0.2s; font-family:'Instrument Sans',sans-serif; }
    .add-row-btn:hover { border-color:var(--orange-500); background:rgba(249,115,22,0.09); }
    .tag-chip { display:inline-flex; align-items:center; padding:2px 10px; border-radius:99px; font-size:0.68rem; font-weight:700; background:rgba(249,115,22,0.1); color:var(--orange-600); border:1px solid rgba(249,115,22,0.2); min-width:2.5rem; text-align:center; justify-content:center; }
    .feature-btn { transition:all 0.2s ease; }
    .feature-btn:hover { border-color:rgba(249,115,22,0.5) !important; background:rgba(249,115,22,0.12) !important; }
    .feature-btn-active { border-color:var(--orange-500) !important; background:rgba(249,115,22,0.13) !important; color:var(--orange-600) !important; }
    .feature-section { animation:slideIn 0.3s ease-out; }
    @keyframes slideIn { from{opacity:0;transform:translateY(-6px);} to{opacity:1;transform:translateY(0);} }
    @keyframes fadeUp  { from{opacity:0;transform:translateY(14px);} to{opacity:1;transform:translateY(0);} }
    .fade-up { animation:fadeUp 0.45s ease both; }

    /* TE history chips */
    .te-chip { display:inline-flex; align-items:center; gap:0.5rem; padding:0.45rem 0.875rem; border-radius:99px; background:rgba(249,115,22,0.08); border:1.5px solid rgba(249,115,22,0.2); font-size:0.775rem; font-weight:700; color:var(--orange-600); }
    .te-chip .te-days { font-weight:400; color:var(--ink-muted); font-size:0.72rem; }
    .te-chip .te-cost { font-weight:600; color:#16a34a; font-size:0.72rem; }
</style>

<div class="max-w-4xl mx-auto fade-up">

    @if(session('success'))
    <div style="background:rgba(22,163,74,0.08); border:1px solid rgba(22,163,74,0.25); border-radius:10px; padding:0.75rem 1rem; margin-bottom:1rem; display:flex; align-items:center; gap:0.6rem;">
        <i class="fas fa-check-circle" style="color:#16a34a;"></i>
        <span style="font-size:0.85rem; font-weight:600; color:#15803d;">{{ session('success') }}</span>
    </div>
    @endif

    <div class="last-updated">
        <i class="fas fa-clock" style="color:var(--orange-500); font-size:0.85rem;"></i>
        <p style="font-size:0.825rem; color:#92400e;">
            Last updated: <span style="font-weight:700;">{{ $project->updated_at->format('F d, Y \a\t h:i A') }}</span>
        </p>
    </div>

    <form method="POST" action="{{ route('admin.projects.update', $project) }}">
        @csrf
        @method('PATCH')

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.25rem;">

            {{-- ═══ Project Information ═══ --}}
            <div class="form-card" style="grid-column:1/-1;">
                <div class="section-header">
                    <i class="fas fa-circle-info" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Project Information</span>
                </div>
                <div class="section-body" style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                    <div>
                        <label class="field-label">In Charge</label>
                        <input type="text" name="in_charge" class="field-input readonly-field" readonly
                            value="{{ old('in_charge', $project->in_charge) }}">
                    </div>
                    <div>
                        <label class="field-label">Project Title</label>
                        <input type="text" name="project_title" class="field-input readonly-field" readonly
                            value="{{ old('project_title', $project->project_title) }}">
                    </div>
                    <div>
                        <label class="field-label">Location</label>
                        <input type="text" name="location" class="field-input readonly-field" readonly
                            value="{{ old('location', $project->location) }}">
                    </div>
                    <div>
                        <label class="field-label">Contractor</label>
                        <input type="text" name="contractor" class="field-input readonly-field" readonly
                            value="{{ old('contractor', $project->contractor) }}">
                    </div>
                    <div>
                        <label class="field-label">Contract Amount</label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.85rem; font-weight:600; pointer-events:none;">₱</span>
                            <input type="number" name="contract_amount" id="contract_amount" class="field-input readonly-field" readonly
                                style="padding-left:1.75rem;"
                                value="{{ old('contract_amount', $project->contract_amount) }}" min="0" step="0.01">
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Status</label>
                        <select name="status" class="field-input" id="status_sel" onchange="toggleCompletedAt()"
                            style="appearance:none; background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b4f35%22 stroke-width=%222%22%3E%3Cpath d=%22M6 9l6 6 6-6%22/%3E%3C/svg%3E'); background-position:right 0.6rem center; background-repeat:no-repeat; background-size:1.1em; padding-right:2rem; cursor:pointer;">
                            <option value="ongoing"   {{ old('status',$project->status)=='ongoing'   ? 'selected':'' }}>Ongoing</option>
                            <option value="completed" {{ old('status',$project->status)=='completed' ? 'selected':'' }}>Completed</option>
                            <option value="expired"   {{ old('status',$project->status)=='expired'   ? 'selected':'' }}>Expired</option>
                        </select>
                    </div>
                    <div id="completed_at_field" class="{{ old('status',$project->status)=='completed' ? '' : 'hidden' }}" style="grid-column:1/-1;">
                        <label class="field-label">Date Completed</label>
                        <input type="date" name="completed_at" class="field-input {{ $errors->has('completed_at') ? 'has-error':'' }}"
                            value="{{ old('completed_at', $project->completed_at?->format('Y-m-d')) }}">
                        @error('completed_at')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- ═══ Contract Dates ═══ --}}
            <div class="form-card">
                <div class="section-header">
                    <i class="fas fa-calendar-days" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Contract Dates</span>
                </div>
                <div class="section-body" style="display:flex; flex-direction:column; gap:1.1rem;">
                    <div>
                        <label class="field-label">Date Started</label>
                        <input type="date" name="date_started" class="field-input readonly-field" readonly
                            value="{{ old('date_started', $project->date_started->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label class="field-label">Original Expiry</label>
                        <input type="date" name="original_contract_expiry" id="original_contract_expiry"
                            class="field-input readonly-field" readonly
                            value="{{ old('original_contract_expiry', $project->original_contract_expiry->format('Y-m-d')) }}">
                    </div>
                    <div>
                        <label class="field-label" style="display:flex; align-items:center; justify-content:space-between;">
                            <span>Revised Expiry</span>
                            <span id="revised-preview-pill" style="display:none; align-items:center; gap:0.3rem; padding:2px 9px; border-radius:99px; font-size:0.68rem; font-weight:700; background:rgba(34,197,94,0.1); color:#16a34a; border:1px solid rgba(34,197,94,0.25);">
                                <i class="fas fa-calculator" style="font-size:0.6rem;"></i>
                                <span id="revised-preview-text"></span>
                            </span>
                        </label>
                        <input type="date" name="revised_contract_expiry" id="revised_contract_expiry"
                            class="field-input readonly-field" readonly
                            value="{{ old('revised_contract_expiry', $project->revised_contract_expiry?->format('Y-m-d')) }}">
                    </div>
                </div>
            </div>

            {{-- ═══ Progress ═══ --}}
            <div class="form-card">
                <div class="section-header">
                    <i class="fas fa-chart-bar" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Progress</span>
                </div>
                <div class="section-body" style="display:flex; flex-direction:column; gap:1.1rem;">
                    <div>
                        <label class="field-label">As Planned (%)</label>
                        <div style="position:relative;">
                            <input type="number" name="as_planned" id="as_planned" class="field-input"
                                style="padding-right:2.5rem;"
                                value="{{ old('as_planned', $project->as_planned) }}" min="0" max="100" step="0.01"
                                oninput="computeSlippage()" required>
                            <span style="position:absolute; right:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.8rem; font-weight:600;">%</span>
                        </div>
                        <div class="prog-bar-track"><div class="prog-bar-fill" id="ap_bar" style="background:var(--orange-500); width:{{ $project->as_planned }}%;"></div></div>
                        @error('as_planned')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">Work Done (%)</label>
                        <div style="position:relative;">
                            <input type="number" name="work_done" id="work_done" class="field-input"
                                style="padding-right:2.5rem;"
                                value="{{ old('work_done', $project->work_done) }}" min="0" max="100" step="0.01"
                                oninput="computeSlippage()" required>
                            <span style="position:absolute; right:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.8rem; font-weight:600;">%</span>
                        </div>
                        <div class="prog-bar-track"><div class="prog-bar-fill" id="wd_bar" style="background:#3b82f6; width:{{ $project->work_done }}%;"></div></div>
                        @error('work_done')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">Slippage <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto)</span></label>
                        <input type="hidden" name="slippage" id="slippage" value="{{ old('slippage', $project->slippage) }}">
                        <div id="slippage-display" style="display:flex; align-items:center; justify-content:space-between; padding:0.75rem 1rem; border:1.5px solid rgba(26,15,0,0.08); border-radius:9px; background:var(--bg-secondary); min-height:42px;">
                            <p id="slippage_label" style="font-size:0.825rem; font-weight:600; color:#9ca3af;"><i class="fas fa-minus"></i> On schedule</p>
                            <span id="slippage-value" style="font-family:'Syne',sans-serif; font-size:1.15rem; font-weight:800; color:#9ca3af;">—</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ Issuances ═══ --}}
            @php
                $issuanceOptions = ['1st Notice of Negative Slippage','2nd Notice of Negative Slippage','3rd Notice of Negative Slippage','Liquidated Damages','Notice to Terminate','Notice of Expiry'];
                $savedIssuances  = old('issuances', $project->issuances ?? []);
                if (is_string($savedIssuances)) $savedIssuances = json_decode($savedIssuances, true) ?? [];
                if (empty($savedIssuances)) $savedIssuances = [''];
            @endphp
            <div class="form-card" style="grid-column:1/-1;">
                <div class="section-header">
                    <i class="fas fa-paper-plane" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Issuances</span>
                </div>
                <div class="section-body">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem; padding-bottom:0.875rem; border-bottom:1px solid var(--border);">
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <div style="width:36px; height:36px; background:rgba(249,115,22,0.1); border-radius:9px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-bell" style="color:var(--orange-500); font-size:0.85rem;"></i>
                            </div>
                            <div>
                                <p style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--ink-muted);">Contractor Notifications</p>
                                <p style="font-size:0.7rem; color:#9ca3af; margin-top:3px;">Notices sent to contractor</p>
                            </div>
                        </div>
                        <span class="tag-chip" id="issuance-count">0</span>
                    </div>
                    <div id="issuances-list" style="display:flex; flex-direction:column; gap:0.6rem;">
                        @foreach($savedIssuances as $val)
                        <div class="dynamic-row">
                            <select name="issuances[]" class="dynamic-select"
                                onchange="updateCount('issuances-list','issuance-count')">
                                <option value="">— Select Issuance —</option>
                                @foreach($issuanceOptions as $opt)
                                    <option value="{{ $opt }}" {{ $val===$opt ? 'selected':'' }}>{{ $opt }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="remove-btn" onclick="removeIssuanceRow(this)"><i class="fas fa-times"></i></button>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="add-row-btn" onclick="addIssuanceRow()" style="margin-top:0.875rem;">
                        <i class="fas fa-plus"></i> Add Issuance
                    </button>
                </div>
            </div>

            {{-- ═══ Remarks ═══ --}}
            <div class="form-card" style="grid-column:1/-1;">
                <div class="section-header">
                    <i class="fas fa-comment-dots" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Remarks / Recommendation <span style="font-weight:400; font-family:'Instrument Sans',sans-serif; letter-spacing:0; font-size:0.78rem; color:#9ca3af;">(optional)</span></span>
                </div>
                <div class="section-body">
                    <textarea name="remarks_recommendation" rows="4" class="field-input" style="resize:none;"
                        placeholder="Enter any remarks or recommendations…">{{ old('remarks_recommendation', $project->remarks_recommendation) }}</textarea>
                </div>
            </div>

            {{-- ═══ TE / SO Count Scoreboard ═══ --}}
            <div style="grid-column:1/-1; display:grid; grid-template-columns:1fr 1fr; gap:1rem;">

                {{-- Time Extension Count --}}
                <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; background:rgba(249,115,22,0.05); border:1.5px solid rgba(249,115,22,0.18); border-radius:12px;">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div style="width:36px; height:36px; background:rgba(249,115,22,0.12); border-radius:9px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-clock" style="color:#f97316; font-size:0.9rem;"></i>
                        </div>
                        <div>
                            <p style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted);">Time Extensions Applied</p>
                            @if($teCount > 0)
                                <p style="font-size:0.7rem; color:#9ca3af; margin-top:2px;">
                                    {{ collect($project->extension_days ?? [])->sum() }} total days
                                </p>
                            @else
                                <p style="font-size:0.7rem; color:#9ca3af; margin-top:2px;">None applied yet</p>
                            @endif
                        </div>
                    </div>
                    <span style="font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; color:#f97316; line-height:1;">{{ $teCount }}</span>
                </div>

                {{-- Suspension Order Count --}}
                <div style="display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; background:rgba(234,179,8,0.05); border:1.5px solid rgba(234,179,8,0.2); border-radius:12px;">
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div style="width:36px; height:36px; background:rgba(234,179,8,0.12); border-radius:9px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-pause-circle" style="color:#d97706; font-size:0.9rem;"></i>
                        </div>
                        <div>
                            <p style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted);">Suspension Orders Applied</p>
                            @if($hasSO)
                                <p style="font-size:0.7rem; color:#9ca3af; margin-top:2px;">{{ $project->suspension_days }} total days</p>
                            @else
                                <p style="font-size:0.7rem; color:#9ca3af; margin-top:2px;">None applied yet</p>
                            @endif
                        </div>
                    </div>
                    <span style="font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; color:#d97706; line-height:1;">{{ $soCount }}</span>
                </div>

            </div>

            {{-- TE History (read-only chips) --}}
            @if($teCount > 0)
            <div class="form-card" style="grid-column:1/-1;">
                <div class="section-header">
                    <i class="fas fa-history" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Time Extension History</span>
                    <span style="margin-left:auto; font-size:0.72rem; color:#9ca3af; font-weight:400; font-family:'Instrument Sans',sans-serif;">Read-only — saved records</span>
                </div>
                <div class="section-body" style="display:flex; flex-wrap:wrap; gap:0.6rem;">
                    @foreach($teHistory as $te)
                    <div class="te-chip">
                        <i class="fas fa-clock" style="font-size:0.7rem; opacity:0.6;"></i>
                        <span>{{ $te['label'] }}</span>
                        <span class="te-days">· {{ $te['days'] }} days</span>
                        @if($te['cost'])
                            <span class="te-cost">· ₱{{ number_format($te['cost'], 2) }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- SO History --}}
            @if($hasSO)
            <div style="grid-column:1/-1; padding:0.75rem 1rem; border:1.5px solid rgba(234,179,8,0.25); border-radius:10px; background:rgba(234,179,8,0.04); display:flex; align-items:center; gap:0.75rem;">
                <i class="fas fa-pause-circle" style="color:#d97706; font-size:1rem;"></i>
                <div>
                    <p style="font-size:0.78rem; font-weight:700; color:#92400e;">Suspension Order Active</p>
                    <p style="font-size:0.72rem; color:#9ca3af; margin-top:1px;">{{ $project->suspension_days }} days total suspended · Revised expiry extended accordingly</p>
                </div>
            </div>
            @endif

            {{-- ═══ Additional Features ═══ --}}
            <div class="form-card" style="grid-column:1/-1;">
                <div class="section-header">
                    <i class="fas fa-cog" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Additional Features</span>
                    <span style="margin-left:auto; font-size:0.72rem; color:#9ca3af; font-weight:400; font-family:'Instrument Sans',sans-serif;">Select one to expand</span>
                </div>
                <div style="padding:1.25rem;">
                    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:0.875rem;">
                        <button type="button" class="feature-btn feature-btn-active" id="btn-liquidated-damages"
                            onclick="toggleFeature('liquidated-damages')" style="padding:1rem; border:2px solid rgba(249,115,22,0.3); border-radius:10px; background:rgba(249,115,22,0.08); cursor:pointer; font-weight:600; font-size:0.875rem; color:var(--orange-600); display:flex; align-items:center; gap:0.6rem; font-family:'Instrument Sans',sans-serif;">
                            <i class="fas fa-calculator"></i> Liquidated Damages
                        </button>
                        <button type="button" class="feature-btn" id="btn-time-extension"
                            onclick="toggleFeature('time-extension')" style="padding:1rem; border:2px solid rgba(249,115,22,0.15); border-radius:10px; background:transparent; cursor:pointer; font-weight:600; font-size:0.875rem; color:var(--text-secondary); display:flex; align-items:center; gap:0.6rem; font-family:'Instrument Sans',sans-serif;">
                            <i class="fas fa-clock"></i>
                            Add Time Extension
                            @if($teCount > 0)
                                <span style="margin-left:auto; background:rgba(249,115,22,0.12); color:#ea580c; font-size:0.68rem; padding:1px 8px; border-radius:99px; font-weight:700;">TE {{ $teCount }}</span>
                            @endif
                        </button>
                        <button type="button" class="feature-btn" id="btn-suspension-order"
                            onclick="toggleFeature('suspension-order')" style="padding:1rem; border:2px solid rgba(249,115,22,0.15); border-radius:10px; background:transparent; cursor:pointer; font-weight:600; font-size:0.875rem; color:var(--text-secondary); display:flex; align-items:center; gap:0.6rem; font-family:'Instrument Sans',sans-serif;">
                            <i class="fas fa-pause-circle"></i>
                            {{ $hasSO ? 'Add SO Days' : 'Suspension Order' }}
                            @if($hasSO)
                                <span style="margin-left:auto; background:rgba(234,179,8,0.12); color:#b45309; font-size:0.68rem; padding:1px 8px; border-radius:99px; font-weight:700;">{{ $project->suspension_days }}d</span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── Liquidated Damages ── --}}
            <div class="form-card feature-section" id="section-liquidated-damages" style="grid-column:1/-1; display:grid;">
                <div class="section-header">
                    <i class="fas fa-calculator" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Liquidated Damages</span>
                </div>
                <div class="section-body" style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                    <div>
                        <label class="field-label">Accomplished (%)</label>
                        <div style="position:relative;">
                            <input type="number" id="ld_accomplished" name="ld_accomplished" class="field-input"
                                value="{{ old('ld_accomplished', $project->ld_accomplished ?? '') }}"
                                min="0" max="100" step="0.01" oninput="calculateLDPerDay()" style="padding-right:2.5rem;">
                            <span style="position:absolute; right:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.8rem; font-weight:600;">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Unworked (%) <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto)</span></label>
                        <div style="position:relative;">
                            <input type="number" id="ld_unworked" name="ld_unworked" class="field-input readonly-field" readonly
                                value="{{ old('ld_unworked', $project->ld_unworked ?? '') }}" style="padding-right:2.5rem;">
                            <span style="position:absolute; right:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.8rem; font-weight:600;">%</span>
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Days Overdue</label>
                        <input type="number" id="ld_days_overdue_input" name="ld_days_overdue" class="field-input"
                            value="{{ old('ld_days_overdue', $project->ld_days_overdue ?? '') }}"
                            min="0" step="1" oninput="calculateLDTotal()" placeholder="e.g. 30">
                        <p style="font-size:0.72rem; color:#9ca3af; margin-top:0.35rem;">Number of overdue days</p>
                    </div>
                    <div>
                        <label class="field-label">LD per Day (₱) <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto)</span></label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.85rem; font-weight:600; pointer-events:none;">₱</span>
                            <input type="number" id="ld_per_day" name="ld_per_day" class="field-input readonly-field" readonly
                                value="{{ old('ld_per_day', $project->ld_per_day ?? '0.00') }}" step="0.01" style="padding-left:1.75rem;">
                        </div>
                        <p style="font-size:0.72rem; color:#9ca3af; margin-top:0.35rem; font-style:italic;">(Accomplished ÷ 100) × Contract Amount × 0.001</p>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="field-label">Total LD (₱) <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto)</span></label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:0.875rem; top:50%; transform:translateY(-50%); color:#dc2626; font-size:0.85rem; font-weight:800; pointer-events:none;">₱</span>
                            <input type="number" id="total_ld" name="total_ld" class="field-input readonly-field" readonly
                                value="{{ old('total_ld', $project->total_ld ?? '0.00') }}" step="0.01"
                                style="padding-left:1.75rem; background:rgba(239,68,68,0.05); border-color:rgba(239,68,68,0.2); color:#dc2626; font-weight:700;">
                        </div>
                        <p style="font-size:0.72rem; color:#9ca3af; margin-top:0.35rem; font-style:italic;">LD per Day × Days Overdue</p>
                    </div>
                </div>
            </div>

            {{-- ── Add Time Extension ── --}}
            <div class="form-card feature-section" id="section-time-extension" style="grid-column:1/-1; display:none;">
                <div class="section-header">
                    <i class="fas fa-clock" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Add Time Extension</span>
                </div>
                <div class="section-body" style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                    <div>
                        <label class="field-label">Time Extension Number <span style="color:#ef4444;">*</span></label>
                        <select name="new_te_number" id="new_te_number" class="field-input"
                            style="appearance:none; background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b4f35%22 stroke-width=%222%22%3E%3Cpath d=%22M6 9l6 6 6-6%22/%3E%3C/svg%3E'); background-position:right 0.6rem center; background-repeat:no-repeat; background-size:1.1em; padding-right:2rem; cursor:pointer;">
                            <option value="">— Select Time Extension —</option>
                            @for($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}">Time Extension {{ $i }}</option>
                            @endfor
                        </select>
                        <p style="font-size:0.72rem; color:#9ca3af; margin-top:0.35rem;">Select the extension count being applied</p>
                    </div>
                    <div>
                        <label class="field-label">Extension Days <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="new_te_days" id="new_te_days" class="field-input"
                            min="1" step="1" placeholder="e.g. 60" oninput="updateTEPreview()">
                        <p style="font-size:0.72rem; color:#9ca3af; margin-top:0.35rem;">Days to extend the deadline</p>
                    </div>
                    <div>
                        <label class="field-label">Cost Involved (₱) <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(optional)</span></label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.85rem; font-weight:600; pointer-events:none;">₱</span>
                            <input type="number" name="new_te_cost" class="field-input" min="0" step="0.01" placeholder="0.00" style="padding-left:1.75rem;">
                        </div>
                        <p style="font-size:0.72rem; color:#9ca3af; margin-top:0.35rem;">Additional cost for this extension</p>
                    </div>
                    <div style="grid-column:1/-1;">
                        <label class="field-label">New Revised Expiry (Preview)</label>
                        <div style="padding:0.875rem 1rem; border:1.5px solid rgba(249,115,22,0.2); border-radius:9px; background:rgba(249,115,22,0.04); display:flex; align-items:center; gap:0.6rem;">
                            <i class="fas fa-calendar-check" style="color:var(--orange-500); font-size:0.9rem;"></i>
                            <span id="te_revised_preview" style="font-weight:600; color:var(--text-primary);">Enter days above to preview</span>
                        </div>
                        @if($teCount > 0 || $hasSO)
                        <p style="font-size:0.72rem; color:#9ca3af; margin-top:0.35rem;">
                            Original expiry
                            @if($teCount > 0) + existing {{ collect($project->extension_days ?? [])->sum() }} TE days @endif
                            @if($hasSO) + {{ $project->suspension_days }} SO days @endif
                            + new days
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Suspension Order ── --}}
            <div class="form-card feature-section" id="section-suspension-order" style="grid-column:1/-1; display:none;">
                <div class="section-header">
                    <i class="fas fa-pause-circle" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>{{ $hasSO ? 'Add More Suspension Days' : 'Suspension Order' }}</span>
                    @if($hasSO)
                    <span style="margin-left:auto; font-size:0.72rem; color:#9ca3af; font-family:'Instrument Sans',sans-serif; font-weight:400;">
                        Currently <strong style="color:#d97706;">{{ $project->suspension_days }} days</strong> suspended
                    </span>
                    @endif
                </div>
                <div class="section-body" style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                    <div>
                        <label class="field-label">{{ $hasSO ? 'Additional' : '' }} Suspension Days <span style="color:#ef4444;">*</span></label>
                        <input type="number" name="new_so_days" id="new_so_days" class="field-input"
                            min="1" step="1" placeholder="e.g. 30" oninput="updateSOPreview()">
                        <p style="font-size:0.72rem; color:#9ca3af; margin-top:0.35rem;">
                            {{ $hasSO ? 'Added to existing ' . $project->suspension_days . ' days' : 'Days that work was suspended' }}
                        </p>
                    </div>
                    <div>
                        <label class="field-label">New Revised Expiry (Preview)</label>
                        <div style="padding:0.875rem 1rem; border:1.5px solid rgba(234,179,8,0.25); border-radius:9px; background:rgba(234,179,8,0.04); display:flex; align-items:center; gap:0.6rem; min-height:46px;">
                            <i class="fas fa-calendar-check" style="color:#d97706; font-size:0.9rem;"></i>
                            <span id="so_revised_preview" style="font-weight:600; color:var(--text-primary);">Enter days to preview</span>
                        </div>
                    </div>
                    <div style="grid-column:1/-1; padding:0.875rem 1rem; border-left:3px solid #d97706; background:rgba(234,179,8,0.04); border-radius:6px;">
                        <p style="font-size:0.8rem; color:var(--text-primary); margin:0;">
                            <i class="fas fa-info-circle" style="color:#d97706; margin-right:0.4rem;"></i>
                            {{ $hasSO ? 'New suspension days are added on top of existing days. Revised expiry is extended accordingly.' : 'Suspension Order extends only the Revised Expiry date, not the original contract expiry.' }}
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Actions --}}
        <div style="display:flex; gap:0.875rem;">
            <button type="submit"
                style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.75rem 1.75rem; background:var(--orange-500); color:white; font-weight:700; font-size:0.9rem; border-radius:10px; border:none; cursor:pointer; box-shadow:0 3px 14px rgba(249,115,22,0.38); font-family:'Instrument Sans',sans-serif; transition:all 0.2s;"
                onmouseover="this.style.background='#ea580c';this.style.transform='translateY(-1px)'"
                onmouseout="this.style.background='#f97316';this.style.transform='translateY(0)'">
                <i class="fas fa-save"></i> Save Changes
            </button>
            <a href="{{ route('admin.projects.index') }}"
               style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.75rem 1.5rem; border:1.5px solid var(--border); border-radius:10px; font-weight:600; font-size:0.875rem; color:var(--ink-muted); text-decoration:none; background:var(--bg-primary); transition:all 0.2s;">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
// ── Theme ──
function initTheme() {
    const saved = localStorage.getItem('theme-mode');
    if (saved === 'dark') { document.documentElement.classList.add('dark'); document.body.classList.add('dark'); }
    updateThemeButton(document.documentElement.classList.contains('dark') ? 'dark' : 'light');
}
function updateThemeButton(theme) {
    document.getElementById('themeIcon').className  = 'fas ' + (theme === 'dark' ? 'fa-moon' : 'fa-sun');
    document.getElementById('themeLabel').textContent = theme === 'dark' ? 'Dark' : 'Light';
}
function toggleTheme() {
    const isDark = document.documentElement.classList.toggle('dark');
    document.body.classList.toggle('dark', isDark);
    localStorage.setItem('theme-mode', isDark ? 'dark' : 'light');
    updateThemeButton(isDark ? 'dark' : 'light');
}
initTheme();

// ── Slippage ──
function toggleCompletedAt() {
    document.getElementById('completed_at_field').classList.toggle('hidden', document.getElementById('status_sel').value !== 'completed');
}
function computeSlippage() {
    const ap = parseFloat(document.getElementById('as_planned').value);
    const wd = parseFloat(document.getElementById('work_done').value);
    document.getElementById('ap_bar').style.width = Math.min(ap || 0, 100) + '%';
    document.getElementById('wd_bar').style.width = Math.min(wd || 0, 100) + '%';
    const lbl = document.getElementById('slippage_label');
    const valEl = document.getElementById('slippage-value');
    const display = document.getElementById('slippage-display');
    if (isNaN(ap) || isNaN(wd)) { valEl.textContent = '—'; return; }
    const sl = (wd - ap).toFixed(2);
    document.getElementById('slippage').value = sl;
    if (+sl > 0) { lbl.style.color='#16a34a'; lbl.innerHTML='<i class="fas fa-arrow-up"></i> Ahead'; valEl.style.color='#16a34a'; }
    else if (+sl < 0) { lbl.style.color='#dc2626'; lbl.innerHTML='<i class="fas fa-arrow-down"></i> Behind'; valEl.style.color='#dc2626'; }
    else { lbl.style.color='#9ca3af'; lbl.innerHTML='<i class="fas fa-minus"></i> On schedule'; valEl.style.color='#9ca3af'; }
    valEl.textContent = (+sl > 0 ? '+' : '') + sl + '%';
}

// ── Feature Toggle ──
function toggleFeature(name) {
    document.querySelectorAll('.feature-section').forEach(s => s.style.display = 'none');
    document.querySelectorAll('.feature-btn').forEach(b => {
        b.classList.remove('feature-btn-active');
        b.style.borderColor = 'rgba(249,115,22,0.15)';
        b.style.background  = 'transparent';
        b.style.color       = 'var(--text-secondary)';
    });
    const sec = document.getElementById('section-' + name);
    if (sec) sec.style.display = 'grid';
    const btn = document.getElementById('btn-' + name);
    if (btn) { btn.classList.add('feature-btn-active'); btn.style.borderColor='rgba(249,115,22,0.3)'; btn.style.background='rgba(249,115,22,0.08)'; btn.style.color='var(--orange-600)'; }
}

// ── LD ──
function calculateLDPerDay() {
    const acc    = parseFloat(document.getElementById('ld_accomplished').value) || 0;
    const amt    = parseFloat(document.getElementById('contract_amount').value) || 0;
    document.getElementById('ld_unworked').value = Math.max(0, 100 - acc).toFixed(2);
    document.getElementById('ld_per_day').value  = ((acc / 100) * amt * 0.001).toFixed(2);
    calculateLDTotal();
}
function calculateLDTotal() {
    const perDay   = parseFloat(document.getElementById('ld_per_day').value) || 0;
    const overdue  = parseFloat(document.getElementById('ld_days_overdue_input').value) || 0;
    document.getElementById('total_ld').value = (perDay * overdue).toFixed(2);
}

// ── TE Preview ──
const originalExpiry = '{{ $project->original_contract_expiry->format("Y-m-d") }}';
const existingTEDays = {{ (int) collect($project->extension_days ?? [])->sum() }};
const existingSODays = {{ (int) ($project->suspension_days ?? 0) }};

function updateTEPreview() {
    const newDays = parseInt(document.getElementById('new_te_days').value) || 0;
    const preview = document.getElementById('te_revised_preview');
    if (newDays < 1) { preview.textContent = 'Enter days above to preview'; return; }
    const total = existingTEDays + existingSODays + newDays;
    const d = new Date(originalExpiry + 'T00:00:00');
    d.setDate(d.getDate() + total);
    preview.textContent = d.toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric' });
}

// ── SO Preview ──
function updateSOPreview() {
    const newDays = parseInt(document.getElementById('new_so_days').value) || 0;
    const preview = document.getElementById('so_revised_preview');
    if (newDays < 1) { preview.textContent = 'Enter days to preview'; return; }
    const total = existingTEDays + existingSODays + newDays;
    const d = new Date(originalExpiry + 'T00:00:00');
    d.setDate(d.getDate() + total);
    preview.textContent = d.toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric' });
}

// ── Issuances ──
const ISSUANCE_OPTS = ['1st Notice of Negative Slippage','2nd Notice of Negative Slippage','3rd Notice of Negative Slippage','Liquidated Damages','Notice to Terminate','Notice of Expiry'];
function issuanceRowHTML(val = '') {
    let opts = '<option value="">— Select Issuance —</option>';
    ISSUANCE_OPTS.forEach(o => opts += `<option value="${o}" ${o===val?'selected':''}>${o}</option>`);
    return `<div class="dynamic-row"><select name="issuances[]" class="dynamic-select" onchange="updateCount('issuances-list','issuance-count')">${opts}</select><button type="button" class="remove-btn" onclick="removeIssuanceRow(this)"><i class="fas fa-times"></i></button></div>`;
}
function addIssuanceRow() {
    const list = document.getElementById('issuances-list');
    list.insertAdjacentHTML('beforeend', issuanceRowHTML());
    updateCount('issuances-list', 'issuance-count');
}
function removeIssuanceRow(btn) {
    const list = document.getElementById('issuances-list');
    if (list.querySelectorAll('.dynamic-row').length <= 1) { list.querySelector('select').value = ''; updateCount('issuances-list','issuance-count'); return; }
    btn.closest('.dynamic-row').remove();
    updateCount('issuances-list', 'issuance-count');
}
function updateCount(listId, countId) {
    const filled = [...document.getElementById(listId).querySelectorAll('select')].filter(s => s.value !== '').length;
    const chip   = document.getElementById(countId);
    chip.textContent = filled;
    chip.style.background  = filled > 0 ? 'rgba(249,115,22,0.15)' : 'rgba(249,115,22,0.07)';
    chip.style.color       = filled > 0 ? '#ea580c' : '#9ca3af';
    chip.style.borderColor = filled > 0 ? 'rgba(249,115,22,0.3)' : 'rgba(249,115,22,0.15)';
}

document.addEventListener('DOMContentLoaded', () => {
    computeSlippage();
    updateCount('issuances-list', 'issuance-count');
    calculateLDPerDay();
});
</script>
</x-app-layout>