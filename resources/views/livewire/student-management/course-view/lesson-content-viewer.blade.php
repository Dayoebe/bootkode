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
    <div class="lesson-content-wrapper">
        <!-- Main Text Content with Trix Styling -->
        @if($lesson->content)
            <div class="lesson-content-display mb-6">
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
    /* Enhanced Lesson Content Display Styles - Matching Trix Editor */
    .lesson-content-display {
        color: #f3f4f6;
        font-size: 1rem;
        line-height: 1.7;
        max-width: none;
    }
    
    /* Headings */
    .lesson-content-display h1 {
        font-size: 1.875rem;
        font-weight: 700;
        margin: 1.5rem 0 1rem 0;
        color: #ffffff;
        line-height: 1.2;
    }
    
    .lesson-content-display h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 1.25rem 0 0.75rem 0;
        color: #ffffff;
        line-height: 1.3;
    }
    
    .lesson-content-display h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 1rem 0 0.5rem 0;
        color: #ffffff;
        line-height: 1.4;
    }
    
    .lesson-content-display h4 {
        font-size: 1.125rem;
        font-weight: 600;
        margin: 0.875rem 0 0.5rem 0;
        color: #ffffff;
        line-height: 1.4;
    }
    
    .lesson-content-display h5 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0.75rem 0 0.5rem 0;
        color: #ffffff;
        line-height: 1.4;
    }
    
    .lesson-content-display h6 {
        font-size: 0.875rem;
        font-weight: 600;
        margin: 0.75rem 0 0.5rem 0;
        color: #ffffff;
        line-height: 1.4;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* Paragraphs */
    .lesson-content-display p {
        margin: 0.875rem 0;
        color: #f3f4f6;
        line-height: 1.7;
    }
    
    /* Links */
    .lesson-content-display a {
        color: #60a5fa;
        text-decoration: underline;
        transition: color 0.2s ease;
    }
    
    .lesson-content-display a:hover {
        color: #93c5fd;
    }
    
    /* Text Formatting */
    .lesson-content-display strong,
    .lesson-content-display b {
        font-weight: 700;
        color: #ffffff;
    }
    
    .lesson-content-display em,
    .lesson-content-display i {
        font-style: italic;
        color: #e5e7eb;
    }
    
    .lesson-content-display u {
        text-decoration: underline;
        text-decoration-color: #9ca3af;
    }
    
    .lesson-content-display s,
    .lesson-content-display strike,
    .lesson-content-display del {
        text-decoration: line-through;
        color: #9ca3af;
    }
    
    /* Lists */
    .lesson-content-display ul {
        list-style-type: disc;
        margin: 1rem 0;
        padding-left: 1.5rem;
        color: #f3f4f6;
    }
    
    .lesson-content-display ol {
        list-style-type: decimal;
        margin: 1rem 0;
        padding-left: 1.5rem;
        color: #f3f4f6;
    }
    
    .lesson-content-display ul ul {
        list-style-type: circle;
        margin: 0.5rem 0;
    }
    
    .lesson-content-display ul ul ul {
        list-style-type: square;
    }
    
    .lesson-content-display ol ol {
        list-style-type: lower-alpha;
        margin: 0.5rem 0;
    }
    
    .lesson-content-display ol ol ol {
        list-style-type: lower-roman;
    }
    
    .lesson-content-display li {
        margin: 0.5rem 0;
        line-height: 1.6;
        color: #f3f4f6;
    }
    
    .lesson-content-display li p {
        margin: 0.25rem 0;
    }
    
    /* Blockquotes */
    .lesson-content-display blockquote {
        border-left: 4px solid #60a5fa;
        padding-left: 1rem;
        padding-right: 1rem;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        margin: 1.5rem 0;
        font-style: italic;
        color: #d1d5db;
        background-color: rgba(55, 65, 81, 0.5);
        border-radius: 0.5rem;
        position: relative;
    }
    
    .lesson-content-display blockquote p {
        margin: 0.5rem 0;
        color: #d1d5db;
    }
    
    .lesson-content-display blockquote::before {
        content: '"';
        font-size: 2rem;
        color: #60a5fa;
        position: absolute;
        left: 0.5rem;
        top: -0.25rem;
        font-family: Georgia, serif;
    }
    
    /* Code */
    .lesson-content-display code {
        background-color: #374151;
        color: #fbbf24;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.875rem;
        font-weight: 500;
        border: 1px solid #4b5563;
    }
    
    .lesson-content-display pre {
        background-color: #1f2937;
        color: #f3f4f6;
        padding: 1.25rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1.5rem 0;
        border: 1px solid #4b5563;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.875rem;
        line-height: 1.5;
    }
    
    .lesson-content-display pre code {
        background: none;
        color: inherit;
        padding: 0;
        border: none;
        font-size: inherit;
        border-radius: 0;
    }
    
    /* Images */
    .lesson-content-display img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        margin: 1.5rem auto;
        display: block;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        border: 1px solid #4b5563;
    }
    
    /* Horizontal Rule */
    .lesson-content-display hr {
        border: none;
        border-top: 2px solid #4b5563;
        margin: 2rem 0;
        border-radius: 1px;
    }
    
    /* Tables */
    .lesson-content-display table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
        background-color: #374151;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 1px solid #4b5563;
    }
    
    .lesson-content-display th,
    .lesson-content-display td {
        padding: 0.75rem 1rem;
        text-align: left;
        border-bottom: 1px solid #4b5563;
        color: #f3f4f6;
    }
    
    .lesson-content-display th {
        background-color: #4b5563;
        font-weight: 600;
        color: #ffffff;
    }
    
    .lesson-content-display tr:hover {
        background-color: rgba(75, 85, 99, 0.5);
    }
    
    /* Div styling */
    .lesson-content-display div {
        margin: 0.5rem 0;
        color: #f3f4f6;
    }
    
    .lesson-content-display div.highlight {
        background-color: rgba(251, 191, 36, 0.2);
        padding: 0.75rem;
        border-radius: 0.375rem;
        border-left: 4px solid #fbbf24;
        margin: 1rem 0;
    }
    
    /* Span styling */
    .lesson-content-display span {
        color: inherit;
    }
    
    .lesson-content-display span.highlight {
        background-color: rgba(251, 191, 36, 0.3);
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
    }
    
    /* Special content blocks */
    .lesson-content-display .note,
    .lesson-content-display .info {
        background-color: rgba(59, 130, 246, 0.1);
        border-left: 4px solid #3b82f6;
        padding: 1rem;
        margin: 1.5rem 0;
        border-radius: 0.5rem;
    }
    
    .lesson-content-display .warning {
        background-color: rgba(251, 191, 36, 0.1);
        border-left: 4px solid #fbbf24;
        padding: 1rem;
        margin: 1.5rem 0;
        border-radius: 0.5rem;
    }
    
    .lesson-content-display .danger,
    .lesson-content-display .error {
        background-color: rgba(239, 68, 68, 0.1);
        border-left: 4px solid #ef4444;
        padding: 1rem;
        margin: 1.5rem 0;
        border-radius: 0.5rem;
    }
    
    .lesson-content-display .success {
        background-color: rgba(34, 197, 94, 0.1);
        border-left: 4px solid #22c55e;
        padding: 1rem;
        margin: 1.5rem 0;
        border-radius: 0.5rem;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .lesson-content-display {
            font-size: 0.95rem;
        }
        
        .lesson-content-display h1 {
            font-size: 1.625rem;
        }
        
        .lesson-content-display h2 {
            font-size: 1.375rem;
        }
        
        .lesson-content-display h3 {
            font-size: 1.125rem;
        }
        
        .lesson-content-display pre {
            padding: 1rem;
            font-size: 0.8rem;
        }
        
        .lesson-content-display table {
            font-size: 0.875rem;
        }
        
        .lesson-content-display th,
        .lesson-content-display td {
            padding: 0.5rem 0.75rem;
        }
    }
    
    /* Print styles */
    @media print {
        .lesson-content-display {
            color: #000;
        }
        
        .lesson-content-display h1,
        .lesson-content-display h2,
        .lesson-content-display h3,
        .lesson-content-display h4,
        .lesson-content-display h5,
        .lesson-content-display h6 {
            color: #000;
            page-break-after: avoid;
        }
        
        .lesson-content-display blockquote {
            border-left-color: #000;
            background: #f5f5f5;
        }
        
        .lesson-content-display code {
            background: #f5f5f5;
            color: #000;
        }
        
        .lesson-content-display pre {
            background: #f5f5f5;
            color: #000;
            border: 1px solid #ccc;
        }
    }
    
    /* Legacy wrapper styles for backwards compatibility */
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