<div>


<div>
    @if ($assessmentState === 'list')
        <!-- Assessment List -->
        @if ($assessments->count() > 0)
            <div class="mb-6">
                <div class="flex justify-between items-center cursor-pointer group"
                    onclick="toggleSection('assessments')">
                    <h3 class="text-lg font-semibold text-white">Lesson Assessments</h3>
                    <i class="fas fa-chevron-down text-gray-400 transform transition-transform group-hover:text-white"
                        id="assessments-chevron"></i>
                </div>
                <div class="mt-3 hidden" id="assessments-content">
                    <div class="space-y-4">
                        @foreach ($assessments as $assessment)
                            @php
                                $hasAttempted = \App\Models\StudentAnswer::where('user_id', Auth::id())
                                    ->where('assessment_id', $assessment->id)
                                    ->exists();
                                $latestAttempt = \App\Models\StudentAnswer::where('user_id', Auth::id())
                                    ->where('assessment_id', $assessment->id)
                                    ->orderBy('attempt_number', 'desc')
                                    ->first();
                                $passed = false;
                                if ($latestAttempt) {
                                    $totalPoints = \App\Models\StudentAnswer::where('user_id', Auth::id())
                                        ->where('assessment_id', $assessment->id)
                                        ->where('attempt_number', $latestAttempt->attempt_number)
                                        ->sum('points_earned');
                                    $maxPoints = $assessment->questions->sum('points');
                                    $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;
                                    $passed = $percentage >= $assessment->pass_percentage;
                                }
                            @endphp

                            <div
                                class="bg-gray-700 rounded-lg p-5 border-l-4 
                                        {{ $passed ? 'border-green-500' : ($hasAttempted ? 'border-yellow-500' : 'border-indigo-500') }}">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h4 class="text-lg font-semibold text-white">{{ $assessment->title }}</h4>
                                            <span
                                                class="px-2 py-1 rounded-full text-xs font-medium
                                                        {{ $assessment->type === 'quiz' ? 'bg-purple-600 text-white' : 'bg-blue-600 text-white' }}">
                                                {{ ucfirst($assessment->type) }}
                                            </span>
                                            @if ($assessment->is_mandatory)
                                                <span
                                                    class="px-2 py-1 bg-red-600 text-white rounded-full text-xs font-medium">
                                                    Mandatory
                                                </span>
                                            @endif
                                        </div>

                                        @if ($assessment->description)
                                            <p class="text-gray-300 mb-3">{{ $assessment->description }}</p>
                                        @endif

                                        <div class="flex flex-wrap gap-4 text-sm text-gray-400">
                                            <span class="flex items-center">
                                                <i class="fas fa-questions mr-1"></i>
                                                {{ $assessment->questions->count() }} questions
                                            </span>
                                            @if ($assessment->estimated_duration_minutes)
                                                <span class="flex items-center">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $assessment->estimated_duration_minutes }} minutes
                                                </span>
                                            @endif
                                            <span class="flex items-center">
                                                <i class="fas fa-percentage mr-1"></i>
                                                {{ $assessment->pass_percentage }}% to pass
                                            </span>
                                            @if ($assessment->questions->sum('points') > 0)
                                                <span class="flex items-center">
                                                    <i class="fas fa-star mr-1"></i>
                                                    {{ $assessment->questions->sum('points') }} points
                                                </span>
                                            @endif
                                        </div>

                                        @if ($hasAttempted)
                                            <div class="mt-3 p-3 bg-gray-600 rounded-lg">
                                                <div class="flex items-center justify-between">
                                                    <div>
                                                        @if ($passed)
                                                            <span class="text-green-400 font-medium">
                                                                <i class="fas fa-check-circle mr-1"></i>
                                                                Passed
                                                                ({{ round(($totalPoints / $maxPoints) * 100, 1) }}%)
                                                            </span>
                                                        @else
                                                            <span class="text-yellow-400 font-medium">
                                                                <i class="fas fa-exclamation-circle mr-1"></i>
                                                                Score:
                                                                {{ round(($totalPoints / $maxPoints) * 100, 1) }}%
                                                                (Needs {{ $assessment->pass_percentage }}%)
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-400">
                                                        Last attempt:
                                                        {{ $latestAttempt->created_at->format('M j, Y') }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-2">
                                        @if (!$hasAttempted)
                                            <button wire:click="startAssessment({{ $assessment->id }})"
                                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm transition-colors flex items-center">
                                                <i class="fas fa-play mr-2"></i>
                                                Start Assessment
                                            </button>
                                        @else
                                            <button wire:click="startAssessment({{ $assessment->id }})"
                                                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition-colors flex items-center">
                                                <i class="fas fa-eye mr-2"></i>
                                                View Results
                                            </button>
                                            @if (!$passed)
                                                <button wire:click="startAssessment({{ $assessment->id }})"
                                                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors flex items-center">
                                                    <i class="fas fa-redo mr-2"></i>
                                                    Retake
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @elseif($assessmentState === 'taking')
        <!-- Assessment Taking Interface -->
        <div class="bg-gray-800 rounded-xl p-6">
            <!-- Assessment Header -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-white">{{ $currentAssessment->title }}</h2>
                    <p class="text-gray-400 mt-1">{{ $currentAssessment->description }}</p>
                </div>

                <div class="flex items-center gap-4">
                    @if ($timeRemaining)
                    <div x-data="timerApp()" x-init="init()">
                        <!-- Timer Display -->
                        <div class="timer-container">
                            <div class="timer-text">
                                <i class="fas fa-clock mr-1"></i>
                                Time Remaining: 
                                <span x-text="formatTime(timeRemaining)" :class="{
                                    'timer-digits': true,
                                    'timer-warning': timeRemaining <= 300000 && timeRemaining > 60000,
                                    'timer-danger': timeRemaining <= 60000
                                }"></span>
                            </div>
                        </div>
                    @endif

                    <button wire:click="backToAssessmentList"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-400">
                        Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
                    </span>
                    <span class="text-sm text-gray-400">
                        {{ $this->getAnsweredQuestionsCount() }}/{{ count($questions) }} answered
                    </span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-2">
                    <div class="bg-indigo-500 h-2 rounded-full transition-all duration-300"
                        style="width: {{ $this->getQuestionProgress() }}%"></div>
                </div>
            </div>

            @if ($this->getCurrentQuestion())
                @php $currentQuestion = $this->getCurrentQuestion(); @endphp

                <!-- Question Content -->
                <div class="bg-gray-700 rounded-lg p-6 mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-white">
                            Question {{ $currentQuestionIndex + 1 }}
                            @if ($currentQuestion->is_required)
                                <span class="text-red-400 text-sm ml-1">*</span>
                            @endif
                        </h3>
                        <div class="text-sm text-gray-400">
                            {{ $currentQuestion->points }} {{ $currentQuestion->points == 1 ? 'point' : 'points' }}
                        </div>
                    </div>

                    <div class="text-white mb-6 leading-relaxed">
                        {!! nl2br(e($currentQuestion->question_text)) !!}
                    </div>

                    <!-- Answer Options -->
