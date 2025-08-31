{{-- resources/views/livewire/cbt/cbt-exam-selection.blade.php --}}
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    {{-- Header Section --}}
    <div class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-4 lg:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900">Computer Based Tests (CBT)</h1>
                    <p class="text-gray-600 mt-1">Select and take your examinations</p>
                </div>

                {{-- Quick Stats --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $exams->total() }}</div>
                        <div class="text-sm text-blue-600">Total Exams</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-green-600">
                            {{ collect($examStatuses)->where('status', 'completed')->where('passed', true)->count() }}
                        </div>
                        <div class="text-sm text-green-600">Passed</div>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-orange-600">
                            {{ collect($examStatuses)->where('status', 'in_progress')->count() }}
                        </div>
                        <div class="text-sm text-orange-600">In Progress</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ collect($examStatuses)->where('status', 'not_started')->count() }}
                        </div>
                        <div class="text-sm text-purple-600">Not Started</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 py-8">
        {{-- Filters and Search --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 lg:mb-0">Find Your Exams</h2>

                {{-- View Toggle --}}
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="showAvailableOnly"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Available only</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" wire:model.live="showMyResults"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-600">Show my results</span>
                    </label>
                    <button wire:click="clearFilters" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Clear All
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search exams, codes, descriptions..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- Course Filter --}}
                <div>
                    <select wire:model.live="selectedCourse"
                        class="block w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Type Filter --}}
                <div>
                    <select wire:model.live="selectedType"
                        class="block w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        @foreach($examTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Difficulty Filter --}}
                <div>
                    <select wire:model.live="selectedDifficulty"
                        class="block w-full border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Levels</option>
                        @foreach($difficultyLevels as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Sort Options --}}
            <div class="mt-4 flex flex-wrap items-center gap-4">
                <span class="text-sm font-medium text-gray-700">Sort by:</span>
                <div class="flex flex-wrap gap-2">
                    <button wire:click="sortBy('title')"
                        class="px-3 py-1 text-sm rounded-full border {{ $sortBy === 'title' ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-gray-100 text-gray-600 border-gray-300' }}">
                        Title
                        @if($sortBy === 'title')
                            <i class="fas fa-{{ $sortDirection === 'asc' ? 'sort-up' : 'sort-down' }} ml-1"></i>
                        @endif
                    </button>
                    <button wire:click="sortBy('created_at')"
                        class="px-3 py-1 text-sm rounded-full border {{ $sortBy === 'created_at' ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-gray-100 text-gray-600 border-gray-300' }}">
                        Date
                        @if($sortBy === 'created_at')
                            <i class="fas fa-{{ $sortDirection === 'asc' ? 'sort-up' : 'sort-down' }} ml-1"></i>
                        @endif
                    </button>
                    <button wire:click="sortBy('duration_minutes')"
                        class="px-3 py-1 text-sm rounded-full border {{ $sortBy === 'duration_minutes' ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-gray-100 text-gray-600 border-gray-300' }}">
                        Duration
                        @if($sortBy === 'duration_minutes')
                            <i class="fas fa-{{ $sortDirection === 'asc' ? 'sort-up' : 'sort-down' }} ml-1"></i>
                        @endif
                    </button>
                    <button wire:click="sortBy('difficulty_level')"
                        class="px-3 py-1 text-sm rounded-full border {{ $sortBy === 'difficulty_level' ? 'bg-blue-100 text-blue-700 border-blue-300' : 'bg-gray-100 text-gray-600 border-gray-300' }}">
                        Difficulty
                        @if($sortBy === 'difficulty_level')
                            <i class="fas fa-{{ $sortDirection === 'asc' ? 'sort-up' : 'sort-down' }} ml-1"></i>
                        @endif
                    </button>
                </div>
            </div>
        </div>

        {{-- Exams Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($exams as $exam)
                @php
                    $status = $examStatuses[$exam->id];
                    $statusConfig = match ($status['status']) {
                        'not_started' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'icon' => 'fa-play-circle', 'label' => 'Start Exam'],
                        'in_progress' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-700', 'icon' => 'fa-clock', 'label' => 'Resume Exam'],
                        'completed' => $status['passed'] ?
                        ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'icon' => 'fa-check-circle', 'label' => 'Passed'] :
                        ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'icon' => 'fa-times-circle', 'label' => 'Failed'],
                        default => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'icon' => 'fa-info-circle', 'label' => 'View Details']
                    };
                @endphp

                <div
                    class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-200 overflow-hidden border border-gray-100">
                    {{-- Exam Header --}}
                    <div class="relative p-6 pb-4">
                        {{-- Status Badge --}}
                        <div class="absolute top-4 right-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                <i class="fas {{ $statusConfig['icon'] }} mr-1"></i>
                                {{ $statusConfig['label'] }}
                            </span>
                        </div>

                        {{-- Exam Type Badge --}}
                        <div class="mb-3">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="{{ $exam->exam_type_icon }} mr-1"></i>
                                {{ ucfirst(str_replace('_', ' ', $exam->exam_type)) }}
                            </span>
                        </div>

                        {{-- Title and Course --}}
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 pr-12 line-clamp-2">
                            {{ $exam->title }}
                        </h3>

                        @if($exam->course)
                            <p class="text-sm text-gray-600 mb-2">
                                <i class="fas fa-book mr-1"></i>
                                {{ $exam->course->title }}
                            </p>
                        @endif

                        {{-- Exam Code --}}
                        <p class="text-sm font-mono text-gray-500 mb-3">{{ $exam->exam_code }}</p>

                        {{-- Description --}}
                        @if($exam->description)
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $exam->description }}</p>
                        @endif
                    </div>

                    {{-- Exam Details --}}
                    <div class="px-6 pb-4">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-clock mr-2 text-blue-500"></i>
                                <span>{{ $exam->formatted_duration }}</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-question-circle mr-2 text-green-500"></i>
                                <span>{{ $exam->total_questions }} questions</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-chart-line mr-2 text-orange-500"></i>
                                <span>{{ $exam->pass_percentage }}% to pass</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-layer-group mr-2 text-purple-500"></i>
                                <span class="capitalize">{{ $exam->difficulty_level }}</span>
                            </div>
                        </div>

                        {{-- User Progress --}}
                        @if($status['status'] !== 'not_started')
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-gray-700">Your Progress</span>
                                    <span class="text-gray-600">{{ $status['attempts'] }}/{{ $exam->max_attempts }}
                                        attempts</span>
                                </div>
                                @if($status['best_score'] !== null)
                                    <div class="mt-2">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600">Best Score</span>
                                            <span class="font-medium {{ $status['passed'] ? 'text-green-600' : 'text-red-600' }}">
                                                {{ number_format($status['best_score'], 1) }}%
                                            </span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $status['passed'] ? 'bg-green-500' : 'bg-red-500' }}"
                                                style="width: {{ $status['best_score'] }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        <div class="flex items-center justify-between">
                            <button wire:click="showDetails({{ $exam->id }})"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                <i class="fas fa-info-circle mr-1"></i>View Details
                            </button>

                            @if($status['status'] === 'not_started')
                                @php
                                    [$canTake, $reason] = $exam->canUserTake(Auth::user());
                                @endphp
                                @if($canTake)
                                    <button wire:click="confirmStart({{ $exam->id }})"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-play mr-1"></i>Start Exam
                                    </button>
                                @else
                                    <span class="text-sm text-red-600 font-medium">{{ $reason }}</span>
                                @endif
                            @elseif($status['status'] === 'in_progress')
                                <button wire:click="resumeExam('{{ $status['session_id'] }}')"
                                    class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    <i class="fas fa-play mr-1"></i>Resume
                                </button>
                            @elseif($status['status'] === 'completed')
                                @if($status['attempts'] < $exam->max_attempts && !$status['passed'])
                                    <button wire:click="confirmStart({{ $exam->id }})"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-redo mr-1"></i>Retry
                                    </button>
                                @else
                                    <span class="text-sm font-medium {{ $status['passed'] ? 'text-green-600' : 'text-gray-600' }}">
                                        {{ $status['passed'] ? 'Completed' : 'No retries left' }}
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No exams found</h3>
                        <p class="text-gray-600 mb-4">Try adjusting your search filters or check back later.</p>
                        <button wire:click="clearFilters"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                            Clear Filters
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $exams->links() }}
        </div>
    </div>

    {{-- Exam Details Modal --}}
    @if($showExamDetails && $selectedExam)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                {{-- Modal Header --}}
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $selectedExam->title }}</h3>
                        <button wire:click="closeDetails" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                {{-- Modal Content --}}
                <div class="p-6">
                    <div class="grid md:grid-cols-3 gap-8">
                        {{-- Main Info --}}
                        <div class="md:col-span-2 space-y-6">
                            {{-- Basic Info --}}
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Exam Information</h4>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Exam Code</span>
                                        <span class="font-mono font-medium">{{ $selectedExam->exam_code }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Course</span>
                                        <span class="font-medium">{{ $selectedExam->course->title ?? 'General' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Type</span>
                                        <span
                                            class="capitalize font-medium">{{ str_replace('_', ' ', $selectedExam->exam_type) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Difficulty</span>
                                        <span class="capitalize font-medium">{{ $selectedExam->difficulty_level }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Description --}}
                            @if($selectedExam->description)
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Description</h4>
                                    <div class="prose prose-gray max-w-none">
                                        <p>{{ $selectedExam->description }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Instructions --}}
                            @if($selectedExam->instructions)
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Instructions</h4>
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="prose prose-blue max-w-none">
                                            {!! nl2br(e($selectedExam->instructions)) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Exam Statistics --}}
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Exam Statistics</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-blue-600">{{ $selectedExam->results_count }}
                                        </div>
                                        <div class="text-sm text-blue-600">Total Attempts</div>
                                    </div>
                                    <div class="bg-green-50 rounded-lg p-4 text-center">
                                        <div class="text-2xl font-bold text-green-600">
                                            {{ $selectedExam->participants_count }}</div>
                                        <div class="text-sm text-green-600">Participants</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Exam Details Sidebar --}}
                        <div class="space-y-6">
                            {{-- Quick Facts --}}
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Quick Facts</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Duration</span>
                                        <span class="font-medium">{{ $selectedExam->formatted_duration }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Questions</span>
                                        <span class="font-medium">{{ $selectedExam->total_questions }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Pass Mark</span>
                                        <span class="font-medium">{{ $selectedExam->pass_percentage }}%</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Max Attempts</span>
                                        <span class="font-medium">{{ $selectedExam->max_attempts }}</span>
                                    </div>
                                    @if($selectedExam->max_participants)
                                        <div class="flex items-center justify-between">
                                            <span class="text-gray-600">Max Participants</span>
                                            <span class="font-medium">{{ $selectedExam->max_participants }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Features --}}
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Features</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <i
                                            class="fas {{ $selectedExam->allow_navigation ? 'fa-check text-green-500' : 'fa-times text-red-500' }} mr-2"></i>
                                        <span class="text-sm">Question Navigation</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i
                                            class="fas {{ $selectedExam->randomize_questions ? 'fa-check text-green-500' : 'fa-times text-red-500' }} mr-2"></i>
                                        <span class="text-sm">Randomized Questions</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i
                                            class="fas {{ $selectedExam->allow_review ? 'fa-check text-green-500' : 'fa-times text-red-500' }} mr-2"></i>
                                        <span class="text-sm">Review Before Submit</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i
                                            class="fas {{ $selectedExam->show_results_immediately ? 'fa-check text-green-500' : 'fa-times text-red-500' }} mr-2"></i>
                                        <span class="text-sm">Immediate Results</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Availability --}}
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3">Availability</h4>
                                <div class="space-y-2 text-sm">
                                    @if($selectedExam->start_date)
                                        <div>
                                            <span class="text-gray-600">Starts:</span>
                                            <span
                                                class="font-medium">{{ $selectedExam->start_date->format('M d, Y H:i') }}</span>
                                        </div>
                                    @endif
                                    @if($selectedExam->end_date)
                                        <div>
                                            <span class="text-gray-600">Ends:</span>
                                            <span class="font-medium">{{ $selectedExam->end_date->format('M d, Y H:i') }}</span>
                                        </div>
                                    @endif
                                    @if($selectedExam->available_days)
                                        <div>
                                            <span class="text-gray-600">Available Days:</span>
                                            <div class="mt-1">
                                                @php
                                                    $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                                @endphp
                                                @foreach($selectedExam->available_days as $day)
                                                    <span
                                                        class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs mr-1">
                                                        {{ $days[$day] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                    <div class="flex justify-end">
                        <button wire:click="closeDetails"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg mr-3">
                            Close
                        </button>
                        @php
                            $status = $examStatuses[$selectedExam->id];
                            [$canTake, $reason] = $selectedExam->canUserTake(Auth::user());
                        @endphp
                        @if($canTake && $status['status'] === 'not_started')
                            <button wire:click="confirmStart({{ $selectedExam->id }})"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                                Start Exam
                            </button>
                        @elseif($status['status'] === 'in_progress')
                            <button wire:click="resumeExam('{{ $status['session_id'] }}')"
                                class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-lg">
                                Resume Exam
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Start Confirmation Modal --}}
    @if($showStartConfirmation && $selectedExam)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-play text-blue-500 text-2xl"></i>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Start Exam?</h3>
                        <p class="text-gray-600 mb-4">{{ $selectedExam->title }}</p>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6 text-left">
                            <h4 class="font-medium text-yellow-800 mb-2">Important Reminders:</h4>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>• Ensure stable internet connection</li>
                                <li>• Duration: {{ $selectedExam->formatted_duration }}</li>
                                <li>• {{ $selectedExam->total_questions }} questions</li>
                                <li>• Pass mark: {{ $selectedExam->pass_percentage }}%</li>
                                @if(!$selectedExam->allow_navigation)
                                    <li>• No going back to previous questions</li>
                                @endif
                                <li>• Exam will auto-submit when time expires</li>
                            </ul>
                        </div>

                        <div class="flex space-x-3">
                            <button wire:click="cancelStart"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 rounded-lg">
                                Cancel
                            </button>
                            <button wire:click="startExam"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium">
                                Start Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center">
        <div class="bg-white rounded-lg p-6 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading...</p>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush