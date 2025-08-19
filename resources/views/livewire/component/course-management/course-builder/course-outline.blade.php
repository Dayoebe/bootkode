<div class="bg-gray-900 p-4 sm:p-6 rounded-xl shadow-lg"
     role="region" 
     aria-label="Course Outline">
    <h3 class="text-lg font-bold text-white mb-4">Course Outline</h3>

    <!-- Create Section Form -->
    <div class="mb-6">
        <form wire:submit.prevent="createSection" class="flex flex-col sm:flex-row gap-2">
            <input type="text" 
                   wire:model="newSectionTitle"
                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                   placeholder="Enter section title..."
                   aria-label="New section title">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center"
                    aria-label="Create new section">
                <i class="fas fa-plus mr-2"></i> Add Section
            </button>
        </form>
        @error('newSectionTitle')
            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- Sections List -->
    <div class="space-y-2 max-h-[60vh] overflow-y-auto custom-scrollbar" id="sortable-sections">
        @forelse ($sections as $section)
            <div wire:key="section-{{ $section->id }}" 
                 data-id="{{ $section->id }}"
                 class="bg-gray-800 rounded-lg border border-gray-700 transition-all duration-300"
                 x-data="{ showLessonForm: false }">
                <!-- Section Header -->
                <div class="flex items-center justify-between p-3 cursor-pointer hover:bg-gray-700 transition-colors">
                    <div class="flex items-center flex-1 min-w-0">
                        <i class="fas fa-grip-vertical mr-2 text-gray-400 hover:text-white cursor-move"></i>
                        <button wire:click="toggleSection({{ $section->id }})" 
                                class="flex items-center flex-1 min-w-0 text-left">
                            <i class="fas fa-folder text-blue-400 mr-2"></i>
                            @if($editingSectionId === $section->id)
                                <input wire:model="newSectionTitleEdit"
                                       wire:keydown.enter="updateSection"
                                       wire:keydown.escape="cancelEditSection"
                                       class="bg-gray-700 text-white px-2 py-1 rounded flex-1"
                                       autofocus>
                            @else
                                <span class="text-white truncate">{{ $section->title }}</span>
                            @endif
                        </button>
                    </div>
                    
                    <div class="flex space-x-2">
                        @if($editingSectionId === $section->id)
                            <button wire:click="updateSection" 
                                    class="text-green-400 hover:text-green-300 transition-colors p-1">
                                <i class="fas fa-check"></i>
                            </button>
                            <button wire:click="cancelEditSection" 
                                    class="text-gray-400 hover:text-white transition-colors p-1">
                                <i class="fas fa-times"></i>
                            </button>
                        @else
                            <button wire:click="startEditSection({{ $section->id }})" 
                                    class="text-gray-400 hover:text-blue-400 transition-colors p-1">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="deleteSection({{ $section->id }})" 
                                    class="text-gray-400 hover:text-red-400 transition-colors p-1"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">
                                <i class="fas fa-trash"></i>
                            </button>
                            <button wire:click="toggleSection({{ $section->id }})" 
                                    class="text-gray-400 hover:text-white transition-transform p-1"
                                    :class="{ 'rotate-90': @js(in_array($section->id, $expandedSections)) }">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Lessons List (Collapsible) -->
                <div x-show="@js(in_array($section->id, $expandedSections))" 
                     x-collapse
                     class="ml-8 pr-3 pb-2 space-y-2"
                     id="sortable-lessons-{{ $section->id }}">
                    @foreach($section->lessons as $lesson)
                        <div wire:key="lesson-{{ $lesson->id }}" 
                             data-id="{{ $lesson->id }}"
                             class="flex items-center justify-between group bg-gray-700 p-2 rounded hover:bg-gray-600 transition-colors">
                            <button wire:click="selectLesson({{ $lesson->id }})"
                                    class="flex items-center flex-1 min-w-0 text-left">
                                <i class="fas fa-play-circle text-blue-400 mr-2"></i>
                                <span class="text-white truncate">{{ $lesson->title }}</span>
                            </button>
                            <button wire:click="deleteLesson({{ $lesson->id }})"
                                    class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-400 ml-2 transition-opacity p-1"
                                    onclick="confirm('Delete this lesson?') || event.stopImmediatePropagation()">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    @endforeach

                    <!-- Add Lesson Form -->
                    <form wire:submit.prevent="createLesson({{ $section->id }})" 
                          class="flex items-center gap-2 pt-2">
                        <input type="text" 
                               wire:model="newLessonTitles.{{ $section->id }}"
                               class="flex-1 px-3 py-1 bg-gray-700 border border-gray-600 rounded text-white placeholder-gray-400 text-sm"
                               placeholder="New lesson title...">
                        <button type="submit"
                                class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm">
                            Add
                        </button>
                    </form>
                    @error('newLessonTitles.'.$section->id)
                        <span class="text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        @empty
            <p class="text-gray-400 text-center py-4">No sections yet. Create one to start!</p>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:navigated', () => {
        // Sections reordering
        new Sortable(document.getElementById('sortable-sections'), {
            handle: '.fa-grip-vertical',
            animation: 150,
            ghostClass: 'bg-blue-900/20',
            onEnd: (evt) => {
                const orderedIds = Array.from(evt.from.children).map(el => el.dataset.id);
                Livewire.dispatch('reorder-sections', { orderedIds });
            }
        });

        // Lessons reordering (for each section)
        @foreach($sections ?? [] as $section)
            new Sortable(document.getElementById('sortable-lessons-{{ $section->id }}'), {
                animation: 150,
                ghostClass: 'bg-blue-900/20',
                onEnd: (evt) => {
                    const orderedIds = Array.from(evt.from.children)
                        .filter(el => el.dataset.id)
                        .map(el => el.dataset.id);
                    Livewire.dispatch('reorder-lessons', {
                        sectionId: {{ $section->id }},
                        orderedIds: orderedIds
                    });
                }
            });
        @endforeach
    });
</script>
@endpush