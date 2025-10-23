<div class="px-4 py-6 bg-themed-primary dark:bg-gray-900 min-h-screen transition-colors duration-300">
    <!-- Page Header -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-themed-primary mb-2">CBT Examinations</h2>
        <p class="text-themed-secondary">Select an assessment to begin your computer-based test</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('warning'))
        <div class="mb-6 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 text-yellow-800 dark:text-yellow-300 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <span>{{ session('warning') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Assessments Grid -->
    @if($availableAssessments->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($availableAssessments as $assessment)
                <div class="bg-themed-secondary border border-themed-primary rounded-lg shadow-sm hover:shadow-md transition-all duration-300 border-l-4 
                    {{ $assessment->user_result && $assessment->user_result['passed'] ? 'border-l-green-500' : 'border-l-accent-themed-primary' }}">
                    
                    <div class="p-6">
                        <!-- Assessment Header -->
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-themed-primary leading-tight">{{ $assessment->title }}</h3>
                            @if($assessment->user_result)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $assessment->user_result['passed'] ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                    {{ $assessment->user_result['passed'] ? 'PASSED' : 'FAILED' }}
                                </span>
                            @endif
                        </div>

                        <!-- Assessment Description -->
                        @if($assessment->description)
                            <p class="text-themed-secondary text-sm mb-4 line-clamp-2">{{ $assessment->description }}</p>
                        @endif

                        <!-- Assessment Stats -->
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <div class="text-center">
                                <div class="text-xs text-themed-tertiary mb-1">Questions</div>
                                <div class="font-semibold text-themed-primary">{{ $assessment->questions->count() }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xs text-themed-tertiary mb-1">Duration</div>
                                <div class="font-semibold text-themed-primary">{{ $assessment->formatted_duration }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-xs text-themed-tertiary mb-1">Pass %</div>
                                <div class="font-semibold text-themed-primary">{{ $assessment->pass_percentage }}%</div>
                            </div>
                        </div>

                        <!-- Previous Results (if any) -->
                        @if($assessment->user_result)
                            <div class="bg-themed-tertiary rounded-lg p-3 mb-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-themed-secondary">Previous Score:</span>
                                    <span class="font-semibold {{ $assessment->user_result['passed'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $assessment->user_result['percentage'] }}%
                                    </span>
                                </div>
                                @if($assessment->attempts_count > 1)
                                    <div class="flex justify-between items-center text-sm mt-1">
                                        <span class="text-themed-secondary">Attempts:</span>
                                        <span class="text-themed-primary">{{ $assessment->attempts_count }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            @if(!$assessment->user_result || $assessment->can_retake)
                                <button wire:click="startExam({{ $assessment->id }})" 
                                    wire:confirm="Are you ready to start this exam? Once started, the timer will begin immediately."
                                    class="w-full bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center font-medium">
                                    <i class="fas fa-play mr-2"></i>
                                    {{ $assessment->user_result ? 'Retake Exam' : 'Start Exam' }}
                                </button>
                            @endif

                            @if($assessment->user_result)
                                <button wire:click="viewResults({{ $assessment->id }})"
                                    class="w-full bg-themed-tertiary hover:bg-themed-secondary text-themed-primary border border-themed-secondary px-4 py-2 rounded-lg transition-colors flex items-center justify-center font-medium">
                                    <i class="fas fa-chart-line mr-2"></i>View Results
                                </button>
                            @endif
                        </div>

                        <!-- Course Info -->
                        @if($assessment->course)
                            <div class="mt-4 pt-4 border-t border-themed-secondary">
                                <div class="flex items-center text-sm text-themed-secondary">
                                    <i class="fas fa-book mr-2"></i>
                                    <span>{{ $assessment->course->title }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Statistics Summary -->
        @php
            $totalAssessments = $availableAssessments->count();
            $completedAssessments = $availableAssessments->filter(fn($a) => $a->user_result)->count();
            $passedAssessments = $availableAssessments->filter(fn($a) => $a->user_result && $a->user_result['passed'])->count();
        @endphp

        @if($completedAssessments > 0)
            <div class="mt-8 bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6">
                <h3 class="text-lg font-semibold text-themed-primary mb-4">Your Progress Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-accent-themed-primary">{{ $completedAssessments }}</div>
                        <div class="text-sm text-themed-secondary">Completed</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $passedAssessments }}</div>
                        <div class="text-sm text-themed-secondary">Passed</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $totalAssessments - $completedAssessments }}</div>
                        <div class="text-sm text-themed-secondary">Remaining</div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <!-- No Assessments Available -->
        <div class="text-center py-16">
            <div class="w-24 h-24 bg-themed-tertiary rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-clipboard-list text-themed-secondary text-4xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-themed-secondary mb-2">No CBT Assessments Available</h3>
            <p class="text-themed-tertiary mb-6">There are currently no computer-based tests available for you to take.</p>
            <a href="{{ route('student.dashboard') }}" 
                class="inline-flex items-center px-4 py-2 bg-accent-themed-primary text-white rounded-lg hover:bg-accent-themed-secondary transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    @endif

    <!-- Important Notes -->
    <div class="mt-8 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-3 flex items-center">
            <i class="fas fa-info-circle mr-2"></i>Important Notes
        </h3>
        <ul class="space-y-2 text-blue-800 dark:text-blue-300 text-sm">
            <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Ensure you have a stable internet connection before starting any exam</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Exams are conducted in fullscreen mode for security purposes</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Your progress is automatically saved as you answer questions</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Avoid switching tabs or minimizing the window during an exam</span>
            </li>
            <li class="flex items-start">
                <span class="mr-2">•</span>
                <span>Contact support if you experience any technical difficulties</span>
            </li>
        </ul>
    </div>
    
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Smooth transitions for dark mode */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
</div>