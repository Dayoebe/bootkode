<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700">
        <div class="px-6 py-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">My Mentorships</h1>
            <p class="text-xl text-gray-600 dark:text-gray-400">Manage your mentorship connections</p>
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
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8">
            <div class="grid md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">View As</label>
                    <select wire:model.live="roleView"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select wire:model.live="statusFilter"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-3 py-2 rounded-lg border border-blue-200 dark:border-blue-800">
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
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                        @if(auth()->id() === $mentorship->mentee_id)
                                            {{ substr($mentorship->mentor->name, 0, 1) }}
                                        @else
                                            {{ substr($mentorship->mentee->name, 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            @if(auth()->id() === $mentorship->mentee_id)
                                                {{ $mentorship->mentor->name }}
                                            @else
                                                {{ $mentorship->mentee->name }}
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
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
                                               px-3 py-1 rounded-full text-sm border border-{{ $mentorship->status_color }}-200 dark:border-{{ $mentorship->status_color }}-800">
                                        {{ $mentorship->status_label }}
                                    </span>
                                </div>
                            </div>

                            <!-- Goals -->
                            @if($mentorship->goals && count($mentorship->goals) > 0)
                                <div class="mb-4">
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Goals:</h4>
                                    <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1">
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
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $mentorship->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full" style="width: {{ $mentorship->progress_percentage }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Next Session -->
                            @if($mentorship->isActive() && $mentorship->next_session)
                                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-blue-900 dark:text-blue-300">Next Session</p>
                                            <p class="text-xs text-blue-700 dark:text-blue-400">{{ $mentorship->next_session->title }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-bold text-blue-900 dark:text-blue-300">
                                                {{ $mentorship->next_session->scheduled_at->format('M j') }}
                                            </p>
                                            <p class="text-xs text-blue-700 dark:text-blue-400">
                                                {{ $mentorship->next_session->scheduled_at->format('g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Metadata -->
                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400 mb-4">
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
                                        class="flex-1 bg-green-600 dark:bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 dark:hover:bg-green-600 transition-colors">
                                        <i class="fas fa-check mr-1"></i>Accept
                                    </button>
                                    <button wire:click="rejectMentorship({{ $mentorship->id }})"
                                        class="flex-1 bg-red-600 dark:bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 dark:hover:bg-red-600 transition-colors">
                                        <i class="fas fa-times mr-1"></i>Reject
                                    </button>
                                @elseif($mentorship->isActive())
                                    <a href="{{ route('mentorship.sessions') }}"
                                       class="flex-1 bg-blue-600 dark:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors text-center">
                                        <i class="fas fa-calendar-plus mr-1"></i>Schedule Session
                                    </a>
                                    <a href="{{ route('mentorship.code-reviews') }}"
                                       class="flex-1 bg-purple-600 dark:bg-purple-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-purple-700 dark:hover:bg-purple-600 transition-colors text-center">
                                        <i class="fas fa-code mr-1"></i>Request Review
                                    </a>
                                    @if(auth()->id() === $mentorship->mentor_id)
                                        <button wire:click="completeMentorship({{ $mentorship->id }})"
                                            class="bg-gray-600 dark:bg-gray-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                                            <i class="fas fa-flag-checkered mr-1"></i>Complete
                                        </button>
                                    @endif
                                @elseif($mentorship->isPending() && auth()->id() === $mentorship->mentee_id)
                                    <button wire:click="cancelMentorship({{ $mentorship->id }})"
                                        class="bg-red-600 dark:bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 dark:hover:bg-red-600 transition-colors">
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
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl shadow-lg">
                <div class="text-6xl text-gray-400 dark:text-gray-600 mb-4">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No mentorships yet</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">
                    @if(auth()->user()->isStudent())
                        Start your journey by finding a mentor
                    @else
                        Your mentorships will appear here once you accept requests
                    @endif
                </p>
                @if(auth()->user()->isStudent())
                    <a href="{{ route('mentorship.find') }}"
                       class="inline-block bg-blue-600 dark:bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                        <i class="fas fa-search mr-2"></i>Find a Mentor
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Loading Indicator -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 dark:bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-6 w-6 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700 dark:text-gray-300 font-medium">Loading...</span>
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