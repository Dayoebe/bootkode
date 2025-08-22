<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CourseReview;
use App\Models\ReviewReply;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

#[Layout('layouts.dashboard', ['title' => 'Course Reviews', 'description' => 'Manage course reviews including viewing and responding to student feedback', 'icon' => 'fas fa-star', 'active' => 'instructor.course-reviews'])]
class CourseReviews extends Component
{
    use WithPagination;

    public $search = '';
    public $isReplyModalOpen = false;
    public $isDeleteModalOpen = false;
    public $currentReviewId = null;
    public $reviewToDelete = null;

    #[Rule('required|string|max:1000')]
    public $replyText = '';

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

        $reviews = CourseReview::query()
            ->when($user->isInstructor() && !$user->hasAnyRole(['super_admin', 'academy_admin']), function ($query) use ($user) {
                $query->whereHas('course', function ($subQuery) use ($user) {
                    $subQuery->where('instructor_id', $user->id);
                });
            })
            ->when($this->search, function ($query) {
                $query->where('comment', 'like', '%' . $this->search . '%')
                    ->orWhereHas('course', function ($subQuery) {
                        $subQuery->where('title', 'like', '%' . $this->search . '%');
                    });
            })
            ->with(['user', 'course.instructor', 'replies.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.course-management.course-reviews', [
            'reviews' => $reviews,
        ]);
    }

    /**
     * Open reply modal for a review.
     */
    public function openReplyModal($reviewId)
    {
        if (!$this->canManageReview()) {
            $this->flashMessage('You are not authorized to reply to reviews.', 'error');
            return;
        }

        $this->currentReviewId = $reviewId;
        $this->replyText = '';
        $this->isReplyModalOpen = true;
    }

    /**
     * Save a reply to a review.
     */
    public function saveReply()
    {
        if (!$this->canManageReview()) {
            $this->flashMessage('You are not authorized to reply to reviews.', 'error');
            return;
        }

        $this->validate();

        try {
            ReviewReply::create([
                'review_id' => $this->currentReviewId,
                'user_id' => Auth::id(),
                'reply_text' => strip_tags($this->replyText),
            ]);

            $this->flashMessage('Reply added successfully.');
            $this->isReplyModalOpen = false;
            $this->replyText = '';
            $this->dispatch('review-updated');
        } catch (\Exception $e) {
            $this->flashMessage('Error saving reply: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Open delete confirmation modal.
     */
    public function confirmDelete($reviewId)
    {
        if (!$this->canManageReview()) {
            $this->flashMessage('You are not authorized to delete reviews.', 'error');
            return;
        }

        $this->reviewToDelete = $reviewId;
        $this->isDeleteModalOpen = true;
    }

    /**
     * Delete a review.
     */
    public function delete()
    {
        if (!$this->canManageReview()) {
            $this->flashMessage('You are not authorized to delete reviews.', 'error');
            return;
        }

        try {
            $review = CourseReview::findOrFail($this->reviewToDelete);
            $review->delete();

            $this->flashMessage('Review deleted successfully.');
            $this->isDeleteModalOpen = false;
            $this->reviewToDelete = null;
            $this->resetPage();
            $this->dispatch('review-updated');
        } catch (\Exception $e) {
            $this->flashMessage('Error deleting review: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Close modals and reset fields.
     */
    public function closeModal()
    {
        $this->isReplyModalOpen = false;
        $this->isDeleteModalOpen = false;
        $this->replyText = '';
        $this->currentReviewId = null;
        $this->resetValidation();
    }

    /**
     * Check if the user can manage reviews (admin or instructor of the course).
     */
    private function canManageReview()
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        return $user->hasAnyRole(['super_admin', 'academy_admin']) || $user->isInstructor();
    }

    /**
     * Centralized flash message handler.
     */
    private function flashMessage(string $message, string $type = 'success')
    {
        session()->flash($type === 'success' ? 'message' : 'error', $message);
    }
}