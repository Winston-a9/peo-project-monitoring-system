/* ── Amount input comma formatter ── */
(function () {
    function rawVal(str) {
        const isNeg = str.trim().startsWith('-');
        const cleaned = str.replace(/,/g, '').replace(/[^0-9.]/g, '');
        return isNeg && cleaned !== '' ? '-' + cleaned : cleaned;
    }

    function formatWithCommas(str) {
        const trimmed = str.trim();
        // Allow bare minus while user is still typing
        if (trimmed === '-') return '-';

        const isNeg = trimmed.startsWith('-');
        const raw = rawVal(trimmed);
        if (raw === '') return '';

        const abs = raw.replace(/^-/, '');
        const parts = abs.split('.');
        if (parts.length > 2) parts.splice(2);
        const intFormatted = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        const formatted = parts.length === 2 ? intFormatted + '.' + parts[1] : intFormatted;
        return isNeg ? '-' + formatted : formatted;
    }

    window.initAmountInput = function (el) {
        if (!el || el.dataset.amountInit) return;
        el.dataset.amountInit = '1';
        if (el.value) el.value = formatWithCommas(el.value);

        el.addEventListener('keydown', function (e) {
            // Allow minus only at the start and only if not already present
            if (e.key === '-') {
                if (this.selectionStart === 0 && !this.value.startsWith('-')) {
                    return; // allow it
                }
                e.preventDefault();
            }
        });

        el.addEventListener('input', function () {
            const cursorPos = this.selectionStart;
            const beforeLen = this.value.length;
            this.value = formatWithCommas(this.value);
            const afterLen = this.value.length;
            const newPos = cursorPos + (afterLen - beforeLen);
            this.setSelectionRange(newPos, newPos);
        });

        el.addEventListener('blur', function () {
            // On blur, if just '-' was typed with nothing after, clear it
            if (this.value.trim() === '-') this.value = '';
            else this.value = formatWithCommas(this.value);
        });
    };

    // Strip commas before any form submits so Laravel receives clean numbers
    document.addEventListener('submit', function (e) {
        e.target.querySelectorAll('[data-amount]').forEach(el => {
            el.value = rawVal(el.value);
        });
    }, true);
})();

/* ── Tab switching ── */
window.switchTab = function (tabId, btnElement) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('tab-active'));
    document.getElementById(tabId).classList.add('active');
    btnElement.classList.add('tab-active');
};

/* ── Accordion toggle ── */
window.toggleAcc = function (id) {
    const hdr = document.getElementById('acc-' + id + '-hdr');
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
};

/* ── Status field visibility ── */
window.toggleCompletedAt = function () {
    document.getElementById('completed_at_field')
        .classList.toggle('hidden', document.getElementById('status_sel').value !== 'completed');
};

/* ── LDA read-only toggle ── */
window.toggleLDAReadOnly = function (isComplete) {
    const ldAccomplishedInput = document.getElementById('ld_accomplished');
    const ldSection = ldAccomplishedInput?.closest('.form-card');
    if (!ldSection) return;

    const inputs = ldSection.querySelectorAll('input:not([type="hidden"])');

    if (isComplete) {
        // Lock all visible inputs
        inputs.forEach(input => {
            input.setAttribute('readonly', true);
            input.style.opacity = '0.5';
            input.style.cursor = 'not-allowed';
            input.style.pointerEvents = 'none';
        });

        // Show a notice if not already present
        if (!document.getElementById('lda-readonly-notice')) {
            const notice = document.createElement('div');
            notice.id = 'lda-readonly-notice';
            notice.style.cssText = `
                display:flex; align-items:center; gap:0.6rem;
                padding:0.75rem 1rem; border-radius:9px;
                background:rgba(22,163,74,0.06);
                border:1.5px solid rgba(22,163,74,0.2);
                margin-bottom:1rem;
            `;
            notice.innerHTML = `
                <i class="fas fa-lock" style="color:#16a34a; font-size:0.85rem; flex-shrink:0;"></i>
                <p style="margin:0; font-size:0.8rem; color:#15803d; font-weight:600;">
                    Work is 100% complete — Liquidated Damages assessment is locked.
                    <span style="font-weight:400; color:#16a34a;">No LD applies when work is fully done.</span>
                </p>
            `;
            // Insert before the grid inside section-body
            const sectionBody = ldSection.querySelector('.section-body');
            if (sectionBody) sectionBody.prepend(notice);
        }

        // Zero out LD values visually and in hidden inputs
        ['ld_per_day_display', 'total_ld_display', 'ld_unworked_display'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.textContent = '0.00';
        });
        ['ld_per_day', 'total_ld', 'ld_unworked', 'ld_days_overdue_input'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '0';
        });

    } else {
        // Unlock
        inputs.forEach(input => {
            input.removeAttribute('readonly');
            input.style.opacity = '';
            input.style.cursor = '';
            input.style.pointerEvents = '';
        });

        // Remove notice
        document.getElementById('lda-readonly-notice')?.remove();

        // Recalculate with current values
        window.calculateLDPerDay();
        window.calculateDaysOverdue();
    }
};

