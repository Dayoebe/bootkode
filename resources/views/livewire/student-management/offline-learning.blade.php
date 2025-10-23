<div>
    <div class="bg-themed-secondary rounded-xl shadow-lg border border-themed-primary p-6 mb-6 transition-colors duration-300">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-themed-primary flex items-center gap-2">
                    <i class="fas fa-download text-accent-themed-primary"></i>
                    Offline Learning Center
                </h2>
                <p class="text-themed-secondary mt-1">
                    Access your learning materials without internet connection
                </p>
            </div>

            <div class="bg-themed-tertiary px-4 py-3 rounded-lg border border-themed-secondary">
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="text-themed-secondary">Storage Used:</span>
                    <span class="font-medium text-themed-primary">{{ number_format($storageUsage, 1) }} MB</span>
                </div>
                <div class="w-full bg-themed-secondary rounded-full h-2.5">
                    <div class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary h-2.5 rounded-full"
                        style="width: {{ min(100, ($storageUsage / config('app.offline_storage_limit_mb', 500)) * 100) }}%">
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs mt-1 text-themed-tertiary">
                    <span>{{ config('app.offline_storage_limit_mb', 500) }} MB limit</span>
                    <span>{{ number_format($availableSpace, 1) }} MB available</span>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-themed-secondary mb-6">
            <nav class="flex space-x-6">
                <button type="button" wire:click="$set('activeTab', 'downloaded')"
                    class="{{ $activeTab === 'downloaded' ? 'border-accent-themed-primary text-accent-themed-primary' : 'border-transparent text-themed-tertiary hover:text-themed-secondary' }} py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-300">
                    <i class="fas fa-check-circle mr-2"></i> Downloaded
                </button>
                <button type="button" wire:click="$set('activeTab', 'available')"
                    class="{{ $activeTab === 'available' ? 'border-accent-themed-primary text-accent-themed-primary' : 'border-transparent text-themed-tertiary hover:text-themed-secondary' }} py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-300">
                    <i class="fas fa-cloud-download-alt mr-2"></i> Available
                </button>
                <button type="button" wire:click="$set('activeTab', 'notes')"
                    class="{{ $activeTab === 'notes' ? 'border-accent-themed-primary text-accent-themed-primary' : 'border-transparent text-themed-tertiary hover:text-themed-secondary' }} py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-300">
                    <i class="fas fa-sticky-note mr-2"></i> My Notes
                </button>
            </nav>
        </div>

        <!-- Search and Filter -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-grow">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search {{ $activeTab === 'notes' ? 'notes' : 'courses' }}..."
                    class="w-full bg-themed-tertiary border border-themed-secondary rounded-lg px-4 py-2 text-themed-primary placeholder-themed-tertiary focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300">
                <i class="fas fa-search absolute right-3 top-3 text-themed-tertiary"></i>
            </div>

            @if($activeTab !== 'notes')
                <select wire:model.live="selectedTypes" multiple
                    class="bg-themed-tertiary border border-themed-secondary rounded-lg px-3 py-2 text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300 min-w-[180px]">
                    <option value="lesson">Lessons</option>
                    <option value="pdf">PDFs</option>
                    <option value="audio">Audio</option>
                    <option value="video">Videos</option>
                    <option value="quiz">Quizzes</option>
                </select>
            @endif

            @if($activeTab === 'notes')
                <select wire:model.live="selectedCourse"
                    class="bg-themed-tertiary border border-themed-secondary rounded-lg px-3 py-2 text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300 min-w-[180px]">
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
            <div class="bg-themed-secondary rounded-xl shadow-lg border border-themed-primary p-8 text-center transition-colors duration-300">
                <i class="fas fa-download text-5xl text-themed-tertiary mb-4"></i>
                <h3 class="text-xl font-bold text-themed-primary mb-2">No Downloaded Content</h3>
                <p class="text-themed-secondary mb-4">
                    You haven't downloaded any courses for offline learning yet.
                    Browse your available courses below to get started.
                </p>
                <button wire:click="$set('activeTab', 'available')"
                    class="inline-flex items-center px-4 py-2 bg-accent-themed-primary hover:bg-accent-themed-secondary rounded-lg text-white transition-colors duration-300">
                    <i class="fas fa-cloud-download-alt mr-2"></i> View Available Content
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($downloadedContent as $content)
                    <div class="bg-themed-secondary rounded-xl shadow-lg border border-themed-primary overflow-hidden hover:border-accent-themed-primary transition-colors duration-300">
                        <div class="relative">
                            <img src="{{ asset('storage/' . ($content->course->thumbnail ?? 'images/default-course.png')) }}"
                                alt="{{ $content->course->title }}" class="w-full h-40 object-cover">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-themed-primary/80 to-transparent p-4">
                                <h3 class="text-lg font-bold text-white">{{ $content->course->title }}</h3>
                            </div>
                            <div class="absolute top-2 right-2 bg-themed-primary/80 rounded-full p-2">
                                <button wire:click="deleteDownloadedContent('{{ $content->id }}')"
                                    class="text-themed-secondary hover:text-accent-themed-primary transition-colors duration-300"
                                    title="Remove from offline storage">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="flex justify-between items-center text-sm text-themed-tertiary mb-3">
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
                                <span class="text-sm text-themed-secondary">
                                    @php
                                        $types = json_decode($content->content_types, true) ?? ['lesson'];
                                    @endphp
                                    @foreach($types as $type)
                                        <span class="px-2 py-1 bg-themed-tertiary rounded-full text-xs mr-1 inline-block">
                                            {{ ucfirst($type) }}
                                        </span>
                                    @endforeach
                                </span>

                                <a href="{{ route('student.enrolled-courses', ['course' => $content->course_id, 'offline' => true]) }}"
                                    class="text-accent-themed-primary hover:text-accent-themed-secondary text-sm font-medium flex items-center transition-colors duration-300">
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
            <div class="bg-themed-secondary rounded-xl shadow-lg border border-themed-primary p-8 text-center transition-colors duration-300">
                <i class="fas fa-check-circle text-5xl text-themed-tertiary mb-4"></i>
                <h3 class="text-xl font-bold text-themed-primary mb-2">All Available Content Downloaded</h3>
                <p class="text-themed-secondary mb-4">
                    You've already downloaded all courses that are available for offline access.
                    Check back later as new content becomes available.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($availableCourses as $course)
                    <div class="bg-themed-secondary rounded-xl shadow-lg border border-themed-primary overflow-hidden hover:border-accent-themed-primary transition-colors duration-300">
                        <div class="relative">
                            <img src="{{ asset('storage/' . ($course->thumbnail ?? 'images/default-course.png')) }}" alt="{{ $course->title }}"
                                class="w-full h-40 object-cover">
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-themed-primary/80 to-transparent p-4">
                                <h3 class="text-lg font-bold text-white">{{ $course->title }}</h3>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="flex justify-between items-center text-sm text-themed-tertiary mb-3">
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
                                <div class="flex justify-between text-xs text-themed-tertiary mb-1">
                                    <span>Available content types:</span>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($course->offline_content_types as $type)
                                        <span class="px-2 py-1 bg-themed-tertiary rounded-full text-xs">
                                            {{ ucfirst($type) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            @if($isDownloading && $selectedCourse == $course->id)
                                <div class="mb-3">
                                    <div class="flex justify-between text-xs text-themed-tertiary mb-1">
                                        <span>Downloading...</span>
                                        <span>{{ $downloadProgress }}%</span>
                                    </div>
                                    <div class="w-full bg-themed-tertiary rounded-full h-2">
                                        <div class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary h-2 rounded-full" style="width: {{ $downloadProgress }}%"></div>
                                    </div>
                                </div>
                            @else
                                <button wire:click="downloadCourseContent('{{ $course->id }}')" @disabled($availableSpace < $course->offline_size_mb)
                                    class="w-full py-2 px-4 bg-accent-themed-primary hover:bg-accent-themed-secondary disabled:opacity-50 disabled:cursor-not-allowed rounded-lg text-white transition-colors duration-300 flex items-center justify-center gap-2">
                                    <i class="fas fa-download"></i>
                                    Download for Offline
                                </button>

                                @if($availableSpace < $course->offline_size_mb)
                                    <p class="text-accent-themed-primary text-xs mt-2 text-center">
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
        <div class="bg-themed-secondary rounded-xl shadow-lg border border-themed-primary p-6 mb-6 transition-colors duration-300">
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Note Form -->
                <div class="md:w-1/3">
                    <h3 class="text-lg font-bold text-themed-primary mb-3 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-accent-themed-primary"></i>
                        Add New Note
                    </h3>

                    <div class="mb-4">
                        <label class="block text-themed-secondary text-sm mb-2" for="course-select">
                            For Course (optional)
                        </label>
                        <select wire:model="selectedCourse" id="course-select"
                            class="w-full bg-themed-tertiary border border-themed-secondary rounded-lg px-3 py-2 text-themed-primary focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300">
                            <option value="">General Note</option>
                            @foreach($enrolledCourses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-themed-secondary text-sm mb-2" for="new-note">
                            Your Note
                        </label>
                        <textarea wire:model="newNote" id="new-note" rows="5"
                            class="w-full bg-themed-tertiary border border-themed-secondary rounded-lg px-3 py-2 text-themed-primary placeholder-themed-tertiary focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary transition-colors duration-300"
                            placeholder="Write your notes here..."></textarea>
                    </div>

                    <button wire:click="saveNote"
                        class="w-full py-2 px-4 bg-accent-themed-primary hover:bg-accent-themed-secondary rounded-lg text-white transition-colors duration-300">
                        <i class="fas fa-save mr-2"></i> Save Note
                    </button>
                </div>

                <!-- Notes List -->
                <div class="md:w-2/3">
                    <h3 class="text-lg font-bold text-themed-primary mb-3 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-accent-themed-primary"></i>
                        Your Notes
                    </h3>

                    @if($offlineNotes->count() === 0)
                        <div class="bg-themed-tertiary rounded-lg border border-themed-secondary p-8 text-center">
                            <i class="fas fa-sticky-note text-3xl text-themed-tertiary mb-3"></i>
                            <p class="text-themed-secondary">
                                No notes yet. Add your first note to reference while offline.
                            </p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($offlineNotes as $note)
                                <div class="bg-themed-tertiary rounded-lg border border-themed-secondary p-4 transition-colors duration-300">
                                    <div class="flex justify-between items-start mb-2">
                                        @if($note->course)
                                            <span class="px-2 py-1 bg-themed-secondary rounded-full text-xs text-themed-primary">
                                                {{ $note->course->title }}
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-themed-secondary rounded-full text-xs text-themed-primary">
                                                General Note
                                            </span>
                                        @endif

                                        <button wire:click="deleteNote('{{ $note->id }}')"
                                            class="text-themed-tertiary hover:text-accent-themed-primary transition-colors duration-300">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>

                                    <div class="prose prose-sm max-w-none text-themed-primary">
                                        {!! Str::markdown($note->content) !!}
                                    </div>

                                    <div class="text-xs text-themed-tertiary mt-2">
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

    <style>
        /* Theme transition support */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        /* Prose styling for notes with theme support */
        .prose {
            color: rgb(var(--text-primary));
        }

        .prose strong {
            color: rgb(var(--text-primary));
        }

        .prose em {
            color: rgb(var(--text-secondary));
        }

        /* Smooth theme support */
        input[type="text"],
        textarea,
        select {
            background-color: rgb(var(--bg-tertiary));
            border-color: rgb(var(--border-secondary));
            color: rgb(var(--text-primary));
        }

        input[type="text"]::placeholder,
        textarea::placeholder {
            color: rgb(var(--text-tertiary));
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            border-color: rgb(var(--accent-primary));
            ring-color: rgb(var(--accent-primary));
        }
    </style>
</div>