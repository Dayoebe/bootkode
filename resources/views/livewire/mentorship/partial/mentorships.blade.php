<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Mentees</h2>
        <div class="flex items-center space-x-4">
            <select wire:model.live="statusFilter"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($mentorship->mentee->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $mentorship->mentee->name }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $mentorship->mentee->email }}</p>
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

                    <div class="mb-4">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Goals:</h4>
                        <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            @foreach($mentorship->goals ?? [] as $goal)
                                <li>{{ $goal }}</li>
                            @endforeach
                        </ul>
                    </div>

                    @if($mentorship->isActive())
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

                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span>Duration: {{ $mentorship->duration_formatted }}</span>
                            @if($mentorship->started_at)
                                <span class="mx-2">•</span>
                                <span>Started: {{ $mentorship->started_at->format('M j, Y') }}</span>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            @if($mentorship->isPending())
                                <button wire:click="acceptMentorship({{ $mentorship->id }})"
                                    class="bg-green-600 dark:bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 dark:hover:bg-green-600 transition-colors">
                                    Accept
                                </button>
                                <button wire:click="rejectMentorship({{ $mentorship->id }})"
                                    class="bg-red-600 dark:bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 dark:hover:bg-red-600 transition-colors">
                                    Reject
                                </button>
                            @elseif($mentorship->isActive())
                                <button wire:click="completeMentorship({{ $mentorship->id }})"
                                    class="bg-blue-600 dark:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
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
            <div class="text-6xl text-gray-400 dark:text-gray-600 mb-4">
                <i class="fas fa-users"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No mentees yet</h3>
            <p class="text-gray-500 dark:text-gray-400">Your mentees will appear here once you accept requests</p>
        </div>
    @endif
</div>