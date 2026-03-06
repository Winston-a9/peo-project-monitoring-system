<x-app-layout>
    <x-slot name="header">
<<<<<<< HEAD
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl flex items-center gap-3"
                    style="font-family: 'Syne', sans-serif; letter-spacing: -0.03em; color: #2c3e4f;">
                    <span style="background: #f97316; padding: 0.45rem 0.6rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(249,115,22,0.35);">
                        <i class="fas fa-chart-pie text-white text-base"></i>
                    </span>
                    Admin Dashboard
                </h2>
                <p class="text-sm mt-1" style="color: #6b4f35;">
                    Overview of system performance and project management
                </p>
            </div>
            <div class="flex items-center gap-2 text-sm" style="color: #6b4f35;">
                <i class="fas fa-clock" style="color: #f97316;"></i>
                <span>{{ now()->format('l, F j, Y') }}</span>
            </div>
        </div>
    </x-slot>

    {{-- Inline styles scoped to this view --}}
    <style>
        .dash-card {
            background: white;
            border: 1px solid rgba(249,115,22,0.12);
            border-radius: 14px;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .dash-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 36px rgba(249,115,22,0.1), 0 4px 10px rgba(0,0,0,0.05);
            border-color: rgba(249,115,22,0.25);
        }
        .dark .dash-card {
            background: #1e1610;
            border-color: rgba(249,115,22,0.15);
        }
        .metric-bar {
            height: 4px;
            background: rgba(249,115,22,0.1);
            border-radius: 99px;
            margin-top: 1rem;
            overflow: hidden;
        }
        .metric-bar-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 1s ease;
        }
        .action-btn {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(249,115,22,0.35);
        }
        .action-btn:active { transform: translateY(0); }
        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            animation: pulse-dot 2s infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
    </style>

    <div class="space-y-6">

        {{-- ── WELCOME BANNER ── --}}
        <div style="background: linear-gradient(135deg, rgba(249,115,22,0.08) 0%, rgba(251,146,60,0.06) 100%); border: 1px solid rgba(249,115,22,0.2); border-radius: 14px; padding: 1.25rem 1.5rem;">
            <div class="flex items-center gap-4">
                <div style="width:40px; height:40px; background: rgba(249,115,22,0.12); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas fa-shield-alt" style="color:#f97316; font-size:1rem;"></i>
                </div>
                <div>
                    <p style="font-weight: 700; color: #92400e; font-size: 0.9rem; font-family: 'Syne', sans-serif;">
                        Welcome back, {{ Auth::user()->name }}
                    </p>
                    <p style="color: #b45309; font-size: 0.82rem; margin-top: 2px;">
                        You have full administrative access. Use the sidebar to navigate management areas.
                    </p>
=======
        <h2 class="font-semibold text-3xl bg-gradient-to-r from-orange-600 to-orange-400 bg-clip-text text-transparent dark:from-orange-400 dark:to-orange-300 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Card -->
        <div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Main Welcome Card -->
            <div class="col-span-1 md:col-span-2 bg-gradient-to-br from-orange-50 to-white dark:from-orange-900/20 dark:to-gray-800 overflow-hidden rounded-2xl border-2 border-orange-200 dark:border-orange-700/50 shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="p-8 md:p-12">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                Welcome back, {{ Auth::user()->name }}! 👋
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 max-w-2xl">
                                You have administrator privileges. Manage users, monitor system activity, and oversee platform operations from this dashboard.
                            </p>
                        </div>
                        <div class="hidden md:block text-6xl opacity-10">📊</div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\User::count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-2xl">👥</div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Admin Users</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\User::where('role', 'admin')->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center text-2xl">🔐</div>
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
                </div>
            </div>
        </div>

