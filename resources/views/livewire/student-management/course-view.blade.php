<div class="bg-gray-100 dark:bg-gray-900 rounded-xl p-6 transition-colors duration-300">
    <!-- Course Header -->
    <livewire:student-management.course-view.course-progress-header :course="$course"
        :overallProgress="$this->calculateOverallProgress()" :currentSection="$currentSection"
        :completedLessons="$completedLessons" wire:key="header-{{ $course->id }}" />

    <!-- Continue Learning Banner -->
    @if ($this->getLastViewedLesson() && $this->getLastViewedLesson()->id !== $currentLesson?->id)
        <div class="mb-6 bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary rounded-xl p-4 shadow-lg animate-fade-in transition-colors duration-300">
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
                    class="px-6 py-3 bg-white text-accent-themed-primary hover:bg-gray-100 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-arrow-right"></i>
                    Continue
                </button>
            </div>
        </div>
    @endif

    <!-- Certificate Earned Banner -->
    @if($certificateEarned ?? false)
        <div class="mb-6 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white shadow-lg animate-fade-in transition-colors duration-300">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-trophy text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold mb-1">Congratulations!</h3>
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
                <div class="bg-themed-secondary rounded-xl p-10 text-center border border-themed-primary transition-colors duration-300 shadow-lg">
                    <i class="fas fa-book-open text-themed-tertiary text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-themed-primary mb-2">No lesson selected</h3>
                    <p class="text-themed-secondary">Select a lesson from the sidebar to begin learning.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="mt-12">
        <livewire:student-management.course-view.review :course="$course" wire:key="reviews-{{ $course->id }}" />
    </div>
</div>

<style>
    /* Theme transition support */
    * {
        transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
    }

    /* Ensure proper color application for accents */
    .text-accent-themed-primary {
        color: rgb(var(--accent-primary));
    }

    .bg-accent-themed-primary {
        background-color: rgb(var(--accent-primary));
    }

    .to-accent-themed-secondary {
        --tw-gradient-to: rgb(var(--accent-secondary));
    }

    .from-accent-themed-primary {
        --tw-gradient-from: rgb(var(--accent-primary));
    }
</style>