/* ── Slippage calculator ── */
window.computeSlippage = function () {
    const ap = parseFloat(document.getElementById('as_planned').value);
    const wd = parseFloat(document.getElementById('work_done').value);
    const lbl = document.getElementById('slippage_label');
    const valEl = document.getElementById('slippage-value');

    document.getElementById('ap_bar').style.width = Math.min(ap || 0, 100) + '%';
    document.getElementById('wd_bar').style.width = Math.min(wd || 0, 100) + '%';

    if (isNaN(ap) || isNaN(wd)) { valEl.textContent = '—'; return; }

    const sl = parseFloat((wd - ap).toFixed(3));
    document.getElementById('slippage').value = sl;

    if (sl > 0) { lbl.style.color = '#16a34a'; lbl.innerHTML = '<i class="fas fa-arrow-up"></i> Ahead'; valEl.style.color = '#16a34a'; }
    else if (sl < 0) { lbl.style.color = '#dc2626'; lbl.innerHTML = '<i class="fas fa-arrow-down"></i> Behind'; valEl.style.color = '#dc2626'; }
    else { lbl.style.color = '#9ca3af'; lbl.innerHTML = '<i class="fas fa-minus"></i> On schedule'; valEl.style.color = '#9ca3af'; }

    valEl.textContent = (sl > 0 ? '+' : '') + sl + '%';

    // ── Toggle LDA read-only when work is fully done ──
    window.toggleLDAReadOnly(wd >= 100);
};
/* ── LD calculator ── */
function fmtNum(n, decimals) {
    return n.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
}

/**
 * Auto-calculates days overdue (or remaining) from the contract expiry date.
 * Uses revisedExpiry if set, otherwise originalExpiry.
 * Both variables are injected by the blade inline script before this file loads.
 *
 * - Contract still active  → shows "X days remaining" in green, hidden input = 0
 * - Expires today          → shows "0 days — expires today" in amber, hidden input = 0
 * - Contract overdue       → shows "X days overdue" in red, hidden input = X
 */
window.calculateDaysOverdue = function () {
    const expiryStr = (typeof revisedExpiry !== 'undefined' && revisedExpiry !== '')
        ? revisedExpiry
        : (typeof originalExpiry !== 'undefined' ? originalExpiry : null);

    if (!expiryStr) return;

    const expiry = new Date(expiryStr + 'T00:00:00');
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const diffMs = today - expiry;
    const diffDays = Math.floor(Math.abs(diffMs) / 86400000);
    const isOverdue = diffMs > 0;

    const hiddenEl = document.getElementById('ld_days_overdue_input');
    const displayEl = document.getElementById('ld_days_overdue_display');
    const unitEl = document.getElementById('ld_days_unit');
    const hintEl = document.getElementById('ld_overdue_hint');
    const boxEl = displayEl ? displayEl.closest('[id="ld_days_box"], div') : null;

    // Set the hidden input — only store overdue count when contract is actually overdue
    if (hiddenEl) hiddenEl.value = isOverdue ? diffDays : 0;
    if (displayEl) displayEl.textContent = diffDays;

    if (isOverdue) {
        if (displayEl) displayEl.style.color = '#dc2626';
        if (unitEl) { unitEl.textContent = 'days overdue'; unitEl.style.color = '#dc2626'; }
        if (hintEl) { hintEl.style.color = '#dc2626'; hintEl.innerHTML = '<i class="fas fa-triangle-exclamation"></i> Contract is overdue — LD is accumulating'; }
    } else if (diffDays === 0) {
        if (displayEl) displayEl.style.color = '#f59e0b';
        if (unitEl) { unitEl.textContent = 'days — expires today'; unitEl.style.color = '#f59e0b'; }
        if (hintEl) { hintEl.style.color = '#d97706'; hintEl.innerHTML = '<i class="fas fa-clock"></i> Contract expires today'; }
    } else {
        if (displayEl) displayEl.style.color = '#16a34a';
        if (unitEl) { unitEl.textContent = 'days remaining'; unitEl.style.color = '#16a34a'; }
        if (hintEl) { hintEl.style.color = '#16a34a'; hintEl.innerHTML = '<i class="fas fa-check-circle"></i> Contract still active — no LD applies'; }
    }

    window.calculateLDTotal();
};

