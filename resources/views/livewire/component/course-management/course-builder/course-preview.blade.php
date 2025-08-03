<div class="bg-gray-900 text-white min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Back to Builder Button -->
        <div class="mb-6">
            <a href="{{ route('course-builder', $course) }}"
                class="inline-flex items-center text-blue-400 hover:text-blue-300">
                <i class="fas fa-arrow-left mr-2"></i> Back to Builder
            </a>
        </div>

        <!-- Course Header -->
        <div class="flex flex-col md:flex-row gap-8 mb-12">
            <div class="md:w-1/3">
                @if ($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                        class="w-full h-auto rounded-xl shadow-xl">
                @else
                    <div class="w-full h-48 bg-gray-800 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book-open text-4xl text-gray-600"></i>
                    </div>
                @endif
            </div>
            <div class="md:w-2/3">
                <div class="flex items-center space-x-2 mb-4">
                    <span class="px-3 py-1 bg-blue-600 text-white rounded-full text-sm">
                        {{ ucfirst($course->difficulty_level) }}
                    </span>
                    <span class="px-3 py-1 bg-gray-700 text-white rounded-full text-sm">
                        {{ $course->category->name ?? 'Uncategorized' }}
                    </span>
                </div>
                <h1 class="text-4xl font-bold mb-4">{{ $course->title }}</h1>
                <p class="text-gray-300 text-lg mb-6">{{ $course->description }}</p>

                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <i class="fas fa-user text-blue-400 mr-2"></i>
                        <span>Created by {{ $course->instructor->name }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock text-blue-400 mr-2"></i>
                        <span>{{ $course->estimated_duration_minutes }} min</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Course Navigation Sidebar -->
            <div class="lg:col-span-1 order-first">
                <div class="sticky top-4 space-y-6">
                    <!-- Navigation -->
                    <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                        <h3 class="text-xl font-bold mb-4">Course Navigation</h3>
                        <div class="space-y-2 max-h-[calc(100vh-200px)] overflow-y-auto">
                            @foreach ($course->sections as $section)
                                <details @if ($loop->first) open @endif class="group">
                                    <summary
                                        class="flex items-center justify-between font-medium cursor-pointer py-2 px-3 hover:bg-gray-700 rounded-lg transition-colors">
                                        <span>{{ $section->title }}</span>
                                        <i
                                            class="fas fa-chevron-down text-xs text-gray-400 group-open:rotate-180 transition-transform"></i>
                                    </summary>
                                    <div class="ml-4 mt-1 space-y-1">
                                        @foreach ($section->lessons as $lesson)
                                            <a href="#lesson-{{ $lesson->id }}"
                                                class="block py-2 px-3 text-sm hover:bg-gray-700 rounded-lg transition-colors
                                                    @if ($highlightLesson == $lesson->id) bg-blue-900/30 text-blue-300 @endif">
                                                {{ $lesson->title }}
                                                @if ($lesson->duration_minutes)
                                                    <span
                                                        class="text-xs text-gray-500 ml-2">{{ $lesson->duration_minutes }}m</span>
                                                @endif
                                            </a>
                                        @endforeach
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                        <h3 class="text-xl font-bold mb-4">Your Progress</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Lessons Completed</span>
                                <span
                                    class="font-medium">5/{{ $course->sections->sum(fn($s) => $s->lessons->count()) }}</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: 45%"></div>
                            </div>
                            <button
                                class="w-full mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg font-medium transition-colors">
                                Continue Learning
                            </button>
                        </div>
                    </div>

                    <!-- Instructor -->
                    <div class="bg-gray-800 rounded-xl p-6 shadow-lg">
                        <h3 class="text-xl font-bold mb-4">Instructor</h3>
                        <div class="flex items-center space-x-4">
                            <img src="{{ $course->instructor->avatar_url ?? asset('images/default-avatar.png') }}"
                                class="w-12 h-12 rounded-full object-cover border-2 border-gray-600">
                            <div>
                                <h4 class="font-medium">{{ $course->instructor->name }}</h4>
                                <p class="text-sm text-gray-400">Course Author</p>
                            </div>
                        </div>
                        <p class="mt-4 text-gray-300 text-sm">
                            {{ $course->instructor->bio ?? 'No bio available' }}
                        </p>
                        <button
                            class="w-full mt-4 px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg font-medium transition-colors">
                            Message Instructor
                        </button>
                    </div>
                </div>
            </div>

            <!-- Course Content -->
            <div class="lg:col-span-3 space-y-8">
                @foreach ($course->sections as $section)
                    <div class="bg-gray-800 rounded-xl overflow-hidden shadow-lg">
                        <div class="p-6 border-b border-gray-700">
                            <h2 class="text-2xl font-bold">{{ $section->title }}</h2>
                            @if ($section->description)
                                <p class="text-gray-300 mt-2">{{ $section->description }}</p>
                            @endif
                        </div>

                        <div class="divide-y divide-gray-700">
                            @foreach ($section->lessons as $lesson)
                                <div id="lesson-{{ $lesson->id }}"
                                    class="p-6 hover:bg-gray-750 transition-colors
                                    @if ($highlightLesson == $lesson->id) bg-gray-750 border-l-4 border-blue-500 @endif">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="text-xl font-semibold flex items-center">
                                                <span class="mr-3 text-blue-400">#{{ $loop->iteration }}</span>
                                                {{ $lesson->title }}
                                            </h3>
                                            @if ($lesson->duration_minutes)
                                                <div class="flex items-center mt-2 text-sm text-gray-400">
                                                    <i class="fas fa-clock mr-1.5"></i>
                                                    {{ $lesson->duration_minutes }} min
                                                </div>
                                            @endif
                                        </div>
                                        <button class="text-blue-400 hover:text-blue-300 ml-4">
                                            <i class="fas fa-play-circle text-xl"></i>
                                        </button>
                                    </div>

                                    <!-- Lesson Content -->
                                    @if ($lesson->content)
                                        <div class="mt-4 pt-4 border-t border-gray-700">
                                            <!-- Main Content -->
                                            <div class="prose prose-invert max-w-none text-gray-300">
                                                {!! $lesson->content['body'] ?? '' !!}
                                            </div>

                                            <!-- Content Blocks -->
                                            @isset($lesson->content['blocks'])
                                                <div class="mt-6 space-y-4">
                                                    @foreach ($lesson->content['blocks'] as $block)
                                                        @switch($block['type'])
                                                            @case('image')
                                                                <div
                                                                    class="bg-gray-850 rounded-lg overflow-hidden border border-gray-700">
                                                                    <img src="{{ Storage::url($block['file_path']) }}"
                                                                        alt="{{ $block['caption'] ?? '' }}" class="w-full h-auto">
                                                                    @if (isset($block['caption']) && $block['caption'])
                                                                        <p class="p-3 text-sm text-gray-300 bg-gray-900">
                                                                            {{ $block['caption'] }}</p>
                                                                    @endif
                                                                </div>
                                                            @break

                                                            @case('video')
                                                                <div
                                                                    class="bg-gray-850 rounded-lg overflow-hidden border border-gray-700">
                                                                    <div class="aspect-w-16 aspect-h-9">
                                                                        @if (isset($block['video_url']))
                                                                            <iframe src="{{ $block['video_url'] }}"
                                                                                class="w-full h-64" frameborder="0"
                                                                                allowfullscreen></iframe>
                                                                        @else
                                                                            <video controls class="w-full">
                                                                                <source
                                                                                    src="{{ Storage::url($block['file_path']) }}"
                                                                                    type="video/mp4">
                                                                            </video>
                                                                        @endif
                                                                    </div>
                                                                    @if (isset($block['title']))
                                                                        <div class="p-3 text-sm text-gray-300 bg-gray-900">
                                                                            {{ $block['title'] }}</div>
                                                                    @endif
                                                                </div>
                                                            @break

                                                            @case('file')
                                                                <div
                                                                    class="bg-gray-850 rounded-lg p-4 flex items-center border border-gray-700">
                                                                    <i class="fas fa-file text-2xl text-blue-400 mr-3"></i>
                                                                    <div class="flex-1">
                                                                        <p class="font-medium">{{ $block['file_name'] }}</p>
                                                                        <p class="text-xs text-gray-400 mt-1">
                                                                            {{ round($block['file_size'] / 1024) }} KB
                                                                        </p>
                                                                    </div>
                                                                    <a href="{{ Storage::url($block['file_path']) }}" download
                                                                        class="text-blue-400 hover:text-blue-300">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                </div>
                                                            @break

                                                            @case('code')
                                                                <div
                                                                    class="bg-gray-900 rounded-lg overflow-hidden border border-gray-700">
                                                                    <div
                                                                        class="flex justify-between items-center px-4 py-2 bg-gray-800 border-b border-gray-700">
                                                                        <span
                                                                            class="text-sm text-gray-300">{{ $block['title'] ?? 'Code Example' }}</span>
                                                                        <button onclick="copyToClipboard(this)"
                                                                            class="text-gray-400 hover:text-white text-sm"
                                                                            data-code="{{ htmlentities($block['code']) }}">
                                                                            <i class="fas fa-copy mr-1"></i> Copy
                                                                        </button>
                                                                    </div>
                                                                    <pre class="p-4 overflow-x-auto bg-gray-900 text-green-400 font-mono text-sm"><code>{{ $block['code'] }}</code></pre>
                                                                </div>
                                                            @break

                                                            @case('note')
                                                            @php
                                                                $noteClasses = [
                                                                    'tip' => 'bg-blue-900/20 border-blue-500',
                                                                    'warning' => 'bg-yellow-900/20 border-yellow-500', 
                                                                    'info' => 'bg-green-900/20 border-green-500'
                                                                ];
                                                                $noteIcons = [
                                                                    'tip' => 'lightbulb',
                                                                    'warning' => 'exclamation-triangle',
                                                                    'info' => 'info-circle'
                                                                ];
                                                                
                                                                $noteType = $block['note_type'] ?? 'tip';
                                                                $noteClass = $noteClasses[$noteType] ?? 'bg-blue-900/20 border-blue-500';
                                                                $noteIcon = $noteIcons[$noteType] ?? 'lightbulb';
                                                            @endphp
                                                            
                                                            <div class="p-4 rounded-lg border-l-4 {{ $noteClass }}">
                                                                <div class="flex items-start">
                                                                    <i class="fas fa-{{ $noteIcon }} mt-1 mr-3"></i>
                                                                    <div>
                                                                        <h4 class="font-bold">{{ $block['title'] }}</h4>
                                                                        <p class="text-gray-300 mt-1">{{ $block['content'] }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @break
                                                        @endswitch
                                                    @endforeach
                                                </div>
                                            @endisset
                                        </div>
                                    @else
                                        <div class="mt-4 text-sm text-gray-500 italic">
                                            No content added yet
                                        </div>
                                    @endif

                                    <!-- Lesson Actions -->
                                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-700">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="complete-{{ $lesson->id }}"
                                                class="rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                                            <label for="complete-{{ $lesson->id }}"
                                                class="ml-2 text-sm text-gray-300">
                                                Mark as complete
                                            </label>
                                        </div>
                                        <button class="text-sm text-blue-400 hover:text-blue-300">
                                            <i class="fas fa-flag mr-1"></i> Report Issue
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if ($highlightLesson)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const element = document.getElementById('lesson-{{ $highlightLesson }}');
                if (element) {
                    setTimeout(() => {
                        element.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        element.classList.add('ring-2', 'ring-blue-500');
                        setTimeout(() => element.classList.remove('ring-2', 'ring-blue-500'), 2000);
                    }, 300);
                }
            });

            function copyToClipboard(button) {
                const code = button.getAttribute('data-code');
                navigator.clipboard.writeText(code).then(() => {
                    const originalHtml = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check mr-1"></i> Copied!';
                    setTimeout(() => {
                        button.innerHTML = originalHtml;
                    }, 2000);
                });
            }

            function printCourse() {
                window.print();
            }
        </script>
    @endif
</div>
