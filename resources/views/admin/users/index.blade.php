<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 style="font-family:'Syne',sans-serif; font-weight:800; font-size:1.6rem; letter-spacing:-0.03em; color:var(--text-primary); display:flex; align-items:center; gap:0.6rem;">
                    <span style="background:#f97316; width:34px; height:34px; border-radius:9px; display:inline-flex; align-items:center; justify-content:center; box-shadow:0 2px 10px rgba(249,115,22,0.35);">
                        <i class="fas fa-users-cog" style="color:white; font-size:0.85rem;"></i>
                    </span>
                    User Management
                </h2>
                <p style="color:var(--text-secondary); font-size:0.82rem; margin-top:3px;">
                    Manage all system users and their division access
                </p>
            </div>
            <div>
                <button onclick="openModal('create-user-modal')"
                    style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.65rem 1.25rem; background:#f97316; color:white; border:none; border-radius:9px; font-size:0.855rem; font-weight:700; cursor:pointer; font-family:'Instrument Sans',sans-serif; box-shadow:0 2px 10px rgba(249,115,22,0.35); transition:all 0.2s;"
                    onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='#f97316'">
                    <i class="fas fa-user-plus" style="font-size:0.8rem;"></i> Add New User
                </button>
            </div>
        </div>
    </x-slot>

    @push('styles')
        @vite('resources/css/admin/users/index.css')
    @endpush

    @php
        $divisions = [
            'First District Engineering Office',
            'Second District Engineering Office',
            'Third District Engineering Office',
            'Fourth District Engineering Office',
            'Fifth District Engineering Office',
        ];

        $totalUsers       = $users->where('role', 'user')->count();
        $totalAdmins      = $users->where('role', 'admin')->count();
        $totalSuperAdmins = $users->where('role', 'admin')->whereNull('division')->count();
        $totalDivAdmins   = $users->where('role', 'admin')->whereNotNull('division')->count();
    @endphp

    <div class="space-y-5 fade-up">

        {{-- ── Flash messages ── --}}
        @if(session('success'))
            <div style="background:rgba(22,163,74,0.08); border:1px solid rgba(22,163,74,0.25); border-radius:10px; padding:0.9rem 1.1rem; display:flex; align-items:center; gap:0.6rem;">
                <i class="fas fa-check-circle" style="color:#16a34a;"></i>
                <span style="font-size:0.875rem; font-weight:600; color:#15803d;">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.22); border-radius:10px; padding:0.9rem 1.1rem; display:flex; align-items:center; gap:0.6rem;">
                <i class="fas fa-exclamation-circle" style="color:#dc2626;"></i>
                <span style="font-size:0.875rem; font-weight:600; color:#dc2626;">{{ session('error') }}</span>
            </div>
        @endif

        {{-- ── Stat cards ── --}}
        <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:1rem;">
            @foreach([
                ['Total Users',       $users->count(),   'fa-users',         '#f97316', 'rgba(249,115,22,0.1)'],
                ['Regular Users',     $totalUsers,       'fa-user',          '#3b82f6', 'rgba(59,130,246,0.1)'],
                ['Super Admins',      $totalSuperAdmins, 'fa-user-shield',   '#f97316', 'rgba(249,115,22,0.1)'],
                ['Division Admins',   $totalDivAdmins,   'fa-user-tie',      '#6366f1', 'rgba(99,102,241,0.1)'],
            ] as [$label,$val,$icon,$color,$bg])
            <div style="background:var(--bg-primary); border:1px solid var(--border); border-radius:14px; padding:1.25rem 1.4rem;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:0.5rem;">
                    <div>
                        <p style="font-size:0.63rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; color:var(--text-secondary); margin-bottom:0.5rem;">{{ $label }}</p>
                        <p style="font-family:'Syne',sans-serif; font-size:2.4rem; font-weight:800; letter-spacing:-0.04em; line-height:1; color:var(--text-primary);">{{ $val }}</p>
                    </div>
                    <div style="width:38px; height:38px; background:{{ $bg }}; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas {{ $icon }}" style="color:{{ $color }}; font-size:1rem;"></i>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ── Users table ── --}}
        <div class="users-card">
            <div style="padding:1.1rem 1.5rem; border-bottom:1px solid var(--border); background:var(--bg-secondary); display:flex; align-items:center; gap:0.5rem;">
                <i class="fas fa-list" style="color:#f97316; font-size:0.82rem;"></i>
                <span style="font-family:'Syne',sans-serif; font-weight:700; font-size:0.875rem; color:var(--text-primary);">All Users</span>
                <span style="margin-left:auto; font-size:0.72rem; color:var(--text-secondary);">{{ $users->count() }} total</span>
            </div>

            <div style="overflow-x:auto;">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Division</th>
                            <th>Joined</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            {{-- Name --}}
                            <td>
                                <div style="display:flex; align-items:center; gap:0.6rem;">
                                    <div style="width:32px; height:32px; border-radius:9px; background:linear-gradient(135deg,#f97316,#ea580c); display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-weight:800; font-size:0.78rem; color:white; flex-shrink:0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p style="font-weight:700; color:var(--text-primary); font-size:0.855rem;">{{ $user->name }}</p>
                                        @if($user->id === auth()->id())
                                            <p style="font-size:0.65rem; color:#f97316; font-weight:600;">You</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Email --}}
                            <td style="color:var(--text-secondary);">{{ $user->email }}</td>

                            {{-- Role badge --}}
                            <td>
                                @if($user->role === 'admin' && $user->division === null)
                                    <span class="badge badge-super">
                                        <i class="fas fa-shield-alt" style="font-size:0.55rem;"></i> Super Admin
                                    </span>
                                @elseif($user->role === 'admin')
                                    <span class="badge badge-division-admin">
                                        <i class="fas fa-user-tie" style="font-size:0.55rem;"></i> Division Admin
                                    </span>
                                @else
                                    <span class="badge badge-user">
                                        <i class="fas fa-user" style="font-size:0.55rem;"></i> User
                                    </span>
                                @endif
                            </td>

                            {{-- Division --}}
                            <td>
                                @if($user->division)
                                    <span class="division-pill">
                                        <i class="fas fa-building" style="font-size:0.55rem;"></i>
                                        {{ $user->division }}
                                    </span>
                                @elseif($user->role === 'admin')
                                    <span style="font-size:0.72rem; color:#f97316; font-weight:600;">
                                        <i class="fas fa-globe" style="font-size:0.6rem;"></i> All Divisions
                                    </span>
                                @else
                                    <span style="color:#9ca3af; font-size:0.78rem;">—</span>
                                @endif
                            </td>

                            {{-- Joined date --}}
                            <td style="font-size:0.78rem; white-space:nowrap;">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div style="display:flex; align-items:center; gap:0.4rem; justify-content:flex-end;">
                                    @if($user->id !== auth()->id())
                                        <button
                                            class="action-btn action-btn-edit"
                                            onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}', '{{ $user->role }}', '{{ $user->division ?? '' }}')"
                                        >
                                            <i class="fas fa-pen" style="font-size:0.6rem;"></i> Edit
                                        </button>
                                        <button
                                            class="action-btn action-btn-delete"
                                            onclick="openDeleteModal({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                        >
                                            <i class="fas fa-trash" style="font-size:0.6rem;"></i> Delete
                                        </button>
                                    @else
                                        <span style="font-size:0.72rem; color:#9ca3af; font-style:italic;">Current account</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:3rem;">
                                <p style="color:#9ca3af; font-size:0.875rem;">No users found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         CREATE USER MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal id="create-user-modal" title="Add New User" type="default" icon="fa-user-plus" size="md">
        <form id="create-user-form" method="POST" action="{{ route('admin.users.store') }}" style="display:contents;">
            @csrf
            <div style="display:flex; flex-direction:column; gap:1.1rem;">

                {{-- Name --}}
                <div>
                    <label class="modal-field-label">Full Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" class="modal-field-input" placeholder="e.g. Juan dela Cruz" required value="{{ old('name') }}">
                    @error('name') <p class="modal-field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="modal-field-label">Email Address <span style="color:#ef4444;">*</span></label>
                    <input type="email" name="email" class="modal-field-input" placeholder="user@example.com" required value="{{ old('email') }}">
                    @error('email') <p class="modal-field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label class="modal-field-label">Role <span style="color:#ef4444;">*</span></label>
                    <select name="role" id="create-role-select" class="modal-field-select" onchange="toggleCreateDivision()" required>
                        <option value="">— Select Role —</option>
                        <option value="user"  {{ old('role') === 'user'  ? 'selected' : '' }}>Regular User (View Only)</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    @error('role') <p class="modal-field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                </div>

                {{-- Division (only shown when role = admin) --}}
                <div id="create-division-wrap" style="display:none;">
                    <label class="modal-field-label">Division / Access Level <span style="color:#ef4444;">*</span></label>
                    <select name="division" id="create-division-select" class="modal-field-select">
                        <option value="super">Super Admin — Access All Divisions</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div }}" {{ old('division') === $div ? 'selected' : '' }}>{{ $div }}</option>
                        @endforeach
                    </select>
                    <p style="font-size:0.68rem; color:#9ca3af; margin-top:0.35rem;">
                        <i class="fas fa-info-circle"></i>
                        Super Admin can see all projects. Division Admin only sees their division's projects.
                    </p>
                    @error('division') <p class="modal-field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label class="modal-field-label">Password <span style="color:#ef4444;">*</span></label>
                    <input type="password" name="password" class="modal-field-input" placeholder="Minimum 8 characters" required>
                    @error('password') <p class="modal-field-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</p> @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="modal-field-label">Confirm Password <span style="color:#ef4444;">*</span></label>
                    <input type="password" name="password_confirmation" class="modal-field-input" placeholder="Repeat password" required>
                </div>

            </div>
        </form>

        <x-slot name="footer">
            <button type="button" onclick="closeModal('create-user-modal')"
                style="padding:0.6rem 1.2rem; border:1.5px solid var(--border); border-radius:9px; background:var(--bg-primary); color:var(--text-secondary); font-weight:600; font-size:0.85rem; cursor:pointer; font-family:'Instrument Sans',sans-serif;"
                onmouseover="this.style.borderColor='#f97316'" onmouseout="this.style.borderColor='var(--border)'">
                Cancel
            </button>
            <button type="button" onclick="document.getElementById('create-user-form').submit()"
                style="padding:0.6rem 1.4rem; background:#f97316; color:white; border:none; border-radius:9px; font-weight:700; font-size:0.85rem; cursor:pointer; font-family:'Instrument Sans',sans-serif; box-shadow:0 2px 8px rgba(249,115,22,0.3);"
                onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='#f97316'">
                <i class="fas fa-user-plus" style="font-size:0.75rem; margin-right:0.3rem;"></i> Create User
            </button>
        </x-slot>
    </x-modal>

    {{-- ══════════════════════════════════════════════════
         EDIT USER MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal id="edit-user-modal" title="Edit User" type="default" icon="fa-user-edit" size="md">
        <form id="edit-user-form" method="POST" action="" style="display:contents;">
            @csrf
            @method('PATCH')
            <div style="display:flex; flex-direction:column; gap:1.1rem;">

                <div>
                    <label class="modal-field-label">Full Name <span style="color:#ef4444;">*</span></label>
                    <input type="text" name="name" id="edit-name" class="modal-field-input" required>
                </div>

                <div>
                    <label class="modal-field-label">Email Address <span style="color:#ef4444;">*</span></label>
                    <input type="email" name="email" id="edit-email" class="modal-field-input" required>
                </div>

                <div>
                    <label class="modal-field-label">Role <span style="color:#ef4444;">*</span></label>
                    <select name="role" id="edit-role-select" class="modal-field-select" onchange="toggleEditDivision()" required>
                        <option value="user">Regular User (View Only)</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <div id="edit-division-wrap" style="display:none;">
                    <label class="modal-field-label">Division / Access Level</label>
                    <select name="division" id="edit-division-select" class="modal-field-select">
                        <option value="super">Super Admin — Access All Divisions</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div }}">{{ $div }}</option>
                        @endforeach
                    </select>
                    <p style="font-size:0.68rem; color:#9ca3af; margin-top:0.35rem;">
                        <i class="fas fa-info-circle"></i>
                        Super Admin can see all projects. Division Admin only sees their division's projects.
                    </p>
                </div>

                {{-- New password (optional) --}}
                <div style="padding:0.875rem 1rem; border-radius:10px; background:var(--bg-secondary); border:1px solid var(--border);">
                    <p style="font-size:0.72rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-secondary); margin-bottom:0.75rem;">
                        <i class="fas fa-lock" style="color:#f97316; margin-right:0.3rem;"></i> Change Password (optional)
                    </p>
                    <div style="display:flex; flex-direction:column; gap:0.75rem;">
                        <input type="password" name="password" class="modal-field-input" placeholder="Leave blank to keep current password">
                        <input type="password" name="password_confirmation" class="modal-field-input" placeholder="Confirm new password">
                    </div>
                </div>

            </div>
        </form>

        <x-slot name="footer">
            <button type="button" onclick="closeModal('edit-user-modal')"
                style="padding:0.6rem 1.2rem; border:1.5px solid var(--border); border-radius:9px; background:var(--bg-primary); color:var(--text-secondary); font-weight:600; font-size:0.85rem; cursor:pointer; font-family:'Instrument Sans',sans-serif;"
                onmouseover="this.style.borderColor='#f97316'" onmouseout="this.style.borderColor='var(--border)'">
                Cancel
            </button>
            <button type="button" onclick="document.getElementById('edit-user-form').submit()"
                style="padding:0.6rem 1.4rem; background:#f97316; color:white; border:none; border-radius:9px; font-weight:700; font-size:0.85rem; cursor:pointer; font-family:'Instrument Sans',sans-serif; box-shadow:0 2px 8px rgba(249,115,22,0.3);"
                onmouseover="this.style.background='#ea580c'" onmouseout="this.style.background='#f97316'">
                <i class="fas fa-save" style="font-size:0.75rem; margin-right:0.3rem;"></i> Save Changes
            </button>
        </x-slot>
    </x-modal>

    {{-- ══════════════════════════════════════════════════
         DELETE CONFIRM MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal id="delete-user-modal" title="Delete User" type="danger" icon="fa-trash" size="sm">
        <div style="display:flex; flex-direction:column; gap:1rem;">
            <div style="display:flex; align-items:center; gap:0.75rem; padding:0.875rem 1rem; background:rgba(239,68,68,0.06); border:1px solid rgba(239,68,68,0.18); border-radius:10px;">
                <i class="fas fa-exclamation-triangle" style="color:#dc2626; font-size:1rem; flex-shrink:0;"></i>
                <div>
                    <p style="font-size:0.82rem; font-weight:700; color:#dc2626; margin-bottom:2px;">This action cannot be undone.</p>
                    <p style="font-size:0.78rem; color:#ef4444;">
                        You are about to permanently delete <strong id="delete-user-name" style="color:#dc2626;"></strong>.
                    </p>
                </div>
            </div>
        </div>

        <form id="delete-user-form" method="POST" action="" style="display:none;">
            @csrf
            @method('DELETE')
        </form>

        <x-slot name="footer">
            <button type="button" onclick="closeModal('delete-user-modal')"
                style="padding:0.6rem 1.2rem; border:1.5px solid var(--border); border-radius:9px; background:var(--bg-primary); color:var(--text-secondary); font-weight:600; font-size:0.85rem; cursor:pointer; font-family:'Instrument Sans',sans-serif;">
                Cancel
            </button>
            <button type="button" onclick="document.getElementById('delete-user-form').submit()"
                style="padding:0.6rem 1.4rem; background:#dc2626; color:white; border:none; border-radius:9px; font-weight:700; font-size:0.85rem; cursor:pointer; font-family:'Instrument Sans',sans-serif; box-shadow:0 2px 8px rgba(220,38,38,0.3);"
                onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                <i class="fas fa-trash" style="font-size:0.75rem; margin-right:0.3rem;"></i> Yes, Delete
            </button>
        </x-slot>
    </x-modal>

    @push('scripts')
        @vite('resources/js/admin/users/index.js')
        
        {{-- Auto-open create modal if there were validation errors --}}
        @if ($errors->any() && old('_method') === null && old('name'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    openModal('create-user-modal');
                    toggleCreateDivision();
                });
            </script>
        @endif
    @endpush
</x-app-layout>