<!-- Search Bar & Create Button -->
<div class="mb-6 flex gap-4">
    <div class="flex-1 relative">
        <input type="text" wire:model.live.debounce="search"
               class="w-full pl-10 pr-4 py-2 bg-themed-secondary border border-themed-primary text-themed-primary rounded-lg placeholder-themed-secondary focus:ring-2 focus:ring-blue-500 focus:border-transparent"
               placeholder="Search discussions...">
        <i class="fas fa-search absolute left-3 top-2.5 text-themed-secondary"></i>
    </div>
    <button wire:click="openModal('forum')"
            class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
        <i class="fas fa-plus"></i>
        <span class="hidden sm:inline">New Discussion</span>
    </button>
</div>

<!-- Threads List -->
<div class="space-y-3">
    @forelse($threads as $thread)
        <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:shadow-md hover:border-blue-500/50 transition-all group cursor-pointer">
            <div class="flex gap-3">

                <img src="{{ $thread->user->profile_picture ? asset('storage/' . $thread->user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($thread->user->name) }}"
                     alt="{{ $thread->user->name }}"
                     class="w-10 h-10 rounded-full flex-shrink-0">

                <div class="flex-1 min-w-0">
                    <!-- Title & Badges -->
                    <div class="flex items-start gap-2 mb-1 flex-wrap">
                        <div class="flex items-center gap-2">
                            @if($thread->is_pinned)
                                <span class="text-red-500" title="Pinned">
                                    <i class="fas fa-thumbtack text-sm"></i>
                                </span>
                            @endif
                            @if($thread->is_locked)
                                <span class="text-themed-secondary" title="Locked">
                                    <i class="fas fa-lock text-sm"></i>
                                </span>
                            @endif
                        </div>
                        <h3 class="font-semibold text-themed-primary group-hover:text-blue-600 transition-colors line-clamp-2 flex-1">
                            {{ $thread->title }}
                        </h3>
                    </div>

                    <!-- Preview & Metadata -->
                    <p class="text-sm text-themed-secondary line-clamp-2 mb-2">
                        {{ Str::limit(strip_tags($thread->content), 150) }}
                    </p>

                    <div class="flex items-center justify-between text-xs text-themed-secondary gap-2 flex-wrap">
                        <div class="flex items-center gap-3">
                            <span>{{ $thread->user->name }}</span>
                            <span>{{ $thread->created_at->diffForHumans() }}</span>
                            <span><i class="fas fa-eye mr-1"></i>{{ $thread->views }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span><i class="fas fa-comments mr-1"></i>{{ $thread->replies_count }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-12 bg-themed-secondary border border-themed-primary rounded-lg">
            <i class="fas fa-comments text-themed-secondary text-4xl mb-3 block"></i>
            <h3 class="text-lg font-semibold text-themed-primary mb-1">No discussions yet</h3>
            <p class="text-themed-secondary mb-4">Start a conversation!</p>
            <button wire:click="openModal('forum')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center gap-2">
                <i class="fas fa-plus"></i>Create Discussion
            </button>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($threads->hasPages())
    <div class="mt-6">
        {{ $threads->links() }}
    </div>
@endif