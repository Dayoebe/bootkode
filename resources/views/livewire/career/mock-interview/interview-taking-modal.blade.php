{{-- interview-taking-modal.blade.php --}}
@if($showInterviewModal && $currentInterview)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-themed-secondary border border-themed-primary rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <!-- Interview Header -->
            <div class="p-6 border-b border-themed-primary bg-gradient-to-r from-accent-themed-primary/10 to-accent-themed-secondary/10">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-themed-primary">{{ $currentInterview->title }}</h2>
                        <p class="text-themed-secondary">Question {{ $currentQuestionIndex + 1 }} of {{ count($currentInterview->questions) }}</p>
                    </div>
                    <div class="text-right">
                        <div class="bg-accent-themed-primary/10 border border-accent-themed-primary/30 px-4 py-2 rounded-lg">
                            <span class="text-accent-themed-primary font-semibold" id="timer">{{ gmdate('i:s', $timeRemaining) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="w-full bg-themed-tertiary rounded-full h-2">
                        <div class="bg-accent-themed-primary h-2 rounded-full transition-all duration-300"
                            style="width: {{ (($currentQuestionIndex) / count($currentInterview->questions)) * 100 }}%">
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                @if($currentQuestionIndex < count($currentInterview->questions))
                    <!-- Current Question -->
                    <div class="mb-8">
                        <div class="bg-themed-tertiary border border-themed-primary p-6 rounded-xl mb-6">
                            <h3 class="text-lg font-semibold text-themed-primary mb-2">Question {{ $currentQuestionIndex + 1 }}</h3>
                            <p class="text-themed-primary text-lg">
                                {{ $currentInterview->questions[$currentQuestionIndex]['question'] }}
                            </p>
                        </div>

                        <!-- Answer Input -->
                        <div class="space-y-4">
                            @if($currentInterview->format === 'text')
                                <textarea wire:model="currentAnswer" placeholder="Type your answer here..."
                                    class="w-full h-40 px-4 py-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent resize-none transition-all"
                                    autofocus></textarea>
                            @elseif($currentInterview->format === 'voice')
                                <div class="text-center py-8">
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-full transition-colors">
                                        <i class="fas fa-microphone text-2xl mr-2"></i> Start Recording
                                    </button>
                                    <p class="text-themed-secondary mt-4">Click to start voice recording</p>
                                </div>
                            @elseif($currentInterview->format === 'video')
                                <div class="text-center py-8">
                                    <div class="bg-themed-tertiary rounded-xl p-8 mb-4 border border-themed-primary">
                                        <i class="fas fa-video text-6xl text-themed-secondary mb-4"></i>
                                        <p class="text-themed-secondary">Video recording will start here</p>
                                    </div>
                                    <button class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-full transition-colors">
                                        <i class="fas fa-video mr-2"></i> Start Video Recording
                                    </button>
                                </div>
                            @endif

                            <!-- Recording Controls -->
                            @if(in_array($currentInterview->format, ['voice', 'video']))
                                <div class="flex justify-center space-x-4 mt-4">
                                    <button class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-2 rounded-lg transition-colors">
                                        <i class="fas fa-pause mr-2"></i> Pause
                                    </button>
                                    <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                                        <i class="fas fa-play mr-2"></i> Resume
                                    </button>
                                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
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
                        class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i> End Interview
                    </button>

                    <div class="space-x-4">
                        @if($currentQuestionIndex > 0)
                            <button class="bg-themed-tertiary hover:bg-accent-themed-primary hover:text-white text-themed-primary border border-themed-primary px-6 py-3 rounded-lg transition-colors">
                                <i class="fas fa-chevron-left mr-2"></i> Previous
                            </button>
                        @endif

                        <button wire:click="submitAnswer"
                            class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-8 py-3 rounded-lg font-semibold transition-colors"
                            {{ empty($currentAnswer) && $currentInterview->format === 'text' ? 'disabled opacity-50 cursor-not-allowed' : '' }}>
                            {{ $currentQuestionIndex === count($currentInterview->questions) - 1 ? 'Finish Interview' : 'Next Question' }}
                            <i class="fas fa-chevron-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif