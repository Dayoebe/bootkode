{{-- resources/views/livewire/affiliate/settings.blade.php --}}
<div class=" px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Affiliate Settings</h1>
        <p class="text-gray-600">Manage affiliate program settings and monitor performance</p>
    </div>

    <!-- System Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Affiliates</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $systemStats['total_affiliates'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Affiliates</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $systemStats['active_affiliates'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-user-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending Approval</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $systemStats['pending_approval'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Paid</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($systemStats['total_commissions_paid'], 2) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Settings -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Global Settings</h3>
        <form wire:submit.prevent="saveGlobalSettings">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Default Commission Rate (%)</label>
                    <input type="number" wire:model="globalCommissionRate" step="0.01" min="0" max="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('globalCommissionRate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Withdrawal (₦)</label>
                    <input type="number" wire:model="minimumWithdrawal" step="0.01" min="100"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('minimumWithdrawal') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" wire:model="autoApproveInstructors" id="autoApprove"
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="autoApprove" class="ml-2 block text-sm text-gray-900">Auto-approve Instructors</label>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Affiliate Management -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Affiliate Management</h3>
                
                <!-- Bulk Actions -->
                <div class="flex items-center space-x-3">
                    <select wire:model="bulkAction" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
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
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" class="rounded border-gray-300">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Affiliate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Earned</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Referrals</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($affiliates as $affiliate)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" wire:model="selectedAffiliates" value="{{ $affiliate->id }}" 
                                           class="rounded border-gray-300">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">{{ substr($affiliate->user->name, 0, 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $affiliate->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $affiliate->user->email }}</div>
                                            <div class="text-xs text-gray-400">Code: {{ $affiliate->referral_code }}</div>
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
                                           class="w-20 px-2 py-1 text-sm border border-gray-300 rounded" 
                                           step="0.1" min="0" max="100">
                                    <span class="text-xs text-gray-500">%</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                    {{ $affiliate->formatted_total_earned }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $affiliate->total_referrals }} total</div>
                                    <div class="text-xs text-gray-500">{{ $affiliate->active_referrals }} active</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center space-x-2">
                                        @if($affiliate->status !== 'active')
                                            <button wire:click="updateAffiliateStatus({{ $affiliate->id }}, 'active')"
                                                    class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        
                                        @if($affiliate->status !== 'suspended')
                                            <button wire:click="updateAffiliateStatus({{ $affiliate->id }}, 'suspended')"
                                                    class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif
                                        
                                        <button class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
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
                {{ $affiliates->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-users text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No affiliates found</h3>
                <p class="text-gray-500">Affiliates will appear here once they join the program.</p>
            </div>
        @endif
    </div>

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
</div>