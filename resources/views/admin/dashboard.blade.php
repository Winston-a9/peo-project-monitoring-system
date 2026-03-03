<x-app-layout>
    <x-slot name="header">
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
                </div>
            </div>
        </div>

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
