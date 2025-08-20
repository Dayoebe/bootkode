<div class="bg-gray-900 p-4 sm:p-6 rounded-xl shadow-lg" role="region" aria-label="Course Outline">
    <h3 class="text-lg font-bold text-white mb-4">Course Outline</h3>

    <!-- Create Section Form -->
    <div class="mb-6">
        <form wire:submit.prevent="createSection" class="flex flex-col sm:flex-row gap-2">
            <input type="text" wire:model="newSectionTitle"
                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Enter section title..." aria-label="New section title">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center animate__animated animate__pulse animate__infinite animate__slower"
                aria-label="Create new section">
                <i class="fas fa-plus mr-2"></i> Add Section
            </button>
        </form>
        @error('newSectionTitle')
            <span class="text-red-400 text-sm mt-1 animate__animated animate__shakeX">{{ $message }}</span>
        @enderror
    </div>

    <!-- Sections List -->
    <div class="space-y-2 max-h-[60vh] overflow-y-auto custom-scrollbar">
        @forelse ($sections as $section)
            <div wire:key="section-{{ $section->id }}"
                class="bg-gray-800 rounded-lg border border-gray-700 transition-all duration-300 animate__animated animate__fadeInUp"
                x-data="{ expanded: @js(in_array($section->id, $expandedSections)) }">
                <!-- Section Header -->
                <div class="flex items-center justify-between p-3 cursor-pointer hover:bg-gray-700 transition-colors rounded-t-lg"
                    @click="expanded = !expanded; $wire.toggleSection({{ $section->id }})">
                    <div class="flex items-center flex-1 min-w-0">
                        <i class="fas fa-folder text-blue-400 mr-2 transition-transform duration-200"
                            :class="{ 'text-blue-300': expanded }"></i>
                        @if ($editingSectionId === $section->id)
                            <input wire:model="newSectionTitleEdit" wire:keydown.enter="updateSection"
                                wire:keydown.escape="cancelEditSection"
                                class="bg-gray-700 text-white px-2 py-1 rounded flex-1 animate__animated animate__fadeIn"
                                autofocus>
                        @else
                            <span class="text-white truncate">{{ $section->title }}</span>
                        @endif
                    </div>

                    <div class="flex space-x-2">
                        @if ($editingSectionId === $section->id)
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
                            <button class="text-gray-400 hover:text-white transition-all duration-200 p-1"
                                :class="{ 'rotate-90 text-blue-400': expanded }">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Lessons List (Collapsible) -->
                <div x-show="expanded" x-collapse
                    class="ml-8 pr-3 pb-2 space-y-2 bg-gray-800 rounded-b-lg border-t border-gray-700">
                    @foreach ($section->lessons as $lesson)
                        <div wire:key="lesson-{{ $lesson->id }}"
                            class="flex items-center justify-between group bg-gray-700 p-2 rounded hover:bg-gray-600 transition-all duration-200 
                                    {{ $activeLessonId == $lesson->id ? 'bg-blue-600 shadow-lg transform scale-[1.02]' : '' }}">
                            <div class="flex items-center flex-1 min-w-0">
                                <i
                                    class="fas fa-grip-vertical mr-2 text-gray-400 hover:text-white cursor-move opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                <!-- Placeholder for future drag if needed -->
                                @if ($editingLessonId === $lesson->id)
                                    <input wire:model="newLessonTitleEdit" wire:keydown.enter="updateLesson"
                                        wire:keydown.escape="cancelEditLesson"
                                        class="bg-gray-700 text-white px-2 py-1 rounded flex-1 animate__animated animate__fadeIn"
                                        autofocus>
                                @else
                                    <button wire:click="selectLesson({{ $lesson->id }})"
                                        class="flex items-center flex-1 min-w-0 text-left truncate">
                                        <i class="fas fa-play-circle mr-2 transition-all duration-200"
                                            :class="'{{ $activeLessonId == $lesson->id ? 'text-white text-lg' : 'text-blue-400' }}'"></i>
                                        <span class="truncate transition-all duration-200"
                                            :class="'{{ $activeLessonId == $lesson->id ? 'text-white font-medium' : 'text-white' }}'">
                                            {{ $lesson->title }}
                                        </span>
                                    </button>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                @if ($editingLessonId === $lesson->id)
                                    <button wire:click="updateLesson"
                                        class="text-green-400 hover:text-green-300 transition-colors p-1">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button wire:click="cancelEditLesson"
                                        class="text-gray-400 hover:text-white transition-colors p-1">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @else
                                    <button wire:click="startEditLesson({{ $lesson->id }})"
                                        class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-blue-400 transition-all duration-200 p-1">
                                        <i class="fas fa-edit text-xs"></i>
                                    </button>
                                    <button wire:click="deleteLesson({{ $lesson->id }})"
                                        class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-400 transition-all duration-200 p-1"
                                        onclick="confirm('Delete this lesson?') || event.stopImmediatePropagation()">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Add Lesson Form -->
                    <form wire:submit.prevent="createLesson({{ $section->id }})"
                        class="flex items-center gap-2 pt-2 animate__animated animate__fadeIn">
                        <input type="text" wire:model="newLessonTitles.{{ $section->id }}"
                            class="flex-1 px-3 py-1 bg-gray-700 border border-gray-600 rounded text-white placeholder-gray-400 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="New lesson title...">
                        <button type="submit"
                            class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-sm transition-colors">
                            <i class="fas fa-plus mr-1"></i>Add
                        </button>
                    </form>
                    @error('newLessonTitles.' . $section->id)
                        <span class="text-red-400 text-xs animate__animated animate__shakeX">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        @empty
            <div class="text-gray-400 text-center py-8 animate__animated animate__fadeIn">
                <i class="fas fa-folder-plus text-4xl mb-3 opacity-50"></i>
                <p>No sections yet. Create one to start!</p>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #374151;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #6B7280;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9CA3AF;
        }
    </style>
@endpush
