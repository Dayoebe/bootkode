<?php

namespace App\Livewire\Component\CourseManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CourseReview;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.dashboard', ['title' => 'Course Reviews', 'description' => 'Manage course reviews including viewing and responding to student feedback', 'icon' => 'fas fa-star', 'active' => 'instructor.course-reviews'])]
class CourseReviews extends Component
{
    use WithPagination;

    public $search = '';

    /**
     * Resets the pagination when the search term changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Render the component view with reviews.
     */
    public function render()
    {
        $user = Auth::user();

        // Get reviews for all courses if the user is an admin
        // Otherwise, get reviews for courses taught by the current instructor
        $reviews = CourseReview::query()
            ->when($user->isInstructor(), function ($query) use ($user) {
                // Filter reviews for courses taught by the current instructor
                $query->whereHas('course', function ($subQuery) use ($user) {
                    $subQuery->where('instructor_id', $user->id);
                });
            })
            ->when($this->search, function ($query) {
                // Search by review text or course title
                $query->where('review_text', 'like', '%' . $this->search . '%')
                    ->orWhereHas('course', function ($subQuery) {
                        $subQuery->where('title', 'like', '%' . $this->search . '%');
                    });
            })
            ->with(['user', 'course']) // Eager load relationships to prevent N+1 queries
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.component.course-management.course-reviews', [
            'reviews' => $reviews,
        ]);
    }
}