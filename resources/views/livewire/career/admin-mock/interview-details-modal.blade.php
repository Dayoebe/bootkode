@if($selectedInterview)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" wire:key="interview-modal-{{ $selectedInterview->id }}">
        <div class="rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden" style="background-color: rgb(var(--bg-secondary))">
            <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: rgb(var(--border-primary))">
                <h3 class="text-lg font-medium" style="color: rgb(var(--text-primary))">Interview Details</h3>
                <button wire:click="$set('selectedInterview', null)" class="hover:opacity-70 transition-opacity" style="color: rgb(var(--text-tertiary))">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div>
                        <h4 class="text-md font-medium mb-4" style="color: rgb(var(--text-primary))">Basic Information</h4>
                        <dl class="space-y-2">
                            <div>
                                <dt class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Title</dt>
                                <dd class="text-sm" style="color: rgb(var(--text-primary))">{{ $selectedInterview->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium" style="color: rgb(var(--text-secondary))">User</dt>
                                <dd class="text-sm" style="color: rgb(var(--text-primary))">
                                    {{ $selectedInterview->user->name }} ({{ $selectedInterview->user->email }})
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Type</dt>
                                <dd class="text-sm" style="color: rgb(var(--text-primary))">{{ $selectedInterview->type_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Status</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $selectedInterview->getStatusColor() }}-100 text-{{ $selectedInterview->getStatusColor() }}-800">
                                        {{ $selectedInterview->status_label }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Difficulty</dt>
                                <dd class="text-sm" style="color: rgb(var(--text-primary))">{{ $selectedInterview->difficulty_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Duration</dt>
                                <dd class="text-sm" style="color: rgb(var(--text-primary))">{{ $selectedInterview->duration_formatted }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Scores -->
                    @if($selectedInterview->isCompleted())
                        <div>
                            <h4 class="text-md font-medium mb-4" style="color: rgb(var(--text-primary))">Performance Scores</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Overall Score</dt>
                                    <dd class="text-sm" style="color: rgb(var(--text-primary))">
                                        {{ number_format($selectedInterview->overall_score, 1) }}%
                                        ({{ $selectedInterview->overall_rating }})
                                    </dd>
                                </div>
                                @if($selectedInterview->technical_score)
                                    <div>
                                        <dt class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Technical</dt>
                                        <dd class="text-sm" style="color: rgb(var(--text-primary))">
                                            {{ number_format($selectedInterview->technical_score, 1) }}%
                                        </dd>
                                    </div>
                                @endif
                                @if($selectedInterview->communication_score)
                                    <div>
                                        <dt class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Communication</dt>
                                        <dd class="text-sm" style="color: rgb(var(--text-primary))">
                                            {{ number_format($selectedInterview->communication_score, 1) }}%
                                        </dd>
                                    </div>
                                @endif
                                @if($selectedInterview->confidence_score)
                                    <div>
                                        <dt class="text-sm font-medium" style="color: rgb(var(--text-secondary))">Confidence</dt>
                                        <dd class="text-sm" style="color: rgb(var(--text-primary))">
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
                        <h4 class="text-md font-medium mb-4" style="color: rgb(var(--text-primary))">Questions & Responses</h4>
                        <div class="space-y-4">
                            @foreach($selectedInterview->questions as $index => $question)
                                <div class="border rounded-lg p-4" style="border-color: rgb(var(--border-primary))">
                                    <div class="font-medium mb-2" style="color: rgb(var(--text-primary))">
                                        Q{{ $index + 1 }}: {{ $question['question'] }}
                                    </div>
                                    @if($selectedInterview->user_responses && isset($selectedInterview->user_responses[$question['id']]))
                                        <div class="p-3 rounded-md" style="background-color: rgb(var(--bg-tertiary))">
                                            <div class="text-sm" style="color: rgb(var(--text-primary))">
                                                {{ $selectedInterview->user_responses[$question['id']]['answer'] ?? 'No answer provided' }}
                                            </div>
                                            @if(isset($selectedInterview->user_responses[$question['id']]['response_time']))
                                                <div class="text-xs mt-2" style="color: rgb(var(--text-tertiary))">
                                                    Response time: {{ $selectedInterview->user_responses[$question['id']]['response_time'] }}s
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-sm italic" style="color: rgb(var(--text-secondary))">No response provided</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- AI Feedback -->
                @if($selectedInterview->ai_feedback)
                    <div class="mt-6">
                        <h4 class="text-md font-medium mb-4" style="color: rgb(var(--text-primary))">AI Feedback</h4>
                        <div class="space-y-4">
                            @if($selectedInterview->strengths)
                                <div>
                                    <h5 class="text-sm font-medium text-green-700 mb-2">Strengths</h5>
                                    <ul class="list-disc list-inside text-sm" style="color: rgb(var(--text-primary))">
                                        @foreach($selectedInterview->strengths as $strength)
                                            <li>{{ $strength }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($selectedInterview->weaknesses)
                                <div>
                                    <h5 class="text-sm font-medium text-red-700 mb-2">Areas for Improvement</h5>
                                    <ul class="list-disc list-inside text-sm" style="color: rgb(var(--text-primary))">
                                        @foreach($selectedInterview->weaknesses as $weakness)
                                            <li>{{ $weakness }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($selectedInterview->improvement_suggestions)
                                <div>
                                    <h5 class="text-sm font-medium text-blue-700 mb-2">Recommendations</h5>
                                    <ul class="list-disc list-inside text-sm" style="color: rgb(var(--text-primary))">
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
            <div class="px-6 py-4 border-t flex items-center justify-between" style="border-color: rgb(var(--border-primary))">
                <div class="flex space-x-2">
                    @if($selectedInterview->isCompleted() && !$selectedInterview->ai_feedback)
                        <button wire:click="generateAIFeedback({{ $selectedInterview->id }})"
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                            Generate AI Feedback
                        </button>
                    @endif
                </div>
                <button wire:click="$set('selectedInterview', null)"
                    class="px-4 py-2 border rounded-md hover:opacity-80 transition-opacity"
                    style="border-color: rgb(var(--border-primary)); color: rgb(var(--text-primary)); background-color: rgb(var(--bg-tertiary))">
                    Close
                </button>
            </div>
        </div>
    </div>
@endif