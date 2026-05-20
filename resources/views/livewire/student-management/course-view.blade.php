@php
    $overallProgress = $this->overallProgress;
    $sectionCount = $course->sections->count();
    $lessonCount = $course->sections->sum(fn($section) => $section->lessons->count());
    $lastViewedLesson = $this->lastViewedLesson;
    $resumeLesson = $lastViewedLesson && $lastViewedLesson->id !== $currentLesson?->id ? $lastViewedLesson : null;
    $completedSectionCount = $course->sections->filter(function ($section) use ($completedLessons) {
        return $section->lessons->count() > 0
            && $section->lessons->every(fn($lesson) => in_array($lesson->id, $completedLessons, true));
    })->count();
    $unlockedSectionCount = count($unlockedSections);
    $currentSectionProgress = $currentSection ? $this->calculateSectionProgress($currentSection) : 0;
    $nextLockedSection = $course->sections->first(fn($section) => !in_array($section->id, $unlockedSections, true));
    $progressKey = $course->id . '-' . $progressVersion . '-' . ($currentLesson?->id ?? 'none');
    $offlinePack = $this->offlinePack;
    $offlineEstimateMb = $this->estimateOfflinePackSizeMb();
    $offlineTypes = [
        'lessons' => ['label' => 'Lessons', 'icon' => 'fas fa-align-left'],
        'documents' => ['label' => 'PDFs', 'icon' => 'fas fa-file-pdf'],
        'images' => ['label' => 'Images', 'icon' => 'fas fa-image'],
        'audio' => ['label' => 'Audio', 'icon' => 'fas fa-headphones'],
        'video' => ['label' => 'Videos', 'icon' => 'fas fa-video'],
    ];
@endphp

