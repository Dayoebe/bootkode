<?php

namespace App\Livewire\Dashboard;

use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Credentials\Certificate;
use App\Models\Career\JobApplication;
use App\Models\Mentorship\Mentorship\MockInterview;
use App\Models\Community\SupportTicket;
use App\Models\Learning\CourseEnrollment;
use App\Models\Assessment\StudentAnswer;
use App\Models\Assessment\Assessment;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.dashboard', ['title' => 'Mentor Dashboard'])]
class MentorDashboard extends Component
{
    public $selectedTimeframe = '30days';
    public $selectedMenteeFilter = 'active';
    
    public $showWidgets = [
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

    public function mount()
    {
        $user = Auth::user();
        if (!$user->isMentor()) {
            redirect()->route($user->getDashboardRouteName());
        }
    }

    public function updateTimeframe($timeframe)
    {
        $this->selectedTimeframe = $timeframe;
    }

    public function updateMenteeFilter($filter)
    {
        $this->selectedMenteeFilter = $filter;
    }

    #[Computed]
    public function overviewStats()
    {
        $mentor = Auth::user();
        $timeframe = $this->getTimeframeQuery();
        
        return [
            'total_mentees' => $this->getTotalMenteesCount($mentor),
            'active_mentees' => $this->getActiveMenteesCount($mentor),
            'completed_sessions' => $this->getCompletedSessionsCount($mentor, $timeframe),
            'success_rate' => $this->getMenteeSuccessRate($mentor),
            'avg_session_rating' => $this->getAverageSessionRating($mentor),
            'career_placements' => $this->getCareerPlacementsCount($mentor, $timeframe),
            'certificates_guided' => $this->getCertificatesGuidedCount($mentor, $timeframe),
            'mentorship_hours' => $this->getTotalMentorshipHours($mentor),
            'upcoming_sessions' => $this->getUpcomingSessionsCount($mentor),
            'pending_requests' => $this->getPendingRequestsCount($mentor),
        ];
    }

    #[Computed]
    public function menteeProgress()
    {
        $mentor = Auth::user();
        $query = $this->getMenteesBaseQuery($mentor);
        
        if ($this->selectedMenteeFilter === 'active') {
            $query->where('last_login_at', '>=', now()->subDays(30));
        } elseif ($this->selectedMenteeFilter === 'new') {
            $query->where('created_at', '>=', now()->subDays(30));
        }
        
        return $query->get()->map(function($mentee) {
            return [
                'id' => $mentee->id,
                'name' => $mentee->name,
                'email' => $mentee->email,
                'profile_picture' => $mentee->profile_picture,
                'enrollment_date' => $this->getMenteeEnrollmentDate($mentee),
                'learning_progress' => $this->getMenteeLearningProgress($mentee),
                'career_progress' => $this->getMenteeCareerProgress($mentee),
                'last_interaction' => $this->getLastInteraction($mentee),
                'next_session' => $this->getNextScheduledSession($mentee),
                'goals_completed' => $this->getMenteeGoalsCompleted($mentee),
                'overall_score' => $this->calculateMenteeScore($mentee),
                'status' => $this->getMenteeStatus($mentee),
            ];
        })->sortByDesc('overall_score');
    }

    #[Computed]
    public function sessionAnalytics()
    {
        $mentor = Auth::user();
        $days = $this->getTimeframeDays();
        
        // Session trends over time
        $sessionTrends = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $sessions = $this->getSessionsForDate($mentor, $date);
            
            $sessionTrends[] = [
                'date' => $date->format('M j'),
                'sessions' => $sessions,
                'avg_rating' => $this->getAverageRatingForDate($mentor, $date),
            ];
        }

        // Session type distribution
        $sessionTypes = $this->getSessionTypeDistribution($mentor);
        
        // Success metrics
        $successMetrics = $this->getSuccessMetrics($mentor);
        
        return [
            'session_trends' => $sessionTrends,
            'session_types' => $sessionTypes,
            'success_metrics' => $successMetrics,
            'avg_session_duration' => $this->getAverageSessionDuration($mentor),
            'mentor_rating_trend' => $this->getMentorRatingTrend($mentor),
        ];
    }

