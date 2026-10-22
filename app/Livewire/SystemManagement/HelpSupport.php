<?php

namespace App\Livewire\SystemManagement;

use App\Models\Community\SupportTicket;
use App\Models\Community\Faq;
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
    use \App\Livewire\Concerns\RequiresAuthenticatedAccount;
    use \App\Livewire\Concerns\HasActionFeedback;

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
            $data['attachment'] = $this->attachment->store('private/support-ticket-attachments', 'local');
        }

        try {
            $ticket = SupportTicket::create($data);
        } catch (\Throwable $exception) {
            if (isset($data['attachment'])) \Illuminate\Support\Facades\Storage::disk('local')->delete($data['attachment']);
            throw $exception;
        }

        // Corrected dispatch method to use the Livewire 3 syntax
        $this->flashFeedback('success', 'Your support ticket was submitted. Follow its status in My tickets.');
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
        $this->validate(['activeTab' => 'in:faqs,submit_ticket,ticket_history', 'ticketStatusFilter' => 'in:all,open,in_progress,resolved,closed', 'ticketSearch' => 'string|max:255', 'searchFaq' => 'string|max:255']);
        $faqs = Faq::where('is_published', true)
            ->when($this->searchFaq, function ($query) {
                $query->where(fn ($matching) => $matching->where('question', 'like', '%' . $this->searchFaq . '%')->orWhere('answer', 'like', '%' . $this->searchFaq . '%'));
            })
            ->orderBy('order')
            ->get();

        $tickets = SupportTicket::where('user_id', Auth::id())
            ->when($this->ticketSearch, function ($query) {
                $query->where(fn ($matching) => $matching->where('subject', 'like', '%' . $this->ticketSearch . '%')->orWhere('description', 'like', '%' . $this->ticketSearch . '%'));
            })
            ->when($this->ticketStatusFilter !== 'all', function ($query) {
                $query->where('status', $this->ticketStatusFilter);
            })
            ->with('responder')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.system-management.help-support', [
            'faqs' => $faqs,
            'tickets' => $tickets,
        ]);
    }
}
