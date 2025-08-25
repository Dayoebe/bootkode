<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Certificate;
use App\Models\CourseCategory;
use App\Models\CourseEnrollment;
use App\Models\Section;
use App\Models\Assessment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.app', [
    'title' => 'Platform Statistics - BootKode',
    'description' => "Comprehensive analytics and insights into BootKode's growth and impact across Africa.",
    'developer' => 'Bootkode',
    'developer_url' => 'https://bootkode.com'
])]

class Statistics extends Component
{
    public $overviewStats = [];
    public $userGrowthData = [];
    public $courseStats = [];
    public $certificateStats = [];
    public $engagementMetrics = [];
    public $geographicData = [];
    public $revenueData = [];
    public $topPerformers = [];
    public $recentActivity = [];
    public $trendingCourses = [];
    public $instructorLeaderboard = [];
    public $completionRates = [];
    public $categoryBreakdown = [];
    public $timeBasedAnalytics = [];

    public function mount()
    {
        $this->loadOverviewStats();
        $this->loadUserGrowthData();
        $this->loadCourseStatistics();
        $this->loadCertificateAnalytics();
        $this->loadEngagementMetrics();
        $this->loadGeographicData();
        $this->loadRevenueData();
        $this->loadTopPerformers();
        $this->loadRecentActivity();
        $this->loadTrendingCourses();
        $this->loadInstructorLeaderboard();
        $this->loadCompletionRates();
        $this->loadCategoryBreakdown();
        $this->loadTimeBasedAnalytics();
    }

    private function loadOverviewStats()
    {
        $this->overviewStats = Cache::remember('overview_stats', 60, function () {
            // Get counts from the database
            $totalUsers = User::count();
            // Using course enrollments to determine active users as last_active_at is not available
            $totalActiveUsers = CourseEnrollment::where('created_at', '>=', Carbon::now()->subMonth())->distinct('user_id')->count('user_id');
            $totalCourses = Course::count();
            $publishedCourses = Course::where('is_published', 1)->count();
            $totalEnrollments = CourseEnrollment::count();
            $totalCertificates = Certificate::count();
            $totalInstructors = User::where('role', User::ROLE_INSTRUCTOR)->count();
            $totalStudents = User::where('role', User::ROLE_STUDENT)->count();
            $coursesWithEnrollments = Course::withCount('enrollments')->get();
            $totalLessons = Lesson::count();

            // Additional logic for approval rates, completion rates, etc.
            $courseApprovalRate = ($totalCourses > 0) ? (Course::where('is_approved', 1)->count() / $totalCourses) * 100 : 0;
            $avgCompletionRate = ($totalEnrollments > 0) ? (Certificate::count() / $totalEnrollments) * 100 : 0;
            $activeUserPercentage = ($totalUsers > 0) ? ($totalActiveUsers / $totalUsers) * 100 : 0;

            // Calculate enrollments for premium courses
            $premiumCourseEnrollments = 0;
            $premiumCourses = Course::premium()->get();
            foreach ($premiumCourses as $premiumCourse) {
                $premiumCourseEnrollments += $premiumCourse->enrollments()->count();
            }

            return [
                'total_users' => $totalUsers,
                'active_users' => $totalActiveUsers,
                'active_user_percentage' => number_format($activeUserPercentage, 2),
                'published_courses' => $publishedCourses,
                'total_lessons' => $totalLessons,
                'course_approval_rate' => number_format($courseApprovalRate, 2),
                'total_enrollments' => $totalEnrollments,
                'certificates_issued' => $totalCertificates,
                'avg_completion_rate' => number_format($avgCompletionRate, 2),
                'total_instructors' => $totalInstructors,
                'total_students' => $totalStudents,
                'premium_course_enrollments' => $premiumCourseEnrollments,
            ];
        });
    }

    private function loadUserGrowthData()
    {
        $this->userGrowthData = Cache::remember('user_growth_data', 60, function () {
            $months = collect(range(0, 11))->map(function ($i) {
                return Carbon::now()->subMonths($i)->format('Y-m');
            })->reverse();

            $userCounts = User::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
                ->where('created_at', '>=', Carbon::now()->subYear())
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $cumulativeUsers = [];
            $total = 0;
            foreach ($months as $month) {
                $count = $userCounts->where('month', $month)->first()['count'] ?? 0;
                $total += $count;
                $cumulativeUsers[] = ['month' => Carbon::createFromFormat('Y-m', $month)->format('M'), 'cumulative' => $total];
            }

            return $cumulativeUsers;
        });
    }

    private function loadCourseStatistics()
    {
        $this->courseStats = Cache::remember('course_stats', 60, function () {
            $freeCourses = Course::free()->count();
            $premiumCourses = Course::premium()->count();
            $publishedCourses = Course::published()->count();

            return [
                'total_published' => $publishedCourses,
                'free_courses' => $freeCourses,
                'premium_courses' => $premiumCourses,
            ];
        });
    }

