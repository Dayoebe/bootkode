<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\CourseRejection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

#[Layout('layouts.dashboard', ['title' => 'Course Approvals', 'description' => 'Manage course approvals including approving and rejecting courses', 'icon' => 'fas fa-check-circle', 'active' => 'admin.course-approvals'])]
class CourseApprovals extends Component
{
    use WithPagination;

    public $search = '';
    public $isApproveModalOpen = false;
    public $isRejectModalOpen = false;
    public $currentCourseId = null;

    #[Rule('required|string|max:1000')]
    public $rejectionReason = '';



    public function render()
    {
        $currentPage = $this->getPage();
        $cacheKey = 'course_approvals_paginated_' . md5($this->search . $currentPage);
    
        $courses = Cache::remember($cacheKey, 600, function () {
            return Course::query()
                ->where('is_approved', false)
                ->where('is_published', false)
                ->when($this->search, function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('subtitle', 'like', '%' . $this->search . '%');
                })
                ->with('instructor')
                ->orderBy('created_at', 'asc')
                ->paginate(10);
        });
    
        return view('livewire.course-management.course-approvals', [
            'courses' => $courses,
        ]);
    }
    
    public function approveCourse()
    {
        if (!$this->canManageCourses()) {
            $this->flashMessage('You are not authorized to approve courses.', 'error');
            return;
        }
    
        try {
            $course = Course::findOrFail($this->currentCourseId);
            $course->update([
                'is_approved' => true,
                'is_published' => true,
            ]);
    
            $this->clearCache();
            $this->flashMessage('Course approved successfully.');
            $this->isApproveModalOpen = false;
            $this->resetPage();
            $this->dispatch('course-updated');
        } catch (\Exception $e) {
            $this->flashMessage('Error approving course: ' . $e->getMessage(), 'error');
        }
    }
    
    public function rejectCourse()
    {
        if (!$this->canManageCourses()) {
            $this->flashMessage('You are not authorized to reject courses.', 'error');
            return;
        }
    
        $this->validate();
    
        try {
            $course = Course::findOrFail($this->currentCourseId);
            $course->update([
                'is_approved' => false,
                'is_published' => false,
            ]);
    
            CourseRejection::create([
                'course_id' => $this->currentCourseId,
                'user_id' => Auth::id(),
                'reason' => strip_tags($this->rejectionReason),
            ]);
    
            $this->clearCache();
            $this->flashMessage('Course rejected successfully.');
            $this->isRejectModalOpen = false;
            $this->rejectionReason = '';
            $this->resetPage();
            $this->dispatch('course-updated');
        } catch (\Exception $e) {
            $this->flashMessage('Error rejecting course: ' . $e->getMessage(), 'error');
        }
    }





    /**
     * Resets the pagination when the search term changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Open approve confirmation modal.
     */
    public function openApproveModal($courseId)
    {
        if (!$this->canManageCourses()) {
            $this->flashMessage('You are not authorized to approve courses.', 'error');
            return;
        }
        $this->currentCourseId = $courseId;
        $this->isApproveModalOpen = true;
    }

    /**
     * Open reject modal with reason input.
     */
    public function openRejectModal($courseId)
    {
        if (!$this->canManageCourses()) {
            $this->flashMessage('You are not authorized to reject courses.', 'error');
            return;
        }
        $this->currentCourseId = $courseId;
        $this->rejectionReason = '';
        $this->isRejectModalOpen = true;
    }

   
    /**
     * Close modals and reset fields.
     */
    public function closeModal()
    {
        $this->isApproveModalOpen = false;
        $this->isRejectModalOpen = false;
        $this->rejectionReason = '';
        $this->currentCourseId = null;
        $this->resetValidation();
    }

   
    /**
     * Check if the user can manage courses.
     */
    private function canManageCourses()
    {
        $user = Auth::user();
        return $user && $user->hasAnyRole(['super_admin', 'academy_admin']);
    }

    /**
     * Clear cache for all pages and search terms.
     */
    private function clearCache()
    {
        foreach (range(1, 10) as $page) {
            Cache::forget('course_approvals_paginated_' . md5($this->search . $page));
            Cache::forget('course_approvals_paginated_' . md5('' . $page));
        }
    }

    /**
     * Centralized flash message handler.
     */
    private function flashMessage(string $message, string $type = 'success')
    {
        session()->flash($type === 'success' ? 'message' : 'error', $message);
    }
}