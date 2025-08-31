{{-- resources/views/livewire/cbt/cbt-exam-viewer.blade.php --}}
<div class="min-h-screen bg-slate-50" 
     x-data="cbtExamViewer()" 
     x-init="initComponent()"
     @visibilitychange.window="handleVisibilityChange($event)"
     @beforeunload.window="handleBeforeUnload($event)">

    {{-- Pre-Start Instructions --}}
    <div x-show="examState === 'pre_start'" x-transition class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            {{-- Header --}}
            <div class="bg-slate-800 text-white p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">{{ $exam->title }}</h1>
                        <p class="text-slate-300 mt-1">{{ $exam->course->title ?? 'General Exam' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-300">Exam Code</div>
                        <div class="font-mono text-lg">{{ $exam->exam_code }}</div>
                    </div>
                </div>
            </div>

            {{-- Exam Information --}}
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>Exam Details
                        </h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-600">Duration</span>
                                <span class="font-medium">{{ $exam->formatted_duration }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Total Questions</span>
                                <span class="font-medium">{{ $exam->total_questions }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Passing Score</span>
                                <span class="font-medium">{{ $exam->pass_percentage }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Question Type</span>
                                <span class="font-medium capitalize">{{ str_replace('_', ' ', $exam->exam_type) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Difficulty</span>
                                <span class="font-medium capitalize">{{ $exam->difficulty_level }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Max Attempts</span>
                                <span class="font-medium">{{ $exam->max_attempts }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">
                            <i class="fas fa-cogs mr-2 text-green-500"></i>Exam Features
                        </h3>
                        
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <i class="fas {{ $exam->allow_navigation ? 'fa-check text-green-500' : 'fa-times text-red-500' }} mr-3"></i>
                                <span class="text-sm">Question Navigation</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas {{ $exam->randomize_questions ? 'fa-check text-green-500' : 'fa-times text-red-500' }} mr-3"></i>
                                <span class="text-sm">Randomized Questions</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas {{ $exam->randomize_options ? 'fa-check text-green-500' : 'fa-times text-red-500' }} mr-3"></i>
                                <span class="text-sm">Randomized Options</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas {{ $exam->allow_review ? 'fa-check text-green-500' : 'fa-times text-red-500' }} mr-3"></i>
                                <span class="text-sm">Review Before Submit</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas {{ $exam->show_results_immediately ? 'fa-check text-green-500' : 'fa-times text-red-500' }} mr-3"></i>
                                <span class="text-sm">Immediate Results</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Instructions --}}
                @if($exam->instructions)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-3">
                        <i class="fas fa-clipboard-list mr-2"></i>Instructions
                    </h3>
                    <div class="prose prose-blue max-w-none text-slate-700">
                        {!! nl2br(e($exam->instructions)) !!}
                    </div>
                </div>
                @endif

                {{-- Important Notes --}}
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-amber-800 mb-3">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Important Notes
                    </h3>
                    <ul class="space-y-2 text-sm text-amber-700">
                        <li class="flex items-start">
                            <i class="fas fa-dot-circle mr-3 mt-0.5 text-amber-500"></i>
                            Ensure you have a stable internet connection throughout the exam
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-dot-circle mr-3 mt-0.5 text-amber-500"></i>
                            Do not refresh or close your browser during the exam
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-dot-circle mr-3 mt-0.5 text-amber-500"></i>
                            Your progress is automatically saved as you answer questions
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-dot-circle mr-3 mt-0.5 text-amber-500"></i>
                            The exam will auto-submit when time expires
                        </li>
                        @if(!$exam->allow_navigation)
                        <li class="flex items-start">
                            <i class="fas fa-dot-circle mr-3 mt-0.5 text-amber-500"></i>
                            You cannot go back to previous questions once answered
                        </li>
                        @endif
                    </ul>
                </div>

                {{-- System Check --}}
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <span class="text-green-700 font-medium">System Ready</span>
                        </div>
                        <span class="text-sm text-green-600" x-text="'Connection: ' + (navigator.onLine ? 'Online' : 'Offline')"></span>
                    </div>
                </div>

                {{-- Start Button --}}
                <div class="text-center">
                    <button wire:click="startExam" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-play mr-2"></i>Start Exam
                    </button>
                    <p class="text-sm text-slate-500 mt-3">Click to begin your exam session</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Exam Interface --}}
    <div x-show="examState === 'in_progress'" x-transition class="min-h-screen bg-slate-100">
        {{-- Exam Header --}}
        <div class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-40">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between py-4">
                    {{-- Exam Info --}}
                    <div>
                        <h1 class="text-xl font-bold text-slate-800">{{ $exam->title }}</h1>
                        <p class="text-sm text-slate-600">{{ $exam->course->title ?? 'General Exam' }}</p>
                    </div>

                    {{-- Status Bar --}}
                    <div class="flex items-center space-x-6">
                        {{-- Timer --}}
                        <div class="flex items-center bg-red-50 text-red-700 px-4 py-2 rounded-lg border border-red-200">
                            <i class="fas fa-clock mr-2"></i>
                            <span class="font-mono text-lg font-bold" x-text="formatTime(timeRemaining)"></span>
                        </div>

                        {{-- Progress --}}
                        <div class="flex items-center bg-blue-50 text-blue-700 px-4 py-2 rounded-lg border border-blue-200">
                            <i class="fas fa-tasks mr-2"></i>
                            <span class="font-semibold" x-text="(currentQuestionIndex + 1) + ' / ' + examQuestions.length"></span>
                        </div>

                        {{-- Question Navigation Toggle --}}
                        @if($exam->allow_navigation)
                        <button @click="showQuestionNavigation = !showQuestionNavigation"
                                class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-lg border border-slate-300 transition-colors">
                            <i class="fas fa-th-large mr-2"></i>Questions
                        </button>
                        @endif

                        {{-- Emergency Exit --}}
                        <button @click="confirmSubmission()"
                                class="bg-orange-100 hover:bg-orange-200 text-orange-700 px-4 py-2 rounded-lg border border-orange-300 transition-colors">
                            <i class="fas fa-sign-out-alt mr-2"></i>Submit
                        </button>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="pb-4">
                    <div class="flex justify-between text-sm text-slate-600 mb-2">
                        <span>Progress: {{ $this->getAnsweredCount() }}/{{ count($examQuestions) }} answered</span>
                        <span x-text="getProgressPercentage() + '% complete'"></span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-300" 
                             :style="'width: ' + getProgressPercentage() + '%'"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Area --}}
        <div class="container mx-auto px-4 py-6 max-w-6xl">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Question Content --}}
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        {{-- Question Header --}}
                        <div class="bg-slate-50 px-6 py-4 border-b border-slate-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-800">
                                        Question <span x-text="currentQuestionIndex + 1"></span>
                                    </h2>
                                    <div class="flex items-center mt-1 space-x-4">
                                        <span class="text-sm text-slate-600">
                                            <i class="fas fa-star mr-1 text-yellow-500"></i>
                                            <span x-text="getCurrentQuestion()?.points || 1"></span> points
                                        </span>
                                        <span class="text-sm text-slate-600" x-show="getCurrentQuestion()?.difficulty">
                                            <i class="fas fa-layer-group mr-1 text-purple-500"></i>
                                            <span x-text="getCurrentQuestion()?.difficulty" class="capitalize"></span>
                                        </span>
                                    </div>
                                </div>
                                
                                {{-- Flag Question --}}
                                <button @click="toggleQuestionFlag()"
                                        :class="flaggedQuestions.includes(getCurrentQuestion()?.id) ? 'text-yellow-500 bg-yellow-50' : 'text-slate-400 bg-slate-50'"
                                        class="px-3 py-2 rounded-lg border transition-all hover:bg-yellow-50 hover:text-yellow-500">
                                    <i class="fas fa-flag mr-2"></i>
                                    <span x-text="flaggedQuestions.includes(getCurrentQuestion()?.id) ? 'Flagged' : 'Flag'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Question Content --}}
                        <div class="p-6">
                            <template x-if="getCurrentQuestion()">
                                <div>
                                    {{-- Question Text --}}
                                    <div class="mb-6">
                                        <div class="prose prose-slate max-w-none">
                                            <p class="text-lg leading-relaxed text-slate-800" x-html="getCurrentQuestion().text"></p>
                                        </div>
                                        
                                        {{-- Question Media --}}
                                        <div x-show="getCurrentQuestion().media_url" class="mt-4">
                                            <template x-if="getCurrentQuestion().media_type === 'image'">
                                                <img :src="getCurrentQuestion().media_url" 
                                                     alt="Question image" 
                                                     class="max-w-full h-auto rounded-lg border border-slate-200">
                                            </template>
                                            <template x-if="getCurrentQuestion().media_type === 'video'">
                                                <video controls class="max-w-full h-auto rounded-lg border border-slate-200">
                                                    <source :src="getCurrentQuestion().media_url" type="video/mp4">
                                                </video>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Answer Options --}}
                                    <div class="space-y-3" x-show="getCurrentQuestion().type === 'multiple_choice'">
                                        <template x-for="(option, index) in getCurrentQuestion().options" :key="index">
                                            <label class="flex items-start p-4 rounded-lg border-2 cursor-pointer transition-all hover:bg-slate-50"
                                                   :class="userAnswers[getCurrentQuestion().id] == index ? 'border-blue-500 bg-blue-50' : 'border-slate-200 hover:border-slate-300'">
                                                <input type="radio" 
                                                       :name="'question_' + getCurrentQuestion().id"
                                                       :value="index"
                                                       x-model="userAnswers[getCurrentQuestion().id]"
                                                       @change="saveAnswer(getCurrentQuestion().id, $event.target.value)"
                                                       class="mt-1 mr-4 text-blue-600 focus:ring-blue-500">
                                                <div class="flex-1">
                                                    <div class="flex items-center">
                                                        <span class="w-8 h-8 bg-slate-600 text-white text-sm rounded-full flex items-center justify-center mr-3 font-medium" 
                                                              x-text="String.fromCharCode(65 + index)"></span>
                                                        <span class="text-slate-800" x-text="option"></span>
                                                    </div>
                                                </div>
                                            </label>
                                        </template>
                                    </div>

                                    {{-- Text Answer --}}
                                    <div x-show="getCurrentQuestion().type === 'text'" class="space-y-3">
                                        <textarea x-model="userAnswers[getCurrentQuestion().id]"
                                                  @input="saveAnswer(getCurrentQuestion().id, $event.target.value)"
                                                  placeholder="Enter your answer here..."
                                                  class="w-full p-4 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                                  rows="6"></textarea>
                                    </div>

                                    {{-- True/False --}}
                                    <div x-show="getCurrentQuestion().type === 'true_false'" class="space-y-3">
                                        <label class="flex items-center p-4 rounded-lg border-2 cursor-pointer transition-all hover:bg-slate-50"
                                               :class="userAnswers[getCurrentQuestion().id] === 'true' ? 'border-green-500 bg-green-50' : 'border-slate-200'">
                                            <input type="radio" 
                                                   :name="'question_' + getCurrentQuestion().id"
                                                   value="true"
                                                   x-model="userAnswers[getCurrentQuestion().id]"
                                                   @change="saveAnswer(getCurrentQuestion().id, 'true')"
                                                   class="mr-4 text-green-600">
                                            <i class="fas fa-check-circle mr-3 text-green-500"></i>
                                            <span class="text-slate-800 font-medium">True</span>
                                        </label>
                                        
                                        <label class="flex items-center p-4 rounded-lg border-2 cursor-pointer transition-all hover:bg-slate-50"
                                               :class="userAnswers[getCurrentQuestion().id] === 'false' ? 'border-red-500 bg-red-50' : 'border-slate-200'">
                                            <input type="radio" 
                                                   :name="'question_' + getCurrentQuestion().id"
                                                   value="false"
                                                   x-model="userAnswers[getCurrentQuestion().id]"
                                                   @change="saveAnswer(getCurrentQuestion().id, 'false')"
                                                   class="mr-4 text-red-600">
                                            <i class="fas fa-times-circle mr-3 text-red-500"></i>
                                            <span class="text-slate-800 font-medium">False</span>
                                        </label>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Navigation Footer --}}
                        <div class="bg-slate-50 px-6 py-4 border-t border-slate-200">
                            <div class="flex justify-between items-center">
                                {{-- Previous Button --}}
                                <button @click="previousQuestion()" 
                                        :disabled="isFirstQuestion() || !allowNavigation"
                                        :class="isFirstQuestion() || !allowNavigation ? 'opacity-50 cursor-not-allowed' : 'hover:bg-slate-600'"
                                        class="bg-slate-500 text-white px-6 py-2 rounded-lg transition-all">
                                    <i class="fas fa-arrow-left mr-2"></i>Previous
                                </button>

                                {{-- Question Status --}}
                                <div class="text-center">
                                    <div class="text-sm text-slate-600 mb-1">
                                        <span x-show="getUserAnswerForQuestion(getCurrentQuestion()?.id) !== null" class="text-green-600">
                                            <i class="fas fa-check mr-1"></i>Answered
                                        </span>
                                        <span x-show="getUserAnswerForQuestion(getCurrentQuestion()?.id) === null" class="text-orange-600">
                                            <i class="fas fa-exclamation mr-1"></i>Not answered
                                        </span>
                                    </div>
                                    <div class="flex justify-center space-x-1">
                                        <template x-for="(question, index) in examQuestions.slice(Math.max(0, currentQuestionIndex - 2), currentQuestionIndex + 3)" :key="question.id">
                                            <div :class="index + Math.max(0, currentQuestionIndex - 2) === currentQuestionIndex ? 'bg-blue-500' : getQuestionDotColor(question.id)"
                                                 class="w-2 h-2 rounded-full"></div>
                                        </template>
                                    </div>
                                </div>

                                {{-- Next/Submit Button --}}
                                <div>
                                    <button x-show="!isLastQuestion()" 
                                            @click="nextQuestion()"
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-all">
                                        Next<i class="fas fa-arrow-right ml-2"></i>
                                    </button>
                                    
                                    <button x-show="isLastQuestion()" 
                                            @click="confirmSubmission()"
                                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-all">
                                        <i class="fas fa-check-circle mr-2"></i>Submit
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1">
                    {{-- Quick Stats --}}
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">
                            <i class="fas fa-chart-pie mr-2 text-blue-500"></i>Progress
                        </h3>
                        
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Answered</span>
                                <span class="font-semibold text-green-600" x-text="getAnsweredCount() + '/' + examQuestions.length"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Flagged</span>
                                <span class="font-semibold text-yellow-600" x-text="getFlaggedCount()"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-600">Remaining</span>
                                <span class="font-semibold text-slate-800" x-text="(examQuestions.length - getAnsweredCount())"></span>
                            </div>
                            
                            {{-- Progress Circle --}}
                            <div class="text-center mt-4">
                                <div class="relative inline-block">
                                    <svg class="w-20 h-20 transform -rotate-90">
                                        <circle cx="40" cy="40" r="32" stroke="currentColor" stroke-width="6" fill="transparent" class="text-slate-200"/>
                                        <circle cx="40" cy="40" r="32" stroke="currentColor" stroke-width="6" fill="transparent" 
                                                :stroke-dasharray="201.06" 
                                                :stroke-dashoffset="201.06 - (201.06 * getProgressPercentage() / 100)"
                                                class="text-blue-500 transition-all duration-500"/>
                                    </svg>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="text-sm font-bold text-slate-800" x-text="Math.round(getProgressPercentage()) + '%'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">
                            <i class="fas fa-tools mr-2 text-green-500"></i>Quick Actions
                        </h3>
                        
                        <div class="space-y-3">
                            @if($exam->allow_navigation)
                            <button @click="showQuestionNavigation = true"
                                    class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-3 rounded-lg transition-all text-sm border border-blue-200">
                                <i class="fas fa-th-large mr-2"></i>Question Overview
                            </button>
                            @endif
                            
                            <button @click="goToFirstUnanswered()"
                                    class="w-full bg-orange-50 hover:bg-orange-100 text-orange-700 px-4 py-3 rounded-lg transition-all text-sm border border-orange-200">
                                <i class="fas fa-search mr-2"></i>First Unanswered
                            </button>
                            
                            <button @click="goToFirstFlagged()"
                                    x-show="getFlaggedCount() > 0"
                                    class="w-full bg-yellow-50 hover:bg-yellow-100 text-yellow-700 px-4 py-3 rounded-lg transition-all text-sm border border-yellow-200">
                                <i class="fas fa-flag mr-2"></i>Review Flagged
                            </button>
                        </div>
                    </div>

                    {{-- Exam Summary --}}
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-slate-800 mb-4">
                            <i class="fas fa-clipboard-check mr-2 text-purple-500"></i>Summary
                        </h3>
                        
                        <div class="text-sm space-y-2">
                            <div class="flex justify-between">
                                <span class="text-slate-600">Duration</span>
                                <span class="font-medium">{{ $exam->formatted_duration }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Questions</span>
                                <span class="font-medium">{{ $exam->total_questions }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Pass Mark</span>
                                <span class="font-medium">{{ $exam->pass_percentage }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Attempt</span>
                                <span class="font-medium" x-text="'#' + (currentSession?.attempt_number || 1)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Question Navigation Panel --}}
        <div x-show="showQuestionNavigation" 
             x-transition:enter="transform transition ease-in-out duration-300 translate-x-full"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300 translate-x-0"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 w-80 bg-white shadow-2xl z-50 overflow-y-auto">
            
            <div class="p-6">
                {{-- Header --}}
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-slate-800">Question Navigator</h3>
                    <button @click="showQuestionNavigation = false" 
                            class="text-slate-400 hover:text-slate-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                {{-- Legend --}}
                <div class="grid grid-cols-2 gap-2 mb-6 text-xs">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded mr-2"></div>
                        <span>Answered</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded mr-2"></div>
                        <span>Flagged</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded mr-2"></div>
                        <span>Current</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-slate-300 rounded mr-2"></div>
                        <span>Unanswered</span>
                    </div>
                </div>

                {{-- Question Grid --}}
                <div class="grid grid-cols-6 gap-2">
                    <template x-for="(question, index) in examQuestions" :key="question.id">
                        <button @click="navigateToQuestion(index)" 
                                :class="getQuestionNavClass(question.id, index)"
                                class="w-10 h-10 rounded-lg font-medium text-sm transition-all border-2">
                            <span x-text="index + 1"></span>
                        </button>
                    </template>
                </div>

                {{-- Summary Stats --}}
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Total Questions</span>
                            <span class="font-medium" x-text="examQuestions.length"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Answered</span>
                            <span class="font-medium text-green-600" x-text="getAnsweredCount()"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Flagged</span>
                            <span class="font-medium text-yellow-600" x-text="getFlaggedCount()"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Remaining</span>
                            <span class="font-medium text-slate-800" x-text="examQuestions.length - getAnsweredCount()"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit Confirmation Modal --}}
    <div x-show="showSubmitConfirmation" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-orange-500 text-2xl"></i>
                    </div>
                    
                    <h3 class="text-lg font-semibold text-slate-800 mb-2">Submit Exam?</h3>
                    <p class="text-slate-600 mb-6">
                        Are you sure you want to submit your exam? This action cannot be undone.
                    </p>
                    
                    {{-- Summary --}}
                    <div class="bg-slate-50 rounded-lg p-4 mb-6 text-sm">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-slate-500">Answered</div>
                                <div class="font-semibold" x-text="getAnsweredCount() + '/' + examQuestions.length"></div>
                            </div>
                            <div>
                                <div class="text-slate-500">Time Left</div>
                                <div class="font-semibold" x-text="formatTime(timeRemaining)"></div>
                            </div>
                            <div>
                                <div class="text-slate-500">Flagged</div>
                                <div class="font-semibold text-yellow-600" x-text="getFlaggedCount()"></div>
                            </div>
                            <div>
                                <div class="text-slate-500">Unanswered</div>
                                <div class="font-semibold text-red-600" x-text="examQuestions.length - getAnsweredCount()"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button @click="cancelSubmission()" 
                                class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-3 rounded-lg transition-all">
                            Continue Exam
                        </button>
                        <button wire:click="submitExam" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg transition-all font-semibold">
                            Submit Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Results Modal --}}
    <div x-show="showResultModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            {{-- Result Header --}}
            <div class="bg-slate-800 text-white p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold">Exam Results</h2>
                        <p class="text-slate-300">{{ $exam->title }}</p>
                    </div>
                    <button wire:click="closeResultModal" 
                            class="text-slate-300 hover:text-white">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            {{-- Results Content --}}
            @if($currentSession)
            <div class="p-6">
                {{-- Score Display --}}
                <div class="text-center mb-8">
                    <div class="relative inline-block mb-4">
                        <svg class="w-32 h-32 transform -rotate-90">
                            <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-200"/>
                            <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" 
                                    stroke-dasharray="351.86" 
                                    stroke-dashoffset="{{ 351.86 - (351.86 * $currentSession->percentage_score / 100) }}"
                                    class="{{ $currentSession->passed ? 'text-green-500' : 'text-red-500' }} transition-all duration-1000"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                            <span class="text-3xl font-bold {{ $currentSession->passed ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($currentSession->percentage_score, 1) }}%
                            </span>
                            <span class="text-sm text-slate-500">Score</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <span class="px-6 py-2 rounded-full text-lg font-semibold {{ $currentSession->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $currentSession->passed ? 'PASSED' : 'FAILED' }}
                        </span>
                    </div>

                    <div class="text-slate-600">
                        Grade: <span class="font-bold text-2xl text-slate-800">{{ $currentSession->grade }}</span>
                    </div>
                </div>

                {{-- Detailed Results --}}
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-slate-50 rounded-lg p-4">
                        <h4 class="font-semibold text-slate-800 mb-3">Performance Breakdown</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-600">Correct Answers</span>
                                <span class="font-medium text-green-600">{{ $currentSession->correct_answers }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Wrong Answers</span>
                                <span class="font-medium text-red-600">{{ $currentSession->wrong_answers }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Unanswered</span>
                                <span class="font-medium text-slate-600">{{ $currentSession->unanswered_questions }}</span>
                            </div>
                            <div class="flex justify-between border-t border-slate-200 pt-2">
                                <span class="text-slate-600">Total Questions</span>
                                <span class="font-medium">{{ $currentSession->total_questions }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-lg p-4">
                        <h4 class="font-semibold text-slate-800 mb-3">Time Analysis</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-slate-600">Time Spent</span>
                                <span class="font-medium">{{ gmdate('H:i:s', $currentSession->time_spent_seconds) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Time Remaining</span>
                                <span class="font-medium">{{ gmdate('H:i:s', $currentSession->time_remaining_seconds) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Avg per Question</span>
                                <span class="font-medium">{{ gmdate('i:s', $currentSession->time_spent_seconds / $currentSession->total_questions) }}</span>
                            </div>
                            @if($currentSession->auto_submitted)
                            <div class="flex justify-between">
                                <span class="text-red-600">Auto Submitted</span>
                                <i class="fas fa-exclamation-triangle text-red-500"></i>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Points Breakdown --}}
                <div class="bg-slate-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-slate-800 mb-3">Points Summary</h4>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-600">Points Earned</span>
                        <span class="text-2xl font-bold text-blue-600">{{ $currentSession->points_earned }}/{{ $currentSession->total_points }}</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-3 mt-2">
                        <div class="bg-blue-500 h-3 rounded-full transition-all duration-500" 
                             style="width: {{ ($currentSession->points_earned / max(1, $currentSession->total_points)) * 100 }}%"></div>
                    </div>
                </div>

                {{-- Next Steps --}}
                <div class="text-center space-y-3">
                    @if($currentSession->passed)
                        @if($exam->exam_type === 'certification')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <i class="fas fa-certificate text-green-500 text-2xl mb-2"></i>
                            <p class="text-green-700 font-medium">Certificate Eligible!</p>
                            <p class="text-sm text-green-600">Your certificate will be processed within 24-48 hours.</p>
                        </div>
                        @endif
                    @else
                        @php $remainingAttempts = $exam->max_attempts - $currentSession->attempt_number; @endphp
                        @if($remainingAttempts > 0)
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <i class="fas fa-redo text-blue-500 text-xl mb-2"></i>
                            <p class="text-blue-700 font-medium">{{ $remainingAttempts }} attempt(s) remaining</p>
                            <p class="text-sm text-blue-600">Review the material and try again when ready.</p>
                        </div>
                        @endif
                    @endif

                    <button wire:click="finishExam" 
                            class="bg-slate-600 hover:bg-slate-700 text-white px-8 py-3 rounded-lg transition-all">
                        <i class="fas fa-home mr-2"></i>Return to Dashboard
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Completed/Expired State --}}
    <div x-show="examState === 'completed' || examState === 'expired'" x-transition class="container mx-auto px-4 py-8 max-w-2xl">
        <div class="bg-white rounded-lg shadow-lg text-center p-8">
            <div x-show="examState === 'completed'">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Exam Completed!</h2>
                <p class="text-slate-600 mb-6">Your exam has been successfully submitted and is being processed.</p>
            </div>

            <div x-show="examState === 'expired'">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-orange-500 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Time Expired</h2>
                <p class="text-slate-600 mb-6">Your exam time has expired and has been automatically submitted.</p>
            </div>

            @if($exam->show_results_immediately)
                <button @click="showResultModal = true" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg mr-3 transition-all">
                    <i class="fas fa-chart-bar mr-2"></i>View Results
                </button>
            @endif

            <button wire:click="finishExam" 
                    class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-lg transition-all">
                <i class="fas fa-home mr-2"></i>Return to Dashboard
            </button>
        </div>
    </div>

    {{-- Loading State --}}
    <div wire:loading.flex class="fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center">
        <div class="bg-white rounded-lg p-6 text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-slate-600">Processing...</p>
        </div>
    </div>
</div>

{{-- Alpine.js Component --}}
@push('scripts')
<script>
function cbtExamViewer() {
    return {
        examState: @entangle('examState'),
        examQuestions: @entangle('examQuestions'),
        currentQuestionIndex: @entangle('currentQuestionIndex'),
        userAnswers: @entangle('userAnswers'),
        timeRemaining: @entangle('timeRemaining'),
        flaggedQuestions: @entangle('flaggedQuestions'),
        showQuestionNavigation: @entangle('showQuestionNavigation'),
        showSubmitConfirmation: @entangle('showSubmitConfirmation'),
        showResultModal: @entangle('showResultModal'),
        allowNavigation: {{ $exam->allow_navigation ? 'true' : 'false' }},
        tabSwitchCount: 0,
        examTimer: null,
        securityMonitor: true,

        initComponent() {
            this.initKeyboardShortcuts();
            this.initSecurityMonitoring();
            
            if (this.examState === 'in_progress') {
                this.startTimer();
            }
        },

        initKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                if (this.examState !== 'in_progress') return;

                // Prevent certain keys during exam
                if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
                    e.preventDefault();
                    this.showSecurityWarning('Developer tools are disabled during exam');
                }

                // Navigation shortcuts
                if (this.allowNavigation) {
                    if (e.key === 'ArrowLeft' && e.altKey) {
                        e.preventDefault();
                        this.previousQuestion();
                    } else if (e.key === 'ArrowRight' && e.altKey) {
                        e.preventDefault();
                        this.nextQuestion();
                    }
                }

                // Flag shortcut
                if (e.key === 'f' && e.ctrlKey) {
                    e.preventDefault();
                    this.toggleFlag();
                }
            });
        },

        initSecurityMonitoring() {
            if (!this.securityMonitor) return;

            // Disable right-click
            document.addEventListener('contextmenu', (e) => {
                if (this.examState === 'in_progress') {
                    e.preventDefault();
                }
            });

            // Monitor focus changes
            let focusLossCount = 0;
            window.addEventListener('blur', () => {
                if (this.examState === 'in_progress') {
                    focusLossCount++;
                    if (focusLossCount >= 3) {
                        this.showSecurityWarning('Multiple window focus changes detected');
                    }
                }
            });
        },

        startTimer() {
            this.examTimer = setInterval(() => {
                if (this.timeRemaining > 0) {
                    this.timeRemaining--;
                    
                    // Update Livewire component every 30 seconds
                    if (this.timeRemaining % 30 === 0) {
                        Livewire.emit('examTimerTick', this.timeRemaining);
                    }

                    // Show warnings
                    if (this.timeRemaining === 300) {
                        this.showTimeWarning('5 minutes remaining!');
                    } else if (this.timeRemaining === 60) {
                        this.showTimeWarning('1 minute remaining!');
                    }
                } else {
                    this.handleTimeExpired();
                }
            }, 1000);
        },

        handleTimeExpired() {
            clearInterval(this.examTimer);
            Livewire.emit('examTimerExpired');
        },

        handleVisibilityChange(event) {
            if (this.examState !== 'in_progress') return;
            
            const isVisible = !document.hidden;
            if (!isVisible) {
                this.tabSwitchCount++;
                Livewire.emit('visibilityChanged', false);
            }
        },

        handleBeforeUnload(event) {
            if (this.examState === 'in_progress') {
                event.preventDefault();
                event.returnValue = 'Are you sure you want to leave? Your exam progress will be saved but leaving may affect your session.';
                Livewire.emit('beforeUnload');
            }
        },

        // Navigation methods
        previousQuestion() {
            if (this.currentQuestionIndex > 0 && this.allowNavigation) {
                this.currentQuestionIndex--;
            }
        },

        nextQuestion() {
            if (this.currentQuestionIndex < this.examQuestions.length - 1) {
                this.currentQuestionIndex++;
            }
        },

        navigateToQuestion(index) {
            if (this.allowNavigation && index >= 0 && index < this.examQuestions.length) {
                this.currentQuestionIndex = index;
                this.showQuestionNavigation = false;
            }
        },

        goToFirstUnanswered() {
            for (let i = 0; i < this.examQuestions.length; i++) {
                const questionId = this.examQuestions[i].id;
                if (!this.userAnswers[questionId] || this.userAnswers[questionId] === null) {
                    this.navigateToQuestion(i);
                    return;
                }
            }
            this.showNotification('All questions have been answered!', 'success');
        },

        goToFirstFlagged() {
            for (let i = 0; i < this.examQuestions.length; i++) {
                const questionId = this.examQuestions[i].id;
                if (this.flaggedQuestions.includes(questionId)) {
                    this.navigateToQuestion(i);
                    return;
                }
            }
        },

        // Answer management
        saveAnswer(questionId, answer) {
            this.userAnswers[questionId] = answer;
            Livewire.emit('saveAnswer', questionId, answer);
        },

        toggleFlag() {
            const currentQuestion = this.getCurrentQuestion();
            if (currentQuestion) {
                Livewire.emit('toggleQuestionFlag', currentQuestion.id);
            }
        },

        // Utility methods
        getCurrentQuestion() {
            return this.examQuestions[this.currentQuestionIndex] || null;
        },

        getUserAnswerForQuestion(questionId) {
            return this.userAnswers[questionId] || null;
        },

        getAnsweredCount() {
            return Object.values(this.userAnswers).filter(answer => answer !== null && answer !== '').length;
        },

        getFlaggedCount() {
            return this.flaggedQuestions.length;
        },

        getProgressPercentage() {
            if (this.examQuestions.length === 0) return 0;
            return Math.round((this.getAnsweredCount() / this.examQuestions.length) * 100);
        },

        isFirstQuestion() {
            return this.currentQuestionIndex === 0;
        },

        isLastQuestion() {
            return this.currentQuestionIndex === this.examQuestions.length - 1;
        },

        // UI state methods
        confirmSubmission() {
            this.showSubmitConfirmation = true;
        },

        cancelSubmission() {
            this.showSubmitConfirmation = false;
        },

        // Visual helpers
        getQuestionNavClass(questionId, index) {
            let classes = 'transition-all duration-200 ';
            
            if (index === this.currentQuestionIndex) {
                classes += 'border-blue-500 bg-blue-500 text-white shadow-lg';
            } else if (this.flaggedQuestions.includes(questionId)) {
                classes += 'border-yellow-400 bg-yellow-100 text-yellow-800';
            } else if (this.userAnswers[questionId] !== null && this.userAnswers[questionId] !== '') {
                classes += 'border-green-400 bg-green-100 text-green-800';
            } else {
                classes += 'border-slate-300 bg-white text-slate-600 hover:border-slate-400';
            }
            
            return classes;
        },

        getQuestionDotColor(questionId) {
            if (this.flaggedQuestions.includes(questionId)) {
                return 'bg-yellow-400';
            } else if (this.userAnswers[questionId] !== null) {
                return 'bg-green-400';
            } else {
                return 'bg-slate-300';
            }
        },

        // Time formatting
        formatTime(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            if (hours > 0) {
                return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
            return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        },

        formatDuration(seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            
            if (hours > 0) {
                return `${hours}h ${minutes}m`;
            }
            return `${minutes}m`;
        },

        // Notification system
        showTimeWarning(message) {
            this.showNotification(message, 'warning');
        },

        showSecurityWarning(message) {
            this.showNotification(message, 'error');
        },

        showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white transform transition-all duration-300 ${
                type === 'success' ? 'bg-green-500' :
                type === 'warning' ? 'bg-yellow-500' :
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            }`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${
                        type === 'success' ? 'fa-check-circle' :
                        type === 'warning' ? 'fa-exclamation-triangle' :
                        type === 'error' ? 'fa-times-circle' : 'fa-info-circle'
                    } mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => notification.remove(), 300);
                }
            }, 5000);
        }
    }
}

// Global exam listeners
window.addEventListener('livewire:load', function () {
    // Listen for Livewire events
    Livewire.on('examCompleted', () => {
        if (window.cbtExamTimer) {
            clearInterval(window.cbtExamTimer);
        }
    });

    Livewire.on('startExamTimer', (data) => {
        // Timer is handled by Alpine component
    });

    Livewire.on('resumeExamTimer', (data) => {
        // Timer is handled by Alpine component  
    });

    Livewire.on('showTimeWarning', (message) => {
        // Handled by Alpine component
    });

    Livewire.on('showSecurityWarning', (message) => {
        // Handled by Alpine component
    });
});
</script>
@endpush

@push('styles')
<style>
/* Custom exam styles */
.exam-container {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* Disable text selection in exam mode */
.exam-mode {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Question transition effects */
.question-slide-enter {
    opacity: 0;
    transform: translateX(30px);
}

.question-slide-enter-active {
    opacity: 1;
    transform: translateX(0);
    transition: all 0.3s ease;
}

.question-slide-leave {
    opacity: 1;
    transform: translateX(0);
}

.question-slide-leave-active {
    opacity: 0;
    transform: translateX(-30px);
    transition: all 0.3s ease;
}

/* Timer pulse animation */
.timer-warning {
    animation: pulse-red 1s infinite;
}

@keyframes pulse-red {
    0%, 100% {
        background-color: rgb(254 226 226);
        color: rgb(185 28 28);
    }
    50% {
        background-color: rgb(239 68 68);
        color: white;
    }
}

/* Progress bar smooth animation */
.progress-smooth {
    transition: width 0.5s ease-in-out;
}

/* Custom scrollbar for navigation panel */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Disable zooming on mobile */
@media screen and (max-width: 768px) {
    .exam-container {
        touch-action: manipulation;
    }
}

/* Print styles (disable printing during exam) */
@media print {
    .exam-mode {
        display: none !important;
    }
    
    .no-print {
        display: none !important;
    }
    
    body::after {
        content: "Printing is disabled during exam session";
        display: block;
        text-align: center;
        font-size: 24px;
        margin-top: 50px;
    }
}

/* Focus styles for accessibility */
input[type="radio"]:focus,
button:focus,
textarea:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Question number badges */
.question-badge {
    min-width: 2.5rem;
    height: 2.5rem;
}

/* Answer option hover effects */
.answer-option:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Loading spinner */
.loading-spinner {
    border-color: transparent;
    border-top-color: #3b82f6;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

/* Mobile responsiveness */
@media (max-width: 640px) {
    .exam-header {
        padding: 1rem;
    }
    
    .question-content {
        padding: 1rem;
    }
    
    .navigation-panel {
        width: 100%;
        height: 100%;
        left: 0;
        right: 0;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .border-slate-200 {
        border-color: #000;
    }
    
    .text-slate-600 {
        color: #000;
    }
    
    .bg-slate-50 {
        background-color: #fff;
        border: 1px solid #000;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush