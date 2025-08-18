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
    ];

    #[Computed]
    public function isCourseEditable()
    {
        return Auth::user()->isInstructor() || Auth::user()->isAcademyAdmin() || Auth::user()->isSuperAdmin();
    }

    // public function mount(Course $course)
    // {
    //     if (!Auth::user()->isInstructor() && !Auth::user()->isAcademyAdmin() && !Auth::user()->isSuperAdmin()) {
    //         abort(403, 'Unauthorized access to course builder.');
    //     }
    //     if (Auth::user()->isInstructor() && $course->instructor_id !== Auth::id()) {
    //         abort(403, 'You can only edit your own courses.');
    //     }

    //     $this->categories = CourseCategory::all();

    //     try {
    //         $this->course = $course->fresh(); // Ensure fresh data
    //         $this->course->load([
    //             'sections.lessons', // Lessons in sections
    //             'sections.assessments.submissions', // Assessments (projects/quizzes) with submissions
    //             'assessments.questions.options', // Questions for quiz-type assessments
    //         ]);
    //         if ($this->course->sections->isNotEmpty() && $this->course->sections->first()->lessons->isNotEmpty()) {
    //             $this->activeLessonId = $this->course->sections->first()->lessons->first()->id;
    //         }
    //     } catch (\Exception $e) {
    //         $this->dispatch('notify', type: 'error', message: 'Failed to load course data: ' . $e->getMessage());
    //         abort(500, 'Course loading failed.');
    //     }
    // }
    public function mount(Course $course)
{
    if (!$this->isCourseEditable) {
        abort(403, 'Unauthorized access to course builder.');
    }
    if (Auth::user()->isInstructor() && $course->instructor_id !== Auth::id()) {
        abort(403, 'You can only edit your own courses.');
    }

    $this->categories = CourseCategory::pluck('name', 'id')->toArray();
    $this->course = $course->load([
        'sections' => fn($query) => $query->with(['lessons' => fn($q) => $q->take(10)]), // Limit initial lessons
        'assessments.questions.options', // Limit quizzes
    ]);

    $this->activeLessonId = $this->course->sections->first()?->lessons->first()->id ?? null;
}

    public function updateCourseSettings()
    {
        $this->validate([
            'course.title' => 'required|string|max:255',
            'course.description' => 'nullable|string|max:1000',
            'course.category_id' => 'required|exists:course_categories,id',
            'course.difficulty_level' => 'required|in:beginner,intermediate,advanced',
            'course.price' => 'required|numeric|min:0',
            'course.is_premium' => 'boolean',
            'thumbnail' => 'nullable|image|max:2048', // 2MB max
        ]);

        try {
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
        try {
            $this->course->refresh();
            $this->course->load([
                'sections.lessons',
                'sections.assessments.submissions',
                'assessments.questions.options',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to refresh course: ' . $e->getMessage());
        }
    }

    #[On('save-content-requested')]
    public function handleSaveContent()
    {
        $this->dispatch('save-content');
        $this->course->refresh();
        $this->dispatch('course-updated');
    }

    // New Feature: Quick Enrollment Preview
    public function previewEnrollments()
    {
        $enrollments = $this->course->enrollments()->with('user')->latest()->take(5)->get();
        $totalEnrollments = $this->course->enrollments()->count();

        $this->dispatch('open-enrollment-modal', [
            'total' => $totalEnrollments,
            'recent' => $enrollments->map(function ($enrollment) {
                return [
                    'user_name' => $enrollment->user->name,
                    'enrolled_at' => $enrollment->enrolled_at->diffForHumans(),
                ];
            })->toArray(),
        ]);
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