/**
 * LD Per Day formula:
 *   LD per day = (Unworked % ÷ 100) × Remaining Balance × 0.001
 *
 * Note: uses UNWORKED %, not accomplished %.
 * ld_unworked is kept in sync for display only.
 */
window.calculateLDPerDay = function () {
    const acc = parseFloat(document.getElementById('ld_accomplished').value) || 0;
    const remainingEl = document.getElementById('remaining_balance');
    const remaining = remainingEl ? parseFloat(remainingEl.value.replace(/,/g, '')) || 0 : NaN;
    const fallbackAmt = parseFloat(document.getElementById('original_contract_amount')?.value.replace(/,/g, '')) || 0;
    const basisAmount = Number.isFinite(remaining) ? remaining : fallbackAmt;

    const unworked = 100 - acc;  // ← NO rounding, keep full precision
    const perDay = Math.max(0, unworked) / 100 * basisAmount * 0.001;

    document.getElementById('ld_unworked').value = unworked.toFixed(2);  // store 2dp for display
    document.getElementById('ld_per_day').value = perDay;  // full precision

    const unworkedDisplay = document.getElementById('ld_unworked_display');
    const perDayDisplay = document.getElementById('ld_per_day_display');
    if (unworkedDisplay) unworkedDisplay.textContent = fmtNum(unworked, 2);  // show 2dp
    if (perDayDisplay) perDayDisplay.textContent = fmtNum(perDay, 2);

    window.calculateLDTotal();
};

/**
 * Total LD formula:
 *   Total LD = LD per day × Days Overdue
 *
 * Days overdue is auto-filled by calculateDaysOverdue() — grows each day the
 * project remains past its expiry without any user input needed.
 */
window.calculateLDTotal = function () {
    const perDay = parseFloat(document.getElementById('ld_per_day').value) || 0;
    const overdue = parseFloat(document.getElementById('ld_days_overdue_input').value) || 0;
    const total = perDay * overdue;  // full precision multiplication

    document.getElementById('total_ld').value = total.toFixed(2);

    const totalDisplay = document.getElementById('total_ld_display');
    if (totalDisplay) totalDisplay.textContent = fmtNum(total, 2);
};

/* ── Date preview helpers ──
   Depend on: originalExpiry, existingTEDays, existingVODays, existingSODays
   (declared inline in the blade before this script loads)           */
function addDaysToDate(dateStr, days) {
    const d = new Date(dateStr + 'T00:00:00');
    d.setDate(d.getDate() + days);
    return d;
}
function formatDate(d) {
    return d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
}

window.updateTEPreview = function () {
    const newDays = parseInt(document.getElementById('new_te_days').value) || 0;
    const preview = document.getElementById('te_revised_preview');
    if (newDays < 1) { preview.textContent = 'Enter days above to preview'; return; }
    preview.textContent = formatDate(addDaysToDate(originalExpiry, existingTEDays + existingVODays + existingSODays + newDays));
};

window.updateVOPreview = function () {
    const newDays = parseInt(document.getElementById('new_vo_days').value) || 0;
    const preview = document.getElementById('vo_revised_preview');
    if (newDays < 1) { preview.textContent = 'Enter days above to preview'; return; }
    preview.textContent = formatDate(addDaysToDate(originalExpiry, existingTEDays + existingVODays + existingSODays + newDays));
};

