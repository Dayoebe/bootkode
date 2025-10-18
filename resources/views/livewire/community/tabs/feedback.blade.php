
{{-- resources/views/livewire/community/tabs/feedback.blade.php --}}
<div>
    <!-- Search & Filters -->
    <div class="mb-6 flex gap-4 flex-wrap">
        <div class="flex-1 min-w-[250px] relative">
            <input type="text" wire:model.live.debounce="search"
                   class="w-full pl-10 pr-4 py-2 bg-themed-secondary border border-themed-primary text-themed-primary rounded-lg placeholder-themed-secondary focus:ring-2 focus:ring-red-500 focus:border-transparent"
                   placeholder="Search feedback...">
            <i class="fas fa-search absolute left-3 top-2.5 text-themed-secondary"></i>
        </div>
        <button wire:click="openModal('feedback')"
                class="bg-red-600 hover:bg-red-700 active:bg-red-800 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="fas fa-plus"></i>
            <span class="hidden sm:inline">Feedback</span>
        </button>
    </div>

    <!-- Feedback List -->
    <div class="space-y-3">
        @forelse($feedback as $item)
            <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:shadow-md transition-all">
                <!-- Header -->
                <div class="flex items-start justify-between gap-2 mb-2">
                    <h3 class="font-semibold text-themed-primary flex-1">{{ $item->subject }}</h3>
                    <span class="bg-{{ $item->getStatusColorAttribute() }}-100/20 text-{{ $item->getStatusColorAttribute() }}-400 px-2 py-1 rounded text-xs font-medium border border-{{ $item->getStatusColorAttribute() }}-500/30 whitespace-nowrap">
                        {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                    </span>
                </div>

                <!-- Category & Priority -->
                <div class="flex items-center gap-2 text-xs text-themed-secondary mb-2 flex-wrap">
                    <span class="bg-themed-tertiary px-2 py-1 rounded border border-themed-primary">
                        {{ ucfirst(str_replace('_', ' ', $item->category)) }}
                    </span>
                    <span class="bg-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-100/20 
                               text-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-400 
                               px-2 py-1 rounded border border-{{ $item->priority === 'high' ? 'red' : ($item->priority === 'medium' ? 'yellow' : 'gray') }}-500/30">
                        {{ ucfirst($item->priority) }} Priority
                    </span>
                </div>

                <!-- Message Preview -->
                <p class="text-sm text-themed-secondary line-clamp-2 mb-2">
                    {{ $item->message }}
                </p>

                <!-- Footer -->
                <div class="text-xs text-themed-secondary">
                    <i class="fas fa-clock mr-1"></i>{{ $item->created_at->diffForHumans() }}
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-themed-secondary border border-themed-primary rounded-lg">
                <i class="fas fa-comments text-themed-secondary text-4xl mb-3 block"></i>
                <h3 class="text-lg font-semibold text-themed-primary mb-1">No feedback yet</h3>
                <p class="text-themed-secondary mb-4">Share your thoughts to help us improve!</p>
                <button wire:click="openModal('feedback')"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>Submit Feedback
                </button>
            </div>
        @endforelse
    </div>

    @if($feedback->hasPages())
        <div class="mt-6">
            {{ $feedback->links() }}
        </div>
    @endif
</div>