{{-- FILE: resources/views/livewire/mentorship/partial/code-reviews.blade.php --}}

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Code Reviews</h2>
    </div>

    @if($reviews->count() > 0)
        <div class="grid gap-4">
            @foreach($reviews as $review)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center border border-purple-200 dark:border-purple-800">
                                <i class="fas fa-code-branch"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $review->title }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">by {{ $review->requester->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-100 dark:bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-900/30 
                                       text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-800 dark:text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-300 
                                       px-3 py-1 rounded-full text-sm border border-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-200 dark:border-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-800">
                                {{ ucfirst($review->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($review->description, 150) }}</p>
                    </div>

                    @if($review->technologies && count($review->technologies) > 0)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach(array_slice($review->technologies, 0, 4) as $tech)
                                <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-full text-xs border border-blue-200 dark:border-blue-800">{{ $tech }}</span>
                            @endforeach
                            @if(count($review->technologies) > 4)
                                <span class="text-gray-500 dark:text-gray-400 text-xs">+{{ count($review->technologies) - 4 }} more</span>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                            <span><i class="fas fa-flag mr-1"></i>{{ ucfirst($review->priority) }}</span>
                            <span><i class="fas fa-clock mr-1"></i>{{ $review->requested_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex space-x-2">
                            @if($review->status === 'pending')
                                <button wire:click="startReview({{ $review->id }})"
                                    class="bg-green-600 dark:bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 dark:hover:bg-green-600 transition-colors">
                                    <i class="fas fa-play mr-1"></i>Start Review
                                </button>
                            @elseif($review->status === 'in_review')
                                <button wire:click="completeReview({{ $review->id }})"
                                    class="bg-blue-600 dark:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-check mr-1"></i>Complete
                                </button>
                            @endif
                            <button wire:click="viewReview({{ $review->id }})"
                                class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors border border-gray-200 dark:border-gray-600">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="text-6xl text-gray-400 dark:text-gray-600 mb-4">
                <i class="fas fa-code"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No code reviews yet</h3>
            <p class="text-gray-500 dark:text-gray-400">Code review requests will appear here</p>
        </div>
    @endif
</div>