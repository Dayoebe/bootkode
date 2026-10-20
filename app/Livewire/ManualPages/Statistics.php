<?php

namespace App\Livewire\ManualPages;

use App\Models\Core\User;
use App\Models\Credentials\Certificate;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\CourseEnrollment;
use App\Services\PublicCatalogService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Platform statistics'])]
class Statistics extends Component
{
    public function render(PublicCatalogService $catalog)
    {
        $stats = $catalog->statistics();
        $enrollments = CourseEnrollment::count();
        $issued = Certificate::approved()->count();
        $allCourses = Course::count();
        $start = now()->startOfMonth()->subMonths(11);
        $growth = User::where('created_at', '>=', $start)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->groupBy('month')->pluck('total', 'month');
        $cumulative = User::where('created_at', '<', $start)->count();
        $points = [];
        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);
            $cumulative += (int) ($growth[$month->format('Y-m')] ?? 0);
            $points[] = ['month' => $month->format('M'), 'cumulative' => $cumulative];
        }

        return view('livewire.manual-pages.statistics', [
            'overviewStats' => [
                'total_users' => User::count(), 'published_courses' => $stats['courses'],
                'total_lessons' => $stats['lessons'], 'certificates_issued' => $issued,
                'course_approval_rate' => $allCourses ? round(Course::where('is_approved', true)->count() / $allCourses * 100, 1) : 0,
                'avg_completion_rate' => $enrollments ? round($issued / $enrollments * 100, 1) : 0,
            ],
            'courseStats' => [
                'total_published' => $stats['courses'],
                'free_courses' => Course::publiclyAvailable()->where('price', '<=', 0)->count(),
                'premium_courses' => Course::publiclyAvailable()->where('price', '>', 0)->count(),
            ],
            'engagementMetrics' => [
                'daily_active_users' => User::where('last_login_at', '>=', now()->startOfDay())->count(),
                'weekly_active_users' => User::where('last_login_at', '>=', now()->startOfWeek())->count(),
                'monthly_active_users' => User::where('last_login_at', '>=', now()->startOfMonth())->count(),
            ],
            'categoryBreakdown' => CourseCategory::whereHas('courses', fn ($q) => $q->publiclyAvailable())
                ->withCount(['courses' => fn ($q) => $q->publiclyAvailable()])->orderByDesc('courses_count')->limit(6)
                ->get()->map(fn ($category) => ['name' => $category->name, 'courses' => $category->courses_count])->all(),
            'trendingCourses' => Course::publiclyAvailable()->withCount(['enrollments' => fn ($q) => $q->where('created_at', '>=', now()->subWeek())])
                ->withAvg('reviews', 'rating')->orderByDesc('enrollments_count')->limit(5)->get()
                ->map(fn ($course) => ['title' => $course->title, 'average_rating' => $course->reviews_avg_rating !== null ? round($course->reviews_avg_rating, 1) : 'No ratings', 'enrollments_count' => $course->enrollments_count])->all(),
            'userGrowthData' => $points,
        ]);
    }
}
