<div x-data="cbtExamInterface" class="h-screen flex flex-col bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

    @if(!$examStarted)
        <!-- Pre-Exam Instructions (Mobile Responsive) -->
        <div class="flex-1 flex items-center justify-center p-4 md:p-6">
            <div class="w-full max-w-4xl">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700">
                    <div class="bg-yellow-500 dark:bg-yellow-600 text-yellow-900 dark:text-yellow-100 px-4 md:px-6 py-4 rounded-t-lg">
                        <h1 class="text-lg md:text-xl font-semibold flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Exam Instructions
                        </h1>
                    </div>
                    <div class="p-4 md:p-8">
                        <div class="text-center mb-6 md:mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">{{ $assessment->title }}</h2>
                            @if($assessment->description)
                                <p class="text-gray-600 dark:text-gray-400 text-base md:text-lg">{{ $assessment->description }}</p>
                            @endif
                        </div>

                        <!-- Exam Details (Mobile Responsive Grid) -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
                            <div class="text-center border border-gray-200 dark:border-gray-700 rounded-lg p-3 md:p-4 bg-gray-50 dark:bg-gray-900">
                                <i class="fas fa-question-circle text-2xl md:text-3xl text-blue-600 dark:text-blue-400 mb-2"></i>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-lg md:text-xl">{{ count($questions) }}</div>
                                <div class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Questions</div>
                            </div>
                            <div class="text-center border border-gray-200 dark:border-gray-700 rounded-lg p-3 md:p-4 bg-gray-50 dark:bg-gray-900">
                                <i class="fas fa-clock text-2xl md:text-3xl text-yellow-500 dark:text-yellow-400 mb-2"></i>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-lg md:text-xl">{{ $assessment->formatted_duration }}</div>
                                <div class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Duration</div>
                            </div>
                            <div class="text-center border border-gray-200 dark:border-gray-700 rounded-lg p-3 md:p-4 bg-gray-50 dark:bg-gray-900">
                                <i class="fas fa-trophy text-2xl md:text-3xl text-green-500 dark:text-green-400 mb-2"></i>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-lg md:text-xl">{{ $assessment->pass_percentage }}%</div>
                                <div class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Pass Mark</div>
                            </div>
                            <div class="text-center border border-gray-200 dark:border-gray-700 rounded-lg p-3 md:p-4 bg-gray-50 dark:bg-gray-900">
                                <i class="fas fa-hashtag text-2xl md:text-3xl text-purple-500 dark:text-purple-400 mb-2"></i>
                                <div class="font-semibold text-gray-900 dark:text-gray-100 text-lg md:text-xl"># {{ $attemptNumber }}</div>
                                <div class="text-xs md:text-sm text-gray-500 dark:text-gray-400">Attempt</div>
                            </div>
                        </div>

                        <!-- Accessibility Notice -->
                        <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-600 rounded-lg p-4 md:p-6 mb-4 md:mb-6">
                            <h3 class="text-base md:text-lg font-semibold text-blue-800 dark:text-blue-300 mb-3 flex items-center">
                                <i class="fas fa-universal-access mr-2"></i>Accessibility Features Available
                            </h3>
                            <ul class="space-y-2 text-blue-800 dark:text-blue-300 text-sm">
                                <li class="flex items-start">
                                    <span class="mr-2">✓</span>
                                    <span><strong>Font Size Control:</strong> Adjustable text size (Ctrl + / Ctrl -)</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">✓</span>
                                    <span><strong>Keyboard Navigation:</strong> Navigate with arrow keys (when enabled)</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">✓</span>
                                    <span><strong>Dark Mode & High Contrast:</strong> Customizable display options</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">✓</span>
                                    <span><strong>Progress Tracking:</strong> Real-time progress and time estimates</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Security Instructions -->
                        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-600 rounded-lg p-4 md:p-6 mb-4 md:mb-6">
                            <h3 class="text-base md:text-lg font-semibold text-red-800 dark:text-red-300 mb-3 flex items-center">
                                <i class="fas fa-shield-alt mr-2"></i>Security Requirements
                            </h3>
                            <ul class="space-y-2 text-red-800 dark:text-red-300 text-sm">
                                <li class="flex items-start">
                                    <span class="mr-2">⚠️</span>
                                    <span>Exam will enter <strong>fullscreen mode</strong> and <strong>browser lockdown</strong></span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">⚠️</span>
                                    <span>Switching applications or tabs will trigger security warnings</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">⚠️</span>
                                    <span>Multiple violations may result in automatic submission</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">⚠️</span>
                                    <span>All activities are monitored and logged</span>
                                </li>
                            </ul>
                        </div>

                        <!-- General Instructions -->
                        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-600 rounded-lg p-4 md:p-6 mb-6 md:mb-8">
                            <h3 class="text-base md:text-lg font-semibold text-green-800 dark:text-green-300 mb-3 flex items-center">
                                <i class="fas fa-list-check mr-2"></i>Exam Instructions
                            </h3>
                            <ul class="space-y-2 text-green-800 dark:text-green-300 text-sm">
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
                                    <span>Flag questions for review using the flag button (F key)</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">💾</span>
                                    <span>Your answers are automatically saved as you progress</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">📱</span>
                                    <span>On mobile: swipe left/right to navigate questions</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Start Exam Button -->
                        <div class="text-center">
                            <button wire:click="startExam" wire:loading.attr="disabled" wire:target="startExam"
                                class="bg-green-600 hover:bg-green-700 disabled:bg-green-400 text-white px-8 md:px-12 py-3 md:py-4 rounded-lg text-lg md:text-xl font-semibold transition-colors flex items-center mx-auto shadow-lg">
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
                            <p class="text-gray-500 dark:text-gray-400 text-sm mt-4">
                                Security features will activate when you start the exam
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif($examCompleted)
        <!-- Results Page (Mobile Responsive) -->
        <div class="flex-1 flex items-center justify-center p-4 md:p-6">
            <div class="w-full max-w-4xl">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border dark:border-gray-700">
                    <div class="px-4 md:px-6 py-4 rounded-t-lg {{ $results['passed'] ? 'bg-green-600' : 'bg-red-600' }} text-white">
                        <h1 class="text-xl md:text-2xl font-semibold flex items-center justify-center">
                            <i class="fas {{ $results['passed'] ? 'fa-check-circle' : 'fa-times-circle' }} mr-3"></i>
                            <span class="text-center">Exam {{ $results['passed'] ? 'COMPLETED - PASSED' : 'COMPLETED - FAILED' }}</span>
                        </h1>
                    </div>
                    <div class="p-4 md:p-8">
                        <div class="text-center mb-6 md:mb-8">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $assessment->title }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">Attempt #{{ $results['attempt_number'] }}</p>
                        </div>

                        <!-- Results Grid (Mobile Responsive) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 text-center mb-6 md:mb-8">
                            <div class="border rounded-lg p-4 md:p-6 {{ $results['passed'] ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20' }}">
                                <div class="text-3xl md:text-4xl font-bold mb-2 {{ $results['passed'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $results['percentage'] }}%
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Final Score</div>
                            </div>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 md:p-6 bg-gray-50 dark:bg-gray-900">
                                <div class="text-2xl md:text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">{{ $results['total_points'] }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Points</div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">out of {{ $results['max_points'] }}</div>
                            </div>
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 md:p-6 bg-gray-50 dark:bg-gray-900">
                                <div class="text-2xl md:text-3xl font-bold text-gray-600 dark:text-gray-400 mb-2">{{ gmdate('H:i:s', $results['time_spent']) }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">Time Used</div>
                            </div>
                        </div>

                        <!-- Pass/Fail Message -->
                        <div class="text-center mb-6 md:mb-8">
                            @if($results['passed'])
                                <div class="bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-600 text-green-800 dark:text-green-300 px-4 md:px-6 py-4 rounded-lg">
                                    <h3 class="text-lg md:text-xl font-semibold mb-2">🎉 Congratulations!</h3>
                                    <p>You have successfully passed this assessment with {{ $results['percentage'] }}%</p>
                                </div>
                            @else
                                <div class="bg-red-100 dark:bg-red-900/30 border border-red-300 dark:border-red-600 text-red-800 dark:text-red-300 px-4 md:px-6 py-4 rounded-lg">
                                    <h3 class="text-lg md:text-xl font-semibold mb-2">Assessment Not Passed</h3>
                                    <p>You scored {{ $results['percentage'] }}%. You need {{ $assessment->pass_percentage }}% to pass.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons (Mobile Responsive) -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            @if(!$results['passed'])
                                <button wire:click="retakeExam"
                                    class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-6 md:px-8 py-3 rounded-lg text-lg font-semibold transition-colors flex items-center justify-center">
                                    <i class="fas fa-redo mr-2"></i>Retake Exam
                                </button>
                            @endif

                            <a href="{{ route('cbt.exams') }}"
                                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 md:px-8 py-3 rounded-lg text-lg font-semibold transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-list mr-2"></i>Back to Exams
                            </a>

                            <a href="{{ route('dashboard') }}"
                                class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white px-6 md:px-8 py-3 rounded-lg text-lg font-semibold transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-home mr-2"></i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Active Exam Interface (Mobile Responsive) -->
        <div class="flex flex-col lg:flex-row h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Questions Sidebar (Responsive) -->
            <div class="w-full lg:w-80 bg-gray-50 dark:bg-gray-800 border-b lg:border-r lg:border-b-0 border-gray-200 dark:border-gray-700 flex flex-col">
                <!-- Sidebar Header -->
                <div class="p-3 lg:p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-1 text-sm lg:text-base">Question Navigator</h3>
                    <div class="text-xs lg:text-sm text-gray-600 dark:text-gray-400">
                        {{ $this->getAnsweredQuestionsCount() }} of {{ count($questions) }} answered
                    </div>
                    <!-- Enhanced Progress Bar -->
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-500 progress-animate"
                            style="width: {{ count($questions) > 0 ? ($this->getAnsweredQuestionsCount() / count($questions)) * 100 : 0 }}%">
                        </div>
                    </div>
                    <!-- Time Estimate -->
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-show="timeRemaining > 0">
                        <span x-text="`Est. ${Math.ceil((timeRemaining / 60) / (totalQuestions - answeredQuestions || 1))}min per remaining question`"></span>
                    </div>
                </div>

                <!-- Question Grid -->
                <div class="p-2 lg:p-4 flex-1 overflow-y-auto">
                    <div class="grid grid-cols-8 lg:grid-cols-5 gap-1 lg:gap-2">
                        @foreach($questions as $index => $question)
                            <button wire:click="goToQuestion({{ $index }})" 
                                class="relative aspect-square flex items-center justify-center text-xs lg:text-sm font-bold rounded-lg transition-all focus:ring-2 focus:ring-blue-500
                                @if($currentQuestionIndex === $index) 
                                    bg-blue-600 text-white ring-2 ring-blue-300
                                @elseif(isset($answers[$question['id']]) && $answers[$question['id']] !== null) 
                                    bg-green-500 text-white
                                @else 
                                    bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/20
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

                    <!-- Legend -->
                    <div class="mt-4 lg:mt-6 space-y-2 text-xs">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-600 rounded mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-400">Current</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-400">Answered</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded mr-2"></div>
                            <span class="text-gray-600 dark:text-gray-400">Not Answered</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-flag text-yellow-400 text-xs mr-2 w-3"></i>
                            <span class="text-gray-600 dark:text-gray-400">Flagged</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Exam Area -->
            <div class="flex-1 flex flex-col">
                <!-- Timer Header (Mobile Responsive) -->
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
                        <!-- Timer (Enhanced with estimates) -->
                        <div class="flex items-center bg-gray-800 dark:bg-gray-700 px-3 lg:px-4 py-2 rounded-lg flex-1 lg:flex-none"
                            x-bind:class="timeRemaining <= 300 ? 'timer-critical' : ''">
                            <i class="fas fa-clock mr-2"></i>
                            <div class="text-center">
                                <div x-text="formatTime(timeRemaining)" class="font-mono text-base lg:text-xl font-bold"></div>
                                <div class="text-xs text-gray-300 dark:text-gray-400" x-show="timeRemaining > 300">
                                    <span x-text="`${Math.floor(timeRemaining / 60 / (totalQuestions - answeredQuestions || 1))}min/q left`"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Flag Button -->
                        <button wire:click="toggleFlag({{ $currentQuestionIndex }})"
                            class="px-3 py-2 rounded-lg transition-colors flex items-center text-sm
                            {{ $this->isQuestionFlagged($currentQuestionIndex) ? 'bg-yellow-500 text-yellow-900' : 'border border-gray-400 text-gray-300 hover:bg-gray-700 dark:hover:bg-gray-600' }}"
                            title="Flag for review (F key)">
                            <i class="fas fa-flag mr-1"></i>
                            <span class="hidden sm:inline">Flag</span>
                        </button>
                    </div>
                </div>

                <!-- Question Content (Mobile Responsive) -->
                <div class="flex-1 overflow-y-auto bg-white dark:bg-gray-800">
                    @if($this->getCurrentQuestion())
                        @php $question = $this->getCurrentQuestion(); @endphp
                        <div class="p-4 lg:p-8 max-w-4xl mx-auto">
                            <!-- Question Header -->
                            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6 lg:mb-8">
                                <div>
                                    <h2 class="text-xl lg:text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                        Question {{ $currentQuestionIndex + 1 }}
                                    </h2>
                                    <div class="flex flex-wrap items-center space-x-2 lg:space-x-4 text-xs lg:text-sm text-gray-500 dark:text-gray-400">
                                        <span>Type: {{ ucfirst(str_replace('_', ' ', $question['question_type'] ?? 'multiple_choice')) }}</span>
                                        <span>•</span>
                                        <span>{{ $question['points'] ?? 1 }} point(s)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-6 lg:mb-8">
                                <div class="bg-gray-50 dark:bg-gray-900 border-l-4 border-blue-500 dark:border-blue-400 p-4 lg:p-6 rounded-r-lg">
                                    <p class="text-base lg:text-lg text-gray-800 dark:text-gray-200 leading-relaxed font-medium">
                                        {!! nl2br(e($question['question_text'] ?? '')) !!}
                                    </p>
                                </div>
                            </div>

                            <!-- Answer Options (Mobile Responsive) -->
                            <div class="space-y-3 lg:space-y-4">
                                @if(($question['question_type'] ?? '') === 'multiple_choice')
                                    @if(is_array($question['options']) && count($question['options']) > 0)
                                        @foreach($question['options'] as $optionIndex => $option)
                                            @if(trim($option))
                                                <div class="border-2 rounded-lg transition-all duration-200 hover:border-blue-300 dark:hover:border-blue-500
                                                    {{ isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-400' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                                    <label class="flex items-start cursor-pointer p-3 lg:p-4 w-full">
                                                        <input class="form-radio mt-1 mr-3 lg:mr-4 h-4 w-4 lg:h-5 lg:w-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                                                            type="radio" wire:click="saveAnswer({{ $question['id'] }}, {{ $optionIndex }})"
                                                            name="question_{{ $question['id'] }}" id="option_{{ $optionIndex }}"
                                                            @if(isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex) checked @endif
                                                            tabindex="{{ $optionIndex + 10 }}">
                                                        <div class="flex-1">
                                                            <span class="font-semibold text-blue-600 dark:text-blue-400 mr-2 lg:mr-3 text-base lg:text-lg">{{ chr(65 + $optionIndex) }}.</span>
                                                            <span class="text-gray-800 dark:text-gray-200 text-sm lg:text-base">{{ $option }}</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-600 text-yellow-800 dark:text-yellow-300 px-4 py-3 rounded-lg">
                                            <div class="flex items-center">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                                <span>No options available for this question. Please contact your instructor.</span>
                                            </div>
                                        </div>
                                    @endif

                                @elseif(($question['question_type'] ?? '') === 'true_false')
                                    <div class="space-y-3">
                                        <div class="border-2 rounded-lg transition-all duration-200 hover:border-blue-300 dark:hover:border-blue-500
                                            {{ isset($answers[$question['id']]) && $answers[$question['id']] == 0 ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-400' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                            <label class="flex items-center cursor-pointer p-3 lg:p-4 w-full">
                                                <input class="form-radio mr-3 lg:mr-4 h-4 w-4 lg:h-5 lg:w-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                                                    type="radio" wire:click="saveAnswer({{ $question['id'] }}, 0)"
                                                    name="question_{{ $question['id'] }}" @if(isset($answers[$question['id']]) && $answers[$question['id']] == 0) checked @endif
                                                    tabindex="10">
                                                <span class="font-semibold text-blue-600 dark:text-blue-400 mr-2 lg:mr-3 text-base lg:text-lg">A.</span>
                                                <span class="text-gray-800 dark:text-gray-200 font-medium text-sm lg:text-base">True</span>
                                            </label>
                                        </div>
                                        <div class="border-2 rounded-lg transition-all duration-200 hover:border-blue-300 dark:hover:border-blue-500
                                            {{ isset($answers[$question['id']]) && $answers[$question['id']] == 1 ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-400' : 'border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                            <label class="flex items-center cursor-pointer p-3 lg:p-4 w-full">
                                                <input class="form-radio mr-3 lg:mr-4 h-4 w-4 lg:h-5 lg:w-5 text-blue-600 border-gray-300 focus:ring-blue-500"
                                                    type="radio" wire:click="saveAnswer({{ $question['id'] }}, 1)"
                                                    name="question_{{ $question['id'] }}" @if(isset($answers[$question['id']]) && $answers[$question['id']] == 1) checked @endif
                                                    tabindex="11">
                                                <span class="font-semibold text-blue-600 dark:text-blue-400 mr-2 lg:mr-3 text-base lg:text-lg">B.</span>
                                                <span class="text-gray-800 dark:text-gray-200 font-medium text-sm lg:text-base">False</span>
                                            </label>
                                        </div>
                                    </div>

                                @else
                                    <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-600 text-yellow-800 dark:text-yellow-300 px-4 py-3 rounded-lg">
                                        <div class="flex items-center">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span>Question type "{{ $question['question_type'] ?? 'undefined' }}" is not supported in this interface.</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-4 lg:p-8 text-center">
                            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-600 text-red-800 dark:text-red-300 px-6 py-4 rounded-lg">
                                <div class="flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    <span>Unable to load the current question. Please contact support.</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Navigation Footer (Mobile Responsive) -->
                <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 lg:p-6">
                    <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0 mb-4">
                        <button wire:click="previousQuestion" 
                            class="w-full lg:w-auto order-2 lg:order-1 px-4 lg:px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors flex items-center justify-center font-medium text-sm lg:text-base
                            {{ !$this->canGoPrevious() ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ !$this->canGoPrevious() ? 'disabled' : '' }}
                            tabindex="100">
                            <i class="fas fa-arrow-left mr-2"></i>Previous
                        </button>

                        <div class="text-center order-1 lg:order-2">
                            <div class="text-xs lg:text-sm text-gray-500 dark:text-gray-400 mb-1">Progress</div>
                            <div class="font-semibold text-gray-900 dark:text-gray-100 text-sm lg:text-base">
                                {{ $currentQuestionIndex + 1 }} / {{ count($questions) }}
                            </div>
                        </div>

                        @if($this->isLastQuestion())
                            <button wire:click="showSubmitConfirmation"
                                class="w-full lg:w-auto order-3 bg-red-600 hover:bg-red-700 text-white px-6 lg:px-8 py-3 rounded-lg transition-colors flex items-center justify-center font-semibold text-sm lg:text-base"
                                tabindex="101">
                                <i class="fas fa-paper-plane mr-2"></i>Submit Exam
                            </button>
                        @else
                            <button wire:click="nextQuestion"
                                class="w-full lg:w-auto order-3 bg-blue-600 hover:bg-blue-700 text-white px-4 lg:px-6 py-3 rounded-lg transition-colors flex items-center justify-center font-medium text-sm lg:text-base"
                                tabindex="101">
                                Next<i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        @endif
                    </div>

                    <!-- Enhanced Progress Bar -->
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="bg-blue-600 h-3 rounded-full transition-all duration-500 progress-animate"
                            style="width: {{ $this->getProgressPercentage() }}%">
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span>{{ round($this->getProgressPercentage(), 1) }}% Complete</span>
                        <span>{{ count($questions) - $this->getAnsweredQuestionsCount() }} remaining</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Enhanced Submit Confirmation Modal (Mobile Responsive) -->
    @if($showSubmitModal)
        <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full animate-pulse-scale">
                <div class="p-4 lg:p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                        Confirm Exam Submission
                    </h3>
                </div>
                <div class="p-4 lg:p-6">
                    <div class="mb-6">
                        <p class="text-gray-700 dark:text-gray-300 mb-4 text-sm lg:text-base">Are you sure you want to submit your exam? This action cannot be undone.</p>

                        <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Questions Answered:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $this->getAnsweredQuestionsCount() }} / {{ count($questions) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Time Remaining:</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100" x-text="formatTime(timeRemaining)"></span>
                            </div>
                            @if(count($flaggedQuestions) > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Flagged Questions:</span>
                                    <span class="font-semibold text-yellow-600 dark:text-yellow-400">{{ count($flaggedQuestions) }}</span>
                                </div>
                            @endif
                        </div>

                        @if($this->getAnsweredQuestionsCount() < count($questions))
                            <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-600 rounded-lg">
                                <p class="text-yellow-800 dark:text-yellow-300 text-sm flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    You have {{ count($questions) - $this->getAnsweredQuestionsCount() }} unanswered questions.
                                </p>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col lg:flex-row space-y-3 lg:space-y-0 lg:space-x-4">
                        <button wire:click="cancelSubmission"
                            class="flex-1 px-4 py-3 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors font-medium text-sm lg:text-base">
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
                console.log('Enhanced CBT Exam Interface initialized');
                
                // Set up Livewire event listeners
                this.$wire.$on('startTimer', () => {
                    console.log('Timer started');
                    this.startTimer();
                });

                this.$wire.$on('examCompleted', () => {
                    console.log('Exam completed');
                    this.stopTimer();
                    if (window.examSecurity) {
                        window.examSecurity.allowFullscreenExit();
                    }
                });

                this.$wire.$on('markExamStarted', () => {
                    console.log('Mark exam started - enforcing security');
                    if (window.examSecurity) {
                        window.examSecurity.markExamStarted();
                    }
                });

                this.$wire.$on('allowFullscreenExit', () => {
                    console.log('Allowing fullscreen exit');
                    if (window.examSecurity) {
                        window.examSecurity.allowFullscreenExit();
                    }
                });

                this.$wire.$on('answerSaved', (event) => {
                    console.log('Answer saved for question:', event.questionId);
                    this.answeredQuestions = this.getAnsweredCount();
                    this.updateProgressTracking();
                });

                this.$wire.$on('questionChanged', (event) => {
                    if (window.examSecurity) {
                        window.examSecurity.trackQuestionTime(event.previousIndex);
                    }
                });

                // Auto-start if exam is already started
                if (@js($examStarted ?? false)) {
                    console.log('Exam already started, initializing timer and security');
                    this.startTimer();
                    if (window.examSecurity) {
                        window.examSecurity.markExamStarted();
                    }
                }

                // Update progress tracking
                this.updateProgressTracking();
            },

            startTimer() {
                if (this.timerInterval) {
                    clearInterval(this.timerInterval);
                }
                
                this.timerInterval = setInterval(() => {
                    if (this.timeRemaining > 0) {
                        this.timeRemaining--;
                        this.updateProgressTracking();
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

            getAnsweredCount() {
                // Count answered questions from the interface
                const answered = document.querySelectorAll('input[type="radio"]:checked').length;
                return answered;
            },

            updateProgressTracking() {
                const progressData = {
                    answered: this.answeredQuestions,
                    total: this.totalQuestions,
                    timeRemaining: this.timeRemaining,
                    avgTimePerQuestion: Math.floor(this.timeRemaining / (this.totalQuestions - this.answeredQuestions || 1)),
                    estimatedFinishTime: this.calculateEstimatedFinish()
                };

                if (window.examInterface) {
                    window.examInterface.updateProgress(progressData);
                }
            },

            calculateEstimatedFinish() {
                const avgTimePerRemaining = Math.floor(this.timeRemaining / (this.totalQuestions - this.answeredQuestions || 1));
                const estimatedRemainingTime = avgTimePerRemaining * (this.totalQuestions - this.answeredQuestions);
                const finishTime = new Date(Date.now() + (estimatedRemainingTime * 1000));
                return finishTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
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
        .form-radio:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
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

        /* Enhanced mobile responsiveness */
        @media (max-width: 640px) {
            .grid-cols-8 {
                grid-template-columns: repeat(6, minmax(0, 1fr));
            }
        }
    </style>
    @endpush
</div>