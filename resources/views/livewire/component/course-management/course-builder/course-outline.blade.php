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

    <!-- Search and Filter Bar -->
    <div class="p-4 border-b border-gray-700">
        <div class="space-y-3">
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="searchTerm"
                    placeholder="Search lessons and sections..."
                    class="w-full pl-10 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>

            <select wire:model.live="filterType"
                class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                <option value="all">All Content Types</option>
                <option value="text">Text Content</option>
                <option value="video">Video Lessons</option>
                <option value="file">File Resources</option>
            </select>

            @if (count($selectedLessons) > 0)
                <div class="flex items-center justify-between p-2 bg-gray-700 rounded-lg">
                    <span class="text-sm text-gray-400">{{ count($selectedLessons) }} selected</span>
                    <div class="flex space-x-2">
                        <button wire:click="bulkDeleteLessons"
                            class="px-2 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                        <button wire:click="deselectAllLessons"
                            class="px-2 py-1 bg-gray-600 text-white rounded text-xs hover:bg-gray-700">
                            Clear
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Sections List -->
    <div class="p-4 max-h-96 overflow-y-auto">
        <div id="sectionsList" class="space-y-2">
            @forelse($filteredSections as $section)
                <div class="section-item {{ $activeLessonId && $course->sections->flatMap->lessons->where('id', $activeLessonId)->first()?->section_id === $section->id ? 'bg-blue-600 bg-opacity-20 border-blue-500' : 'bg-gray-700 hover:bg-gray-600 border-gray-600' }} rounded-lg border cursor-pointer transition-all duration-200"
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
                                            class="font-semibold {{ $activeLessonId && $course->sections->flatMap->lessons->where('id', $activeLessonId)->first()?->section_id === $section->id ? 'text-blue-300' : 'text-white' }}">
                                            {{ $section->title }}
                                        </h3>
                                        @if ($section->description)
                                            <p
                                                class="text-xs {{ $activeLessonId && $course->sections->flatMap->lessons->where('id', $activeLessonId)->first()?->section_id === $section->id ? 'text-blue-200' : 'text-gray-400' }} mt-1">
                                                {{ Str::limit($section->description, 60) }}
                                            </p>
                                        @endif
                                        <p
                                            class="text-xs {{ $activeLessonId && $course->sections->flatMap->lessons->where('id', $activeLessonId)->first()?->section_id === $section->id ? 'text-blue-200' : 'text-gray-400' }} mt-1">
                                            {{ $section->lessons->count() }} lessons •
                                            {{ $section->lessons->sum('duration_minutes') }} min
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if (
                                    $activeLessonId &&
                                        $course->sections->flatMap->lessons->where('id', $activeLessonId)->first()?->section_id === $section->id)
                                    <div class="w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                                @endif
                                <div class="flex items-center space-x-1" x-data="{ open: false }">
                                    <button @click="open = !open" class="text-gray-400 hover:text-gray-300 text-sm">
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
                                    <div class="flex items-center justify-between p-2 rounded {{ $activeLessonId && $activeLessonId === $lesson->id ? 'bg-blue-500 bg-opacity-30' : 'hover:bg-gray-600' }} group"
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
                                                        <input type="text" wire:model="editingLessonTitle"
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
                                                        class="text-sm {{ $activeLessonId && $activeLessonId === $lesson->id ? 'text-white font-medium' : 'text-gray-300' }}">
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
                                            <button wire:click.stop="duplicateLesson({{ $lesson->id }})"
                                                class="text-green-400 hover:text-green-300 text-xs">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                            <button wire:click.stop="deleteLesson({{ $lesson->id }})"
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
                                <input type="text" wire:model="newLessonTitle" placeholder="Lesson title..."
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
                                    <input type="number" wire:model="newLessonDuration" placeholder="Duration (min)"
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

                        <!-- Projects Section -->
                        <details class="group mt-4">
                            <summary
                                class="flex items-center justify-between font-medium cursor-pointer py-2 px-3 hover:bg-gray-700 rounded-lg transition-colors bg-indigo-900/20">
                                <span class="text-indigo-300"><i class="fas fa-project-diagram mr-2"></i>Projects
                                    ({{ $section->projects->count() }})</span>
                                <i class="fas fa-chevron-down group-open:rotate-180 transition-transform"></i>
                            </summary>
                            <div class="mt-2 space-y-2 px-3 pb-3">
                                <div id="projects-{{ $section->id }}" class="space-y-2 sortable">
                                    @foreach ($section->projects->sortBy('order') as $project)
                                        <div wire:key="project-{{ $project->id }}" data-project-id="{{ $project->id }}"
                                            class="flex items-center justify-between bg-gray-700 p-3 rounded-lg hover:bg-gray-600 transition-colors animate__animated animate__fadeInUp">
                                            <div class="flex items-center space-x-3">
                                                <i class="fas fa-grip-vertical text-gray-500 cursor-move"></i>
                                                <div>
                                                    @if ($editingProjectId === $project->id)
                                                        <input type="text" wire:model="editingProjectTitle"
                                                            wire:keydown.enter="updateProject"
                                                            class="bg-gray-800 border border-gray-600 rounded px-2 py-1 text-white focus:ring-blue-500">
                                                        <button wire:click="updateProject" class="ml-2 text-green-400"><i
                                                                class="fas fa-check"></i></button>
                                                        <button wire:click="$set('editingProjectId', null)"
                                                            class="ml-2 text-red-400"><i class="fas fa-times"></i></button>
                                                    @else
                                                        <span class="font-medium text-indigo-300">{{ $project->title }}</span>
                                                        <div class="text-xs text-gray-400">
                                                            Type: {{ ucfirst($project->project_type) }} | Duration:
                                                            {{ $project->estimated_duration_minutes }} min
                                                            @if ($project->deadline)
                                                                | Due: {{ $project->deadline->format('M d, Y') }}
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button wire:click="editProject({{ $project->id }})"
                                                    class="text-yellow-400 hover:text-yellow-300"><i
                                                        class="fas fa-edit"></i></button>
                                                <button wire:click="deleteProject({{ $project->id }})"
                                                    wire:confirm="Delete this project?"
                                                    class="text-red-400 hover:text-red-300"><i
                                                        class="fas fa-trash"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($isAddingProjectToSectionId === $section->id)
                                    <div
                                        class="mt-3 p-3 bg-gray-700 rounded-lg border border-gray-600 animate__animated animate__zoomIn">
                                        <input type="text" wire:model="newProjectTitle" placeholder="Project title..."
                                            class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent mb-2">
                                        <textarea wire:model="newProjectDescription" placeholder="Description..."
                                            class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-transparent mb-2"
                                            rows="2"></textarea>
                                        <select wire:model="newProjectType"
                                            class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white mb-2">
                                            <option value="individual">Individual</option>
                                            <option value="group">Group</option>
                                            <option value="capstone">Capstone</option>
                                        </select>
                                        <input type="number" wire:model="newProjectDurationMinutes"
                                            placeholder="Duration (minutes)"
                                            class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white mb-2">
                                        <input type="date" wire:model="newProjectDeadline"
                                            class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white mb-3">
                                        <div class="flex space-x-2">
                                            <button wire:click="addProject"
                                                class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                                <i class="fas fa-plus mr-1"></i> Add Project
                                            </button>
                                            <button wire:click="cancelAddProject"
                                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <button wire:click="showAddProjectForm({{ $section->id }})"
                                        class="mt-3 w-full px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                                        <i class="fas fa-plus mr-1"></i> Add Project
                                    </button>
                                @endif
                            </div>
                        </details>
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

<script>
    // Add Sortable for projects per section
    document.addEventListener('livewire:navigated', () => {
        document.querySelectorAll('.sortable').forEach(container => {
            new Sortable(container, {
                handle: '.fa-grip-vertical',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: (evt) => {
                    const sectionId = container.id.split('-')[1]; // e.g., projects-1
                    const orderedIds = Array.from(container.children).map(el => el.dataset
                        .projectId);
                    @this.call('reorderProjects', sectionId, orderedIds);
                }
            });
        });
    });
</script>