@php
    $overallProgress = $this->overallProgress;
    $sectionCount = $course->sections->count();
    $lessonCount = $course->sections->sum(fn($section) => $section->lessons->count());
    $lastViewedLesson = $this->lastViewedLesson;
    $resumeLesson = $lastViewedLesson && $lastViewedLesson->id !== $currentLesson?->id ? $lastViewedLesson : null;
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
    x-data="{
        navOpen: false,
        navTrigger: null,
        curriculumCollapsed: localStorage.getItem('course-curriculum-collapsed') === 'true',
        openNav(event) {
            this.navTrigger = event.currentTarget;
            this.navOpen = true;
            this.$nextTick(() => document.getElementById('course-nav-close')?.focus());
        },
        closeNav() {
            const trigger = this.navTrigger;
            this.navOpen = false;
            window.setTimeout(() => trigger?.focus(), 200);
        },
        toggleCurriculum() {
            this.curriculumCollapsed = !this.curriculumCollapsed;
            localStorage.setItem('course-curriculum-collapsed', this.curriculumCollapsed.toString());
        }
    }"
    x-on:close-course-nav.window="closeNav()"
    x-on:keydown.escape.window="if (navOpen) closeNav()"
    class="course-view-shell relative">


    <div class="relative space-y-6 md:space-y-8">
        <livewire:student-management.course-view.course-progress-header :course="$course"
            :overallProgress="$overallProgress" :currentSection="$currentSection"
            :completedLessons="$completedLessons" :unlockedSections="$unlockedSections"
            :sectionCompletionThreshold="$sectionCompletionThreshold"
            wire:key="header-{{ $progressKey }}" />

        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.300ms
                class="overflow-hidden rounded-lg border border-emerald-400/40 bg-brand-ink p-4 text-white shadow-sm sm:p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-white/15 shadow-sm sm:h-16 sm:w-16">
                            <i aria-hidden="true" class="fas fa-rocket text-xl sm:text-3xl"></i>
                        </div>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-white/80">Enrollment confirmed</p>
                            <h3 class="mt-2 text-lg font-semibold sm:text-2xl">Welcome to your new course</h3>
                            <p class="mt-2 text-sm leading-6 text-white/85">{{ session('success') }}</p>
                            <p class="mt-1 text-sm text-white/75">
                                You are enrolled in <span class="font-semibold">{{ $course->title }}</span>.
                            </p>
                        </div>
                    </div>

                    <button @click="show = false"
                        class="self-start rounded-lg border border-white/20 bg-white/10 px-4 py-3 text-sm font-medium text-white transition hover:bg-white/15">
                        Dismiss
                    </button>
                </div>

                <div class="mt-6 hidden gap-3 sm:grid sm:grid-cols-3">
                    <div class="rounded-lg border border-white/15 bg-white/10 p-4 text-center ">
                        <p class="text-2xl font-semibold">{{ $sectionCount }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-white/75">Sections</p>
                    </div>
                    <div class="rounded-lg border border-white/15 bg-white/10 p-4 text-center ">
                        <p class="text-2xl font-semibold">{{ $lessonCount }}</p>
                        <p class="mt-1 text-xs font-semibold uppercase tracking-[0.16em] text-white/75">Lessons</p>
                    </div>
                    <div class="rounded-lg border border-white/15 bg-white/10 p-4 text-center ">
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
                class="hidden overflow-hidden rounded-lg border border-themed-primary bg-themed-secondary p-5 shadow-sm lg:block">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-lg text-white shadow-sm"
                            style="background: rgb(var(--bk-action)); color: rgb(var(--bk-action-text));">
                            <i aria-hidden="true" class="fas fa-play-circle text-2xl"></i>
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
                        class="inline-flex items-center justify-center gap-2 rounded-lg px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5"
                        style="background: rgb(var(--bk-action)); color: rgb(var(--bk-action-text));">
                        <i aria-hidden="true" class="fas fa-arrow-right"></i>
                        Continue
                    </button>
                </div>
            </div>
        @endif

        @include('livewire.student-management.course-view.lesson-workspace')

        @if(count($course->getDocumentsArray()) > 0)
            <details class="group overflow-hidden rounded-lg border border-themed-primary bg-themed-secondary shadow-sm">
                <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-4 p-4 text-themed-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-action">
                    <span>
                        <span class="block text-sm font-semibold">Course resources</span>
                        <span class="mt-1 block text-sm text-themed-secondary">{{ count($course->getDocumentsArray()) }} downloadable {{ \Illuminate\Support\Str::plural('file', count($course->getDocumentsArray())) }}</span>
                    </span>
                    <i aria-hidden="true" class="fas fa-chevron-down text-sm text-themed-secondary transition-transform group-open:rotate-180"></i>
                </summary>
                <div class="grid gap-3 border-t border-themed-secondary p-4">
                    @foreach($course->getDocumentsArray() as $courseDocumentIndex => $courseDocument)
                        <article class="flex flex-col gap-4 rounded-lg border border-themed-secondary bg-themed-tertiary p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <h2 class="truncate font-semibold text-themed-primary">{{ $courseDocument['name'] ?? 'Course resource' }}</h2>
                                <p class="mt-1 text-sm text-themed-secondary">
                                    {{ strtoupper(pathinfo($courseDocument['name'] ?? ($courseDocument['path'] ?? ''), PATHINFO_EXTENSION) ?: 'FILE') }}
                                    @if(isset($courseDocument['size'])) · {{ \Illuminate\Support\Number::fileSize((int) $courseDocument['size'], precision: 1) }} @endif
                                </p>
                            </div>
                            <x-file-preview-link source="course-document" :record="$course->id" :file="$courseDocumentIndex" label="View resource" mode="overlay" />
                        </article>
                    @endforeach
                </div>
            </details>
        @endif

        @if ($course->instructor)
            <section class="overflow-hidden rounded-lg border border-themed-primary bg-themed-secondary p-5 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex min-w-0 items-center gap-4">
                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-lg bg-action text-lg font-semibold text-action-foreground">
                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($course->instructor->name ?? 'I', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Instructor support</p>
                            <h2 class="mt-1 truncate text-lg font-semibold text-themed-primary">{{ $course->instructor->name }}</h2>
                            <p class="mt-1 text-sm leading-6 text-themed-secondary">Ask course questions directly and keep the conversation attached to this course.</p>
                        </div>
                    </div>

                    <button wire:click="messageInstructor" wire:loading.attr="disabled" wire:target="messageInstructor"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-action px-5 py-3 text-sm font-semibold text-action-foreground shadow-sm transition hover:bg-action disabled:cursor-not-allowed disabled:opacity-60">
                        <i aria-hidden="true" class="fas fa-comment-dots"></i>
                        <span wire:loading.remove wire:target="messageInstructor">Message Instructor</span>
                        <span wire:loading wire:target="messageInstructor">Opening</span>
                    </button>
                </div>
            </section>
        @endif

        <details class="group overflow-hidden rounded-lg border border-themed-primary bg-themed-secondary shadow-sm">
            <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-4 p-4 text-themed-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-action">
                <span>
                    <span class="block text-sm font-semibold">Offline learning</span>
                    <span class="mt-1 block text-sm text-themed-secondary">Download or manage this course on this device</span>
                </span>
                <i aria-hidden="true" class="fas fa-chevron-down text-sm text-themed-secondary transition-transform group-open:rotate-180"></i>
            </summary>
            <section
                x-data="window.bootkodeOffline.panel({
                    slug: @js($course->slug),
                    manifestUrl: @js(route('offline-learning.manifest', $course->slug)),
                    storageLimitMb: @js($offlineStorageLimitMb),
                    types: @js($offlineContentTypes)
                })"
                class="overflow-hidden rounded-lg bg-themed-secondary p-5 shadow-sm">
            <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Offline pack</p>
                    <h2 class="mt-2 text-xl font-semibold text-themed-primary">Download this course for mobile learning</h2>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-themed-secondary">
                        Cache lesson text, course files, images, and audio on this device. Complete lessons in the offline reader and sync progress when the connection returns.
                    </p>

                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                        <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Estimated Pack</p>
                            <p class="mt-2 text-2xl font-semibold text-themed-primary">{{ number_format($offlinePack?->size_mb ?? $offlineEstimateMb, 2) }} MB</p>
                        </div>
                        <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Last Download</p>
                            <p class="mt-2 text-sm font-semibold text-themed-primary">{{ $offlinePack?->downloaded_at?->diffForHumans() ?? 'Not downloaded yet' }}</p>
                        </div>
                        <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-4">
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
                                    <i aria-hidden="true" class="{{ $meta['icon'] }} mr-1"></i>{{ $meta['label'] }}
                                </button>
                            @endforeach
                        </div>

                        <div class="mt-4 grid gap-2 sm:grid-cols-2">
                            <button type="button" @click="download" :disabled="downloading"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-action px-4 py-2 text-sm font-semibold text-action-foreground transition hover:bg-action/90 disabled:opacity-60">
                                <i aria-hidden="true" class="fas fa-download"></i>
                                <span x-text="downloading ? 'Downloading' : (installed ? 'Refresh Pack' : 'Download Pack')"></span>
                            </button>
                            <button type="button" @click="open" x-show="installed"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-themed-secondary bg-themed-secondary px-4 py-2 text-sm font-semibold text-themed-primary">
                                <i aria-hidden="true" class="fas fa-book-reader"></i>
                                Read Offline
                            </button>
                            <button type="button" @click="sync"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-themed-secondary bg-themed-secondary px-4 py-2 text-sm font-semibold text-themed-primary">
                                <i aria-hidden="true" class="fas fa-rotate"></i>
                                Sync Progress
                            </button>
                            <button type="button" @click="remove" x-show="installed"
                                class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                                <i aria-hidden="true" class="fas fa-trash"></i>
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
        </details>

        @if ($certificateEarned ?? false)
            <div
                class="overflow-hidden rounded-lg border border-emerald-400/40 bg-brand-ink   p-6 text-white shadow-sm  ">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-lg bg-white/15 shadow-sm">
                            <i aria-hidden="true" class="fas fa-trophy text-3xl"></i>
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
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-surface px-5 py-3 text-sm font-semibold text-emerald-600 shadow-sm transition hover:-translate-y-0.5 hover:bg-emerald-50">
                        <i aria-hidden="true" class="fas fa-certificate"></i>
                        View Certificate
                    </a>
                </div>
            </div>
        @endif

        <details id="course-reviews" class="group overflow-hidden rounded-lg border border-themed-secondary bg-themed-secondary shadow-sm">
            <summary class="flex min-h-11 cursor-pointer list-none items-center justify-between gap-4 p-4 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-action">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-themed-secondary">Optional</p>
                    <h2 class="mt-1 text-base font-semibold text-themed-primary">Learner reviews</h2>
                    <p class="mt-1 text-sm leading-6 text-themed-secondary">Read recorded feedback or leave your own review.</p>
                </div>
                <i class="fas fa-chevron-down text-sm text-themed-secondary transition-transform group-open:rotate-180" aria-hidden="true"></i>
            </summary>

            <div class="border-t border-themed-secondary p-4">
                <livewire:student-management.course-view.review :course="$course" wire:key="reviews-{{ $course->id }}" />
            </div>
        </details>
    </div>

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
