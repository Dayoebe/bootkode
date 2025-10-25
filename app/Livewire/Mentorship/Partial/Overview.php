<?php
// FILE: app/Livewire/Mentorship/Partial/Overview.php

namespace App\Livewire\Mentorship\Partial;

use Livewire\Component;
use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MentorshipSession;
use App\Models\Mentorship\CodeReview;
use Illuminate\Support\Facades\Auth;

class Overview extends Component
{
    public $performanceMetrics = [];
    public $pendingRequestsList = [];
    public $upcomingSessionsList = [];
    public $recentCodeReviews = [];
    public $profileId = null;
    public $isAvailable = true;

    public function mount()
    {
        $this->loadProfile();
        $this->loadPerformanceMetrics();
        $this->loadPendingRequests();
        $this->loadUpcomingSessions();
        $this->loadRecentCodeReviews();
    }

    public function loadProfile()
    {
        $profile = Auth::user()->mentorProfile;
        if ($profile) {
            $this->profileId = $profile->id;
            $this->isAvailable = $profile->is_available;
        }
    }

    public function loadPerformanceMetrics()
    {
        $user = Auth::user();
        $startDate = now()->startOfMonth();

        $this->performanceMetrics = [
            'sessions_conducted' => MentorshipSession::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            })
            ->where('status', MentorshipSession::STATUS_COMPLETED)
            ->where('ended_at', '>=', $startDate)
            ->count(),

            'average_session_rating' => MentorshipSession::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            })
            ->where('status', MentorshipSession::STATUS_COMPLETED)
            ->where('ended_at', '>=', $startDate)
            ->whereNotNull('mentee_rating')
            ->avg('mentee_rating') ?? 0,

            'code_reviews_completed' => CodeReview::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            })
            ->where('status', CodeReview::STATUS_COMPLETED)
            ->where('completed_at', '>=', $startDate)
            ->count(),

            'response_time_hours' => $this->calculateAverageResponseTime($startDate),
        ];
    }

    private function calculateAverageResponseTime($startDate)
    {
        $codeReviews = CodeReview::whereHas('mentorship', function($q) {
            $q->where('mentor_id', Auth::id());
        })
        ->whereNotNull('started_review_at')
        ->where('requested_at', '>=', $startDate)
        ->get();

        if ($codeReviews->isEmpty()) return 0;

        $totalHours = $codeReviews->sum(function($review) {
            return $review->requested_at->diffInHours($review->started_review_at);
        });

        return round($totalHours / $codeReviews->count(), 1);
    }

    public function loadPendingRequests()
    {
        $this->pendingRequestsList = Mentorship::with('mentee')
            ->where('mentor_id', Auth::id())
            ->where('status', Mentorship::STATUS_PENDING)
            ->orderBy('requested_at', 'desc')
            ->get();
    }

    public function loadUpcomingSessions()
    {
        $this->upcomingSessionsList = MentorshipSession::with(['mentorship.mentee'])
            ->whereHas('mentorship', function($q) {
                $q->where('mentor_id', Auth::id());
            })
            ->where('status', MentorshipSession::STATUS_SCHEDULED)
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();
    }

    public function loadRecentCodeReviews()
    {
        $this->recentCodeReviews = CodeReview::with(['mentorship.mentee', 'requester'])
            ->whereHas('mentorship', function($q) {
                $q->where('mentor_id', Auth::id());
            })
            ->orderBy('requested_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function acceptMentorship($mentorshipId)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        if (!$mentorship || $mentorship->mentor_id !== Auth::id()) {
            session()->flash('error', 'Invalid mentorship request.');
            return;
        }

        $profile = Auth::user()->mentorProfile;
        if (!$profile || !$profile->canAcceptMentees()) {
            session()->flash('error', 'You have reached your maximum mentee capacity.');
            return;
        }

        $mentorship->accept();
        $mentorship->mentee->notify(new \App\Notifications\MentorshipAccepted($mentorship));

        session()->flash('message', 'Mentorship request accepted!');
        
        $this->loadPendingRequests();
        $this->dispatch('mentorship-updated');
    }

    public function rejectMentorship($mentorshipId, $reason = null)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        if (!$mentorship || $mentorship->mentor_id !== Auth::id()) {
            session()->flash('error', 'Invalid mentorship request.');
            return;
        }

        $mentorship->reject($reason);
        $mentorship->mentee->notify(new \App\Notifications\MentorshipRejected($mentorship));

        session()->flash('message', 'Mentorship request rejected.');
        
        $this->loadPendingRequests();
        $this->dispatch('mentorship-updated');
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

    public function editProfile()
    {
        $this->dispatch('open-profile-modal');
    }

    public function applyToBecomeMentor()
    {
        $this->dispatch('open-application-modal');
    }

    public function render()
    {
        return view('livewire.mentorship.partial.overview');
    }
}