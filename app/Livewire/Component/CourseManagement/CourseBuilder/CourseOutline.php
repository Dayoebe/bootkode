<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Assessment;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Computed;

class CourseOutline extends Component
{
    public Course $course;
    public $activeLessonId;
    public $searchTerm = '';
    public $filterType = 'all';
    public $selectedLessons = [];
    
    // Section management
    public $isAddingSection = false;
    public $newSectionTitle = '';
    public $newSectionDescription = '';
    public $editingSectionId = null;
    public $editingSectionTitle = '';
    public $editingSectionDescription = '';
    
    // Lesson management
    public $isAddingLessonToSectionId = null;
    public $newLessonTitle = '';
    public $newLessonDescription = '';
    public $newLessonContentType = 'text';
    public $newLessonDuration = 0;
    public $editingLessonId = null;
    public $editingLessonTitle = '';
    
    // Assessment management
    public $isAddingAssessmentToSectionId = null;
    public $newAssessmentTitle = '';
    public $newAssessmentDescription = '';
    public $newAssessmentType = 'project'; // Default to project
    public $newAssessmentDurationMinutes = 0;
    public $newAssessmentDeadline = null;
    public $editingAssessmentId = null;
    public $editingAssessmentTitle = '';

    protected $rules = [
        'newSectionTitle' => 'required|string|max:255',
        'newSectionDescription' => 'nullable|string|max:1000',
        'newLessonTitle' => 'required|string|max:255',
        'newLessonDescription' => 'nullable|string|max:1000',
        'newLessonDuration' => 'nullable|integer|min:0|max:1440',
        'editingSectionTitle' => 'required|string|max:255',
        'editingSectionDescription' => 'nullable|string|max:1000',
        'editingLessonTitle' => 'required|string|max:255',
        'newAssessmentTitle' => 'required|string|max:255',
        'newAssessmentDescription' => 'nullable|string|max:1000',
        'newAssessmentType' => 'required|in:project,quiz,assignment',
        'newAssessmentDurationMinutes' => 'nullable|integer|min:0|max:1440',
        'newAssessmentDeadline' => 'nullable|date|after:now',
        'editingAssessmentTitle' => 'required|string|max:255',
    ];

    #[Computed]
    public function courseStats()
    {
        return [
            'total_sections' => $this->course->sections()->count(),
            'total_lessons' => $this->course->allLessons()->count(),
            'total_assessments' => $this->course->assessments()->count(),
            'total_projects' => $this->course->assessments()->where('type', 'project')->count(),
            'total_quizzes' => $this->course->assessments()->where('type', 'quiz')->count(),
            'total_assignments' => $this->course->assessments()->where('type', 'assignment')->count(),
            'total_enrollments' => $this->course->enrollments()->count(),
            'total_duration' => $this->course->allLessons()->sum('duration_minutes'),
            'total_storage' => $this->course->allLessons()->sum('size_mb'),
            'completion_percentage' => $this->course->allLessons()->count() > 0 
                ? round(($this->course->allLessons()->whereNotNull('content')->count() / $this->course->allLessons()->count()) * 100) 
                : 0,
        ];
    }

    #[Computed]
    public function filteredSections()
    {
        $query = $this->course->sections()->with(['lessons', 'assessments']);

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('lessons', function ($q) {
                      $q->where('title', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
                  })
                  ->orWhereHas('assessments', function ($q) {
                      $q->where('title', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
                  });
            });
        }

        if ($this->filterType !== 'all') {
            $query->whereHas('lessons', function ($q) {
                $q->where('content_type', $this->filterType);
            });
        }

