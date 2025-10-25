<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use App\Models\Learning\Course;
use App\Services\ReviewAnalyticsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Review Analytics',
    'description' => 'Detailed analytics and insights from course reviews',
    'icon' => 'fas fa-chart-line',
    'active' => 'review-analytics'
])]
class ReviewAnalytics extends Component
{
    public $courseId = null;
    public $timeRange = 30; // days
    public $selectedMetric = 'rating';

    public function mount($courseId = null)
    {
        $this->courseId = $courseId;
    }

    public function render(ReviewAnalyticsService $analyticsService)
    {
        $user = Auth::user();
        
        // Get courses based on user role
        if ($user->hasRole('super_admin')) {
            $courses = Course::whereHas('reviews')->get();
        } else {
            $courses = Course::where('instructor_id', $user->id)
                ->whereHas('reviews')
                ->get();
        }

        // If no course selected, use first available
        if (!$this->courseId && $courses->isNotEmpty()) {
            $this->courseId = $courses->first()->id;
        }

        $selectedCourse = $this->courseId ? Course::find($this->courseId) : null;
        
        $data = [
            'courses' => $courses,
            'selectedCourse' => $selectedCourse,
            'ratingTrends' => null,
            'instructorMetrics' => null,
            'satisfactionMetrics' => null,
            'theme' => session('theme', 'light'),
        ];

        if ($selectedCourse) {
            $data['ratingTrends'] = $analyticsService->getRatingTrends($selectedCourse, $this->timeRange);
            $data['instructorMetrics'] = $analyticsService->getInstructorMetrics($selectedCourse);
            $data['satisfactionMetrics'] = $analyticsService->getSatisfactionMetrics($selectedCourse, $this->timeRange);
        }

        return view('livewire.course-management.review-analytics', $data);
    }
}