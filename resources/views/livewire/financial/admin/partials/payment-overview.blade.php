<!-- resources/views/livewire/financial/admin/partials/payment-overview.blade.php -->
<div>
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <button onclick="window.livewire.emit('setTab', 'transactions')" 
                    class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-colors">
                <div class="text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                    </svg>
                    <p class="text-sm font-medium">Verify Pending</p>
                </div>
            </button>
            
            <button onclick="window.livewire.emit('setTab', 'withdrawals')" 
                    class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition-colors">
                <div class="text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-sm font-medium">Process Withdrawals</p>
                </div>
            </button>
            
            <button onclick="window.livewire.emit('setTab', 'failed')" 
                    class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition-colors">
                <div class="text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm font-medium">Review Failed</p>
                </div>
            </button>
            
            <button onclick="window.livewire.emit('setTab', 'reports')" 
                    class="p-4 border-2 border-dashed border-gray-300 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-colors">
                <div class="text-center">
                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 012-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z"></path>
                    </svg>
                    <p class="text-sm font-medium">Generate Reports</p>
                </div>
            </button>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Failed Payments -->
        <div class="bg-white rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Failed Payments</h3>
            </div>
            <div class="p-6">
                @if($failedPayments->count() > 0)
                    <div class="space-y-4">
                        @foreach($failedPayments as $payment)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $payment->customer_name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">{{ $payment->customer_email }}</p>
                                    <p class="text-xs text-gray-400">{{ $payment->created_at->diffForHumans() }}</p>
                                    @if($payment->gateway_response)
                                        <p class="text-xs text-red-600">{{ $payment->gateway_response }}</p>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-red-600">₦{{ number_format($payment->amount, 2) }}</p>
                                    <button wire:click="verifySinglePayment({{ $payment->id }})" 
                                            class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200 transition-colors">
                                        Retry
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500 mt-2">No failed payments</p>
                        <p class="text-sm text-gray-400">All recent transactions processed successfully</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pending Withdrawals -->
        <div class="bg-white rounded-xl shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pending Withdrawals</h3>
            </div>
            <div class="p-6">
                @if($pendingWithdrawals->count() > 0)
                    <div class="space-y-4">
                        @foreach($pendingWithdrawals as $withdrawal)
                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full" 
                                         src="{{ $withdrawal->user->profile_picture ?? 'https://ui-avatars.com/api/?name=' . urlencode($withdrawal->user->name) }}" 
                                         alt="">
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900">{{ $withdrawal->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $withdrawal->account_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $withdrawal->requested_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-yellow-600">₦{{ number_format($withdrawal->amount, 2) }}</p>
                                    <div class="flex space-x-1 mt-1">
                                        <button wire:click="approveWithdrawal({{ $withdrawal->id }})" 
                                                class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200 transition-colors"
                                                onclick="return confirm('Approve this withdrawal?')">
                                            Approve
                                        </button>
                                        <button wire:click="rejectWithdrawal({{ $withdrawal->id }})" 
                                                class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200 transition-colors"
                                                onclick="return confirm('Reject this withdrawal?')">
                                            Reject
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <p class="text-gray-500 mt-2">No pending withdrawals</p>
                        <p class="text-sm text-gray-400">All withdrawal requests have been processed</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>