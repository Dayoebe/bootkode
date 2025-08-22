<div>
    <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-download text-orange-500"></i>
                    Offline Learning Center
                </h2>
                <p class="text-gray-400 mt-1">
                    Access your learning materials without internet connection
                </p>
            </div>
            
            <div class="bg-gray-700 px-4 py-3 rounded-lg border border-gray-600">
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="text-gray-300">Storage Used:</span>
                    <span class="font-medium">{{ number_format($storageUsage, 1) }} MB</span>
                </div>
                <div class="w-full bg-gray-600 rounded-full h-2.5">
                    <div 
                        class="bg-orange-500 h-2.5 rounded-full" 
                        style="width: {{ min(100, ($storageUsage / config('app.offline_storage_limit_mb', 500)) * 100) }}%"
                    ></div>
                </div>
                <div class="flex items-center justify-between text-xs mt-1 text-gray-400">
                    <span>{{ config('app.offline_storage_limit_mb', 500) }} MB limit</span>
                    <span>{{ number_format($availableSpace, 1) }} MB available</span>
                </div>
            </div>
        </div>

        <!-- Tabs -->
<!-- Tabs -->
<div class="border-b border-gray-700 mb-6">
    <nav class="flex space-x-6">
        <button
            type="button"
            wire:click="$set('activeTab', 'downloaded')"
            class="{{ $activeTab === 'downloaded' ? 'border-orange-500 text-orange-400' : 'border-transparent text-gray-400 hover:text-gray-300' }} py-4 px-1 border-b-2 font-medium text-sm"
        >
            <i class="fas fa-check-circle mr-2"></i> Downloaded
        </button>
        <button
            type="button"
            wire:click="$set('activeTab', 'available')"
            class="{{ $activeTab === 'available' ? 'border-orange-500 text-orange-400' : 'border-transparent text-gray-400 hover:text-gray-300' }} py-4 px-1 border-b-2 font-medium text-sm"
        >
            <i class="fas fa-cloud-download-alt mr-2"></i> Available
        </button>
        <button
            type="button"
            wire:click="$set('activeTab', 'notes')"
            class="{{ $activeTab === 'notes' ? 'border-orange-500 text-orange-400' : 'border-transparent text-gray-400 hover:text-gray-300' }} py-4 px-1 border-b-2 font-medium text-sm"
        >
            <i class="fas fa-sticky-note mr-2"></i> My Notes
        </button>
    </nav>