window.updateSOPreview = function () {
    const newDays = parseInt(document.getElementById('new_so_days').value) || 0;
    const preview = document.getElementById('so_revised_preview');
    if (newDays < 1) { preview.textContent = 'Enter days to preview'; return; }
    preview.textContent = formatDate(addDaysToDate(originalExpiry, existingTEDays + existingVODays + existingSODays + newDays));
};

/* ── Issuances ── */
window.checkPerformanceBond = function () {
    const hasPerformanceBond = [...document.querySelectorAll('#issuances-list select')]
        .some(s => s.value === 'Performance Bond');
    const field = document.getElementById('performance-bond-date-field');
    if (field) field.style.display = hasPerformanceBond ? 'block' : 'none';
};

window.issuanceRowHTML = function (val = '') {
    let opts = '<option value="">— Select Issuance —</option>';
    ISSUANCE_OPTS.forEach(o => opts += `<option value="${o}" ${o === val ? 'selected' : ''}>${o}</option>`);
    return `<div class="dynamic-row">
        <select name="issuances[]" class="dynamic-select" onchange="updateCount('issuances-list','issuance-count'); checkPerformanceBond()">${opts}</select>
        <button type="button" class="remove-btn" onclick="removeIssuanceRow(this)"><i class="fas fa-times"></i></button>
    </div>`;
};

window.addIssuanceRow = function () {
    document.getElementById('issuances-list').insertAdjacentHTML('beforeend', window.issuanceRowHTML());
    window.updateCount('issuances-list', 'issuance-count');
};

window.removeIssuanceRow = function (btn) {
    const list = document.getElementById('issuances-list');
    if (list.querySelectorAll('.dynamic-row').length <= 1) {
        list.querySelector('select').value = '';
        window.updateCount('issuances-list', 'issuance-count');
        window.checkPerformanceBond();
        return;
    }
    btn.closest('.dynamic-row').remove();
    window.updateCount('issuances-list', 'issuance-count');
    window.checkPerformanceBond();
};

window.updateCount = function (listId, countId) {
    const filled = [...document.getElementById(listId).querySelectorAll('select')]
        .filter(s => s.value !== '').length;
    const chip = document.getElementById(countId);
    chip.textContent = filled;
    chip.style.background = filled > 0 ? 'rgba(249,115,22,0.15)' : 'rgba(249,115,22,0.07)';
    chip.style.color = filled > 0 ? '#ea580c' : '#9ca3af';
    chip.style.borderColor = filled > 0 ? 'rgba(249,115,22,0.3)' : 'rgba(249,115,22,0.15)';
};

/* ── Delete entry modal ── */
window.openDeleteModal = function (type, index, label, days) {
    window._delType = type;
    window._delIndex = index;
    const isVO = type === 'vo';

    document.getElementById('del-entry-icon').className = 'fas ' + (isVO ? 'fa-file-signature' : 'fa-clock');
    document.getElementById('del-entry-icon').style.color = '#dc2626';
    document.getElementById('del-entry-label').textContent = label;
    document.getElementById('del-entry-days').textContent = '+' + days + 'd';

    const titleEl = document.getElementById('delete-entry-modal-title');
    if (titleEl) titleEl.textContent = 'Delete ' + label;

    const totalOfType = isVO ? _totalVOCount : _totalTECount;
    const notice = document.getElementById('del-renumber-notice');
    if (totalOfType > 1) {
        notice.style.display = 'flex';
        document.getElementById('del-renumber-text').innerHTML =
            'Remaining <strong style="color:var(--text-primary);">' +
            (isVO ? 'Variation Orders' : 'Time Extensions') +
            '</strong> will be renumbered from <strong style="color:var(--text-primary);">1</strong>.';
    } else {
        notice.style.display = 'none';
    }

    const textarea = document.getElementById('del-reason-input');
    textarea.value = '';
    textarea.style.borderColor = 'var(--border)';
    textarea.style.boxShadow = 'none';
    document.getElementById('del-reason-count').textContent = '0';
    document.getElementById('del-reason-error').style.display = 'none';

    const btn = document.getElementById('del-confirm-btn');
    btn.innerHTML = '<i class="fas fa-trash-alt" style="font-size:0.75rem;"></i> Delete Entry';
    btn.disabled = false;
    btn.style.opacity = '1';

    openModal('delete-entry-modal');
};

