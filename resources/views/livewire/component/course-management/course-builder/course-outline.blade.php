<div class="bg-gray-900 rounded-xl shadow-2xl sticky top-8 p-6 animate__animated animate__fadeIn" 
     role="navigation" 
     aria-label="Course Roadmap">
    
    <!-- Loading State -->
    <div wire:loading class="absolute inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center rounded-xl">
        <i class="fas fa-spinner fa-spin text-2xl text-blue-500" aria-hidden="true"></i>
        <span class="sr-only">Loading course roadmap...</span>
    </div>

    <!-- Toast Notification -->
    <div x-data="{ show: false, message: '', type: '' }" 
         x-show="show" 
         @notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
         :class="{ 'bg-green-600': type === 'success', 'bg-red-600': type === 'error', 'bg-blue-600': type === 'info' }"
         class="fixed top-4 right-4 px-4 py-2 text-white rounded-lg shadow-lg animate__animated animate__fadeInDown max-w-sm z-50">
        <span x-text="message"></span>
        <button @click="show = false" class="ml-4 text-white hover:text-gray-200" aria-label="Close notification">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6 border-b border-gray-700 pb-4">
        <h2 class="text-2xl font-bold text-white">Course Roadmap</h2>
        <button wire:click="showAddSectionForm" 
                @click="console.log('Add Step button clicked')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm"
                aria-label="Add a new step">
            <span wire:loading wire:target="showAddSectionForm" class="inline-block">
                <i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>
            </span>
            <span wire:loading.remove wire:target="showAddSectionForm">
                <i class="fas fa-plus mr-2" aria-hidden="true"></i> Add Step
            </span>
        </button>
    </div>

    <!-- Stats and Progress -->
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-gray-800 p-4 rounded-lg text-center animate__animated animate__fadeInUp">
            <div class="text-blue-400 font-bold text-lg">{{ $courseStats['total_sections'] }}</div>
            <div class="text-gray-300 text-sm">Steps</div>
        </div>
        <div class="bg-gray-800 p-4 rounded-lg text-center animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
            <div class="text-green-400 font-bold text-lg">{{ $courseStats['total_lessons'] }}</div>
            <div class="text-gray-300 text-sm">Lessons</div>
        </div>
    </div>
    <div class="mb-6">
        <div class="flex justify-between text-sm text-gray-300 mb-2">
            <span>Course Progress</span>
            <span>{{ $courseStats['completion_percentage'] }}%</span>
        </div>
        <div class="w-full bg-gray-700 rounded-full h-3">
            <div class="bg-gradient-to-r from-blue-500 to-green-500 h-3 rounded-full transition-all duration-300 animate__animated animate__pulse"
                 style="width: {{ $courseStats['completion_percentage'] }}%"></div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 space-y-3">
        <div class="relative">
            <input type="text" 
                   wire:model.live.debounce.300ms="searchTerm"
                   placeholder="Search steps or lessons..."
                   class="w-full pl-10 pr-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   aria-label="Search course content">
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" aria-hidden="true"></i>
        </div>
        <select wire:model.live="filterType"
                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500"
                aria-label="Filter content type">
            <option value="all">All Content Types</option>
            <option value="text">Text Content</option>
            <option value="video">Video Lessons</option>
            <option value="file">Other Files</option>
        </select>
    </div>

    <!-- Roadmap -->
    <div class="space-y-6 max-h-[calc(100vh-300px)] overflow-y-auto custom-scrollbar">
        @forelse ($filteredSections as $index => $section)
            <div class="relative pl-8">
                <!-- Timeline Line -->
                <div class="absolute left-4 top-0 bottom-0 w-1 bg-gray-600"></div>
                <!-- Timeline Dot -->
                <div class="absolute left-2.5 top-4 w-3 h-3 bg-blue-500 rounded-full"></div>
                
                <div class="bg-gray-800 rounded-lg shadow-md p-4 animate__animated animate__fadeIn"
                     role="region"
                     aria-labelledby="step-{{ $section->id }}-title">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            @if ($section->is_locked)
                                <i class="fas fa-lock text-yellow-400" aria-hidden="true" title="Locked"></i>
                            @else
                                <i class="fas fa-unlock text-green-400" aria-hidden="true" title="Unlocked"></i>
                            @endif
                            <h3 class="text-lg font-semibold text-white" id="step-{{ $section->id }}-title">
                                Step {{ $index + 1 }}: {{ $section->title }}
                            </h3>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button wire:click="editSection({{ $section->id }})" 
                                    class="text-gray-400 hover:text-blue-400"
                                    aria-label="Edit step {{ $section->title }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="deleteSection({{ $section->id }})" 
                                    class="text-gray-400 hover:text-red-400"
                                    aria-label="Delete step {{ $section->title }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <!-- Lessons -->
                        @foreach ($section->lessons as $lesson)
                            <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors animate__animated animate__fadeIn">
                                <div class="flex items-center space-x-3">
                                    @if (Auth::user()->isInstructor())
                                        <input type="checkbox" 
                                               wire:model="selectedLessons" 
                                               value="{{ $lesson->id }}"
                                               class="rounded bg-gray-600 border-gray-500 text-blue-500"
                                               aria-label="Select lesson {{ $lesson->title }}">
                                    @endif
                                    <i class="fas fa-{{ $lesson->content_type === 'video' ? 'video' : ($lesson->content_type === 'file' ? 'file' : 'file-text') }} text-blue-400" 
                                       aria-hidden="true"></i>
                                    <a wire:click="$set('activeLessonId', {{ $lesson->id }})" 
                                       class="text-gray-200 hover:text-white cursor-pointer"
                                       aria-label="View lesson {{ $lesson->title }}"
                                       @if ($section->is_locked && !Auth::user()->isInstructor()) disabled @endif>
                                        {{ $lesson->title }}
                                    </a>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if (UserProgress::where('user_id', Auth::id())->where('lesson_id', $lesson->id)->where('is_completed', true)->exists())
                                        <i class="fas fa-check-circle text-green-400" aria-hidden="true" title="Completed"></i>
                                    @endif
                                    @if (Auth::user()->isInstructor())
                                        <button wire:click="editLesson({{ $lesson->id }})" 
                                                class="text-gray-400 hover:text-blue-400"
                                                aria-label="Edit lesson {{ $lesson->title }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="deleteLesson({{ $lesson->id }})" 
                                                class="text-gray-400 hover:text-red-400"
                                                aria-label="Delete lesson {{ $lesson->title }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Assessments -->
                        <div id="assessments-{{ $section->id }}" class="space-y-2 sortable">
                            @foreach ($section->assessments as $assessment)
                                <div class="flex items-center justify-between p-3 bg-gray-700 rounded-lg hover:bg-gray-600 transition-colors animate__animated animate__fadeIn"
                                     data-assessment-id="{{ $assessment->id }}">
                                    <div class="flex items-center space-x-3">
                                        @if (Auth::user()->isInstructor())
                                            <i class="fas fa-grip-vertical text-gray-500 cursor-move" aria-hidden="true"></i>
                                        @endif
                                        <i class="fas fa-{{ $assessment->type === 'quiz' ? 'question-circle' : ($assessment->type === 'project' ? 'folder' : 'file-alt') }} text-indigo-400" 
                                           aria-hidden="true"></i>
                                    <span class="text-gray-200">{{ $assessment->title }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if (UserProgress::where('user_id', Auth::id())->where('assessment_id', $assessment->id)->where('is_completed', true)->exists())
                                        <i class="fas fa-check-circle text-green-400" aria-hidden="true" title="Passed"></i>
                                    @endif
                                    @if (Auth::user()->isInstructor())
                                        <button wire:click="editAssessment({{ $assessment->id }})" 
                                                class="text-gray-400 hover:text-blue-400"
                                                aria-label="Edit assessment {{ $assessment->title }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="deleteAssessment({{ $assessment->id }})" 
                                                class="text-gray-400 hover:text-red-400"
                                                aria-label="Delete assessment {{ $assessment->title }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <button wire:click="$emit('start-assessment', {{ $assessment->id }})"
                                                class="px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                                                aria-label="Start assessment {{ $assessment->title }}"
                                                @if ($section->is_locked) disabled @endif>
                                            <i class="fas fa-play mr-1" aria-hidden="true"></i> Start
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Add Lesson/Assessment Buttons -->
                    @if (Auth::user()->isInstructor())
                        <div class="flex space-x-2 mt-3">
                            <button wire:click="showAddLessonForm({{ $section->id }})" 
                                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                                    aria-label="Add lesson to step {{ $section->title }}">
                                <i class="fas fa-plus mr-2" aria-hidden="true"></i> Add Lesson
                            </button>
                            <button wire:click="showAddAssessmentForm({{ $section->id }})" 
                                    class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors"
                                    aria-label="Add assessment to step {{ $section->title }}">
                                <i class="fas fa-plus mr-2" aria-hidden="true"></i> Add Assessment
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center text-gray-400 py-8 animate__animated animate__fadeIn">
                <i class="fas fa-road text-3xl mb-3" aria-hidden="true"></i>
                <p>No steps found.</p>
                @if (empty($searchTerm))
                    <p class="text-sm mt-2">Add your first step to get started.</p>
                @else
                    <p class="text-sm mt-2">Try adjusting your search terms.</p>
                @endif
            </div>
        @endforelse

        <!-- Pagination -->
        @if ($filteredSections->hasPages())
            <div class="mt-4">
                {{ $filteredSections->links() }}
            </div>
        @endif
    </div>

    <!-- Section Modal -->
    @if ($showSectionModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
             role="dialog" 
             aria-labelledby="section-modal-title"
             wire:click.self="cancelAddSection"
             x-data="{ debug: 'Section modal rendered' }"
             x-init="console.log(debug)">
            <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full animate__animated animate__zoomIn"
                 wire:loading.class="opacity-50 pointer-events-none"
                 wire:target="addSection,updateSection">
                <h3 id="section-modal-title" class="text-xl font-bold text-white mb-4">
                    {{ $editingSectionId ? 'Edit Step' : 'Add Step' }}
                </h3>
                <div class="space-y-4">
                    <div>
                        <label for="section-title" class="block text-sm font-medium text-gray-300 mb-2">Step Title</label>
                        <input type="text" 
                               id="section-title"
                               wire:model.live="{{ $editingSectionId ? 'editingSectionTitle' : 'newSectionTitle' }}"
                               placeholder="Step title..."
                               class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500"
                               wire:keydown.enter="{{ $editingSectionId ? 'updateSection' : 'addSection' }}"
                               aria-required="true">
                        @error($editingSectionId ? 'editingSectionTitle' : 'newSectionTitle')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="section-description" class="block text-sm font-medium text-gray-300 mb-2">Description (Optional)</label>
                        <textarea id="section-description"
                                  wire:model.live="{{ $editingSectionId ? 'editingSectionDescription' : 'newSectionDescription' }}"
                                  placeholder="Step description..."
                                  class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 resize-none"
                                  rows="3"></textarea>
                        @error($editingSectionId ? 'editingSectionDescription' : 'newSectionDescription')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex space-x-3">
                        <button wire:click="{{ $editingSectionId ? 'updateSection' : 'addSection' }}"
                                class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors relative"
                                aria-label="{{ $editingSectionId ? 'Update step' : 'Add step' }}">
                            <span wire:loading wire:target="{{ $editingSectionId ? 'updateSection' : 'addSection' }}" class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span wire:loading.remove wire:target="{{ $editingSectionId ? 'updateSection' : 'addSection' }}">
                                <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ $editingSectionId ? 'Update' : 'Add' }}
                            </span>
                        </button>
                        <button wire:click="cancelAddSection"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                                aria-label="Cancel">
                            <i class="fas fa-times mr-2" aria-hidden="true"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Lesson Modal -->
    @if ($showLessonModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
             role="dialog" 
             aria-labelledby="lesson-modal-title"
             wire:click.self="cancelAddLesson"
             x-data="{ debug: 'Lesson modal rendered' }"
             x-init="console.log(debug)">
            <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full animate__animated animate__zoomIn">
                <h3 id="lesson-modal-title" class="text-xl font-bold text-white mb-4">
                    {{ $editingLessonId ? 'Edit Lesson' : 'Add Lesson' }}
                </h3>
                <div class="space-y-4">
                    <div>
                        <label for="lesson-title" class="block text-sm font-medium text-gray-300 mb-2">Lesson Title</label>
                        <input type="text" 
                               id="lesson-title"
                               wire:model.live="{{ $editingLessonId ? 'editingLessonTitle' : 'newLessonTitle' }}"
                               placeholder="Lesson title..."
                               class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500"
                               wire:keydown.enter="{{ $editingLessonId ? 'updateLesson' : 'addLesson' }}"
                               aria-required="true">
                        @error($editingLessonId ? 'editingLessonTitle' : 'newLessonTitle')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="lesson-description" class="block text-sm font-medium text-gray-300 mb-2">Description (Optional)</label>
                        <textarea id="lesson-description"
                                  wire:model.live="newLessonDescription"
                                  placeholder="Lesson description..."
                                  class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 resize-none"
                                  rows="3"></textarea>
                        @error('newLessonDescription')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="lesson-content-type" class="block text-sm font-medium text-gray-300 mb-2">Content Type</label>
                        <select id="lesson-content-type"
                                wire:model.live="newLessonContentType"
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                            <option value="text">Text</option>
                            <option value="video">Video</option>
                            <option value="file">File</option>
                        </select>
                        @error('newLessonContentType')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="lesson-duration" class="block text-sm font-medium text-gray-300 mb-2">Duration (Minutes)</label>
                        <input type="number" 
                               id="lesson-duration"
                               wire:model.live="newLessonDuration"
                               class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500"
                               min="0" max="1440">
                        @error('newLessonDuration')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex space-x-3">
                        <button wire:click="{{ $editingLessonId ? 'updateLesson' : 'addLesson' }}"
                                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors relative"
                                aria-label="{{ $editingLessonId ? 'Update lesson' : 'Add lesson' }}">
                            <span wire:loading wire:target="{{ $editingLessonId ? 'updateLesson' : 'addLesson' }}" class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span wire:loading.remove wire:target="{{ $editingLessonId ? 'updateLesson' : 'addLesson' }}">
                                <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ $editingLessonId ? 'Update' : 'Add' }}
                            </span>
                        </button>
                        <button wire:click="cancelAddLesson"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                                aria-label="Cancel">
                            <i class="fas fa-times mr-2" aria-hidden="true"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Assessment Modal -->
    @if ($showAssessmentModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
             role="dialog" 
             aria-labelledby="assessment-modal-title"
             wire:click.self="cancelAddAssessment"
             x-data="{ debug: 'Assessment modal rendered' }"
             x-init="console.log(debug)">
            <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full animate__animated animate__zoomIn">
                <h3 id="assessment-modal-title" class="text-xl font-bold text-white mb-4">
                    {{ $editingAssessmentId ? 'Edit Assessment' : 'Add Assessment' }}
                </h3>
                <div class="space-y-4">
                    <div>
                        <label for="assessment-title" class="block text-sm font-medium text-gray-300 mb-2">Assessment Title</label>
                        <input type="text" 
                               id="assessment-title"
                               wire:model.live="{{ $editingAssessmentId ? 'editingAssessmentTitle' : 'newAssessmentTitle' }}"
                               placeholder="Assessment title..."
                               class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500"
                               wire:keydown.enter="{{ $editingAssessmentId ? 'updateAssessment' : 'addAssessment' }}"
                               aria-required="true">
                        @error($editingAssessmentId ? 'editingAssessmentTitle' : 'newAssessmentTitle')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="assessment-description" class="block text-sm font-medium text-gray-300 mb-2">Description (Optional)</label>
                        <textarea id="assessment-description"
                                  wire:model.live="newAssessmentDescription"
                                  placeholder="Assessment description..."
                                  class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 resize-none"
                                  rows="3"></textarea>
                        @error('newAssessmentDescription')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="assessment-type" class="block text-sm font-medium text-gray-300 mb-2">Type</label>
                        <select id="assessment-type"
                                wire:model.live="newAssessmentType"
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                            <option value="quiz">Quiz</option>
                            <option value="project">Project</option>
                            <option value="assignment">Assignment</option>
                        </select>
                        @error('newAssessmentType')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="assessment-duration" class="block text-sm font-medium text-gray-300 mb-2">Duration (Minutes)</label>
                        <input type="number" 
                               id="assessment-duration"
                               wire:model.live="newAssessmentDurationMinutes"
                               class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500"
                               min="0" max="1440">
                        @error('newAssessmentDurationMinutes')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="assessment-deadline" class="block text-sm font-medium text-gray-300 mb-2">Deadline (Optional)</label>
                        <input type="datetime-local" 
                               id="assessment-deadline"
                               wire:model.live="newAssessmentDeadline"
                               class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500">
                        @error('newAssessmentDeadline')
                            <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="flex space-x-3">
                        <button wire:click="{{ $editingAssessmentId ? 'updateAssessment' : 'addAssessment' }}"
                                class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors relative"
                                aria-label="{{ $editingAssessmentId ? 'Update assessment' : 'Add assessment' }}">
                            <span wire:loading wire:target="{{ $editingAssessmentId ? 'updateAssessment' : 'addAssessment' }}" class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin" aria-hidden="true"></i>
                            </span>
                            <span wire:loading.remove wire:target="{{ $editingAssessmentId ? 'updateAssessment' : 'addAssessment' }}">
                                <i class="fas fa-save mr-2" aria-hidden="true"></i> {{ $editingAssessmentId ? 'Update' : 'Add' }}
                            </span>
                        </button>
                        <button wire:click="cancelAddAssessment"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                                aria-label="Cancel">
                            <i class="fas fa-times mr-2" aria-hidden="true"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Actions -->
    @if (count($selectedLessons) > 0)
        <div class="fixed bottom-4 left-4 bg-gray-800 rounded-lg p-3 shadow-lg animate__animated animate__slideInUp">
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-300">{{ count($selectedLessons) }} selected</span>
                <button wire:click="bulkDeleteLessons"
                        class="px-3 py-1 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm"
                        aria-label="Delete selected lessons">
                    <i class="fas fa-trash mr-1" aria-hidden="true"></i> Delete
                </button>
            </div>
        </div>
    @endif

    <!-- Confirmation Dialog -->
    <div x-data="{ open: false, title: '', message: '', method: '', params: [] }"
         x-show="open"
         @confirm-delete.window="open = true; title = $event.detail.title; message = $event.detail.message; method = $event.detail.method; params = $event.detail.params"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         role="dialog"
         aria-labelledby="confirm-modal-title">
        <div class="bg-gray-800 rounded-xl p-6 max-w-md w-full animate__animated animate__zoomIn">
            <h3 id="confirm-modal-title" class="text-xl font-bold text-white mb-4" x-text="title"></h3>
            <p class="text-gray-300 mb-6" x-text="message"></p>
            <div class="flex space-x-3">
                <button @click="$wire.call(method, ...params); open = false"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                        aria-label="Confirm deletion">
                    <i class="fas fa-check mr-2" aria-hidden="true"></i> Confirm
                </button>
                <button @click="open = false"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors"
                        aria-label="Cancel deletion">
                    <i class="fas fa-times mr-2" aria-hidden="true"></i> Cancel
                </button>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1f2937;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #6b7280;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        .sortable-ghost {
            opacity: 0.4;
            background: rgba(59, 130, 246, 0.1);
        }
    </style>

    <script>
        document.addEventListener('livewire:navigated', () => {
            console.log('Livewire initialized for CourseOutline');
            document.querySelectorAll('.sortable').forEach(container => {
                new Sortable(container, {
                    handle: '.fa-grip-vertical',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: (evt) => {
                        const sectionId = container.id.split('-')[1];
                        const orderedIds = Array.from(container.children).map(el => el.dataset.assessmentId);
                        @this.call('reorderAssessments', sectionId, orderedIds);
                    }
                });
            });
        });

        // Listen for log-to-console event
        document.addEventListener('livewire:init', () => {
            Livewire.on('log-to-console', (event) => {
                console.log(event.message);
            });
        });
    </script>
</div>