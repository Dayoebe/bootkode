<div class="bg-white dark:bg-gray-800 p-4 sm:p-6 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">
    
    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 transition-colors duration-300">Course Outline</h3>

    <!-- Create Section Form -->
    <div class="mb-6">
        <form wire:submit.prevent="createSection" class="flex flex-col sm:flex-row gap-2">
            <input type="text" wire:model="newSectionTitle"
                class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300"
                placeholder="Enter section title..." aria-label="New section title">
            <button type="submit" 
                wire:loading.attr="disabled" 
                wire:target="createSection"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg transition-colors duration-300 flex items-center justify-center min-w-[120px]">
                <span wire:loading.remove wire:target="createSection">
                    <i class="fas fa-plus mr-2"></i> Add Section
                </span>
                <span wire:loading wire:target="createSection">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Creating...
                </span>
            </button>
        </form>
        @error('newSectionTitle')
            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- Sections List -->
    <div class="space-y-2">
        @forelse ($sections as $sectionIndex => $section)
            <div wire:key="section-{{ $section->id }}" class="bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 transition-all duration-300">
                
                <!-- Section Header -->
                <div class="flex items-center justify-between p-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-300 rounded-t-lg"
                    wire:click="toggleSection({{ $section->id }})">
                    <div class="flex items-center flex-1 min-w-0">
                        <i class="fas fa-folder text-blue-600 dark:text-blue-400 mr-2 transition-all duration-200"></i>
                        @if ($editingSectionId === $section->id)
                            <input wire:model="newSectionTitleEdit" 
                                   wire:keydown.enter="updateSection"
                                   wire:keydown.escape="cancelEditSection"
                                   class="bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-2 py-1 rounded flex-1 border border-gray-300 dark:border-gray-600 transition-colors duration-300"
                                   autofocus>
                        @else
                            <span class="text-gray-800 dark:text-white truncate transition-colors duration-300">{{ $section->title }}</span>
                        @endif
                        <span class="ml-2 text-xs text-gray-500 dark:text-gray-400 bg-gray-200 dark:bg-gray-600 px-2 py-1 rounded-full">
                            {{ $section->lessons->count() }}
                        </span>
                    </div>

                    <div class="flex space-x-2">
                        @if ($editingSectionId === $section->id)
                            <button wire:click="updateSection"
                                class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 transition-colors p-1">
                                <i class="fas fa-check"></i>
                            </button>
                            <button wire:click="cancelEditSection"
                                class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-colors p-1">
                                <i class="fas fa-times"></i>
                            </button>
                        @else
                            <button wire:click="startEditSection({{ $section->id }})"
                                class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors p-1">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button wire:click="deleteSection({{ $section->id }})"
                                onclick="return confirm('Are you sure you want to delete this section and all its lessons?')"
                                class="text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors p-1">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Lessons List -->
                @if(in_array($section->id, $expandedSections))
                    <div class="ml-8 pr-3 pb-2 space-y-2 bg-gray-50 dark:bg-gray-700 rounded-b-lg border-t border-gray-200 dark:border-gray-600 transition-colors duration-300">
                        
                        <!-- Lessons -->
                        @foreach ($section->lessons as $lesson)
                            <div wire:key="lesson-{{ $lesson->id }}"
                                class="flex items-center justify-between group p-2 rounded transition-all duration-200
                                        {{ $activeLessonId == $lesson->id 
                                            ? 'bg-blue-500 text-white shadow-lg transform scale-[1.02]' 
                                            : 'bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-800 dark:text-white' }}">
                                <div class="flex items-center flex-1 min-w-0">
                                    @if ($editingLessonId === $lesson->id)
                                        <input wire:model="newLessonTitleEdit" 
                                               wire:keydown.enter="updateLesson"
                                               wire:keydown.escape="cancelEditLesson"
                                               class="bg-white dark:bg-gray-700 text-gray-900 dark:text-white px-2 py-1 rounded flex-1 border border-gray-300 dark:border-gray-600 transition-colors duration-300"
                                               autofocus>
                                    @else
                                        <button wire:click="selectLesson({{ $lesson->id }})"
                                            class="flex items-center flex-1 min-w-0 text-left truncate">
                                            <i class="fas fa-play-circle mr-2 transition-all duration-200
                                                {{ $activeLessonId == $lesson->id ? 'text-white text-lg' : 'text-blue-600 dark:text-blue-400' }}"></i>
                                            <span class="truncate transition-all duration-200 {{ $activeLessonId == $lesson->id ? 'text-white font-medium' : '' }}">
                                                {{ $lesson->title }}
                                            </span>
                                        </button>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    @if ($editingLessonId === $lesson->id)
                                        <button wire:click="updateLesson"
                                            class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 transition-colors p-1">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button wire:click="cancelEditLesson"
                                            class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white transition-colors p-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <button wire:click="startEditLesson({{ $lesson->id }})"
                                            class="opacity-0 group-hover:opacity-100 transition-all duration-200 p-1
                                                {{ $activeLessonId == $lesson->id 
                                                    ? 'text-white hover:text-blue-200' 
                                                    : 'text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400' }}">
                                            <i class="fas fa-edit text-xs"></i>
                                        </button>
                                        <button wire:click="deleteLesson({{ $lesson->id }})"
                                            onclick="return confirm('Delete this lesson? This action cannot be undone.')"
                                            class="opacity-0 group-hover:opacity-100 transition-all duration-200 p-1
                                                {{ $activeLessonId == $lesson->id 
                                                    ? 'text-white hover:text-red-200' 
                                                    : 'text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400' }}">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Add Lesson Form -->
                        <form wire:submit.prevent="createLesson({{ $section->id }})" class="flex items-center gap-2 pt-2">
                            <input type="text" wire:model="newLessonTitles.{{ $section->id }}"
                                class="flex-1 px-3 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300"
                                placeholder="New lesson title...">
                            <button type="submit" 
                                wire:loading.attr="disabled"
                                wire:target="createLesson({{ $section->id }})"
                                class="px-2 py-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded text-sm transition-colors duration-300 min-w-[60px] flex items-center justify-center">
                                <span wire:loading.remove wire:target="createLesson({{ $section->id }})">
                                    <i class="fas fa-plus mr-1"></i>Add
                                </span>
                                <span wire:loading wire:target="createLesson({{ $section->id }})">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </span>
                            </button>
                        </form>
                        @error('newLessonTitles.' . $section->id)
                            <span class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>
        @empty
            <div class="text-gray-500 dark:text-gray-400 text-center py-8 transition-colors duration-300">
                <i class="fas fa-folder-plus text-4xl mb-3 opacity-50"></i>
                <p>No sections yet. Create one to start!</p>
            </div>
        @endforelse
    </div>
</div>