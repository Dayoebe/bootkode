{{-- resources/views/livewire/affiliate/commission-history.blade.php --}}
<div class=" px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class=" font-bold text-content bk-page-title">Commission History</h1>
        <p class="text-content-muted">Track all your commission transactions and earnings</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-surface rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Total Commissions</p>
                    <p class="text-2xl font-bold text-content">₦{{ number_format($totalCommissions, 2) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-coins text-green-600 text-xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Pending Commissions</p>
                    <p class="text-2xl font-bold text-content">₦{{ number_format($pendingCommissions, 2) }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-surface rounded-lg shadow-sm p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-content mb-2">Search</label>
                <input aria-label="Search courses or students" type="text" wire:model.live="search" placeholder="Search courses or students..."
                       class="w-full px-3 py-2 border border-line rounded-md shadow-sm focus:ring-brand/30 focus:border-brand">
            </div>

            <div>
                <label class="block text-sm font-medium text-content mb-2">Status</label>
                <select aria-label="Status filter" wire:model.live="statusFilter" class="w-full px-3 py-2 border border-line rounded-md shadow-sm focus:ring-brand/30 focus:border-brand">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-content mb-2">Date Range</label>
                <select aria-label="Date range" wire:model.live="dateRange" class="w-full px-3 py-2 border border-line rounded-md shadow-sm focus:ring-brand/30 focus:border-brand">
                    <option value="all">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="quarter">This Quarter</option>
                </select>
            </div>

            <div class="flex items-end">
                <button wire:click="$refresh" class="w-full px-4 py-2 bg-action text-action-foreground rounded-md hover:bg-action transition-colors">
                    <i class="fas fa-sync-alt mr-2" aria-hidden="true"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="bg-surface rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-line">
            <h3 class="text-lg font-semibold text-content">Transactions</h3>
        </div>

        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <x-ui.table label="Commission History records"><table class="min-w-full divide-y divide-line">
                    <thead class="bg-surface-muted">
                        <tr>
                            <th scope="col" wire:click="sortBy('created_at')" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider cursor-pointer hover:bg-surface-muted">
                                Date
                                @if($sortBy === 'created_at')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1" aria-hidden="true"></i>
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Course</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Student</th>
                            <th scope="col" wire:click="sortBy('commission_amount')" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider cursor-pointer hover:bg-surface-muted">
                                Commission
                                @if($sortBy === 'commission_amount')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1" aria-hidden="true"></i>
                                @endif
                            </th>
                            <th scope="col" wire:click="sortBy('status')" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider cursor-pointer hover:bg-surface-muted">
                                Status
                                @if($sortBy === 'status')
                                    <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1" aria-hidden="true"></i>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-surface divide-y divide-line">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-surface-muted">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
                                    {{ $transaction->created_at->format('M d, Y') }}
                                    <div class="text-xs text-content-muted">{{ $transaction->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-content">{{ Str::limit($transaction->course->title, 40) }}</div>
                                    <div class="text-sm text-content-muted">₦{{ number_format($transaction->course_price, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-content">{{ $transaction->referral->referredUser->name }}</div>
                                    <div class="text-sm text-content-muted">{{ $transaction->referral->referredUser->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-green-600">{{ $transaction->formatted_commission }}</div>
                                    <div class="text-xs text-content-muted">{{ $transaction->commission_rate }}% rate</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $transaction->status === 'paid' ? 'bg-green-100 text-green-800' :
                                           ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                    @if($transaction->paid_at)
                                        <div class="text-xs text-content-muted mt-1">{{ $transaction->paid_at->format('M d, Y') }}</div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></x-ui.table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-line">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-receipt text-6xl text-content-muted mb-4" aria-hidden="true"></i>
                <h3 class="text-lg font-medium text-content mb-2">No transactions found</h3>
                <p class="text-content-muted">
                    @if($search || $statusFilter !== 'all' || $dateRange !== 'all')
                        Try adjusting your filters to see more results.
                    @else
                        Your commission transactions will appear here once you start earning.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
