<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Toolbar extends Component
{
    public Course $course;
    public $sectionCount;
    public $lessonCount;

    public function mount(Course $course)
    {
        $this->course = $course;
        $this->sectionCount = $course->sections()->count();
        $this->lessonCount = $course->sections()->withCount('lessons')->get()->sum('lessons_count');
        $this->categories = Cache::remember('course_categories', 3600, fn() => CourseCategory::all());
    }

    public function togglePublished()
    {
        try {
            $this->course->update(['is_published' => !$this->course->is_published]);
            $this->notify("Course " . ($this->course->is_published ? 'published' : 'unpublished') . " successfully!", 'success');
            $this->dispatch('course-updated')->to('component.course-management.course-builder');
        } catch (\Exception $e) {
            $this->notify('Failed to update course status: Unable to save changes', 'error');
        }
    }

    public function notify($message, $type = 'success')
    {
        $this->dispatch('notify', message: $message, type: $type);
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder.toolbar');
    }
}