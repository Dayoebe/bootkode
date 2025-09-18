<?php

namespace App\Livewire\Dashboard;

use App\Models\Course;
use App\Models\User;
use App\Models\Announcement;
use App\Models\Certificate;
use App\Models\SupportTicket;
use App\Models\UserAchievement;
use App\Models\SystemStatus;
use App\Models\CourseEnrollment;
use App\Models\StudentAnswer;
use App\Models\Assessment;
use App\Models\CbtResult;
use App\Models\JobApplication;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.dashboard', ['title' => 'Student Dashboard'])]
class StudentDashboard extends Component
{
    public $selectedTimeframe = '7days';
    public $showWidgets = [
        'quick_stats' => true,
        'learning_progress' => true,
        'recent_courses' => true,
        'achievements' => true,
        'upcoming_tasks' => true,
        'performance_analytics' => true,
        'announcements' => true,
        'career_tools' => true,
    ];

    protected $listeners = [
        'refreshDashboard' => 'loadAllData',
        'timeframeChanged' => 'updateTimeframe',
    ];

    public function mount()
    {
        $user = Auth::user();
        if (!$user->isStudent()) {
            redirect()->route($user->getDashboardRouteName());
        }
    }

    public function updateTimeframe($timeframe)
    {
        $this->selectedTimeframe = $timeframe;
    }

    #[Computed]
    public function quickStats()
    {
        $user = Auth::user();
        $timeframe = $this->getTimeframeQuery();
        
        return [
            'enrolled_courses' => $user->enrollments()->count(),
            'completed_courses' => $user->enrollments()->where('is_completed', true)->count(),
            'certificates_earned' => $user->certificates()->approved()->count(),
            'study_streak' => $this->calculateStudyStreak($user),
            'lessons_completed' => $this->getCompletedLessonsCount($user),
            'average_score' => $this->getAverageAssessmentScore($user),
            'total_study_hours' => $this->getTotalStudyTime($user),
            'current_level' => $this->getUserLevel($user),
        ];
    }

    #[Computed]
    public function learningProgress()
    {
        $user = Auth::user();
        $enrollments = $user->enrollments()
            ->with(['course' => function($query) {
                $query->select('id', 'title', 'thumbnail', 'estimated_duration_minutes');
            }])
            ->where('progress_percentage', '<', 100)
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        return $enrollments->map(function($enrollment) {
            return [
                'id' => $enrollment->course->id,
                'title' => $enrollment->course->title,
                'thumbnail' => $enrollment->course->thumbnail,
                'progress' => $enrollment->progress_percentage ?? 0,
                'last_accessed' => $enrollment->updated_at,
                'estimated_remaining' => $this->calculateRemainingTime($enrollment),
                'next_lesson' => $this->getNextLesson($enrollment->course, $enrollment->user_id),
            ];
        });
    }

    #[Computed]
    public function recentAchievements()
    {
        return Auth::user()->badges()
            ->latest()
            ->take(5)
            ->get()
            ->map(function($badge) {
                return [
                    'name' => $badge->badge_name,
                    'description' => $badge->badge_description,
                    'icon' => $badge->badge_icon,
                    'rarity' => $badge->rarity,
                    'earned_at' => $badge->created_at,
                ];
            });
    }

    #[Computed]
    public function upcomingTasks()
    {
        $user = Auth::user();
        $tasks = collect();

        // Upcoming assessments
        $upcomingAssessments = Assessment::whereHas('course.enrollments', function($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', false);
            })
            ->whereDoesntHave('studentAnswers', function($query) use ($user) {
                $query->where('user_id', $user->id)->whereNotNull('submitted_at');
            })
            ->where('due_date', '>', now())
            ->orderBy('due_date')
            ->take(5)
            ->get();

        foreach($upcomingAssessments as $assessment) {
            $tasks->push([
                'type' => 'assessment',
                'title' => $assessment->title,
                'course' => $assessment->course->title,
                'due_date' => $assessment->due_date,
                'priority' => $this->calculateTaskPriority($assessment->due_date),
                'url' => route('course.view', ['course' => $assessment->course->slug]),
            ]);
        }

        // Continue course lessons
        foreach($this->learningProgress as $progress) {
            if($progress['next_lesson']) {
                $tasks->push([
                    'type' => 'lesson',
                    'title' => 'Continue: ' . $progress['next_lesson']['title'],
                    'course' => $progress['title'],
                    'due_date' => now()->addDays(1), // Suggested completion
                    'priority' => 'medium',
                    'url' => route('course.view', ['course' => $progress['id']]),
                ]);
            }
        }

        return $tasks->sortBy('due_date')->take(8);
    }

