{{-- resources/views/livewire/affiliate/commission-reports.blade.php --}}
<div class=" px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class=" font-bold text-content bk-page-title">Commission Reports</h1>
        <p class="text-content-muted">Generate detailed reports and export your data</p>
    </div>

    <!-- Report Controls -->
    <div class="bg-surface rounded-lg shadow-sm p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-content mb-2">Report Type</label>
                <select aria-label="Report type" wire:model.live="reportType" class="w-full px-3 py-2 border border-line rounded-md shadow-sm focus:ring-brand/30 focus:border-brand">
                    <option value="overview">Overview</option>
                    <option value="commissions">Commission Details</option>
                    <option value="referrals">Referral Analysis</option>
                    <option value="performance">Performance Breakdown</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-content mb-2">From Date</label>
                <input aria-label="Date from" type="date" wire:model.live="dateFrom" class="w-full px-3 py-2 border border-line rounded-md shadow-sm focus:ring-brand/30 focus:border-brand">
            </div>

            <div>
                <label class="block text-sm font-medium text-content mb-2">To Date</label>
                <input aria-label="Date to" type="date" wire:model.live="dateTo" class="w-full px-3 py-2 border border-line rounded-md shadow-sm focus:ring-brand/30 focus:border-brand">
            </div>

            <div class="flex items-end">
                <button wire:click="exportReport" class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                    <i aria-hidden="true" class="fas fa-download mr-2"></i> Export CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-surface rounded-lg shadow-sm p-6 text-center">
            <div class="text-3xl font-bold text-brand">{{ $summaryStats['total_referrals'] }}</div>
            <div class="text-sm text-content-muted">Total Referrals</div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm p-6 text-center">
            <div class="text-3xl font-bold text-green-600">{{ $summaryStats['total_sales'] }}</div>
            <div class="text-sm text-content-muted">Total Sales</div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm p-6 text-center">
            <div class="text-3xl font-bold text-brand">₦{{ number_format($summaryStats['total_commissions'], 2) }}</div>
            <div class="text-sm text-content-muted">Total Commissions</div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm p-6 text-center">
            <div class="text-3xl font-bold text-yellow-600">{{ $summaryStats['conversion_rate'] }}%</div>
            <div class="text-sm text-content-muted">Conversion Rate</div>
        </div>
    </div>

    <!-- Report Data -->
    <div class="bg-surface rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-line">
            <h3 class="text-lg font-semibold text-content">
                {{ ucfirst($reportType) }} Report
                <span class="text-sm font-normal text-content-muted">({{ $dateFrom }} to {{ $dateTo }})</span>
            </h3>
        </div>

        @if(count($reportData) > 0)
            <div class="overflow-x-auto">
                <x-ui.table label="Commission Reports records"><table class="min-w-full divide-y divide-line">
                    <thead class="bg-surface-muted">
                        <tr>
                            @if(in_array($reportType, ['overview', 'commissions'], true))
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Course</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Student</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Commission</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Status</th>
                            @elseif($reportType === 'referrals')
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">User</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Total Spent</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Commission Earned</th>
                            @elseif($reportType === 'performance')
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Period</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Referrals</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Sales</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Commission</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Conversion</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-surface divide-y divide-line">
                        @foreach($reportData as $row)
                            <tr class="hover:bg-surface-muted">
                                @foreach($row as $cell)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-content">{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table></x-ui.table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i aria-hidden="true" class="fas fa-chart-bar text-6xl text-content-muted mb-4"></i>
                <h3 class="text-lg font-medium text-content mb-2">No data available</h3>
                <p class="text-content-muted">No data found for the selected report type and date range.</p>
            </div>
        @endif
    </div>
</div>
