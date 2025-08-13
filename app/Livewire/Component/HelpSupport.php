<?php

namespace App\Livewire\Component;

use App\Models\SupportTicket;
use App\Models\Faq;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Help & Support',
    'description' => 'Access FAQs, submit support tickets, and view ticket history',
    'icon' => 'fas fa-question-circle',
    'active' => 'help.support'
])]
class HelpSupport extends Component
{
    use WithPagination, WithFileUploads;

    public $activeTab = 'faqs';
    public $searchFaq = '';
    public $subject = '';
    public $description = '';
    public $attachment;
    public $ticketSearch = '';
    public $ticketStatusFilter = 'all';

    protected $queryString = [
        'ticketSearch' => ['except' => ''],
        'ticketStatusFilter' => ['except' => 'all'],
    ];

    protected function rules()
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,png,pdf', 'max:2048'], // 2MB max, images/PDF
        ];
    }

    public function submitTicket()
    {
        $this->validate();

        $data = [
            'user_id' => Auth::id(),
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => 'open',
        ];

        if ($this->attachment) {
            $path = $this->attachment->store('ticket_attachments', 'public');
            $data['attachment'] = $path;
        }

        $ticket = SupportTicket::create($data);

        Auth::user()->logCustomActivity('Submitted support ticket: ' . $this->subject, ['ticket_id' => $ticket->id]);
        $this->dispatch('notify', 'Support ticket submitted successfully!', 'success');
        $this->dispatchTo('notifications', 'notify', [
            'message' => 'New support ticket submitted: ' . $this->subject,
            'type' => 'success'
        ]);
        $this->reset(['subject', 'description', 'attachment']);
        $this->activeTab = 'ticket_history';
    }

    public function pollTickets()
    {
        // Triggered by wire:poll to check for ticket updates
        $this->dispatch('refresh-tickets');
    }

    public function render()
    {
        $faqs = Faq::where('is_published', true)
            ->when($this->searchFaq, function ($query) {
                $query->where('question', 'like', '%' . $this->searchFaq . '%')
                      ->orWhere('answer', 'like', '%' . $this->searchFaq . '%');
            })
            ->orderBy('order')
            ->get();

        $tickets = SupportTicket::where('user_id', Auth::id())
            ->when($this->ticketSearch, function ($query) {
                $query->where('subject', 'like', '%' . $this->ticketSearch . '%')
                      ->orWhere('description', 'like', '%' . $this->ticketSearch . '%');
            })
            ->when($this->ticketStatusFilter !== 'all', function ($query) {
                $query->where('status', $this->ticketStatusFilter);
            })
            ->with('responder')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.component.help-support', [
            'faqs' => $faqs,
            'tickets' => $tickets,
        ]);
    }
}