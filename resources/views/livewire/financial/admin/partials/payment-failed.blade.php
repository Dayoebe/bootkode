<!-- resources/views/livewire/financial/admin/partials/payment-failed.blade.php -->
<div>
    <!-- Filters for Failed Payments -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Failed Payments Analysis</h3>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input wire:model.debounce.300ms="search" type="text" placeholder="Search by reference, email..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <select wire:model="failedDateRange" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="week">Last 7 days</option>
                    <option value="month">Last 30 days</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Failure Reason</label>
                <select wire:model="failureReason" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Reasons</option>
                    <option value="insufficient_funds">Insufficient Funds</option>
                    <option value="invalid_card">Invalid Card</option>
                    <option value="expired_card">Expired Card</option>
                    <option value="declined">Declined by Bank</option>
                    <option value="timeout">Transaction Timeout</option>
                    <option value="network_error">Network Error</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount Range</label>
                <select wire:model="failedAmountRange" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">All Amounts</option>
                    <option value="0-1000">₦0 - ₦1,000</option>
                    <option value="1000-5000">₦1,000 - ₦5,000</option>
                    <option value="5000-10000">₦5,000 - ₦10,000</option>
                    <option value="10000+">₦10,000+</option>
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="generateFailureReport" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors">
                    Analyze Failures
                </button>
            </div>
        </div>

        <!-- Failure Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                <div class="text-center">
                    <p class="text-sm text-red-600">Total Failed</p>
                    <p class="text-2xl font-bold text-red-800">{{ $stats['total_failed'] }}</p>
                </div>
            </div>
            <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                <div class="text-center">
                    <p class="text-sm text-orange-600">Amount Lost</p>
                    <p class="text-lg font-bold text-orange-800">₦{{ number_format($stats['total_amount_lost'], 2) }}</p>
                </div>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                <div class="text-center">
                    <p class="text-sm text-blue-600">Top Reason</p>
                    <p class="text-xs font-bold text-blue-800">{{ Str::limit(ucfirst(str_replace('_', ' ', $stats['most_common_reason'])), 20) }}</p>
                </div>
            </div>
            <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                <div class="text-center">
                    <p class="text-sm text-green-600">Retry Success</p>
                    <p class="text-2xl font-bold text-green-800">{{ $stats['retry_success_rate'] }}%</p>
                </div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
                <div class="text-center">
                    <p class="text-sm text-purple-600">Daily Average</p>
                    <p class="text-2xl font-bold text-purple-800">{{ $stats['daily_average'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Transactions Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Failed Transactions</h3>
            <div class="flex space-x-2">
                <button wire:click="retryAllFailedPayments" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 text-sm transition-colors">
                    Retry All
                </button>
                <button wire:click="exportFailedTransactions" 
                        class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 text-sm transition-colors">
                    Export Failed
                </button>
            </div>
        </div>
        
        @if($failedTransactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Failure Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Failed At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($failedTransactions as $transaction)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $transaction->customer_name ?? 'N/A' }}</p>
                                        <p class="text-sm text-gray-500">{{ $transaction->customer_email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-red-600">₦{{ number_format($transaction->amount, 2) }}</div>
                                    <div class="text-xs text-gray-500">{{ $transaction->currency }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ Str::limit($transaction->gateway_response ?? 'Unknown', 25) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->created_at->format('M d, Y') }}
                                    <br>
                                    <span class="text-xs text-gray-400">{{ $transaction->created_at->format('H:i') }}</span>
                                    <br>
                                    <span class="text-xs text-gray-400">{{ $transaction->created_at->diffForHumans() }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-mono">{{ Str::limit($transaction->reference, 15) }}</div>
                                    @if($transaction->paystack_reference)
                                        <div class="text-xs text-gray-500 font-mono">{{ Str::limit($transaction->paystack_reference, 15) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button wire:click="retryFailedPayment({{ $transaction->id }})" 
                                                class="text-blue-600 hover:text-blue-900 transition-colors">
                                            Retry
                                        </button>
                                        <button wire:click="investigateFailure({{ $transaction->id }})" 
                                                class="text-yellow-600 hover:text-yellow-900 transition-colors">
                                            Investigate
                                        </button>
                                        <button wire:click="markAsIrrecoverable({{ $transaction->id }})" 
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                onclick="return confirm('Mark this transaction as irrecoverable?')">
                                            Mark Final
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
                {{ $failedTransactions->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No failed transactions</h3>
                <p class="mt-1 text-sm text-gray-500">All recent transactions have been processed successfully.</p>
            </div>
        @endif
    </div>
</div>