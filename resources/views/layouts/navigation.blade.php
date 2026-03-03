<nav x-data="{ open: false }" class="bg-gradient-to-r from-white to-orange-50 dark:from-gray-900 dark:to-gray-800 border-b-2 border-orange-500 dark:border-orange-600 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="group transition-transform duration-300 hover:scale-105">
                    @elseif(Auth::user()->role === 'student')
                        <a href="{{ route('student.dashboard') }}" class="group transition-transform duration-300 hover:scale-105">
                    @else
                        <a href="{{ route('user.dashboard') }}" class="group transition-transform duration-300 hover:scale-105">
                    @endif
                        <x-application-logo class="block h-9 w-auto fill-current text-orange-600 dark:text-orange-400 group-hover:text-orange-700 dark:group-hover:text-orange-300 transition-colors duration-300" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-10 sm:flex items-center">
                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 px-3 py-2 rounded-md transition-all duration-300">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @elseif(Auth::user()->role === 'student')
                        <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 px-3 py-2 rounded-md transition-all duration-300">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 px-3 py-2 rounded-md transition-all duration-300">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif

                    @if(Auth::user()->role === 'admin')
                        <x-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 px-3 py-2 rounded-md transition-all duration-300 flex items-center gap-2">
                            <span>{{ __('Students') }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.646 4 4 0 010-8.646M9 9H5a4 4 0 014-4h6a4 4 0 014 4h-4a4 4 0 00-4-4M9 17H5a4 4 0 00-4 4v2h8v-2a4 4 0 00-4-4zm6-4h4a4 4 0 014 4v2h-8v-2a4 4 0 014-4z"></path>
                            </svg>
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-4 py-2 border-2 border-orange-200 dark:border-orange-700 text-sm leading-4 font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-orange-50 dark:hover:bg-gray-700 hover:border-orange-400 dark:hover:border-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-300">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white text-xs font-semibold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="text-left">
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->role }}</div>
                                <div class="font-medium">{{ Auth::user()->name }}</div>
                            </div>

                            <svg class="fill-current h-4 w-4 text-gray-500 ms-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();"
                                    class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-orange-100 dark:focus:bg-gray-700 focus:text-orange-600 dark:focus:text-orange-400 transition-all duration-150">
                    <svg class="h-6 w-6 transition-transform duration-300" stroke="currentColor" fill="none" viewBox="0 0 24 24" :class="{ 'rotate-90': open }">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden transition-all duration-300 ease-in-out border-t-2 border-orange-200 dark:border-orange-700">
        <div class="pt-2 pb-3 space-y-1 bg-orange-50 dark:bg-gray-800">
            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-100 dark:hover:bg-gray-700 block px-4 py-2 rounded-md transition-all duration-300">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @elseif(Auth::user()->role === 'student')
                <x-responsive-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-100 dark:hover:bg-gray-700 block px-4 py-2 rounded-md transition-all duration-300">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-100 dark:hover:bg-gray-700 block px-4 py-2 rounded-md transition-all duration-300">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
            @endif

            @if(Auth::user()->role === 'admin')
                <x-responsive-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')" class="text-gray-700 dark:text-gray-300 hover:text-orange-600 dark:hover:text-orange-400 hover:bg-orange-100 dark:hover:bg-gray-700 flex items-center gap-2 px-4 py-2 rounded-md transition-all duration-300">
                    <span>{{ __('Students') }}</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.646 4 4 0 010-8.646M9 9H5a4 4 0 014-4h6a4 4 0 014 4h-4a4 4 0 00-4-4M9 17H5a4 4 0 00-4 4v2h8v-2a4 4 0 00-4-4zm6-4h4a4 4 0 014 4v2h-8v-2a4 4 0 014-4z"></path>
                    </svg>
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t-2 border-orange-200 dark:border-orange-700 bg-white dark:bg-gray-900">
            <div class="px-4 py-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->role }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();"
                            class="text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 block px-4 py-2 rounded-md transition-all duration-300">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
