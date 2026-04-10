/**
 * show.js — resources/js/admin/projects/show.js
 */

/* ── Theme toggle ── */
function initTheme() {
    const saved = localStorage.getItem('theme-mode');
    if (saved === 'dark') {
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark');
    }
    updateThemeBtn(document.documentElement.classList.contains('dark') ? 'dark' : 'light');
}

function updateThemeBtn(t) {
    const icon = document.getElementById('themeIcon');
    const label = document.getElementById('themeLabel');
    if (icon) icon.className = 'fas ' + (t === 'dark' ? 'fa-moon' : 'fa-sun');
    if (label) label.textContent = t === 'dark' ? 'Dark' : 'Light';
}

window.toggleTheme = function () {
    const isDark = document.documentElement.classList.toggle('dark');
    document.body.classList.toggle('dark', isDark);
    localStorage.setItem('theme-mode', isDark ? 'dark' : 'light');
    updateThemeBtn(isDark ? 'dark' : 'light');
};

/* ── Show page tab switcher ── */
window.switchShowTab = function (tabId, btnEl) {
    /* hide all panels */
    document.querySelectorAll('.show-tab-panel').forEach(p => p.style.display = 'none');
    /* deactivate all buttons */
    document.querySelectorAll('.show-tab-btn').forEach(b => b.classList.remove('active'));
    /* show target panel */
    const panel = document.getElementById('show-tab-' + tabId);
    if (panel) panel.style.display = 'block';
    /* activate clicked button */
    btnEl.classList.add('active');
    /* persist selection */
    localStorage.setItem('show-tab', tabId);
};

/* Restore last active tab on page load */
document.addEventListener('DOMContentLoaded', function () {
    const saved = localStorage.getItem('show-tab') || 'overview';
    const btn = document.querySelector('.show-tab-btn[data-tab="' + saved + '"]');
    if (btn) {
        switchShowTab(saved, btn);
    } else {
        /* fallback: activate overview */
        const fallbackBtn = document.querySelector('.show-tab-btn[data-tab="overview"]');
        if (fallbackBtn) switchShowTab('overview', fallbackBtn);
    }
});

/* ── Activity log entry toggle ── */
window.toggleLog = function (id) {
    const el = document.getElementById(id);
    const ch = document.getElementById(id + '-chevron');
    const open = el.style.display === 'flex';
    el.style.display = open ? 'none' : 'flex';
    if (ch) ch.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
};

/* ── Billing inner tab toggle ── */
window.toggleBillingTab = function (tab) {
    const summaryContent = document.getElementById('billing-tab-summary-content');
    const tableContent = document.getElementById('billing-tab-table-content');
    const summaryBtn = document.getElementById('billing-tab-summary');
    const tableBtn = document.getElementById('billing-tab-table');
    if (!summaryContent) return;

    if (tab === 'summary') {
        summaryContent.style.display = 'block';
        tableContent.style.display = 'none';
        summaryBtn.style.borderBottomColor = '#16a34a';
        summaryBtn.style.color = 'var(--tx)';
        tableBtn.style.borderBottomColor = 'transparent';
        tableBtn.style.color = 'var(--tx2)';
    } else {
        summaryContent.style.display = 'none';
        tableContent.style.display = 'block';
        summaryBtn.style.borderBottomColor = 'transparent';
        summaryBtn.style.color = 'var(--tx2)';
        tableBtn.style.borderBottomColor = '#16a34a';
        tableBtn.style.color = 'var(--tx)';
    }
};

/* ── Init ── */
initTheme();