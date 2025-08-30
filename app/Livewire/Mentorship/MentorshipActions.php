<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Mentorship;
use App\Models\MentorshipSession;
use App\Models\CodeReview;
use App\Models\MentorshipReview;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\On;

class MentorshipActions extends Component
{
    use WithFileUploads;

    // Session Management
    public $sessionId = null;
    public $sessionTitle = '';
    public $sessionDescription = '';
    public $sessionType = 'general';
    public $sessionFormat = 'video';
    public $scheduledAt = '';
    public $duration = 60;
    public $agenda = '';
    public $materials = [];
    public $meetingLink = '';
    public $sessionNotes = '';
    public $actionItems = [];
    public $mentorFeedback = '';
    public $menteeFeedback = '';
    public $mentorRating = 0;
    public $menteeRating = 0;
    public $attachments = [];

    // Code Review Management
    public $reviewId = null;
    public $reviewTitle = '';
    public $reviewDescription = '';
    public $technologies = [];
    public $repositoryUrl = '';
    public $branchName = 'main';
    public $pullRequestUrl = '';
    public $filesToReview = [];
    public $specificQuestions = '';
    public $codeSnippets = '';
    public $priority = 'medium';
    public $reviewFeedback = '';
    public $suggestions = [];
    public $codeQualityScore = 0;
    public $improvementAreas = [];

    // Review & Rating
    public $overallRating = 0;
    public $communicationRating = 0;
    public $expertiseRating = 0;
    public $helpfulnessRating = 0;
    public $professionalismRating = 0;
    public $reviewText = '';
    public $pros = [];
    public $cons = [];
    public $wouldRecommend = true;
    public $reviewTags = [];

    // UI State
    public $showSessionModal = false;
    public $showCodeReviewModal = false;
    public $showReviewModal = false;
    public $showCompletionModal = false;
    public $modalType = ''; // create, edit, view, complete
    public $selectedMentorship = null;
    public $currentSession = null;
    public $currentCodeReview = null;

    protected $rules = [
        'sessionTitle' => 'required|string|max:255',
        'sessionDescription' => 'required|string|min:20',
        'sessionType' => 'required|in:general,code_review,project_guidance,career_advice,mock_interview',
        'sessionFormat' => 'required|in:video,audio,screen_share,text',
        'scheduledAt' => 'required|date|after:now',
        'duration' => 'required|integer|min:15|max:240',

        'reviewTitle' => 'required|string|max:255',
        'reviewDescription' => 'required|string|min:20',
        'technologies' => 'required|array|min:1',
        'priority' => 'required|in:low,medium,high,urgent',

        'overallRating' => 'required|numeric|min:1|max:5',
        'reviewText' => 'required|string|min:20|max:2000'
    ];

    protected $messages = [
        'sessionTitle.required' => 'Session title is required',
        'sessionDescription.min' => 'Please provide a detailed description (at least 20 characters)',
        'scheduledAt.after' => 'Session must be scheduled for a future date',
        'reviewTitle.required' => 'Code review title is required',
        'technologies.required' => 'Please specify at least one technology',
        'overallRating.required' => 'Overall rating is required',
        'reviewText.min' => 'Please provide detailed feedback (at least 20 characters)'
    ];

    public function mount()
    {
        $this->initializeArrays();
    }

    private function initializeArrays()
    {
        $this->technologies = [''];
        $this->actionItems = [''];
        $this->suggestions = [''];
        $this->improvementAreas = [''];
        $this->pros = [''];
        $this->cons = [''];
    }

    // Session Management Methods
    public function createSession($mentorshipId)
    {
        $this->selectedMentorship = Mentorship::find($mentorshipId);
        
        if (!$this->selectedMentorship || !$this->selectedMentorship->isActive()) {
            session()->flash('error', 'Cannot create session for inactive mentorship.');
            return;
        }

        $this->resetSessionForm();
        $this->modalType = 'create';
        $this->showSessionModal = true;
    }

