{{-- resources/views/livewire/community/tabs/study-groups.blade.php --}}
<div>
    <!-- Search & Create -->
    <div class="mb-6 flex gap-4">
        <div class="flex-1 relative">
            <input type="text" wire:model.live.debounce="search"
                   class="w-full pl-10 pr-4 py-2 bg-themed-secondary border border-themed-primary text-themed-primary rounded-lg placeholder-themed-secondary focus:ring-2 focus:ring-green-500 focus:border-transparent"
                   placeholder="Search study groups...">
            <i class="fas fa-search absolute left-3 top-2.5 text-themed-secondary"></i>
        </div>
        <button wire:click="openModal('study-group')"
                class="bg-green-600 hover:bg-green-700 active:bg-green-800 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="fas fa-plus"></i>
            <span class="hidden sm:inline">New Group</span>
        </button>
    </div>

    <!-- Study Groups Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($studyGroups as $group)
            <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:shadow-md hover:border-green-500/50 transition-all">
                <!-- Header -->
                <div class="flex items-start justify-between mb-3">
                    <h3 class="font-semibold text-themed-primary text-lg flex-1">{{ $group->title }}</h3>
                    <span class="bg-green-100/20 text-green-400 px-2 py-1 rounded text-xs font-medium border border-green-500/30">
                        {{ $group->status === 'active' ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Description -->
                <p class="text-sm text-themed-secondary line-clamp-2 mb-3">
                    {{ $group->description }}
                </p>

                <!-- Tags -->
                @if($group->tags && count($group->tags) > 0)
                    <div class="flex flex-wrap gap-1 mb-3">
                        @foreach(array_slice($group->tags, 0, 3) as $tag)
                            <span class="text-xs bg-themed-tertiary text-themed-secondary px-2 py-1 rounded border border-themed-primary">
                                {{ $tag }}
                            </span>
                        @endforeach
                        @if(count($group->tags) > 3)
                            <span class="text-xs text-themed-secondary">+{{ count($group->tags) - 3 }}</span>
                        @endif
                    </div>
                @endif

                <!-- Info Row -->
                <div class="flex items-center gap-2 text-xs text-themed-secondary mb-3 pb-3 border-b border-themed-primary flex-wrap">
                    <span><i class="fas fa-user mr-1"></i>{{ $group->creator->name }}</span>
                    <span><i class="fas fa-users mr-1"></i>{{ $group->activeParticipants->count() }} joined</span>
                    @if($group->start_date)
                        <span><i class="fas fa-calendar mr-1"></i>{{ $group->start_date->format('M j') }}</span>
                    @endif
                </div>

                <!-- Action Button -->
                @php
                    $isJoined = $group->activeParticipants()->where('user_id', auth()->id())->exists();
                @endphp
                @if($isJoined)
                    <button wire:click="leaveStudyGroup({{ $group->id }})"
                            class="w-full bg-red-100/20 hover:bg-red-100/40 text-red-400 border border-red-500/30 rounded-lg py-2 font-medium transition-colors text-sm">
                        <i class="fas fa-arrow-right-from-bracket mr-1"></i>Leave
                    </button>
                @else
                    <button wire:click="joinStudyGroup({{ $group->id }})"
                            class="w-full bg-green-600 hover:bg-green-700 text-white rounded-lg py-2 font-medium transition-colors text-sm">
                        <i class="fas fa-user-plus mr-1"></i>Join Group
                    </button>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-themed-secondary border border-themed-primary rounded-lg">
                <i class="fas fa-users text-themed-secondary text-4xl mb-3 block"></i>
                <h3 class="text-lg font-semibold text-themed-primary mb-1">No study groups yet</h3>
                <p class="text-themed-secondary mb-4">Create one to get started!</p>
                <button wire:click="openModal('study-group')"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>Create Group
                </button>
            </div>
        @endforelse
    </div>

    @if($studyGroups->hasPages())
        <div class="mt-6">
            {{ $studyGroups->links() }}
        </div>
    @endif
</div>

