<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; letter-spacing:-0.03em; color:#1a0f00; display:flex; align-items:center; gap:0.6rem;">
                <span style="background:#f97316; width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(249,115,22,0.35);">
                    <i class="fas fa-edit" style="color:white; font-size:0.85rem;"></i>
                </span>
                Edit Project
            </h2>
            <p style="color:#6b4f35; font-size:0.82rem; margin-top:3px;">
                Editing: <span style="font-weight:700; color:#f97316;">{{ $project->project_title }}</span>
            </p>
        </div>
        <div style="display:flex; gap:0.6rem;">
            <a href="{{ route('admin.projects.show', $project) }}"
               style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.6rem 1rem; border:1.5px solid rgba(26,15,0,0.1); border-radius:9px; font-weight:600; font-size:0.825rem; color:#6b4f35; text-decoration:none; background:white; transition:all 0.2s;">
                <i class="fas fa-eye"></i> View
            </a>
            <a href="{{ route('admin.projects.index') }}"
               style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.6rem 1rem; border:1.5px solid rgba(26,15,0,0.1); border-radius:9px; font-weight:600; font-size:0.825rem; color:#6b4f35; text-decoration:none; background:white; transition:all 0.2s;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
</x-slot>

<style>
    :root {
        --orange-500: #f97316;
        --orange-600: #ea580c;
        --ink:        #1a0f00;
        --ink-muted:  #6b4f35;
        --border:     rgba(249,115,22,0.14);
    }
    .form-card { background:white; border:1px solid var(--border); border-radius:14px; overflow:hidden; }
    .section-header { padding:1rem 1.5rem; border-bottom:1px solid var(--border); background:#fffaf5; display:flex; align-items:center; gap:0.5rem; }
    .section-header span { font-family:'Syne',sans-serif; font-weight:700; font-size:0.875rem; color:var(--ink); }
    .section-body { padding:1.5rem; }
    .field-label { display:block; font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); margin-bottom:0.45rem; }
    .field-input { width:100%; padding:0.7rem 1rem; border:1.5px solid rgba(26,15,0,0.1); border-radius:9px; font-size:0.875rem; color:var(--ink); background:white; outline:none; font-family:'Instrument Sans',sans-serif; transition:border-color 0.2s, box-shadow 0.2s; }
    .field-input:focus { border-color:var(--orange-500); box-shadow:0 0 0 3px rgba(249,115,22,0.1); }
    .field-input.readonly-field { background:#fffaf5; cursor:not-allowed; color:var(--ink-muted); }
    .field-input.has-error { border-color:#ef4444; }
    .field-error { font-size:0.775rem; color:#ef4444; margin-top:0.35rem; display:flex; align-items:center; gap:0.3rem; }
    .prog-bar-track { height:4px; background:rgba(249,115,22,0.1); border-radius:99px; margin-top:0.5rem; overflow:hidden; }
    .prog-bar-fill  { height:100%; border-radius:99px; transition:width 0.4s ease; }
    @keyframes fadeUp { from{opacity:0;transform:translateY(14px);} to{opacity:1;transform:translateY(0);} }
    .fade-up { animation:fadeUp 0.45s ease both; }
    .last-updated { background:rgba(249,115,22,0.06); border:1px solid rgba(249,115,22,0.18); border-radius:10px; padding:0.65rem 1rem; display:flex; align-items:center; gap:0.6rem; margin-bottom:1.25rem; }

    @keyframes rowIn { from{opacity:0;transform:translateX(-6px);} to{opacity:1;transform:translateX(0);} }
    .dynamic-row { display:flex; align-items:center; gap:0.5rem; animation:rowIn 0.18s ease both; }

    .dynamic-select {
        flex:1; min-width:0;
        padding:0.65rem 2.2rem 0.65rem 0.9rem;
        border:1.5px solid rgba(26,15,0,0.1); border-radius:9px;
        font-size:0.855rem; color:var(--ink); background:white; outline:none;
        font-family:'Instrument Sans',sans-serif; appearance:none;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236b4f35' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
        background-position:right 0.6rem center; background-repeat:no-repeat; background-size:1.1em;
        cursor:pointer; transition:border-color 0.2s, box-shadow 0.2s;
    }
    .dynamic-select:focus { border-color:var(--orange-500); box-shadow:0 0 0 3px rgba(249,115,22,0.1); }

    /* Days input */
    .days-wrap { position:relative; flex-shrink:0; width:96px; }
    .days-input {
        width:100%; padding:0.65rem 1.8rem 0.65rem 0.75rem;
        border:1.5px solid rgba(26,15,0,0.1); border-radius:9px;
        font-size:0.855rem; color:var(--ink); background:white; outline:none;
        font-family:'Instrument Sans',sans-serif; text-align:center;
        transition:border-color 0.2s, box-shadow 0.2s;
    }
    .days-input:focus { border-color:var(--orange-500); box-shadow:0 0 0 3px rgba(249,115,22,0.1); }
    .days-input:disabled { background:#f3f4f6; color:#9ca3af; cursor:not-allowed; border-color:rgba(26,15,0,0.06); }
    .days-wrap .days-lbl {
        position:absolute; right:0.5rem; top:50%; transform:translateY(-50%);
        font-size:0.62rem; font-weight:700; text-transform:uppercase;
        color:#9ca3af; pointer-events:none; letter-spacing:0.04em;
    }

    .remove-btn {
        width:32px; height:32px; border-radius:8px;
        border:1.5px solid #fecaca; background:#fef2f2; color:#dc2626;
        cursor:pointer; display:flex; align-items:center; justify-content:center;
        flex-shrink:0; font-size:0.72rem; transition:all 0.18s;
    }
    .remove-btn:hover { background:#dc2626; color:white; border-color:#dc2626; }
    .add-row-btn {
        margin-top:0.65rem; display:inline-flex; align-items:center; gap:0.4rem;
        padding:0.45rem 0.9rem; border:1.5px dashed rgba(249,115,22,0.35);
        border-radius:8px; font-size:0.775rem; font-weight:600;
        color:var(--orange-600); background:rgba(249,115,22,0.04);
        cursor:pointer; transition:all 0.2s; font-family:'Instrument Sans',sans-serif;
    }
    .add-row-btn:hover { border-color:var(--orange-500); background:rgba(249,115,22,0.09); }
    .tag-chip {
        display:inline-flex; align-items:center; padding:2px 10px; border-radius:99px;
        font-size:0.68rem; font-weight:700; background:rgba(249,115,22,0.1);
        color:var(--orange-600); border:1px solid rgba(249,115,22,0.2);
        min-width:2.5rem; text-align:center; justify-content:center;
    }
    .col-divider { width:1px; background:var(--border); margin:0 0.25rem; align-self:stretch; }

    /* Total days summary */
    .days-summary {
        display:flex; align-items:center; justify-content:space-between;
        margin-top:0.875rem; padding:0.6rem 0.875rem; border-radius:9px;
        background:rgba(249,115,22,0.05); border:1px solid rgba(249,115,22,0.14);
    }
    .days-summary-num { font-family:'Syne',sans-serif; font-size:1.2rem; font-weight:800; color:var(--orange-600); }
</style>

<div class="max-w-4xl mx-auto fade-up">

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

            {{-- Project Information --}}
            <div class="form-card" style="grid-column:1/-1;">
                <div class="section-header">
                    <i class="fas fa-circle-info" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Project Information</span>
                </div>
                <div class="section-body" style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                    <div>
                        <label class="field-label">In Charge</label>
                        <input type="text" name="in_charge" class="field-input readonly-field"
                            value="{{ old('in_charge', $project->in_charge) }}" readonly>
                    </div>
                    <div>
                        <label class="field-label">Project Title</label>
                        <input type="text" name="project_title" class="field-input readonly-field"
                            value="{{ old('project_title', $project->project_title) }}" readonly>
                    </div>
                    <div>
                        <label class="field-label">Location</label>
                        <input type="text" name="location" class="field-input readonly-field"
                            value="{{ old('location', $project->location) }}" readonly>
                    </div>
                    <div>
                        <label class="field-label">Contractor</label>
                        <input type="text" name="contractor" class="field-input readonly-field"
                            value="{{ old('contractor', $project->contractor) }}" readonly>
                    </div>
                    <div>
                        <label class="field-label">Contract Amount</label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.85rem; font-weight:600; pointer-events:none;">₱</span>
                            <input type="number" name="contract_amount" class="field-input readonly-field" style="padding-left:1.75rem;"
                                value="{{ old('contract_amount', $project->contract_amount) }}" min="0" step="0.01" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="field-label">Status</label>
                        <select name="status" class="field-input" id="status_sel" onchange="toggleCompletedAt()"
                            style="appearance:none; background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b4f35%22 stroke-width=%222%22%3E%3Cpath d=%22M6 9l6 6 6-6%22/%3E%3C/svg%3E'); background-position:right 0.6rem center; background-repeat:no-repeat; background-size:1.1em; padding-right:2rem; cursor:pointer;">
                            <option value="ongoing"   {{ old('status', $project->status) == 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div id="completed_at_field" class="{{ old('status', $project->status) == 'completed' ? '' : 'hidden' }}" style="grid-column:1/-1;">
                        <label class="field-label">Date Completed</label>
                        <input type="date" name="completed_at" class="field-input {{ $errors->has('completed_at') ? 'has-error' : '' }}"
                            value="{{ old('completed_at', $project->completed_at ? $project->completed_at->format('Y-m-d') : '') }}">
                        @error('completed_at')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Contract Dates --}}
            <div class="form-card">
                <div class="section-header">
                    <i class="fas fa-calendar-days" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Contract Dates</span>
                </div>
                <div class="section-body" style="display:flex; flex-direction:column; gap:1.1rem;">
                    <div>
                        <label class="field-label">Date Started</label>
                        <input type="date" name="date_started" class="field-input readonly-field"
                            value="{{ old('date_started', $project->date_started->format('Y-m-d')) }}" readonly>
                    </div>
                    <div>
                        <label class="field-label">Original Expiry</label>
                        <input type="date" name="original_contract_expiry" id="original_contract_expiry" class="field-input readonly-field"
                            value="{{ old('original_contract_expiry', $project->original_contract_expiry->format('Y-m-d')) }}" readonly>
                    </div>
                    <div>
                        <label class="field-label" style="display:flex; align-items:center; justify-content:space-between;">
                            <span>Revised Expiry <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;"></span></span>
                            <span id="revised-preview-pill" style="display:none; align-items:center; gap:0.3rem; padding:2px 9px; border-radius:99px; font-size:0.68rem; font-weight:700; background:rgba(34,197,94,0.1); color:#16a34a; border:1px solid rgba(34,197,94,0.25);">
                                <i class="fas fa-calculator" style="font-size:0.6rem;"></i>
                                <span id="revised-preview-text"></span>
                            </span>
                        </label>
                        <input type="date" name="revised_contract_expiry" id="revised_contract_expiry"
                            class="field-input readonly-field"
                            value="{{ old('revised_contract_expiry', $project->revised_contract_expiry ? $project->revised_contract_expiry->format('Y-m-d') : '') }}"
                            readonly>
                            @error('revised_contract_expiry')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Progress --}}
            <div class="form-card">
                <div class="section-header">
                    <i class="fas fa-chart-bar" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Progress</span>
                </div>
                <div class="section-body" style="display:flex; flex-direction:column; gap:1.1rem;">
                    <div>
                        <label class="field-label">As Planned (%)</label>
                        <div style="position:relative;">
                            <input type="number" name="as_planned" id="as_planned" class="field-input {{ $errors->has('as_planned') ? 'has-error' : '' }}"
                                style="padding-right:2.5rem;"
                                value="{{ old('as_planned', $project->as_planned) }}" min="0" max="100" step="0.01" oninput="computeSlippage()" required>
                            <span style="position:absolute; right:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.8rem; font-weight:600;">%</span>
                        </div>
                        <div class="prog-bar-track"><div class="prog-bar-fill" id="ap_bar" style="background:var(--orange-500); width:{{ $project->as_planned }}%;"></div></div>
                        @error('as_planned')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">Work Done (%)</label>
                        <div style="position:relative;">
                            <input type="number" name="work_done" id="work_done" class="field-input {{ $errors->has('work_done') ? 'has-error' : '' }}"
                                style="padding-right:2.5rem;"
                                value="{{ old('work_done', $project->work_done) }}" min="0" max="100" step="0.01" oninput="computeSlippage()" required>
                            <span style="position:absolute; right:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.8rem; font-weight:600;">%</span>
                        </div>
                        <div class="prog-bar-track"><div class="prog-bar-fill" id="wd_bar" style="background:#3b82f6; width:{{ $project->work_done }}%;"></div></div>
                        @error('work_done')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">Slippage <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto)</span></label>
                        <div style="position:relative;">
                            <input type="number" name="slippage" id="slippage" class="field-input"
                                style="padding-right:2.5rem; background:#fffaf5; cursor:not-allowed;"
                                value="{{ old('slippage', $project->slippage) }}" readonly>
                            <span style="position:absolute; right:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.8rem; font-weight:600;">%</span>
                        </div>
                        <p id="slippage_label" style="font-size:0.75rem; margin-top:0.4rem; font-weight:600;"></p>
                    </div>
                </div>
            </div>

            {{-- ═══ Issuances & Documents Pressed ═══ --}}
            @php
                $issuanceOptions = [
                    '1st Notice of Negative Slippage',
                    '2nd Notice of Negative Slippage',
                    '3rd Notice of Negative Slippage',
                    'Liquidated Damages',
                    'Notice to Terminate',
                    'Notice of Expiry',
                ];
                $documentOptions = [
                    'Time Extension 1','Time Extension 2','Time Extension 3',
                    'Time Extension 4','Time Extension 5',
                    'Variation Order 1','Variation Order 2',
                    'Suspension Order',
                ];
                $savedIssuances = old('issuances', $project->issuances ?? []);
                $savedDocuments = old('documents_pressed', $project->documents_pressed ?? []);
                $savedDays      = old('extension_days', $project->extension_days ?? []);
                if (is_string($savedIssuances)) $savedIssuances = json_decode($savedIssuances, true) ?? [];
                if (is_string($savedDocuments)) $savedDocuments = json_decode($savedDocuments, true) ?? [];
                if (is_string($savedDays))      $savedDays      = json_decode($savedDays, true) ?? [];
                if (empty($savedIssuances)) $savedIssuances = [''];
                if (empty($savedDocuments)) { $savedDocuments = ['']; $savedDays = [0]; }
            @endphp

            <div class="form-card" style="grid-column:1/-1;">
                <div class="section-header">
                    <i class="fas fa-file-signature" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Issuances &amp; Documents Pressed</span>
                </div>
                <div class="section-body" style="display:grid; grid-template-columns:1fr 1px 1fr; gap:0 2rem;">

                    {{-- LEFT: Issuances --}}
                    <div>
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.875rem; padding-bottom:0.75rem; border-bottom:1px solid var(--border);">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div style="width:28px; height:28px; background:rgba(249,115,22,0.1); border-radius:7px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-paper-plane" style="color:var(--orange-500); font-size:0.72rem;"></i>
                                </div>
                                <div>
                                    <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); line-height:1;">Issuance</p>
                                    <p style="font-size:0.68rem; color:#9ca3af; margin-top:1px;">Notices sent to contractor</p>
                                </div>
                            </div>
                            <span class="tag-chip" id="issuance-count">0</span>
                        </div>

                        <div id="issuances-list" style="display:flex; flex-direction:column; gap:0.5rem;">
                            @foreach($savedIssuances as $val)
                            <div class="dynamic-row">
                                <select name="issuances[]" class="dynamic-select"
                                    onchange="updateCount('issuances-list','issuance-count')">
                                    <option value="">— Select Issuance —</option>
                                    @foreach($issuanceOptions as $opt)
                                        <option value="{{ $opt }}" {{ $val === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="remove-btn"
                                    onclick="removeIssuanceRow(this)" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- end LEFT --}}

                    {{-- Divider --}}
                    <div class="col-divider"></div>

                    {{-- RIGHT: Documents Pressed + Days --}}
                    <div>
                        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.875rem; padding-bottom:0.75rem; border-bottom:1px solid var(--border);">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div style="width:28px; height:28px; background:rgba(249,115,22,0.1); border-radius:7px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-file-alt" style="color:var(--orange-500); font-size:0.72rem;"></i>
                                </div>
                                <div>
                                    <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--ink-muted); line-height:1;">Documents Pressed</p>
                                    <p style="font-size:0.68rem; color:#9ca3af; margin-top:1px;">Extensions &amp; orders filed</p>
                                </div>
                            </div>
                            <span class="tag-chip" id="documents-count">0</span>
                        </div>

                        {{-- Column sub-labels --}}
                        <div style="display:grid; grid-template-columns:1fr 96px 32px; gap:0.5rem; padding:0 0; margin-bottom:0.3rem;">
                            <p style="font-size:0.63rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9ca3af; padding-left:0.1rem;">Document</p>
                            <p style="font-size:0.63rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#9ca3af; text-align:center;">Days Ext.</p>
                            <span></span>
                        </div>

                        <div id="documents-list" style="display:flex; flex-direction:column; gap:0.5rem;">
                            @foreach($savedDocuments as $i => $val)
                            @php $dayVal = $savedDays[$i] ?? 0; $isTE = str_starts_with($val ?? '', 'Time Extension'); @endphp
                            <div class="dynamic-row">
                                <select name="documents_pressed[]" class="dynamic-select"
                                    onchange="onDocumentChange(this)">
                                    <option value="">— Select Document —</option>
                                    @foreach($documentOptions as $opt)
                                        <option value="{{ $opt }}" {{ $val === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                    @endforeach
                                </select>
                                <div class="days-wrap">
                                    <input type="number" name="extension_days[]" class="days-input"
                                        value="{{ $dayVal }}" min="0" placeholder="0"
                                        {{ !$isTE ? 'disabled' : '' }}
                                        oninput="recomputeRevisedExpiry()">
                                    <span class="days-lbl">days</span>
                                </div>
                                <button type="button" class="remove-btn"
                                    onclick="removeDocumentRow(this)" title="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            @endforeach
                        </div>
                        {{-- Total days summary --}}
                        <div class="days-summary" id="days-summary" style="display:none;">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <i class="fas fa-sigma" style="color:var(--orange-500); font-size:0.8rem;"></i>
                                <span style="font-size:0.8rem; font-weight:600; color:var(--ink-muted);">Total days extended</span>
                            </div>
                            <span class="days-summary-num" id="total-days-num">0</span>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Remarks --}}
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
               style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.75rem 1.5rem; border:1.5px solid rgba(26,15,0,0.1); border-radius:10px; font-weight:600; font-size:0.875rem; color:var(--ink-muted); text-decoration:none; background:white; transition:all 0.2s;">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
    /* ── Slippage ── */
function toggleCompletedAt() {
        document.getElementById('completed_at_field').classList.toggle('hidden', document.getElementById('status_sel').value !== 'completed');
    }
function computeSlippage() {
        const ap = parseFloat(document.getElementById('as_planned').value) || 0;
        const wd = parseFloat(document.getElementById('work_done').value) || 0;
        const sl = (wd - ap).toFixed(2);
        document.getElementById('slippage').value = sl;
        document.getElementById('ap_bar').style.width = Math.min(ap, 100) + '%';
        document.getElementById('wd_bar').style.width = Math.min(wd, 100) + '%';
        const lbl = document.getElementById('slippage_label');
        if      (sl > 0) { lbl.style.color='#16a34a'; lbl.innerHTML='<i class="fas fa-arrow-up"></i> Ahead of schedule'; }
        else if (sl < 0) { lbl.style.color='#dc2626'; lbl.innerHTML='<i class="fas fa-arrow-down"></i> Behind schedule'; }
        else             { lbl.style.color='#9ca3af'; lbl.innerHTML='<i class="fas fa-minus"></i> On schedule'; }
    }

    /* ── Revised expiry auto-compute ── */
function recomputeRevisedExpiry() {
    const originalVal = document.getElementById('original_contract_expiry').value;
    const rows = document.getElementById('documents-list').querySelectorAll('.dynamic-row');

    let total = 0;
    rows.forEach(row => {
        const sel  = row.querySelector('select');
        const days = row.querySelector('.days-input');
        if (sel && days && sel.value.startsWith('Time Extension')) {
            // Count days whether input is enabled or disabled (previously saved rows)
            total += parseInt(days.value) || 0;
        }
    });

    const summaryEl  = document.getElementById('days-summary');
    const totalNumEl = document.getElementById('total-days-num');
    if (total > 0) {
        totalNumEl.textContent = total + ' days';
        summaryEl.style.display = 'flex';
    } else {
        summaryEl.style.display = 'none';
    }

    const pill     = document.getElementById('revised-preview-pill');
    const pillText = document.getElementById('revised-preview-text');
    if (total > 0 && originalVal) {
        const base = new Date(originalVal);
        base.setDate(base.getDate() + total);
        const iso = base.toISOString().split('T')[0];
        document.getElementById('revised_contract_expiry').value = iso;
        const fmt = base.toLocaleDateString('en-US', { month:'long', day:'numeric', year:'numeric' });
        pillText.textContent = fmt;
        pill.style.display = 'inline-flex';
    } else {
        document.getElementById('revised_contract_expiry').value = '';
        pill.style.display = 'none';
    }
}

    /* ── Document row: enable days only for Time Extension ── */
    /* ── Document row: enable days only for Time Extension ── */
function onDocumentChange(sel) {
    const row  = sel.closest('.dynamic-row');
    const days = row.querySelector('.days-input');
    const isTE = sel.value.startsWith('Time Extension');
    days.disabled = !isTE;
    if (!isTE) days.value = 0;
    updateCount('documents-list', 'documents-count');
    recomputeRevisedExpiry();
    refreshDocumentOptions();
}

/* ── Figure out which Time Extensions are already used & what the next allowed one is ── */
function getUsedTimeExtensions() {
    const selects = document.getElementById('documents-list').querySelectorAll('select');
    const used = new Set();
    selects.forEach(s => {
        if (s.value.startsWith('Time Extension')) used.add(s.value);
    });
    return used;
}

function getNextAllowedExtension(used) {
    for (let i = 1; i <= 5; i++) {
        const te = `Time Extension ${i}`;
        if (!used.has(te)) return te; // first unused = next allowed
    }
    return null; // all 5 used
}

function refreshDocumentOptions() {
    const selects = Array.from(document.getElementById('documents-list').querySelectorAll('select'));

    // Build used set from ALL rows (including current row's own selection)
    const used = getUsedTimeExtensions();

    // Find the highest TE number already used across all rows
    let highestUsed = 0;
    used.forEach(te => {
        const n = parseInt(te.replace('Time Extension ', ''));
        if (n > highestUsed) highestUsed = n;
    });

    // Next allowed = highestUsed + 1
    const nextAllowedNum = highestUsed + 1;

    selects.forEach(sel => {
        const currentVal = sel.value;
        const currentNum = currentVal.startsWith('Time Extension')
            ? parseInt(currentVal.replace('Time Extension ', ''))
            : null;

        Array.from(sel.options).forEach(opt => {
            if (!opt.value.startsWith('Time Extension')) return;

            const num = parseInt(opt.value.replace('Time Extension ', ''));

            // This row's own current selection is always visible
            if (num === currentNum) {
                opt.hidden = false;
                opt.disabled = false;
                return;
            }

            // Hide if already used by any row, OR not the next sequential one
            const alreadyUsed   = used.has(opt.value);
            const notNextInLine = num !== nextAllowedNum;

            opt.hidden   = alreadyUsed || notNextInLine;
            opt.disabled = alreadyUsed || notNextInLine;
        });
    });
}

    /* ── Issuance rows ── */
    const ISSUANCE_OPTS = [
        '1st Notice of Negative Slippage','2nd Notice of Negative Slippage',
        '3rd Notice of Negative Slippage','Liquidated Damages',
        'Notice to Terminate','Notice of Expiry',
    ];
    function issuanceRowHTML() {
        let opts = '<option value="">— Select Issuance —</option>';
        ISSUANCE_OPTS.forEach(o => opts += `<option value="${o}">${o}</option>`);
        return `<select name="issuances[]" class="dynamic-select"
                    onchange="updateCount('issuances-list','issuance-count')">${opts}</select>
                <button type="button" class="remove-btn" onclick="removeIssuanceRow(this)" title="Remove">
                    <i class="fas fa-times"></i>
                </button>`;
    }
    function addIssuanceRow() {
        const row = document.createElement('div');
        row.className = 'dynamic-row';
        row.innerHTML = issuanceRowHTML();
        document.getElementById('issuances-list').appendChild(row);
        updateCount('issuances-list', 'issuance-count');
    }
    function removeIssuanceRow(btn) {
        const list = document.getElementById('issuances-list');
        if (list.querySelectorAll('.dynamic-row').length <= 1) {
            list.querySelector('select').value = '';
            updateCount('issuances-list', 'issuance-count');
            return;
        }
        btn.closest('.dynamic-row').remove();
        updateCount('issuances-list', 'issuance-count');
    }

    /* ── Document rows ── */
    const DOCUMENT_OPTS = [
        'Time Extension 1','Time Extension 2','Time Extension 3',
        'Time Extension 4','Time Extension 5',
        'Variation Order 1','Variation Order 2','Suspension Order',
    ];
    function documentRowHTML() {
        let opts = '<option value="">— Select Document —</option>';
        DOCUMENT_OPTS.forEach(o => opts += `<option value="${o}">${o}</option>`);
        return `<select name="documents_pressed[]" class="dynamic-select"
                    onchange="onDocumentChange(this)">${opts}</select>
                <div class="days-wrap">
                    <input type="number" name="extension_days[]" class="days-input"
                        value="0" min="0" placeholder="0" disabled
                        oninput="recomputeRevisedExpiry()">
                    <span class="days-lbl">days</span>
                </div>
                <button type="button" class="remove-btn" onclick="removeDocumentRow(this)" title="Remove">
                    <i class="fas fa-times"></i>
                </button>`;
    }
    function addDocumentRow() {
        const row = document.createElement('div');
        row.className = 'dynamic-row';
        row.innerHTML = documentRowHTML();
        document.getElementById('documents-list').appendChild(row);
        updateCount('documents-list', 'documents-count');
        refreshDocumentOptions();
    }
    function removeDocumentRow(btn) {
        const list = document.getElementById('documents-list');
        if (list.querySelectorAll('.dynamic-row').length <= 1) {
            const sel  = list.querySelector('select');
            const days = list.querySelector('.days-input');
            sel.value = ''; days.value = 0; days.disabled = true;
            updateCount('documents-list', 'documents-count');
            recomputeRevisedExpiry();
            return;
        }
        btn.closest('.dynamic-row').remove();
        updateCount('documents-list', 'documents-count');
        recomputeRevisedExpiry();
        refreshDocumentOptions();
    }

    /* ── Generic count chip updater ── */
    function updateCount(listId, countId) {
        const selects = document.getElementById(listId).querySelectorAll('select');
        const filled  = [...selects].filter(s => s.value !== '').length;
        const chip    = document.getElementById(countId);
        chip.textContent = filled;
        chip.style.background  = filled > 0 ? 'rgba(249,115,22,0.15)' : 'rgba(249,115,22,0.07)';
        chip.style.color       = filled > 0 ? '#ea580c' : '#9ca3af';
        chip.style.borderColor = filled > 0 ? 'rgba(249,115,22,0.3)' : 'rgba(249,115,22,0.15)';
    }

   document.addEventListener('DOMContentLoaded', () => {
        computeSlippage();
        updateCount('issuances-list', 'issuance-count');
        updateCount('documents-list', 'documents-count');
        recomputeRevisedExpiry();
        refreshDocumentOptions();
    });
</script>
</x-app-layout>