<div>
    <div>
        @if ($assessmentState === 'list')
            <!-- Assessment List -->
            @if ($assessments->count() > 0)
                <div class="mb-6 bg-purple-900/20 border-2 border-purple-500 rounded-xl p-6">
                    <!-- Prominent Assessment Header -->
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-check text-2xl text-white"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-purple-300 mb-2">Assessment Required</h2>
                        <p class="text-purple-200">Complete all assessments in this lesson to proceed</p>
                    </div>

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
                                $percentage = 0;

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
                                class="bg-gray-800 rounded-lg p-6 border-l-4 {{ $passed ? 'border-green-500 bg-green-900/10' : ($hasAttempted ? 'border-yellow-500 bg-yellow-900/10' : 'border-purple-500') }}">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <!-- Assessment Header -->
                                        <div class="flex items-center gap-3 mb-3">
                                            @if ($passed)
                                                <div
                                                    class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-check text-white text-sm"></i>
                                                </div>
                                            @elseif ($hasAttempted)
                                                <div
                                                    class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-exclamation text-white text-sm"></i>
                                                </div>
                                            @else
                                                <div
                                                    class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-play text-white text-sm"></i>
                                                </div>
                                            @endif

                                            <div>
                                                <h3 class="text-xl font-bold text-white">{{ $assessment->title }}</h3>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span
                                                        class="px-2 py-1 rounded-full text-xs font-medium {{ $assessment->type === 'quiz' ? 'bg-purple-600 text-white' : 'bg-blue-600 text-white' }}">
                                                        {{ ucfirst($assessment->type) }}
                                                    </span>
                                                    @if ($assessment->is_mandatory)
                                                        <span
                                                            class="px-2 py-1 bg-red-600 text-white rounded-full text-xs font-medium">
                                                            Mandatory
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if ($assessment->description)
                                            <p class="text-gray-300 mb-4">{{ $assessment->description }}</p>
                                        @endif

                                        <!-- Assessment Details -->
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                            <div class="bg-gray-700 rounded-lg p-3 text-center">
                                                <div class="text-lg font-bold text-white">
                                                    {{ $assessment->questions->count() }}</div>
                                                <div class="text-xs text-gray-400">Questions</div>
                                            </div>
                                            @if ($assessment->estimated_duration_minutes)
                                                <div class="bg-gray-700 rounded-lg p-3 text-center">
                                                    <div class="text-lg font-bold text-white">
                                                        {{ $assessment->estimated_duration_minutes }}</div>
                                                    <div class="text-xs text-gray-400">Minutes</div>
                                                </div>
                                            @endif
                                            <div class="bg-gray-700 rounded-lg p-3 text-center">
                                                <div class="text-lg font-bold text-white">
                                                    {{ $assessment->pass_percentage }}%</div>
                                                <div class="text-xs text-gray-400">To Pass</div>
                                            </div>
                                            @if ($assessment->questions->sum('points') > 0)
                                                <div class="bg-gray-700 rounded-lg p-3 text-center">
                                                    <div class="text-lg font-bold text-white">
                                                        {{ $assessment->questions->sum('points') }}</div>
                                                    <div class="text-xs text-gray-400">Points</div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Attempt Status -->
                                        @if ($hasAttempted)
                                            <div
                                                class="p-4 rounded-lg {{ $passed ? 'bg-green-900/20 border border-green-700' : 'bg-yellow-900/20 border border-yellow-700' }}">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        @if ($passed)
                                                            <i class="fas fa-trophy text-green-400 text-xl"></i>
                                                            <div>
                                                                <div class="text-green-300 font-bold text-lg">PASSED
                                                                </div>
                                                                <div class="text-green-200 text-sm">Score:
                                                                    {{ $percentage }}%</div>
                                                            </div>
                                                        @else
                                                            <i class="fas fa-redo text-yellow-400 text-xl"></i>
                                                            <div>
                                                                <div class="text-yellow-300 font-bold text-lg">RETRY
                                                                    NEEDED</div>
                                                                <div class="text-yellow-200 text-sm">Score:
                                                                    {{ $percentage }}% (Need
                                                                    {{ $assessment->pass_percentage }}%)</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="text-right">
                                                        <div class="text-xs text-gray-400">Last attempt</div>
                                                        <div class="text-sm text-gray-300">
                                                            {{ $latestAttempt->created_at->format('M j, Y') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="p-4 bg-purple-900/20 border border-purple-600 rounded-lg">
                                                <div class="flex items-center gap-3">
                                                    <i class="fas fa-play-circle text-purple-400 text-xl"></i>
                                                    <div>
                                                        <div class="text-purple-300 font-medium">Ready to Start</div>
                                                        <div class="text-purple-200 text-sm">Click below to begin this
                                                            assessment</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Action Buttons -->
                                    <!-- Updated Action Buttons with Clear Attempts -->
                                    <div class="flex flex-col gap-3 lg:w-64">
                                        @if (!$hasAttempted)
                                            <button wire:click="startAssessment({{ $assessment->id }})"
                                                class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                                <i class="fas fa-play"></i>
                                                Start Assessment
                                            </button>
                                        @else
                                            <button wire:click="startAssessment({{ $assessment->id }})"
                                                class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                                <i class="fas fa-eye"></i>
                                                View Results
                                            </button>
                                            @if (!$passed)
                                                <button wire:click="retakeAssessment({{ $assessment->id }})"
                                                    class="px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                                                    <i class="fas fa-redo"></i>
                                                    Retake
                                                </button>
                                            @endif

                                            <!-- Clear Attempts Button -->
                                            <button wire:click="clearPreviousAttempts({{ $assessment->id }})"
                                                wire:confirm="Are you sure you want to clear all previous attempts? This action cannot be undone."
                                                class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
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
                            $latestAttempt = \App\Models\StudentAnswer::where('user_id', Auth::id())
                                ->where('assessment_id', $assessment->id)
                                ->orderBy('attempt_number', 'desc')
                                ->first();
                            if ($latestAttempt) {
                                $totalPoints = \App\Models\StudentAnswer::where('user_id', Auth::id())
                                    ->where('assessment_id', $assessment->id)
                                    ->where('attempt_number', $latestAttempt->attempt_number)
                                    ->sum('points_earned');
                                $maxPoints = $assessment->questions->sum('points');
                                $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;
                                if ($percentage >= $assessment->pass_percentage) {
                                    $passedAssessments++;
                                }
                            }
                        }
                        $allPassed = $passedAssessments === $totalAssessments;
                    @endphp

                    <div
                        class="mt-6 p-4 rounded-lg border-2 {{ $allPassed ? 'bg-green-900/20 border-green-500' : 'bg-purple-900/20 border-purple-500' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @if ($allPassed)
                                    <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                                    <div>
                                        <div class="text-green-300 font-bold">All Assessments Passed!</div>
                                        <div class="text-green-200 text-sm">You can now proceed to the next lesson</div>
                                    </div>
                                @else
                                    <i class="fas fa-clipboard-list text-purple-400 text-2xl"></i>
                                    <div>
                                        <div class="text-purple-300 font-bold">Assessment Progress</div>
                                        <div class="text-purple-200 text-sm">
                                            {{ $passedAssessments }}/{{ $totalAssessments }} assessments passed</div>
                                    </div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div
                                    class="text-2xl font-bold {{ $allPassed ? 'text-green-400' : 'text-purple-400' }}">
                                    {{ $totalAssessments > 0 ? round(($passedAssessments / $totalAssessments) * 100) : 0 }}%
                                </div>
                                <div class="text-xs text-gray-400">Complete</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @elseif($assessmentState === 'taking')
            <!-- Assessment Taking Interface -->
            <div class="bg-gray-800 rounded-xl p-6 border-2 border-purple-500">
                <!-- Assessment Header -->
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-clipboard-check text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">{{ $currentAssessment->title }}</h2>
                            <p class="text-gray-400 mt-1">{{ $currentAssessment->description }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        @if ($timeRemaining)
                            <div class="bg-red-900/20 border border-red-600 rounded-lg px-4 py-2">
                                <div class="flex items-center gap-2 text-red-300">
                                    <i class="fas fa-clock"></i>
                                    <span class="font-mono font-bold" id="timer">
                                        {{ gmdate('i:s', $timeRemaining) }}
                                    </span>
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
                    <div class="w-full bg-gray-700 rounded-full h-3">
                        <div class="bg-purple-500 h-3 rounded-full transition-all duration-300"
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
                            <div class="text-sm text-purple-300 font-medium">
                                {{ $currentQuestion->points }}
                                {{ $currentQuestion->points == 1 ? 'point' : 'points' }}
                            </div>
                        </div>

                        <div class="text-white mb-6 leading-relaxed text-lg">
                            {!! nl2br(e($currentQuestion->question_text)) !!}
                        </div>

                        <!-- Answer Options -->
                        @if ($currentQuestion->question_type === 'multiple_choice')
                            <div class="space-y-3">
                                @foreach (json_decode($currentQuestion->options, true) ?? [] as $index => $option)
                                    <label
                                        class="flex items-center p-4 bg-gray-600 rounded-lg hover:bg-gray-500 cursor-pointer transition-all duration-200 border-2 border-transparent hover:border-purple-500">
                                        @if ($currentQuestion->hasMultipleCorrectAnswers())
                                            <input type="checkbox"
                                                wire:model.live="answers.{{ $currentQuestion->id }}"
                                                value="{{ $index }}"
                                                class="mr-3 rounded w-4 h-4 text-purple-600">
                                        @else
                                            <input type="radio"
                                                wire:model.live="answers.{{ $currentQuestion->id }}"
                                                value="{{ $index }}" class="mr-3 w-4 h-4 text-purple-600">
                                        @endif
                                        <span class="text-white text-lg">{{ chr(65 + $index) }}.
                                            {{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($currentQuestion->question_type === 'true_false')
                            <div class="space-y-3">
                                @foreach (json_decode($currentQuestion->options, true) ?? [] as $index => $option)
                                    <label
                                        class="flex items-center p-4 bg-gray-600 rounded-lg hover:bg-gray-500 cursor-pointer transition-all duration-200 border-2 border-transparent hover:border-purple-500">
                                        <input type="radio" wire:model.live="answers.{{ $currentQuestion->id }}"
                                            value="{{ $index }}" class="mr-3 w-4 h-4 text-purple-600">
                                        <span class="text-white text-lg">{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif(in_array($currentQuestion->question_type, ['short_answer', 'fill_blank']))
                            <div>
                                <input type="text" wire:model.live="answers.{{ $currentQuestion->id }}"
                                    placeholder="Enter your answer..."
                                    class="w-full px-4 py-3 bg-gray-600 border-2 border-gray-500 rounded-lg text-white placeholder-gray-400 focus:border-purple-500 focus:ring-purple-500 text-lg">
                            </div>
                        @elseif($currentQuestion->question_type === 'essay')
                            <div>
                                <textarea wire:model.live="answers.{{ $currentQuestion->id }}" rows="8"
                                    placeholder="Write your essay answer here..."
                                    class="w-full px-4 py-3 bg-gray-600 border-2 border-gray-500 rounded-lg text-white placeholder-gray-400 focus:border-purple-500 focus:ring-purple-500 resize-y text-lg"></textarea>
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
                                class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-medium transition-colors
                                           {{ $index === $currentQuestionIndex
                                               ? 'bg-purple-600 text-white shadow-lg'
                                               : ($this->isQuestionAnswered($question->id)
                                                   ? 'bg-green-600 text-white'
                                                   : 'bg-gray-600 text-gray-300 hover:bg-gray-500') }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Submit Button -->
                    <button wire:click="submitAssessment" @if (!$this->canSubmitAssessment()) disabled @endif
                        class="px-8 py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-500 disabled:cursor-not-allowed text-white rounded-lg font-bold transition-colors text-lg">
                        <i class="fas fa-check mr-2"></i>
                        Submit Assessment
                    </button>
                </div>
            </div>
        @elseif($assessmentState === 'results')
            <!-- Results Display -->
            <div
                class="bg-gray-800 rounded-xl p-6 border-2 {{ $results['passed'] ? 'border-green-500' : 'border-red-500' }}">
                <div class="text-center mb-8">
                    <div class="mb-6">
                        @if ($results['passed'])
                            <div
                                class="w-24 h-24 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-bounce">
                                <i class="fas fa-trophy text-4xl text-white"></i>
                            </div>
                            <h2 class="text-3xl font-bold text-green-400 mb-2">Congratulations!</h2>
                            <p class="text-green-300 text-lg">You passed the assessment</p>
                        @else
                            <div
                                class="w-24 h-24 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-times text-4xl text-white"></i>
                            </div>
                            <h2 class="text-3xl font-bold text-red-400 mb-2">Assessment Incomplete</h2>
                            <p class="text-red-300 text-lg">You need {{ $currentAssessment->pass_percentage }}% to
                                pass</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-gray-700 rounded-lg p-6">
                            <div
                                class="text-3xl font-bold {{ $results['passed'] ? 'text-green-400' : 'text-red-400' }}">
                                {{ $results['percentage'] }}%
                            </div>
                            <div class="text-sm text-gray-400">Final Score</div>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-6">
                            <div class="text-3xl font-bold text-white">
                                {{ $results['correct_answers'] }}/{{ $results['total_questions'] }}
                            </div>
                            <div class="text-sm text-gray-400">Correct</div>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-6">
                            <div class="text-3xl font-bold text-white">{{ $results['total_points'] }}</div>
                            <div class="text-sm text-gray-400">Points Earned</div>
                        </div>
                        <div class="bg-gray-700 rounded-lg p-6">
                            <div class="text-3xl font-bold text-white">{{ $results['max_points'] }}</div>
                            <div class="text-sm text-gray-400">Total Points</div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Results -->

                <!-- Updated Question Review section for the Blade template -->


                <!-- Updated Detailed Results with Fixed Answer Display -->
        <div class="space-y-4 mb-8">
            <h3 class="text-xl font-semibold text-white mb-4">Question Review</h3>
            @foreach ($currentAssessment->questions as $index => $question)
                @php
                    $studentAnswer = $results['answers'][$question->id] ?? null;
                    $isCorrect = $studentAnswer && isset($studentAnswer->is_correct) ? $studentAnswer->is_correct : false;
                    $wasAnswered = $studentAnswer && isset($studentAnswer->formatted_answer) && $studentAnswer->formatted_answer !== 'Not answered';
                @endphp

                <div class="bg-gray-700 rounded-lg p-6 border-l-4 {{ $isCorrect ? 'border-green-500' : ($wasAnswered ? 'border-red-500' : 'border-yellow-500') }}">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-semibold text-white text-lg">Question {{ $index + 1 }}</h4>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-400 bg-gray-600 px-3 py-1 rounded-full">
                                {{ $studentAnswer ? ($studentAnswer->points_earned ?? 0) : 0 }}/{{ $question->points }} points
                            </span>
                            @if ($isCorrect)
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                            @elseif ($wasAnswered)
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-times text-white"></i>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-minus text-white"></i>
                                </div>
                            @endif
                        </div>
                    </div>

                    <p class="text-gray-200 mb-4 text-lg leading-relaxed">{!! nl2br(e($question->question_text)) !!}</p>

                    <!-- Answer Options Display for Multiple Choice/True-False -->
                    @if (in_array($question->question_type, ['multiple_choice', 'true_false']))
                        @php
                            $options = json_decode($question->options, true) ?? [];
                            $correctAnswers = json_decode($question->correct_answers, true) ?? [];
                            $userAnswers = [];
                            
                            if ($studentAnswer && isset($studentAnswer->raw_answer)) {
                                $rawAnswer = $studentAnswer->raw_answer;
                                
                                // Handle JSON string answers
                                if (is_string($rawAnswer) && json_decode($rawAnswer) !== null) {
                                    $rawAnswer = json_decode($rawAnswer, true);
                                }
                                
                                $userAnswers = is_array($rawAnswer) ? array_map('intval', $rawAnswer) : [(int) $rawAnswer];
                            }
                        @endphp
                        
                        <div class="bg-gray-800/50 rounded-lg p-4 mb-4">
                            <div class="text-sm text-gray-400 mb-3">Answer choices:</div>
                            <div class="space-y-2">
                                @foreach ($options as $optionIndex => $option)
                                    @php
                                        $isCorrectOption = in_array($optionIndex, $correctAnswers);
                                        $isUserChoice = in_array($optionIndex, $userAnswers);
                                    @endphp
                                    
                                    <div class="flex items-center gap-3 p-3 rounded-lg transition-colors
                                        {{ $isCorrectOption ? 'bg-green-900/30 border border-green-700' : '' }}
                                        {{ $isUserChoice && !$isCorrectOption ? 'bg-red-900/30 border border-red-700' : '' }}
                                        {{ !$isCorrectOption && !$isUserChoice ? 'bg-gray-700/50' : '' }}">
                                        
                                        <!-- Option Letter -->
                                        <span class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                            {{ $isCorrectOption ? 'bg-green-600 text-white' : ($isUserChoice ? 'bg-red-600 text-white' : 'bg-gray-600 text-gray-300') }}">
                                            {{ chr(65 + $optionIndex) }}
                                        </span>
                                        
                                        <!-- Option Text -->
                                        <span class="text-white flex-1 text-lg">{{ $option }}</span>
                                        
                                        <!-- Indicators -->
                                        <div class="flex items-center gap-2">
                                            @if ($isUserChoice)
                                                <span class="text-xs px-2 py-1 rounded-full font-medium
                                                    {{ $isCorrectOption ? 'bg-green-600 text-white' : 'bg-red-600 text-white' }}">
                                                    <i class="fas fa-user mr-1"></i>Your choice
                                                </span>
                                            @endif
                                            @if ($isCorrectOption)
                                                <span class="text-xs px-2 py-1 bg-green-600 text-white rounded-full font-medium">
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
                            <div class="bg-gray-600 rounded-lg p-4 mb-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-6 h-6 {{ $isCorrect ? 'bg-green-600' : ($wasAnswered ? 'bg-red-600' : 'bg-yellow-600') }} rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                        <i class="fas fa-user text-white text-xs"></i>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-blue-300 font-medium text-sm">Your answer:</span>
                                        <div class="text-white mt-1 text-lg">
                                            @if ($studentAnswer->formatted_answer === 'Not answered')
                                                <em class="text-yellow-300">{{ $studentAnswer->formatted_answer }}</em>
                                            @else
                                                {{ $studentAnswer->formatted_answer }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Correct Answer for text questions -->
                            @if (!$isCorrect && isset($question->formatted_correct_answer))
                                <div class="bg-green-900/20 border border-green-700 rounded-lg p-4 mb-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
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
                        <div class="bg-blue-900/20 border border-blue-700 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                    <i class="fas fa-lightbulb text-white text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <span class="text-blue-300 font-medium text-sm">Explanation:</span>
                                    <p class="text-blue-200 mt-1 leading-relaxed">
                                        {{ $question->explanation }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Action Buttons with Clear Attempts option -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button wire:click="backToAssessmentList"
                class="px-8 py-4 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-colors text-lg">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Assessments
            </button>

            @if (!$results['passed'])
                <button wire:click="retakeAssessment"
                    class="px-8 py-4 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-semibold transition-colors text-lg">
                    <i class="fas fa-redo mr-2"></i>
                    Retake Assessment
                </button>
            @endif

            <button wire:click="clearPreviousAttempts"
                wire:confirm="Are you sure you want to clear all attempts for this assessment? This action cannot be undone."
                class="px-8 py-4 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-colors text-lg">
                <i class="fas fa-trash mr-2"></i>
                Clear All Attempts
            </button>

            @if ($results['passed'])
                <button onclick="window.print()"
                    class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition-colors text-lg">
                    <i class="fas fa-print mr-2"></i>
                    Print Results
                </button>
            @endif
        </div>
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
                        timerElement.parentElement.classList.add('animate-pulse');
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

            @keyframes bounce {

                0%,
                20%,
                53%,
                80%,
                100% {
                    transform: translate3d(0, 0, 0);
                }

                40%,
                43% {
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

            /* Answer option hover effects */
            .answer-option {
                transition: all 0.2s ease;
            }

            .answer-option:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            /* Enhanced radio and checkbox styling */
            input[type="radio"]:checked,
            input[type="checkbox"]:checked {
                background-color: #7c3aed;
                border-color: #7c3aed;
            }

            input[type="radio"]:focus,
            input[type="checkbox"]:focus {
                ring-color: #7c3aed;
                border-color: #7c3aed;
            }
        </style>
    </div>