    public function editSession($sessionId)
    {
        $session = MentorshipSession::find($sessionId);
        
        if (!$session || !$this->canManageSession($session)) {
            session()->flash('error', 'You cannot edit this session.');
            return;
        }

        $this->loadSessionData($session);
        $this->modalType = 'edit';
        $this->showSessionModal = true;
    }

    public function viewSession($sessionId)
    {
        $this->currentSession = MentorshipSession::with(['mentorship.mentor', 'mentorship.mentee'])->find($sessionId);
        
        if (!$this->currentSession) {
            session()->flash('error', 'Session not found.');
            return;
        }

        $this->modalType = 'view';
        $this->showSessionModal = true;
    }

    public function completeSession($sessionId)
    {
        $session = MentorshipSession::find($sessionId);
        
        if (!$session || !$this->canManageSession($session)) {
            session()->flash('error', 'You cannot complete this session.');
            return;
        }

        $this->currentSession = $session;
        $this->loadSessionData($session);
        $this->modalType = 'complete';
        $this->showCompletionModal = true;
    }

    public function submitSession()
    {
        $this->validate([
            'sessionTitle' => 'required|string|max:255',
            'sessionDescription' => 'required|string|min:20',
            'sessionType' => 'required|in:general,code_review,project_guidance,career_advice,mock_interview',
            'scheduledAt' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:240'
        ]);

        $sessionData = [
            'title' => $this->sessionTitle,
            'description' => $this->sessionDescription,
            'type' => $this->sessionType,
            'format' => $this->sessionFormat,
            'scheduled_at' => $this->scheduledAt,
            'duration_minutes' => $this->duration,
            'agenda' => $this->agenda,
            'materials' => array_filter($this->materials),
            'meeting_link' => $this->meetingLink,
            'is_billable' => $this->selectedMentorship->is_paid,
            'session_cost' => $this->selectedMentorship->is_paid ? 
                ($this->selectedMentorship->hourly_rate * ($this->duration / 60)) : 0
        ];

        if ($this->modalType === 'create') {
            $sessionData['mentorship_id'] = $this->selectedMentorship->id;
            $sessionData['status'] = MentorshipSession::STATUS_SCHEDULED;
            
            $session = MentorshipSession::create($sessionData);
            
            // Send notifications
            $this->sendSessionNotification($session, 'scheduled');
            
            session()->flash('message', 'Session scheduled successfully!');
        } else {
            $session = MentorshipSession::find($this->sessionId);
            $session->update($sessionData);
            
            session()->flash('message', 'Session updated successfully!');
        }

        $this->resetSessionForm();
        $this->showSessionModal = false;
        $this->dispatch('session-updated');
    }

    public function submitSessionCompletion()
    {
        $this->validate([
            'sessionNotes' => 'required|string|min:20',
            'mentorRating' => 'nullable|numeric|min:1|max:5',
            'menteeRating' => 'nullable|numeric|min:1|max:5'
        ]);

        $this->currentSession->update([
            'status' => MentorshipSession::STATUS_COMPLETED,
            'ended_at' => now(),
            'session_notes' => $this->sessionNotes,
            'action_items' => array_filter($this->actionItems),
            'mentor_feedback' => $this->mentorFeedback,
            'mentee_feedback' => $this->menteeFeedback,
            'mentor_rating' => $this->mentorRating,
            'mentee_rating' => $this->menteeRating,
            'actual_duration_minutes' => $this->duration
        ]);

        // Update mentor profile stats
        $this->currentSession->mentorship->mentor->mentorProfile?->increment('total_sessions');

        // Handle file uploads if any
        if ($this->attachments) {
            $attachmentPaths = [];
            foreach ($this->attachments as $attachment) {
                $path = $attachment->store('mentorship/sessions/' . $this->currentSession->id, 'public');
                $attachmentPaths[] = [
                    'name' => $attachment->getClientOriginalName(),
                    'path' => $path,
                    'size' => $attachment->getSize(),
                    'type' => $attachment->getMimeType()
                ];
            }
            $this->currentSession->update(['attachments' => $attachmentPaths]);
        }

        session()->flash('message', 'Session completed successfully!');
        
        $this->showCompletionModal = false;
        $this->dispatch('session-completed');
    }

