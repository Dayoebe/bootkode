<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Assessment;
use App\Models\Lesson;
use Livewire\Component;
use Illuminate\Support\Str;

class AssessmentManager extends Component
{
    public $lessonId;
    public $lesson;
    public $assessments = [];
    public $selectedAssessment = null;
    public $activeView = 'list'; // list, create, edit, questions
    
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
    public $maxAttempts = null;
    public $weight = 1;

    protected $listeners = [
        'assessmentCreated' => 'loadAssessments',
        'assessmentUpdated' => 'loadAssessments',
        'assessmentDeleted' => 'loadAssessments',
        'questionsUpdated' => 'loadAssessments',
        'backToAssessmentList' => 'backToList'
    ];

    protected $rules = [
        'assessmentTitle' => 'required|string|max:255',
        'assessmentDescription' => 'nullable|string|max:1000',
        'assessmentType' => 'required|in:quiz,project,assignment,qna',
        'passPercentage' => 'required|integer|min:1|max:100',
        'timeLimit' => 'nullable|integer|min:1',
        'isMandatory' => 'boolean',
        'allowMultipleAttempts' => 'boolean',
        'showResultsImmediately' => 'boolean',
        'maxAttempts' => 'nullable|integer|min:1|max:10',
        'weight' => 'required|numeric|min:0.1|max:10',
    ];

    public function mount($lessonId)
    {
        $this->lessonId = $lessonId;
        $this->lesson = Lesson::findOrFail($lessonId);
        $this->loadAssessments();
    }

    public function loadAssessments()
    {
        $this->assessments = Assessment::where('lesson_id', $this->lessonId)
            ->with(['questions'])
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        $this->activeView = $this->showCreateForm ? 'create' : 'list';
        
        if (!$this->showCreateForm) {
            $this->resetForm();
        }
    }

    public function createAssessment()
    {
        $this->validate();

        $assessment = Assessment::create([
            'lesson_id' => $this->lessonId,
            'section_id' => $this->lesson->section_id,
            'course_id' => $this->lesson->section->course_id,
            'title' => $this->assessmentTitle,
            'slug' => Str::slug($this->assessmentTitle),
            'description' => $this->assessmentDescription,
            'type' => $this->assessmentType,
            'pass_percentage' => $this->passPercentage,
            'estimated_duration_minutes' => $this->timeLimit,
            'is_mandatory' => $this->isMandatory,
            'weight' => $this->weight,
            'max_score' => 100, // Default, will be calculated based on questions
            'order' => count($this->assessments) + 1,
        ]);

        $this->loadAssessments();
        $this->resetForm();
        $this->showCreateForm = false;
        $this->activeView = 'list';
        
        session()->flash('success', 'Assessment created successfully!');
        $this->dispatch('assessmentCreated', $assessment->id);
    }

    public function editAssessment($assessmentId)
    {
        $this->selectedAssessment = collect($this->assessments)
            ->firstWhere('id', $assessmentId);
        
        if ($this->selectedAssessment) {
            $this->activeView = 'edit';
            $this->fillFormFromAssessment();
        }
    }

    public function manageQuestions($assessmentId)
    {
        $this->selectedAssessment = collect($this->assessments)
            ->firstWhere('id', $assessmentId);
        
        if ($this->selectedAssessment) {
            $this->activeView = 'questions';
        }
    }

    // NEW: Method to determine if assessment type supports questions/criteria
    public function canManageQuestions($assessmentType)
    {
        return in_array($assessmentType, ['quiz', 'project', 'assignment', 'qna']);
    }

    // NEW: Get the appropriate question manager component based on assessment type
    public function getQuestionManagerType($assessmentType)
    {
        $managers = [
            'quiz' => 'question-manager',
            'project' => 'project-criteria-manager',
            'assignment' => 'assignment-criteria-manager',
            'qna' => 'qna-criteria-manager'
        ];

        return $managers[$assessmentType] ?? 'question-manager';
    }

    // NEW: Get the appropriate item type name for display
    public function getAssessmentItemType($assessmentType)
    {
        $itemTypes = [
            'quiz' => 'questions',
            'project' => 'criteria',
            'assignment' => 'questions',
            'qna' => 'topics'
        ];

        return $itemTypes[$assessmentType] ?? 'items';
    }

    public function deleteAssessment($assessmentId)
    {
        Assessment::findOrFail($assessmentId)->delete();
        $this->loadAssessments();
        session()->flash('success', 'Assessment deleted successfully!');
    }

    public function backToList()
    {
        $this->activeView = 'list';
        $this->selectedAssessment = null;
        $this->resetForm();
    }

    protected function fillFormFromAssessment()
    {
        if ($this->selectedAssessment) {
            $this->assessmentTitle = $this->selectedAssessment['title'];
            $this->assessmentDescription = $this->selectedAssessment['description'];
            $this->assessmentType = $this->selectedAssessment['type'];
            $this->passPercentage = $this->selectedAssessment['pass_percentage'];
            $this->timeLimit = $this->selectedAssessment['estimated_duration_minutes'];
            $this->isMandatory = $this->selectedAssessment['is_mandatory'];
            $this->weight = $this->selectedAssessment['weight'] ?? 1;
        }
    }

    public function updateAssessment()
    {
        $this->validate();

        if ($this->selectedAssessment) {
            $assessment = Assessment::findOrFail($this->selectedAssessment['id']);
            $assessment->update([
                'title' => $this->assessmentTitle,
                'description' => $this->assessmentDescription,
                'type' => $this->assessmentType,
                'pass_percentage' => $this->passPercentage,
                'estimated_duration_minutes' => $this->timeLimit,
                'is_mandatory' => $this->isMandatory,
                'weight' => $this->weight,
            ]);

            $this->loadAssessments();
            $this->backToList();
            
            session()->flash('success', 'Assessment updated successfully!');
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
        $this->maxAttempts = null;
        $this->weight = 1;
    }

    public function selectAssessmentType($type)
    {
        $this->assessmentType = $type;
    }

    public function duplicateAssessment($assessmentId)
    {
        $originalAssessment = Assessment::with('questions')->findOrFail($assessmentId);
        
        $duplicatedAssessment = $originalAssessment->replicate();
        $duplicatedAssessment->title = $originalAssessment->title . ' (Copy)';
        $duplicatedAssessment->order = count($this->assessments) + 1;
        $duplicatedAssessment->save();

        // Duplicate questions for any assessment type that has them
        if ($this->canManageQuestions($originalAssessment->type)) {
            foreach ($originalAssessment->questions as $question) {
                $duplicatedQuestion = $question->replicate();
                $duplicatedQuestion->assessment_id = $duplicatedAssessment->id;
                $duplicatedQuestion->save();
            }
        }

        $this->loadAssessments();
        session()->flash('success', 'Assessment duplicated successfully!');
    }

    public function reorderAssessments($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            Assessment::where('id', $id)->update(['order' => $index + 1]);
        }
        
        $this->loadAssessments();
    }

    public function render()
    {
        return view('livewire.course-management.course-builder.assessment-manager');
    }
}