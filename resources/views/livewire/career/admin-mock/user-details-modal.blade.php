@if($showUserModal && $selectedUser)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" wire:key="user-modal-{{ $selectedUser->id }}">
        <div class="rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden bg-themed-secondary border border-themed-primary">
            <div class="px-6 py-4 border-b border-themed-primary bg-themed-tertiary flex items-center justify-between">
                <h3 class="text-lg font-medium text-themed-primary">User Details - {{ $selectedUser->name }}</h3>
                <button wire:click="$set('showUserModal', false)" class="text-themed-secondary hover:text-themed-primary transition-colors">
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
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <div class="text-2xl font-bold text-blue-600">{{ $userStats['total_interviews'] }}</div>
                        <div class="text-sm text-blue-700">Total Interviews</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($userStats['average_score'], 1) }}%</div>
                        <div class="text-sm text-green-700">Average Score</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ $userStats['total_interviews'] > 0 ? number_format(($userStats['completed_interviews'] / $userStats['total_interviews']) * 100, 1) : 0 }}%
                        </div>
                        <div class="text-sm text-purple-700">Completion Rate</div>
                    </div>
                </div>

                <!-- Recent Interviews -->
                <div>
                    <h4 class="text-md font-medium mb-4 text-themed-primary">Recent Interviews</h4>
                    <div class="overflow-hidden shadow ring-1 ring-themed-primary md:rounded-lg">
                        <table class="min-w-full divide-y divide-themed-primary">
                            <thead class="bg-themed-tertiary">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                                        Interview
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                                        Score
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                                        Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-themed-primary bg-themed-secondary">
                                @foreach($selectedUser->mockInterviews as $interview)
                                    <tr class="hover:bg-themed-tertiary transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-themed-primary">
                                            {{ $interview->title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-secondary">
                                            {{ $interview->type_label }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                {{ $interview->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $interview->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $interview->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $interview->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ $interview->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary">
                                            {{ $interview->overall_score ? number_format($interview->overall_score, 1) . '%' : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-secondary">
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
            <div class="px-6 py-4 border-t border-themed-primary bg-themed-tertiary flex justify-end">
                <button wire:click="$set('showUserModal', false)"
                    class="px-4 py-2 border border-themed-primary rounded-md hover:bg-themed-secondary transition-colors text-themed-primary font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>
@endif