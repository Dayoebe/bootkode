@if (!$compact)
    <div class="bg-gray-800 rounded-xl p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h1 class="text-2xl font-bold text-white">
                {{ $compact ? 'My Courses' : 'Enrolled Courses' }}
            </h1>

            @if (!$compact)
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <div class="relative flex-1">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search courses..."
                            class="w-full pl-10 pr-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>

                    <select wire:model.live="categoryFilter"
                        class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="sortBy"
                        class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2">
                        <option value="recent">Recently Accessed</option>
                        <option value="progress">Progress</option>
                        <option value="title">Alphabetical</option>
                    </select>
                </div>
            @endif
        </div>
@endif

@if ($courses->count() > 0)
    <div class="grid grid-cols-1 @if (!$compact) md:grid-cols-2 lg:grid-cols-3 @endif gap-4">
        @foreach ($courses as $course)
            @php $progress = $this->calculateProgress($course); @endphp
            <div
                class="bg-gray-700 rounded-lg border border-gray-600 hover:border-blue-500 transition-colors overflow-hidden">
                <div class="relative">
                    <img src="{{ $course->thumbnail ?? asset('images/default-course.png') }}" alt="{{ $course->title }}"
                        class="w-full h-40 object-cover">
                    <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">
                        {{ $course->category->name ?? 'Uncategorized' }}
                    </span>
                    @if ($progress >= 100)
                        <span class="absolute top-2 right-2 bg-green-500 text-white p-1 rounded-full">
                            <i class="fas fa-check text-xs"></i>
                        </span>
                    @endif
                </div>

                <div class="p-4">
                    <h3 class="font-bold text-white mb-1">{{ $course->title }}</h3>
                    <p class="text-gray-400 text-sm mb-3 line-clamp-2">{{ $course->description }}</p>

                    <div class="flex justify-between text-xs text-gray-400 mb-2">
                        <span>{{ $progress }}% complete</span>
                        <span>{{ $course->pivot->updated_at->diffForHumans() }}</span>
                    </div>
                    <div class="w-full bg-gray-600 rounded-full h-1.5">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>

                    <div class="mt-4 flex justify-between gap-2">
                        <a href="{{ route('course.view', $course->slug) }}" 
                            class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white py-1.5 px-4 rounded text-sm">
                             {{ $progress > 0 ? 'Continue' : 'Start' }}
                         </a>
                        @if (!$compact)
                            <button class="bg-gray-600 hover:bg-gray-500 text-white p-1.5 rounded">
                                <i class="fas fa-ellipsis-v text-xs"></i>
                            </button>
                        @endif
                    </div>
                </div>







            </div>
        @endforeach
    </div>

    @if (!$compact)
        <div class="mt-6">
            {{ $courses->links() }}
        </div>
    @endif
@else
    <div class="bg-gray-700/50 border border-dashed border-gray-600 rounded-xl p-8 text-center">
        <i class="fas fa-book-open text-gray-400 text-4xl mb-3"></i>
        <h3 class="text-lg font-medium text-gray-300 mb-2">
            {{ $compact ? 'No courses in progress' : 'No enrolled courses' }}
        </h3>
        <p class="text-gray-500 mb-4">
            {{ $compact ? 'Continue learning from your courses' : 'Browse the catalog to enroll in courses' }}
        </p>
        <a href="{{ route('student.course-catalog') }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm"
            wire:navigate>
            <i class="fas fa-book-open mr-2"></i> Browse Catalog
        </a>
    </div>
@endif

@if (!$compact)
    </div>
@endif