window.delClearError = function () {
    document.getElementById('del-reason-error').style.display = 'none';
    document.getElementById('del-reason-input').style.borderColor = 'var(--border)';
};

/* ── Billing preview ── */
window.updateBillingPreview = function () {
    const input = document.getElementById('new_billing_amount');
    const totalEl = document.getElementById('billing_total_preview');
    const remainingEl = document.getElementById('billing_remaining_val');
    const previewP = document.getElementById('billing_remaining_preview');
    if (!input || !totalEl || !remainingEl) return;

    const newAmt = parseFloat(input.value.replace(/,/g, '')) || 0;
    const base = parseFloat(totalEl.dataset.base) || 0;
    const originalAmt = parseFloat(document.getElementById('original_contract_amount')?.value) || 0;

    // Mirror the blade @php: adjustedContractAmt = original + sum of all TE/VO costs
    // The blade already renders the adjusted amount as the denominator in the remaining balance display.
    // We read it from the rendered remaining balance + totalBilled instead of recomputing costs in JS.
    const renderedRemaining = parseFloat(
        document.getElementById('billing_remaining_val')?.textContent.replace(/,/g, '')
    ) || 0;
    const adjustedContractAmt = base + renderedRemaining; // base is totalBilled at page load

    const newTotal = base + newAmt;
    const newRemain = adjustedContractAmt - newTotal;

    totalEl.textContent = newTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    remainingEl.textContent = newRemain.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    if (previewP) previewP.style.color = newRemain >= 0 ? '#3b82f6' : '#dc2626';
};

