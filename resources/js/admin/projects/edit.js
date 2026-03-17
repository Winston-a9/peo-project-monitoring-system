
/* ── Tab switching ── */
window.switchTab = function (tabId, btnElement) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('tab-active'));
    document.getElementById(tabId).classList.add('active');
    btnElement.classList.add('tab-active');
};

/* ── Accordion toggle ── */
window.toggleAcc = function (id) {
    const hdr  = document.getElementById('acc-' + id + '-hdr');
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

/* ── Slippage calculator ── */
window.computeSlippage = function () {
    const ap    = parseFloat(document.getElementById('as_planned').value);
    const wd    = parseFloat(document.getElementById('work_done').value);
    const lbl   = document.getElementById('slippage_label');
    const valEl = document.getElementById('slippage-value');

    document.getElementById('ap_bar').style.width = Math.min(ap || 0, 100) + '%';
    document.getElementById('wd_bar').style.width = Math.min(wd || 0, 100) + '%';

    if (isNaN(ap) || isNaN(wd)) { valEl.textContent = '—'; return; }

    // ✅ parseFloat fixes string comparison bug from toFixed()
    const sl = parseFloat((wd - ap).toFixed(3));
    document.getElementById('slippage').value = sl;

    if (sl > 0)      { lbl.style.color = '#16a34a'; lbl.innerHTML = '<i class="fas fa-arrow-up"></i> Ahead';    valEl.style.color = '#16a34a'; }
    else if (sl < 0) { lbl.style.color = '#dc2626'; lbl.innerHTML = '<i class="fas fa-arrow-down"></i> Behind'; valEl.style.color = '#dc2626'; }
    else             { lbl.style.color = '#9ca3af'; lbl.innerHTML = '<i class="fas fa-minus"></i> On schedule'; valEl.style.color = '#9ca3af'; }

    valEl.textContent = (sl > 0 ? '+' : '') + sl + '%';
};

/* ── LD calculator ── */
function fmtNum(n, decimals) {
    return n.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
}

// LD PER DAY CALCULATOR
window.calculateLDPerDay = function () {
    const acc      = parseFloat(document.getElementById('ld_accomplished').value) || 0;
    const amt      = parseFloat(document.getElementById('contract_amount').value.replace(/,/g, '')) || 0;
    const unworked = Math.max(0, 100 - acc);
    const perDay   = (unworked / 100) * amt * 0.001;

    document.getElementById('ld_unworked').value = unworked.toFixed(3);
    document.getElementById('ld_per_day').value  = perDay.toFixed(3);

    const unworkedDisplay = document.getElementById('ld_unworked_display');
    const perDayDisplay   = document.getElementById('ld_per_day_display');
    if (unworkedDisplay) unworkedDisplay.textContent = fmtNum(unworked, 3);
    if (perDayDisplay)   perDayDisplay.textContent   = fmtNum(perDay, 3);

    window.calculateLDTotal();
};

// LD TOTAL CALCULATOR
window.calculateLDTotal = function () {
    const perDay  = parseFloat(document.getElementById('ld_per_day').value)            || 0;
    const overdue = parseFloat(document.getElementById('ld_days_overdue_input').value) || 0;
    const total   = perDay * overdue;

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

// TIME EXTENSION EXPIRY PREVIEW
window.updateTEPreview = function () {
    const newDays = parseInt(document.getElementById('new_te_days').value) || 0;
    const preview = document.getElementById('te_revised_preview');
    if (newDays < 1) { preview.textContent = 'Enter days above to preview'; return; }
    preview.textContent = formatDate(addDaysToDate(originalExpiry, existingTEDays + existingVODays + existingSODays + newDays));
};

// VARIATION ORDER EXPIRY PREVIEW
window.updateVOPreview = function () {
    const newDays = parseInt(document.getElementById('new_vo_days').value) || 0;
    const preview = document.getElementById('vo_revised_preview');
    if (newDays < 1) { preview.textContent = 'Enter days above to preview'; return; }
    preview.textContent = formatDate(addDaysToDate(originalExpiry, existingTEDays + existingVODays + existingSODays + newDays));
};

// SUSPENSION ORDER EXPIRY PREVIEW
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

// ISSUANCE ROW HTML BUILDER
window.issuanceRowHTML = function (val = '') {
    let opts = '<option value="">— Select Issuance —</option>';
    ISSUANCE_OPTS.forEach(o => opts += `<option value="${o}" ${o === val ? 'selected' : ''}>${o}</option>`);
    return `<div class="dynamic-row">
        <select name="issuances[]" class="dynamic-select" onchange="updateCount('issuances-list','issuance-count'); checkPerformanceBond()">${opts}</select>
        <button type="button" class="remove-btn" onclick="removeIssuanceRow(this)"><i class="fas fa-times"></i></button>
    </div>`;
};

// ADD ISSUANCE ROW
window.addIssuanceRow = function () {
    document.getElementById('issuances-list').insertAdjacentHTML('beforeend', window.issuanceRowHTML());
    window.updateCount('issuances-list', 'issuance-count');
};

// REMOVE ISSUANCE ROW
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

// UPDATE ISSUANCE COUNT BADGE
window.updateCount = function (listId, countId) {
    const filled = [...document.getElementById(listId).querySelectorAll('select')]
        .filter(s => s.value !== '').length;
    const chip = document.getElementById(countId);
    chip.textContent       = filled;
    chip.style.background  = filled > 0 ? 'rgba(249,115,22,0.15)' : 'rgba(249,115,22,0.07)';
    chip.style.color       = filled > 0 ? '#ea580c' : '#9ca3af';
    chip.style.borderColor = filled > 0 ? 'rgba(249,115,22,0.3)' : 'rgba(249,115,22,0.15)';
};

/* ── Delete entry modal ── */
window.openDeleteModal = function (type, index, label, days) {
    window._delType  = type;
    window._delIndex = index;
    const isVO = type === 'vo';

    document.getElementById('del-entry-icon').className    = 'fas ' + (isVO ? 'fa-file-signature' : 'fa-clock');
    document.getElementById('del-entry-icon').style.color  = '#dc2626';
    document.getElementById('del-entry-label').textContent = label;
    document.getElementById('del-entry-days').textContent  = '+' + days + 'd';

    const titleEl = document.getElementById('delete-entry-modal-title');
    if (titleEl) titleEl.textContent = 'Delete ' + label;

    const totalOfType = isVO ? _totalVOCount : _totalTECount;
    const notice      = document.getElementById('del-renumber-notice');
    if (totalOfType > 1) {
        notice.style.display = 'flex';
        document.getElementById('del-renumber-text').innerHTML =
            'Remaining <strong style="color:var(--text-primary);">' +
            (isVO ? 'Variation Orders' : 'Time Extensions') +
            '</strong> will be renumbered from <strong style="color:var(--text-primary);">1</strong>.';
    } else {
        notice.style.display = 'none';
    }

    const textarea             = document.getElementById('del-reason-input');
    textarea.value             = '';
    textarea.style.borderColor = 'var(--border)';
    textarea.style.boxShadow   = 'none';
    document.getElementById('del-reason-count').textContent   = '0';
    document.getElementById('del-reason-error').style.display = 'none';

    const btn     = document.getElementById('del-confirm-btn');
    btn.innerHTML = '<i class="fas fa-trash-alt" style="font-size:0.75rem;"></i> Delete Entry';
    btn.disabled  = false;
    btn.style.opacity = '1';

    openModal('delete-entry-modal');
};

// CLEAR DELETE ERROR STATE
window.delClearError = function () {
    document.getElementById('del-reason-error').style.display     = 'none';
    document.getElementById('del-reason-input').style.borderColor = 'var(--border)';
};

/* ── Billing preview ── */
window.updateBillingPreview = function () {
    const input       = document.getElementById('new_billing_amount');
    const totalEl     = document.getElementById('billing_total_preview');
    const remainingEl = document.getElementById('billing_remaining_val');
    const previewP    = document.getElementById('billing_remaining_preview');
    if (!input || !totalEl || !remainingEl) return;

    const newAmt      = parseFloat(input.value) || 0;
    const base        = parseFloat(totalEl.dataset.base) || 0;
    const contractAmt = parseFloat(document.getElementById('contract_amount')?.value) || 0;
    const newTotal    = base + newAmt;
    const newRemain   = contractAmt - newTotal;

    totalEl.textContent     = newTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

    const issuancesList = document.getElementById('issuances-list');
    if (issuancesList) window.updateCount('issuances-list', 'issuance-count');

    const ldAccomplished = document.getElementById('ld_accomplished');
    if (ldAccomplished) window.calculateLDPerDay();

    window.checkPerformanceBond();
    // Init billing preview base value
    const billingTotalEl = document.getElementById('billing_total_preview');
    if (billingTotalEl && !billingTotalEl.dataset.base) {
        billingTotalEl.dataset.base = billingTotalEl.textContent.replace(/,/g, '');
    }
});