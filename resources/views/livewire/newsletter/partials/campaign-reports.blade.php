<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4 w-full md:w-auto">
            <!-- Search -->
            <div class="relative">
                <input
 aria-label="Search campaigns"                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search campaigns..."
                    class="pl-10 pr-4 py-2 w-full md:w-64 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent"
                >
                <i aria-hidden="true" class="fas fa-search absolute left-3 top-3 text-content-muted"></i>
            </div>
        </div>
    </div>

    <!-- Campaigns Table -->
    <div class="bg-surface rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <x-ui.table label="Campaign Reports records"><table class="min-w-full divide-y divide-line">
                <thead class="bg-surface-muted">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Campaign</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Sent</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Opens</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Clicks</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Bounces</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Unsubscribes</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-line">
                    @forelse($campaigns as $campaign)
                        <tr class="hover:bg-surface-muted">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <div class="text-sm font-medium text-content">{{ $campaign->name }}</div>
                                    <div class="text-sm text-content-muted">{{ $campaign->subject }}</div>
                                    <div class="text-xs text-content-muted">{{ $campaign->sent_at?->format('M j, Y H:i') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-content">
                                {{ $campaign->sent_count }}
                            </td>
                            <td class="px-6 py-4 text-sm text-brand">
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
                                    class="text-brand hover:text-brand text-sm"
                                    title="View Details"
                                >
                                    <i aria-hidden="true" class="fas fa-eye"></i> View Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-content-muted">
                                <i aria-hidden="true" class="fas fa-chart-pie text-4xl mb-4 text-gray-300"></i>
                                <p>No campaign reports available.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table></x-ui.table>
        </div>

        @if($campaigns->hasPages())
            <div class="px-6 py-3 border-t border-line">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>

    <!-- Campaign Details Modal -->
    @if($selectedCampaign)
        <div x-data="{ show: true }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-trap.inert.noscroll="show" role="dialog" aria-modal="true" aria-label="Dialog" tabindex="-1" @keydown.escape.stop="show = false">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false; $wire.closeCampaignDetails()"></div>
                <div class="relative bg-surface rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">Campaign Report: {{ $selectedCampaign->name }}</h3>
                            <button @click="show = false; $wire.closeCampaignDetails()" class="text-content-muted hover:text-content-muted" aria-label="Close">
                                <i aria-hidden="true" class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Campaign Overview -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-brand/10 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-brand">{{ $campaignDetails['sent'] ?? 0 }}</div>
                                <div class="text-sm text-brand">Total Sent</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $campaignDetails['opens'] ?? 0 }}</div>
                                <div class="text-sm text-green-800">Opens ({{ $campaignDetails['open_rate'] ?? 0 }}%)</div>
                            </div>
                            <div class="bg-brand/10 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-brand">{{ $campaignDetails['clicks'] ?? 0 }}</div>
                                <div class="text-sm text-brand">Clicks ({{ $campaignDetails['click_rate'] ?? 0 }}%)</div>
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
                            <div class="bg-surface-muted p-4 rounded-lg">
                                <div class="text-lg font-semibold text-content">Failed Deliveries</div>
                                <div class="text-2xl font-bold text-content-muted">{{ $campaignDetails['failed'] ?? 0 }}</div>
                            </div>
                        </div>

                        <!-- Recipient List -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold mb-3">Recipient Details</h4>
                            <div class="overflow-x-auto">
                                <x-ui.table label="Campaign Reports records"><table class="min-w-full divide-y divide-line">
                                    <thead class="bg-surface-muted">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Email</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Opened</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Clicked</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-surface divide-y divide-line">
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
                                                        <span class="text-content-muted">No</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-sm">
                                                    @if($selectedCampaign->interactions->where('subscriber_id', $interaction->subscriber_id)->where('type', 'click')->count() > 0)
                                                        <span class="text-green-600">Yes</span>
                                                    @else
                                                        <span class="text-content-muted">No</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table></x-ui.table>
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
