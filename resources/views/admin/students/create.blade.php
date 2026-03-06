<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl bg-gradient-to-r from-orange-600 to-orange-400 bg-clip-text text-transparent dark:from-orange-400 dark:to-orange-300 leading-tight">
            {{ __('Add New Student') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                {{ __('Back to Students') }}
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden">
            <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-700 rounded-lg">
                        <h3 class="font-semibold text-red-700 dark:text-red-300 mb-2">{{ __('Please fix the following errors:') }}</h3>
                        <ul class="list-disc list-inside space-y-1 text-red-600 dark:text-red-400">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Profile Photo Section -->
                <div class="mb-8 pb-8 border-b-2 border-gray-200 dark:border-gray-700">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-4">{{ __('Profile Photo') }}</label>
                    <div class="flex items-center gap-6">
                        <div class="w-24 h-24 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden" id="photoPreview">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input type="file" name="profile_photo" id="profile_photo" class="form-input flex-1" accept="image/*" onchange="previewPhoto(event)"/>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-gradient-to-b from-orange-400 to-orange-600 rounded-full"></span>
                        {{ __('Personal Information') }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('First Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" id="first_name" 
                                   class="form-input @error('first_name') border-red-500 @enderror" 
                                   value="{{ old('first_name') }}" 
                                   required>
                            @error('first_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Last Name') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" id="last_name" 
                                   class="form-input @error('last_name') border-red-500 @enderror" 
                                   value="{{ old('last_name') }}" 
                                   required>
                            @error('last_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Middle Name -->
                        <div>
                            <label for="middle_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Middle Name') }}</label>
                            <input type="text" name="middle_name" id="middle_name" 
                                   class="form-input" 
                                   value="{{ old('middle_name') }}">
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Date of Birth') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="date_of_birth" id="date_of_birth" 
                                   class="form-input @error('date_of_birth') border-red-500 @enderror" 
                                   value="{{ old('date_of_birth') }}" 
                                   required>
                            @error('date_of_birth')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Gender') }} <span class="text-red-500">*</span></label>
                            <select name="gender" id="gender" 
                                    class="form-input @error('gender') border-red-500 @enderror"
                                    required>
                                <option value="">{{ __('Select Gender') }}</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                            </select>
                            @error('gender')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Email') }} <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email" 
                                   class="form-input @error('email') border-red-500 @enderror" 
                                   value="{{ old('email') }}" 
                                   required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Password') }} <span class="text-red-500">*</span></label>
                            <input type="password" name="password" id="password" 
                                   class="form-input @error('password') border-red-500 @enderror"
                                   required>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-gray-500 text-xs mt-1">{{ __('Minimum 8 characters') }}</p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Confirm Password') }} <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                   class="form-input @error('password_confirmation') border-red-500 @enderror"
                                   required>
                            @error('password_confirmation')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" id="phone" 
                                   class="form-input" 
                                   value="{{ old('phone') }}">
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="mb-8">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Address') }} <span class="text-red-500">*</span></label>
                    <textarea name="address" id="address" rows="3" 
                              class="form-input @error('address') border-red-500 @enderror"
                              required>{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Academic Information -->
                <div class="mb-8 pb-8 border-b-2 border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <span class="w-1 h-6 bg-gradient-to-b from-orange-400 to-orange-600 rounded-full"></span>
                        {{ __('Academic Information') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Student Number -->
                        <div>
                            <label for="student_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Student Number') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="student_number" id="student_number" 
                                   class="form-input @error('student_number') border-red-500 @enderror" 
                                   value="{{ old('student_number') }}"
                                   placeholder="e.g., 2024001"
                                   required>
                            @error('student_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Enrollment Date -->
                        <div>
                            <label for="enrollment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Enrollment Date') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="enrollment_date" id="enrollment_date" 
                                   class="form-input @error('enrollment_date') border-red-500 @enderror" 
                                   value="{{ old('enrollment_date') }}" 
                                   required>
                            @error('enrollment_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Course -->
                        <div>
                            <label for="course" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Course') }} <span class="text-red-500">*</span></label>
                            <input type="text" name="course" id="course" 
                                   class="form-input @error('course') border-red-500 @enderror" 
                                   value="{{ old('course') }}" 
                                   placeholder="e.g., Bachelor of Science in Computer Science"
                                   required>
                            @error('course')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Year Level -->
                        <div>
                            <label for="year_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Year Level') }} <span class="text-red-500">*</span></label>
                            <select name="year_level" id="year_level" 
                                    class="form-input @error('year_level') border-red-500 @enderror"
                                    required>
                                <option value="">{{ __('Select Year') }}</option>
                                <option value="1" {{ old('year_level') == 1 ? 'selected' : '' }}>{{ __('1st Year') }}</option>
                                <option value="2" {{ old('year_level') == 2 ? 'selected' : '' }}>{{ __('2nd Year') }}</option>
                                <option value="3" {{ old('year_level') == 3 ? 'selected' : '' }}>{{ __('3rd Year') }}</option>
                                <option value="4" {{ old('year_level') == 4 ? 'selected' : '' }}>{{ __('4th Year') }}</option>
                                <option value="5" {{ old('year_level') == 5 ? 'selected' : '' }}>{{ __('5th Year') }}</option>
                            </select>
                            @error('year_level')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- GPA -->
                        <div>
                            <label for="gpa" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('GPA') }}</label>
                            <input type="number" name="gpa" id="gpa" step="0.01" min="0" max="4" 
                                   class="form-input @error('gpa') border-red-500 @enderror" 
                                   value="{{ old('gpa') }}" 
                                   placeholder="0.00">
                            @error('gpa')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                            <select name="status" id="status" class="form-input">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                <option value="graduated" {{ old('status') == 'graduated' ? 'selected' : '' }}>{{ __('Graduated') }}</option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div class="mb-8">
                    <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Remarks') }}</label>
                    <textarea name="remarks" id="remarks" rows="3" 
                              class="form-input"
                              placeholder="Any additional notes about the student...">{{ old('remarks') }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center gap-4 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                    <button type="submit" class="btn-primary">
                        <span>{{ __('Create Student') }}</span>
                    </button>
                    <a href="{{ route('admin.students.index') }}" class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-semibold transition-all duration-300">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewPhoto(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('photoPreview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>
