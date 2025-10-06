<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CourseReview;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.dashboard', ['title' => 'Course Reviews', 'description' => 'Manage course reviews including viewing and responding to student feedback', 'icon' => 'fas fa-star', 'active' => 'course-reviews'])]
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

    public function updatingSearch()
    {
        $this->resetPage();
    }

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
            ->with(['user', 'course.instructor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.course-management.course-reviews', [
            'reviews' => $reviews,
        ]);
    }

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

    public function saveReply()
    {
        if (!$this->canManageReview()) {
            $this->flashMessage('You are not authorized to reply to reviews.', 'error');
            return;
        }
    
        $this->validate();
    
        try {
            $review = CourseReview::findOrFail($this->currentReviewId);
            
            // Verify instructor owns this course
            if ($review->course->instructor_id !== Auth::id() && !Auth::user()->hasAnyRole(['super_admin', 'academy_admin'])) {
                $this->flashMessage('You are not authorized to reply to this review.', 'error');
                return;
            }
    
            // Update the review with instructor reply
            $review->update([
                'instructor_reply' => $this->replyText,
                'replied_at' => now()
            ]);
    
            // Send email notification to student
            $review->user->notify(new \App\Notifications\InstructorReplyNotification($review));
    
            $this->flashMessage('Reply sent to student successfully!');
            $this->closeModal();
        } catch (\Exception $e) {
            $this->flashMessage('Error: ' . $e->getMessage(), 'error');
        }
    }

    public function confirmDelete($reviewId)
    {
        if (!$this->canManageReview()) {
            $this->flashMessage('You are not authorized to delete reviews.', 'error');
            return;
        }

        $this->reviewToDelete = $reviewId;
        $this->isDeleteModalOpen = true;
    }

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
        } catch (\Exception $e) {
            $this->flashMessage('Error: ' . $e->getMessage(), 'error');
        }
    }

    public function closeModal()
    {
        $this->isReplyModalOpen = false;
        $this->isDeleteModalOpen = false;
        $this->replyText = '';
        $this->currentReviewId = null;
        $this->resetValidation();
    }

    private function canManageReview()
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }
        return $user->hasAnyRole(['super_admin', 'academy_admin']) || $user->isInstructor();
    }

    private function flashMessage(string $message, string $type = 'success')
    {
        session()->flash($type === 'success' ? 'message' : 'error', $message);
    }
}