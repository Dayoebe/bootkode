@php
    $defaultExpanded = collect([$currentLesson?->section_id, $sections->first()?->id])
        ->filter()
        ->unique()
        ->values()
        ->all();
    $totalLessons = $sections->flatMap->lessons->count();
@endphp

<div x-data="{
        expandedSections: @js($defaultExpanded),
        toggleSection(id) {
            this.expandedSections = this.expandedSections.includes(id)
                ? this.expandedSections.filter(sectionId => sectionId !== id)
                : [...this.expandedSections, id];
        },
        isExpanded(id) {
            return this.expandedSections.includes(id);
        }
    }"
    class="sticky top-4 rounded-[2rem] border border-themed-primary bg-themed-secondary p-4 shadow-xl transition-colors duration-300 animate__animated animate__fadeInLeft">
    <div class="rounded-2xl border border-themed-secondary p-5 shadow-sm"
        style="background: linear-gradient(160deg, rgba(var(--accent-primary), 0.14), rgba(var(--accent-secondary), 0.08));">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Course Map</p>
                <h2 class="mt-2 text-xl font-semibold text-themed-primary">Navigate with clarity</h2>
                <p class="mt-2 text-sm leading-6 text-themed-secondary">
                    Open a section, pick an unlocked lesson, and keep moving in sequence.
                </p>
            </div>
            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-themed-secondary shadow-sm">
                <i class="fas fa-compass text-lg accent-themed-primary"></i>
            </span>
        </div>

        <div class="mt-5 grid grid-cols-3 gap-3 text-center">
            <div class="rounded-2xl border border-themed-secondary bg-themed-secondary px-3 py-3 shadow-sm">
                <p class="text-lg font-semibold text-themed-primary">{{ $sections->count() }}</p>
                <p class="text-[11px] uppercase tracking-[0.16em] text-themed-tertiary">Sections</p>
            </div>
            <div class="rounded-2xl border border-themed-secondary bg-themed-secondary px-3 py-3 shadow-sm">
                <p class="text-lg font-semibold text-themed-primary">{{ $totalLessons }}</p>
                <p class="text-[11px] uppercase tracking-[0.16em] text-themed-tertiary">Lessons</p>
            </div>
            <div class="rounded-2xl border border-themed-secondary bg-themed-secondary px-3 py-3 shadow-sm">
                <p class="text-lg font-semibold text-themed-primary">{{ count($completedLessons) }}</p>
                <p class="text-[11px] uppercase tracking-[0.16em] text-themed-tertiary">Done</p>
            </div>
        </div>
    </div>

    <div class="mt-4 max-h-[calc(100vh-14rem)] space-y-3 overflow-y-auto pr-1">
        @foreach ($sections as $section)
            @php
                $sectionProgress = $this->calculateSectionProgress($section);
                $isUnlocked = $this->isSectionUnlocked($section->id);
                $isCompleted = $this->isSectionCompleted($section);
            @endphp

            <section
                class="overflow-hidden rounded-2xl border border-themed-secondary bg-themed-tertiary shadow-sm transition-all duration-300 {{ $isUnlocked ? '' : 'opacity-70' }}">
                <button type="button"
                    @click="toggleSection({{ $section->id }})"
                    class="flex w-full items-start justify-between gap-3 px-4 py-4 text-left">
                    <div class="flex min-w-0 items-start gap-3">
                        <span
                            class="mt-0.5 flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-2xl {{ $isCompleted ? 'bg-green-500/15 text-green-500' : ($isUnlocked ? 'bg-themed-secondary accent-themed-primary' : 'bg-themed-secondary text-themed-tertiary') }}">
                            @if ($isCompleted)
                                <i class="fas fa-check-circle"></i>
                            @elseif ($isUnlocked)
                                <i class="fas fa-book-open"></i>
                            @else
                                <i class="fas fa-lock"></i>
                            @endif
                        </span>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="truncate text-sm font-semibold text-themed-primary">
                                    {{ $section->title }}
                                </h3>
                                <span
                                    class="rounded-full border border-themed-secondary bg-themed-secondary px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-themed-tertiary">
                                    {{ $section->lessons->count() }} lessons
                                </span>
                            </div>

                            <p class="mt-1 text-xs leading-5 text-themed-secondary">
                                @if ($isCompleted)
                                    Section completed. You can revisit any lesson here.
                                @elseif ($isUnlocked)
                                    {{ $sectionProgress }}% complete. Continue from the current focus or revisit any open lesson.
                                @else
                                    Complete {{ $sectionCompletionThreshold ?? 80 }}% of the previous section to unlock this one.
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-shrink-0 flex-col items-end gap-2">
                        <span class="text-xs font-semibold text-themed-secondary">{{ $sectionProgress }}%</span>
                        <span class="flex h-8 w-8 items-center justify-center rounded-full border border-themed-secondary bg-themed-secondary">
                            <i class="fas fa-chevron-down text-xs text-themed-tertiary transition-transform duration-300"
                                :class="{ 'rotate-180': isExpanded({{ $section->id }}) }"></i>
                        </span>
                    </div>
                </button>

                <div class="px-4 pb-4" x-show="isExpanded({{ $section->id }})" x-transition.opacity.scale.origin.top>
                    <div class="mb-4 h-2 overflow-hidden rounded-full border border-themed-secondary bg-themed-secondary">
                        <div class="h-full rounded-full transition-all duration-300"
                            style="width: {{ $sectionProgress }}%; background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));">
                        </div>
                    </div>

                    <div class="space-y-2 border-t border-themed-secondary pt-4">
                        @foreach ($section->lessons as $lesson)
                            @php
                                $isLessonCompleted = in_array($lesson->id, $completedLessons);
                                $isCurrentLesson = $currentLesson && $currentLesson->id == $lesson->id;
                            @endphp

                            <button wire:click="selectLesson({{ $lesson->id }}, {{ $section->id }})"
                                @click="$dispatch('close-course-nav')"
                                @if (!$isUnlocked) disabled @endif
                                class="flex w-full items-center justify-between gap-3 rounded-2xl border px-3 py-3 text-left transition-all duration-200
                                    {{ $isCurrentLesson
                                        ? 'border-transparent text-white shadow-lg'
                                        : ($isUnlocked
                                            ? 'border-themed-secondary bg-themed-secondary text-themed-primary hover:-translate-y-0.5 hover:shadow-md'
                                            : 'border-themed-secondary bg-themed-secondary text-themed-tertiary cursor-not-allowed') }}"
                                @if ($isCurrentLesson)
                                    style="background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));"
                                @endif>
                                <div class="flex min-w-0 items-center gap-3">
                                    <span
                                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-2xl {{ $isCurrentLesson ? 'bg-white/20 text-white' : ($isLessonCompleted ? 'bg-green-500/15 text-green-500' : ($isUnlocked ? 'bg-themed-tertiary accent-themed-primary' : 'bg-themed-tertiary text-themed-tertiary')) }}">
                                        @if ($isLessonCompleted)
                                            <i class="fas fa-check"></i>
                                        @elseif ($isCurrentLesson)
                                            <i class="fas fa-play"></i>
                                        @elseif ($isUnlocked)
                                            <i class="far fa-circle"></i>
                                        @else
                                            <i class="fas fa-lock"></i>
                                        @endif
                                    </span>

                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-medium {{ $isCurrentLesson ? 'text-white' : '' }}">
                                            {{ $lesson->title }}
                                        </p>
                                        <div
                                            class="mt-1 flex flex-wrap items-center gap-2 text-[11px] {{ $isCurrentLesson ? 'text-white/80' : 'text-themed-secondary' }}">
                                            @if ($lesson->duration_minutes)
                                                <span>{{ $lesson->duration_minutes }} min</span>
                                            @endif
                                            @if ($lesson->hasVideo())
                                                <span class="inline-flex items-center gap-1">
                                                    <i class="fas fa-video text-red-500"></i>
                                                    Video
                                                </span>
                                            @endif
                                            @if ($lesson->hasDocuments())
                                                <span class="inline-flex items-center gap-1">
                                                    <i class="fas fa-file-alt text-indigo-500"></i>
                                                    Resources
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if ($isCurrentLesson)
                                    <i class="fas fa-arrow-right text-sm text-white"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </section>
        @endforeach
    </div>

    <div class="mt-4 rounded-2xl border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
        <div class="flex items-center justify-between text-sm">
            <span class="text-themed-secondary">Unlock rule</span>
            <span class="font-semibold text-themed-primary">{{ $sectionCompletionThreshold ?? 80 }}%</span>
        </div>
        <p class="mt-2 text-xs leading-5 text-themed-secondary">
            Progress through each section in order. Once you hit the required completion threshold, the next section opens automatically.
        </p>
    </div>
</div>
