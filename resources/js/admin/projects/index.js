/**
 * index.js
 * JavaScript for: resources/views/admin/projects/index.blade.php
 */

function changePerPage(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', val);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}

// Search on Enter key
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); this.closest('form').submit(); }
        });
    }

    // Confirm delete button
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', () => {
            if (!pendingDeleteId) return;
            const f = document.createElement('form');
            f.method = 'POST';
            f.action = `/admin/projects/${pendingDeleteId}`;
            // CSRF token from meta tag
            const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            f.innerHTML = `<input type="hidden" name="_token" value="${token}"><input type="hidden" name="_method" value="DELETE">`;
            document.body.appendChild(f);
            f.submit();
        });
    }
});

let pendingDeleteId = null;

function confirmDelete(id, name) {
    pendingDeleteId = id;
    document.getElementById('deleteProjectName').textContent = `"${name}"`;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
    pendingDeleteId = null;
}