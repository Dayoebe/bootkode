<div class="bg-gray-900 rounded-xl p-6">
    <!-- Course Header -->
    <livewire:student-management.course-view.course-progress-header :course="$course" :overallProgress="$this->calculateOverallProgress()"
        :currentSection="$currentSection" :completedLessons="$completedLessons" wire:key="header-{{ $course->id }}" />

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
        <!-- Sidebar - Course Navigation -->
        <div class="lg:col-span-1">
            <livewire:student-management.course-view.course-progress-sidebar :course="$course" :sections="$course->sections"
                :currentLesson="$currentLesson" :completedLessons="$completedLessons" :unlockedSections="$unlockedSections" :sectionCompletionThreshold="$sectionCompletionThreshold"
                wire:key="sidebar-{{ $course->id }}-{{ $currentLesson?->id ?? 'none' }}" />
        </div>

        <!-- Main Content - Lesson View -->
        <div class="lg:col-span-3">
            @if ($currentLesson)
                @php
                    $allLessons = $course->sections->flatMap->lessons;
                @endphp

                <livewire:student-management.course-view.lesson-content-viewer :lesson="$currentLesson" :allLessons="$allLessons->toArray()"
                    :completedLessons="$completedLessons" :unlockedSections="$unlockedSections" wire:key="content-{{ $currentLesson->id }}" />
            @else
                <!-- Empty State -->
                <div class="bg-gray-800 rounded-xl p-10 text-center">
                    <i class="fas fa-book-open text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-white mb-2">No lesson selected</h3>
                    <p class="text-gray-400">Select a lesson from the sidebar to begin learning.</p>
                </div>
            @endif
        </div>
    </div>
</div>
