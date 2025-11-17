<div x-data="cbtExamInterface" class="h-screen flex flex-col bg-themed-primary dark:bg-gray-900 transition-colors duration-300">

    @if(!$examStarted)
        <!-- Pre-Exam Instructions - Fully Responsive -->
        <div class="flex-1 flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto">
            <div class="w-full max-w-4xl">
                <div class="bg-themed-secondary rounded-lg shadow-lg border border-themed-primary">
                    <div class="bg-yellow-500 dark:bg-yellow-600 text-yellow-900 dark:text-yellow-100 px-3 sm:px-4 md:px-6 py-3 md:py-4 rounded-t-lg">
                        <h1 class="text-base sm:text-lg md:text-xl font-semibold flex items-center">
                            <i class="fas fa-info-circle mr-2 text-sm sm:text-base"></i>
                            <span class="break-words">Exam Instructions</span>
                        </h1>
                    </div>
                    <div class="p-3 sm:p-4 md:p-6 lg:p-8">
                        <div class="text-center mb-4 sm:mb-6 md:mb-8">
                            <h2 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-accent-themed-primary mb-2 break-words px-2">{{ $assessment->title }}</h2>
                            @if($assessment->description)
                                <p class="text-themed-secondary text-sm sm:text-base md:text-lg px-2">{{ $assessment->description }}</p>
                            @endif
                        </div>

                        <!-- Exam Details Grid - Responsive -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-4 sm:mb-6 md:mb-8">
                            <div class="text-center border border-themed-secondary rounded-lg p-2 sm:p-3 md:p-4 bg-themed-tertiary">
                                <i class="fas fa-question-circle text-xl sm:text-2xl md:text-3xl text-accent-themed-primary mb-1 sm:mb-2"></i>
                                <div class="font-semibold text-themed-primary text-base sm:text-lg md:text-xl">{{ count($questions) }}</div>
                                <div class="text-xs sm:text-sm text-themed-secondary">Questions</div>
                            </div>
                            <div class="text-center border border-themed-secondary rounded-lg p-2 sm:p-3 md:p-4 bg-themed-tertiary">
                                <i class="fas fa-clock text-xl sm:text-2xl md:text-3xl text-accent-themed-primary mb-1 sm:mb-2"></i>
                                <div class="font-semibold text-themed-primary text-base sm:text-lg md:text-xl">{{ $assessment->formatted_duration }}</div>
                                <div class="text-xs sm:text-sm text-themed-secondary">Duration</div>
                            </div>
                            <div class="text-center border border-themed-secondary rounded-lg p-2 sm:p-3 md:p-4 bg-themed-tertiary">
                                <i class="fas fa-trophy text-xl sm:text-2xl md:text-3xl text-accent-themed-primary mb-1 sm:mb-2"></i>
                                <div class="font-semibold text-themed-primary text-base sm:text-lg md:text-xl">{{ $assessment->pass_percentage }}%</div>
                                <div class="text-xs sm:text-sm text-themed-secondary">Pass Mark</div>
                            </div>
                            <div class="text-center border border-themed-secondary rounded-lg p-2 sm:p-3 md:p-4 bg-themed-tertiary">
                                <i class="fas fa-hashtag text-xl sm:text-2xl md:text-3xl text-accent-themed-primary mb-1 sm:mb-2"></i>
                                <div class="font-semibold text-themed-primary text-base sm:text-lg md:text-xl"># {{ $attemptNumber }}</div>
                                <div class="text-xs sm:text-sm text-themed-secondary">Attempt</div>
                            </div>
                        </div>

                        <!-- Start Exam Button - Responsive -->
                        <div class="text-center px-2">
                            <button wire:click="startExam" wire:loading.attr="disabled" wire:target="startExam"
                                class="w-full sm:w-auto bg-blue-500 hover:bg-accent-themed-secondary disabled:bg-accent-themed-secondary disabled:opacity-50 text-white px-6 sm:px-8 md:px-12 py-3 md:py-4 rounded-lg text-base sm:text-lg md:text-xl font-semibold transition-colors flex items-center justify-center shadow-lg">
                                <div wire:loading.remove wire:target="startExam" class="flex items-center">
                                    <i class="fas fa-play mr-2 sm:mr-3"></i>
                                    <span>Start CBT Exam</span>
                                </div>
                                <div wire:loading wire:target="startExam" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 sm:mr-3 h-5 w-5 text-white"
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
        <!-- Results Page - Fully Responsive -->
        <div class="flex-1 flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto">
            <div class="w-full max-w-4xl">
                <div class="bg-themed-secondary rounded-lg shadow-lg border border-themed-primary">
                    <div class="px-3 sm:px-4 md:px-6 py-3 md:py-4 rounded-t-lg {{ $results['passed'] ? 'bg-green-600' : 'bg-red-600' }} text-white">
                        <h1 class="text-base sm:text-lg md:text-xl lg:text-2xl font-semibold flex items-center justify-center text-center">
                            <i class="fas {{ $results['passed'] ? 'fa-check-circle' : 'fa-times-circle' }} mr-2 sm:mr-3 flex-shrink-0"></i>
                            <span class="break-words">Exam {{ $results['passed'] ? 'COMPLETED - PASSED' : 'COMPLETED - FAILED' }}</span>
                        </h1>
                    </div>
                    <div class="p-3 sm:p-4 md:p-6 lg:p-8">
                        <div class="text-center mb-4 sm:mb-6 md:mb-8">
                            <h2 class="text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold text-themed-primary mb-2 break-words px-2">{{ $assessment->title }}</h2>
                            <p class="text-themed-secondary text-sm sm:text-base">Attempt #{{ $results['attempt_number'] }}</p>
                        </div>

                        <!-- Results Grid - Responsive -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 md:gap-6 text-center mb-4 sm:mb-6 md:mb-8">
                            <div class="border rounded-lg p-3 sm:p-4 md:p-6 {{ $results['passed'] ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-red-500 bg-red-50 dark:bg-red-900/20' }}">
                                <div class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2 {{ $results['passed'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $results['percentage'] }}%
                                </div>
                                <div class="text-xs sm:text-sm text-themed-secondary font-medium">Final Score</div>
                            </div>
                            <div class="border border-themed-secondary rounded-lg p-3 sm:p-4 md:p-6 bg-themed-tertiary">
                                <div class="text-xl sm:text-2xl md:text-3xl font-bold text-accent-themed-primary mb-2">{{ $results['total_points'] }}</div>
                                <div class="text-xs sm:text-sm text-themed-secondary font-medium">Points</div>
                                <div class="text-xs text-themed-tertiary">out of {{ $results['max_points'] }}</div>
                            </div>
                            <div class="border border-themed-secondary rounded-lg p-3 sm:p-4 md:p-6 bg-themed-tertiary">
                                <div class="text-xl sm:text-2xl md:text-3xl font-bold text-themed-primary mb-2">{{ $this->formatTimeSpent($results['time_spent']) }}</div>
                                <div class="text-xs sm:text-sm text-themed-secondary font-medium">Time Used</div>
                            </div>
                        </div>

                        <!-- Action Buttons - Responsive -->
                        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center px-2">
                            @if(!$results['passed'])
                                <button wire:click="retakeExam"
                                    class="w-full sm:w-auto bg-yellow-500 hover:bg-yellow-600 text-white px-4 sm:px-6 md:px-8 py-3 rounded-lg text-base sm:text-lg font-semibold transition-colors flex items-center justify-center">
                                    <i class="fas fa-redo mr-2"></i>Retake Exam
                                </button>
                            @endif

                            <a href="{{ route('cbt.exams') }}"
                                class="w-full sm:w-auto bg-blue-500 hover:bg-accent-themed-secondary text-white px-4 sm:px-6 md:px-8 py-3 rounded-lg text-base sm:text-lg font-semibold transition-colors inline-flex items-center justify-center">
                                <i class="fas fa-list mr-2"></i>Back to Exams
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Active Exam Interface - Fully Responsive with Collapsible Sidebar -->
        <div class="flex flex-col h-screen bg-themed-primary dark:bg-gray-900">
            
            <!-- Mobile Header with Timer and Status -->
            <div class="lg:hidden bg-gray-900 dark:bg-gray-800 text-white sticky top-0 z-30">
                <!-- Top bar with timer and flag -->
                <div class="p-3 flex justify-between items-center border-b border-gray-700">
                    <button @click="progressOpen = !progressOpen" 
                            class="flex items-center space-x-2 p-2 hover:bg-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-list-ol"></i>
                        <span class="text-sm font-medium">{{ $currentQuestionIndex + 1 }}/{{ count($questions) }}</span>
                        <i class="fas fa-chevron-down text-xs transition-transform" 
                           :class="progressOpen ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-clock"></i>
                        <div x-text="formatTime(timeRemaining)" class="font-mono text-lg font-bold"
                             x-bind:class="timeRemaining <= 300 ? 'text-red-400' : ''"></div>
                    </div>
                    
                    <button wire:click="toggleFlag({{ $currentQuestionIndex }})"
                        class="p-2 rounded-lg transition-colors {{ $this->isQuestionFlagged($currentQuestionIndex) ? 'bg-yellow-500 text-yellow-900' : 'border border-gray-400 text-gray-300 hover:bg-gray-700' }}">
                        <i class="fas fa-flag"></i>
                    </button>
                </div>

                <!-- Expandable Progress Dropdown -->
                <div x-show="progressOpen" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="bg-gray-800 border-b border-gray-700 p-3 max-h-64 overflow-y-auto"
                     style="display: none;">
                    
                    <!-- Progress Stats -->
                    <div class="mb-3 pb-3 border-b border-gray-700">
                        <div class="flex justify-between text-xs mb-2">
                            <span class="text-gray-400">Progress</span>
                            <span class="font-semibold">{{ $this->getAnsweredQuestionsCount() }}/{{ count($questions) }} answered</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-500"
                                style="width: {{ count($questions) > 0 ? ($this->getAnsweredQuestionsCount() / count($questions)) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="flex justify-between items-center text-xs mb-3 px-1">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-green-500 rounded flex items-center justify-center text-white font-bold text-xs">5</div>
                            <span class="text-gray-300">Answered</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-gray-600 border-2 border-gray-500 rounded flex items-center justify-center text-white font-bold text-xs">5</div>
                            <span class="text-gray-300">Unanswered</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-blue-500 rounded flex items-center justify-center text-white font-bold text-xs">5</div>
                            <span class="text-gray-300">Current</span>
                        </div>
                    </div>

                    <!-- Question Grid -->
                    <div class="grid grid-cols-8 gap-2">
                        @foreach($questions as $index => $question)
                            <button wire:click="goToQuestion({{ $index }})" 
                                @click="progressOpen = false"
                                class="relative aspect-square flex items-center justify-center text-xs font-bold rounded-lg transition-all focus:ring-2 focus:ring-blue-400
                                @if($currentQuestionIndex === $index) 
                                    bg-blue-500 text-white ring-2 ring-blue-300 shadow-lg scale-110
                                @elseif(isset($answers[$question['id']]) && $answers[$question['id']] !== null) 
                                    bg-green-500 text-white hover:bg-green-600
                                @else 
                                    bg-gray-600 border-2 border-gray-500 text-gray-200 hover:border-blue-400 hover:bg-gray-500
                                @endif">
                                {{ $index + 1 }}
                                @if($this->isQuestionFlagged($index))
                                    <i class="fas fa-flag absolute -top-1 -right-1 text-yellow-400 text-[8px] drop-shadow-lg"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    <!-- Quick Jump -->
                    <div class="mt-3 pt-3 border-t border-gray-700">
                        <div class="text-xs text-gray-400 mb-2">Quick Jump:</div>
                        <div class="flex gap-2">
                            <button @click="$wire.goToQuestion(0); progressOpen = false" 
                                    class="flex-1 px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded text-xs font-medium transition-colors">
                                <i class="fas fa-fast-backward mr-1"></i>First
                            </button>
                            <button @click="$wire.goToQuestion({{ count($questions) - 1 }}); progressOpen = false" 
                                    class="flex-1 px-3 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded text-xs font-medium transition-colors">
                                Last<i class="fas fa-fast-forward ml-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex flex-1 overflow-hidden">
                
                <!-- Sidebar - Collapsible on Mobile -->
                <div x-data="{ sidebarOpen: false }" 
                     class="fixed lg:relative inset-0 lg:inset-auto z-40 lg:z-0 flex"
                     x-show="sidebarOpen || window.innerWidth >= 1024"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    
                    <!-- Overlay for mobile -->
                    <div class="lg:hidden fixed inset-0 bg-black bg-opacity-50" 
                         @click="sidebarOpen = false"
                         x-show="sidebarOpen"></div>
                    
                    <!-- Sidebar content -->
                    <div class="relative w-72 sm:w-80 lg:w-80 bg-themed-tertiary border-r border-themed-secondary flex flex-col max-h-screen overflow-hidden">
                        <div class="p-3 lg:p-4 border-b border-themed-secondary bg-themed-secondary sticky top-0 z-10">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-semibold text-themed-primary text-sm lg:text-base">Question Navigator</h3>
                                <button class="lg:hidden p-1 hover:bg-themed-tertiary rounded" @click="sidebarOpen = false">
                                    <i class="fas fa-times text-themed-secondary"></i>
                                </button>
                            </div>
                            <div class="text-xs lg:text-sm text-themed-secondary">
                                {{ $this->getAnsweredQuestionsCount() }} of {{ count($questions) }} answered
                            </div>
                            <div class="w-full bg-themed-secondary rounded-full h-2 mt-2">
                                <div class="bg-accent-themed-primary h-2 rounded-full transition-all duration-500 progress-animate"
                                    style="width: {{ count($questions) > 0 ? ($this->getAnsweredQuestionsCount() / count($questions)) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Question Grid - Scrollable -->
                        <div class="p-3 lg:p-4 flex-1 overflow-y-auto">
                            <div class="grid grid-cols-5 sm:grid-cols-6 lg:grid-cols-5 gap-2">
                                @foreach($questions as $index => $question)
                                    <button wire:click="goToQuestion({{ $index }})" 
                                        @click="sidebarOpen = false"
                                        class="relative aspect-square flex items-center justify-center text-xs sm:text-sm font-bold rounded-lg transition-all focus:ring-2 focus:ring-accent-themed-primary
                                        @if($currentQuestionIndex === $index) 
                                            bg-accent-themed-primary text-white ring-2 ring-accent-themed-secondary
                                        @elseif(isset($answers[$question['id']]) && $answers[$question['id']] !== null) 
                                            bg-green-500 text-white
                                        @else 
                                            bg-themed-secondary border-2 border-themed-secondary text-themed-primary hover:border-accent-themed-primary hover:bg-themed-tertiary
                                        @endif">
                                        {{ $index + 1 }}
                                        @if($this->isQuestionFlagged($index))
                                            <i class="fas fa-flag absolute -top-1 -right-1 text-yellow-400 text-xs"></i>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Question Area -->
                <div class="flex-1 flex flex-col overflow-hidden">
                    
                    <!-- Desktop Timer Header -->
                    <div class="hidden lg:flex bg-gray-900 dark:bg-gray-800 text-white p-4 justify-between items-center">
                        <div>
                            <h1 class="font-semibold text-lg">{{ $assessment->title }}</h1>
                            <div class="text-sm text-gray-300 dark:text-gray-400">
                                Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center bg-gray-800 dark:bg-gray-700 px-4 py-2 rounded-lg"
                                x-bind:class="timeRemaining <= 300 ? 'timer-critical' : ''">
                                <i class="fas fa-clock mr-2"></i>
                                <div x-text="formatTime(timeRemaining)" class="font-mono text-xl font-bold"></div>
                            </div>

                            <button wire:click="toggleFlag({{ $currentQuestionIndex }})"
                                class="px-3 py-2 rounded-lg transition-colors flex items-center
                                {{ $this->isQuestionFlagged($currentQuestionIndex) ? 'bg-yellow-500 text-yellow-900' : 'border border-gray-400 text-gray-300 hover:bg-gray-700 dark:hover:bg-gray-600' }}">
                                <i class="fas fa-flag mr-1"></i>
                                <span>Flag</span>
                            </button>
                        </div>
                    </div>

                    <!-- Question Content - Scrollable with custom scrollbar -->
                    <div class="flex-1 overflow-y-auto bg-themed-secondary custom-scrollbar">
                        @if($this->getCurrentQuestion())
                            @php $question = $this->getCurrentQuestion(); @endphp
                            <div class="p-3 sm:p-4 md:p-6 lg:p-8 max-w-4xl mx-auto pb-24">
                                <!-- Question Header -->
                                <div class="mb-4 sm:mb-6 md:mb-8">
                                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-themed-primary mb-2">
                                        Question {{ $currentQuestionIndex + 1 }}
                                    </h2>
                                    <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-themed-secondary">
                                        <span>Type: {{ ucfirst(str_replace('_', ' ', $question['question_type'] ?? 'multiple_choice')) }}</span>
                                        <span>•</span>
                                        <span>{{ $question['points'] ?? 1 }} point(s)</span>
                                    </div>
                                </div>

                                <!-- Question Text with MathJax Support - NO SCROLLBARS -->
                                <div class="mb-4 sm:mb-6 md:mb-8">
                                    <div class="bg-themed-tertiary border-l-4 border-accent-themed-primary p-3 sm:p-4 md:p-6 rounded-r-lg overflow-visible">
                                        <div class="text-sm sm:text-base md:text-lg text-themed-primary leading-relaxed math-content overflow-visible" 
                                             style="overflow: visible !important;"
                                             wire:key="question-{{ $question['id'] }}"
                                             x-init="$nextTick(() => { if (window.MathJax) { MathJax.typesetPromise([$el]).catch(err => console.error('MathJax error:', err)); } })">
                                            {!! $question['question_text'] ?? '' !!}
                                        </div>
                                    </div>
                                </div>

                                <!-- Answer Options with MathJax Support - NO SCROLLBARS -->
                                <div class="space-y-3">
                                    @if(($question['question_type'] ?? '') === 'multiple_choice')
                                        @if(is_array($question['options']) && count($question['options']) > 0)
                                            @foreach($question['options'] as $optionIndex => $option)
                                                @if(trim(strip_tags($option)))
                                                    <div class="border-2 rounded-lg transition-all duration-200 hover:border-accent-themed-primary
                                                        {{ isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex ? 'border-accent-themed-primary bg-accent-themed-primary bg-opacity-10' : 'border-themed-secondary hover:bg-themed-tertiary' }}">
                                                        <label class="flex items-start cursor-pointer p-3 sm:p-4 w-full">
                                                            <input class="form-radio mt-1 mr-3 h-4 w-4 sm:h-5 sm:w-5 text-accent-themed-primary border-themed-secondary focus:ring-accent-themed-primary flex-shrink-0"
                                                                type="radio" wire:click="saveAnswer({{ $question['id'] }}, {{ $optionIndex }})"
                                                                name="question_{{ $question['id'] }}" id="option_{{ $optionIndex }}"
                                                                @if(isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex) checked @endif>
                                                            <div class="flex-1 overflow-visible">
                                                                <div class="flex items-start">
                                                                    <span class="font-semibold text-accent-themed-primary mr-2 text-sm sm:text-base md:text-lg flex-shrink-0">{{ chr(65 + $optionIndex) }}.</span>
                                                                    <div class="flex-1 text-themed-primary text-sm sm:text-base prose-math-option"
                                                                         wire:key="option-{{ $question['id'] }}-{{ $optionIndex }}"
                                                                         x-init="$nextTick(() => { if (window.MathJax) { MathJax.typesetPromise([$el]).catch(err => console.error('MathJax error:', err)); } })">
                                                                        {!! $option !!}
                                                                    </div>
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
                                                <label class="flex items-center cursor-pointer p-3 sm:p-4 w-full">
                                                    <input class="form-radio mr-3 h-4 w-4 sm:h-5 sm:w-5 text-accent-themed-primary border-themed-secondary focus:ring-accent-themed-primary"
                                                        type="radio" wire:click="saveAnswer({{ $question['id'] }}, 0)"
                                                        name="question_{{ $question['id'] }}" @if(isset($answers[$question['id']]) && $answers[$question['id']] == 0) checked @endif>
                                                    <span class="font-semibold text-accent-themed-primary mr-2 text-sm sm:text-base md:text-lg">A.</span>
                                                    <span class="text-themed-primary font-medium text-sm sm:text-base">True</span>
                                                </label>
                                            </div>
                                            <div class="border-2 rounded-lg transition-all duration-200 hover:border-accent-themed-primary
                                                {{ isset($answers[$question['id']]) && $answers[$question['id']] == 1 ? 'border-accent-themed-primary bg-accent-themed-primary bg-opacity-10' : 'border-themed-secondary hover:bg-themed-tertiary' }}">
                                                <label class="flex items-center cursor-pointer p-3 sm:p-4 w-full">
                                                    <input class="form-radio mr-3 h-4 w-4 sm:h-5 sm:w-5 text-accent-themed-primary border-themed-secondary focus:ring-accent-themed-primary"
                                                        type="radio" wire:click="saveAnswer({{ $question['id'] }}, 1)"
                                                        name="question_{{ $question['id'] }}" @if(isset($answers[$question['id']]) && $answers[$question['id']] == 1) checked @endif>
                                                    <span class="font-semibold text-accent-themed-primary mr-2 text-sm sm:text-base md:text-lg">B.</span>
                                                    <span class="text-themed-primary font-medium text-sm sm:text-base">False</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Navigation Footer - Fixed at Bottom -->
                    <div class="bg-themed-secondary border-t border-themed-secondary p-3 sm:p-4 md:p-6 sticky bottom-0 z-20">
                        <div class="flex flex-col space-y-3">
                            <!-- Navigation Buttons -->
                            <div class="flex justify-between items-center gap-2">
                                <button wire:click="previousQuestion" 
                                    class="flex-1 sm:flex-none px-4 sm:px-6 md:px-8 py-3 sm:py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center justify-center font-semibold text-sm sm:text-base md:text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200
                                    {{ !$this->canGoPrevious() ? 'opacity-50 cursor-not-allowed hover:bg-blue-600 hover:translate-y-0' : '' }}"
                                    {{ !$this->canGoPrevious() ? 'disabled' : '' }}>
                                    <i class="fas fa-arrow-left mr-2 text-base sm:text-xl"></i>
                                    <span class="hidden sm:inline">Previous</span>
                                    <span class="sm:hidden">Prev</span>
                                </button>

                                <div class="text-center px-2">
                                    <div class="text-xs sm:text-sm text-themed-secondary">Progress</div>
                                    <div class="font-bold text-themed-primary text-sm sm:text-base md:text-lg lg:text-xl whitespace-nowrap">
                                        {{ $currentQuestionIndex + 1 }} / {{ count($questions) }}
                                    </div>
                                </div>

                                @if($this->isLastQuestion())
                                    <button wire:click="showSubmitConfirmation"
                                        class="flex-1 sm:flex-none bg-red-600 hover:bg-red-700 text-white px-4 sm:px-6 md:px-10 py-3 sm:py-4 rounded-lg transition-colors flex items-center justify-center font-bold text-sm sm:text-base md:text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                        <i class="fas fa-paper-plane mr-2 text-base sm:text-xl"></i>
                                        <span class="hidden sm:inline">Submit Exam</span>
                                        <span class="sm:hidden">Submit</span>
                                    </button>
                                @else
                                    <button wire:click="nextQuestion"
                                        class="flex-1 sm:flex-none bg-green-600 hover:bg-green-700 text-white px-4 sm:px-6 md:px-8 py-3 sm:py-4 rounded-lg transition-colors flex items-center justify-center font-semibold text-sm sm:text-base md:text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                        <span class="hidden sm:inline">Next</span>
                                        <span class="sm:hidden">Next</span>
                                        <i class="fas fa-arrow-right ml-2 text-base sm:text-xl"></i>
                                    </button>
                                @endif
                            </div>

                            <!-- Progress Bar -->
                            <div class="w-full bg-themed-tertiary rounded-full h-2 sm:h-3">
                                <div class="bg-accent-themed-primary h-2 sm:h-3 rounded-full transition-all duration-500 progress-animate"
                                    style="width: {{ $this->getProgressPercentage() }}%">
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-themed-secondary">
                                <span>{{ round($this->getProgressPercentage(), 1) }}% Complete</span>
                                <span>{{ count($questions) - $this->getAnsweredQuestionsCount() }} remaining</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif

    <!-- Submit Confirmation Modal - Responsive -->
    @if($showSubmitModal)
        <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-3 sm:p-4">
            <div class="bg-themed-secondary rounded-lg max-w-md w-full animate-pulse-scale border border-themed-primary">
                <div class="p-4 sm:p-6 border-b border-themed-secondary">
                    <h3 class="text-base sm:text-lg font-semibold text-themed-primary flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2 sm:mr-3 flex-shrink-0"></i>
                        <span class="break-words">Confirm Exam Submission</span>
                    </h3>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="mb-4 sm:mb-6">
                        <p class="text-themed-primary mb-4 text-sm sm:text-base">Are you sure you want to submit your exam? This action cannot be undone.</p>

                        <div class="bg-themed-tertiary rounded-lg p-3 sm:p-4 space-y-2">
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-themed-secondary">Questions Answered:</span>
                                <span class="font-semibold text-themed-primary">{{ $this->getAnsweredQuestionsCount() }} / {{ count($questions) }}</span>
                            </div>
                            <div class="flex justify-between text-xs sm:text-sm">
                                <span class="text-themed-secondary">Time Remaining:</span>
                                <span class="font-semibold text-themed-primary" x-text="formatTime(timeRemaining)"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                        <button wire:click="cancelSubmission"
                            class="flex-1 px-4 py-3 bg-themed-tertiary text-themed-primary rounded-lg hover:bg-themed-tertiary hover:opacity-75 transition-colors font-medium text-sm sm:text-base border border-themed-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button wire:click="submitExam"
                            class="flex-1 px-4 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold text-sm sm:text-base">
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
            sidebarOpen: false,
            progressOpen: false,

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

                // Auto-close sidebar on larger screens
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1024) {
                        this.sidebarOpen = false;
                    }
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
        /* Custom Scrollbar Styling */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgb(var(--bg-tertiary));
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgb(var(--accent-primary));
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgb(var(--accent-secondary));
        }

        /* Firefox scrollbar */
        .custom-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: rgb(var(--accent-primary)) rgb(var(--bg-tertiary));
        }

        /* Hide scrollbar for mobile but keep functionality */
        @media (max-width: 640px) {
            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }
        }

        /* MathJax specific styling */
        .math-content mjx-container {
            display: inline-block !important;
            margin: 0.2em 0;
            max-width: 100%;
            overflow-x: auto;
        }

        .math-content mjx-container[display="true"] {
            display: block !important;
            margin: 1em 0;
        }

        /* Mobile responsive math */
        @media (max-width: 640px) {
            .math-content mjx-container {
                font-size: 0.85em;
            }
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
            padding: 0.75rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            font-size: 0.875rem;
        }

        @media (max-width: 640px) {
            .prose pre {
                padding: 0.5rem;
                font-size: 0.75rem;
            }
        }

        .prose code {
            background-color: #e5e7eb;
            padding: 0.15rem 0.3rem;
            border-radius: 0.25rem;
            font-size: 0.875em;
            word-break: break-word;
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
            padding-left: 1.25rem;
        }

        @media (max-width: 640px) {
            .prose ul, .prose ol {
                padding-left: 1rem;
            }
        }

        .prose li {
            margin: 0.25rem 0;
        }

        .prose a {
            color: rgb(var(--accent-primary));
            text-decoration: underline;
            word-break: break-word;
        }

        .prose table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            font-size: 0.875rem;
            display: block;
            overflow-x: auto;
        }

        @media (max-width: 640px) {
            .prose table {
                font-size: 0.75rem;
            }
        }

        .prose th, .prose td {
            border: 1px solid #e5e7eb;
            padding: 0.5rem;
            min-width: 80px;
        }

        @media (max-width: 640px) {
            .prose th, .prose td {
                padding: 0.25rem;
                min-width: 60px;
            }
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

        /* Mobile touch targets - ensure buttons are easily tappable */
        @media (max-width: 640px) {
            button, .cursor-pointer {
                min-height: 44px;
                min-width: 44px;
            }
        }

        /* Prevent text selection during exam on mobile */
        .app-locked {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
        }

        /* Improve scrolling on mobile */
        .overflow-y-auto {
            -webkit-overflow-scrolling: touch;
        }

        /* Better word breaking for long content */
        .break-words {
            word-break: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }

        /* Responsive sidebar transitions */
        @media (max-width: 1023px) {
            .sidebar-transition {
                transition: transform 0.3s ease-in-out;
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

        /* Smooth transitions */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        /* Accessibility - focus indicators */
        button:focus, 
        input:focus, 
        a:focus {
            outline: 2px solid rgb(var(--accent-primary));
            outline-offset: 2px;
        }

        /* Landscape mode optimization for mobile */
        @media (max-width: 896px) and (orientation: landscape) {
            .landscape-compact {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
        }
    </style>
    @endpush
</div>