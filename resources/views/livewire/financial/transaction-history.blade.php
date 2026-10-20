
<!-- resources/views/livewire/financial/transaction-history.blade.php -->
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8 flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-bold text-themed-primary">Transaction History</h1>
            <p class="text-themed-secondary">View and manage all your financial transactions</p>
        </div>
        <button wire:click="openExportModal" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-themed-secondary rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-themed-secondary">Total Balance</h3>
            <p class="text-2xl font-bold text-themed-primary">₦{{ number_format($summary['current_balances']['total'], 2) }}</p>
            <div class="mt-2 text-xs text-themed-secondary">
                <p>User: ₦{{ number_format($summary['current_balances']['user_wallet'], 2) }}</p>
                <p>Instructor: ₦{{ number_format($summary['current_balances']['instructor_wallet'], 2) }}</p>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-themed-secondary">Total Credits</h3>
            <p class="text-2xl font-bold text-green-600">₦{{ number_format($summary['transaction_summary']['total_credits'], 2) }}</p>
            <p class="text-xs text-themed-secondary mt-2">{{ $summary['transaction_summary']['total_transactions'] }} transactions</p>
        </div>

        <div class="bg-themed-secondary rounded-lg shadow-sm p-6">
            <h3 class="text-sm font-medium text-themed-secondary">Total Debits</h3>
            <p class="text-2xl font-bold text-red-600">₦{{ number_format($summary['transaction_summary']['total_debits'], 2) }}</p>
            <p class="text-xs text-themed-secondary mt-2">Net: ₦{{ number_format($summary['transaction_summary']['net_amount'], 2) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-themed-secondary rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Filters</h3>
            <div class="flex space-x-2">
                <button wire:click="toggleFilters" class="text-blue-600 hover:text-blue-800">
                    {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                </button>
                <button wire:click="clearFilters" class="text-themed-secondary hover:text-gray-700">Clear All</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <input wire:model.debounce.300ms="search" type="text" placeholder="Search transactions..."
                       class="w-full rounded-md border-themed-secondary shadow-sm">
            </div>
            <div>
                <select wire:model="categoryFilter" class="w-full rounded-md border-themed-secondary shadow-sm">
                    <option value="all">All Categories</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model="typeFilter" class="w-full rounded-md border-themed-secondary shadow-sm">
                    <option value="all">All Types</option>
                    <option value="credit">Credits</option>
                    <option value="debit">Debits</option>
                </select>
            </div>
            <div>
                <select wire:model="walletFilter" class="w-full rounded-md border-themed-secondary shadow-sm">
                    <option value="all">All Wallets</option>
                    @foreach($walletTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($showFilters)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-themed-primary">Date From</label>
                    <input wire:model="dateFrom" type="date" class="mt-1 w-full rounded-md border-themed-secondary shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-themed-primary">Date To</label>
                    <input wire:model="dateTo" type="date" class="mt-1 w-full rounded-md border-themed-secondary shadow-sm">
                </div>
            </div>
        @endif
    </div>

    <!-- Transactions Table -->
    <div class="bg-themed-secondary rounded-lg shadow-sm overflow-hidden">
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-themed-primary">
                    <thead class="bg-themed-tertiary">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-themed-secondary divide-y divide-themed-primary">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-secondary">
                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $transaction->type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->category)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold
                                    {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'credit' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary">
                                    ₦{{ number_format($transaction->balance_after, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-themed-primary max-w-xs truncate">
                                    {{ $transaction->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        bg-{{ $transaction->status_color }}-100 text-{{ $transaction->status_color }}-800">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-themed-primary">No transactions found</h3>
                <p class="mt-1 text-sm text-themed-secondary">Try adjusting your filters or date range.</p>
            </div>
        @endif
    </div>

    <!-- Export Modal -->
    @if($showExportModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeExportModal"></div>
                <div class="bg-themed-secondary rounded-lg p-6 max-w-md w-full relative">
                    <h3 class="text-lg font-semibold mb-4">Export Transactions</h3>
                    <form wire:submit.prevent="exportTransactions">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-themed-primary">Format</label>
                                <select wire:model="exportFormat" class="mt-1 w-full rounded-md border-themed-secondary">
                                    <option value="csv">CSV</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-themed-primary">From Date</label>
                                    <input wire:model="exportDateFrom" type="date" class="mt-1 w-full rounded-md border-themed-secondary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-themed-primary">To Date</label>
                                    <input wire:model="exportDateTo" type="date" class="mt-1 w-full rounded-md border-themed-secondary">
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button type="button" wire:click="closeExportModal" class="px-4 py-2 text-themed-primary border border-themed-secondary rounded-md hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
