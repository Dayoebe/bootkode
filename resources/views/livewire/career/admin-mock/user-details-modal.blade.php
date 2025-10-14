@if($showUserModal && $selectedUser)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" wire:key="user-modal-{{ $selectedUser->id }}">
        <div class="rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden" style="background-color: rgb(var(--bg-secondary))">
            <div class="px-6 py-4 border-b flex items-center justify-between" style="border-color: rgb(var(--border-primary))">
                <h3 class="text-lg font-medium" style="color: rgb(var(--text-primary))">User Details - {{ $selectedUser->name }}</h3>
                <button wire:click="$set('showUserModal', false)" class="hover:opacity-70 transition-opacity" style="color: rgb(var(--text-tertiary))">
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
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-blue-600">{{ $userStats['total_interviews'] }}</div>
                        <div class="text-sm" style="color: rgb(var(--text-secondary))">Total Interviews</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($userStats['average_score'], 1) }}%</div>
                        <div class="text-sm" style="color: rgb(var(--text-secondary))">Average Score</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="text-2xl font-bold text-purple-600">
                            {{ $userStats['total_interviews'] > 0 ? number_format(($userStats['completed_interviews'] / $userStats['total_interviews']) * 100, 1) : 0 }}%
                        </div>
                        <div class="text-sm" style="color: rgb(var(--text-secondary))">Completion Rate</div>
                    </div>
                </div>

                <!-- Recent Interviews -->
                <div>
                    <h4 class="text-md font-medium mb-4" style="color: rgb(var(--text-primary))">Recent Interviews</h4>
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full divide-y" style="border-color: rgb(var(--border-primary))">
                            <thead style="background-color: rgb(var(--bg-tertiary))">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                                        Interview
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                                        Type
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                                        Score
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                                        Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="background-color: rgb(var(--bg-secondary)); border-color: rgb(var(--border-primary))">
                                @foreach($selectedUser->mockInterviews as $interview)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" style="color: rgb(var(--text-primary))">
                                            {{ $interview->title }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-secondary))">
                                            {{ $interview->type_label }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $interview->getStatusColor() }}-100 text-{{ $interview->getStatusColor() }}-800">
                                                {{ $interview->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-primary))">
                                            {{ $interview->overall_score ? number_format($interview->overall_score, 1) . '%' : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-secondary))">
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
            <div class="px-6 py-4 border-t flex justify-end" style="border-color: rgb(var(--border-primary))">
                <button wire:click="$set('showUserModal', false)"
                    class="px-4 py-2 border rounded-md hover:opacity-80 transition-opacity"
                    style="border-color: rgb(var(--border-primary)); color: rgb(var(--text-primary)); background-color: rgb(var(--bg-tertiary))">
                    Close
                </button>
            </div>
        </div>
    </div>
@endif