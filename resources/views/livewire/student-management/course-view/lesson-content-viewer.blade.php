@php
    $totalLessons = count($allLessons);
    $coursePosition = $totalLessons > 0 ? round((($currentIndex + 1) / $totalLessons) * 100) : 0;
    $timeComparison = $showTimeComparison ? $this->getTimeComparison() : null;
    $previousLesson = $this->getPreviousLesson();
    $nextLesson = $this->getNextLesson();
    $estimatedDuration = $lesson->estimated_duration_minutes ?? $lesson->duration_minutes ?? null;
    $documentCount = $lesson->hasDocuments() ? count($lesson->getDocumentsArray()) : 0;
    $uploadedVideoCount = $lesson->hasVideo() ? count($lesson->getVideosArray()) : 0;
    $audioCount = $lesson->hasAudio() ? count($lesson->getAudiosArray()) : 0;
    $imageCount = ($lesson->image_path ? 1 : 0) + ($lesson->hasImage() ? count($lesson->getImagesArray()) : 0);
    $linkCount = $lesson->hasExternalLinks() ? count($lesson->getExternalLinksArray()) : 0;
    $resourceCount = $documentCount + $uploadedVideoCount + $audioCount + $imageCount + $linkCount + ($lesson->video_url ? 1 : 0);
    $assessmentCount = $hasAssessments ? \App\Models\Assessment\Assessment::where('lesson_id', $lesson->id)->count() : 0;
    $openPanels = [];

    if ($hasAssessments && !$allAssessmentsPassed) {
        $openPanels[] = 'assessments';
    }

    if ($lesson->video_url) {
        $openPanels[] = 'video-content';
    }
@endphp