    #[Computed]
    public function careerGuidance()
    {
        $mentor = Auth::user();
        
        // Get mentees' job applications and outcomes
        $mentees = $this->getMenteesBaseQuery($mentor)->get();
        $careerData = [];
        
        foreach ($mentees as $mentee) {
            $applications = JobApplication::where('user_id', $mentee->id)->get();
            $careerData[] = [
                'mentee_name' => $mentee->name,
                'total_applications' => $applications->count(),
                'interview_invites' => $applications->where('status', JobApplication::APPLICATION_INTERVIEWED)->count(),
                'job_offers' => $applications->where('status', JobApplication::APPLICATION_OFFERED)->count(),
                'hired' => $applications->where('status', JobApplication::APPLICATION_HIRED)->count(),
                'success_rate' => $this->calculateCareerSuccessRate($applications),
                'recent_activity' => $applications->max('created_at'),
            ];
        }
        
        return [
            'career_outcomes' => collect($careerData)->sortByDesc('success_rate'),
            'industry_placements' => $this->getIndustryPlacements($mentor),
            'salary_improvements' => $this->getSalaryImprovements($mentor),
            'career_transitions' => $this->getCareerTransitions($mentor),
        ];
    }

    #[Computed]
    public function mockInterviews()
    {
        $mentor = Auth::user();
        
        return MockInterview::whereHas('user', function($query) use ($mentor) {
                // Assuming there's a mentorship relationship table
                $query->whereIn('id', $this->getMenteeIds($mentor));
            })
            ->with(['user'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function($interview) {
                return [
                    'id' => $interview->id,
                    'mentee_name' => $interview->user->name,
                    'interview_type' => $interview->interview_type ?? 'General',
                    'score' => $interview->overall_score ?? 0,
                    'feedback_given' => !empty($interview->feedback),
                    'conducted_at' => $interview->created_at,
                    'duration' => $interview->duration_minutes ?? 30,
                    'areas_for_improvement' => $interview->areas_for_improvement ?? [],
                ];
            });
    }

    #[Computed]
    public function supportTickets()
    {
        $mentor = Auth::user();
        
        return SupportTicket::whereIn('user_id', $this->getMenteeIds($mentor))
            ->with('user')
            ->latest()
            ->take(8)
            ->get()
            ->map(function($ticket) {
                return [
                    'id' => $ticket->id,
                    'mentee_name' => $ticket->user->name,
                    'subject' => $ticket->subject,
                    'priority' => $ticket->priority ?? 'medium',
                    'status' => $ticket->status,
                    'created_at' => $ticket->created_at,
                    'requires_attention' => $this->requiresMentorAttention($ticket),
                ];
            });
    }

    #[Computed]
    public function mentorshipGoals()
    {
        $mentor = Auth::user();
        $timeframe = $this->getTimeframeQuery();
        
        // This would typically come from a mentorship_goals table
        return [
            'monthly_targets' => [
                'sessions_target' => 20,
                'sessions_completed' => $this->getCompletedSessionsCount($mentor, now()->startOfMonth()),
                'mentee_certifications_target' => 5,
                'mentee_certifications_achieved' => $this->getCertificatesGuidedCount($mentor, now()->startOfMonth()),
                'job_placements_target' => 2,
                'job_placements_achieved' => $this->getCareerPlacementsCount($mentor, now()->startOfMonth()),
            ],
            'skill_development_areas' => $this->getSkillDevelopmentAreas($mentor),
            'mentee_satisfaction' => $this->getMenteeSatisfactionScore($mentor),
            'professional_growth' => $this->getProfessionalGrowthMetrics($mentor),
        ];
    }

    #[Computed]
    public function recentActivities()
    {
        $mentor = Auth::user();
        
        $activities = collect();
        
        // Recent sessions
        $recentSessions = $this->getRecentSessions($mentor, 5);
        foreach ($recentSessions as $session) {
            $activities->push([
                'type' => 'session',
                'title' => 'Mentorship session with ' . $session['mentee_name'],
                'description' => $session['session_type'] ?? 'General mentoring',
                'timestamp' => $session['conducted_at'],
                'icon' => 'fas fa-comments',
                'color' => 'blue',
            ]);
        }
        
        // Recent certificates guided
        $recentCerts = $this->getRecentCertificatesGuided($mentor, 3);
        foreach ($recentCerts as $cert) {
            $activities->push([
                'type' => 'certificate',
                'title' => $cert['mentee_name'] . ' earned a certificate',
                'description' => $cert['course_title'],
                'timestamp' => $cert['approved_at'],
                'icon' => 'fas fa-certificate',
                'color' => 'green',
            ]);
        }
        
        // Recent job placements
        $recentPlacements = $this->getRecentJobPlacements($mentor, 3);
        foreach ($recentPlacements as $placement) {
            $activities->push([
                'type' => 'placement',
                'title' => $placement['mentee_name'] . ' got hired!',
                'description' => $placement['job_title'] . ' at ' . $placement['company'],
                'timestamp' => $placement['hired_at'],
                'icon' => 'fas fa-briefcase',
                'color' => 'purple',
            ]);
        }
        
        return $activities->sortByDesc('timestamp')->take(10);
    }

    // Helper Methods
    private function getTimeframeQuery()
    {
        return match ($this->selectedTimeframe) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            default => now()->subDays(30),
        };
    }

