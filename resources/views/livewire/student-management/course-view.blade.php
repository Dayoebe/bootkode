<div class="bg-gray-100 dark:bg-gray-900 rounded-xl p-6 transition-colors duration-300">
    <!-- Course Header -->
    <livewire:student-management.course-view.course-progress-header :course="$course"
        :overallProgress="$this->calculateOverallProgress()" :currentSection="$currentSection"
        :completedLessons="$completedLessons" wire:key="header-{{ $course->id }}" />


    @if ($this->getLastViewedLesson() && $this->getLastViewedLesson()->id !== $currentLesson?->id)
        <div class="mb-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-4 shadow-lg animate-fade-in">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-play-circle text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Continue Learning</h3>
                        <p class="text-sm opacity-90">
                            Resume from: <span class="font-medium">{{ $this->getLastViewedLesson()->title }}</span>
                        </p>
                    </div>
                </div>
                <button wire:click="continueFromLastLesson"
                    class="px-6 py-3 bg-white text-indigo-600 hover:bg-gray-100 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-arrow-right"></i>
                    Continue
                </button>
            </div>
        </div>
    @endif

    <!-- Certificate Earned Banner -->
    @if($certificateEarned ?? false)
        <div
            class="mb-6 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white shadow-lg animate-fade-in">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-trophy text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold mb-1">🎉 Congratulations!</h3>
                        <p class="text-lg opacity-90">You've completed this course and earned your certificate!</p>
                    </div>
                </div>
                <a href="{{ route('student.certificates.index') }}"
                    class="px-6 py-3 bg-white text-green-600 hover:bg-gray-100 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-certificate"></i>
                    View Certificate
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
        <!-- Sidebar - Course Navigation -->
        <div class="lg:col-span-1">
            <livewire:student-management.course-view.course-progress-sidebar :course="$course"
                :sections="$course->sections" :currentLesson="$currentLesson" :completedLessons="$completedLessons"
                :unlockedSections="$unlockedSections" :sectionCompletionThreshold="$sectionCompletionThreshold"
                wire:key="sidebar-{{ $course->id }}-{{ $currentLesson?->id ?? 'none' }}" />
        </div>

        <!-- Main Content - Lesson View -->
        <div class="lg:col-span-3">
            @if ($currentLesson)
                @php
                    $allLessons = $course->sections->flatMap->lessons;
                @endphp

                <livewire:student-management.course-view.lesson-content-viewer :lesson="$currentLesson"
                    :allLessons="$allLessons->toArray()" :completedLessons="$completedLessons"
                    :unlockedSections="$unlockedSections" wire:key="content-{{ $currentLesson->id }}" />
            @else
                <!-- Empty State -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-10 text-center border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                    <i class="fas fa-book-open text-gray-400 dark:text-gray-500 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No lesson selected</h3>
                    <p class="text-gray-600 dark:text-gray-400">Select a lesson from the sidebar to begin learning.</p>
                </div>
            @endif
        </div>
    </div>
</div>