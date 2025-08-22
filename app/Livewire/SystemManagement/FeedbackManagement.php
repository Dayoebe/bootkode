<?php

namespace App\Livewire\SystemManagement;

use App\Models\Feedback;
use App\Notifications\FeedbackResponseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Feedback Management', 'description' => 'Manage user feedback', 'icon' => 'fas fa-comment-dots', 'active' => 'feedback.management'])]
class FeedbackManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'open';
    public $response = '';
    public $selectedFeedbackId = null;

    public function respond($feedbackId)
    {
        if (!Auth::user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->dispatch('notify', 'Unauthorized!', 'error');
            return;
        }
    
        $this->validate([
            'response' => ['required', 'string', 'max:2000'],
        ]);
    
        $feedback = Feedback::findOrFail($feedbackId);
        $feedback->update([
            'status' => 'responded',
            'response' => $this->response,
            'responded_by' => Auth::user()->id,
            'responded_at' => now(),
        ]);
        $feedback->user->notify(new \App\Livewire\Component\Notifications\FeedbackResponseNotification($feedback));

        Livewire::dispatch('notify', 'Your feedback has been responded to!', 'success');

        // $feedback->user->notify(new \App\Notifications\FeedbackResponseNotification($feedback));
        // $this->dispatch('notify', 'Feedback responded successfully!', 'success');
        $this->dispatchTo('notifications', 'notify', [
            'message' => 'New response to your feedback: ' . Str::limit($feedback->message, 30),
            'type' => 'success'
        ]);
    
        $this->reset(['response', 'selectedFeedbackId']);
    }

    public function close($feedbackId)
    {
        if (!Auth::user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->dispatch('notify', 'Unauthorized!', 'error');
            return;
        }

        $feedback = Feedback::findOrFail($feedbackId);
        $feedback->update(['status' => 'closed']);
        Auth::user()->logCustomActivity('Closed feedback', ['feedback_id' => $feedbackId]);
        $this->dispatch('notify', 'Feedback closed successfully!', 'success');
    }

    public function render()
    {
        $feedbacks = Feedback::with(['user', 'course'])
            ->when($this->search, function ($query) {
                $query->where('message', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('course', function ($q) {
                          $q->where('title', 'like', '%' . $this->search . '%');
                      });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.system-management.feedback-management', [
            'feedbacks' => $feedbacks,
        ]);
    }
}