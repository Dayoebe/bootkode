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
    public $modalState = [
        'type' => null, // 'settings'
        'isOpen' => false,
        'data' => [
            'title' => '',
            'subtitle' => '',
            'description' => '',
            'category_id' => null,
            'difficulty_level' => '',
            'estimated_duration_minutes' => 0,
        ],
    ];
    public $categories;
    public $difficultyLevels = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
    ];

    protected $rules = [
        'modalState.data.title' => 'required|string|max:255',
        'modalState.data.subtitle' => 'nullable|string|max:255',
        'modalState.data.description' => 'nullable|string|max:1000',
        'modalState.data.category_id' => 'required|exists:course_categories,id',
        'modalState.data.difficulty_level' => 'required|in:beginner,intermediate,advanced',
        'modalState.data.estimated_duration_minutes' => 'required|integer|min:1',
    ];

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

    public function openSettings()
    {
        $this->modalState = [
            'type' => 'settings',
            'isOpen' => true,
            'data' => [
                'title' => $this->course->title ?? '',
                'subtitle' => $this->course->subtitle ?? '',
                'description' => $this->course->description ?? '',
                'category_id' => $this->course->category_id ?? null,
                'difficulty_level' => $this->course->difficulty_level ?? '',
                'estimated_duration_minutes' => $this->course->estimated_duration_minutes ?? 0,
            ],
        ];
    }

    public function closeSettingsModal()
    {
        $this->modalState = [
            'type' => null,
            'isOpen' => false,
            'data' => [
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'category_id' => null,
                'difficulty_level' => '',
                'estimated_duration_minutes' => 0,
            ],
        ];
    }

    public function updateSettings()
    {
        $this->validate();

        try {
            $data = [
                'title' => $this->modalState['data']['title'],
                'subtitle' => $this->modalState['data']['subtitle'],
                'description' => $this->modalState['data']['description'],
                'category_id' => $this->modalState['data']['category_id'],
                'difficulty_level' => $this->modalState['data']['difficulty_level'],
                'estimated_duration_minutes' => (int) $this->modalState['data']['estimated_duration_minutes'],
            ];

            $this->course->update($data);
            $this->notify('Course settings updated successfully!', 'success');
            $this->dispatch('course-updated')->to('component.course-management.course-builder');
            $this->closeSettingsModal();
        } catch (\Exception $e) {
            $this->notify('Failed to update course: Invalid input data', 'error');
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