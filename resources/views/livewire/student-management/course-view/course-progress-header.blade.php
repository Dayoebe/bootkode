@php
    $stats = $this->getProgressStats();
    $remainingLessons = max($stats['total'] - $stats['completed'], 0);
    $completedSections = $course->sections->filter(function ($section) use ($completedLessons) {
        return $section->lessons->count() > 0
            && $section->lessons->every(fn($lesson) => in_array($lesson->id, $completedLessons ?? []));
    })->count();
@endphp

<div
    class="relative overflow-hidden rounded-[2rem] border border-themed-primary bg-themed-secondary shadow-xl transition-colors duration-300 animate__animated animate__fadeInDown">
    <div class="absolute inset-0 opacity-80"
        style="background:
            radial-gradient(circle at top left, rgba(var(--accent-primary), 0.18), transparent 36%),
            radial-gradient(circle at top right, rgba(var(--accent-secondary), 0.14), transparent 32%),
            linear-gradient(135deg, rgba(var(--accent-primary), 0.06), transparent 55%);">
    </div>

    <div class="relative p-6 md:p-8">
        <div class="flex flex-col gap-8 xl:flex-row xl:items-start xl:justify-between">
            <div class="max-w-3xl">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em]">
                    <span
                        class="rounded-full border border-themed-secondary bg-themed-tertiary px-3 py-1 text-themed-secondary">
                        {{ $course->category->name ?? 'Uncategorized' }}
                    </span>
                    <span
                        class="rounded-full border border-themed-secondary bg-themed-tertiary px-3 py-1 text-themed-secondary capitalize">
                        {{ $course->difficulty_level }}
                    </span>
                    <span
                        class="rounded-full border border-transparent px-3 py-1 text-white"
                        style="background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));">
                        {{ $course->formatted_duration }}
                    </span>
                </div>

                <h1 class="mt-5 text-3xl font-semibold tracking-tight text-themed-primary md:text-4xl">
                    {{ $course->title }}
                </h1>

                <p class="mt-3 max-w-2xl text-base leading-7 text-themed-secondary md:text-lg">
                    {{ $course->subtitle ?: \Illuminate\Support\Str::limit(strip_tags($course->description), 180) }}
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-4 text-sm text-themed-secondary">
                    <span class="inline-flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full border border-themed-secondary bg-themed-tertiary">
                            <i class="fas fa-user-graduate accent-themed-primary"></i>
                        </span>
                        <span>{{ $course->instructor->name ?? 'Bootkode Instructor' }}</span>
                    </span>

                    <span class="inline-flex items-center gap-2">
                        <span class="flex h-9 w-9 items-center justify-center rounded-full border border-themed-secondary bg-themed-tertiary">
                            <i class="fas fa-layer-group accent-themed-primary"></i>
                        </span>
                        <span>{{ $course->sections->count() }} sections</span>
                    </span>

                    @if ($currentSection)
                        <span class="inline-flex items-center gap-2 rounded-full border border-themed-secondary bg-themed-tertiary px-4 py-2">
                            <i class="fas fa-location-arrow accent-themed-primary"></i>
                            <span>Current focus: {{ $currentSection->title }}</span>
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid w-full gap-3 sm:grid-cols-2 xl:max-w-md">
                <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Overall Progress</p>
                    <div class="mt-3 flex items-end justify-between gap-3">
                        <span class="text-4xl font-semibold text-themed-primary">{{ $overallProgress }}%</span>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold text-white"
                            style="background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));">
                            {{ $stats['completed'] }}/{{ $stats['total'] }} lessons
                        </span>
                    </div>
                </div>

                <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Completed Sections</p>
                    <div class="mt-3 flex items-end justify-between gap-3">
                        <span class="text-4xl font-semibold text-themed-primary">{{ $completedSections }}</span>
                        <span class="text-sm text-themed-secondary">of {{ $course->sections->count() }}</span>
                    </div>
                </div>

                <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Remaining Lessons</p>
                    <div class="mt-3 flex items-end justify-between gap-3">
                        <span class="text-4xl font-semibold text-themed-primary">{{ $remainingLessons }}</span>
                        <span class="text-sm text-themed-secondary">still ahead</span>
                    </div>
                </div>

                <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Learning Pace</p>
                    <div class="mt-3 flex items-end justify-between gap-3">
                        <span class="text-4xl font-semibold text-themed-primary">{{ $stats['total'] > 0 ? round($stats['completed'] / $stats['total'] * 100) : 0 }}%</span>
                        <span class="text-sm text-themed-secondary">steady completion</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid gap-4 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-5 shadow-sm">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Course Progress</p>
                        <p class="mt-2 text-sm text-themed-secondary">
                            You have completed {{ $stats['completed'] }} lessons. Keep moving section by section to unlock the rest of the course.
                        </p>
                    </div>
                    <div class="hidden rounded-2xl border border-themed-secondary bg-themed-secondary px-4 py-3 text-right shadow-sm md:block">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Current Section</p>
                        <p class="mt-2 text-sm font-semibold text-themed-primary">
                            {{ $currentSection?->title ?? 'Starting point' }}
                        </p>
                    </div>
                </div>

                <div class="mt-5 h-3 overflow-hidden rounded-full border border-themed-secondary bg-themed-secondary">
                    <div class="h-full rounded-full transition-all duration-500"
                        style="width: {{ $overallProgress }}%; background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Navigation Hint</p>
                <p class="mt-3 text-sm leading-6 text-themed-secondary">
                    Use the course map to jump between unlocked lessons. New sections open when you reach
                    {{ $course->completion_rate_threshold ?? 80 }}% completion for the previous section.
                </p>
            </div>
        </div>
    </div>
</div>
