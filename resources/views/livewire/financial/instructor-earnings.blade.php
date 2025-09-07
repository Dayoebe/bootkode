<!-- resources/views/livewire/financial/instructor-earnings.blade.php -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Instructor Earnings</h1>
        <p class="text-gray-600">Track your course sales and earnings performance</p>
    </div>

    <!-- Period Selector -->
    <div class="mb-6">
        <select wire:model="selectedPeriod" class="rounded-md border-gray-300 shadow-sm">
            <option value="last_7_days">Last 7 Days</option>
            <option value="last_30_days">Last 30 Days</option>
            <option value="last_90_days">Last 90 Days</option>
            <option value="last_365_days">Last Year</option>
        </select>
    </div>

    <!-- Earnings Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100">Available Balance</p>
                    <p class="text-3xl font-bold">₦{{ number_format($earningsData['available_for_withdrawal'], 2) }}</p>
                </div>
                <div class="bg-white bg-opacity-20 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
            @if($earningsData['available_for_withdrawal'] >= 1000)
                <button wire:click="openWithdrawalModal" class="mt-4 bg-white text-green-600 py-2 px-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Request Withdrawal
                </button>
            @else
                <p class="mt-4 text-green-100 text-sm">Minimum withdrawal: ₦1,000</p>
            @endif
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Earnings</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($earningsData['total_earnings'], 2) }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Courses Sold</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($earningsData['courses_sold']) }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending Withdrawals</p>
                    <p class="text-2xl font-bold text-gray-900">₦{{ number_format($earningsData['pending_withdrawals'], 2) }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performing Courses -->
    <div class="bg-white rounded-xl shadow-lg mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Top Performing Courses</h3>
        </div>
        <div class="p-6">
            @if($earningsData['top_courses']->count() > 0)
                <div class="space-y-4">
                    @foreach($earningsData['top_courses'] as $courseData)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <img src="{{ $courseData['course']->thumbnail ?? 'https://via.placeholder.com/60' }}" 
                                     alt="{{ $courseData['course']->title }}" 
                                     class="w-12 h-12 rounded-lg object-cover">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $courseData['course']->title }}</h4>
                                    <p class="text-sm text-gray-500">{{ $courseData['sales_count'] }} sales</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-bold text-green-600">₦{{ number_format($courseData['total_earnings'], 2) }}</p>
                                <p class="text-sm text-gray-500">Avg: ₦{{ number_format($courseData['average_per_sale'], 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-gray-500">No course sales in this period</p>
            @endif
        </div>
    </div>

    <!-- Recent Earnings -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Earnings</h3>
        </div>
        <div class="overflow-x-auto">
            @if($earningsData['recent_earnings']->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Course</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($earningsData['recent_earnings'] as $earning)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $earning->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $earning->transactionable->title ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                    ₦{{ number_format($earning->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $earning->description }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No earnings found for this period</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Withdrawal Modal -->
    @if($showWithdrawalModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" wire:click="closeWithdrawalModal"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="requestWithdrawal">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Request Withdrawal</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="withdrawalAmount" class="block text-sm font-medium text-gray-700">Amount (₦)</label>
                                    <input type="number" id="withdrawalAmount" wire:model="withdrawalAmount" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                           min="1000" max="{{ $earningsData['available_for_withdrawal'] }}" step="0.01">
                                    @error('withdrawalAmount') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="selectedBankCode" class="block text-sm font-medium text-gray-700">Bank</label>
                                    <select id="selectedBankCode" wire:model="selectedBankCode" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                        <option value="">Select Bank</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank['code'] }}">{{ $bank['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedBankCode') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="accountNumber" class="block text-sm font-medium text-gray-700">Account Number</label>
                                    <input type="text" id="accountNumber" wire:model="accountNumber" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                           maxlength="10">
                                    @error('accountNumber') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="accountName" class="block text-sm font-medium text-gray-700">Account Name</label>
                                    <input type="text" id="accountName" wire:model="accountName" 
                                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50" readonly>
                                    @if($isResolvingAccount) <p class="text-blue-600 text-sm mt-1">Resolving account details...</p> @endif
                                    @error('accountName') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Submit Request
                            </button>
                            <button type="button" wire:click="closeWithdrawalModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
</div>
