<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Assessment;
use App\Models\Lesson;
use Livewire\Component;

class AssessmentManager extends Component
{
    public $lessonId;
    public $lesson;
    public $assessments = [];
    
    // Assessment creation form
    public $showCreateForm = false;
    public $assessmentType = 'quiz';
    public $assessmentTitle = '';
    public $assessmentDescription = '';
    public $passPercentage = 70;
    public $timeLimit = null;
    public $isMandatory = false;
    public $allowMultipleAttempts = true;
    public $showResultsImmediately = true;

    protected $rules = [
        'assessmentTitle' => 'required|string|max:255',
        'assessmentDescription' => 'nullable|string|max:1000',
        'assessmentType' => 'required|in:quiz,project,assignment,qna',
        'passPercentage' => 'required|integer|min:1|max:100',
        'timeLimit' => 'nullable|integer|min:1',
        'isMandatory' => 'boolean',
        'allowMultipleAttempts' => 'boolean',
        'showResultsImmediately' => 'boolean',
    ];

    public function mount($lessonId)
    {
        $this->lessonId = $lessonId;
        $this->lesson = Lesson::findOrFail($lessonId);
        $this->loadAssessments();
    }

    protected function loadAssessments()
    {
        $this->assessments = Assessment::where('lesson_id', $this->lessonId)
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        if (!$this->showCreateForm) {
            $this->resetForm();
        }
    }

    protected function resetForm()
    {
        $this->assessmentTitle = '';
        $this->assessmentDescription = '';
        $this->assessmentType = 'quiz';
        $this->passPercentage = 70;
        $this->timeLimit = null;
        $this->isMandatory = false;
        $this->allowMultipleAttempts = true;
        $this->showResultsImmediately = true;
    }

    public function createAssessment()
    {
        $this->validate();

        $assessment = Assessment::create([
            'lesson_id' => $this->lessonId,
            'section_id' => $this->lesson->section_id,
            'course_id' => $this->lesson->section->course_id,
            'title' => $this->assessmentTitle,
            'description' => $this->assessmentDescription,
            'type' => $this->assessmentType,
            'pass_percentage' => $this->passPercentage,
            'estimated_duration_minutes' => $this->timeLimit,
            'is_mandatory' => $this->isMandatory,
            'order' => count($this->assessments) + 1,
        ]);

        $this->loadAssessments();
        $this->resetForm();
        $this->showCreateForm = false;
        
        session()->flash('success', 'Assessment created successfully!');
    }

    public function deleteAssessment($assessmentId)
    {
        Assessment::findOrFail($assessmentId)->delete();
        $this->loadAssessments();
        session()->flash('success', 'Assessment deleted successfully!');
    }

    public function selectAssessmentType($type)
    {
        $this->assessmentType = $type;
    }

    public function render()
    {
        return view('livewire.course-management.course-builder.assessment-manager');
    }
}