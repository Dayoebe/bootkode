<div class="min-h-screen bg-themed-primary py-8" wire:poll.5000ms="pollTickets">
    <div class="px-4 sm:px-6 lg:px-8">
        <!-- Enhanced Header -->
        <div class="relative bg-gradient-to-br from-cyan-600 via-blue-700 to-indigo-800 rounded-2xl shadow-2xl overflow-hidden mb-8">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"4\" /%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

            <div class="relative p-8">
                <div class="flex items-center">
                    <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl mr-6 shadow-lg">
                        <i class="fas fa-question-circle text-3xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-white mb-2">Help & Support Center</h1>
                        <p class="text-blue-100 text-lg">Find answers, submit tickets, and track your requests</p>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center">
                            <i class="fas fa-book-open text-white/80 text-xl mr-3"></i>
                            <div>
                                <p class="text-white/80 text-sm">Knowledge Base</p>
                                <p class="text-white text-lg font-semibold">{{ $faqs->count() }} Articles</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center">
                            <i class="fas fa-ticket-alt text-white/80 text-xl mr-3"></i>
                            <div>
                                <p class="text-white/80 text-sm">Active Tickets</p>
                                <p class="text-white text-lg font-semibold">
                                    {{ $tickets->where('status', '!=', 'resolved')->count() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-white/80 text-xl mr-3"></i>
                            <div>
                                <p class="text-white/80 text-sm">Avg Response</p>
                                <p class="text-white text-lg font-semibold">2-4 Hours</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Tab Container -->
        <div x-data="{
            activeTab: @entangle('activeTab').live,
            openFaq: null,
            openTicket: null,
            tabs: [
                { id: 'faqs', label: 'Knowledge Base', icon: 'fas fa-book-open', count: {{ $faqs->count() }} },
                { id: 'submit_ticket', label: 'New Ticket', icon: 'fas fa-plus-circle', count: null },
                { id: 'ticket_history', label: 'My Tickets', icon: 'fas fa-history', count: {{ $tickets->total() }} }
            ]
        }" class="mb-8">

            <!-- Enhanced Tabs Navigation -->
            <div class="mb-8">
                <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-2">
                    <nav class="flex space-x-2" aria-label="Tabs">
                        <template x-for="tab in tabs" :key="tab.id">
                            <button @click="activeTab = tab.id"
                                :class="{
                                    'bg-gradient-to-r from-cyan-600 to-blue-600 text-white shadow-lg': activeTab === tab.id,
                                    'text-themed-secondary hover:text-themed-primary hover:bg-themed-tertiary': activeTab !== tab.id
                                }"
                                class="relative py-4 px-6 font-medium text-sm rounded-xl border border-transparent flex items-center transition-all duration-300 ease-out flex-1 justify-center">
                                <i :class="tab.icon" class="mr-3 text-lg"></i>
                                <span x-text="tab.label"></span>
                                <span x-show="tab.count !== null"
                                    :class="{
                                        'bg-white/20 text-white': activeTab === tab.id,
                                        'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-800 dark:text-cyan-300': activeTab !== tab.id
                                    }"
                                    class="ml-2 px-2 py-1 text-xs rounded-full font-medium transition-colors duration-300"
                                    x-text="tab.count">
                                </span>
                                <!-- Active indicator -->
                                <div x-show="activeTab === tab.id"
                                    class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-8 h-1 bg-white rounded-full"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-0"
                                    x-transition:enter-end="opacity-100 scale-100">
                                </div>
                            </button>
                        </template>
                    </nav>
                </div>
            </div>

            <!-- FAQs Tab -->
            <div x-show="activeTab === 'faqs'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">
                <!-- Search Header -->
                <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-themed-primary">Knowledge Base</h2>
                            <p class="text-themed-secondary mt-1">Find answers to common questions</p>
                        </div>
                        <div class="relative w-full md:w-80">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-themed-secondary"></i>
                            </div>
                            <input wire:model.live.debounce.300ms="searchFaq" type="text"
                                placeholder="Search articles..."
                                class="pl-12 w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 text-themed-primary">
                            <div wire:loading.delay wire:target="searchFaq"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <i class="fas fa-circle-notch fa-spin text-themed-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ List -->
                <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary overflow-hidden">
                    <div class="divide-y divide-themed-primary">
                        @forelse($faqs as $index => $faq)
                            <div class="transition-all duration-200 hover:bg-themed-tertiary">
                                <div class="flex justify-between items-center p-6 cursor-pointer"
                                    @click="openFaq = openFaq === {{ $faq->id }} ? null : {{ $faq->id }}">
                                    <div class="flex items-start flex-1">
                                        <div class="bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400 rounded-full p-2 mr-4 mt-1 flex-shrink-0">
                                            <i class="fas fa-question text-sm"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-themed-primary pr-4">{{ $faq->question }}</h3>
                                            <p class="text-sm text-themed-secondary mt-1">
                                                {{ Str::limit(strip_tags($faq->answer), 120) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        <i class="fas transition-transform duration-300 text-cyan-600 dark:text-cyan-400"
                                            :class="{
                                                'fa-chevron-up': openFaq === {{ $faq->id }},
                                                'fa-chevron-down': openFaq !== {{ $faq->id }}
                                            }"></i>
                                    </div>
                                </div>
                                <div x-show="openFaq === {{ $faq->id }}"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 max-h-0"
                                    x-transition:enter-end="opacity-100 max-h-screen"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 max-h-screen"
                                    x-transition:leave-end="opacity-0 max-h-0" class="px-6 pb-6 overflow-hidden">
                                    <div class="bg-cyan-100/30 dark:bg-cyan-900/20 rounded-xl p-4 border-l-4 border-cyan-400 dark:border-cyan-600">
                                        <div class="prose prose-sm max-w-none text-themed-primary">
                                            {!! nl2br(e($faq->answer)) !!}
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="text-sm text-themed-secondary">
                                            <i class="fas fa-clock mr-1"></i>
                                            Last updated {{ $faq->updated_at->diffForHumans() }}
                                        </div>
                                        <div class="flex space-x-2">
                                            <button
                                                class="text-sm text-cyan-600 dark:text-cyan-400 hover:text-cyan-800 dark:hover:text-cyan-300 transition-colors">
                                                <i class="fas fa-thumbs-up mr-1"></i> Helpful
                                            </button>
                                            <button
                                                class="text-sm text-themed-secondary hover:text-themed-primary transition-colors">
                                                <i class="fas fa-share mr-1"></i> Share
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16">
                                <div
                                    class="bg-themed-tertiary rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                                    <i class="fas fa-search fa-2x text-themed-secondary"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-themed-primary mb-2">No articles found</h3>
                                <p class="text-themed-secondary mb-6">Try adjusting your search terms or browse all articles.</p>
                                <button wire:click="$set('searchFaq', '')"
                                    class="bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-700 dark:hover:bg-cyan-800 text-white px-4 py-2 rounded-lg transition-colors">
                                    Clear Search
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Submit Ticket Tab -->
            <div x-show="activeTab === 'submit_ticket'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary">
                <div class="p-6 border-b border-themed-primary">
                    <h2 class="text-2xl font-bold text-themed-primary">Submit New Ticket</h2>
                    <p class="text-themed-secondary mt-1">Describe your issue and we'll get back to you soon</p>
                </div>

                <form wire:submit.prevent="submitTicket" class="p-6">
                    <div class="space-y-6">
                        <!-- Subject Field -->
                        <div>
                            <label for="subject" class="block text-sm font-semibold text-themed-primary mb-2">
                                <i class="fas fa-tag mr-2 text-cyan-600 dark:text-cyan-400"></i>Subject *
                            </label>
                            <input wire:model="subject" type="text" id="subject"
                                class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-xl shadow-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 text-themed-primary @error('subject') border-red-500 @enderror"
                                placeholder="Brief description of your issue">
                            @error('subject')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Description Field -->
                        <div>
                            <label for="description" class="block text-sm font-semibold text-themed-primary mb-2">
                                <i class="fas fa-align-left mr-2 text-cyan-600 dark:text-cyan-400"></i>Description *
                            </label>
                            <textarea wire:model="description" id="description" rows="6"
                                class="w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-xl shadow-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 text-themed-primary @error('description') border-red-500 @enderror"
                                placeholder="Please provide detailed information about your issue, including steps to reproduce if applicable"></textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Priority Selector -->
                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-2">
                                <i class="fas fa-exclamation-triangle mr-2 text-cyan-600 dark:text-cyan-400"></i>Priority
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="relative">
                                    <input type="radio" name="priority" value="low" class="sr-only peer">
                                    <div
                                        class="p-3 border-2 border-themed-primary rounded-xl cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-100/20 dark:peer-checked:bg-green-900/20 hover:border-green-300 dark:hover:border-green-700 transition-all duration-200 text-center">
                                        <i class="fas fa-arrow-down text-green-600 dark:text-green-400 text-lg mb-1 block"></i>
                                        <p class="text-sm font-medium text-themed-primary">Low</p>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="priority" value="medium" class="sr-only peer" checked>
                                    <div
                                        class="p-3 border-2 border-themed-primary rounded-xl cursor-pointer peer-checked:border-yellow-500 peer-checked:bg-yellow-100/20 dark:peer-checked:bg-yellow-900/20 hover:border-yellow-300 dark:hover:border-yellow-700 transition-all duration-200 text-center">
                                        <i class="fas fa-minus text-yellow-600 dark:text-yellow-400 text-lg mb-1 block"></i>
                                        <p class="text-sm font-medium text-themed-primary">Medium</p>
                                    </div>
                                </label>
                                <label class="relative">
                                    <input type="radio" name="priority" value="high" class="sr-only peer">
                                    <div
                                        class="p-3 border-2 border-themed-primary rounded-xl cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-100/20 dark:peer-checked:bg-red-900/20 hover:border-red-300 dark:hover:border-red-700 transition-all duration-200 text-center">
                                        <i class="fas fa-arrow-up text-red-600 dark:text-red-400 text-lg mb-1 block"></i>
                                        <p class="text-sm font-medium text-themed-primary">High</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-8 flex space-x-4">
                        <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 inline-flex justify-center items-center px-6 py-4 border border-transparent text-base font-semibold rounded-xl shadow-lg text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 dark:focus:ring-offset-themed-secondary disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105">
                            <span wire:loading.remove>
                                <i class="fas fa-paper-plane mr-3"></i> Submit Ticket
                            </span>
                            <span wire:loading>
                                <i class="fas fa-circle-notch fa-spin mr-3"></i> Processing...
                            </span>
                        </button>
                        <button type="button" @click="activeTab = 'faqs'"
                            class="px-6 py-4 border border-themed-primary text-themed-primary font-medium rounded-xl hover:bg-themed-tertiary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 dark:focus:ring-offset-themed-secondary transition-all duration-200">
                            <i class="fas fa-book-open mr-2"></i> Check FAQs First
                        </button>
                    </div>
                </form>
            </div>

            <!-- Ticket History Tab -->
            <div x-show="activeTab === 'ticket_history'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                class="space-y-6">
                <!-- Search and Filter Header -->
                <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-themed-primary">My Support Tickets</h2>
                            <p class="text-themed-secondary mt-1">Track and manage your support requests</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-themed-secondary"></i>
                                </div>
                                <input wire:model.live.debounce.300ms="ticketSearch" type="text"
                                    placeholder="Search tickets..."
                                    class="pl-12 w-full sm:w-80 px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200 text-themed-primary">
                            </div>
                            <select wire:model.live="ticketStatusFilter"
                                class="px-4 py-3 border border-themed-primary bg-themed-secondary text-themed-primary rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-200">
                                <option value="all">All Statuses</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Tickets List -->
                <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary overflow-hidden">
                    <div class="divide-y divide-themed-primary">
                        @forelse($tickets as $ticket)
                            <div class="transition-all duration-200 hover:bg-themed-tertiary">
                                <div class="flex justify-between items-start p-6 cursor-pointer"
                                    @click="openTicket = openTicket === {{ $ticket->id }} ? null : {{ $ticket->id }}">
                                    <div class="flex-1">
                                        <div class="flex items-center flex-wrap gap-3 mb-3">
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                @if ($ticket->status === 'open') bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300
                                                @elseif($ticket->status === 'in_progress') bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300
                                                @else bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 @endif">
                                                <i
                                                    class="fas 
                                                    @if ($ticket->status === 'open') fa-clock
                                                    @elseif($ticket->status === 'in_progress') fa-cog fa-spin
                                                    @else fa-check @endif mr-1"></i>
                                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                            </span>
                                            <span class="text-sm text-themed-secondary flex items-center">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                {{ $ticket->created_at->format('M d, Y \a\t g:i A') }}
                                            </span>
                                            <span class="text-sm text-themed-secondary">
                                                #{{ $ticket->id }}
                                            </span>
                                        </div>

                                        <h3 class="text-lg font-semibold text-themed-primary mb-2">{{ $ticket->subject }}</h3>
                                        <p class="text-themed-secondary mb-3">{{ Str::limit($ticket->description, 150) }}</p>

                                        <div class="flex items-center space-x-4">
                                            @if ($ticket->attachment)
                                                <a href="{{ Storage::url($ticket->attachment) }}" target="_blank"
                                                    class="inline-flex items-center text-sm text-cyan-600 dark:text-cyan-400 hover:text-cyan-800 dark:hover:text-cyan-300 transition-colors"
                                                    onclick="event.stopPropagation();">
                                                    <i class="fas fa-paperclip mr-1"></i> View Attachment
                                                </a>
                                            @endif
                                            @if ($ticket->response)
                                                <span class="inline-flex items-center text-sm text-green-600 dark:text-green-400">
                                                    <i class="fas fa-reply mr-1"></i> Response Available
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 ml-4 flex flex-col items-center">
                                        <i class="fas transition-transform duration-300 text-cyan-600 dark:text-cyan-400 text-lg mb-2"
                                            :class="{
                                                'fa-chevron-up': openTicket === {{ $ticket->id }},
                                                'fa-chevron-down': openTicket !== {{ $ticket->id }}
                                            }"></i>
                                        @if ($ticket->status !== 'resolved')
                                            <div class="w-3 h-3 bg-cyan-500 dark:bg-cyan-400 rounded-full animate-pulse"></div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Expanded Ticket Details -->
                                <div x-show="openTicket === {{ $ticket->id }}"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 max-h-0"
                                    x-transition:enter-end="opacity-100 max-h-screen"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 max-h-screen"
                                    x-transition:leave-end="opacity-0 max-h-0"
                                    class="overflow-hidden border-t border-themed-primary bg-themed-tertiary/50">
                                    <div class="p-6">
                                        <!-- Original Message -->
                                        <div class="bg-themed-secondary rounded-xl p-4 mb-4 border-l-4 border-cyan-400 dark:border-cyan-600">
                                            <div class="flex items-start">
                                                <div class="bg-cyan-100 dark:bg-cyan-900/30 rounded-full p-2 mr-3">
                                                    <i class="fas fa-user text-cyan-600 dark:text-cyan-400"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-themed-primary mb-2">Your Message</h4>
                                                    <p class="text-themed-primary whitespace-pre-wrap">
                                                        {{ $ticket->description }}</p>
                                                    <p class="text-xs text-themed-secondary mt-2">
                                                        {{ $ticket->created_at->format('F j, Y \a\t g:i A') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Support Response -->
                                        @if ($ticket->response)
                                            <div
                                                class="bg-green-100/30 dark:bg-green-900/20 rounded-xl p-4 border-l-4 border-green-400 dark:border-green-600">
                                                <div class="flex items-start">
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-green-900 dark:text-green-100 mb-2">Support Team Response</h4>
                                                        <p class="text-green-800 dark:text-green-200 whitespace-pre-wrap">
                                                            {{ $ticket->response }}</p>
                                                        <div class="flex items-center justify-between mt-3">
                                                            @if ($ticket->responder)
                                                                <p class="text-xs text-green-700 dark:text-green-300">
                                                                    <i class="fas fa-user-circle mr-1"></i>
                                                                    Responded by {{ $ticket->responder->name }}
                                                                </p>
                                                            @endif
                                                            <p class="text-xs text-green-700 dark:text-green-300">
                                                                <i class="fas fa-clock mr-1"></i>
                                                                {{ $ticket->updated_at->format('F j, Y \a\t g:i A') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div
                                                class="bg-orange-100/30 dark:bg-orange-900/20 rounded-xl p-4 border-l-4 border-orange-400 dark:border-orange-600">
                                                <div class="flex items-center">
                                                    <div class="bg-orange-100 dark:bg-orange-900/30 rounded-full p-2 mr-3">
                                                        <i class="fas fa-clock text-orange-600 dark:text-orange-400"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-semibold text-orange-900 dark:text-orange-100 mb-1">Awaiting Response</h4>
                                                        <p class="text-sm text-orange-800 dark:text-orange-200">Our support team will respond to your ticket within 2-4 hours during business hours.</p>
                                                        <div class="flex items-center mt-2">
                                                            <div class="flex space-x-1">
                                                                <div class="w-2 h-2 bg-orange-400 rounded-full animate-bounce"></div>
                                                                <div class="w-2 h-2 bg-orange-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                                                <div class="w-2 h-2 bg-orange-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                                            </div>
                                                            <span class="ml-2 text-xs text-orange-700 dark:text-orange-300">Processing...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Ticket Timeline -->
                                        <div class="mt-6 border-t border-themed-primary pt-4">
                                            <h5 class="text-sm font-medium text-themed-primary mb-3 flex items-center">
                                                <i class="fas fa-history mr-2"></i>
                                                Ticket Timeline
                                            </h5>
                                            <div class="space-y-3">
                                                <div class="flex items-center text-sm">
                                                    <div class="w-2 h-2 bg-cyan-400 dark:bg-cyan-500 rounded-full mr-3"></div>
                                                    <span class="text-themed-primary">Ticket created</span>
                                                    <span class="ml-auto text-themed-secondary">{{ $ticket->created_at->format('M j, g:i A') }}</span>
                                                </div>
                                                @if ($ticket->response)
                                                    <div class="flex items-center text-sm">
                                                        <div class="w-2 h-2 bg-green-400 dark:bg-green-500 rounded-full mr-3"></div>
                                                        <span class="text-themed-primary">Support team responded</span>
                                                        <span class="ml-auto text-themed-secondary">{{ $ticket->updated_at->format('M j, g:i A') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-16">
                                <div class="bg-themed-tertiary rounded-full p-6 w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                                    <i class="fas fa-ticket-alt fa-2x text-themed-secondary"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-themed-primary mb-2">No tickets found</h3>
                                <p class="text-themed-secondary mb-6">You haven't submitted any support tickets yet.</p>
                                <button @click="activeTab = 'submit_ticket'"
                                    class="bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 dark:from-cyan-700 dark:to-blue-800 dark:hover:from-cyan-800 dark:hover:to-blue-900 text-white px-6 py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 shadow-lg">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Submit Your First Ticket
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Enhanced Pagination -->
                @if ($tickets->hasPages())
                    <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-themed-primary">
                                <span class="font-medium">{{ $tickets->firstItem() ?? 0 }}</span>
                                -
                                <span class="font-medium">{{ $tickets->lastItem() ?? 0 }}</span>
                                of
                                <span class="font-medium">{{ $tickets->total() }}</span>
                                tickets
                            </div>
                            <div class="flex items-center space-x-2">
                                {{ $tickets->links('custom.pagination') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div> <!-- End of main x-data container -->

        <!-- Loading State Overlay -->
        <div wire:loading.delay wire:target="submitTicket,pollTickets"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-themed-secondary rounded-2xl p-8 shadow-2xl max-w-sm w-full mx-4 border border-themed-primary">
                <div class="text-center">
                    <div class="bg-cyan-100 dark:bg-cyan-900/30 rounded-full p-6 w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-circle-notch fa-spin fa-2x text-cyan-600 dark:text-cyan-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-themed-primary mb-2">Processing...</h3>
                    <p class="text-themed-secondary">Please wait while we process your request.</p>
                </div>
            </div>
        </div>

        <!-- Success/Error Notification Toast -->
        <div x-data="{ show: false, message: '', type: 'success' }"
            @notify.window="show = true; message = $event.detail[0]; type = $event.detail[1]; setTimeout(() => show = false, 5000)"
            x-show="show" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-full"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-full" class="fixed top-4 right-4 z-50 max-w-sm">
            <div :class="{
                'bg-green-500 dark:bg-green-600': type === 'success',
                'bg-red-500 dark:bg-red-600': type === 'error',
                'bg-cyan-500 dark:bg-cyan-600': type === 'info',
                'bg-yellow-500 dark:bg-yellow-600': type === 'warning'
            }"
                class="rounded-xl shadow-lg p-4 text-white border border-white/20">
                <div class="flex items-center cursor-pointer hover:shadow-xl transition-shadow" @click="show = false">
                    <div class="flex-shrink-0">
                        <i :class="{
                            'fas fa-check-circle': type === 'success',
                            'fas fa-exclamation-circle': type === 'error',
                            'fas fa-info-circle': type === 'info',
                            'fas fa-exclamation-triangle': type === 'warning'
                        }"
                            class="text-xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="font-medium" x-text="message"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <button @click="show = false" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>