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
                                <p class="mt-2 text-2xl font-semibold text-themed-primary">{{ $this->getFormattedTimeSpent() }}</p>
                                @if ($timeComparison && $timeComparison['estimated'] > 0)
                                    <p class="mt-1 text-xs {{ $timeComparison['over_time'] ? 'text-amber-500' : 'text-emerald-500' }}">
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
                    <div class="flex justify-between items-center cursor-pointer group bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-2 border-blue-500 dark:border-blue-600 rounded-lg p-4 hover:from-blue-100 hover:to-indigo-100 dark:hover:from-blue-900/30 dark:hover:to-indigo-900/30 transition-all duration-200 shadow-md hover:shadow-lg"
                        onclick="toggleSection('assessments')">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-full flex items-center justify-center mr-3 shadow-md">
                                <i class="fas fa-clipboard-check text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Assessment Required
                                    @if (!$allAssessmentsPassed)
                                        <span class="inline-flex items-center ml-2 px-2 py-1 rounded-full text-xs font-medium bg-red-600 text-white animate-pulse">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Required
                                        </span>
                                    @else
                                        <span class="inline-flex items-center ml-2 px-2 py-1 rounded-full text-xs font-medium bg-green-600 text-white">
                                            <i class="fas fa-check mr-1"></i>
                                            Completed
                                        </span>
                                    @endif
                                </h3>
                                <p class="text-blue-700 dark:text-blue-300 text-sm mt-1">
                                    @php
                                        $assessmentCount = \App\Models\Assessment\Assessment::where('lesson_id', $lesson->id)->count();
                                    @endphp
                                    {{ $assessmentCount }} assessment{{ $assessmentCount > 1 ? 's' : '' }} must be completed to proceed
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            @if (!$allAssessmentsPassed)
                                <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center mr-3 animate-pulse shadow-md">
                                    <i class="fas fa-exclamation text-white text-sm"></i>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3 shadow-md">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                            @endif
                            <i class="fas fa-chevron-down text-blue-600 dark:text-blue-400 transform transition-transform"
                                id="assessments-chevron"></i>
                        </div>
                    </div>

                    <div class="mt-3 {{ $allAssessmentsPassed ? 'hidden' : '' }}" id="assessments-content">
                        <div class="bg-gray-50 dark:bg-gray-900/50 border border-blue-300 dark:border-blue-700 rounded-lg p-1 shadow-inner">
                            <livewire:student-management.course-view.student-assessment-taker :lesson="$lesson"
                                wire:key="assessment-{{ $lesson->id }}" wire:poll.10s="pollAssessmentStatus" />
                        </div>
                    </div>
                </div>
            @endif

            <!-- Documents -->
            @if ($lesson->hasDocuments() && count($lesson->getDocumentsArray()) > 0)
                <div class="mb-6">
                    <div class="flex justify-between items-center cursor-pointer group bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 border border-gray-200 dark:border-gray-600"
                        onclick="toggleSection('documents')">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-file-alt text-indigo-600 dark:text-indigo-400 mr-2"></i>
                            Course Materials
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transform transition-transform group-hover:text-gray-700 dark:group-hover:text-white"
                            id="documents-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="documents-content">
                        <div class="grid gap-3">
                            @foreach ($lesson->getDocumentsArray() as $index => $document)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 flex items-center justify-between border border-gray-200 dark:border-gray-600 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-indigo-600 rounded flex items-center justify-center mr-3 shadow-md">
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
                                            <p class="text-gray-900 dark:text-white font-medium">{{ $document['name'] }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ number_format($document['size'] / 1024 / 1024, 1) }}MB
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            onclick="openDocumentModal('{{ asset('storage/' . $document['path']) }}', '{{ $document['name'] }}')"
                                            class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm transition-colors shadow-md hover:shadow-lg">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </button>
                                        <a href="{{ asset('storage/' . $document['path']) }}" target="_blank"
                                            class="px-3 py-1 bg-gray-500 dark:bg-gray-600 hover:bg-gray-600 dark:hover:bg-gray-700 text-white rounded text-sm transition-colors shadow-md hover:shadow-lg">
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
                    <div class="flex justify-between items-center cursor-pointer group bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 border border-gray-200 dark:border-gray-600"
                        onclick="toggleSection('video-content')">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-video text-red-600 dark:text-red-400 mr-2"></i>
                            Video Lesson
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transform transition-transform group-hover:text-gray-700 dark:group-hover:text-white"
                            id="video-content-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="video-content-content">
                        <div class="bg-black rounded-lg overflow-hidden shadow-lg">
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
                                    <iframe class="w-full aspect-video"
                                        src="https://www.youtube.com/embed/{{ $videoId }}" title="Lesson Video"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen>
                                    </iframe>
                                @endif
                            @else
                                <video controls class="w-full aspect-video">
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
                    <div class="flex justify-between items-center cursor-pointer group bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 border border-gray-200 dark:border-gray-600"
                        onclick="toggleSection('uploaded-videos')">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-film text-purple-600 dark:text-purple-400 mr-2"></i>
                            Course Videos
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transform transition-transform group-hover:text-gray-700 dark:group-hover:text-white"
                            id="uploaded-videos-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="uploaded-videos-content">
                        <div class="grid gap-4">
                            @foreach ($lesson->getVideosArray() as $video)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-900 dark:text-white font-medium">{{ $video['name'] }}</span>
                                        <span class="text-xs text-gray-600 dark:text-gray-400 bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded-full">
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
                    <div class="flex justify-between items-center cursor-pointer group bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 border border-gray-200 dark:border-gray-600"
                        onclick="toggleSection('audio-content')">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-headphones text-green-600 dark:text-green-400 mr-2"></i>
                            Audio Content
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transform transition-transform group-hover:text-gray-700 dark:group-hover:text-white"
                            id="audio-content-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="audio-content-content">
                        <div class="space-y-3">
                            @foreach ($lesson->getAudiosArray() as $index => $audio)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-shadow duration-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-gray-900 dark:text-white font-medium">{{ $audio['name'] }}</span>
                                        <span class="text-xs text-gray-600 dark:text-gray-400 bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded-full">
                                            {{ number_format($audio['size'] / 1024 / 1024, 1) }}MB
                                        </span>
                                    </div>
                                    <div class="mini-player flex items-center gap-4 p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm">
                                        <button
                                            class="play-pause-btn w-10 h-10 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg"
                                            onclick="togglePlayPause({{ $index }})">
                                            <i class="fas fa-play" id="play-icon-{{ $index }}"></i>
                                            <i class="fas fa-pause hidden" id="pause-icon-{{ $index }}"></i>
                                        </button>
                                        <div class="flex-1">
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-1 shadow-inner">
                                                <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-2 rounded-full transition-all duration-200" style="width: 0%"
                                                    id="progress-bar-{{ $index }}"></div>
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
                    <div class="flex justify-between items-center cursor-pointer group bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 border border-gray-200 dark:border-gray-600"
                        onclick="toggleSection('images-content')">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-images text-pink-600 dark:text-pink-400 mr-2"></i>
                            Lesson Images
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transform transition-transform group-hover:text-gray-700 dark:group-hover:text-white"
                            id="images-content-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="images-content-content">
                        @if ($lesson->image_path)
                            <img src="{{ asset('storage/' . $lesson->image_path) }}" alt="Lesson Image"
                                class="w-full rounded-lg mb-4 border border-gray-200 dark:border-gray-700 shadow-md hover:shadow-lg transition-shadow duration-200 cursor-pointer"
                                onclick="openImageModal('{{ asset('storage/' . $lesson->image_path) }}')">
                        @endif

                        @if (count($lesson->getImagesArray()) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($lesson->getImagesArray() as $image)
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg overflow-hidden border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md transition-shadow duration-200">
                                        <img src="{{ asset('storage/' . $image['path']) }}" alt="Lesson Image"
                                            class="w-full h-48 object-cover cursor-pointer hover:opacity-90 transition-opacity duration-200"
                                            onclick="openImageModal('{{ asset('storage/' . $image['path']) }}')">
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
                    <div class="flex justify-between items-center cursor-pointer group bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200 border border-gray-200 dark:border-gray-600"
                        onclick="toggleSection('external-links')">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <i class="fas fa-external-link-alt text-orange-600 dark:text-orange-400 mr-2"></i>
                            Additional Resources
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 dark:text-gray-400 transform transition-transform group-hover:text-gray-700 dark:group-hover:text-white"
                            id="external-links-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="external-links-content">
                        <div class="space-y-2">
                            @foreach ($lesson->getExternalLinksArray() as $link)
                                <a href="{{ $link['url'] }}" target="_blank"
                                    class="block bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg p-4 transition-all duration-200 border border-gray-200 dark:border-gray-600 shadow-sm hover:shadow-md">
                                    <div class="flex items-center">
                                        <i class="fas fa-external-link-alt text-orange-500 dark:text-orange-400 mr-3"></i>
                                        <div>
                                            <p class="text-gray-900 dark:text-white font-medium">{{ $link['title'] }}</p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $link['url'] }}</p>
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
        <div id="document-modal"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg w-11/12 h-5/6 max-w-6xl flex flex-col shadow-2xl">
                <div class="flex justify-between items-center p-4 border-b border-gray-300 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="document-modal-title"></h3>
                    <button onclick="closeDocumentModal()" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="flex-1 p-4">
                    <iframe id="document-iframe" class="w-full h-full bg-white rounded" frameborder="0"></iframe>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div id="image-modal"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-lg w-11/12 h-5/6 max-w-6xl flex flex-col shadow-2xl">
                <div class="flex justify-between items-center p-4 border-b border-gray-300 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Image Preview</h3>
                    <button onclick="closeImageModal()" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="flex-1 p-4 flex items-center justify-center">
                    <img id="modal-image" class="max-w-full max-h-full object-contain" src="" alt="">
                </div>
            </div>
        </div>

        <!-- Lesson Navigation -->
        <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-300 dark:border-gray-700">
            @if ($this->getPreviousLesson())
                @php $prevLesson = $this->getPreviousLesson(); @endphp
                <button wire:click="goToPreviousLesson"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg flex items-center transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span class="hidden sm:inline">Previous:</span>
                    <span class="ml-1 truncate max-w-32">
                        {{ is_object($prevLesson) ? $prevLesson->title : $prevLesson['title'] }}
                    </span>
                </button>
            @else
                <div></div>
            @endif

            @if ($this->getNextLesson())
                @php $nextLesson = $this->getNextLesson(); @endphp
                @if ($this->canProceedToNext() && $this->isNextLessonUnlocked())
                    <button wire:click="goToNextLesson"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg flex items-center transition-all duration-200 shadow-md hover:shadow-lg">
                        <span class="hidden sm:inline">Next:</span>
                        <span class="mr-1 truncate max-w-32">
                            {{ is_object($nextLesson) ? $nextLesson->title : $nextLesson['title'] }}
                        </span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                @elseif (!$this->canProceedToNext())
                    <div class="px-4 py-2 bg-red-600 text-white rounded-lg flex items-center cursor-not-allowed opacity-75">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        <span class="text-sm">Complete assessments to continue</span>
                    </div>
                @else
                    <div class="px-4 py-2 bg-gray-400 dark:bg-gray-600 text-gray-700 dark:text-gray-400 rounded-lg flex items-center cursor-not-allowed">
                        <i class="fas fa-lock mr-2"></i>
                        <span class="text-sm">Complete section to continue</span>
                    </div>
                @endif
            @else
                <button wire:click="completeCourse"
                    class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg flex items-center transition-all duration-200 shadow-md hover:shadow-lg
                    {{ !$this->canProceedToNext() ? 'opacity-50 cursor-not-allowed' : '' }}"
                    @if (!$this->canProceedToNext()) disabled @endif>
                    <i class="fas fa-trophy mr-2"></i>
                    Complete Course
                </button>
            @endif
        </div>

        <!-- Progress Indicator -->
        <div class="mt-4">
            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                <span>Lesson {{ $currentIndex + 1 }} of {{ count($allLessons) }}</span>
                <span>{{ round((($currentIndex + 1) / count($allLessons)) * 100) }}% through course</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 shadow-inner">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-300"
                    style="width: {{ round((($currentIndex + 1) / count($allLessons)) * 100) }}%"></div>
            </div>
        </div>
    </div>

    <script>
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Only handle shortcuts when not typing in input/textarea
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                return;
            }

            switch(e.key) {
                case 'ArrowLeft':
                    e.preventDefault();
                    @this.call('goToPreviousLesson');
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    @this.call('goToNextLesson');
                    break;
                case ' ':
                    e.preventDefault();
                    @this.call('markAsCompleted');
                    break;
            }
        });

        // Section toggle function
        function toggleSection(sectionId) {
            const content = document.getElementById(`${sectionId}-content`);
            const chevron = document.getElementById(`${sectionId}-chevron`);
            content.classList.toggle('hidden');
            chevron.classList.toggle('fa-chevron-down');
            chevron.classList.toggle('fa-chevron-up');
        }

        // Document modal functions
        function openDocumentModal(url, title) {
            document.getElementById('document-modal-title').textContent = title;
            document.getElementById('document-iframe').src = url;
            document.getElementById('document-modal').classList.remove('hidden');
        }

        function closeDocumentModal() {
            document.getElementById('document-modal').classList.add('hidden');
            document.getElementById('document-iframe').src = '';
        }

        // Image modal functions
        function openImageModal(url) {
            document.getElementById('modal-image').src = url;
            document.getElementById('image-modal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('image-modal').classList.add('hidden');
            document.getElementById('modal-image').src = '';
        }

        // Audio player functions
        function initAudioPlayer(index) {
            const audio = document.getElementById(`audio-${index}`);
            const durationElement = document.getElementById(`duration-${index}`);
            const minutes = Math.floor(audio.duration / 60);
            const seconds = Math.floor(audio.duration % 60);
            durationElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        function togglePlayPause(index) {
            const audio = document.getElementById(`audio-${index}`);
            const playIcon = document.getElementById(`play-icon-${index}`);
            const pauseIcon = document.getElementById(`pause-icon-${index}`);

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

        // Modal close on click outside
        document.getElementById('document-modal').addEventListener('click', function(e) {
            if (e.target === this) closeDocumentModal();
        });

        document.getElementById('image-modal').addEventListener('click', function(e) {
            if (e.target === this) closeImageModal();
        });

        // Assessment polling
        let assessmentPollingActive = @json($shouldPoll);
        let lastPollTime = 0;
        const POLL_INTERVAL = 10000;

        if (assessmentPollingActive) {
            setInterval(() => {
                const now = Date.now();
                if (now - lastPollTime >= POLL_INTERVAL) {
                    @this.call('pollAssessmentStatus');
                    lastPollTime = now;
                }
            }, POLL_INTERVAL);
        }

        document.addEventListener('livewire:init', () => {
            @this.on('assessment-completed', () => {
                assessmentPollingActive = false;
            });
        });
    </script>

    <style>
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

        #document-modal,
        #image-modal {
            transition: opacity 0.3s ease;
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

        kbd {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1;
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