    private function loadCertificateAnalytics()
    {
        $this->certificateStats = Cache::remember('certificate_analytics', 60, function () {
            $monthlyTrend = collect(range(0, 5))->map(function ($i) {
                $month = Carbon::now()->subMonths($i);
                $certificates = Certificate::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count();
                return ['month' => $month->format('M'), 'certificates' => $certificates];
            })->reverse()->values()->toArray();

            return [
                'monthly_trend' => $monthlyTrend,
                'top_certificate_holders' => Certificate::with('user')
                    ->select('user_id', DB::raw('count(*) as certificates_count'))
                    ->groupBy('user_id')
                    ->orderByDesc('certificates_count')
                    ->limit(5)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'name' => $item->user->name ?? 'N/A',
                            'certificates_count' => $item->certificates_count,
                        ];
                    })->toArray(),
            ];
        });
    }

    private function loadEngagementMetrics()
    {
        $this->engagementMetrics = Cache::remember('engagement_metrics', 60, function () {
            return [
                'daily_active_users' => CourseEnrollment::where('created_at', '>=', Carbon::now()->subDay())->distinct('user_id')->count('user_id'),
                'weekly_active_users' => CourseEnrollment::where('created_at', '>=', Carbon::now()->subWeek())->distinct('user_id')->count('user_id'),
                'monthly_active_users' => CourseEnrollment::where('created_at', '>=', Carbon::now()->subMonth())->distinct('user_id')->count('user_id'),
            ];
        });
    }

    private function loadGeographicData()
    {
        $this->geographicData = Cache::remember('geographic_data', 60, function () {
            // Get total user count for percentage calculation
            $totalUsers = User::count();

            // Correctly query the 'users' table using the 'address_country' column
            $countryData = DB::table('users')
                ->select('address_country as country', DB::raw('count(*) as users_count'))
                ->whereNotNull('address_country')
                ->groupBy('address_country')
                ->orderByDesc('users_count')
                ->get();

            // Format the data for the frontend
            $formattedData = [];
            foreach ($countryData as $data) {
                if ($totalUsers > 0) {
                    $percentage = ($data->users_count / $totalUsers) * 100;
                } else {
                    $percentage = 0;
                }

                $formattedData[$data->country] = [
                    'users' => $data->users_count,
                    'percentage' => number_format($percentage, 2),
                ];
            }

            return $formattedData;
        });
    }

    private function loadRevenueData()
    {
        $this->revenueData = Cache::remember('revenue_data', 60, function () {
            // Updated to join the courses table and filter by premium courses to get the revenue
            $totalRevenue = CourseEnrollment::join('courses', 'course_enrollments.course_id', '=', 'courses.id')
                ->where('courses.is_premium', true)
                ->sum('courses.price');

            $monthlyRecurring = CourseEnrollment::where('course_enrollments.created_at', '>=', Carbon::now()->subMonth())
                ->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
                ->where('courses.is_premium', true)
                ->sum('courses.price');

            $revenueByMonth = collect(range(0, 5))->map(function ($i) {
                $month = Carbon::now()->subMonths($i);
                // Updated to join the courses table and filter by premium courses
                $amount = CourseEnrollment::whereMonth('course_enrollments.created_at', $month->month)
                    ->whereYear('course_enrollments.created_at', $month->year)
                    ->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
                    ->where('courses.is_premium', true)
                    ->sum('courses.price');
                return ['month' => $month->format('M'), 'amount' => $amount];
            })->reverse()->values()->toArray();

            return [
                'total_revenue' => $totalRevenue,
                'monthly_recurring' => $monthlyRecurring,
                'revenue_by_month' => $revenueByMonth,
            ];
        });
    }

    private function loadTopPerformers()
    {
        $this->topPerformers = Cache::remember('top_performers', 60, function () {
            // Top students based on certificates
            $topStudents = User::withCount('certificates')
                ->where('role', User::ROLE_STUDENT)
                ->orderByDesc('certificates_count')
                ->limit(5)
                ->get()
                ->map(fn($user) => ['name' => $user->name, 'certificates_count' => $user->certificates_count])
                ->toArray();
            
            // Top instructors based on course count
            $topInstructors = User::withCount('courses')
                ->where('role', User::ROLE_INSTRUCTOR)
                ->orderByDesc('courses_count')
                ->limit(5)
                ->get()
                ->map(fn($user) => ['name' => $user->name, 'courses_count' => $user->courses_count])
                ->toArray();
                
            return [
                'top_students' => $topStudents,
                'top_instructors' => $topInstructors
            ];
        });
    }

    private function loadRecentActivity()
    {
        $this->recentActivity = Cache::remember('recent_activity', 60, function () {
            // Sample data - would be pulled from a log table
            return [
                ['message' => 'New user signed up from Nigeria.', 'time' => '10 minutes ago', 'icon' => 'fas fa-user-plus', 'color' => 'blue'],
                ['message' => 'Course "Introduction to Laravel" published.', 'time' => '1 hour ago', 'icon' => 'fas fa-book', 'color' => 'green'],
                ['message' => 'A new certificate was issued to a student.', 'time' => '2 hours ago', 'icon' => 'fas fa-award', 'color' => 'purple'],
                ['message' => 'New instructor joined the platform.', 'time' => '4 hours ago', 'icon' => 'fas fa-chalkboard-teacher', 'color' => 'orange'],
            ];
        });
    }

    private function loadTrendingCourses()
    {
        $this->trendingCourses = Cache::remember('trending_courses', 60, function () {
            // Fetch courses with their enrollments and average rating, ordering by recent enrollments
            return Course::withCount(['enrollments' => function($query) {
                $query->where('created_at', '>=', Carbon::now()->subWeek());
            }])
            ->orderByDesc('enrollments_count')
            ->limit(5)
            ->get()
            ->map(function ($course) {
                return [
                    'title' => $course->title,
                    'average_rating' => $course->average_rating ?? 'N/A',
                    'enrollments_count' => $course->enrollments_count,
                ];
            })
            ->toArray();
        });
    }

    private function loadInstructorLeaderboard()
    {
        $this->instructorLeaderboard = Cache::remember('instructor_leaderboard', 60, function () {
            return User::where('role', User::ROLE_INSTRUCTOR)
                ->withCount(['courses', 'reviews'])
                ->withAvg('reviews', 'rating')
                ->orderByDesc('courses_count')
                ->limit(5)
                ->get()
                ->map(function ($instructor) {
                    return [
                        'name' => $instructor->name,
                        'courses' => $instructor->courses_count,
                        'avg_rating' => number_format($instructor->reviews_avg_rating ?? 0, 2),
                    ];
                })
                ->toArray();
        });
    }

    private function loadCompletionRates()
    {
        $this->completionRates = Cache::remember('completion_rates', 60, function () {
            return [
                'overall_trend' => [
                    ['month' => 'Jan', 'rate' => rand(50, 70)],
                    ['month' => 'Feb', 'rate' => rand(50, 70)],
                    ['month' => 'Mar', 'rate' => rand(50, 70)],
                    ['month' => 'Apr', 'rate' => rand(50, 70)],
                    ['month' => 'May', 'rate' => rand(50, 70)],
                    ['month' => 'Jun', 'rate' => rand(50, 70)],
                ],
                'by_difficulty' => [
                    'Beginner' => rand(60, 80),
                    'Intermediate' => rand(40, 60),
                    'Advanced' => rand(20, 40),
                ],
                'by_duration' => [
                    'Under 2 hours' => rand(70, 90),
                    '2-5 hours' => rand(50, 70),
                    'Over 5 hours' => rand(30, 50),
                ],
            ];
        });
    }

    private function loadCategoryBreakdown()
    {
        $this->categoryBreakdown = Cache::remember('category_breakdown', 60, function () {
            return CourseCategory::withCount('courses')
                ->get()
                ->map(function ($category) {
                    $totalEnrollments = $category->courses->flatMap(function ($course) {
                        return $course->enrollments;
                    })->count();

                    return [
                        'name' => $category->name,
                        'courses' => $category->courses_count,
                        'enrollments' => $totalEnrollments,
                        'avg_rating' => rand(40, 50) / 10 // Would be calculated from actual ratings
                    ];
                })
                ->sortByDesc('courses')
                ->values()
                ->toArray();
        });
    }

    private function loadTimeBasedAnalytics()
    {
        $this->timeBasedAnalytics = [
            'peak_hours' => [
                ['hour' => '9 AM', 'activity' => 23],
                ['hour' => '10 AM', 'activity' => 45],
                ['hour' => '11 AM', 'activity' => 67],
                ['hour' => '2 PM', 'activity' => 89],
                ['hour' => '3 PM', 'activity' => 92],
                ['hour' => '4 PM', 'activity' => 78],
                ['hour' => '8 PM', 'activity' => 156],
                ['hour' => '9 PM', 'activity' => 134],
            ],
            'weekly_pattern' => [
                ['day' => 'Mon', 'users' => 234],
                ['day' => 'Tue', 'users' => 287],
                ['day' => 'Wed', 'users' => 312],
                ['day' => 'Thu', 'users' => 298],
                ['day' => 'Fri', 'users' => 189],
                ['day' => 'Sat', 'users' => 154],
                ['day' => 'Sun', 'users' => 167],
            ]
        ];
    }
}
