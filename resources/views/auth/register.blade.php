<x-guest-layout>
    <div class="max-w-xl mx-auto mt-16 p-8 bg-white/80 backdrop-blur-md rounded-2xl shadow-2xl transition-all duration-300">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Instructor Registration</h1>

        <form method="POST" action="{{ route('register') }}" class="space-y-6" novalidate>
            @csrf

            {{-- Name Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="first_name" :value="__('First Name')" />
                    <x-text-input id="first_name" name="first_name" type="text" placeholder="Juan" class="w-full mt-1" :value="old('first_name')" required />
                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="middle_name" :value="__('Middle Name')" />
                    <x-text-input id="middle_name" name="middle_name" type="text" placeholder="(optional)" class="w-full mt-1" :value="old('middle_name')" />
                    <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="last_name" :value="__('Last Name')" />
                    <x-text-input id="last_name" name="last_name" type="text" placeholder="Dela Cruz" class="w-full mt-1" :value="old('last_name')" required />
                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                </div>
            </div>

            {{-- Email Username --}}
            <div>
                <x-input-label for="email" :value="__('Email Username')" />
                <div class="flex rounded-md shadow-sm">
                    <x-text-input id="email" name="email" type="text"
                        placeholder="jdelacruz"
                        class="rounded-r-none w-full mt-1"
                        :value="old('email')"
                        required
                        pattern="^[^@]+$"
                        title="Do not include '@' or domain — just the username." />
                    <span class="inline-flex items-center px-3 rounded-r-md bg-gray-200 border border-l-0 border-gray-300 mt-1 text-sm text-gray-600">@brokenshire.edu.ph</span>
                </div>

                {{-- Live warning --}}
                <p id="email-warning" class="text-sm text-red-600 mt-1 hidden">
                    Please enter only your username — do not include '@' or email domain.
                </p>

                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            {{-- Department --}}
            <div>
                <x-input-label for="department_id" :value="__('Select Department')" />
                <select id="department_id" name="department_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    <option value="">-- Choose Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->department_description }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
            </div>

            {{-- Course --}}
            <div id="course-wrapper" style="display: none;">
                <x-input-label for="course_id" :value="__('Select Course')" />
                <select id="course_id" name="course_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    <option value="">-- Choose Course --</option>
                </select>
                <div id="ge-course-note" class="mt-2 p-2 bg-blue-50 text-blue-700 hidden" style="transition: opacity 0.3s ease-in-out;">
                    <p class="text-sm font-medium">
                        <i class="fas fa-info-circle mr-1"></i>
                        Note: Selecting "General Education" will register you as a GE Instructor
                    </p>
                </div>
                <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
            </div>

            {{-- Password --}}
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" name="password" type="password" class="w-full mt-1" required placeholder="Min. 8 characters" autocomplete="new-password" oninput="checkPassword(this.value)" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />

                {{-- Password Rules in 2 Columns --}}
                <div id="password-requirements" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm text-gray-700">
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div id="circle-length" class="w-3 h-3 rounded-full bg-gray-300 border transition-all"></div>
                            <span>Minimum 8 characters</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div id="circle-case" class="w-3 h-3 rounded-full bg-gray-300 border transition-all"></div>
                            <span>Upper & lowercase</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div id="circle-number" class="w-3 h-3 rounded-full bg-gray-300 border transition-all"></div>
                            <span>At least 1 number</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div id="circle-special" class="w-3 h-3 rounded-full bg-gray-300 border transition-all"></div>
                            <span>Special character</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="w-full mt-1" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:underline">Already registered?</a>
                <x-primary-button>
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    {{-- JavaScript --}}
    <script>
        // Function to check if a course is GE
        function isGECourse(courseCode) {
            return courseCode === 'GE';
        }

        // Function to update course options
        function updateCourseOptions(departmentId) {
            const courseSelect = document.getElementById('course_id');
            const courseWrapper = document.getElementById('course-wrapper');
            const geNote = document.getElementById('ge-course-note');
            
            console.log('Updating courses for department:', departmentId, 'Course wrapper:', courseWrapper);
            
            // Clear existing options except the first one
            courseSelect.innerHTML = '<option value="">-- Choose Course --</option>';
            
            // Ensure course wrapper is visible when department is selected
            if (departmentId) {
                courseWrapper.style.display = 'block';
            } else {
                courseWrapper.style.display = 'none';
                return;
            }
            
            // Hide GE note by default
            geNote.classList.add('hidden');
            
            if (!departmentId) {
                courseWrapper.classList.add('opacity-0', 'h-0', 'overflow-hidden');
                return;
            }
            
            // Show course select
            courseWrapper.classList.remove('opacity-0', 'h-0', 'overflow-hidden');
            courseSelect.disabled = true;
            
            // Show loading state
            const loadingOption = new Option('Loading courses...', '');
            loadingOption.disabled = true;
            courseSelect.add(loadingOption);
            
            // Fetch courses for the selected department
            const apiUrl = `/api/department/${departmentId}/courses`;
            console.log('Fetching courses from:', apiUrl);
            
            fetch(apiUrl)
                .then(response => {
                    console.log('API response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(courses => {
                    console.log('Received courses data:', courses);
                    // Clear loading option
                    courseSelect.innerHTML = '<option value="">-- Choose Course --</option>';
                    
                    if (courses && courses.length > 0) {
                        // Add courses
                        courses.forEach(course => {
                            console.log('Processing course:', course);
                            // Format as 'CODE - Description'
                            const displayText = course.name || course.course_description || `Course ${course.id}`;
                            const option = new Option(displayText, course.id);
                            // No special styling for General Education courses
                            courseSelect.add(option);
                        });
                    } else {
                        console.log('No courses found for department:', departmentId);
                        const option = new Option('No courses available', '');
                        option.disabled = true;
                        courseSelect.add(option);
                    }
                    
                    courseSelect.disabled = false;
                    console.log('Course select options updated. Total options:', courseSelect.options.length);
                })
                .catch(error => {
                    console.error('Error loading courses:', error);
                    courseSelect.innerHTML = '<option value="">Error loading courses</option>';
                    courseSelect.disabled = false;
                });
        }
        
        // Show/hide GE note based on course selection
        document.getElementById('course_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const geNote = document.getElementById('ge-course-note');
            
            console.log('Course selected. Value:', selectedOption.value, 'Text:', selectedOption.text);
            
            if (!selectedOption.value) {
                console.log('No course selected, hiding GE note');
                geNote.classList.add('hidden');
                return;
            }
            
            // Check if the course is General Education
            const courseText = selectedOption.text.trim();
            const isGE = courseText.includes('General Education');
            
            console.log('Course text:', courseText, 'Is GE:', isGE);
            
            if (isGE) {
                console.log('GE course selected, showing note');
                geNote.classList.remove('hidden');
                // Force a reflow to ensure the transition works
                void geNote.offsetWidth;
                geNote.style.opacity = '1';
            } else {
                console.log('Non-GE course selected, hiding note');
                geNote.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            // Email @ symbol warning
            const emailInput = document.getElementById('email');
            const emailWarning = document.getElementById('email-warning');

            emailInput.addEventListener('input', () => {
                const hasAtSymbol = emailInput.value.includes('@');

                if (hasAtSymbol) {
                    emailWarning.classList.remove('hidden');
                    emailInput.setCustomValidity("Please enter only your username, not the full email.");
                } else {
                    emailWarning.classList.add('hidden');
                    emailInput.setCustomValidity("");
                }
            });

            // Add event listener for department select change
            const deptSelect = document.getElementById('department_id');
            console.log('Department select element:', deptSelect);
            
            deptSelect.addEventListener('change', function() {
                console.log('Department changed to:', this.value);
                updateCourseOptions(this.value);
            });
            
            // Debug: Check initial state
            console.log('Initial department value:', deptSelect.value);
            if (deptSelect.value) {
                updateCourseOptions(deptSelect.value);
            }
        });

        function checkPassword(password) {
            const checks = {
                length: password.length >= 8,
                number: /[0-9]/.test(password),
                case: /[a-z]/.test(password) && /[A-Z]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };

            const update = (id, valid) => {
                const el = document.getElementById(`circle-${id}`);
                el.classList.remove('bg-red-400', 'bg-green-500', 'bg-gray-300');
                el.classList.add(valid ? 'bg-green-500' : 'bg-red-400');
            };

            update('length', checks.length);
            update('number', checks.number);
            update('case', checks.case);
            update('special', checks.special);

            const requirementsBox = document.getElementById('password-requirements');
            const allValid = Object.values(checks).every(Boolean);
            requirementsBox.classList.toggle('hidden', allValid);
        }
    </script>
</x-guest-layout>
