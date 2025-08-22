<div>
<div class="bg-gray-800 p-4 sm:p-6 lg:p-8 rounded-2xl">
    <!-- Toolbar -->
    <livewire:component.course-management.course-builder.toolbar 
        :course="$course" 
        wire:key="toolbar-{{ $course->id }}" />

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
        <!-- Course Outline Sidebar -->
        <div class="lg:col-span-1">
            <livewire:component.course-management.course-builder.course-outline
                :course="$course"
                :activeSectionId="$activeSectionId"
                :activeLessonId="$activeContentId"
                wire:key="outline-{{ $course->id }}-{{ $activeSectionId }}" />
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-3">
            <div class="animate__animated animate__fadeIn">
                @if($activeContentType === 'lesson' && $activeContentId)
                    <livewire:component.course-management.course-builder.lesson-editor
                        :lessonId="$activeContentId"
                        wire:key="lesson-editor-{{ $activeContentId }}" />
                @else
                    <!-- Empty State -->
                    <div class="bg-gray-800 rounded-xl border-2 border-dashed border-gray-700 p-12 text-center">
                        <div class="max-w-md mx-auto">
                            <div class="w-20 h-20 bg-blue-600/20 rounded-full flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-book-open text-3xl text-blue-400"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-3">Get Started</h3>
                            @if($course->sections->count() === 0)
                                <p class="text-gray-400 mb-4">
                                    Create your first section to start building your course content.
                                </p>
                                <button class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Your First Section
                                </button>
                            @elseif($course->sections->sum(fn($section) => $section->lessons->count()) === 0)
                                <p class="text-gray-400 mb-4">
                                    Your course has sections but no lessons yet. Add your first lesson to begin.
                                </p>
                                <button class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Your First Lesson
                                </button>
                            @else
                                <p class="text-gray-400">
                                    Select a lesson from the course outline to begin editing its content.
                                </p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
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

    .animate__fadeIn {
        animation: fadeIn 0.3s ease-in-out;
    }
</style>
</div>