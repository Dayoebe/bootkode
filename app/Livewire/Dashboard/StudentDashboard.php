<?php

namespace App\Livewire\Dashboard;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.dashboard', ['title' => 'Student Dashboard'])]
class StudentDashboard extends Component
{
    public $lastAccessedCourse;
    public $inProgressCourses = [];
    public $completedCourses = [];
    public $recommendedCourses = [];
    public $upcomingAssignments = [];
    public $recentActivity = [];

    public function mount()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user->isStudent()) {
            redirect()->route($user->getDashboardRouteName());
        }

        // Get all enrolled courses with progress
        $enrolledCourses = $user->courses()
            ->with(['sections.lessons', 'assignments'])
            ->get()
            ->map(function ($course) use ($user) {
                // Calculate progress through sections and lessons
                $totalLessons = $course->sections->flatMap->lessons->count();
                $completedLessons = $user->completedLessons()
                    ->whereIn('lesson_id', $course->sections->flatMap->lessons->pluck('id'))
                    ->count();
                
                $progress = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;
                
                $course->progress = round($progress, 0);
                $course->totalLessons = $totalLessons;
                $course->completedLessons = $completedLessons;
                
                return $course;
            });

        // Last accessed course
        $this->lastAccessedCourse = $user->courses()
            ->orderByPivot('last_accessed_at', 'desc')
            ->first();

        // Separate courses into in-progress and completed
        $this->inProgressCourses = $enrolledCourses->filter(fn($course) => $course->progress < 100);
        $this->completedCourses = $enrolledCourses->filter(fn($course) => $course->progress >= 100);

        // Recommended courses (not yet enrolled)
        $this->recommendedCourses = Course::whereNotIn('id', $enrolledCourses->pluck('id'))
            ->where('is_published', true)
            ->where('is_approved', true)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        // Upcoming assignments (due in next 7 days)
        $this->upcomingAssignments = $user->courses()
            ->with(['assignments' => function($query) {
                $query->whereBetween('due_date', [now(), now()->addDays(7)])
                      ->orderBy('due_date');
            }])
            ->get()
            ->flatMap->assignments;

        // Recent activity (completed lessons in last 7 days)
        $this->recentActivity = $user->completedLessons()
            ->with(['section.course'])
            ->wherePivot('completed_at', '>=', now()->subDays(7))
            ->orderByPivot('completed_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard.student-dashboard');
    }
}