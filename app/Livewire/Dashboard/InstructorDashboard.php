<?php

namespace App\Livewire\Dashboard;

use App\Models\Course;
use App\Models\User;
use App\Models\Certificate;
use App\Models\CourseReview;
use App\Models\CourseEnrollment;
use App\Models\StudentAnswer;
use App\Models\Assessment;
use App\Models\SupportTicket;
use App\Models\MarketplaceItem;
use App\Models\JobApplication;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.dashboard', ['title' => 'Instructor Dashboard'])]
class InstructorDashboard extends Component
{
    public $selectedTimeframe = '30days';
    public $selectedCourseFilter = 'all';
    
    public $showWidgets = [
        'overview_stats' => true,
        'course_performance' => true,
        'student_analytics' => true,
        'recent_enrollments' => true,
        'certificate_requests' => true,
        'course_reviews' => true,
        'earnings_overview' => true,
        'marketplace_items' => true,
    ];

    protected $listeners = [
        'refreshDashboard' => 'loadAllData',
        'timeframeChanged' => 'updateTimeframe',
        'courseFilterChanged' => 'updateCourseFilter',
    ];

    public function mount()
    {
        $user = Auth::user();
        if (!$user->isInstructor()) {
            redirect()->route($user->getDashboardRouteName());
        }
    }

    public function updateTimeframe($timeframe)
    {
        $this->selectedTimeframe = $timeframe;
    }

    public function updateCourseFilter($filter)
    {
        $this->selectedCourseFilter = $filter;
    }

    #[Computed]
    public function overviewStats()
    {
        $instructor = Auth::user();
        $timeframe = $this->getTimeframeQuery();
        
        return [
            'total_courses' => $instructor->courses()->count(),
            'published_courses' => $instructor->courses()->where('is_published', true)->count(),
            'pending_courses' => $instructor->courses()->where('is_approved', false)->count(),
            'total_students' => $this->getTotalStudentsCount($instructor),
            'new_enrollments' => $this->getNewEnrollmentsCount($instructor, $timeframe),
            'course_completions' => $this->getCourseCompletionsCount($instructor, $timeframe),
            'average_rating' => $this->getAverageRating($instructor),
            'total_reviews' => $this->getTotalReviewsCount($instructor),
            'certificates_issued' => $this->getCertificatesIssuedCount($instructor, $timeframe),
            'total_earnings' => $this->getTotalEarnings($instructor),
            'monthly_earnings' => $this->getMonthlyEarnings($instructor),
        ];
    }

    #[Computed]
    public function coursePerformance()
    {
        $instructor = Auth::user();
        $query = $instructor->courses()->with(['enrollments', 'reviews']);
        
        if ($this->selectedCourseFilter !== 'all') {
            $query->where('courses.id', $this->selectedCourseFilter);
        }
        
        return $query->get()->map(function($course) {
            $enrollments = $course->enrollments;
            $totalEnrollments = $enrollments->count();
            $completedEnrollments = $enrollments->where('is_completed', true)->count();
            
            return [
                'id' => $course->id,
                'title' => $course->title,
                'thumbnail' => $course->thumbnail,
                'total_enrollments' => $totalEnrollments,
                'completion_rate' => $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0,
                'average_rating' => $course->reviews->avg('rating') ?? 0,
                'total_reviews' => $course->reviews->count(),
                'revenue' => $this->getCourseRevenue($course),
                'last_enrollment' => $enrollments->max('created_at'),
                'status' => $this->getCourseStatus($course),
            ];
        })->sortByDesc('total_enrollments');
    }

    #[Computed]
    public function studentAnalytics()
    {
        $instructor = Auth::user();
        $days = $this->getTimeframeDays();
        
        // Enrollment trends
        $enrollmentTrends = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $enrollments = CourseEnrollment::whereHas('course', function($query) use ($instructor) {
                    $query->where('instructor_id', $instructor->id);
                })
                ->whereDate('created_at', $date)
                ->count();
            
            $enrollmentTrends[] = [
                'date' => $date->format('M j'),
                'enrollments' => $enrollments,
            ];
        }

        // Student engagement metrics
        $totalStudents = $this->getTotalStudentsCount($instructor);
        $activeStudents = $this->getActiveStudentsCount($instructor);
        
        // Top performing students
        $topStudents = $this->getTopPerformingStudents($instructor);
        