@if ($currentQuestion->question_type === 'multiple_choice')
<div class="space-y-3">
    @foreach (json_decode($currentQuestion->options, true) ?? [] as $index => $option)
        <label
            class="flex items-center p-3 bg-gray-600 rounded-lg hover:bg-gray-500 cursor-pointer transition-colors">
            @if ($currentQuestion->hasMultipleCorrectAnswers())
                <input type="checkbox" wire:model.live="answers.{{ $currentQuestion->id }}"
                       value="{{ $index }}" class="mr-3 rounded">
            @else
                <input type="radio" wire:model.live="answers.{{ $currentQuestion->id }}"
                       value="{{ $index }}" class="mr-3">
            @endif
            <span class="text-white">{{ chr(65 + $index) }}. {{ $option }}</span>
        </label>
    @endforeach
</div>
@elseif($currentQuestion->question_type === 'true_false')
<div class="space-y-3">
    @foreach (json_decode($currentQuestion->options, true) ?? [] as $index => $option)
        <label
            class="flex items-center p-3 bg-gray-600 rounded-lg hover:bg-gray-500 cursor-pointer transition-colors">
            <input type="radio" wire:model.live="answers.{{ $currentQuestion->id }}"
                   value="{{ $index }}" class="mr-3">
            <span class="text-white">{{ $option }}</span>
        </label>
    @endforeach
</div>
@elseif(in_array($currentQuestion->question_type, ['short_answer', 'fill_blank']))
<div>
    <input type="text" wire:model.live="answers.{{ $currentQuestion->id }}"
           placeholder="Enter your answer..."
           class="w-full px-4 py-3 bg-gray-600 border border-gray-500 rounded-lg text-white placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500">
