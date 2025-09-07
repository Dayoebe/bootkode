<!-- resources/views/livewire/financial/admin/partials/payment-transactions.blade.php -->
<div>
    <!-- Filters and Search -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
            <div>
                <input wire:model.debounce.300ms="search" type="text" placeholder="Search transactions..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select wire:model="statusFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="success">Success</option>
                    <option value="failed">Failed</option>
                    <option value="abandoned">Abandoned</option>
                </select>
            </div>
            <div>
                <select wire:model="typeFilter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Types</option>
                    <option value="wallet_funding">Wallet Funding</option>
                    <option value="withdrawal">Withdrawal</option>
                    <option value="refund">Refund</option>
                </select>
            </div>
            <div>
                <input wire:model="dateFrom" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <input wire:model="dateTo" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
        </div>

        <!-- Bulk Actions -->
        @if(count($selectedTransactions) > 0)
            <div class="flex items-center space-x-4 p-3 bg-blue-50 rounded-lg">
                <span class="text-sm text-blue-800 font-medium">{{ count($selectedTransactions) }} selected</span>
                <select wire:model="bulkAction" class="rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">Choose action...</option>
                    <option value="verify">Verify Payments</option>
                    <option value="retry">Retry Failed</option>
                    <option value="mark_failed">Mark as Failed</option>
                </select>
                <button wire:click="processBulkAction" 
                        class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors">
                    Apply
                </button>
            </div>
        @endif
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" wire:model="selectAll" class="rounded border-gray-300 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" wire:model="selectedTransactions" 
                                           value="{{ $transaction->id }}" 
                                           class="rounded border-gray-300 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->created_at->format('M d, Y') }}
                                    <br>
                                    <span class="text-xs text-gray-400">{{ $transaction->created_at->format('H:i') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $transaction->customer_name ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->customer_email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-mono">{{ Str::limit($transaction->reference, 15) }}</div>
                                    @if($transaction->paystack_reference)
                                        <div class="text-xs text-gray-500 font-mono">{{ Str::limit($transaction->paystack_reference, 15) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">₦{{ number_format($transaction->amount, 2) }}</div>
                                    <div class="text-xs text-gray-500">{{ $transaction->currency }}</div>
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
                                                    class="text-blue-600 hover:text-blue-900 transition-colors">
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
                                                class="text-gray-600 hover:text-gray-900 transition-colors" 
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
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No transactions found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or search criteria.</p>
            </div>
        @endif
    </div>

    <!-- Refund Modal -->
    @if($showRefundModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeRefundModal"></div>
                <div class="bg-white rounded-lg p-6 max-w-md w-full relative transform transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Process Refund</h3>
                        <button wire:click="closeRefundModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <form wire:submit.prevent="processRefund">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Refund Amount (₦)</label>
                                <input wire:model="refundAmount" type="number" step="0.01" min="1"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('refundAmount') 
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Refund</label>
                                <textarea wire:model="refundReason" rows="3" 
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                                          placeholder="Enter the reason for this refund..."></textarea>
                                @error('refundReason') 
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p> 
                                @enderror
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeRefundModal" 
                                    class="px-4 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 transition-colors">
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