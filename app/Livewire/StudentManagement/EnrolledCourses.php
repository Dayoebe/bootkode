<?php

namespace App\Livewire\StudentManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Enrolled courses', 'description' => 'View and manage your enrolled courses', 'icon' => 'fas fa-book', 'active' => 'student.enrolled-courses'])]

class EnrolledCourses extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $sortBy = 'recent';
    public $compact = false;

    public function mount($compact = false)
    {
        $this->compact = $compact;
    }

    public function getCourses()
    {
        return Auth::user()->courses()
            ->with(['category', 'sections.lessons'])
            ->withPivot(['progress', 'updated_at']) // Make sure to include pivot columns
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->sortBy === 'recent', fn($q) => $q->orderBy('course_user.updated_at', 'desc'))
            ->when($this->sortBy === 'progress', fn($q) => $q->orderBy('course_user.progress', 'desc'))
            ->when($this->sortBy === 'title', fn($q) => $q->orderBy('title'))
            ->paginate($this->compact ? 3 : 12);
    }

    public function calculateProgress($course)
    {
        // Use the pivot progress if available, otherwise calculate fresh
        if (isset($course->pivot->progress)) {
            return $course->pivot->progress;
        }

        $totalLessons = $course->sections->flatMap->lessons->count();
        $completed = Auth::user()->completedLessons()
            ->whereIn('lesson_id', $course->sections->flatMap->lessons->pluck('id'))
            ->count();
            
        return $totalLessons > 0 ? round(($completed / $totalLessons) * 100) : 0;
    }

    public function render()
    {
        return view('livewire.student-management.enrolled-courses', [
            'courses' => $this->getCourses(),
            'categories' => CourseCategory::all(),
            'compact' => $this->compact
        ]);
    }
}