/* ── Init on DOMContentLoaded ── */
document.addEventListener('DOMContentLoaded', () => {
    const delReasonInput = document.getElementById('del-reason-input');
    if (delReasonInput) {
        delReasonInput.addEventListener('input', function () {
            document.getElementById('del-reason-count').textContent = this.value.length;
        });
    }

    window.computeSlippage();

    // ── Run LDA lock on initial load if work_done is already 100 ──
    const initialWD = parseFloat(document.getElementById('work_done')?.value) || 0;
    window.toggleLDAReadOnly(initialWD >= 100);

    const issuancesList = document.getElementById('issuances-list');
    if (issuancesList) window.updateCount('issuances-list', 'issuance-count');

    window.calculateDaysOverdue();

    const ldAccomplished = document.getElementById('ld_accomplished');
    if (ldAccomplished && ldAccomplished.value && initialWD < 100) {
        // Only restore saved DB values if NOT locked (work_done < 100)
        const savedUnworked = parseFloat(document.getElementById('ld_unworked').value) || 0;
        const savedPerDay = parseFloat(document.getElementById('ld_per_day').value) || 0;
        const savedTotal = parseFloat(document.getElementById('total_ld').value) || 0;

        const unworkedDisplay = document.getElementById('ld_unworked_display');
        const perDayDisplay = document.getElementById('ld_per_day_display');
        const totalDisplay = document.getElementById('total_ld_display');

        if (unworkedDisplay) unworkedDisplay.textContent = fmtNum(savedUnworked, 2);
        if (perDayDisplay) perDayDisplay.textContent = fmtNum(savedPerDay, 2);
        if (totalDisplay) totalDisplay.textContent = fmtNum(savedTotal, 2);
    }

    window.checkPerformanceBond();

    const billingTotalEl = document.getElementById('billing_total_preview');
    if (billingTotalEl && !billingTotalEl.dataset.base) {
        billingTotalEl.dataset.base = billingTotalEl.textContent.replace(/,/g, '');
    }

    /* ── Mark as Completed toggle ── */
    window.toggleCompleteSection = function () {
        const section = document.getElementById('complete-section');
        if (!section) return;
        const isHidden = section.style.display === 'none';
        section.style.display = isHidden ? 'block' : 'none';
        if (isHidden) {
            document.getElementById('completed_at_input')?.focus();
        } else {
            document.getElementById('completed_at_input').value = '';
            document.getElementById('completed_at_hidden').value = '';
        }
    };

    /* ── Reactivate toggle ── */
    window.toggleReactivateSection = function () {
        const section = document.getElementById('reactivate-section');
        if (!section) return;
        section.style.display = section.style.display === 'none' ? 'block' : 'none';
    };

    window.confirmReactivate = function () {
        const form = document.getElementById('reactivate-form');
        if (form) form.submit();
    };

    /* ── Advance billing & retention calculator ── */
    window.calcAdvanceRetention = function () {
        const originalAmt = parseFloat(
            document.getElementById('original_contract_amount')?.value.replace(/,/g, '') || 0
        ) || 0;

        const advPct = parseFloat(document.getElementById('advance_billing_pct')?.value) || 0;
        const retPct = parseFloat(document.getElementById('retention_pct')?.value) || 0;

        const advAmt = advPct > 0 ? (advPct / 100) * originalAmt : 0;
        const retAmt = retPct > 0 ? (retPct / 100) * originalAmt : 0;

        const advDisplay = document.getElementById('advance_billing_amount_display');
        const retDisplay = document.getElementById('retention_amount_display');

        if (advDisplay) advDisplay.textContent = advAmt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (retDisplay) retDisplay.textContent = retAmt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    // ── Reason intercept modal ────────────────────────────────
    window._rimType = null; // 'te' | 'vo' | 'so' | null
    window._pendingForm = null; // Store form reference to avoid selector issues
    window._rimBypassIntercept = false; // Flag to bypass modal on re-submission

    // Use event delegation to handle form submissions reliably across page reloads
    document.addEventListener('submit', function (e) {
        // If we just confirmed and are re-submitting, bypass the modal check
        if (window._rimBypassIntercept) {
            window._rimBypassIntercept = false;
            return;
        }

        // Only handle forms that have extension/suspension day inputs
        const form = e.target;
        if (!form.querySelector('[name="new_te_days"]')) return;

        const teDays = parseInt(form.querySelector('[name="new_te_days"]')?.value) || 0;
        const voDays = parseInt(form.querySelector('[name="new_vo_days"]')?.value) || 0;
        const soDays = parseInt(form.querySelector('[name="new_so_days"]')?.value) || 0;

        // Only intercept when a new TE, VO, or SO is actually being added
        if (teDays < 1 && voDays < 1 && soDays < 1) return;

        const teReasonInput = form.querySelector('[name="new_te_reason"]');
        const voReasonInput = form.querySelector('[name="new_vo_reason"]');
        const soReasonInput = form.querySelector('[name="new_so_reason"]');

        const teReason = teReasonInput?.value.trim() || '';
        const voReason = voReasonInput?.value.trim() || '';
        const soReason = soReasonInput?.value.trim() || '';

        // If all filled reasons match their days, let through
        if (
            (teDays < 1 || teReason !== '') &&
            (voDays < 1 || voReason !== '') &&
            (soDays < 1 || soReason !== '')
        ) return;

        e.preventDefault();

        // Store the form reference for later use in rimConfirm()
        window._pendingForm = form;

        // Ask in order: TE → VO → SO
        if (teDays > 0 && teReason === '') {
            window._rimType = 'te';
        } else if (voDays > 0 && voReason === '') {
            window._rimType = 'vo';
        } else if (soDays > 0 && soReason === '') {
            window._rimType = 'so';
        }

        openRIM();
    }, false);

    function openRIM() {
        const isVO = window._rimType === 'vo';
        const isSO = window._rimType === 'so';
        const isTE = window._rimType === 'te';

        const accent = isVO ? '#6366f1' : isSO ? '#d97706' : 'var(--orange-500)';

        const icon = document.getElementById('rim-icon');
        const lbl = document.getElementById('rim-label');
        if (icon) {
            icon.className = 'fas ' + (isVO ? 'fa-file-signature' : isSO ? 'fa-pause-circle' : 'fa-clock');
            icon.style.color = accent;
        }
        if (lbl) {
            let days, label;
            if (isVO) {
                days = parseInt(document.getElementById('new_vo_days')?.value) || 0;
                label = `Variation Order ${_totalVOCount + 1} — ${days} day${days !== 1 ? 's' : ''}`;
            } else if (isSO) {
                days = parseInt(document.getElementById('new_so_days')?.value) || 0;
                label = `Suspension Order — ${days} day${days !== 1 ? 's' : ''}`;
            } else {
                days = parseInt(document.getElementById('new_te_days')?.value) || 0;
                label = `Time Extension ${_totalTECount + 1} — ${days} day${days !== 1 ? 's' : ''}`;
            }
            lbl.textContent = label;
        }

        const btn = document.getElementById('rim-confirm-btn');
        if (btn) {
            btn.style.background = isVO ? '#6366f1' : isSO ? '#d97706' : 'var(--orange-500)';
            btn.style.boxShadow = isVO
                ? '0 2px 8px rgba(99,102,241,0.3)'
                : isSO
                    ? '0 2px 8px rgba(217,119,6,0.3)'
                    : '0 2px 8px rgba(249,115,22,0.3)';
            btn.onmouseover = () => btn.style.background = isVO ? '#4f46e5' : isSO ? '#b45309' : '#ea580c';
            btn.onmouseout = () => btn.style.background = isVO ? '#6366f1' : isSO ? '#d97706' : 'var(--orange-500)';
        }

        const ta = document.getElementById('rim-reason');
        if (ta) {
            ta.value = '';
            ta.style.borderColor = 'var(--border)';
            ta.style.boxShadow = 'none';
            ta.onfocus = () => {
                ta.style.borderColor = accent;
                ta.style.boxShadow = `0 0 0 3px ${isVO ? 'rgba(99,102,241,0.1)' :
                    isSO ? 'rgba(217,119,6,0.1)' :
                        'rgba(249,115,22,0.1)'
                    }`;
            };
            ta.onblur = () => { ta.style.borderColor = 'var(--border)'; ta.style.boxShadow = 'none'; };
        }

        // Update the info note color to match type
        const infoNote = document.querySelector('#reason-intercept-modal [style*="border-left"]');
        if (infoNote) {
            infoNote.style.borderLeftColor = accent;
            infoNote.style.background = isVO
                ? 'rgba(99,102,241,0.04)'
                : isSO
                    ? 'rgba(217,119,6,0.04)'
                    : 'rgba(249,115,22,0.04)';
            const noteIcon = infoNote.querySelector('i');
            if (noteIcon) noteIcon.style.color = accent;
        }

        document.getElementById('rim-char-count').textContent = '0';
        document.getElementById('rim-error').style.display = 'none';

        openModal('reason-intercept-modal');
        setTimeout(() => ta?.focus(), 150);
    }

    window.rimCancel = function () {
        closeModal('reason-intercept-modal');
        window._rimType = null;

        // Clear reason values
        if (window._pendingForm) {
            const teH = window._pendingForm.querySelector('[name="new_te_reason"]');
            const voH = window._pendingForm.querySelector('[name="new_vo_reason"]');
            const soH = window._pendingForm.querySelector('[name="new_so_reason"]');
            if (teH) teH.value = '';
            if (voH) voH.value = '';
            if (soH) soH.value = '';
        }
    };

    window.rimConfirm = function () {
        const reason = document.getElementById('rim-reason')?.value.trim();
        const errEl = document.getElementById('rim-error');
        const ta = document.getElementById('rim-reason');

        if (!reason) {
            errEl.style.display = 'flex';
            ta.style.borderColor = '#ef4444';
            ta.style.boxShadow = '0 0 0 3px rgba(239,68,68,0.1)';
            ta.focus();
            return;
        }

        // Use the stored form reference instead of searching for it
        const form = window._pendingForm;
        if (!form) {
            console.error('Form reference lost - unable to submit');
            return;
        }

        // Set the appropriate reason field based on type
        if (window._rimType === 'te') {
            const input = form.querySelector('[name="new_te_reason"]');
            if (input) input.value = reason;
        } else if (window._rimType === 'vo') {
            const input = form.querySelector('[name="new_vo_reason"]');
            if (input) input.value = reason;
        } else if (window._rimType === 'so') {
            const input = form.querySelector('[name="new_so_reason"]');
            if (input) input.value = reason;
        }

        closeModal('reason-intercept-modal');
        window._rimType = null;

        // Set bypass flag so the submit event listener doesn't re-trigger the modal
        window._rimBypassIntercept = true;

        // Submit the form - this will pass through the event listener without modal re-appearing
        form.submit();
    };

    // ── Init all amount inputs on page load ──
    document.querySelectorAll('[data-amount]').forEach(window.initAmountInput);

    const originalAmtInput = document.getElementById('original_contract_amount');
    if (originalAmtInput) {
        originalAmtInput.addEventListener('input', function () {
            window.calculateLDPerDay();
            window.calcAdvanceRetention();
        });
    }

});