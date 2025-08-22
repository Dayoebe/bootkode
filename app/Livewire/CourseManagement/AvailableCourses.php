<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Notifications\CourseUpdateNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Available Courses', 'description' => 'Browse and enroll in available courses', 'icon' => 'fas fa-book-open', 'active' => 'courses.available'])]
class AvailableCourses extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';
    protected int $perPage = 9;

    /**
     * Enroll the user in a course with validation and error handling.
     */
    public function enroll(int $courseId): void
    {
        $validated = $this->validate(['courseId' => 'required|exists:courses,id']);
        $course = Course::findOrFail($validated['courseId']);
        $user = Auth::user();

        if ($user->courses()->where('course_id', $courseId)->exists()) {
            $this->dispatch('notify', ['message' => 'You are already enrolled in this course!', 'type' => 'info']);
            return;
        }

        try {
            $user->courses()->attach($courseId, ['last_accessed_at' => now()]);
            $user->logCustomActivity('Enrolled in course: ' . $course->title, ['course_id' => $courseId]);
            $user->notify(new CourseUpdateNotification($course));

            $this->dispatch('notify', ['message' => 'Successfully enrolled in ' . $course->title . '!', 'type' => 'success']);
            $this->redirect(route('courses.preview', [$course->slug])); // Changed to existing route
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Enrollment failed: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function render()
    {
        $courses = Course::where('is_published', true)
            ->where('is_approved', true)
            ->when($this->search, fn($query) => $query->where('title', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%'))
            ->when($this->categoryFilter, fn($query) => $query->where('category_id', $this->categoryFilter))
            ->with(['category', 'instructor'])
            ->withAvg('reviews as average_rating', 'rating') // Use reviews relationship
            ->withCount('reviews as rating_count')
            ->select(['id', 'title', 'description', 'thumbnail', 'difficulty_level', 'category_id', 'instructor_id', 'slug'])
            ->paginate($this->perPage);

        $categories = Cache::remember('course_categories', 3600, fn() => CourseCategory::orderBy('name')->get());

        return view('livewire.course-management.available-courses', compact('courses', 'categories'));
    }
}