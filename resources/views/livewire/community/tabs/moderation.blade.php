{{-- resources/views/livewire/community/tabs/moderation.blade.php --}}
<div>
    <!-- Moderation Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4">
            <div class="text-3xl font-bold text-red-400 mb-1">{{ $pendingReports ?? 0 }}</div>
            <div class="text-sm text-themed-secondary">Pending Reports</div>
        </div>
        <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4">
            <div class="text-3xl font-bold text-yellow-400 mb-1">{{ $pendingFeedback ?? 0 }}</div>
            <div class="text-sm text-themed-secondary">Pending Feedback</div>
        </div>
        <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4">
            <div class="text-3xl font-bold text-brand mb-1">{{ $flaggedThreads ?? 0 }}</div>
            <div class="text-sm text-themed-secondary">Flagged Threads</div>
        </div>
        <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4">
            <div class="text-3xl font-bold text-brand mb-1">{{ $blockedUsers ?? 0 }}</div>
            <div class="text-sm text-themed-secondary">Blocked Users</div>
        </div>
    </div>

    <!-- Moderation Tabs -->
    <div class="bg-themed-secondary border-b border-themed-primary mb-6">
        <div class="flex gap-2 overflow-x-auto px-4">
            <button wire:click="setModerationTab('reports')"
                class="px-4 py-3 font-medium text-sm transition-all whitespace-nowrap {{ $moderationTab === 'reports' ? 'border-b-2 border-red-500 text-red-500' : 'border-b-2 border-transparent text-themed-secondary hover:text-themed-primary' }}">
                <i aria-hidden="true" class="fas fa-flag mr-2"></i>Community Reports
            </button>
            <button wire:click="setModerationTab('feedback')"
                class="px-4 py-3 font-medium text-sm transition-all whitespace-nowrap {{ $moderationTab === 'feedback' ? 'border-b-2 border-yellow-500 text-yellow-500' : 'border-b-2 border-transparent text-themed-secondary hover:text-themed-primary' }}">
                <i aria-hidden="true" class="fas fa-comment mr-2"></i>User Feedback
            </button>
            <button wire:click="setModerationTab('flagged')"
                class="px-4 py-3 font-medium text-sm transition-all whitespace-nowrap {{ $moderationTab === 'flagged' ? 'border-b-2 border-brand text-blue-500' : 'border-b-2 border-transparent text-themed-secondary hover:text-themed-primary' }}">
                <i aria-hidden="true" class="fas fa-exclamation-triangle mr-2"></i>Flagged Content
            </button>
            <button wire:click="setModerationTab('users')"
                class="px-4 py-3 font-medium text-sm transition-all whitespace-nowrap {{ $moderationTab === 'users' ? 'border-b-2 border-brand text-purple-500' : 'border-b-2 border-transparent text-themed-secondary hover:text-themed-primary' }}">
                <i aria-hidden="true" class="fas fa-users-slash mr-2"></i>Manage Users
            </button>
        </div>
    </div>

    <!-- Reports Tab -->
    @if($moderationTab === 'reports')
        <div class="space-y-3">
            <div class="mb-4 flex gap-2">
                <select aria-label="Report status filter" wire:model.live="reportStatusFilter"
                    class="bg-themed-secondary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="reviewed">Reviewed</option>
                    <option value="resolved">Resolved</option>
                    <option value="dismissed">Dismissed</option>
                </select>
            </div>

            @forelse($communityReports ?? [] as $report)
                <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-semibold text-themed-primary">Report #{{ $report->id }}</span>
                                <span
                                    class="text-xs bg-{{ $report->getStatusColorAttribute() }}-100/20 text-{{ $report->getStatusColorAttribute() }}-400 px-2 py-1 rounded border border-{{ $report->getStatusColorAttribute() }}-500/30">
                                    {{ ucfirst($report->status) }}
                                </span>
                                <span class="text-xs bg-red-100/20 text-red-400 px-2 py-1 rounded border border-red-500/30">
                                    {{ ucfirst($report->reason) }}
                                </span>
                            </div>
                            <p class="text-sm text-themed-secondary mb-2">Reported by:
                                <strong>{{ $report->reporter->name }}</strong></p>
                        </div>
                    </div>

                    <p class="text-sm text-themed-secondary mb-3 line-clamp-3">{{ $report->description }}</p>

                    <div class="text-xs text-themed-secondary mb-3 pb-3 border-b border-themed-primary">
                        <i aria-hidden="true" class="fas fa-clock mr-1"></i>{{ $report->created_at->diffForHumans() }}
                    </div>

                    @if($report->status === 'pending')
                        <div class="flex gap-2">
                            <button wire:click="reviewReport({{ $report->id }})"
                                class="flex-1 bg-action hover:bg-action text-action-foreground px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i aria-hidden="true" class="fas fa-eye mr-1"></i>Review
                            </button>
                            <button wire:click="dismissReport({{ $report->id }})"
                                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i aria-hidden="true" class="fas fa-times mr-1"></i>Dismiss
                            </button>
                        </div>
                    @else
                        <div class="text-xs text-themed-secondary">
                            <p class="mb-2"><strong>Moderator:</strong> {{ $report->moderator?->name ?? 'N/A' }}</p>
                            <p><strong>Notes:</strong> {{ $report->moderator_notes ?? 'No notes' }}</p>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 bg-themed-secondary border border-themed-primary rounded-lg">
                    <i aria-hidden="true" class="fas fa-inbox text-themed-secondary text-4xl mb-3 block"></i>
                    <h3 class="text-lg font-semibold text-themed-primary mb-1">No reports</h3>
                    <p class="text-themed-secondary">All community reports have been addressed!</p>
                </div>
            @endforelse
        </div>
    @endif

    <!-- Feedback Tab -->
    @if($moderationTab === 'feedback')
        <div class="space-y-3">
            <div class="mb-4 flex gap-2">
                <select aria-label="Feedback status filter" wire:model.live="feedbackStatusFilter"
                    class="bg-themed-secondary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 text-sm">
                    <option value="">All Statuses</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            @forelse($feedbackItems ?? [] as $feedback)
                <section id="staff-feedback-{{ $feedback->id }}" tabindex="-1" wire:key="staff-feedback-{{ $feedback->id }}" class="space-y-3 rounded-lg border border-line bg-surface p-4">
                    <h3 class="break-words font-semibold text-content">{{ $feedback->subject }}</h3><p class="text-sm text-content-muted">{{ $feedback->user->name }} · {{ ucfirst(str_replace('_', ' ', $feedback->status)) }} · {{ ucfirst($feedback->priority) }} priority</p><p class="whitespace-pre-wrap break-words text-content">{{ $feedback->message }}</p>
                    @if($feedback->admin_response)<div class="space-y-2 border-t border-line pt-3"><h4 class="font-semibold text-content">Recorded community reply</h4><p class="whitespace-pre-wrap break-words text-content">{{ $feedback->admin_response }}</p><p class="text-sm text-content-muted">{{ $feedback->responded_at?->format('M j, Y H:i') }}</p></div>@endif
                    <div class="flex flex-wrap gap-3">
                        @if($feedback->status === 'open')<x-secondary-button wire:click="assignFeedback({{ $feedback->id }})">Assign feedback to me</x-secondary-button>@endif
                        @if(!$feedback->admin_response && $feedback->status !== 'closed')<x-secondary-button wire:click="respondToFeedback({{ $feedback->id }})" wire:loading.class="opacity-60" wire:target="respondToFeedback">Reply to requester</x-secondary-button>@endif
                        @if(!in_array($feedback->status, ['resolved', 'closed']))<x-secondary-button wire:click="resolveFeedback({{ $feedback->id }})">Resolve feedback</x-secondary-button>@endif
                    </div>
                </section>
            @empty
                <div class="text-center py-12 bg-themed-secondary border border-themed-primary rounded-lg">
                    <i aria-hidden="true" class="fas fa-comments text-themed-secondary text-4xl mb-3 block"></i>
                    <h3 class="text-lg font-semibold text-themed-primary mb-1">No feedback</h3>
                    <p class="text-themed-secondary">All user feedback has been addressed!</p>
                </div>
            @endforelse
        </div>
    @endif

    <!-- Flagged Content Tab -->
    @if($moderationTab === 'flagged')
        <div class="space-y-3">
            @forelse($flaggedThreadsList ?? [] as $thread)
                <div class="bg-themed-secondary border border-yellow-500/50 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="font-semibold text-themed-primary flex-1 line-clamp-2">{{ $thread->title }}</h3>
                        <span class="text-yellow-400 text-sm font-medium whitespace-nowrap ml-2">
                            <i aria-hidden="true" class="fas fa-flag mr-1"></i>Flagged
                        </span>
                    </div>

                    <p class="text-sm text-themed-secondary mb-2 line-clamp-2">
                        {{ Str::limit(strip_tags($thread->content), 150) }}</p>

                    <div class="text-xs text-themed-secondary mb-3 pb-3 border-b border-themed-primary">
                        <i aria-hidden="true" class="fas fa-user mr-1"></i>{{ $thread->user->name }}
                        <i aria-hidden="true" class="fas fa-clock ml-3 mr-1"></i>{{ $thread->created_at->diffForHumans() }}
                    </div>

                    <div class="flex gap-2">
                        <button wire:click="unflagThread({{ $thread->id }})"
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i aria-hidden="true" class="fas fa-check mr-1"></i>Approve
                        </button>
                        <button wire:confirm="Remove this discussion and its replies?" wire:click="removeThread({{ $thread->id }})"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i aria-hidden="true" class="fas fa-trash mr-1"></i>Remove
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-themed-secondary border border-themed-primary rounded-lg">
                    <i aria-hidden="true" class="fas fa-check-circle text-themed-secondary text-4xl mb-3 block"></i>
                    <h3 class="text-lg font-semibold text-themed-primary mb-1">No flagged content</h3>
                    <p class="text-themed-secondary">All content is in good standing!</p>
                </div>
            @endforelse
        </div>
    @endif

    <!-- Users Tab -->
    <!-- Users Tab -->
    @if($moderationTab === 'users')
        <div class="space-y-3">
            <div class="mb-4">
                <input aria-label="Search users" type="text" wire:model.live.debounce="userSearch"
                    class="w-full pl-10 pr-4 py-2 bg-themed-secondary border border-themed-primary text-themed-primary rounded-lg placeholder-themed-secondary focus:ring-2 focus:ring-brand/30 focus:border-transparent"
                    placeholder="Search users...">
            </div>

            @forelse($managedUsers ?? [] as $user)
                <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <img src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) }}"
                            alt="{{ $user->name }}" class="w-10 h-10 rounded-full">
                        <div class="flex-1">
                            <h3 class="font-semibold text-themed-primary">{{ $user->name }}</h3>
                            <p class="text-xs text-themed-secondary">{{ $user->email }}</p>
                        </div>
                        @if(!$user->is_active)
                            <span class="text-xs bg-red-100/20 text-red-400 px-2 py-1 rounded border border-red-500/30">
                                Blocked
                            </span>
                        @endif
                    </div>

                    <div class="text-xs text-themed-secondary mb-3 pb-3 border-b border-themed-primary">
                        <p><strong>Role:</strong> {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                        <p><strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="flex gap-2">
                        @if($user->is_active)
                            <button wire:confirm="Block this account?" wire:click="blockUser({{ $user->id }})"
                                class="flex-1 bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i aria-hidden="true" class="fas fa-ban mr-1"></i>Block User
                            </button>
                        @else
                            <button wire:confirm="Restore this account?" wire:click="unblockUser({{ $user->id }})"
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                                <i aria-hidden="true" class="fas fa-check mr-1"></i>Unblock User
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-themed-secondary border border-themed-primary rounded-lg">
                    <i aria-hidden="true" class="fas fa-users text-themed-secondary text-4xl mb-3 block"></i>
                    <h3 class="text-lg font-semibold text-themed-primary mb-1">No users found</h3>
                    <p class="text-themed-secondary">Try adjusting your search filters.</p>
                </div>
            @endforelse
        </div>
    @endif
</div>
