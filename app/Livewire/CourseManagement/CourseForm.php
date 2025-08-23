<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'description' => 'Create or edit courses with details, pricing, and content',
    'icon' => 'fas fa-plus-circle',
    'active' => 'instructor.course-form'
])]
class CourseForm extends Component
{
    use WithFileUploads;

    public bool $isEditMode = false;
    public ?Course $course = null;
    public string $pageTitle = 'Course Form';

    public $categories = [];

    // Properties matching model fields
    public $title = '';
    public $subtitle = '';
    public $slug = '';
    public $description = '';
    public $category_id = '';
    public $difficulty_level = 'beginner';
    public $is_published = false;
    public $is_approved = false;
    public $is_free = true; // Default to free
    public $is_premium = false;
    public $thumbnail = null;
    public $estimated_duration_minutes = null;
    public $price = 0.00;
    public $target_audience = '';
    public array $learning_outcomes = [''];
    public array $prerequisites = [''];
    public $syllabus_overview = '';
    public array $faqs = [['question' => '', 'answer' => '']];
    public $completion_rate_threshold = 80.00;
    public $scheduled_publish_at = null;

    // Step management
    public $currentStep = 1;
    public $totalSteps = 5;

    // Available difficulty levels
    public $difficultyLevels = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'expert' => 'Expert'
    ];

    // Validation rules
    protected $rules = [
        'title' => 'required|string|min:3|max:255',
        'subtitle' => 'nullable|string|max:255',
        'slug' => 'required|string|min:3|max:255|regex:/^[a-z0-9\-]+$/|unique:courses,slug',
        'description' => 'nullable|string|min:10',
        'category_id' => 'required|exists:course_categories,id',
        'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
        'is_published' => 'boolean',
        'is_approved' => 'boolean',
        'is_free' => 'boolean',
        'is_premium' => 'boolean',
        'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'estimated_duration_minutes' => 'nullable|integer|min:1',
        'price' => 'nullable|numeric|min:0|max:9999',
        'target_audience' => 'nullable|string|max:500',
        'learning_outcomes' => 'array|max:10',
        'learning_outcomes.*' => 'nullable|string|max:500',
        'prerequisites' => 'array|max:10',
        'prerequisites.*' => 'nullable|string|max:1000',
        'syllabus_overview' => 'nullable|string|max:1000',
        'faqs' => 'nullable|array|max:20',
        'faqs.*.question' => 'nullable|string|max:255',
        'faqs.*.answer' => 'nullable|string|max:1000',
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
            $this->dispatch('update-title', $this->pageTitle);

            // Override unique rule for slug in edit mode
            $this->rules['slug'] = 'required|string|min:3|max:255|regex:/^[a-z0-9\-]+$/|unique:courses,slug,' . $this->course->id;
        } else {
            $this->pageTitle = 'Create New Course';
            $this->dispatch('update-title', $this->pageTitle);
        }
    }

    public function nextStep()
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep($step)
    {
        if ($step >= 1 && $step <= $this->totalSteps) {
            $this->currentStep = $step;
        }
    }

    private function validateCurrentStep()
    {
        $stepValidationRules = [
            1 => ['title', 'slug', 'category_id', 'difficulty_level'],
            2 => ['description'],
            3 => [], // Pricing step - validate dynamically based on is_free
            4 => [], // Additional info - all optional
            5 => [], // Review step - no additional validation
        ];

        if (isset($stepValidationRules[$this->currentStep])) {
            $rules = [];
            foreach ($stepValidationRules[$this->currentStep] as $field) {
                if (isset($this->rules[$field])) {
                    $rules[$field] = $this->rules[$field];
                }
            }

            // Add dynamic pricing validation
            if ($this->currentStep === 3 && !$this->is_free) {
                $rules['price'] = 'required|numeric|min:0.01|max:9999';
            }

            if (!empty($rules)) {
                $this->validate($rules);
            }
        }
    }

    public function addLearningOutcome()
    {
        $this->learning_outcomes[] = '';
    }

    public function removeLearningOutcome($index)
    {
        if (count($this->learning_outcomes) > 1) {
            unset($this->learning_outcomes[$index]);
            $this->learning_outcomes = array_values($this->learning_outcomes);
        }
    }

    public function addPrerequisite()
    {
        $this->prerequisites[] = '';
    }

    public function removePrerequisite($index)
    {
        if (count($this->prerequisites) > 1) {
            unset($this->prerequisites[$index]);
            $this->prerequisites = array_values($this->prerequisites);
        }
    }

    public function addFaq()
    {
        $this->faqs[] = ['question' => '', 'answer' => ''];
    }

    public function removeFaq($index)
    {
        if (count($this->faqs) > 1) {
            unset($this->faqs[$index]);
            $this->faqs = array_values($this->faqs);
        }
    }

    public function updateCourse()
    {
        $this->save();
    }

    public function updatedTitle($value)
    {
        if (!empty($value)) {
            $this->slug = 'course-' . \Illuminate\Support\Str::slug($value);
        }
    }

    public function updatedIsFree($value)
    {
        if ($value) {
            $this->is_premium = false;
            $this->price = 0.00;
        }
    }

    public function updatedIsPremium($value)
    {
        if ($value) {
            $this->is_free = false;
            if ($this->price == 0) {
                $this->price = 9.99; // Default premium price
            }
        } else if (!$this->is_free) {
            // Regular paid course logic
            if ($this->price == 0) {
                $this->price = 4.99; // Default regular price
            }
        }
    }

    public function setPaidCourse()
    {
        $this->is_free = false;
        $this->is_premium = false;
        if ($this->price == 0) {
            $this->price = 4.99; // Default regular price
        }
    }

    public function save()
    {
        \Log::info('CourseForm save method called', ['isEditMode' => $this->isEditMode]);

        try {
            // Check if this is a scheduled submission
            if ($this->scheduled_publish_at && now()->lt($this->scheduled_publish_at)) {
                \Log::info('Course scheduled for future publication', ['scheduled_publish_at' => $this->scheduled_publish_at]);
                $this->dispatch('notify', [
                    'message' => 'Course has been scheduled for submission on ' . $this->scheduled_publish_at->format('M d, Y \a\t H:i'),
                    'type' => 'info'
                ]);
                return;
            }

            // Validate all data
            \Log::info('Validating course data');
            $this->validate();
            \Log::info('Course data validated successfully');

            // Clean up array fields by removing empty entries
            $this->learning_outcomes = array_filter($this->learning_outcomes ?? [], fn($outcome) => !empty(trim($outcome)));
            $this->prerequisites = array_filter($this->prerequisites ?? [], fn($prereq) => !empty(trim($prereq)));
            $this->faqs = array_filter($this->faqs ?? [], fn($faq) => !empty(trim($faq['question'] ?? '')) && !empty(trim($faq['answer'] ?? '')));

            // Prepare data array
            $data = $this->only([
                'title',
                'subtitle',
                'slug',
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
                'scheduled_publish_at',
            ]);

            \Log::debug('Prepared course data', $data);

            // Only allow admins to approve courses
            $user = Auth::user();
            if ($user->hasRole('super_admin') || $user->hasRole('academy_admin')) {
                $data['is_approved'] = $this->is_approved;
                \Log::info('Admin approval status set', ['is_approved' => $this->is_approved]);
            } else {
                $data['is_approved'] = false; // Regular users cannot approve their own courses
                \Log::info('Non-admin user, setting is_approved to false');
            }

            // Sanitize description
            if (!empty($data['description'])) {
                $data['description'] = strip_tags($data['description']);
            }

            // Handle thumbnail upload
            if ($this->thumbnail) {
                \Log::info('Processing thumbnail upload');
                $data['thumbnail'] = $this->thumbnail->store('thumbnails', 'public');
                \Log::info('Thumbnail stored', ['path' => $data['thumbnail']]);
            } elseif ($this->isEditMode && !$this->thumbnail) {
                // Preserve existing thumbnail when editing and no new file is uploaded
                $data['thumbnail'] = $this->course->thumbnail;
                \Log::info('Preserving existing thumbnail', ['path' => $data['thumbnail']]);
            }

            // Handle publishing dates
            $now = now();
            if (isset($data['scheduled_publish_at']) && $now->gt($data['scheduled_publish_at'])) {
                $data['published_at'] = $data['scheduled_publish_at'];
                \Log::info('Setting published_at from scheduled_publish_at', ['published_at' => $data['published_at']]);
            } elseif ($data['is_published'] && !isset($data['scheduled_publish_at'])) {
                $data['published_at'] = $now;
                \Log::info('Setting published_at to now', ['published_at' => $data['published_at']]);
            }

            if ($this->isEditMode) {
                $this->course->update($data);
                $message = 'Course updated successfully!';
                $type = 'success';
                
                // Show success notification
                $this->dispatch('notify', ['message' => $message, 'type' => $type]);
                
                // Dispatch the correct event name that matches the JavaScript listener
                $this->dispatch('redirect-after-delay', [
                    'url' => route('all-course'),
                    'delay' => 1500 // 1.5 seconds delay to show the notification
                ]);
            } else {
                \Log::info('Creating new course');
                $data['instructor_id'] = Auth::id();
                $course = Course::create($data);
                $message = 'Course created successfully and submitted for approval!';
                $type = 'success';
                
                // Use string interpolation to ensure the URL is properly constructed
                $redirectUrl = "/dashboard/courses/{$course->id}/builder";
                
                \Log::info('Course created successfully', ['course_id' => $course->id]);
                \Log::info('Redirect URL', ['url' => $redirectUrl]);
                
                // Show success notification
                $this->dispatch('notify', ['message' => $message, 'type' => $type]);
                
                // Dispatch the correct event name that matches the JavaScript listener
                $this->dispatch('redirect-after-delay', [
                    'url' => $redirectUrl,
                    'delay' => 1500 // 1.5 seconds delay to show the notification
                ]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in course form', ['errors' => $e->errors()]);
            $this->dispatch('notify', [
                'message' => 'Please check the form for errors: ' . collect($e->errors())->flatten()->first(),
                'type' => 'error'
            ]);

            // Re-throw to show validation errors in the form
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error saving course', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function suggestAiContent(string $field)
    {
        // Placeholder for future AI integration
        $this->dispatch('notify', ['message' => 'AI suggestion feature coming soon!', 'type' => 'info']);
    }

    public function render()
    {
        if ($this->isEditMode) {
            return view('livewire.course-management.edit-course', [
                'categories' => $this->categories,
            ]);
        } else {
            return view('livewire.course-management.create-course', [
                'categories' => $this->categories,
                'difficultyLevels' => $this->difficultyLevels,
            ]);
        }
    }
}