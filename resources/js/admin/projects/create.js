document.addEventListener('DOMContentLoaded', function () {
    liveSlippage();
    calculateOriginalExpiry();
    toggleCompletedAt();
});

// ✅ Expose to window so inline oninput= attributes in the blade can call them
window.toggleCompletedAt = function () {
    const sel = document.getElementById('status_sel');
    if (!sel) return;
    document.getElementById('completed_at_field').classList.toggle('hidden', sel.value !== 'completed');
};

window.liveSlippage = function () {
    const ap = parseFloat(document.getElementById('as_planned').value);
    const wd = parseFloat(document.getElementById('work_done').value);

    document.getElementById('ap_bar').style.width = Math.min(ap || 0, 100) + '%';
    document.getElementById('wd_bar').style.width = Math.min(wd || 0, 100) + '%';

    const lbl     = document.getElementById('slippage_label');
    const valEl   = document.getElementById('slippage-value');
    const display = document.getElementById('slippage-display');

    if (isNaN(ap) || isNaN(wd)) {
        lbl.style.color           = '#9ca3af';
        lbl.innerHTML             = '<i class="fas fa-minus"></i> Enter values above';
        valEl.textContent         = '—';
        valEl.style.color         = '#9ca3af';
        display.style.borderColor = 'rgba(26,15,0,0.08)';
        display.style.background  = '#fffaf5';
        document.getElementById('slippage').value = 0;
        return;
    }

    const sl = parseFloat((wd - ap).toFixed(2));
    document.getElementById('slippage').value = sl;

    if (sl > 0) {
        lbl.style.color           = '#16a34a';
        lbl.innerHTML             = '<i class="fas fa-arrow-up"></i> Ahead of schedule';
        valEl.style.color         = '#16a34a';
        display.style.borderColor = 'rgba(22,163,74,0.2)';
        display.style.background  = 'rgba(22,163,74,0.04)';
    } else if (sl < 0) {
        lbl.style.color           = '#dc2626';
        lbl.innerHTML             = '<i class="fas fa-arrow-down"></i> Behind schedule';
        valEl.style.color         = '#dc2626';
        display.style.borderColor = 'rgba(220,38,38,0.2)';
        display.style.background  = 'rgba(220,38,38,0.04)';
    } else {
        lbl.style.color           = '#9ca3af';
        lbl.innerHTML             = '<i class="fas fa-minus"></i> On schedule';
        valEl.style.color         = '#9ca3af';
        display.style.borderColor = 'rgba(26,15,0,0.08)';
        display.style.background  = '#fffaf5';
    }

    valEl.textContent = (sl > 0 ? '+' : '') + sl + '%';
};

window.calculateOriginalExpiry = function () {
    const dateStartedInput    = document.getElementById('date_started').value;
    const contractDaysInput   = document.getElementById('contract_days').value;
    const originalExpiryInput = document.getElementById('original_contract_expiry');

    if (!dateStartedInput || !contractDaysInput) {
        originalExpiryInput.value = '';
        return;
    }

    const dateStarted  = new Date(dateStartedInput + 'T00:00:00');
    const contractDays = parseInt(contractDaysInput);

    if (isNaN(dateStarted.getTime()) || isNaN(contractDays) || contractDays < 1) {
        originalExpiryInput.value = '';
        return;
    }

    const originalExpiry = new Date(dateStarted);
    originalExpiry.setDate(originalExpiry.getDate() + contractDays - 1);

    const year  = originalExpiry.getFullYear();
    const month = String(originalExpiry.getMonth() + 1).padStart(2, '0');
    const day   = String(originalExpiry.getDate()).padStart(2, '0');

    originalExpiryInput.value = `${year}-${month}-${day}`;
};