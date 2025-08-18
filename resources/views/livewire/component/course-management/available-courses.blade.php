<div class="bg-gradient-to-br from-blue-900 to-teal-600 p-8 rounded-2xl shadow-2xl text-white animate__animated animate__fadeIn" x-data="{ tooltip: '' }">
    <div class="bg-gradient-to-r from-indigo-800 to-teal-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-book-open mr-2 text-indigo-300" aria-hidden="true"></i> Available Courses
        </h1>
        <p class="text-indigo-200 mt-2">Browse and enroll in courses to start learning</p>
    </div>
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search courses..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white dark:border-gray-600"
                   aria-label="Search courses">
        </div>
        <div class="flex-1">
            <select wire:model.live="categoryFilter" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-white dark:border-gray-600"
                    aria-label="Filter by category">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $index => $course)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden transform transition duration-300 hover:scale-105 animate__animated animate__fadeInUp" style="animation-delay: {{ $index * 0.1 }}s">
                <img src="{{ Storage::url($course->thumbnail) }}" alt="{{ $course->title }} thumbnail" class="w-full h-48 object-cover" loading="lazy">
                <div class="p-4">
                    <h2 class="text-xl font-bold mb-2 text-gray-900 dark:text-white">{{ $course->title }}</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-2">{{ Str::limit($course->description, 100) }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Instructor: {{ $course->instructor->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Difficulty: 
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $course->difficulty_level === 'beginner' ? 'bg-green-100 text-green-800' : ($course->difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($course->difficulty_level) }}
                        </span>
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center">
                        Rating: 
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ ($course->average_rating ?? 0) >= $i ? 'text-yellow-400' : 'text-gray-300' }} ml-1" aria-label="Rating: {{ $course->average_rating ?? 0 }} out of 5"></i>
                        @endfor
                        <span class="ml-2">({{ number_format($course->average_rating ?? 0, 1) }} from {{ $course->rating_count ?? 0 }} reviews)</span>
                    </p>
                    @if ($course->certificate_template)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-medal mr-1 text-indigo-500"></i> Earn a Certificate
                        </p>
                    @endif
                    @if (Auth::user()->courses->contains($course->id))
                        <button class="mt-4 w-full px-4 py-2 bg-green-600 text-white rounded-md cursor-not-allowed" disabled>
                            <i class="fas fa-check-circle mr-2"></i> Already Enrolled
                        </button>
                    @else
                        <button wire:click="enroll({{ $course->id }})" wire:loading.attr="disabled"
                                class="mt-4 w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                aria-label="Enroll in {{ $course->title }}">
                            <span wire:loading.remove><i class="fas fa-plus-circle mr-2"></i> Enroll Now</span>
                            <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Enrolling...</span>
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $courses->links('pagination::tailwind') }}</div>
</div>