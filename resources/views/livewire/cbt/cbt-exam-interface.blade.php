<div wire:ignore.self x-data="cbtExamInterface()" x-init="$nextTick(() => init())" class="h-screen flex flex-col">
    @if(!$examStarted)
        <!-- Pre-Exam Instructions -->
        <div class="flex-1 flex items-center justify-center p-6">
            <div class="w-full max-w-4xl">
                <div class="bg-white rounded-lg shadow-lg">
                    <div class="bg-yellow-500 text-yellow-900 px-6 py-4 rounded-t-lg">
                        <h1 class="text-xl font-semibold flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Exam Instructions
                        </h1>
                    </div>
                    <div class="p-8">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-blue-600 mb-2">{{ $assessment->title }}</h2>
                            @if($assessment->description)
                                <p class="text-gray-600 text-lg">{{ $assessment->description }}</p>
                            @endif
                        </div>

                        <!-- Exam Details -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                            <div class="text-center border border-gray-200 rounded-lg p-4">
                                <i class="fas fa-question-circle text-3xl text-blue-600 mb-2"></i>
                                <div class="font-semibold text-gray-900 text-xl">{{ count($questions) }}</div>
                                <div class="text-sm text-gray-500">Questions</div>
                            </div>
                            <div class="text-center border border-gray-200 rounded-lg p-4">
                                <i class="fas fa-clock text-3xl text-yellow-500 mb-2"></i>
                                <div class="font-semibold text-gray-900 text-xl">{{ $assessment->formatted_duration }}</div>
                                <div class="text-sm text-gray-500">Duration</div>
                            </div>
                            <div class="text-center border border-gray-200 rounded-lg p-4">
                                <i class="fas fa-trophy text-3xl text-green-500 mb-2"></i>
                                <div class="font-semibold text-gray-900 text-xl">{{ $assessment->pass_percentage }}%</div>
                                <div class="text-sm text-gray-500">Pass Mark</div>
                            </div>
                            <div class="text-center border border-gray-200 rounded-lg p-4">
                                <i class="fas fa-hashtag text-3xl text-purple-500 mb-2"></i>
                                <div class="font-semibold text-gray-900 text-xl"># {{ $attemptNumber }}</div>
                                <div class="text-sm text-gray-500">Attempt</div>
                            </div>
                        </div>

                        <!-- Security Instructions -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                            <h3 class="text-lg font-semibold text-red-800 mb-3 flex items-center">
                                <i class="fas fa-shield-alt mr-2"></i>Security Requirements
                            </h3>
                            <ul class="space-y-2 text-red-800 text-sm">
                                <li class="flex items-start">
                                    <span class="mr-2">⚠️</span>
                                    <span>This exam must be taken in <strong>fullscreen mode</strong></span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">⚠️</span>
                                    <span>Do not minimize, resize, or switch tabs during the exam</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">⚠️</span>
                                    <span>Right-click and keyboard shortcuts are disabled</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">⚠️</span>
                                    <span>All activities are monitored for security purposes</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">⚠️</span>
                                    <span>Multiple violations may result in automatic submission</span>
                                </li>
                            </ul>
                        </div>

                        <!-- General Instructions -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                            <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
                                <i class="fas fa-list-check mr-2"></i>Exam Instructions
                            </h3>
                            <ul class="space-y-2 text-blue-800 text-sm">
                                <li class="flex items-start">
                                    <span class="mr-2">📝</span>
                                    <span>Read each question carefully before selecting your answer</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">⏱️</span>
                                    <span>You have <strong>{{ $assessment->formatted_duration }}</strong> to complete all questions</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">🎯</span>
                                    <span>You need <strong>{{ $assessment->pass_percentage }}%</strong> or higher to pass</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">🚩</span>
                                    <span>You can flag questions for review using the flag button</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">💾</span>
                                    <span>Your answers are automatically saved as you progress</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">🔒</span>
                                    <span>Once submitted, you cannot change your answers</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Start Exam Button -->
                        <div class="text-center">
                            <button wire:click="startExam" 
                                class="bg-green-600 hover:bg-green-700 text-white px-12 py-4 rounded-lg text-xl font-semibold transition-colors flex items-center mx-auto shadow-lg">
                                <i class="fas fa-play mr-3"></i>Start CBT Exam
                            </button>
                            <p class="text-gray-500 text-sm mt-4">The timer will start immediately when you click this button</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif($examCompleted)
        <!-- Results Page -->
        <div class="flex-1 flex items-center justify-center p-6">
            <div class="w-full max-w-4xl">
                <div class="bg-white rounded-lg shadow-lg">
                    <div class="px-6 py-4 rounded-t-lg {{ $results['passed'] ? 'bg-green-600' : 'bg-red-600' }} text-white">
                        <h1 class="text-2xl font-semibold flex items-center justify-center">
                            <i class="fas {{ $results['passed'] ? 'fa-check-circle' : 'fa-times-circle' }} mr-3"></i>
                            Exam {{ $results['passed'] ? 'COMPLETED - PASSED' : 'COMPLETED - FAILED' }}
                        </h1>
                    </div>
                    <div class="p-8">
                        <div class="text-center mb-8">
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ $assessment->title }}</h2>
                            <p class="text-gray-600">Attempt #{{ $results['attempt_number'] }}</p>
                        </div>

                        <!-- Results Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center mb-8">
                            <div class="border rounded-lg p-6 {{ $results['passed'] ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50' }}">
                                <div class="text-4xl font-bold mb-2 {{ $results['passed'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $results['percentage'] }}%
                                </div>
                                <div class="text-sm text-gray-600 font-medium">Final Score</div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="text-3xl font-bold text-purple-600 mb-2">{{ $results['total_points'] }}</div>
                                <div class="text-sm text-gray-600 font-medium">Points</div>
                                <div class="text-xs text-gray-500">out of {{ $results['max_points'] }}</div>
                            </div>
                            <div class="border border-gray-200 rounded-lg p-6">
                                <div class="text-3xl font-bold text-gray-600 mb-2">{{ gmdate('H:i:s', $results['time_spent']) }}</div>
                                <div class="text-sm text-gray-600 font-medium">Time Used</div>
                            </div>
                        </div>

                        <!-- Pass/Fail Message -->
                        <div class="text-center mb-8">
                            @if($results['passed'])
                                <div class="bg-green-100 border border-green-300 text-green-800 px-6 py-4 rounded-lg">
                                    <h3 class="text-xl font-semibold mb-2">🎉 Congratulations!</h3>
                                    <p>You have successfully passed this assessment with {{ $results['percentage'] }}%</p>
                                </div>
                            @else
                                <div class="bg-red-100 border border-red-300 text-red-800 px-6 py-4 rounded-lg">
                                    <h3 class="text-xl font-semibold mb-2">Assessment Not Passed</h3>
                                    <p>You scored {{ $results['percentage'] }}%. You need {{ $assessment->pass_percentage }}% to pass.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Security Summary -->
                        @if(count($securityViolations) > 0)
                            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-6 py-4 rounded-lg mb-6">
                                <h4 class="font-semibold mb-2 flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Security Monitoring Summary
                                </h4>
                                <p>{{ count($securityViolations) }} security event(s) were recorded during your exam.</p>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            @if(!$results['passed'])
                                <button wire:click="retakeExam" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-8 py-3 rounded-lg text-lg font-semibold transition-colors flex items-center justify-center">
                                    <i class="fas fa-redo mr-2"></i>Retake Exam
                                </button>
                            @endif
                            
                            <a href="{{ route('cbt.exams') }}" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg text-lg font-semibold transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-list mr-2"></i>Back to Exams
                            </a>
                            
                            <a href="{{ route('student.dashboard') }}" 
                                class="bg-gray-500 hover:bg-gray-600 text-white px-8 py-3 rounded-lg text-lg font-semibold transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-home mr-2"></i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Active Exam Interface -->
        <div class="flex h-screen">
            <!-- Questions Sidebar -->
            <div class="w-80 bg-gray-50 border-r border-gray-200 flex flex-col">
                <!-- Sidebar Header -->
                <div class="p-4 border-b border-gray-200 bg-white">
                    <h3 class="font-semibold text-gray-900 mb-1">Question Navigator</h3>
                    <div class="text-sm text-gray-600">
                        {{ $this->getAnsweredQuestionsCount() }} of {{ count($questions) }} answered
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                            style="width: {{ count($questions) > 0 ? ($this->getAnsweredQuestionsCount() / count($questions)) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

                <!-- Question Grid -->
                <div class="p-4 flex-1 overflow-y-auto">
                    <div class="grid grid-cols-5 gap-2">
                        @foreach($questions as $index => $question)
                            <button wire:click="goToQuestion({{ $index }})" 
                                class="relative aspect-square flex items-center justify-center text-sm font-bold rounded-lg transition-all
                                @if($currentQuestionIndex === $index) 
                                    bg-blue-600 text-white ring-2 ring-blue-300
                                @elseif(isset($answers[$question['id']]) && $answers[$question['id']] !== null) 
                                    bg-green-500 text-white
                                @else 
                                    bg-white border-2 border-gray-300 text-gray-700 hover:border-blue-300 hover:bg-blue-50
                                @endif">
                                {{ $index + 1 }}
                                @if($this->isQuestionFlagged($index))
                                    <i class="fas fa-flag absolute -top-1 -right-1 text-yellow-400 text-xs"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    <!-- Legend -->
                    <div class="mt-6 space-y-2 text-xs">
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-blue-600 rounded mr-2"></div>
                            <span class="text-gray-600">Current</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                            <span class="text-gray-600">Answered</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 bg-white border-2 border-gray-300 rounded mr-2"></div>
                            <span class="text-gray-600">Not Answered</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-flag text-yellow-400 text-xs mr-2 w-4"></i>
                            <span class="text-gray-600">Flagged</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Exam Area -->
            <div class="flex-1 flex flex-col">
                <!-- Timer Header -->
                <div class="bg-gray-900 text-white p-4 flex justify-between items-center">
                    <div class="flex items-center space-x-6">
                        <div>
                            <h1 class="font-semibold text-lg">{{ $assessment->title }}</h1>
                            <div class="text-sm text-gray-300">
                                Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Timer -->
                        <div class="flex items-center bg-gray-800 px-4 py-2 rounded-lg" 
                            :class="timeRemaining <= 300 ? 'timer-critical' : ''">
                            <i class="fas fa-clock mr-2"></i>
                            <span x-text="formatTime(timeRemaining)" class="font-mono text-xl font-bold"></span>
                        </div>
                        
                        <!-- Flag Button -->
                        <button wire:click="toggleFlag({{ $currentQuestionIndex }})"
                            class="px-3 py-2 rounded-lg transition-colors flex items-center
                            {{ $this->isQuestionFlagged($currentQuestionIndex) ? 'bg-yellow-500 text-yellow-900' : 'border border-gray-400 text-gray-300 hover:bg-gray-700' }}">
                            <i class="fas fa-flag mr-1"></i>
                            <span class="hidden sm:inline">Flag</span>
                        </button>
                    </div>
                </div>

                <!-- Question Content -->
                <div class="flex-1 overflow-y-auto bg-white">
                    @if($this->getCurrentQuestion())
                        @php $question = $this->getCurrentQuestion(); @endphp
                        <div class="p-8 max-w-4xl mx-auto">
                            <!-- Question Header -->
                            <div class="flex justify-between items-start mb-8">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900 mb-2">
                                        Question {{ $currentQuestionIndex + 1 }}
                                    </h2>
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>Type: {{ ucfirst(str_replace('_', ' ', $question['question_type'] ?? 'multiple_choice')) }}</span>
                                        <span>•</span>
                                        <span>{{ $question['points'] ?? 1 }} point(s)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-8">
                                <div class="bg-gray-50 border-l-4 border-blue-500 p-6 rounded-r-lg">
                                    <p class="text-lg text-gray-800 leading-relaxed font-medium">
                                        {!! nl2br(e($question['question_text'] ?? '')) !!}
                                    </p>
                                </div>
                            </div>

                            <!-- Answer Options -->
                            <div class="space-y-4">
                                @if(($question['question_type'] ?? '') === 'multiple_choice')
                                    @if(is_array($question['options']) && count($question['options']) > 0)
                                        @foreach($question['options'] as $optionIndex => $option)
                                            @if(trim($option))
                                                <div class="border-2 rounded-lg transition-all duration-200 hover:border-blue-300
                                                    {{ isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                                    <label class="flex items-start cursor-pointer p-4 w-full">
                                                        <input class="form-radio mt-1 mr-4 h-5 w-5 text-blue-600 border-gray-300" 
                                                            type="radio"
                                                            wire:click="saveAnswer({{ $question['id'] }}, {{ $optionIndex }})"
                                                            name="question_{{ $question['id'] }}" 
                                                            id="option_{{ $optionIndex }}"
                                                            @if(isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex) checked @endif>
                                                        <div class="flex-1">
                                                            <span class="font-semibold text-blue-600 mr-3 text-lg">{{ chr(65 + $optionIndex) }}.</span>
                                                            <span class="text-gray-800">{{ $option }}</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                <span>No options available for this question. Please contact your instructor.</span>
                                            </div>
                                        </div>
                                    @endif

                                @elseif(($question['question_type'] ?? '') === 'true_false')
                                    <div class="space-y-3">
                                        <div class="border-2 rounded-lg transition-all duration-200 hover:border-blue-300
                                            {{ isset($answers[$question['id']]) && $answers[$question['id']] == 0 ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                            <label class="flex items-center cursor-pointer p-4 w-full">
                                                <input class="form-radio mr-4 h-5 w-5 text-blue-600 border-gray-300" 
                                                    type="radio"
                                                    wire:click="saveAnswer({{ $question['id'] }}, 0)"
                                                    name="question_{{ $question['id'] }}"
                                                    @if(isset($answers[$question['id']]) && $answers[$question['id']] == 0) checked @endif>
                                                <span class="font-semibold text-blue-600 mr-3 text-lg">A.</span>
                                                <span class="text-gray-800 font-medium">True</span>
                                            </label>
                                        </div>
                                        <div class="border-2 rounded-lg transition-all duration-200 hover:border-blue-300
                                            {{ isset($answers[$question['id']]) && $answers[$question['id']] == 1 ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                                            <label class="flex items-center cursor-pointer p-4 w-full">
                                                <input class="form-radio mr-4 h-5 w-5 text-blue-600 border-gray-300" 
                                                    type="radio"
                                                    wire:click="saveAnswer({{ $question['id'] }}, 1)"
                                                    name="question_{{ $question['id'] }}"
                                                    @if(isset($answers[$question['id']]) && $answers[$question['id']] == 1) checked @endif>
                                                <span class="font-semibold text-blue-600 mr-3 text-lg">B.</span>
                                                <span class="text-gray-800 font-medium">False</span>
                                            </label>
                                        </div>
                                    </div>

                                @elseif(($question['question_type'] ?? '') === 'short_answer')
                                    <div class="border-2 border-gray-200 rounded-lg focus-within:border-blue-500">
                                        <textarea wire:model.live.debounce.500ms="answers.{{ $question['id'] }}" 
                                            class="w-full px-4 py-3 border-0 rounded-lg focus:outline-none focus:ring-0 resize-none" 
                                            rows="4" 
                                            placeholder="Type your answer here..."
                                            maxlength="500"></textarea>
                                        <div class="px-4 py-2 bg-gray-50 text-xs text-gray-500 rounded-b-lg">
                                            Character limit: 500
                                        </div>
                                    </div>

                                @elseif(($question['question_type'] ?? '') === 'essay')
                                    <div class="border-2 border-gray-200 rounded-lg focus-within:border-blue-500">
                                        <textarea wire:model.live.debounce.500ms="answers.{{ $question['id'] }}" 
                                            class="w-full px-4 py-3 border-0 rounded-lg focus:outline-none focus:ring-0 resize-none" 
                                            rows="8" 
                                            placeholder="Write your essay here..."
                                            maxlength="2000"></textarea>
                                        <div class="px-4 py-2 bg-gray-50 text-xs text-gray-500 rounded-b-lg">
                                            Character limit: 2000
                                        </div>
                                    </div>

                                @else
                                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span>Unknown question type: {{ $question['question_type'] ?? 'undefined' }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-8 text-center">
                            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span>Unable to load the current question. Please contact support.</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Navigation Footer -->
                <div class="bg-white border-t border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <button wire:click="previousQuestion" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center font-medium
                            {{ !$this->canGoPrevious() ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ !$this->canGoPrevious() ? 'disabled' : '' }}>
                            <i class="fas fa-arrow-left mr-2"></i>Previous
                        </button>

                        <div class="text-center">
                            <div class="text-sm text-gray-500 mb-1">Progress</div>
                            <div class="font-semibold text-gray-900">
                                {{ $currentQuestionIndex + 1 }} / {{ count($questions) }}
                            </div>
                        </div>

                        @if($this->isLastQuestion())
                            <button wire:click="submitExam"
                                wire:confirm="Are you sure you want to submit your exam? This action cannot be undone."
                                class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg transition-colors flex items-center font-semibold">
                                <i class="fas fa-paper-plane mr-2"></i>Submit Exam
                            </button>
                        @else
                            <button wire:click="nextQuestion" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center font-medium">
                                Next<i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-300" 
                            style="width: {{ $this->getProgressPercentage() }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Alpine.js Component Script -->
    <script>
        document.addEventListener('livewire:init', () => {
     function cbtExamInterface() {
    return {
        timeRemaining: @js($timeRemaining ?? 0),
        timerInterval: null,
        componentInstance: null,

        init() {
            // Wait for Livewire to be ready before setting up event listeners
            this.$nextTick(() => {
                this.setupLivewireListeners();
            });
        },

        setupLivewireListeners() {
            // Ensure $wire is available before setting up listeners
            if (!this.$wire) {
                console.warn('$wire not available, retrying in 100ms...');
                setTimeout(() => this.setupLivewireListeners(), 100);
                return;
            }

            try {
                // Listen for Livewire events using v3 syntax
                this.$wire.$on('startTimer', () => {
                    this.startTimer();
                    if (typeof window.markExamStarted === 'function') {
                        window.markExamStarted();
                    }
                });

                this.$wire.$on('examCompleted', () => {
                    this.stopTimer();
                });

                this.$wire.$on('markExamStarted', () => {
                    if (typeof window.markExamStarted === 'function') {
                        window.markExamStarted();
                    }
                });

                this.$wire.$on('answerSaved', (data) => {
                    console.log('Answer saved for question:', data[0]);
                });

                console.log('Livewire event listeners setup successfully');
            } catch (error) {
                console.error('Error setting up Livewire listeners:', error);
                // Retry after a short delay
                setTimeout(() => this.setupLivewireListeners(), 500);
            }
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
                    // Ensure $wire is available before calling submitExam
                    if (this.$wire && typeof this.$wire.call === 'function') {
                        this.$wire.call('submitExam');
                    }
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
    }
}
});
    </script>

    <style>
        .form-radio:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .form-radio {
            border-radius: 50%;
        }

        .timer-critical {
            animation: pulse-red 1s infinite;
        }

        @keyframes pulse-red {
            0%, 100% {
                background-color: rgb(185 28 28);
            }
            50% {
                background-color: rgb(239 68 68);
            }
        }
    </style>
