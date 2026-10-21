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
    use \App\Livewire\Concerns\RequiresAuthenticatedAccount;

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
        
        $mine = Mentorship::where(fn ($query) => $query->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id));
        $this->activeMentorships = (clone $mine)->where('status', Mentorship::STATUS_ACTIVE)->count();
        $this->completedMentorships = (clone $mine)->where('status', Mentorship::STATUS_COMPLETED)->count();
        $ids = (clone $mine)->pluck('id');
        $this->totalSessions = MentorshipSession::whereIn('mentorship_id', $ids)->where('status', MentorshipSession::STATUS_COMPLETED)->count();
        $this->upcomingSessions = MentorshipSession::whereIn('mentorship_id', $ids)->where('status', MentorshipSession::STATUS_SCHEDULED)->where('scheduled_at', '>', now())->count();
        $this->pendingCodeReviews = CodeReview::whereIn('mentorship_id', $ids)->where('status', CodeReview::STATUS_PENDING)->count();

        $this->loadUpcomingSessions($user);
        $this->loadRecentCodeReviews($user);
        $this->loadMyActiveMentorships($user);
    }

    private function loadUpcomingSessions($user)
    {
        $query = MentorshipSession::with(['mentorship.mentor', 'mentorship.mentee'])
            ->where('status', MentorshipSession::STATUS_SCHEDULED)
            ->where('scheduled_at', '>', now());

        $query->whereHas('mentorship', fn ($query) => $query->where(fn ($owned) => $owned->where('mentee_id', $user->id)->orWhere('mentor_id', $user->id)));

        $this->upcomingSessionsList = $query->orderBy('scheduled_at')->take(5)->get();
    }

    private function loadRecentCodeReviews($user)
    {
        $query = CodeReview::with(['mentorship.mentor', 'mentorship.mentee', 'requester', 'reviewer']);

        $query->whereHas('mentorship', fn ($query) => $query->where(fn ($owned) => $owned->where('mentee_id', $user->id)->orWhere('mentor_id', $user->id)));

        $this->recentCodeReviews = $query->orderBy('requested_at', 'desc')->take(5)->get();
    }

    private function loadMyActiveMentorships($user)
    {
        $query = Mentorship::with(['mentor.mentorProfile', 'mentee'])
            ->where('status', Mentorship::STATUS_ACTIVE);

        $query->where(fn ($owned) => $owned->where('mentee_id', $user->id)->orWhere('mentor_id', $user->id));

        $this->myActiveMentorships = $query->orderBy('started_at', 'desc')->take(5)->get();
    }

    public function render()
    {
        return view('livewire.mentorship.mentorship-hub');
    }
}