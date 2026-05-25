<div>
    <!-- resources/views/livewire/financial/admin/payment-processing.blade.php -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-700">Payment Processing Center</h1>
            <p class="text-gray-600">Manage payments, withdrawals, and financial operations</p>
            <a href="{{ route('admin.commercial.readiness') }}" class="mt-3 inline-flex rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 hover:border-blue-400 hover:text-blue-700">
                <i class="fas fa-file-invoice-dollar mr-2"></i>Commercial readiness
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-4 mb-8">
            <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Amount</p>
                        <p class="text-xl font-bold text-gray-900">₦{{ number_format($stats['total_amount'], 2) }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-blue-500">
                <p class="text-xs text-gray-600">Success Rate</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['success_rate'] }}%</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-green-500">
                <p class="text-xs text-gray-600">Successful</p>
                <p class="text-lg font-bold text-green-600">{{ $stats['successful_payments'] }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-yellow-500">
                <p class="text-xs text-gray-600">Pending</p>
                <p class="text-lg font-bold text-yellow-600">{{ $stats['pending_payments'] }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-red-500">
                <p class="text-xs text-gray-600">Failed</p>
                <p class="text-lg font-bold text-red-600">{{ $stats['failed_payments'] }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-4 border-l-4 border-purple-500">
                <p class="text-xs text-gray-600">Withdrawals</p>
                <p class="text-lg font-bold text-purple-600">{{ $stats['pending_withdrawals'] }}</p>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 mb-8">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="setTab('overview')"
                    class="{{ $selectedTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Payment Overview
                </button>
                <button wire:click="setTab('transactions')"
                    class="{{ $selectedTab === 'transactions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    All Transactions
                </button>
                <button wire:click="setTab('withdrawals')"
                    class="{{ $selectedTab === 'withdrawals' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Pending Withdrawals
                </button>
                <button wire:click="setTab('failed')"
                    class="{{ $selectedTab === 'failed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Review Failed
                </button>
                <button wire:click="setTab('reports')"
                    class="{{ $selectedTab === 'reports' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                    Generate Reports
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        @if($selectedTab === 'overview')
        <livewire:financial.admin.partials.payment-overview :stats="$stats" />
        @elseif($selectedTab === 'transactions')
        <livewire:financial.admin.partials.payment-transactions />
        @elseif($selectedTab === 'withdrawals')
        <livewire:financial.admin.partials.payment-withdrawals />
        @elseif($selectedTab === 'failed')
        <livewire:financial.admin.partials.payment-failed />
        @elseif($selectedTab === 'reports')
        <livewire:financial.admin.partials.payment-reports />
        @endif

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('info'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
                class="fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
                {{ session('info') }}
            </div>
        @endif
    </div>

    <!-- Loading Spinner -->
    <div wire:loading class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-gray-700">Processing...</span>
        </div>
    </div>
</div>
