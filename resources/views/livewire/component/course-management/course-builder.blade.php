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
                @if($activeContentType === 'lesson')
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
                            <h3 class="text-2xl font-bold text-white mb-3">Select Content to Edit</h3>
                            <p class="text-gray-400">
                                Choose a lesson from the course outline to begin editing
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>