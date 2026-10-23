{{-- resources/views/livewire/affiliate/settings.blade.php --}}
<div class=" px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class=" font-bold text-content bk-page-title">Affiliate Settings</h1>
        <p class="text-content-muted">Manage affiliate program settings and monitor performance</p>
    </div>

    <!-- System Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface rounded-lg shadow-sm p-6 border-l-4 border-brand">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Total Affiliates</p>
                    <p class="text-2xl font-bold text-content">{{ $systemStats['total_affiliates'] }}</p>
                </div>
                <div class="bg-brand/10 p-3 rounded-lg">
                    <i class="fas fa-users text-brand text-xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Active Affiliates</p>
                    <p class="text-2xl font-bold text-content">{{ $systemStats['active_affiliates'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-user-check text-green-600 text-xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Pending Approval</p>
                    <p class="text-2xl font-bold text-content">{{ $systemStats['pending_approval'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm p-6 border-l-4 border-brand">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Total Paid</p>
                    <p class="text-2xl font-bold text-content">₦{{ number_format($systemStats['total_commissions_paid'], 2) }}</p>
                </div>
                <div class="bg-brand/10 p-3 rounded-lg">
                    <i class="fas fa-money-bill-wave text-brand text-xl" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Settings -->
    <div class="bg-surface rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-content mb-4">Global Settings</h3>
        <p class="mb-4 text-sm text-content-muted">Global defaults come from server configuration. Account-specific commission rates can be managed below.</p>
        <dl class="grid gap-4 sm:grid-cols-3"><div><dt class="text-sm text-content-muted">Default commission</dt><dd class="font-semibold text-content">{{ $globalCommissionRate }}% of the platform share</dd></div><div><dt class="text-sm text-content-muted">Minimum withdrawal</dt><dd class="font-semibold text-content">₦{{ number_format($minimumWithdrawal, 2) }}</dd></div><div><dt class="text-sm text-content-muted">Automatic instructor approval</dt><dd class="font-semibold text-content">{{ $autoApproveInstructors ? 'Enabled' : 'Disabled' }}</dd></div></dl>
    </div>

    <!-- Affiliate Management -->
    <div class="bg-surface rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-line">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-content">Affiliate Management</h3>

                <!-- Bulk Actions -->
                <div class="flex items-center space-x-3">
                    <select aria-label="Bulk action" wire:model="bulkAction" class="px-3 py-2 border border-line rounded-md text-sm">
                        <option value="">Bulk Actions</option>
                        <option value="activate">Activate</option>
                        <option value="deactivate">Deactivate</option>
                        <option value="suspend">Suspend</option>
                    </select>
                    <button wire:click="bulkUpdateAffiliates"
                            class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                        Apply
                    </button>
                </div>
            </div>
        </div>

        @if($affiliates->count() > 0)
            <div class="overflow-x-auto">
                <x-ui.table label="Settings records"><table class="min-w-full divide-y divide-line">
                    <thead class="bg-surface-muted">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">
                                <input type="checkbox" class="rounded border-line">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Affiliate</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Commission Rate</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Total Earned</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Referrals</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-surface divide-y divide-line">
                        @foreach($affiliates as $affiliate)
                            <tr class="hover:bg-surface-muted">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input aria-label="Selected affiliates" type="checkbox" wire:model="selectedAffiliates" value="{{ $affiliate->id }}"
                                           class="rounded border-line">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-content">{{ substr($affiliate->user->name, 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-content">{{ $affiliate->user->name }}</div>
                                            <div class="text-sm text-content-muted">{{ $affiliate->user->email }}</div>
                                            <div class="text-xs text-content-muted">Code: {{ $affiliate->referral_code }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $affiliate->status === 'active' ? 'bg-green-100 text-green-800' :
                                           ($affiliate->status === 'inactive' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($affiliate->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" value="{{ $affiliate->commission_rate }}"
                                           wire:change="updateCommissionRate({{ $affiliate->id }}, $event.target.value)"
                                           class="w-20 px-2 py-1 text-sm border border-line rounded"
                                           step="0.1" min="0" max="100">
                                    <span class="text-xs text-content-muted">%</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                    {{ $affiliate->formatted_total_earned }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
                                    <div>{{ $affiliate->total_referrals }} total</div>
                                    <div class="text-xs text-content-muted">{{ $affiliate->active_referrals }} active</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center space-x-2">
                                        @if($affiliate->status !== 'active')
                                            <button wire:click="updateAffiliateStatus({{ $affiliate->id }}, 'active')"
                                                    class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-check" aria-hidden="true"></i>
                                            </button>
                                        @endif

                                        @if($affiliate->status !== 'suspended')
                                            <button wire:click="updateAffiliateStatus({{ $affiliate->id }}, 'suspended')"
                                                    class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-ban" aria-hidden="true"></i>
                                            </button>
                                        @endif

                                        <button class="text-brand hover:text-brand">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></x-ui.table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-line">
                {{ $affiliates->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-users text-6xl text-content-muted mb-4" aria-hidden="true"></i>
                <h3 class="text-lg font-medium text-content mb-2">No affiliates found</h3>
                <p class="text-content-muted">Affiliates will appear here once they join the program.</p>
            </div>
        @endif
    </div>

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
</div>
