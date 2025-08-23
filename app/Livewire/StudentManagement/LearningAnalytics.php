<?php

namespace App\Livewire\StudentManagement;

use Livewire\Component;
use App\Models\Course;
use App\Models\Assessment;
use App\Models\Lesson;
use App\Models\CourseEnrollment;
use App\Models\UserAchievement;
use App\Models\LearningSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Learning Analytics', 'description' => 'Track your learning progress and analytics', 'icon' => 'fas fa-chart-line', 'active' => 'student.learning-analytics'])]

class LearningAnalytics extends Component
{
    use WithPagination;

    public $timeRange = 'month';
    public $selectedCategory = 'all';
    public $showGoals = true;
    public $studyGoal = 30; // minutes per day
    public $weeklyGoal = 5; // lessons per week
    
    // Chart data
    public $activityData = [];
    public $progressData = [];
    public $timeSpentData = [];
    public $performanceData = [];
    public $weeklyProgressData = [];
    public $categoryDistributionData = [];
    public $achievementData = [];

    // Stats
    public $totalStats = [];
    public $weeklyStats = [];
    public $achievements = [];
    public $predictions = [];

    protected $listeners = ['refreshCharts' => 'prepareAllData'];

    public function mount()
    {
        try {
            $this->prepareAllData();
        } catch (\Exception $e) {
            \Log::error('Error in Learning Analytics mount: ' . $e->getMessage());
            // Initialize with empty data to prevent errors
            $this->initializeEmptyData();
        }
    }

    public function updatedTimeRange()
    {
        $this->prepareAllData();
    }

    public function updatedSelectedCategory()
    {
        $this->prepareAllData();
    }

    private function initializeEmptyData()
    {
        $this->activityData = ['labels' => [], 'datasets' => []];
        $this->progressData = ['labels' => [], 'datasets' => []];
        $this->timeSpentData = ['labels' => [], 'datasets' => []];
        $this->performanceData = ['labels' => [], 'datasets' => []];
        $this->weeklyProgressData = ['labels' => [], 'datasets' => []];
        $this->categoryDistributionData = ['labels' => [], 'datasets' => []];
        $this->totalStats = [
            'totalCourses' => 0,
            'completedCourses' => 0,
            'inProgressCourses' => 0,
            'totalLessons' => 0,
            'recentLessons' => 0,
            'averageScore' => 0,
            'totalStudyTime' => 0,
            'activeStreak' => 0,
            'weeklyGoalProgress' => 0,
            'monthlyGoalProgress' => 0,
        ];
        $this->achievements = [];
        $this->predictions = [];
    }

