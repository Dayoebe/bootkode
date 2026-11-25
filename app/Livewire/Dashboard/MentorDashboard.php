<?php

namespace App\Livewire\Dashboard;

use App\Models\Career\JobApplication;
use App\Models\Community\SupportTicket;
use App\Models\Core\User;
use App\Models\Credentials\Certificate;
use App\Models\Learning\CourseEnrollment;
use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MentorshipReview;
use App\Models\Mentorship\MentorshipSession;
use App\Models\Mentorship\MockInterview;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', ['title' => 'Mentor Dashboard'])]
class MentorDashboard extends Component
{
    public string $selectedTimeframe = '30days';
    public string $selectedMenteeFilter = 'active';
    public array $showWidgets = [
        'overview_stats' => true,
        'mentee_progress' => true,
        'session_analytics' => true,
        'career_guidance' => true,
        'mock_interviews' => true,
        'support_tickets' => true,
        'mentorship_goals' => true,
        'recent_activities' => true,
    ];

    protected $listeners = [
        'refreshDashboard' => 'loadAllData',
        'timeframeChanged' => 'updateTimeframe',
        'menteeFilterChanged' => 'updateMenteeFilter',
    ];

    public function mount(): void
    {
        $user = Auth::user();

        if (! $user->canAccessDashboardRole(User::ROLE_MENTOR)) {
            $this->redirectRoute($user->getDashboardRouteName(), navigate: true);
        }
    }

    public function hydrate(): void
    {
        abort_unless(Auth::user()?->canAccessDashboardRole(User::ROLE_MENTOR), 403);
    }

    public function updateTimeframe(string $timeframe): void
    {
        validator(['timeframe' => $timeframe], [
            'timeframe' => ['required', Rule::in(['7days', '30days', '90days'])],
        ])->validate();

        $this->selectedTimeframe = $timeframe;
    }

    public function updateMenteeFilter(string $filter): void
    {
        validator(['filter' => $filter], [
            'filter' => ['required', Rule::in(['all', 'active', 'new', 'struggling'])],
        ])->validate();

        $this->selectedMenteeFilter = $filter;
    }

    public function updatedSelectedMenteeFilter(string $filter): void
    {
        $this->updateMenteeFilter($filter);
    }

    #[Computed]
    public function overviewStats(): array
    {
        $mentor = Auth::user();
        $since = $this->timeframeStart();
        $menteeIds = $this->menteeIds($mentor);

        return [
            'total_mentees' => $menteeIds->count(),
            'active_mentees' => Mentorship::query()
                ->where('mentor_id', $mentor->id)
                ->where('status', Mentorship::STATUS_ACTIVE)
                ->distinct('mentee_id')
                ->count('mentee_id'),
            'completed_sessions' => $this->sessionsFor($mentor)
                ->where('status', MentorshipSession::STATUS_COMPLETED)
                ->where('scheduled_at', '>=', $since)
                ->count(),
            'success_rate' => $this->recordedOutcomeRate($menteeIds),
            'avg_session_rating' => $this->mentorRating($mentor),
            'career_placements' => JobApplication::query()
                ->whereIn('user_id', $menteeIds)
                ->where('status', JobApplication::APPLICATION_HIRED)
                ->where('updated_at', '>=', $since)
                ->count(),
            'certificates_guided' => Certificate::query()
                ->whereIn('user_id', $menteeIds)
                ->where('status', Certificate::STATUS_APPROVED)
                ->where('approved_at', '>=', $since)
                ->count(),
            'mentorship_hours' => round(((int) $this->sessionsFor($mentor)
                ->where('status', MentorshipSession::STATUS_COMPLETED)
                ->sum('duration_minutes')) / 60, 1),
            'upcoming_sessions' => $this->sessionsFor($mentor)
                ->where('status', MentorshipSession::STATUS_SCHEDULED)
                ->where('scheduled_at', '>=', now())
                ->count(),
            'pending_requests' => Mentorship::query()
                ->where('mentor_id', $mentor->id)
                ->where('status', Mentorship::STATUS_PENDING)
                ->count(),
            'open_support_tickets' => SupportTicket::query()
                ->whereIn('user_id', $menteeIds)
                ->whereIn('status', ['open', 'pending'])
                ->count(),
        ];
    }