</div>
@elseif($currentQuestion->question_type === 'essay')
<div>
    <textarea wire:model.live="answers.{{ $currentQuestion->id }}" rows="6"
              placeholder="Write your essay answer here..."
              class="w-full px-4 py-3 bg-gray-600 border border-gray-500 rounded-lg text-white placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 resize-y"></textarea>
</div>
@endif
                </div>
            @endif

            <!-- Question Navigation -->
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                <div class="flex gap-2">
                    <button wire:click="previousQuestion" @if ($currentQuestionIndex === 0) disabled @endif
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition-colors">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Previous
                    </button>

                    <button wire:click="nextQuestion" @if ($currentQuestionIndex === count($questions) - 1) disabled @endif
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition-colors">
                        Next
                        <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>

                <!-- Question Overview -->
                <div class="flex flex-wrap gap-2">
                    @foreach ($questions as $index => $question)
                        <button wire:click="goToQuestion({{ $index }})"
                            class="w-8 h-8 rounded flex items-center justify-center text-sm font-medium transition-colors
                                       {{ $index === $currentQuestionIndex
                                           ? 'bg-indigo-600 text-white'
                                           : ($this->isQuestionAnswered($question->id)
                                               ? 'bg-green-600 text-white'
                                               : 'bg-gray-600 text-gray-300 hover:bg-gray-500') }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>

                <!-- Submit Button -->
                <button wire:click="submitAssessment" @if (!$this->canSubmitAssessment()) disabled @endif
                    class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Submit Assessment
                </button>
            </div>
        </div>
    @elseif($assessmentState === 'results')
        <!-- Results Display -->
        <div class="bg-gray-800 rounded-xl p-6">
            <div class="text-center mb-6">
                <div class="mb-4">
                    @if ($results['passed'])
                        <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check text-3xl text-white"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-green-400 mb-2">Congratulations!</h2>
                        <p class="text-gray-300">You passed the assessment</p>
                    @else
                        <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-times text-3xl text-white"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-red-400 mb-2">Assessment Incomplete</h2>
                        <p class="text-gray-300">You need {{ $currentAssessment->pass_percentage }}% to pass</p>
                    @endif
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-700 rounded-lg p-4">
                        <div class="text-2xl font-bold text-white">{{ $results['percentage'] }}%</div>
                        <div class="text-sm text-gray-400">Score</div>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-4">
                        <div class="text-2xl font-bold text-white">
                            {{ $results['correct_answers'] }}/{{ $results['total_questions'] }}</div>
                        <div class="text-sm text-gray-400">Correct</div>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-4">
                        <div class="text-2xl font-bold text-white">{{ $results['total_points'] }}</div>
                        <div class="text-sm text-gray-400">Points Earned</div>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-4">
                        <div class="text-2xl font-bold text-white">{{ $results['max_points'] }}</div>
                        <div class="text-sm text-gray-400">Total Points</div>
                    </div>
                </div>
            </div>

            <!-- Detailed Results -->
            <div class="space-y-4 mb-6">
                <h3 class="text-lg font-semibold text-white">Question Review</h3>
                @foreach ($currentAssessment->questions as $index => $question)
                    @php
                        $studentAnswer = $results['answers'][$question->id] ?? null;
                        $isCorrect = $studentAnswer && $studentAnswer->is_correct;
                    @endphp

                    <div
                        class="bg-gray-700 rounded-lg p-4 border-l-4 {{ $isCorrect ? 'border-green-500' : 'border-red-500' }}">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-white">Question {{ $index + 1 }}</h4>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-400">
                                    {{ $studentAnswer ? $studentAnswer->points_earned : 0 }}/{{ $question->points }}
                                    points
                                </span>
                                @if ($isCorrect)
                                    <i class="fas fa-check text-green-400"></i>
                                @else
                                    <i class="fas fa-times text-red-400"></i>
                                @endif
                            </div>
                        </div>

                        <p class="text-gray-300 mb-3">{!! nl2br(e($question->question_text)) !!}</p>

                        @if ($studentAnswer)
                            <div class="text-sm">
                                <span class="text-gray-400">Your answer:</span>
                                <span class="text-white ml-2">{{ $studentAnswer->formatted_answer }}</span>
                            </div>

                            @if ($question->explanation && !$isCorrect)
                                <div class="mt-2 p-3 bg-blue-900/30 border border-blue-700 rounded">
                                    <span class="text-blue-300 text-sm font-medium">Explanation:</span>
                                    <p class="text-blue-200 text-sm mt-1">{{ $question->explanation }}</p>
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button wire:click="backToAssessmentList"
                    class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Assessments
                </button>

                @if (!$results['passed'])
                    <button wire:click="retakeAssessment"
                        class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-redo mr-2"></i>
                        Retake Assessment
                    </button>
                @endif

                @if ($results['passed'])
                    <button onclick="window.print()"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-print mr-2"></i>
                        Print Results
                    </button>
                @endif
            </div>
        </div>
    @endif
    
    <script>
    // Timer functionality
    let timeRemaining = @json($timeRemaining);
    let timerInterval;

    function startTimer() {
        if (timeRemaining && timeRemaining > 0) {
            timerInterval = setInterval(function() {
                timeRemaining--;
                updateTimerDisplay();

                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    @this.dispatch('timer-ended');
                }
            }, 1000);
        }
    }

    function updateTimerDisplay() {
        const timerElement = document.getElementById('timer');
        if (timerElement) {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            timerElement.textContent = minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');

            // Change color when time is running out
            if (timeRemaining <= 300) { // 5 minutes
                timerElement.parentElement.className = timerElement.parentElement.className.replace('text-red-300',
                    'text-red-400 animate-pulse');
            }
        }
    }

    // Start timer when assessment begins
    document.addEventListener('livewire:init', function() {
        @this.on('assessment-started', function() {
            setTimeout(startTimer, 100);
        });
    });

    // Toggle section visibility (for assessment list)
    function toggleSection(sectionId) {
        const content = document.getElementById(`${sectionId}-content`);
        const chevron = document.getElementById(`${sectionId}-chevron`);

        if (content && chevron) {
            content.classList.toggle('hidden');
            chevron.classList.toggle('fa-chevron-down');
            chevron.classList.toggle('fa-chevron-up');
        }
    }

    // Auto-save answers periodically
    let autoSaveInterval;

    function startAutoSave() {
        autoSaveInterval = setInterval(function() {
            // Trigger a silent save of current answers
            @this.call('$refresh');
        }, 30000); // Save every 30 seconds
    }

    function stopAutoSave() {
        if (autoSaveInterval) {
            clearInterval(autoSaveInterval);
        }
    }

    // Confirmation before leaving page during assessment
    window.addEventListener('beforeunload', function(e) {
        if (@json($assessmentState) === 'taking' && !@json($isSubmitted)) {
            e.preventDefault();
            e.returnValue = '';
            return 'You have an assessment in progress. Are you sure you want to leave?';
        }
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (@json($assessmentState) === 'taking') {
            if (e.key === 'ArrowLeft' && e.ctrlKey) {
                e.preventDefault();
                @this.call('previousQuestion');
            } else if (e.key === 'ArrowRight' && e.ctrlKey) {
                e.preventDefault();
                @this.call('nextQuestion');
            } else if (e.key === 'Enter' && e.ctrlKey && e.shiftKey) {
                e.preventDefault();
                if (@this.canSubmitAssessment()) {
                    @this.call('submitAssessment');
                }
            }
        }
    });

    // Focus management for accessibility
    function focusCurrentQuestion() {
        const currentQuestionContainer = document.querySelector('.bg-gray-700.rounded-lg.p-6.mb-6');
        if (currentQuestionContainer) {
            const firstInput = currentQuestionContainer.querySelector('input, textarea');
            if (firstInput) {
                firstInput.focus();
            }
        }
    }

    // Clean up intervals when component is destroyed
    document.addEventListener('livewire:navigating', function() {
        if (timerInterval) {
            clearInterval(timerInterval);
        }
        if (autoSaveInterval) {
            clearInterval(autoSaveInterval);
        }
    });
</script>

<style>
    @media print {
        .no-print {
            display: none !important;
        }

        .print-only {
            display: block !important;
        }

        body {
            background: white !important;
            color: black !important;
        }

        .bg-gray-800,
        .bg-gray-700,
        .bg-gray-600 {
            background: white !important;
            color: black !important;
            border: 1px solid #ccc !important;
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .5;
        }
    }

    /* Question navigation indicators */
    .question-nav-btn {
        transition: all 0.2s ease;
    }

    .question-nav-btn:hover {
        transform: scale(1.05);
    }

    /* Progress bar animation */
    .progress-bar {
        transition: width 0.3s ease-in-out;
    }

    /* Answer option hover effects */
    .answer-option {
        transition: all 0.2s ease;
    }

    .answer-option:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Timer warning colors */
    .timer-warning {
        color: #f59e0b !important;
    }

    .timer-danger {
        color: #ef4444 !important;
    }
</style>

</div>