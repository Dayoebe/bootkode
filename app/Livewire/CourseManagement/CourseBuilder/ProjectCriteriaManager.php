<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Question;
use App\Models\Assessment;
use Livewire\Component;

class ProjectCriteriaManager extends Component
{
    public $assessmentId;
    public $assessment;
    public $criteria = [];
    public $showCreateForm = false;
    public $editingCriteria = null;
    
    // Criteria form fields
    public $criteriaTitle = '';
    public $criteriaDescription = '';
    public $criteriaType = 'deliverable';
    public $points = 10;
    public $isRequired = true;
    public $rubricLevels = [];
    
    // File requirements
    public $fileTypes = [];
    public $maxFileSize = 10; // MB
    public $minFiles = 1;
    public $maxFiles = 5;
    
    protected $listeners = [
        'criteriaCreated' => 'loadCriteria',
        'criteriaUpdated' => 'loadCriteria',
        'criteriaDeleted' => 'loadCriteria',
    ];

    protected function rules()
    {
        return [
            'criteriaTitle' => 'required|string|max:255',
            'criteriaDescription' => 'required|string|max:1000',
            'criteriaType' => 'required|in:deliverable,rubric,presentation,documentation',
            'points' => 'required|numeric|min:1|max:100',
            'isRequired' => 'boolean',
            'maxFileSize' => 'nullable|integer|min:1|max:100',
            'minFiles' => 'nullable|integer|min:0|max:20',
            'maxFiles' => 'nullable|integer|min:1|max:20',
        ];
    }

    public function mount($assessmentId)
    {
        $this->assessmentId = $assessmentId;
        $this->assessment = Assessment::findOrFail($assessmentId);
        $this->loadCriteria();
        
        // Initialize rubric levels
        $this->rubricLevels = [
            ['name' => 'Excellent', 'description' => '', 'points' => 10],
            ['name' => 'Good', 'description' => '', 'points' => 8],
            ['name' => 'Satisfactory', 'description' => '', 'points' => 6],
            ['name' => 'Needs Improvement', 'description' => '', 'points' => 4],
        ];
    }

    public function loadCriteria()
    {
        $this->criteria = Question::where('assessment_id', $this->assessmentId)
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

    public function selectCriteriaType($type)
    {
        $this->criteriaType = $type;
    }

    public function addRubricLevel()
    {
        $this->rubricLevels[] = [
            'name' => '',
            'description' => '',
            'points' => 0
        ];
    }

    public function removeRubricLevel($index)
    {
        if (count($this->rubricLevels) > 2) {
            array_splice($this->rubricLevels, $index, 1);
        }
    }

    public function addFileType()
    {
        $this->fileTypes[] = '';
    }

    public function removeFileType($index)
    {
        array_splice($this->fileTypes, $index, 1);
    }

    public function createCriteria()
    {
        $this->validate();

        $criteriaData = [
            'assessment_id' => $this->assessmentId,
            'question_text' => $this->criteriaTitle,
            'question_type' => 'project_criteria',
            'points' => $this->points,
            'explanation' => $this->criteriaDescription,
            'is_required' => $this->isRequired,
            'order' => count($this->criteria) + 1,
        ];

        // Store project-specific data in options
        $projectData = [
            'criteria_type' => $this->criteriaType,
            'file_types' => array_filter($this->fileTypes),
            'max_file_size' => $this->maxFileSize,
            'min_files' => $this->minFiles,
            'max_files' => $this->maxFiles,
            'rubric_levels' => $this->rubricLevels
        ];

        $criteriaData['options'] = json_encode($projectData);

        Question::create($criteriaData);

        $this->loadCriteria();
        $this->resetForm();
        $this->showCreateForm = false;
        
        session()->flash('success', 'Project criteria created successfully!');
    }

    public function editCriteria($criteriaId)
    {
        $criteria = collect($this->criteria)->firstWhere('id', $criteriaId);
        if ($criteria) {
            $this->editingCriteria = $criteria;
            $this->fillFormFromCriteria($criteria);
            $this->showCreateForm = true;
        }
    }

    protected function fillFormFromCriteria($criteria)
    {
        $this->criteriaTitle = $criteria['question_text'];
        $this->criteriaDescription = $criteria['explanation'] ?? '';
        $this->points = $criteria['points'];
        $this->isRequired = $criteria['is_required'] ?? true;

        $projectData = json_decode($criteria['options'], true) ?? [];
        $this->criteriaType = $projectData['criteria_type'] ?? 'deliverable';
        $this->fileTypes = $projectData['file_types'] ?? [];
        $this->maxFileSize = $projectData['max_file_size'] ?? 10;
        $this->minFiles = $projectData['min_files'] ?? 1;
        $this->maxFiles = $projectData['max_files'] ?? 5;
        $this->rubricLevels = $projectData['rubric_levels'] ?? $this->rubricLevels;
    }

    public function updateCriteria()
    {
        $this->validate();

        if ($this->editingCriteria) {
            $criteria = Question::findOrFail($this->editingCriteria['id']);
            
            $projectData = [
                'criteria_type' => $this->criteriaType,
                'file_types' => array_filter($this->fileTypes),
                'max_file_size' => $this->maxFileSize,
                'min_files' => $this->minFiles,
                'max_files' => $this->maxFiles,
                'rubric_levels' => $this->rubricLevels
            ];

            $criteria->update([
                'question_text' => $this->criteriaTitle,
                'explanation' => $this->criteriaDescription,
                'points' => $this->points,
                'is_required' => $this->isRequired,
                'options' => json_encode($projectData)
            ]);

            $this->loadCriteria();
            $this->resetForm();
            $this->showCreateForm = false;
            
            session()->flash('success', 'Project criteria updated successfully!');
        }
    }

    public function deleteCriteria($criteriaId)
    {
        Question::findOrFail($criteriaId)->delete();
        $this->loadCriteria();
        
        session()->flash('success', 'Project criteria deleted successfully!');
    }

    public function duplicateCriteria($criteriaId)
    {
        $originalCriteria = Question::findOrFail($criteriaId);
        $duplicatedCriteria = $originalCriteria->replicate();
        $duplicatedCriteria->question_text = $originalCriteria->question_text . ' (Copy)';
        $duplicatedCriteria->order = count($this->criteria) + 1;
        $duplicatedCriteria->save();

        $this->loadCriteria();
        session()->flash('success', 'Project criteria duplicated successfully!');
    }

    public function reorderCriteria($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            Question::where('id', $id)->update(['order' => $index + 1]);
        }
        
        $this->loadCriteria();
    }

    protected function resetForm()
    {
        $this->criteriaTitle = '';
        $this->criteriaDescription = '';
        $this->criteriaType = 'deliverable';
        $this->points = 10;
        $this->isRequired = true;
        $this->fileTypes = [];
        $this->maxFileSize = 10;
        $this->minFiles = 1;
        $this->maxFiles = 5;
        $this->rubricLevels = [
            ['name' => 'Excellent', 'description' => '', 'points' => 10],
            ['name' => 'Good', 'description' => '', 'points' => 8],
            ['name' => 'Satisfactory', 'description' => '', 'points' => 6],
            ['name' => 'Needs Improvement', 'description' => '', 'points' => 4],
        ];
        $this->editingCriteria = null;
    }

    public function render()
    {
        return view('livewire.course-management.course-builder.project-criteria-manager');
    }
}