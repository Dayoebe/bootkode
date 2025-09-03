<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\User;
use App\Models\Course;
use App\Models\Assessment;
use App\Models\GamificationData;
use App\Models\UserBadge;
use App\Models\LearningSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'About Us - BootKode', 
    'description' => "Empowering Africa's youth with digital skills, mentorship & careers.", 
    'developer' => 'Bootkode', 
    'developer_url' => 'https://bootkode.com'
])]


class LearningAnalyticsDashboard extends Component
{
    public $user;
    public $selectedTimeframe = '7d';
    public $selectedMetric = 'overview';
    public $showAchievements = false;
    public $showLeaderboard = false;
    public $autoRefresh = true;
    
    // Real-time data properties
    public $stats = [];
    public $chartData = [];
    public $achievements = [];
    public $leaderboard = [];
    public $streakData = [];
    public $progressData = [];

    protected $listeners = [
        'refresh-dashboard' => 'loadDashboardData',
        'metric-changed' => 'updateMetric',
        'timeframe-changed' => 'updateTimeframe'
    ];

    public function mount($userId = null)
    {
        $this->user = $userId ? User::find($userId) : auth()->user();
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->stats = $this->getGamificationStats();
        $this->chartData = $this->getChartData();
        $this->achievements = $this->getRecentAchievements();
        $this->leaderboard = $this->getLeaderboardData();
        $this->streakData = $this->getStreakAnalytics();
        $this->progressData = $this->getProgressAnalytics();
    }

    public function updateMetric($metric)
    {
        $this->selectedMetric = $metric;
        $this->chartData = $this->getChartData();
    }

    public function updateTimeframe($timeframe)
    {
        $this->selectedTimeframe = $timeframe;
        $this->chartData = $this->getChartData();
    }

    public function toggleAchievements()
    {
        $this->showAchievements = !$this->showAchievements;
    }

    public function toggleLeaderboard()
    {
        $this->showLeaderboard = !$this->showLeaderboard;
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        $this->emit('data-refreshed');
    }

    private function getGamificationStats()
    {
        $gamificationData = $this->user->getOrCreateGamificationData();
        $energyStatus = $this->user->getEnergyStatus();
        
        return [
            'level' => $gamificationData->level,
            'total_points' => $gamificationData->total_points,
            'coins' => $gamificationData->coins,
            'gems' => $gamificationData->gems,
            'current_streak' => $gamificationData->current_streak,
            'longest_streak' => $gamificationData->longest_streak,
            'energy' => $energyStatus,
            'progress_to_next_level' => $gamificationData->progress_percentage ?? 0,
            'rank' => $this->getUserRank(),
            'badges_count' => $this->user->badges()->count(),
            'courses_completed' => $this->user->courses()->wherePivot('progress', 100)->count(),
            'lessons_completed' => $this->user->completedLessons()->count(),
            'average_quiz_score' => $this->getAverageQuizScore(),
        ];
    }

    private function getChartData()
    {
        $cacheKey = "chart_data_{$this->user->id}_{$this->selectedMetric}_{$this->selectedTimeframe}";
        
        return Cache::remember($cacheKey, 300, function () {
            $days = $this->getTimeframeDays();
            $startDate = now()->subDays($days);

            switch ($this->selectedMetric) {
                case 'points':
                    return $this->getPointsChartData($startDate);
                case 'learning_time':
                    return $this->getLearningTimeChartData($startDate);
                case 'assessments':
                    return $this->getAssessmentChartData($startDate);
                case 'streaks':
                    return $this->getStreakChartData($startDate);
                default:
                    return $this->getOverviewChartData($startDate);
            }
        });
    }