    #[Computed]
    public function menteeProgress(): Collection
    {
        $mentor = Auth::user();
        $relationships = Mentorship::query()
            ->where('mentor_id', $mentor->id)
            ->whereIn('status', [Mentorship::STATUS_ACTIVE, Mentorship::STATUS_COMPLETED])
            ->with('mentee')
            ->latest('accepted_at')
            ->get()
            ->unique('mentee_id');

        $relationships = match ($this->selectedMenteeFilter) {
            'active' => $relationships->where('status', Mentorship::STATUS_ACTIVE),
            'new' => $relationships->filter(fn (Mentorship $mentorship) =>
                ($mentorship->accepted_at ?? $mentorship->created_at)->gte(now()->subDays(30))),
            'struggling' => $relationships->filter(fn (Mentorship $mentorship) =>
                ! $mentorship->mentee?->last_login_at || $mentorship->mentee->last_login_at->lt(now()->subDays(30))),
            default => $relationships,
        };

        return $relationships
            ->filter(fn (Mentorship $mentorship) => $mentorship->mentee)
            ->map(function (Mentorship $mentorship) use ($mentor): array {
                $mentee = $mentorship->mentee;
                $learningProgress = $this->learningProgress($mentee);
                $careerProgress = $this->careerProgress($mentee);
                $certificates = Certificate::query()
                    ->where('user_id', $mentee->id)
                    ->where('status', Certificate::STATUS_APPROVED)
                    ->count();

                return [
                    'id' => $mentee->id,
                    'name' => $mentee->name,
                    'email' => $mentee->email,
                    'profile_picture' => $mentee->profile_picture,
                    'enrollment_date' => $mentorship->accepted_at ?? $mentorship->created_at,
                    'learning_progress' => $learningProgress,
                    'career_progress' => $careerProgress,
                    'last_interaction' => $this->sessionsFor($mentor)
                        ->where('mentorship_id', $mentorship->id)
                        ->whereNotNull('ended_at')
                        ->latest('ended_at')
                        ->value('ended_at'),
                    'next_session' => $this->sessionsFor($mentor)
                        ->where('mentorship_id', $mentorship->id)
                        ->where('status', MentorshipSession::STATUS_SCHEDULED)
                        ->where('scheduled_at', '>=', now())
                        ->oldest('scheduled_at')
                        ->value('scheduled_at'),
                    'goals_completed' => collect($mentorship->goals_with_progress)->where('completed', true)->count(),
                    'overall_score' => round(($learningProgress * 0.5) + ($careerProgress['success_rate'] * 0.4) + min($certificates * 2, 10), 1),
                    'status' => $mentorship->status,
                ];
            })
            ->sortByDesc('overall_score')
            ->values();
    }

    #[Computed]
    public function sessionAnalytics(): array
    {
        $mentor = Auth::user();
        $sessionTrends = [];

        for ($offset = $this->timeframeDays() - 1; $offset >= 0; $offset--) {
            $date = now()->subDays($offset);
            $dailySessions = $this->sessionsFor($mentor)
                ->whereDate('scheduled_at', $date)
                ->where('status', MentorshipSession::STATUS_COMPLETED);
            $sessionTrends[] = [
                'date' => $date->format('M j'),
                'sessions' => (clone $dailySessions)->count(),
                'avg_rating' => round((float) ((clone $dailySessions)->avg('mentee_rating') ?? 0), 1),
            ];
        }

        $typeCounts = $this->sessionsFor($mentor)
            ->where('scheduled_at', '>=', $this->timeframeStart())
            ->where('status', MentorshipSession::STATUS_COMPLETED)
            ->selectRaw('type, COUNT(*) as aggregate')
            ->groupBy('type')
            ->pluck('aggregate', 'type');
        $totalSessions = max(1, (int) $typeCounts->sum());
        $relationships = Mentorship::query()
            ->where('mentor_id', $mentor->id)
            ->whereIn('status', [Mentorship::STATUS_ACTIVE, Mentorship::STATUS_COMPLETED])
            ->get();

        return [
            'session_trends' => $sessionTrends,
            'session_types' => $typeCounts
                ->map(fn ($count) => round(((int) $count / $totalSessions) * 100, 1))
                ->all(),
            'success_metrics' => [
                'goal_completion_rate' => round((float) ($relationships->avg('goal_completion_percentage') ?? 0), 1),
                'mentee_retention_rate' => $this->relationshipCompletionRate($mentor),
            ],
        ];
    }

