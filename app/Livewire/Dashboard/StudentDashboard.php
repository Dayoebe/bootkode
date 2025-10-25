<?php

namespace App\Livewire\Dashboard;

use App\Models\Learning\Course;
use App\Models\Core\User;
use App\Models\Admin\Announcement;
use App\Models\Credentials\Certificate;
use App\Models\Core\UserAchievement;
use App\Models\Community\SystemStatus;
use App\Models\Learning\CourseEnrollment;
use App\Models\Assessment\StudentAnswer;
use App\Models\Assessment\Assessment;
use App\Models\Career\JobApplication;
use App\Models\Mentorship\Mentorship;
use App\Models\Assessment\LearningSession;
use App\Models\Content\Portfolio;
use App\Models\Career\Wishlist;
use App\Models\Learning\CourseReview;
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
        'achievements' => true,
        'upcoming_tasks' => true,
        'performance_analytics' => true,
        'announcements' => true,
        'career_tools' => true,
        'mentorship_status' => true,
        'recent_reviews' => true,
        'wishlist_preview' => true,
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
        
        return [
            'enrolled_courses' => $user->enrollments()->count(),
            'completed_courses' => $user->enrollments()->where('is_completed', true)->count(),
            'certificates_earned' => $user->certificates()->approved()->count(),
            'study_streak' => LearningSession::getStudyStreak($user->id),
            'lessons_completed' => DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->count(),
            'average_score' => $this->getAverageAssessmentScore($user),
            'total_study_hours' => $this->getTotalStudyTime($user),
            'current_level' => $user->getOrCreateGamificationData()->level ?? 1,
            'total_points' => $user->getOrCreateGamificationData()->points ?? 0,
            'achievements_unlocked' => $user->badges()->count(),
            'pending_assessments' => $this->getPendingAssessmentsCount($user),
            'wishlist_count' => Wishlist::where('user_id', $user->id)->count(),
        ];
    }

    #[Computed]
    public function learningProgress()
    {
        $user = Auth::user();
        $enrollments = $user->enrollments()
            ->with(['course' => function($query) {
                $query->select('id', 'title', 'thumbnail', 'estimated_duration_minutes', 'instructor_id')
                    ->with('instructor:id,name');
            }])
            ->where('progress_percentage', '<', 100)
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        return $enrollments->filter(function($enrollment) {
            return $enrollment->course !== null;
        })->map(function($enrollment) use ($user) {
            $nextLesson = $this->getNextLesson($enrollment->course, $user->id);
            
            return [
                'id' => $enrollment->course->id,
                'title' => $enrollment->course->title,
                'thumbnail' => $enrollment->course->thumbnail,
                'progress' => $enrollment->progress_percentage ?? 0,
                'last_accessed' => $enrollment->updated_at,
                'estimated_remaining' => $this->calculateRemainingTime($enrollment),
                'next_lesson' => $nextLesson,
                'instructor_name' => optional($enrollment->course->instructor)->name ?? 'Unknown',
                'total_lessons' => $enrollment->course->allLessons()->count(),
                'completed_lessons' => DB::table('lesson_user')
                    ->whereIn('lesson_id', $enrollment->course->allLessons()->pluck('id'))
                    ->where('user_id', $user->id)
                    ->whereNotNull('completed_at')
                    ->count(),
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
                    'points_earned' => $badge->points_awarded ?? 0,
                ];
            });
    }

    #[Computed]
    public function upcomingTasks()
    {
        $user = Auth::user();
        $tasks = collect();
    
        // Upcoming assessments from enrolled courses
        $upcomingAssessments = Assessment::whereHas('course.enrollments', function($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', false);
            })
            ->whereDoesntHave('studentAnswers', function($query) use ($user) {
                $query->where('user_id', $user->id)->whereNotNull('submitted_at');
            })
            ->where(function($query) {
                $query->where('due_date', '>', now())
                    ->orWhere('deadline', '>', now())
                    ->orWhereNull('due_date');
            })
            ->with('course:id,title,slug')
            ->orderByRaw('COALESCE(due_date, deadline, NOW() + INTERVAL 30 DAY)')
            ->take(5)
            ->get();
    
        foreach($upcomingAssessments as $assessment) {
            $dueDate = $assessment->due_date ?? $assessment->deadline ?? now()->addDays(30);
            
            $tasks->push([
                'type' => 'assessment',
                'title' => $assessment->title,
                'course' => $assessment->course->title,
                'due_date' => $dueDate,
                'priority' => $this->calculateTaskPriority($dueDate),
                'url' => route('course.view', ['course' => $assessment->course->slug]),
                'is_mandatory' => $assessment->is_mandatory ?? false,
            ]);
        }
    
        // Incomplete lessons from active courses
        $activeCourses = $user->enrollments()
            ->where('progress_percentage', '<', 100)
            ->where('progress_percentage', '>', 0)
            ->with('course')
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get();

        foreach($activeCourses as $enrollment) {
            $nextLesson = $this->getNextLesson($enrollment->course, $user->id);
            
            if($nextLesson) {
                $tasks->push([
                    'type' => 'lesson',
                    'title' => 'Continue: ' . $nextLesson['title'],
                    'course' => $enrollment->course->title,
                    'due_date' => now()->addDays(2),
                    'priority' => 'medium',
                    'url' => route('course.view', ['course' => $enrollment->course->id]),
                    'is_mandatory' => false,
                ]);
            }
        }

        // Pending certificate requests
        $pendingCertificates = Certificate::where('user_id', $user->id)
            ->whereIn('status', [Certificate::STATUS_REQUESTED, Certificate::STATUS_PENDING])
            ->with('course:id,title')
            ->get();

        foreach($pendingCertificates as $cert) {
            $tasks->push([
                'type' => 'certificate',
                'title' => 'Certificate Pending: ' . $cert->course->title,
                'course' => $cert->course->title,
                'due_date' => $cert->requested_at->addDays(7),
                'priority' => 'low',
                'url' => route('student.certificates.index'),
                'is_mandatory' => false,
            ]);
        }
    
        return $tasks->sortBy('due_date')->take(10);
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
            $activity = LearningSession::where('user_id', $user->id)
                ->whereDate('started_at', $date)
                ->whereNotNull('ended_at')
                ->sum('duration_minutes');
            
            $studyActivity[] = [
                'date' => $date->format('M j'),
                'activity' => round($activity / 60, 1), // Convert to hours
            ];
        }

        // Subject/Category performance
        $subjectPerformance = CourseEnrollment::where('user_id', $user->id)
            ->with(['course.category'])
            ->whereNotNull('progress_percentage')
            ->get()
            ->filter(function($enrollment) {
                return $enrollment->course !== null;
            })
            ->groupBy(function($enrollment) {
                return optional($enrollment->course->category)->name ?? 'Uncategorized';
            })
            ->map(function($enrollments, $category) {
                $avgProgress = $enrollments->avg('progress_percentage');
                $completedCount = $enrollments->where('is_completed', true)->count();
                
                return [
                    'category' => $category,
                    'average_progress' => round($avgProgress, 1),
                    'courses_enrolled' => $enrollments->count(),
                    'courses_completed' => $completedCount,
                    'completion_rate' => $enrollments->count() > 0 
                        ? round(($completedCount / $enrollments->count()) * 100, 1) 
                        : 0,
                ];
            })
            ->sortByDesc('average_progress')
            ->values();

        return [
            'study_activity' => $studyActivity,
            'subject_performance' => $subjectPerformance,
            'weekly_goals' => $this->getWeeklyGoals($user),
            'improvement_areas' => $this->getImprovementAreas($user),
            'total_study_time_this_period' => array_sum(array_column($studyActivity, 'activity')),
        ];
    }

    #[Computed]
    public function recentAnnouncements()
    {
        $user = Auth::user();
        $enrolledCourseIds = $user->enrollments()->pluck('course_id');

        return Announcement::where('status', 'published')
            ->where('published_at', '<=', now())
            ->where(function($query) use ($enrolledCourseIds) {
                $query->whereNull('course_id')
                    ->orWhereIn('course_id', $enrolledCourseIds);
            })
            ->with('course:id,title', 'user:id,name')
            ->latest('published_at')
            ->take(5)
            ->get()
            ->map(function($announcement) {
                return [
                    'id' => $announcement->id,
                    'title' => $announcement->title,
                    'content' => \Str::limit(strip_tags($announcement->content), 150),
                    'published_at' => $announcement->published_at,
                    'course_title' => optional($announcement->course)->title ?? 'General Announcement',
                    'author' => optional($announcement->user)->name ?? 'System',
                    'is_new' => $announcement->published_at->isAfter(now()->subDays(3)),
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
                ->with(['job' => function($query) {
                    $query->select('id', 'title', 'company_name', 'employment_type');
                }])
                ->latest()
                ->take(3)
                ->get()
                ->filter(function($app) {
                    return $app->job !== null;
                })
                ->map(function($app) {
                    return [
                        'id' => $app->id,
                        'job_title' => $app->job->title ?? 'N/A',
                        'company' => $app->job->company_name ?? 'N/A',
                        'status' => $app->status,
                        'status_label' => ucfirst(str_replace('_', ' ', $app->status)),
                        'status_color' => $this->getApplicationStatusColor($app->status),
                        'applied_at' => $app->created_at,
                    ];
                }),
            'total_applications' => JobApplication::where('user_id', $user->id)->count(),
            'shortlisted_count' => JobApplication::where('user_id', $user->id)
                ->where('status', 'shortlisted')
                ->count(),
        ];
    }

    #[Computed]
    public function mentorshipStatus()
    {
        $user = Auth::user();
        
        $activeMentorships = Mentorship::where('mentee_id', $user->id)
            ->where('status', Mentorship::STATUS_ACTIVE)
            ->with('mentor:id,name')
            ->get()
            ->filter(function($mentorship) {
                return $mentorship->mentor !== null;
            });

        $pendingRequests = Mentorship::where('mentee_id', $user->id)
            ->where('status', Mentorship::STATUS_PENDING)
            ->count();

        return [
            'active_mentorships' => $activeMentorships->map(function($mentorship) {
                return [
                    'id' => $mentorship->id,
                    'mentor_name' => $mentorship->mentor->name ?? 'Unknown Mentor',
                    'started_at' => $mentorship->started_at,
                    'progress_percentage' => $mentorship->progress_percentage ?? 0,
                    'next_session' => $mentorship->next_session,
                    'duration_weeks' => $mentorship->duration_weeks ?? 0,
                ];
            }),
            'pending_requests' => $pendingRequests,
            'completed_mentorships' => Mentorship::where('mentee_id', $user->id)
                ->where('status', Mentorship::STATUS_COMPLETED)
                ->count(),
        ];
    }

    #[Computed]
    public function recentReviews()
    {
        $user = Auth::user();
        
        return CourseReview::where('user_id', $user->id)
            ->with('course:id,title,slug')
            ->latest()
            ->take(3)
            ->get()
            ->map(function($review) {
                return [
                    'id' => $review->id,
                    'course_title' => $review->course->title,
                    'rating' => $review->rating,
                    'comment' => \Str::limit($review->comment, 100),
                    'is_approved' => $review->is_approved,
                    'helpful_count' => $review->helpful_count ?? 0,
                    'has_reply' => !empty($review->instructor_reply),
                    'created_at' => $review->created_at,
                ];
            });
    }

    #[Computed]
    public function wishlistPreview()
    {
        $user = Auth::user();
        
        return Wishlist::where('user_id', $user->id)
            ->with(['course' => function($query) {
                $query->select('id', 'title', 'thumbnail', 'price', 'is_free', 'slug', 'average_rating', 'instructor_id')
                    ->with('instructor:id,name');
            }])
            ->latest()
            ->take(4)
            ->get()
            ->filter(function($wishlist) {
                return $wishlist->course !== null;
            })
            ->map(function($wishlist) {
                return [
                    'id' => $wishlist->id,
                    'course_id' => $wishlist->course->id,
                    'title' => $wishlist->course->title,
                    'thumbnail' => $wishlist->course->thumbnail,
                    'price' => $wishlist->course->price,
                    'is_free' => $wishlist->course->is_free,
                    'instructor' => optional($wishlist->course->instructor)->name ?? 'Unknown',
                    'rating' => $wishlist->course->average_rating,
                    'slug' => $wishlist->course->slug,
                    'added_at' => $wishlist->created_at,
                ];
            });
    }

    #[Computed]
    public function systemHealth()
    {
        $status = SystemStatus::latest()->first();
        return [
            'status' => $status->status ?? 'operational',
            'message' => $status->message ?? 'All systems operational',
            'updated_at' => $status ? $status->updated_at->diffForHumans() : 'Just now',
            'incidents_count' => SystemStatus::where('status', '!=', 'operational')
                ->where('created_at', '>', now()->subDays(7))
                ->count(),
        ];
    }

    // Helper Methods
    private function getTimeframeDays()
    {
        return match ($this->selectedTimeframe) {
            '24hours' => 1,
            '7days' => 7,
            '30days' => 30,
            default => 7,
        };
    }

    private function getAverageAssessmentScore(User $user)
    {
        $avgScore = StudentAnswer::where('user_id', $user->id)
            ->whereNotNull('points_earned')
            ->whereNotNull('submitted_at')
            ->join('assessments', 'student_answers.assessment_id', '=', 'assessments.id')
            ->selectRaw('AVG((student_answers.points_earned / assessments.max_score) * 100) as avg_score')
            ->value('avg_score');

        return round($avgScore ?? 0, 1);
    }

    private function getTotalStudyTime(User $user)
    {
        $totalMinutes = LearningSession::where('user_id', $user->id)
            ->whereNotNull('ended_at')
            ->sum('duration_minutes');
        
        return round($totalMinutes / 60, 1);
    }

    private function getPendingAssessmentsCount(User $user)
    {
        return Assessment::whereHas('course.enrollments', function($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', false);
            })
            ->whereDoesntHave('studentAnswers', function($query) use ($user) {
                $query->where('user_id', $user->id)->whereNotNull('submitted_at');
            })
            ->count();
    }

    private function calculateRemainingTime($enrollment)
    {
        $course = $enrollment->course;
        if (!$course->estimated_duration_minutes) return null;
        
        $remainingPercentage = 100 - ($enrollment->progress_percentage ?? 0);
        $remainingMinutes = ($course->estimated_duration_minutes * $remainingPercentage) / 100;
        
        return round($remainingMinutes / 60, 1);
    }

    private function getNextLesson($course, $userId)
    {
        $completedLessons = DB::table('lesson_user')
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->pluck('lesson_id');

        $nextLesson = $course->allLessons()
            ->whereNotIn('lessons.id', $completedLessons)
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
        $lessonsCompleted = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('completed_at', '>=', now()->startOfWeek())
            ->count();

        $studyMinutes = LearningSession::where('user_id', $user->id)
            ->where('started_at', '>=', now()->startOfWeek())
            ->whereNotNull('ended_at')
            ->sum('duration_minutes');

        return [
            'lessons_target' => 5,
            'lessons_completed' => $lessonsCompleted,
            'study_hours_target' => 10,
            'study_hours_completed' => round($studyMinutes / 60, 1),
        ];
    }

    private function getImprovementAreas(User $user)
    {
        $lowScoreAssessments = Assessment::whereHas('studentAnswers', function($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->whereRaw('(points_earned / (SELECT max_score FROM assessments WHERE id = student_answers.assessment_id)) * 100 < 70');
            })
            ->with(['course.category'])
            ->take(3)
            ->get()
            ->filter(function($assessment) {
                return $assessment->course !== null;
            });

        return $lowScoreAssessments->map(function($assessment) {
            return [
                'area' => optional($assessment->course->category)->name ?? 'General',
                'course' => $assessment->course->title ?? 'Unknown Course',
                'suggestion' => 'Review fundamentals and practice more exercises',
                'resources_available' => true,
            ];
        });
    }

    private function getPortfolioCompletion(User $user)
    {
        $portfolio = Portfolio::where('user_id', $user->id)->first();
        if (!$portfolio) return 0;
        
        $fields = ['title', 'description', 'category', 'technologies', 'image_path'];
        $completedFields = 0;
        
        foreach($fields as $field) {
            if (!empty($portfolio->$field)) $completedFields++;
        }
        
        return round(($completedFields / count($fields)) * 100);
    }

    private function getResumeStatus(User $user)
    {
        $resume = $user->resumeProfile;
        return [
            'exists' => !!$resume,
            'completion' => $resume ? $user->getResumeCompletionPercentage() : 0,
            'last_updated' => $resume ? $resume->updated_at->diffForHumans() : null,
        ];
    }

    private function getApplicationStatusColor($status)
    {
        return match($status) {
            'pending' => 'yellow',
            'reviewing' => 'blue',
            'shortlisted' => 'indigo',
            'interviewed' => 'purple',
            'offered' => 'green',
            'hired' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }

    public function render()
    {
        return view('livewire.dashboard.student-dashboard');
    }
}