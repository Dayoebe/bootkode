<div class="space-y-6">
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-600 text-white p-4 rounded-lg animate__animated animate__fadeIn">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header with Add Criteria Button -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium text-white">Project Criteria</h3>
            <p class="text-gray-400 text-sm">Total Points: {{ array_sum(array_column($criteria, 'points')) }}</p>
        </div>
        <button wire:click="toggleCreateForm"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            {{ $showCreateForm ? 'Cancel' : 'Add Criteria' }}
        </button>
    </div>

    <!-- Create/Edit Criteria Form -->
    @if ($showCreateForm)
        <div class="bg-gray-700 rounded-lg p-6 border border-gray-600">
            <h4 class="text-white font-medium mb-4">
                {{ $editingCriteria ? 'Edit Project Criteria' : 'Create New Project Criteria' }}
            </h4>

            <form wire:submit.prevent="{{ $editingCriteria ? 'updateCriteria' : 'createCriteria' }}" class="space-y-6">
                <!-- Criteria Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">Criteria Type</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @php
                            $criteriaTypes = [
                                'deliverable' => [
                                    'icon' => 'fa-file-upload',
                                    'label' => 'Deliverable',
                                    'color' => 'blue',
                                ],
                                'rubric' => ['icon' => 'fa-clipboard-check', 'label' => 'Rubric', 'color' => 'green'],
                                'presentation' => [
                                    'icon' => 'fa-presentation',
                                    'label' => 'Presentation',
                                    'color' => 'purple',
                                ],
                                'documentation' => [
                                    'icon' => 'fa-file-alt',
                                    'label' => 'Documentation',
                                    'color' => 'yellow',
                                ],
                            ];
                        @endphp

                        @foreach ($criteriaTypes as $type => $config)
                            <button type="button" wire:click="selectCriteriaType('{{ $type }}')"
                                class="p-3 rounded-lg text-center transition-colors border-2 text-sm
                                        {{ $criteriaType === $type
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
                            Criteria Title <span class="text-red-400">*</span>
                        </label>
                        <input type="text" wire:model="criteriaTitle"
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                      focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Enter criteria title...">
                        @error('criteriaTitle')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Points</label>
                        <input type="number" wire:model="points" step="1" min="1" max="100"
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                      focus:ring-2 focus:ring-green-500 focus:border-transparent">
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
                    <textarea wire:model="criteriaDescription" rows="3"
                        class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                     focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="Describe what students need to accomplish..."></textarea>
                    @error('criteriaDescription')
                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- File Requirements (for deliverable type) -->
                @if ($criteriaType === 'deliverable')
                    <div class="bg-gray-600 rounded-lg p-4">
                        <h5 class="text-white font-medium mb-3">File Upload Settings</h5>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Max File Size (MB)</label>
                                <input type="number" wire:model="maxFileSize" min="1" max="100"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                                @error('maxFileSize')
                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Min Files</label>
                                <input type="number" wire:model="minFiles" min="0" max="20"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                                @error('minFiles')
                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Max Files</label>
                                <input type="number" wire:model="maxFiles" min="1" max="20"
                                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                                @error('maxFiles')
                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- File Types -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-300">Allowed File Types</label>
                                <button type="button" wire:click="addFileType"
                                    class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                                    <i class="fas fa-plus mr-1"></i>Add Type
                                </button>
                            </div>

                            @foreach ($fileTypes as $index => $fileType)
                                <div class="flex gap-2 mb-2">
                                    <input type="text" wire:model="fileTypes.{{ $index }}"
                                        class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white"
                                        placeholder="e.g., pdf, docx, jpg">
                                    <button type="button" wire:click="removeFileType({{ $index }})"
                                        class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Rubric Levels (for rubric type) -->
                @if ($criteriaType === 'rubric')
                    <div class="bg-gray-600 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="text-white font-medium">Rubric Levels</h5>
                            <button type="button" wire:click="addRubricLevel"
                                class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                                <i class="fas fa-plus mr-1"></i>Add Level
                            </button>
                        </div>

                        @foreach ($rubricLevels as $index => $level)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3 p-3 bg-gray-700 rounded-lg">
                                <div>
                                    <input type="text" wire:model="rubricLevels.{{ $index }}.name"
                                        class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white"
                                        placeholder="Level name">
                                </div>
                                <div>
                                    <input type="number" wire:model="rubricLevels.{{ $index }}.points"
                                        min="0"
                                        class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white"
                                        placeholder="Points">
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" wire:model="rubricLevels.{{ $index }}.description"
                                        class="flex-1 px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white"
                                        placeholder="Description">
                                    @if (count($rubricLevels) > 2)
                                        <button type="button" wire:click="removeRubricLevel({{ $index }})"
                                            class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Required Toggle -->
                <div class="flex items-center">
                    <input type="checkbox" wire:model="isRequired" id="isRequired"
                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-600 rounded bg-gray-700">
                    <label for="isRequired" class="ml-2 block text-sm text-gray-300">
                        Required for project completion
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-4 border-t border-gray-600">
                    <button type="button" wire:click="toggleCreateForm"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Cancel
                    </button>

                    <button type="submit" wire:loading.attr="disabled"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors 
                                   disabled:opacity-50 flex items-center gap-2">
                        <span wire:loading.remove>
                            <i class="fas fa-{{ $editingCriteria ? 'save' : 'plus' }} mr-2"></i>
                            {{ $editingCriteria ? 'Update Criteria' : 'Create Criteria' }}
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            {{ $editingCriteria ? 'Updating...' : 'Creating...' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Criteria List -->
    <div class="space-y-4">
        <h4 class="text-white font-medium">Project Criteria ({{ count($criteria) }})</h4>

        @if (count($criteria) > 0)
            <div class="space-y-3" id="criteria-container">
                @foreach ($criteria as $index => $criterium)
                    @php
                        $criteriumData = json_decode($criterium['options'], true) ?? [];
                        $criteriumType = $criteriumData['criteria_type'] ?? 'deliverable';
                    @endphp
                    <div class="bg-gray-700 rounded-lg border border-gray-600 p-4 sortable-item"
                        data-id="{{ $criterium['id'] }}">
                        <div class="flex items-start gap-4">
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-move text-gray-500 hover:text-gray-300 mt-1">
                                <i class="fas fa-grip-vertical"></i>
                            </div>

                            <!-- Criteria Number -->
                            <div
                                class="flex-shrink-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ $criterium['order'] }}
                            </div>

                            <!-- Criteria Content -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full 
                                                {{ $criteriumType === 'deliverable'
                                                    ? 'bg-blue-100 text-blue-800'
                                                    : ($criteriumType === 'rubric'
                                                        ? 'bg-green-100 text-green-800'
                                                        : ($criteriumType === 'presentation'
                                                            ? 'bg-purple-100 text-purple-800'
                                                            : 'bg-yellow-100 text-yellow-800')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $criteriumType)) }}
                                            </span>
                                            <span
                                                class="text-sm text-green-400 font-medium">{{ $criterium['points'] }}
                                                pts</span>
                                            @if ($criterium['is_required'])
                                                <span class="text-xs text-red-400">Required</span>
                                            @endif
                                        </div>

                                        <p class="text-white font-medium mb-2">{{ $criterium['question_text'] }}</p>

                                        @if ($criterium['explanation'])
                                            <p class="text-gray-400 text-sm mb-2">{{ $criterium['explanation'] }}</p>
                                        @endif

                                        <!-- Type-specific display -->
                                        @if ($criteriumType === 'deliverable' && !empty($criteriumData['file_types']))
                                            <div class="text-sm text-blue-300 bg-blue-900/20 px-2 py-1 rounded">
                                                <strong>File Types:</strong>
                                                {{ implode(', ', $criteriumData['file_types']) }}
                                                | Max: {{ $criteriumData['max_file_size'] ?? 10 }}MB
                                            </div>
                                        @elseif ($criteriumType === 'rubric' && !empty($criteriumData['rubric_levels']))
                                            <div class="text-sm text-green-300 bg-green-900/20 px-2 py-1 rounded">
                                                <strong>Levels:</strong>
                                                @foreach ($criteriumData['rubric_levels'] as $level)
                                                    {{ $level['name'] }}
                                                    ({{ $level['points'] }}pts){{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex gap-2 ml-4">
                                        <button wire:click="editCriteria({{ $criterium['id'] }})"
                                            class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button wire:click="duplicateCriteria({{ $criterium['id'] }})"
                                            class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm"
                                            title="Duplicate Criteria">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <button wire:click="deleteCriteria({{ $criterium['id'] }})"
                                            onclick="return confirm('Are you sure you want to delete this criteria?')"
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
        @else
            <!-- Empty State -->
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-project-diagram text-2xl text-gray-500"></i>
                </div>
                <h4 class="text-lg font-medium text-white mb-2">No Project Criteria Yet</h4>
                <p class="text-gray-400 mb-4">Create criteria to define project requirements and evaluation standards.
                </p>
                <button wire:click="toggleCreateForm"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Add Your First Criteria
                </button>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        // Sortable functionality for criteria
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('criteria-container');
            if (container && typeof Sortable !== 'undefined') {
                new Sortable(container, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: function(evt) {
                        const orderedIds = Array.from(container.children).map(el => el.dataset.id);
                        @this.reorderCriteria(orderedIds);
                    }
                });
            }
        });
    </script>
@endpush
