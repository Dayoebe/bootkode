<?php

namespace App\Livewire\Component\CourseManagement;

use Livewire\Component;
use App\Models\Course;
use App\Models\CourseCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.dashboard', ['title' => 'Edit Course'])]
class EditCourse extends Component
{
    public ?Course $course = null;

    #[Rule('required|string|min:3|max:255')]
    public $title = '';

    #[Rule('nullable|string|max:255')]
    public $subtitle = '';

    #[Rule('nullable|string|min:10')]
    public $description = '';

    #[Rule('required|exists:course_categories,id')]
    public $categoryId = '';

    #[Rule('required|in:beginner,intermediate,advanced')]
    public $difficulty_level = 'beginner';

    #[Rule('boolean')]
    public $is_published = false;

    /**
     * Load the course data when the component is mounted.
     */
    public function mount(Course $course)
    {
        $this->course = $course;
        $this->title = $course->title;
        $this->subtitle = $course->subtitle;
        $this->description = $course->description;
        $this->categoryId = $course->category_id;
        $this->difficulty_level = $course->difficulty_level;
        $this->is_published = $course->is_published;
    }

    /**
     * Update the course in the database.
     */
    public function updateCourse()
    {
        $this->validate();

        $this->course->update([
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'category_id' => $this->categoryId,
            'difficulty_level' => $this->difficulty_level,
            'is_published' => $this->is_published,
        ]);
        $this->dispatch('notify', 'Course updated successfully!', 'success');
    return $this->redirect(route('all-course'));

    
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.component.course-management.edit-course', [
            'categories' => CourseCategory::all(),
        ]);
    }
}