    #[Computed]
    public function performanceAnalytics()
    {
        $user = Auth::user();
        $days = $this->getTimeframeDays();
        
        // Daily study activity
        $studyActivity = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $activity = StudentAnswer::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->count();
            
            $studyActivity[] = [
                'date' => $date->format('M j'),
                'activity' => $activity,
            ];
        }

        // Subject performance
        $subjectPerformance = Assessment::whereHas('studentAnswers', function($query) use ($user) {
                $query->where('user_id', $user->id)->whereNotNull('submitted_at');
            })
            ->with(['course', 'studentAnswers' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get()
            ->groupBy('course.category.name')
            ->map(function($assessments, $category) {
                $totalScore = 0;
                $maxScore = 0;
                $count = 0;

                foreach($assessments as $assessment) {
                    foreach($assessment->studentAnswers as $answer) {
                        $totalScore += $answer->points_earned ?? 0;
                        $maxScore += $assessment->max_score ?? 100;
                        $count++;
                    }
                }

                return [
                    'category' => $category ?? 'General',
                    'average_score' => $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 1) : 0,
                    'assessments_taken' => $count,
                ];
            })
            ->values();

        return [
            'study_activity' => $studyActivity,
            'subject_performance' => $subjectPerformance,
            'weekly_goals' => $this->getWeeklyGoals($user),
            'improvement_areas' => $this->getImprovementAreas($user),
        ];
    }

