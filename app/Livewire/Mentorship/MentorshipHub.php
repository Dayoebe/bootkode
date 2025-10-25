<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MentorshipSession;
use App\Models\Mentorship\CodeReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Mentorship Hub', 
    'description' => 'Connect, Learn, and Grow with Expert Mentors', 
    'icon' => 'fas fa-hands-helping', 
    'active' => 'mentorship'
])]
class MentorshipHub extends Component
{
    // Dashboard stats
    public $activeMentorships = 0;
    public $completedMentorships = 0;
    public $totalSessions = 0;
    public $upcomingSessions = 0;
    public $pendingCodeReviews = 0;

    // Collections
    public $upcomingSessionsList = [];
    public $recentCodeReviews = [];
    public $myActiveMentorships = [];

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $user = Auth::user();
        
        // Load based on user role
        if ($user->isStudent()) {
            $this->loadStudentDashboard($user);
        }
        
        if ($user->isMentor()) {
            $this->loadMentorDashboard($user);
        }
        
        $this->loadUpcomingSessions($user);
        $this->loadRecentCodeReviews($user);
        $this->loadMyActiveMentorships($user);
    }

    private function loadStudentDashboard($user)
    {
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

    private function loadMentorDashboard($user)
    {
        $this->activeMentorships = Mentorship::where('mentor_id', $user->id)
            ->where('status', Mentorship::STATUS_ACTIVE)
            ->count();
            
        $this->totalSessions = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
            $q->where('mentor_id', $user->id);
        })->where('status', MentorshipSession::STATUS_COMPLETED)->count();
        
        $this->upcomingSessions = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
            $q->where('mentor_id', $user->id)->where('status', Mentorship::STATUS_ACTIVE);
        })->where('status', MentorshipSession::STATUS_SCHEDULED)
          ->where('scheduled_at', '>', now())
          ->count();
        
        $this->pendingCodeReviews = CodeReview::whereHas('mentorship', function($q) use ($user) {
            $q->where('mentor_id', $user->id);
        })->where('status', CodeReview::STATUS_PENDING)->count();
    }

    private function loadUpcomingSessions($user)
    {
        $query = MentorshipSession::with(['mentorship.mentor', 'mentorship.mentee'])
            ->where('status', MentorshipSession::STATUS_SCHEDULED)
            ->where('scheduled_at', '>', now());

        if ($user->isStudent()) {
            $query->whereHas('mentorship', fn($q) => $q->where('mentee_id', $user->id));
        } elseif ($user->isMentor()) {
            $query->whereHas('mentorship', fn($q) => $q->where('mentor_id', $user->id));
        }

        $this->upcomingSessionsList = $query->orderBy('scheduled_at')->take(5)->get();
    }

    private function loadRecentCodeReviews($user)
    {
        $query = CodeReview::with(['mentorship.mentor', 'mentorship.mentee', 'requester', 'reviewer']);

        if ($user->isStudent()) {
            $query->where('requested_by', $user->id);
        } elseif ($user->isMentor()) {
            $query->whereHas('mentorship', fn($q) => $q->where('mentor_id', $user->id));
        }

        $this->recentCodeReviews = $query->orderBy('requested_at', 'desc')->take(5)->get();
    }

    private function loadMyActiveMentorships($user)
    {
        $query = Mentorship::with(['mentor.mentorProfile', 'mentee'])
            ->where('status', Mentorship::STATUS_ACTIVE);

        if ($user->isStudent()) {
            $query->where('mentee_id', $user->id);
        } elseif ($user->isMentor()) {
            $query->where('mentor_id', $user->id);
        }

        $this->myActiveMentorships = $query->orderBy('started_at', 'desc')->take(5)->get();
    }

    public function render()
    {
        return view('livewire.mentorship.mentorship-hub');
    }
}