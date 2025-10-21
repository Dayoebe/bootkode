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
                            @if($mentorship->goals && count($mentorship->goals) > 0)
                                <div class="mb-4">
                                    <h4 class="font-medium text-themed-primary mb-2 transition-colors duration-300">Goals:</h4>
                                    <ul class="list-disc list-inside text-sm text-themed-secondary space-y-1 transition-colors duration-300">
                                        @foreach($mentorship->goals as $goal)
                                            <li>{{ $goal }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

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