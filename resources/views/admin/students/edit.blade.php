<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl bg-gradient-to-r from-orange-600 to-orange-400 bg-clip-text text-transparent dark:from-orange-400 dark:to-orange-300 leading-tight">
            {{ __('Edit Student') }} - {{ $student->first_name }} {{ $student->last_name }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-12">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('admin.students.show', $student->id) }}" class="inline-flex items-center gap-2 px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                {{ __('Back to Student') }}
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-200 dark:border-gray-700 shadow-lg overflow-hidden">
            <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')

                <!-- Success Message -->
                @if (session('success'))
                    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-700 rounded-lg flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <div>
                            <h3 class="font-semibold text-green-700 dark:text-green-300">{{ __('Success!') }}</h3>
                            <p class="text-green-600 dark:text-green-400 text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

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
                        <div class="relative">
                            <div class="w-24 h-24 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border-2 border-gray-300 dark:border-gray-600" id="photoPreview">
                                @if ($student->profile_photo)
                                    <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->first_name }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                @endif
                            </div>
                            @if ($student->profile_photo)
                                <button type="button" onclick="document.getElementById('photo_remove').value = 1; document.getElementById('photoPreview').innerHTML = '<svg class=\"w-12 h-12 text-gray-400\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z\"></path></svg>'; document.getElementById('profile_photo').value = '';" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            @endif
                        </div>
                        <div class="flex-1">
                            <input type="file" name="profile_photo" id="profile_photo" class="form-input w-full" accept="image/*" onchange="previewPhoto(event)"/>
                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-2">{{ __('JPG, PNG or GIF (Max. 2MB)') }}</p>
                            <input type="hidden" id="photo_remove" name="photo_remove" value="0">
                        </div>
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
                                   value="{{ old('first_name', $student->first_name) }}" 
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
                                   value="{{ old('last_name', $student->last_name) }}" 
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
                                   value="{{ old('middle_name', $student->middle_name) }}">
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Date of Birth') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="date_of_birth" id="date_of_birth" 
                                   class="form-input @error('date_of_birth') border-red-500 @enderror" 
                                   value="{{ old('date_of_birth', $student->date_of_birth->format('Y-m-d')) }}" 
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
                                <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
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
                                   value="{{ old('email', $student->email) }}" 
                                   required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" id="phone" 
                                   class="form-input" 
                                   value="{{ old('phone', $student->phone) }}">
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="mb-8">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Address') }} <span class="text-red-500">*</span></label>
                    <textarea name="address" id="address" rows="3" 
                              class="form-input @error('address') border-red-500 @enderror"
                              required>{{ old('address', $student->address) }}</textarea>
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
                                   value="{{ old('student_number', $student->student_number) }}"
                                   required
                                   disabled>
                            <p class="text-gray-500 dark:text-gray-400 text-xs mt-1">{{ __('Student ID cannot be modified') }}</p>
                            @error('student_number')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Enrollment Date -->
                        <div>
                            <label for="enrollment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Enrollment Date') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="enrollment_date" id="enrollment_date" 
                                   class="form-input @error('enrollment_date') border-red-500 @enderror" 
                                   value="{{ old('enrollment_date', $student->enrollment_date->format('Y-m-d')) }}" 
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
                                   value="{{ old('course', $student->course) }}" 
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
                                <option value="1" {{ old('year_level', $student->year_level) == 1 ? 'selected' : '' }}>{{ __('1st Year') }}</option>
                                <option value="2" {{ old('year_level', $student->year_level) == 2 ? 'selected' : '' }}>{{ __('2nd Year') }}</option>
                                <option value="3" {{ old('year_level', $student->year_level) == 3 ? 'selected' : '' }}>{{ __('3rd Year') }}</option>
                                <option value="4" {{ old('year_level', $student->year_level) == 4 ? 'selected' : '' }}>{{ __('4th Year') }}</option>
                                <option value="5" {{ old('year_level', $student->year_level) == 5 ? 'selected' : '' }}>{{ __('5th Year') }}</option>
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
                                   value="{{ old('gpa', $student->gpa) }}" 
                                   placeholder="0.00">
                            @error('gpa')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Status') }}</label>
                            <select name="status" id="status" class="form-input">
                                <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="inactive" {{ old('status', $student->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>{{ __('Graduated') }}</option>
                                <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Remarks -->
                <div class="mb-8">
                    <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Remarks') }}</label>
                    <textarea name="remarks" id="remarks" rows="3" 
                              class="form-input"
                              placeholder="Any additional notes about the student...">{{ old('remarks', $student->remarks) }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center gap-4 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
                    <button type="submit" class="btn-primary">
                        <span>{{ __('Update Student') }}</span>
                    </button>
                    <a href="{{ route('admin.students.show', $student->id) }}" class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-semibold transition-all duration-300">
                        {{ __('Cancel') }}
                    </a>
                    <button type="button" onclick="openDeleteModal()" class="ms-auto px-6 py-3 border-2 border-red-300 dark:border-red-700 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg font-semibold transition-all duration-300">
                        {{ __('Delete Student') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full mx-4 overflow-hidden border-2 border-red-200 dark:border-red-700/50">
            <div class="p-8">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                
                <h3 class="text-lg font-semibold text-center text-gray-900 dark:text-white mb-2">
                    {{ __('Delete Student?') }}
                </h3>
                
                <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
                    {{ __('Are you sure you want to delete') }} <span class="font-semibold">{{ $student->first_name }} {{ $student->last_name }}</span>? {{ __('This action cannot be undone.') }}
                </p>

                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 font-semibold transition-all duration-300">
                        {{ __('Cancel') }}
                    </button>
                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all duration-300">
                            {{ __('Delete') }}
                        </button>
                    </form>
                </div>
            </div>
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

        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</x-app-layout>
