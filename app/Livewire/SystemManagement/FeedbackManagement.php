<?php

namespace App\Livewire\SystemManagement;

use App\Models\Community\Feedback;
use App\Notifications\FeedbackResponseNotification;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', ['title' => 'Feedback management', 'active' => 'feedback.management'])]
class FeedbackManagement extends Component
{
    use WithPagination;
    use \App\Livewire\Concerns\AuthorizesPlatformAdministration;
    use \App\Livewire\Concerns\HasActionFeedback;

    public string $search = '';
    public string $statusFilter = 'open';
    public string $response = '';
    public ?int $selectedFeedbackId = null;
    public bool $showResponseModal = false;

    public function openFeedback(int $id): void
    {
        $feedback = Feedback::findOrFail($id);
        $this->selectedFeedbackId = $feedback->id;
        $this->response = $feedback->response ?? '';
        $this->showResponseModal = true;
    }

    public function respond(?int $feedbackId = null): void
    {
        $id = $feedbackId ?? $this->selectedFeedbackId;
        $this->validate(['response' => 'required|string|min:5|max:2000']);
        \Illuminate\Support\Facades\DB::transaction(function () use ($id) {
            $feedback = Feedback::lockForUpdate()->findOrFail($id);
            $feedback->update(['status' => 'responded', 'response' => $this->response, 'responded_by' => auth()->id(), 'responded_at' => now()]);
            \Illuminate\Support\Facades\DB::afterCommit(fn () => $feedback->user?->notify(new FeedbackResponseNotification($feedback)));
        });
        $this->flashFeedback('success', 'Response saved. Notification requested for the account owner.');
        $this->reset(['response', 'selectedFeedbackId', 'showResponseModal']);
    }

    public function close($id): void
    {
        Feedback::findOrFail($id)->update(['status' => 'closed']);
        $this->flashFeedback('success', 'Feedback closed.');
    }

    public function render()
    {
        $this->validate(['statusFilter' => 'in:all,open,responded,closed', 'search' => 'string|max:255']);
        return view('livewire.system-management.feedback-management', [
            'selectedFeedback' => $this->selectedFeedbackId ? Feedback::find($this->selectedFeedbackId) : null,
            'feedbacks' => Feedback::with(['user', 'course'])
                ->when($this->search !== '', fn ($query) => $query->where(fn ($matching) => $matching->where('message', 'like', '%'.$this->search.'%')->orWhereHas('user', fn ($user) => $user->where('name', 'like', '%'.$this->search.'%'))->orWhereHas('course', fn ($course) => $course->where('title', 'like', '%'.$this->search.'%'))))
                ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))->latest()->paginate(10),
        ]);
    }
}
