<?php

namespace App\Livewire\Component\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'All Courses', 'description' => 'Manage all courses including creation, editing, and deletion', 'icon' => 'fas fa-book', 'active' => 'admin.all-courses'])]

class ViewProfile extends Component

{
    public $user;
    public $activeTab = 'personal';
    protected $listeners = ['refresh' => '$refresh'];
    public function mount()
    {
        $this->user = Auth::user();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }
    public function getLearningProgressProperty()
    {
        $user = $this->user;

        return [
            'total_courses' => $user->courses()->count(),
            'completed_lessons' => $user->completedLessons()->count(),
            'wishlist_items' => $user->wishlists()->count(),
            'saved_resources' => $user->savedResources()->count(),
            'offline_notes' => $user->offlineNotes()->count(),
            'average_weekly_progress' => $this->calculateWeeklyAverage(),
            'offline_content_size_mb' => $user->offline_content_size_mb,
            // 'reviews' => $user->reviews()->count(), // Uncomment if reviews are implemented
            'downloaded_content' => $user->downloadedContent()->count(),
            'recent_activities' => $this->getRecentActivitiesProperty(),
            'wishlist' => $this->getWishlistProperty(),
            'notes' => $this->getNotesProperty(),
            'activity_stats' => $this->getActivityStatsProperty(),
            'completed_assignments' => $user->completedLessons()->count(), 
        ];
    }

    public function getActivityStatsProperty()
    {
        $user = $this->user;
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();

        return [
            'courses_enrolled' => [
                'total' => $user->courses()->count(),
                'this_month' => $user->courses()
                    ->where('course_user.created_at', '>=', $startOfMonth)
                    ->count(),
            ],
            'lessons_completed' => [
                'total' => $user->completedLessons()->count(),
                'this_month' => $user->completedLessons()
                    ->where('lesson_user.completed_at', '>=', $startOfMonth)
                    ->count(),
            ],
        ];
    }

    public function getRecentActivitiesProperty()
    {
        return $this->user->completedLessons()
            ->with(['module.course']) // Load course through module
            ->orderByDesc('lesson_user.completed_at')
            ->take(5)
            ->get()
            ->map(function ($lesson) {
                return [
                    'title' => 'Completed lesson: ' . $lesson->title,
                    'course' => $lesson->module->course->title ?? 'Unknown Course',
                    'date' => $lesson->pivot->completed_at,
                    'icon' => 'check-circle',
                    'color' => 'green-400',
                ];
            });
    }

    protected function calculateWeeklyAverage()
    {
        $user = $this->user;
        $completedLessons = $user->completedLessons()
            ->withPivot('completed_at')
            ->get();

        if ($completedLessons->isEmpty()) {
            return 0;
        }

        $firstCompletion = $completedLessons->min('pivot.completed_at');
        $weeks = max(1, Carbon::parse($firstCompletion)->diffInWeeks(now()));

        return round($completedLessons->sum('duration_minutes') / 60 / $weeks, 1);
    }


    public function getSavedResourcesProperty()
    {
        return $this->user->savedResources()
            ->with(['resourceable', 'course'])
            ->latest()
            ->take(5)
            ->get();
    }
    public function getWishlistProperty()
    {
        return $this->user->wishlists()
            ->with('course.category')
            ->latest()
            ->take(5)
            ->get();
    }
    // In ViewProfile.php
    public function getNotesProperty()
    {
        return $this->user->offlineNotes()
            ->with('course')
            ->latest()
            ->take(5)
            ->get();
    }
    // In ViewProfile.php
    // public function getReviewsProperty()
    // {
    //     return $this->user->reviews()
    //         ->with('course')
    //         ->latest()
    //         ->take(5)
    //         ->get();
    // }
    public function render()
    {
        return view('livewire.component.profile.view', [
            'activityStats' => $this->activityStats,
            'recentActivities' => $this->recentActivities,
            'learningProgress' => $this->learningProgress,
            'savedResources' => $this->savedResources,
            'wishlist' => $this->wishlist,
            'notes' => $this->notes,
            // 'reviews' => $this->reviews,
        ])->layout('layouts.dashboard', [
                    'title' => 'View Profile'
                ]);
    }
}