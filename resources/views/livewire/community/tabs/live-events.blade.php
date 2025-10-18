{{-- resources/views/livewire/community/tabs/live-events.blade.php --}}
<div>
    <!-- Search & Filters -->
    <div class="mb-6 flex gap-4 flex-wrap">
        <div class="flex-1 min-w-[250px] relative">
            <input type="text" wire:model.live.debounce="search"
                   class="w-full pl-10 pr-4 py-2 bg-themed-secondary border border-themed-primary text-themed-primary rounded-lg placeholder-themed-secondary focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="Search events...">
            <i class="fas fa-search absolute left-3 top-2.5 text-themed-secondary"></i>
        </div>
        <select wire:model.live="timeFilter"
                class="bg-themed-secondary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
            <option value="upcoming">Upcoming</option>
            <option value="ongoing">Ongoing</option>
            <option value="past">Past Events</option>
        </select>
        <button wire:click="openModal('live-event')"
                class="bg-purple-600 hover:bg-purple-700 active:bg-purple-800 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2 whitespace-nowrap">
            <i class="fas fa-plus"></i>
            <span class="hidden sm:inline">Event</span>
        </button>
    </div>

    <!-- Events Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($events as $event)
            <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:shadow-md hover:border-purple-500/50 transition-all">
                <!-- Header -->
                <div class="flex items-start justify-between mb-3">
                    <h3 class="font-semibold text-themed-primary flex-1">{{ $event->title }}</h3>
                    <span class="bg-{{ $event->start_date > now() ? 'green' : 'gray' }}-100/20 
                               text-{{ $event->start_date > now() ? 'green' : 'gray' }}-400 
                               px-2 py-1 rounded text-xs font-medium border border-{{ $event->start_date > now() ? 'green' : 'gray' }}-500/30">
                        {{ $event->start_date > now() ? 'Upcoming' : 'Past' }}
                    </span>
                </div>

                <!-- Description -->
                <p class="text-sm text-themed-secondary line-clamp-2 mb-3">
                    {{ $event->description }}
                </p>

                <!-- Event Details -->
                <div class="space-y-2 text-xs text-themed-secondary mb-3 pb-3 border-b border-themed-primary">
                    <div><i class="fas fa-calendar mr-2 text-purple-400"></i>{{ $event->start_date->format('M j, Y g:i A') }}</div>
                    <div><i class="fas fa-map-pin mr-2 text-purple-400"></i>{{ Str::limit($event->location, 40) }}</div>
                    <div><i class="fas fa-users mr-2 text-purple-400"></i>{{ $event->activeParticipants->count() }} registered</div>
                </div>

                <!-- Action Button -->
                @php
                    $isRegistered = $event->activeParticipants()->where('user_id', auth()->id())->exists();
                @endphp
                @if($isRegistered)
                    <button wire:click="unregisterFromEvent({{ $event->id }})"
                            class="w-full bg-red-100/20 hover:bg-red-100/40 text-red-400 border border-red-500/30 rounded-lg py-2 font-medium transition-colors text-sm">
                        <i class="fas fa-times mr-1"></i>Unregister
                    </button>
                @else
                    <button wire:click="registerForEvent({{ $event->id }})"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white rounded-lg py-2 font-medium transition-colors text-sm">
                        <i class="fas fa-user-plus mr-1"></i>Register
                    </button>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-12 bg-themed-secondary border border-themed-primary rounded-lg">
                <i class="fas fa-video text-themed-secondary text-4xl mb-3 block"></i>
                <h3 class="text-lg font-semibold text-themed-primary mb-1">No events scheduled</h3>
                <p class="text-themed-secondary mb-4">Check back soon or create one!</p>
                <button wire:click="openModal('live-event')"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>Create Event
                </button>
            </div>
        @endforelse
    </div>

    @if($events->hasPages())
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    @endif
</div>
