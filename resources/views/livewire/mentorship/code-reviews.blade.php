<div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-themed-primary transition-colors duration-300">Code Reviews</h2>
    </div>

    @if($reviews->count() > 0)
        <div class="grid gap-4">
            @foreach($reviews as $review)
                <div class="border border-themed-primary rounded-lg p-6 hover:shadow-md transition-all duration-300 bg-themed-secondary">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-purple-100/50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center border border-purple-200/50 dark:border-purple-800 transition-colors duration-300">
                                <i class="fas fa-code-branch"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-themed-primary transition-colors duration-300">{{ $review->title }}</h3>
                                <p class="text-sm text-themed-secondary transition-colors duration-300">by {{ $review->requester->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-100 dark:bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-900/30 
                                       text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-800 dark:text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-300 
                                       px-3 py-1 rounded-full text-sm border border-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-200 dark:border-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-800 transition-colors duration-300">
                                {{ ucfirst($review->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-themed-secondary transition-colors duration-300">{{ \Illuminate\Support\Str::limit($review->description, 150) }}</p>
                    </div>

                    @if($review->technologies && count($review->technologies) > 0)
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach(array_slice($review->technologies, 0, 4) as $tech)
                                <span class="bg-accent-primary/10 text-accent-primary px-2 py-1 rounded-full text-xs border border-accent-primary/30 transition-colors duration-300">{{ $tech }}</span>
                            @endforeach
                            @if(count($review->technologies) > 4)
                                <span class="text-themed-tertiary text-xs transition-colors duration-300">+{{ count($review->technologies) - 4 }} more</span>
                            @endif
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-themed-secondary transition-colors duration-300">
                            <span><i class="fas fa-flag mr-1"></i>{{ ucfirst($review->priority) }}</span>
                            <span><i class="fas fa-clock mr-1"></i>{{ $review->requested_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex space-x-2">
                            @if($review->status === 'pending')
                                <button wire:click="startReview({{ $review->id }})"
                                    class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-play mr-1"></i>Start Review
                                </button>
                            @elseif($review->status === 'in_review')
                                <button wire:click="completeReview({{ $review->id }})"
                                    class="bg-accent-primary hover:bg-accent-secondary text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    <i class="fas fa-check mr-1"></i>Complete
                                </button>
                            @endif
                            <button wire:click="viewReview({{ $review->id }})"
                                class="bg-themed-tertiary hover:bg-accent-primary/10 text-themed-primary hover:text-accent-primary px-4 py-2 rounded-lg text-sm transition-colors duration-300 border border-themed-primary">
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
            <div class="text-6xl text-themed-tertiary mb-4 transition-colors duration-300">
                <i class="fas fa-code"></i>
            </div>
            <h3 class="text-xl font-semibold text-themed-primary mb-2 transition-colors duration-300">No code reviews yet</h3>
            <p class="text-themed-secondary transition-colors duration-300">Code review requests will appear here</p>
        </div>
    @endif
</div>