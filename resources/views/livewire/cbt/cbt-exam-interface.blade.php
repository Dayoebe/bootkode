<div x-data="modernCbtExam()" 
     x-init="init()"
     class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 transition-all duration-300"
     :class="{ 'exam-mode': examStarted && !examCompleted }"
     @click="closeDropdown($event)">

    {{-- Pre-Exam Welcome Screen --}}
    @if(!$examStarted)
    <div class="min-h-screen flex items-center justify-center p-4 animate__animated animate__fadeIn">
        <div class="max-w-4xl w-full bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden">
            {{-- Header Banner --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-8 text-white">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm animate__animated animate__bounceIn">
                        <i class="fas fa-graduation-cap text-4xl"></i>
                    </div>
                </div>
                <h1 class="text-3xl md:text-4xl font-bold text-center mb-2">{{ $assessment->title }}</h1>
                @if($assessment->description)
                <p class="text-center text-blue-100 text-lg">{{ $assessment->description }}</p>
                @endif
            </div>

            {{-- Exam Info Cards --}}
            <div class="p-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 p-6 rounded-2xl text-center transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-question-circle text-3xl text-blue-600 dark:text-blue-400 mb-3"></i>
                        <div class="text-3xl font-bold text-gray-800 dark:text-white">{{ count($questions) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Questions</div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 p-6 rounded-2xl text-center transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-clock text-3xl text-purple-600 dark:text-purple-400 mb-3"></i>
                        <div class="text-3xl font-bold text-gray-800 dark:text-white">{{ $assessment->formatted_duration }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Duration</div>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 p-6 rounded-2xl text-center transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-trophy text-3xl text-green-600 dark:text-green-400 mb-3"></i>
                        <div class="text-3xl font-bold text-gray-800 dark:text-white">{{ $assessment->pass_percentage }}%</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pass Mark</div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-800/30 p-6 rounded-2xl text-center transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-hashtag text-3xl text-orange-600 dark:text-orange-400 mb-3"></i>
                        <div class="text-3xl font-bold text-gray-800 dark:text-white">#{{ $attemptNumber }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Attempt</div>
                    </div>
                </div>

                {{-- Important Instructions --}}
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 p-6 rounded-lg mb-8">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 text-2xl mr-4 mt-1"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">Important Instructions</h3>
                            <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-yellow-600 mr-2 mt-1"></i>
                                    <span>Once started, the timer cannot be paused</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-yellow-600 mr-2 mt-1"></i>
                                    <span>Your answers are automatically saved</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-yellow-600 mr-2 mt-1"></i>
                                    <span>You can navigate between questions freely</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-yellow-600 mr-2 mt-1"></i>
                                    <span>Avoid switching tabs or minimizing the window</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Start Button --}}
                <button wire:click="startExam" 
                        wire:loading.attr="disabled"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xl font-bold py-5 rounded-2xl transform hover:scale-[1.02] transition-all duration-300 shadow-lg hover:shadow-2xl disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                    <div wire:loading.remove class="flex items-center justify-center">
                        <i class="fas fa-rocket mr-3 animate__animated animate__pulse animate__infinite"></i>
                        <span>Start Exam Now</span>
                    </div>
                    <div wire:loading class="flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-3"></i>
                        <span>Preparing Exam...</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    @elseif($examCompleted)
    {{-- Results Screen --}}
    <div class="min-h-screen flex items-center justify-center p-4 animate__animated animate__fadeIn">
        <div class="max-w-4xl w-full bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden">
            {{-- Results Header --}}
            <div class="p-8 text-center {{ $results['passed'] ? 'bg-gradient-to-r from-green-600 to-emerald-600' : 'bg-gradient-to-r from-red-600 to-pink-600' }}">
                <div class="w-32 h-32 mx-auto bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm mb-6 animate__animated animate__bounceIn">
                    <i class="fas {{ $results['passed'] ? 'fa-check-circle' : 'fa-times-circle' }} text-7xl text-white"></i>
                </div>
                <h1 class="text-4xl font-bold text-white mb-2">
                    {{ $results['passed'] ? 'Congratulations!' : 'Keep Trying!' }}
                </h1>
                <p class="text-xl text-white/90">{{ $assessment->title }}</p>
            </div>

            <div class="p-8">
                {{-- Score Display --}}
                <div class="text-center mb-8">
                    <div class="inline-block relative">
                        <svg class="transform -rotate-90 w-48 h-48">
                            <circle cx="96" cy="96" r="88" stroke="currentColor" stroke-width="12" fill="transparent" 
                                    class="text-gray-200 dark:text-gray-700" />
                            <circle cx="96" cy="96" r="88" stroke="currentColor" stroke-width="12" fill="transparent"
                                    class="{{ $results['passed'] ? 'text-green-500' : 'text-red-500' }}"
                                    :stroke-dasharray="`${({{ $results['percentage'] }} / 100) * 552.92} 552.92`"
                                    stroke-linecap="round" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div>
                                <div class="text-5xl font-bold {{ $results['passed'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $results['percentage'] }}%
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Your Score</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $results['total_points'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Points Earned</div>
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">out of {{ $results['max_points'] }}</div>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-2xl text-center">
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $results['correct_answers'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Correct Answers</div>
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">out of {{ $results['total_questions'] }}</div>
                    </div>

                    <div class="bg-orange-50 dark:bg-orange-900/20 p-6 rounded-2xl text-center">
                        <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $this->formatTimeSpent($results['time_spent']) }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Time Taken</div>
                        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">Attempt #{{ $results['attempt_number'] }}</div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    @if(!$results['passed'])
                    <button wire:click="retakeExam" 
                            class="flex-1 bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-semibold py-4 rounded-2xl transition-all duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-redo mr-2"></i>Retake Exam
                    </button>
                    @endif
                    <a href="{{ route('cbt.exams') }}" 
                       class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-4 rounded-2xl text-center transition-all duration-300 transform hover:scale-[1.02]">
                        <i class="fas fa-home mr-2"></i>Back to Exams
                    </a>
                </div>
            </div>
        </div>
    </div>

    @else
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
   
    
    {{-- Main Exam Interface --}}
    <div class="min-h-screen flex flex-col bg-white dark:bg-gray-900 safe-area-inset-bottom">
        {{-- Top Navigation Bar --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg z-20 flex-shrink-0">
            <div class="flex items-center justify-between px-4 md:px-6 py-4">
                {{-- Left: Question Counter with Mobile Dropdown --}}
                <div class="flex items-center space-x-4">
                    {{-- Mobile Question Dropdown --}}
                    <div class="lg:hidden relative" x-data="{ open: false }">
                        <button @click.stop="open = !open; questionDropdownOpen = open" 
                                class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-xl hover:bg-white/30 transition-colors">
                            <i class="fas fa-list text-xl"></i>
                            <span class="font-bold">{{ $currentQuestionIndex + 1 }}/{{ count($questions) }}</span>
                            <i class="fas fa-chevron-down text-sm transition-transform" :class="{ 'rotate-180': open }"></i>
                        </button>
    
                        {{-- Dropdown Menu --}}
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             @click.away="open = false; questionDropdownOpen = false"
                             class="absolute left-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl z-50 max-h-96 overflow-hidden"
                             style="display: none;">
                            
                            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-600">
                                <h3 class="font-semibold text-gray-800 dark:text-white mb-2">Jump to Question</h3>
                                <div class="flex items-center justify-between text-xs text-gray-600 dark:text-gray-400">
                                    <span>Answered: {{ $this->getAnsweredQuestionsCount() }}</span>
                                    <span>Remaining: {{ count($questions) - $this->getAnsweredQuestionsCount() }}</span>
                                </div>
                            </div>
    
                            <div class="p-4 overflow-y-auto max-h-80">
                                {{-- Legend --}}
                                <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-5 h-5 bg-green-500 rounded"></div>
                                        <span class="text-gray-600 dark:text-gray-400">Answered</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-600 rounded"></div>
                                        <span class="text-gray-600 dark:text-gray-400">Unanswered</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-5 h-5 bg-blue-600 rounded"></div>
                                        <span class="text-gray-600 dark:text-gray-400">Current</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-5 h-5 bg-yellow-500 rounded flex items-center justify-center">
                                            <i class="fas fa-flag text-xs text-white"></i>
                                        </div>
                                        <span class="text-gray-600 dark:text-gray-400">Flagged</span>
                                    </div>
                                </div>
    
                                {{-- Question Grid --}}
                                <div class="grid grid-cols-6 gap-2">
                                    @foreach($questions as $index => $question)
                                    <button wire:click="goToQuestion({{ $index }})"
                                            @click="open = false; questionDropdownOpen = false"
                                            class="relative aspect-square flex items-center justify-center rounded-lg font-bold text-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            :class="{
                                                'bg-blue-600 text-white scale-110': {{ $currentQuestionIndex }} === {{ $index }},
                                                'bg-green-500 text-white hover:bg-green-600': {{ $currentQuestionIndex }} !== {{ $index }} && {{ isset($answers[$question['id']]) && $answers[$question['id']] !== null ? 'true' : 'false' }},
                                                'border-2 border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-blue-500': {{ $currentQuestionIndex }} !== {{ $index }} && {{ !isset($answers[$question['id']]) || $answers[$question['id']] === null ? 'true' : 'false' }}
                                            }">
                                        {{ $index + 1 }}
                                        @if($this->isQuestionFlagged($index))
                                        <i class="fas fa-flag absolute -top-1 -right-1 text-yellow-500 text-xs"></i>
                                        @endif
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
    
                    {{-- Desktop Question Counter --}}
                    <button @click="toggleSidebar()" class="hidden lg:flex items-center space-x-3">
                        <div class="flex items-center justify-center w-12 h-12 bg-white/20 rounded-xl backdrop-blur-sm">
                            <i class="fas fa-file-alt text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium opacity-90">Question Progress</div>
                            <div class="text-xl font-bold">{{ $currentQuestionIndex + 1 }} / {{ count($questions) }}</div>
                        </div>
                    </button>
                </div>
    
                {{-- Center: Progress Bar (Hidden on mobile) --}}
                <div class="hidden md:flex flex-1 max-w-md mx-8">
                    <div class="w-full bg-white/20 rounded-full h-3 backdrop-blur-sm overflow-hidden">
                        <div class="bg-white h-full rounded-full transition-all duration-500 shadow-lg"
                             :style="`width: ${({{ $currentQuestionIndex + 1 }} / {{ count($questions) }}) * 100}%`"></div>
                    </div>
                </div>
    
                {{-- Right: Timer & Actions --}}
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <div class="flex items-center space-x-2 bg-white/20 backdrop-blur-sm px-3 sm:px-4 py-2 rounded-xl"
                         :class="{ 'animate__animated animate__pulse animate__infinite bg-red-500': timeRemaining <= 300 }">
                        <i class="fas fa-clock"></i>
                        <span class="font-mono text-lg sm:text-xl font-bold" x-text="formatTime(timeRemaining)"></span>
                    </div>
                    
                    <button wire:click="toggleFlag({{ $currentQuestionIndex }})"
                            class="p-2 sm:p-3 rounded-xl transition-all transform hover:scale-110"
                            :class="'{{ $this->isQuestionFlagged($currentQuestionIndex) }}' ? 'bg-yellow-500 text-yellow-900' : 'bg-white/20 hover:bg-white/30'">
                        <i class="fas fa-flag"></i>
                    </button>
                </div>
            </div>
    
            {{-- Mobile Progress Bar --}}
            <div class="md:hidden px-4 pb-3">
                <div class="w-full bg-white/20 rounded-full h-2 overflow-hidden">
                    <div class="bg-white h-full rounded-full transition-all duration-500"
                         :style="`width: ${({{ $currentQuestionIndex + 1 }} / {{ count($questions) }}) * 100}%`"></div>
                </div>
            </div>
        </div>
    
        <div class="flex-1 flex overflow-hidden min-h-0">
            {{-- Question Navigation Sidebar --}}
            <div class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 shadow-xl lg:shadow-none z-10"
                 :class="sidebarOpen ? 'fixed inset-y-0 left-0 w-80' : 'hidden lg:block lg:w-72'">
                
                {{-- Sidebar Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-semibold text-gray-800 dark:text-white">Questions</h3>
                        <button @click="toggleSidebar()" class="lg:hidden p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>Answered: {{ $this->getAnsweredQuestionsCount() }}</span>
                        <span>Remaining: {{ count($questions) - $this->getAnsweredQuestionsCount() }}</span>
                    </div>
                </div>
    
                {{-- Question Grid --}}
                <div class="p-4 overflow-y-auto h-[calc(100vh-180px)]">
                    {{-- Legend --}}
                    <div class="grid grid-cols-2 gap-2 mb-4 text-xs">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-green-500 rounded-lg"></div>
                            <span class="text-gray-600 dark:text-gray-400">Answered</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 border-2 border-gray-300 dark:border-gray-600 rounded-lg"></div>
                            <span class="text-gray-600 dark:text-gray-400">Unanswered</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-blue-600 rounded-lg"></div>
                            <span class="text-gray-600 dark:text-gray-400">Current</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-yellow-500 rounded-lg flex items-center justify-center">
                                <i class="fas fa-flag text-xs text-white"></i>
                            </div>
                            <span class="text-gray-600 dark:text-gray-400">Flagged</span>
                        </div>
                    </div>
    
                    {{-- Question Numbers --}}
                    <div class="grid grid-cols-5 gap-3">
                        @foreach($questions as $index => $question)
                        <button wire:click="goToQuestion({{ $index }})"
                                @click="sidebarOpen = false"
                                class="relative aspect-square flex items-center justify-center rounded-xl font-bold text-sm transition-all transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{
                                    'bg-blue-600 text-white shadow-lg scale-110': {{ $currentQuestionIndex }} === {{ $index }},
                                    'bg-green-500 text-white hover:bg-green-600': {{ $currentQuestionIndex }} !== {{ $index }} && {{ isset($answers[$question['id']]) && $answers[$question['id']] !== null ? 'true' : 'false' }},
                                    'border-2 border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-blue-500': {{ $currentQuestionIndex }} !== {{ $index }} && {{ !isset($answers[$question['id']]) || $answers[$question['id']] === null ? 'true' : 'false' }}
                                }">
                            {{ $index + 1 }}
                            @if($this->isQuestionFlagged($index))
                            <i class="fas fa-flag absolute -top-1 -right-1 text-yellow-500 text-xs"></i>
                            @endif
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>
    
            {{-- Sidebar Overlay (Mobile) --}}
            <div x-show="sidebarOpen" 
                 @click="sidebarOpen = false"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black bg-opacity-50 lg:hidden z-0"></div>
    
            {{-- Main Content Area --}}
            <div class="flex-1 flex flex-col overflow-hidden min-h-0">
                {{-- Question Content --}}
                <div class="flex-1 overflow-y-auto p-4 md:p-8 pb-24 md:pb-8"> {{-- Added pb-24 for mobile --}}
                    @if($this->getCurrentQuestion())
                    @php $question = $this->getCurrentQuestion(); @endphp
                    <div class="max-w-4xl mx-auto animate__animated animate__fadeIn">
                        {{-- Question Header --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8 mb-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                        {{ $currentQuestionIndex + 1 }}
                                    </div>
                                    <div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Question Type</div>
                                        <div class="font-semibold text-gray-800 dark:text-white">
                                            {{ ucfirst(str_replace('_', ' ', $question['question_type'] ?? 'multiple_choice')) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Points</div>
                                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $question['points'] ?? 1 }}</div>
                                </div>
                            </div>
    
                            {{-- Question Text --}}
                            <div class="prose prose-lg max-w-none dark:prose-invert math-content text-gray-800 dark:text-gray-200"
                                 wire:key="question-{{ $question['id'] }}"
                                 x-init="$nextTick(() => { if (window.MathJax) { MathJax.typesetPromise([$el]).catch(err => console.error('MathJax error:', err)); } })">
                                {!! $question['question_text'] ?? '' !!}
                            </div>
                        </div>
    
                        {{-- Answer Options --}}
                        <div class="space-y-4">
                            @if(($question['question_type'] ?? '') === 'multiple_choice')
                                @if(is_array($question['options']) && count($question['options']) > 0)
                                    @foreach($question['options'] as $optionIndex => $option)
                                        @if(trim(strip_tags($option)))
                                        <label class="block cursor-pointer group">
                                            <input type="radio" 
                                                   wire:click="saveAnswer({{ $question['id'] }}, {{ $optionIndex }})"
                                                   name="question_{{ $question['id'] }}" 
                                                   value="{{ $optionIndex }}"
                                                   class="hidden peer"
                                                   {{ isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex ? 'checked' : '' }}>
                                            
                                            <div class="bg-white dark:bg-gray-800 border-2 rounded-2xl p-5 transition-all duration-300 border-gray-200 dark:border-gray-700 group-hover:border-blue-300 dark:group-hover:border-blue-700 transform group-hover:scale-[1.02]"
                                                 :class="{ 
                                                     'border-blue-500 bg-blue-50 dark:bg-blue-900/20 shadow-lg': {{ isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex ? 'true' : 'false' }}
                                                 }">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold mr-4 shadow-lg">
                                                        {{ chr(65 + $optionIndex) }}
                                                    </div>
                                                    <div class="flex-1 prose dark:prose-invert math-content text-gray-700 dark:text-gray-200"
                                                         wire:key="option-{{ $question['id'] }}-{{ $optionIndex }}"
                                                         x-init="$nextTick(() => renderMathInElement($el))">
                                                        {!! $option !!}
                                                    </div>
                                                    <div class="flex-shrink-0 ml-4">
                                                        <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                                             :class="{
                                                                 'border-blue-500 bg-blue-500': {{ isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex ? 'true' : 'false' }},
                                                                 'border-gray-300 dark:border-gray-600': {{ !isset($answers[$question['id']]) || $answers[$question['id']] != $optionIndex ? 'true' : 'false' }}
                                                             }">
                                                            <i class="fas fa-check text-white text-xs"
                                                               :class="{ 'opacity-100': {{ isset($answers[$question['id']]) && $answers[$question['id']] == $optionIndex ? 'true' : 'false' }}, 'opacity-0': {{ !isset($answers[$question['id']]) || $answers[$question['id']] != $optionIndex ? 'true' : 'false' }} }"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </label>
                                        @endif
                                    @endforeach
                                @endif
    
                            @elseif(($question['question_type'] ?? '') === 'true_false')
                                <label class="block cursor-pointer group">
                                    <input type="radio" 
                                           wire:click="saveAnswer({{ $question['id'] }}, 0)"
                                           name="question_{{ $question['id'] }}" 
                                           value="0"
                                           class="hidden peer"
                                           {{ isset($answers[$question['id']]) && $answers[$question['id']] == 0 ? 'checked' : '' }}>
                                    
                                    <div class="bg-white dark:bg-gray-800 border-2 rounded-2xl p-6 transition-all duration-300 border-gray-200 dark:border-gray-700 group-hover:border-green-300 dark:group-hover:border-green-700 transform group-hover:scale-[1.02]"
                                         :class="{
                                             'border-green-500 bg-green-50 dark:bg-green-900/20 shadow-lg': {{ isset($answers[$question['id']]) && $answers[$question['id']] == 0 ? 'true' : 'false' }}
                                         }">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 text-white flex items-center justify-center font-bold shadow-lg">
                                                    <i class="fas fa-check text-2xl"></i>
                                                </div>
                                                <span class="text-xl font-semibold text-gray-800 dark:text-white">True</span>
                                            </div>
                                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                                 :class="{
                                                     'border-green-500 bg-green-500': {{ isset($answers[$question['id']]) && $answers[$question['id']] == 0 ? 'true' : 'false' }},
                                                     'border-gray-300 dark:border-gray-600': {{ !isset($answers[$question['id']]) || $answers[$question['id']] != 0 ? 'true' : 'false' }}
                                                 }">
                                                <i class="fas fa-check text-white text-xs"
                                                   :class="{ 'opacity-100': {{ isset($answers[$question['id']]) && $answers[$question['id']] == 0 ? 'true' : 'false' }}, 'opacity-0': {{ !isset($answers[$question['id']]) || $answers[$question['id']] != 0 ? 'true' : 'false' }} }"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
    
                                <label class="block cursor-pointer group">
                                    <input type="radio" 
                                           wire:click="saveAnswer({{ $question['id'] }}, 1)"
                                           name="question_{{ $question['id'] }}" 
                                           value="1"
                                           class="hidden peer"
                                           {{ isset($answers[$question['id']]) && $answers[$question['id']] == 1 ? 'checked' : '' }}>
                                    
                                    <div class="bg-white dark:bg-gray-800 border-2 rounded-2xl p-6 transition-all duration-300 border-gray-200 dark:border-gray-700 group-hover:border-red-300 dark:group-hover:border-red-700 transform group-hover:scale-[1.02]"
                                         :class="{
                                             'border-red-500 bg-red-50 dark:bg-red-900/20 shadow-lg': {{ isset($answers[$question['id']]) && $answers[$question['id']] == 1 ? 'true' : 'false' }}
                                         }">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-pink-600 text-white flex items-center justify-center font-bold shadow-lg">
                                                    <i class="fas fa-times text-2xl"></i>
                                                </div>
                                                <span class="text-xl font-semibold text-gray-800 dark:text-white">False</span>
                                            </div>
                                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                                 :class="{
                                                     'border-red-500 bg-red-500': {{ isset($answers[$question['id']]) && $answers[$question['id']] == 1 ? 'true' : 'false' }},
                                                     'border-gray-300 dark:border-gray-600': {{ !isset($answers[$question['id']]) || $answers[$question['id']] != 1 ? 'true' : 'false' }}
                                                 }">
                                                <i class="fas fa-check text-white text-xs"
                                                   :class="{ 'opacity-100': {{ isset($answers[$question['id']]) && $answers[$question['id']] == 1 ? 'true' : 'false' }}, 'opacity-0': {{ !isset($answers[$question['id']]) || $answers[$question['id']] != 1 ? 'true' : 'false' }} }"></i>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
    
                {{-- Bottom Navigation - Fixed for Mobile --}}
                <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 md:p-6 shadow-lg sticky bottom-0 z-10 md:relative">
                    <div class="max-w-4xl mx-auto">
                        {{-- Progress Info --}}
                        <div class="flex items-center justify-between mb-4 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <span>{{ $this->getAnsweredQuestionsCount() }} answered</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-circle-notch text-gray-400"></i>
                                <span>{{ count($questions) - $this->getAnsweredQuestionsCount() }} remaining</span>
                            </div>
                        </div>
    
                        {{-- Progress Bar --}}
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-6 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-full rounded-full transition-all duration-500 shadow-lg"
                                 :style="`width: ${({{ $this->getAnsweredQuestionsCount() }} / {{ count($questions) }}) * 100}%`"></div>
                        </div>
    
                        {{-- Navigation Buttons --}}
                        <div class="flex items-center justify-between gap-4">
                            <button wire:click="previousQuestion"
                                    class="flex items-center space-x-2 px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 flex-1 justify-center"
                                    {{ !$this->canGoPrevious() ? 'disabled' : '' }}>
                                <i class="fas fa-arrow-left"></i>
                                <span class="hidden sm:inline">Previous</span>
                            </button>
    
                            <div class="flex-1 text-center min-w-0 px-2">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Progress</div>
                                <div class="text-lg font-bold text-gray-800 dark:text-white whitespace-nowrap">
                                    {{ round($this->getProgressPercentage(), 1) }}%
                                </div>
                            </div>
    
                            @if($this->isLastQuestion())
                            <button wire:click="showSubmitConfirmation"
                                    class="flex items-center space-x-2 px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-bold hover:from-green-700 hover:to-emerald-700 transition-all transform hover:scale-105 shadow-lg flex-1 justify-center">
                                <i class="fas fa-paper-plane"></i>
                                <span class="hidden sm:inline">Submit</span>
                                <span class="sm:hidden">End</span>
                            </button>
                            @else
                            <button wire:click="nextQuestion"
                                    class="flex items-center space-x-2 px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-bold hover:from-blue-700 hover:to-indigo-700 transition-all transform hover:scale-105 shadow-lg flex-1 justify-center">
                                <span class="hidden sm:inline">Next</span>
                                <span class="sm:hidden">Next</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    





















    @endif

    {{-- Submit Confirmation Modal --}}
    @if($showSubmitModal)
    <div class="fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4 animate__animated animate__fadeIn">
        <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-md w-full shadow-2xl animate__animated animate__bounceIn">
            <div class="p-8">
                <div class="w-20 h-20 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center mx-auto mb-6 animate__animated animate__pulse animate__infinite">
                    <i class="fas fa-exclamation-triangle text-white text-4xl"></i>
                </div>

                <h3 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-4">
                    Submit Your Exam?
                </h3>

                <p class="text-center text-gray-600 dark:text-gray-400 mb-6">
                    Once submitted, you cannot change your answers. Make sure you've reviewed all questions.
                </p>

                {{-- Summary Stats --}}
                <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-6 mb-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Questions Answered:</span>
                        <span class="font-bold text-gray-800 dark:text-white">{{ $this->getAnsweredQuestionsCount() }} / {{ count($questions) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Time Remaining:</span>
                        <span class="font-bold text-gray-800 dark:text-white" x-text="formatTime(timeRemaining)"></span>
                    </div>
                    @if($this->getAnsweredQuestionsCount() < count($questions))
                    <div class="flex items-start space-x-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                        <span class="text-sm text-yellow-800 dark:text-yellow-300">
                            You have {{ count($questions) - $this->getAnsweredQuestionsCount() }} unanswered questions
                        </span>
                    </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-4">
                    <button wire:click="cancelSubmission"
                            class="flex-1 px-6 py-4 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                    <button wire:click="submitExam"
                            class="flex-1 px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-bold hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg">
                        <i class="fas fa-check mr-2"></i>Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Alpine.js Component --}}
    @push('scripts')
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('modernCbtExam', () => ({
            timeRemaining: @js($timeRemaining ?? 0),
            timerInterval: null,
            sidebarOpen: false,
            questionDropdownOpen: false,
            examStarted: @js($examStarted ?? false),
            examCompleted: @js($examCompleted ?? false),

            init() {
                console.log('Alpine initialized', {
                    examStarted: this.examStarted,
                    examCompleted: this.examCompleted,
                    timeRemaining: this.timeRemaining
                });

                // Start timer if exam is active
                if (this.examStarted && !this.examCompleted) {
                    this.startTimer();
                    this.markExamStarted();
                }

                // Listen for Livewire events
                this.$wire.$on('startTimer', () => {
                    this.examStarted = true;
                    this.startTimer();
                });
                
                this.$wire.$on('examCompleted', () => {
                    this.examCompleted = true;
                    this.stopTimer();
                });
                
                this.$wire.$on('markExamStarted', () => {
                    this.examStarted = true;
                    this.markExamStarted();
                });
                
                this.$wire.$on('allowFullscreenExit', () => {
                    this.allowFullscreenExit();
                });
                
                this.$wire.$on('questionChanged', () => {
                    this.renderMath();
                });

                // Initial MathJax render
                this.$nextTick(() => this.renderMath());

                // Re-render math after Livewire updates
                Livewire.hook('morph.updated', ({ el, component }) => {
                    this.$nextTick(() => this.renderMath());
                });
            },

            startTimer() {
                if (this.timerInterval) clearInterval(this.timerInterval);
                
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
            },

            toggleSidebar() {
                this.sidebarOpen = !this.sidebarOpen;
            },

            closeDropdown(event) {
                if (this.questionDropdownOpen && !event.target.closest('.relative')) {
                    this.questionDropdownOpen = false;
                }
            },

            markExamStarted() {
                if (window.examSecurity) {
                    window.examSecurity.markExamStarted();
                }
            },

            allowFullscreenExit() {
                if (window.examSecurity) {
                    window.examSecurity.allowFullscreenExit();
                }
            },

            renderMath() {
                if (typeof MathJax !== 'undefined' && MathJax.typesetPromise) {
                    // Clear any existing MathJax elements first
                    document.querySelectorAll('.math-content').forEach(element => {
                        const mjxContainers = element.querySelectorAll('mjx-container');
                        mjxContainers.forEach(container => {
                            // Store the original text before removing MathJax rendering
                            if (!container.dataset.originalText) {
                                container.dataset.originalText = container.textContent;
                            }
                        });
                    });

                    // Re-render MathJax
                    MathJax.typesetClear();
                    MathJax.typesetPromise().then(() => {
                        console.log('MathJax rendered successfully');
                    }).catch(err => {
                        console.error('MathJax error:', err);
                    });
                }
            },

            renderMathInElement(element) {
                this.$nextTick(() => {
                    if (typeof MathJax !== 'undefined' && MathJax.typesetPromise) {
                        MathJax.typesetPromise([element]).catch(err => {
                            console.error('MathJax element error:', err);
                        });
                    }
                });
            }
        }));
    });

    // Global function for rendering math in specific elements
    window.renderMathInElement = function(element) {
        if (typeof MathJax !== 'undefined' && MathJax.typesetPromise) {
            MathJax.typesetPromise([element]).catch(err => {
                console.error('MathJax error:', err);
            });
        }
    };
    </script>
    @endpush

    @push('styles')
    <style>
        /* Smooth transitions */
        * {
            transition-property: background-color, border-color, color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 200ms;
        }

        /* Exam mode - prevent selection */
        .exam-mode {
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #3b82f6, #6366f1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #2563eb, #4f46e5);
        }

        /* Dark mode scrollbar */
        .dark ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        /* MathJax styling */
        .math-content mjx-container {
            display: inline-block !important;
            margin: 0.2em 0;
        }

        .math-content mjx-container[display="true"] {
            display: block !important;
            margin: 1em 0;
        }

        /* Prose styling */
        .prose img {
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .prose code {
            background-color: rgba(59, 130, 246, 0.1);
            padding: 0.2em 0.4em;
            border-radius: 0.25rem;
        }

        .dark .prose code {
            background-color: rgba(96, 165, 250, 0.2);
        }

        /* Prevent animations on resize */
        .resize-animation-stopper * {
            animation: none !important;
            transition: none !important;
        }

        /* Loading state */
        [wire\:loading] {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Focus styles */
        button:focus,
        input:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Radio button styling */
        input[type="radio"]:checked ~ div {
            animation: radioCheck 0.3s ease-in-out;
        }

        @keyframes radioCheck {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .math-content {
                font-size: 0.9em;
            }
        }

        /* Print prevention */
        @media print {
            body { display: none !important; }
        }
    </style>
    @endpush
</div>