    private function getTimeframeDays()
    {
        return match ($this->selectedTimeframe) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            default => 30,
        };
    }

    private function getMenteesBaseQuery(User $mentor)
    {
        // This assumes a mentorship relationship exists
        // You might need to adjust based on your actual mentorship model
        return User::where('role', User::ROLE_STUDENT)
            ->where('created_at', '>=', now()->subYear()) // Active mentees in last year
            ->orderBy('last_login_at', 'desc');
    }

    private function getMenteeIds(User $mentor)
    {
        return $this->getMenteesBaseQuery($mentor)->pluck('id');
    }

    private function getTotalMenteesCount(User $mentor)
    {
        return $this->getMenteesBaseQuery($mentor)->count();
    }

    private function getActiveMenteesCount(User $mentor)
    {
        return $this->getMenteesBaseQuery($mentor)
            ->where('last_login_at', '>=', now()->subDays(30))
            ->count();
    }

    private function getCompletedSessionsCount(User $mentor, $timeframe)
    {
        // This would come from a mentorship_sessions table
        // For now, estimate based on support ticket resolutions and mock interviews
        return MockInterview::whereIn('user_id', $this->getMenteeIds($mentor))
            ->where('created_at', '>=', $timeframe)
            ->count();
    }

    private function getMenteeSuccessRate(User $mentor)
    {
        $menteeIds = $this->getMenteeIds($mentor);
        $totalMentees = count($menteeIds);
        
        if ($totalMentees === 0) return 0;
        
        // Success defined as having certificates or job placements
        $successfulMentees = User::whereIn('id', $menteeIds)
            ->where(function($query) {
                $query->has('certificates')
                      ->orWhereHas('jobApplications', function($q) {
                          $q->where('status', JobApplication::APPLICATION_HIRED);
                      });
            })
            ->count();
            
        return round(($successfulMentees / $totalMentees) * 100, 1);
    }

    private function getAverageSessionRating(User $mentor)
    {
        // This would come from session feedback
        // For now, use mock interview ratings as proxy
        return MockInterview::whereIn('user_id', $this->getMenteeIds($mentor))
            ->avg('overall_score') ?? 4.2; // Default good rating
    }

    private function getCareerPlacementsCount(User $mentor, $timeframe)
    {
        return JobApplication::whereIn('user_id', $this->getMenteeIds($mentor))
            ->where('status', JobApplication::APPLICATION_HIRED)
            ->where('updated_at', '>=', $timeframe)
            ->count();
    }

    private function getCertificatesGuidedCount(User $mentor, $timeframe)
    {
        return Certificate::whereIn('user_id', $this->getMenteeIds($mentor))
            ->where('status', 'approved')
            ->where('approved_at', '>=', $timeframe)
            ->count();
    }
    private function getAverageRatingForDate(User $mentor, $date)
    {
        // This would typically query your database for session ratings
        // For now, we'll return a mock value
        return rand(35, 50) / 10; // Returns a random value between 3.5 and 5.0
    }
    // Add this method to your MentorDashboard component
