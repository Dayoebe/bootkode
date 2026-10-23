<!-- resources/views/livewire/financial/admin/partials/payment-withdrawals.blade.php -->
<div>
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-themed-secondary rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary">Pending Withdrawals</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_count'] }}</p>
                    <p class="text-sm text-themed-secondary">₦{{ number_format($stats['pending_amount'], 2) }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg shadow p-6 border-l-4 border-brand">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary">Processing</p>
                    <p class="text-2xl font-bold text-brand">{{ $stats['processing_count'] }}</p>
                    <p class="text-sm text-themed-secondary">₦{{ number_format($stats['processing_amount'], 2) }}</p>
                </div>
                <div class="bg-brand/10 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary">Completed Today</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['completed_today'] }}</p>
                    <p class="text-sm text-themed-secondary">₦{{ number_format($stats['completed_today_amount'], 2) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-themed-secondary rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input aria-label="Search withdrawals" wire:model.debounce.300ms="search" type="text" placeholder="Search withdrawals..."
                       class="w-full rounded-md border-themed-secondary shadow-sm focus:border-brand focus:ring-brand/30">
            </div>
            <div>
                <select aria-label="Status filter" wire:model="statusFilter" class="w-full rounded-md border-themed-secondary shadow-sm focus:border-brand focus:ring-brand/30">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div>
                <input aria-label="Date from" wire:model="dateFrom" type="date" class="w-full rounded-md border-themed-secondary shadow-sm focus:border-brand focus:ring-brand/30">
            </div>
            <div>
                <input aria-label="Date to" wire:model="dateTo" type="date" class="w-full rounded-md border-themed-secondary shadow-sm focus:border-brand focus:ring-brand/30">
            </div>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="bg-themed-secondary rounded-lg shadow-sm overflow-hidden">
        @if($withdrawals->count() > 0)
            <div class="overflow-x-auto">
                <x-ui.table label="Payment Withdrawals.Blade records"><table class="min-w-full divide-y divide-themed-primary">
                    <thead class="bg-themed-tertiary">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Instructor</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Bank Details</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Requested</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-themed-secondary divide-y divide-themed-primary">
                        @foreach($withdrawals as $withdrawal)
                            <tr class="hover:bg-surface-muted transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img class="h-10 w-10 rounded-full"
                                             src="{{ $withdrawal->user->profile_picture ?? 'https://ui-avatars.com/api/?name=' . urlencode($withdrawal->user->name) }}"
                                             alt="">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-themed-primary">{{ $withdrawal->user->name }}</div>
                                            <div class="text-sm text-themed-secondary">{{ $withdrawal->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-themed-primary">₦{{ number_format($withdrawal->amount, 2) }}</div>
                                    <div class="text-xs text-themed-secondary">{{ $withdrawal->withdrawal_id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-themed-primary">
                                        <p class="font-medium">{{ $withdrawal->account_name }}</p>
                                        <p class="text-themed-secondary">{{ $withdrawal->account_number }}</p>
                                        <p class="text-xs text-content-muted">{{ $withdrawal->bank_code }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-secondary">
                                    {{ $withdrawal->requested_at->format('M d, Y') }}
                                    <br>
                                    <span class="text-xs">{{ $withdrawal->requested_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColor = match($withdrawal->status) {
                                            'pending' => 'yellow',
                                            'approved' => 'blue',
                                            'processing' => 'purple',
                                            'completed' => 'green',
                                            'failed' => 'red',
                                            'rejected' => 'red',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                        {{ ucfirst($withdrawal->status) }}
                                    </span>
                                    @if($withdrawal->failure_reason && in_array($withdrawal->status, ['failed', 'rejected']))
                                        <div class="text-xs text-red-600 mt-1">{{ Str::limit($withdrawal->failure_reason, 30) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($withdrawal->status === 'pending')
                                        <div class="flex space-x-2">
                                            <button wire:click="approveWithdrawal({{ $withdrawal->id }})"
                                                    class="text-green-600 hover:text-green-900 transition-colors"
                                                    onclick="return confirm('Are you sure you want to approve this withdrawal?')">
                                                Approve
                                            </button>
                                            <button wire:click="rejectWithdrawal({{ $withdrawal->id }})"
                                                    class="text-red-600 hover:text-red-900 transition-colors"
                                                    onclick="return confirm('Are you sure you want to reject this withdrawal?')">
                                                Reject
                                            </button>
                                        </div>
                                    @elseif($withdrawal->status === 'processing')
                                        <span class="text-brand text-sm">In Progress...</span>
                                    @elseif($withdrawal->status === 'completed')
                                        <span class="text-green-600 text-sm">✓ Completed</span>
                                    @else
                                        <span class="text-themed-secondary text-sm">{{ ucfirst($withdrawal->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></x-ui.table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-themed-primary">
                {{ $withdrawals->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-content-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-themed-primary">No withdrawals found</h3>
                <p class="mt-1 text-sm text-themed-secondary">Try adjusting your filters to see more results.</p>
            </div>
        @endif
    </div>
</div>
