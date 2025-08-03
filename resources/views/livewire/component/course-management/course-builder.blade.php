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
                        <span
                            class="text-green-400 font-semibold ml-1">{{ $courseStats['completion_percentage'] }}%</span>
                    </div>
                    <div class="bg-gray-800 px-3 py-2 rounded-lg">
                        <span class="text-gray-400">Duration:</span>
                        <span class="text-blue-400 font-semibold ml-1">{{ floor($courseStats['total_duration'] / 60) }}h
                            {{ $courseStats['total_duration'] % 60 }}m</span>
                    </div>
                </div>

                <span class="text-gray-400 hidden sm:block">
                    Status: <span class="text-{{ $course->is_published ? 'green' : 'yellow' }}-400 font-semibold">
                        {{ $course->is_published ? 'Published' : 'Draft' }}
                    </span>
                </span>
                <button wire:click="togglePublished"
                    class="px-4 sm:px-6 py-2.5 bg-gradient-to-r {{ $course->is_published ? 'from-red-600 to-pink-600' : 'from-green-600 to-emerald-600' }} text-white rounded-xl font-semibold hover:opacity-90 transition-all duration-300 shadow-lg">
                    <i class="fas fa-{{ $course->is_published ? 'eye-slash' : 'eye' }} mr-1"></i>
                    {{ $course->is_published ? 'Unpublish' : 'Publish' }}
                </button>
                <button wire:click="saveContent"
                    class="px-4 sm:px-6 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl font-semibold hover:opacity-90 transition-all duration-300 shadow-lg"
                    :disabled="!@this.isDirty">
                    <i class="fas fa-save mr-1"></i>
                    <span x-show="@this.isDirty" class="hidden sm:inline">Save Changes</span>
                    <span x-show="!@this.isDirty" class="hidden sm:inline">Saved</span>
                    <span class="sm:hidden">Save</span>
                </button>

                <!-- More Actions Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="px-3 py-2.5 bg-gray-700 text-gray-300 rounded-xl hover:bg-gray-600 transition-colors">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-xl border border-gray-700 z-50">
                        <a href="#" wire:click="previewLesson"
                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 rounded-t-lg">
                            <i class="fas fa-eye mr-2"></i> Preview Course
                        </a>
                        <a href="#" wire:click="exportCourseOutline"
                            class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">
                            <i class="fas fa-download mr-2"></i> Export Outline
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 rounded-b-lg">
                            <i class="fas fa-cog mr-2"></i> Course Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto-save indicator -->
        @if ($autoSaveMessage)
            <div class="fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-all duration-300"
                x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                <i class="fas fa-check mr-2"></i>{{ $autoSaveMessage }}
            </div>
        @endif

        <!-- Search and Filter Bar -->
        <div class="bg-gray-800 rounded-xl p-4 mb-6 border border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="searchTerm"
                            placeholder="Search lessons and sections..."
                            class="w-full pl-10 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            x-ref="searchInput">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <select wire:model.live="filterType"
                        class="px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                        <option value="all">All Content Types</option>
                        <option value="text">Text Content</option>
                        <option value="video">Video Lessons</option>
                        <option value="file">File Resources</option>
                    </select>

                    @if (count($selectedLessons) > 0)
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-400">{{ count($selectedLessons) }} selected</span>
                            <button wire:click="bulkDeleteLessons"
                                class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                                <i class="fas fa-trash mr-1"></i> Delete
                            </button>
                            <button wire:click="deselectAllLessons"
                                class="px-3 py-1 bg-gray-600 text-white rounded text-sm hover:bg-gray-700">
                                Clear
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar - Course Outline -->
            <div class="lg:col-span-1 order-2 lg:order-1">
                <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl sticky top-8">
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

                        <!-- Progress Bar -->
                        <div class="mt-4">
                            <div class="flex justify-between text-sm text-gray-400 mb-1">
                                <span>Course Progress</span>
                                <span>{{ $courseStats['completion_percentage'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-600 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-green-500 h-2 rounded-full transition-all duration-300"
                                    style="width: {{ $courseStats['completion_percentage'] }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Sections List -->
                    <div class="p-4 max-h-96 overflow-y-auto">
                        <div id="sectionsList" class="space-y-2">
                            @forelse($filteredSections as $section)
                                <div class="section-item {{ $activeLesson && $activeLesson->course_section_id === $section->id ? 'bg-blue-600 bg-opacity-20 border-blue-500' : 'bg-gray-700 hover:bg-gray-600 border-gray-600' }} rounded-lg border cursor-pointer transition-all duration-200"
                                    data-section="{{ $section->id }}">
                                    <div class="p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3 flex-1">
                                                <i class="fas fa-grip-vertical text-gray-400 cursor-move"></i>
                                                <div class="flex-1">
                                                    @if ($editingSectionId === $section->id)
                                                        <div class="space-y-2">
                                                            <input type="text" wire:model="editingSectionTitle"
                                                                class="w-full bg-gray-800 border border-gray-600 rounded px-2 py-1 text-sm text-white"
                                                                wire:keydown.enter="updateSection"
                                                                wire:keydown.escape="cancelEditSection">
                                                            <textarea wire:model="editingSectionDescription"
                                                                class="w-full bg-gray-800 border border-gray-600 rounded px-2 py-1 text-sm text-white resize-none" rows="2"
                                                                placeholder="Section description..."></textarea>
                                                            <div class="flex space-x-2">
                                                                <button wire:click="updateSection"
                                                                    class="text-green-400 hover:text-green-300 text-sm">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                                <button wire:click="cancelEditSection"
                                                                    class="text-red-400 hover:text-red-300 text-sm">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <h3
                                                            class="font-semibold {{ $activeLesson && $activeLesson->course_section_id === $section->id ? 'text-blue-300' : 'text-white' }}">
                                                            {{ $section->title }}
                                                        </h3>
                                                        @if ($section->description)
                                                            <p
                                                                class="text-xs {{ $activeLesson && $activeLesson->course_section_id === $section->id ? 'text-blue-200' : 'text-gray-400' }} mt-1">
                                                                {{ Str::limit($section->description, 60) }}
                                                            </p>
                                                        @endif
                                                        <p
                                                            class="text-xs {{ $activeLesson && $activeLesson->course_section_id === $section->id ? 'text-blue-200' : 'text-gray-400' }} mt-1">
                                                            {{ $section->lessons->count() }} lessons •
                                                            {{ $section->lessons->sum('duration_minutes') }} min
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                @if ($activeLesson && $activeLesson->course_section_id === $section->id)
                                                    <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                                                @endif
                                                <div class="flex items-center space-x-1" x-data="{ open: false }">
                                                    <button @click="open = !open"
                                                        class="text-gray-400 hover:text-gray-300 text-sm">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div x-show="open" @click.outside="open = false" x-transition
                                                        class="absolute right-0 mt-8 w-32 bg-gray-900 rounded-lg shadow-xl border border-gray-600 z-10">
                                                        <button wire:click="editSection({{ $section->id }})"
                                                            class="block w-full text-left px-3 py-2 text-sm text-gray-300 hover:bg-gray-800 rounded-t-lg">
                                                            <i class="fas fa-edit mr-2"></i> Edit
                                                        </button>
                                                        <button wire:click="duplicateSection({{ $section->id }})"
                                                            class="block w-full text-left px-3 py-2 text-sm text-gray-300 hover:bg-gray-800">
                                                            <i class="fas fa-copy mr-2"></i> Duplicate
                                                        </button>
                                                        <button wire:click="deleteSection({{ $section->id }})"
                                                            class="block w-full text-left px-3 py-2 text-sm text-red-400 hover:bg-gray-800 rounded-b-lg">
                                                            <i class="fas fa-trash mr-2"></i> Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Lessons for this section -->
                                        @if ($section->lessons->isNotEmpty())
                                            <div class="mt-3 ml-6 space-y-1" id="lessons-{{ $section->id }}">
                                                @foreach ($section->lessons as $lesson)
                                                    <div class="flex items-center justify-between p-2 rounded {{ $activeLesson && $activeLesson->id === $lesson->id ? 'bg-blue-500 bg-opacity-30' : 'hover:bg-gray-600' }} group"
                                                        wire:click="selectLesson({{ $lesson->id }})">
                                                        <div class="flex items-center space-x-2 flex-1">
                                                            <input type="checkbox"
                                                                wire:click.stop="toggleLessonSelection({{ $lesson->id }})"
                                                                {{ in_array($lesson->id, $selectedLessons) ? 'checked' : '' }}
                                                                class="rounded text-blue-500 bg-gray-700 border-gray-600 focus:ring-blue-500 focus:ring-offset-gray-800">
                                                            <i
                                                                class="fas fa-grip-vertical text-gray-500 text-xs cursor-move opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                                            <i
                                                                class="fas fa-{{ $lesson->content_type === 'video' ? 'video' : ($lesson->content_type === 'file' ? 'file' : 'file-text') }} text-xs text-gray-400"></i>
                                                            <div class="flex-1">
                                                                @if ($editingLessonId === $lesson->id)
                                                                    <div class="space-y-1">
                                                                        <input type="text"
                                                                            wire:model="editingLessonTitle"
                                                                            class="w-full bg-gray-800 border border-gray-600 rounded px-2 py-1 text-xs text-white"
                                                                            wire:keydown.enter="updateLesson"
                                                                            wire:keydown.escape="cancelEditLesson">
                                                                        <div class="flex space-x-1">
                                                                            <button wire:click="updateLesson"
                                                                                class="text-green-400 hover:text-green-300 text-xs">
                                                                                <i class="fas fa-check"></i>
                                                                            </button>
                                                                            <button wire:click="cancelEditLesson"
                                                                                class="text-red-400 hover:text-red-300 text-xs">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <span
                                                                        class="text-sm {{ $activeLesson && $activeLesson->id === $lesson->id ? 'text-white font-medium' : 'text-gray-300' }}">
                                                                        {{ $lesson->title }}
                                                                    </span>
                                                                    @if ($lesson->duration_minutes)
                                                                        <div class="text-xs text-gray-500">
                                                                            {{ $lesson->duration_minutes }} min</div>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <button wire:click.stop="editLesson({{ $lesson->id }})"
                                                                class="text-blue-400 hover:text-blue-300 text-xs">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button
                                                                wire:click.stop="duplicateLesson({{ $lesson->id }})"
                                                                class="text-green-400 hover:text-green-300 text-xs">
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                            <button
                                                                wire:click.stop="deleteLesson({{ $lesson->id }})"
                                                                class="text-red-400 hover:text-red-300 text-xs">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- Add lesson button -->
                                        @if ($isAddingLessonToSectionId === $section->id)
                                            <div class="mt-3 p-3 bg-gray-600 rounded-lg">
                                                <input type="text" wire:model="newLessonTitle"
                                                    placeholder="Lesson title..."
                                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 mb-2"
                                                    wire:keydown.enter="addLesson">
                                                <textarea wire:model="newLessonDescription" placeholder="Lesson description (optional)..."
                                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 mb-2 resize-none"
                                                    rows="2"></textarea>
                                                <div class="grid grid-cols-2 gap-2 mb-2">
                                                    <select wire:model="newLessonContentType"
                                                        class="px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                                                        <option value="text">Text Content</option>
                                                        <option value="video">Video</option>
                                                        <option value="file">File</option>
                                                    </select>
                                                    <input type="number" wire:model="newLessonDuration"
                                                        placeholder="Duration (min)"
                                                        class="px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400"
                                                        min="0" max="1440">
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button wire:click="addLesson"
                                                        class="flex-1 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                                                        <i class="fas fa-plus mr-1"></i> Add Lesson
                                                    </button>
                                                    <button wire:click="cancelAddLesson"
                                                        class="px-3 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition-colors text-sm">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        @else
                                            <button wire:click="showAddLessonForm({{ $section->id }})"
                                                class="mt-3 w-full px-3 py-2 bg-gray-600 text-gray-300 rounded-lg hover:bg-gray-500 transition-colors text-sm">
                                                <i class="fas fa-plus mr-1"></i> Add Lesson
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-gray-400 py-8">
                                    <i class="fas fa-book-open text-3xl mb-3"></i>
                                    <p>No sections found.</p>
                                    @if (empty($searchTerm))
                                        <p class="text-sm mt-2">Add your first section to get started.</p>
                                    @else
                                        <p class="text-sm mt-2">Try adjusting your search terms.</p>
                                    @endif
                                </div>
                            @endforelse

                            <!-- Add Section Form -->
                            @if ($isAddingSection)
                                <div class="mt-4 p-4 bg-gray-700 rounded-lg border border-gray-600">
                                    <input type="text" wire:model="newSectionTitle" placeholder="Section title..."
                                        class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-3"
                                        wire:keydown.enter="addSection">
                                    <textarea wire:model="newSectionDescription" placeholder="Section description (optional)..."
                                        class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-3 resize-none"
                                        rows="3"></textarea>
                                    <div class="flex space-x-2">
                                        <button wire:click="addSection"
                                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                            <i class="fas fa-plus mr-1"></i> Add Section
                                        </button>
                                        <button wire:click="cancelAddSection"
                                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Editor Area -->
            <div class="lg:col-span-3 order-1 lg:order-2">
                @if ($activeLesson)
                    <!-- Lesson Editor -->
                    <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl">
                        <!-- Editor Header -->
                        <div class="p-6 border-b border-gray-700">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div class="mb-4 sm:mb-0">
                                    <h2 class="text-2xl font-bold text-white mb-2">{{ $activeLesson->title }}</h2>
                                    <div class="flex flex-wrap items-center space-x-4 text-sm text-gray-400">
                                        <span><i class="fas fa-list mr-1"></i> Section:
                                            {{ $course->sections->where('id', $activeLesson->course_section_id)->first()->title ?? 'Unknown' }}</span>
                                        <span><i
                                                class="fas fa-{{ $activeLesson->content_type === 'video' ? 'video' : ($activeLesson->content_type === 'file' ? 'file' : 'file-text') }} mr-1"></i>
                                            {{ ucfirst($activeLesson->content_type) }} Content</span>
                                        @if ($activeLesson->duration_minutes)
                                            <span><i class="fas fa-clock mr-1"></i>
                                                {{ $activeLesson->duration_minutes }} minutes</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <button wire:click="previewLesson"
                                        class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> Preview
                                    </button>
                                    <button wire:click="showQuizModal"
                                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                        <i class="fas fa-question-circle mr-1"></i> Add Quiz
                                    </button>
                                    <button wire:click="saveContent"
                                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold"
                                        :class="{ 'opacity-50': !@this.isDirty }">
                                        <i class="fas fa-save mr-1"></i> Save Lesson
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Content Editor -->
                        <div class="p-6">
                            <!-- Rich Text Toolbar -->
                            <div
                                class="mb-4 flex flex-wrap items-center gap-2 p-3 bg-gray-700 rounded-lg border border-gray-600">
                                <div class="flex items-center space-x-1 border-r border-gray-600 pr-3">
                                    <button type="button" onclick="document.execCommand('bold', false, null)"
                                        class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                                        title="Bold">
                                        <i class="fas fa-bold"></i>
                                    </button>
                                    <button type="button" onclick="document.execCommand('italic', false, null)"
                                        class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                                        title="Italic">
                                        <i class="fas fa-italic"></i>
                                    </button>
                                    <button type="button" onclick="document.execCommand('underline', false, null)"
                                        class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                                        title="Underline">
                                        <i class="fas fa-underline"></i>
                                    </button>
                                </div>

                                <div class="flex items-center space-x-1 border-r border-gray-600 pr-3">
                                    <button type="button" onclick="document.execCommand('formatBlock', false, 'h1')"
                                        class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                                        title="Heading 1">
                                        <i class="fas fa-heading"></i>
                                    </button>
                                    <button type="button"
                                        onclick="document.execCommand('insertUnorderedList', false, null)"
                                        class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                                        title="Bullet List">
                                        <i class="fas fa-list-ul"></i>
                                    </button>
                                    <button type="button"
                                        onclick="document.execCommand('insertOrderedList', false, null)"
                                        class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                                        title="Numbered List">
                                        <i class="fas fa-list-ol"></i>
                                    </button>
                                    <button type="button"
                                        onclick="document.execCommand('formatBlock', false, 'blockquote')"
                                        class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                                        title="Quote">
                                        <i class="fas fa-quote-left"></i>
                                    </button>
                                </div>

                                <div class="flex items-center space-x-1 border-r border-gray-600 pr-3">
                                    <button wire:click="showImageModal"
                                        class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                                        title="Add Image">
                                        <i class="fas fa-image"></i>
                                    </button>
                                    <button wire:click="showVideoModal"
                                        class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                                        title="Add Video">
                                        <i class="fas fa-video"></i>
                                    </button>
                                    <button wire:click="showFileModal"
                                        class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                                        title="Add File">
                                        <i class="fas fa-file-upload"></i>
                                    </button>
                                    <button wire:click="showAudioModal"
                                        class="flex flex-col items-center space-y-2 px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                        <i class="fas fa-music text-xl"></i>
                                        <span class="text-sm">Audio</span>
                                    </button>
                                    <button wire:click="showFileModal"
                                        class="flex flex-col items-center space-y-2 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-file-upload text-xl"></i>
                                        <span class="text-sm">File</span>
                                    </button>
                                    <button wire:click="addCodeBlock"
                                        class="flex flex-col items-center space-y-2 px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                        <i class="fas fa-code text-xl"></i>
                                        <span class="text-sm">Code</span>
                                    </button>
                                    <button wire:click="addNoteBlock"
                                        class="flex flex-col items-center space-y-2 px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                                        <i class="fas fa-lightbulb text-xl"></i>
                                        <span class="text-sm">Note</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Content Editor Area -->
                            <div class="bg-gray-700 rounded-lg p-4 min-h-96">
                                <div wire:ignore>
                                    <trix-editor
                                        class="trix-content bg-gray-800 text-white rounded-lg p-4 min-h-96"
                                        wire:model="activeLessonContent"
                                        wire:key="trix-editor-{{ $activeLesson->id }}"></trix-editor>
                                </div>
                                
                                <!-- Content Blocks -->
                                <div id="contentBlocks" class="mt-4 space-y-4">
                                    @foreach($contentBlocks as $block)
                                        <div class="content-block bg-gray-800 rounded-lg p-4 border border-gray-600" 
                                             data-block-id="{{ $block['id'] }}">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-grip-vertical text-gray-400 cursor-move"></i>
                                                    <span class="text-xs text-gray-400">
                                                        {{ ucfirst($block['type']) }} Block
                                                    </span>
                                                </div>
                                                <button wire:click="removeContentBlock('{{ $block['id'] }}')"
                                                    class="text-red-400 hover:text-red-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            
                                            @if($block['type'] === 'image')
                                                <div class="flex flex-col items-center">
                                                    <img src="{{ Storage::disk('public')->url($block['file_path']) }}" 
                                                         alt="{{ $block['caption'] }}" 
                                                         class="max-w-full h-auto rounded-lg mb-2">
                                                    @if($block['caption'])
                                                        <p class="text-sm text-gray-300">{{ $block['caption'] }}</p>
                                                    @endif
                                                </div>
                                            @elseif($block['type'] === 'video')
                                                @if(isset($block['video_url']))
                                                    <div class="aspect-w-16 aspect-h-9">
                                                        <iframe src="{{ $block['video_url'] }}" 
                                                                class="w-full h-96 rounded-lg" 
                                                                frameborder="0" 
                                                                allowfullscreen></iframe>
                                                    </div>
                                                    <p class="text-sm text-gray-300 mt-2">{{ $block['title'] }}</p>
                                                @else
                                                    <video controls class="w-full rounded-lg">
                                                        <source src="{{ Storage::disk('public')->url($block['file_path']) }}" 
                                                                type="video/mp4">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                    <p class="text-sm text-gray-300 mt-2">{{ $block['title'] }}</p>
                                                @endif
                                            @elseif($block['type'] === 'file')
                                                <div class="flex items-center space-x-3 p-3 bg-gray-700 rounded-lg">
                                                    <i class="fas fa-file text-2xl text-blue-400"></i>
                                                    <div class="flex-1">
                                                        <p class="text-white font-medium">{{ $block['file_name'] }}</p>
                                                        <p class="text-xs text-gray-400">{{ $this->formatFileSize($block['file_size']) }}</p>
                                                        @if($block['description'])
                                                            <p class="text-sm text-gray-300 mt-1">{{ $block['description'] }}</p>
                                                        @endif
                                                    </div>
                                                    <a href="{{ Storage::disk('public')->url($block['file_path']) }}" 
                                                       download="{{ $block['file_name'] }}"
                                                       class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            @elseif($block['type'] === 'audio')
                                                <div class="flex items-center space-x-3 p-3 bg-gray-700 rounded-lg">
                                                    <i class="fas fa-music text-2xl text-purple-400"></i>
                                                    <div class="flex-1">
                                                        <p class="text-white font-medium">{{ $block['title'] }}</p>
                                                        <p class="text-xs text-gray-400">{{ $this->formatFileSize($block['file_size']) }}</p>
                                                    </div>
                                                    <audio controls class="flex-1">
                                                        <source src="{{ Storage::disk('public')->url($block['file_path']) }}" 
                                                                type="audio/mpeg">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </div>
                                            @elseif($block['type'] === 'code')
                                                <div class="relative">
                                                    <div class="flex justify-between items-center bg-gray-900 px-3 py-2 rounded-t-lg">
                                                        <span class="text-sm text-gray-300">{{ $block['title'] }}</span>
                                                        <button onclick="copyToClipboard('{{ $block['id'] }}')"
                                                            class="text-gray-400 hover:text-white text-sm">
                                                            <i class="fas fa-copy mr-1"></i> Copy
                                                        </button>
                                                    </div>
                                                    <textarea id="code-{{ $block['id'] }}" 
                                                              class="w-full bg-gray-900 text-green-400 font-mono text-sm p-3 rounded-b-lg border-t-0 border-gray-700"
                                                              rows="8" readonly>{{ $block['code'] }}</textarea>
                                                </div>
                                            @elseif($block['type'] === 'note')
                                                <div class="note-{{ $block['note_type'] }} p-4 rounded-lg">
                                                    <div class="flex items-start space-x-3">
                                                        <i class="fas fa-{{ $this->getNoteIcon($block['note_type']) }} text-lg mt-1"></i>
                                                        <div>
                                                            <h4 class="font-bold text-white">{{ $block['title'] }}</h4>
                                                            <p class="text-gray-300 mt-1">{{ $block['content'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quiz Section -->
                    @if ($activeQuiz)
                        <div class="mt-6 bg-gray-800 rounded-xl border border-gray-700 shadow-xl">
                            <div class="p-6 border-b border-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-xl font-bold text-white">{{ $activeQuiz->title }}</h3>
                                        <p class="text-gray-400 mt-1">{{ $activeQuiz->description }}</p>
                                        <p class="text-sm text-gray-500 mt-1">Pass percentage:
                                            {{ $activeQuiz->pass_percentage }}%</p>
                                    </div>
                                    <button wire:click="$set('activeQuiz', null)"
                                        class="text-gray-400 hover:text-white">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="p-6">
                                <!-- Add Question Form -->
                                <div class="bg-gray-700 rounded-lg p-4 mb-6">
                                    <h4 class="text-lg font-semibold text-white mb-4">Add New Question</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-2">Question
                                                Text</label>
                                            <textarea wire:model="newQuestionText"
                                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 resize-none"
                                                rows="3" placeholder="Enter your question..."></textarea>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-2">Question
                                                Type</label>
                                            <select wire:model.live="newQuestionType"
                                                class="px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                                                <option value="multiple_choice">Multiple Choice</option>
                                                <option value="true_false">True/False</option>
                                                <option value="short_answer">Short Answer</option>
                                                <option value="essay">Essay</option>
                                            </select>
                                        </div>

                                        @if ($newQuestionType === 'multiple_choice')
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-2">Answer
                                                    Options</label>
                                                @for ($i = 0; $i < 4; $i++)
                                                    <div class="flex items-center space-x-2 mb-2">
                                                        <input type="radio" name="correct_option"
                                                            value="{{ $i }}"
                                                            wire:click="$set('correctOptionIndex', {{ $i }})"
                                                            {{ $correctOptionIndex === $i ? 'checked' : '' }}
                                                            class="text-green-500 bg-gray-700 border-gray-600 focus:ring-green-500 focus:ring-offset-gray-800">
                                                        <input type="text"
                                                            wire:model="newQuestionOptions.{{ $i }}"
                                                            placeholder="Option {{ $i + 1 }}..."
                                                            class="flex-1 px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400">
                                                    </div>
                                                @endfor
                                                <p class="text-xs text-gray-500 mt-1">Select the radio button next to
                                                    the correct answer</p>
                                            </div>
                                        @elseif($newQuestionType === 'true_false')
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-2">Correct
                                                    Answer</label>
                                                <div class="flex space-x-4">
                                                    <label class="flex items-center">
                                                        <input type="radio" name="true_false_answer" value="0"
                                                            wire:click="$set('correctOptionIndex', 0)"
                                                            {{ $correctOptionIndex === 0 ? 'checked' : '' }}
                                                            class="text-green-500 bg-gray-700 border-gray-600 focus:ring-green-500 focus:ring-offset-gray-800">
                                                        <span class="ml-2 text-white">True</span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="true_false_answer" value="1"
                                                            wire:click="$set('correctOptionIndex', 1)"
                                                            {{ $correctOptionIndex === 1 ? 'checked' : '' }}
                                                            class="text-green-500 bg-gray-700 border-gray-600 focus:ring-green-500 focus:ring-offset-gray-800">
                                                        <span class="ml-2 text-white">False</span>
                                                    </label>
                                                </div>
                                            </div>
                                        @elseif(in_array($newQuestionType, ['short_answer', 'essay']))
                                            <div>
                                                <label class="block text-sm font-medium text-gray-300 mb-2">Correct
                                                    Answer</label>
                                                <textarea wire:model="newQuestionCorrectAnswer"
                                                    class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 resize-none"
                                                    rows="{{ $newQuestionType === 'essay' ? '4' : '2' }}" placeholder="Enter the correct answer..."></textarea>
                                            </div>
                                        @endif

                                        <button wire:click="addQuestion"
                                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                            <i class="fas fa-plus mr-1"></i> Add Question
                                        </button>
                                    </div>
                                </div>

                                <!-- Questions List -->
                                @if ($activeQuiz->questions->count() > 0)
                                    <div class="space-y-4">
                                        <h4 class="text-lg font-semibold text-white">Questions
                                            ({{ $activeQuiz->questions->count() }})</h4>
                                        @foreach ($activeQuiz->questions as $question)
                                            <div class="bg-gray-700 rounded-lg p-4">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex-1">
                                                        <div class="flex items-center space-x-2 mb-2">
                                                            <span
                                                                class="px-2 py-1 bg-purple-600 text-white text-xs rounded">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                                                        </div>
                                                        <p class="text-white font-medium mb-2">
                                                            {{ $question->question_text }}</p>

                                                        @if ($question->type === 'multiple_choice' && $question->options->count() > 0)
                                                            <div class="space-y-1">
                                                                @foreach ($question->options as $option)
                                                                    <div class="flex items-center space-x-2">
                                                                        <i
                                                                            class="fas fa-{{ $option->is_correct ? 'check-circle text-green-400' : 'circle text-gray-500' }}"></i>
                                                                        <span
                                                                            class="text-gray-300">{{ $option->option_text }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @elseif($question->type === 'true_false' && $question->options->count() > 0)
                                                            <div class="space-y-1">
                                                                @foreach ($question->options as $option)
                                                                    <div class="flex items-center space-x-2">
                                                                        <i
                                                                            class="fas fa-{{ $option->is_correct ? 'check-circle text-green-400' : 'circle text-gray-500' }}"></i>
                                                                        <span
                                                                            class="text-gray-300">{{ $option->option_text }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @elseif(in_array($question->type, ['short_answer', 'essay']))
                                                            <div class="bg-gray-800 rounded p-3">
                                                                <p class="text-sm text-gray-400 mb-1">Correct Answer:
                                                                </p>
                                                                <p class="text-gray-300">
                                                                    {{ $question->correct_answer }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <button wire:click="deleteQuestion({{ $question->id }})"
                                                        class="text-red-400 hover:text-red-300 ml-4">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-gray-400 py-8">
                                        <i class="fas fa-question-circle text-3xl mb-3"></i>
                                        <p>No questions added yet.</p>
                                        <p class="text-sm mt-2">Add your first question above.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <!-- Welcome State -->
                    <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl p-12 text-center">
                        <div class="max-w-md mx-auto">
                            <div
                                class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-book-open text-2xl text-white"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-4">Ready to Create Amazing Content?</h3>
                            <p class="text-gray-400 mb-6">Select a lesson from the course outline to start writing your
                                content. You can add text, images, videos, and files all in one place.</p>

                            <!-- Quick Stats -->
                            <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <div class="text-blue-400 font-bold text-2xl">{{ $courseStats['total_sections'] }}
                                    </div>
                                    <div class="text-gray-300">Sections</div>
                                </div>
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <div class="text-green-400 font-bold text-2xl">{{ $courseStats['total_lessons'] }}
                                    </div>
                                    <div class="text-gray-300">Lessons</div>
                                </div>
                            </div>

                            <button wire:click="showAddSectionForm"
                                class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors font-semibold mb-4">
                                <i class="fas fa-plus mr-2"></i> Create Your First Section
                            </button>

                            <div class="text-sm text-gray-500 space-y-2">
                                <p>💡 <strong>Tip:</strong> Use keyboard shortcuts to work faster</p>
                                <div class="flex justify-center space-x-4 text-xs">
                                    <span><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+S</kbd> Save</span>
                                    <span><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+N</kbd> New Section</span>
                                    <span><kbd class="bg-gray-700 px-2 py-1 rounded">Ctrl+/</kbd> Search</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modals -->

        <!-- Image Upload Modal -->
        @if ($showImageModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                wire:click.self="closeModals">
                <div class="bg-gray-800 rounded-xl border border-gray-700 max-w-md w-full">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-white">Add Image</h3>
                            <button wire:click="closeModals" class="text-gray-400 hover:text-white">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Upload Image</label>
                                <input type="file" wire:model="mediaFile" accept="image/*"
                                    class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                                @error('mediaFile')
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Image Caption
                                    (Optional)</label>
                                <input type="text" wire:model="mediaCaption" placeholder="Describe your image..."
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="flex space-x-3">
                                <button wire:click="addImage"
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus mr-1"></i> Add Image
                                </button>
                                <button wire:click="closeModals"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Video Upload Modal -->
        @if ($showVideoModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                wire:click.self="closeModals">
                <div class="bg-gray-800 rounded-xl border border-gray-700 max-w-md w-full">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-white">Add Video</h3>
                            <button wire:click="closeModals" class="text-gray-400 hover:text-white">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">YouTube URL</label>
                                <input type="url" wire:model="videoUrl"
                                    placeholder="https://youtube.com/watch?v=..."
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('videoUrl')
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="text-center text-gray-400">OR</div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Upload Video File</label>
                                <input type="file" wire:model="mediaFile" accept="video/*"
                                    class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-600 file:text-white hover:file:bg-red-700">
                                @error('mediaFile')
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Video Title
                                    (Optional)</label>
                                <input type="text" wire:model="videoTitle" placeholder="Video title..."
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="flex space-x-3">
                                <button wire:click="addVideo"
                                    class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-plus mr-1"></i> Add Video
                                </button>
                                <button wire:click="closeModals"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Audio Upload Modal -->
        @if ($showAudioModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                wire:click.self="closeModals">
                <div class="bg-gray-800 rounded-xl border border-gray-700 max-w-md w-full">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-white">Add Audio</h3>
                            <button wire:click="closeModals" class="text-gray-400 hover:text-white">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Upload Audio File</label>
                                <input type="file" wire:model="mediaFile" accept="audio/*"
                                    class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                                @error('mediaFile')
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Audio Title
                                    (Optional)</label>
                                <input type="text" wire:model="audioTitle" placeholder="Audio title..."
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="flex space-x-3">
                                <button wire:click="addAudio"
                                    class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    <i class="fas fa-plus mr-1"></i> Add Audio
                                </button>
                                <button wire:click="closeModals"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- File Upload Modal -->
        @if ($showFileModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                wire:click.self="closeModals">
                <div class="bg-gray-800 rounded-xl border border-gray-700 max-w-md w-full">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-white">Add File</h3>
                            <button wire:click="closeModals" class="text-gray-400 hover:text-white">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Upload File</label>
                                <input type="file" wire:model="mediaFile"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar"
                                    class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700">
                                @error('mediaFile')
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Supported formats: PDF, DOC, DOCX, XLS, XLSX,
                                    PPT, PPTX, TXT, ZIP, RAR</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">File Description</label>
                                <input type="text" wire:model="fileDescription"
                                    placeholder="Describe this file..."
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div class="flex space-x-3">
                                <button wire:click="addFile"
                                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-plus mr-1"></i> Add File
                                </button>
                                <button wire:click="closeModals"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quiz Modal -->
        @if ($showQuizModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
                wire:click.self="closeModals">
                <div class="bg-gray-800 rounded-xl border border-gray-700 max-w-md w-full">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-white">Create Quiz</h3>
                            <button wire:click="closeModals" class="text-gray-400 hover:text-white">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Quiz Title</label>
                                <input type="text" wire:model="newQuizTitle" placeholder="Quiz title..."
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('newQuizTitle')
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Quiz Description
                                    (Optional)</label>
                                <textarea wire:model="newQuizDescription" placeholder="Quiz description..."
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                    rows="3"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Pass Percentage</label>
                                <input type="number" wire:model="newQuizPassPercentage" min="1"
                                    max="100"
                                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('newQuizPassPercentage')
                                    <span class="text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex space-x-3">
                                <button wire:click="addQuiz"
                                    class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    <i class="fas fa-plus mr-1"></i> Create Quiz
                                </button>
                                <button wire:click="closeModals"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- JavaScript for enhanced functionality -->
    <script>
        function courseBuilder() {
            return {
                init() {
                    this.initializeKeyboardShortcuts();
                    this.initializeTrixEditor();
                    this.initializeSortable();
                    this.initializeDragDrop();
                },

                initializeKeyboardShortcuts() {
                    document.addEventListener('keydown', (e) => {
                        // Ctrl/Cmd + S to save
                        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                            e.preventDefault();
                            @this.call('saveContent');
                        }

                        // Ctrl/Cmd + N to add new section
                        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                            e.preventDefault();
                            @this.call('showAddSectionForm');
                        }

                        // Ctrl/Cmd + / to focus search
                        if ((e.ctrlKey || e.metaKey) && e.key === '/') {
                            e.preventDefault();
                            this.$refs.searchInput?.focus();
                        }

                        // Escape to close modals
                        if (e.key === 'Escape') {
                            @this.call('closeModals');
                        }
                    });
                },

                initializeTrixEditor() {
                    document.addEventListener('trix-change', (e) => {
                        @this.set('activeLessonContent', e.target.value);
                    });

                    // Auto-save functionality
                    let autoSaveTimeout;
                    document.addEventListener('trix-change', () => {
                        clearTimeout(autoSaveTimeout);
                        autoSaveTimeout = setTimeout(() => {
                            @this.call('autoSave');
                        }, 3000);
                    });
                },

                initializeSortable() {
                    if (typeof Sortable !== 'undefined') {
                        // Make content blocks sortable
                        const contentBlocksContainer = document.getElementById('contentBlocks');
                        if (contentBlocksContainer) {
                            new Sortable(contentBlocksContainer, {
                                handle: '.fa-grip-vertical',
                                animation: 150,
                                ghostClass: 'sortable-ghost',
                                onEnd: (evt) => {
                                    const orderedIds = Array.from(contentBlocksContainer.children).map(
                                        el => el.getAttribute('data-block-id')
                                    );
                                    @this.call('reorderContentBlocks', orderedIds);
                                }
                            });
                        }

                        // Make sections sortable
                        const sectionsList = document.getElementById('sectionsList');
                        if (sectionsList) {
                            new Sortable(sectionsList, {
                                handle: '.fa-grip-vertical',
                                animation: 150,
                                ghostClass: 'sortable-ghost',
                                onEnd: (evt) => {
                                    const orderedIds = Array.from(sectionsList.children)
                                        .filter(el => el.hasAttribute('data-section'))
                                        .map(el => el.getAttribute('data-section'));
                                    @this.call('reorderSections', orderedIds);
                                }
                            });
                        }

                        // Make lessons sortable within each section
                        document.querySelectorAll('[id^="lessons-"]').forEach(lessonContainer => {
                            const sectionId = lessonContainer.id.replace('lessons-', '');
                            new Sortable(lessonContainer, {
                                handle: '.fa-grip-vertical',
                                animation: 150,
                                ghostClass: 'sortable-ghost',
                                group: 'lessons',
                                onEnd: (evt) => {
                                    const orderedIds = Array.from(evt.to.children).map(
                                        el => el.getAttribute('data-lesson-id')
                                    ).filter(id => id);
                                    const targetSectionId = evt.to.id.replace('lessons-', '');
                                    @this.call('reorderLessons', orderedIds, targetSectionId);
                                }
                            });
                        });
                    }
                },

                initializeDragDrop() {
                    // Handle file drag and drop
                    const dropZones = document.querySelectorAll('.trix-content');

                    dropZones.forEach(zone => {
                        zone.addEventListener('dragover', (e) => {
                            e.preventDefault();
                            zone.classList.add('border-blue-500');
                        });

                        zone.addEventListener('dragleave', (e) => {
                            e.preventDefault();
                            zone.classList.remove('border-blue-500');
                        });

                        zone.addEventListener('drop', (e) => {
                            e.preventDefault();
                            zone.classList.remove('border-blue-500');

                            const files = e.dataTransfer.files;
                            if (files.length > 0) {
                                const file = files[0];
                                if (file.type.startsWith('image/')) {
                                    @this.set('mediaFile', file);
                                    @this.call('showImageModal');
                                } else if (file.type.startsWith('video/')) {
                                    @this.set('mediaFile', file);
                                    @this.call('showVideoModal');
                                } else if (file.type.startsWith('audio/')) {
                                    @this.set('mediaFile', file);
                                    @this.call('showAudioModal');
                                } else {
                                    @this.set('mediaFile', file);
                                    @this.call('showFileModal');
                                }
                            }
                        });
                    });
                }
            }
        }

        // Utility functions
        function insertLink() {
            const url = prompt('Enter the URL:');
            if (url) {
                document.execCommand('createLink', false, url);
            }
        }

        function copyToClipboard(blockId) {
            const codeElement = document.getElementById('code-' + blockId);
            if (codeElement) {
                navigator.clipboard.writeText(codeElement.value).then(() => {
                    // Show toast notification
                    const toast = document.createElement('div');
                    toast.className =
                        'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                    toast.innerHTML = '<i class="fas fa-check mr-2"></i>Code copied to clipboard!';
                    document.body.appendChild(toast);
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 3000);
                });
            }
        }

        // Auto-resize textareas
        document.addEventListener('input', (e) => {
            if (e.target.tagName === 'TEXTAREA') {
                e.target.style.height = 'auto';
                e.target.style.height = e.target.scrollHeight + 'px';
            }
        });

        // Initialize existing textareas
        document.querySelectorAll('textarea').forEach(textarea => {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        });

        // Confirmation dialog for deletions
        window.addEventListener('confirm-delete', (e) => {
            if (confirm(e.detail.message)) {
                @this.call(e.detail.action, ...e.detail.params);
            }
        });

        // Listen for Livewire events
        document.addEventListener('livewire:navigated', () => {
            // Reinitialize components after navigation
            setTimeout(() => {
                if (window.courseBuilderInstance) {
                    window.courseBuilderInstance.init();
                }
            }, 100);
        });

        // Update Trix content when lesson changes
        Livewire.on('lesson-selected', (lessonId) => {
            setTimeout(() => {
                const trixEditor = document.querySelector('trix-editor');
                if (trixEditor) {
                    trixEditor.editor.loadHTML(@this.get('activeLessonContent') || '');
                }
            }, 100);
        });

        // Clear auto-save message
        window.addEventListener('auto-saved', () => {
            setTimeout(() => {
                @this.set('autoSaveMessage', '');
            }, 3000);
        });

        // Progress bar animation
        document.addEventListener('DOMContentLoaded', () => {
            const progressBars = document.querySelectorAll('[style*="width:"]');
            progressBars.forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            });
        });
    </script>

    @push('styles')
        <!-- Trix Editor Styles -->
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">

        <style>
            /* Custom Trix Editor Styling for Dark Theme */
            trix-editor {
                background-color: #111827 !important;
                color: #f9fafb !important;
                border: 1px solid #4b5563 !important;
                border-radius: 0.5rem !important;
                min-height: 24rem !important;
                padding: 1.5rem !important;
            }

            trix-editor:focus {
                outline: none !important;
                border-color: #3b82f6 !important;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
            }

            trix-editor h1,
            trix-editor h2,
            trix-editor h3,
            trix-editor h4,
            trix-editor h5,
            trix-editor h6 {
                color: #e5e7eb !important;
                margin-top: 1rem !important;
                margin-bottom: 0.5rem !important;
            }

            trix-editor h1 {
                font-size: 2rem !important;
                font-weight: bold !important;
            }

            trix-editor h2 {
                font-size: 1.5rem !important;
                font-weight: bold !important;
            }

            trix-editor h3 {
                font-size: 1.25rem !important;
                font-weight: bold !important;
            }

            trix-editor blockquote {
                border-left: 4px solid #3b82f6 !important;
                padding-left: 1rem !important;
                background: rgba(59, 130, 246, 0.1) !important;
                border-radius: 0.375rem !important;
                margin: 1rem 0 !important;
                font-style: italic !important;
            }

            trix-editor ul,
            trix-editor ol {
                padding-left: 1.5rem !important;
                margin: 0.5rem 0 !important;
            }

            trix-editor li {
                margin: 0.25rem 0 !important;
            }

            trix-editor a {
                color: #60a5fa !important;
                text-decoration: underline !important;
            }

            trix-editor a:hover {
                color: #93c5fd !important;
            }

            trix-editor strong {
                font-weight: bold !important;
                color: #ffffff !important;
            }

            trix-editor em {
                font-style: italic !important;
                color: #d1d5db !important;
            }

            /* Hide Trix toolbar since we have our own */
            trix-toolbar {
                display: none !important;
            }

            /* Custom scrollbar */
            .overflow-y-auto::-webkit-scrollbar {
                width: 8px;
            }

            .overflow-y-auto::-webkit-scrollbar-track {
                background: #374151;
                border-radius: 4px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #6B7280;
                border-radius: 4px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #9CA3AF;
            }

            /* Sortable styles */
            .sortable-ghost {
                opacity: 0.4;
                background: rgba(59, 130, 246, 0.1) !important;
            }

            /* Content block animations */
            .content-block {
                transition: all 0.3s ease;
            }

            .content-block:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            }

            /* Keyboard shortcut styling */
            kbd {
                background-color: #374151;
                border: 1px solid #4B5563;
                border-radius: 0.25rem;
                padding: 0.125rem 0.375rem;
                font-size: 0.75rem;
                font-family: ui-monospace, SFMono-Regular, monospace;
            }

            /* Loading states */
            .loading {
                opacity: 0.7;
                pointer-events: none;
            }

            /* Custom animations */
            @keyframes slideIn {
                from {
                    transform: translateX(-100%);
                    opacity: 0;
                }

                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .slide-in {
                animation: slideIn 0.3s ease-out;
            }

            .fade-in {
                animation: fadeIn 0.2s ease-out;
            }

            /* Progress bar animation */
            .progress-bar {
                transition: width 1s ease-in-out;
            }

            /* Code block syntax highlighting preparation */
            .code-block {
                font-family: 'JetBrains Mono', 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
                line-height: 1.5;
            }

            /* Note block styling variants */
            .note-tip {
                background: rgba(59, 130, 246, 0.1);
                border-left: 4px solid #3B82F6;
            }

            .note-warning {
                background: rgba(245, 158, 11, 0.1);
                border-left: 4px solid #F59E0B;
            }

            .note-info {
                background: rgba(16, 185, 129, 0.1);
                border-left: 4px solid #10B981;
            }

            .note-success {
                background: rgba(34, 197, 94, 0.1);
                border-left: 4px solid #22C55E;
            }

            /* Drag and drop styling */
            .trix-content.border-blue-500 {
                border-color: #3b82f6 !important;
                background-color: rgba(59, 130, 246, 0.05) !important;
            }

            /* Modal backdrop blur */
            .modal-backdrop {
                backdrop-filter: blur(4px);
            }

            /* File upload hover effects */
            input[type="file"]:hover {
                cursor: pointer;
            }

            /* Button hover effects */
            button:hover {
                transform: translateY(-1px);
            }

            button:active {
                transform: translateY(0);
            }

            /* Selection styles */
            ::selection {
                background-color: rgba(59, 130, 246, 0.3);
            }

            /* Focus styles */
            input:focus,
            textarea:focus,
            select:focus {
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            /* Responsive text sizing */
            @media (max-width: 640px) {
                trix-editor {
                    min-height: 16rem !important;
                    padding: 1rem !important;
                }
            }

            /* Print styles */
            @media print {
                .no-print {
                    display: none !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <!-- Include Trix Editor -->
        <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

        <!-- Include SortableJS for drag and drop functionality -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

        <!-- Initialize course builder -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                window.courseBuilderInstance = courseBuilder();
                window.courseBuilderInstance.init();
            });

            // Livewire hooks
            document.addEventListener('livewire:init', () => {
                Livewire.on('content-saved', () => {
                    // Show success animation
                    const saveButton = document.querySelector('[wire\\:click="saveContent"]');
                    if (saveButton) {
                        saveButton.classList.add('animate-pulse');
                        setTimeout(() => {
                            saveButton.classList.remove('animate-pulse');
                        }, 1000);
                    }
                });

                Livewire.on('notify', (message, type) => {
                    // Custom notification system
                    const notification = document.createElement('div');
                    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 text-white transition-all duration-300 ${
                    type === 'success' ? 'bg-green-600' : 
                    type === 'error' ? 'bg-red-600' : 
                    'bg-blue-600'
                }`;
                    notification.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'} mr-2"></i>
                        <span>${message}</span>
                    </div>
                `;

                    document.body.appendChild(notification);

                    // Animate in
                    setTimeout(() => {
                        notification.style.transform = 'translateX(0)';
                        notification.style.opacity = '1';
                    }, 10);

                    // Remove after delay
                    setTimeout(() => {
                        notification.style.transform = 'translateX(100%)';
                        notification.style.opacity = '0';
                        setTimeout(() => {
                            if (notification.parentNode) {
                                document.body.removeChild(notification);
                            }
                        }, 300);
                    }, 4000);
                });

                Livewire.on('focus-search', () => {
                    const searchInput = document.querySelector(
                        'input[wire\\:model\\.live\\.debounce\\.300ms="searchTerm"]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                });
            });

            // Error handling
            window.addEventListener('error', (e) => {
                console.error('Course Builder Error:', e.error);
            });

            // Performance monitoring
            if ('performance' in window) {
                window.addEventListener('load', () => {
                    setTimeout(() => {
                        const perfData = performance.getEntriesByType('navigation')[0];
                        console.log('Course Builder Load Time:', perfData.loadEventEnd - perfData
                            .loadEventStart, 'ms');
                    }, 0);
                });
            }
        </script>
    @endpush
</div>