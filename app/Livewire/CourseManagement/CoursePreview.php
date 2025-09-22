<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\User;
use App\Models\Section;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard', ['title' => 'courses.preview', 'description' => 'Preview course details and content structure', 'icon' => 'fas fa-eye', 'active' => 'courses.preview'])]
class CoursePreview extends Component
{
    public Course $course;
    public $activeTab = 'overview';
    public $isEnrolled = false;
    public $enrollmentProgress = 0;
    public $userReview = null;
    public $showEnrollModal = false;
    public $showReviewModal = false;
    public $reviewRating = 5;
    public $reviewComment = '';
    
    // Course statistics
    public $totalLessons = 0;
    public $totalDuration = 0;
    public $completionRate = 0;
    public $averageRating = 0;
    public $totalReviews = 0;
    public $totalEnrollments = 0;
    
    protected $listeners = [
        'courseEnrolled' => 'refreshEnrollmentStatus',
        'reviewSubmitted' => 'refreshReviews'
    ];

    public function mount(Course $course)
    {
        $this->course = $course->load([
            'category', 
            'instructor', 
            'sections.lessons', 
            'reviews.user',
            'enrollments' => function($query) {
                $query->where('user_id', Auth::id());
            }
        ]);
        
        $this->checkEnrollmentStatus();
        $this->calculateStatistics();
        $this->loadUserReview();
    }

    private function checkEnrollmentStatus()
    {
        if (Auth::check()) {
            $enrollment = CourseEnrollment::where('course_id', $this->course->id)
                ->where('user_id', Auth::id())
                ->first();
                
            $this->isEnrolled = !is_null($enrollment);
            $this->enrollmentProgress = $enrollment ? $enrollment->progress_percentage : 0;
        }
    }

    private function calculateStatistics()
    {
        // Calculate total lessons and duration
        $this->totalLessons = $this->course->sections->sum(function ($section) {
            return $section->lessons->count();
        });

        $this->totalDuration = $this->course->sections->sum(function ($section) {
            return $section->lessons->sum('duration_minutes');
        });

        // Get review statistics
        $reviews = $this->course->reviews;
        $this->totalReviews = $reviews->count();
        $this->averageRating = $reviews->avg('rating') ?: 0;
        $this->totalEnrollments = $this->course->enrollments()->count();
        
        // Calculate completion rate (placeholder - you might want to implement this based on your business logic)
        $completedEnrollments = $this->course->enrollments()->where('is_completed', true)->count();
        $this->completionRate = $this->totalEnrollments > 0 
            ? ($completedEnrollments / $this->totalEnrollments) * 100 
            : 0;
    }

    private function loadUserReview()
    {
        if (Auth::check()) {
            $this->userReview = $this->course->reviews()
                ->where('user_id', Auth::id())
                ->first();
                
            if ($this->userReview) {
                $this->reviewRating = $this->userReview->rating;
                $this->reviewComment = $this->userReview->review_text;
            }
        }
    }

    public function enroll()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            // Check if already enrolled
            if ($this->isEnrolled) {
                $this->dispatch('notify', [
                    'message' => 'You are already enrolled in this course!',
                    'type' => 'info'
                ]);
                return;
            }

            CourseEnrollment::create([
                'course_id' => $this->course->id,
                'user_id' => Auth::id(),
                'enrolled_at' => now(),
                'progress_percentage' => 0,
                'is_completed' => false
            ]);

            $this->isEnrolled = true;
            $this->totalEnrollments++;
            $this->showEnrollModal = false;

            $this->dispatch('notify', [
                'message' => "Successfully enrolled in '{$this->course->title}'!",
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Enrollment failed. Please try again.',
                'type' => 'error'
            ]);
        }
    }

    public function submitReview()
    {
        $this->validate([
            'reviewRating' => 'required|integer|between:1,5',
            'reviewComment' => 'required|string|min:10|max:1000'
        ]);

        try {
            if ($this->userReview) {
                // Update existing review
                $this->userReview->update([
                    'rating' => $this->reviewRating,
                    'review_text' => $this->reviewComment
                ]);
            } else {
                // Create new review
                $this->course->reviews()->create([
                    'user_id' => Auth::id(),
                    'rating' => $this->reviewRating,
                    'review_text' => $this->reviewComment
                ]);
            }

            $this->showReviewModal = false;
            $this->loadUserReview();
            $this->calculateStatistics();

            $this->dispatch('notify', [
                'message' => 'Review submitted successfully!',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Failed to submit review. Please try again.',
                'type' => 'error'
            ]);
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    #[On('courseEnrolled')]
    public function refreshEnrollmentStatus()
    {
        $this->checkEnrollmentStatus();
        $this->calculateStatistics();
    }

    #[On('reviewSubmitted')]  
    public function refreshReviews()
    {
        $this->course->refresh();
        $this->calculateStatistics();
        $this->loadUserReview();
    }

    public function getFormattedDurationProperty()
    {
        if ($this->totalDuration < 60) {
            return $this->totalDuration . 'm';
        }
        
        $hours = floor($this->totalDuration / 60);
        $minutes = $this->totalDuration % 60;
        
        return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
    }

    public function canEnroll()
    {
        return Auth::check() && 
               !$this->isEnrolled && 
               $this->course->is_published && 
               $this->course->is_approved;
    }

    public function canReview()
    {
        return Auth::check() && 
               $this->isEnrolled && 
               $this->enrollmentProgress >= 25; // Can review after 25% completion
    }

    public function render()
    {
        return view('livewire.course-management.course-preview');
    }
}