<?php

namespace App\Livewire\SystemManagement;

use App\Models\Community\SupportTicket;
use App\Notifications\TicketUpdateNotification;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', ['title' => 'Support tickets', 'active' => 'support.tickets'])]
class SupportTicketManagement extends Component
{
    use WithPagination;
    use \App\Livewire\Concerns\AuthorizesPlatformAdministration;
    use \App\Livewire\Concerns\HasActionFeedback;

    public string $search = '';
    public string $statusFilter = 'open';
    public string $response = '';
    public ?int $selectedTicketId = null;
    public bool $showResponseModal = false;
    public string $resolutionStatus = 'in_progress';

    public function openTicket(int $id): void
    {
        $ticket = SupportTicket::findOrFail($id);
        $this->selectedTicketId = $ticket->id;
        $this->response = $ticket->response ?? '';
        $this->resolutionStatus = in_array($ticket->status, ['in_progress', 'resolved'], true) ? $ticket->status : 'in_progress';
        $this->showResponseModal = true;
    }

    public function respondToTicket(): void
    {
        $this->validate(['selectedTicketId' => 'required|integer|exists:support_tickets,id', 'response' => 'required|string|min:5|max:5000', 'resolutionStatus' => 'required|in:in_progress,resolved']);
        $this->persistUpdate($this->selectedTicketId, $this->resolutionStatus, $this->response);
        $this->showResponseModal = false;
    }

    public function updateStatus($ticketId, $status): void
    {
        $this->persistUpdate($ticketId, $status);
    }

    private function persistUpdate(int $ticketId, string $status, ?string $response = null): void
    {
        validator(['status' => $status, 'response' => $response], ['status' => 'required|in:open,in_progress,resolved,closed', 'response' => 'nullable|string|max:5000'])->validate();
        DB::transaction(function () use ($ticketId, $status, $response) {
            $ticket = SupportTicket::lockForUpdate()->findOrFail($ticketId);
            $data = ['status' => $status, 'responded_by' => auth()->id(), 'responded_at' => now()];
            if ($response !== null) $data['response'] = $response;
            $ticket->update($data);
            DB::afterCommit(fn () => $ticket->user?->notify(new TicketUpdateNotification($ticket)));
        });
        $this->flashFeedback('success', 'Ticket updated. Notification requested for the account owner.');
        $this->reset(['response', 'selectedTicketId']);
    }

    public function render()
    {
        $this->validate(['statusFilter' => 'in:all,open,in_progress,resolved,closed', 'search' => 'string|max:255']);
        return view('livewire.system-management.support-ticket-management', ['selectedTicket' => $this->selectedTicketId ? SupportTicket::find($this->selectedTicketId) : null, 'tickets' => SupportTicket::with('user')
            ->when($this->search !== '', fn ($query) => $query->where(fn ($matching) => $matching->where('subject', 'like', '%'.$this->search.'%')->orWhere('description', 'like', '%'.$this->search.'%')))
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))->latest()->paginate(10)]);
    }
}
