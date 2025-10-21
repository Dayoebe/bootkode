<div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-themed-primary transition-colors duration-300">My Mentees</h2>
        <div class="flex items-center space-x-4">
            <select wire:model.live="statusFilter"
                class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
            </select>
        </div>
    </div>
    @if(count($mentorships) > 0)
        <div class="grid gap-6">
            @foreach($mentorships as $mentorship)
                <div
                    class="border border-themed-primary rounded-lg p-6 hover:shadow-md transition-all duration-300 bg-themed-secondary">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-accent-primary to-accent-secondary rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($mentorship->mentee->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-themed-primary transition-colors duration-300">
                                    {{ $mentorship->mentee->name }}</h3>
                                <p class="text-sm text-themed-secondary transition-colors duration-300">
                                    {{ $mentorship->mentee->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span
                                class="bg-{{ $mentorship->status_color }}-100 dark:bg-{{ $mentorship->status_color }}-900/30 
                                           text-{{ $mentorship->status_color }}-800 dark:text-{{ $mentorship->status_color }}-300 
                                           px-3 py-1 rounded-full text-sm border border-{{ $mentorship->status_color }}-200 dark:border-{{ $mentorship->status_color }}-800 transition-colors duration-300">
                                {{ $mentorship->status_label }}
                            </span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4 class="font-medium text-themed-primary mb-2 transition-colors duration-300">Goals:</h4>
                        <ul
                            class="list-disc list-inside text-sm text-themed-secondary space-y-1 transition-colors duration-300">
                            @foreach($mentorship->goals ?? [] as $goal)
                                <li>{{ $goal }}</li>
                            @endforeach
                        </ul>
                    </div>

                    @if($mentorship->isActive())
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-themed-primary transition-colors duration-300">Progress</span>
                                <span
                                    class="text-sm text-themed-secondary transition-colors duration-300">{{ $mentorship->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-themed-tertiary rounded-full h-2 transition-colors duration-300">
                                <div class="bg-accent-primary h-2 rounded-full transition-colors duration-300"
                                    style="width: {{ $mentorship->progress_percentage }}%"></div>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-themed-secondary transition-colors duration-300">
                            <span>Duration: {{ $mentorship->duration_formatted }}</span>
                            @if($mentorship->started_at)
                                <span class="mx-2">•</span>
                                <span>Started: {{ $mentorship->started_at->format('M j, Y') }}</span>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            @if($mentorship->isPending())
                                <button wire:click="acceptMentorship({{ $mentorship->id }})"
                                    class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Accept
                                </button>
                                <button wire:click="rejectMentorship({{ $mentorship->id }})"
                                    class="bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Reject
                                </button>
                            @elseif($mentorship->isActive())
                                <button wire:click="completeMentorship({{ $mentorship->id }})"
                                    class="bg-accent-primary hover:bg-accent-secondary text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Complete
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $mentorships->links() }}
        </div>
    @else
        <div class="text-center py-16">
            <div class="text-6xl text-themed-tertiary mb-4 transition-colors duration-300">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="text-xl font-semibold text-themed-primary mb-2 transition-colors duration-300">No mentees yet</h3>
            <p class="text-themed-secondary transition-colors duration-300">Your mentees will appear here once you accept
                requests</p>
        </div>
    @endif
</div>