<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.dashboard', [
    'description' => 'Edit courses with details, pricing, and content',
    'icon' => 'fas fa-edit',
    'active' => 'instructor.course-form'
])]
class EditCourse extends Component
{
    use WithFileUploads;

    public Course $course;
    public $categories = [];
    public string $pageTitle;

    // File upload properties
    public $thumbnail;
    public $thumbnailPreview = null;
    public $shouldRemoveThumbnail = false;
    public $existingThumbnail = null;
    public $slug_manual_edit = false;

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

    // Form fields
    public $title = '';
    public $subtitle = '';
    public $slug = '';
    public $description = '';
    public $category_id = '';
    public $difficulty_level = 'beginner';
    public $is_published = false;
    public $is_approved = false;
    public $is_free = true;
    public $is_premium = false;
    public $estimated_duration_minutes = null;
    public $price = 0.00;
    public $target_audience = '';
    public $learning_outcomes = [];
    public $prerequisites = [];
    public $syllabus_overview = '';
    public $faqs = [];
    public $completion_rate_threshold = 80.00;
    public $scheduled_publish_at = null;
    public array $materials_included = [''];
    public array $tags = [''];

    public function mount(Course $course)
    {
        $this->course = $course;
        $this->pageTitle = 'Edit Course: ' . Str::limit($course->title, 30);
        $this->categories = Cache::remember('course_categories', 3600, fn() => CourseCategory::orderBy('name')->get());

        // Store existing thumbnail info
        $this->existingThumbnail = $course->thumbnail;

        // Fill form with course data
        $this->title = $course->title ?? '';
        $this->subtitle = $course->subtitle ?? '';
        $this->slug = $course->slug ?? '';
        $this->description = $course->description ?? '';
        $this->category_id = $course->category_id ?? '';
        $this->difficulty_level = $course->difficulty_level ?? 'beginner';
        $this->is_published = $course->is_published ?? false;
        $this->is_approved = $course->is_approved ?? false;
        $this->is_free = $course->is_free ?? true;
        $this->is_premium = $course->is_premium ?? false;
        $this->target_audience = $course->target_audience ?? '';
        $this->syllabus_overview = $course->syllabus_overview ?? '';
        $this->estimated_duration_minutes = $course->estimated_duration_minutes ?? null;
        $this->price = $course->price ?? 0.00;
        $this->completion_rate_threshold = $course->completion_rate_threshold ?? 80.00;
        $this->scheduled_publish_at = $course->scheduled_publish_at ?? null;

        // Initialize file properties
        $this->thumbnail = null;
        $this->shouldRemoveThumbnail = false;

        // Ensure arrays are properly initialized
        $this->learning_outcomes = is_array($course->learning_outcomes) && !empty($course->learning_outcomes)
            ? $course->learning_outcomes
            : [''];
        $this->prerequisites = is_array($course->prerequisites) && !empty($course->prerequisites)
            ? $course->prerequisites
            : [''];
        $this->faqs = is_array($course->faqs) && !empty($course->faqs)
            ? $course->faqs
            : [['question' => '', 'answer' => '']];
        $this->materials_included = is_array($course->materials_included) && !empty($course->materials_included)
            ? $course->materials_included
            : [''];
        $this->tags = is_array($course->tags) && !empty($course->tags)
            ? $course->tags
            : [''];
    }

