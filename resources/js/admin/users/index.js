// ── Create modal: toggle division field based on role ──
window.toggleCreateDivision = function() {
    const role = document.getElementById('create-role-select').value;
    const wrap = document.getElementById('create-division-wrap');
    wrap.style.display = role === 'admin' ? 'block' : 'none';
};

// ── Edit modal: toggle division field based on role ──
window.toggleEditDivision = function() {
    const role = document.getElementById('edit-role-select').value;
    const wrap = document.getElementById('edit-division-wrap');
    wrap.style.display = role === 'admin' ? 'block' : 'none';
};

// ── Open edit modal and populate fields ──
window.openEditModal = function(userId, name, email, role, division) {
    document.getElementById('edit-name').value = name;
    document.getElementById('edit-email').value = email;

    const roleSelect = document.getElementById('edit-role-select');
    roleSelect.value = role;

    const divSelect = document.getElementById('edit-division-select');

    // Show/hide division field
    window.toggleEditDivision();

    // Set the division select value
    if (role === 'admin') {
        if (!division || division === '') {
            divSelect.value = 'super';
        } else {
            divSelect.value = division;
        }
    }

    // Set form action to the correct update route
    const form = document.getElementById('edit-user-form');
    form.action = '/admin/users/' + userId;

    window.openModal('edit-user-modal');
};

// ── Open delete confirmation modal ──
window.openDeleteModal = function(userId, name) {
    document.getElementById('delete-user-name').textContent = '"' + name + '"';
    document.getElementById('delete-user-form').action = '/admin/users/' + userId;
    window.openModal('delete-user-modal');
};