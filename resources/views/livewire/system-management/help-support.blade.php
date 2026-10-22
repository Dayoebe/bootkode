<div wire:poll.visible.15s="pollTickets" class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
    <x-ui.page-header title="Help and support" description="Find published answers, submit a private ticket, and follow the response." />
    <x-ui.flash :messages="$feedback" />
    <nav aria-label="Support sections" class="flex max-w-full gap-4 overflow-x-auto border-b border-line">
        @foreach(['faqs' => 'Knowledge base', 'submit_ticket' => 'New ticket', 'ticket_history' => 'My tickets'] as $tab => $label)
            <button type="button" wire:click="$set('activeTab', '{{ $tab }}')" aria-pressed="{{ $activeTab === $tab ? 'true' : 'false' }}" @class(['min-h-11 shrink-0 border-b-2 px-2 py-3 text-sm font-semibold', 'border-brand text-brand' => $activeTab === $tab, 'border-transparent text-content-muted' => $activeTab !== $tab])>{{ $label }}</button>
        @endforeach
    </nav>
    @if($activeTab === 'faqs')
        <x-ui.panel>
            <x-input-label for="faq-search" value="Search published answers" />
            <x-text-input id="faq-search" wire:model.live.debounce.300ms="searchFaq" type="search" class="mt-2" />
            <div class="mt-6 space-y-3">
                @forelse($faqs as $faq)
                    <details class="rounded-lg border border-line p-4"><summary class="min-h-11 cursor-pointer font-semibold text-content">{{ $faq->question }}</summary><div class="mt-3 whitespace-pre-line text-content-muted">{{ strip_tags($faq->answer) }}</div></details>
                @empty
                    <x-ui.empty-state title="No published answer found" description="Try another search or submit a support ticket." />
                @endforelse
            </div>
        </x-ui.panel>
    @elseif($activeTab === 'submit_ticket')
        <x-ui.panel>
            <form wire:submit="submitTicket" class="max-w-3xl space-y-5">
                <div><x-input-label for="ticket-subject" value="Subject" /><x-text-input id="ticket-subject" wire:model="subject" class="mt-2" required aria-describedby="ticket-subject-error" /><x-input-error id="ticket-subject-error" :messages="$errors->get('subject')" /></div>
                <div><x-input-label for="ticket-description" value="Describe the issue" /><textarea id="ticket-description" wire:model="description" class="bk-input mt-2" rows="6" required aria-describedby="ticket-description-error"></textarea><x-input-error id="ticket-description-error" :messages="$errors->get('description')" /></div>
                <div><x-input-label for="ticket-attachment" value="Attachment (optional)" /><input id="ticket-attachment" type="file" wire:model="attachment" accept="image/jpeg,image/png,application/pdf" class="mt-2 min-h-11 max-w-full" aria-describedby="ticket-attachment-help ticket-attachment-error" /><p id="ticket-attachment-help" class="mt-2 text-sm text-content-muted">JPG, PNG or PDF, up to 2 MB. Accessible to you and platform support staff.</p><x-input-error id="ticket-attachment-error" :messages="$errors->get('attachment')" /></div>
                <x-primary-button wire:loading.attr="disabled" wire:target="submitTicket,attachment">Submit ticket</x-primary-button><p role="status" wire:loading wire:target="submitTicket,attachment">Saving your ticket…</p>
            </form>
        </x-ui.panel>
    @else
        <x-ui.panel>
            <div class="grid gap-4 sm:grid-cols-2"><div><x-input-label for="ticket-search" value="Search your tickets" /><x-text-input id="ticket-search" wire:model.live.debounce.300ms="ticketSearch" type="search" class="mt-2" /></div><div><x-input-label for="ticket-status" value="Status" /><select id="ticket-status" wire:model.live="ticketStatusFilter" class="bk-input mt-2">@foreach(['all', 'open', 'in_progress', 'resolved', 'closed'] as $status)<option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></div></div>
            <div class="mt-6 space-y-4">
                @forelse($tickets as $ticket)
                    <article class="rounded-lg border border-line p-4"><div class="flex flex-wrap items-start justify-between gap-3"><h2 class="break-words text-lg font-semibold text-content">{{ $ticket->subject }}</h2><span class="text-sm text-content-muted">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></div><p class="mt-2 text-sm text-content-muted">Ticket #{{ $ticket->id }} · {{ $ticket->created_at->format('M j, Y') }}</p><p class="mt-4 whitespace-pre-line break-words text-content">{{ $ticket->description }}</p>
                        @if($ticket->attachment)<a href="{{ $ticket->attachment_url }}" class="mt-3 inline-flex min-h-11 items-center text-brand underline">Download attachment</a>@endif
                        @if($ticket->response)<div class="mt-4 rounded-lg bg-surface-muted p-4"><p class="font-semibold text-content">Support response</p><p class="mt-2 whitespace-pre-line break-words text-content-muted">{{ $ticket->response }}</p><p class="mt-2 text-sm text-content-muted">{{ $ticket->responded_at?->format('M j, Y, g:i a') ?? 'Response date not recorded' }}</p></div>@endif
                    </article>
                @empty
                    <x-ui.empty-state title="No tickets found" description="Your submitted tickets will appear here." />
                @endforelse
            </div>
            <div class="mt-6">{{ $tickets->links() }}</div>
        </x-ui.panel>
    @endif
</div>
