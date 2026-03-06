<x-app-layout>
    <x-slot name="header">
<<<<<<< HEAD
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-3xl text-gray-900 dark:text-white flex items-center gap-3">
                    <span class="bg-gradient-to-br from-purple-600 to-violet-600 p-2 rounded-lg text-white">
                        <i class="fas fa-tachometer-alt"></i>
                    </span>
                    User Dashboard
                </h2>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Welcome back, {{ Auth::user()->name }}!</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-purple-600/10 to-violet-600/10 dark:from-purple-600/5 dark:to-violet-600/5 border border-purple-200 dark:border-purple-900/30 rounded-xl p-8 backdrop-filter backdrop-blur-sm">
            <div class="flex items-center gap-4">
                <div class="text-5xl">👋</div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Welcome to PEO Document Monitoring</h3>
                    <p class="text-gray-700 dark:text-gray-300">You're now logged in as a user. Use the sidebar to explore available features and manage your documents efficiently.</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Stat Card 1 -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-filter backdrop-blur-lg border border-white/20 dark:border-gray-700/20 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">
                            <i class="fas fa-file-alt text-purple-600 mr-2"></i>Total Documents
                        </p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">0</p>
                    </div>
                    <div class="text-4xl text-purple-200 dark:text-purple-900/30">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-filter backdrop-blur-lg border border-white/20 dark:border-gray-700/20 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">
                            <i class="fas fa-project-diagram text-blue-600 mr-2"></i>Active Projects
                        </p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">0</p>
                    </div>
                    <div class="text-4xl text-blue-200 dark:text-blue-900/30">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                </div>
            </div>

            <!-- Stat Card 3 -->
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-filter backdrop-blur-lg border border-white/20 dark:border-gray-700/20 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium mb-1">
                            <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>Pending Reviews
                        </p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">0</p>
                    </div>
                    <div class="text-4xl text-orange-200 dark:text-orange-900/30">
                        <i class="fas fa-exclamation-triangle"></i>
=======
        <h2 class="font-semibold text-3xl bg-gradient-to-r from-orange-600 to-orange-400 bg-clip-text text-transparent dark:from-orange-400 dark:to-orange-300 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <div class="bg-gradient-to-br from-orange-50 to-white dark:from-orange-900/20 dark:to-gray-800 overflow-hidden rounded-2xl border-2 border-orange-200 dark:border-orange-700/50 shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="p-8 md:p-12">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                Welcome back, {{ Auth::user()->name }}! 👋
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 max-w-2xl">
                                Manage your profile, view your activities, and access all your resources in one place.
                            </p>
                        </div>
                        <div class="hidden md:block text-6xl opacity-10">📚</div>
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
                    </div>
                </div>
            </div>
        </div>

<<<<<<< HEAD
        <!-- Quick Actions -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-filter backdrop-blur-lg border border-white/20 dark:border-gray-700/20 rounded-xl p-8 shadow-sm">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-lightning-bolt text-yellow-500"></i>Quick Actions
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="#" class="flex items-center gap-4 p-4 bg-gradient-to-br from-purple-600 to-violet-600 hover:from-purple-700 hover:to-violet-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-md hover:shadow-lg">
                    <i class="fas fa-upload text-2xl"></i>
                    <div>
                        <p class="font-semibold">Upload Documents</p>
                        <p class="text-sm opacity-90">Add new documents to your library</p>
                    </div>
                </a>
                <a href="#" class="flex items-center gap-4 p-4 bg-gradient-to-br from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-md hover:shadow-lg">
                    <i class="fas fa-search text-2xl"></i>
                    <div>
                        <p class="font-semibold">Search Documents</p>
                        <p class="text-sm opacity-90">Find documents quickly</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Info Section -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-900/30 rounded-xl p-6">
            <div class="flex items-start gap-4">
                <i class="fas fa-info-circle text-blue-600 text-2xl mt-1"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">Need Help?</h4>
                    <p class="text-blue-800 dark:text-blue-400 text-sm">If you have any questions about using the PEO Document Monitoring system, please don't hesitate to contact support or refer to our documentation.</p>
=======
        <!-- Quick Actions and Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Profile Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300 p-6 group cursor-pointer">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-semibold text-gray-900 dark:text-white">Profile</h4>
                    <span class="text-2xl group-hover:scale-110 transition-transform">👤</span>
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Full Name</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email Address</p>
                        <p class="font-medium text-gray-900 dark:text-white text-sm truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="inline-block mt-3 text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 text-sm font-medium">
                        Edit Profile →
                    </a>
                </div>
            </div>

            <!-- Account Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-semibold text-gray-900 dark:text-white">Account Status</h4>
                    <span class="text-2xl">✅</span>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Account Type</p>
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-100 dark:bg-green-900/30">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span>
                            <span class="text-xs font-medium text-green-700 dark:text-green-400 capitalize">{{ Auth::user()->role ?? 'user' }}</span>
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Member Since</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="bg-gradient-to-br from-orange-50 to-white dark:from-orange-900/20 dark:to-gray-800 rounded-xl border-2 border-orange-200 dark:border-orange-700/50 shadow-sm hover:shadow-md transition-all duration-300 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-semibold text-gray-900 dark:text-white">Quick Stats</h4>
                    <span class="text-2xl">📊</span>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                        <span class="text-lg font-bold text-orange-600 dark:text-orange-400">Active</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Account Health</span>
                        <span class="text-lg font-bold text-green-600 dark:text-green-400">100%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Account Management -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-2 h-8 bg-gradient-to-b from-orange-400 to-orange-600 rounded-full"></span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Account Management</h3>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('profile.edit') }}" class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-orange-50 dark:hover:bg-orange-900/10 transition-colors group">
                        <span class="text-lg mr-3">🔐</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white group-hover:text-orange-600 transition-colors">Security Settings</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Manage password & security</p>
                        </div>
                        <span class="text-gray-400">→</span>
                    </a>
                    <a href="{{ route('profile.edit') }}" class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-orange-50 dark:hover:bg-orange-900/10 transition-colors group">
                        <span class="text-lg mr-3">📧</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white group-hover:text-orange-600 transition-colors">Email Preferences</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Manage email notifications</p>
                        </div>
                        <span class="text-gray-400">→</span>
                    </a>
                </div>
            </div>

            <!-- Resources & Support -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-2 h-8 bg-gradient-to-b from-blue-400 to-blue-600 rounded-full"></span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Help & Resources</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors cursor-pointer group">
                        <span class="text-lg mr-3">📚</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">Documentation</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Learn how to use the system</p>
                        </div>
                        <span class="text-gray-400">→</span>
                    </div>
                    <div class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors cursor-pointer group">
                        <span class="text-lg mr-3">💬</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">Contact Support</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Get help from our team</p>
                        </div>
                        <span class="text-gray-400">→</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-sm p-8">
            <div class="flex items-center gap-3 mb-6">
                <span class="w-2 h-8 bg-gradient-to-b from-green-400 to-green-600 rounded-full"></span>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Account Activity</h3>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center gap-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">Account Created</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-3 h-3 rounded-full bg-orange-500"></div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">Last Login</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Today</p>
                    </div>
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
                </div>
            </div>
        </div>
    </div>
<<<<<<< HEAD
</x-app-layout>
=======
</x-app-layout>
>>>>>>> 89caed72e1a46b970403232f253207870b3ea870
