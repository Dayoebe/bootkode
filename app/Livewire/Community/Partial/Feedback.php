<?php

namespace App\Livewire\Community\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\CommunityFeedback;
use App\Models\User;

class Feedback extends Component
{
    use WithPagination, WithFileUploads;
    
    public $showCreateForm = false;
    public $search = '';
    public $statusFilter = 'all';
    public $typeFilter = 'all';
    
    // Form properties
    public $type = 'general';
    public $title = '';
    public $message = '';
    public $priority = 'medium';
    public $rating = null;
    public $attachments = [];

    protected $rules = [
        'type' => 'required|in:general,course,instructor,feature_request,bug_report',
        'title' => 'required|min:5|max:255',
        'message' => 'required|min:10|max:2000',
        'priority' => 'required|in:low,medium,high',
        'rating' => 'nullable|integer|min:1|max:5',
        'attachments.*' => 'nullable|file|max:5120', // 5MB max per file
    ];

    public function submitFeedback()
    {
        $this->validate();

        $attachmentPaths = [];
        if (!empty($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                $path = $attachment->store('feedback-attachments', 'public');
                $attachmentPaths[] = [
                    'path' => $path,
                    'name' => $attachment->getClientOriginalName(),
                    'size' => $attachment->getSize(),
                ];
            }
        }

        CommunityFeedback::create([
            'user_id' => auth()->id(),
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'priority' => $this->priority,
            'rating' => $this->rating,
            'attachments' => $attachmentPaths,
        ]);

        $this->reset(['type', 'title', 'message', 'priority', 'rating', 'attachments', 'showCreateForm']);
        
        session()->flash('message', 'Feedback submitted successfully! We\'ll review it soon.');
    }

    public function updatedType()
    {
        // Reset rating if not course/instructor feedback
        if (!in_array($this->type, ['course', 'instructor'])) {
            $this->rating = null;
        }
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function removeAttachment($index)
    {
        array_splice($this->attachments, $index, 1);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $feedbackQuery = CommunityFeedback::with(['user', 'assignedTo'])
            ->where('user_id', auth()->id()) // Users can only see their own feedback
            ->when($this->search, function($query) {
                return $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('message', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function($query) {
                return $query->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter !== 'all', function($query) {
                return $query->where('type', $this->typeFilter);
            })
            ->latest();

        $feedback = $feedbackQuery->paginate(10);

        $stats = [
            'total' => CommunityFeedback::where('user_id', auth()->id())->count(),
            'open' => CommunityFeedback::where('user_id', auth()->id())->where('status', 'open')->count(),
            'resolved' => CommunityFeedback::where('user_id', auth()->id())->where('status', 'resolved')->count(),
        ];

        return view('livewire.community.partial.feedback', [
            'feedback' => $feedback,
            'stats' => $stats,
        ]);
    }
}