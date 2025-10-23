<div>


@if (!$compact)
    <div class="bg-themed-secondary rounded-xl p-6 mb-6 border border-themed-primary transition-colors duration-300">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h1 class="text-2xl font-bold text-themed-primary">
                {{ $compact ? 'My Courses' : 'Enrolled Courses' }}
            </h1>

            @if (!$compact)
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                    <div class="relative flex-1">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search courses..."
                            class="w-full pl-10 pr-4 py-2 bg-themed-primary border border-themed-secondary rounded-lg text-themed-primary placeholder-themed-tertiary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                        <div class="absolute left-3 top-2.5 text-themed-tertiary">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>

                    <select wire:model.live="categoryFilter"
                        class="bg-themed-primary border border-themed-secondary text-themed-primary rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="sortBy" class="bg-themed-primary border border-themed-secondary text-themed-primary rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-accent-themed-primary">
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
                    class="bg-themed-secondary rounded-lg border border-themed-primary hover:border-accent-themed-primary transition-colors overflow-hidden">
                    <div class="relative">
                        <img src="{{ asset('storage/' . ($course->thumbnail ?? 'images/default-course.png')) }}" alt="{{ $course->title }}"
                            class="w-full h-40 object-cover">
                        <span class="absolute top-2 left-2 bg-accent-themed-primary text-white text-xs px-2 py-1 rounded">
                            {{ $course->category->name ?? 'Uncategorized' }}
                        </span>
                        @if ($progress >= 100)
                            <span class="absolute top-2 right-2 bg-green-500 text-white p-1 rounded-full">
                                <i class="fas fa-check text-xs"></i>
                            </span>
                        @endif
                    </div>

                    <div class="p-4">
                        <h3 class="font-bold text-themed-primary mb-1">{{ $course->title }}</h3>
                        <p class="text-themed-secondary text-sm mb-3 line-clamp-2">{{ $course->description }}</p>

                        <div class="flex justify-between text-xs text-themed-tertiary mb-2">
                            <span>{{ $progress }}% complete</span>
                            <span>{{ $course->pivot->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="w-full bg-themed-tertiary rounded-full h-1.5">
                            <div class="bg-accent-themed-primary h-1.5 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                        </div>

                        <div class="mt-4 flex justify-between gap-2">
                            <a href="{{ route('course.view', $course->slug) }}"
                                class="flex-1 text-center bg-accent-themed-primary hover:bg-accent-themed-secondary text-white py-1.5 px-4 rounded text-sm font-medium transition-colors">
                                {{ $progress > 0 ? 'Continue' : 'Start' }}
                            </a>
                            @if (!$compact)
                                <button class="bg-themed-tertiary hover:bg-themed-secondary text-themed-primary p-1.5 rounded border border-themed-secondary transition-colors">
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
        <div class="bg-themed-tertiary border-2 border-dashed border-themed-secondary rounded-xl p-8 text-center">
            <i class="fas fa-book-open text-themed-tertiary text-4xl mb-3"></i>
            <h3 class="text-lg font-medium text-themed-secondary mb-2">
                {{ $compact ? 'No courses in progress' : 'No enrolled courses' }}
            </h3>
            <p class="text-themed-tertiary mb-4">
                {{ $compact ? 'Continue learning from your courses' : 'Browse the catalog to enroll in courses' }}
            </p>
            <a href="{{ route('student.course-catalog') }}"
                class="inline-flex items-center px-4 py-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-lg text-sm font-medium transition-colors"
                wire:navigate>
                <i class="fas fa-book-open mr-2"></i> Browse Catalog
            </a>
        </div>
    @endif

    @if (!$compact)
        </div>
    @endif

    <style>
        /* Smooth transitions for theme changes */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
    </div>