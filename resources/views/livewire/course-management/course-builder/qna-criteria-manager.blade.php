<div class="space-y-6">
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-600 text-white p-4 rounded-lg animate__animated animate__fadeIn">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header with Add Topic Button -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium text-white">Discussion Topics</h3>
            <p class="text-gray-400 text-sm">Total Points: {{ array_sum(array_column($topics, 'points')) }}</p>
        </div>
        <button wire:click="toggleCreateForm"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            {{ $showCreateForm ? 'Cancel' : 'Add Topic' }}
        </button>
    </div>

    <!-- Create/Edit Topic Form -->
    @if ($showCreateForm)
        <div class="bg-gray-700 rounded-lg p-6 border border-gray-600">
            <h4 class="text-white font-medium mb-4">
                {{ $editingTopic ? 'Edit Discussion Topic' : 'Create New Discussion Topic' }}
            </h4>

            <form wire:submit.prevent="{{ $editingTopic ? 'updateTopic' : 'createTopic' }}" class="space-y-6">
                <!-- Topic Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">Topic Type</label>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        @php
                            $topicTypes = [
                                'discussion' => ['icon' => 'fa-comments', 'label' => 'Discussion', 'color' => 'blue'],
                                'debate' => ['icon' => 'fa-balance-scale', 'label' => 'Debate', 'color' => 'red'],
                                'peer_review' => ['icon' => 'fa-users', 'label' => 'Peer Review', 'color' => 'green'],
                                'reflection' => ['icon' => 'fa-mirror', 'label' => 'Reflection', 'color' => 'yellow'],
                                'case_study' => ['icon' => 'fa-search', 'label' => 'Case Study', 'color' => 'purple'],
                            ];
                        @endphp

                        @foreach ($topicTypes as $type => $config)
                            <button type="button" wire:click="selectTopicType('{{ $type }}')"
                                    class="p-3 rounded-lg text-center transition-colors border-2 text-sm
                                        {{ $topicType === $type 
                                            ? 'bg-' . $config['color'] . '-600 border-' . $config['color'] . '-500 text-white' 
                                            : 'bg-gray-600 border-gray-500 text-gray-300 hover:bg-gray-500 hover:border-gray-400' }}">
                                <i class="fas {{ $config['icon'] }} text-lg mb-1 block"></i>
                                <div class="font-medium">{{ $config['label'] }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            Topic Title <span class="text-red-400">*</span>
                        </label>
                        <input type="text" wire:model="topicTitle"
                               class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Enter topic title...">
                        @error('topicTitle')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Points</label>
                        <input type="number" wire:model="points" step="1" min="1" max="100"
                               class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('points')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Description <span class="text-red-400">*</span>
                    </label>
                    <textarea wire:model="topicDescription" rows="3"
                              class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                     focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Describe the discussion topic..."></textarea>
                    @error('topicDescription')
                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Discussion Settings -->
                <div class="bg-gray-600 rounded-lg p-4">
                    <h5 class="text-white font-medium mb-3">Discussion Requirements</h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Min Responses</label>
                            <input type="number" wire:model="minResponses" min="1" max="10"
                                   class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                            @error('minResponses')
                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Min Response Length</label>
                            <input type="number" wire:model="minResponseLength" min="10" max="1000"
                                   class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                            @error('minResponseLength')
                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Time Limit (minutes)</label>
                            <input type="number" wire:model="timeLimit" min="1" max="10080"
                                   class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white"
                                   placeholder="Optional">
                            @error('timeLimit')
                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Discussion Options -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="allowPeerReview" id="allowPeerReview"
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                            <label for="allowPeerReview" class="ml-2 block text-sm text-gray-300">
                                Allow Peer Review
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="moderatorApproval" id="moderatorApproval"
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                            <label for="moderatorApproval" class="ml-2 block text-sm text-gray-300">
                                Require Moderator Approval
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" wire:model="isRequired" id="isRequired"
                                   class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                            <label for="isRequired" class="ml-2 block text-sm text-gray-300">
                                Required for completion
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Discussion Prompts -->
                <div class="bg-gray-600 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-white font-medium">Discussion Prompts</h5>
                        <button type="button" wire:click="addDiscussionPrompt"
                                class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                            <i class="fas fa-plus mr-1"></i>Add Prompt
                        </button>
                    </div>
                    
                    @foreach ($discussionPrompts as $index => $prompt)
                        <div class="flex gap-2 mb-2">
                            <input type="text" wire:model="discussionPrompts.{{ $index }}"
                                   class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white"
                                   placeholder="Discussion prompt {{ $index + 1 }}">
                            @if (count($discussionPrompts) > 1)
                                <button type="button" wire:click="removeDiscussionPrompt({{ $index }})"
                                        class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Evaluation Criteria -->
                <div class="bg-gray-600 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-white font-medium">Evaluation Criteria</h5>
                        <button type="button" wire:click="addEvaluationCriteria"
                                class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                            <i class="fas fa-plus mr-1"></i>Add Criteria
                        </button>
                    </div>
                    
                    @foreach ($evaluationCriteria as $index => $criteria)
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3 p-3 bg-gray-700 rounded-lg">
                            <div>
                                <input type="text" wire:model="evaluationCriteria.{{ $index }}.name"
                                       class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white"
                                       placeholder="Criteria name">
                            </div>
                            <div>
                                <input type="number" wire:model="evaluationCriteria.{{ $index }}.weight" min="0" max="100"
                                       class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white"
                                       placeholder="Weight %">
                            </div>
                            <div>
                                <input type="text" wire:model="evaluationCriteria.{{ $index }}.description"
                                       class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white"
                                       placeholder="Description">
                            </div>
                            <div class="flex items-center">
                                @if (count($evaluationCriteria) > 1)
                                    <button type="button" wire:click="removeEvaluationCriteria({{ $index }})"
                                            class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded w-full">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-4 border-t border-gray-600">
                    <button type="button" wire:click="toggleCreateForm"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Cancel
                    </button>
                    
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors 
                                   disabled:opacity-50 flex items-center gap-2">
                        <span wire:loading.remove>
                            <i class="fas fa-{{ $editingTopic ? 'save' : 'plus' }} mr-2"></i>
                            {{ $editingTopic ? 'Update Topic' : 'Create Topic' }}
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            {{ $editingTopic ? 'Updating...' : 'Creating...' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Topics List -->
    <div class="space-y-4">
        <h4 class="text-white font-medium">Discussion Topics ({{ count($topics) }})</h4>

        @if (count($topics) > 0)
            <div class="space-y-3" id="topics-container">
                @foreach ($topics as $index => $topic)
                    @php
                        $topicData = json_decode($topic['options'], true) ?? [];
                        $topicType = $topicData['topic_type'] ?? 'discussion';
                    @endphp
                    <div class="bg-gray-700 rounded-lg border border-gray-600 p-4 sortable-item" 
                         data-id="{{ $topic['id'] }}">
                        <div class="flex items-start gap-4">
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-move text-gray-500 hover:text-gray-300 mt-1">
                                <i class="fas fa-grip-vertical"></i>
                            </div>

                            <!-- Topic Number -->
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ $topic['order'] }}
                            </div>

                            <!-- Topic Content -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $topicType === 'discussion' ? 'bg-blue-100 text-blue-800' : 
                                                   ($topicType === 'debate' ? 'bg-red-100 text-red-800' : 
                                                    ($topicType === 'peer_review' ? 'bg-green-100 text-green-800' : 
                                                     ($topicType === 'reflection' ? 'bg-yellow-100 text-yellow-800' : 
                                                      'bg-purple-100 text-purple-800'))) }}">
                                                {{ ucfirst(str_replace('_', ' ', $topicType)) }}
                                            </span>
                                            <span class="text-sm text-green-400 font-medium">{{ $topic['points'] }} pts</span>
                                            @if ($topic['is_required'])
                                                <span class="text-xs text-red-400">Required</span>
                                            @endif
                                            @if ($topic['time_limit'])
                                                <span class="text-sm text-blue-400">{{ $topic['time_limit'] }}min</span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-white font-medium mb-2">{{ $topic['question_text'] }}</p>
                                        
                                        @if ($topic['explanation'])
                                            <p class="text-gray-400 text-sm mb-2">{{ $topic['explanation'] }}</p>
                                        @endif

                                        <!-- Topic Requirements -->
                                        @if (!empty($topicData['min_responses']) || !empty($topicData['min_response_length']))
                                            <div class="text-sm text-blue-300 bg-blue-900/20 px-2 py-1 rounded mb-2">
                                                <strong>Requirements:</strong>
                                                @if (!empty($topicData['min_responses']))
                                                    {{ $topicData['min_responses'] }} responses min
                                                @endif
                                                @if (!empty($topicData['min_response_length']))
                                                    {{ !empty($topicData['min_responses']) ? ', ' : '' }}{{ $topicData['min_response_length'] }} chars min
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Discussion Prompts Preview -->
                                        @if (!empty($topicData['discussion_prompts']))
                                            <div class="text-sm text-purple-300 bg-purple-900/20 px-2 py-1 rounded">
                                                <strong>Prompts:</strong> {{ count($topicData['discussion_prompts']) }} discussion prompts
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex gap-2 ml-4">
                                        <button wire:click="editTopic({{ $topic['id'] }})"
                                                class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button wire:click="duplicateTopic({{ $topic['id'] }})"
                                                class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm"
                                                title="Duplicate Topic">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <button wire:click="deleteTopic({{ $topic['id'] }})"
                                                onclick="return confirm('Are you sure you want to delete this topic?')"
                                                class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Topic Statistics -->
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $typeStats = [];
                    $totalPoints = array_sum(array_column($topics, 'points'));
                    foreach ($topics as $topic) {
                        $data = json_decode($topic['options'], true) ?? [];
                        $type = $data['topic_type'] ?? 'discussion';
                        $typeStats[$type] = ($typeStats[$type] ?? 0) + 1;
                    }
                @endphp

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-blue-400">{{ $typeStats['discussion'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">Discussions</div>
                </div>

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-red-400">{{ $typeStats['debate'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">Debates</div>
                </div>

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-green-400">{{ $typeStats['peer_review'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">Peer Reviews</div>
                </div>

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-purple-400">{{ $totalPoints }}</div>
                    <div class="text-xs text-gray-400">Total Points</div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comments text-2xl text-gray-500"></i>
                </div>
                <h4 class="text-lg font-medium text-white mb-2">No Discussion Topics Yet</h4>
                <p class="text-gray-400 mb-4">Create discussion topics to enable Q&A based evaluation.</p>
                <button wire:click="toggleCreateForm"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Add Your First Topic
                </button>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Sortable functionality for topics
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('topics-container');
    if (container && typeof Sortable !== 'undefined') {
        new Sortable(container, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'opacity-50',
            onEnd: function(evt) {
                const orderedIds = Array.from(container.children).map(el => el.dataset.id);
                @this.reorderTopics(orderedIds);
            }
        });
    }
});
</script>
@endpush