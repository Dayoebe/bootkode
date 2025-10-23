<div class="bg-themed-secondary rounded-xl p-4 sticky top-4 max-h-[80vh] overflow-y-auto border border-themed-primary transition-colors duration-300 shadow-lg">
    <h2 class="text-lg font-bold text-themed-primary mb-4 flex items-center">
        <i class="fas fa-list-ul mr-2 text-accent-themed-primary"></i>
        Course Content
    </h2>
    
    <div class="space-y-3">
        @foreach($sections as $section)
            @php
                $sectionProgress = $this->calculateSectionProgress($section);
                $isUnlocked = $this->isSectionUnlocked($section->id);
                $isCompleted = $this->isSectionCompleted($section);
            @endphp
            
            <div class="bg-themed-tertiary rounded-lg p-3 transition-all duration-200 border border-themed-secondary
                        {{ $isUnlocked ? '' : 'opacity-50' }}
                        {{ $isCompleted ? 'ring-2 ring-green-500' : '' }}">
                
                <!-- Section Header -->
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        @if($isCompleted)
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        @elseif($isUnlocked)
                            <i class="fas fa-unlock text-accent-themed-primary mr-2"></i>
                        @else
                            <i class="fas fa-lock text-themed-tertiary mr-2"></i>
                        @endif
                        
                        <h3 class="font-medium text-themed-primary text-sm">{{ $section->title }}</h3>
                    </div>
                    
                    <div class="flex items-center text-xs text-themed-tertiary">
                        <span>{{ $section->lessons->count() }}</span>
                        <i class="fas fa-book-open ml-1"></i>
                    </div>
                </div>
                
                <!-- Section Progress Bar -->
                <div class="mb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs text-themed-tertiary">Progress</span>
                        <span class="text-xs font-medium text-themed-primary">{{ $sectionProgress }}%</span>
                    </div>
                    <div class="w-full bg-themed-secondary rounded-full h-1.5 border border-themed-secondary">
                        <div class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary h-1.5 rounded-full transition-all duration-300" 
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
                                    ? 'bg-accent-themed-primary text-white shadow-lg' 
                                    : ($isUnlocked 
                                        ? 'bg-themed-secondary hover:bg-themed-tertiary text-themed-primary' 
                                        : 'bg-themed-secondary text-themed-tertiary cursor-not-allowed')
                                }}">
                            
                            <div class="flex items-center min-w-0">
                                @if($isLessonCompleted)
                                    <i class="fas fa-check-circle text-green-500 mr-2 flex-shrink-0"></i>
                                @elseif($isCurrentLesson)
                                    <i class="fas fa-play-circle text-white mr-2 flex-shrink-0"></i>
                                @elseif($isUnlocked)
                                    <i class="far fa-circle mr-2 flex-shrink-0"></i>
                                @else
                                    <i class="fas fa-lock text-themed-tertiary mr-2 flex-shrink-0"></i>
                                @endif
                                
                                <span class="truncate">{{ $lesson->title }}</span>
                            </div>
                            
                            <div class="flex items-center text-xs ml-2 flex-shrink-0">
                                @if($lesson->formatted_duration !== 'N/A')
                                    <span class="text-themed-tertiary">{{ $lesson->formatted_duration }}</span>
                                @endif
                                
                                @if($lesson->hasVideo())
                                    <i class="fas fa-video ml-1 text-purple-500"></i>
                                @endif
                                
                                @if($lesson->hasAudio())
                                    <i class="fas fa-volume-up ml-1 text-blue-500"></i>
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
                
                @if(!$isUnlocked && !$loop->first)
                    <div class="mt-2 text-xs text-themed-tertiary bg-themed-secondary p-2 rounded text-center border border-themed-secondary">
                        <i class="fas fa-info-circle mr-1"></i>
                        Complete {{ $this->sectionCompletionThreshold ?? 80 }}% of previous section to unlock
                    </div>
                @elseif($isCompleted)
                    <div class="mt-2 text-xs text-green-400 bg-green-500/20 p-2 rounded text-center border border-green-500/30">
                        <i class="fas fa-trophy mr-1"></i>
                        Section Completed!
                    </div>
                @elseif($isUnlocked && $sectionProgress > 0)
                    <div class="mt-2 text-xs text-accent-themed-primary bg-accent-themed-primary/20 p-2 rounded text-center border border-accent-themed-primary/30">
                        <i class="fas fa-play mr-1"></i>
                        In Progress - {{ $sectionProgress }}%
                    </div>
                @endif
            </div>
        @endforeach
    </div>
    
    <!-- Course Stats -->
    <div class="mt-4 pt-4 border-t border-themed-secondary">
        <div class="text-xs text-themed-tertiary space-y-1">
            <div class="flex justify-between">
                <span>Total Sections:</span>
                <span class="font-medium text-themed-primary">{{ $sections->count() }}</span>
            </div>
            <div class="flex justify-between">
                <span>Total Lessons:</span>
                <span class="font-medium text-themed-primary">{{ $sections->flatMap->lessons->count() }}</span>
            </div>
            <div class="flex justify-between">
                <span>Completed:</span>
                <span class="text-green-400 font-medium">{{ count($completedLessons) }}</span>
            </div>
        </div>
    </div>

    <style>
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
</div>