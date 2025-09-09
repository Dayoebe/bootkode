<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
            <!-- Search -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search campaigns..."
                    class="pl-10 pr-4 py-2 w-full md:w-64 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opens</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clicks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bounces</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unsubscribes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($campaigns as $campaign)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-gray-900">{{ $campaign->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $campaign->subject }}</div>
                                    <div class="text-xs text-gray-400">{{ $campaign->sent_at?->format('M j, Y H:i') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $campaign->sent_count }}
                            </td>
                            <td class="px-6 py-4 text-sm text-blue-600">
                                {{ $campaign->open_count }} ({{ $campaign->sent_count > 0 ? round(($campaign->open_count / $campaign->sent_count) * 100, 2) : 0 }}%)
                            </td>
                            <td class="px-6 py-4 text-sm text-green-600">
                                {{ $campaign->click_count }} ({{ $campaign->sent_count > 0 ? round(($campaign->click_count / $campaign->sent_count) * 100, 2) : 0 }}%)
                            </td>
                            <td class="px-6 py-4 text-sm text-red-600">
                                {{ $campaign->bounce_count }} ({{ $campaign->sent_count > 0 ? round(($campaign->bounce_count / $campaign->sent_count) * 100, 2) : 0 }}%)
                            </td>
                            <td class="px-6 py-4 text-sm text-yellow-600">
                                {{ $campaign->unsubscribe_count }} ({{ $campaign->sent_count > 0 ? round(($campaign->unsubscribe_count / $campaign->sent_count) * 100, 2) : 0 }}%)
                            </td>
                            <td class="px-6 py-4">
                                <button 
                                    wire:click="selectCampaign({{ $campaign->id }})"
                                    class="text-blue-600 hover:text-blue-900 text-sm"
                                    title="View Details"
                                >
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-chart-pie text-4xl mb-4 text-gray-300"></i>
                                <p>No campaign reports available.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($campaigns->hasPages())
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>

    <!-- Campaign Details Modal -->
    @if($selectedCampaign)
        <div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false; $wire.closeCampaignDetails()"></div>
                <div class="relative bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Campaign Report: {{ $selectedCampaign->name }}</h3>
                            <button @click="show = false; $wire.closeCampaignDetails()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Campaign Overview -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $campaignDetails['sent'] ?? 0 }}</div>
                                <div class="text-sm text-blue-800">Total Sent</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $campaignDetails['opens'] ?? 0 }}</div>
                                <div class="text-sm text-green-800">Opens ({{ $campaignDetails['open_rate'] ?? 0 }}%)</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ $campaignDetails['clicks'] ?? 0 }}</div>
                                <div class="text-sm text-purple-800">Clicks ({{ $campaignDetails['click_rate'] ?? 0 }}%)</div>
                            </div>
                            <div class="bg-red-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-red-600">{{ $campaignDetails['bounces'] ?? 0 }}</div>
                                <div class="text-sm text-red-800">Bounces ({{ $campaignDetails['bounce_rate'] ?? 0 }}%)</div>
                            </div>
                        </div>
                        <!-- Add this section to the campaign details modal -->
@if(!empty($campaignDetails['failure_reasons']))
<div class="mb-6">
    <h4 class="text-md font-semibold mb-3">Failure Reasons</h4>
    <div class="bg-red-50 p-4 rounded-lg">
        @foreach($campaignDetails['failure_reasons'] as $reason => $count)
            <div class="mb-2">
                <span class="font-medium text-red-800">{{ $reason }}:</span>
                <span class="text-red-600">{{ $count }} occurrence(s)</span>
            </div>
        @endforeach
    </div>
</div>
@endif

                        <!-- Additional Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-yellow-50 p-4 rounded-lg">
                                <div class="text-lg font-semibold text-yellow-800">Unsubscribes</div>
                                <div class="text-2xl font-bold text-yellow-600">{{ $campaignDetails['unsubscribes'] ?? 0 }} ({{ $campaignDetails['unsubscribe_rate'] ?? 0 }}%)</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-lg font-semibold text-gray-800">Failed Deliveries</div>
                                <div class="text-2xl font-bold text-gray-600">{{ $campaignDetails['failed'] ?? 0 }}</div>
                            </div>
                        </div>

                        <!-- Recipient List -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold mb-3">Recipient Details</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opened</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clicked</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($selectedCampaign->interactions->where('type', 'send') as $interaction)
                                            <tr>
                                                <td class="px-4 py-2 text-sm">{{ $interaction->subscriber->email }}</td>
                                                <td class="px-4 py-2 text-sm">
                                                    <span class="px-2 py-1 text-xs rounded-full 
                                                        {{ $interaction->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst($interaction->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-2 text-sm">
                                                    @if($selectedCampaign->interactions->where('subscriber_id', $interaction->subscriber_id)->where('type', 'open')->count() > 0)
                                                        <span class="text-green-600">Yes</span>
                                                    @else
                                                        <span class="text-gray-400">No</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-sm">
                                                    @if($selectedCampaign->interactions->where('subscriber_id', $interaction->subscriber_id)->where('type', 'click')->count() > 0)
                                                        <span class="text-green-600">Yes</span>
                                                    @else
                                                        <span class="text-gray-400">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button 
                                @click="show = false; $wire.closeCampaignDetails()"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>