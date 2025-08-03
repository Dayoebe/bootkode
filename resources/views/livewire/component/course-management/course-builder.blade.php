<div class="bg-gray-900 text-white min-h-screen" x-data="courseBuilder()">
    <div class="min-h-screen p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-700 pb-6 mb-8">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <a href="{{ route('all-course') }}"
                    class="px-4 py-2.5 bg-gray-700 text-gray-300 rounded-xl hover:bg-gray-600 transition-colors duration-200 font-semibold">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Courses
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                        Course Builder: <span class="text-blue-400">{{ $course->title }}</span>
                    </h1>
                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-400">
                        <span><i class="fas fa-user mr-1"></i> {{ $course->instructor->name }}</span>
                        <span><i class="fas fa-calendar mr-1"></i> Updated
                            {{ $course->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Course Stats -->
                <div class="hidden lg:flex items-center space-x-4 text-sm">
                    <div class="bg-gray-800 px-3 py-2 rounded-lg">
                        <span class="text-gray-400">Progress:</span>
                        <span class="font-bold text-blue-400">{{ $courseStats['completion_percentage'] }}%</span>
                    </div>
                    <div class="bg-gray-800 px-3 py-2 rounded-lg">
                        <span class="text-gray-400">Lessons:</span>
                        <span
                            class="font-bold text-green-400">{{ $courseStats['published_lessons'] }}/{{ $courseStats['total_lessons'] }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    <button wire:click="exportCourseOutline"
                        class="px-3 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                        <i class="fas fa-download mr-1"></i> Export Outline
                    </button>
                    <button wire:click="saveContent"
                        class="px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-medium">
                        <i class="fas fa-save mr-1"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Course Outline Sidebar -->
            <div class="w-full lg:w-1/3 xl:w-1/4">
                <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg">
                    <!-- Sidebar Header -->
                    <div class="p-6 border-b border-gray-700">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-white">Course Outline</h2>
                            <button wire:click="showAddSectionForm"
                                class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                <i class="fas fa-plus mr-1"></i> Add Section
                            </button>
                        </div>
                        <!-- Course Stats -->
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="bg-gray-700 p-3 rounded-lg text-center">
                                <div class="text-blue-400 font-bold text-lg">{{ $courseStats['total_sections'] }}</div>
                                <div class="text-gray-300">Sections</div>
                            </div>
                            <div class="bg-gray-700 p-3 rounded-lg text-center">
                                <div class="text-green-400 font-bold text-lg">{{ $courseStats['total_lessons'] }}</div>
                                <div class="text-gray-300">Lessons</div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="p-4 border-b border-gray-700">
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="searchTerm"
                                placeholder="Search lessons..."
                                class="w-full bg-gray-700 text-white rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i
                                class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <div class="mt-2 flex space-x-2">
                            <select wire:model.live="filterType"
                                class="w-full bg-gray-700 text-white rounded-lg px-3 py-2 focus:outline-none">
                                <option value="all">All Lessons</option>
                                <option value="published">Published</option>
                                <option value="draft">Drafts</option>
                                <option value="video">Video Lessons</option>
                                <option value="text">Text Lessons</option>
                            </select>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    @if ($showBulkActions)
                        <div class="p-4 border-b border-gray-700 bg-gray-700">
                            <div class="flex items-center justify-between">
                                <span class="text-sm">{{ count($selectedLessons) }} selected</span>
                                <div class="flex space-x-2">
                                    <button wire:click="publishSelectedLessons"
                                        class="px-2 py-1 bg-green-600 text-white rounded text-xs">Publish</button>
                                    <button wire:click="unpublishSelectedLessons"
                                        class="px-2 py-1 bg-yellow-600 text-white rounded text-xs">Unpublish</button>
                                    <button wire:click="moveSelectedLessons"
                                        class="px-2 py-1 bg-blue-600 text-white rounded text-xs">Move</button>
                                    <button wire:click="deleteSelectedLessons"
                                        class="px-2 py-1 bg-red-600 text-white rounded text-xs">Delete</button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Sections List -->
                    <div class="overflow-y-auto max-h-[60vh]">
                        @forelse($filteredSections as $section)
                            <div class="border-b border-gray-700 last:border-0">
                                <!-- Section Header -->
                                <div class="p-4 bg-gray-800 hover:bg-gray-750 transition-colors cursor-pointer group"
                                    wire:click="toggleSection('{{ $section->id }}')">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center space-x-2">
                                            <i
                                                class="fas {{ in_array($section->id, $expandedSections) ? 'fa-chevron-down' : 'fa-chevron-right' }} text-gray-400 text-sm"></i>
                                            <span class="font-medium text-white">{{ $section->title }}</span>
                                        </div>
                                        <div
                                            class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button wire:click.stop="showEditSectionForm({{ $section->id }})"
                                                class="text-gray-400 hover:text-white">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click.stop="confirmDeleteSection({{ $section->id }})"
                                                class="text-gray-400 hover:text-red-400">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button wire:click.stop="duplicateSection({{ $section->id }})"
                                                class="text-gray-400 hover:text-blue-400">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lessons for this section -->
                                @if (in_array($section->id, $expandedSections) && $section->lessons->isNotEmpty())
                                    <div class="mt-3 ml-6 space-y-1" id="lessons-{{ $section->id }}">
                                        @foreach ($section->lessons as $lesson)
                                            <div class="flex items-center justify-between p-2 rounded {{ $activeLesson && $activeLesson->id === $lesson->id ? 'bg-blue-500 bg-opacity-30' : 'hover:bg-gray-600' }} group"
                                                wire:click="selectLesson({{ $lesson->id }})">
                                                <div class="flex items-center space-x-2 flex-1">
                                                    <input type="checkbox"
                                                        wire:click.stop="toggleLessonSelection({{ $lesson->id }})"
                                                        {{ in_array($lesson->id, $selectedLessons) ? 'checked' : '' }}
                                                        class="text-blue-500 rounded focus:ring-blue-500">
                                                    <div>
                                                        <div class="font-medium text-white">{{ $lesson->title }}</div>
                                                        <div class="text-xs text-gray-400 flex items-center space-x-1">
                                                            @if ($lesson->content_type === 'video')
                                                                <i class="fas fa-video"></i>
                                                            @elseif($lesson->content_type === 'text')
                                                                <i class="fas fa-file-alt"></i>
                                                            @elseif($lesson->content_type === 'quiz')
                                                                <i class="fas fa-question-circle"></i>
                                                            @endif
                                                            <span>{{ $lesson->duration_minutes }} min</span>
                                                            @if (!$lesson->is_published)
                                                                <span
                                                                    class="bg-yellow-900 text-yellow-300 px-1.5 py-0.5 rounded text-xs">Draft</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button wire:click.stop="duplicateLesson({{ $lesson->id }})"
                                                        class="text-gray-400 hover:text-blue-400 p-1">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <button wire:click.stop="confirmDeleteLesson({{ $lesson->id }})"
                                                        class="text-gray-400 hover:text-red-400 p-1">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Add Lesson Button -->
                                @if (in_array($section->id, $expandedSections))
                                <div class="px-6 py-3 border-t border-gray-700">
                                    <button wire:click="showAddLessonForm({{ $section->id }})"
                                        class="w-full flex items-center justify-center px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors text-sm">
                                        <i class="fas fa-plus mr-2"></i> Add Lesson
                                    </button>
                                </div>
                            @endif
                            </div>
                        @empty
                            <div class="p-6 text-center text-gray-400">
                                <i class="fas fa-folder-open text-3xl mb-2"></i>
                                <p>No sections yet. Click "Add Section" to get started.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="w-full lg:w-2/3 xl:w-3/4">
                @if ($activeLesson)
                    <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg mb-6">
                        <div class="p-6 border-b border-gray-700">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-white mb-1">{{ $activeLesson->title }}</h2>
                                    <div class="flex flex-wrap items-center text-sm text-gray-400">
                                        <span class="mr-3 mb-1 sm:mb-0"><i class="fas fa-book mr-1"></i> Section
                                            {{ $activeLesson->section->order + 1 }}</span>
                                        <span class="mr-3 mb-1 sm:mb-0"><i class="fas fa-clock mr-1"></i>
                                            {{ $activeLesson->duration_minutes }} min</span>
                                        <span class="mr-3 mb-1 sm:mb-0">
                                            <i
                                                class="fas {{ $activeLesson->content_type === 'video' ? 'fa-video' : ($activeLesson->content_type === 'text' ? 'fa-file-alt' : 'fa-question-circle') }} mr-1"></i>
                                            {{ ucfirst($activeLesson->content_type) }}
                                        </span>
                                        @if (!$activeLesson->is_published)
                                            <span
                                                class="bg-yellow-900 text-yellow-300 px-2 py-0.5 rounded">Draft</span>
                                        @else
                                            <span
                                                class="bg-green-900 text-green-300 px-2 py-0.5 rounded">Published</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex space-x-2 mt-3 sm:mt-0">
                                    <button wire:click="togglePublishLesson"
                                        class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                                 {{ $activeLesson->is_published ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-green-600 text-white hover:bg-green-700' }}">
                                        <i
                                            class="fas {{ $activeLesson->is_published ? 'fa-eye-slash' : 'fa-eye' }} mr-1"></i>
                                        {{ $activeLesson->is_published ? 'Unpublish' : 'Publish' }}
                                    </button>
                                    <button wire:click="previewLesson"
                                        class="px-3 py-1.5 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors text-sm">
                                        <i class="fas fa-eye mr-1"></i> Preview
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <!-- Lesson Editor Tabs -->
                            <div class="border-b border-gray-700 mb-6">
                                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                    <button @click="activeTab = 'content'"
                                        :class="{ 'border-blue-500 text-white': activeTab === 'content', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300': activeTab !== 'content' }"
                                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm focus:outline-none">
                                        Content
                                    </button>
                                    <button @click="activeTab = 'settings'"
                                        :class="{ 'border-blue-500 text-white': activeTab === 'settings', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300': activeTab !== 'settings' }"
                                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm focus:outline-none">
                                        Settings
                                    </button>
                                    @if ($activeLesson->content_type === 'quiz')
                                        <button @click="activeTab = 'quiz'"
                                            :class="{ 'border-blue-500 text-white': activeTab === 'quiz', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-300': activeTab !== 'quiz' }"
                                            class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm focus:outline-none">
                                            Quiz
                                        </button>
                                    @endif
                                </nav>
                            </div>

                            <!-- Content Tab -->
                            <div x-show="activeTab === 'content'" x-cloak>
                                @if ($activeLesson->content_type === 'text')
                                    <div class="mb-6">
                                        <input type="text" wire:model.live.debounce.300ms="activeLesson.title"
                                            placeholder="Lesson title"
                                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-3 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg font-medium">

                                        <trix-editor wire:model.debounce.1000ms="activeLessonContent"
                                            class="trix-content bg-gray-700 text-white rounded-lg min-h-[300px] p-4"
                                            input="lesson-content"></trix-editor>
                                        <input id="lesson-content" type="hidden"
                                            value="{{ $activeLessonContent }}">
                                    </div>
                                @elseif($activeLesson->content_type === 'video')
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Video
                                                Title</label>
                                            <input type="text" wire:model.live.debounce.300ms="activeLesson.title"
                                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Video
                                                URL</label>
                                            <input type="url"
                                                wire:model.live.debounce.300ms="activeLesson.video_url"
                                                placeholder="https://youtube.com/..."
                                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Video
                                                Transcript/Description</label>
                                            <trix-editor wire:model.debounce.1000ms="activeLessonContent"
                                                class="trix-content bg-gray-700 text-white rounded-lg min-h-[200px] p-4"
                                                input="video-description"></trix-editor>
                                            <input id="video-description" type="hidden"
                                                value="{{ $activeLessonContent }}">
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Settings Tab -->
                            <div x-show="activeTab === 'settings'" x-cloak>
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Lesson
                                            Title</label>
                                        <input type="text" wire:model.live.debounce.300ms="activeLesson.title"
                                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Content
                                                Type</label>
                                            <select wire:model.live="activeLesson.content_type"
                                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="text">Text Content</option>
                                                <option value="video">Video Lesson</option>
                                                <option value="quiz">Quiz</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Estimated
                                                Duration (minutes)</label>
                                            <input type="number" wire:model.live="activeLesson.duration_minutes"
                                                min="1" max="300"
                                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="flex items-center">
                                            <input type="checkbox" wire:model.live="activeLesson.is_published"
                                                class="h-4 w-4 text-blue-500 rounded focus:ring-blue-500">
                                            <span class="ml-2 text-sm font-medium text-gray-300">Published</span>
                                        </label>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-300 mb-1">Prerequisites</label>
                                        <select multiple wire:model.live="selectedPrerequisites"
                                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 h-32">
                                            @foreach ($allLessons as $lesson)
                                                @if ($lesson->id !== $activeLesson->id)
                                                    <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Quiz Tab -->
                            <div x-show="activeTab === 'quiz'" x-cloak>
                                @if ($activeLesson->content_type === 'quiz')
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Quiz
                                                Title</label>
                                            <input type="text"
                                                wire:model.live.debounce.300ms="activeLesson.quiz_title"
                                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Quiz
                                                Description</label>
                                            <textarea wire:model.live.debounce.300ms="activeLesson.quiz_description" rows="3"
                                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                        </div>

                                        <div class="border border-gray-600 rounded-lg overflow-hidden">
                                            <div class="bg-gray-700 px-4 py-3 border-b border-gray-600 font-medium">
                                                Questions</div>

                                            @foreach ($questions as $index => $question)
                                                <div class="p-4 border-b border-gray-600 last:border-0">
                                                    <div class="flex justify-between items-start mb-3">
                                                        <div class="font-medium text-white">Question
                                                            #{{ $index + 1 }}</div>
                                                        <button wire:click="removeQuestion({{ $index }})"
                                                            class="text-red-400 hover:text-red-300">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>

                                                    <div class="mb-3">
                                                        <input type="text"
                                                            wire:model.live.debounce.300ms="questions.{{ $index }}.text"
                                                            placeholder="Question text"
                                                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    </div>

                                                    <div class="mb-3">
                                                        <select wire:model.live="questions.{{ $index }}.type"
                                                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                            <option value="multiple_choice">Multiple Choice</option>
                                                            <option value="true_false">True/False</option>
                                                            <option value="short_answer">Short Answer</option>
                                                            <option value="essay">Essay</option>
                                                        </select>
                                                    </div>

                                                    @if ($question['type'] === 'multiple_choice' || $question['type'] === 'true_false')
                                                        <div class="space-y-2 mb-3">
                                                            @foreach ($question['options'] as $optIndex => $option)
                                                                <div class="flex items-center">
                                                                    <input type="radio"
                                                                        wire:model.live="questions.{{ $index }}.correct_answer"
                                                                        value="{{ $optIndex }}"
                                                                        class="h-4 w-4 text-blue-500 focus:ring-blue-500">
                                                                    <input type="text"
                                                                        wire:model.live.debounce.300ms="questions.{{ $index }}.options.{{ $optIndex }}"
                                                                        placeholder="Option {{ $optIndex + 1 }}"
                                                                        class="ml-2 flex-1 bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                                    <button
                                                                        wire:click="removeOption({{ $index }}, {{ $optIndex }})"
                                                                        class="ml-2 text-red-400 hover:text-red-300">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            @endforeach
                                                            <button wire:click="addOption({{ $index }})"
                                                                class="mt-1 px-3 py-1 bg-gray-700 text-gray-300 rounded text-sm hover:bg-gray-600">
                                                                <i class="fas fa-plus mr-1"></i> Add Option
                                                            </button>
                                                        </div>
                                                    @elseif($question['type'] === 'short_answer')
                                                        <div class="mb-3">
                                                            <label
                                                                class="block text-sm font-medium text-gray-300 mb-1">Correct
                                                                Answer</label>
                                                            <input type="text"
                                                                wire:model.live.debounce.300ms="questions.{{ $index }}.correct_answer"
                                                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                        </div>
                                                    @elseif($question['type'] === 'essay')
                                                        <div class="mb-3">
                                                            <label
                                                                class="block text-sm font-medium text-gray-300 mb-1">Grading
                                                                Guidelines</label>
                                                            <textarea wire:model.live.debounce.300ms="questions.{{ $index }}.grading_guidelines" rows="3"
                                                                class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                                        </div>
                                                    @endif

                                                    <div class="mb-3">
                                                        <label
                                                            class="block text-sm font-medium text-gray-300 mb-1">Points</label>
                                                        <input type="number"
                                                            wire:model.live="questions.{{ $index }}.points"
                                                            min="1" max="100"
                                                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    </div>
                                                </div>
                                            @endforeach

                                            <div class="p-4 bg-gray-700">
                                                <button wire:click="addQuestion"
                                                    class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                    <i class="fas fa-plus mr-2"></i> Add Question
                                                </button>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Passing
                                                Percentage</label>
                                            <div class="flex items-center">
                                                <input type="number"
                                                    wire:model.live="activeLesson.quiz_pass_percentage" min="1"
                                                    max="100"
                                                    class="w-20 bg-gray-700 text-white rounded-l-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <div
                                                    class="bg-gray-600 text-gray-200 px-3 py-2.5 rounded-r-lg border border-gray-500">
                                                    %
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg p-12 text-center">
                        <i class="fas fa-book-open text-5xl text-gray-500 mb-4"></i>
                        <h2 class="text-xl font-bold text-white mb-2">Select a Lesson to Edit</h2>
                        <p class="text-gray-400 mb-6 max-w-md mx-auto">Choose a lesson from the course outline on the
                            left to start editing its content, settings, or quiz questions.</p>
                        <div class="flex flex-wrap justify-center gap-4">
                            <button wire:click="showAddSectionForm"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i> Create First Section
                            </button>
                            <button wire:click="previewCourse"
                                class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors">
                                <i class="fas fa-eye mr-2"></i> Preview Course
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Add Section Modal -->
                @if ($showAddSectionForm)
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
                        @click.self="showAddSectionForm = false">
                        <div class="bg-gray-800 rounded-xl w-full max-w-md">
                            <div class="p-6 border-b border-gray-700">
                                <h3 class="text-xl font-bold text-white">Add New Section</h3>
                            </div>
                            <div class="p-6">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Section Title</label>
                                    <input type="text" wire:model.live.debounce.300ms="newSectionTitle"
                                        placeholder="e.g., Introduction to HTML"
                                        class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('newSectionTitle')
                                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Section
                                        Description</label>
                                    <textarea wire:model.live.debounce.300ms="newSectionDescription" rows="3"
                                        placeholder="Brief description of this section"
                                        class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                            </div>
                            <div class="p-6 border-t border-gray-700 flex justify-end space-x-3">
                                <button @click="showAddSectionForm = false"
                                    class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors">
                                    Cancel
                                </button>
                                <button wire:click="addSection"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Create Section
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Add Lesson Modal -->
                @if ($showAddLessonForm)
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
                        @click.self="showAddLessonForm = false">
                        <div class="bg-gray-800 rounded-xl w-full max-w-md">
                            <div class="p-6 border-b border-gray-700">
                                <h3 class="text-xl font-bold text-white">Add New Lesson</h3>
                            </div>
                            <div class="p-6">
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Lesson Title</label>
                                    <input type="text" wire:model.live.debounce.300ms="newLessonTitle"
                                        placeholder="e.g., HTML Basics"
                                        class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('newLessonTitle')
                                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Content Type</label>
                                    <select wire:model.live="newLessonType"
                                        class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="text">Text Content</option>
                                        <option value="video">Video Lesson</option>
                                        <option value="quiz">Quiz</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-300 mb-1">Estimated Duration
                                        (minutes)</label>
                                    <input type="number" wire:model.live="newLessonDuration" min="1"
                                        max="300" value="10"
                                        class="w-full bg-gray-700 text-white rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="p-6 border-t border-gray-700 flex justify-end space-x-3">
                                <button @click="showAddLessonForm = false"
                                    class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors">
                                    Cancel
                                </button>
                                <button wire:click="addLesson"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Create Lesson
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Confirmation Modal -->
                @if ($showConfirmationModal)
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                        <div class="bg-gray-800 rounded-xl w-full max-w-md">
                            <div class="p-6">
                                <div class="text-center">
                                    <div
                                        class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 bg-opacity-10 mb-4">
                                        <i class="fas fa-exclamation-triangle text-red-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-white mb-2">{{ $confirmationTitle }}</h3>
                                    <p class="text-gray-300">{{ $confirmationMessage }}</p>
                                </div>
                            </div>
                            <div class="px-6 pb-6 flex justify-end space-x-3">
                                <button wire:click="cancelConfirmation"
                                    class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors">
                                    Cancel
                                </button>
                                <button
                                    wire:click="{{ $confirmationAction }}({{ implode(',', $confirmationParams) }})"
                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    Confirm
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Preview Modal -->
                <div x-show="showPreviewModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95">
                    <div class="bg-gray-800 rounded-xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col">
                        <div class="flex justify-between items-center p-4 border-b border-gray-700">
                            <h3 class="text-xl font-bold text-white">Lesson Preview</h3>
                            <div class="flex space-x-2">
                                <button @click="showPreviewModal = false" class="text-gray-400 hover:text-white">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex-1 overflow-y-auto p-6">
                            <div class="bg-gray-900 rounded-lg p-6 max-h-[70vh] overflow-y-auto">
                                <h4 class="text-xl font-bold text-white mb-4" x-text="previewLessonData.title"></h4>
                                <div class="prose prose-invert max-w-none" x-html="previewLessonContent"></div>
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-700 flex justify-end">
                            <button @click="showPreviewModal = false"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .trix-button-group--file-tools {
            display: none;
        }

        .trix-content {
            color: #f3f4f6;
        }

        .trix-content p {
            margin-bottom: 1rem;
        }

        .trix-content h1,
        .trix-content h2,
        .trix-content h3 {
            color: white;
            margin: 1.5rem 0 1rem;
        }

        .trix-content ul,
        .trix-content ol {
            padding-left: 1.5rem;
            margin: 1rem 0;
        }

        .trix-content li {
            margin-bottom: 0.5rem;
        }

        .trix-content blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 1rem;
            color: #9ca3af;
            margin: 1rem 0;
        }

        .trix-content code {
            background-color: #374151;
            padding: 0.2em 0.4em;
            border-radius: 0.25rem;
            font-family: 'Courier New', monospace;
        }

        .trix-content pre {
            background-color: #1f2937;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin: 1rem 0;
        }

        .animate-pulse {
            animation: pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .no-print {
            display: none !important;
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('courseBuilder', () => ({
                activeTab: 'content',
                showPreviewModal: false,
                previewLessonData: {},
                previewLessonContent: '',

                init() {
                    this.initializeTrixEditor();
                    this.initializeSortable();
                    this.initializeDragDrop();
                    this.initializeKeyboardShortcuts();

                    // Listen for Livewire events
                    Livewire.on('open-preview-modal', (data) => {
                        this.previewLessonData = data.lesson;
                        this.previewLessonContent = data.content || '';
                        this.showPreviewModal = true;
                    });
                },

                initializeTrixEditor() {
                    document.addEventListener('trix-blur', (event) => {
                        if (event.target.getAttribute('input') === 'lesson-content' ||
                            event.target.getAttribute('input') === 'video-description') {
                            Livewire.dispatch('updatedActiveLessonContent');
                        }
                    });
                },

                initializeSortable() {
                    // Initialize section sorting
                    if (document.getElementById('sections-list')) {
                        new Sortable(document.getElementById('sections-list'), {
                            animation: 150,
                            handle: '.handle',
                            onEnd: function(evt) {
                                Livewire.dispatch('reorder-sections', {
                                    oldIndex: evt.oldIndex,
                                    newIndex: evt.newIndex
                                });
                            }
                        });
                    }

                    // Initialize lesson sorting within sections
                    document.querySelectorAll('[id^="lessons-"]').forEach(container => {
                        new Sortable(container, {
                            animation: 150,
                            onEnd: function(evt) {
                                Livewire.dispatch('reorder-lessons', {
                                    sectionId: container.id.replace(
                                        'lessons-', ''),
                                    oldIndex: evt.oldIndex,
                                    newIndex: evt.newIndex
                                });
                            }
                        });
                    });
                },

                initializeDragDrop() {
                    // Setup drag and drop for lessons between sections
                    const lessonElements = document.querySelectorAll('[id^="lessons-"] > div');
                    const sectionHeaders = document.querySelectorAll('.section-header');

                    lessonElements.forEach(lesson => {
                        lesson.draggable = true;

                        lesson.addEventListener('dragstart', function(e) {
                            e.dataTransfer.setData('lessonId', this.dataset.lessonId);
                            this.classList.add('opacity-50');
                        });

                        lesson.addEventListener('dragend', function() {
                            this.classList.remove('opacity-50');
                        });
                    });

                    sectionHeaders.forEach(header => {
                        header.addEventListener('dragover', function(e) {
                            e.preventDefault();
                            this.classList.add('bg-gray-700');
                        });

                        header.addEventListener('dragleave', function() {
                            this.classList.remove('bg-gray-700');
                        });

                        header.addEventListener('drop', function(e) {
                            e.preventDefault();
                            this.classList.remove('bg-gray-700');

                            const lessonId = e.dataTransfer.getData('lessonId');
                            const sectionId = this.dataset.sectionId;

                            Livewire.dispatch('move-lesson', {
                                lessonId: lessonId,
                                targetSectionId: sectionId
                            });
                        });
                    });
                },

                initializeKeyboardShortcuts() {
                    document.addEventListener('keydown', (e) => {
                        // Ctrl/Cmd + S to save
                        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                            e.preventDefault();
                            Livewire.dispatch('saveContent');
                        }

                        // Ctrl/Cmd + N to add new section
                        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                            e.preventDefault();
                            Livewire.dispatch('showAddSectionForm');
                        }

                        // Ctrl/Cmd + L to add new lesson
                        if ((e.ctrlKey || e.metaKey) && e.key === 'l') {
                            e.preventDefault();
                            if (this.activeSectionId) {
                                Livewire.dispatch('showAddLessonForm', {
                                    sectionId: this.activeSectionId
                                });
                            }
                        }
                    });
                }
            }));
        });

        // Initialize course builder when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Trix editor
            document.addEventListener('trix-initialize', function(event) {
                const editor = event.target.editor;

                // Add custom buttons to the Trix toolbar
                const toolbar = event.target.toolbarElement;
                const fileTools = toolbar.querySelector('.trix-button-group--file-tools');
                if (fileTools) fileTools.remove();

                // Add code block button
                const codeButton = document.createElement('button');
                codeButton.type = 'button';
                codeButton.className = 'trix-button trix-button--icon trix-button--icon-code';
                codeButton.innerHTML = '<i class="fas fa-code"></i>';
                codeButton.title = 'Insert Code Block';
                codeButton.setAttribute('data-trix-attribute', 'code');
                codeButton.setAttribute('data-trix-key', 'b');
                codeButton.setAttribute('data-trix-mutable', 'true');

                const codeGroup = document.createElement('span');
                codeGroup.className = 'trix-button-group trix-button-group--text-tools';
                codeGroup.appendChild(codeButton);

                toolbar.querySelector('.trix-button-group--text-tools').after(codeGroup);
            });

            // Handle Trix editor content updates
            document.addEventListener('trix-change', function(event) {
                const editor = event.target.editor;
                if (event.target.getAttribute('input') === 'lesson-content' ||
                    event.target.getAttribute('input') === 'video-description') {
                    Livewire.dispatch('updatedContentBlocks', {
                        content: editor.getDocument().toString()
                    });
                }
            });

            // Listen for Livewire events
            Livewire.on('content-saved', () => {
                const saveButton = document.querySelector('[wire\\:click="saveContent"]');
                if (saveButton) {
                    saveButton.classList.add('animate-pulse');
                    setTimeout(() => {
                        saveButton.classList.remove('animate-pulse');
                    }, 1000);
                }
            });

            Livewire.on('notify', (message, type) => {
                const notification = document.createElement('div');
                notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg shadow-lg z-50 transition-all duration-300 transform ${
            type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
          } text-white`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateY(-10px)';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 3000);
            });

            // Performance monitoring
            setTimeout(() => {
                const perfData = performance.getEntriesByType('navigation')[0];
                console.log('Course Builder Load Time:', perfData.loadEventEnd - perfData.loadEventStart,
                    'ms');
            }, 0);
        });
    </script>
</div>
