<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" wire:poll.5000ms="pollTickets">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-question-circle mr-2"></i> Help & Support
        </h1>
        <p class="text-gray-400 mt-2">Access FAQs, submit support tickets, and view ticket history</p>
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: @entangle('activeTab') }" class="mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'faqs'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'faqs', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'faqs' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-book mr-2"></i> FAQs
                </button>
                <button @click="activeTab = 'submit_ticket'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'submit_ticket', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'submit_ticket' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-ticket-alt mr-2"></i> Submit Ticket
                </button>
                <button @click="activeTab = 'ticket_history'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'ticket_history', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'ticket_history' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-history mr-2"></i> Ticket History
                </button>
            </nav>
        </div>
    </div>

    <!-- FAQs -->
    <div x-show="activeTab === 'faqs'" class="bg-white shadow rounded-lg p-6 mb-8 animate__animated animate__fadeInUp">
        <div class="mb-4">
            <input wire:model.live.debounce.300ms="searchFaq" type="text" placeholder="Search FAQs..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div x-data="{ openFaq: null }" class="space-y-4">
            @forelse($faqs as $faq)
                <div class="border-b border-gray-200 pb-4">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $faq->question }}</h3>
                        <button @click="openFaq = openFaq === '{{ $faq->id }}' ? null : '{{ $faq->id }}'"
                                class="text-blue-600 hover:text-blue-800">
                            <i class="fas" :class="{ 'fa-chevron-up': openFaq === '{{ $faq->id }}', 'fa-chevron-down': openFaq !== '{{ $faq->id }}' }"></i>
                        </button>
                    </div>
                    <div x-show="openFaq === '{{ $faq->id }}'" x-transition class="mt-2 text-sm text-gray-600">
                        {{ $faq->answer }}
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No FAQs found.</p>
            @endforelse
        </div>
    </div>

    <!-- Submit Ticket -->
    <div x-show="activeTab === 'submit_ticket'" class="bg-white shadow rounded-lg p-6 mb-8 animate__animated animate__fadeInUp">
        <form wire:submit.prevent="submitTicket">
            <div class="space-y-6">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                    <input wire:model="subject" type="text" id="subject"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('subject') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea wire:model="description" id="description" rows="5"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="attachment" class="block text-sm font-medium text-gray-700">Attachment (optional)</label>
                    <input wire:model="attachment" type="file" id="attachment"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('attachment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                    <span wire:loading.remove><i class="fas fa-ticket-alt mr-2"></i> Submit Ticket</span>
                    <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Submitting...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Ticket History -->
    <div x-show="activeTab === 'ticket_history'" class="bg-white shadow rounded-lg p-6 animate__animated animate__fadeInUp">
        <div class="mb-4 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input wire:model.live.debounce.300ms="ticketSearch" type="text" placeholder="Search tickets..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <select wire:model.live="ticketStatusFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Statuses</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>
        </div>
        <div x-data="{ openTicket: null }" class="space-y-4">
            @forelse($tickets as $ticket)
                <div class="border-b border-gray-200 pb-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $ticket->subject }}</h3>
                            <p class="text-sm text-gray-600">{{ Str::limit($ticket->description, 100) }}</p>
                            <p class="text-xs text-gray-400">
                                Status: {{ ucfirst($ticket->status) }} | Created: {{ $ticket->created_at->format('M d, Y') }}
                                @if($ticket->responder)
                                    | Responded by: {{ $ticket->responder->name }}
                                @endif
                            </p>
                            @if($ticket->attachment)
                                <a href="{{ Storage::url($ticket->attachment) }}" target="_blank"
                                   class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-file mr-1"></i> View Attachment
                                </a>
                            @endif
                        </div>
                        <button @click="openTicket = openTicket === '{{ $ticket->id }}' ? null : '{{ $ticket->id }}'"
                                class="text-blue-600 hover:text-blue-800">
                            <i class="fas" :class="{ 'fa-chevron-up': openTicket === '{{ $ticket->id }}', 'fa-chevron-down': openTicket !== '{{ $ticket->id }}' }"></i>
                        </button>
                    </div>
                    <div x-show="openTicket === '{{ $ticket->id }}'" x-transition class="mt-2 text-sm text-gray-600">
                        {{ $ticket->description }}
                        @if($ticket->response)
                            <p class="mt-2 text-sm text-gray-600"><strong>Response:</strong> {{ $ticket->response }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No tickets found.</p>
            @endforelse
            <div class="mt-4">{{ $tickets->links() }}</div>
        </div>
    </div>
</div>