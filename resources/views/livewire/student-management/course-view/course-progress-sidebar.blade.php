<div class="bg-gray-800 rounded-xl p-4 sticky top-4 max-h-[80vh] overflow-y-auto">
    <h2 class="text-lg font-bold text-white mb-4 flex items-center">
        <i class="fas fa-list-ul mr-2 text-indigo-400"></i>
        Course Content
    </h2>
    
    <div class="space-y-3">
        @foreach($sections as $section)
            @php
                $sectionProgress = $this->calculateSectionProgress($section);
                $isUnlocked = $this->isSectionUnlocked($section->id);
                $isCompleted = $this->isSectionCompleted($section);
            @endphp
            
            <div class="bg-gray-700 rounded-lg p-3 transition-all duration-200 
                        {{ $isUnlocked ? '' : 'opacity-50' }}
                        {{ $isCompleted ? 'ring-2 ring-green-500' : '' }}">
                
                <!-- Section Header -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        @if($isCompleted)
                            <i class="fas fa-check-circle text-green-400 mr-2"></i>
                        @elseif($isUnlocked)
                            <i class="fas fa-unlock text-indigo-400 mr-2"></i>
                        @else
                            <i class="fas fa-lock text-gray-500 mr-2"></i>
                        @endif
                        
                        <h3 class="font-medium text-white text-sm">{{ $section->title }}</h3>
                    </div>
                    
                    <div class="flex items-center text-xs text-gray-400">
                        <span>{{ $section->lessons->count() }}</span>
                        <i class="fas fa-book-open ml-1"></i>
                    </div>
                </div>
                
                <!-- Section Progress Bar -->
                <div class="mb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-gray-400">Progress</span>
                        <span class="text-xs text-gray-300 font-medium">{{ $sectionProgress }}%</span>
                    </div>
                    <div class="w-full bg-gray-600 rounded-full h-1.5">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-1.5 rounded-full transition-all duration-300" 
                             style="width: {{ $sectionProgress }}%"></div>
                    </div>
                </div>
                
                <!-- Lessons List -->
                <div class="space-y-1">
                    @foreach($section->lessons as $lesson)
                        @php
                            $isLessonCompleted = in_array($lesson->id, $completedLessons);
                            $isCurrentLesson = $currentLesson && $currentLesson->id == $lesson->id;
                        @endphp
                        
                        <button 
                            wire:click="selectLesson({{ $lesson->id }}, {{ $section->id }})"
                            @if(!$isUnlocked) disabled @endif
                            class="w-full text-left p-2 rounded-md flex items-center justify-between text-sm transition-all duration-150
                                {{ $isCurrentLesson 
                                    ? 'bg-indigo-600 text-white shadow-lg' 
                                    : ($isUnlocked 
                                        ? 'bg-gray-600 hover:bg-gray-500 text-gray-300' 
                                        : 'bg-gray-600 text-gray-500 cursor-not-allowed')
                                }}">
                            
                            <div class="flex items-center min-w-0">
                                @if($isLessonCompleted)
                                    <i class="fas fa-check-circle text-green-400 mr-2 flex-shrink-0"></i>
                                @elseif($isCurrentLesson)
                                    <i class="fas fa-play-circle text-white mr-2 flex-shrink-0"></i>
                                @elseif($isUnlocked)
                                    <i class="far fa-circle mr-2 flex-shrink-0"></i>
                                @else
                                    <i class="fas fa-lock text-gray-500 mr-2 flex-shrink-0"></i>
                                @endif
                                
                                <span class="truncate">{{ $lesson->title }}</span>
                            </div>
                            
                            <div class="flex items-center text-xs ml-2 flex-shrink-0">
                                @if($lesson->formatted_duration !== 'N/A')
                                    <span class="text-gray-400">{{ $lesson->formatted_duration }}</span>
                                @endif
                                
                                @if($lesson->hasVideo())
                                    <i class="fas fa-video ml-1 text-purple-400"></i>
                                @endif
                                
                                @if($lesson->hasAudio())
                                    <i class="fas fa-volume-up ml-1 text-blue-400"></i>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
                
                @if(!$isUnlocked && !$loop->first)
                    <div class="mt-2 text-xs text-gray-500 bg-gray-600 p-2 rounded text-center">
                        <i class="fas fa-info-circle mr-1"></i>
                        Complete {{ $this->sectionCompletionThreshold ?? 80 }}% of previous section to unlock
                    </div>
                @elseif($isCompleted)
                    <div class="mt-2 text-xs text-green-400 bg-green-900/20 p-2 rounded text-center">
                        <i class="fas fa-trophy mr-1"></i>
                        Section Completed!
                    </div>
                @elseif($isUnlocked && $sectionProgress > 0)
                    <div class="mt-2 text-xs text-blue-400 bg-blue-900/20 p-2 rounded text-center">
                        <i class="fas fa-play mr-1"></i>
                        In Progress - {{ $sectionProgress }}%
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    
    <!-- Course Stats -->
    <div class="mt-4 pt-4 border-t border-gray-600">
        <div class="text-xs text-gray-400 space-y-1">
            <div class="flex justify-between">
                <span>Total Sections:</span>
                <span>{{ $sections->count() }}</span>
            </div>
            <div class="flex justify-between">
                <span>Total Lessons:</span>
                <span>{{ $sections->flatMap->lessons->count() }}</span>
            </div>
            <div class="flex justify-between">
                <span>Completed:</span>
                <span class="text-green-400">{{ count($completedLessons) }}</span>
            </div>
        </div>
    </div>
</div>