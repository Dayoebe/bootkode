<div class="bg-gray-800 p-6 rounded-lg shadow-xl text-white max-w-7xl mx-auto my-8">
    <div class="flex items-center justify-between border-b border-gray-700 pb-4 mb-6">
        <h2 class="text-3xl font-extrabold text-white">Course Reviews</h2>
    </div>

    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div class="w-full sm:w-1/3">
            <label for="search" class="sr-only">Search Reviews</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="search" wire:model.live.debounce.300ms="search" placeholder="Search reviews or courses..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-700 border border-gray-600 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm">
            </div>
        </div>
    </div>

    @if ($reviews->isEmpty())
        <div class="text-center py-10">
            <p class="text-gray-400 text-lg">No course reviews found.</p>
        </div>
    @else
        <div class="overflow-x-auto rounded-lg shadow-md border border-gray-700">
            <table class="min-w-full divide-y divide-gray-700">
                <thead class="bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Course
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Reviewer
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Review
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                            Rating
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-gray-800 divide-y divide-gray-700">
                    @foreach ($reviews as $review)
                        <tr class="hover:bg-gray-700 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-white">{{ Str::limit($review->course->title, 40) }}</div>
                                <div class="text-xs text-gray-400">by {{ $review->course->instructor->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-300">{{ $review->user->name }}</div>
                                <div class="text-xs text-gray-400">{{ $review->user->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-300 max-w-sm">{{ Str::limit($review->review_text, 150) }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-400">
                                @for ($i = 0; $i < $review->rating; $i++)
                                    <i class="fas fa-star"></i>
                                @endfor
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="" class="text-blue-400 hover:text-blue-600 mr-3">
                                    Reply
                                </button>
                                <button wire:click=""
                                        class="text-red-400 hover:text-red-600">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $reviews->links('pagination::tailwind') }}
        </div>
    @endif
</div>