<x-app-layout>
    <x-slot name="header">
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
                    </div>
                </div>
            </div>
        </div>

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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