<<<<<<< HEAD
        {{-- ── KEY METRICS ── --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Total Projects --}}
            <div class="dash-card p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#6b4f35; margin-bottom:0.75rem;">
                            Total Projects
                        </p>
                        <p style="font-size:2.5rem; font-weight:800; color:#1a0f00; font-family:'Syne',sans-serif; letter-spacing:-0.03em; line-height:1;">
                            {{ \App\Models\Project::count() }}
                        </p>
                        <p style="font-size:0.75rem; color:#9ca3af; margin-top:0.4rem;">All-time</p>
                    </div>
                    <div style="width:42px; height:42px; background:rgba(249,115,22,0.1); border-radius:11px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-folder" style="color:#f97316; font-size:1.1rem;"></i>
                    </div>
                </div>
                <div class="metric-bar"><div class="metric-bar-fill" style="background:#f97316; width:100%;"></div></div>
            </div>

            {{-- Active Contracts --}}
            <div class="dash-card p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#6b4f35; margin-bottom:0.75rem;">
                            Active Contracts
                        </p>
                        <p style="font-size:2.5rem; font-weight:800; color:#1a0f00; font-family:'Syne',sans-serif; letter-spacing:-0.03em; line-height:1;">
                            {{ \App\Models\Project::where(function($q){ $q->whereNull('revised_contract_expiry')->where('original_contract_expiry','>=',now())->orWhere('revised_contract_expiry','>=',now()); })->count() }}
                        </p>
                        <p style="font-size:0.75rem; color:#9ca3af; margin-top:0.4rem;">In progress</p>
                    </div>
                    <div style="width:42px; height:42px; background:rgba(34,197,94,0.1); border-radius:11px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-check-circle" style="color:#22c55e; font-size:1.1rem;"></i>
                    </div>
                </div>
                <div class="metric-bar"><div class="metric-bar-fill" style="background:#22c55e; width:75%;"></div></div>
            </div>

            {{-- Expiring Soon --}}
            <div class="dash-card p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#6b4f35; margin-bottom:0.75rem;">
                            Expiring Soon
                        </p>
                        <p style="font-size:2.5rem; font-weight:800; color:#1a0f00; font-family:'Syne',sans-serif; letter-spacing:-0.03em; line-height:1;">
                            {{ \App\Models\Project::where(function($q){ $q->whereNull('revised_contract_expiry')->whereBetween('original_contract_expiry',[now(), now()->addDays(30)])->orWhereBetween('revised_contract_expiry',[now(), now()->addDays(30)]); })->count() }}
                        </p>
                        <p style="font-size:0.75rem; color:#9ca3af; margin-top:0.4rem;">Next 30 days</p>
                    </div>
                    <div style="width:42px; height:42px; background:rgba(234,179,8,0.1); border-radius:11px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-clock" style="color:#eab308; font-size:1.1rem;"></i>
                    </div>
                </div>
                <div class="metric-bar"><div class="metric-bar-fill" style="background:#eab308; width:50%;"></div></div>
            </div>

            {{-- Expired --}}
            <div class="dash-card p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p style="font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#6b4f35; margin-bottom:0.75rem;">
                            Expired
                        </p>
                        <p style="font-size:2.5rem; font-weight:800; color:#1a0f00; font-family:'Syne',sans-serif; letter-spacing:-0.03em; line-height:1;">
                            {{ \App\Models\Project::where(function($q){ $q->whereNull('revised_contract_expiry')->where('original_contract_expiry','<',now())->orWhere('revised_contract_expiry','<',now()); })->count() }}
                        </p>
                        <p style="font-size:0.75rem; color:#9ca3af; margin-top:0.4rem;">Requires action</p>
                    </div>
                    <div style="width:42px; height:42px; background:rgba(239,68,68,0.1); border-radius:11px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-times-circle" style="color:#ef4444; font-size:1.1rem;"></i>
                    </div>
                </div>
                <div class="metric-bar"><div class="metric-bar-fill" style="background:#ef4444; width:25%;"></div></div>
            </div>

        </div>

        {{-- ── ACTIONS + STATUS ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            {{-- Admin Actions (2/3) --}}
            <div class="dash-card p-6 lg:col-span-2">
                <h3 style="font-family:'Syne',sans-serif; font-weight:700; font-size:0.95rem; color:#1a0f00; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem; letter-spacing:-0.01em;">
                    <i class="fas fa-bolt" style="color:#f97316;"></i>
                    Quick Actions
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                    <a href="{{ route('admin.projects.create') }}" class="action-btn"
                        style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); box-shadow: 0 4px 14px rgba(249,115,22,0.35);">
                        <div style="width:38px; height:38px; background:rgba(255,255,255,0.2); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1px solid rgba(255,255,255,0.2);">
                            <i class="fas fa-plus-circle text-white"></i>
                        </div>
                        <div>
                            <p style="font-weight:600; font-size:0.875rem; color:white;">Create Project</p>
                            <p style="font-size:0.75rem; color:rgba(255,255,255,0.75); margin-top:1px;">Add a new project</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.projects.index') }}" class="action-btn"
                        style="background: linear-gradient(135deg, #fb923c 0%, #f97316 100%); box-shadow: 0 4px 14px rgba(249,115,22,0.25);">
                        <div style="width:38px; height:38px; background:rgba(255,255,255,0.2); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1px solid rgba(255,255,255,0.2);">
                            <i class="fas fa-list text-white"></i>
                        </div>
                        <div>
                            <p style="font-weight:600; font-size:0.875rem; color:white;">View Projects</p>
                            <p style="font-size:0.75rem; color:rgba(255,255,255,0.75); margin-top:1px;">Manage all projects</p>
                        </div>
                    </a>

                    <a href="#" class="action-btn"
                        style="background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%); box-shadow: 0 4px 14px rgba(251,191,36,0.3);">
                        <div style="width:38px; height:38px; background:rgba(255,255,255,0.2); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1px solid rgba(255,255,255,0.2);">
                            <i class="fas fa-file-pdf text-white"></i>
                        </div>
                        <div>
                            <p style="font-weight:600; font-size:0.875rem; color:white;">Generate Report</p>
                            <p style="font-size:0.75rem; color:rgba(255,255,255,0.75); margin-top:1px;">Download analytics</p>
                        </div>
                    </a>

                </div>
            </div>

            {{-- System Status (1/3) --}}
            <div class="dash-card p-6">
                <h3 style="font-family:'Syne',sans-serif; font-weight:700; font-size:0.95rem; color:#1a0f00; margin-bottom:1.25rem; display:flex; align-items:center; gap:0.5rem; letter-spacing:-0.01em;">
                    <i class="fas fa-server" style="color:#f97316;"></i>
                    System Status
                </h3>
                <div style="display:flex; flex-direction:column; gap:1rem;">

                    @foreach([['System', 'Operational'], ['Database', 'Connected'], ['API', 'Healthy']] as [$label, $status])
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:0.6rem 0.75rem; background:rgba(249,115,22,0.04); border-radius:9px; border:1px solid rgba(249,115,22,0.08);">
                        <div style="display:flex; align-items:center; gap:0.65rem;">
                            <div class="status-dot" style="background:#22c55e; flex-shrink:0;"></div>
                            <div>
                                <p style="font-size:0.7rem; color:#6b4f35; text-transform:uppercase; letter-spacing:0.04em; font-weight:600;">{{ $label }}</p>
                                <p style="font-size:0.825rem; font-weight:600; color:#1a0f00;">{{ $status }}</p>
                            </div>
                        </div>
                        <span style="font-size:0.7rem; background:rgba(34,197,94,0.1); color:#16a34a; padding:3px 10px; border-radius:99px; font-weight:600; border:1px solid rgba(34,197,94,0.2);">Online</span>
                    </div>
                    @endforeach

                </div>
            </div>

        </div>

    </div>
</x-app-layout>
=======
        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-sm p-8">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <span class="w-1 h-6 bg-gradient-to-b from-orange-400 to-orange-600 rounded-full"></span>
                Quick Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="#" class="group p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-orange-400 dark:hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/10 transition-all duration-300">
                    <div class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Manage Users</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View and manage all users</p>
                </a>
                <a href="#" class="group p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-orange-400 dark:hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/10 transition-all duration-300">
                    <div class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">View Logs</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Check system activity logs</p>
                </a>
                <a href="#" class="group p-4 rounded-lg border-2 border-gray-200 dark:border-gray-700 hover:border-orange-400 dark:hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/10 transition-all duration-300">
                    <div class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Settings</div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Configure system settings</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