    private function getPointsChartData($startDate)
    {
        $transactions = $this->user->gamificationTransactions()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(points) as points')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $transactions->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M j')),
            'datasets' => [
                [
                    'label' => 'Points Earned',
                    'data' => $transactions->pluck('points'),
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ]
            ]
        ];
    }

    private function getLearningTimeChartData($startDate)
    {
        $sessions = LearningSession::where('user_id', $this->user->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(duration_minutes) as minutes')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $sessions->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M j')),
            'datasets' => [
                [
                    'label' => 'Learning Time (minutes)',
                    'data' => $sessions->pluck('minutes'),
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ]
            ]
        ];
    }

    private function getAssessmentChartData($startDate)
    {
        $scores = DB::table('student_answers')
            ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
            ->where('student_answers.user_id', $this->user->id)
            ->where('student_answers.created_at', '>=', $startDate)
            ->whereNotNull('student_answers.submitted_at')
            ->selectRaw('DATE(student_answers.created_at) as date, AVG((student_answers.points_earned / assessments.max_score) * 100) as avg_score')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $scores->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M j')),
            'datasets' => [
                [
                    'label' => 'Average Quiz Score (%)',
                    'data' => $scores->pluck('avg_score')->map(fn($score) => round($score, 1)),
                    'borderColor' => '#F59E0B',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                ]
            ]
        ];
    }

    private function getStreakChartData($startDate)
    {
        $streakHistory = [];
        $currentDate = now();
        $endDate = $startDate;
        
        while ($currentDate->gte($endDate)) {
            $streakHistory[] = [
                'date' => $currentDate->format('Y-m-d'),
                'active' => LearningSession::where('user_id', $this->user->id)
                    ->whereDate('created_at', $currentDate)
                    ->exists() ? 1 : 0
            ];
            $currentDate->subDay();
        }

        $streakHistory = array_reverse($streakHistory);

        return [
            'labels' => collect($streakHistory)->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M j')),
            'datasets' => [
                [
                    'label' => 'Daily Activity',
                    'data' => collect($streakHistory)->pluck('active'),
                    'borderColor' => '#EF4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'type' => 'bar',
                ]
            ]
        ];
    }

    private function getOverviewChartData($startDate)
    {
        $dailyStats = DB::table('learning_sessions')
            ->where('user_id', $this->user->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('
                DATE(created_at) as date,
                COUNT(*) as sessions,
                SUM(duration_minutes) as total_minutes,
                COUNT(DISTINCT course_id) as courses_studied
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $dailyStats->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M j')),
            'datasets' => [
                [
                    'label' => 'Study Sessions',
                    'data' => $dailyStats->pluck('sessions'),
                    'borderColor' => '#3B82F6',
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Study Time (min)',
                    'data' => $dailyStats->pluck('total_minutes'),
                    'borderColor' => '#10B981',
                    'yAxisID' => 'y1',
                ]
            ]
        ];
    }

    private function getRecentAchievements()
    {
        return $this->user->badges()
            ->latest()
            ->limit(6)
            ->get()
            ->map(function ($badge) {
                return [
                    'id' => $badge->id,
                    'name' => $badge->badge_name,
                    'description' => $badge->badge_description,
                    'icon' => $badge->badge_icon,
                    'color' => $badge->badge_color,
                    'rarity' => $badge->rarity,
                    'earned_at' => $badge->created_at,
                    'points_reward' => $badge->points_reward,
                ];
            });
    }

    private function getLeaderboardData()
    {
        return Cache::remember('leaderboard_top_10', 300, function () {
            return GamificationData::with('user')
                ->orderBy('total_points', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($data, $index) {
                    return [
                        'rank' => $index + 1,
                        'user' => $data->user,
                        'points' => $data->total_points,
                        'level' => $data->level,
                        'streak' => $data->current_streak,
                        'badges_count' => $data->user->badges()->count(),
                        'is_current_user' => $data->user_id === $this->user->id,
                    ];
                });
        });
    }

    private function getStreakAnalytics()
    {
        $gamificationData = $this->user->getOrCreateGamificationData();
        
        return [
            'current_streak' => $gamificationData->current_streak,
            'longest_streak' => $gamificationData->longest_streak,
            'streak_goal' => $this->getNextStreakMilestone($gamificationData->current_streak),
            'days_to_goal' => $this->getNextStreakMilestone($gamificationData->current_streak) - $gamificationData->current_streak,
            'streak_percentage' => $this->calculateStreakProgress($gamificationData->current_streak),
            'recent_activity' => $this->getRecentActivityDays(14),
        ];
    }

    private function getProgressAnalytics()
    {
        $enrolledCourses = $this->user->enrollments()->with('course')->get();
        
        return [
            'courses_in_progress' => $enrolledCourses->where('progress', '<', 100)->count(),
            'courses_completed' => $enrolledCourses->where('progress', 100)->count(),
            'total_progress' => $enrolledCourses->avg('progress') ?? 0,
            'most_active_course' => $this->getMostActiveCourse(),
            'completion_rate' => $this->getWeeklyCompletionRate(),
            'study_consistency' => $this->getStudyConsistency(),
        ];
    }

    private function getUserRank()
    {
        $totalPoints = method_exists($this->user, 'getTotalPoints') 
            ? $this->user->getTotalPoints() 
            : ($this->user->gamificationData->total_points ?? 0);
        
        return GamificationData::where('total_points', '>', $totalPoints)->count() + 1;
    }

    private function getAverageQuizScore()
    {
        return DB::table('student_answers')
            ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
            ->where('student_answers.user_id', $this->user->id)
            ->whereNotNull('student_answers.submitted_at')
            ->avg(DB::raw('(student_answers.points_earned / assessments.max_score) * 100')) ?? 0;
    }

    private function getTimeframeDays()
    {
        return match($this->selectedTimeframe) {
            '24h' => 1,
            '7d' => 7,
            '30d' => 30,
            '90d' => 90,
            '1y' => 365,
            default => 7
        };
    }

    private function getNextStreakMilestone($currentStreak)
    {
        $milestones = [7, 14, 30, 60, 100, 365];
        foreach ($milestones as $milestone) {
            if ($currentStreak < $milestone) {
                return $milestone;
            }
        }
        return 500; // Ultimate goal
    }

    private function calculateStreakProgress($currentStreak)
    {
        $nextMilestone = $this->getNextStreakMilestone($currentStreak);
        $previousMilestone = 0;
        
        $milestones = [0, 7, 14, 30, 60, 100, 365];
        foreach ($milestones as $milestone) {
            if ($currentStreak >= $milestone) {
                $previousMilestone = $milestone;
            }
        }
        
        if ($nextMilestone == $previousMilestone) return 100;
        
        return round((($currentStreak - $previousMilestone) / ($nextMilestone - $previousMilestone)) * 100, 1);
    }

    private function getRecentActivityDays($days)
    {
        $activityDays = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $hasActivity = LearningSession::where('user_id', $this->user->id)
                ->whereDate('created_at', $date)
                ->exists();
            
            $activityDays[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('D'),
                'active' => $hasActivity,
                'intensity' => $hasActivity ? $this->getDayIntensity($date) : 0,
            ];
        }
        
        return $activityDays;
    }

    private function getDayIntensity($date)
    {
        $totalMinutes = LearningSession::where('user_id', $this->user->id)
            ->whereDate('created_at', $date)
            ->sum('duration_minutes');
            
        return min(100, ($totalMinutes / 120) * 100); // 120 minutes = 100% intensity
    }

    private function getMostActiveCourse()
    {
        $courseActivity = LearningSession::where('user_id', $this->user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->with('course')
            ->selectRaw('course_id, COUNT(*) as sessions, SUM(duration_minutes) as total_time')
            ->groupBy('course_id')
            ->orderBy('sessions', 'desc')
            ->first();

        return $courseActivity ? $courseActivity->course : null;
    }

    private function getWeeklyCompletionRate()
    {
        $thisWeekCompletions = $this->user->completedLessons()
            ->where('lesson_user.created_at', '>=', now()->startOfWeek())
            ->count();
            
        $lastWeekCompletions = $this->user->completedLessons()
            ->whereBetween('lesson_user.created_at', [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek()
            ])
            ->count();

        if ($lastWeekCompletions == 0) {
            return $thisWeekCompletions > 0 ? 100 : 0;
        }

        return round((($thisWeekCompletions - $lastWeekCompletions) / $lastWeekCompletions) * 100, 1);
    }

    private function getStudyConsistency()
    {
        $daysWithActivity = LearningSession::where('user_id', $this->user->id)
            ->where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date')
            ->distinct()
            ->count();
            
        return round(($daysWithActivity / 30) * 100, 1);
    }

    public function render()
    {
        return view('livewire.dashboard.learning-analytics-dashboard');
    }
}