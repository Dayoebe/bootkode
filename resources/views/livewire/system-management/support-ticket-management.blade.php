<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-cyan-600 to-blue-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-ticket-alt mr-2"></i> Support Ticket Management
        </h1>
        <p class="text-cyan-100 mt-2">Manage and resolve user support tickets</p>
    </div>

    <!-- Search & Filter Section -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <label for="search" class="block text-sm font-medium text-themed-primary mb-2">Search</label>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-3 text-themed-secondary"></i>
                <input wire:model.live.debounce.300ms="search" type="text" id="search"
                       placeholder="Search tickets by ID, subject, or user..."
                       class="w-full pl-10 pr-4 py-2 border border-themed-primary bg-themed-tertiary rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-themed-primary transition-colors duration-200">
            </div>
        </div>
        <div class="flex-1">
            <label for="status_filter" class="block text-sm font-medium text-themed-primary mb-2">Filter by Status</label>
            <select wire:model.live="statusFilter" id="status_filter"
                    class="w-full px-4 py-2 border border-themed-primary bg-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-themed-primary transition-colors duration-200">
                <option value="all">All Tickets</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
            </select>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-themed-secondary shadow rounded-lg overflow-hidden border border-themed-primary transition-colors duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-themed-primary">
                <thead class="bg-themed-tertiary">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Ticket ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-themed-primary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-themed-primary">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-themed-tertiary transition-colors duration-200 animate__animated animate__fadeInUp">
                            <td class="px-6 py-4 text-sm">
                                <code class="px-2 py-1 rounded bg-themed-tertiary text-themed-primary font-mono text-xs">
                                    #{{ $ticket->id }}
                                </code>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="font-medium text-themed-primary">{{ $ticket->user->name }}</div>
                                <div class="text-themed-secondary text-xs">{{ $ticket->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <p class="font-medium text-themed-primary max-w-xs truncate">{{ $ticket->subject }}</p>
                                <p class="text-themed-secondary text-xs max-w-xs truncate">{{ Str::limit($ticket->description, 60) }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($ticket->status === 'open') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                    @elseif($ticket->status === 'in_progress') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                                    @elseif($ticket->status === 'resolved') bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 @endif">
                                    <i class="fas 
                                        @if($ticket->status === 'open') fa-clock
                                        @elseif($ticket->status === 'in_progress') fa-cog fa-spin
                                        @elseif($ticket->status === 'resolved') fa-check
                                        @else fa-times @endif mr-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($ticket->priority === 'high') bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                                    @elseif($ticket->priority === 'medium') bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300
                                    @else bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 @endif">
                                    <i class="fas 
                                        @if($ticket->priority === 'high') fa-arrow-up
                                        @elseif($ticket->priority === 'medium') fa-minus
                                        @else fa-arrow-down @endif mr-1"></i>
                                    {{ ucfirst($ticket->priority ?? 'normal') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-themed-secondary">
                                <div class="flex items-center space-x-1">
                                    <i class="fas fa-calendar text-cyan-600 dark:text-cyan-400"></i>
                                    {{ $ticket->created_at->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-themed-tertiary">{{ $ticket->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="$emit('viewTicket', {{ $ticket->id }})"
                                            class="p-2 text-cyan-600 dark:text-cyan-400 hover:text-cyan-800 dark:hover:text-cyan-300 hover:bg-cyan-100/30 dark:hover:bg-cyan-900/20 rounded-lg transition-colors"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($ticket->status !== 'resolved')
                                        <button wire:click="markAsInProgress({{ $ticket->id }})"
                                                class="p-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:bg-blue-100/30 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                                title="Mark In Progress">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                    @endif
                                    @if($ticket->status !== 'resolved' && $ticket->status !== 'closed')
                                        <button wire:click="markAsResolved({{ $ticket->id }})"
                                                class="p-2 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 hover:bg-green-100/30 dark:hover:bg-green-900/20 rounded-lg transition-colors"
                                                title="Mark Resolved">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    <button wire:click="closeTicket({{ $ticket->id }})" wire:confirm="Close this ticket?"
                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300 hover:bg-gray-100/30 dark:hover:bg-gray-900/20 rounded-lg transition-colors"
                                            title="Close">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="bg-themed-tertiary rounded-full p-6 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                                    <i class="fas fa-inbox text-2xl text-themed-secondary"></i>
                                </div>
                                <p class="text-themed-secondary text-lg font-medium">No tickets found</p>
                                <p class="text-themed-tertiary text-sm mt-1">All support tickets have been resolved or no new tickets submitted</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
            <div class="p-4 border-t border-themed-primary flex justify-center">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-100 dark:bg-yellow-900/30">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Open Tickets</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $tickets->where('status', 'open')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <i class="fas fa-cog text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">In Progress</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $tickets->where('status', 'in_progress')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Resolved</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $tickets->where('status', 'resolved')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-red-100 dark:bg-red-900/30">
                    <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">High Priority</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $tickets->where('priority', 'high')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Response Modal (inline form) -->
    @if(isset($respondingToTicketId))
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-themed-secondary rounded-lg shadow-xl max-w-2xl w-full p-6 border border-themed-primary">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-themed-primary">Respond to Ticket #{{ $respondingToTicketId }}</h3>
                    <button type="button" wire:click="$set('respondingToTicketId', null)"
                            class="text-themed-secondary hover:text-themed-primary transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form wire:submit.prevent="respondToTicket">
                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="response" class="block text-sm font-medium text-themed-primary mb-2">Your Response</label>
                            <textarea wire:model="response" id="response" rows="6"
                                      class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-themed-primary transition-colors duration-200"
                                      placeholder="Enter your response to the ticket..."></textarea>
                        </div>

                        <div>
                            <label for="resolution_status" class="block text-sm font-medium text-themed-primary mb-2">Resolution Status</label>
                            <select wire:model="resolution_status" id="resolution_status"
                                    class="w-full px-4 py-2 border border-themed-primary bg-themed-tertiary rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-themed-primary transition-colors duration-200">
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Mark as Resolved</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex space-x-3 justify-end">
                        <button type="button" wire:click="$set('respondingToTicketId', null)"
                                class="px-4 py-2 border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-tertiary transition-colors font-medium">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                                class="px-6 py-2 bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-700 dark:hover:bg-cyan-800 text-white rounded-lg transition-colors disabled:opacity-50 font-medium">
                            <span wire:loading.remove><i class="fas fa-send mr-2"></i>Send Response</span>
                            <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Sending...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>