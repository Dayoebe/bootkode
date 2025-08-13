<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-book-open mr-2"></i> Available Courses
        </h1>
        <p class="text-gray-400 mt-2">Browse and enroll in courses to start learning</p>
    </div>
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search courses..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex-1">
            <select wire:model.live="categoryFilter" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $course)
            <div class="bg-white shadow rounded-lg overflow-hidden animate__animated animate__fadeInUp">
                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h2 class="text-xl font-bold mb-2">{{ $course->title }}</h2>
                    <p class="text-gray-600 mb-2">{{ $course->description }}</p>
                    <p class="text-sm text-gray-500">Instructor: {{ $course->instructor->name }}</p>
                    <p class="text-sm text-gray-500">Difficulty: {{ ucfirst($course->difficulty_level) }}</p>
                    <p class="text-sm text-gray-500">
                        Rating: 
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $course->average_rating >= $i ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                        @endfor
                        ({{ number_format($course->average_rating, 1) }} from {{ $course->rating_count }} reviews)
                    </p>
                    <button wire:click="enroll({{ $course->id }})" wire:loading.attr="disabled"
                            class="mt-4 w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                        <span wire:loading.remove><i class="fas fa-plus-circle mr-2"></i> Enroll Now</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Enrolling...</span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-6">{{ $courses->links() }}</div>
</div>