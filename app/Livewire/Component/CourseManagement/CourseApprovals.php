<?php

namespace App\Livewire\Component\CourseManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Course Approvals', 'description' => 'Manage course approvals including approving and rejecting courses', 'icon' => 'fas fa-check-circle', 'active' => 'admin.course-approvals'])]
class CourseApprovals extends Component
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
     * Approve a pending course.
     */
    public function approveCourse(Course $course)
    {
        $course->update([
            'status' => 'approved',
            'is_published' => true, // Automatically publish approved courses
        ]);
        $this->dispatch('notify', 'Course approved successfully!', 'success');
    }

    /**
     * Reject a pending course.
     */
    public function rejectCourse(Course $course)
    {
        $course->update([
            'status' => 'rejected',
            'is_published' => false, // Ensure rejected courses are not published
        ]);
        $this->dispatch('notify', 'Course rejected successfully!', 'error');
    }

    /**
     * Render the component view with pending courses.
     */
    public function render()
    {
        $courses = Course::query()
            ->where('status', 'pending')
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('subtitle', 'like', '%' . $this->search . '%');
            })
            ->with('instructor') // Eager load the instructor
            ->orderBy('created_at', 'asc') // Show oldest first
            ->paginate(10);

        return view('livewire.component.course-management.course-approvals', [
            'courses' => $courses,
        ]);
    }
}