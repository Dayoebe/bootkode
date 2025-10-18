@if($showInterviewModal && $currentInterview)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-themed-secondary rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <!-- Interview Header -->
            <div class="p-6 border-b border-themed-primary bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-themed-primary">{{ $currentInterview->title }}</h2>
                        <p class="text-themed-secondary">Question {{ $currentQuestionIndex + 1 }} of {{ count($currentInterview->questions) }}</p>
                    </div>
                    <div class="text-right">
                        <div class="bg-blue-100 dark:bg-blue-900/50 px-4 py-2 rounded-lg">
                            <span class="text-blue-800 dark:text-blue-200 font-semibold" id="timer">{{ gmdate('i:s', $timeRemaining) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="w-full bg-themed-tertiary rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                            style="width: {{ (($currentQuestionIndex) / count($currentInterview->questions)) * 100 }}%">
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                @if($currentQuestionIndex < count($currentInterview->questions))
                    <!-- Current Question -->
                    <div class="mb-8">
                        <div class="bg-themed-tertiary p-6 rounded-xl mb-6">
                            <h3 class="text-lg font-semibold text-themed-primary mb-2">Question {{ $currentQuestionIndex + 1 }}</h3>
                            <p class="text-themed-primary text-lg">
                                {{ $currentInterview->questions[$currentQuestionIndex]['question'] }}
                            </p>
                        </div>

                        <!-- Answer Input -->
                        <div class="space-y-4">
                            @if($currentInterview->format === 'text')
                                <textarea wire:model="currentAnswer" placeholder="Type your answer here..."
                                    class="w-full h-40 px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-themed-secondary text-themed-primary"
                                    autofocus></textarea>
                            @elseif($currentInterview->format === 'voice')
                                <div class="text-center py-8">
                                    <button class="bg-red-600 text-white px-8 py-4 rounded-full hover:bg-red-700 transition-colors">
                                        <i class="fas fa-microphone text-2xl mr-2"></i> Start Recording
                                    </button>
                                    <p class="text-themed-secondary mt-4">Click to start voice recording</p>
                                </div>
                            @elseif($currentInterview->format === 'video')
                                <div class="text-center py-8">
                                    <div class="bg-themed-tertiary rounded-xl p-8 mb-4">
                                        <i class="fas fa-video text-6xl text-themed-secondary mb-4"></i>
                                        <p class="text-themed-secondary">Video recording will start here</p>
                                    </div>
                                    <button class="bg-red-600 text-white px-8 py-4 rounded-full hover:bg-red-700 transition-colors">
                                        <i class="fas fa-video mr-2"></i> Start Video Recording
                                    </button>
                                </div>
                            @endif

                            <!-- Recording Controls (if applicable) -->
                            @if(in_array($currentInterview->format, ['voice', 'video']))
                                <div class="flex justify-center space-x-4 mt-4">
                                    <button class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-pause mr-2"></i> Pause
                                    </button>
                                    <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                        <i class="fas fa-play mr-2"></i> Resume
                                    </button>
                                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-stop mr-2"></i> Stop & Continue
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-between pt-6 border-t border-themed-primary">
                    <button wire:click="completeInterview"
                        class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-times mr-2"></i> End Interview
                    </button>

                    <div class="space-x-4">
                        @if($currentQuestionIndex > 0)
                            <button class="bg-themed-tertiary text-themed-primary px-6 py-3 rounded-lg hover:bg-themed-primary hover:text-white transition-colors">
                                <i class="fas fa-chevron-left mr-2"></i> Previous
                            </button>
                        @endif

                        <button wire:click="submitAnswer"
                            class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold"
                            {{ empty($currentAnswer) && $currentInterview->format === 'text' ? 'disabled' : '' }}>
                            {{ $currentQuestionIndex === count($currentInterview->questions) - 1 ? 'Finish Interview' : 'Next Question' }}
                            <i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif