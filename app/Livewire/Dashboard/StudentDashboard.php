<?php

namespace App\Livewire\Dashboard;

use App\Models\Admin\Announcement;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Assessment\StudentAnswer;
use App\Models\Career\JobApplication;
use App\Models\Career\Wishlist;
use App\Models\Community\SystemStatus;
use App\Models\Content\Portfolio;
use App\Models\Core\User;
use App\Models\Credentials\Certificate;
use App\Models\Learning\CourseEnrollment;
use App\Models\Learning\CourseReview;
use App\Models\Mentorship\Mentorship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

#[Layout('layouts.dashboard', ['title' => 'Student Dashboard'])]
class StudentDashboard extends Component
{
    #[Locked]
    public array $widgetFailures = [];

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
        if (! $user->canAccessDashboardRole(User::ROLE_STUDENT)) {
            return $this->redirectRoute($user->getDashboardRouteName(), navigate: true);
        }
    }

    public function updateTimeframe($timeframe)
    {
        abort_unless(in_array($timeframe, ['7days', '30days'], true), 422);
        $this->selectedTimeframe = $timeframe;
    }

    #[Computed]
    public function learningProfile()
    {
        return \App\Models\Ai\AiLearningProfile::where('user_id', Auth::id())->first();
    }

    #[Computed]
    public function quickStats()
    {
        $user = Auth::user();
        $enrollmentCounts = $user->enrollments()
            ->selectRaw('COUNT(*) as enrolled_courses')
            ->selectRaw('SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed_courses')
            ->first();

        return [
            'enrolled_courses' => (int) ($enrollmentCounts?->enrolled_courses ?? 0),
            'completed_courses' => (int) ($enrollmentCounts?->completed_courses ?? 0),
            'certificates_earned' => $user->certificates()->approved()->count(),
            'lessons_completed' => DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->count(),
            'average_score' => $this->getAverageAssessmentScore($user),
            'total_study_hours' => $this->getTotalStudyTime($user),
            'achievements_unlocked' => $user->badges()->count(),
            'pending_assessments' => $this->getPendingAssessmentsCount($user),
            'wishlist_count' => Wishlist::where('user_id', $user->id)->count(),
        ];
    }

    #[Computed]
    public function learningProgress()
    {
        $user = Auth::user();
        $completedLessonIds = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->pluck('lesson_id');
        $completedLessonLookup = array_fill_keys($completedLessonIds->map(fn ($id) => (int) $id)->all(), true);

        $enrollments = $user->enrollments()
            ->with(['course' => function ($query) {
                $query->select('id', 'title', 'slug', 'thumbnail', 'estimated_duration_minutes', 'instructor_id')
                    ->with([
                        'instructor:id,name',
                        'sections' => fn ($sectionQuery) => $sectionQuery
                            ->select('id', 'course_id', 'title', 'order')
                            ->orderBy('order')
                            ->with([
                                'lessons' => fn ($lessonQuery) => $lessonQuery
                                    ->select('id', 'section_id', 'title', 'order', 'published_at', 'scheduled_publish_at')
                                    ->published()
                                    ->orderBy('order'),
                            ]),
                    ]);
            }])
            ->where('progress_percentage', '<', 100)
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        return $enrollments->filter(function ($enrollment) {
            return $enrollment->course !== null;
        })->map(function ($enrollment) use ($completedLessonLookup) {
            $lessons = $enrollment->course->sections->flatMap(function ($section) {
                return $section->lessons->each(fn ($lesson) => $lesson->setRelation('section', $section));
            });
            $nextLessonModel = $lessons->first(fn ($lesson) => ! isset($completedLessonLookup[(int) $lesson->id]));
            $nextLesson = $nextLessonModel ? [
                'id' => $nextLessonModel->id,
                'title' => $nextLessonModel->title,
                'section' => $nextLessonModel->section?->title ?? 'General',
            ] : null;

            // Fix thumbnail URL - check if it exists and build proper URL
            $thumbnailUrl = null;
            if ($enrollment->course->thumbnail) {
                // Check if it's already a full URL
                if (filter_var($enrollment->course->thumbnail, FILTER_VALIDATE_URL)) {
                    $thumbnailUrl = $enrollment->course->thumbnail;
                }
                // Check if file exists in storage
                elseif (Storage::disk('public')->exists($enrollment->course->thumbnail)) {
                    $thumbnailUrl = Storage::url($enrollment->course->thumbnail);
                }
                // Fallback - try direct storage path
                else {
                    $thumbnailUrl = asset('storage/'.$enrollment->course->thumbnail);
                }
            }

            return [
                'id' => $enrollment->course->id,
                'slug' => $enrollment->course->slug, // Add slug for routing
                'title' => $enrollment->course->title,
                'thumbnail' => $thumbnailUrl,
                'progress' => $enrollment->progress_percentage ?? 0,
                'last_accessed' => $enrollment->updated_at,
                'estimated_remaining' => $this->calculateRemainingTime($enrollment),
                'next_lesson' => $nextLesson,
                'instructor_name' => optional($enrollment->course->instructor)->name ?? 'Unknown',
                'total_lessons' => $lessons->count(),
                'completed_lessons' => $lessons
                    ->filter(fn ($lesson) => isset($completedLessonLookup[(int) $lesson->id]))
                    ->count(),
            ];
        });
    }

    #[Computed]
    public function recentAchievements()
    {
        return $this->secondaryWidget('achievements', collect(), fn () => Auth::user()->badges()
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($badge) => [
                'name' => $badge->badge_name,
                'description' => $badge->badge_description,
                'icon' => $badge->badge_icon,
                'rarity' => $badge->rarity,
                'earned_at' => $badge->created_at,
                'points_earned' => $badge->points_awarded ?? 0,
            ]));
    }

    #[Computed]
    public function upcomingTasks()
    {
        return $this->secondaryWidget('upcoming_tasks', collect(), function () {
            $user = Auth::user();
            $tasks = collect();

            // Upcoming assessments from enrolled courses
            $upcomingAssessments = Assessment::whereHas('course.enrollments', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('is_completed', false);
            })
                ->whereDoesntHave('studentAnswers', function ($query) use ($user) {
                    $query->where('user_id', $user->id)->whereNotNull('submitted_at');
                })
                ->where(function ($query) {
                    $query->where('due_date', '>', now())
                        ->orWhere('deadline', '>', now())
                        ->orWhereNull('due_date');
                })
                ->with('course:id,title,slug')
                ->orderByRaw('COALESCE(due_date, deadline, NOW() + INTERVAL 30 DAY)')
                ->take(5)
                ->get();

            foreach ($upcomingAssessments as $assessment) {
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
            foreach ($this->learningProgress->where('progress', '>', 0)->take(3) as $progress) {
                if ($progress['next_lesson']) {
                    $tasks->push([
                        'type' => 'lesson',
                        'title' => 'Continue: '.$progress['next_lesson']['title'],
                        'course' => $progress['title'],
                        'due_date' => now()->addDays(2),
                        'priority' => 'medium',
                        'url' => route('course.view', ['course' => $progress['slug']]),
                        'is_mandatory' => false,
                    ]);
                }
            }

            // Pending certificate requests
            $pendingCertificates = Certificate::where('user_id', $user->id)
                ->whereIn('status', [Certificate::STATUS_REQUESTED, Certificate::STATUS_PENDING])
                ->with('course:id,title')
                ->get();

            foreach ($pendingCertificates as $cert) {
                $tasks->push([
                    'type' => 'certificate',
                    'title' => 'Certificate Pending: '.$cert->course->title,
                    'course' => $cert->course->title,
                    'due_date' => $cert->requested_at->addDays(7),
                    'priority' => 'low',
                    'url' => route('student.certificates.index'),
                    'is_mandatory' => false,
                ]);
            }

            return $tasks->sortBy('due_date')->take(10);
        });
    }

    #[Computed]
    public function performanceAnalytics()
    {
        return $this->secondaryWidget('performance_analytics', $this->emptyPerformanceAnalytics(), function () {
            $user = Auth::user();
            $days = $this->getTimeframeDays();

            // Daily study activity
            $studyActivity = [];
            $dailyMinutes = LearningSession::where('user_id', $user->id)
                ->where('started_at', '>=', now()->subDays($days - 1)->startOfDay())
                ->whereNotNull('ended_at')
                ->selectRaw('DATE(started_at) as study_date, SUM(duration_minutes) as minutes')
                ->groupByRaw('DATE(started_at)')->pluck('minutes', 'study_date');
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $activity = (float) ($dailyMinutes[$date->toDateString()] ?? 0);

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
                ->filter(function ($enrollment) {
                    return $enrollment->course !== null;
                })
                ->groupBy(function ($enrollment) {
                    return optional($enrollment->course->category)->name ?? 'Uncategorized';
                })
                ->map(function ($enrollments, $category) {
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
        });
    }

    #[Computed]
    public function recentAnnouncements()
    {
        return $this->secondaryWidget('announcements', collect(), function () {
            $user = Auth::user();
            $enrolledCourseIds = $user->enrollments()->pluck('course_id');

            return Announcement::where('status', 'published')
                ->where('published_at', '<=', now())
                ->where(function ($query) use ($enrolledCourseIds) {
                    $query->whereNull('course_id')
                        ->orWhereIn('course_id', $enrolledCourseIds);
                })
                ->with('course:id,title', 'user:id,name')
                ->latest('published_at')
                ->take(5)
                ->get()
                ->map(function ($announcement) {
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
        });
    }

    #[Computed]
    public function careerTools()
    {
        return $this->secondaryWidget('career_tools', $this->emptyCareerTools(), function () {
            $user = Auth::user();

            return [
                'portfolio_completion' => $this->getPortfolioCompletion($user),
                'resume_status' => $this->getResumeStatus($user),
                'job_applications' => JobApplication::where('user_id', $user->id)
                    ->with(['job' => function ($query) {
                        $query->select('id', 'title', 'company_name', 'employment_type');
                    }])
                    ->latest()
                    ->take(3)
                    ->get()
                    ->filter(function ($app) {
                        return $app->job !== null;
                    })
                    ->map(function ($app) {
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
        });
    }

    #[Computed]
    public function mentorshipStatus()
    {
        return $this->secondaryWidget('mentorship_status', $this->emptyMentorshipStatus(), function () {
            $user = Auth::user();

            $activeMentorships = Mentorship::where('mentee_id', $user->id)
                ->where('status', Mentorship::STATUS_ACTIVE)
                ->with('mentor:id,name')
                ->get()
                ->filter(function ($mentorship) {
                    return $mentorship->mentor !== null;
                });

            $pendingRequests = Mentorship::where('mentee_id', $user->id)
                ->where('status', Mentorship::STATUS_PENDING)
                ->count();

            return [
                'active_mentorships' => $activeMentorships->map(function ($mentorship) {
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
        });
    }

    #[Computed]
    public function recentReviews()
    {
        return $this->secondaryWidget('recent_reviews', collect(), function () {
            $user = Auth::user();

            return CourseReview::where('user_id', $user->id)
                ->with('course:id,title,slug')
                ->latest()
                ->take(3)
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'course_title' => $review->course->title,
                        'rating' => $review->rating,
                        'comment' => \Str::limit($review->comment, 100),
                        'is_approved' => $review->is_approved,
                        'helpful_count' => $review->helpful_count ?? 0,
                        'has_reply' => ! empty($review->instructor_reply),
                        'created_at' => $review->created_at,
                    ];
                });
        });
    }

    #[Computed]
    public function wishlistPreview()
    {
        return $this->secondaryWidget('wishlist_preview', collect(), function () {
            $user = Auth::user();

            return Wishlist::where('user_id', $user->id)
                ->with(['course' => function ($query) {
                    $query->select('id', 'title', 'thumbnail', 'price', 'is_free', 'slug', 'average_rating', 'instructor_id')
                        ->with('instructor:id,name');
                }])
                ->latest()
                ->take(4)
                ->get()
                ->filter(function ($wishlist) {
                    return $wishlist->course !== null;
                })
                ->map(function ($wishlist) {
                    // Fix thumbnail URL
                    $thumbnailUrl = null;
                    if ($wishlist->course->thumbnail) {
                        if (filter_var($wishlist->course->thumbnail, FILTER_VALIDATE_URL)) {
                            $thumbnailUrl = $wishlist->course->thumbnail;
                        } elseif (Storage::disk('public')->exists($wishlist->course->thumbnail)) {
                            $thumbnailUrl = Storage::url($wishlist->course->thumbnail);
                        } else {
                            $thumbnailUrl = asset('storage/'.$wishlist->course->thumbnail);
                        }
                    }

                    return [
                        'id' => $wishlist->id,
                        'course_id' => $wishlist->course->id,
                        'title' => $wishlist->course->title,
                        'thumbnail' => $thumbnailUrl,
                        'price' => $wishlist->course->price,
                        'is_free' => $wishlist->course->is_free,
                        'instructor' => optional($wishlist->course->instructor)->name ?? 'Unknown',
                        'rating' => $wishlist->course->average_rating,
                        'slug' => $wishlist->course->slug,
                        'added_at' => $wishlist->created_at,
                    ];
                });
        });
    }

    #[Computed]
    public function systemHealth()
    {
        return $this->secondaryWidget('system_health', $this->emptySystemHealth(), function () {
            $status = SystemStatus::latest()->first();

            return [
                'status' => $status->status ?? 'operational',
                'message' => $status->message ?? 'All systems operational',
                'updated_at' => $status ? $status->updated_at->diffForHumans() : 'Just now',
                'incidents_count' => SystemStatus::where('status', '!=', 'operational')
                    ->where('created_at', '>', now()->subDays(7))
                    ->count(),
            ];
        });
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
        return Assessment::whereHas('course.enrollments', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_completed', false);
        })
            ->whereDoesntHave('studentAnswers', function ($query) use ($user) {
                $query->where('user_id', $user->id)->whereNotNull('submitted_at');
            })
            ->count();
    }

    private function calculateRemainingTime($enrollment)
    {
        $course = $enrollment->course;
        if (! $course->estimated_duration_minutes) {
            return null;
        }

        $remainingPercentage = 100 - ($enrollment->progress_percentage ?? 0);
        $remainingMinutes = ($course->estimated_duration_minutes * $remainingPercentage) / 100;

        return round($remainingMinutes / 60, 1);
    }

    private function calculateTaskPriority($dueDate)
    {
        if (! $dueDate) {
            return 'low';
        }

        $daysUntilDue = now()->diffInDays($dueDate, false);

        if ($daysUntilDue <= 1) {
            return 'high';
        }
        if ($daysUntilDue <= 3) {
            return 'medium';
        }

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
        $lowScoreAssessments = Assessment::whereHas('studentAnswers', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->whereRaw('(points_earned / (SELECT max_score FROM assessments WHERE id = student_answers.assessment_id)) * 100 < 70');
        })
            ->with(['course.category'])
            ->take(3)
            ->get()
            ->filter(function ($assessment) {
                return $assessment->course !== null;
            });

        return $lowScoreAssessments->map(function ($assessment) {
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
        if (! $portfolio) {
            return 0;
        }

        $fields = ['title', 'description', 'category', 'technologies', 'image_path'];
        $completedFields = 0;

        foreach ($fields as $field) {
            if (! empty($portfolio->$field)) {
                $completedFields++;
            }
        }

        return round(($completedFields / count($fields)) * 100);
    }

    private function getResumeStatus(User $user)
    {
        $resume = $user->resumeProfile;

        return [
            'exists' => (bool) $resume,
            'completion' => $resume ? $user->getResumeCompletionPercentage() : 0,
            'last_updated' => $resume ? $resume->updated_at->diffForHumans() : null,
        ];
    }

    private function getApplicationStatusColor($status)
    {
        return match ($status) {
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

    public function loadAllData()
    {
        $this->dispatch('dashboard-updated');
    }

    public function render()
    {
        $this->widgetFailures = [];

        return view('livewire.dashboard.student-dashboard');
    }

    private function secondaryWidget(string $widget, mixed $fallback, callable $load): mixed
    {
        try {
            return $load();
        } catch (Throwable $exception) {
            report($exception);
            $this->widgetFailures[$widget] = true;

            return $fallback;
        }
    }

    private function emptyPerformanceAnalytics(): array
    {
        return [
            'study_activity' => [],
            'subject_performance' => collect(),
            'weekly_goals' => [
                'lessons_target' => 5,
                'lessons_completed' => 0,
                'study_hours_target' => 10,
                'study_hours_completed' => 0,
            ],
            'improvement_areas' => collect(),
            'total_study_time_this_period' => 0,
        ];
    }

    private function emptyCareerTools(): array
    {
        return [
            'portfolio_completion' => 0,
            'resume_status' => ['exists' => false, 'completion' => 0, 'last_updated' => null],
            'job_applications' => collect(),
            'total_applications' => 0,
            'shortlisted_count' => 0,
        ];
    }

    private function emptyMentorshipStatus(): array
    {
        return [
            'active_mentorships' => collect(),
            'pending_requests' => 0,
            'completed_mentorships' => 0,
        ];
    }

    private function emptySystemHealth(): array
    {
        return [
            'status' => 'unavailable',
            'message' => 'Status information is temporarily unavailable.',
            'updated_at' => 'Not available',
            'incidents_count' => 0,
        ];
    }
}
