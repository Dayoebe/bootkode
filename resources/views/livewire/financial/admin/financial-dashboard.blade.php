<!-- resources/views/livewire/financial/admin-financial-dashboard.blade.php -->
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class=" font-bold text-themed-primary bk-page-title">Financial Dashboard</h1>
        <p class="text-themed-secondary">Monitor platform revenue, withdrawals, and financial health</p>
        <div class="mt-3 flex flex-wrap gap-2">
            <a href="{{ route('admin.revenue.reports') }}" class="inline-flex rounded-lg border border-themed-secondary px-3 py-2 text-sm font-semibold text-themed-primary hover:border-brand hover:text-brand">Revenue reports</a>
            <a href="{{ route('admin.commercial.readiness') }}" class="inline-flex rounded-lg border border-themed-secondary px-3 py-2 text-sm font-semibold text-themed-primary hover:border-brand hover:text-brand">Commercial readiness</a>
        </div>
    </div>

    @if($financialUnavailable ?? false)
        <x-ui.empty-state title="Financial data is unavailable" description="The report could not be loaded. Try again shortly or contact platform support."><x-secondary-button wire:click="$refresh">Try again</x-secondary-button></x-ui.empty-state>
    @else
    <!-- Tab Navigation -->
    <div class="border-b border-themed-primary mb-8">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="setTab('overview')"
                class="{{ $selectedTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-themed-secondary hover:text-content hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Overview
            </button>
            <button wire:click="setTab('withdrawals')"
                class="{{ $selectedTab === 'withdrawals' ? 'border-blue-500 text-blue-600' : 'border-transparent text-themed-secondary hover:text-content hover:border-gray-300' }} whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                Withdrawals
            </button>
        </nav>
    </div>

    @if($selectedTab === 'overview')
        <!-- Revenue Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-themed-secondary rounded-lg shadow-sm p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary">Total Revenue</p>
                        <p class="text-2xl font-bold text-themed-primary">
                            {{ isset($revenueAnalytics['formatted']['total_course_sales']) ? $revenueAnalytics['formatted']['total_course_sales'] : '₦0.00' }}
                        </p>
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

            <div class="bg-themed-secondary rounded-lg shadow-sm p-6 border-l-4 border-brand">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary">Platform Commission</p>
                        <p class="text-2xl font-bold text-themed-primary">
                            {{ isset($revenueAnalytics['formatted']['platform_commission']) ? $revenueAnalytics['formatted']['platform_commission'] : '₦0.00' }}
                        </p>
                    </div>
                    <div class="bg-brand/10 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 012-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-themed-secondary rounded-lg shadow-sm p-6 border-l-4 border-brand">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary">Instructor Earnings</p>
                        <p class="text-2xl font-bold text-themed-primary">
                            {{ isset($revenueAnalytics['formatted']['instructor_earnings']) ? $revenueAnalytics['formatted']['instructor_earnings'] : '₦0.00' }}
                        </p>
                    </div>
                    <div class="bg-brand/10 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-themed-secondary rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary">Pending Withdrawals</p>
                        <p class="text-2xl font-bold text-themed-primary">
                            {{ isset($revenueAnalytics['formatted']['pending_withdrawals']) ? $revenueAnalytics['formatted']['pending_withdrawals'] : '₦0.00' }}
                        </p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paystack Balance -->
        @if(isset($paystackBalance['success']) && $paystackBalance['success'])
            <div class="bg-themed-secondary rounded-lg shadow-sm p-6 mb-8">
                <h3 class="text-lg font-semibold text-themed-primary mb-4">Paystack Account Balance</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if(isset($paystackBalance['balances']) && is_array($paystackBalance['balances']))
                        @foreach($paystackBalance['balances'] as $balance)
                            <div class="bg-themed-tertiary rounded-lg p-4">
                                <p class="text-sm text-themed-secondary">{{ strtoupper($balance['currency'] ?? 'NGN') }}</p>
                                <p class="text-xl font-bold text-themed-primary">₦{{ number_format(($balance['balance'] ?? 0) / 100, 2) }}</p>
                            </div>
                        @endforeach
                    @else
                        <div class="bg-themed-tertiary rounded-lg p-4">
                            <p class="text-sm text-themed-secondary">NGN</p>
                            <p class="text-xl font-bold text-themed-primary">₦0.00</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="bg-themed-secondary rounded-lg shadow-sm p-6 mb-8">
                <h3 class="text-lg font-semibold text-themed-primary mb-4">Paystack Account Balance</h3>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800 text-sm">Unable to fetch Paystack balance at this time.</p>
                </div>
            </div>
        @endif
    @endif

    @if($selectedTab === 'withdrawals')
        <!-- Pending Withdrawals -->
        <div class="bg-themed-secondary rounded-lg shadow-sm">
            <div class="p-6 border-b border-themed-primary">
                <h3 class="text-lg font-semibold text-themed-primary">Pending Withdrawal Requests</h3>
            </div>
            <div class="overflow-x-auto">
                @if(isset($pendingWithdrawals) && $pendingWithdrawals->count() > 0)
                    <x-ui.table label="Financial Dashboard records"><table class="min-w-full divide-y divide-themed-primary">
                        <thead class="bg-themed-tertiary">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">
                                    Instructor</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">
                                    Amount</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Bank
                                    Details</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">
                                    Requested</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-themed-secondary divide-y divide-themed-primary">
                            @foreach($pendingWithdrawals as $withdrawal)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full"
                                                    src="{{ $withdrawal->user->profile_picture ?? 'https://ui-avatars.com/api/?name=' . urlencode($withdrawal->user->name ?? 'Unknown') }}"
                                                    alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-themed-primary">{{ $withdrawal->user->name ?? 'Unknown User' }}</div>
                                                <div class="text-sm text-themed-secondary">{{ $withdrawal->user->email ?? 'No email' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-themed-primary">
                                        {{ $withdrawal->formatted_amount ?? '₦0.00' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-themed-primary">
                                        <div>
                                            <p class="font-medium">{{ $withdrawal->account_name ?? 'N/A' }}</p>
                                            <p class="text-themed-secondary">{{ $withdrawal->account_number ?? 'N/A' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-secondary">
                                        {{ $withdrawal->requested_at ? $withdrawal->requested_at->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <button wire:click="approveWithdrawal({{ $withdrawal->id }})"
                                            class="text-green-600 hover:text-green-900"
                                            onclick="return confirm('Are you sure you want to approve this withdrawal?')">
                                            Approve
                                        </button>
                                        <button wire:click="rejectWithdrawal({{ $withdrawal->id }})"
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to reject this withdrawal?')">
                                            Reject
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table></x-ui.table>
                    <div class="px-6 py-4 border-t border-themed-primary">
                        {{ $pendingWithdrawals->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-content-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-themed-primary">No pending withdrawals</h3>
                        <p class="mt-1 text-sm text-themed-secondary">All withdrawal requests have been processed.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-sm z-50">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-sm z-50">
            {{ session('error') }}
        </div>
    @endif
    @endif
</div>
