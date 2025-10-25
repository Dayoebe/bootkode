<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Mentorship\CodeReview;
use App\Models\Mentorship\Mentorship;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard', [
    'title' => 'Code Reviews', 
    'description' => 'Manage Code Reviews', 
    'icon' => 'fas fa-code', 
    'active' => 'mentorship'
])]
class CodeReviews extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $priorityFilter = '';
    public $showReviewModal = false;
    public $selectedReview = null;

    // Create review form
    public $mentorshipId = null;
    public $reviewTitle = '';
    public $reviewDescription = '';
    public $technologies = [''];
    public $repositoryUrl = '';
    public $branchName = 'main';
    public $pullRequestUrl = '';
    public $specificQuestions = '';
    public $priority = 'medium';

    protected $rules = [
        'reviewTitle' => 'required|string|max:255',
        'reviewDescription' => 'required|string|min:20',
        'technologies' => 'required|array|min:1',
        'technologies.*' => 'required|string|max:100',
        'priority' => 'required|in:low,medium,high,urgent'
    ];

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'priorityFilter' => ['except' => '']
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['statusFilter', 'priorityFilter'])) {
            $this->resetPage();
        }
    }

    public function createReview($mentorshipId)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        if (!$mentorship || !$mentorship->isActive()) {
            session()->flash('error', 'Cannot create code review for inactive mentorship.');
            return;
        }

        $this->mentorshipId = $mentorshipId;
        $this->showReviewModal = true;
    }

    public function submitReview()
    {
        $this->validate();

        CodeReview::create([
            'mentorship_id' => $this->mentorshipId,
            'requested_by' => Auth::id(),
            'title' => $this->reviewTitle,
            'description' => $this->reviewDescription,
            'status' => CodeReview::STATUS_PENDING,
            'priority' => $this->priority,
            'technologies' => array_filter($this->technologies),
            'repository_url' => $this->repositoryUrl,
            'branch_name' => $this->branchName ?: 'main',
            'pull_request_url' => $this->pullRequestUrl,
            'specific_questions' => $this->specificQuestions,
            'requested_at' => now(),
            'is_urgent' => $this->priority === 'urgent'
        ]);

        $this->resetReviewForm();
        $this->showReviewModal = false;
        
        session()->flash('message', 'Code review request submitted successfully!');
        $this->dispatch('review-created');
    }

    public function startReview($reviewId)
    {
        $review = CodeReview::find($reviewId);
        
        if (!$review || !$this->canReviewCode($review)) {
            session()->flash('error', 'You cannot start this review.');
            return;
        }

        $review->startReview(Auth::id());
        session()->flash('message', 'Code review started.');
        $this->dispatch('review-updated');
    }

    public function viewReview($reviewId)
    {
        $this->selectedReview = CodeReview::with([
            'mentorship.mentor', 
            'mentorship.mentee', 
            'requester', 
            'reviewer'
        ])->find($reviewId);
        
        if (!$this->selectedReview) {
            session()->flash('error', 'Code review not found.');
            return;
        }

        $this->dispatch('open-review-view', reviewId: $reviewId);
    }

    public function completeReview($reviewId)
    {
        $review = CodeReview::find($reviewId);
        
        if (!$review || !$this->canReviewCode($review)) {
            session()->flash('error', 'You cannot complete this review.');
            return;
        }

        $this->dispatch('open-review-completion', reviewId: $reviewId);
    }

    private function canReviewCode($review)
    {
        $user = Auth::user();
        return $user->id === $review->mentorship->mentor_id ||
               $user->isAcademyAdmin() ||
               $user->isSuperAdmin();
    }

    public function addTechnology()
    {
        $this->technologies[] = '';
    }

    public function removeTechnology($index)
    {
        unset($this->technologies[$index]);
        $this->technologies = array_values($this->technologies);
    }

    public function resetReviewForm()
    {
        $this->reset([
            'mentorshipId', 'reviewTitle', 'reviewDescription', 
            'repositoryUrl', 'branchName', 'pullRequestUrl', 
            'specificQuestions', 'priority'
        ]);
        $this->technologies = [''];
        $this->priority = 'medium';
        $this->branchName = 'main';
    }

    public function closeModal()
    {
        $this->showReviewModal = false;
        $this->selectedReview = null;
        $this->resetReviewForm();
    }

    #[On('review-created')]
    #[On('review-updated')]
    #[On('review-completed')]
    public function refreshReviews()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = CodeReview::with(['mentorship.mentor', 'mentorship.mentee', 'requester', 'reviewer'])
            ->whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
            });

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Priority filter
        if ($this->priorityFilter) {
            $query->where('priority', $this->priorityFilter);
        }

        $reviews = $query->orderBy('requested_at', 'desc')->paginate(10);

        // Get active mentorships for review creation
        $activeMentorships = Mentorship::with(['mentor', 'mentee'])
            ->where(function($q) use ($user) {
                $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
            })
            ->where('status', Mentorship::STATUS_ACTIVE)
            ->get();

        return view('livewire.mentorship.code-reviews', [
            'reviews' => $reviews,
            'activeMentorships' => $activeMentorships
        ]);
    }
}