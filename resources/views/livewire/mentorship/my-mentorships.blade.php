<div class="min-h-screen bg-themed-primary transition-colors duration-300">
    <!-- Header -->
    <div class="bg-themed-secondary shadow-lg border-b border-themed-primary transition-colors duration-300">
        <div class="px-6 py-8">
            <h1 class="text-4xl font-bold text-themed-primary mb-2 transition-colors duration-300">My Mentorships</h1>
            <p class="text-xl text-themed-secondary transition-colors duration-300">Manage your mentorship connections</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg animate-fade-in">
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg animate-fade-in">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="px-6 py-8">
        <!-- Filters -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6 mb-8 border border-themed-primary transition-colors duration-300">
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">View As</label>
                    <select wire:model.live="roleView"
                        class="w-full px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                        <option value="all">All Mentorships</option>
                        @if(auth()->user()->isMentor())
                            <option value="as_mentor">As Mentor</option>
                        @endif
                        @if(auth()->user()->isStudent())
                            <option value="as_mentee">As Mentee</option>
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Status</label>
                    <select wire:model.live="statusFilter"
                        class="w-full px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <div class="text-sm text-themed-secondary transition-colors duration-300">
                        <span class="bg-accent-primary/10 text-accent-primary px-3 py-2 rounded-lg border border-accent-primary/30 transition-colors duration-300">
                            {{ $mentorships->total() }} mentorship(s)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mentorships List -->
        @if($mentorships->count() > 0)
            <div class="grid gap-6">
                @foreach($mentorships as $mentorship)
                    <div class="bg-themed-secondary rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-themed-primary">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-accent-primary to-accent-secondary rounded-full flex items-center justify-center text-white font-bold text-xl">
                                        @if(auth()->id() === $mentorship->mentee_id)
                                            {{ substr($mentorship->mentor->name, 0, 1) }}
                                        @else
                                            {{ substr($mentorship->mentee->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-themed-primary transition-colors duration-300">
                                            @if(auth()->id() === $mentorship->mentee_id)
                                                {{ $mentorship->mentor->name }}
                                            @else
                                                {{ $mentorship->mentee->name }}
                                            @endif
                                        </h3>
                                        <p class="text-sm text-themed-secondary transition-colors duration-300">
                                            @if(auth()->id() === $mentorship->mentee_id)
                                                Your Mentor
                                            @else
                                                Your Mentee
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="bg-{{ $mentorship->status_color }}-100 dark:bg-{{ $mentorship->status_color }}-900/30 
                                               text-{{ $mentorship->status_color }}-800 dark:text-{{ $mentorship->status_color }}-300 
                                               px-3 py-1 rounded-full text-sm border border-{{ $mentorship->status_color }}-200 dark:border-{{ $mentorship->status_color }}-800 transition-colors duration-300">
                                        {{ $mentorship->status_label }}
                                    </span>
                                </div>
                            </div>

                            <!-- Goals -->
                            <div class="mb-4 rounded-lg border border-themed-primary bg-themed-primary p-4">
                                <div class="flex items-center justify-between gap-3 mb-3">
                                    <div>
                                        <h4 class="font-medium text-themed-primary transition-colors duration-300">Learner goals</h4>
                                        <p class="text-xs text-themed-secondary">{{ $mentorship->goal_completion_percentage }}% complete</p>
                                    </div>
                                    <button type="button" wire:click="editGoals({{ $mentorship->id }})"
                                        class="rounded-lg border border-themed-secondary px-3 py-2 text-xs font-semibold text-themed-primary hover:border-accent-themed-primary">
                                        <i class="fas fa-bullseye mr-1"></i>Manage
                                    </button>
                                </div>

                                @if($mentorship->goals_with_progress && count($mentorship->goals_with_progress) > 0)
                                    <div class="space-y-2">
                                        @foreach($mentorship->goals_with_progress as $goal)
                                            <button type="button" wire:click="toggleGoal({{ $mentorship->id }}, {{ $goal['index'] }})"
                                                class="flex w-full items-start gap-3 rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-left hover:border-accent-themed-primary">
                                                <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full {{ $goal['completed'] ? 'bg-green-600 text-white' : 'bg-themed-tertiary text-themed-secondary' }}">
                                                    <i class="fas {{ $goal['completed'] ? 'fa-check' : 'fa-circle' }} text-[10px]"></i>
                                                </span>
                                                <span class="{{ $goal['completed'] ? 'line-through text-themed-tertiary' : 'text-themed-secondary' }} text-sm">{{ $goal['text'] }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-themed-secondary">No learner goals have been defined yet.</p>
                                @endif
                            </div>

                            <!-- Progress Bar (for active mentorships) -->
                            @if($mentorship->isActive() && $mentorship->duration_weeks)
                                <div class="mb-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-themed-primary transition-colors duration-300">Progress</span>
                                        <span class="text-sm text-themed-secondary transition-colors duration-300">{{ $mentorship->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-themed-tertiary rounded-full h-2 transition-colors duration-300">
                                        <div class="bg-accent-primary h-2 rounded-full transition-colors duration-300" style="width: {{ $mentorship->progress_percentage }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Next Session -->
                            @if($mentorship->isActive() && $mentorship->next_session)
                                <div class="mb-4 p-3 bg-accent-primary/10 border border-accent-primary/30 rounded-lg transition-colors duration-300">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-accent-primary transition-colors duration-300">Next Session</p>
                                            <p class="text-xs text-accent-primary/80 transition-colors duration-300">{{ $mentorship->next_session->title }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-accent-primary transition-colors duration-300">
                                                {{ $mentorship->next_session->scheduled_at->format('M j') }}
                                            </p>
                                            <p class="text-xs text-accent-primary/80 transition-colors duration-300">
                                                {{ $mentorship->next_session->scheduled_at->format('g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Metadata -->
                            <div class="flex items-center justify-between text-sm text-themed-secondary mb-4 transition-colors duration-300">
                                <div class="flex items-center space-x-4">
                                    @if($mentorship->duration_weeks)
                                        <span><i class="fas fa-calendar mr-1"></i>{{ $mentorship->duration_formatted }}</span>
                                    @endif
                                    @if($mentorship->started_at)
                                        <span><i class="fas fa-clock mr-1"></i>Started {{ $mentorship->started_at->diffForHumans() }}</span>
                                    @else
                                        <span><i class="fas fa-clock mr-1"></i>Requested {{ $mentorship->requested_at->diffForHumans() }}</span>
                                    @endif
                                    @if($mentorship->is_paid)
                                        <span><i class="fas fa-dollar-sign mr-1"></i>${{ number_format($mentorship->hourly_rate, 0) }}/hr</span>
                                    @else
                                        <span class="text-green-600 dark:text-green-400"><i class="fas fa-gift mr-1"></i>Free</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex space-x-2">
                                @if($mentorship->isPending() && auth()->id() === $mentorship->mentor_id)
                                    <button wire:click="acceptMentorship({{ $mentorship->id }})"
                                        class="flex-1 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                        <i class="fas fa-check mr-1"></i>Accept
                                    </button>
                                    <button wire:click="rejectMentorship({{ $mentorship->id }})"
                                        class="flex-1 bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                        <i class="fas fa-times mr-1"></i>Reject
                                    </button>
                                @elseif($mentorship->isActive())
                                    <a href="{{ route('mentorship.sessions') }}"
                                       class="flex-1 bg-accent-primary hover:bg-accent-secondary text-white px-4 py-2 rounded-lg text-sm transition-colors text-center">
                                        <i class="fas fa-calendar-plus mr-1"></i>Schedule Session
                                    </a>
                                    <a href="{{ route('mentorship.code-reviews') }}"
                                       class="flex-1 bg-purple-600 hover:bg-purple-700 dark:bg-purple-500 dark:hover:bg-purple-600 text-white px-4 py-2 rounded-lg text-sm transition-colors text-center">
                                        <i class="fas fa-code mr-1"></i>Request Review
                                    </a>
                                    @if(auth()->id() === $mentorship->mentor_id)
                                        <button wire:click="completeMentorship({{ $mentorship->id }})"
                                            class="bg-themed-tertiary hover:bg-themed-secondary text-themed-primary px-4 py-2 rounded-lg text-sm transition-colors border border-themed-primary">
                                            <i class="fas fa-flag-checkered mr-1"></i>Complete
                                        </button>
                                    @endif
                                @elseif($mentorship->isPending() && auth()->id() === $mentorship->mentee_id)
                                    <button wire:click="cancelMentorship({{ $mentorship->id }})"
                                        class="bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                        <i class="fas fa-times mr-1"></i>Cancel Request
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $mentorships->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary transition-colors duration-300">
                <div class="text-6xl text-themed-tertiary mb-4 transition-colors duration-300">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3 class="text-xl font-semibold text-themed-primary mb-2 transition-colors duration-300">No mentorships yet</h3>
                <p class="text-themed-secondary mb-6 transition-colors duration-300">
                    @if(auth()->user()->isStudent())
                        Start your journey by finding a mentor
                    @else
                        Your mentorships will appear here once you accept requests
                    @endif
                </p>
                @if(auth()->user()->isStudent())
                    <a href="{{ route('mentorship.find') }}"
                       class="inline-block bg-accent-primary hover:bg-accent-secondary text-white px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-search mr-2"></i>Find a Mentor
                    </a>
                @endif
            </div>
        @endif
    </div>

    @if($showGoalsModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="w-full max-w-2xl rounded-lg border border-themed-primary bg-themed-secondary shadow-xl">
                <div class="flex items-center justify-between border-b border-themed-primary p-5">
                    <h2 class="text-xl font-bold text-themed-primary">Manage learner goals</h2>
                    <button type="button" wire:click="closeGoalsModal" class="grid h-9 w-9 place-items-center rounded-lg bg-themed-tertiary text-themed-secondary">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveGoals" class="space-y-4 p-5">
                    @foreach($goalInputs as $index => $goal)
                        <div class="flex gap-2">
                            <input type="text" wire:model="goalInputs.{{ $index }}"
                                class="flex-1 rounded-lg border border-themed-primary bg-themed-primary px-3 py-2 text-themed-primary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary"
                                placeholder="Learner goal">
                            <button type="button" wire:click="removeGoal({{ $index }})"
                                class="grid h-10 w-10 place-items-center rounded-lg bg-red-600 text-white">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addGoal"
                        class="inline-flex items-center gap-2 rounded-lg border border-themed-primary px-3 py-2 text-sm font-medium text-themed-primary">
                        <i class="fas fa-plus"></i>
                        Add goal
                    </button>

                    <div class="flex justify-end gap-3 border-t border-themed-primary pt-5">
                        <button type="button" wire:click="closeGoalsModal"
                            class="rounded-lg border border-themed-primary px-4 py-2 text-sm font-medium text-themed-primary hover:bg-themed-tertiary">
                            Cancel
                        </button>
                        <button type="submit"
                            class="rounded-lg bg-accent-primary px-4 py-2 text-sm font-semibold text-white hover:bg-accent-secondary">
                            Save goals
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 dark:bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-themed-secondary rounded-lg p-6 shadow-xl border border-themed-primary transition-colors duration-300">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-6 w-6 text-accent-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-themed-primary font-medium transition-colors duration-300">Loading...</span>
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</div>
