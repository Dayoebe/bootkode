{{-- FILE: resources/views/livewire/mentorship/partial/sessions.blade.php --}}

<div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-themed-primary transition-colors duration-300">Sessions Management</h2>
        <div class="flex items-center space-x-4">
            <select wire:model.live="dateFilter"
                class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                <option value="this_week">This Week</option>
                <option value="this_month">This Month</option>
                <option value="this_quarter">This Quarter</option>
            </select>
        </div>
    </div>

    @if($sessions->count() > 0)
        <div class="grid gap-4">
            @foreach($sessions as $session)
                <div class="border border-themed-primary rounded-lg p-6 hover:shadow-md transition-all duration-300 bg-themed-secondary">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-accent-primary/20 text-accent-primary rounded-full flex items-center justify-center border border-accent-primary/30 transition-colors duration-300">
                                <i class="fas fa-{{ $session->type === 'code_review' ? 'code' : 'video' }}"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-themed-primary transition-colors duration-300">{{ $session->title }}</h3>
                                <p class="text-sm text-themed-secondary transition-colors duration-300">with {{ $session->mentorship->mentee->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-accent-primary transition-colors duration-300">{{ $session->scheduled_at->format('M j, Y') }}</p>
                            <p class="text-lg font-bold text-themed-primary transition-colors duration-300">{{ $session->scheduled_at->format('g:i A') }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-themed-secondary transition-colors duration-300">{{ $session->description }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-themed-secondary transition-colors duration-300">
                            <span><i class="fas fa-clock mr-1"></i>{{ $session->duration_minutes ?? 60 }} min</span>
                            <span><i class="fas fa-video mr-1"></i>{{ ucfirst($session->format ?? 'video') }}</span>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="viewSession({{ $session->id }})"
                                class="bg-themed-tertiary hover:bg-accent-primary/10 text-themed-primary hover:text-accent-primary px-4 py-2 rounded-lg text-sm transition-colors duration-300 border border-themed-primary">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                            @if($session->status === 'scheduled')
                                <button wire:click="completeSession({{ $session->id }})"
                                    class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Complete
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $sessions->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="text-6xl text-themed-tertiary mb-4 transition-colors duration-300">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3 class="text-xl font-semibold text-themed-primary mb-2 transition-colors duration-300">No sessions yet</h3>
            <p class="text-themed-secondary transition-colors duration-300">Schedule your first session with your mentees</p>
        </div>
    @endif
</div>