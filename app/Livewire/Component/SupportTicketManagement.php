<?php

namespace App\Livewire\Component;

use App\Models\SupportTicket;
use App\Notifications\TicketUpdateNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Support Ticket Management', 'description' => 'Manage support tickets', 'icon' => 'fas fa-ticket-alt', 'active' => 'support.tickets'])]
class SupportTicketManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'open';
    public $response = '';
    public $selectedTicketId = null;

    public function updateStatus($ticketId, $status)
    {
        if (!Auth::user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->dispatch('notify', 'Unauthorized!', 'error');
            return;
        }

        $ticket = SupportTicket::findOrFail($ticketId);
        $data = [
            'status' => $status,
            'responded_by' => Auth::user()->id,
            'responded_at' => now(),
        ];

        if ($this->response) {
            $data['response'] = $this->response;
        }

        $ticket->update($data);

        // Trigger notification to user
        $ticket->user->notify(new TicketUpdateNotification($ticket));
        Auth::user()->logCustomActivity('Updated support ticket status', ['ticket_id' => $ticketId]);

        $this->dispatch('notify', 'Ticket updated successfully!', 'success');
        $this->reset(['response', 'selectedTicketId']);
    }

    public function render()
    {
        $tickets = SupportTicket::with('user')
            ->when($this->search, function ($query) {
                $query->where('subject', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.component.support-ticket-management', [
            'tickets' => $tickets,
        ]);
    }
}