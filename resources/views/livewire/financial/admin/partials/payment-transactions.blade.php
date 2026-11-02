<!-- resources/views/livewire/financial/admin/partials/payment-transactions.blade.php -->
<div>
    <!-- Filters and Search -->
    <div class="bg-themed-secondary rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
            <div>
                <input aria-label="Search transactions" wire:model.debounce.300ms="search" type="text" placeholder="Search transactions..."
                       class="w-full rounded-md border-themed-secondary shadow-sm focus:border-brand focus:ring-brand/30">
            </div>
            <div>
                <select aria-label="Status filter" wire:model="statusFilter" class="w-full rounded-md border-themed-secondary shadow-sm focus:border-brand focus:ring-brand/30">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="success">Success</option>
                    <option value="failed">Failed</option>
                    <option value="abandoned">Abandoned</option>
                </select>
            </div>
            <div>
                <select aria-label="Type filter" wire:model="typeFilter" class="w-full rounded-md border-themed-secondary shadow-sm focus:border-brand focus:ring-brand/30">
                    <option value="all">All Types</option>
                    <option value="wallet_funding">Wallet Funding</option>
                    <option value="withdrawal">Withdrawal</option>
                    <option value="refund">Refund</option>
                </select>
            </div>
            <div>
                <input aria-label="Date from" wire:model="dateFrom" type="date" class="w-full rounded-md border-themed-secondary shadow-sm focus:border-brand focus:ring-brand/30">
            </div>
            <div>
                <input aria-label="Date to" wire:model="dateTo" type="date" class="w-full rounded-md border-themed-secondary shadow-sm focus:border-brand focus:ring-brand/30">
            </div>
        </div>

        <!-- Bulk Actions -->
        @if(count($selectedTransactions) > 0)
            <div class="flex items-center space-x-4 p-3 bg-brand/10 rounded-lg">
                <span class="text-sm text-brand font-medium">{{ count($selectedTransactions) }} selected</span>
                <select aria-label="Bulk action" wire:model="bulkAction" class="rounded-md border-themed-secondary shadow-sm text-sm">
                    <option value="">Choose action...</option>
                    <option value="verify">Verify Payments</option>
                    <option value="retry">Retry Failed</option>
                    <option value="mark_failed">Mark as Failed</option>
                </select>
                <button wire:click="processBulkAction"
                        class="bk-button bk-button-primary   px-3 py-1 rounded text-sm  transition-colors">
                    Apply
                </button>
            </div>
        @endif
    </div>

    <!-- Transactions Table -->
    <div class="bg-themed-secondary rounded-lg shadow-sm overflow-hidden">
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <x-ui.table label="Payment Transactions records"><table class="min-w-full divide-y divide-themed-primary">
                    <thead class="bg-themed-tertiary">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">
                                <input aria-label="Select all" type="checkbox" wire:model="selectAll" class="rounded border-themed-secondary focus:ring-brand/30">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Customer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Reference</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Type</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-themed-secondary divide-y divide-themed-primary">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-surface-muted transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input aria-label="Selected transactions" type="checkbox" wire:model="selectedTransactions"
                                           value="{{ $transaction->id }}"
                                           class="rounded border-themed-secondary focus:ring-brand/30">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-secondary">
                                    {{ $transaction->created_at->format('M d, Y') }}
                                    <br>
                                    <span class="text-xs text-content-muted">{{ $transaction->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-themed-primary">{{ $transaction->customer_name ?? 'N/A' }}</p>
                                        <p class="text-sm text-themed-secondary">{{ $transaction->customer_email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-themed-primary font-mono">{{ Str::limit($transaction->reference, 15) }}</div>
                                    @if($transaction->paystack_reference)
                                        <div class="text-xs text-themed-secondary font-mono">{{ Str::limit($transaction->paystack_reference, 15) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-themed-primary">₦{{ number_format($transaction->amount, 2) }}</div>
                                    <div class="text-xs text-themed-secondary">{{ $transaction->currency }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColor = match($transaction->status) {
                                            'success' => 'green',
                                            'pending' => 'yellow',
                                            'failed' => 'red',
                                            'abandoned' => 'gray',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                    @if($transaction->gateway_response && $transaction->status === 'failed')
                                        <div class="text-xs text-red-600 mt-1">{{ Str::limit($transaction->gateway_response, 20) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($transaction->status === 'pending')
                                            <button wire:click="verifySinglePayment({{ $transaction->id }})"
                                                    class="text-brand hover:text-brand transition-colors">
                                                Verify
                                            </button>
                                        @endif

                                        @if($transaction->status === 'success' && $transaction->transaction_type !== 'refund')
                                            <button wire:click="openRefundModal({{ $transaction->id }})"
                                                    class="text-orange-600 hover:text-orange-900 transition-colors">
                                                Refund
                                            </button>
                                        @endif

                                        <button onclick="navigator.clipboard.writeText('{{ $transaction->reference }}')"
                                                class="text-themed-secondary hover:text-content transition-colors"
                                                title="Copy Reference">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></x-ui.table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-themed-primary">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-content-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-themed-primary">No transactions found</h3>
                <p class="mt-1 text-sm text-themed-secondary">Try adjusting your filters or search criteria.</p>
            </div>
        @endif
    </div>

    <!-- Refund Modal -->
    @if($showRefundModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Process Refund" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showRefundModal&#x27;, false)">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeRefundModal"></div>
                <div class="bg-themed-secondary rounded-lg p-6 max-w-md w-full relative transform transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-themed-primary">Process Refund</h3>
                        <button wire:click="closeRefundModal" class="text-content-muted hover:text-content-muted">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="processRefund">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-1">Refund Amount (₦)</label>
                                <input aria-label="Refund amount" wire:model="refundAmount" type="number" step="0.01" min="1"
                                       class="w-full rounded-md border-themed-secondary shadow-sm focus:border-brand focus:ring-brand/30">
                                @error('refundAmount')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-1">Reason for Refund</label>
                                <textarea aria-label="Enter the reason for this refund" wire:model="refundReason" rows="3"
                                          class="w-full rounded-md border-themed-secondary shadow-sm focus:border-brand focus:ring-brand/30"
                                          placeholder="Enter the reason for this refund..."></textarea>
                                @error('refundReason')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeRefundModal"
                                    class="px-4 py-2 text-themed-primary bg-themed-tertiary border border-themed-secondary rounded-md hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                                Process Refund
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
