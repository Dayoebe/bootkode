{{-- resources/views/livewire/marketplace/admin/partials/payments-tab.blade.php --}}
<div class="space-y-4">
    <!-- Payments Header Actions -->
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
            <div class="relative">
                <input wire:model.live.debounce.300ms="transactionSearch" type="text"
                    placeholder="Search transactions..."
                    class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>

            <select wire:model.live="transactionStatus"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="all">All Transactions</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
            </select>

            <input wire:model.live="transactionDateFrom" type="date"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
        </div>

        <div class="flex gap-2">
            <button wire:click="processAutomaticPayouts"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-play mr-2"></i>
                Process Auto Payouts
            </button>
        </div>
    </div>

    <!-- Financial Overview Dashboard -->
    @isset($paymentStats)
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="p-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg text-white text-center">
                <div class="text-2xl font-bold">₦{{ number_format($paymentStats['total_revenue'] ?? 0, 0) }}
                </div>
                <div class="text-xs opacity-90">Total Revenue</div>
            </div>
            <div class="p-4 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg text-white text-center">
                <div class="text-2xl font-bold">
                    ₦{{ number_format($paymentStats['platform_commission'] ?? 0, 0) }}</div>
                <div class="text-xs opacity-90">Platform Earnings</div>
            </div>
            <div class="p-4 bg-gradient-to-r from-green-500 to-green-600 rounded-lg text-white text-center">
                <div class="text-2xl font-bold">₦{{ number_format($paymentStats['vendor_earnings'] ?? 0, 0) }}
                </div>
                <div class="text-xs opacity-90">Vendor Earnings</div>
            </div>
            <div class="p-4 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg text-white text-center">
                <div class="text-2xl font-bold">₦{{ number_format($paymentStats['pending_payouts'] ?? 0, 0) }}
                </div>
                <div class="text-xs opacity-90">Pending Payouts</div>
            </div>
        </div>
    @endisset

    <!-- Payment Sub-tabs -->
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8">
            <button wire:click="setPaymentTab('transactions')"
                class="py-2 px-1 border-b-2 font-medium text-sm {{ $paymentTab === 'transactions' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Recent Transactions
            </button>
            <button wire:click="setPaymentTab('withdrawals')"
                class="py-2 px-1 border-b-2 font-medium text-sm {{ $paymentTab === 'withdrawals' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Withdrawal Requests
                @if(isset($paymentStats) && ($paymentStats['pending_withdrawals'] ?? 0) > 0)
                    <span class="ml-1 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                        {{ $paymentStats['pending_withdrawals'] }}
                    </span>
                @endif
            </button>
        </nav>
    </div>

    <!-- Payment Content -->
    <div class="mt-6">
        @if($paymentTab === 'transactions')
            <!-- Recent Transactions -->
            @if(isset($transactions) && $transactions->count() > 0)
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-gray-700">
                                                        {{ strtoupper(substr($transaction->wallet->user->name ?? 'U', 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $transaction->wallet->user->name ?? 'Unknown' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $transaction->wallet->user->email ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transaction->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ ucfirst(str_replace('_', ' ', $transaction->category)) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                            ₦{{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $status = strtolower($transaction->status ?? 'pending');
                                                $badge = 'bg-yellow-100 text-yellow-800';
                                                if ($status === 'completed')
                                                    $badge = 'bg-green-100 text-green-800';
                                                if ($status === 'failed')
                                                    $badge = 'bg-red-100 text-red-800';
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $transactions->links() }}
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                    <i class="fas fa-credit-card text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                    <p class="text-gray-500">Transaction data will appear here when available.</p>
                </div>
            @endif

        @elseif($paymentTab === 'withdrawals')
            <!-- Withdrawal Requests -->
            @if(isset($withdrawals) && $withdrawals->count() > 0)
                <div class="space-y-4">
                    @foreach($withdrawals as $withdrawal)
                        <div
                            class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-all duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center">
                                            <span class="text-white font-medium">
                                                {{ strtoupper(substr($withdrawal->user->name ?? 'V', 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $withdrawal->user->name ?? 'Vendor' }}
                                        </h3>
                                        <p class="text-sm text-gray-500">{{ $withdrawal->user->email ?? 'N/A' }}</p>
                                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-600">
                                            <span><i
                                                    class="fas fa-calendar mr-1"></i>{{ $withdrawal->created_at->format('M d, Y') }}</span>
                                            <span><i
                                                    class="fas fa-wallet mr-1"></i>{{ ucfirst($withdrawal->method ?? 'bank') }}
                                                Transfer</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <div class="text-2xl font-bold text-gray-900">
                                        ₦{{ number_format($withdrawal->amount ?? 0, 0) }}</div>
                                    <div class="mt-2 flex space-x-2">
                                        <button onclick="alert('Withdrawal approval system will be implemented')"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                            <i class="fas fa-check mr-2"></i>
                                            Approve
                                        </button>
                                        <button onclick="alert('Withdrawal rejection system will be implemented')"
                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                            <i class="fas fa-times mr-2"></i>
                                            Decline
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="mt-6">
                        {{ $withdrawals->links() }}
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                    <i class="fas fa-university text-gray-300 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No withdrawal requests</h3>
                    <p class="text-gray-500">Vendor withdrawal requests will appear here when submitted.</p>
                </div>
            @endif
        @endif
    </div>
</div>