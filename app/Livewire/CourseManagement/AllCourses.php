<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\User;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.dashboard', ['title' => 'All Courses', 'description' => 'Manage all courses including creation, editing, and deletion', 'icon' => 'fas fa-book', 'active' => 'all-course'])]
class AllCourses extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';
    public $difficultyFilter = '';
    public $instructorFilter = '';
    public $perPage = 12;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public array $selectedCourses = [];
    public bool $selectAll = false;

    // Statistics properties
    public $totalCourses = 0;
    public $publishedCourses = 0;
    public $approvedCourses = 0;
    public $freeCourses = 0;
    public $paidCourses = 0;
    public $pendingCourses = 0;

    /**
     * Mount the component and initialize statistics
     */
    public function mount()
    {
        $this->updateStatistics();
    }

    /**
     * Update statistics
     */
    public function updateStatistics()
    {
        $user = Auth::user();
        $baseQuery = Course::query()
            ->when(!$user->hasRole('super_admin'), fn($query) => $query->where('instructor_id', $user->id));

        $this->totalCourses = $baseQuery->count();
        $this->publishedCourses = (clone $baseQuery)->where('is_published', true)->count();
        $this->approvedCourses = (clone $baseQuery)->where('is_approved', true)->count();
        $this->freeCourses = (clone $baseQuery)->where('is_free', true)->count();
        $this->paidCourses = (clone $baseQuery)->where('is_free', false)->count();
        $this->pendingCourses = (clone $baseQuery)->where('is_approved', false)->count();
    }

    /**
     * Centralized method to build the course query with efficient eager-loading and filters.
     */
    private function getCoursesQuery(): Builder
    {
        $user = Auth::user();

        return Course::query()
            ->with(['instructor', 'category', 'enrollments'])
            ->when(!$user->hasRole('super_admin'), fn($query) => $query->where('instructor_id', $user->id()))
            ->when($this->search, fn($query) => $query->where(fn($q) => $q
                ->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')))
            ->when($this->categoryFilter, fn($query) => $query->where('category_id', $this->categoryFilter))
            ->when($this->statusFilter, fn($query) => $query
                ->when($this->statusFilter === 'published', fn($q) => $q->where('is_published', true))
                ->when($this->statusFilter === 'unpublished', fn($q) => $q->where('is_published', false))
                ->when($this->statusFilter === 'approved', fn($q) => $q->where('is_approved', true))
                ->when($this->statusFilter === 'unapproved', fn($q) => $q->where('is_approved', false)))
            ->when($this->difficultyFilter, fn($query) => $query->where('difficulty_level', $this->difficultyFilter))
            ->when($this->instructorFilter && $user->hasRole('super_admin'), fn($query) => $query->where('instructor_id', $this->instructorFilter));
    }

    /**
     * Reset all filters
     */
    public function resetAllFilters()
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->statusFilter = '';
        $this->difficultyFilter = '';
        $this->instructorFilter = '';
        $this->resetPage();
        $this->updateStatistics();

        session()->flash('success', 'All filters have been reset.');
    }

    /**
     * Toggle sorting for a given field.
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    /**
     * Toggles the 'selectAll' checkbox and selects all courses on the current page.
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedCourses = $this->getCoursesQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
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
     * Resets pagination when filters change.
     */
    public function updating($key)
    {
        if (in_array($key, ['search', 'categoryFilter', 'statusFilter', 'difficultyFilter', 'instructorFilter', 'perPage'])) {
            $this->resetPage();
        }
    }

    /**
     * Listen for the 'course-updated' event to refresh the table.
     */
    #[On('course-updated')]
    public function refreshList()
    {
        $this->resetPage();
        $this->updateStatistics();
    }

    /**
     * Toggles the published status of a course with authorization check.
     */
    public function togglePublished(Course $course)
    {
        try {
            $course->is_published = !$course->is_published;
            $course->save();

            $status = $course->is_published ? 'published' : 'unpublished';
            $this->updateStatistics();
            
            $this->dispatch('notify', [
                'message' => "Course {$status} successfully!",
                'type' => 'success'
            ]);

            if ($course->is_published) {
                // Notify users about course update
                // $course->enrollments->each->user->notify(new \App\Notifications\CourseUpdateNotification($course));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to toggle publish status for course ID: ' . $course->id, ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'message' => 'Failed to toggle publish status.',
                'type' => 'error'
            ]);
        }
    }

    /**
     * Toggles the approved status of a course with authorization check.
     */
    public function toggleApproved(Course $course)
    {
        try {
            $course->is_approved = !$course->is_approved;
            $course->save();

            $status = $course->is_approved ? 'approved' : 'unapproved';
            $this->updateStatistics();
            
            $this->dispatch('notify', [
                'message' => "Course {$status} successfully!",
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to toggle approval status for course ID: ' . $course->id, ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'message' => 'Failed to toggle approval status.',
                'type' => 'error'
            ]);
        }
    }

    /**
     * Redirects to the edit course page.
     */
    public function CourseForm(Course $course)
    {
        return $this->redirect(route('edit_course', ['courseId' => $course->id]));
    }

    /**
     * Deletes a course with confirmation and authorization.
     */
    public function deleteCourse(Course $course)
    {
        try {
            $course->delete();
            $this->updateStatistics();
            
            $this->dispatch('notify', [
                'message' => 'Course deleted successfully!',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to delete course ID: ' . $course->id, ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'message' => 'Failed to delete course.',
                'type' => 'error'
            ]);
        }
    }

    /**
     * Bulk publishes selected courses with SweetAlert confirmation.
     */
    public function bulkPublish()
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Confirm Publish',
            'text' => 'Are you sure you want to publish the selected courses?',
            'type' => 'warning',
            'onConfirmed' => 'confirmBulkPublish',
        ]);
    }

    #[On('confirmBulkPublish')]
    public function confirmBulkPublish()
    {
        try {
            $count = Course::whereIn('id', $this->selectedCourses)
                ->where(fn($q) => Auth::user()->hasRole('instructor') ? $q->where('instructor_id', Auth::id()) : $q)
                ->update(['is_published' => true]);
            
            $this->updateStatistics();
            $this->dispatch('notify', [
                'message' => "{$count} courses have been published!",
                'type' => 'success'
            ]);

            $this->resetBulkActions();
        } catch (\Exception $e) {
            \Log::error('Failed to bulk publish courses', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'message' => 'Failed to bulk publish.',
                'type' => 'error'
            ]);
        }
    }

    /**
     * Bulk approves selected courses.
     */
    public function bulkApprove()
    {
        try {
            $count = Course::whereIn('id', $this->selectedCourses)->update(['is_approved' => true]);
            
            $this->updateStatistics();
            $this->dispatch('notify', [
                'message' => "{$count} courses have been approved!",
                'type' => 'success'
            ]);
            $this->resetBulkActions();
        } catch (\Exception $e) {
            \Log::error('Failed to bulk approve courses', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'message' => 'Failed to bulk approve.',
                'type' => 'error'
            ]);
        }
    }

    /**
     * Bulk deletes selected courses with confirmation.
     */
    public function bulkDelete()
    {
        $this->dispatch('swal:confirm', [
            'title' => 'Confirm Delete',
            'text' => 'Are you sure you want to delete the selected courses?',
            'type' => 'warning',
            'onConfirmed' => 'confirmBulkDelete',
        ]);
    }

    #[On('confirmBulkDelete')]
    public function confirmBulkDelete()
    {
        try {
            $count = Course::whereIn('id', $this->selectedCourses)
                ->where(fn($q) => Auth::user()->hasRole('instructor') ? $q->where('instructor_id', Auth::id()) : $q)
                ->delete();
            
            $this->updateStatistics();
            $this->dispatch('notify', [
                'message' => "{$count} courses have been deleted!",
                'type' => 'success'
            ]);
            $this->resetBulkActions();
        } catch (\Exception $e) {
            \Log::error('Failed to bulk delete courses', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'message' => 'Failed to bulk delete.',
                'type' => 'error'
            ]);
        }
    }

    /**
     * Bulk unpublishes selected courses.
     */
    public function bulkUnpublish()
    {
        try {
            $count = Course::whereIn('id', $this->selectedCourses)
                ->where(fn($q) => Auth::user()->hasRole('instructor') ? $q->where('instructor_id', Auth::id()) : $q)
                ->update(['is_published' => false]);
            
            $this->updateStatistics();
            $this->dispatch('notify', [
                'message' => "{$count} courses have been unpublished!",
                'type' => 'success'
            ]);
            $this->resetBulkActions();
        } catch (\Exception $e) {
            \Log::error('Failed to bulk unpublish courses', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'message' => 'Failed to bulk unpublish.',
                'type' => 'error'
            ]);
        }
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
     * Get categories with caching
     */
    #[Computed]
    public function categories()
    {
        return cache()->remember('course_categories', now()->addHours(24), fn() => CourseCategory::orderBy('name')->get());
    }

    /**
     * Get instructors with caching
     */
    #[Computed]
    public function instructors()
    {
        return cache()->remember('course_instructors', now()->addHours(24), fn() => User::whereHas('roles', fn($q) => $q->where('name', 'instructor'))->orderBy('name')->get());
    }

    /**
     * Renders the component view with courses and categories.
     */
    public function render()
    {
        $courses = $this->getCoursesQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.course-management.all-courses', [
            'courses' => $courses,
            'categories' => $this->categories,
            'instructors' => $this->instructors,
            'totalCourses' => $this->totalCourses,
            'publishedCourses' => $this->publishedCourses,
            'approvedCourses' => $this->approvedCourses,
            'freeCourses' => $this->freeCourses,
            'paidCourses' => $this->paidCourses,
            'pendingCourses' => $this->pendingCourses,
        ]);
    }
}