<div
    x-data="{ navOpen: false }"
    x-on:close-course-nav.window="navOpen = false"
    class="course-view-shell relative overflow-hidden rounded-[2rem]">
    <div class="pointer-events-none absolute inset-0 opacity-70"
        style="background:
            radial-gradient(circle at top left, rgba(var(--accent-primary), 0.12), transparent 28%),
            radial-gradient(circle at top right, rgba(var(--accent-secondary), 0.1), transparent 26%),
            linear-gradient(180deg, rgba(var(--accent-primary), 0.03), transparent 35%);">
    </div>

    <div class="relative space-y-6 md:space-y-8">
        <livewire:student-management.course-view.course-progress-header :course="$course"
            :overallProgress="$overallProgress" :currentSection="$currentSection"
            :completedLessons="$completedLessons" :unlockedSections="$unlockedSections"
            :sectionCompletionThreshold="$sectionCompletionThreshold"
            wire:key="header-{{ $progressKey }}" />

        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms
                class="overflow-hidden rounded-[2rem] border border-emerald-400/40 bg-gradient-to-r from-emerald-500 to-green-600 p-6 text-white shadow-xl animate__animated animate__fadeIn">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-3xl bg-white/15 shadow-lg">
                            <i class="fas fa-rocket text-3xl"></i>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/80">Enrollment confirmed</p>
                            <h3 class="mt-2 text-2xl font-semibold">Welcome to your new course</h3>
                            <p class="mt-2 text-sm leading-6 text-white/85">{{ session('success') }}</p>
                            <p class="mt-1 text-sm text-white/75">
                                You are enrolled in <span class="font-semibold">{{ $course->title }}</span>.
                            </p>
                        </div>
                    </div>

                    <button @click="show = false"
                        class="self-start rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-sm font-medium text-white transition hover:bg-white/15">
                        Dismiss
                    </button>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 text-center backdrop-blur-sm">
                        <p class="text-2xl font-semibold">{{ $sectionCount }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-white/75">Sections</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 text-center backdrop-blur-sm">
                        <p class="text-2xl font-semibold">{{ $lessonCount }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-white/75">Lessons</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-4 text-center backdrop-blur-sm">
                        <p class="text-2xl font-semibold">
                            {{ $course->estimated_duration_minutes ? round($course->estimated_duration_minutes / 60) . 'h' : 'Self-paced' }}
                        </p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-white/75">Estimated Time</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($resumeLesson)
            <div
                class="overflow-hidden rounded-[2rem] border border-themed-primary bg-themed-secondary p-5 shadow-xl animate__animated animate__fadeInUp">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-3xl text-white shadow-lg"
                            style="background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));">
                            <i class="fas fa-play-circle text-2xl"></i>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Continue learning</p>
                            <h3 class="mt-2 text-xl font-semibold text-themed-primary">Jump back into your last lesson</h3>
                            <p class="mt-2 text-sm leading-6 text-themed-secondary">
                                Resume from <span class="font-semibold text-themed-primary">{{ $resumeLesson->title }}</span>
                                and keep your momentum.
                            </p>
                        </div>
                    </div>

                    <button wire:click="continueFromLastLesson"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5"
                        style="background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));">
                        <i class="fas fa-arrow-right"></i>
                        Continue
                    </button>
                </div>
            </div>
        @endif

        @if ($course->instructor)
            <section class="overflow-hidden rounded-[1.5rem] border border-themed-primary bg-themed-secondary p-5 shadow-lg">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex min-w-0 items-center gap-4">
                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-lg font-semibold text-white">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($course->instructor->name ?? 'I', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Instructor support</p>
                            <h2 class="mt-1 truncate text-lg font-semibold text-themed-primary">{{ $course->instructor->name }}</h2>
                            <p class="mt-1 text-sm leading-6 text-themed-secondary">Ask course questions directly and keep the conversation attached to this course.</p>
                        </div>
                    </div>

                    <button wire:click="messageInstructor" wire:loading.attr="disabled" wire:target="messageInstructor"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                        <i class="fas fa-comment-dots"></i>
                        <span wire:loading.remove wire:target="messageInstructor">Message Instructor</span>
                        <span wire:loading wire:target="messageInstructor">Opening</span>
                    </button>
                </div>
            </section>
        @endif

        <section
            x-data="window.bootkodeOffline.panel({
                slug: @js($course->slug),
                manifestUrl: @js(route('offline-learning.manifest', $course->slug)),
                storageLimitMb: @js($offlineStorageLimitMb),
                types: @js($offlineContentTypes)
            })"
            class="overflow-hidden rounded-[1.5rem] border border-themed-primary bg-themed-secondary p-5 shadow-lg">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Offline pack</p>
                    <h2 class="mt-2 text-xl font-semibold text-themed-primary">Download this course for mobile learning</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-themed-secondary">
                        Cache lesson text, course files, images, and audio on this device. Complete lessons in the offline reader and sync progress when the connection returns.
                    </p>

                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Estimated Pack</p>
                            <p class="mt-2 text-2xl font-semibold text-themed-primary">{{ number_format($offlinePack?->size_mb ?? $offlineEstimateMb, 2) }} MB</p>
                        </div>
                        <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Last Download</p>
                            <p class="mt-2 text-sm font-semibold text-themed-primary">{{ $offlinePack?->downloaded_at?->diffForHumans() ?? 'Not downloaded yet' }}</p>
                        </div>
                        <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Last Sync</p>
                            <p class="mt-2 text-sm font-semibold text-themed-primary">{{ $offlinePack?->last_synced_at?->diffForHumans() ?? 'Waiting for offline progress' }}</p>
                        </div>
                    </div>
                </div>

                <div class="w-full xl:max-w-md">
                    <div class="rounded-[1.25rem] border border-themed-secondary bg-themed-tertiary p-4">
                        <label class="block text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Storage limit on this device</label>
                        <div class="mt-2 flex items-center gap-2">
                            <input type="number" min="50" max="5000" step="50" x-model.number="storageLimitMb"
                                class="min-w-0 flex-1 rounded-lg border border-themed-secondary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                            <span class="text-sm font-semibold text-themed-secondary">MB</span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2">
                            @foreach($offlineTypes as $type => $meta)
                                <button type="button" @click="toggleType(@js($type))"
                                    class="rounded-lg border px-3 py-2 text-left text-xs font-semibold transition"
                                    :class="selectedTypes.includes(@js($type)) ? 'border-emerald-500 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : 'border-themed-secondary bg-themed-secondary text-themed-secondary'">
                                    <i class="{{ $meta['icon'] }} mr-1"></i>{{ $meta['label'] }}
                                </button>
                            @endforeach
                        </div>

                        <div class="mt-4 grid gap-2 sm:grid-cols-2">
                            <button type="button" @click="download" :disabled="downloading"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-accent-themed-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-accent-themed-secondary disabled:opacity-60">
                                <i class="fas fa-download"></i>
                                <span x-text="downloading ? 'Downloading' : (installed ? 'Refresh Pack' : 'Download Pack')"></span>
                            </button>
                            <button type="button" @click="open" x-show="installed"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-themed-secondary bg-themed-secondary px-4 py-2 text-sm font-semibold text-themed-primary">
                                <i class="fas fa-book-reader"></i>
                                Read Offline
                            </button>
                            <button type="button" @click="sync"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-themed-secondary bg-themed-secondary px-4 py-2 text-sm font-semibold text-themed-primary">
                                <i class="fas fa-rotate"></i>
                                Sync Progress
                            </button>
                            <button type="button" @click="remove" x-show="installed"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                                <i class="fas fa-trash"></i>
                                Remove
                            </button>
                        </div>

                        <div class="mt-3 space-y-1 text-sm">
                            <p class="font-medium text-themed-primary" x-text="status"></p>
                            <p class="text-xs text-themed-secondary" x-text="progressText"></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
            <div class="overflow-hidden rounded-[1.75rem] border border-themed-primary bg-themed-secondary p-5 shadow-lg">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Unlocked Sections</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <span class="text-3xl font-semibold text-themed-primary">{{ $unlockedSectionCount }}</span>
                    <span class="rounded-full border border-themed-secondary bg-themed-tertiary px-3 py-1 text-xs font-semibold text-themed-secondary">
                        of {{ $sectionCount }}
                    </span>
                </div>
                <p class="mt-3 text-sm leading-6 text-themed-secondary">
                    {{ $nextLockedSection ? 'Keep progressing to open the next chapter.' : 'Every section is open for review.' }}
                </p>
            </div>

            <div class="overflow-hidden rounded-[1.75rem] border border-themed-primary bg-themed-secondary p-5 shadow-lg">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Completed Lessons</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <span class="text-3xl font-semibold text-themed-primary">{{ count($completedLessons) }}</span>
                    <span class="rounded-full border border-themed-secondary bg-themed-tertiary px-3 py-1 text-xs font-semibold text-themed-secondary">
                        {{ $lessonCount }} total
                    </span>
                </div>
                <p class="mt-3 text-sm leading-6 text-themed-secondary">
                    {{ $overallProgress }}% of the course is done and saved to your enrollment record.
                </p>
            </div>

            <div class="overflow-hidden rounded-[1.75rem] border border-themed-primary bg-themed-secondary p-5 shadow-lg">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Current Section</p>
                <div class="mt-3 flex items-end justify-between gap-3">
                    <span class="truncate text-2xl font-semibold text-themed-primary">
                        {{ $currentSection?->title ?? 'Ready to begin' }}
                    </span>
                    <span class="rounded-full border border-themed-secondary bg-themed-tertiary px-3 py-1 text-xs font-semibold text-themed-secondary">
                        {{ $currentSectionProgress }}%
                    </span>
                </div>
                <div class="mt-4 h-2 overflow-hidden rounded-full border border-themed-secondary bg-themed-tertiary">
                    <div class="h-full rounded-full"
                        style="width: {{ $currentSectionProgress }}%; background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));">
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-[1.75rem] border border-themed-primary bg-themed-secondary p-5 shadow-lg">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Next Unlock</p>
                <div class="mt-3">
                    <p class="text-2xl font-semibold text-themed-primary">
                        {{ $nextLockedSection?->title ?? 'All open' }}
                    </p>
                    <p class="mt-2 text-sm leading-6 text-themed-secondary">
                        @if ($nextLockedSection)
                            Reach {{ $sectionCompletionThreshold }}% in the current section to unlock it.
                        @else
                            You can move freely through every section now.
                        @endif
                    </p>
                </div>
            </div>
        </section>

        @if ($certificateEarned ?? false)
            <div
                class="overflow-hidden rounded-[2rem] border border-emerald-400/40 bg-gradient-to-r from-emerald-500 to-green-600 p-6 text-white shadow-xl animate__animated animate__fadeInUp">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-3xl bg-white/15 shadow-lg">
                            <i class="fas fa-trophy text-3xl"></i>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/80">Course completed</p>
                            <h3 class="mt-2 text-2xl font-semibold">Your certificate is ready</h3>
                            <p class="mt-2 text-sm leading-6 text-white/85">
                                You have finished this course. View your certificate and keep building your portfolio.
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('student.certificates.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-emerald-600 shadow-lg transition hover:-translate-y-0.5 hover:bg-emerald-50">
                        <i class="fas fa-certificate"></i>
                        View Certificate
                    </a>
                </div>
            </div>
        @endif

        <div class="lg:hidden">
            <div class="sticky top-4 z-30 overflow-hidden rounded-[1.75rem] border border-themed-primary bg-themed-secondary p-4 shadow-xl animate__animated animate__fadeInUp">
                <div class="flex flex-col gap-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Current lesson</p>
                        <p class="mt-2 truncate text-sm font-semibold text-themed-primary">
                            {{ $currentLesson?->title ?? 'Choose a lesson to begin' }}
                        </p>
                        <p class="mt-1 text-xs text-themed-secondary">
                            {{ $currentSection?->title ?? 'Course overview' }} • {{ $overallProgress }}% complete
                        </p>
                        </div>

                        <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary px-3 py-2 text-right">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-themed-secondary">Completed</p>
                            <p class="mt-1 text-sm font-semibold text-themed-primary">{{ count($completedLessons) }}/{{ $lessonCount }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary px-4 py-3">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-themed-secondary">Unlocked</p>
                            <p class="mt-1 text-lg font-semibold text-themed-primary">{{ $unlockedSectionCount }}/{{ $sectionCount }}</p>
                        </div>
                        <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary px-4 py-3">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-themed-secondary">Current Section</p>
                            <p class="mt-1 truncate text-sm font-semibold text-themed-primary">{{ $currentSection?->title ?? 'Ready to start' }}</p>
                        </div>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-2">
                        @if ($resumeLesson)
                            <button wire:click="continueFromLastLesson"
                                class="inline-flex w-full items-center justify-center gap-2 rounded-2xl border border-themed-secondary bg-themed-tertiary px-4 py-3 text-sm font-semibold text-themed-primary shadow-sm transition">
                                <i class="fas fa-history"></i>
                                Resume
                            </button>
                        @endif

                        <button @click="navOpen = true"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl px-4 py-3 text-sm font-semibold text-white shadow-lg transition"
                            style="background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));">
                            <i class="fas fa-compass"></i>
                            Course Map
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div x-cloak x-show="navOpen" class="fixed inset-0 z-50 lg:hidden">
            <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="navOpen = false"></div>

            <div class="absolute inset-y-0 left-0 w-full max-w-md p-4">
                <div
                    class="flex h-full flex-col overflow-hidden rounded-[2rem] border border-themed-primary bg-themed-secondary shadow-2xl animate__animated animate__fadeInLeft">
                    <div class="flex items-center justify-between border-b border-themed-secondary px-5 py-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Learning map</p>
                            <h2 class="mt-1 text-lg font-semibold text-themed-primary">Navigate the course</h2>
                        </div>

                        <button @click="navOpen = false"
                            class="flex h-10 w-10 items-center justify-center rounded-2xl border border-themed-secondary bg-themed-tertiary text-themed-primary transition hover:bg-themed-secondary">
                            <i class="fas fa-times"></i>
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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[22rem_minmax(0,1fr)] lg:items-start">
            <aside class="hidden lg:block">
                <livewire:student-management.course-view.course-progress-sidebar :course="$course"
                    :sections="$course->sections" :currentLesson="$currentLesson" :completedLessons="$completedLessons"
                    :unlockedSections="$unlockedSections" :sectionCompletionThreshold="$sectionCompletionThreshold"
                    wire:key="sidebar-desktop-{{ $progressKey }}" />
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
                    <div
                        class="rounded-[2rem] border border-themed-primary bg-themed-secondary p-6 text-center shadow-xl animate__animated animate__fadeInUp sm:p-10">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl border border-themed-secondary bg-themed-tertiary">
                            <i class="fas fa-book-open text-2xl accent-themed-primary"></i>
                        </div>
                        <h3 class="mt-5 text-xl font-semibold text-themed-primary">No lesson selected</h3>
                        <p class="mt-2 text-sm leading-6 text-themed-secondary">
                            Open the course map and choose any unlocked lesson to begin learning.
                        </p>
                        <button @click="navOpen = true"
                            class="mt-5 inline-flex items-center gap-2 rounded-2xl border border-themed-secondary bg-themed-tertiary px-5 py-3 text-sm font-semibold text-themed-primary transition hover:bg-themed-secondary lg:hidden">
                            <i class="fas fa-compass"></i>
                            Open Course Map
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <section id="course-reviews"
            class="overflow-hidden rounded-[1.5rem] border border-themed-secondary bg-themed-tertiary/40 p-4 shadow-sm animate__animated animate__fadeInUp">
            <div class="flex flex-col gap-2 border-b border-themed-secondary/70 pb-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-themed-secondary">Optional</p>
                    <h2 class="mt-2 text-lg font-semibold text-themed-primary">Learner reviews</h2>
                    <p class="mt-1 text-sm leading-6 text-themed-secondary">
                        Keep this collapsed unless you want to read feedback or leave a review.
                    </p>
                </div>

                <div class="rounded-2xl border border-themed-secondary bg-themed-secondary px-4 py-2 text-xs text-themed-secondary">
                    Secondary section
                </div>
            </div>

            <div class="mt-4">
                <livewire:student-management.course-view.review :course="$course" wire:key="reviews-{{ $course->id }}" />
            </div>
        </section>
    </div>

    <style>
        .course-view-shell [x-cloak] {
            display: none !important;
        }

        .course-view-shell * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

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

    @if ($shouldCelebrate)
        @push('scripts')
            <script>
                document.addEventListener('livewire:init', () => {
                    Livewire.on('confetti', () => {
                        if (typeof confetti === 'undefined') {
                            const script = document.createElement('script');
                            script.src = 'https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js';
                            script.onload = () => {
                                fireConfetti();
                            };
                            script.onerror = () => {
                                console.error('Failed to load confetti script');
                            };
                            document.head.appendChild(script);
                        } else {
                            fireConfetti();
                        }
                    });
                });

                function fireConfetti() {
                    if (typeof confetti === 'undefined') {
                        return;
                    }

                    confetti({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 }
                    });

                    setTimeout(() => {
                        confetti({
                            particleCount: 50,
                            angle: 60,
                            spread: 55,
                            origin: { x: 0 }
                        });
                    }, 200);

                    setTimeout(() => {
                        confetti({
                            particleCount: 50,
                            angle: 120,
                            spread: 55,
                            origin: { x: 1 }
                        });
                    }, 400);
                }

                @if ($shouldCelebrate)
                    document.addEventListener('DOMContentLoaded', () => {
                        if (window.Livewire) {
                            Livewire.dispatch('confetti');
                        }
                    });
                @endif
            </script>
        @endpush
    @endif
</div>
