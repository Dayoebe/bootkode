@if($selectedInterview)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 flex items-center justify-center p-4 z-50" wire:key="interview-modal-{{ $selectedInterview->id }}">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Interview Details</h3>
                <button wire:click="$set('selectedInterview', null)" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Basic Information</h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $selectedInterview->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">
                                    {{ $selectedInterview->user->name }} ({{ $selectedInterview->user->email }})
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Type</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $selectedInterview->type_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $selectedInterview->getStatusColor() }}-100 dark:bg-{{ $selectedInterview->getStatusColor() }}-900/30 text-{{ $selectedInterview->getStatusColor() }}-800 dark:text-{{ $selectedInterview->getStatusColor() }}-400">
                                        {{ $selectedInterview->status_label }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Difficulty</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $selectedInterview->difficulty_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $selectedInterview->duration_formatted }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Scores -->
                    @if($selectedInterview->isCompleted())
                        <div>
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Performance Scores</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Overall Score</dt>
                                    <dd class="text-sm text-gray-900 dark:text-white">
                                        {{ number_format($selectedInterview->overall_score, 1) }}%
                                        ({{ $selectedInterview->overall_rating }})
                                    </dd>
                                </div>
                                @if($selectedInterview->technical_score)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Technical</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">
                                            {{ number_format($selectedInterview->technical_score, 1) }}%
                                        </dd>
                                    </div>
                                @endif
                                @if($selectedInterview->communication_score)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Communication</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">
                                            {{ number_format($selectedInterview->communication_score, 1) }}%
                                        </dd>
                                    </div>
                                @endif
                                @if($selectedInterview->confidence_score)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Confidence</dt>
                                        <dd class="text-sm text-gray-900 dark:text-white">
                                            {{ number_format($selectedInterview->confidence_score, 1) }}%
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    @endif
                </div>

                <!-- Questions and Responses -->
                @if($selectedInterview->questions)
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Questions & Responses</h4>
                        <div class="space-y-4">
                            @foreach($selectedInterview->questions as $index => $question)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="font-medium text-gray-900 dark:text-white mb-2">
                                        Q{{ $index + 1 }}: {{ $question['question'] }}
                                    </div>
                                    @if($selectedInterview->user_responses && isset($selectedInterview->user_responses[$question['id']]))
                                        <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-md">
                                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                                {{ $selectedInterview->user_responses[$question['id']]['answer'] ?? 'No answer provided' }}
                                            </div>
                                            @if(isset($selectedInterview->user_responses[$question['id']]['response_time']))
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                    Response time: {{ $selectedInterview->user_responses[$question['id']]['response_time'] }}s
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500 dark:text-gray-400 italic">No response provided</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- AI Feedback -->
                @if($selectedInterview->ai_feedback)
                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">AI Feedback</h4>
                        <div class="space-y-4">
                            @if($selectedInterview->strengths)
                                <div>
                                    <h5 class="text-sm font-medium text-green-700 dark:text-green-400 mb-2">Strengths</h5>
                                    <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-300">
                                        @foreach($selectedInterview->strengths as $strength)
                                            <li>{{ $strength }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($selectedInterview->weaknesses)
                                <div>
                                    <h5 class="text-sm font-medium text-red-700 dark:text-red-400 mb-2">Areas for Improvement</h5>
                                    <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-300">
                                        @foreach($selectedInterview->weaknesses as $weakness)
                                            <li>{{ $weakness }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($selectedInterview->improvement_suggestions)
                                <div>
                                    <h5 class="text-sm font-medium text-blue-700 dark:text-blue-400 mb-2">Recommendations</h5>
                                    <ul class="list-disc list-inside text-sm text-gray-700 dark:text-gray-300">
                                        @foreach($selectedInterview->improvement_suggestions as $suggestion)
                                            <li>{{ $suggestion }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex space-x-2">
                    @if($selectedInterview->isCompleted() && !$selectedInterview->ai_feedback)
                        <button wire:click="generateAIFeedback({{ $selectedInterview->id }})"
                            class="bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded-md hover:bg-green-700 dark:hover:bg-green-600">
                            Generate AI Feedback
                        </button>
                    @endif
                </div>
                <button wire:click="$set('selectedInterview', null)"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                    Close
                </button>
            </div>
        </div>
    </div>
@endif