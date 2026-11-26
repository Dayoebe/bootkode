<div class="sticky top-[4.5rem] z-30 lg:hidden">
    <div class="overflow-hidden rounded-lg border border-themed-primary bg-themed-secondary p-3 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate text-[11px] font-semibold uppercase tracking-[0.14em] text-themed-secondary">
                    {{ $currentSection?->title ?? 'Course map' }}
                </p>
                <p class="truncate text-sm font-semibold text-themed-primary">
                    {{ $currentLesson?->title ?? 'Choose a lesson to begin' }}
                </p>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                @if ($resumeLesson)
                    <button wire:click="continueFromLastLesson"
                        class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-themed-secondary bg-themed-tertiary px-3 text-sm font-semibold text-themed-primary transition">
                        <i aria-hidden="true" class="fas fa-history"></i>
                        <span class="sr-only sm:not-sr-only">Resume</span>
                    </button>
                @endif

                <button @click="openNav($event)" aria-controls="course-map-drawer" :aria-expanded="navOpen.toString()"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg px-3 text-sm font-semibold text-white shadow-sm transition"
                    style="background: rgb(var(--bk-action)); color: rgb(var(--bk-action-text));">
                    <i aria-hidden="true" class="fas fa-compass"></i>
                    <span>Map</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div x-cloak x-show="navOpen" id="course-map-drawer" class="fixed inset-0 z-50 lg:hidden"
    role="dialog" aria-modal="true" aria-labelledby="course-map-title" tabindex="-1"
    x-trap.inert.noscroll="navOpen">
    <div class="absolute inset-0 bg-black/60" @click="closeNav()"></div>

    <div class="absolute inset-y-0 left-0 w-full max-w-md p-4">
        <div class="flex h-full flex-col overflow-hidden rounded-lg border border-themed-primary bg-themed-secondary shadow-sm">
            <div class="flex items-center justify-between border-b border-themed-secondary px-5 py-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Learning map</p>
                    <h2 id="course-map-title" class="mt-1 text-lg font-semibold text-themed-primary">Navigate the course</h2>
                </div>

                <button id="course-nav-close" @click="closeNav()"
                    class="flex h-10 w-10 items-center justify-center rounded-lg border border-themed-secondary bg-themed-tertiary text-themed-primary transition hover:bg-themed-secondary"
                    aria-label="Close course map">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4">
                <livewire:student-management.course-view.course-progress-sidebar :course="$course"
                    :sections="$course->sections" :currentLesson="$currentLesson" :completedLessons="$completedLessons"
                    :unlockedSections="$unlockedSections" :sectionCompletionThreshold="$sectionCompletionThreshold"
                    wire:key="sidebar-mobile-{{ $progressKey }}" />
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-5 lg:items-start"
    :class="curriculumCollapsed
        ? 'lg:grid-cols-[4.5rem_minmax(0,1fr)] min-[1360px]:grid-cols-[4.5rem_minmax(0,1fr)_18rem]'
        : 'lg:grid-cols-[21rem_minmax(0,1fr)] min-[1360px]:grid-cols-[21rem_minmax(0,1fr)_18rem]'">
    <aside class="hidden lg:block" aria-label="Course curriculum">
        <div x-show="!curriculumCollapsed" x-transition.opacity>
            <div class="mb-2 flex justify-end">
                <button type="button" @click="toggleCurriculum()" class="bk-button bk-button-secondary"
                    aria-label="Collapse course curriculum" title="Collapse curriculum">
                    <i class="fas fa-angles-left" aria-hidden="true"></i>
                    Collapse
                </button>
            </div>
            <livewire:student-management.course-view.course-progress-sidebar :course="$course"
                :sections="$course->sections" :currentLesson="$currentLesson" :completedLessons="$completedLessons"
                :unlockedSections="$unlockedSections" :sectionCompletionThreshold="$sectionCompletionThreshold"
                wire:key="sidebar-desktop-{{ $progressKey }}" />
        </div>

        <div x-cloak x-show="curriculumCollapsed" class="sticky top-20 rounded-lg border border-themed-primary bg-themed-secondary p-2 shadow-sm">
            <button type="button" @click="toggleCurriculum()"
                class="flex min-h-11 w-full items-center justify-center rounded-lg text-themed-primary transition hover:bg-themed-tertiary"
                aria-label="Expand course curriculum" title="Expand curriculum">
                <i class="fas fa-angles-right" aria-hidden="true"></i>
            </button>
            <div class="my-2 border-t border-themed-secondary"></div>
            <div class="space-y-3 py-2 text-center">
                <div>
                    <span class="block text-lg font-semibold text-themed-primary">{{ $overallProgress }}%</span>
                    <span class="text-[10px] font-semibold uppercase tracking-wide text-themed-secondary">Done</span>
                </div>
                <div class="mx-auto flex h-20 w-1 items-end overflow-hidden rounded-full bg-themed-tertiary" aria-hidden="true">
                    <div class="w-full rounded-full bg-action" style="height: {{ $overallProgress }}%"></div>
                </div>
                <div>
                    <span class="block text-sm font-semibold text-themed-primary">{{ count($completedLessons) }}/{{ $lessonCount }}</span>
                    <span class="text-[10px] font-semibold uppercase tracking-wide text-themed-secondary">Lessons</span>
                </div>
            </div>
        </div>
    </aside>

    <div class="min-w-0">
        @if ($currentLesson)
            @php
                $allLessons = $course->sections->flatMap->lessons;
            @endphp

            <livewire:student-management.course-view.lesson-content-viewer :lesson="$currentLesson"
                :allLessons="$allLessons->toArray()" :completedLessons="$completedLessons"
                :unlockedSections="$unlockedSections"
                wire:key="content-{{ $currentLesson->id }}-{{ $progressVersion }}" />
        @else
            <div class="rounded-lg border border-themed-primary bg-themed-secondary p-6 text-center shadow-sm sm:p-10">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-lg border border-themed-secondary bg-themed-tertiary">
                    <i aria-hidden="true" class="fas fa-book-open text-2xl accent-themed-primary"></i>
                </div>
                <h3 class="mt-5 text-xl font-semibold text-themed-primary">No lesson selected</h3>
                <p class="mt-2 text-sm leading-6 text-themed-secondary">
                    Open the course map and choose any unlocked lesson to begin learning.
                </p>
                <button @click="openNav($event)" aria-controls="course-map-drawer" :aria-expanded="navOpen.toString()"
                    class="mt-5 inline-flex items-center gap-2 rounded-lg border border-themed-secondary bg-themed-tertiary px-5 py-3 text-sm font-semibold text-themed-primary transition hover:bg-themed-secondary lg:hidden">
                    <i aria-hidden="true" class="fas fa-compass"></i>
                    Open Course Map
                </button>
            </div>
        @endif
    </div>

    @if ($currentLesson)
        <div class="mt-5 min-[1360px]:mt-0">
            <livewire:student-management.course-view.lesson-tools-panel :lesson="$currentLesson"
                wire:key="tools-{{ $currentLesson->id }}" />
        </div>
    @endif
</div>
