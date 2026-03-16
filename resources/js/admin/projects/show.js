/**
 * show.js
 * JavaScript for: resources/views/admin/projects/show.blade.php
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
    const icon  = document.getElementById('themeIcon');
    const label = document.getElementById('themeLabel');
    if (icon)  icon.className    = 'fas ' + (t === 'dark' ? 'fa-moon' : 'fa-sun');
    if (label) label.textContent = t === 'dark' ? 'Dark' : 'Light';
}

window.toggleTheme = function () {
    const isDark = document.documentElement.classList.toggle('dark');
    document.body.classList.toggle('dark', isDark);
    localStorage.setItem('theme-mode', isDark ? 'dark' : 'light');
    updateThemeBtn(isDark ? 'dark' : 'light');
};

/* ── Activity log toggle ── */
window.toggleLog = function (id) {
    const el   = document.getElementById(id);
    const ch   = document.getElementById(id + '-chevron');
    const open = el.style.display === 'flex';
    el.style.display   = open ? 'none' : 'flex';
    ch.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
};

/* ── LD tab toggle ── */
window.toggleLDTab = function (tab) {
    const viewContent   = document.getElementById('ld-tab-view-content');
    const updateContent = document.getElementById('ld-tab-update-content');
    const viewBtn       = document.getElementById('ld-tab-view');
    const updateBtn     = document.getElementById('ld-tab-update');
    if (!viewContent) return;

    if (tab === 'view') {
        viewContent.style.display         = 'block';
        updateContent.style.display       = 'none';
        viewBtn.style.borderBottomColor   = '#f97316';
        viewBtn.style.color               = 'var(--tx)';
        updateBtn.style.borderBottomColor = 'transparent';
        updateBtn.style.color             = 'var(--tx2)';
    } else {
        viewContent.style.display         = 'none';
        updateContent.style.display       = 'block';
        viewBtn.style.borderBottomColor   = 'transparent';
        viewBtn.style.color               = 'var(--tx2)';
        updateBtn.style.borderBottomColor = '#f97316';
        updateBtn.style.color             = 'var(--tx)';
    }
};

/* ── Billing tab toggle ── */
window.toggleBillingTab = function (tab) {
    const summaryContent = document.getElementById('billing-tab-summary-content');
    const tableContent   = document.getElementById('billing-tab-table-content');
    const summaryBtn     = document.getElementById('billing-tab-summary');
    const tableBtn       = document.getElementById('billing-tab-table');
    if (!summaryContent) return;

    if (tab === 'summary') {
        summaryContent.style.display       = 'block';
        tableContent.style.display         = 'none';
        summaryBtn.style.borderBottomColor = '#16a34a';
        summaryBtn.style.color             = 'var(--tx)';
        tableBtn.style.borderBottomColor   = 'transparent';
        tableBtn.style.color               = 'var(--tx2)';
    } else {
        summaryContent.style.display       = 'none';
        tableContent.style.display         = 'block';
        summaryBtn.style.borderBottomColor = 'transparent';
        summaryBtn.style.color             = 'var(--tx2)';
        tableBtn.style.borderBottomColor   = '#16a34a';
        tableBtn.style.color               = 'var(--tx)';
    }
};

/* ── Init on load ── */
initTheme();