@php
    $normalizeBuilderArray = static function ($value, array $default = []) {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            return $value->values()->all();
        }

        if ($value instanceof \Illuminate\Contracts\Support\Arrayable) {
            $arrayValue = $value->toArray();

            return is_array($arrayValue) ? $arrayValue : $default;
        }

        if ($value instanceof \JsonSerializable) {
            $jsonValue = $value->jsonSerialize();

            return is_array($jsonValue) ? $jsonValue : $default;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : $default;
        }

        return $default;
    };
@endphp
<div class="space-y-6">
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-700 p-4 rounded-lg animate__animated animate__fadeIn transition-colors duration-300">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header with Add Criteria Button -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium text-themed-primary transition-colors duration-300">Project Criteria</h3>
            <p class="text-themed-secondary text-sm transition-colors duration-300">Total Points: {{ array_sum(array_column($criteria, 'points')) }}</p>
        </div>
        <button wire:click="toggleCreateForm"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            {{ $showCreateForm ? 'Cancel' : 'Add Criteria' }}
        </button>
    </div>

    <!-- Create/Edit Criteria Form -->
    @if ($showCreateForm)
        <div class="bg-themed-tertiary rounded-lg p-6 border border-themed-primary transition-colors duration-300">
            <h4 class="text-themed-primary font-medium mb-4 transition-colors duration-300">
                {{ $editingCriteria ? 'Edit Project Criteria' : 'Create New Project Criteria' }}
            </h4>

            <form wire:submit.prevent="{{ $editingCriteria ? 'updateCriteria' : 'createCriteria' }}" class="space-y-6">
                <!-- Criteria Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-3 transition-colors duration-300">Criteria Type</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @php
                            $criteriaTypes = [
                                'deliverable' => ['icon' => 'fa-file-upload', 'label' => 'Deliverable', 'color' => 'blue'],
                                'rubric' => ['icon' => 'fa-clipboard-check', 'label' => 'Rubric', 'color' => 'green'],
                                'presentation' => ['icon' => 'fa-presentation', 'label' => 'Presentation', 'color' => 'purple'],
                                'documentation' => ['icon' => 'fa-file-alt', 'label' => 'Documentation', 'color' => 'yellow'],
                            ];
                        @endphp

                        @foreach ($criteriaTypes as $type => $config)
                            <button type="button" wire:click="selectCriteriaType('{{ $type }}')"
                                class="p-3 rounded-lg text-center transition-colors border-2 text-sm duration-300
                                        {{ $criteriaType === $type
                                            ? 'bg-' . $config['color'] . '-600 border-' . $config['color'] . '-500 text-white'
                                            : 'bg-themed-secondary border-themed-primary text-themed-primary hover:bg-themed-tertiary hover:border-' . $config['color'] . '-400' }}">
                                <i class="fas {{ $config['icon'] }} text-lg mb-1 block"></i>
                                <div class="font-medium">{{ $config['label'] }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                            Criteria Title <span class="text-red-600 dark:text-red-400">*</span>
                        </label>
                        <input type="text" wire:model="criteriaTitle"
                            class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                      focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300"
                            placeholder="Enter criteria title...">
                        @error('criteriaTitle')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Points</label>
                        <input type="number" wire:model="points" step="1" min="1" max="100"
                            class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                      focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300">
                        @error('points')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                        Description <span class="text-red-600 dark:text-red-400">*</span>
                    </label>
                    <textarea wire:model="criteriaDescription" rows="3"
                        class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                     focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300"
                        placeholder="Describe what students need to accomplish..."></textarea>
                    @error('criteriaDescription')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- File Requirements (for deliverable type) -->
                @if ($criteriaType === 'deliverable')
                    <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                        <h5 class="text-themed-primary font-medium mb-3 transition-colors duration-300">File Upload Settings</h5>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Max File Size (MB)</label>
                                <input type="number" wire:model="maxFileSize" min="1" max="100"
                                    class="w-full px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary transition-colors duration-300">
                                @error('maxFileSize')
                                    <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Min Files</label>
                                <input type="number" wire:model="minFiles" min="0" max="20"
                                    class="w-full px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary transition-colors duration-300">
                                @error('minFiles')
                                    <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Max Files</label>
                                <input type="number" wire:model="maxFiles" min="1" max="20"
                                    class="w-full px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary transition-colors duration-300">
                                @error('maxFiles')
                                    <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- File Types -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-themed-primary transition-colors duration-300">Allowed File Types</label>
                                <button type="button" wire:click="addFileType"
                                    class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors">
                                    <i class="fas fa-plus mr-1"></i>Add Type
                                </button>
                            </div>

                            @foreach ($fileTypes as $index => $fileType)
                                <div class="flex gap-2 mb-2">
                                    <input type="text" wire:model="fileTypes.{{ $index }}"
                                        class="flex-1 px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary transition-colors duration-300"
                                        placeholder="e.g., pdf, docx, jpg">
                                    <button type="button" wire:click="removeFileType({{ $index }})"
                                        class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition-colors">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Rubric Levels (for rubric type) -->
                @if ($criteriaType === 'rubric')
                    <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <h5 class="text-themed-primary font-medium transition-colors duration-300">Rubric Levels</h5>
                            <button type="button" wire:click="addRubricLevel"
                                class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors">
                                <i class="fas fa-plus mr-1"></i>Add Level
                            </button>
                        </div>

                        @foreach ($rubricLevels as $index => $level)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3 p-3 bg-themed-tertiary rounded-lg border border-themed-primary transition-colors duration-300">
                                <div>
                                    <input type="text" wire:model="rubricLevels.{{ $index }}.name"
                                        class="w-full px-3 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary transition-colors duration-300"
                                        placeholder="Level name">
                                </div>
                                <div>
                                    <input type="number" wire:model="rubricLevels.{{ $index }}.points"
                                        min="0"
                                        class="w-full px-3 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary transition-colors duration-300"
                                        placeholder="Points">
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" wire:model="rubricLevels.{{ $index }}.description"
                                        class="flex-1 px-3 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary transition-colors duration-300"
                                        placeholder="Description">
                                    @if (count($rubricLevels) > 2)
                                        <button type="button" wire:click="removeRubricLevel({{ $index }})"
                                            class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition-colors">
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
                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-themed-primary rounded bg-themed-tertiary">
                    <label for="isRequired" class="ml-2 block text-sm text-themed-primary transition-colors duration-300">
                        Required for project completion
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-4 border-t border-themed-primary transition-colors duration-300">
                    <button type="button" wire:click="toggleCreateForm"
                        class="px-4 py-2 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary rounded-lg transition-colors duration-300 border border-themed-primary">
                        Cancel
                    </button>

                    <button type="submit" wire:loading.attr="disabled"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-300 
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
        <h4 class="text-themed-primary font-medium transition-colors duration-300">Project Criteria ({{ count($criteria) }})</h4>

        @if (count($criteria) > 0)
            <div class="space-y-3" id="criteria-container">
                @foreach ($criteria as $index => $criterium)
                    @php
                        $criteriumData = $normalizeBuilderArray($criterium['options']);
                        $criteriumType = $criteriumData['criteria_type'] ?? 'deliverable';
                    @endphp
                    <div class="bg-themed-tertiary rounded-lg border border-themed-primary p-4 sortable-item transition-colors duration-300"
                        data-id="{{ $criterium['id'] }}">
                        <div class="flex items-start gap-4">
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-move text-themed-secondary hover:text-themed-primary mt-1 transition-colors duration-300">
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
                                                class="px-2 py-1 text-xs rounded-full border transition-colors duration-300
                                                {{ $criteriumType === 'deliverable'
                                                    ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-700'
                                                    : ($criteriumType === 'rubric'
                                                        ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border-green-200 dark:border-green-700'
                                                        : ($criteriumType === 'presentation'
                                                            ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 border-purple-200 dark:border-purple-700'
                                                            : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $criteriumType)) }}
                                            </span>
                                            <span
                                                class="text-sm text-green-600 dark:text-green-400 font-medium">{{ $criterium['points'] }}
                                                pts</span>
                                            @if ($criterium['is_required'])
                                                <span class="text-xs text-red-600 dark:text-red-400">Required</span>
                                            @endif
                                        </div>

                                        <p class="text-themed-primary font-medium mb-2 transition-colors duration-300">{{ $criterium['question_text'] }}</p>

                                        @if ($criterium['explanation'])
                                            <p class="text-themed-secondary text-sm mb-2 transition-colors duration-300">{{ $criterium['explanation'] }}</p>
                                        @endif

                                        <!-- Type-specific display -->
                                        @if ($criteriumType === 'deliverable' && !empty($criteriumData['file_types']))
                                            <div class="text-sm text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 px-2 py-1 rounded border border-blue-200 dark:border-blue-700 transition-colors duration-300">
                                                <strong>File Types:</strong>
                                                {{ implode(', ', $criteriumData['file_types']) }}
                                                | Max: {{ $criteriumData['max_file_size'] ?? 10 }}MB
                                            </div>
                                        @elseif ($criteriumType === 'rubric' && !empty($criteriumData['rubric_levels']))
                                            <div class="text-sm text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded border border-green-200 dark:border-green-700 transition-colors duration-300">
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
                                            class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button wire:click="duplicateCriteria({{ $criterium['id'] }})"
                                            class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors"
                                            title="Duplicate Criteria">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <button wire:click="deleteCriteria({{ $criterium['id'] }})"
                                            onclick="return confirm('Are you sure you want to delete this criteria?')"
                                            class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition-colors">
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
                <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mx-auto mb-4 border border-themed-primary transition-colors duration-300">
                    <i class="fas fa-project-diagram text-2xl text-themed-secondary transition-colors duration-300"></i>
                </div>
                <h4 class="text-lg font-medium text-themed-primary mb-2 transition-colors duration-300">No Project Criteria Yet</h4>
                <p class="text-themed-secondary mb-4 transition-colors duration-300">Create criteria to define project requirements and evaluation standards.
                </p>
                <button wire:click="toggleCreateForm"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
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
