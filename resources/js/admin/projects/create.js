// ─────────────────────────────────────────────
// INITIALIZATION
// Runs all setup functions once the page is ready
// ─────────────────────────────────────────────
/* ── Contract Amount formatter ── */
window.formatContractAmount = function (el) {
    let raw = el.value.replace(/,/g, '').replace(/[^0-9.]/g, '');

    // Prevent multiple dots
    const parts = raw.split('.');
    if (parts.length > 2) raw = parts[0] + '.' + parts.slice(1).join('');

    // Format whole part with commas
    const [intPart, decPart] = raw.split('.');
    const formatted = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    el.value = decPart !== undefined ? formatted + '.' + decPart : formatted;

    // Sync raw value to hidden input
    document.getElementById('original_contract_amount_raw').value = raw;
};

document.addEventListener('DOMContentLoaded', function () {
    liveSlippage();          // Set initial slippage display state
    calculateOriginalExpiry(); // Calculate expiry if old() values are present
    toggleCompletedAt();     // Show/hide the Date Completed field on load
    initContractAmount();     // Format the Contract Amount field on load
});

document.querySelector('form').addEventListener('submit', function () {
    const raw = document.getElementById('original_contract_amount_raw').value;
    document.getElementById('original_contract_amount_raw').value = raw.replace(/,/g, '');
});


// ─────────────────────────────────────────────
// TOGGLE DATE COMPLETED FIELD
// Shows the "Date Completed" input only when
// the Status dropdown is set to "Completed"
// ─────────────────────────────────────────────
window.toggleCompletedAt = function () {
    const sel = document.getElementById('status_sel');
    const field = document.getElementById('completed_at_field');
    if (!sel || !field) return;
    field.classList.toggle('hidden', sel.value !== 'completed');
};


// ─────────────────────────────────────────────
// LIVE SLIPPAGE CALCULATOR
// Triggered on every keystroke in As Planned or Work Done.
// Computes slippage = Work Done - As Planned, then:
//   - Updates the progress bar widths
//   - Updates the hidden slippage input (submitted with form)
//   - Changes the label color and text (Ahead / Behind / On schedule)
// ─────────────────────────────────────────────
window.liveSlippage = function () {
    const ap = parseFloat(document.getElementById('as_planned').value);
    const wd = parseFloat(document.getElementById('work_done').value);

    // Update the visual progress bars
    document.getElementById('ap_bar').style.width = Math.min(ap || 0, 100) + '%';
    document.getElementById('wd_bar').style.width = Math.min(wd || 0, 100) + '%';

    const lbl = document.getElementById('slippage_label');
    const valEl = document.getElementById('slippage-value');
    const display = document.getElementById('slippage-display');

    // If either field is empty/invalid, reset to neutral state
    if (isNaN(ap) || isNaN(wd)) {
        lbl.style.color = '#9ca3af';
        lbl.innerHTML = '<i class="fas fa-minus"></i> Enter values above';
        valEl.textContent = '—';
        valEl.style.color = '#9ca3af';
        display.style.borderColor = 'rgba(26,15,0,0.08)';
        display.style.background = '#fffaf5';
        document.getElementById('slippage').value = 0;
        return;
    }

    // Calculate slippage to 3 decimal places
    const sl = parseFloat((wd - ap).toFixed(3));
    document.getElementById('slippage').value = sl; // Write to hidden input for form submission

    // Color-code the display based on result
    if (sl > 0) {
        lbl.style.color = '#16a34a';  // Green = ahead
        lbl.innerHTML = '<i class="fas fa-arrow-up"></i> Ahead of schedule';
        valEl.style.color = '#16a34a';
        display.style.borderColor = 'rgba(22,163,74,0.2)';
        display.style.background = 'rgba(22,163,74,0.04)';
    } else if (sl < 0) {
        lbl.style.color = '#dc2626';  // Red = behind
        lbl.innerHTML = '<i class="fas fa-arrow-down"></i> Behind schedule';
        valEl.style.color = '#dc2626';
        display.style.borderColor = 'rgba(220,38,38,0.2)';
        display.style.background = 'rgba(220,38,38,0.04)';
    } else {
        lbl.style.color = '#9ca3af';  // Gray = on schedule
        lbl.innerHTML = '<i class="fas fa-minus"></i> On schedule';
        valEl.style.color = '#9ca3af';
        display.style.borderColor = 'rgba(26,15,0,0.08)';
        display.style.background = '#fffaf5';
    }

    // Show the final value e.g. "+2.500%" or "-1.200%"
    valEl.textContent = (sl > 0 ? '+' : '') + sl + '%';
};