    #[Computed]
    public function careerGuidance(): array
    {
        $outcomes = User::query()
            ->whereIn('id', $this->menteeIds(Auth::user()))
            ->get()
            ->map(function (User $mentee): array {
                $applications = JobApplication::query()->where('user_id', $mentee->id)->get();

                return [
                    'mentee_name' => $mentee->name,
                    'total_applications' => $applications->count(),
                    'interview_invites' => $applications->where('status', JobApplication::APPLICATION_INTERVIEWED)->count(),
                    'job_offers' => $applications->where('status', JobApplication::APPLICATION_OFFERED)->count(),
                    'hired' => $applications->where('status', JobApplication::APPLICATION_HIRED)->count(),
                    'success_rate' => $this->careerSuccessRate($applications),
                    'recent_activity' => $applications->max('created_at'),
                ];
            });

        return ['career_outcomes' => $outcomes->sortByDesc('success_rate')];
    }

    #[Computed]
    public function mockInterviews(): Collection
    {
        return MockInterview::query()
            ->whereIn('user_id', $this->menteeIds(Auth::user()))
            ->with('user')
            ->latest()
            ->take(10)
            ->get()
            ->filter(fn (MockInterview $interview) => $interview->user)
            ->map(fn (MockInterview $interview): array => [
                'id' => $interview->id,
                'mentee_name' => $interview->user->name,
                'interview_type' => $interview->interview_type ?? 'General',
                'score' => $interview->overall_score ?? 0,
                'feedback_given' => filled($interview->feedback),
                'conducted_at' => $interview->created_at,
                'duration' => $interview->duration_minutes ?? 0,
            ]);
    }

    #[Computed]
    public function supportTickets(): Collection
    {
        return SupportTicket::query()
            ->whereIn('user_id', $this->menteeIds(Auth::user()))
            ->with('user')
            ->latest()
            ->take(8)
            ->get()
            ->filter(fn (SupportTicket $ticket) => $ticket->user)
            ->map(fn (SupportTicket $ticket): array => [
                'id' => $ticket->id,
                'mentee_name' => $ticket->user->name,
                'subject' => $ticket->subject,
                'priority' => $ticket->priority ?? 'medium',
                'status' => $ticket->status,
                'created_at' => $ticket->created_at,
            ]);
    }

    #[Computed]
    public function mentorshipGoals(): array
    {
        $mentor = Auth::user();
        $menteeIds = $this->menteeIds($mentor);

        return ['recorded_this_month' => [
            'sessions_completed' => $this->sessionsFor($mentor)
                ->where('status', MentorshipSession::STATUS_COMPLETED)
                ->where('scheduled_at', '>=', now()->startOfMonth())
                ->count(),
            'mentee_certifications' => Certificate::query()
                ->whereIn('user_id', $menteeIds)
                ->where('status', Certificate::STATUS_APPROVED)
                ->where('approved_at', '>=', now()->startOfMonth())
                ->count(),
            'job_placements' => JobApplication::query()
                ->whereIn('user_id', $menteeIds)
                ->where('status', JobApplication::APPLICATION_HIRED)
                ->where('updated_at', '>=', now()->startOfMonth())
                ->count(),
        ]];
    }

