<?php
// FILE: app/Livewire/Mentorship/Partial/Analytics.php

namespace App\Livewire\Mentorship\Partial;

use Livewire\Component;
use App\Models\Mentorship\MentorshipSession;
use App\Models\Mentorship\CodeReview;
use App\Models\Mentorship\Mentorship;
use Illuminate\Support\Facades\Auth;

class Analytics extends Component
{
    public $dateFilter = 'this_month';
    public $performanceMetrics = [];
    public $monthlyEarnings = 0;
    public $totalEarnings = 0;
    public $completedSessions = 0;
    public $upcomingSessions = 0;
    public $totalMentees = 0;
    public $averageRating = 0;
    public $totalReviews = 0;
    public $pendingCodeReviews = 0;

    public function mount()
    {
        $this->loadAnalytics();
    }

    public function updatedDateFilter()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        $user = Auth::user();
        $startDate = match($this->dateFilter) {
            'this_week' => now()->startOfWeek(),
            'this_month' => now()->startOfMonth(),
            'this_quarter' => now()->startOfQuarter(),
            'this_year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };

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

        $this->calculateEarnings();
        $this->loadStatistics();
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

    private function loadStatistics()
    {
        $user = Auth::user();
        $profile = $user->mentorProfile;

        if ($profile) {
            $this->totalMentees = $profile->total_mentees;
            $this->averageRating = $profile->rating;
            $this->totalReviews = $profile->total_reviews;
        }

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
    }

    public function render()
    {
        return view('livewire.mentorship.partial.analytics');
    }
}