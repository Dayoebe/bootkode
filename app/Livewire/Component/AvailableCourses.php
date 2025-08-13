<?php

namespace App\Livewire\Component;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Notifications\CourseUpdateNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Available Courses', 'description' => 'Browse and enroll in available courses', 'icon' => 'fas fa-book-open', 'active' => 'courses.available'])]
class AvailableCourses extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';

    public function enroll($courseId)
    {
        $course = Course::findOrFail($courseId);
        $user = Auth::user();

        if ($user->courses()->where('course_id', $courseId)->exists()) {
            $this->dispatch('notify', 'You are already enrolled in this course!', 'info');
            return;
        }

        $user->courses()->attach($courseId, ['last_accessed_at' => now()]);
        $user->logCustomActivity('Enrolled in course: ' . $course->title, ['course_id' => $courseId]);
        $user->notify(new CourseUpdateNotification($course));

        $this->dispatch('notify', 'Successfully enrolled in ' . $course->title . '!', 'success');
        // Redirect to course start or dashboard
        return $this->redirect(route('courses.start', $course->slug));
    }

    public function render()
    {
        $courses = Course::where('is_published', true)
            ->where('is_approved', true)
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->with(['category', 'instructor'])
            ->paginate(9);

        return view('livewire.component.available-courses', [
            'courses' => $courses,
            'categories' => CourseCategory::orderBy('name')->get(),
        ]);
    }
}