    #[Computed]
    public function recentActivities(): Collection
    {
        $mentor = Auth::user();
        $menteeIds = $this->menteeIds($mentor);
        $activities = collect();

        $this->sessionsFor($mentor)
            ->with('mentorship.mentee')
            ->where('status', MentorshipSession::STATUS_COMPLETED)
            ->latest('ended_at')
            ->take(5)
            ->get()
            ->each(function (MentorshipSession $session) use ($activities): void {
                $activities->push([
                    'title' => 'Session completed with '.$session->mentorship->mentee->name,
                    'description' => $session->title,
                    'timestamp' => $session->ended_at ?? $session->updated_at,
                    'icon' => 'fas fa-comments',
                    'color' => 'text-brand',
                ]);
            });

        Certificate::query()
            ->whereIn('user_id', $menteeIds)
            ->where('status', Certificate::STATUS_APPROVED)
            ->with(['user', 'course'])
            ->latest('approved_at')
            ->take(3)
            ->get()
            ->each(function (Certificate $certificate) use ($activities): void {
                $activities->push([
                    'title' => $certificate->user->name.' earned a certificate',
                    'description' => $certificate->course?->title ?? 'Course certificate',
                    'timestamp' => $certificate->approved_at,
                    'icon' => 'fas fa-certificate',
                    'color' => 'text-green-600',
                ]);
            });

        JobApplication::query()
            ->whereIn('user_id', $menteeIds)
            ->where('status', JobApplication::APPLICATION_HIRED)
            ->with(['user', 'job'])
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->each(function (JobApplication $application) use ($activities): void {
                $activities->push([
                    'title' => $application->user->name.' recorded a hired outcome',
                    'description' => collect([$application->job?->title, $application->job?->company_name])->filter()->join(' at '),
                    'timestamp' => $application->updated_at,
                    'icon' => 'fas fa-briefcase',
                    'color' => 'text-brand',
                ]);
            });

        return $activities->filter(fn (array $activity) => $activity['timestamp'])
            ->sortByDesc('timestamp')
            ->take(10)
            ->values();
    }

    public function loadAllData(): void
    {
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view('livewire.dashboard.mentor-dashboard');
    }

    private function timeframeStart()
    {
        return match ($this->selectedTimeframe) {
            '7days' => now()->subDays(7),
            '90days' => now()->subDays(90),
            default => now()->subDays(30),
        };
    }

    private function timeframeDays(): int
    {
        return match ($this->selectedTimeframe) {
            '7days' => 7,
            '90days' => 90,
            default => 30,
        };
    }

    private function menteeIds(User $mentor): Collection
    {
        return Mentorship::query()
            ->where('mentor_id', $mentor->id)
            ->whereIn('status', [Mentorship::STATUS_ACTIVE, Mentorship::STATUS_COMPLETED])
            ->distinct()
            ->pluck('mentee_id');
    }

    private function sessionsFor(User $mentor)
    {
        return MentorshipSession::query()
            ->whereHas('mentorship', fn ($query) => $query->where('mentor_id', $mentor->id));
    }

    private function recordedOutcomeRate(Collection $menteeIds): float
    {
        if ($menteeIds->isEmpty()) {
            return 0;
        }

        $outcomes = User::query()
            ->whereIn('id', $menteeIds)
            ->where(function ($query): void {
                $query->whereHas('certificates', fn ($certificates) =>
                    $certificates->where('status', Certificate::STATUS_APPROVED))
                    ->orWhereHas('jobApplications', fn ($applications) =>
                        $applications->where('status', JobApplication::APPLICATION_HIRED));
            })
            ->count();

        return round(($outcomes / $menteeIds->count()) * 100, 1);
    }

    private function mentorRating(User $mentor): float
    {
        return round((float) (MentorshipReview::query()
            ->where('reviewee_id', $mentor->id)
            ->avg('overall_rating') ?? 0), 1);
    }

    private function learningProgress(User $mentee): float
    {
        return round((float) (CourseEnrollment::query()
            ->where('user_id', $mentee->id)
            ->avg('progress_percentage') ?? 0), 1);
    }

    private function careerProgress(User $mentee): array
    {
        $applications = JobApplication::query()->where('user_id', $mentee->id)->get();

        return [
            'applications' => $applications->count(),
            'hired' => $applications->where('status', JobApplication::APPLICATION_HIRED)->count(),
            'success_rate' => $this->careerSuccessRate($applications),
        ];
    }

    private function careerSuccessRate(Collection $applications): float
    {
        if ($applications->isEmpty()) {
            return 0;
        }

        $successful = $applications->whereIn('status', [
            JobApplication::APPLICATION_OFFERED,
            JobApplication::APPLICATION_HIRED,
        ])->count();

        return round(($successful / $applications->count()) * 100, 1);
    }

    private function relationshipCompletionRate(User $mentor): float
    {
        $relationships = Mentorship::query()
            ->where('mentor_id', $mentor->id)
            ->whereIn('status', [Mentorship::STATUS_ACTIVE, Mentorship::STATUS_COMPLETED]);
        $total = (clone $relationships)->count();

        return $total === 0
            ? 0
            : round(((clone $relationships)->where('status', Mentorship::STATUS_COMPLETED)->count() / $total) * 100, 1);
    }
}
