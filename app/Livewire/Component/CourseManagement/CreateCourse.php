<?php

namespace App\Livewire\Component\CourseManagement;

use Livewire\Component;
use Livewire\WithFileUploads; // Trait for file uploads
use App\Models\Course;
use App\Models\User;
use App\Models\CourseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout; // Import the Layout attribute
use Livewire\Attributes\Rule;

#[Layout('layouts.dashboard', ['title' => 'Create New Course', 'description' => 'Create and manage new courses, including setting details and uploading content', 'icon' => 'fas fa-plus-circle', 'active' => 'instructor.create-course'])] // Set the layout for this page component
class CreateCourse extends Component
{
    use WithFileUploads;

    // Course properties
    public $is_premium = false;
    public $categories; // To populate the category dropdown

    public ?Course $course = null;

    #[Rule('required|string|min:3|max:255')]
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

    #[Rule('nullable|image|max:2048')] // 2MB max for thumbnail
    public $thumbnail;

    #[Rule('nullable|integer|min:1')]
    public $estimated_duration_minutes = null;

    #[Rule('required|numeric|min:0')]
    public $price = 0.00;

    /**
     * Mount the component and load necessary data.
     */
    public function mount($course = null)
    {
        $this->categories = CourseCategory::orderBy('name')->get();
        $user = Auth::user();

        // If the user is an instructor, they might not be able to set is_approved directly
        // This logic can be refined with policies later.
        if ($user->hasRole('instructor')) {
            $this->is_approved = false; // Instructors cannot self-approve
        }
        if ($course) {
            $this->course = Course::findOrFail($course);
            $this->title = $this->course->title;
            $this->subtitle = $this->course->subtitle;
            $this->description = $this->course->description;
            $this->categoryId = $this->course->category_id;
            $this->difficulty_level = $this->course->difficulty_level;
            $this->is_published = $this->course->is_published;
        }
    }
    


    /**
     * Validation rules for course creation.
     */
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255|unique:courses,title',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:course_categories,id',
            'thumbnail' => 'nullable|image|max:2048', // 2MB max
            'difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'estimated_duration_minutes' => 'nullable|integer|min:1',
            'price' => 'required_if:is_premium,true|numeric|min:0|nullable',
            'is_premium' => 'boolean',
            'is_published' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Custom validation messages.
     */
    protected $messages = [
        'title.unique' => 'A course with this title already exists.',
        'price.required_if' => 'Price is required for premium courses.',
    ];

    /**
     * Create a new course.
     */
    public function createCourse()
    {
        $this->validate();

        $thumbnailPath = null;
        if ($this->thumbnail) {
            // Store the thumbnail in the 'public/thumbnails' directory
            $thumbnailPath = $this->thumbnail->store('thumbnails', 'public');
        }

        Course::create([
            'instructor_id' => Auth::id(), // Assign current user as instructor
            'category_id' => $this->category_id,
            'title' => $this->title,
            'slug' => Str::slug($this->title), // Generate slug automatically
            'description' => $this->description,
            'thumbnail' => $thumbnailPath,
            'difficulty_level' => $this->difficulty_level,
            'estimated_duration_minutes' => $this->estimated_duration_minutes,
            'price' => $this->is_premium ? ($this->price ?? 0.00) : 0.00, // Set price to 0 if not premium
            'is_premium' => $this->is_premium,
            'is_published' => $this->is_published,
            'is_approved' => $this->is_approved, // Will be false for instructors, can be true for admins
        ]);

        $this->dispatch('notify', 'Course created successfully!', 'success');

        // Reset form fields after successful creation
        $this->reset([
            'title', 'description', 'category_id', 'thumbnail',
            'difficulty_level', 'estimated_duration_minutes', 'price',
            'is_premium', 'is_published', 'is_approved'
        ]);

        // Optionally redirect to the "All Courses" page
        return redirect()->route('all-course');
    }

    public function saveCourse()
    {
        $this->validate();

        if ($this->course) {
            // Update an existing course
            $this->course->update([
                'title' => $this->title,
                'subtitle' => $this->subtitle,
                'description' => $this->description,
                'category_id' => $this->categoryId,
                'difficulty_level' => $this->difficulty_level,
                'is_published' => $this->is_published,
            ]);
            $message = 'Course updated successfully!';
        } else {
            // Create a new course
            Course::create([
                'title' => $this->title,
                'subtitle' => $this->subtitle,
                'description' => $this->description,
                'category_id' => $this->categoryId,
                'difficulty_level' => $this->difficulty_level,
                'instructor_id' => Auth::id(), // Assign the current user as the instructor
                'is_published' => $this->is_published,
            ]);
            $message = 'Course created successfully!';
        }
        
        $this->dispatch('notify', $message, 'success');
        $this->redirect(route('all-course'), navigate: true);
    }


    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.component.course-management.create-course', [
            'categories' => CourseCategory::all(),
            'user'=> User::all(),
        ]);
    }
}