// ─────────────────────────────────────────────
// AUTO-CALCULATE ORIGINAL EXPIRY DATE
// Triggered when Date Started or Contract Days changes.
// Formula: Original Expiry = Date Started + Contract Days - 1
// The -1 is because the start date itself counts as day 1.
// Writes the result into the read-only expiry date input.
// ─────────────────────────────────────────────
window.calculateOriginalExpiry = function () {
    const dateStartedInput = document.getElementById('date_started').value;
    const contractDaysInput = document.getElementById('contract_days').value;
    const originalExpiryInput = document.getElementById('original_contract_expiry');

    // Clear the field if either input is missing
    if (!dateStartedInput || !contractDaysInput) {
        originalExpiryInput.value = '';
        return;
    }

    const dateStarted = new Date(dateStartedInput + 'T00:00:00');
    const contractDays = parseInt(contractDaysInput);

    // Clear the field if inputs are invalid
    if (isNaN(dateStarted.getTime()) || isNaN(contractDays) || contractDays < 1) {
        originalExpiryInput.value = '';
        return;
    }

    // Add (contractDays - 1) to the start date
    const originalExpiry = new Date(dateStarted);
    originalExpiry.setDate(originalExpiry.getDate() + contractDays - 1);

    // Format as YYYY-MM-DD for the date input
    const year = originalExpiry.getFullYear();
    const month = String(originalExpiry.getMonth() + 1).padStart(2, '0');
    const day = String(originalExpiry.getDate()).padStart(2, '0');

    originalExpiryInput.value = `${year}-${month}-${day}`;
};
// ─────────────────────────────────────────────
// CONTRACT AMOUNT FORMATTER
// Displays commas every 3 whole digits while keeping
// a clean numeric value in the hidden input for Laravel.
// ─────────────────────────────────────────────
window.formatContractAmount = function (input) {
    // Strip everything except digits and one decimal point
    let raw = input.value.replace(/[^0-9.]/g, '');

    // Prevent multiple decimal points
    const parts = raw.split('.');
    if (parts.length > 2) raw = parts[0] + '.' + parts.slice(1).join('');

    // Limit to 2 decimal places
    const wholePart = (parts[0] || '').replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    const decimalPart = parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '';

    // Update display input with formatted value
    input.value = wholePart + decimalPart;

    // Store clean numeric value in hidden input for form submission
    const cleanRaw = (parts[0] || '') + (parts[1] !== undefined ? '.' + parts[1].slice(0, 2) : '');
    document.getElementById('original_contract_amount_raw').value = cleanRaw;
};

// ─────────────────────────────────────────────
// INIT CONTRACT AMOUNT
// On page load, if there's an old() value (after a
// validation error), re-apply the comma formatting.
// ─────────────────────────────────────────────
window.initContractAmount = function () {
    const display = document.getElementById('original_contract_amount_display');
    const raw = document.getElementById('original_contract_amount_raw');
    if (!display || !raw) return;

    // If there's already a value (from old()), format it
    if (display.value) {
        formatContractAmount(display);
    }

    // Keep raw input in sync if user somehow edits display directly
    display.addEventListener('blur', function () {
        // Pad to 2 decimal places on blur for a cleaner look
        const rawVal = parseFloat(raw.value);
        if (!isNaN(rawVal)) {
            const parts = rawVal.toFixed(2).split('.');
            const wholePart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            display.value = wholePart + '.' + parts[1];
        }
    });
};