        return [
            'enrollment_trends' => $enrollmentTrends,
            'total_students' => $totalStudents,
            'active_students' => $activeStudents,
            'engagement_rate' => $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 1) : 0,
            'top_students' => $topStudents,
            'completion_rate' => $this->getOverallCompletionRate($instructor),
        ];
    }

    #[Computed]
    public function recentEnrollments()
    {
        $instructor = Auth::user();
        
        return CourseEnrollment::whereHas('course', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })
            ->with(['user', 'course'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function($enrollment) {
                return [
                    'student_name' => $enrollment->user->name,
                    'student_email' => $enrollment->user->email,
                    'course_title' => $enrollment->course->title,
                    'enrolled_at' => $enrollment->created_at,
                    'progress' => $enrollment->progress_percentage ?? 0,
                    'is_completed' => $enrollment->is_completed,
                ];
            });
    }

    #[Computed]
    public function certificateRequests()
    {
        $instructor = Auth::user();
        
        return Certificate::whereHas('course', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })
            ->with(['user', 'course'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($certificate) {
                return [
                    'id' => $certificate->id,
                    'student_name' => $certificate->user->name,
                    'course_title' => $certificate->course->title,
                    'completion_date' => $certificate->completion_date,
                    'grade' => $certificate->grade,
                    'requested_at' => $certificate->created_at,
                ];
            });
    }

    #[Computed]
    public function courseReviews()
    {
        $instructor = Auth::user();
        
        return CourseReview::whereHas('course', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })
            ->with(['user', 'course'])
            ->latest()
            ->take(8)
            ->get()
            ->map(function($review) {
                return [
                    'id' => $review->id,
                    'student_name' => $review->user->name,
                    'course_title' => $review->course->title,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at,
                ];
            });
    }

    #[Computed]
    public function earningsOverview()
    {
        $instructor = Auth::user();
        $days = $this->getTimeframeDays();
        
        // Daily earnings for the selected timeframe
        $dailyEarnings = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $earnings = $this->getDailyEarnings($instructor, $date);
            
            $dailyEarnings[] = [
                'date' => $date->format('M j'),
                'earnings' => $earnings,
            ];
        }
        
        return [
            'daily_earnings' => $dailyEarnings,
            'total_earnings' => $this->getTotalEarnings($instructor),
            'monthly_earnings' => $this->getMonthlyEarnings($instructor),
            'pending_earnings' => $this->getPendingEarnings($instructor),
            'top_earning_courses' => $this->getTopEarningCourses($instructor),
        ];
    }

    #[Computed]
    public function marketplaceItems()
    {
        $instructor = Auth::user();
        
        return $instructor->marketplaceItems()
            ->latest()
            ->take(6)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'type' => $item->type_name,
                    'status' => $item->status_name,
                    'price' => $item->getFormattedPrice(),
                    'views' => $item->views_count ?? 0,
                    'sales' => $item->orders()->where('status', 'completed')->count(),
                ];
            });
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

    private function getTotalStudentsCount(User $instructor)
    {
        return CourseEnrollment::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->distinct('user_id')->count('user_id');
    }

    private function getNewEnrollmentsCount(User $instructor, $timeframe)
    {
        return CourseEnrollment::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->where('created_at', '>=', $timeframe)->count();
    }

    private function getCourseCompletionsCount(User $instructor, $timeframe)
    {
        return CourseEnrollment::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })
        ->where('is_completed', true)
        ->where('completed_at', '>=', $timeframe)
        ->count();
    }

    private function getAverageRating(User $instructor)
    {
        return CourseReview::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->avg('rating') ?? 0;
    }

    private function getTotalReviewsCount(User $instructor)
    {
        return CourseReview::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->count();
    }

    private function getCertificatesIssuedCount(User $instructor, $timeframe)
    {
        return Certificate::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })
        ->where('status', 'approved')
        ->where('approved_at', '>=', $timeframe)
        ->count();
    }

// Find the getTotalEarnings method and update the subquery:
private function getTotalEarnings(User $instructor)
{
    return DB::table('wallet_transactions')
        ->where('transactionable_type', 'App\Models\Course')
        ->whereIn('transactionable_id', function($query) use ($instructor) {
            $query->select('id')
                  ->from('courses')
                  ->where('courses.instructor_id', $instructor->id); // Specify table
        })
        ->where('category', 'instructor_earning')
        ->where('type', 'credit')
        ->sum('amount') ?? 0;
}