    // Code Review Methods
    public function createCodeReview($mentorshipId)
    {
        $this->selectedMentorship = Mentorship::find($mentorshipId);
        
        if (!$this->selectedMentorship || !$this->selectedMentorship->isActive()) {
            session()->flash('error', 'Cannot create code review for inactive mentorship.');
            return;
        }

        $this->resetCodeReviewForm();
        $this->modalType = 'create';
        $this->showCodeReviewModal = true;
    }

    public function editCodeReview($reviewId)
    {
        $review = CodeReview::find($reviewId);
        
        if (!$review || !$this->canManageCodeReview($review)) {
            session()->flash('error', 'You cannot edit this code review.');
            return;
        }

        $this->loadCodeReviewData($review);
        $this->modalType = 'edit';
        $this->showCodeReviewModal = true;
    }

    public function viewCodeReview($reviewId)
    {
        $this->currentCodeReview = CodeReview::with([
            'mentorship.mentor', 
            'mentorship.mentee', 
            'requester', 
            'reviewer'
        ])->find($reviewId);
        
        if (!$this->currentCodeReview) {
            session()->flash('error', 'Code review not found.');
            return;
        }

        $this->modalType = 'view';
        $this->showCodeReviewModal = true;
    }

    public function startCodeReview($reviewId)
    {
        $review = CodeReview::find($reviewId);
        
        if (!$review || !$this->canReviewCode($review)) {
            session()->flash('error', 'You cannot start this code review.');
            return;
        }

        $review->startReview(Auth::id());
        
        session()->flash('message', 'Code review started. You can now provide feedback.');
        
        $this->dispatch('code-review-updated');
    }