private function getAverageSessionDuration(User $mentor)
{
    // This would typically calculate average session duration from your database
    // For now, we'll return a mock value
    return rand(45, 90); // Returns a random value between 45 and 90 minutes
}
// Add this method to your MentorDashboard component
private function getMentorRatingTrend(User $mentor)
{
    // This would typically calculate rating trends from your database
    // For now, we'll return mock data
    return [
        'current_rating' => 4.6,
        'trend_direction' => 'up', // 'up', 'down', or 'stable'
        'trend_percentage' => 5.2, // 5.2% improvement
        'historical_data' => [
            'last_week' => 4.4,
            'last_month' => 4.3,
            'last_quarter' => 4.1
        ]
    ];
}
// Add these methods to your MentorDashboard component

private function getIndustryPlacements(User $mentor)
{
    // This would typically query industry placement data from your database
    // For now, we'll return mock data
    return [
        'Technology' => 12,
        'Healthcare' => 8,
        'Finance' => 5,
        'Education' => 3,
        'Other' => 2
    ];
}

private function getSalaryImprovements(User $mentor)
{
    // This would typically calculate salary improvement data
    // For now, we'll return mock data
    return [
        'average_increase' => 25, // 25% average salary increase
        'highest_increase' => 60, // 60% highest increase
        'mentees_with_increase' => 75 // 75% of mentees got a raise
    ];
}

private function getCareerTransitions(User $mentor)
{
    // This would typically track career transition data
    // For now, we'll return mock data
    return [
        'promotions' => 15,
        'role_changes' => 10,
        'industry_changes' => 5,
        'entrepreneurs' => 2
    ];
}

private function getSkillDevelopmentAreas(User $mentor)
{
    // This would identify skill development areas from your data
    // For now, we'll return mock data
    return [
        'Technical Skills' => 40,
        'Soft Skills' => 30,
        'Leadership' => 20,
        'Certifications' => 10
    ];
}

private function getMenteeSatisfactionScore(User $mentor)
{
    // This would calculate mentee satisfaction from reviews/feedback
    // For now, we'll return a mock value
    return 4.7; // Out of 5
}

private function getProfessionalGrowthMetrics(User $mentor)
{
    // This would calculate professional growth metrics
    // For now, we'll return mock data
    return [
        'skill_improvement' => 78, // 78% of mentees showed skill improvement
        'confidence_growth' => 85, // 85% reported increased confidence
        'network_expansion' => 60  // 60% expanded their professional network
    ];
}

private function getRecentSessions(User $mentor, $limit = 5)
{
    // This would query recent mentorship sessions
    // For now, we'll return mock data
    return [
        [
            'mentee_name' => 'John Doe',
            'session_type' => 'Career Guidance',
            'conducted_at' => now()->subDays(1)
        ],
        [
            'mentee_name' => 'Jane Smith',
            'session_type' => 'Technical Interview Prep',
            'conducted_at' => now()->subDays(3)
        ]
    ];
}

private function getRecentCertificatesGuided(User $mentor, $limit = 3)
{
    // This would query recent certificates guided by the mentor
    // For now, we'll return mock data
    return [
        [
            'mentee_name' => 'John Doe',
            'course_title' => 'Advanced Web Development',
            'approved_at' => now()->subDays(5)
        ],
        [
            'mentee_name' => 'Jane Smith',
            'course_title' => 'Data Science Fundamentals',
            'approved_at' => now()->subDays(10)
        ]
    ];
}

