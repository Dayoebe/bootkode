@php
    $stats = $this->getProgressStats();
    $remainingLessons = max($stats['total'] - $stats['completed'], 0);
@endphp

<section class="rounded-lg border border-themed-primary bg-themed-secondary px-4 py-4 shadow-sm sm:px-5"
    aria-label="Course progress summary">
    <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(17rem,23rem)] lg:items-center">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.14em] text-themed-secondary">
                <span>{{ $course->category->name ?? 'Course' }}</span>
                <span aria-hidden="true">·</span>
                <span class="capitalize">{{ $course->difficulty_level ?: 'Self paced' }}</span>
                <span aria-hidden="true">·</span>
                <span>{{ $course->sections->count() }} sections</span>
                <span aria-hidden="true">·</span>
                <span>{{ $stats['total'] }} lessons</span>
            </div>

            <p class="mt-2 truncate font-display text-lg font-semibold text-themed-primary sm:text-xl">{{ $course->title }}</p>
            @if($course->subtitle || $course->description)
                <p class="mt-1 line-clamp-2 max-w-4xl text-sm leading-6 text-themed-secondary">
                    {{ $course->subtitle ?: \Illuminate\Support\Str::limit(strip_tags($course->description), 180) }}
                </p>
            @endif
        </div>

        <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-3 sm:p-4">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-themed-secondary">Course progress</p>
                    <p class="mt-1 text-sm text-themed-primary">
                        <span class="font-semibold">{{ $stats['completed'] }} of {{ $stats['total'] }}</span> lessons completed
                    </p>
                </div>
                <span class="text-2xl font-semibold text-themed-primary">{{ $overallProgress }}%</span>
            </div>
            <div class="mt-3 h-2 overflow-hidden rounded-full bg-themed-secondary"
                role="progressbar" aria-label="Course completion" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $overallProgress }}">
                <div class="h-full rounded-full bg-action transition-[width] duration-300" style="width: {{ $overallProgress }}%"></div>
            </div>
            <p class="mt-2 text-xs text-themed-secondary">
                {{ $remainingLessons > 0 ? $remainingLessons.' lesson'.($remainingLessons === 1 ? '' : 's').' remaining' : 'All lessons completed' }}
                @if($currentSection) · Current section: {{ $currentSection->title }} @endif
            </p>
        </div>
    </div>
</section>
