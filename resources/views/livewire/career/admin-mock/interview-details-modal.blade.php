@if($selectedInterview)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" wire:key="interview-modal-{{ $selectedInterview->id }}">
        <div class="rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden bg-themed-secondary border border-themed-primary">
            <div class="px-6 py-4 border-b border-themed-primary bg-themed-tertiary flex items-center justify-between">
                <h3 class="text-lg font-medium text-themed-primary">Interview Details</h3>
                <button wire:click="$set('selectedInterview', null)" class="text-themed-secondary hover:text-themed-primary transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div>
                        <h4 class="text-md font-medium mb-4 text-themed-primary">Basic Information</h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium text-themed-secondary">Title</dt>
                                <dd class="text-sm text-themed-primary">{{ $selectedInterview->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-themed-secondary">User</dt>
                                <dd class="text-sm text-themed-primary">
                                    {{ $selectedInterview->user->name }} ({{ $selectedInterview->user->email }})
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-themed-secondary">Type</dt>
                                <dd class="text-sm text-themed-primary">{{ $selectedInterview->type_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-themed-secondary">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $selectedInterview->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $selectedInterview->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $selectedInterview->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $selectedInterview->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ $selectedInterview->status_label }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-themed-secondary">Difficulty</dt>
                                <dd class="text-sm text-themed-primary">{{ $selectedInterview->difficulty_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-themed-secondary">Duration</dt>
                                <dd class="text-sm text-themed-primary">{{ $selectedInterview->duration_formatted }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Scores -->
                    @if($selectedInterview->isCompleted())
                        <div>
                            <h4 class="text-md font-medium mb-4 text-themed-primary">Performance Scores</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-themed-secondary">Overall Score</dt>
                                    <dd class="text-sm text-themed-primary">
                                        {{ number_format($selectedInterview->overall_score, 1) }}%
                                        ({{ $selectedInterview->overall_rating }})
                                    </dd>
                                </div>
                                @if($selectedInterview->technical_score)
                                    <div>
                                        <dt class="text-sm font-medium text-themed-secondary">Technical</dt>
                                        <dd class="text-sm text-themed-primary">
                                            {{ number_format($selectedInterview->technical_score, 1) }}%
                                        </dd>
                                    </div>
                                @endif
                                @if($selectedInterview->communication_score)
                                    <div>
                                        <dt class="text-sm font-medium text-themed-secondary">Communication</dt>
                                        <dd class="text-sm text-themed-primary">
                                            {{ number_format($selectedInterview->communication_score, 1) }}%
                                        </dd>
                                    </div>
                                @endif
                                @if($selectedInterview->confidence_score)
                                    <div>
                                        <dt class="text-sm font-medium text-themed-secondary">Confidence</dt>
                                        <dd class="text-sm text-themed-primary">
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
                        <h4 class="text-md font-medium mb-4 text-themed-primary">Questions & Responses</h4>
                        <div class="space-y-4">
                            @foreach($selectedInterview->questions as $index => $question)
                                <div class="border border-themed-primary rounded-lg p-4 bg-themed-tertiary">
                                    <div class="font-medium mb-2 text-themed-primary">
                                        Q{{ $index + 1 }}: {{ $question['question'] }}
                                    </div>
                                    @if($selectedInterview->user_responses && isset($selectedInterview->user_responses[$question['id']]))
                                        <div class="p-3 rounded-md bg-themed-secondary border border-themed-primary">
                                            <div class="text-sm text-themed-primary">
                                                {{ $selectedInterview->user_responses[$question['id']]['answer'] ?? 'No answer provided' }}
                                            </div>
                                            @if(isset($selectedInterview->user_responses[$question['id']]['response_time']))
                                                <div class="text-xs mt-2 text-themed-tertiary">
                                                    Response time: {{ $selectedInterview->user_responses[$question['id']]['response_time'] }}s
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm italic text-themed-secondary">No response provided</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- AI Feedback -->
                @if($selectedInterview->ai_feedback)
                    <div class="mt-6">
                        <h4 class="text-md font-medium mb-4 text-themed-primary">AI Feedback</h4>
                        <div class="space-y-4">
                            @if($selectedInterview->strengths)
                                <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                                    <h5 class="text-sm font-medium text-green-700 mb-2">Strengths</h5>
                                    <ul class="list-disc list-inside text-sm text-themed-primary space-y-1">
                                        @foreach($selectedInterview->strengths as $strength)
                                            <li>{{ $strength }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($selectedInterview->weaknesses)
                                <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                                    <h5 class="text-sm font-medium text-red-700 mb-2">Areas for Improvement</h5>
                                    <ul class="list-disc list-inside text-sm text-themed-primary space-y-1">
                                        @foreach($selectedInterview->weaknesses as $weakness)
                                            <li>{{ $weakness }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($selectedInterview->improvement_suggestions)
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    <h5 class="text-sm font-medium text-blue-700 mb-2">Recommendations</h5>
                                    <ul class="list-disc list-inside text-sm text-themed-primary space-y-1">
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
            <div class="px-6 py-4 border-t border-themed-primary bg-themed-tertiary flex items-center justify-between">
                <div class="flex space-x-2">
                    @if($selectedInterview->isCompleted() && !$selectedInterview->ai_feedback)
                        <button wire:click="generateAIFeedback({{ $selectedInterview->id }})"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors font-medium">
                            Generate AI Feedback
                        </button>
                    @endif
                </div>
                <button wire:click="$set('selectedInterview', null)"
                    class="px-4 py-2 border border-themed-primary rounded-md hover:bg-themed-secondary transition-colors text-themed-primary font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>
@endif