    public function prepareAllData()
    {
        try {
            $user = auth()->user();
            if (!$user) {
                $this->initializeEmptyData();
                return;
            }

            $cacheKey = "learning_analytics_{$user->id}_{$this->timeRange}_{$this->selectedCategory}";
            
            $data = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($user) {
                return [
                    'activity' => $this->getActivityData($user),
                    'progress' => $this->getProgressData($user),
                    'timeSpent' => $this->getTimeSpentData($user),
                    'performance' => $this->getPerformanceData($user),
                    'weeklyProgress' => $this->getWeeklyProgressData($user),
                    'categoryDistribution' => $this->getCategoryDistributionData($user),
                    'stats' => $this->calculateStats($user),
                    'achievements' => $this->getAchievements($user),
                    'predictions' => $this->getPredictions($user),
                ];
            });

            $this->activityData = $data['activity'];
            $this->progressData = $data['progress'];
            $this->timeSpentData = $data['timeSpent'];
            $this->performanceData = $data['performance'];
            $this->weeklyProgressData = $data['weeklyProgress'];
            $this->categoryDistributionData = $data['categoryDistribution'];
            $this->totalStats = $data['stats'];
            $this->achievements = $data['achievements'];
            $this->predictions = $data['predictions'];
            
        } catch (\Exception $e) {
            \Log::error('Error preparing analytics data: ' . $e->getMessage());
            $this->initializeEmptyData();
        }
    }

    protected function getDateRange()
    {
        return match($this->timeRange) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'quarter' => now()->subMonths(3),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };
    }

    protected function getActivityData($user)
    {
        try {
            $dateRange = $this->getDateRange();
            $periodFormat = match($this->timeRange) {
                'week' => '%Y-%m-%d',
                'month' => '%Y-%m-%d',
                'quarter' => '%Y-%U',
                'year' => '%Y-%m',
                default => '%Y-%m-%d',
            };

            // Check if lesson_user table exists and has the required columns
            if (!$this->tableExists('lesson_user')) {
                return $this->getEmptyChartData();
            }

            $activity = DB::table('lesson_user')
                ->select(DB::raw("DATE_FORMAT(completed_at, '{$periodFormat}') as period, COUNT(*) as lessons_completed"))
                ->where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->where('completed_at', '>=', $dateRange)
                ->groupBy('period')
                ->orderBy('period')
                ->get();

            // Get study time data with fallback for missing columns
            $studyTimeQuery = DB::table('lesson_user')
                ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
                ->select(DB::raw("DATE_FORMAT(lesson_user.completed_at, '{$periodFormat}') as period"))
                ->where('lesson_user.user_id', $user->id)
                ->whereNotNull('lesson_user.completed_at')
                ->where('lesson_user.completed_at', '>=', $dateRange);

            // Check if time_spent_minutes column exists
            if ($this->columnExists('lesson_user', 'time_spent_minutes')) {
                $studyTimeQuery->selectRaw("SUM(COALESCE(lesson_user.time_spent_minutes, COALESCE(lessons.duration_minutes, 15))) as minutes");
            } else {
                $studyTimeQuery->selectRaw("SUM(COALESCE(lessons.duration_minutes, 15)) as minutes");
            }

            $studyTime = $studyTimeQuery
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->keyBy('period');

            $activityByPeriod = $activity->keyBy('period');
            $allPeriods = $this->generatePeriodRange($dateRange);

            return [
                'labels' => $allPeriods,
                'datasets' => [
                    [
                        'label' => 'Lessons Completed',
                        'type' => 'bar',
                        'data' => collect($allPeriods)->map(fn($period) => 
                            $activityByPeriod->get($period)?->lessons_completed ?? 0
                        )->toArray(),
                        'backgroundColor' => 'rgba(59, 130, 246, 0.6)',
                        'borderColor' => 'rgba(59, 130, 246, 1)',
                        'borderWidth' => 2,
                        'yAxisID' => 'y'
                    ],
                    [
                        'label' => 'Study Time (hours)',
                        'type' => 'line',
                        'data' => collect($allPeriods)->map(fn($period) => 
                            round(($studyTime->get($period)?->minutes ?? 0) / 60, 1)
                        )->toArray(),
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'borderColor' => 'rgba(16, 185, 129, 1)',
                        'borderWidth' => 3,
                        'fill' => true,
                        'tension' => 0.4,
                        'yAxisID' => 'y1'
                    ]
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getActivityData: ' . $e->getMessage());
            return $this->getEmptyChartData();
        }
    }

    protected function getProgressData($user)
    {
        try {
            if (!$this->tableExists('course_user') || !$this->tableExists('courses')) {
                return $this->getEmptyChartData();
            }

            $query = DB::table('course_user')
                ->join('courses', 'course_user.course_id', '=', 'courses.id')
                ->select('courses.title', 'courses.difficulty_level', 'courses.category_id')
                ->where('course_user.user_id', $user->id);

            // Check if progress column exists
            if ($this->columnExists('course_user', 'progress')) {
                $query->addSelect('course_user.progress');
                $query->orderByDesc('course_user.progress');
            } else {
                $query->addSelect(DB::raw('0 as progress'));
            }

            if ($this->selectedCategory !== 'all') {
                $query->where('courses.category_id', $this->selectedCategory);
            }

            $courses = $query->limit(10)->get();

            return [
                'labels' => $courses->pluck('title')->map(fn($title) => 
                    strlen($title) > 20 ? substr($title, 0, 20) . '...' : $title
                )->toArray(),
                'datasets' => [[
                    'label' => 'Progress %',
                    'data' => $courses->pluck('progress')->toArray(),
                    'backgroundColor' => $courses->map(function($course) {
                        return match($course->difficulty_level ?? 'beginner') {
                            'beginner' => 'rgba(34, 197, 94, 0.7)',
                            'intermediate' => 'rgba(245, 158, 11, 0.7)',
                            'advanced' => 'rgba(239, 68, 68, 0.7)',
                            default => 'rgba(59, 130, 246, 0.7)'
                        };
                    })->toArray(),
                    'borderColor' => 'rgba(255, 255, 255, 0.8)',
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                ]]
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getProgressData: ' . $e->getMessage());
            return $this->getEmptyChartData();
        }
    }

    protected function getPerformanceData($user)
    {
        try {
            if (!$this->tableExists('student_answers') || !$this->tableExists('assessments')) {
                return $this->getEmptyChartData();
            }

            $assessmentResults = DB::table('student_answers')
                ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
                ->select(
                    'assessments.title',
                    DB::raw('AVG(student_answers.points_earned / assessments.max_score * 100) as avg_score'),
                    DB::raw('COUNT(DISTINCT student_answers.attempt_number) as attempts'),
                    'assessments.type'
                )
                ->where('student_answers.user_id', $user->id)
                ->whereNotNull('student_answers.submitted_at')
                ->groupBy('assessments.id', 'assessments.title', 'assessments.max_score', 'assessments.type')
                ->orderByDesc('avg_score')
                ->limit(8)
                ->get();

            return [
                'labels' => $assessmentResults->pluck('title')->map(fn($title) => 
                    strlen($title) > 15 ? substr($title, 0, 15) . '...' : $title
                )->toArray(),
                'datasets' => [
                    [
                        'label' => 'Average Score %',
                        'data' => $assessmentResults->pluck('avg_score')->map(fn($score) => round($score, 1))->toArray(),
                        'backgroundColor' => $assessmentResults->map(function($result) {
                            $score = $result->avg_score;
                            if ($score >= 90) return 'rgba(34, 197, 94, 0.8)';
                            if ($score >= 80) return 'rgba(59, 130, 246, 0.8)';
                            if ($score >= 70) return 'rgba(245, 158, 11, 0.8)';
                            return 'rgba(239, 68, 68, 0.8)';
                        })->toArray(),
                        'borderColor' => 'rgba(255, 255, 255, 0.9)',
                        'borderWidth' => 2
                    ]
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getPerformanceData: ' . $e->getMessage());
            return $this->getEmptyChartData();
        }
    }

    protected function getWeeklyProgressData($user)
    {
        try {
            $weeks = collect(range(6, 0))->map(function($weeksAgo) use ($user) {
                $startDate = now()->subWeeks($weeksAgo)->startOfWeek();
                $endDate = $startDate->copy()->endOfWeek();
                
                $lessonsCompleted = 0;
                $studyTime = 0;

                if ($this->tableExists('lesson_user')) {
                    $lessonsCompleted = DB::table('lesson_user')
                        ->where('user_id', $user->id)
                        ->whereNotNull('completed_at')
                        ->whereBetween('completed_at', [$startDate, $endDate])
                        ->count();
                        
                    if ($this->tableExists('lessons')) {
                        $studyTimeQuery = DB::table('lesson_user')
                            ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
                            ->where('lesson_user.user_id', $user->id)
                            ->whereNotNull('lesson_user.completed_at')
                            ->whereBetween('lesson_user.completed_at', [$startDate, $endDate]);

                        if ($this->columnExists('lesson_user', 'time_spent_minutes')) {
                            $studyTime = $studyTimeQuery->sum(DB::raw('COALESCE(lesson_user.time_spent_minutes, COALESCE(lessons.duration_minutes, 15))'));
                        } else {
                            $studyTime = $studyTimeQuery->sum(DB::raw('COALESCE(lessons.duration_minutes, 15)'));
                        }
                    }
                }

                return [
                    'week' => $startDate->format('M d'),
                    'lessons' => $lessonsCompleted,
                    'minutes' => $studyTime,
                    'goal_met' => $lessonsCompleted >= $this->weeklyGoal
                ];
            });

            return [
                'labels' => $weeks->pluck('week')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Lessons Completed',
                        'data' => $weeks->pluck('lessons')->toArray(),
                        'backgroundColor' => $weeks->map(fn($week) => 
                            $week['goal_met'] ? 'rgba(34, 197, 94, 0.7)' : 'rgba(156, 163, 175, 0.7)'
                        )->toArray(),
                        'borderColor' => 'rgba(59, 130, 246, 1)',
                        'borderWidth' => 2
                    ],
                    [
                        'type' => 'line',
                        'label' => 'Weekly Goal',
                        'data' => array_fill(0, 7, $this->weeklyGoal),
                        'borderColor' => 'rgba(239, 68, 68, 1)',
                        'borderDash' => [5, 5],
                        'borderWidth' => 2,
                        'pointRadius' => 0,
                        'fill' => false
                    ]
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getWeeklyProgressData: ' . $e->getMessage());
            return $this->getEmptyChartData();
        }
    }

    protected function getCategoryDistributionData($user)
    {
        try {
            if (!$this->tableExists('course_user') || !$this->tableExists('courses') || !$this->tableExists('course_categories')) {
                return $this->getEmptyChartData();
            }

            $query = DB::table('course_user')
                ->join('courses', 'course_user.course_id', '=', 'courses.id')
                ->join('course_categories', 'courses.category_id', '=', 'course_categories.id')
                ->select(
                    'course_categories.name',
                    DB::raw('COUNT(*) as course_count')
                )
                ->where('course_user.user_id', $user->id)
                ->groupBy('course_categories.id', 'course_categories.name')
                ->orderByDesc('course_count');

            // Add progress and time calculations if columns exist
            if ($this->columnExists('course_user', 'progress')) {
                $query->addSelect(DB::raw('AVG(course_user.progress) as avg_progress'));
            } else {
                $query->addSelect(DB::raw('0 as avg_progress'));
            }

            if ($this->columnExists('courses', 'estimated_duration_minutes')) {
                if ($this->columnExists('course_user', 'progress')) {
                    $query->addSelect(DB::raw('SUM(courses.estimated_duration_minutes * course_user.progress / 100) as time_spent'));
                } else {
                    $query->addSelect(DB::raw('SUM(courses.estimated_duration_minutes * 0.1) as time_spent'));
                }
            } else {
                $query->addSelect(DB::raw('0 as time_spent'));
            }

            $categoryData = $query->get();

            return [
                'labels' => $categoryData->pluck('name')->toArray(),
                'datasets' => [[
                    'label' => 'Time Spent (hours)',
                    'data' => $categoryData->pluck('time_spent')->map(fn($minutes) => round(($minutes ?? 0) / 60, 1))->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                    ],
                    'borderWidth' => 0,
                    'hoverBorderWidth' => 3,
                    'hoverBorderColor' => 'rgba(255, 255, 255, 1)'
                ]]
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getCategoryDistributionData: ' . $e->getMessage());
            return $this->getEmptyChartData();
        }
    }

    protected function calculateStats($user)
    {
        try {
            $dateRange = $this->getDateRange();
            
            $stats = [
                'totalCourses' => 0,
                'completedCourses' => 0,
                'inProgressCourses' => 0,
                'totalLessons' => 0,
                'recentLessons' => 0,
                'averageScore' => 0,
                'totalStudyTime' => 0,
                'activeStreak' => 0,
                'weeklyGoalProgress' => 0,
                'monthlyGoalProgress' => 0,
            ];

            // Course stats
            if ($this->tableExists('course_user')) {
                $stats['totalCourses'] = DB::table('course_user')->where('user_id', $user->id)->count();
                
                if ($this->columnExists('course_user', 'progress')) {
                    $stats['completedCourses'] = DB::table('course_user')
                        ->where('user_id', $user->id)
                        ->where('progress', 100)
                        ->count();
                    
                    $stats['inProgressCourses'] = DB::table('course_user')
                        ->where('user_id', $user->id)
                        ->where('progress', '>', 0)
                        ->where('progress', '<', 100)
                        ->count();
                }
            }

            // Lesson stats
            if ($this->tableExists('lesson_user')) {
                $stats['totalLessons'] = DB::table('lesson_user')
                    ->where('user_id', $user->id)
                    ->whereNotNull('completed_at')
                    ->count();
                
                $stats['recentLessons'] = DB::table('lesson_user')
                    ->where('user_id', $user->id)
                    ->whereNotNull('completed_at')
                    ->where('completed_at', '>=', $dateRange)
                    ->count();
            }

            // Assessment stats
            $stats['averageScore'] = $this->getAverageAssessmentScore($user);
            
            // Study time
            $stats['totalStudyTime'] = $this->getTotalStudyTime($user);
            
            // Streak calculation
            if (class_exists(\App\Models\LearningSession::class)) {
                try {
                    $stats['activeStreak'] = \App\Models\LearningSession::getStudyStreak($user->id);
                } catch (\Exception $e) {
                    $stats['activeStreak'] = $this->calculateStreakFallback($user);
                }
            } else {
                $stats['activeStreak'] = $this->calculateStreakFallback($user);
            }
            
            // Goal progress
            $stats['weeklyGoalProgress'] = $this->getWeeklyGoalProgress($user);
            $stats['monthlyGoalProgress'] = $this->getMonthlyGoalProgress($user);

            return $stats;
        } catch (\Exception $e) {
            \Log::error('Error in calculateStats: ' . $e->getMessage());
            return $this->initializeEmptyData()['totalStats'] ?? [];
        }
    }

    protected function calculateStreakFallback($user)
    {
        try {
            if (!$this->tableExists('lesson_user')) {
                return 0;
            }

            $streak = 0;
            $currentDate = now()->startOfDay();
            
            // Check if user completed any lessons today
            $hasActivityToday = DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->whereDate('completed_at', $currentDate)
                ->exists();
                
            if ($hasActivityToday) {
                $streak = 1;
                
                // Check previous days
                for ($i = 1; $i < 365; $i++) {
                    $checkDate = $currentDate->copy()->subDays($i);
                    
                    $hasActivity = DB::table('lesson_user')
                        ->where('user_id', $user->id)
                        ->whereNotNull('completed_at')
                        ->whereDate('completed_at', $checkDate)
                        ->exists();
                        
                    if ($hasActivity) {
                        $streak++;
                    } else {
                        break;
                    }
                }
            }
            
            return $streak;
        } catch (\Exception $e) {
            \Log::error('Error in calculateStreakFallback: ' . $e->getMessage());
            return 0;
        }
    }

    protected function getAchievements($user)
    {
        try {
            // Try to use UserAchievement model if it exists
            if (class_exists(\App\Models\UserAchievement::class)) {
                try {
                    return \App\Models\UserAchievement::getRecentAchievements($user->id)->toArray();
                } catch (\Exception $e) {
                    \Log::warning('UserAchievement model exists but failed: ' . $e->getMessage());
                }
            }

            // Fallback to manual achievement calculation
            return $this->calculateAchievementsFallback($user);
        } catch (\Exception $e) {
            \Log::error('Error in getAchievements: ' . $e->getMessage());
            return [];
        }
    }

    protected function calculateAchievementsFallback($user)
    {
        $achievements = [];
        
        try {
            // Streak achievements
            $streak = $this->calculateStreakFallback($user);
            if ($streak >= 7) $achievements[] = ['title' => 'Week Warrior', 'icon' => '🔥', 'description' => '7+ day streak'];
            if ($streak >= 30) $achievements[] = ['title' => 'Month Master', 'icon' => '🏆', 'description' => '30+ day streak'];
            
            // Course completion achievements
            if ($this->tableExists('course_user') && $this->columnExists('course_user', 'progress')) {
                $completedCourses = DB::table('course_user')
                    ->where('user_id', $user->id)
                    ->where('progress', 100)
                    ->count();
                    
                if ($completedCourses >= 1) $achievements[] = ['title' => 'First Steps', 'icon' => '🎯', 'description' => 'Completed first course'];
                if ($completedCourses >= 5) $achievements[] = ['title' => 'Knowledge Seeker', 'icon' => '📚', 'description' => 'Completed 5 courses'];
                if ($completedCourses >= 10) $achievements[] = ['title' => 'Learning Expert', 'icon' => '🌟', 'description' => 'Completed 10 courses'];
            }
            
            // Assessment achievements
            $avgScore = $this->getAverageAssessmentScore($user);
            if ($avgScore >= 80) $achievements[] = ['title' => 'High Achiever', 'icon' => '💯', 'description' => '80%+ average score'];
            if ($avgScore >= 95) $achievements[] = ['title' => 'Perfectionist', 'icon' => '👑', 'description' => '95%+ average score'];
            
        } catch (\Exception $e) {
            \Log::error('Error calculating fallback achievements: ' . $e->getMessage());
        }
        
        return $achievements;
    }

    protected function getPredictions($user)
    {
        try {
            $recentActivity = 0;
            if ($this->tableExists('lesson_user')) {
                $recentActivity = DB::table('lesson_user')
                    ->where('user_id', $user->id)
                    ->whereNotNull('completed_at')
                    ->where('completed_at', '>=', now()->subWeeks(4))
                    ->count();
            }
            
            $weeklyAverage = $recentActivity / 4;
            $projectedLessonsNextMonth = $weeklyAverage * 4;
            
            $averageProgress = 0;
            if ($this->tableExists('course_user') && $this->columnExists('course_user', 'progress')) {
                $averageProgress = DB::table('course_user')
                    ->where('user_id', $user->id)
                    ->where('progress', '>', 0)
                    ->where('progress', '<', 100)
                    ->avg('progress') ?? 0;
            }
            
            $estimatedCompletionWeeks = $averageProgress > 0 ? ceil((100 - $averageProgress) / ($weeklyAverage * 2)) : null;
            
            return [
                'projectedLessonsNextMonth' => round($projectedLessonsNextMonth),
                'estimatedCompletionWeeks' => $estimatedCompletionWeeks,
                'recommendedStudyTime' => $this->getRecommendedStudyTime($weeklyAverage),
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getPredictions: ' . $e->getMessage());
            return [
                'projectedLessonsNextMonth' => 0,
                'estimatedCompletionWeeks' => null,
                'recommendedStudyTime' => '30 minutes daily',
            ];
        }
    }

    // Helper methods
    protected function tableExists($tableName)
    {
        try {
            return DB::getSchemaBuilder()->hasTable($tableName);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function columnExists($tableName, $columnName)
    {
        try {
            return DB::getSchemaBuilder()->hasColumn($tableName, $columnName);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function getEmptyChartData()
    {
        return [
            'labels' => [],
            'datasets' => []
        ];
    }

    protected function generatePeriodRange($startDate)
    {
        try {
            $periods = [];
            $current = $startDate->copy();
            $end = now();
            
            $format = match($this->timeRange) {
                'week' => 'Y-m-d',
                'month' => 'Y-m-d',
                'quarter' => 'Y-\WW',
                'year' => 'Y-m',
                default => 'Y-m-d',
            };
            
            $interval = match($this->timeRange) {
                'week' => 'day',
                'month' => 'day',
                'quarter' => 'week',
                'year' => 'month',
                default => 'day',
            };
            
            while ($current <= $end) {
                $periods[] = $current->format($format);
                $current->add(1, $interval);
            }
            
            return $periods;
        } catch (\Exception $e) {
            \Log::error('Error generating period range: ' . $e->getMessage());
            return [];
        }
    }

    protected function getAverageAssessmentScore($user)
    {
        try {
            if (!$this->tableExists('student_answers') || !$this->tableExists('assessments')) {
                return 0;
            }

            return DB::table('student_answers')
                ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
                ->where('student_answers.user_id', $user->id)
                ->whereNotNull('student_answers.submitted_at')
                ->avg(DB::raw('student_answers.points_earned / assessments.max_score * 100')) ?? 0;
        } catch (\Exception $e) {
            \Log::error('Error getting average assessment score: ' . $e->getMessage());
            return 0;
        }
    }

    protected function getTotalStudyTime($user)
    {
        try {
            if (!$this->tableExists('lesson_user') || !$this->tableExists('lessons')) {
                return 0;
            }

            $query = DB::table('lesson_user')
                ->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
                ->where('lesson_user.user_id', $user->id)
                ->whereNotNull('lesson_user.completed_at');

            if ($this->columnExists('lesson_user', 'time_spent_minutes')) {
                return $query->sum(DB::raw('COALESCE(lesson_user.time_spent_minutes, COALESCE(lessons.duration_minutes, 15))')) ?? 0;
            } else {
                return $query->sum(DB::raw('COALESCE(lessons.duration_minutes, 15)')) ?? 0;
            }
        } catch (\Exception $e) {
            \Log::error('Error getting total study time: ' . $e->getMessage());
            return 0;
        }
    }

    protected function getWeeklyGoalProgress($user)
    {
        try {
            if (!$this->tableExists('lesson_user')) {
                return 0;
            }

            $thisWeek = DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->where('completed_at', '>=', now()->startOfWeek())
                ->count();
                
            return min(100, ($thisWeek / $this->weeklyGoal) * 100);
        } catch (\Exception $e) {
            \Log::error('Error getting weekly goal progress: ' . $e->getMessage());
            return 0;
        }
    }

    protected function getMonthlyGoalProgress($user)
    {
        try {
            if (!$this->tableExists('lesson_user')) {
                return 0;
            }

            $thisMonth = DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->where('completed_at', '>=', now()->startOfMonth())
                ->count();
                
            $monthlyGoal = $this->weeklyGoal * 4; // Approximate monthly goal
            return min(100, ($thisMonth / $monthlyGoal) * 100);
        } catch (\Exception $e) {
            \Log::error('Error getting monthly goal progress: ' . $e->getMessage());
            return 0;
        }
    }

    protected function getRecommendedStudyTime($weeklyAverage)
    {
        if ($weeklyAverage < 3) return '30 minutes daily';
        if ($weeklyAverage < 7) return '45 minutes daily';
        return '1 hour daily';
    }

    public function getTimeSpentData($user = null)
    {
        try {
            $user = $user ?? auth()->user();
            if (!$user || !$this->tableExists('lesson_user')) {
                return [
                    'label' => 'Time Spent',
                    'value' => 0,
                    'unit'  => 'minutes',
                ];
            }

            $query = DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->whereNotNull('completed_at');

            if ($this->columnExists('lesson_user', 'time_spent_minutes')) {
                $query->selectRaw('SUM(time_spent_minutes) as total_time');
            } else if ($this->tableExists('lessons')) {
                $query->join('lessons', 'lesson_user.lesson_id', '=', 'lessons.id')
                     ->selectRaw('SUM(COALESCE(lessons.duration_minutes, 15)) as total_time');
            } else {
                $query->selectRaw('COUNT(*) * 15 as total_time'); // Fallback: 15 minutes per lesson
            }

            if ($this->timeRange === 'week') {
                $query->whereBetween('lesson_user.completed_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($this->timeRange === 'month') {
                $query->whereMonth('lesson_user.completed_at', now()->month);
            }

            $total = $query->value('total_time') ?? 0;

            return [
                'label' => 'Time Spent',
                'value' => $total,
                'unit'  => 'minutes',
            ];
        } catch (\Exception $e) {
            \Log::error('Error in getTimeSpentData: ' . $e->getMessage());
            return [
                'label' => 'Time Spent',
                'value' => 0,
                'unit'  => 'minutes',
            ];
        }
    }
    
    public function render()
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return view('livewire.student-management.learning-analytics', [
                    'categories' => collect(),
                    'recentCourses' => collect(),
                ]);
            }
            
            // Get categories for filter - with error handling
            $categories = collect();
            if ($this->tableExists('course_categories') && $this->tableExists('courses') && $this->tableExists('course_user')) {
                try {
                    $categories = DB::table('course_categories')
                        ->join('courses', 'course_categories.id', '=', 'courses.category_id')
                        ->join('course_user', 'courses.id', '=', 'course_user.course_id')
                        ->where('course_user.user_id', $user->id)
                        ->select('course_categories.id', 'course_categories.name')
                        ->distinct()
                        ->get();
                } catch (\Exception $e) {
                    \Log::error('Error fetching categories: ' . $e->getMessage());
                }
            }

            // Get recent courses with progress - with error handling
            $recentCourses = collect();
            if ($this->tableExists('course_user') && $this->tableExists('courses')) {
                try {
                    $query = DB::table('course_user')
                        ->join('courses', 'course_user.course_id', '=', 'courses.id')
                        ->where('course_user.user_id', $user->id)
                        ->select('courses.*')
                        ->orderBy('course_user.updated_at', 'desc')
                        ->limit(5);

                    // Add pivot data if columns exist
                    if ($this->columnExists('course_user', 'progress')) {
                        $query->addSelect('course_user.progress as pivot_progress');
                    }
                    if ($this->columnExists('course_user', 'updated_at')) {
                        $query->addSelect('course_user.updated_at as pivot_updated_at');
                    }

                    $recentCourses = $query->get()->map(function ($course) {
                        // Convert to object with pivot data
                        $courseObj = (object) [
                            'id' => $course->id,
                            'title' => $course->title,
                            'slug' => $course->slug ?? '',
                            'difficulty_level' => $course->difficulty_level ?? 'beginner',
                            'pivot' => (object) [
                                'progress' => $course->pivot_progress ?? 0,
                                'updated_at' => $course->pivot_updated_at ? 
                                    \Carbon\Carbon::parse($course->pivot_updated_at) : null,
                            ]
                        ];
                        return $courseObj;
                    });
                } catch (\Exception $e) {
                    \Log::error('Error fetching recent courses: ' . $e->getMessage());
                }
            }

            return view('livewire.student-management.learning-analytics', [
                'categories' => $categories,
                'recentCourses' => $recentCourses,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in Learning Analytics render: ' . $e->getMessage());
            
            return view('livewire.student-management.learning-analytics', [
                'categories' => collect(),
                'recentCourses' => collect(),
            ]);
        }
    }
}