    #[Computed]
    public function recentAnnouncements()
    {
        return Announcement::where('status', 'published')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(3)
            ->get()
            ->map(function($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => \Str::limit($announcement->content, 120),
                    'published_at' => $announcement->published_at,
                    'course_title' => $announcement->course->title ?? null,
                ];
            });
    }

    #[Computed]
    public function careerTools()
    {
        $user = Auth::user();
        
        return [
            'portfolio_completion' => $this->getPortfolioCompletion($user),
            'resume_status' => $this->getResumeStatus($user),
            'job_applications' => JobApplication::where('user_id', $user->id)
                ->latest()
                ->take(3)
                ->get(),
            'interview_prep' => $this->getInterviewPrepStatus($user),
        ];
    }

    #[Computed]
    public function systemHealth()
    {
        $status = SystemStatus::latest()->first();
        return [
            'status' => $status->status ?? 'operational',
            'message' => $status->message ?? 'All systems operational',
            'updated_at' => $status ? $status->updated_at->diffForHumans() : 'Just now',
        ];
    }

    // Helper Methods
    private function getTimeframeQuery()
    {
        return match ($this->selectedTimeframe) {
            '24hours' => now()->subHours(24),
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            default => now()->subDays(7),
        };
    }

    private function getTimeframeDays()
    {
        return match ($this->selectedTimeframe) {
            '24hours' => 1,
            '7days' => 7,
            '30days' => 30,
            default => 7,
        };
    }

    private function calculateStudyStreak(User $user)
    {
        $streak = 0;
        $currentDate = now()->startOfDay();
        
        while (true) {
            $hasActivity = StudentAnswer::where('user_id', $user->id)
                ->whereDate('created_at', $currentDate)
                ->exists();
            
            if ($hasActivity) {
                $streak++;
                $currentDate->subDay();
            } else {
                break;
            }
        }
        
        return $streak;
    }

    private function getCompletedLessonsCount(User $user)
    {
        return $user->completedLessons()->count();
    }

    private function getAverageAssessmentScore(User $user)
    {
        return StudentAnswer::where('user_id', $user->id)
            ->whereNotNull('points_earned')
            ->whereNotNull('submitted_at')
            ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
            ->selectRaw('AVG((student_answers.points_earned / assessments.max_score) * 100) as avg_score')
            ->value('avg_score') ?? 0;
    }

    private function getTotalStudyTime(User $user)
    {
        // This would ideally come from a learning_sessions table
        // For now, estimate based on completed lessons and assessments
        $completedLessons = $this->getCompletedLessonsCount($user);
        $estimatedHours = $completedLessons * 0.5; // Assume 30 minutes per lesson
        
        return round($estimatedHours, 1);
    }

    private function getUserLevel(User $user)
    {
        $gamificationData = $user->getOrCreateGamificationData();
        return $gamificationData->level ?? 1;
    }

    private function calculateRemainingTime($enrollment)
    {
        $course = $enrollment->course;
        if (!$course->estimated_duration_minutes) return null;
        
        $remainingPercentage = 100 - ($enrollment->progress_percentage ?? 0);
        $remainingMinutes = ($course->estimated_duration_minutes * $remainingPercentage) / 100;
        
        return round($remainingMinutes / 60, 1); // Convert to hours
    }

    private function getNextLesson($course, $userId)
    {
        // Get completed lessons for this course
        $completedLessons = DB::table('lesson_user')
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->pluck('lesson_id');

        // Find next uncompleted lesson
        $nextLesson = $course->allLessons()
            ->whereNotIn('id', $completedLessons)
            ->first();

        return $nextLesson ? [
            'id' => $nextLesson->id,
            'title' => $nextLesson->title,
            'section' => $nextLesson->section->title ?? 'General',
        ] : null;
    }

    private function calculateTaskPriority($dueDate)
    {
        if (!$dueDate) return 'low';
        
        $daysUntilDue = now()->diffInDays($dueDate, false);
        
        if ($daysUntilDue <= 1) return 'high';
        if ($daysUntilDue <= 3) return 'medium';
        return 'low';
    }

    private function getWeeklyGoals(User $user)
    {
        // This could be stored in user preferences or calculated
        return [
            'lessons_target' => 5,
            'lessons_completed' => $this->getCompletedLessonsThisWeek($user),
            'study_hours_target' => 10,
            'study_hours_completed' => $this->getStudyHoursThisWeek($user),
        ];
    }

    private function getCompletedLessonsThisWeek(User $user)
    {
        return DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('completed_at', '>=', now()->startOfWeek())
            ->count();
    }

    private function getStudyHoursThisWeek(User $user)
    {
        // Estimate based on activity this week
        $activeDays = StudentAnswer::where('user_id', $user->id)
            ->where('created_at', '>=', now()->startOfWeek())
            ->distinct('created_at')
            ->count(DB::raw('DATE(created_at)'));
        
        return $activeDays * 1.5; // Estimate 1.5 hours per active day
    }

    private function getImprovementAreas(User $user)
    {
        // Analyze performance to suggest improvement areas
        $lowScoreAssessments = Assessment::whereHas('studentAnswers', function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->whereRaw('(points_earned / (SELECT max_score FROM assessments WHERE id = student_answers.assessment_id)) * 100 < 70');
            })
            ->with('course')
            ->take(3)
            ->get();

        return $lowScoreAssessments->map(function($assessment) {
            return [
                'area' => $assessment->course->category->name ?? 'General',
                'suggestion' => 'Review fundamentals and practice more exercises',
            ];
        });
    }

    private function getPortfolioCompletion(User $user)
    {
        $portfolio = $user->portfolios()->first();
        if (!$portfolio) return 0;
        
        // Calculate completion based on filled fields
        $totalFields = 10; // Assuming 10 key fields
        $completedFields = 0;
        
        if ($portfolio->title) $completedFields++;
        if ($portfolio->bio) $completedFields++;
        if ($portfolio->skills) $completedFields++;
        // ... check other fields
        
        return round(($completedFields / $totalFields) * 100);
    }

    private function getResumeStatus(User $user)
    {
        $resume = $user->resumeProfile;
        return [
            'exists' => !!$resume,
            'completion' => $user->getResumeCompletionPercentage(),
            'last_updated' => $resume ? $resume->updated_at->diffForHumans() : null,
        ];
    }

    private function getInterviewPrepStatus(User $user)
    {
        $mockInterviews = $user->mockInterviews()->count();
        return [
            'completed_sessions' => $mockInterviews,
            'skill_level' => $mockInterviews > 5 ? 'Advanced' : ($mockInterviews > 2 ? 'Intermediate' : 'Beginner'),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.student-dashboard');
    }
}