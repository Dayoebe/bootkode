
<div class="space-y-6">
    <!-- Header Actions -->
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

            <!-- Status Filter -->
            <select wire:model.live="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="scheduled">Scheduled</option>
                <option value="sending">Sending</option>
                <option value="sent">Sent</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <button 
            wire:click="$set('showCreateModal', true)"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
        >
            <i class="fas fa-plus mr-2"></i>Create Campaign
        </button>
    </div>

    <!-- Campaigns Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($campaigns as $campaign)
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $campaign->name }}</h3>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                            {{ $campaign->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $campaign->status === 'scheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $campaign->status === 'sending' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $campaign->status === 'sent' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $campaign->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                        ">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>

                    <p class="text-sm text-gray-600 mb-3">{{ $campaign->subject }}</p>

                    <div class="flex justify-between text-sm text-gray-500 mb-4">
                        <span>Recipients: {{ number_format($campaign->total_recipients) }}</span>
                        <span>Created: {{ $campaign->created_at->format('M j') }}</span>
                    </div>

                    @if($campaign->status === 'sent')
                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
                            <div class="text-center">
                                <div class="font-semibold text-green-600">{{ $campaign->open_rate }}%</div>
                                <div class="text-gray-500">Open Rate</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-blue-600">{{ $campaign->click_rate }}%</div>
                                <div class="text-gray-500">Click Rate</div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2">
                            @if($campaign->canBeSent())
                                <button 
                                    wire:click="editCampaign({{ $campaign->id }})"
                                    class="text-blue-600 hover:text-blue-900 text-sm"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>
                            @endif
                            <button 
                                wire:click="duplicateCampaign({{ $campaign->id }})"
                                class="text-purple-600 hover:text-purple-900 text-sm"
                                title="Duplicate"
                            >
                                <i class="fas fa-copy"></i>
                            </button>
                            @if($campaign->status !== 'sent' && $campaign->status !== 'sending')
                                <button 
                                    wire:click="deleteCampaign({{ $campaign->id }})"
                                    onclick="return confirm('Are you sure?')"
                                    class="text-red-600 hover:text-red-900 text-sm"
                                    title="Delete"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>

                        <div class="flex space-x-1">
                            @if($campaign->canBeSent())
                                <button 
                                    wire:click="sendCampaign({{ $campaign->id }})"
                                    onclick="return confirm('Are you sure you want to send this campaign?')"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors"
                                >
                                    Send Now
                                </button>
                            @endif
                            @if(in_array($campaign->status, ['scheduled','sending']))
                                <button 
                                    wire:click="cancelCampaign({{ $campaign->id }})"
                                    onclick="return confirm('Are you sure you want to cancel this campaign?')"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm transition-colors"
                                >
                                    Cancel
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-paper-plane text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No campaigns found.</p>
                <button 
                    wire:click="$set('showCreateModal', true)"
                    class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                >
                    Create Your First Campaign
                </button>
            </div>
        @endforelse
    </div>

    @if($campaigns->hasPages())
        <div class="mt-6">
            {{ $campaigns->links() }}
        </div>
    @endif

    <!-- Create Campaign Modal -->
    <div x-data="{ show: @entangle('showCreateModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Create New Campaign</h3>
                    <form wire:submit.prevent="createCampaign">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Campaign Name *</label>
                                    <input 
                                        type="text" 
                                        wire:model="name" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Subject *</label>
                                    <input 
                                        type="text" 
                                        wire:model="subject" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                    @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Preview Text</label>
                                    <textarea 
                                        wire:model="previewText" 
                                        rows="3"
                                        placeholder="This text appears in email clients as a preview..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Name *</label>
                                    <input 
                                        type="text" 
                                        wire:model="fromName" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Email *</label>
                                    <input 
                                        type="email" 
                                        wire:model="fromEmail" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reply To</label>
                                    <input 
                                        type="email" 
                                        wire:model="replyTo" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Send (Optional)</label>
                                    <input 
                                        type="datetime-local" 
                                        wire:model="scheduledAt" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Template (Optional)</label>
                                    <select 
                                        wire:model.live="templateId" 
                                        wire:change="loadTemplate($event.target.value)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">Select a template...</option>
                                        @foreach($templates as $template)
                                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Content *</label>
                                    <textarea 
                                        wire:model="htmlContent" 
                                        rows="15"
                                        required
                                        placeholder="Enter your email HTML content here..."
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    ></textarea>
                                    @error('htmlContent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    <p class="text-xs text-gray-500 mt-1">
                                        Available variables: @{{subscriber_email}}, @{{subscriber_first_name}}, @{{subscriber_last_name}}, @{{unsubscribe_url}}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button" 
                                @click="show = false"
                                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            >
                                {{ $scheduledAt ? 'Schedule Campaign' : 'Create Draft' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Campaign Modal -->
    <div x-data="{ show: @entangle('showEditModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Edit Campaign</h3>
                    <form wire:submit.prevent="updateCampaign">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Campaign Name *</label>
                                    <input 
                                        type="text" 
                                        wire:model="name" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Subject *</label>
                                    <input 
                                        type="text" 
                                        wire:model="subject" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                    @error('subject') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Preview Text</label>
                                    <textarea 
                                        wire:model="previewText" 
                                        rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Name *</label>
                                    <input 
                                        type="text" 
                                        wire:model="fromName" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">From Email *</label>
                                    <input 
                                        type="email" 
                                        wire:model="fromEmail" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reply To</label>
                                    <input 
                                        type="email" 
                                        wire:model="replyTo" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Schedule Send (Optional)</label>
                                    <input 
                                        type="datetime-local" 
                                        wire:model="scheduledAt" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Content *</label>
                                    <textarea 
                                        wire:model="htmlContent" 
                                        rows="20"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    ></textarea>
                                    @error('htmlContent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button" 
                                @click="show = false"
                                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            >
                                Update Campaign
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
