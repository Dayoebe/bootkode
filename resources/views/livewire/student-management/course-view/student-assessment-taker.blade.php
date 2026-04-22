<div>
    @if ($assessmentState === 'list')
        <!-- Assessment List -->
        @if ($assessments->count() > 0)
            <div class="mb-6 bg-gradient-to-br from-accent-themed-primary/20 to-accent-themed-secondary/20 border-2 border-accent-themed-primary/30 rounded-xl p-6 shadow-lg transition-colors duration-300">
                <!-- Assessment Header -->
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-accent-themed-primary to-accent-themed-secondary rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-clipboard-check text-2xl text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-themed-primary mb-2">Assessment Required</h2>
                    <p class="text-themed-secondary">Complete all assessments in this lesson to proceed</p>
                </div>

                <div class="space-y-4">
                    @foreach ($assessments as $assessment)
                        @php
                            $status = $this->getAssessmentStatus($assessment->id);
                            $hasAttempted = $status['hasAttempted'];
                            $passed = $status['passed'];
                            $percentage = $status['percentage'];
                        @endphp
                        <div class="bg-themed-secondary rounded-lg p-6 border-l-4 {{ $passed ? 'border-green-500' : ($hasAttempted ? 'border-yellow-500' : 'border-accent-themed-primary') }} shadow-md hover:shadow-lg transition-shadow duration-200 border border-themed-secondary">
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                <div class="flex-1">
                                    <!-- Assessment Header -->
                                    <div class="flex items-center gap-3 mb-3">
                                        @if ($passed)
                                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center shadow-md">
                                                <i class="fas fa-check text-white text-lg"></i>
                                            </div>
                                        @elseif ($hasAttempted)
                                            <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center shadow-md">
                                                <i class="fas fa-exclamation text-white text-lg"></i>
                                            </div>
                                        @else
                                            <div class="w-10 h-10 bg-accent-themed-primary rounded-full flex items-center justify-center shadow-md">
                                                <i class="fas fa-play text-white text-lg"></i>
                                            </div>
                                        @endif

                                        <div>
                                            <h3 class="text-xl font-bold text-themed-primary">{{ $assessment->title }}</h3>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $assessment->type === 'quiz' ? 'bg-accent-themed-primary/20 text-accent-themed-primary' : 'bg-purple-500/20 text-purple-400' }}">
                                                    {{ ucfirst($assessment->type) }}
                                                </span>
                                                @if ($assessment->is_mandatory)
                                                    <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded-full text-xs font-medium">
                                                        Mandatory
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if ($assessment->description)
                                        <p class="text-themed-secondary mb-4">{{ $assessment->description }}</p>
                                    @endif

                                    <!-- Assessment Details -->
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                                        <div class="bg-themed-tertiary rounded-lg p-3 text-center">
                                            <div class="text-lg font-bold text-themed-primary">{{ $assessment->questions->count() }}</div>
                                            <div class="text-xs text-themed-tertiary">Questions</div>
                                        </div>
                                        @if ($assessment->estimated_duration_minutes)
                                            <div class="bg-themed-tertiary rounded-lg p-3 text-center">
                                                <div class="text-lg font-bold text-themed-primary">{{ $assessment->estimated_duration_minutes }}</div>
                                                <div class="text-xs text-themed-tertiary">Minutes</div>
                                            </div>
                                        @endif
                                        <div class="bg-themed-tertiary rounded-lg p-3 text-center">
                                            <div class="text-lg font-bold text-themed-primary">{{ $assessment->pass_percentage }}%</div>
                                            <div class="text-xs text-themed-tertiary">To Pass</div>
                                        </div>
                                        @if ($assessment->questions->sum('points') > 0)
                                            <div class="bg-themed-tertiary rounded-lg p-3 text-center">
                                                <div class="text-lg font-bold text-themed-primary">{{ $assessment->questions->sum('points') }}</div>
                                                <div class="text-xs text-themed-tertiary">Points</div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Attempt Status -->
                                    @if ($hasAttempted)
                                        <div class="p-4 rounded-lg {{ $passed ? 'bg-green-500/20 border border-green-500/30' : 'bg-yellow-500/20 border border-yellow-500/30' }} transition-colors duration-300">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-3">
                                                    @if ($passed)
                                                        <i class="fas fa-trophy text-green-400 text-xl"></i>
                                                        <div>
                                                            <div class="text-green-300 font-bold text-lg">PASSED</div>
                                                            <div class="text-green-400 text-sm">Score: {{ $percentage }}%</div>
                                                        </div>
                                                    @else
                                                        <i class="fas fa-redo text-yellow-400 text-xl"></i>
                                                        <div>
                                                            <div class="text-yellow-300 font-bold text-lg">RETRY NEEDED</div>
                                                            <div class="text-yellow-400 text-sm">Score: {{ $percentage }}% (Need {{ $assessment->pass_percentage }}%)</div>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if ($status['latestAttempt'])
                                                <div class="text-right">
                                                    <div class="text-xs text-themed-tertiary">Last attempt</div>
                                                    <div class="text-sm text-themed-secondary">{{ $status['latestAttempt']->created_at->format('M j, Y') }}</div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <div class="p-4 bg-accent-themed-primary/20 border border-accent-themed-primary/30 rounded-lg transition-colors duration-300">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-play-circle text-accent-themed-primary text-xl"></i>
                                                <div>
                                                    <div class="text-accent-themed-primary font-medium">Ready to Start</div>
                                                    <div class="text-accent-themed-primary/70 text-sm">Click below to begin this assessment</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-col gap-2 lg:w-48">
                                    @if (!$hasAttempted)
                                        <button wire:click="startAssessment({{ $assessment->id }})"
                                            class="px-6 py-3 bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary hover:from-accent-themed-secondary hover:to-accent-themed-primary text-white rounded-lg font-medium transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                            <i class="fas fa-play"></i>
                                            Start Assessment
                                        </button>
                                    @else
                                        <button wire:click="startAssessment({{ $assessment->id }})"
                                            class="px-6 py-3 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary rounded-lg font-medium transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg border border-themed-secondary">
                                            <i class="fas fa-eye"></i>
                                            View Results
                                        </button>
                                        @if (!$passed)
                                            <button wire:click="retakeAssessment({{ $assessment->id }})"
                                                class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                                <i class="fas fa-redo"></i>
                                                Retake
                                            </button>
                                        @endif
                                        <button wire:click="clearPreviousAttempts({{ $assessment->id }})"
                                            wire:confirm="Are you sure you want to clear all previous attempts? This action cannot be undone."
                                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                                            <i class="fas fa-trash"></i>
                                            Clear Attempts
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Overall Assessment Status -->
                @php
                    $totalAssessments = $assessments->count();
                    $passedAssessments = 0;
                    foreach ($assessments as $assessment) {
                        if ($this->getAssessmentStatus($assessment->id)['passed']) {
                            $passedAssessments++;
                        }
                    }
                    $allPassed = $passedAssessments === $totalAssessments;
                @endphp

                <div class="mt-6 p-4 rounded-lg border-2 {{ $allPassed ? 'bg-green-500/20 border-green-500/50' : 'bg-accent-themed-primary/20 border-accent-themed-primary/50' }} transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if ($allPassed)
                                <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                                <div>
                                    <div class="text-green-300 font-bold">All Assessments Passed!</div>
                                    <div class="text-green-400 text-sm">You can now proceed to the next lesson</div>
                                </div>
                            @else
                                <i class="fas fa-clipboard-list text-accent-themed-primary text-2xl"></i>
                                <div>
                                    <div class="text-accent-themed-primary font-bold">Assessment Progress</div>
                                    <div class="text-accent-themed-primary/70 text-sm">{{ $passedAssessments }}/{{ $totalAssessments }} assessments passed</div>
                                </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold {{ $allPassed ? 'text-green-400' : 'text-accent-themed-primary' }}">
                                {{ $totalAssessments > 0 ? round(($passedAssessments / $totalAssessments) * 100) : 0 }}%
                            </div>
                            <div class="text-xs text-themed-tertiary">Complete</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    @elseif($assessmentState === 'taking')
        <!-- Assessment Taking Interface -->
        <div class="bg-themed-secondary rounded-xl p-6 border-2 border-accent-themed-primary/50 shadow-lg transition-colors duration-300">
            <!-- Assessment Header -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6 pb-6 border-b border-themed-secondary">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-accent-themed-primary to-accent-themed-secondary rounded-full flex items-center justify-center shadow-lg">
                        <i class="fas fa-clipboard-check text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-themed-primary">{{ $currentAssessment->title }}</h2>
                        <p class="text-themed-secondary mt-1">{{ $currentAssessment->description }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    @if ($timeRemaining)
                        <div class="bg-red-500/20 border-2 border-red-500/50 rounded-lg px-4 py-2 shadow-md transition-colors duration-300">
                            <div class="flex items-center gap-2 text-red-400">
                                <i class="fas fa-clock"></i>
                                <span class="font-mono font-bold" id="timer">
                                    {{ gmdate('i:s', $timeRemaining) }}
                                </span>
                            </div>
                        </div>
                    @endif

                    <button wire:click="backToAssessmentList"
                        class="px-4 py-2 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary rounded-lg text-sm transition-colors duration-300 shadow-md hover:shadow-lg border border-themed-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Back to List
                    </button>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-themed-secondary">
                        Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
                    </span>
                    <span class="text-sm font-medium text-themed-secondary">
                        {{ $this->getAnsweredQuestionsCount() }}/{{ count($questions) }} answered
                    </span>
                </div>
                <div class="w-full bg-themed-tertiary rounded-full h-3 shadow-inner">
                    <div class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary h-3 rounded-full transition-all duration-300"
                        style="width: {{ $this->getQuestionProgress() }}%"></div>
                </div>
            </div>

            @if ($this->getCurrentQuestion())
                @php $currentQuestion = $this->getCurrentQuestion(); @endphp

                <!-- Question Content -->
                <div class="bg-themed-tertiary/50 rounded-lg p-6 mb-6 border border-themed-secondary transition-colors duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-themed-primary">
                            Question {{ $currentQuestionIndex + 1 }}
                            @if ($currentQuestion->is_required)
                                <span class="text-red-500 text-sm ml-1">*</span>
                            @endif
                        </h3>
                        <div class="text-sm font-medium px-3 py-1 bg-accent-themed-primary/20 text-accent-themed-primary rounded-full border border-accent-themed-primary/30">
                            {{ $currentQuestion->points }} {{ $currentQuestion->points == 1 ? 'point' : 'points' }}
                        </div>
                    </div>

                    <div class="text-themed-primary mb-6 leading-relaxed text-lg">
                        {!! nl2br(e($currentQuestion->question_text)) !!}
                    </div>

                    <!-- Answer Options -->
                    @php
                        $questionOptions = is_array($currentQuestion->options)
                            ? $currentQuestion->options
                            : (json_decode($currentQuestion->options ?? '[]', true) ?: []);
                    @endphp

	                    @if ($currentQuestion->question_type === 'multiple_choice')
	                        <div class="space-y-3">
	                            @foreach ($questionOptions as $optionIndex => $option)
	                                @php
	                                    $questionId = $currentQuestion->id;
	                                    $currentAnswer = $answers[$questionId] ?? null;
	                                    $isMultiSelect = $currentQuestion->hasMultipleCorrectAnswers();
	                                    $selectedAnswers = is_array($currentAnswer)
	                                        ? array_map('intval', $currentAnswer)
	                                        : (($currentAnswer === null || $currentAnswer === '')
	                                            ? []
	                                            : [(int) $currentAnswer]);
	                                    $isSelected = $isMultiSelect
	                                        ? in_array((int) $optionIndex, $selectedAnswers, true)
	                                        : (string) $currentAnswer === (string) $optionIndex;
	                                @endphp
	                                <div
	                                    @if ($isMultiSelect)
	                                        wire:click="toggleMultipleChoiceAnswer({{ $questionId }}, {{ $optionIndex }})"
	                                    @else
	                                        wire:click="$set('answers.{{ $questionId }}', '{{ $optionIndex }}')"
	                                    @endif
	                                    class="flex items-center p-4 bg-themed-secondary rounded-lg hover:bg-themed-tertiary/50 cursor-pointer transition-all duration-200 border-2 {{ $isSelected ? 'border-accent-themed-primary bg-accent-themed-primary/10 shadow-md' : 'border-themed-secondary' }}">
	                                    @if ($isMultiSelect)
	                                        <input type="checkbox"
	                                            @if($isSelected) checked @endif
	                                            class="mr-3 rounded w-5 h-5 text-accent-themed-primary pointer-events-none border-themed-secondary">
                                    @else
                                        <input type="radio"
                                            name="question_{{ $questionId }}"
                                            @if($isSelected) checked @endif
                                            class="mr-3 w-5 h-5 text-accent-themed-primary pointer-events-none border-themed-secondary">
                                    @endif
                                    <span class="text-accent-themed-primary text-lg font-medium">{{ chr(65 + $optionIndex) }}.</span>
                                    <span class="text-themed-primary text-lg ml-2">{{ $option }}</span>
                                </div>
                            @endforeach
                        </div>
                    @elseif($currentQuestion->question_type === 'true_false')
                        <div class="space-y-3">
                            @foreach ($questionOptions as $optionIndex => $option)
                                @php
                                    $questionId = $currentQuestion->id;
                                    $currentAnswer = isset($answers[$questionId]) ? $answers[$questionId] : null;
                                    $isSelected = (string)$currentAnswer === (string)$optionIndex;
                                @endphp
                                <div
                                    wire:click="$set('answers.{{ $questionId }}', '{{ $optionIndex }}')"
                                    class="flex items-center p-4 bg-themed-secondary rounded-lg hover:bg-themed-tertiary/50 cursor-pointer transition-all duration-200 border-2 {{ $isSelected ? 'border-accent-themed-primary bg-accent-themed-primary/10 shadow-md' : 'border-themed-secondary' }}">
                                    <input type="radio"
                                        name="question_{{ $questionId }}"
                                        @if($isSelected) checked @endif
                                        class="mr-3 w-5 h-5 text-accent-themed-primary pointer-events-none border-themed-secondary">
                                    <span class="text-themed-primary text-lg">{{ $option }}</span>
                                </div>
                            @endforeach
                        </div>
                    @elseif(in_array($currentQuestion->question_type, ['short_answer', 'fill_blank']))
                        <div>
                            <input type="text" wire:model.live="answers.{{ $currentQuestion->id }}"
                                placeholder="Enter your answer..."
                                class="w-full px-4 py-3 bg-themed-tertiary border-2 border-themed-secondary rounded-lg text-themed-primary placeholder-themed-tertiary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 text-lg transition-colors duration-300">
                        </div>
                    @elseif($currentQuestion->question_type === 'essay')
                        <div>
                            <textarea wire:model.live="answers.{{ $currentQuestion->id }}" rows="8"
                                placeholder="Write your essay answer here..."
                                class="w-full px-4 py-3 bg-themed-tertiary border-2 border-themed-secondary rounded-lg text-themed-primary placeholder-themed-tertiary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 resize-y text-lg transition-colors duration-300"></textarea>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Question Navigation -->
            <div class="flex flex-col lg:flex-row justify-between items-center gap-4 pt-6 border-t border-themed-secondary">
                <div class="flex gap-2">
                    <button wire:click="previousQuestion" @if ($currentQuestionIndex === 0) disabled @endif
                        class="px-4 py-2 bg-themed-tertiary hover:bg-themed-secondary disabled:opacity-50 disabled:cursor-not-allowed text-themed-primary rounded-lg transition-colors duration-300 shadow-md hover:shadow-lg border border-themed-secondary">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Previous
                    </button>

                    <button wire:click="nextQuestion" @if ($currentQuestionIndex === count($questions) - 1) disabled @endif
                        class="px-4 py-2 bg-themed-tertiary hover:bg-themed-secondary disabled:opacity-50 disabled:cursor-not-allowed text-themed-primary rounded-lg transition-colors duration-300 shadow-md hover:shadow-lg border border-themed-secondary">
                        Next
                        <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>

                <!-- Question Overview -->
                <div class="flex flex-wrap gap-2 justify-center">
                    @foreach ($questions as $index => $question)
                        <button wire:click="goToQuestion({{ $index }})"
                            class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-medium transition-all duration-200
                                {{ $index === $currentQuestionIndex
                                    ? 'bg-gradient-to-br from-accent-themed-primary to-accent-themed-secondary text-white shadow-lg ring-2 ring-accent-themed-primary/50'
                                    : ($this->isQuestionAnswered($question->id)
                                        ? 'bg-green-500 hover:bg-green-600 text-white shadow-md'
                                        : 'bg-themed-tertiary text-themed-primary hover:bg-themed-secondary border border-themed-secondary') }}">
                            {{ $index + 1 }}
                        </button>
                    @endforeach
                </div>

                <!-- Submit Button -->
                <button wire:click="submitAssessment" @if (!$this->canSubmitAssessment()) disabled @endif
                    class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 disabled:from-themed-tertiary disabled:to-themed-tertiary disabled:cursor-not-allowed text-white rounded-lg font-bold transition-all duration-200 text-lg shadow-lg hover:shadow-xl">
                    <i class="fas fa-check mr-2"></i>
                    Submit Assessment
                </button>
            </div>
        </div>

    @elseif($assessmentState === 'results')
        <!-- Results Display -->
        <div class="bg-themed-secondary rounded-xl p-6 border-2 {{ $results['passed'] ? 'border-green-500/50' : 'border-red-500/50' }} shadow-lg transition-colors duration-300">
            <div class="text-center mb-8">
                <div class="mb-6">
                    @if ($results['passed'])
                        <div class="w-24 h-24 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce shadow-lg">
                            <i class="fas fa-trophy text-4xl text-white"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-green-400 mb-2">Congratulations!</h2>
                        <p class="text-green-300 text-lg">You passed the assessment</p>
                    @else
                        <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-times text-4xl text-white"></i>
                        </div>
                        <h2 class="text-3xl font-bold text-red-400 mb-2">Assessment Incomplete</h2>
                        <p class="text-red-300 text-lg">You need {{ $currentAssessment->pass_percentage }}% to pass</p>
                    @endif
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-themed-tertiary rounded-lg p-6 border border-themed-secondary transition-colors duration-300">
                        <div class="text-3xl font-bold {{ $results['passed'] ? 'text-green-400' : 'text-red-400' }}">
                            {{ $results['percentage'] }}%
                        </div>
                        <div class="text-sm text-themed-tertiary mt-1">Final Score</div>
                    </div>
                    <div class="bg-themed-tertiary rounded-lg p-6 border border-themed-secondary transition-colors duration-300">
                        <div class="text-3xl font-bold text-themed-primary">
                            {{ $results['correct_answers'] }}/{{ $results['total_questions'] }}
                        </div>
                        <div class="text-sm text-themed-tertiary mt-1">Correct</div>
                    </div>
                    <div class="bg-themed-tertiary rounded-lg p-6 border border-themed-secondary transition-colors duration-300">
                        <div class="text-3xl font-bold text-themed-primary">{{ $results['total_points'] }}</div>
                        <div class="text-sm text-themed-tertiary mt-1">Points Earned</div>
                    </div>
                    <div class="bg-themed-tertiary rounded-lg p-6 border border-themed-secondary transition-colors duration-300">
                        <div class="text-3xl font-bold text-themed-primary">{{ $results['max_points'] }}</div>
                        <div class="text-sm text-themed-tertiary mt-1">Total Points</div>
                    </div>
                </div>
            </div>

            <!-- Detailed Results -->
            <div class="space-y-4 mb-8">
                <h3 class="text-xl font-semibold text-themed-primary mb-4 flex items-center">
                    <i class="fas fa-list-check mr-2 text-accent-themed-primary"></i>
                    Question Review
                </h3>
                @foreach ($currentAssessment->questions as $index => $question)
                    @php
                        $studentAnswer = $results['answers'][$question->id] ?? null;
                        $isCorrect = $studentAnswer && isset($studentAnswer->is_correct) ? $studentAnswer->is_correct : false;
                        $wasAnswered = $studentAnswer && isset($studentAnswer->formatted_answer) && $studentAnswer->formatted_answer !== 'Not answered';
                    @endphp

                    <div class="bg-themed-tertiary rounded-lg p-6 border-l-4 {{ $isCorrect ? 'border-green-500' : ($wasAnswered ? 'border-red-500' : 'border-yellow-500') }} border border-themed-secondary shadow-sm transition-colors duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-themed-primary text-lg">Question {{ $index + 1 }}</h4>
                            <div class="flex items-center gap-3">
                                <span class="text-sm bg-themed-secondary text-themed-secondary px-3 py-1 rounded-full font-medium border border-themed-secondary">
                                    {{ $studentAnswer ? ($studentAnswer->points_earned ?? 0) : 0 }}/{{ $question->points }} points
                                </span>
                                @if ($isCorrect)
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-md">
                                        <i class="fas fa-check text-white"></i>
                                    </div>
                                @elseif ($wasAnswered)
                                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center shadow-md">
                                        <i class="fas fa-times text-white"></i>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center shadow-md">
                                        <i class="fas fa-minus text-white"></i>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="text-themed-primary mb-4 text-lg leading-relaxed">{!! nl2br(e($question->question_text)) !!}</p>

                        <!-- Answer Options Display for Multiple Choice/True-False -->
                        @if (in_array($question->question_type, ['multiple_choice', 'true_false']))
                            @php
                                $options = json_decode($question->options, true) ?? [];
                                $correctAnswers = json_decode($question->correct_answers, true) ?? [];
                                $userAnswers = [];
                                
                                if ($studentAnswer && isset($studentAnswer->answer)) {
                                    $answer = $studentAnswer->answer;
                                    $userAnswers = is_array($answer) ? array_map('intval', $answer) : [(int) $answer];
                                }
                            @endphp
                            
                            <div class="bg-themed-secondary rounded-lg p-4 mb-4 border border-themed-secondary transition-colors duration-300">
                                <div class="text-sm text-themed-tertiary mb-3 font-medium">Answer choices:</div>
                                <div class="space-y-2">
                                    @foreach ($options as $optionIndex => $option)
                                        @php
                                            $isCorrectOption = in_array($optionIndex, $correctAnswers);
                                            $isUserChoice = in_array($optionIndex, $userAnswers);
                                        @endphp
                                        
                                        <div class="flex items-center gap-3 p-3 rounded-lg transition-colors duration-300
                                            {{ $isCorrectOption ? 'bg-green-500/20 border-2 border-green-500 dark:border-green-600' : '' }}
                                            {{ $isUserChoice && !$isCorrectOption ? 'bg-red-500/20 border-2 border-red-500 dark:border-red-600' : '' }}
                                            {{ !$isCorrectOption && !$isUserChoice ? 'bg-themed-tertiary/50 border border-themed-secondary' : '' }}">
                                            
                                            <!-- Option Letter -->
                                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                                {{ $isCorrectOption ? 'bg-green-500 text-white' : ($isUserChoice ? 'bg-red-500 text-white' : 'bg-themed-tertiary text-themed-primary') }}">
                                                {{ chr(65 + $optionIndex) }}
                                            </span>
                                            
                                            <!-- Option Text -->
                                            <span class="text-themed-primary flex-1 text-lg">{{ $option }}</span>
                                            
                                            <!-- Indicators -->
                                            <div class="flex items-center gap-2">
                                                @if ($isUserChoice)
                                                    <span class="text-xs px-2 py-1 rounded-full font-medium
                                                        {{ $isCorrectOption ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                                        <i class="fas fa-user mr-1"></i>Your choice
                                                    </span>
                                                @endif
                                                @if ($isCorrectOption)
                                                    <span class="text-xs px-2 py-1 bg-green-500 text-white rounded-full font-medium">
                                                        <i class="fas fa-check mr-1"></i>Correct
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <!-- For text-based questions -->
                            @if ($studentAnswer && isset($studentAnswer->formatted_answer))
                                <div class="bg-themed-secondary rounded-lg p-4 mb-4 border border-themed-secondary transition-colors duration-300">
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 {{ $isCorrect ? 'bg-green-500' : ($wasAnswered ? 'bg-red-500' : 'bg-yellow-500') }} rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                            <i class="fas fa-user text-white text-xs"></i>
                                        </div>
                                        <div class="flex-1">
                                            <span class="text-accent-themed-primary font-medium text-sm">Your answer:</span>
                                            <div class="text-themed-primary mt-1 text-lg">
                                                @if ($studentAnswer->formatted_answer === 'Not answered')
                                                    <em class="text-yellow-400">{{ $studentAnswer->formatted_answer }}</em>
                                                @else
                                                    {{ $studentAnswer->formatted_answer }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Correct Answer for text questions -->
                                @if (!$isCorrect && isset($question->formatted_correct_answer))
                                    <div class="bg-green-500/20 border-2 border-green-500 rounded-lg p-4 mb-4 transition-colors duration-300">
                                        <div class="flex items-start gap-3">
                                            <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                            <div class="flex-1">
                                                <span class="text-green-300 font-medium text-sm">Correct answer:</span>
                                                <div class="text-green-200 mt-1 text-lg">
                                                    {{ $question->formatted_correct_answer }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @endif

                        <!-- Explanation -->
                        @if ($question->explanation && (!$isCorrect || !$wasAnswered))
                            <div class="bg-accent-themed-primary/20 border-2 border-accent-themed-primary rounded-lg p-4 transition-colors duration-300">
                                <div class="flex items-start gap-3">
                                    <div class="w-6 h-6 bg-accent-themed-primary rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                        <i class="fas fa-lightbulb text-white text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-accent-themed-primary font-medium text-sm">Explanation:</span>
                                        <p class="text-accent-themed-primary/80 mt-1 leading-relaxed">
                                            {{ $question->explanation }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center pt-6 border-t border-themed-secondary">
                <button wire:click="backToAssessmentList"
                    class="px-8 py-4 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary rounded-lg font-semibold transition-all duration-200 text-lg shadow-md hover:shadow-lg border border-themed-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Assessments
                </button>

                @if (!$results['passed'])
                    <button wire:click="retakeAssessment"
                        class="px-8 py-4 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-semibold transition-all duration-200 text-lg shadow-md hover:shadow-lg">
                        <i class="fas fa-redo mr-2"></i>
                        Retake Assessment
                    </button>
                @endif

                <button wire:click="clearPreviousAttempts"
                    wire:confirm="Are you sure you want to clear all attempts for this assessment? This action cannot be undone."
                    class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all duration-200 text-lg shadow-md hover:shadow-lg">
                    <i class="fas fa-trash mr-2"></i>
                    Clear All Attempts
                </button>

                @if ($results['passed'])
                    <button onclick="window.print()"
                        class="px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg font-semibold transition-all duration-200 text-lg shadow-lg hover:shadow-xl">
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

                if (timeRemaining <= 300) {
                    timerElement.parentElement.classList.add('animate-pulse');
                }
            }
        }

        document.addEventListener('livewire:init', function() {
            @this.on('assessment-started', function() {
                setTimeout(startTimer, 100);
            });
        });

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

        window.addEventListener('beforeunload', function(e) {
            if (@json($assessmentState) === 'taking' && !@json($isSubmitted)) {
                e.preventDefault();
                e.returnValue = '';
                return 'You have an assessment in progress. Are you sure you want to leave?';
            }
        });

        document.addEventListener('livewire:navigating', function() {
            if (timerInterval) {
                clearInterval(timerInterval);
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
                color: rgb(var(--text-primary)) !important;
            }

            .bg-themed-secondary,
            .bg-themed-tertiary {
                background: white !important;
                color: rgb(var(--text-primary)) !important;
                border: 1px solid #ccc !important;
            }
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

        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% {
                transform: translate3d(0, 0, 0);
            }
            40%, 43% {
                transform: translate3d(0, -15px, 0);
            }
            70% {
                transform: translate3d(0, -7px, 0);
            }
            90% {
                transform: translate3d(0, -2px, 0);
            }
        }

        .animate-bounce {
            animation: bounce 1s ease-in-out 2;
        }

        input[type="radio"]:checked,
        input[type="checkbox"]:checked {
            background-color: rgb(var(--accent-primary));
            border-color: rgb(var(--accent-primary));
        }

        input[type="radio"]:focus,
        input[type="checkbox"]:focus {
            ring-color: rgb(var(--accent-primary));
            border-color: rgb(var(--accent-primary));
        }

        /* Theme transition support */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
</div>
