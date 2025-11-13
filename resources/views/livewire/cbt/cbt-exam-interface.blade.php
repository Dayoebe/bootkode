<div x-data="cbtExamInterface" class="h-screen flex flex-col bg-themed-primary dark:bg-gray-900 transition-colors duration-300">

    @if(!$examStarted)
        <!-- Pre-Exam Instructions remain the same -->
        <div class="flex-1 flex items-center justify-center p-4 md:p-6">
            <div class="w-full max-w-4xl">
                <div class="bg-themed-secondary rounded-lg shadow-lg border border-themed-primary">
                    <div class="bg-yellow-500 dark:bg-yellow-600 text-yellow-900 dark:text-yellow-100 px-4 md:px-6 py-4 rounded-t-lg">
                        <h1 class="text-lg md:text-xl font-semibold flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Exam Instructions
                        </h1>
                    </div>
                    <div class="p-4 md:p-8">
                        <div class="text-center mb-6 md:mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold text-accent-themed-primary mb-2">{{ $assessment->title }}</h2>
                            @if($assessment->description)
                                <p class="text-themed-secondary text-base md:text-lg">{{ $assessment->description }}</p>
                            @endif
                        </div>

                        <!-- Exam Details Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
                            <div class="text-center border border-themed-secondary rounded-lg p-3 md:p-4 bg-themed-tertiary">
                                <i class="fas fa-question-circle text-2xl md:text-3xl text-accent-themed-primary mb-2"></i>
                                <div class="font-semibold text-themed-primary text-lg md:text-xl">{{ count($questions) }}</div>
                                <div class="text-xs md:text-sm text-themed-secondary">Questions</div>
                            </div>
                            <div class="text-center border border-themed-secondary rounded-lg p-3 md:p-4 bg-themed-tertiary">
                                <i class="fas fa-clock text-2xl md:text-3xl text-accent-themed-primary mb-2"></i>
                                <div class="font-semibold text-themed-primary text-lg md:text-xl">{{ $assessment->formatted_duration }}</div>
                                <div class="text-xs md:text-sm text-themed-secondary">Duration</div>
                            </div>
                            <div class="text-center border border-themed-secondary rounded-lg p-3 md:p-4 bg-themed-tertiary">
                                <i class="fas fa-trophy text-2xl md:text-3xl text-accent-themed-primary mb-2"></i>
                                <div class="font-semibold text-themed-primary text-lg md:text-xl">{{ $assessment->pass_percentage }}%</div>
                                <div class="text-xs md:text-sm text-themed-secondary">Pass Mark</div>
                            </div>
                            <div class="text-center border border-themed-secondary rounded-lg p-3 md:p-4 bg-themed-tertiary">
                                <i class="fas fa-hashtag text-2xl md:text-3xl text-accent-themed-primary mb-2"></i>
                                <div class="font-semibold text-themed-primary text-lg md:text-xl"># {{ $attemptNumber }}</div>
                                <div class="text-xs md:text-sm text-themed-secondary">Attempt</div>
                            </div>
                        </div>

                        <!-- Start Exam Button -->
                        <div class="text-center">
                            <button wire:click="startExam" wire:loading.attr="disabled" wire:target="startExam"
                                class="bg-blue-500 hover:bg-accent-themed-secondary disabled:bg-accent-themed-secondary disabled:opacity-50 text-white px-8 md:px-12 py-3 md:py-4 rounded-lg text-lg md:text-xl font-semibold transition-colors flex items-center mx-auto shadow-lg">
                                <div wire:loading.remove wire:target="startExam" class="flex items-center">
                                    <i class="fas fa-play mr-3"></i>Start CBT Exam
                                </div>
                                <div wire:loading wire:target="startExam" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Starting...
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif($examCompleted)
        <!-- Results Page remains the same -->
        <div class="flex-1 flex items-center justify-center p-4 md:p-6">
            <div class="w-full max-w-4xl">
                <div class="bg-themed-secondary rounded-lg shadow-lg border border-themed-primary">
                    <div class="px-4 md:px-6 py-4 rounded-t-lg {{ $results['passed'] ? 'bg-green-600' : 'bg-red-600' }} text-white">
                        <h1 class="text-xl md:text-2xl font-semibold flex items-center justify-center">
                            <i class="fas {{ $results['passed'] ? 'fa-check-circle' : 'fa-times-circle' }} mr-3"></i>
                            <span class="text-center">Exam {{ $results['passed'] ? 'COMPLETED - PASSED' : 'COMPLETED - FAILED' }}</span>
                        </h1>
                    </div>
                    <div class="p-4 md:p-8">
                        <div class="text-center mb-6 md:mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold text-themed-primary mb-2">{{ $assessment->title }}</h2>
                            <p class="text-themed-secondary">Attempt #{{ $results['attempt_number'] }}</p>
                        </div>

                        <!-- Results Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 text-center mb-6 md:mb-8">
                            <div class="border rounded-lg p-4 md:p-6 {{ $results['passed'] ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20' }}">
                                <div class="text-3xl md:text-4xl font-bold mb-2 {{ $results['passed'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $results['percentage'] }}%
                                </div>
                                <div class="text-sm text-themed-secondary font-medium">Final Score</div>
                            </div>
                            <div class="border border-themed-secondary rounded-lg p-4 md:p-6 bg-themed-tertiary">
                                <div class="text-2xl md:text-3xl font-bold text-accent-themed-primary mb-2">{{ $results['total_points'] }}</div>
                                <div class="text-sm text-themed-secondary font-medium">Points</div>
                                <div class="text-xs text-themed-tertiary">out of {{ $results['max_points'] }}</div>
                            </div>
                            <div class="border border-themed-secondary rounded-lg p-4 md:p-6 bg-themed-tertiary">
                                <div class="text-2xl md:text-3xl font-bold text-themed-primary mb-2">{{ gmdate('H:i:s', $results['time_spent']) }}</div>
                                <div class="text-sm text-themed-secondary font-medium">Time Used</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            @if(!$results['passed'])
                                <button wire:click="retakeExam"
                                    class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-6 md:px-8 py-3 rounded-lg text-lg font-semibold transition-colors flex items-center justify-center">
                                    <i class="fas fa-redo mr-2"></i>Retake Exam
                                </button>
                            @endif

                            <a href="{{ route('cbt.exams') }}"
                                class="w-full sm:w-auto bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 md:px-8 py-3 rounded-lg text-lg font-semibold transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-list mr-2"></i>Back to Exams
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Active Exam Interface with MathJax Support -->
        <div class="flex flex-col lg:flex-row h-screen bg-themed-primary dark:bg-gray-900">
            <!-- Questions Sidebar -->
            <div class="w-full lg:w-80 bg-themed-tertiary border-b lg:border-r lg:border-b-0 border-themed-secondary flex flex-col">
                <div class="p-3 lg:p-4 border-b border-themed-secondary bg-themed-secondary">
                    <h3 class="font-semibold text-themed-primary mb-1 text-sm lg:text-base">Question Navigator</h3>
                    <div class="text-xs lg:text-sm text-themed-secondary">
                        {{ $this->getAnsweredQuestionsCount() }} of {{ count($questions) }} answered
                    </div>
                    <div class="w-full bg-themed-secondary rounded-full h-2 mt-2">
                        <div class="bg-accent-themed-primary h-2 rounded-full transition-all duration-500 progress-animate"
                            style="width: {{ count($questions) > 0 ? ($this->getAnsweredQuestionsCount() / count($questions)) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

                <!-- Question Grid -->
                <div class="p-2 lg:p-4 flex-1 overflow-y-auto">
                    <div class="grid grid-cols-8 lg:grid-cols-5 gap-1 lg:gap-2">
                        @foreach($questions as $index => $question)
                            <button wire:click="goToQuestion({{ $index }})" 
                                class="relative aspect-square flex items-center justify-center text-xs lg:text-sm font-bold rounded-lg transition-all focus:ring-2 focus:ring-accent-themed-primary
                                @if($currentQuestionIndex === $index) 
                                    bg-accent-themed-primary text-white ring-2 ring-accent-themed-secondary
                                @elseif(isset($answers[$question['id']]) && $answers[$question['id']] !== null) 
                                    bg-green-500 text-white
                                @else 
                                    bg-themed-secondary border-2 border-themed-secondary text-themed-primary hover:border-accent-themed-primary hover:bg-themed-tertiary
                                @endif"
                                title="Question {{ $index + 1 }}{{ $this->isQuestionFlagged($index) ? ' (Flagged)' : '' }}"
                                tabindex="{{ $index + 1 }}">
                                {{ $index + 1 }}
                                @if($this->isQuestionFlagged($index))
                                    <i class="fas fa-flag absolute -top-1 -right-1 text-yellow-400 text-xs"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Main Exam Area -->
            <div class="flex-1 flex flex-col">
                <!-- Timer Header -->
                <div class="bg-gray-900 dark:bg-gray-800 text-white p-3 lg:p-4 flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-2 lg:space-y-0">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-6">
                        <div>
                            <h1 class="font-semibold text-base lg:text-lg">{{ $assessment->title }}</h1>
                            <div class="text-xs lg:text-sm text-gray-300 dark:text-gray-400">
                                Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 lg:space-x-4 w-full lg:w-auto">
                        <div class="flex items-center bg-gray-800 dark:bg-gray-700 px-3 lg:px-4 py-2 rounded-lg flex-1 lg:flex-none"
                            x-bind:class="timeRemaining <= 300 ? 'timer-critical' : ''">
                            <i class="fas fa-clock mr-2"></i>
                            <div class="text-center">
                                <div x-text="formatTime(timeRemaining)" class="font-mono text-base lg:text-xl font-bold"></div>
                            </div>
                        </div>

                        <button wire:click="toggleFlag({{ $currentQuestionIndex }})"
                            class="px-3 py-2 rounded-lg transition-colors flex items-center text-sm
                            {{ $this->isQuestionFlagged($currentQuestionIndex) ? 'bg-yellow-500 text-yellow-900' : 'border border-gray-400 text-gray-300 hover:bg-gray-700 dark:hover:bg-gray-600' }}"
                            title="Flag for review (F key)">
                            <i class="fas fa-flag mr-1"></i>
                            <span class="hidden sm:inline">Flag</span>
                        </button>
                    </div>
                </div>

                <!-- Question Content with MathJax Support -->
                <div class="flex-1 overflow-y-auto bg-themed-secondary">
                    @if($this->getCurrentQuestion())
                        @php $question = $this->getCurrentQuestion(); @endphp
                        <div class="p-4 lg:p-8 max-w-4xl mx-auto">
                            <!-- Question Header -->
                            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6 lg:mb-8">
                                <div>
                                    <h2 class="text-xl lg:text-2xl font-bold text-themed-primary mb-2">
                                        Question {{ $currentQuestionIndex + 1 }}
                                    </h2>
                                    <div class="flex flex-wrap items-center space-x-2 lg:space-x-4 text-xs lg:text-sm text-themed-secondary">
                                        <span>Type: {{ ucfirst(str_replace('_', ' ', $question['question_type'] ?? 'multiple_choice')) }}</span>
                                        <span>•</span>
                                        <span>{{ $question['points'] ?? 1 }} point(s)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Question Text with MathJax Support -->
                            <div class="mb-6 lg:mb-8">
                                <div class="bg-themed-tertiary border-l-4 border-accent-themed-primary p-4 lg:p-6 rounded-r-lg">
                                    <div class="text-base lg:text-lg text-themed-primary leading-relaxed prose prose-sm max-w-none dark:prose-invert math-content" 
                                         wire:key="question-{{ $question['id'] }}"
                                         x-init="$nextTick(() => { if (window.MathJax) { MathJax.typesetPromise([$el]).catch(err => console.error('MathJax error:', err)); } })">
                                        {!! $question['question_text'] ?? '' !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Answer Options with MathJax Support -->
                            <div class="space-y-3 lg:space-y-4">
                                @if(($question['question_type'] ?? '') === 'multiple_choice')
                                    @if(is_array($question['options']) && count($question['options']) > 0)
                                        @foreach($question['options'] as $optionIndex => $option)
                                            @if(trim(strip_tags($option)))
                                                <div class="border-2 rounded-lg transition-all duration-200 hover:border-accent-themed-primary
                                                    {{ isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex ? 'border-accent-themed-primary bg-accent-themed-primary bg-opacity-10' : 'border-themed-secondary hover:bg-themed-tertiary' }}">
                                                    <label class="flex items-start cursor-pointer p-3 lg:p-4 w-full">
                                                        <input class="form-radio mt-1 mr-3 lg:mr-4 h-4 w-4 lg:h-5 lg:w-5 text-accent-themed-primary border-themed-secondary focus:ring-accent-themed-primary"
                                                            type="radio" wire:click="saveAnswer({{ $question['id'] }}, {{ $optionIndex }})"
                                                            name="question_{{ $question['id'] }}" id="option_{{ $optionIndex }}"
                                                            @if(isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex) checked @endif
                                                            tabindex="{{ $optionIndex + 10 }}">
                                                        <div class="flex-1">
                                                            <span class="font-semibold text-accent-themed-primary mr-2 lg:mr-3 text-base lg:text-lg">{{ chr(65 + $optionIndex) }}.</span>
                                                            <div class="inline text-themed-primary text-sm lg:text-base prose prose-sm max-w-none dark:prose-invert math-content"
                                                                 wire:key="option-{{ $question['id'] }}-{{ $optionIndex }}"
                                                                 x-init="$nextTick(() => { if (window.MathJax) { MathJax.typesetPromise([$el]).catch(err => console.error('MathJax error:', err)); } })">
                                                                {!! $option !!}
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif

                                @elseif(($question['question_type'] ?? '') === 'true_false')
                                    <div class="space-y-3">
                                        <div class="border-2 rounded-lg transition-all duration-200 hover:border-accent-themed-primary
                                            {{ isset($answers[$question['id']]) && $answers[$question['id']] == 0 ? 'border-accent-themed-primary bg-accent-themed-primary bg-opacity-10' : 'border-themed-secondary hover:bg-themed-tertiary' }}">
                                            <label class="flex items-center cursor-pointer p-3 lg:p-4 w-full">
                                                <input class="form-radio mr-3 lg:mr-4 h-4 w-4 lg:h-5 lg:w-5 text-accent-themed-primary border-themed-secondary focus:ring-accent-themed-primary"
                                                    type="radio" wire:click="saveAnswer({{ $question['id'] }}, 0)"
                                                    name="question_{{ $question['id'] }}" @if(isset($answers[$question['id']]) && $answers[$question['id']] == 0) checked @endif
                                                    tabindex="10">
                                                <span class="font-semibold text-accent-themed-primary mr-2 lg:mr-3 text-base lg:text-lg">A.</span>
                                                <span class="text-themed-primary font-medium text-sm lg:text-base">True</span>
                                            </label>
                                        </div>
                                        <div class="border-2 rounded-lg transition-all duration-200 hover:border-accent-themed-primary
                                            {{ isset($answers[$question['id']]) && $answers[$question['id']] == 1 ? 'border-accent-themed-primary bg-accent-themed-primary bg-opacity-10' : 'border-themed-secondary hover:bg-themed-tertiary' }}">
                                            <label class="flex items-center cursor-pointer p-3 lg:p-4 w-full">
                                                <input class="form-radio mr-3 lg:mr-4 h-4 w-4 lg:h-5 lg:w-5 text-accent-themed-primary border-themed-secondary focus:ring-accent-themed-primary"
                                                    type="radio" wire:click="saveAnswer({{ $question['id'] }}, 1)"
                                                    name="question_{{ $question['id'] }}" @if(isset($answers[$question['id']]) && $answers[$question['id']] == 1) checked @endif
                                                    tabindex="11">
                                                <span class="font-semibold text-accent-themed-primary mr-2 lg:mr-3 text-base lg:text-lg">B.</span>
                                                <span class="text-themed-primary font-medium text-sm lg:text-base">False</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Navigation Footer -->
<!-- Navigation Footer - Updated with more prominent buttons -->
<div class="bg-themed-secondary border-t border-themed-secondary p-4 lg:p-6">
    <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0 mb-4">
        <!-- Previous Button - Made more prominent -->
        <button wire:click="previousQuestion" 
            class="w-full lg:w-auto order-2 lg:order-1 px-6 lg:px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center justify-center font-semibold text-base lg:text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200
            {{ !$this->canGoPrevious() ? 'opacity-50 cursor-not-allowed hover:bg-blue-600 hover:translate-y-0' : '' }}"
            {{ !$this->canGoPrevious() ? 'disabled' : '' }}
            tabindex="100">
            <i class="fas fa-arrow-left mr-3 text-xl"></i>Previous Question
        </button>

        <div class="text-center order-1 lg:order-2">
            <div class="text-xs lg:text-sm text-themed-secondary mb-1">Progress</div>
            <div class="font-bold text-themed-primary text-lg lg:text-xl">
                {{ $currentQuestionIndex + 1 }} / {{ count($questions) }}
            </div>
        </div>

        @if($this->isLastQuestion())
            <!-- Submit Button - Made more prominent -->
            <button wire:click="showSubmitConfirmation"
                class="w-full lg:w-auto order-3 bg-red-600 hover:bg-red-700 text-white px-8 lg:px-10 py-4 rounded-lg transition-colors flex items-center justify-center font-bold text-base lg:text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                tabindex="101">
                <i class="fas fa-paper-plane mr-3 text-xl"></i>Submit Exam
            </button>
        @else
            <!-- Next Button - Made more prominent -->
            <button wire:click="nextQuestion"
                class="w-full lg:w-auto order-3 bg-green-600 hover:bg-green-700 text-white px-6 lg:px-8 py-4 rounded-lg transition-colors flex items-center justify-center font-semibold text-base lg:text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200"
                tabindex="101">
                Next Question<i class="fas fa-arrow-right ml-3 text-xl"></i>
            </button>
        @endif
    </div>

    <!-- Progress bar remains the same -->
    <div class="w-full bg-themed-tertiary rounded-full h-3">
        <div class="bg-accent-themed-primary h-3 rounded-full transition-all duration-500 progress-animate"
            style="width: {{ $this->getProgressPercentage() }}%">
        </div>
    </div>
    <div class="flex justify-between text-xs text-themed-secondary mt-1">
        <span>{{ round($this->getProgressPercentage(), 1) }}% Complete</span>
        <span>{{ count($questions) - $this->getAnsweredQuestionsCount() }} remaining</span>
    </div>
</div>



            </div>
        </div>
    @endif

    <!-- Submit Confirmation Modal -->
    @if($showSubmitModal)
        <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
            <div class="bg-themed-secondary rounded-lg max-w-md w-full animate-pulse-scale border border-themed-primary">
                <div class="p-4 lg:p-6 border-b border-themed-secondary">
                    <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                        Confirm Exam Submission
                    </h3>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="mb-6">
                        <p class="text-themed-primary mb-4 text-sm lg:text-base">Are you sure you want to submit your exam? This action cannot be undone.</p>

                        <div class="bg-themed-tertiary rounded-lg p-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-themed-secondary">Questions Answered:</span>
                                <span class="font-semibold text-themed-primary">{{ $this->getAnsweredQuestionsCount() }} / {{ count($questions) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-themed-secondary">Time Remaining:</span>
                                <span class="font-semibold text-themed-primary" x-text="formatTime(timeRemaining)"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row space-y-3 lg:space-y-0 lg:space-x-4">
                        <button wire:click="cancelSubmission"
                            class="flex-1 px-4 py-3 bg-themed-tertiary text-themed-primary rounded-lg hover:bg-themed-tertiary hover:opacity-75 transition-colors font-medium text-sm lg:text-base border border-themed-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button wire:click="submitExam"
                            class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold text-sm lg:text-base">
                            <i class="fas fa-paper-plane mr-2"></i>Submit Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cbtExamInterface', () => ({
            timeRemaining: @js($timeRemaining ?? 0),
            totalQuestions: @js(count($questions)),
            answeredQuestions: @js($this->getAnsweredQuestionsCount()),
            timerInterval: null,

            init() {
                // Listen for Livewire events
                this.$wire.$on('startTimer', () => {
                    this.startTimer();
                });

                this.$wire.$on('examCompleted', () => {
                    this.stopTimer();
                    if (window.examSecurity) {
                        window.examSecurity.allowFullscreenExit();
                    }
                });

                this.$wire.$on('markExamStarted', () => {
                    if (window.examSecurity) {
                        window.examSecurity.markExamStarted();
                    }
                });

                this.$wire.$on('allowFullscreenExit', () => {
                    if (window.examSecurity) {
                        window.examSecurity.allowFullscreenExit();
                    }
                });

                // Listen for question changes to re-render MathJax
                this.$wire.$on('questionChanged', (data) => {
                    this.$nextTick(() => {
                        if (window.MathJax && window.MathJax.typesetPromise) {
                            window.MathJax.typesetPromise().catch(err => {
                                console.error('MathJax rendering error:', err);
                            });
                        }
                    });
                });

                // Start exam if already started
                if (@js($examStarted ?? false)) {
                    this.startTimer();
                    if (window.examSecurity) {
                        window.examSecurity.markExamStarted();
                    }
                }

                // Setup Livewire hooks for MathJax rendering
                Livewire.hook('morph.updated', ({ el, component }) => {
                    this.$nextTick(() => {
                        const mathElements = el.querySelectorAll('.math-content');
                        if (mathElements.length > 0 && window.MathJax && window.MathJax.typesetPromise) {
                            window.MathJax.typesetPromise(Array.from(mathElements)).catch(err => {
                                console.error('MathJax rendering error:', err);
                            });
                        }
                    });
                });
            },

            startTimer() {
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                }
                
                this.timerInterval = setInterval(() => {
                    if (this.timeRemaining > 0) {
                        this.timeRemaining--;
                    } else {
                        this.stopTimer();
                        this.$wire.call('submitExam');
                    }
                }, 1000);
            },

            stopTimer() {
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                    this.timerInterval = null;
                }
            },

            formatTime(seconds) {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;

                if (hours > 0) {
                    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                }
                return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
        }))
    });
    </script>
    @endpush

    @push('styles')
    <style>
        /* MathJax specific styling */
        .math-content mjx-container {
            display: inline-block !important;
            margin: 0.2em 0;
        }

        .math-content mjx-container[display="true"] {
            display: block !important;
            margin: 1em 0;
        }

        /* Prose styles for rich text content */
        .prose {
            max-width: none;
        }

        .prose img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }

        .prose pre {
            background-color: #1f2937;
            color: #f3f4f6;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
        }

        .prose code {
            background-color: #e5e7eb;
            padding: 0.2rem 0.4rem;
            border-radius: 0.25rem;
            font-size: 0.875em;
        }

        .dark .prose code {
            background-color: #374151;
            color: #f3f4f6;
        }

        .prose blockquote {
            border-left: 4px solid rgb(var(--accent-primary));
            padding-left: 1rem;
            font-style: italic;
            color: #6b7280;
        }

        .dark .prose blockquote {
            color: #9ca3af;
        }

        .prose ul, .prose ol {
            padding-left: 1.5rem;
        }

        .prose li {
            margin: 0.5rem 0;
        }

        .prose a {
            color: rgb(var(--accent-primary));
            text-decoration: underline;
        }

        .prose table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .prose th, .prose td {
            border: 1px solid #e5e7eb;
            padding: 0.5rem;
        }

        .dark .prose th, .dark .prose td {
            border-color: #374151;
        }

        .prose th {
            background-color: #f3f4f6;
            font-weight: 600;
        }

        .dark .prose th {
            background-color: #1f2937;
        }

        /* Form styles */
        .form-radio:checked {
            background-color: rgb(var(--accent-primary));
            border-color: rgb(var(--accent-primary));
        }

        .form-radio {
            border-radius: 50%;
        }

        .animate-pulse-scale {
            animation: pulseScale 0.3s ease-out;
        }

        @keyframes pulseScale {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Progress bar animation */
        .progress-animate {
            transition: width 0.5s ease-in-out;
        }

        /* Timer critical state */
        .timer-critical {
            animation: pulse-red 1s infinite;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
        }

        @keyframes pulse-red {
            0%, 100% {
                background-color: rgb(185 28 28);
                color: white;
            }
            50% {
                background-color: rgb(239 68 68);
                color: white;
            }
        }

        /* Mobile responsiveness */
        @media (max-width: 640px) {
            .grid-cols-8 {
                grid-template-columns: repeat(6, minmax(0, 1fr));
            }
            
            .math-content {
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            .math-content mjx-container {
                font-size: 0.9em;
            }
        }

        /* Dark mode prose adjustments */
        .dark .prose {
            color: #f3f4f6;
        }

        .dark .prose strong {
            color: #ffffff;
        }

        .dark .prose h1, 
        .dark .prose h2, 
        .dark .prose h3, 
        .dark .prose h4, 
        .dark .prose h5, 
        .dark .prose h6 {
            color: #ffffff;
        }
    </style>
    @endpush
</div>