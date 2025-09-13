{{-- resources/views/livewire/marketplace/partial/payments-payouts.blade.php --}}
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Payments & Payouts</h2>
                <p class="text-gray-600">Manage platform payments and vendor payouts</p>
            </div>
            
            <button wire:click="processAutomaticPayouts" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-play mr-2"></i>
                Process Auto Payouts
            </button>
        </div>

        <!-- Financial Overview -->
        <div class="mt-6 grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="p-4 bg-blue-50 rounded-lg text-center">
                <div class="text-lg font-semibold text-blue-600">₦{{ number_format($stats['total_revenue'], 0) }}</div>
                <div class="text-xs text-blue-600">Total Revenue</div>
            </div>
            <div class="p-4 bg-purple-50 rounded-lg text-center">
                <div class="text-lg font-semibold text-purple-600">₦{{ number_format($stats['platform_commission'], 0) }}</div>
                <div class="text-xs text-purple-600">Platform Earnings</div>
            </div>
            <div class="p-4 bg-green-50 rounded-lg text-center">
                <div class="text-lg font-semibold text-green-600">₦{{ number_format($stats['vendor_earnings'], 0) }}</div>
                <div class="text-xs text-green-600">Vendor Earnings</div>
            </div>
            <div class="p-4 bg-yellow-50 rounded-lg text-center">
                <div class="text-lg font-semibold text-yellow-600">₦{{ number_format($stats['pending_payouts'], 0) }}</div>
                <div class="text-xs text-yellow-600">Pending Payouts</div>
            </div>
            <div class="p-4 bg-indigo-50 rounded-lg text-center">
                <div class="text-lg font-semibold text-indigo-600">₦{{ number_format($stats['processed_payouts'], 0) }}</div>
                <div class="text-xs text-indigo-600">Paid Out</div>
            </div>
            <div class="p-4 bg-pink-50 rounded-lg text-center">
                <div class="text-lg font-semibold text-pink-600">₦{{ number_format($stats['this_month_revenue'], 0) }}</div>
                <div class="text-xs text-pink-600">This Month</div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6">
                <button wire:click="setActiveTab('transactions')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'transactions' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Recent Transactions
                </button>
                <button wire:click="setActiveTab('withdrawals')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'withdrawals' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Withdrawal Requests
                </button>
                <button wire:click="setActiveTab('analytics')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'analytics' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    Revenue Analytics
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Filters -->
            <div class="mb-6 flex flex-wrap gap-3">
                <div class="relative">
                    <input wire:model.live.debounce.300ms="search" 
                           type="text" 
                           placeholder="Search users..." 
                           class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
                
                @if($activeTab === 'transactions')
                    <select wire:model.live="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="all">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                @endif

                <input wire:model.live="dateFrom" 
                       type="date" 
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                
                <input wire:model.live="dateTo" 
                       type="date" 
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            </div>

            @if($activeTab === 'transactions')
                <!-- Transactions List -->
                @if(isset($transactions) && $transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-700">
                                                    {{ strtoupper(substr($transaction->user->name ?? $transaction->user_email ?? 'U', 0, 1)) }}
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $transaction->user->name ?? $transaction->user_email ?? 'Unknown' }}
                                                    </div>
                                                    @if(!empty($transaction->user->email))
                                                        <div class="text-xs text-gray-500">{{ $transaction->user->email }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ optional($transaction->created_at)->format('M d, Y H:i') }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                            ₦{{ number_format($transaction->amount, 0) }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $status = strtolower($transaction->status ?? 'pending');
                                                $badge = 'bg-yellow-100 text-yellow-800';
                                                if ($status === 'completed') $badge = 'bg-green-100 text-green-800';
                                                if ($status === 'failed') $badge = 'bg-red-100 text-red-800';
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $transaction->vendor->name ?? 'Platform' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button wire:click="viewTransaction({{ $transaction->id }})" class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                                <i class="fas fa-eye mr-2"></i>View
                                            </button>

                                            @if(strtolower($transaction->status) === 'pending')
                                                <button wire:click="markAsCompleted({{ $transaction->id }})" class="inline-flex items-center px-3 py-1 ml-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                                    <i class="fas fa-check mr-2"></i>Complete
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        No transactions found.
                    </div>
                @endif
            @endif

            @if($activeTab === 'withdrawals')
                <!-- Withdrawal Requests -->
                @if(isset($withdrawals) && $withdrawals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($withdrawals as $w)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $w->vendor->name ?? 'Vendor' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ optional($w->created_at)->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 text-right text-sm font-semibold">₦{{ number_format($w->amount, 0) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ ucfirst($w->method ?? 'N/A') }}</td>
                                        <td class="px-6 py-4 text-right text-sm">
                                            <button wire:click="approveWithdrawal({{ $w->id }})" class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700">
                                                Approve
                                            </button>
                                            <button wire:click="declineWithdrawal({{ $w->id }})" class="inline-flex items-center px-3 py-1 ml-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                                Decline
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $withdrawals->links() }}
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        No withdrawal requests.
                    </div>
                @endif
            @endif

            @if($activeTab === 'analytics')
                <!-- Revenue Analytics -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-white border border-gray-200 rounded-lg">
                        <div class="text-sm text-gray-500">Total Revenue</div>
                        <div class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['total_revenue'] ?? 0, 0) }}</div>
                    </div>
                    <div class="p-4 bg-white border border-gray-200 rounded-lg">
                        <div class="text-sm text-gray-500">This Month</div>
                        <div class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['this_month_revenue'] ?? 0, 0) }}</div>
                    </div>
                    <div class="p-4 bg-white border border-gray-200 rounded-lg">
                        <div class="text-sm text-gray-500">Pending Payouts</div>
                        <div class="text-2xl font-bold text-gray-900">₦{{ number_format($stats['pending_payouts'] ?? 0, 0) }}</div>
                    </div>
                </div>

                <!-- Placeholder for charts -->
                <div class="mt-6 bg-white border border-gray-200 rounded-lg p-6">
                    <div class="text-sm text-gray-500 mb-4">Revenue trends (last 30 days)</div>
                    <div class="h-48 bg-gray-50 rounded-md flex items-center justify-center text-gray-400">
                        Chart placeholder
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>