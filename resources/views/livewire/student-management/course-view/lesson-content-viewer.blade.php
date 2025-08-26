<div>
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

                @if ($lesson->description)
                    <p class="text-gray-300 mt-2">{{ $lesson->description }}</p>
                @endif

                <div class="flex items-center gap-4 mt-3 text-sm text-gray-400">
                    @if ($lesson->formatted_duration !== 'N/A')
                        <span class="flex items-center">
                            <i class="fas fa-clock mr-1"></i>
                            {{ $lesson->formatted_duration }}
                        </span>
                    @endif

                    @if ($lesson->difficulty_level)
                        <span class="capitalize bg-gray-700 px-2 py-1 rounded text-xs">
                            {{ $lesson->difficulty_level }}
                        </span>
                    @endif

                    <!-- Assessment Status Indicator in Header -->
                    @if ($hasAssessments)
                        @if ($allAssessmentsPassed)
                            <span class="flex items-center bg-green-600 px-2 py-1 rounded text-xs text-white">
                                <i class="fas fa-check-circle mr-1"></i>
                                Assessments Passed
                            </span>
                        @else
                            <span
                                class="flex items-center bg-red-600 px-2 py-1 rounded text-xs text-white animate-pulse">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Assessment Required
                            </span>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Completion Toggle -->
            <div class="flex items-center gap-3">
                @if ($isCompleted)
                    <button wire:click="markAsIncomplete"
                        class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg text-sm transition-colors flex items-center">
                        <i class="fas fa-undo mr-2"></i> Mark Incomplete
                    </button>
                @else
                    <button wire:click="markAsCompleted"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition-colors flex items-center
                        {{ $hasAssessments && !$allAssessmentsPassed ? 'opacity-50 cursor-not-allowed' : '' }}"
                        @if ($hasAssessments && !$allAssessmentsPassed) disabled @endif>
                        <i class="fas fa-check mr-2"></i> Mark Complete
                    </button>
                @endif
            </div>
        </div>

        <!-- Lesson Content -->
        <div class="lesson-content-wrapper">
            <!-- Main Text Content with Trix Styling -->
            @if ($lesson->content)
                <div class="lesson-content-display mb-6">
                    {!! $lesson->content !!}
                </div>
            @endif

            <!-- ASSESSMENTS SECTION - PROMINENTLY DISPLAYED -->
            @if ($hasAssessments)
                <div class="mb-6">
                    <!-- Assessment Header with Visual Indicator -->
                    <div class="flex justify-between items-center cursor-pointer group bg-purple-900/30 border-2 border-purple-500 rounded-lg p-4 hover:bg-purple-900/50 transition-all duration-200"
                        onclick="toggleSection('assessments')">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-clipboard-check text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-white">
                                    Assessment Required
                                    @if (!$allAssessmentsPassed)
                                        <span
                                            class="inline-flex items-center ml-2 px-2 py-1 rounded-full text-xs font-medium bg-red-600 text-white animate-pulse">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Required
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center ml-2 px-2 py-1 rounded-full text-xs font-medium bg-green-600 text-white">
                                            <i class="fas fa-check mr-1"></i>
                                            Completed
                                        </span>
                                    @endif
                                </h3>
                                <p class="text-purple-200 text-sm mt-1">
                                    @php
                                        $assessmentCount = \App\Models\Assessment::where(
                                            'lesson_id',
                                            $lesson->id,
                                        )->count();
                                    @endphp
                                    {{ $assessmentCount }} assessment{{ $assessmentCount > 1 ? 's' : '' }} must be
                                    completed to proceed
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            @if (!$allAssessmentsPassed)
                                <div
                                    class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center mr-3 animate-pulse">
                                    <i class="fas fa-exclamation text-white text-sm"></i>
                                </div>
                            @else
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                            @endif
                            <i class="fas fa-chevron-down text-purple-300 transform transition-transform group-hover:text-white"
                                id="assessments-chevron"></i>
                        </div>
                    </div>

                    <!-- Assessment Content - Show by default if not all passed -->
                    <div class="mt-3 {{ $allAssessmentsPassed ? 'hidden' : '' }}" id="assessments-content">
                        <div class="bg-purple-900/20 border border-purple-500 rounded-lg p-1">
                            <livewire:student-management.course-view.student-assessment-taker :lesson="$lesson"
                                wire:key="assessment-{{ $lesson->id }}" wire:poll.10s="pollAssessmentStatus" />
                        </div>
                    </div>
                </div>
            @endif

            <!-- Documents -->
            @if ($lesson->hasDocuments() && count($lesson->getDocumentsArray()) > 0)
                <div class="mb-6">
                    <div class="flex justify-between items-center cursor-pointer group"
                        onclick="toggleSection('documents')">
                        <h3 class="text-lg font-semibold text-white">Course Materials</h3>
                        <i class="fas fa-chevron-down text-gray-400 transform transition-transform group-hover:text-white"
                            id="documents-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="documents-content">
                        <div class="grid gap-3">
                            @foreach ($lesson->getDocumentsArray() as $index => $document)
                                <div class="bg-gray-700 rounded-lg p-4 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-indigo-600 rounded flex items-center justify-center mr-3">
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
                                    <div class="flex gap-2">
                                        <button
                                            onclick="openDocumentModal('{{ asset('storage/' . $document['path']) }}', '{{ $document['name'] }}')"
                                            class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm transition-colors">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </button>
                                        <a href="{{ asset('storage/' . $document['path']) }}" target="_blank"
                                            class="px-3 py-1 bg-gray-600 hover:bg-gray-700 text-white rounded text-sm transition-colors">
                                            <i class="fas fa-download mr-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Video Content -->
            @if ($lesson->video_url)
                <div class="mb-6">
                    <div class="flex justify-between items-center cursor-pointer group"
                        onclick="toggleSection('video-content')">
                        <h3 class="text-lg font-semibold text-white">Video Lesson</h3>
                        <i class="fas fa-chevron-down text-gray-400 transform transition-transform group-hover:text-white"
                            id="video-content-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="video-content-content">
                        <div class="bg-black rounded-lg overflow-hidden">
                            @if (str_contains($lesson->video_url, 'youtube.com') || str_contains($lesson->video_url, 'youtu.be'))
                                @php
                                    $videoId = '';
                                    if (str_contains($lesson->video_url, 'youtube.com/watch?v=')) {
                                        parse_str(parse_url($lesson->video_url, PHP_URL_QUERY), $query);
                                        $videoId = $query['v'] ?? '';
                                    } elseif (str_contains($lesson->video_url, 'youtu.be/')) {
                                        $videoId = substr(parse_url($lesson->video_url, PHP_URL_PATH), 1);
                                    }
                                @endphp

                                @if ($videoId)
                                    <iframe class="w-full aspect-video"
                                        src="https://www.youtube.com/embed/{{ $videoId }}" title="Lesson Video"
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
                </div>
            @endif

            <!-- Uploaded Videos -->
            @if ($lesson->hasVideo() && count($lesson->getVideosArray()) > 0)
                <div class="mb-6">
                    <div class="flex justify-between items-center cursor-pointer group"
                        onclick="toggleSection('uploaded-videos')">
                        <h3 class="text-lg font-semibold text-white">Course Videos</h3>
                        <i class="fas fa-chevron-down text-gray-400 transform transition-transform group-hover:text-white"
                            id="uploaded-videos-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="uploaded-videos-content">
                        <div class="grid gap-4">
                            @foreach ($lesson->getVideosArray() as $video)
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
                </div>
            @endif

            <!-- Audio Content -->
            @if ($lesson->hasAudio() && count($lesson->getAudiosArray()) > 0)
                <div class="mb-6">
                    <div class="flex justify-between items-center cursor-pointer group"
                        onclick="toggleSection('audio-content')">
                        <h3 class="text-lg font-semibold text-white">Audio Content</h3>
                        <i class="fas fa-chevron-down text-gray-400 transform transition-transform group-hover:text-white"
                            id="audio-content-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="audio-content-content">
                        <div class="space-y-3">
                            @foreach ($lesson->getAudiosArray() as $index => $audio)
                                <div class="bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-white font-medium">{{ $audio['name'] }}</span>
                                        <span class="text-xs text-gray-400">
                                            {{ number_format($audio['size'] / 1024 / 1024, 1) }}MB
                                        </span>
                                    </div>
                                    <div class="mini-player flex items-center gap-4 p-3 bg-gray-800 rounded-lg">
                                        <button
                                            class="play-pause-btn w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white hover:bg-indigo-700 transition-colors"
                                            onclick="togglePlayPause({{ $index }})">
                                            <i class="fas fa-play" id="play-icon-{{ $index }}"></i>
                                            <i class="fas fa-pause hidden" id="pause-icon-{{ $index }}"></i>
                                        </button>
                                        <div class="flex-1">
                                            <div class="w-full bg-gray-600 rounded-full h-2 mb-1">
                                                <div class="bg-indigo-500 h-2 rounded-full" style="width: 0%"
                                                    id="progress-bar-{{ $index }}"></div>
                                            </div>
                                            <div class="flex justify-between text-xs text-gray-400">
                                                <span id="current-time-{{ $index }}">0:00</span>
                                                <span id="duration-{{ $index }}">0:00</span>
                                            </div>
                                        </div>
                                        <audio id="audio-{{ $index }}"
                                            onloadedmetadata="initAudioPlayer({{ $index }})"
                                            ontimeupdate="updateProgress({{ $index }})">
                                            <source src="{{ asset('storage/' . $audio['path']) }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Images -->
            @if ($lesson->hasImage())
                <div class="mb-6">
                    <div class="flex justify-between items-center cursor-pointer group"
                        onclick="toggleSection('images-content')">
                        <h3 class="text-lg font-semibold text-white">Lesson Images</h3>
                        <i class="fas fa-chevron-down text-gray-400 transform transition-transform group-hover:text-white"
                            id="images-content-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="images-content-content">
                        @if ($lesson->image_path)
                            <img src="{{ asset('storage/' . $lesson->image_path) }}" alt="Lesson Image"
                                class="w-full rounded-lg mb-4">
                        @endif

                        @if (count($lesson->getImagesArray()) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($lesson->getImagesArray() as $image)
                                    <div class="bg-gray-700 rounded-lg overflow-hidden">
                                        <img src="{{ asset('storage/' . $image['path']) }}" alt="Lesson Image"
                                            class="w-full h-48 object-cover cursor-pointer"
                                            onclick="openImageModal('{{ asset('storage/' . $image['path']) }}')">
                                        <div class="p-3">
                                            <p class="text-sm text-gray-300">{{ $image['name'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- External Links -->
            @if ($lesson->hasExternalLinks() && count($lesson->getExternalLinksArray()) > 0)
                <div class="mb-6">
                    <div class="flex justify-between items-center cursor-pointer group"
                        onclick="toggleSection('external-links')">
                        <h3 class="text-lg font-semibold text-white">Additional Resources</h3>
                        <i class="fas fa-chevron-down text-gray-400 transform transition-transform group-hover:text-white"
                            id="external-links-chevron"></i>
                    </div>
                    <div class="mt-3 hidden" id="external-links-content">
                        <div class="space-y-2">
                            @foreach ($lesson->getExternalLinksArray() as $link)
                                <a href="{{ $link['url'] }}" target="_blank"
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
                </div>
            @endif
        </div>

        <!-- Document Modal -->
        <div id="document-modal"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
            <div class="bg-gray-800 rounded-lg w-11/12 h-5/6 max-w-6xl flex flex-col">
                <div class="flex justify-between items-center p-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-white" id="document-modal-title"></h3>
                    <button onclick="closeDocumentModal()" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex-1 p-4">
                    <iframe id="document-iframe" class="w-full h-full bg-white rounded" frameborder="0"></iframe>
                </div>
            </div>
        </div>

        <!-- Image Modal -->
        <div id="image-modal"
            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
            <div class="bg-gray-800 rounded-lg w-11/12 h-5/6 max-w-6xl flex flex-col">
                <div class="flex justify-between items-center p-4 border-b border-gray-700">
                    <button onclick="closeImageModal()" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="flex-1 p-4 flex items-center justify-center">
                    <img id="modal-image" class="max-w-full max-h-full object-contain" src=""
                        alt="">
                </div>
            </div>
        </div>

        <!-- Lesson Navigation - Updated with assessment blocking -->
        <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-700">
            @if ($this->getPreviousLesson())
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

            @if ($this->getNextLesson())
                @php $nextLesson = $this->getNextLesson(); @endphp
                @if ($this->canProceedToNext() && $this->isNextLessonUnlocked())
                    <button wire:click="goToNextLesson"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg flex items-center transition-colors">
                        <span class="hidden sm:inline">Next:</span>
                        <span class="mr-1 truncate max-w-32">
                            {{ is_object($nextLesson) ? $nextLesson->title : $nextLesson['title'] }}
                        </span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                @elseif (!$this->canProceedToNext())
                    <div
                        class="px-4 py-2 bg-red-600 text-white rounded-lg flex items-center cursor-not-allowed opacity-75">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        <span class="text-sm">Complete assessments to continue</span>
                    </div>
                @else
                    <div class="px-4 py-2 bg-gray-600 text-gray-400 rounded-lg flex items-center cursor-not-allowed">
                        <i class="fas fa-lock mr-2"></i>
                        <span class="text-sm">Complete section to continue</span>
                    </div>
                @endif
            @else
                <button wire:click="completeCourse"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg flex items-center transition-colors
                    {{ !$this->canProceedToNext() ? 'opacity-50 cursor-not-allowed' : '' }}"
                    @if (!$this->canProceedToNext()) disabled @endif>
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
    </div>

    <!-- Efficient Polling Script -->
    <script>
        // Efficient polling for assessment status
        let assessmentPollingActive = @json($shouldPoll);
        let lastPollTime = 0;
        const POLL_INTERVAL = 10000; // 10 seconds

        // Only poll if assessments exist and are not all passed
        if (assessmentPollingActive) {
            setInterval(() => {
                const now = Date.now();
                if (now - lastPollTime >= POLL_INTERVAL) {
                    @this.call('pollAssessmentStatus');
                    lastPollTime = now;
                }
            }, POLL_INTERVAL);
        }

        // Toggle section visibility
        function toggleSection(sectionId) {
            const content = document.getElementById(`${sectionId}-content`);
            const chevron = document.getElementById(`${sectionId}-chevron`);

            content.classList.toggle('hidden');
            chevron.classList.toggle('fa-chevron-down');
            chevron.classList.toggle('fa-chevron-up');
        }

        // Document modal functions
        function openDocumentModal(url, title) {
            document.getElementById('document-modal-title').textContent = title;
            document.getElementById('document-iframe').src = url;
            document.getElementById('document-modal').classList.remove('hidden');
        }

        function closeDocumentModal() {
            document.getElementById('document-modal').classList.add('hidden');
            document.getElementById('document-iframe').src = '';
        }

        // Image modal functions
        function openImageModal(url) {
            document.getElementById('modal-image').src = url;
            document.getElementById('image-modal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('image-modal').classList.add('hidden');
            document.getElementById('modal-image').src = '';
        }

        // Audio player functions
        function initAudioPlayer(index) {
            const audio = document.getElementById(`audio-${index}`);
            const durationElement = document.getElementById(`duration-${index}`);

            // Format and display duration
            const minutes = Math.floor(audio.duration / 60);
            const seconds = Math.floor(audio.duration % 60);
            durationElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }

        function togglePlayPause(index) {
            const audio = document.getElementById(`audio-${index}`);
            const playIcon = document.getElementById(`play-icon-${index}`);
            const pauseIcon = document.getElementById(`pause-icon-${index}`);

            if (audio.paused) {
                audio.play();
                playIcon.classList.add('hidden');
                pauseIcon.classList.remove('hidden');
            } else {
                audio.pause();
                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
            }
        }

        function updateProgress(index) {
            const audio = document.getElementById(`audio-${index}`);
            const progressBar = document.getElementById(`progress-bar-${index}`);
            const currentTimeElement = document.getElementById(`current-time-${index}`);

            // Update progress bar
            const progress = (audio.currentTime / audio.duration) * 100;
            progressBar.style.width = `${progress}%`;

            // Update current time display
            const minutes = Math.floor(audio.currentTime / 60);
            const seconds = Math.floor(audio.currentTime % 60);
            currentTimeElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

            // Check if audio ended
            if (audio.ended) {
                const playIcon = document.getElementById(`play-icon-${index}`);
                const pauseIcon = document.getElementById(`pause-icon-${index}`);

                playIcon.classList.remove('hidden');
                pauseIcon.classList.add('hidden');
            }
        }

        // Close modals when clicking outside
        document.getElementById('document-modal').addEventListener('click', function(e) {
            if (e.target === this) closeDocumentModal();
        });

        document.getElementById('image-modal').addEventListener('click', function(e) {
            if (e.target === this) closeImageModal();
        });

        // Listen for assessment completion events to update polling
        document.addEventListener('livewire:init', () => {
            @this.on('assessment-completed', () => {
                // Disable polling when assessments are completed
                assessmentPollingActive = false;
            });
        });
    </script>

    <style>
        .mini-player {
            transition: all 0.3s ease;
        }

        .mini-player:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .play-pause-btn {
            transition: all 0.2s ease;
        }

        .play-pause-btn:hover {
            transform: scale(1.05);
        }

        #document-modal,
        #image-modal {
            transition: opacity 0.3s ease;
        }

        /* Assessment section animations */
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .5;
            }
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/trix.css') }}">
</div>
