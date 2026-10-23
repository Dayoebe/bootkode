<!-- resources/views/livewire/financial/wallet-dashboard.blade.php -->
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class=" font-bold text-themed-primary bk-page-title">My Wallet</h1>
        <p class="text-themed-secondary">Manage your funds and track transactions</p>
        <a href="{{ route('billing.documents') }}" class="mt-3 inline-flex rounded-lg border border-themed-secondary px-3 py-2 text-sm font-semibold text-themed-primary hover:border-brand hover:text-brand">
            <i class="fas fa-receipt mr-2" aria-hidden="true"></i>Receipts & invoices
        </a>
    </div>

    <!-- Wallet Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- User Wallet Balance -->
        <div class="bg-themed-secondary rounded-lg shadow-sm p-6 border-l-4 border-brand">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary">User Wallet</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $walletStats['formatted_user_balance'] }}</p>
                </div>
                <div class="bg-brand/10 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <button wire:click="openFundingModal" class="bk-button bk-button-primary w-full   py-2 px-4 rounded-lg  transition-colors">
                    Fund Wallet
                </button>
            </div>
        </div>

        <!-- Instructor Wallet Balance -->
        <div class="bg-themed-secondary rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary">Instructor Earnings</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $walletStats['formatted_instructor_balance'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('withdrawals.index') }}" class="bk-button bk-button-primary w-full   py-2 px-4 rounded-lg  transition-colors inline-block text-center">
                    Request Withdrawal
                </a>
            </div>
        </div>

        <!-- Total Balance -->
        <div class="bg-themed-secondary rounded-lg shadow-sm p-6 border-l-4 border-brand">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary">Total Balance</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $walletStats['formatted_total_balance'] }}</p>
                </div>
                <div class="bg-brand/10 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 012-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-xs text-themed-secondary">Pending: ₦{{ number_format($walletStats['pending_withdrawals'], 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-themed-secondary rounded-lg shadow-sm">
        <div class="p-6 border-b border-themed-primary">
            <h3 class="text-lg font-semibold text-themed-primary">Recent Transactions</h3>
        </div>
        <div class="p-6">
            @if($walletStats['recent_transactions']->count() > 0)
                <div class="space-y-4">
                    @foreach($walletStats['recent_transactions'] as $transaction)
                        <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    @if($transaction->type === 'credit')
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-themed-primary">{{ $transaction->description }}</p>
                                    <p class="text-xs text-themed-secondary">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold {{ $transaction->type === 'credit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->formatted_amount }}
                                </p>
                                <p class="text-xs text-themed-secondary">{{ ucfirst($transaction->category) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-content-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-themed-primary">No transactions yet</h3>
                    <p class="mt-1 text-sm text-themed-secondary">Start by funding your wallet to see transactions here.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Fund Wallet Modal -->
    @if($showFundingModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{}" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Fund your wallet" @keydown.escape.window="$wire.closeFundingModal()">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeFundingModal"></div>

                <div class="inline-block align-bottom bg-themed-secondary rounded-lg text-left overflow-hidden shadow-sm transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="fundWallet" wire:loading.attr="aria-busy">
                        <div class="bg-themed-secondary px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-brand/10 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-themed-primary">Fund Your Wallet</h3>
                                    <div class="mt-4">
                                        <label for="fundAmount" class="block text-sm font-medium text-themed-primary">Amount (₦)</label>
                                        <div class="mt-1">
                                            <input
                                                type="number"
                                                id="fundAmount"
                                                wire:model="fundAmount"
                                                class="shadow-sm focus:ring-brand/30 focus:border-brand block w-full sm:text-sm border-themed-secondary rounded-md"
                                                placeholder="Enter amount (min: ₦100)"
                                                min="100"
                                                max="500000"
                                                step="0.01"
                                            >
                                        </div>
                                        @error('fundAmount') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div class="mt-4 text-sm text-themed-secondary">
                                        <p>• Minimum amount: ₦100</p>
                                        <p>• Maximum amount: ₦500,000</p>
                                        <p>• Secure payment via Paystack</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-themed-tertiary px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="bk-button bk-button-primary w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2  text-base font-medium   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand/30 sm:ml-3 sm:w-auto sm:text-sm">
                                Continue to Payment
                            </button>
                            <button type="button" wire:click="closeFundingModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-themed-secondary shadow-sm px-4 py-2 bg-themed-secondary text-base font-medium text-themed-primary hover:bg-surface-muted focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand/30 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-sm z-50">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-sm z-50">
            {{ session('error') }}
        </div>
    @endif
</div>
