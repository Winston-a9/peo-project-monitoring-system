<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl bg-gradient-to-r from-blue-600 to-blue-400 bg-clip-text text-transparent dark:from-blue-400 dark:to-blue-300 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Welcome Section -->
        <div class="mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 overflow-hidden rounded-2xl border-2 border-blue-200 dark:border-blue-700/50 shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="p-8 md:p-12">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-2">
                                Welcome back, {{ Auth::user()->name }}! 👋
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 max-w-2xl">
                                Access your courses, view grades, submit assignments, and manage your academic profile.
                            </p>
                        </div>
                        <div class="hidden md:block text-6xl opacity-10">📚</div>
                    </div>
                </div>
            </div>
        </div>

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
                    <a href="{{ route('profile.edit') }}" class="inline-block mt-3 text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
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
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="text-xs font-medium text-blue-700 dark:text-blue-400 capitalize">{{ Auth::user()->role ?? 'student' }}</span>
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Enrolled Since</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->created_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Academic Stats Card -->
            <div class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/20 dark:to-gray-800 rounded-xl border-2 border-blue-200 dark:border-blue-700/50 shadow-sm hover:shadow-md transition-all duration-300 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-semibold text-gray-900 dark:text-white">Academic Info</h4>
                    <span class="text-2xl">📊</span>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                        <span class="text-lg font-bold text-blue-600 dark:text-blue-400">Active</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">GPA</span>
                        <span class="text-lg font-bold text-green-600 dark:text-green-400">4.0</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Academics -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-2 h-8 bg-gradient-to-b from-blue-400 to-blue-600 rounded-full"></span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Academics</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors group cursor-pointer">
                        <span class="text-lg mr-3">📖</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">My Courses</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">View enrolled courses</p>
                        </div>
                        <span class="text-gray-400">→</span>
                    </div>
                    <div class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors group cursor-pointer">
                        <span class="text-lg mr-3">📝</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">Assignments</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Submit and track assignments</p>
                        </div>
                        <span class="text-gray-400">→</span>
                    </div>
                    <div class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-colors group cursor-pointer">
                        <span class="text-lg mr-3">⭐</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">Grades</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">View your academic performance</p>
                        </div>
                        <span class="text-gray-400">→</span>
                    </div>
                </div>
            </div>

            <!-- Resources & Support -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-300 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <span class="w-2 h-8 bg-gradient-to-b from-purple-400 to-purple-600 rounded-full"></span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Help & Support</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-purple-50 dark:hover:bg-purple-900/10 transition-colors cursor-pointer group">
                        <span class="text-lg mr-3">📚</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white group-hover:text-purple-600 transition-colors">Library</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Access digital learning resources</p>
                        </div>
                        <span class="text-gray-400">→</span>
                    </div>
                    <div class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-purple-50 dark:hover:bg-purple-900/10 transition-colors cursor-pointer group">
                        <span class="text-lg mr-3">💬</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white group-hover:text-purple-600 transition-colors">Contact Advisor</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Get academic guidance</p>
                        </div>
                        <span class="text-gray-400">→</span>
                    </div>
                    <div class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-purple-50 dark:hover:bg-purple-900/10 transition-colors cursor-pointer group">
                        <span class="text-lg mr-3">❓</span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900 dark:text-white group-hover:text-purple-600 transition-colors">FAQ</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Common questions & answers</p>
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
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Recent Activity</h3>
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
                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900 dark:text-white">Last Login</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Today</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
