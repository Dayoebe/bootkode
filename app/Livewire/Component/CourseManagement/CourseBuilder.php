<?php

namespace App\Livewire\Component\CourseManagement;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\CourseCategory;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard', ['title' => 'Course Builder', 'description' => 'Build and manage your course content including sections, lessons, and quizzes', 'icon' => 'fas fa-cogs', 'active' => 'instructor.course-builder'])]
class CourseBuilder extends Component
{
    use WithFileUploads;

    public Course $course;
    public $activeLessonId = null;
    public $activeQuizId = null;
    public $showSettingsModal = false;
    public $thumbnail;
    public $categories;
    public $difficultyLevels = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        // 'expert' => 'Expert'
    ];


    #[Computed]
    public function isCourseEditable()
    {
        return Auth::user()->isInstructor() || Auth::user()->isAcademyAdmin() || Auth::user()->isSuperAdmin();
    }

    public function mount(Course $course)
    {
        $this->categories = CourseCategory::all();
        // Check if user has permission to edit this course
        if (!Auth::user()->isInstructor() && !Auth::user()->isAcademyAdmin() && !Auth::user()->isSuperAdmin()) {
            abort(403, 'Unauthorized access to course builder.');
        }

        if (Auth::user()->isInstructor() && $course->instructor_id !== Auth::id()) {
            abort(403, 'You can only edit your own courses.');
        }

        $this->course = $course;
        $this->course->load(['sections.lessons', 'modules.lessons', 'modules.quizzes.questions.options']);

        // Select first lesson if available
        if ($this->course->sections->isNotEmpty() && $this->course->sections->first()->lessons->isNotEmpty()) {
            $firstLesson = $this->course->sections->first()->lessons->first();
            $this->activeLessonId = $firstLesson->id;
        }
    }

    #[On('open-course-settings')]
public function showSettingsModal()
{
    $this->showSettingsModal = true;
}


    public function closeSettingsModal()
    {
        $this->showSettingsModal = false;
        $this->thumbnail = null; // Reset thumbnail upload
    }

    public function saveCourseSettings()
    {
        $this->validate([
            'course.title' => 'required|string|max:255',
            'course.description' => 'required|string',
            'course.category_id' => 'required|exists:course_categories,id',
            'course.difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'course.price' => 'required|numeric|min:0',
            'course.is_premium' => 'boolean',
            'thumbnail' => 'nullable|image|max:2048', // 2MB max
        ]);

        try {
            // Handle thumbnail upload
            if ($this->thumbnail) {
                $path = $this->thumbnail->store('course-thumbnails', 'public');
                $this->course->thumbnail = $path;
            }

            $this->course->save();

            $this->dispatch('notify', type: 'success', message: 'Course settings updated successfully!');
            $this->dispatch('course-updated');
            $this->closeSettingsModal();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to update course: ' . $e->getMessage());
        }
    }
    #[On('lesson-selected')]
    public function selectLesson($lessonId)
    {
        $this->activeLessonId = $lessonId;
        $this->activeQuizId = null;
    }

    #[On('quiz-selected')]
    public function selectQuiz($quizId)
    {
        $this->activeQuizId = $quizId;
        $this->activeLessonId = null;
    }

    #[On('course-updated')]
    public function refreshCourse()
    {
        $this->course->refresh();
        $this->course->load(['sections.lessons', 'modules.lessons', 'modules.quizzes.questions.options']);
    }
    #[On('save-content-requested')]
    public function handleSaveContent()
    {
        // This will trigger any components that need to save their content
        $this->dispatch('save-content');

        // Refresh the course data
        $this->course->refresh();
        $this->dispatch('course-updated');
    }


   


        public function render()
        {
            return view('livewire.component.course-management.course-builder', [
                'showSettingsModal' => $this->showSettingsModal,
                'categories' => $this->categories,
                'difficultyLevels' => $this->difficultyLevels
            ]);
        }


}