    public function submitCodeReview()
    {
        $this->validate([
            'reviewTitle' => 'required|string|max:255',
            'reviewDescription' => 'required|string|min:20',
            'technologies' => 'required|array|min:1',
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        $reviewData = [
            'title' => $this->reviewTitle,
            'description' => $this->reviewDescription,
            'technologies' => array_filter($this->technologies),
            'priority' => $this->priority,
            'repository_url' => $this->repositoryUrl,
            'branch_name' => $this->branchName ?: 'main',
            'pull_request_url' => $this->pullRequestUrl,
            'files_to_review' => array_filter($this->filesToReview),
            'specific_questions' => $this->specificQuestions,
            'code_snippets' => $this->codeSnippets ? json_decode($this->codeSnippets, true) : null,
            'is_urgent' => $this->priority === 'urgent'
        ];

        if ($this->modalType === 'create') {
            $reviewData['mentorship_id'] = $this->selectedMentorship->id;
            $reviewData['requested_by'] = Auth::id();
            $reviewData['status'] = CodeReview::STATUS_PENDING;
            $reviewData['requested_at'] = now();
            
            $review = CodeReview::create($reviewData);
            
            // Send notification to mentor
            $this->selectedMentorship->mentor->notify(
                new \App\Notifications\CodeReviewRequested($review)
            );
            
            session()->flash('message', 'Code review requested successfully!');
        } else {
            $review = CodeReview::find($this->reviewId);
            $review->update($reviewData);
            
            session()->flash('message', 'Code review updated successfully!');
        }

        $this->resetCodeReviewForm();
        $this->showCodeReviewModal = false;
        $this->dispatch('code-review-updated');
    }

    public function submitCodeReviewFeedback()
    {
        $this->validate([
            'reviewFeedback' => 'required|string|min:50',
            'codeQualityScore' => 'nullable|numeric|min:1|max:10'
        ]);

        $this->currentCodeReview->complete(
            $this->reviewFeedback,
            array_filter($this->suggestions),
            $this->codeQualityScore ?: null
        );

        $this->currentCodeReview->update([
            'improvement_areas' => array_filter($this->improvementAreas)
        ]);

        // Send notification to mentee
        $this->currentCodeReview->requester->notify(
            new \App\Notifications\CodeReviewCompleted($this->currentCodeReview)
        );

        session()->flash('message', 'Code review completed successfully!');
        
        $this->showCodeReviewModal = false;
        $this->dispatch('code-review-completed');
    }

    // Review & Rating Methods
    public function createReview($mentorshipId, $type = 'mentorship')
    {
        $this->selectedMentorship = Mentorship::find($mentorshipId);
        $this->modalType = $type;
        $this->resetReviewForm();
        $this->showReviewModal = true;
    }

    public function submitReview()
    {
        $this->validate([
            'overallRating' => 'required|numeric|min:1|max:5',
            'reviewText' => 'required|string|min:20|max:2000'
        ]);

        $reviewData = [
            'mentorship_id' => $this->selectedMentorship->id,
            'reviewer_id' => Auth::id(),
            'type' => $this->modalType,
            'overall_rating' => $this->overallRating,
            'communication_rating' => $this->communicationRating ?: null,
            'expertise_rating' => $this->expertiseRating ?: null,
            'helpfulness_rating' => $this->helpfulnessRating ?: null,
            'professionalism_rating' => $this->professionalismRating ?: null,
            'review_text' => $this->reviewText,
            'pros' => array_filter($this->pros),
            'cons' => array_filter($this->cons),
            'would_recommend' => $this->wouldRecommend,
            'tags' => array_filter($this->reviewTags)
        ];

        // Determine reviewee based on user role
        if (Auth::id() === $this->selectedMentorship->mentee_id) {
            $reviewData['reviewee_id'] = $this->selectedMentorship->mentor_id;
        } else {
            $reviewData['reviewee_id'] = $this->selectedMentorship->mentee_id;
        }

        MentorshipReview::create($reviewData);

        session()->flash('message', 'Review submitted successfully!');
        
        $this->resetReviewForm();
        $this->showReviewModal = false;
        $this->dispatch('review-submitted');
    }

    // Helper Methods
    private function canManageSession($session)
    {
        $mentorship = $session->mentorship;
        return Auth::id() === $mentorship->mentor_id || 
               Auth::id() === $mentorship->mentee_id ||
               Auth::user()->isAcademyAdmin() ||
               Auth::user()->isSuperAdmin();
    }

    private function canManageCodeReview($review)
    {
        return Auth::id() === $review->requested_by ||
               Auth::id() === $review->mentorship->mentor_id ||
               Auth::user()->isAcademyAdmin() ||
               Auth::user()->isSuperAdmin();
    }

    private function canReviewCode($review)
    {
        return Auth::id() === $review->mentorship->mentor_id ||
               Auth::user()->isAcademyAdmin() ||
               Auth::user()->isSuperAdmin();
    }

    private function loadSessionData($session)
    {
        $this->sessionId = $session->id;
        $this->sessionTitle = $session->title;
        $this->sessionDescription = $session->description;
        $this->sessionType = $session->type;
        $this->sessionFormat = $session->format;
        $this->scheduledAt = $session->scheduled_at->format('Y-m-d\TH:i');
        $this->duration = $session->duration_minutes ?? 60;
        $this->agenda = $session->agenda;
        $this->materials = $session->materials ?? [];
        $this->meetingLink = $session->meeting_link;
        $this->sessionNotes = $session->session_notes;
        $this->actionItems = $session->action_items ?? [''];
        $this->mentorFeedback = $session->mentor_feedback;
        $this->menteeFeedback = $session->mentee_feedback;
        $this->mentorRating = $session->mentor_rating;
        $this->menteeRating = $session->mentee_rating;
    }

    private function loadCodeReviewData($review)
    {
        $this->reviewId = $review->id;
        $this->reviewTitle = $review->title;
        $this->reviewDescription = $review->description;
        $this->technologies = $review->technologies ?? [''];
        $this->priority = $review->priority;
        $this->repositoryUrl = $review->repository_url;
        $this->branchName = $review->branch_name;
        $this->pullRequestUrl = $review->pull_request_url;
        $this->filesToReview = $review->files_to_review ?? [];
        $this->specificQuestions = $review->specific_questions;
        $this->codeSnippets = $review->code_snippets ? json_encode($review->code_snippets) : '';
        $this->reviewFeedback = $review->review_feedback;
        $this->suggestions = $review->suggestions ?? [''];
        $this->codeQualityScore = $review->code_quality_score;
        $this->improvementAreas = $review->improvement_areas ?? [''];
    }

    private function sendSessionNotification($session, $type)
    {
        $mentorship = $session->mentorship;
        
        if ($type === 'scheduled') {
            $mentorship->mentor->notify(new \App\Notifications\SessionScheduled($session));
            $mentorship->mentee->notify(new \App\Notifications\SessionScheduled($session));
        }
    }

    // Array Management Methods
    public function addActionItem()
    {
        $this->actionItems[] = '';
    }

    public function removeActionItem($index)
    {
        unset($this->actionItems[$index]);
        $this->actionItems = array_values($this->actionItems);
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

    public function addSuggestion()
    {
        $this->suggestions[] = '';
    }

    public function removeSuggestion($index)
    {
        unset($this->suggestions[$index]);
        $this->suggestions = array_values($this->suggestions);
    }

    public function addImprovementArea()
    {
        $this->improvementAreas[] = '';
    }

    public function removeImprovementArea($index)
    {
        unset($this->improvementAreas[$index]);
        $this->improvementAreas = array_values($this->improvementAreas);
    }

    public function addPro()
    {
        $this->pros[] = '';
    }

    public function removePro($index)
    {
        unset($this->pros[$index]);
        $this->pros = array_values($this->pros);
    }

    public function addCon()
    {
        $this->cons[] = '';
    }

    public function removeCon($index)
    {
        unset($this->cons[$index]);
        $this->cons = array_values($this->cons);
    }

    // Reset Methods
    public function resetSessionForm()
    {
        $this->reset([
            'sessionId', 'sessionTitle', 'sessionDescription', 'sessionType',
            'sessionFormat', 'scheduledAt', 'duration', 'agenda', 'materials',
            'meetingLink', 'sessionNotes', 'mentorFeedback', 'menteeFeedback',
            'mentorRating', 'menteeRating', 'attachments'
        ]);
        
        $this->sessionType = 'general';
        $this->sessionFormat = 'video';
        $this->duration = 60;
        $this->actionItems = [''];
    }

    public function resetCodeReviewForm()
    {
        $this->reset([
            'reviewId', 'reviewTitle', 'reviewDescription', 'repositoryUrl',
            'branchName', 'pullRequestUrl', 'filesToReview', 'specificQuestions',
            'codeSnippets', 'priority', 'reviewFeedback', 'codeQualityScore'
        ]);
        
        $this->technologies = [''];
        $this->priority = 'medium';
        $this->branchName = 'main';
        $this->suggestions = [''];
        $this->improvementAreas = [''];
    }

    public function resetReviewForm()
    {
        $this->reset([
            'overallRating', 'communicationRating', 'expertiseRating',
            'helpfulnessRating', 'professionalismRating', 'reviewText',
            'wouldRecommend', 'reviewTags'
        ]);
        
        $this->pros = [''];
        $this->cons = [''];
        $this->wouldRecommend = true;
    }

    public function closeModal()
    {
        $this->showSessionModal = false;
        $this->showCodeReviewModal = false;
        $this->showReviewModal = false;
        $this->showCompletionModal = false;
        $this->resetSessionForm();
        $this->resetCodeReviewForm();
        $this->resetReviewForm();
    }

    #[On('close-modal')]
    public function handleCloseModal()
    {
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.mentorship.mentorship-actions');
    }
}