</div>

        <!-- Search and Filter -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-grow">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search {{ $activeTab === 'notes' ? 'notes' : 'courses' }}..." 
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                >
                <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>
            </div>
            
            @if($activeTab !== 'notes')
                <select 
                    wire:model.live="selectedTypes"
                    multiple
                    class="bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 min-w-[180px]"
                >
                    <option value="lesson">Lessons</option>
                    <option value="pdf">PDFs</option>
                    <option value="audio">Audio</option>
                    <option value="video">Videos</option>
                    <option value="quiz">Quizzes</option>
                </select>
            @endif
            
            @if($activeTab === 'notes')
                <select 
                    wire:model.live="selectedCourse"
                    class="bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500 min-w-[180px]"
                >
                    <option value="">All Courses</option>
                    @foreach($enrolledCourses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            @endif
        </div>
    </div>

    <!-- Downloaded Content Tab -->
    @if($activeTab === 'downloaded')
        @if($downloadedContent->isEmpty())
            <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-8 text-center">
                <i class="fas fa-download text-5xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">No Downloaded Content</h3>
                <p class="text-gray-400 mb-4">
                    You haven't downloaded any courses for offline learning yet. 
                    Browse your available courses below to get started.
                </p>
                <button 
                    wire:click="activeTab = 'available'"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 rounded-lg text-white transition-colors"
                >
                    <i class="fas fa-cloud-download-alt mr-2"></i> View Available Content
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($downloadedContent as $content)
                    <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 overflow-hidden hover:border-orange-500 transition-colors">
                        <div class="relative">
                            <img 
                                src="{{ $content->course->thumbnail ?? asset('images/default-course.png') }}" 
                                alt="{{ $content->course->title }}" 
                                class="w-full h-40 object-cover"
                            >
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-gray-900 to-transparent p-4">
                                <h3 class="text-lg font-bold text-white">{{ $content->course->title }}</h3>
                            </div>
                            <div class="absolute top-2 right-2 bg-gray-900 bg-opacity-80 rounded-full p-2">
                                <button 
                                    wire:click="deleteDownloadedContent('{{ $content->id }}')"
                                    class="text-gray-300 hover:text-orange-500 transition-colors"
                                    title="Remove from offline storage"
                                >
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex justify-between items-center text-sm text-gray-400 mb-3">
                                <span>
                                    <i class="fas fa-database mr-1"></i>
                                    {{ $content->size_mb }} MB
                                </span>
                                <span>
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ $content->downloaded_at->format('M d, Y') }}
                                </span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-300">
                                    @php
                                        $types = json_decode($content->content_types, true) ?? ['lesson'];
                                    @endphp
                                    @foreach($types as $type)
                                        <span class="px-2 py-1 bg-gray-700 rounded-full text-xs mr-1">
                                            {{ ucfirst($type) }}
                                        </span>
                                    @endforeach
                                </span>
                                
                                <a 
                                    href="{{ route('student.enrolled-courses', ['course' => $content->course_id, 'offline' => true]) }}"
                                    class="text-orange-500 hover:text-orange-400 text-sm font-medium flex items-center"
                                >
                                    Open <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{ $downloadedContent->links() }}
        @endif
    @endif

    <!-- Available Content Tab -->
    @if($activeTab === 'available')
        @if($availableCourses->isEmpty())
            <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-8 text-center">
                <i class="fas fa-check-circle text-5xl text-gray-600 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">All Available Content Downloaded</h3>
                <p class="text-gray-400 mb-4">
                    You've already downloaded all courses that are available for offline access.
                    Check back later as new content becomes available.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($availableCourses as $course)
                    <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 overflow-hidden hover:border-orange-500 transition-colors">
                        <div class="relative">
                            <img 
                                src="{{ $course->thumbnail ?? asset('images/default-course.png') }}" 
                                alt="{{ $course->title }}" 
                                class="w-full h-40 object-cover"
                            >
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-gray-900 to-transparent p-4">
                                <h3 class="text-lg font-bold text-white">{{ $course->title }}</h3>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex justify-between items-center text-sm text-gray-400 mb-3">
                                <span>
                                    <i class="fas fa-database mr-1"></i>
                                    {{ $course->offline_size_mb }} MB required
                                </span>
                                <span>
                                    <i class="fas fa-book-open mr-1"></i>
                                    {{ $course->modules_count }} modules
                                </span>
                            </div>
                            
                            <div class="mb-4">
                                <div class="flex justify-between text-xs text-gray-400 mb-1">
                                    <span>Available content types:</span>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($course->offline_content_types as $type)
                                        <span class="px-2 py-1 bg-gray-700 rounded-full text-xs">
                                            {{ ucfirst($type) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            
                            @if($isDownloading && $selectedCourse == $course->id)
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-gray-400 mb-1">
                                        <span>Downloading...</span>
                                        <span>{{ $downloadProgress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-700 rounded-full h-2">
                                        <div 
                                            class="bg-orange-500 h-2 rounded-full" 
                                            style="width: {{ $downloadProgress }}%"
                                        ></div>
                                    </div>
                                </div>
                            @else
                                <button 
                                    wire:click="downloadCourseContent('{{ $course->id }}')"
                                    @disabled($availableSpace < $course->offline_size_mb)
                                    class="w-full py-2 px-4 bg-orange-600 hover:bg-orange-700 rounded-lg text-white transition-colors flex items-center justify-center gap-2"
                                >
                                    <i class="fas fa-download"></i>
                                    Download for Offline
                                </button>

                                <a href="{{ URL::signedRoute('offline.content', ['path' => $filePath]) }}">
    Download File
</a>
                                
                                @if($availableSpace < $course->offline_size_mb)
                                    <p class="text-red-400 text-xs mt-2 text-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Not enough space. Free up {{ $course->offline_size_mb - $availableSpace }}MB.
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{ $availableCourses->links() }}
        @endif
    @endif

    <!-- Notes Tab -->
    @if($activeTab === 'notes')
        <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-6 mb-6">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Note Form -->
                <div class="md:w-1/3">
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-orange-500"></i>
                        Add New Note
                    </h3>
                    
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm mb-2" for="course-select">
                            For Course (optional)
                        </label>
                        <select 
                            wire:model="selectedCourse"
                            id="course-select"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                            <option value="">General Note</option>
                            @foreach($enrolledCourses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-400 text-sm mb-2" for="new-note">
                            Your Note
                        </label>
                        <textarea
                            wire:model="newNote"
                            id="new-note"
                            rows="5"
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 py-2 text-white focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Write your notes here..."
                        ></textarea>
                    </div>
                    
                    <button 
                        wire:click="saveNote"
                        class="w-full py-2 px-4 bg-orange-600 hover:bg-orange-700 rounded-lg text-white transition-colors"
                    >
                        <i class="fas fa-save mr-2"></i> Save Note
                    </button>
                </div>
                
                <!-- Notes List -->
                <div class="md:w-2/3">
                    <h3 class="text-lg font-bold text-white mb-3 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-orange-500"></i>
                        Your Notes
                    </h3>
                    
                    @if($offlineNotes->count() === 0)
                        <div class="bg-gray-700 rounded-lg border border-gray-600 p-8 text-center">
                            <i class="fas fa-sticky-note text-3xl text-gray-500 mb-3"></i>
                            <p class="text-gray-400">
                                No notes yet. Add your first note to reference while offline.
                            </p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($offlineNotes as $note)
                                <div class="bg-gray-700 rounded-lg border border-gray-600 p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        @if($note->course)
                                            <span class="px-2 py-1 bg-gray-600 rounded-full text-xs text-white">
                                                {{ $note->course->title }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-600 rounded-full text-xs text-white">
                                                General Note
                                            </span>
                                        @endif
                                        
                                        <button 
                                            wire:click="deleteNote('{{ $note->id }}')"
                                            class="text-gray-400 hover:text-orange-500 transition-colors"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="prose prose-invert max-w-none">
                                        {!! Str::markdown($note->content) !!}
                                    </div>
                                    
                                    <div class="text-xs text-gray-500 mt-2">
                                        {{ $note->created_at->format('M j, Y \a\t g:i a') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            {{ $offlineNotes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('start-download', (event) => {
        let progress = 0;
        const interval = setInterval(() => {
            progress += 10;
            @this.updateDownloadProgress(progress);
            
            if (progress >= 100) {
                clearInterval(interval);
            }
        }, 300);
    });
});
</script>

</div>