<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'My Courses', 'description' => 'Manage your courses and track their performance', 'icon' => 'fas fa-book', 'active' => 'my-courses'])]
class UserCourses extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';
    public $difficultyFilter = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public array $selectedCourses = [];
    public bool $selectAll = false;

    /**
     * Build the course query for the current user
     */
    private function getCoursesQuery(): Builder
    {
        return Course::query()
            ->with(['category', 'enrollments'])
            ->where('instructor_id', Auth::id())
            ->when($this->search, fn ($query) => $query->where(fn ($q) => $q
                ->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')))
            ->when($this->categoryFilter, fn ($query) => $query->where('category_id', $this->categoryFilter))
            ->when($this->statusFilter, fn ($query) => $query
                ->when($this->statusFilter === 'published', fn ($q) => $q->where('is_published', true))
                ->when($this->statusFilter === 'unpublished', fn ($q) => $q->where('is_published', false))
                ->when($this->statusFilter === 'approved', fn ($q) => $q->where('is_approved', true))
                ->when($this->statusFilter === 'unapproved', fn ($q) => $q->where('is_approved', false)))
            ->when($this->difficultyFilter, fn ($query) => $query->where('difficulty_level', $this->difficultyFilter));
    }

    /**
     * Toggle sorting for a given field
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
     * Toggles the 'selectAll' checkbox
     */
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedCourses = $this->getCoursesQuery()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->selectedCourses = [];
        }
    }

    /**
     * Updates the 'selectAll' checkbox based on selected courses
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
     * Resets pagination when filters change
     */
    public function updating($key)
    {
        if (in_array($key, ['search', 'categoryFilter', 'statusFilter', 'difficultyFilter', 'perPage'])) {
            $this->resetPage();
        }
    }

    /**
     * Toggles the published status of a course
     */
    public function togglePublished(Course $course)
    {
        // Ensure user owns this course
        if ($course->instructor_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized to modify this course.');
            return;
        }

        try {
            $course->is_published = !$course->is_published;
            $course->save();

            $status = $course->is_published ? 'published' : 'unpublished';
            session()->flash('success', "Course {$status} successfully.");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to toggle publish status.');
        }
    }

    /**
     * Redirects to the edit course page
     */
    public function editCourse(Course $course)
    {
        // Ensure user owns this course
        if ($course->instructor_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized to edit this course.');
            return;
        }
    
        // Use the correct parameter name that matches your route definition
        return $this->redirect(route('edit_course', ['courseId' => $course->id]));
    }

    /**
     * Deletes a course with confirmation
     */
    public function deleteCourse(Course $course)
    {
        // Ensure user owns this course
        if ($course->instructor_id !== Auth::id()) {
            session()->flash('error', 'Unauthorized to delete this course.');
            return;
        }

        try {
            $course->delete();
            session()->flash('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete course.');
        }
    }

    /**
     * Bulk publishes selected courses
     */
    public function bulkPublish()
    {
        try {
            $count = Course::whereIn('id', $this->selectedCourses)
                ->where('instructor_id', Auth::id())
                ->update(['is_published' => true]);
                
            session()->flash('success', "{$count} courses have been published.");
            $this->resetBulkActions();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to bulk publish.');
        }
    }

    /**
     * Bulk unpublishes selected courses
     */
    public function bulkUnpublish()
    {
        try {
            $count = Course::whereIn('id', $this->selectedCourses)
                ->where('instructor_id', Auth::id())
                ->update(['is_published' => false]);
                
            session()->flash('success', "{$count} courses have been unpublished.");
            $this->resetBulkActions();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to bulk unpublish.');
        }
    }

    /**
     * Bulk deletes selected courses
     */
    public function bulkDelete()
    {
        try {
            $count = Course::whereIn('id', $this->selectedCourses)
                ->where('instructor_id', Auth::id())
                ->delete();
                
            session()->flash('success', "{$count} courses have been deleted.");
            $this->resetBulkActions();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to bulk delete.');
        }
    }

    /**
     * Reset bulk action state
     */
    private function resetBulkActions()
    {
        $this->selectedCourses = [];
        $this->selectAll = false;
    }

    /**
     * Renders the component view with the user's courses
     */
    public function render()
    {
        $courses = $this->getCoursesQuery()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.course-management.user-courses', [
            'courses' => $courses,
            'categories' => CourseCategory::orderBy('name')->get(),
            'totalEnrollments' => Course::where('instructor_id', Auth::id())->withCount('enrollments')->get()->sum('enrollments_count'),
            'publishedCount' => Course::where('instructor_id', Auth::id())->where('is_published', true)->count(),
            'unpublishedCount' => Course::where('instructor_id', Auth::id())->where('is_published', false)->count(),
        ]);
    }
}