<div class="bg-gray-800 rounded-xl p-6">
    <!-- Lesson Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex-1">
            <div class="flex items-center text-sm text-gray-400 mb-2">
                <span>{{ $lesson->section->title }}</span>
                <i class="fas fa-chevron-right mx-2"></i>
                <span>Lesson {{ $currentIndex + 1 }}</span>
            </div>
            <h2 class="text-xl font-bold text-white">{{ $lesson->title }}</h2>
            
            @if($lesson->description)
                <p class="text-gray-300 mt-2">{{ $lesson->description }}</p>
            @endif
            
            <div class="flex items-center gap-4 mt-3 text-sm text-gray-400">
                @if($lesson->formatted_duration !== 'N/A')
                    <span class="flex items-center">
                        <i class="fas fa-clock mr-1"></i>
                        {{ $lesson->formatted_duration }}
                    </span>
                @endif
                
                @if($lesson->difficulty_level)
                    <span class="capitalize bg-gray-700 px-2 py-1 rounded text-xs">
                        {{ $lesson->difficulty_level }}
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Completion Toggle -->
        <div class="flex items-center gap-3">
            @if($isCompleted)
                <button wire:click="markAsIncomplete" 
                        class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors flex items-center">
                    <i class="fas fa-undo mr-2"></i> Mark Incomplete
                </button>
            @else
                <button wire:click="markAsCompleted" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition-colors flex items-center">
                    <i class="fas fa-check mr-2"></i> Mark Complete
                </button>
            @endif
        </div>
    </div>

    <!-- Lesson Content -->
    <div class="lesson-content">


        <!-- Main Text Content -->
        @if($lesson->content)
            <div class="prose prose-invert max-w-none mb-6">
                {!! $lesson->content !!}
            </div>
        @endif


        <!-- Documents -->
        @if($lesson->hasDocuments() && count($lesson->getDocumentsArray()) > 0)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-white mb-3">Course Materials</h3>
                <div class="grid gap-3">
                    @foreach($lesson->getDocumentsArray() as $document)
                        <div class="bg-gray-700 rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-indigo-600 rounded flex items-center justify-center mr-3">
                                    @switch(strtolower($document['type'] ?? 'file'))
                                        @case('pdf')
                                            <i class="fas fa-file-pdf text-white"></i>
                                            @break
                                        @case('doc')
                                        @case('docx')
                                            <i class="fas fa-file-word text-white"></i>
                                            @break
                                        @case('ppt')
                                        @case('pptx')
                                            <i class="fas fa-file-powerpoint text-white"></i>
                                            @break
                                        @default
                                            <i class="fas fa-file text-white"></i>
                                    @endswitch
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $document['name'] }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ number_format($document['size'] / 1024 / 1024, 1) }}MB
                                    </p>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $document['path']) }}" 
                               target="_blank"
                               class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm transition-colors">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif


        <!-- Video Content -->
        @if($lesson->video_url)
            <div class="mb-6">
                <div class="bg-black rounded-lg overflow-hidden">
                    @if(str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
                        @php
                            $videoId = '';
                            if (str_contains($lesson->video_url, 'youtube.com/watch?v=')) {
                                parse_str(parse_url($lesson->video_url, PHP_URL_QUERY), $query);
                                $videoId = $query['v'] ?? '';
                            } elseif (str_contains($lesson->video_url, 'youtu.be/')) {
                                $videoId = substr(parse_url($lesson->video_url, PHP_URL_PATH), 1);
                            }
                        @endphp
                        
                        @if($videoId)
                            <iframe 
                                class="w-full aspect-video"
                                src="https://www.youtube.com/embed/{{ $videoId }}"
                                title="Lesson Video"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        @endif
                    @else
                        <video controls class="w-full aspect-video">
                            <source src="{{ $lesson->video_url }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @endif
                </div>
            </div>
        @endif

        <!-- Uploaded Videos -->
        @if($lesson->hasVideo() && count($lesson->getVideosArray()) > 0)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-white mb-3">Course Videos</h3>
                <div class="grid gap-4">
                    @foreach($lesson->getVideosArray() as $video)
                        <div class="bg-gray-700 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-white font-medium">{{ $video['name'] }}</span>
                                <span class="text-xs text-gray-400">
                                    {{ number_format($video['size'] / 1024 / 1024, 1) }}MB
                                </span>
                            </div>
                            <video controls class="w-full rounded">
                                <source src="{{ asset('storage/' . $video['path']) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Audio Content -->
        @if($lesson->hasAudio() && count($lesson->getAudiosArray()) > 0)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-white mb-3">Audio Content</h3>
                <div class="space-y-3">
                    @foreach($lesson->getAudiosArray() as $audio)
                        <div class="bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-white font-medium">{{ $audio['name'] }}</span>
                                <span class="text-xs text-gray-400">
                                    {{ number_format($audio['size'] / 1024 / 1024, 1) }}MB
                                </span>
                            </div>
                            <audio controls class="w-full">
                                <source src="{{ asset('storage/' . $audio['path']) }}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Images -->
        @if($lesson->hasImage())
            <div class="mb-6">
                @if($lesson->image_path)
                    <img src="{{ asset('storage/' . $lesson->image_path) }}" 
                         alt="Lesson Image" 
                         class="w-full rounded-lg mb-4">
                @endif
                
                @if(count($lesson->getImagesArray()) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($lesson->getImagesArray() as $image)
                            <div class="bg-gray-700 rounded-lg overflow-hidden">
                                <img src="{{ asset('storage/' . $image['path']) }}" 
                                     alt="Lesson Image" 
                                     class="w-full h-48 object-cover">
                                <div class="p-3">
                                    <p class="text-sm text-gray-300">{{ $image['name'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <!-- External Links -->
        @if($lesson->hasExternalLinks() && count($lesson->getExternalLinksArray()) > 0)
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-white mb-3">Additional Resources</h3>
                <div class="space-y-2">
                    @foreach($lesson->getExternalLinksArray() as $link)
                        <a href="{{ $link['url'] }}" 
                           target="_blank"
                           class="block bg-gray-700 hover:bg-gray-600 rounded-lg p-4 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-external-link-alt text-indigo-400 mr-3"></i>
                                <div>
                                    <p class="text-white font-medium">{{ $link['title'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $link['url'] }}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Lesson Navigation -->
    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-700">
        @if($this->getPreviousLesson())
            @php $prevLesson = $this->getPreviousLesson(); @endphp
            <button wire:click="goToPreviousLesson" 
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg flex items-center transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> 
                <span class="hidden sm:inline">Previous:</span>
                <span class="ml-1 truncate max-w-32">
                    {{ is_object($prevLesson) ? $prevLesson->title : $prevLesson['title'] }}
                </span>
            </button>
        @else
            <div></div>
        @endif

        @if($this->getNextLesson())
            @php $nextLesson = $this->getNextLesson(); @endphp
            @if($this->isNextLessonUnlocked())
                <button wire:click="goToNextLesson" 
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center transition-colors">
                    <span class="hidden sm:inline">Next:</span>
                    <span class="mr-1 truncate max-w-32">
                        {{ is_object($nextLesson) ? $nextLesson->title : $nextLesson['title'] }}
                    </span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            @else
                <div class="px-4 py-2 bg-gray-600 text-gray-400 rounded-lg flex items-center cursor-not-allowed">
                    <i class="fas fa-lock mr-2"></i>
                    <span class="text-sm">Complete section to continue</span>
                </div>
            @endif
        @else
            <button wire:click="completeCourse" 
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center transition-colors">
                <i class="fas fa-trophy mr-2"></i>
                Complete Course
            </button>
        @endif
    </div>

    <!-- Progress Indicator -->
    <div class="mt-4">
        <div class="flex justify-between text-sm text-gray-400 mb-2">
            <span>Lesson {{ $currentIndex + 1 }} of {{ count($allLessons) }}</span>
            <span>{{ round((($currentIndex + 1) / count($allLessons)) * 100) }}% through course</span>
        </div>
        <div class="w-full bg-gray-700 rounded-full h-2">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all duration-300" 
                 style="width: {{ round((($currentIndex + 1) / count($allLessons)) * 100) }}%"></div>
        </div>
    </div>
    
    <style>
    .lesson-content {
        line-height: 1.7;
    }
    
    .lesson-content h1, .lesson-content h2, .lesson-content h3, .lesson-content h4 {
        color: #ffffff;
        margin-top: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .lesson-content p {
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .lesson-content ul, .lesson-content ol {
        color: #d1d5db;
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .lesson-content blockquote {
        border-left: 4px solid #4f46e5;
        padding-left: 1rem;
        margin: 1.5rem 0;
        background-color: rgba(79, 70, 229, 0.1);
        border-radius: 0.375rem;
    }
    
    .lesson-content code {
        background-color: #374151;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
    
    .lesson-content pre {
        background-color: #1f2937;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1rem 0;
    }
</style>
</div>