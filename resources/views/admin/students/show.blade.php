<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl bg-gradient-to-r from-orange-600 to-orange-400 bg-clip-text text-transparent dark:from-orange-400 dark:to-orange-300 leading-tight">
            {{ __('Student Details') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Back Button -->
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                {{ __('Back to Students') }}
            </a>
            <div class="flex gap-2">
                <a href="{{ route('admin.students.edit', $student) }}" class="btn-primary">
                    {{ __('Edit') }}
                </a>
                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this student?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 text-white rounded-lg font-semibold transition-all duration-300">
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Student Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden">
            <!-- Profile Section -->
            <div class="bg-gradient-to-r from-orange-50 to-orange-100 dark:from-gray-700 dark:to-gray-600 p-8 border-b-2 border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-6">
                    <!-- Profile Photo -->
                    <div class="flex-shrink-0">
                        @if($student->profile_photo)
                            <img src="{{ Storage::url($student->profile_photo) }}" alt="{{ $student->full_name }}" class="w-32 h-32 rounded-lg object-cover border-4 border-white dark:border-gray-800 shadow-lg">
                        @else
                            <div class="w-32 h-32 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center border-4 border-white dark:border-gray-800">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Basic Info -->
                    <div class="flex-1">
                        <div class="mb-3">
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $student->full_name }}</h3>
                            <p class="text-lg text-orange-600 dark:text-orange-400 font-semibold">STU-{{ str_pad($student->student_number, 6, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="flex gap-4">
                            <span class="px-4 py-2 bg-white dark:bg-gray-800 rounded-lg border-l-4 border-orange-500">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Course</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $student->course }}</div>
                            </span>
                            <span class="px-4 py-2 bg-white dark:bg-gray-800 rounded-lg border-l-4 border-blue-500">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Year Level</div>
                                <div class="font-semibold text-gray-900 dark:text-white">Year {{ $student->year_level }}</div>
                            </span>
                            <span class="px-4 py-2 bg-white dark:bg-gray-800 rounded-lg border-l-4 @if($student->status === 'active') border-green-500 @elseif($student->status === 'inactive') border-yellow-500 @elseif($student->status === 'graduated') border-blue-500 @else border-red-500 @endif">
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ ucfirst($student->status) }}</div>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="p-8 space-y-8">
                <!-- Personal Information -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-gradient-to-b from-orange-400 to-orange-600 rounded-full"></span>
                        {{ __('Personal Information') }}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('First Name') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $student->first_name }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Last Name') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $student->last_name }}</p>
                        </div>
                        @if($student->middle_name)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Middle Name') }}</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $student->middle_name }}</p>
                            </div>
                        @endif
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Gender') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ ucfirst($student->gender) }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Date of Birth') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $student->date_of_birth->format('F d, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Age') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $student->date_of_birth->age }} years</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-gradient-to-b from-orange-400 to-orange-600 rounded-full"></span>
                        {{ __('Contact Information') }}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Email') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white"><a href="mailto:{{ $student->email }}" class="text-orange-600 dark:text-orange-400 hover:underline">{{ $student->email }}</a></p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Phone') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $student->phone ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg md:col-span-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Address') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $student->address }}</p>
                        </div>
                    </div>
                </div>

                <!-- Academic Information -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-gradient-to-b from-orange-400 to-orange-600 rounded-full"></span>
                        {{ __('Academic Information') }}
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('Enrollment Date') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $student->enrollment_date->format('F d, Y') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('GPA') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                @if($student->gpa)
                                    {{ number_format($student->gpa, 2) }}/4.00
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                @if($student->remarks)
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <span class="w-1 h-6 bg-gradient-to-b from-orange-400 to-orange-600 rounded-full"></span>
                            {{ __('Remarks') }}
                        </h4>
                        <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-500 p-4 rounded">
                            <p class="text-gray-900 dark:text-white">{{ $student->remarks }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
