<div x-data="{ showPreview: @entangle('showPreview') }" class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-plus-circle text-blue-600 mr-2"></i>
                        {{ $editingExam ? 'Edit Exam' : 'Create New Exam' }}
                    </h1>
                    <p class="text-gray-600 mt-1">Build and configure your CBT examination</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button 
                        wire:click="togglePreview"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-eye mr-2"></i>
                        Preview
                    </button>
                    
                    <button 
                        wire:click="saveDraft"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-2"></i>
                        Save Draft
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="py-4">
                <nav aria-label="Progress">
                    <ol class="flex items-center">
                        @for($i = 1; $i <= $maxSteps; $i++)
                            <li class="relative {{ $i < $maxSteps ? 'pr-12 sm:pr-20' : '' }}">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    @if($i < $maxSteps)
                                        <div class="h-0.5 w-full {{ $currentStep > $i ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                                    @endif
                                </div>
                                
                                <button 
                                    wire:click="goToStep({{ $i }})"
                                    class="relative w-8 h-8 flex items-center justify-center {{ $currentStep === $i ? 'bg-blue-600 text-white' : ($currentStep > $i ? 'bg-blue-600 text-white' : 'bg-white border-2 border-gray-300 text-gray-500') }} rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    @if($currentStep > $i)
                                        <i class="fas fa-check text-sm"></i>
                                    @else
                                        <span class="text-sm font-medium">{{ $i }}</span>
                                    @endif
                                </button>
                                
                                <div class="absolute top-10 left-1/2 transform -translate-x-1/2 text-xs font-medium text-gray-900">
                                    @switch($i)
                                        @case(1) Basic Info @break
                                        @case(2) Configuration @break
                                        @case(3) Questions @break
                                        @case(4) Review @break
                                    @endswitch
                                </div>
                            </li>
                        @endfor
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex gap-8">
            <!-- Main Form -->
            <div class="flex-1">
                @if($currentStep === 1)
                    <!-- Step 1: Basic Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center mb-6">
                                <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
                                <h2 class="text-xl font-semibold text-gray-900">Basic Information</h2>
                            </div>

                            <div class="space-y-6">
                                <!-- Title -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                        Exam Title <span class="text-red-500">*</span>
                                    </label>
                                    <input 
                                        type="text" 
                                        id="title"
                                        wire:model="title" 
                                        placeholder="Enter exam title"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    @error('title') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Course Selection -->
                                <div>
                                    <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                                        Course <span class="text-red-500">*</span>
                                    </label>
                                    <select 
                                        id="course_id"
                                        wire:model="course_id" 
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select a course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('course_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Exam Type and Difficulty -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-2">
                                            Exam Type <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            id="exam_type"
                                            wire:model="exam_type" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="practice">Practice</option>
                                            <option value="mock">Mock</option>
                                            <option value="final">Final</option>
                                            <option value="certification">Certification</option>
                                        </select>
                                        @error('exam_type') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">
                                            Difficulty Level <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            id="difficulty_level"
                                            wire:model="difficulty_level" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="beginner">Beginner</option>
                                            <option value="intermediate">Intermediate</option>
                                            <option value="advanced">Advanced</option>
                                            <option value="expert">Expert</option>
                                        </select>
                                        @error('difficulty_level') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Description
                                    </label>
                                    <textarea 
                                        id="description"
                                        wire:model="description" 
                                        rows="4"
                                        placeholder="Brief description of the exam"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    @error('description') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Instructions -->
                                <div>
                                    <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                        Instructions for Students
                                    </label>
                                    <textarea 
                                        id="instructions"
                                        wire:model="instructions" 
                                        rows="4"
                                        placeholder="Special instructions for students taking this exam"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    @error('instructions') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Thumbnail Upload -->
                                <div>
                                    <label for="exam_thumbnail" class="block text-sm font-medium text-gray-700 mb-2">
                                        Exam Thumbnail (Optional)
                                    </label>
                                    <input 
                                        type="file" 
                                        id="exam_thumbnail"
                                        wire:model="exam_thumbnail" 
                                        accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    @if($exam_thumbnail)
                                        <div class="mt-2">
                                            <img src="{{ $exam_thumbnail->temporaryUrl() }}" alt="Preview" class="h-20 w-20 object-cover rounded">
                                        </div>
                                    @endif
                                    @error('exam_thumbnail') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 2)
                    <!-- Step 2: Configuration -->
                    <div class="space-y-6">
                        <!-- Exam Settings -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="p-6">
                                <div class="flex items-center mb-6">
                                    <i class="fas fa-cog text-blue-600 text-xl mr-3"></i>
                                    <h2 class="text-xl font-semibold text-gray-900">Exam Configuration</h2>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <!-- Duration -->
                                    <div>
                                        <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                            Duration (minutes) <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            id="duration_minutes"
                                            wire:model="duration_minutes" 
                                            min="10" max="300"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        @error('duration_minutes') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Pass Percentage -->
                                    <div>
                                        <label for="pass_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                                            Pass Percentage <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            id="pass_percentage"
                                            wire:model="pass_percentage" 
                                            min="50" max="100" step="0.01"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        @error('pass_percentage') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Max Attempts -->
                                    <div>
                                        <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-2">
                                            Max Attempts <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            id="max_attempts"
                                            wire:model="max_attempts" 
                                            min="1" max="10"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        @error('max_attempts') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Questions Per Page -->
                                    <div>
                                        <label for="questions_per_page" class="block text-sm font-medium text-gray-700 mb-2">
                                            Questions Per Page <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            id="questions_per_page"
                                            wire:model="questions_per_page" 
                                            min="1" max="5"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        @error('questions_per_page') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Max Participants -->
                                <div class="mt-6">
                                    <label for="max_participants" class="block text-sm font-medium text-gray-700 mb-2">
                                        Maximum Participants (Optional)
                                    </label>
                                    <input 
                                        type="number" 
                                        id="max_participants"
                                        wire:model="max_participants" 
                                        min="1"
                                        placeholder="Leave blank for unlimited"
                                        class="block w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    @error('max_participants') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Display Settings -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="p-6">
                                <div class="flex items-center mb-6">
                                    <i class="fas fa-display text-blue-600 text-xl mr-3"></i>
                                    <h3 class="text-lg font-semibold text-gray-900">Display Settings</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="randomize_questions"
                                                wire:model="randomize_questions" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="randomize_questions" class="ml-2 block text-sm text-gray-900">
                                                Randomize Questions Order
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="randomize_options"
                                                wire:model="randomize_options" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="randomize_options" class="ml-2 block text-sm text-gray-900">
                                                Randomize Answer Options
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="allow_navigation"
                                                wire:model="allow_navigation" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="allow_navigation" class="ml-2 block text-sm text-gray-900">
                                                Allow Question Navigation
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="allow_review"
                                                wire:model="allow_review" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="allow_review" class="ml-2 block text-sm text-gray-900">
                                                Allow Review Before Submit
                                            </label>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="show_results_immediately"
                                                wire:model="show_results_immediately" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="show_results_immediately" class="ml-2 block text-sm text-gray-900">
                                                Show Results Immediately
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="show_correct_answers"
                                                wire:model="show_correct_answers" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="show_correct_answers" class="ml-2 block text-sm text-gray-900">
                                                Show Correct Answers
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="show_explanations"
                                                wire:model="show_explanations" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="show_explanations" class="ml-2 block text-sm text-gray-900">
                                                Show Explanations
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="email_results"
                                                wire:model="email_results" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="email_results" class="ml-2 block text-sm text-gray-900">
                                                Email Results to Students
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security Settings -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="p-6">
                                <div class="flex items-center mb-6">
                                    <i class="fas fa-shield-alt text-blue-600 text-xl mr-3"></i>
                                    <h3 class="text-lg font-semibold text-gray-900">Security & Monitoring</h3>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="auto_submit"
                                                wire:model="auto_submit" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="auto_submit" class="ml-2 block text-sm text-gray-900">
                                                Auto-submit when time expires
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="prevent_tab_switching"
                                                wire:model="prevent_tab_switching" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="prevent_tab_switching" class="ml-2 block text-sm text-gray-900">
                                                Prevent tab switching
                                            </label>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="webcam_monitoring"
                                                wire:model="webcam_monitoring" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="webcam_monitoring" class="ml-2 block text-sm text-gray-900">
                                                Enable webcam monitoring
                                            </label>
                                        </div>

                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="restrict_copy_paste"
                                                wire:model="restrict_copy_paste" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                            <label for="restrict_copy_paste" class="ml-2 block text-sm text-gray-900">
                                                Restrict copy & paste
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Scheduling -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="p-6">
                                <div class="flex items-center mb-6">
                                    <i class="fas fa-calendar text-blue-600 text-xl mr-3"></i>
                                    <h3 class="text-lg font-semibold text-gray-900">Scheduling</h3>
                                </div>

                                <!-- Result Delivery -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Result Delivery</label>
                                    <div class="space-y-2">
                                        <div class="flex items-center">
                                            <input 
                                                type="radio" 
                                                id="result_instant"
                                                value="instant"
                                                wire:model="result_delivery" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <label for="result_instant" class="ml-2 block text-sm text-gray-900">
                                                Instant - Show results immediately after submission
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input 
                                                type="radio" 
                                                id="result_scheduled"
                                                value="scheduled"
                                                wire:model="result_delivery" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <label for="result_scheduled" class="ml-2 block text-sm text-gray-900">
                                                Scheduled - Release results at specific date/time
                                            </label>
                                        </div>
                                        <div class="flex items-center">
                                            <input 
                                                type="radio" 
                                                id="result_manual"
                                                value="manual"
                                                wire:model="result_delivery" 
                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                            <label for="result_manual" class="ml-2 block text-sm text-gray-900">
                                                Manual - Manually release results after review
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                @if($result_delivery === 'scheduled')
                                    <div class="mb-6">
                                        <label for="result_release_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            Result Release Date & Time
                                        </label>
                                        <input 
                                            type="datetime-local" 
                                            id="result_release_date"
                                            wire:model="result_release_date" 
                                            class="block w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                @endif

                                <!-- Exam Availability -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            Start Date & Time (Optional)
                                        </label>
                                        <input 
                                            type="datetime-local" 
                                            id="start_date"
                                            wire:model="start_date" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                            End Date & Time (Optional)
                                        </label>
                                        <input 
                                            type="datetime-local" 
                                            id="end_date"
                                            wire:model="end_date" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <!-- Time Restrictions -->
                                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                            Daily Start Time (Optional)
                                        </label>
                                        <input 
                                            type="time" 
                                            id="start_time"
                                            wire:model="start_time" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                            Daily End Time (Optional)
                                        </label>
                                        <input 
                                            type="time" 
                                            id="end_time"
                                            wire:model="end_time" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <!-- Available Days -->
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Available Days (Optional)
                                    </label>
                                    <div class="grid grid-cols-7 gap-2">
                                        @php
                                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                        @endphp
                                        @foreach($days as $index => $day)
                                            <div class="flex items-center">
                                                <input 
                                                    type="checkbox" 
                                                    id="day_{{ $index }}"
                                                    value="{{ $index }}"
                                                    wire:model="available_days" 
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <label for="day_{{ $index }}" class="ml-1 block text-sm text-gray-900">
                                                    {{ substr($day, 0, 3) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 3)
                    <!-- Step 3: Questions -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-question-circle text-blue-600 text-xl mr-3"></i>
                                    <h2 class="text-xl font-semibold text-gray-900">Select Questions</h2>
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">{{ count($selectedQuestions) }}</span> questions selected
                                </div>
                            </div>

                            <!-- Question Filters -->
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <label for="searchQuestions" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                        <input 
                                            type="text" 
                                            id="searchQuestions"
                                            wire:model.live="searchQuestions" 
                                            placeholder="Search questions..."
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>

                                    <div>
                                        <label for="questionFilter" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                        <select 
                                            id="questionFilter"
                                            wire:model.live="questionFilter" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="all">All Types</option>
                                            @foreach($questionTypes as $type)
                                                <option value="{{ $type }}">{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="questionDifficulty" class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
                                        <select 
                                            id="questionDifficulty"
                                            wire:model.live="questionDifficulty" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="all">All Levels</option>
                                            @foreach($difficultyLevels as $level)
                                                <option value="{{ $level }}">{{ ucfirst($level) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label for="questionCourse" class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                        <select 
                                            id="questionCourse"
                                            wire:model.live="questionCourse" 
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="all">All Courses</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="mb-6 flex flex-wrap gap-2">
                                <button 
                                    wire:click="selectAllQuestions"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-check-double mr-1"></i>
                                    Select All
                                </button>
                                
                                <button 
                                    wire:click="clearQuestionSelection"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-times mr-1"></i>
                                    Clear All
                                </button>

                                <button 
                                    wire:click="randomSelectQuestions(10)"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-random mr-1"></i>
                                    Random 10
                                </button>

                                <button 
                                    wire:click="randomSelectQuestions(20)"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-random mr-1"></i>
                                    Random 20
                                </button>

                                <button 
                                    wire:click="loadAvailableQuestions"
                                    class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <i class="fas fa-refresh mr-1"></i>
                                    Refresh
                                </button>
                            </div>

                            <!-- Questions List -->
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @forelse($availableQuestions as $question)
                                    <div class="flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50 {{ in_array($question->id, $selectedQuestions) ? 'bg-blue-50 border-blue-200' : '' }}">
                                        <input 
                                            type="checkbox" 
                                            wire:click="toggleQuestionSelection({{ $question->id }})"
                                            {{ in_array($question->id, $selectedQuestions) ? 'checked' : '' }}
                                            class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        
                                        <div class="ml-3 flex-1">
                                            <div class="text-sm font-medium text-gray-900 mb-1">
                                                {!! Str::limit(strip_tags($question->question_text), 100) !!}
                                            </div>
                                            
                                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                                <span class="inline-flex items-center">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    {{ ucwords(str_replace('_', ' ', $question->question_type)) }}
                                                </span>
                                                
                                                <span class="inline-flex items-center">
                                                    <i class="fas fa-signal mr-1"></i>
                                                    {{ ucfirst($question->difficulty_level ?? 'intermediate') }}
                                                </span>
                                                
                                                @if($question->points)
                                                    <span class="inline-flex items-center">
                                                        <i class="fas fa-star mr-1"></i>
                                                        {{ $question->points }} pts
                                                    </span>
                                                @endif
                                                
                                                @if($question->assessment && $question->assessment->course)
                                                    <span class="inline-flex items-center">
                                                        <i class="fas fa-book mr-1"></i>
                                                        {{ Str::limit($question->assessment->course->title, 20) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-search text-2xl mb-2"></i>
                                        <p>No questions found matching your criteria.</p>
                                        <button 
                                            wire:click="loadAvailableQuestions" 
                                            class="mt-2 text-blue-600 hover:text-blue-800">
                                            Refresh questions
                                        </button>
                                    </div>
                                @endforelse
                            </div>

                            @if(count($selectedQuestions) > 0)
                                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                        <span class="text-sm font-medium text-green-800">
                                            {{ count($selectedQuestions) }} question(s) selected for this exam
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                @elseif($currentStep === 4)
                    <!-- Step 4: Review & Publish -->
                    <div class="space-y-6">
                        <!-- Exam Summary -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="p-6">
                                <div class="flex items-center mb-6">
                                    <i class="fas fa-clipboard-check text-blue-600 text-xl mr-3"></i>
                                    <h2 class="text-xl font-semibold text-gray-900">Review & Publish</h2>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    <!-- Left Column -->
                                    <div class="space-y-6">
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-700 mb-2">Basic Information</h3>
                                            <div class="space-y-2 text-sm">
                                                <div><strong>Title:</strong> {{ $title }}</div>
                                                <div><strong>Course:</strong> {{ $courses->find($course_id)?->title ?? 'N/A' }}</div>
                                                <div><strong>Type:</strong> {{ ucfirst($exam_type) }}</div>
                                                <div><strong>Difficulty:</strong> {{ ucfirst($difficulty_level) }}</div>
                                                @if($description)
                                                    <div><strong>Description:</strong> {{ Str::limit($description, 100) }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div>
                                            <h3 class="text-sm font-medium text-gray-700 mb-2">Configuration</h3>
                                            <div class="space-y-2 text-sm">
                                                <div><strong>Duration:</strong> {{ $duration_minutes }} minutes</div>
                                                <div><strong>Pass Percentage:</strong> {{ $pass_percentage }}%</div>
                                                <div><strong>Max Attempts:</strong> {{ $max_attempts }}</div>
                                                <div><strong>Questions Per Page:</strong> {{ $questions_per_page }}</div>
                                                @if($max_participants)
                                                    <div><strong>Max Participants:</strong> {{ $max_participants }}</div>
                                                @endif
                                            </div>
                                        </div>

                                        <div>
                                            <h3 class="text-sm font-medium text-gray-700 mb-2">Questions</h3>
                                            <div class="text-sm">
                                                <div><strong>Total Questions:</strong> {{ count($selectedQuestions) }}</div>
                                                @if(count($selectedQuestions) === 0)
                                                    <div class="text-red-600 mt-1">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        No questions selected! Please go back to step 3.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="space-y-6">
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-700 mb-2">Display Settings</h3>
                                            <div class="space-y-2 text-sm">
                                                <div class="flex items-center">
                                                    <i class="fas fa-{{ $randomize_questions ? 'check text-green-600' : 'times text-red-600' }} w-4 mr-2"></i>
                                                    Randomize Questions
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-{{ $randomize_options ? 'check text-green-600' : 'times text-red-600' }} w-4 mr-2"></i>
                                                    Randomize Options
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-{{ $allow_navigation ? 'check text-green-600' : 'times text-red-600' }} w-4 mr-2"></i>
                                                    Allow Navigation
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-{{ $allow_review ? 'check text-green-600' : 'times text-red-600' }} w-4 mr-2"></i>
                                                    Allow Review
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-{{ $show_results_immediately ? 'check text-green-600' : 'times text-red-600' }} w-4 mr-2"></i>
                                                    Show Results Immediately
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <h3 class="text-sm font-medium text-gray-700 mb-2">Security Settings</h3>
                                            <div class="space-y-2 text-sm">
                                                <div class="flex items-center">
                                                    <i class="fas fa-{{ $auto_submit ? 'check text-green-600' : 'times text-red-600' }} w-4 mr-2"></i>
                                                    Auto Submit
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-{{ $prevent_tab_switching ? 'check text-green-600' : 'times text-red-600' }} w-4 mr-2"></i>
                                                    Prevent Tab Switching
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-{{ $webcam_monitoring ? 'check text-green-600' : 'times text-red-600' }} w-4 mr-2"></i>
                                                    Webcam Monitoring
                                                </div>
                                                <div class="flex items-center">
                                                    <i class="fas fa-{{ $restrict_copy_paste ? 'check text-green-600' : 'times text-red-600' }} w-4 mr-2"></i>
                                                    Restrict Copy/Paste
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <h3 class="text-sm font-medium text-gray-700 mb-2">Scheduling</h3>
                                            <div class="space-y-2 text-sm">
                                                <div><strong>Result Delivery:</strong> {{ ucfirst($result_delivery) }}</div>
                                                @if($result_delivery === 'scheduled' && $result_release_date)
                                                    <div><strong>Release Date:</strong> {{ Carbon\Carbon::parse($result_release_date)->format('M d, Y H:i') }}</div>
                                                @endif
                                                @if($start_date)
                                                    <div><strong>Start:</strong> {{ Carbon\Carbon::parse($start_date)->format('M d, Y H:i') }}</div>
                                                @endif
                                                @if($end_date)
                                                    <div><strong>End:</strong> {{ Carbon\Carbon::parse($end_date)->format('M d, Y H:i') }}</div>
                                                @endif
                                                @if($start_time || $end_time)
                                                    <div><strong>Daily Time:</strong> 
                                                        {{ $start_time ?? '00:00' }} - {{ $end_time ?? '23:59' }}
                                                    </div>
                                                @endif
                                                @if(!empty($available_days))
                                                    <div><strong>Available Days:</strong> 
                                                        @php
                                                            $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                                            $selectedDays = collect($available_days)->map(fn($day) => $dayNames[$day])->join(', ');
                                                        @endphp
                                                        {{ $selectedDays }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Validation Warnings -->
                                @if(count($selectedQuestions) === 0)
                                    <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                                            <span class="text-sm font-medium text-red-800">
                                                Cannot publish exam without questions. Please select questions in step 3.
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                @if($duration_minutes < 30 && count($selectedQuestions) > 10)
                                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-circle text-yellow-600 mr-2"></i>
                                            <span class="text-sm text-yellow-800">
                                                Warning: {{ $duration_minutes }} minutes may not be enough for {{ count($selectedQuestions) }} questions.
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Success Message -->
                                @if(count($selectedQuestions) > 0)
                                    <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                            <span class="text-sm font-medium text-green-800">
                                                Exam is ready to be {{ $editingExam ? 'updated' : 'created' }}!
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Final Actions -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Publication Options</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-gray-900">Save as Draft</h4>
                                            <p class="text-sm text-gray-600">Save your exam for later editing. Students won't be able to access it.</p>
                                        </div>
                                        <button 
                                            wire:click="saveDraft"
                                            wire:loading.attr="disabled"
                                            class="ml-4 inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="fas fa-save mr-2"></i>
                                            Save Draft
                                        </button>
                                    </div>

                                    <div class="border-t border-gray-200 pt-4">
                                        <div class="flex items-start">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900">Publish Exam</h4>
                                                <p class="text-sm text-gray-600">Make the exam available to students immediately (subject to scheduling settings).</p>
                                            </div>
                                            <button 
                                                wire:click="publishExam"
                                                wire:loading.attr="disabled"
                                                {{ count($selectedQuestions) === 0 ? 'disabled' : '' }}
                                                class="ml-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-300 disabled:cursor-not-allowed">
                                                <i class="fas fa-rocket mr-2"></i>
                                                {{ $editingExam ? 'Update & Publish' : 'Create & Publish' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar - Preview -->
            <div class="w-80" x-show="showPreview" x-transition>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-8">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Exam Preview</h3>
                            <button 
                                @click="showPreview = false"
                                class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <!-- Exam Card Preview -->
                        <div class="border border-gray-200 rounded-lg p-4 mb-4">
                            @if($exam_thumbnail)
                                <img src="{{ $exam_thumbnail->temporaryUrl() }}" alt="Thumbnail" class="w-full h-32 object-cover rounded-lg mb-3">
                            @else
                                <div class="w-full h-32 bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                            
                            <div class="space-y-2">
                                <h4 class="font-semibold text-gray-900">{{ $title ?: 'Exam Title' }}</h4>
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-600">
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $duration_minutes }} min
                                    </span>
                                    <span class="inline-flex items-center">
                                        <i class="fas fa-question-circle mr-1"></i>
                                        {{ count($selectedQuestions) }} questions
                                    </span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $exam_type === 'practice' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $exam_type === 'mock' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $exam_type === 'final' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $exam_type === 'certification' ? 'bg-purple-100 text-purple-800' : '' }}">
                                        {{ ucfirst($exam_type) }}
                                    </span>
                                    
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $difficulty_level === 'beginner' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $difficulty_level === 'intermediate' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $difficulty_level === 'advanced' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $difficulty_level === 'expert' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($difficulty_level) }}
                                    </span>
                                </div>
                                
                                @if($description)
                                    <p class="text-xs text-gray-600 mt-2">{{ Str::limit($description, 100) }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Pass Mark:</span>
                                <span class="font-medium">{{ $pass_percentage }}%</span>
                            </div>
                            
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Max Attempts:</span>
                                <span class="font-medium">{{ $max_attempts }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600">Questions/Page:</span>
                                <span class="font-medium">{{ $questions_per_page }}</span>
                            </div>
                            
                            @if($max_participants)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-600">Max Participants:</span>
                                    <span class="font-medium">{{ $max_participants }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Status Indicators -->
                        <div class="mt-4 space-y-2">
                            <div class="text-xs font-medium text-gray-700 mb-2">Features Enabled:</div>
                            
                            <div class="grid grid-cols-2 gap-1 text-xs">
                                @if($randomize_questions)
                                    <span class="inline-flex items-center text-green-600">
                                        <i class="fas fa-check mr-1"></i>
                                        Randomize Q's
                                    </span>
                                @endif
                                
                                @if($randomize_options)
                                    <span class="inline-flex items-center text-green-600">
                                        <i class="fas fa-check mr-1"></i>
                                        Randomize O's
                                    </span>
                                @endif
                                
                                @if($allow_navigation)
                                    <span class="inline-flex items-center text-green-600">
                                        <i class="fas fa-check mr-1"></i>
                                        Navigation
                                    </span>
                                @endif
                                
                                @if($allow_review)
                                    <span class="inline-flex items-center text-green-600">
                                        <i class="fas fa-check mr-1"></i>
                                        Review
                                    </span>
                                @endif
                                
                                @if($auto_submit)
                                    <span class="inline-flex items-center text-blue-600">
                                        <i class="fas fa-check mr-1"></i>
                                        Auto Submit
                                    </span>
                                @endif
                                
                                @if($webcam_monitoring)
                                    <span class="inline-flex items-center text-orange-600">
                                        <i class="fas fa-check mr-1"></i>
                                        Webcam
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between items-center pt-8 border-t border-gray-200">
            <button 
                wire:click="previousStep"
                {{ $currentStep === 1 ? 'disabled' : '' }}
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                <i class="fas fa-chevron-left mr-2"></i>
                Previous
            </button>

            <div class="flex space-x-3">
                @if($currentStep < $maxSteps)
                    <button 
                        wire:click="nextStep"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Next
                        <i class="fas fa-chevron-right ml-2"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading wire:target="saveExam,publishExam,saveDraft" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mr-3"></div>
            <span class="text-gray-700">Processing...</span>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('message') }}
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
                <button @click="show = false" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif
</div>