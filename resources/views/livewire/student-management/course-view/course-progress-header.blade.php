@php
    $stats = $this->getProgressStats();
    $remainingLessons = max($stats['total'] - $stats['completed'], 0);
    $completedSections = $this->getCompletedSectionsCount();
    $unlockedCount = count($unlockedSections ?? []);
    $currentSectionProgress = $currentSection ? $this->calculateSectionProgress($currentSection) : 0;
    $nextUnlock = $this->getNextUnlockMilestone();
@endphp

<div
    class="relative overflow-hidden rounded-lg border border-themed-primary bg-themed-secondary shadow-sm transition-colors duration-300  ">


    <div class="relative p-5 md:p-8">
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(18rem,0.85fr)] xl:items-start">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em]">
                    <span class="rounded-full border border-themed-secondary bg-themed-tertiary px-3 py-1 text-themed-secondary">
                        {{ $course->category->name ?? 'Uncategorized' }}
                    </span>
                    <span class="rounded-full border border-themed-secondary bg-themed-tertiary px-3 py-1 text-themed-secondary capitalize">
                        {{ $course->difficulty_level ?: 'Self paced' }}
                    </span>
                    @if($course->quality_label_text)
                        <span class="rounded-full border border-themed-secondary px-3 py-1 {{ $course->quality_label_class }}">
                            <i aria-hidden="true" class="fas fa-shield-check mr-1"></i>{{ $course->quality_label_text }}
                        </span>
                    @endif
                    <span class="rounded-full border border-transparent px-3 py-1 text-white"
                        style="background: rgb(var(--bk-action)); color: rgb(var(--bk-action-text));">
                        {{ $course->formatted_duration }}
                    </span>
                </div>

                <h1 class="mt-5  font-semibold tracking-tight text-themed-primary  bk-page-title">
                    {{ $course->title }}
                </h1>

                <p class="mt-3 max-w-3xl text-base leading-7 text-themed-secondary md:text-lg">
                    {{ $course->subtitle ?: \Illuminate\Support\Str::limit(strip_tags($course->description), 210) }}
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-3 text-sm text-themed-secondary">
                    <span class="inline-flex items-center gap-2 rounded-full border border-themed-secondary bg-themed-tertiary px-4 py-2">
                        <i aria-hidden="true" class="fas fa-user-graduate accent-themed-primary"></i>
                        {{ $course->instructor->name ?? 'Bootkode Instructor' }}
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-themed-secondary bg-themed-tertiary px-4 py-2">
                        <i aria-hidden="true" class="fas fa-layer-group accent-themed-primary"></i>
                        {{ $course->sections->count() }} sections
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-themed-secondary bg-themed-tertiary px-4 py-2">
                        <i aria-hidden="true" class="fas fa-book-open accent-themed-primary"></i>
                        {{ $stats['total'] }} lessons
                    </span>
                    @if ($currentSection)
                        <span class="inline-flex items-center gap-2 rounded-full border border-themed-secondary bg-themed-tertiary px-4 py-2">
                            <i aria-hidden="true" class="fas fa-location-arrow accent-themed-primary"></i>
                            Current focus: {{ $currentSection->title }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Overall Progress</p>
                    <div class="mt-3 flex items-end justify-between gap-3">
                        <span class="text-4xl font-semibold text-themed-primary">{{ $overallProgress }}%</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                            style="background: rgb(var(--bk-action)); color: rgb(var(--bk-action-text));">
                            {{ $stats['completed'] }}/{{ $stats['total'] }}
                        </span>
                    </div>
                </div>

                <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Unlocked Sections</p>
                    <div class="mt-3 flex items-end justify-between gap-3">
                        <span class="text-4xl font-semibold text-themed-primary">{{ $unlockedCount }}</span>
                        <span class="text-sm text-themed-secondary">of {{ $course->sections->count() }}</span>
                    </div>
                </div>

                <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Completed Sections</p>
                    <div class="mt-3 flex items-end justify-between gap-3">
                        <span class="text-4xl font-semibold text-themed-primary">{{ $completedSections }}</span>
                        <span class="text-sm text-themed-secondary">milestones cleared</span>
                    </div>
                </div>

                <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Remaining Lessons</p>
                    <div class="mt-3 flex items-end justify-between gap-3">
                        <span class="text-4xl font-semibold text-themed-primary">{{ $remainingLessons }}</span>
                        <span class="text-sm text-themed-secondary">still ahead</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-4 lg:grid-cols-[minmax(0,1fr)_22rem]">
            <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Current Runway</p>
                        <h2 class="mt-2 text-xl font-semibold text-themed-primary">
                            {{ $currentSection?->title ?? 'Start with your first lesson' }}
                        </h2>
                        <p class="mt-2 text-sm leading-6 text-themed-secondary">
                            {{ $currentSection
                                ? "You are {$currentSectionProgress}% through this section. Finish strong to unlock the next part of the course."
                                : 'Open any unlocked lesson and start building momentum.' }}
                        </p>
                    </div>

                    <div class="rounded-lg border border-themed-secondary bg-themed-secondary px-4 py-3 text-right shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Unlock Rule</p>
                        <p class="mt-2 text-sm font-semibold text-themed-primary">
                            {{ $sectionCompletionThreshold }}% per section
                        </p>
                    </div>
                </div>

                <div class="mt-5 h-3 overflow-hidden rounded-full border border-themed-secondary bg-themed-secondary">
                    <div class="h-full rounded-full transition-all duration-500"
                        style="width: {{ $overallProgress }}%; background: rgb(var(--accent-primary));">
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Next Milestone</p>

                @if ($nextUnlock)
                    <h3 class="mt-3 text-lg font-semibold text-themed-primary">{{ $nextUnlock['section_title'] }}</h3>
                    <p class="mt-2 text-sm leading-6 text-themed-secondary">
                        @if ($nextUnlock['remaining_lessons'] > 0)
                            Complete {{ $nextUnlock['remaining_lessons'] }} more
                            lesson{{ $nextUnlock['remaining_lessons'] === 1 ? '' : 's' }} in the current section to open it.
                        @else
                            You have reached the unlock threshold. The next section is ready.
                        @endif
                    </p>

                    <div class="mt-4 rounded-lg border border-themed-secondary bg-themed-secondary px-4 py-3 text-sm text-themed-secondary">
                        {{ $nextUnlock['completed_lessons'] }}/{{ $nextUnlock['required_lessons'] }} lessons needed for unlock
                    </div>
                @else
                    <h3 class="mt-3 text-lg font-semibold text-themed-primary">All sections unlocked</h3>
                    <p class="mt-2 text-sm leading-6 text-themed-secondary">
                        Everything is open now. Focus on completion, review, and certification.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