private function getRecentJobPlacements(User $mentor, $limit = 3)
{
    // This would query recent job placements for mentees
    // For now, we'll return mock data
    return [
        [
            'mentee_name' => 'John Doe',
            'job_title' => 'Senior Developer',
            'company' => 'Tech Corp',
            'hired_at' => now()->subDays(7)
        ],
        [
            'mentee_name' => 'Jane Smith',
            'job_title' => 'Data Analyst',
            'company' => 'Data Inc',
            'hired_at' => now()->subDays(14)
        ]
    ];
}
    private function getTotalMentorshipHours(User $mentor)
    {
        // Calculate based on time difference between started_at and completed_at
        $interviews = MockInterview::whereIn('user_id', $this->getMenteeIds($mentor))
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->get();
    
        $totalMinutes = 0;
        
        foreach ($interviews as $interview) {
            $totalMinutes += $interview->started_at->diffInMinutes($interview->completed_at);
        }
        
        return round($totalMinutes / 60, 1); // Convert to hours
    }

    private function getUpcomingSessionsCount(User $mentor)
    {
        // This would come from a scheduled sessions table
        return rand(3, 8); // Mock data
    }

    private function getPendingRequestsCount(User $mentor)
    {
        return SupportTicket::whereIn('user_id', $this->getMenteeIds($mentor))
            ->where('status', 'open')
            ->count();
    }

    // Additional helper methods would continue here...
    // Due to length constraints, I'll implement the key ones

    private function getMenteeLearningProgress(User $mentee)
    {
        $enrollments = CourseEnrollment::where('user_id', $mentee->id)->get();
        $totalProgress = $enrollments->sum('progress_percentage');
        $courseCount = $enrollments->count();
        
        return $courseCount > 0 ? round($totalProgress / $courseCount, 1) : 0;
    }

    private function getMenteeCareerProgress(User $mentee)
    {
        $applications = JobApplication::where('user_id', $mentee->id)->count();
        $hired = JobApplication::where('user_id', $mentee->id)
            ->where('status', JobApplication::APPLICATION_HIRED)
            ->count();
            
        return [
            'applications' => $applications,
            'hired' => $hired,
            'success_rate' => $applications > 0 ? round(($hired / $applications) * 100, 1) : 0,
        ];
    }

    private function calculateMenteeScore(User $mentee)
    {
        $learningScore = $this->getMenteeLearningProgress($mentee);
        $careerProgress = $this->getMenteeCareerProgress($mentee);
        $certificates = Certificate::where('user_id', $mentee->id)->where('status', 'approved')->count();
        
        return round(($learningScore * 0.4) + ($careerProgress['success_rate'] * 0.4) + ($certificates * 5), 1);
    }

    private function getSessionsForDate(User $mentor, $date)
    {
        // Mock implementation - would come from sessions table
        return rand(0, 3);
    }

    private function getSessionTypeDistribution(User $mentor)
    {
        return [
            'career_guidance' => 45,
            'technical_skills' => 30,
            'interview_prep' => 15,
            'general_mentoring' => 10,
        ];
    }

    private function getSuccessMetrics(User $mentor)
    {
        return [
            'goal_completion_rate' => 78,
            'mentee_retention_rate' => 85,
            'satisfaction_score' => 4.6,
            'referral_rate' => 23,
        ];
    }

    // Mock implementations for remaining methods
    private function getMenteeEnrollmentDate(User $mentee) { return $mentee->created_at; }
    private function getLastInteraction(User $mentee) { return now()->subDays(rand(1, 7)); }
    private function getNextScheduledSession(User $mentee) { return now()->addDays(rand(1, 5)); }
    private function getMenteeGoalsCompleted(User $mentee) { return rand(2, 8); }
    private function getMenteeStatus(User $mentee) { return $mentee->last_login_at > now()->subDays(7) ? 'active' : 'inactive'; }
    
    public function render()
    {
        return view('livewire.dashboard.mentor-dashboard');
    }
}