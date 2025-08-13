<?php

namespace App\Livewire\Component\CourseManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\CourseCategory;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'All Courses', 'description' => 'Manage all courses including creation, editing, and deletion', 'icon' => 'fas fa-book', 'active' => 'admin.all-courses'])]
class AllCourses extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';
    public $difficultyFilter = '';

    public array $selectedCourses = [];
    public bool $selectAll = false;

    /**
     * Refactored: Centralized method to build the course query.
     */
    private function getCoursesQuery(): Builder
    {
        $user = Auth::user();

        return Course::query()
            ->when($user->hasRole('instructor'), function ($query) use ($user) {
                $query->where('instructor_id', $user->id);
            })
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'published') {
                    $query->where('is_published', true);
                } elseif ($this->statusFilter === 'unpublished') {
                    $query->where('is_published', false);
                } elseif ($this->statusFilter === 'approved') {
                    $query->where('status', 'approved');
                } elseif ($this->statusFilter === 'unapproved') {
                    $query->where('status', '!=', 'approved');
                }
            })
            ->when($this->difficultyFilter, function ($query) {
                $query->where('difficulty_level', $this->difficultyFilter);
            });
    }
    /**
     * Toggles the 'selectAll' checkbox and selects all courses on the current page.
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedCourses = $this->getCoursesQuery()
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedCourses = [];
        }
    }

    /**
     * Updates the 'selectAll' checkbox based on the 'selectedCourses' array.
     */
    public function updatedSelectedCourses()
    {
        $coursesOnPageCount = $this->getCoursesQuery()->count();
        if ($coursesOnPageCount > 0 && count($this->selectedCourses) === $coursesOnPageCount) {
            $this->selectAll = true;
        } else {
            $this->selectAll = false;
        }
    }

    /**
     * Listen for the 'course-updated' event to refresh the table.
     */
    #[On('course-updated')]
    public function refreshList()
    {
        $this->resetPage();
    }

    /**
     * Resets the pagination when a filter or search term changes.
     */
    public function updating($key)
    {
        if (in_array($key, ['search', 'categoryFilter', 'statusFilter', 'difficultyFilter'])) {
            $this->resetPage();
        }
    }

    /**
     * Toggles the published status of a course.
     */
    public function togglePublished(Course $course)
    {
        $course->is_published = !$course->is_published;
        $course->save();

        $status = $course->is_published ? 'published' : 'unpublished';
        $this->dispatch('notify', "Course status toggled to '{$status}'!", 'success');
    }

    /**
     * Toggles the approved status of a course.
     */
    public function toggleApproved(Course $course)
    {
        if ($course->status === 'approved') {
            $course->update(['status' => 'pending']);
            $this->dispatch('notify', 'Course status changed to pending.', 'warning');
        } else {
            $course->update(['status' => 'approved']);
            $this->dispatch('notify', 'Course approved successfully!', 'success');
        }
    }

    /**
     * Redirects to the dedicated edit course page.
     */
    public function publishCourse($courseId)
    {
        $course = Course::findOrFail($courseId);
        $course->update(['is_published' => !$course->is_published]);
        if ($course->is_published) {
            // Notify enrolled users
            foreach ($course->enrollments as $user) {
                $user->notify(new \App\Notifications\CourseUpdateNotification($course));
            }
        }
        $this->dispatch('notify', 'Course publish status updated successfully!', 'success');
    }
    public function editCourse(Course $course)
    {
        return $this->redirect(route('edit-course', ['course' => $course->id]));
    }

    /**
     * Delete a course.
     */
    public function deleteCourse(Course $course)
    {
        $course->delete();
        $this->dispatch('notify', 'Course deleted successfully!', 'success');
    }

    /**
     * Bulk publish the selected courses.
     */
    // public function bulkPublish()
    // {
    //     $count = Course::whereIn('id', $this->selectedCourses)->update(['is_published' => true]);
    //     $this->dispatch('notify', "{$count} courses have been published.", 'success');
    //     $this->resetBulkActions();
    // }

    public function bulkPublish()
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'title' => 'Confirm Publish',
            'text' => 'Are you sure you want to publish the selected courses?',
            'type' => 'warning',
            'onConfirmed' => 'confirmBulkPublish',
        ]);

    }
    #[On('confirmBulkPublish')]
    public function confirmBulkPublish()
    {
        $count = Course::whereIn('id', $this->selectedCourses)->update(['is_published' => true]);
        $this->dispatch('notify', "{$count} courses have been published.", 'success');
        $this->resetBulkActions();
        $courses = Course::whereIn('id', $this->selectedCourses)->get();
        foreach ($courses as $course) {
            if ($course->is_published) {
                foreach ($course->enrollments as $user) {
                    $user->notify(new \App\Notifications\CourseUpdateNotification($course));
                }
            }
        }
    }
    /**
     * Bulk approve the selected courses.
     */
    public function bulkApprove()
    {
        $count = Course::whereIn('id', $this->selectedCourses)->update(['status' => 'approved']);
        $this->dispatch('notify', "{$count} courses have been approved.", 'success');
        $this->resetBulkActions();
    }

    /**
     * Bulk delete the selected courses.
     */
    public function bulkDelete()
    {
        $count = Course::whereIn('id', $this->selectedCourses)->delete();
        $this->dispatch('notify', "{$count} courses have been deleted.", 'success');
        $this->resetBulkActions();
    }

    /**
     * Reset bulk action state.
     */
    private function resetBulkActions()
    {
        $this->selectedCourses = [];
        $this->selectAll = false;
    }

    /**
     * Renders the component view with courses and categories.
     */
    public function render()
    {
        $courses = $this->getCoursesQuery()
            ->with(['instructor', 'category', 'sections'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.component.course-management.all-courses', [
            'courses' => $courses,
            'categories' => CourseCategory::orderBy('name')->get(),
        ]);
    }
}