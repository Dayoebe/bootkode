<?php

namespace App\Livewire\Component\CourseManagement;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.dashboard', ['title' => 'Create New Course', 'description' => 'Create and manage new courses, including setting details and uploading content', 'icon' => 'fas fa-plus-circle', 'active' => 'instructor.create-course'])]
class CreateCourse extends Component
{
    use WithFileUploads;

    public $is_premium = false;
    public $categories = []; // Populated in mount

    public ?Course $course = null;

    #[Rule('required|string|min:3|max:255|unique:courses,title')]
    public $title = '';

    #[Rule('nullable|string|max:255')]
    public $subtitle = '';

    #[Rule('nullable|string|min:10')]
    public $description = '';

    #[Rule('required|exists:course_categories,id')]
    public $category_id = '';

    #[Rule('required|in:beginner,intermediate,advanced')]
    public $difficulty_level = 'beginner';

    #[Rule('boolean')]
    public $is_published = false;

    #[Rule('boolean')]
    public $is_approved = false;

    #[Rule('nullable|image|mimes:jpeg,png,jpg|max:2048')] // Added MIME for security
    public $thumbnail;

    #[Rule('nullable|integer|min:1')]
    public $estimated_duration_minutes = null;

    #[Rule('required|numeric|min:0|max:9999')]
    public $price = 0.00;

    #[Rule('nullable|string|max:500')]
    public $target_audience = '';

    #[Rule('array|max:10')] // Limit for performance
    #[Rule(['learning_outcomes.*' => 'string|max:500'])]
    public array $learning_outcomes = [];

    #[Rule('array|max:10')]
    #[Rule(['prerequisites.*' => 'string|max:1000'])]
    public array $prerequisites = [];

    #[Rule('nullable|string|max:1000')]
    public $syllabus_overview = '';

    #[Rule('nullable|array|max:20')]
    #[Rule(['faqs.*.question' => 'string|max:255'])]
    #[Rule(['faqs.*.answer' => 'string|max:1000'])]
    public array $faqs = [];

    #[Rule('numeric|between:0,100')]
    public $completion_rate_threshold = 80.00;

    /**
     * Mount the component and load data.
     */
    public function mount($course = null)
    {
        $this->categories = Cache::remember('course_categories_create', 3600, fn() => CourseCategory::orderBy('name')->get());

        if ($course) {
            $this->course = Course::findOrFail($course);
            // Fill form with course data...
            $this->fill($this->course->only([
                'title', 'subtitle', 'description', 'category_id', 'difficulty_level', 'is_published',
                'target_audience', 'learning_outcomes', 'prerequisites', 'syllabus_overview', 'faqs',
                'completion_rate_threshold', 'estimated_duration_minutes', 'price', 'is_premium'
            ]));
            $this->is_approved = $this->course->is_approved;
        }

        $user = Auth::user();
        if (!$user->hasRole('super_admin') && !$user->hasRole('academy_admin')) {
            $this->is_approved = false; // Non-admins can't approve
        }
    }

    /**
     * Save or update the course.
     */
    public function save()
    {
        $this->authorize($this->course ? 'update' : 'create', Course::class); // Add policy check

        $this->validate();

        $data = $this->only([
            'title', 'subtitle', 'description', 'category_id', 'difficulty_level', 'is_published',
            'target_audience', 'learning_outcomes', 'prerequisites', 'syllabus_overview', 'faqs',
            'completion_rate_threshold', 'estimated_duration_minutes', 'price', 'is_premium', 'is_approved'
        ]);

        $data['description'] = strip_tags($data['description']); // Sanitize for XSS

        if ($this->thumbnail) {
            $data['thumbnail'] = $this->thumbnail->store('thumbnails', 'public');
        }

        try {
            if ($this->course) {
                $this->course->update($data);
                $message = 'Course updated successfully!';
            } else {
                $data['instructor_id'] = Auth::id();
                Course::create($data);
                $message = 'Course created successfully!';
            }

            $this->dispatch('notify', ['message' => $message, 'type' => 'success']);
            $this->redirect(route('all-course'), navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    /**
     * Stub for AI-generated suggestions (e.g., learning outcomes).
     */
    public function suggestAiContent(string $field)
    {
        // Future: Integrate AI API (e.g., Grok/xAI) to generate content based on title/description
        // For now, placeholder
        $this->dispatch('notify', 'AI suggestion coming soon!', 'info');
    }

    public function render()
    {
        return view('livewire.component.course-management.create-course', [
            'categories' => $this->categories,
        ]);
    }
}