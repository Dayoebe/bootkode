<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
use App\Models\MentorProfile;
use App\Models\Mentorship;
use App\Models\MentorshipSession;
use App\Models\CodeReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard', [
    'title' => 'Mentor Dashboard', 
    'description' => 'Manage Your Mentorship Activities', 
    'icon' => 'fas fa-chalkboard-teacher', 
    'active' => 'mentor-dashboard'
])]
class MentorDashboard extends Component
{
    public $activeTab = 'overview';
    public $profileId = null;
    public $isAvailable = true;
    
    // Dashboard Stats
    public $totalMentees = 0;
    public $activeMentees = 0;
    public $totalSessions = 0;
    public $completedSessions = 0;
    public $upcomingSessions = 0;
    public $pendingRequests = 0;
    public $pendingCodeReviews = 0;
    public $averageRating = 0;
    public $totalReviews = 0;
    public $monthlyEarnings = 0;
    public $totalEarnings = 0;

    public function mount()
    {
        $this->checkMentorAccess();
        $this->loadMentorProfile();
        $this->loadDashboardStats();
    }

    private function checkMentorAccess()
    {
        $user = Auth::user();
        if (!$user->isMentor() && !$user->isAcademyAdmin() && !$user->isSuperAdmin()) {
            session()->flash('error', 'You need to be a mentor to access this page.');
            return redirect()->route('mentorship.hub');
        }
    }

    #[On('change-tab')]
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function loadMentorProfile()
    {
        $profile = Auth::user()->mentorProfile;
        if ($profile) {
            $this->profileId = $profile->id;
            $this->isAvailable = $profile->is_available;
        }
    }

    public function loadDashboardStats()
    {
        $user = Auth::user();
        $profile = $user->mentorProfile;

        if (!$profile) {
            $this->initializeEmptyStats();
            return;
        }

        $this->totalMentees = $profile->total_mentees;
        $this->activeMentees = $profile->current_mentees;
        $this->totalSessions = $profile->total_sessions;
        $this->averageRating = $profile->rating;
        $this->totalReviews = $profile->total_reviews;

        $this->pendingRequests = Mentorship::where('mentor_id', $user->id)
            ->where('status', Mentorship::STATUS_PENDING)
            ->count();

        $this->completedSessions = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
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

        $this->calculateEarnings();
    }

    private function initializeEmptyStats()
    {
        $this->totalMentees = 0;
        $this->activeMentees = 0;
        $this->totalSessions = 0;
        $this->completedSessions = 0;
        $this->upcomingSessions = 0;
        $this->pendingRequests = 0;
        $this->pendingCodeReviews = 0;
        $this->averageRating = 0;
        $this->totalReviews = 0;
        $this->monthlyEarnings = 0;
        $this->totalEarnings = 0;
    }

    private function calculateEarnings()
    {
        $user = Auth::user();
        $thisMonth = now()->startOfMonth();

        $this->monthlyEarnings = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
            $q->where('mentor_id', $user->id);
        })
        ->where('status', MentorshipSession::STATUS_COMPLETED)
        ->where('is_billable', true)
        ->where('ended_at', '>=', $thisMonth)
        ->sum('session_cost');

        $this->totalEarnings = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
            $q->where('mentor_id', $user->id);
        })
        ->where('status', MentorshipSession::STATUS_COMPLETED)
        ->where('is_billable', true)
        ->sum('session_cost');
    }

    public function toggleAvailability()
    {
        if ($this->profileId) {
            Auth::user()->mentorProfile->update(['is_available' => !$this->isAvailable]);
            $this->isAvailable = !$this->isAvailable;
            
            $status = $this->isAvailable ? 'available' : 'unavailable';
            session()->flash('message', "You are now {$status} for new mentorships.");
        }
    }

    #[On('mentorship-updated')]
    #[On('session-updated')]
    #[On('session-completed')]
    #[On('code-review-updated')]
    #[On('profile-updated')]
    public function refreshDashboard()
    {
        $this->loadMentorProfile();
        $this->loadDashboardStats();
    }

    public function render()
    {
        return view('livewire.mentorship.mentor-dashboard');
    }
}