<div class="space-y-6">
    <x-ui.page-header title="Support tickets" description="Review private requests and record a response for the ticket owner." />
    <x-ui.flash :messages="$feedback" />
    <x-ui.panel>
        <div class="grid gap-4 sm:grid-cols-2"><div><x-input-label for="support-search" value="Search tickets" /><x-text-input id="support-search" wire:model.live.debounce.300ms="search" type="search" class="mt-2" /></div><div><x-input-label for="support-status" value="Status" /><select aria-label="Status filter" id="support-status" wire:model.live="statusFilter" class="bk-input mt-2">@foreach(['all', 'open', 'in_progress', 'resolved', 'closed'] as $status)<option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>@endforeach</select></div></div>
        <div class="mt-6 max-w-full overflow-x-auto"><x-ui.table label="Support Ticket Management.Blade records"><table class="w-full text-left text-sm"><thead><tr class="border-b border-line text-content-muted"><th scope="col" class="p-3">Request</th><th scope="col" class="p-3">Account</th><th scope="col" class="p-3">Status</th><th scope="col" class="p-3">Actions</th></tr></thead><tbody>
            @forelse($tickets as $ticket)<tr class="border-b border-line"><td class="p-3"><p class="font-semibold text-content">{{ $ticket->subject }}</p><p class="mt-1 text-content-muted">#{{ $ticket->id }} · {{ $ticket->created_at->format('M j, Y') }}</p></td><td class="p-3 text-content">{{ $ticket->user?->name ?? 'Deleted account' }}</td><td class="p-3 text-content-muted">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</td><td class="p-3"><x-secondary-button wire:click="openTicket({{ $ticket->id }})">View and respond</x-secondary-button></td></tr>
            @empty<tr><td colspan="4" class="p-6"><x-ui.empty-state title="No matching tickets" description="Try another search or status." /></td></tr>@endforelse
        </tbody></table></x-ui.table></div><div class="mt-6">{{ $tickets->links() }}</div>
    </x-ui.panel>
    <x-modal name="support-response" model="showResponseModal" label="View and respond to support ticket">
        <div class="space-y-5 p-6">
            @if($selectedTicket)
                <h2 class="break-words text-xl font-semibold text-content">{{ $selectedTicket->subject }}</h2><p class="whitespace-pre-line break-words text-content-muted">{{ $selectedTicket->description }}</p>
                @if($selectedTicket->attachment)<a href="{{ $selectedTicket->attachment_url }}" class="inline-flex min-h-11 items-center text-brand underline">Download attachment</a>@endif
            @endif
            <form wire:submit="respondToTicket" class="space-y-5"><div><x-input-label for="support-response" value="Response" /><textarea aria-label="Response" id="support-response" wire:model="response" rows="6" class="bk-input mt-2" required aria-describedby="support-response-error"></textarea><x-input-error id="support-response-error" :messages="$errors->get('response')" /></div><div><x-input-label for="support-resolution" value="Ticket status" /><select aria-label="Resolution status" id="support-resolution" wire:model="resolutionStatus" class="bk-input mt-2"><option value="in_progress">In progress</option><option value="resolved">Resolved</option></select><x-input-error :messages="$errors->get('resolutionStatus')" /></div><div class="flex flex-wrap justify-end gap-3"><x-secondary-button wire:click="$set('showResponseModal', false)">Cancel</x-secondary-button><x-primary-button wire:loading.attr="disabled">Save response</x-primary-button></div></form>
        </div>
    </x-modal>
</div>
