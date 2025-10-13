@if($showUserModal && $selectedUser)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 flex items-center justify-center p-4 z-50" wire:key="user-modal-{{ $selectedUser->id }}">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">User Details - {{ $selectedUser->name }}</h3>
                <button wire:click="$set('showUserModal', false)" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                @php
                    $userStats = [
                        'total_interviews' => $selectedUser->mockInterviews()->count(),
                        'completed_interviews' => $selectedUser->mockInterviews()->completed()->count(),
                        'average_score' => $selectedUser->mockInterviews()->completed()->avg('overall_score') ?? 0,
                    ];
                @endphp

                <!-- User Performance Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $userStats['total_interviews'] }}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total Interviews</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($userStats['average_score'], 1) }}%</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Average Score</div>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ $userStats['total_interviews'] > 0 ? number_format(($userStats['completed_interviews'] / $userStats['total_interviews']) * 100, 1) : 0 }}%
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Completion Rate</div>
                    </div>
                </div>

                <!-- Recent Interviews -->
                <div>
                    <h4 class="text-md font-medium text-gray-900 dark:text-white mb-4">Recent Interviews</h4>
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 dark:ring-gray-700 md:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Interview
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Score
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                @foreach($selectedUser->mockInterviews as $interview)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $interview->title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $interview->type_label }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $interview->getStatusColor() }}-100 dark:bg-{{ $interview->getStatusColor() }}-900/30 text-{{ $interview->getStatusColor() }}-800 dark:text-{{ $interview->getStatusColor() }}-400">
                                                {{ $interview->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $interview->overall_score ? number_format($interview->overall_score, 1) . '%' : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $interview->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button wire:click="$set('showUserModal', false)"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                    Close
                </button>
            </div>
        </div>
    </div>
@endif