// Similarly update getMonthlyEarnings:
private function getMonthlyEarnings(User $instructor)
{
    return DB::table('wallet_transactions')
        ->where('transactionable_type', 'App\Models\Course')
        ->whereIn('transactionable_id', function($query) use ($instructor) {
            $query->select('id')
                  ->from('courses')
                  ->where('courses.instructor_id', $instructor->id); // Specify table
        })
        ->where('category', 'instructor_earning')
        ->where('type', 'credit')
        ->whereMonth('created_at', now()->month)
        ->sum('amount') ?? 0;
}

// And update getDailyEarnings:
private function getDailyEarnings(User $instructor, $date)
{
    return DB::table('wallet_transactions')
        ->where('transactionable_type', 'App\Models\Course')
        ->whereIn('transactionable_id', function($query) use ($instructor) {
            $query->select('id')
                  ->from('courses')
                  ->where('courses.instructor_id', $instructor->id); // Specify table
        })
        ->where('category', 'instructor_earning')
        ->where('type', 'credit')
        ->whereDate('created_at', $date)
        ->sum('amount') ?? 0;
} 

    private function getCourseRevenue(Course $course)
    {
        if (!$course->is_premium) return 0;
        
        $enrollments = $course->enrollments()->count();
        return $enrollments * $course->price;
    }

    private function getCourseStatus(Course $course)
    {
        if (!$course->is_approved) return 'Pending Approval';
        if (!$course->is_published) return 'Draft';
        return 'Published';
    }

    private function getActiveStudentsCount(User $instructor)
    {
        // Students who have activity in the last 30 days
        return CourseEnrollment::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })
        ->where('updated_at', '>=', now()->subDays(30))
        ->distinct('user_id')
        ->count('user_id');
    }

    private function getTopPerformingStudents(User $instructor)
    {
        return StudentAnswer::whereHas('assessment.course', function($query) use ($instructor) {
                $query->where('instructor_id', $instructor->id);
            })
            ->with('user')
            ->select('user_id')
            ->selectRaw('AVG((points_earned / (SELECT max_score FROM assessments WHERE id = student_answers.assessment_id)) * 100) as avg_score')
            ->groupBy('user_id')
            ->orderByDesc('avg_score')
            ->take(5)
            ->get()
            ->map(function($answer) {
                return [
                    'name' => $answer->user->name,
                    'average_score' => round($answer->avg_score, 1),
                ];
            });
    }

    private function getOverallCompletionRate(User $instructor)
    {
        $totalEnrollments = CourseEnrollment::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->count();

        $completedEnrollments = CourseEnrollment::whereHas('course', function($query) use ($instructor) {
            $query->where('instructor_id', $instructor->id);
        })->where('is_completed', true)->count();

        return $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100, 1) : 0;
    }

    private function getPendingEarnings(User $instructor)
    {
        // Earnings that haven't been withdrawn yet
        $totalEarnings = $this->getTotalEarnings($instructor);
        $withdrawnEarnings = $instructor->withdrawals()->where('status', 'completed')->sum('amount');
        
        return $totalEarnings - $withdrawnEarnings;
    }

    private function getTopEarningCourses(User $instructor)
    {
        return $instructor->courses()
            ->where('is_premium', true)
            ->withCount('enrollments')
            ->get()
            ->map(function($course) {
                return [
                    'title' => $course->title,
                    'revenue' => $course->enrollments_count * $course->price,
                ];
            })
            ->sortByDesc('revenue')
            ->take(5);
    }

    public function approveCertificate($certificateId)
    {
        $certificate = Certificate::findOrFail($certificateId);
        
        // Verify the certificate belongs to instructor's course
        if ($certificate->course->instructor_id !== Auth::id()) {
            $this->dispatch('notify', type: 'error', message: 'Unauthorized action.');
            return;
        }

        $certificate->approve(Auth::id());
        $this->dispatch('notify', type: 'success', message: 'Certificate approved successfully!');
    }

    public function rejectCertificate($certificateId, $reason)
    {
        $certificate = Certificate::findOrFail($certificateId);
        
        if ($certificate->course->instructor_id !== Auth::id()) {
            $this->dispatch('notify', type: 'error', message: 'Unauthorized action.');
            return;
        }

        $certificate->reject($reason, Auth::id());
        $this->dispatch('notify', type: 'success', message: 'Certificate rejected.');
    }

    public function render()
    {
        return view('livewire.dashboard.instructor-dashboard');
    }
}