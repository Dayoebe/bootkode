{{-- FILE: resources/views/livewire/mentorship/partial/sessions.blade.php --}}

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sessions Management</h2>
        <div class="flex items-center space-x-4">
            <select wire:model.live="dateFilter"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="this_week">This Week</option>
                <option value="this_month">This Month</option>
                <option value="this_quarter">This Quarter</option>
            </select>
        </div>
    </div>

    @if($sessions->count() > 0)
        <div class="grid gap-4">
            @foreach($sessions as $session)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow bg-white dark:bg-gray-800">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center border border-blue-200 dark:border-blue-800">
                                <i class="fas fa-{{ $session->type === 'code_review' ? 'code' : 'video' }}"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $session->title }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">with {{ $session->mentorship->mentee->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ $session->scheduled_at->format('M j, Y') }}</p>
                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $session->scheduled_at->format('g:i A') }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $session->description }}</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                            <span><i class="fas fa-clock mr-1"></i>{{ $session->duration_minutes ?? 60 }} min</span>
                            <span><i class="fas fa-video mr-1"></i>{{ ucfirst($session->format ?? 'video') }}</span>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="viewSession({{ $session->id }})"
                                class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors border border-gray-200 dark:border-gray-600">
                                <i class="fas fa-eye mr-1"></i>View
                            </button>
                            @if($session->status === 'scheduled')
                                <button wire:click="completeSession({{ $session->id }})"
                                    class="bg-green-600 dark:bg-green-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 dark:hover:bg-green-600 transition-colors">
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
            <div class="text-6xl text-gray-400 dark:text-gray-600 mb-4">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No sessions yet</h3>
            <p class="text-gray-500 dark:text-gray-400">Schedule your first session with your mentees</p>
        </div>
    @endif
</div>