    protected function rules()
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'subtitle' => 'nullable|string|max:255',
            'slug' => 'required|string|min:3|max:255|regex:/^[a-z0-9\-]+$/|unique:courses,slug,' . $this->course->id,
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
            'materials_included' => 'array|max:10',
            'materials_included.*' => 'nullable|string|max:255',
            'tags' => 'array|max:10',
            'tags.*' => 'nullable|string|max:100',
        ];
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
                $allRules = $this->rules();
                if (isset($allRules[$field])) {
                    $rules[$field] = $allRules[$field];
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

    public function updatedTitle($value)
    {
        if (!$this->slug_manual_edit && !empty($value)) {
            $this->slug = Str::slug($value);
        }
    }

    public function updatedSlug()
    {
        $this->slug_manual_edit = true;
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

    public function removeThumbnail()
    {
        $this->thumbnail = null;
        $this->thumbnailPreview = null;
        $this->shouldRemoveThumbnail = true;

        $this->dispatch('notify', [
            'message' => 'Thumbnail will be removed when you save.',
            'type' => 'info'
        ]);
    }

    public function saveDraft()
    {
        $this->is_published = false;
        $this->save(true);
    }

    public function save($isDraft = false)
    {
        try {
            // Validate
            if (!$isDraft) {
                $this->validate();
            } else {
                $this->validate([
                    'title' => 'required|string|min:3|max:255',
                    'slug' => 'required|string|min:3|max:255|regex:/^[a-z0-9\-]+$/|unique:courses,slug,' . $this->course->id,
                    'category_id' => 'required|exists:course_categories,id',
                ]);
            }

            // Clean up arrays
            $this->learning_outcomes = array_values(array_filter($this->learning_outcomes ?? [], fn($outcome) => !empty(trim($outcome))));
            $this->prerequisites = array_values(array_filter($this->prerequisites ?? [], fn($prereq) => !empty(trim($prereq))));
            $this->faqs = array_values(array_filter($this->faqs ?? [], fn($faq) => !empty(trim($faq['question'] ?? '')) && !empty(trim($faq['answer'] ?? ''))));
            $this->materials_included = array_values(array_filter($this->materials_included ?? [], fn($material) => !empty(trim($material))));
            $this->tags = array_values(array_filter($this->tags ?? [], fn($tag) => !empty(trim($tag))));

            // Prepare data
            $data = [
                'title' => $this->title,
                'subtitle' => $this->subtitle,
                'slug' => $this->slug,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'difficulty_level' => $this->difficulty_level,
                'is_published' => $this->is_published,
                'is_free' => $this->is_free,
                'is_premium' => $this->is_premium,
                'target_audience' => $this->target_audience,
                'learning_outcomes' => $this->learning_outcomes,
                'prerequisites' => $this->prerequisites,
                'syllabus_overview' => $this->syllabus_overview,
                'faqs' => $this->faqs,
                'completion_rate_threshold' => $this->completion_rate_threshold,
                'estimated_duration_minutes' => $this->estimated_duration_minutes,
                'price' => $this->price,
                'scheduled_publish_at' => $this->scheduled_publish_at,
                'materials_included' => $this->materials_included,
                'tags' => $this->tags,
            ];

            // Admin approval handling
            $user = Auth::user();
            if ($user->hasRole('super_admin') || $user->hasRole('academy_admin')) {
                $data['is_approved'] = $this->is_approved;
            } else {
                $data['is_approved'] = $this->course->is_approved;
            }

            // Handle thumbnail
            $this->handleThumbnailUpdate($data);

            // Handle publishing dates
            if (!$isDraft && $data['is_published'] && !$this->course->published_at && !$data['scheduled_publish_at']) {
                $data['published_at'] = now();
            }

            // Update course
            $this->course->update($data);

            // Reset flags
            $this->thumbnail = null;
            $this->thumbnailPreview = null;
            $this->shouldRemoveThumbnail = false;
            $this->existingThumbnail = $this->course->fresh()->thumbnail;

            $message = $isDraft ? 'Course saved as draft!' : 'Course updated successfully!';

            session()->flash('success', $message);

            // Redirect to all courses page
            return $this->redirect(route('all-course'), navigate: true);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    private function handleThumbnailUpdate(&$data)
    {
        if ($this->thumbnail) {
            // Delete old thumbnail
            if ($this->existingThumbnail && Storage::disk('public')->exists($this->existingThumbnail)) {
                Storage::disk('public')->delete($this->existingThumbnail);
            }
            $data['thumbnail'] = $this->thumbnail->store('thumbnails', 'public');
        } elseif ($this->shouldRemoveThumbnail) {
            // Remove thumbnail
            if ($this->existingThumbnail && Storage::disk('public')->exists($this->existingThumbnail)) {
                Storage::disk('public')->delete($this->existingThumbnail);
            }
            $data['thumbnail'] = null;
        } else {
            // Keep existing
            $data['thumbnail'] = $this->existingThumbnail;
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
                    'message' => 'Thumbnail uploaded successfully!',
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
            'isEditMode' => true,
        ]);
    }
}