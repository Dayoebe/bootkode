{{-- results-modal.blade.php --}}
@if($showResultsModal && $currentInterview)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
        <div
            class="bg-themed-secondary border border-themed-primary rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden my-8">
            <!-- Results Header -->
            <div
                class="p-6 border-b border-themed-primary bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-themed-primary">Interview Results</h2>
                        <p class="text-themed-secondary">{{ $currentInterview->title }}</p>
                    </div>
                    <button wire:click="$set('showResultsModal', false)"
                        class="text-themed-secondary hover:text-themed-primary transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="p-6">
                    <!-- Overall Score -->
                    <div class="text-center mb-8">
                        <div
                            class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-accent-themed-primary to-accent-themed-secondary rounded-full text-white mb-4">
                            <span
                                class="text-3xl font-bold">{{ number_format($currentInterview->overall_score ?? 0, 1) }}%</span>
                        </div>
                        <h3 class="text-2xl font-bold text-themed-primary mb-2">{{ $currentInterview->overall_rating }}</h3>
                        <p class="text-themed-secondary">Overall Performance</p>
                    </div>

                    <!-- Score Breakdown -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-themed-tertiary border border-themed-primary rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($currentInterview->technical_score ?? 0, 1) }}%</div>
                            <div class="text-sm text-themed-secondary">Technical Skills</div>
                        </div>
                        <div class="bg-themed-tertiary border border-themed-primary rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($currentInterview->communication_score ?? 0, 1) }}%</div>
                            <div class="text-sm text-themed-secondary">Communication</div>
                        </div>
                        <div class="bg-themed-tertiary border border-themed-primary rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                {{ number_format($currentInterview->confidence_score ?? 0, 1) }}%</div>
                            <div class="text-sm text-themed-secondary">Confidence</div>
                        </div>
                        <div class="bg-themed-tertiary border border-themed-primary rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                                {{ number_format($currentInterview->problem_solving_score ?? 0, 1) }}%</div>
                            <div class="text-sm text-themed-secondary">Problem Solving</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- AI Feedback -->
                        @if($currentInterview->ai_feedback)
                            <div class="bg-themed-tertiary border border-themed-primary rounded-xl p-6">
                                <h4 class="text-lg font-bold text-themed-primary mb-4 flex items-center">
                                    <i class="fas fa-brain w-5 h-5 mr-2 text-blue-600 dark:text-blue-400"></i>
                                    AI-Powered Insights
                                </h4>

                                <div class="space-y-4">
                                    @if(isset($currentInterview->ai_feedback['overall_feedback']))
                                        <div>
                                            <h5 class="font-semibold text-themed-primary mb-2">Overall Feedback</h5>
                                            <p class="text-themed-secondary text-sm">
                                                {{ $currentInterview->ai_feedback['overall_feedback'] }}</p>
                                        </div>
                                    @endif

                                    @if(isset($currentInterview->ai_feedback['strengths']))
                                        <div>
                                            <h5 class="font-semibold text-green-600 dark:text-green-400 mb-2">Strengths</h5>
                                            <ul class="space-y-1">
                                                @foreach($currentInterview->ai_feedback['strengths'] as $strength)
                                                    <li class="flex items-start">
                                                        <i
                                                            class="fas fa-check text-green-600 dark:text-green-400 mt-0.5 mr-2 flex-shrink-0"></i>
                                                        <span class="text-sm text-themed-secondary">{{ $strength }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(isset($currentInterview->ai_feedback['areas_for_improvement']))
                                        <div>
                                            <h5 class="font-semibold text-orange-600 dark:text-orange-400 mb-2">Areas for
                                                Improvement</h5>
                                            <ul class="space-y-1">
                                                @foreach($currentInterview->ai_feedback['areas_for_improvement'] as $improvement)
                                                    <li class="flex items-start">
                                                        <i
                                                            class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400 mt-0.5 mr-2 flex-shrink-0"></i>
                                                        <span class="text-sm text-themed-secondary">{{ $improvement }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Recommendations -->
                        <div class="bg-themed-tertiary border border-themed-primary rounded-xl p-6">
                            <h4 class="text-lg font-bold text-themed-primary mb-4 flex items-center">
                                <i class="fas fa-lightbulb w-5 h-5 mr-2 text-purple-600 dark:text-purple-400"></i>
                                Recommendations
                            </h4>

                            @if($currentInterview->improvement_suggestions)
                                <ul class="space-y-3">
                                    @foreach($currentInterview->improvement_suggestions as $suggestion)
                                        <li class="flex items-start">
                                            <div
                                                class="bg-accent-themed-primary/10 border border-accent-themed-primary/30 p-1 rounded-full mr-3 mt-1 flex-shrink-0">
                                                <i class="fas fa-arrow-right text-accent-themed-primary text-xs"></i>
                                            </div>
                                            <span class="text-sm text-themed-primary">{{ $suggestion }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-chart-line text-6xl text-themed-tertiary mb-2"></i>
                                    <p class="text-themed-secondary text-sm">Recommendations will be generated with premium AI
                                        analysis</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        @if($currentInterview->allow_retakes && $currentInterview->retake_count < $currentInterview->max_retakes)
                            <button wire:click="retakeInterview({{ $currentInterview->id }})"
                                class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                                <i class="fas fa-redo mr-2"></i> Retake Interview
                            </button>
                        @endif

                        <button
                            class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-3 rounded-lg transition-colors font-medium">
                            <i class="fas fa-download mr-2"></i> Download Report
                        </button>

                        <button
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                            <i class="fas fa-share mr-2"></i> Share Results
                        </button>

                        <button wire:click="$set('showResultsModal', false)"
                            class="bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary px-6 py-3 rounded-lg transition-colors font-medium">
                            <i class="fas fa-times mr-2"></i> Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif