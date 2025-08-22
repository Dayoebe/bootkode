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

#[Layout('layouts.dashboard', ['title' => 'All Courses', 'description' => 'Manage all courses including creation, editing, and deletion', 'icon' => 'fas fa-book', 'active' => 'all-course'])]
class AllCourses extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';
    public $difficultyFilter = '';
    public $instructorFilter = '';
    public $perPage = 10;
    public $sortField = 'created_at'; // Added for sorting
    public $sortDirection = 'desc'; // Added for sorting

    public array $selectedCourses = [];
    public bool $selectAll = false;

    /**
     * Mount the component and check authorization.
     */
    public function mount()
    {
        // if (! Gate::allows('view-courses')) {
        //     \Log::error('Unauthorized access attempt to AllCourses by user ID: ' . Auth::id());
        //     session()->flash('error', 'Unauthorized access to course management.');
        //     $this->redirectRoute('dashboard');
        // }
    }

    /**
     * Centralized method to build the course query with efficient eager-loading and filters.
     * Restricts instructors to their own courses; super_admin sees all.
     */
    private function getCoursesQuery(): Builder
    {
        $user = Auth::user();

        return Course::query()
            ->with(['instructor', 'category', 'enrollments'])
            ->when(! $user->hasRole('super_admin'), fn ($query) => $query->where('instructor_id', $user->id()))
            ->when($this->search, fn ($query) => $query->where(fn ($q) => $q
                ->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')))
            ->when($this->categoryFilter, fn ($query) => $query->where('category_id', $this->categoryFilter))
            ->when($this->statusFilter, fn ($query) => $query
                ->when($this->statusFilter === 'published', fn ($q) => $q->where('is_published', true))
                ->when($this->statusFilter === 'unpublished', fn ($q) => $q->where('is_published', false))
                ->when($this->statusFilter === 'approved', fn ($q) => $q->where('status', 'approved'))
                ->when($this->statusFilter === 'unapproved', fn ($q) => $q->where('status', '!=', 'approved')))
            ->when($this->difficultyFilter, fn ($query) => $query->where('difficulty_level', $this->difficultyFilter))
            ->when($this->instructorFilter && $user->hasRole('super_admin'), fn ($query) => $query->where('instructor_id', $this->instructorFilter));
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
            $this->selectedCourses = $this->getCoursesQuery()->pluck('id')->map(fn ($id) => (string) $id)->toArray();
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
    }

    /**
     * Toggles the published status of a course with authorization check.
     */
    public function togglePublished(Course $course)
    {
        if (! Gate::allows('publish-courses') || (Auth::user()->hasRole('instructor') && $course->instructor_id !== Auth::id())) {
            session()->flash('error', 'Unauthorized to publish this course.');
            return;
        }

        try {
            $course->is_published = ! $course->is_published;
            $course->save();

            $status = $course->is_published ? 'published' : 'unpublished';
            session()->flash('success', "Course {$status} successfully.");

            if ($course->is_published) {
                $course->enrollments->each->user->notify(new \App\Notifications\CourseUpdateNotification($course));
            }
        } catch (\Exception $e) {
            \Log::error('Failed to toggle publish status for course ID: ' . $course->id, ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to toggle publish status.');
        }
    }

    /**
     * Toggles the approved status of a course with authorization check.
     */
    public function toggleApproved(Course $course)
    {
        if (! Gate::allows('approve-courses') || (Auth::user()->hasRole('instructor') && $course->instructor_id === Auth::id())) {
            session()->flash('error', 'Unauthorized to approve this course.');
            return;
        }

        try {
            $course->status = $course->status === 'approved' ? 'pending' : 'approved';
            $course->save();

            session()->flash('success', "Course status changed to {$course->status}.");
        } catch (\Exception $e) {
            \Log::error('Failed to toggle approval status for course ID: ' . $course->id, ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to toggle approval status.');
        }
    }

    /**
     * Redirects to the edit course page.
     */
    public function CourseForm(Course $course)
    {
        if (! Gate::allows('edit-courses') || (Auth::user()->hasRole('instructor') && $course->instructor_id !== Auth::id())) {
            session()->flash('error', 'Unauthorized to edit this course.');
            return;
        }

        return $this->redirect(route('edit_course', ['course' => $course->id]));
    }

    /**
     * Deletes a course with confirmation and authorization.
     */
    public function deleteCourse(Course $course)
    {
        if (! Gate::allows('delete-courses') || (Auth::user()->hasRole('instructor') && $course->instructor_id !== Auth::id())) {
            session()->flash('error', 'Unauthorized to delete this course.');
            return;
        }

        try {
            $course->delete();
            session()->flash('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete course ID: ' . $course->id, ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to delete course.');
        }
    }

    /**
     * Bulk publishes selected courses with SweetAlert confirmation.
     */
    public function bulkPublish()
    {
        if (! Gate::allows('publish-courses')) {
            session()->flash('error', 'Unauthorized to publish courses.');
            return;
        }

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
                ->where(fn ($q) => Auth::user()->hasRole('instructor') ? $q->where('instructor_id', Auth::id()) : $q)
                ->update(['is_published' => true]);
            session()->flash('success', "{$count} courses have been published.");

            Course::whereIn('id', $this->selectedCourses)->where('is_published', true)->get()->each(function ($course) {
                $course->enrollments->each->user->notify(new \App\Notifications\CourseUpdateNotification($course));
            });

            $this->resetBulkActions();
        } catch (\Exception $e) {
            \Log::error('Failed to bulk publish courses', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to bulk publish.');
        }
    }

    /**
     * Bulk approves selected courses.
     */
    public function bulkApprove()
    {
        if (! Gate::allows('approve-courses')) {
            session()->flash('error', 'Unauthorized to approve courses.');
            return;
        }

        try {
            $count = Course::whereIn('id', $this->selectedCourses)
                ->where('instructor_id', '!=', Auth::id())
                ->update(['status' => 'approved']);
            session()->flash('success', "{$count} courses have been approved.");
            $this->resetBulkActions();
        } catch (\Exception $e) {
            \Log::error('Failed to bulk approve courses', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to bulk approve.');
        }
    }

    /**
     * Bulk deletes selected courses with confirmation.
     */
    public function bulkDelete()
    {
        if (! Gate::allows('delete-courses')) {
            session()->flash('error', 'Unauthorized to delete courses.');
            return;
        }

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
                ->where(fn ($q) => Auth::user()->hasRole('instructor') ? $q->where('instructor_id', Auth::id()) : $q)
                ->delete();
            session()->flash('success', "{$count} courses have been deleted.");
            $this->resetBulkActions();
        } catch (\Exception $e) {
            \Log::error('Failed to bulk delete courses', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to bulk delete.');
        }
    }

    /**
     * Bulk unpublishes selected courses.
     */
    public function bulkUnpublish()
    {
        if (! Gate::allows('publish-courses')) {
            session()->flash('error', 'Unauthorized to unpublish courses.');
            return;
        }

        try {
            $count = Course::whereIn('id', $this->selectedCourses)
                ->where(fn ($q) => Auth::user()->hasRole('instructor') ? $q->where('instructor_id', Auth::id()) : $q)
                ->update(['is_published' => false]);
            session()->flash('success', "{$count} courses have been unpublished.");
            $this->resetBulkActions();
        } catch (\Exception $e) {
            \Log::error('Failed to bulk unpublish courses', ['error' => $e->getMessage()]);
            session()->flash('error', 'Failed to bulk unpublish.');
        }
    }

    /**
     * Exports selected courses to CSV.
     */
    public function exportSelected()
    {
        if (empty($this->selectedCourses)) {
            session()->flash('error', 'No courses selected for export.');
            return;
        }

        $courses = Course::whereIn('id', $this->selectedCourses)
            ->where(fn ($q) => Auth::user()->hasRole('instructor') ? $q->where('instructor_id', Auth::id()) : $q)
            ->get();

        return Response::streamDownload(function () use ($courses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Title', 'Instructor', 'Category', 'Difficulty', 'Published', 'Status']);
            foreach ($courses as $course) {
                fputcsv($file, [
                    $course->title,
                    $course->instructor->name,
                    $course->category->name,
                    ucfirst($course->difficulty_level),
                    $course->is_published ? 'Yes' : 'No',
                    ucfirst($course->status),
                ]);
            }
            fclose($file);
        }, 'selected_courses_' . now()->format('Ymd_His') . '.csv');
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
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.course-management.all-courses', [
            'courses' => $courses,
            'categories' => cache()->remember('course_categories', now()->addHours(24), fn () => CourseCategory::orderBy('name')->get()),
            'instructors' => cache()->remember('course_instructors', now()->addHours(24), fn () => User::whereHas('roles', fn ($q) => $q->where('name', 'instructor'))->orderBy('name')->get()),
        ]);
    }
}