<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.dashboard', [
    'description' => 'Create new courses with details, pricing, and content',
    'icon' => 'fas fa-plus-circle',
    'active' => 'instructor.course-form'
])]
class CourseForm extends Component
{
    use WithFileUploads;

    public $categories = [];
    public string $pageTitle = 'Create New Course';

    // Properties matching model fields
    public $title = '';
    public $subtitle = '';
    public $slug = '';
    public $description = '';
    public $category_id = '';
    public $difficulty_level = 'beginner';
    public $is_published = false;
    public $is_free = true;
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

    public function mount()
    {
        $this->categories = Cache::remember('course_categories', 3600, fn() => CourseCategory::orderBy('name')->get());
    }
    public function removeThumbnail()
    {
        Log::info('CourseForm: Removing thumbnail');
        $this->thumbnail = null;

        $this->dispatch('notify', [
            'message' => 'Thumbnail removed.',
            'type' => 'info'
        ]);
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
                $this->price = 9.99;
            }
        } else if (!$this->is_free) {
            if ($this->price == 0) {
                $this->price = 4.99;
            }
        }
    }

    public function setPaidCourse()
    {
        $this->is_free = false;
        $this->is_premium = false;
        if ($this->price == 0) {
            $this->price = 4.99;
        }
    }

    public function save()
    {
        Log::info('CourseForm: Creating new course');

        try {
            // Check if this is a scheduled submission
            if ($this->scheduled_publish_at && now()->lt($this->scheduled_publish_at)) {
                Log::info('Course scheduled for future publication', ['scheduled_publish_at' => $this->scheduled_publish_at]);
                $this->dispatch('notify', [
                    'message' => 'Course has been scheduled for submission on ' . $this->scheduled_publish_at->format('M d, Y \a\t H:i'),
                    'type' => 'info'
                ]);
                return;
            }

            // Validate all data
            $this->validate();

            // Clean up array fields
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

            // Set instructor and approval
            $data['instructor_id'] = Auth::id();

            $user = Auth::user();
            if ($user->hasRole('super_admin') || $user->hasRole('academy_admin')) {
                $data['is_approved'] = true;
            } else {
                $data['is_approved'] = false;
            }

            // Sanitize description
            if (!empty($data['description'])) {
                $data['description'] = strip_tags($data['description']);
            }

            // Handle thumbnail upload
            if ($this->thumbnail) {
                Log::info('CourseForm: Processing thumbnail upload');
                $data['thumbnail'] = $this->thumbnail->store('thumbnails', 'public');
                Log::info('CourseForm: Thumbnail stored', ['path' => $data['thumbnail']]);
            }

            // Handle publishing dates
            $now = now();
            if (isset($data['scheduled_publish_at']) && $now->gt($data['scheduled_publish_at'])) {
                $data['published_at'] = $data['scheduled_publish_at'];
            } elseif ($data['is_published'] && !isset($data['scheduled_publish_at'])) {
                $data['published_at'] = $now;
            }

            // Create course
            $course = Course::create($data);

            Log::info('CourseForm: Course created successfully', ['course_id' => $course->id]);

            $this->dispatch('notify', [
                'message' => 'Course created successfully and submitted for approval!',
                'type' => 'success'
            ]);

            // Redirect to course builder
            return redirect()->route('course-builder', ['course' => $course->id]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('CourseForm: Validation error', ['errors' => $e->errors()]);
            $this->dispatch('notify', [
                'message' => 'Please check the form for errors: ' . collect($e->errors())->flatten()->first(),
                'type' => 'error'
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('CourseForm: Error creating course', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }


    public function getThumbnailPreview()
    {
        if ($this->thumbnail) {
            try {
                // Store temporary file and return public URL
                $tempPath = $this->thumbnail->store('temp-thumbnails', 'public');
                return asset('storage/' . $tempPath);
            } catch (\Exception $e) {
                \Log::error('Error creating thumbnail preview', ['error' => $e->getMessage()]);
                return null;
            }
        }

        return null;
    }

    public function updatedThumbnail()
    {
        Log::info('Thumbnail updated');

        if ($this->thumbnail) {
            try {
                $this->validateOnly('thumbnail');

                // Clean up any previous temp files
                $this->cleanupTempFiles();

                Log::info('Thumbnail validation passed', [
                    'file_size' => $this->thumbnail->getSize(),
                    'file_type' => $this->thumbnail->getMimeType(),
                    'file_name' => $this->thumbnail->getClientOriginalName()
                ]);

                $this->dispatch('notify', [
                    'message' => 'Thumbnail uploaded successfully!',
                    'type' => 'success'
                ]);

            } catch (\Illuminate\Validation\ValidationException $e) {
                Log::error('Thumbnail validation failed', ['errors' => $e->errors()]);
                $this->thumbnail = null;
                throw $e;
            } catch (\Exception $e) {
                Log::error('Error processing thumbnail', ['error' => $e->getMessage()]);
                $this->thumbnail = null;
                $this->dispatch('notify', [
                    'message' => 'Error uploading thumbnail: ' . $e->getMessage(),
                    'type' => 'error'
                ]);
            }
        }
    }

    private function cleanupTempFiles()
    {
        try {
            // Clean up temp files older than 1 hour
            $tempFiles = Storage::disk('public')->files('temp-thumbnails');
            foreach ($tempFiles as $file) {
                if (Storage::disk('public')->lastModified($file) < now()->subHour()->timestamp) {
                    Storage::disk('public')->delete($file);
                }
            }
        } catch (\Exception $e) {
            Log::warning('Could not cleanup temp files', ['error' => $e->getMessage()]);
        }
    }

    // Add this computed property for the view
    public function getThumbnailUrlProperty()
    {
        if ($this->thumbnail) {
            return $this->getThumbnailPreview();
        }

        // For edit mode, return existing thumbnail
        if (isset($this->existingThumbnail) && $this->existingThumbnail && !($this->shouldRemoveThumbnail ?? false)) {
            return asset('storage/' . $this->existingThumbnail);
        }

        return null;
    }
    public function render()
    {
        return view('livewire.course-management.create-course', [
            'categories' => $this->categories,
            'difficultyLevels' => $this->difficultyLevels,
            'isEditMode' => false, // Always false for create component
        ]);
    }
}