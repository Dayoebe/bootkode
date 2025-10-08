<?php
// LIVE EVENTS VIEW
// resources/views/livewire/community/partial/live-events.blade.php
?>

<div>
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 mb-6 animate__animated animate__fadeInUp">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Live Events</h2>
                <p class="text-gray-600 dark:text-gray-400">Join live webinars, workshops, and community meetups</p>
            </div>
            
            @if(auth()->user()->canManageCourses())
                <button wire:click="$set('showCreateForm', true)" 
                        class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i>Create Event
                </button>
            @endif
        </div>
        
        <!-- Today's Events -->
        @if($todayEvents->count() > 0)
            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-lg p-4 mb-4">
                <h3 class="font-semibold text-indigo-900 dark:text-indigo-300 mb-2">Today's Events</h3>
                <div class="space-y-2">
                    @foreach($todayEvents as $event)
                        <div class="flex items-center justify-between">
                            <span class="text-indigo-800 dark:text-indigo-200">{{ $event->title }}</span>
                            <span class="text-sm text-indigo-600 dark:text-indigo-400">{{ $event->start_date->format('g:i A') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-4 mb-6 animate__animated animate__fadeInUp animate__delay-1s">
        <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search"
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent"
                           placeholder="Search events...">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400 dark:text-gray-500"></i>
                </div>
            </div>
            
            <select wire:model.live="timeFilter" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent">
                <option value="upcoming">Upcoming</option>
                <option value="ongoing">Ongoing</option>
                <option value="past">Past Events</option>
                <option value="all">All Events</option>
            </select>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($events as $event)
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md dark:hover:bg-gray-700/50 transition-all duration-200 animate__animated animate__fadeInUp">
                <div class="p-6">
                    <!-- Event Type & Status -->
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-indigo-100 dark:bg-indigo-900/20 text-indigo-800 dark:text-indigo-300 px-2 py-1 rounded text-xs font-medium border border-indigo-200 dark:border-indigo-800">
                            <i class="fas fa-video mr-1"></i>
                            {{ ucfirst($event->metadata['event_type'] ?? 'Event') }}
                        </span>
                        
                        @if($event->isUpcoming())
                            <span class="bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300 px-2 py-1 rounded text-xs border border-green-200 dark:border-green-800">Upcoming</span>
                        @elseif($event->isOngoing())
                            <span class="bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300 px-2 py-1 rounded text-xs animate-pulse border border-yellow-200 dark:border-yellow-800">Live</span>
                        @else
                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 px-2 py-1 rounded text-xs border border-gray-200 dark:border-gray-600">Ended</span>
                        @endif
                    </div>
                    
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $event->title }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($event->description, 100) }}</p>
                    
                    <!-- Event Details -->
                    <div class="space-y-2 mb-4 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-center">
                            <i class="fas fa-calendar mr-2 w-4 text-indigo-500 dark:text-indigo-400"></i>
                            <span>{{ $event->start_date->format('M j, Y \a\t g:i A') }}</span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-user mr-2 w-4 text-indigo-500 dark:text-indigo-400"></i>
                            <span>{{ $event->creator->name }}</span>
                        </div>
                        
                        <div class="flex items-center">
                            <i class="fas fa-users mr-2 w-4 text-indigo-500 dark:text-indigo-400"></i>
                            <span>{{ $event->participants_count }} registered</span>
                        </div>
                        
                        @if($event->location)
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt mr-2 w-4 text-indigo-500 dark:text-indigo-400"></i>
                                <span class="truncate">{{ $event->location }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Action Button -->
                    @if($event->getUserParticipation())
                        @if($event->isOngoing())
                            <a href="{{ $event->location }}" target="_blank"
                               class="w-full bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 text-center block">
                                <i class="fas fa-play mr-2"></i>Join Live
                            </a>
                        @else
                            <button wire:click="unregisterFromEvent({{ $event->id }})" 
                                    class="w-full bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/30 py-2 rounded-lg font-medium transition-colors duration-200 border border-red-200 dark:border-red-800">
                                <i class="fas fa-user-minus mr-2"></i>Unregister
                            </button>
                        @endif
                    @else
                        <button wire:click="registerForEvent({{ $event->id }})" 
                                class="w-full bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white py-2 rounded-lg font-medium transition-colors duration-200">
                            <i class="fas fa-user-plus mr-2"></i>Register
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-video text-gray-300 dark:text-gray-600 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No events scheduled</h3>
                <p class="text-gray-600 dark:text-gray-400">Check back soon for upcoming live events!</p>
            </div>
        @endforelse
    </div>

    @if($events->hasPages())
        <div class="mt-6">{{ $events->links() }}</div>
    @endif
</div>