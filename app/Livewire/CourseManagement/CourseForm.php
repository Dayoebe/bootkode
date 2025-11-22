<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

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

    // Properties
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
    public $thumbnailPreview = null;
    public $existingThumbnail = null;
    public $shouldRemoveThumbnail = false;
    public $estimated_duration_minutes = null;
    public $price = 0.00;
    public $target_audience = '';
    public array $learning_outcomes = [''];
    public array $prerequisites = [''];
    public $syllabus_overview = '';
    public array $faqs = [['question' => '', 'answer' => '']];
    public $completion_rate_threshold = 80.00;
    public $scheduled_publish_at = null;
    public array $materials_included = [''];
    public array $tags = [''];

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
        'materials_included' => 'array|max:10',
        'materials_included.*' => 'nullable|string|max:255',
        'tags' => 'array|max:10',
        'tags.*' => 'nullable|string|max:100',
    ];

    public function mount()
    {
        $this->categories = Cache::remember('course_categories', 3600, fn() => CourseCategory::orderBy('name')->get());
        
        if (empty($this->materials_included)) {
            $this->materials_included = [''];
        }
        if (empty($this->tags)) {
            $this->tags = [''];
        }
    }
    
    public function removeThumbnail()
    {
        $this->thumbnail = null;
        $this->thumbnailPreview = null;
        $this->shouldRemoveThumbnail = true;

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
            3 => [],
            4 => [],
            5 => [],
        ];

        if (isset($stepValidationRules[$this->currentStep])) {
            $rules = [];
            foreach ($stepValidationRules[$this->currentStep] as $field) {
                if (isset($this->rules[$field])) {
                    $rules[$field] = $this->rules[$field];
                }
            }

            if ($this->currentStep === 3 && !$this->is_free) {
                $rules['price'] = 'required|numeric|min:0.01|max:9999';
            }

            if (!empty($rules)) {
                $this->validate($rules);
            }
        }
    }
  
    public function addMaterial()
    {
        if (count($this->materials_included) < 10) {
            $this->materials_included[] = '';
        }
    }
    
    public function removeMaterial($index)
    {
        if (count($this->materials_included) > 1) {
            unset($this->materials_included[$index]);
            $this->materials_included = array_values($this->materials_included);
        }
    }
    
    public function addTag()
    {
        if (count($this->tags) < 10) {
            $this->tags[] = '';
        }
    }
    
    public function removeTag($index)
    {
        if (count($this->tags) > 1) {
            unset($this->tags[$index]);
            $this->tags = array_values($this->tags);
        }
    }
    
    public function saveDraft()
    {
        $this->is_published = false;
        $this->save(true);
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

    public function save($isDraft = false)
    {
        try {
            // Check scheduled submission
            if (!$isDraft && $this->scheduled_publish_at && now()->lt($this->scheduled_publish_at)) {
                $this->dispatch('notify', [
                    'message' => 'Course scheduled for ' . $this->scheduled_publish_at->format('M d, Y'),
                    'type' => 'info'
                ]);
                return;
            }

            // Validate
            if (!$isDraft) {
                $this->validate();
            } else {
                $this->validate([
                    'title' => 'required|string|min:3|max:255',
                    'slug' => 'required|string|min:3|max:255|regex:/^[a-z0-9\-]+$/|unique:courses,slug',
                    'category_id' => 'required|exists:course_categories,id',
                ]);
            }

            // Clean arrays
            $this->learning_outcomes = array_filter($this->learning_outcomes ?? [], fn($outcome) => !empty(trim($outcome)));
            $this->prerequisites = array_filter($this->prerequisites ?? [], fn($prereq) => !empty(trim($prereq)));
            $this->faqs = array_filter($this->faqs ?? [], fn($faq) => !empty(trim($faq['question'] ?? '')) && !empty(trim($faq['answer'] ?? '')));
            $this->materials_included = array_filter($this->materials_included ?? [], fn($material) => !empty(trim($material)));
            $this->tags = array_filter($this->tags ?? [], fn($tag) => !empty(trim($tag)));

            // Prepare data
            $data = $this->only([
                'title', 'subtitle', 'slug', 'description', 'category_id', 'difficulty_level',
                'is_published', 'is_free', 'is_premium', 'target_audience', 'learning_outcomes',
                'prerequisites', 'syllabus_overview', 'faqs', 'completion_rate_threshold',
                'estimated_duration_minutes', 'price', 'scheduled_publish_at',
                'materials_included', 'tags',
            ]);

            $data['instructor_id'] = Auth::id();

            // Approval
            $user = Auth::user();
            $data['is_approved'] = $user->hasRole('super_admin') || $user->hasRole('academy_admin');

            // Sanitize description
            if (!empty($data['description'])) {
                $data['description'] = strip_tags($data['description']);
            }

            // Handle thumbnail
            if ($this->thumbnail) {
                $data['thumbnail'] = $this->thumbnail->store('thumbnails', 'public');
            }

            // Publishing dates
            $now = now();
            if (!$isDraft) {
                if (isset($data['scheduled_publish_at']) && $now->gt($data['scheduled_publish_at'])) {
                    $data['published_at'] = $data['scheduled_publish_at'];
                } elseif ($data['is_published'] && !isset($data['scheduled_publish_at'])) {
                    $data['published_at'] = $now;
                }
            }

            // Create
            $course = Course::create($data);

            $message = $isDraft ? 'Course saved as draft!' : 'Course created successfully!';

            session()->flash('success', $message);

            return $this->redirect(route('course-builder', ['course' => $course->id]), navigate: true);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify', [
                'message' => 'Please check form errors: ' . collect($e->errors())->flatten()->first(),
                'type' => 'error'
            ]);
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function updatedThumbnail()
    {
        if ($this->thumbnail) {
            try {
                $this->validateOnly('thumbnail');
                $this->shouldRemoveThumbnail = false;
                $this->thumbnailPreview = 'data:' . $this->thumbnail->getMimeType() . ';base64,' . base64_encode($this->thumbnail->get());

                $this->dispatch('notify', [
                    'message' => 'Thumbnail uploaded!',
                    'type' => 'success'
                ]);
            } catch (\Exception $e) {
                $this->thumbnail = null;
                $this->thumbnailPreview = null;
                $this->dispatch('notify', [
                    'message' => 'Error uploading thumbnail.',
                    'type' => 'error'
                ]);
            }
        }
    }
    
    public function render()
    {
        return view('livewire.course-management.create-course', [
            'categories' => $this->categories,
            'difficultyLevels' => $this->difficultyLevels,
            'isEditMode' => false,
        ]);
    }
}