        return $query->orderBy('order')->get();
    }

    public function showAddSectionForm()
    {
        $this->isAddingSection = true;
    }

    public function addSection()
    {
        $this->validate([
            'newSectionTitle' => $this->rules['newSectionTitle'],
            'newSectionDescription' => $this->rules['newSectionDescription'],
        ]);

        $section = $this->course->sections()->create([
            'title' => $this->newSectionTitle,
            'description' => $this->newSectionDescription,
            'slug' => Str::slug($this->newSectionTitle),
            'order' => $this->course->sections()->count() + 1,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Section added successfully!');
        $this->dispatch('course-updated');
        $this->cancelAddSection();
    }

    public function cancelAddSection()
    {
        $this->isAddingSection = false;
        $this->newSectionTitle = '';
        $this->newSectionDescription = '';
    }

    public function showAddLessonForm($sectionId)
    {
        $this->isAddingLessonToSectionId = $sectionId;
    }

    public function addLesson()
    {
        $this->validate([
            'newLessonTitle' => $this->rules['newLessonTitle'],
            'newLessonDescription' => $this->rules['newLessonDescription'],
            'newLessonContentType' => $this->rules['newLessonContentType'],
            'newLessonDuration' => $this->rules['newLessonDuration'],
        ]);

        $section = Section::findOrFail($this->isAddingLessonToSectionId);
        $section->lessons()->create([
            'title' => $this->newLessonTitle,
            'description' => $this->newLessonDescription,
            'content_type' => $this->newLessonContentType,
            'duration_minutes' => $this->newLessonDuration,
            'slug' => Str::slug($this->newLessonTitle),
            'order' => $section->lessons()->count() + 1,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Lesson added successfully!');
        $this->dispatch('course-updated');
        $this->cancelAddLesson();
    }

    public function cancelAddLesson()
    {
        $this->isAddingLessonToSectionId = null;
        $this->newLessonTitle = '';
        $this->newLessonDescription = '';
        $this->newLessonContentType = 'text';
        $this->newLessonDuration = 0;
    }

    public function showAddAssessmentForm($sectionId)
    {
        $this->isAddingAssessmentToSectionId = $sectionId;
    }

    public function addAssessment()
    {
        $this->validate([
            'newAssessmentTitle' => $this->rules['newAssessmentTitle'],
            'newAssessmentDescription' => $this->rules['newAssessmentDescription'],
            'newAssessmentType' => $this->rules['newAssessmentType'],
            'newAssessmentDurationMinutes' => $this->rules['newAssessmentDurationMinutes'],
            'newAssessmentDeadline' => $this->rules['newAssessmentDeadline'],
        ]);

        $section = Section::findOrFail($this->isAddingAssessmentToSectionId);
        $section->assessments()->create([
            'course_id' => $this->course->id,
            'title' => $this->newAssessmentTitle,
            'description' => $this->newAssessmentDescription,
            'type' => $this->newAssessmentType,
            'estimated_duration_minutes' => $this->newAssessmentDurationMinutes,
            'deadline' => $this->newAssessmentDeadline,
            'slug' => Str::slug($this->newAssessmentTitle),
            'order' => $section->assessments()->count() + 1,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Assessment added successfully!');
        $this->dispatch('course-updated');
        $this->cancelAddAssessment();
    }

    public function cancelAddAssessment()
    {
        $this->isAddingAssessmentToSectionId = null;
        $this->newAssessmentTitle = '';
        $this->newAssessmentDescription = '';
        $this->newAssessmentType = 'project';
        $this->newAssessmentDurationMinutes = 0;
        $this->newAssessmentDeadline = null;
    }

    public function editAssessment($assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $this->editingAssessmentId = $assessmentId;
        $this->editingAssessmentTitle = $assessment->title;
    }

    public function updateAssessment()
    {
        $this->validate(['editingAssessmentTitle' => $this->rules['editingAssessmentTitle']]);

        $assessment = Assessment::findOrFail($this->editingAssessmentId);
        $assessment->update([
            'title' => $this->editingAssessmentTitle,
            'slug' => Str::slug($this->editingAssessmentTitle),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Assessment updated successfully!');
        $this->dispatch('course-updated');
        $this->editingAssessmentId = null;
    }

    public function deleteAssessment($assessmentId)
    {
        $assessment = Assessment::findOrFail($assessmentId);
        $assessment->delete();

        $this->dispatch('notify', type: 'success', message: 'Assessment deleted successfully!');
        $this->dispatch('course-updated');
    }

    public function reorderAssessments($sectionId, $orderedIds)
    {
        foreach ($orderedIds as $order => $id) {
            Assessment::where('id', $id)->update(['order' => $order + 1]);
        }
        $this->dispatch('course-updated');
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder.course-outline', [
            'filteredSections' => $this->filteredSections,
            'courseStats' => $this->courseStats
        ]);
    }
}