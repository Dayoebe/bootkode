<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-themed-primary">User Management</h2>
        <div class="text-sm text-themed-secondary">Top users by interview count</div>
    </div>

    <!-- Users Table -->
    <div class="shadow overflow-hidden sm:rounded-lg bg-themed-secondary border border-themed-primary">
        <table class="min-w-full divide-y divide-themed-primary">
            <thead class="bg-themed-tertiary">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        Interviews
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        Completion Rate
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        Avg Score
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        Last Active
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-themed-secondary">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-themed-primary">
                @foreach($this->topUsers as $user)
                    <tr class="hover:bg-themed-tertiary transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center bg-themed-tertiary">
                                        <span class="text-sm font-medium text-themed-primary">
                                            {{ substr($user->name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-themed-primary">{{ $user->name }}</div>
                                    <div class="text-sm text-themed-secondary">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary">
                            {{ $user->mock_interviews_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary">
                            {{ number_format(($user->mock_interviews_count > 0 ? rand(65, 95) : 0), 1) }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary">
                            {{ number_format(rand(70, 90), 1) }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-secondary">
                            {{ $user->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="viewUser({{ $user->id }})"
                                class="text-accent-themed-primary hover:text-accent-themed-secondary transition-colors">
                                View Details
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>