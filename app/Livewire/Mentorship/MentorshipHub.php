<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Mentorship;
use App\Models\MentorProfile;
use App\Models\MentorshipSession;
use App\Models\CodeReview;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard', ['title' => 'Mentorship Hub', 'description' => 'Connect, Learn, and Grow with Expert Mentors', 'icon' => 'fas fa-hands-helping', 'active' => 'mentorship'])]

class MentorshipHub extends Component
{
    use WithFileUploads, WithPagination;

    // UI State
    public $activeTab = 'dashboard';
    public $viewMode = 'grid'; // grid, list, cards
    public $searchTerm = '';
    public $selectedMentor = null;
    public $showMentorModal = false;
    public $showRequestModal = false;
    public $showSessionModal = false;
    public $showCodeReviewModal = false;

    // Dashboard stats
    public $activeMentorships = 0;
    public $completedMentorships = 0;
    public $totalSessions = 0;
    public $upcomingSessions = 0;
    public $pendingCodeReviews = 0;
    public $totalMentors = 0;

    // Request/Session forms
    public $requestMessage = '';
    public $goals = [];
    public $expectations = [];
    public $durationWeeks = 12;
    public $preferredRate = '';
    public $sessionTitle = '';
    public $sessionDescription = '';
    public $sessionType = 'general';
    public $scheduledAt = '';
    public $agenda = '';

    // Code Review form
    public $reviewTitle = '';
    public $reviewDescription = '';
    public $repositoryUrl = '';
    public $branchName = 'main';
    public $pullRequestUrl = '';
    public $technologies = [];
    public $specificQuestions = '';
    public $priority = 'medium';
    public $filesToReview = [];
    public $codeSnippets = '';

    // Filters
    public $statusFilter = '';
    public $typeFilter = '';
    public $experienceFilter = '';
    public $availabilityFilter = 'available';
    public $ratingFilter = '';
    public $priceRangeFilter = '';
    public $specializationFilter = '';

    // Collections
    public $mentors = [];
    public $myMentorships = [];
    public $upcomingSessionsList = [];
    public $recentCodeReviews = [];
    public $mentorApplications = [];

    protected $rules = [
        'requestMessage' => 'required|string|min:50|max:1000',
        'goals' => 'required|array|min:1',
        'expectations' => 'required|array|min:1',
        'durationWeeks' => 'required|integer|min:4|max:52',
        'sessionTitle' => 'required|string|max:255',
        'sessionDescription' => 'required|string|min:20',
        'sessionType' => 'required|in:general,code_review,project_guidance,career_advice',
        'scheduledAt' => 'required|date|after:now',
        'reviewTitle' => 'required|string|max:255',
        'reviewDescription' => 'required|string|min:20',
        'technologies' => 'required|array|min:1',
        'priority' => 'required|in:low,medium,high,urgent'
    ];

    public function mount()
    {
        $this->loadDashboardData();
        $this->loadMentors();
        $this->loadMyMentorships();
        $this->loadUpcomingSessions();
        $this->loadRecentCodeReviews();
    }

    public function loadDashboardData()
    {
        $user = Auth::user();
        
        // Student stats
        if ($user->isStudent()) {
            $this->activeMentorships = Mentorship::where('mentee_id', $user->id)
                ->where('status', Mentorship::STATUS_ACTIVE)
                ->count();
                
            $this->completedMentorships = Mentorship::where('mentee_id', $user->id)
                ->where('status', Mentorship::STATUS_COMPLETED)
                ->count();
                
            $this->totalSessions = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentee_id', $user->id);
            })->where('status', MentorshipSession::STATUS_COMPLETED)->count();
            