<div
    wire:poll.30s="updateTimeSpent"
    x-data="{
        openPanels: @js($openPanels),
        documentModalOpen: false,
        documentModalUrl: '',
        documentModalTitle: '',
        imageModalOpen: false,
        imageModalUrl: '',
        liveSeconds: @js((int) $timeSpentSeconds),
        estimatedMinutes: @js((int) ($timeComparison['estimated'] ?? 0)),
        init() {
            this.liveSeconds = Math.max(this.liveSeconds, @js((int) $timeSpentSeconds));

            window.bootkodeLessonViewer = window.bootkodeLessonViewer || {};

            if (window.bootkodeLessonViewer.liveTimer) {
                clearInterval(window.bootkodeLessonViewer.liveTimer);
            }

            window.bootkodeLessonViewer.liveTimer = setInterval(() => {
                this.liveSeconds += 1;
            }, 1000);
        },
        toggle(panel) {
            this.openPanels = this.openPanels.includes(panel)
                ? this.openPanels.filter(item => item !== panel)
                : [...this.openPanels, panel];
        },
        isOpen(panel) {
            return this.openPanels.includes(panel);
        },
        openDocument(url, title) {
            this.documentModalUrl = url;
            this.documentModalTitle = title;
            this.documentModalOpen = true;
        },
        closeDocument() {
            this.documentModalOpen = false;
            this.documentModalUrl = '';
            this.documentModalTitle = '';
        },
        openImage(url) {
            this.imageModalUrl = url;
            this.imageModalOpen = true;
        },
        closeImage() {
            this.imageModalOpen = false;
            this.imageModalUrl = '';
        },
        formattedLiveTime() {
            const minutesTotal = Math.floor(this.liveSeconds / 60);
            const seconds = this.liveSeconds % 60;

            if (minutesTotal > 60) {
                const hours = Math.floor(minutesTotal / 60);
                const minutes = minutesTotal % 60;
                return `${hours}h ${minutes}m`;
            }

            return `${minutesTotal}m ${seconds}s`;
        },
        timeEstimateText() {
            if (!this.estimatedMinutes) {
                return '';
            }

            const actualMinutes = Math.floor(this.liveSeconds / 60);
            const percentage = this.estimatedMinutes > 0
                ? Math.round((actualMinutes / this.estimatedMinutes) * 100)
                : 0;

            return `${percentage}% of ${this.estimatedMinutes}m estimate`;
        },
        timeEstimateClass() {
            if (!this.estimatedMinutes) {
                return '';
            }

            return Math.floor(this.liveSeconds / 60) > this.estimatedMinutes
                ? 'text-amber-500'
                : 'text-emerald-500';
        }
    }"
    x-on:keydown.escape.window="closeDocument(); closeImage()"
    class="lesson-viewer">
    <!-- Transition Overlay -->
    @if($isTransitioning)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/55 backdrop-blur-sm">
        <div class="rounded-[1.75rem] border border-themed-primary bg-themed-secondary p-8 shadow-2xl">
            <div class="mx-auto mb-4 h-16 w-16 animate-spin rounded-full border-b-4"
                style="border-color: rgba(var(--accent-primary), 0.25); border-bottom-color: rgb(var(--accent-primary));"></div>
            <p class="font-medium text-themed-primary">Loading next lesson...</p>
        </div>
    </div>
    @endif

    <div class="overflow-hidden rounded-[2rem] border border-themed-primary bg-themed-secondary p-5 shadow-xl transition-colors duration-300 animate__animated animate__fadeInUp md:p-6">
        <div class="mb-6 overflow-hidden rounded-[1.75rem] border border-themed-secondary shadow-lg"
            style="background:
                radial-gradient(circle at top left, rgba(var(--accent-primary), 0.16), transparent 34%),
                radial-gradient(circle at top right, rgba(var(--accent-secondary), 0.12), transparent 32%),
                linear-gradient(160deg, rgba(var(--accent-primary), 0.06), rgba(var(--accent-secondary), 0.03));">
            <div class="p-5 md:p-6">
                <div class="flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between">
                    <div class="max-w-3xl">
                        <div class="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em]">
                            <span class="rounded-full border border-themed-secondary bg-themed-secondary px-3 py-1 text-themed-secondary">
                                {{ $lesson->section->title }}
                            </span>
                            <span class="rounded-full border border-themed-secondary bg-themed-secondary px-3 py-1 text-themed-secondary">
                                Lesson {{ $currentIndex + 1 }} of {{ $totalLessons }}
                            </span>
                            @if ($lesson->difficulty_level)
                                <span class="rounded-full border border-themed-secondary bg-themed-secondary px-3 py-1 text-themed-secondary capitalize">
                                    {{ $lesson->difficulty_level }}
                                </span>
                            @endif
                            @if ($hasAssessments)
                                <span
                                    class="rounded-full px-3 py-1 text-white {{ $allAssessmentsPassed ? 'bg-emerald-600' : 'bg-amber-500' }}">
                                    {{ $allAssessmentsPassed ? 'Assessments passed' : 'Assessment required' }}
                                </span>
                            @endif
                        </div>

                        <h2 class="mt-5 text-3xl font-semibold tracking-tight text-themed-primary md:text-4xl">
                            {{ $lesson->title }}
                        </h2>

                        @if ($lesson->description)
                            <p class="mt-3 max-w-2xl text-base leading-7 text-themed-secondary md:text-lg">
                                {{ $lesson->description }}
                            </p>
                        @endif

                        <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Time Spent</p>
                                <p class="mt-2 text-2xl font-semibold text-themed-primary" x-text="formattedLiveTime()">
                                    {{ $this->getFormattedTimeSpent() }}
                                </p>
                                @if ($timeComparison && $timeComparison['estimated'] > 0)
                                    <p class="mt-1 text-xs" :class="timeEstimateClass()" x-text="timeEstimateText()">
                                        {{ $timeComparison['percentage'] }}% of {{ $timeComparison['estimated'] }}m estimate
                                    </p>
                                @endif
                            </div>

                            <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Lesson Length</p>
                                <p class="mt-2 text-2xl font-semibold text-themed-primary">
                                    {{ $estimatedDuration ? $estimatedDuration . ' min' : 'Self-paced' }}
                                </p>
                                <p class="mt-1 text-xs text-themed-secondary">Estimated learning time</p>
                            </div>

                            <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Resources</p>
                                <p class="mt-2 text-2xl font-semibold text-themed-primary">{{ $resourceCount }}</p>
                                <p class="mt-1 text-xs text-themed-secondary">Files, media, and links</p>
                            </div>

                            <div class="rounded-2xl border border-themed-secondary bg-themed-tertiary p-4 shadow-sm">
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Lesson Gate</p>
                                <p class="mt-2 text-2xl font-semibold text-themed-primary">
                                    @if ($isCompleted)
                                        Done
                                    @elseif ($hasAssessments && !$allAssessmentsPassed)
                                        Review
                                    @else
                                        Ready
                                    @endif
                                </p>
                                <p class="mt-1 text-xs text-themed-secondary">
                                    {{ $hasAssessments ? ($allAssessmentsPassed ? 'You can move forward' : 'Pass assessments to continue') : 'No blocking assessment' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full xl:max-w-sm">
                        <div class="rounded-[1.75rem] border border-themed-secondary bg-themed-tertiary p-5 shadow-lg">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Lesson Status</p>

                            <div class="mt-4 flex items-start gap-4">
                                <span
                                    class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-3xl text-white shadow-lg {{ $isCompleted ? 'bg-emerald-600' : ($hasAssessments && !$allAssessmentsPassed ? 'bg-amber-500' : 'bg-blue-600') }}">
                                    <i class="fas {{ $isCompleted ? 'fa-check' : ($hasAssessments && !$allAssessmentsPassed ? 'fa-clipboard-check' : 'fa-play') }} text-lg"></i>
                                </span>

                                <div>
                                    <p class="text-lg font-semibold text-themed-primary">
                                        @if ($isCompleted)
                                            Lesson completed
                                        @elseif ($hasAssessments && !$allAssessmentsPassed)
                                            Complete the assessment
                                        @else
                                            Ready to mark complete
                                        @endif
                                    </p>
                                    <p class="mt-1 text-sm leading-6 text-themed-secondary">
                                        You are {{ $coursePosition }}% through the full course sequence.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-5 h-3 overflow-hidden rounded-full border border-themed-secondary bg-themed-secondary">
                                <div class="h-full rounded-full transition-all duration-500"
                                    style="width: {{ $coursePosition }}%; background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));">
                                </div>
                            </div>

                            <div class="mt-5 space-y-3">
                                @if ($isCompleted)
                                    <button wire:click="markAsIncomplete"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-amber-600">
                                        <i class="fas fa-undo"></i>
                                        Mark Incomplete
                                    </button>
                                @else
                                    <button wire:click="markAsCompleted"
                                        class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-emerald-700 {{ $hasAssessments && !$allAssessmentsPassed ? 'cursor-not-allowed opacity-50' : '' }}"
                                        @if ($hasAssessments && !$allAssessmentsPassed) disabled @endif>
                                        <i class="fas fa-check"></i>
                                        Mark Complete
                                    </button>
                                @endif

                                <div class="rounded-2xl border border-themed-secondary bg-themed-secondary px-4 py-3 text-sm text-themed-secondary">
                                    @if ($hasAssessments && !$allAssessmentsPassed)
                                        Finish the required assessment before moving to the next lesson.
                                    @else
                                        Use the course map or the next button below to keep progressing.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Preview (if not started) -->
        @if ($hasAssessments && !$allAssessmentsPassed)
            @php $assessmentPreview = $this->getAssessmentPreview(); @endphp
            @if ($assessmentPreview)
                <div class="mb-6 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-2 border-blue-300 dark:border-blue-600 rounded-xl p-6 shadow-lg">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-eye mr-2 text-blue-600 dark:text-blue-400"></i>
                        Assessment Preview
                    </h3>
                    
                    <div class="space-y-4">
                        @foreach ($assessmentPreview as $preview)
                            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-blue-200 dark:border-blue-700">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $preview['title'] }}</h4>
                                        <span class="text-xs px-2 py-1 mt-1 inline-block rounded-full {{ $preview['type'] === 'quiz' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' }}">
                                            {{ ucfirst($preview['type']) }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                        {{ $preview['total_points'] }} points
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-3">
                                    <div class="text-center bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $preview['question_count'] }}</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Questions</div>
                                    </div>
                                    @if($preview['estimated_duration'])
                                    <div class="text-center bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $preview['estimated_duration'] }}m</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Duration</div>
                                    </div>
                                    @endif
                                    <div class="text-center bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $preview['pass_percentage'] }}%</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">To Pass</div>
                                    </div>
                                    <div class="text-center bg-gray-50 dark:bg-gray-700 rounded-lg p-2">
                                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($preview['question_types']) }}</div>
                                        <div class="text-xs text-gray-600 dark:text-gray-400">Types</div>
                                    </div>
                                </div>

                                <!-- Question Types -->
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @foreach ($preview['question_types'] as $type)
                                        <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full">
                                            {{ ucwords(str_replace('_', ' ', $type)) }}
                                        </span>
                                    @endforeach
                                </div>

                                <!-- Difficulty Distribution -->
                                @if (!empty($preview['difficulty_distribution']))
                                    <div class="flex items-center gap-2 text-sm flex-wrap">
                                        <span class="text-gray-600 dark:text-gray-400">Difficulty:</span>
                                        @foreach ($preview['difficulty_distribution'] as $level => $count)
                                            <span class="px-2 py-1 rounded text-xs font-medium
                                                {{ $level === 'easy' ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300' : '' }}
                                                {{ $level === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300' : '' }}
                                                {{ $level === 'hard' ? 'bg-orange-100 dark:bg-orange-900 text-orange-700 dark:text-orange-300' : '' }}
                                                {{ $level === 'expert' ? 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300' : '' }}">
                                                {{ ucfirst($level) }}: {{ $count }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        <!-- Lesson Content -->
        <div class="lesson-content-wrapper">
            <!-- Main Text Content -->
            @if ($lesson->content)
                <div class="lesson-content-display mb-6 prose dark:prose-invert max-w-none text-gray-900 dark:text-gray-100">
                    {!! $lesson->content !!}
                </div>
            @endif

            <!-- ASSESSMENTS SECTION -->
            @if ($hasAssessments)
                <div class="mb-6">
                    <button type="button"
                        @click="toggle('assessments')"
                        class="group flex w-full items-center justify-between rounded-[1.5rem] border-2 border-blue-400/70 bg-gradient-to-r from-blue-50 to-indigo-50 p-4 text-left shadow-md transition hover:-translate-y-0.5 hover:shadow-lg dark:border-blue-600 dark:from-blue-900/20 dark:to-indigo-900/20">
                        <div class="flex items-center gap-4">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white shadow-md">
                                <i class="fas fa-clipboard-check text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Assessment Required
                                    @if (!$allAssessmentsPassed)
                                        <span class="ml-2 inline-flex items-center rounded-full bg-red-600 px-2 py-1 text-xs font-medium text-white animate-pulse">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Required
                                        </span>
                                    @else
                                        <span class="ml-2 inline-flex items-center rounded-full bg-green-600 px-2 py-1 text-xs font-medium text-white">
                                            <i class="fas fa-check mr-1"></i>
                                            Completed
                                        </span>
                                    @endif
                                </h3>
                                <p class="mt-1 text-sm text-blue-700 dark:text-blue-300">
                                    {{ $assessmentCount }} assessment{{ $assessmentCount > 1 ? 's' : '' }} attached to this lesson.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if (!$allAssessmentsPassed)
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-600 text-white shadow-md animate-pulse">
                                    <i class="fas fa-exclamation text-sm"></i>
                                </div>
                            @else
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-500 text-white shadow-md">
                                    <i class="fas fa-check text-sm"></i>
                                </div>
                            @endif

                            <i class="fas fa-chevron-down text-blue-600 transition-transform duration-300 dark:text-blue-400"
                                :class="{ 'rotate-180': isOpen('assessments') }"></i>
                        </div>
                    </button>

                    <div class="mt-3" x-show="isOpen('assessments')" x-transition.opacity.scale.origin.top>
                        <div class="rounded-[1.5rem] border border-blue-300 bg-gray-50 p-1 shadow-inner dark:border-blue-700 dark:bg-gray-900/50">
                            <livewire:student-management.course-view.student-assessment-taker :lesson="$lesson"
                                wire:key="assessment-{{ $lesson->id }}" wire:poll.10s="pollAssessmentStatus" />
                        </div>
                    </div>
                </div>
            @endif

            <!-- Documents -->
            @if ($lesson->hasDocuments() && count($lesson->getDocumentsArray()) > 0)
                <div class="mb-6">
                    <button type="button"
                        @click="toggle('documents')"
                        class="group flex w-full items-center justify-between rounded-[1.5rem] border border-gray-200 bg-gray-50 p-4 text-left transition hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:bg-gray-700">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-file-alt mr-2 text-indigo-600 dark:text-indigo-400"></i>
                            Course Materials
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-white"
                            :class="{ 'rotate-180': isOpen('documents') }"></i>
                    </button>

                    <div class="mt-3" x-show="isOpen('documents')" x-transition.opacity.scale.origin.top>
                        <div class="grid gap-3">
                            @foreach ($lesson->getDocumentsArray() as $document)
                                @php
                                    $documentUrl = asset('storage/' . $document['path']);
                                @endphp
                                <div class="flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 p-4 transition-shadow duration-200 hover:shadow-md dark:border-gray-600 dark:bg-gray-700/50">
                                    <div class="flex items-center">
                                        <div class="mr-3 flex h-10 w-10 items-center justify-center rounded bg-indigo-600 shadow-md">
                                            @switch(strtolower($document['type'] ?? 'file'))
                                                @case('pdf')
                                                    <i class="fas fa-file-pdf text-white"></i>
                                                @break
                                                @case('doc')
                                                @case('docx')
                                                    <i class="fas fa-file-word text-white"></i>
                                                @break
                                                @case('ppt')
                                                @case('pptx')
                                                    <i class="fas fa-file-powerpoint text-white"></i>
                                                @break
                                                @default
                                                    <i class="fas fa-file text-white"></i>
                                            @endswitch
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $document['name'] }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ number_format($document['size'] / 1024 / 1024, 1) }}MB
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button"
                                            x-on:click='openDocument(@js($documentUrl), @js($document["name"]))'
                                            class="rounded-lg bg-indigo-600 px-3 py-1 text-sm text-white shadow-md transition-colors hover:bg-indigo-700 hover:shadow-lg">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </button>
                                        <a href="{{ $documentUrl }}" target="_blank"
                                            class="rounded-lg bg-gray-500 px-3 py-1 text-sm text-white shadow-md transition-colors hover:bg-gray-600 hover:shadow-lg dark:bg-gray-600 dark:hover:bg-gray-700">
                                            <i class="fas fa-download mr-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Video Content -->
            @if ($lesson->video_url)
                <div class="mb-6">
                    <button type="button"
                        @click="toggle('video-content')"
                        class="group flex w-full items-center justify-between rounded-[1.5rem] border border-gray-200 bg-gray-50 p-4 text-left transition hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:bg-gray-700">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-video mr-2 text-red-600 dark:text-red-400"></i>
                            Video Lesson
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-white"
                            :class="{ 'rotate-180': isOpen('video-content') }"></i>
                    </button>

                    <div class="mt-3" x-show="isOpen('video-content')" x-transition.opacity.scale.origin.top>
                        <div class="overflow-hidden rounded-[1.5rem] bg-black shadow-lg">
                            @if (str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
                                @php
                                    $videoId = '';
                                    if (str_contains($lesson->video_url, 'youtube.com/watch?v=')) {
                                        parse_str(parse_url($lesson->video_url, PHP_URL_QUERY), $query);
                                        $videoId = $query['v'] ?? '';
                                    } elseif (str_contains($lesson->video_url, 'youtu.be/')) {
                                        $videoId = substr(parse_url($lesson->video_url, PHP_URL_PATH), 1);
                                    }
                                @endphp

                                @if ($videoId)
                                    <iframe class="aspect-video w-full"
                                        src="https://www.youtube.com/embed/{{ $videoId }}" title="Lesson Video"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                @endif
                            @else
                                <video controls class="aspect-video w-full">
                                    <source src="{{ $lesson->video_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Uploaded Videos -->
            @if ($lesson->hasVideo() && count($lesson->getVideosArray()) > 0)
                <div class="mb-6">
                    <button type="button"
                        @click="toggle('uploaded-videos')"
                        class="group flex w-full items-center justify-between rounded-[1.5rem] border border-gray-200 bg-gray-50 p-4 text-left transition hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:bg-gray-700">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-film mr-2 text-purple-600 dark:text-purple-400"></i>
                            Course Videos
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-white"
                            :class="{ 'rotate-180': isOpen('uploaded-videos') }"></i>
                    </button>

                    <div class="mt-3" x-show="isOpen('uploaded-videos')" x-transition.opacity.scale.origin.top>
                        <div class="grid gap-4">
                            @foreach ($lesson->getVideosArray() as $video)
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-600 dark:bg-gray-700/50">
                                    <div class="mb-2 flex items-center justify-between">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $video['name'] }}</span>
                                        <span class="rounded-full bg-gray-200 px-2 py-1 text-xs text-gray-600 dark:bg-gray-600 dark:text-gray-400">
                                            {{ number_format($video['size'] / 1024 / 1024, 1) }}MB
                                        </span>
                                    </div>
                                    <video controls class="w-full rounded shadow-md">
                                        <source src="{{ asset('storage/' . $video['path']) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Audio Content -->
            @if ($lesson->hasAudio() && count($lesson->getAudiosArray()) > 0)
                <div class="mb-6">
                    <button type="button"
                        @click="toggle('audio-content')"
                        class="group flex w-full items-center justify-between rounded-[1.5rem] border border-gray-200 bg-gray-50 p-4 text-left transition hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:bg-gray-700">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-headphones mr-2 text-green-600 dark:text-green-400"></i>
                            Audio Content
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-white"
                            :class="{ 'rotate-180': isOpen('audio-content') }"></i>
                    </button>

                    <div class="mt-3" x-show="isOpen('audio-content')" x-transition.opacity.scale.origin.top>
                        <div class="space-y-3">
                            @foreach ($lesson->getAudiosArray() as $index => $audio)
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-600 dark:bg-gray-700/50">
                                    <div class="mb-3 flex items-center justify-between">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $audio['name'] }}</span>
                                        <span class="rounded-full bg-gray-200 px-2 py-1 text-xs text-gray-600 dark:bg-gray-600 dark:text-gray-400">
                                            {{ number_format($audio['size'] / 1024 / 1024, 1) }}MB
                                        </span>
                                    </div>
                                    <div class="mini-player flex items-center gap-4 rounded-lg border border-gray-200 bg-white p-3 shadow-sm dark:border-gray-600 dark:bg-gray-800">
                                        <button type="button"
                                            class="play-pause-btn flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-green-500 to-emerald-600 text-white shadow-md transition-all duration-200 hover:from-green-600 hover:to-emerald-700 hover:shadow-lg"
                                            onclick="togglePlayPause({{ $index }})">
                                            <i class="fas fa-play" id="play-icon-{{ $index }}"></i>
                                            <i class="fas fa-pause hidden" id="pause-icon-{{ $index }}"></i>
                                        </button>
                                        <div class="flex-1">
                                            <div class="mb-1 h-2 w-full rounded-full bg-gray-200 shadow-inner dark:bg-gray-700">
                                                <div class="h-2 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 transition-all duration-200"
                                                    style="width: 0%" id="progress-bar-{{ $index }}"></div>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
                                                <span id="current-time-{{ $index }}">0:00</span>
                                                <span id="duration-{{ $index }}">0:00</span>
                                            </div>
                                        </div>
                                        <audio id="audio-{{ $index }}"
                                            onloadedmetadata="initAudioPlayer({{ $index }})"
                                            ontimeupdate="updateProgress({{ $index }})">
                                            <source src="{{ asset('storage/' . $audio['path']) }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Images -->
            @if ($lesson->hasImage())
                <div class="mb-6">
                    <button type="button"
                        @click="toggle('images-content')"
                        class="group flex w-full items-center justify-between rounded-[1.5rem] border border-gray-200 bg-gray-50 p-4 text-left transition hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:bg-gray-700">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-images mr-2 text-pink-600 dark:text-pink-400"></i>
                            Lesson Images
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-white"
                            :class="{ 'rotate-180': isOpen('images-content') }"></i>
                    </button>

                    <div class="mt-3" x-show="isOpen('images-content')" x-transition.opacity.scale.origin.top>
                        @if ($lesson->image_path)
                            @php
                                $mainImageUrl = asset('storage/' . $lesson->image_path);
                            @endphp
                            <img src="{{ $mainImageUrl }}" alt="Lesson Image"
                                class="mb-4 w-full cursor-pointer rounded-lg border border-gray-200 shadow-md transition-shadow duration-200 hover:shadow-lg dark:border-gray-700"
                                x-on:click='openImage(@js($mainImageUrl))'>
                        @endif

                        @if (count($lesson->getImagesArray()) > 0)
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                @foreach ($lesson->getImagesArray() as $image)
                                    @php
                                        $imageUrl = asset('storage/' . $image['path']);
                                    @endphp
                                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-gray-50 shadow-sm transition-shadow duration-200 hover:shadow-md dark:border-gray-600 dark:bg-gray-700/50">
                                        <img src="{{ $imageUrl }}" alt="Lesson Image"
                                            class="h-48 w-full cursor-pointer object-cover transition-opacity duration-200 hover:opacity-90"
                                            x-on:click='openImage(@js($imageUrl))'>
                                        <div class="p-3">
                                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $image['name'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- External Links -->
            @if ($lesson->hasExternalLinks() && count($lesson->getExternalLinksArray()) > 0)
                <div class="mb-6">
                    <button type="button"
                        @click="toggle('external-links')"
                        class="group flex w-full items-center justify-between rounded-[1.5rem] border border-gray-200 bg-gray-50 p-4 text-left transition hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700/50 dark:hover:bg-gray-700">
                        <h3 class="flex items-center text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-external-link-alt mr-2 text-orange-600 dark:text-orange-400"></i>
                            Additional Resources
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-white"
                            :class="{ 'rotate-180': isOpen('external-links') }"></i>
                    </button>

                    <div class="mt-3" x-show="isOpen('external-links')" x-transition.opacity.scale.origin.top>
                        <div class="space-y-2">
                            @foreach ($lesson->getExternalLinksArray() as $link)
                                <a href="{{ $link['url'] }}" target="_blank"
                                    class="block rounded-lg border border-gray-200 bg-gray-50 p-4 shadow-sm transition-all duration-200 hover:bg-gray-100 hover:shadow-md dark:border-gray-600 dark:bg-gray-700/50 dark:hover:bg-gray-700">
                                    <div class="flex items-center">
                                        <i class="fas fa-external-link-alt mr-3 text-orange-500 dark:text-orange-400"></i>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $link['title'] }}</p>
                                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">{{ $link['url'] }}</p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Document Modal -->
        <div x-cloak x-show="documentModalOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4"
            @click.self="closeDocument()">
            <div class="flex h-[85vh] w-full max-w-6xl flex-col overflow-hidden rounded-[1.75rem] border border-themed-primary bg-themed-secondary shadow-2xl">
                <div class="flex items-center justify-between border-b border-themed-secondary p-4">
                    <h3 class="text-lg font-semibold text-themed-primary" x-text="documentModalTitle"></h3>
                    <button type="button" @click="closeDocument()"
                        class="text-themed-secondary transition-colors hover:text-themed-primary">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="flex-1 p-4">
                    <iframe class="h-full w-full rounded bg-white" frameborder="0" x-bind:src="documentModalUrl"></iframe>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div x-cloak x-show="imageModalOpen" x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 p-4"
            @click.self="closeImage()">
            <div class="flex h-[85vh] w-full max-w-6xl flex-col overflow-hidden rounded-[1.75rem] border border-themed-primary bg-themed-secondary shadow-2xl">
                <div class="flex items-center justify-between border-b border-themed-secondary p-4">
                    <h3 class="text-lg font-semibold text-themed-primary">Image Preview</h3>
                    <button type="button" @click="closeImage()"
                        class="text-themed-secondary transition-colors hover:text-themed-primary">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="flex flex-1 items-center justify-center p-4">
                    <img class="max-h-full max-w-full object-contain" x-bind:src="imageModalUrl" alt="">
                </div>
            </div>
        </div>

        <!-- Lesson Navigation -->
        <div class="mt-8 grid gap-4 xl:grid-cols-[minmax(0,1fr)_auto]">
            <div class="rounded-[1.75rem] border border-themed-secondary bg-themed-tertiary p-5 shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-tertiary">Course Navigation</p>
                        <h3 class="mt-2 text-xl font-semibold text-themed-primary">
                            Lesson {{ $currentIndex + 1 }} of {{ $totalLessons }}
                        </h3>
                        <p class="mt-1 text-sm leading-6 text-themed-secondary">
                            @if ($nextLesson)
                                {{ $this->canProceedToNext() ? 'You can move forward as soon as you are ready.' : 'Pass the required assessment in this lesson to continue.' }}
                            @else
                                You are on the final lesson. Complete it when you are ready to finish the course.
                            @endif
                        </p>
                    </div>

                    <div class="rounded-2xl border border-themed-secondary bg-themed-secondary px-4 py-3 text-sm text-themed-secondary">
                        <span class="font-semibold text-themed-primary">{{ $coursePosition }}%</span> through course
                    </div>
                </div>

                <div class="mt-5 h-3 overflow-hidden rounded-full border border-themed-secondary bg-themed-secondary">
                    <div class="h-full rounded-full transition-all duration-500"
                        style="width: {{ $coursePosition }}%; background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));">
                    </div>
                </div>

                <div class="mt-4 grid gap-3 md:grid-cols-2">
                    <div class="rounded-2xl border border-themed-secondary bg-themed-secondary px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">Previous</p>
                        <p class="mt-2 text-sm font-semibold text-themed-primary">
                            {{ $previousLesson ? (is_object($previousLesson) ? $previousLesson->title : $previousLesson['title']) : 'Start of course' }}
                        </p>
                    </div>

                    <div class="rounded-2xl border border-themed-secondary bg-themed-secondary px-4 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-themed-tertiary">
                            {{ $nextLesson ? 'Next' : 'Finish' }}
                        </p>
                        <p class="mt-2 text-sm font-semibold text-themed-primary">
                            {{ $nextLesson ? (is_object($nextLesson) ? $nextLesson->title : $nextLesson['title']) : 'Complete this course when ready' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row xl:flex-col xl:justify-end">
                @if ($previousLesson)
                    <button wire:click="goToPreviousLesson"
                        class="inline-flex min-w-[14rem] items-center justify-center gap-2 rounded-2xl border border-themed-secondary bg-themed-tertiary px-5 py-3 text-sm font-semibold text-themed-primary shadow-sm transition hover:-translate-y-0.5 hover:bg-themed-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Previous Lesson
                    </button>
                @else
                    <div class="inline-flex min-w-[14rem] items-center justify-center gap-2 rounded-2xl border border-themed-secondary bg-themed-secondary px-5 py-3 text-sm font-medium text-themed-secondary">
                        <i class="fas fa-book-open"></i>
                        Start of course
                    </div>
                @endif

                @if ($nextLesson)
                    @if ($this->canProceedToNext() && $this->isNextLessonUnlocked())
                        <button wire:click="goToNextLesson"
                            class="inline-flex min-w-[14rem] items-center justify-center gap-2 rounded-2xl px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5"
                            style="background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));">
                            Next Lesson
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    @elseif (!$this->canProceedToNext())
                        <div class="inline-flex min-w-[14rem] items-center justify-center gap-2 rounded-2xl bg-red-600 px-5 py-3 text-sm font-semibold text-white opacity-85">
                            <i class="fas fa-clipboard-check"></i>
                            Complete assessments to continue
                        </div>
                    @else
                        <div class="inline-flex min-w-[14rem] items-center justify-center gap-2 rounded-2xl bg-gray-400 px-5 py-3 text-sm font-semibold text-white dark:bg-gray-600">
                            <i class="fas fa-lock"></i>
                            Complete section to continue
                        </div>
                    @endif
                @else
                    <button wire:click="completeCourse"
                        class="inline-flex min-w-[14rem] items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-emerald-700 {{ !$this->canProceedToNext() ? 'cursor-not-allowed opacity-50' : '' }}"
                        @if (!$this->canProceedToNext()) disabled @endif>
                        <i class="fas fa-trophy"></i>
                        Complete Course
                    </button>
                @endif
            </div>
        </div>
    </div>

    <script>
        window.bootkodeLessonViewer = window.bootkodeLessonViewer || {};

        function initAudioPlayer(index) {
            const audio = document.getElementById(`audio-${index}`);
            const durationElement = document.getElementById(`duration-${index}`);
            if (!audio || !durationElement || Number.isNaN(audio.duration)) {
                return;
            }
            const minutes = Math.floor(audio.duration / 60);
            const seconds = Math.floor(audio.duration % 60);
            durationElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        function togglePlayPause(index) {
            const audio = document.getElementById(`audio-${index}`);
            const playIcon = document.getElementById(`play-icon-${index}`);
            const pauseIcon = document.getElementById(`pause-icon-${index}`);
            if (!audio || !playIcon || !pauseIcon) {
                return;
            }

            if (audio.paused) {
                audio.play();
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
            } else {
                audio.pause();
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
            }
        }

        function updateProgress(index) {
            const audio = document.getElementById(`audio-${index}`);
            const progressBar = document.getElementById(`progress-bar-${index}`);
            const currentTimeElement = document.getElementById(`current-time-${index}`);
            if (!audio || !progressBar || !currentTimeElement || !audio.duration) {
                return;
            }

            const progress = (audio.currentTime / audio.duration) * 100;
            progressBar.style.width = `${progress}%`;

            const minutes = Math.floor(audio.currentTime / 60);
            const seconds = Math.floor(audio.currentTime % 60);
            currentTimeElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            if (audio.ended) {
                const playIcon = document.getElementById(`play-icon-${index}`);
                const pauseIcon = document.getElementById(`pause-icon-${index}`);
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
            }
        }

        if (window.bootkodeLessonViewer.assessmentPoller) {
            clearInterval(window.bootkodeLessonViewer.assessmentPoller);
            window.bootkodeLessonViewer.assessmentPoller = null;
        }

        if (@json($shouldPoll)) {
            let lastPollTime = 0;
            const POLL_INTERVAL = 10000;
            const lessonViewerComponent = @this;

            window.bootkodeLessonViewer.assessmentPoller = setInterval(() => {
                const now = Date.now();

                if (now - lastPollTime < POLL_INTERVAL) {
                    return;
                }

                lessonViewerComponent.call('pollAssessmentStatus');
                lastPollTime = now;
            }, POLL_INTERVAL);
        }

        document.addEventListener('livewire:init', () => {
            if (window.bootkodeLessonViewer.assessmentListenerBound) {
                return;
            }

            window.bootkodeLessonViewer.assessmentListenerBound = true;

            Livewire.on('assessment-completed', () => {
                if (window.bootkodeLessonViewer.assessmentPoller) {
                    clearInterval(window.bootkodeLessonViewer.assessmentPoller);
                    window.bootkodeLessonViewer.assessmentPoller = null;
                }
            });
        });
    </script>

    <style>
        .lesson-viewer [x-cloak] {
            display: none !important;
        }

        .mini-player {
            transition: all 0.3s ease;
        }

        .mini-player:hover {
            transform: translateY(-1px);
        }

        .play-pause-btn {
            transition: all 0.2s ease;
        }

        .play-pause-btn:hover {
            transform: scale(1.05);
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/trix.css') }}">
</div>
