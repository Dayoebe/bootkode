<?php

namespace App\Livewire\Pages;

use App\Models\Learning\Course;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CourseDetails extends Component
{
    public Course $course;

    public function mount(Course $course): void
    {
        $this->course = Course::publiclyAvailable()->whereKey($course->id)
            ->with(['category', 'instructor:id,name', 'sections' => fn ($q) => $q->orderBy('order')])
            ->withCount('allLessons')->firstOrFail();
    }

    public function render()
    {
        $enrolled = auth()->check() && (auth()->user()->enrollments()->where('course_id', $this->course->id)->exists()
            || auth()->user()->courses()->where('courses.id', $this->course->id)->exists());

        return view('livewire.pages.course-details', ['enrolled' => $enrolled])
            ->layoutData(['title' => $this->course->title]);
    }
}
