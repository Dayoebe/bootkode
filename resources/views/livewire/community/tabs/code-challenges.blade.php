<div>
    <!-- Search & Filters -->
    <div class="mb-6 flex gap-4 flex-wrap">
        <div class="flex-1 min-w-[250px] relative">
            <input type="text" aria-label="Search challenges" wire:model.live.debounce="search"
                   class="w-full pl-10 pr-4 py-2 bg-themed-secondary border border-themed-primary text-themed-primary rounded-lg placeholder-themed-secondary focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                   placeholder="Search challenges...">
            <i class="fas fa-search absolute left-3 top-2.5 text-themed-secondary"></i>
        </div>
        <select aria-label="Challenge difficulty" wire:model.live="difficultyFilter"
                class="bg-themed-secondary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
            <option value="all">All Levels</option>
            <option value="easy">Easy</option>
            <option value="medium">Medium</option>
            <option value="hard">Hard</option>
        </select>
        <button wire:click="openModal('code-challenge')"
        class="bg-orange-600 hover:bg-orange-700 active:bg-orange-800 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
    <i class="fas fa-plus"></i>
    <span class="hidden sm:inline">Challenge</span>
</button>
    </div>

    <!-- Challenges Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($challenges as $challenge)
            <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4  hover:border-orange-500/50 transition-all">
                <!-- Header -->
                <div class="flex items-start justify-between mb-3">
                    <h3 class="font-semibold text-themed-primary flex-1">{{ $challenge->title }}</h3>
                    <span class="rounded border border-line bg-surface-muted px-2 py-1 text-xs font-medium text-content">
                        {{ ucfirst($challenge->metadata['difficulty'] ?? 'Not recorded') }}
                    </span>
                </div>

                <!-- Description -->
                <p class="text-sm text-themed-secondary line-clamp-2 mb-3">
                    {{ $challenge->description }}
                </p>

                <!-- Points & Creator -->
                <div class="flex items-center gap-2 text-xs text-themed-secondary mb-3 pb-3 border-b border-themed-primary flex-wrap">
                    <span><i class="fas fa-trophy mr-1 text-orange-400"></i>{{ isset($challenge->metadata['points']) ? $challenge->metadata['points'].' pts' : 'Points not set' }}</span>
                    <span><i class="fas fa-user mr-1"></i>{{ $challenge->creator->name ?? 'Not recorded' }}</span>
                </div>

                <!-- Solve Button -->
                <button wire:click="openCodeChallenge({{ $challenge->id }})"
                        class="w-full bg-orange-600 hover:bg-orange-700 text-white rounded-lg py-2 font-medium transition-colors text-sm">
                    <i class="fas fa-code mr-1"></i>Solve
                </button>
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-themed-secondary border border-themed-primary rounded-lg">
                <i class="fas fa-code text-themed-secondary text-4xl mb-3 block"></i>
                <h3 class="text-lg font-semibold text-themed-primary mb-1">No challenges yet</h3>
                <p class="text-themed-secondary mb-4">Create the first challenge!</p>
                <button wire:click="openModal('code-challenge')"
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>Create Challenge
                </button>
            </div>
        @endforelse
    </div>

    @if($challenges->hasPages())
        <div class="mt-6">
            {{ $challenges->links() }}
        </div>
    @endif
</div>
