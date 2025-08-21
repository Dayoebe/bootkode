<?php

namespace App\Livewire\Component\CourseManagement;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.dashboard', [
    'title' => fn() => $this->pageTitle ?? 'Course Form',
    'description' => 'Create or edit courses with details, pricing, and content',
    'icon' => 'fas fa-plus-circle',
    'active' => 'instructor.course-form'
])]

class CourseForm extends Component
{
    use WithFileUploads;

    public bool $isEditMode = false;
    public ?Course $course = null;
    public string $pageTitle = 'Create New Course'; // For dynamic layout title

    public $categories = [];

    // Properties matching model fields
    public $title = '';
    public $subtitle = '';
    public $description = '';
    public $category_id = '';
    public $difficulty_level = 'beginner';
    public $is_published = false;
    public $is_approved = false;
    public $is_free = false;
    public $is_premium = false;
    public $thumbnail = null;
    public $estimated_duration_minutes = null;
    public $price = 0.00;
    public $target_audience = '';
    public array $learning_outcomes = [];
    public array $prerequisites = [];
    public $syllabus_overview = '';
    public array $faqs = [];
    public $completion_rate_threshold = 80.00;
    public $scheduled_publish_at = null;

    // Validation rules (base set; override dynamically for modes)
    protected $rules = [
        'title' => 'required|string|min:3|max:255|unique:courses,title',
        'subtitle' => 'nullable|string|max:255',
        'description' => 'nullable|string|min:10',
        'category_id' => 'required|exists:course_categories,id',
        'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
        'is_published' => 'boolean',
        'is_approved' => 'boolean',
        'is_free' => 'boolean',
        'is_premium' => 'boolean',
        'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'estimated_duration_minutes' => 'nullable|integer|min:1',
        'price' => 'required|numeric|min:0|max:9999',
        'target_audience' => 'nullable|string|max:500',
        'learning_outcomes' => 'array|max:10',
        'learning_outcomes.*' => 'string|max:500',
        'prerequisites' => 'array|max:10',
        'prerequisites.*' => 'string|max:1000',
        'syllabus_overview' => 'nullable|string|max:1000',
        'faqs' => 'nullable|array|max:20',
        'faqs.*.question' => 'string|max:255',
        'faqs.*.answer' => 'string|max:1000',
        'completion_rate_threshold' => 'numeric|between:0,100',
        'scheduled_publish_at' => 'nullable|date|after:now',
    ];

    public function mount($courseId = null)
    {
        // Load categories from cache for performance
        $this->categories = Cache::remember('course_categories', 3600, fn() => CourseCategory::orderBy('name')->get());

        if ($courseId) {
            $this->course = Course::findOrFail($courseId);
            $this->isEditMode = true;
            $this->pageTitle = 'Edit Course: ' . $this->course->title;

            // Fill properties from existing course
            $this->fill($this->course->only([
                'title',
                'subtitle',
                'description',
                'category_id',
                'difficulty_level',
                'is_published',
                'is_free',
                'is_premium',
                'target_audience',
                'learning_outcomes',
                'prerequisites',
                'syllabus_overview',
                'faqs',
                'completion_rate_threshold',
                'estimated_duration_minutes',
                'price',
                'scheduled_publish_at'
            ]));
            $this->is_approved = $this->course->is_approved;
        } else {
            $this->pageTitle = 'Create New Course';
        }

        // Restrict approval to admins
        $user = Auth::user();
        if (!$user->hasRole('super_admin') && !$user->hasRole('academy_admin')) {
            $this->is_approved = false;
        }

        // For edit mode (simpler blade), relax rules for optional fields not present in edit blade
        if ($this->isEditMode) {
            // Override unique rule for title
            $this->rules['title'] = 'required|string|min:3|max:255|unique:courses,title,' . $this->course->id;

            // Make advanced fields nullable/optional since edit blade may not have them
            $this->rules['subtitle'] = 'nullable';
            $this->rules['price'] = 'nullable';
            // Add similar for others if needed; validation will skip if not submitted
        }
    }

    public function save()
    {
        // Authorize action (assuming CoursePolicy exists)
        $this->authorize($this->isEditMode ? 'update' : 'create', $this->course ?? Course::class);

        $this->validate();

        // Prepare data array
        $data = $this->only([
            'title',
            'subtitle',
            'description',
            'category_id',
            'difficulty_level',
            'is_published',
            'is_free',
            'is_premium',
            'target_audience',
            'learning_outcomes',
            'prerequisites',
            'syllabus_overview',
            'faqs',
            'completion_rate_threshold',
            'estimated_duration_minutes',
            'price',
            'is_approved',
            'scheduled_publish_at'
        ]);

        // Sanitize description
        $data['description'] = strip_tags($data['description']);

        // Handle thumbnail upload if provided
        if ($this->thumbnail) {
            $data['thumbnail'] = $this->thumbnail->store('thumbnails', 'public');
        }

        // Handle publishing dates logically
        if (isset($data['scheduled_publish_at']) && now()->gt($data['scheduled_publish_at'])) {
            $data['published_at'] = $data['scheduled_publish_at'];
        } elseif ($data['is_published'] && !isset($data['scheduled_publish_at'])) {
            $data['published_at'] = now();
        }

        try {
            if ($this->isEditMode) {
                $this->course->update($data);
                $message = 'Course updated successfully!';
            } else {
                $data['instructor_id'] = Auth::id();
                Course::create($data);
                $message = 'Course created successfully!';
            }

            $this->dispatch('notify', ['message' => $message, 'type' => 'success']);
            return $this->redirect(route('all-course'), navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Error: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function suggestAiContent(string $field)
    {
        // Placeholder for future AI integration (e.g., OpenAI API)
        $this->dispatch('notify', ['message' => 'AI suggestion coming soon!', 'type' => 'info']);
    }

    public function render()
    {
        // Conditional rendering to work with both blades
        if ($this->isEditMode) {
            return view('livewire.component.course-management.edit-course', [
                'categories' => $this->categories,
            ]);
        } else {
            return view('livewire.component.course-management.create-course', [
                'categories' => $this->categories,
            ]);
        }
    }
}