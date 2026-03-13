<x-app-layout>
<x-slot name="header">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; letter-spacing:-0.03em; color:var(--text-primary); display:flex; align-items:center; gap:0.6rem;">
                <span style="background:#f97316; width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(249,115,22,0.35);">
                    <i class="fas fa-plus-circle" style="color:white; font-size:0.85rem;"></i>
                </span>
                Create New Project
            </h2>
            <p style="color:var(--text-secondary); font-size:0.82rem; margin-top:3px;">Add a new project to the system</p>
        </div>
        <div style="display:flex; align-items:center; gap:1.25rem;">
            <a href="{{ route('admin.projects.index') }}"
               style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.6rem 1.1rem; border:1.5px solid var(--border); border-radius:9px; font-weight:600; font-size:0.855rem; color:var(--text-secondary); text-decoration:none; background:var(--bg-secondary); transition:all 0.2s;"
               onmouseover="this.style.borderColor='#f97316';this.style.color='#ea580c'"
               onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-secondary)'">
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
        --surface:    #fffaf5;
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

    body { color: var(--text-primary); transition: background 0.3s, color 0.3s; }

    .form-card {
        background: var(--bg-primary);
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
    }

    .section-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        background: var(--bg-secondary);
        display: flex; align-items: center; gap: 0.5rem;
    }

    .section-header span {
        font-family: 'Syne', sans-serif;
        font-weight: 700; font-size: 0.875rem;
        color: var(--ink); letter-spacing: -0.01em;
    }

    .section-body { padding: 1.5rem; }

    .field-label {
        display: block;
        font-size: 0.72rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: var(--ink-muted); margin-bottom: 0.45rem;
    }

    .field-input {
        width: 100%;
        padding: 0.7rem 1rem;
        border: 1.5px solid var(--border);
        border-radius: 9px;
        font-size: 0.875rem;
        color: var(--text-primary);
        background: var(--bg-primary);
        outline: none;
        font-family: 'Instrument Sans', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .field-input:focus {
        border-color: var(--orange-500);
        box-shadow: 0 0 0 3px rgba(249,115,22,0.1);
    }

    .field-input.has-error { border-color: #ef4444; }

    .field-error {
        font-size: 0.775rem; color: #ef4444;
        margin-top: 0.35rem; display: flex; align-items: center; gap: 0.3rem;
    }

    .prefix-wrap { position: relative; }
    .prefix-wrap .prefix {
        position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%);
        color: var(--ink-muted); font-size: 0.85rem; font-weight: 600; pointer-events: none;
    }
    .prefix-wrap .field-input { padding-left: 1.75rem; }

    .prog-bar-track {
        height: 4px; background: rgba(249,115,22,0.1);
        border-radius: 99px; margin-top: 0.5rem; overflow: hidden;
    }
    .prog-bar-fill { height: 100%; border-radius: 99px; transition: width 0.4s ease; }

    @keyframes fadeUp {
        from { opacity:0; transform:translateY(14px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .fade-up { animation: fadeUp 0.45s ease both; }
</style>

<div class="max-w-4xl mx-auto fade-up">
    <form method="POST" action="{{ route('admin.projects.store') }}">
        @csrf

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.25rem;">

            {{-- Project Information --}}
            <div class="form-card" style="grid-column:1 / -1;">
                <div class="section-header">
                    <i class="fas fa-circle-info" style="color:var(--orange-500); font-size:0.85rem;"></i>
                    <span>Project Information</span>
                </div>
                <div class="section-body" style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">

                    <div>
                        <label class="field-label">In Charge</label>
                        <input type="text" name="in_charge" class="field-input {{ $errors->has('in_charge') ? 'has-error' : '' }}"
                            placeholder="Who is responsible?" value="{{ old('in_charge') }}" required>
                        @error('in_charge')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="field-label">Project Title</label>
                        <input type="text" name="project_title" class="field-input {{ $errors->has('project_title') ? 'has-error' : '' }}"
                            placeholder="Give the project a name" value="{{ old('project_title') }}" required>
                        @error('project_title')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="field-label">Location</label>
                        <input type="text" name="location" class="field-input {{ $errors->has('location') ? 'has-error' : '' }}"
                            placeholder="Project location" value="{{ old('location') }}" required>
                        @error('location')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="field-label">Contractor</label>
                        <input type="text" name="contractor" class="field-input {{ $errors->has('contractor') ? 'has-error' : '' }}"
                            placeholder="Contractor company" value="{{ old('contractor') }}" required>
                        @error('contractor')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="field-label">Contract Amount</label>
                        <div class="prefix-wrap">
                            <span class="prefix">₱</span>
                            <input type="number" name="contract_amount" class="field-input {{ $errors->has('contract_amount') ? 'has-error' : '' }}"
                                placeholder="0.00" value="{{ old('contract_amount', 0) }}" min="0" step="0.01" required>
                        </div>
                        @error('contract_amount')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="field-label">Status</label>
                        <select name="status" class="field-input" id="status_sel" onchange="toggleCompletedAt()"
                            style="appearance:none; background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%236b4f35%22 stroke-width=%222%22%3E%3Cpath d=%22M6 9l6 6 6-6%22/%3E%3C/svg%3E'); background-position:right 0.6rem center; background-repeat:no-repeat; background-size:1.1em; padding-right:2rem; cursor:pointer;">
                            <option value="ongoing"   {{ old('status') == 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <div id="completed_at_field" class="{{ old('status') == 'completed' ? '' : 'hidden' }}" style="grid-column:1/-1;">
                        <label class="field-label">Date Completed</label>
                        <input type="date" name="completed_at" class="field-input {{ $errors->has('completed_at') ? 'has-error' : '' }}"
                            value="{{ old('completed_at') }}">
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
                        <input type="date" id="date_started" name="date_started" class="field-input {{ $errors->has('date_started') ? 'has-error' : '' }}"
                            value="{{ old('date_started') }}" required oninput="calculateOriginalExpiry()">
                        @error('date_started')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">Contract Days</label>
                        <input type="number" id="contract_days" name="contract_days" class="field-input {{ $errors->has('contract_days') ? 'has-error' : '' }}"
                            placeholder="Enter number of days" value="{{ old('contract_days') }}" min="0" step="1" required oninput="calculateOriginalExpiry()">
                        @error('contract_days')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="field-label">Original Expiry <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto-calculated)</span></label>
                        <input type="date" id="original_contract_expiry" name="original_contract_expiry" class="field-input {{ $errors->has('original_contract_expiry') ? 'has-error' : '' }}"
                            value="{{ old('original_contract_expiry') }}" readonly style="background:rgba(249,115,22,0.08); cursor:not-allowed;">
                        @error('original_contract_expiry')<p class="field-error"><i class="fas fa-exclamation-circle"></i>{{ $message }}</p>@enderror
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
                        <label class="field-label">As Planned <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(%)</span></label>
                        <div class="prefix-wrap" style="position:relative;">
                            <input type="number" name="as_planned" id="as_planned" class="field-input"
                                style="padding-right:2.5rem;" placeholder="0" value="{{ old('as_planned', 0) }}" min="0" max="100" step="0.01" oninput="liveSlippage()">
                            <span style="position:absolute; right:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.8rem; font-weight:600;">%</span>
                        </div>
                        <div class="prog-bar-track"><div class="prog-bar-fill" id="ap_bar" style="background:var(--orange-500); width:0%;"></div></div>
                    </div>
                    <div>
                        <label class="field-label">Work Done <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(%)</span></label>
                        <div style="position:relative;">
                            <input type="number" name="work_done" id="work_done" class="field-input"
                                style="padding-right:2.5rem;" placeholder="0" value="{{ old('work_done', 0) }}" min="0" max="100" step="0.01" oninput="liveSlippage()">
                            <span style="position:absolute; right:0.875rem; top:50%; transform:translateY(-50%); color:var(--ink-muted); font-size:0.8rem; font-weight:600;">%</span>
                        </div>
                        <div class="prog-bar-track"><div class="prog-bar-fill" id="wd_bar" style="background:#3b82f6; width:0%;"></div></div>
                    </div>
                    <div>
                        <label class="field-label">Slippage <span style="font-weight:400; text-transform:none; letter-spacing:0; color:#9ca3af;">(auto-calculated)</span></label>
                        
                        {{-- Hidden input so slippage still submits with the form --}}
                        <input type="hidden" name="slippage" id="slippage" value="{{ old('slippage', 0) }}">

                        <div id="slippage-display" style="
                            display:flex; align-items:center; justify-content:space-between;
                            padding:0.75rem 1rem;
                            border:1.5px solid rgba(26,15,0,0.08);
                            border-radius:9px;
                            background:#fffaf5;
                            min-height:42px;">
                            <p id="slippage_label" style="font-size:0.825rem; font-weight:600; color:#9ca3af;">
                                <i class="fas fa-minus"></i> Enter values above
                            </p>
                            <span id="slippage-value" style="font-family:'Syne',sans-serif; font-size:1.15rem; font-weight:800; color:#9ca3af;">—</span>
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
                        placeholder="Enter any remarks or recommendations…">{{ old('remarks_recommendation') }}</textarea>
                </div>
            </div>

        </div>

        {{-- Actions --}}
        <div style="display:flex; gap:0.875rem; padding-top:0.25rem;">
            <button type="submit"
                style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.75rem 1.75rem; background:var(--orange-500); color:white; font-weight:700; font-size:0.9rem; border-radius:10px; border:none; cursor:pointer; box-shadow:0 3px 14px rgba(249,115,22,0.38); font-family:'Instrument Sans',sans-serif; transition:all 0.2s;"
                onmouseover="this.style.background='#ea580c';this.style.transform='translateY(-1px)'"
                onmouseout="this.style.background='#f97316';this.style.transform='translateY(0)'">
                <i class="fas fa-save"></i> Create Project
            </button>
            <a href="{{ route('admin.projects.index') }}"
               style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.75rem 1.5rem; border:1.5px solid rgba(26,15,0,0.1); border-radius:10px; font-weight:600; font-size:0.875rem; color:var(--ink-muted); text-decoration:none; background:white; transition:all 0.2s;"
               onmouseover="this.style.borderColor='var(--orange-500)';this.style.color='var(--orange-600)'"
               onmouseout="this.style.borderColor='rgba(26,15,0,0.1)';this.style.color='var(--ink-muted)'">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>

    </form>
</div>

<script>
    function toggleCompletedAt() {
        const v = document.getElementById('status_sel').value;
        document.getElementById('completed_at_field').classList.toggle('hidden', v !== 'completed');
    }

    function liveSlippage() {
    const ap = parseFloat(document.getElementById('as_planned').value);
    const wd = parseFloat(document.getElementById('work_done').value);

    document.getElementById('ap_bar').style.width = Math.min(ap || 0, 100) + '%';
    document.getElementById('wd_bar').style.width = Math.min(wd || 0, 100) + '%';

    const lbl     = document.getElementById('slippage_label');
    const valEl   = document.getElementById('slippage-value');
    const display = document.getElementById('slippage-display');

    if (isNaN(ap) || isNaN(wd)) {
        lbl.style.color   = '#9ca3af';
        lbl.innerHTML     = '<i class="fas fa-minus"></i> Enter values above';
        valEl.textContent = '—';
        valEl.style.color = '#9ca3af';
        display.style.borderColor = 'rgba(26,15,0,0.08)';
        document.getElementById('slippage').value = 0;
        return;
    }

    const sl = (wd - ap).toFixed(2);
    document.getElementById('slippage').value = sl;

    if (sl > 0) {
        lbl.style.color        = '#16a34a';
        lbl.innerHTML          = '<i class="fas fa-arrow-up"></i> Ahead of schedule';
        valEl.style.color      = '#16a34a';
        display.style.borderColor = 'rgba(22,163,74,0.2)';
        display.style.background  = 'rgba(22,163,74,0.04)';
    } else if (sl < 0) {
        lbl.style.color        = '#dc2626';
        lbl.innerHTML          = '<i class="fas fa-arrow-down"></i> Behind schedule';
        valEl.style.color      = '#dc2626';
        display.style.borderColor = 'rgba(220,38,38,0.2)';
        display.style.background  = 'rgba(220,38,38,0.04)';
    } else {
        lbl.style.color        = '#9ca3af';
        lbl.innerHTML          = '<i class="fas fa-minus"></i> On schedule';
        valEl.style.color      = '#9ca3af';
        display.style.borderColor = 'rgba(26,15,0,0.08)';
        display.style.background  = '#fffaf5';
    }

    valEl.textContent = (sl > 0 ? '+' : '') + sl + '%';
}

function calculateOriginalExpiry() {
    const dateStartedInput = document.getElementById('date_started').value;
    const contractDaysInput = document.getElementById('contract_days').value;
    const originalExpiryInput = document.getElementById('original_contract_expiry');

    if (!dateStartedInput || !contractDaysInput) {
        originalExpiryInput.value = '';
        return;
    }

    const dateStarted = new Date(dateStartedInput);
    const contractDays = parseInt(contractDaysInput);

    if (isNaN(dateStarted.getTime()) || isNaN(contractDays)) {
        originalExpiryInput.value = '';
        return;
    }

    // Add days to the date
    const originalExpiry = new Date(dateStarted);
    originalExpiry.setDate(originalExpiry.getDate() + contractDays - 1);

    // Format the date as YYYY-MM-DD for the date input
    const year = originalExpiry.getFullYear();
    const month = String(originalExpiry.getMonth() + 1).padStart(2, '0');
    const day = String(originalExpiry.getDate()).padStart(2, '0');
    
    originalExpiryInput.value = `${year}-${month}-${day}`;
}
</script>
</x-app-layout>