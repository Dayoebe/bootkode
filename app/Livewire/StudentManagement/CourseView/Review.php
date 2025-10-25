<?php

namespace App\Livewire\StudentManagement\CourseView;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Learning\Course;
use App\Models\Learning\CourseReview;
use Illuminate\Support\Facades\Auth;

class Review extends Component
{
    use WithPagination;

    public Course $course;
    public $rating = 5;
    public $comment = '';
    public $showReviewForm = false;
    public $isCollapsed = false; // New collapse state
    
    // Simplified filters
    public $filterRating = '';
    public $sortBy = 'recent';
    public $searchQuery = '';
    
    public $editingReview = null;

    protected $rules = [
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:10|max:1000'
    ];

    protected $queryString = [
        'filterRating' => ['except' => ''],
        'sortBy' => ['except' => 'recent'],
        'searchQuery' => ['except' => ''],
    ];

    public function mount(Course $course)
    {
        $this->course = $course;
        $this->checkExistingReview();
    }

    public function checkExistingReview()
    {
        if (Auth::check()) {
            $existing = $this->course->reviews()
                ->where('user_id', Auth::id())
                ->first();
            
            if ($existing) {
                $this->editingReview = $existing;
                $this->rating = $existing->rating;
                $this->comment = $existing->review_text;
                $this->showReviewForm = false;
            }
        }
    }

    public function toggleCollapse()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function toggleReviewForm()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!$this->canReview()) {
            $this->dispatch('notify', [
                'message' => 'You must be enrolled in this course to leave a review.',
                'type' => 'error'
            ]);
            return;
        }

        $this->showReviewForm = !$this->showReviewForm;
        
        if ($this->showReviewForm && $this->editingReview) {
            $this->rating = $this->editingReview->rating;
            $this->comment = $this->editingReview->review_text;
        }
    }

    public function canReview()
    {
        return Auth::check() && Auth::user()->enrollments()
            ->where('course_id', $this->course->id)
            ->exists();
    }

    public function submitReview()
    {
        if (!$this->canReview()) {
            $this->dispatch('notify', [
                'message' => 'You must be enrolled to review this course.',
                'type' => 'error'
            ]);
            return;
        }

        $this->validate();

        try {
            if ($this->editingReview) {
                $this->editingReview->update([
                    'rating' => $this->rating,
                    'comment' => $this->comment,
                    'review_text' => $this->comment
                ]);
                $message = 'Your review has been updated successfully!';
            } else {
                CourseReview::create([
                    'course_id' => $this->course->id,
                    'user_id' => Auth::id(),
                    'rating' => $this->rating,
                    'comment' => $this->comment,
                    'review_text' => $this->comment,
                    'is_approved' => true
                ]);
                $message = 'Thank you for your review!';
            }

            $this->reset(['rating', 'comment', 'showReviewForm']);
            $this->rating = 5;
            $this->checkExistingReview();

            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Failed to submit review. Please try again.',
                'type' => 'error'
            ]);
        }
    }

    public function deleteReview($reviewId)
    {
        $review = CourseReview::find($reviewId);
        
        if ($review && $review->user_id === Auth::id()) {
            $review->delete();
            
            $this->reset(['editingReview', 'rating', 'comment']);
            $this->rating = 5;
            $this->showReviewForm = false;
            
            $this->dispatch('notify', [
                'message' => 'Your review has been deleted.',
                'type' => 'success'
            ]);
        }
    }

    public function resetFilters()
    {
        $this->reset(['filterRating', 'sortBy', 'searchQuery']);
        $this->resetPage();
    }

    public function render()
    {
        $reviews = $this->course->reviews()
            ->approved()
            ->with(['user'])
            ->when($this->searchQuery, fn($q) => $q->where('comment', 'like', "%{$this->searchQuery}%"))
            ->when($this->filterRating, fn($q) => $q->where('rating', $this->filterRating))
            ->when($this->sortBy === 'recent', fn($q) => $q->latest())
            ->when($this->sortBy === 'rating_high', fn($q) => $q->orderBy('rating', 'desc'))
            ->when($this->sortBy === 'rating_low', fn($q) => $q->orderBy('rating', 'asc'))
            ->paginate(10);

        $ratingDistribution = $this->course->getRatingDistribution();
        $totalReviews = $this->course->getReviewsCount();
        $verifiedCount = $this->course->reviews()
            ->approved()
            ->whereHas('user.enrollments', function($q) {
                $q->where('course_id', $this->course->id)
                  ->where('is_completed', true);
            })->count();

        return view('livewire.student-management.course-view.review', [
            'reviews' => $reviews,
            'ratingDistribution' => $ratingDistribution,
            'totalReviews' => $totalReviews,
            'verifiedCount' => $verifiedCount,
            'averageRating' => $this->course->average_rating ?? 0,
            'instructorResponseRate' => $this->course->getInstructorResponseRate(),
            'averageResponseTime' => $this->course->getAverageResponseTime()
        ]);
    }
}