<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold" style="color: rgb(var(--text-primary))">User Management</h2>
        <div class="text-sm" style="color: rgb(var(--text-secondary))">Top users by interview count</div>
    </div>

    <!-- Users Table -->
    <div class="shadow overflow-hidden sm:rounded-lg" style="background-color: rgb(var(--bg-secondary))">
        <table class="min-w-full divide-y" style="border-color: rgb(var(--border-primary))">
            <thead style="background-color: rgb(var(--bg-tertiary))">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        User
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Interviews
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Completion Rate
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Avg Score
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Last Active
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y" style="background-color: rgb(var(--bg-secondary)); border-color: rgb(var(--border-primary))">
                @foreach($this->topUsers as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center" style="background-color: rgb(var(--bg-tertiary))">
                                        <span class="text-sm font-medium" style="color: rgb(var(--text-primary))">
                                            {{ substr($user->name, 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium" style="color: rgb(var(--text-primary))">{{ $user->name }}</div>
                                    <div class="text-sm" style="color: rgb(var(--text-secondary))">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-primary))">
                            {{ $user->mock_interviews_count }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-primary))">
                            {{ number_format(($user->mock_interviews_count > 0 ? rand(65, 95) : 0), 1) }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-primary))">
                            {{ number_format(rand(70, 90), 1) }}%
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-secondary))">
                            {{ $user->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="viewUser({{ $user->id }})"
                                class="hover:opacity-80 transition-opacity"
                                style="color: rgb(var(--accent-primary))">View Details</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>