            $this->upcomingSessions = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentee_id', $user->id)->where('status', Mentorship::STATUS_ACTIVE);
            })->where('status', MentorshipSession::STATUS_SCHEDULED)
              ->where('scheduled_at', '>', now())
              ->count();
              
            $this->pendingCodeReviews = CodeReview::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentee_id', $user->id);
            })->where('status', CodeReview::STATUS_PENDING)->count();
        }
        
        // Mentor stats
        if ($user->isMentor()) {
            $this->activeMentorships = Mentorship::where('mentor_id', $user->id)
                ->where('status', Mentorship::STATUS_ACTIVE)
                ->count();
                
            $this->totalSessions = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            })->where('status', MentorshipSession::STATUS_COMPLETED)->count();
            
            $this->pendingCodeReviews = CodeReview::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            })->where('status', CodeReview::STATUS_PENDING)->count();
        }

        $this->totalMentors = MentorProfile::verified()->available()->count();
    }

    public function loadMentors()
    {
        $query = MentorProfile::with(['user'])
            ->where('is_verified', true);

        // Apply filters
        if ($this->searchTerm) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%');
            })->orWhereJsonContains('specializations', $this->searchTerm)
              ->orWhereJsonContains('skills', $this->searchTerm);
        }

        if ($this->availabilityFilter === 'available') {
            $query->available();
        }

        if ($this->experienceFilter) {
            $query->where('experience_level', $this->experienceFilter);
        }

        if ($this->ratingFilter) {
            $query->where('rating', '>=', $this->ratingFilter);
        }

        if ($this->specializationFilter) {
            $query->whereJsonContains('specializations', $this->specializationFilter);
        }

        if ($this->priceRangeFilter) {
            [$min, $max] = explode('-', $this->priceRangeFilter);
            $query->whereBetween('hourly_rate', [$min, $max]);
        }

        $this->mentors = $query->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->take(20)
            ->get();
    }

    public function loadMyMentorships()
    {
        $user = Auth::user();
        
        $query = Mentorship::with(['mentor.mentorProfile', 'mentee', 'sessions' => function($q) {
            $q->where('scheduled_at', '>', now())->orderBy('scheduled_at')->limit(1);
        }]);

        if ($user->isStudent()) {
            $query->where('mentee_id', $user->id);
        } elseif ($user->isMentor()) {
            $query->where('mentor_id', $user->id);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $this->myMentorships = $query->orderBy('created_at', 'desc')->get();
    }

    public function loadUpcomingSessions()
    {
        $user = Auth::user();
        
        $query = MentorshipSession::with(['mentorship.mentor', 'mentorship.mentee'])
            ->where('status', MentorshipSession::STATUS_SCHEDULED)
            ->where('scheduled_at', '>', now());

        if ($user->isStudent()) {
            $query->whereHas('mentorship', function($q) use ($user) {
                $q->where('mentee_id', $user->id);
            });
        } elseif ($user->isMentor()) {
            $query->whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            });
        }

        $this->upcomingSessionsList = $query->orderBy('scheduled_at')->take(5)->get();
    }

    public function loadRecentCodeReviews()
    {
        $user = Auth::user();
        
        $query = CodeReview::with(['mentorship.mentor', 'mentorship.mentee', 'requester', 'reviewer']);

        if ($user->isStudent()) {
            $query->where('requested_by', $user->id);
        } elseif ($user->isMentor()) {
            $query->whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            });
        }

        $this->recentCodeReviews = $query->orderBy('requested_at', 'desc')->take(5)->get();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        
        // Reload data based on active tab
        match($tab) {
            'find-mentor' => $this->loadMentors(),
            'my-mentorships' => $this->loadMyMentorships(),
            'sessions' => $this->loadUpcomingSessions(),
            'code-reviews' => $this->loadRecentCodeReviews(),
            default => $this->loadDashboardData()
        };
    }

    public function selectMentor($mentorId)
    {
        $this->selectedMentor = MentorProfile::with('user')->find($mentorId);
        $this->showMentorModal = true;
    }

    public function requestMentorship($mentorId)
    {
        $this->selectedMentor = MentorProfile::with('user')->find($mentorId);
        $this->showRequestModal = true;
        $this->showMentorModal = false;
    }

    public function submitMentorshipRequest()
    {
        $this->validate([
            'requestMessage' => 'required|string|min:50|max:1000',
            'goals' => 'required|array|min:1',
            'expectations' => 'required|array|min:1',
            'durationWeeks' => 'required|integer|min:4|max:52'
        ]);

        $mentorship = Mentorship::create([
            'mentor_id' => $this->selectedMentor->user_id,
            'mentee_id' => Auth::id(),
            'status' => Mentorship::STATUS_PENDING,
            'request_message' => $this->requestMessage,
            'goals' => array_filter($this->goals),
            'expectations' => array_filter($this->expectations),
            'duration_weeks' => $this->durationWeeks,
            'is_paid' => !$this->selectedMentor->offers_free_sessions,
            'hourly_rate' => $this->selectedMentor->hourly_rate,
            'requested_at' => now()
        ]);

        // Send notification to mentor
        $this->selectedMentor->user->notify(
            new \App\Notifications\MentorshipRequested($mentorship)
        );

        $this->resetRequestForm();
        $this->showRequestModal = false;
        
        session()->flash('message', 'Mentorship request sent successfully! You will be notified once the mentor responds.');
        
        $this->loadMyMentorships();
        $this->loadDashboardData();
    }

    public function scheduleSession($mentorshipId)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        if (!$mentorship || !$mentorship->isActive()) {
            session()->flash('error', 'Cannot schedule session for inactive mentorship.');
            return;
        }

        $this->selectedMentorship = $mentorship;
        $this->showSessionModal = true;
    }

    public function submitSessionRequest()
    {
        $this->validate([
            'sessionTitle' => 'required|string|max:255',
            'sessionDescription' => 'required|string|min:20',
            'sessionType' => 'required|in:general,code_review,project_guidance,career_advice',
            'scheduledAt' => 'required|date|after:now'
        ]);

        MentorshipSession::create([
            'mentorship_id' => $this->selectedMentorship->id,
            'title' => $this->sessionTitle,
            'description' => $this->sessionDescription,
            'type' => $this->sessionType,
            'format' => 'video',
            'status' => MentorshipSession::STATUS_SCHEDULED,
            'scheduled_at' => $this->scheduledAt,
            'agenda' => $this->agenda,
            'is_billable' => $this->selectedMentorship->is_paid,
            'session_cost' => $this->selectedMentorship->is_paid ? $this->selectedMentorship->hourly_rate : 0
        ]);

        $this->resetSessionForm();
        $this->showSessionModal = false;
        
        session()->flash('message', 'Session scheduled successfully!');
        
        $this->loadUpcomingSessions();
    }

    public function requestCodeReview($mentorshipId)
    {
        $this->selectedMentorship = Mentorship::find($mentorshipId);
        $this->showCodeReviewModal = true;
    }

    public function submitCodeReview()
    {
        $this->validate([
            'reviewTitle' => 'required|string|max:255',
            'reviewDescription' => 'required|string|min:20',
            'technologies' => 'required|array|min:1',
            'priority' => 'required|in:low,medium,high,urgent'
        ]);

        CodeReview::create([
            'mentorship_id' => $this->selectedMentorship->id,
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

        $this->resetCodeReviewForm();
        $this->showCodeReviewModal = false;
        
        session()->flash('message', 'Code review request submitted successfully!');
        
        $this->loadRecentCodeReviews();
    }

    public function acceptMentorship($mentorshipId)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        if (!$mentorship || !$mentorship->isPending()) {
            session()->flash('error', 'Cannot accept this mentorship request.');
            return;
        }

        // Check if mentor can accept more mentees
        $mentorProfile = $mentorship->mentor->mentorProfile;
        if (!$mentorProfile->canAcceptMentees()) {
            session()->flash('error', 'You have reached your maximum mentee capacity.');
            return;
        }

        $mentorship->accept();
        
        // Send notification to mentee
        $mentorship->mentee->notify(
            new \App\Notifications\MentorshipAccepted($mentorship)
        );

        session()->flash('message', 'Mentorship request accepted successfully!');
        
        $this->loadMyMentorships();
        $this->loadDashboardData();
    }

    public function rejectMentorship($mentorshipId, $reason = null)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        if (!$mentorship || !$mentorship->isPending()) {
            session()->flash('error', 'Cannot reject this mentorship request.');
            return;
        }

        $mentorship->reject($reason);
        
        // Send notification to mentee
        $mentorship->mentee->notify(
            new \App\Notifications\MentorshipRejected($mentorship)
        );

        session()->flash('message', 'Mentorship request rejected.');
        
        $this->loadMyMentorships();
    }

    public function addGoal()
    {
        $this->goals[] = '';
    }

    public function removeGoal($index)
    {
        unset($this->goals[$index]);
        $this->goals = array_values($this->goals);
    }

    public function addExpectation()
    {
        $this->expectations[] = '';
    }

    public function removeExpectation($index)
    {
        unset($this->expectations[$index]);
        $this->expectations = array_values($this->expectations);
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

    public function resetRequestForm()
    {
        $this->reset([
            'requestMessage',
            'goals',
            'expectations',
            'durationWeeks',
            'preferredRate'
        ]);
        $this->goals = [''];
        $this->expectations = [''];
        $this->durationWeeks = 12;
    }

    public function resetSessionForm()
    {
        $this->reset([
            'sessionTitle',
            'sessionDescription',
            'sessionType',
            'scheduledAt',
            'agenda'
        ]);
        $this->sessionType = 'general';
    }

    public function resetCodeReviewForm()
    {
        $this->reset([
            'reviewTitle',
            'reviewDescription',
            'repositoryUrl',
            'branchName',
            'pullRequestUrl',
            'technologies',
            'specificQuestions',
            'priority',
            'codeSnippets'
        ]);
        $this->technologies = [''];
        $this->priority = 'medium';
        $this->branchName = 'main';
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, [
            'searchTerm',
            'statusFilter',
            'experienceFilter',
            'availabilityFilter',
            'ratingFilter',
            'specializationFilter',
            'priceRangeFilter'
        ])) {
            $this->loadMentors();
        }

        if ($propertyName === 'statusFilter' && $this->activeTab === 'my-mentorships') {
            $this->loadMyMentorships();
        }
    }

    #[On('mentorship-updated')]
    public function handleMentorshipUpdated()
    {
        $this->loadMyMentorships();
        $this->loadDashboardData();
    }

    #[On('session-completed')]
    public function handleSessionCompleted()
    {
        $this->loadUpcomingSessions();
        $this->loadDashboardData();
    }

    public function render()
    {
        return view('livewire.mentorship.mentorship-hub');
    }
}