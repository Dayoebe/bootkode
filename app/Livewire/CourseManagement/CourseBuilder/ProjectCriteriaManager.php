<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\Question;
use App\Services\AssessmentAuthoringService;
use App\Services\AssessmentSubmissionService;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ProjectCriteriaManager extends Component
{
    use \App\Livewire\Concerns\AuthorizesCourseAuthoring;

    #[Locked] public $assessmentId;
    #[Locked] public $assessment;
    #[Locked] public $criteria = [];
    #[Locked] public $editingCriteria = null;
    public $showCreateForm = false;
    public $criteriaTitle = '';
    public $criteriaDescription = '';
    public $criteriaType = 'deliverable';
    public $points = 10;
    public $isRequired = true;
    public $rubricLevels = [];
    public $fileTypes = [];
    public $maxFileSize = 10;
    public $minFiles = 1;
    public $maxFiles = 5;
    protected $listeners = ['criteriaCreated' => 'loadCriteria', 'criteriaUpdated' => 'loadCriteria', 'criteriaDeleted' => 'loadCriteria'];

    protected function rules(): array
    {
        $rules = ['criteriaTitle' => 'required|string|max:255', 'criteriaDescription' => 'required|string|max:1000',
            'criteriaType' => 'required|in:deliverable,rubric,presentation,documentation', 'points' => 'required|numeric|decimal:0,2|min:1|max:100',
            'isRequired' => 'boolean', 'maxFileSize' => 'required|integer|min:1|max:100',
            'minFiles' => 'required|integer|min:0|max:20', 'maxFiles' => 'required|integer|min:1|max:20|gte:minFiles',
            'fileTypes' => 'array|max:30', 'fileTypes.*' => ['required', 'string', \Illuminate\Validation\Rule::in(app(AssessmentSubmissionService::class)->extensions())]];
        if ($this->criteriaType === 'rubric') $rules = array_merge($rules, ['rubricLevels' => 'required|array|min:2|max:10',
            'rubricLevels.*' => 'array:name,description,points', 'rubricLevels.*.name' => 'required|string|max:100',
            'rubricLevels.*.description' => 'nullable|string|max:1000', 'rubricLevels.*.points' => 'required|numeric|decimal:0,2|min:0|lte:points']);
        return $rules;
    }

    protected function prepareForValidation($attributes)
    {
        if (is_array($attributes['fileTypes'] ?? null) && collect($attributes['fileTypes'])->every(fn ($value) => is_string($value))) {
            $attributes['fileTypes'] = $this->fileTypes = app(AssessmentSubmissionService::class)->normalizeExtensions($attributes['fileTypes']);
        }
        return $attributes;
    }

    public function mount($assessmentId): void
    {
        $this->assessmentId = $assessmentId;
        $this->authorizeAuthoringContext();
        $this->assessment = Assessment::findOrFail($assessmentId);
        abort_unless($this->assessment->type === 'project', 403);
        $this->resetForm(); $this->loadCriteria();
    }

    private function query()
    {
        return Question::where('assessment_id', $this->assessmentId)->where('question_type', 'project_criteria');
    }

    public function loadCriteria(): void { $this->criteria = $this->query()->orderBy('order')->orderBy('id')->get()->toArray(); }
    public function toggleCreateForm(): void { $this->showCreateForm = !$this->showCreateForm; if (!$this->showCreateForm) $this->resetForm(); }
    public function selectCriteriaType($type): void { abort_unless(in_array($type, ['deliverable', 'rubric', 'presentation', 'documentation'], true), 422); $this->criteriaType = $type; }
    public function addRubricLevel(): void { if (count($this->rubricLevels) < 10) $this->rubricLevels[] = ['name' => '', 'description' => '', 'points' => 0]; }
    public function removeRubricLevel(int $index): void { if (count($this->rubricLevels) > 2 && isset($this->rubricLevels[$index])) array_splice($this->rubricLevels, $index, 1); }
    public function addFileType(): void { if (count($this->fileTypes) < 30) $this->fileTypes[] = ''; }
    public function removeFileType(int $index): void { if (isset($this->fileTypes[$index])) array_splice($this->fileTypes, $index, 1); }

    private function payload(): array
    {
        $this->validate();
        return ['question_text' => $this->criteriaTitle, 'points' => $this->points, 'explanation' => $this->criteriaDescription, 'is_required' => $this->isRequired,
            'options' => ['criteria_type' => $this->criteriaType, 'file_types' => $this->fileTypes, 'max_file_size' => $this->maxFileSize,
                'min_files' => $this->minFiles, 'max_files' => $this->maxFiles, 'rubric_levels' => $this->criteriaType === 'rubric' ? $this->rubricLevels : []]];
    }

    public function createCriteria(): void
    {
        app(AssessmentAuthoringService::class)->saveCriteria((int) $this->assessmentId, 'project_criteria', $this->payload());
        $this->loadCriteria(); $this->resetForm(); $this->showCreateForm = false;
        session()->flash('success', 'Project criterion created.');
    }

    public function editCriteria(int $criteriaId): void
    {
        $item = $this->query()->findOrFail($criteriaId);
        $this->editingCriteria = $item->toArray();
        $data = app(\App\Services\StructuredQuestionService::class)->array($item->options);
        $this->criteriaTitle = $item->question_text; $this->criteriaDescription = $item->explanation ?? '';
        $this->points = $item->points; $this->isRequired = $item->is_required;
        $this->criteriaType = $data['criteria_type'] ?? 'deliverable'; $this->fileTypes = $data['file_types'] ?? [];
        $this->maxFileSize = $data['max_file_size'] ?? 10; $this->minFiles = $data['min_files'] ?? 1; $this->maxFiles = $data['max_files'] ?? 5;
        $this->rubricLevels = ($data['rubric_levels'] ?? []) ?: $this->defaultLevels();
        $this->resetValidation(); $this->showCreateForm = true;
    }

    public function updateCriteria(): void
    {
        abort_unless($this->editingCriteria, 422);
        app(AssessmentAuthoringService::class)->saveCriteria((int) $this->assessmentId, 'project_criteria', $this->payload(), $this->editingCriteria['id']);
        $this->loadCriteria(); $this->resetForm(); $this->showCreateForm = false;
        session()->flash('success', 'Project criterion updated. Existing attempts retain their original requirements.');
    }

    public function deleteCriteria(int $criteriaId): void { $this->query()->findOrFail($criteriaId)->delete(); $this->loadCriteria(); session()->flash('success', 'Project criterion deleted.'); }
    public function duplicateCriteria(int $criteriaId): void { $this->query()->findOrFail($criteriaId); app(AssessmentAuthoringService::class)->duplicateQuestion((int) $this->assessmentId, $criteriaId); $this->loadCriteria(); session()->flash('success', 'Project criterion copied.'); }
    public function reorderCriteria(array $ids): void { app(AssessmentAuthoringService::class)->reorderQuestions((int) $this->assessmentId, $ids); $this->loadCriteria(); }
    public function moveCriteria(int $id, int $direction): void
    {
        $this->query()->findOrFail($id);
        app(AssessmentAuthoringService::class)->moveQuestion((int) $this->assessmentId, $id, $direction); $this->loadCriteria();
        $this->dispatch('bootkode-list-moved', list: 'project-criteria-'.$this->assessmentId, item: $id, direction: $direction > 0 ? 'down' : 'up');
    }

    private function defaultLevels(): array
    {
        return [['name' => 'Excellent', 'description' => '', 'points' => 10], ['name' => 'Good', 'description' => '', 'points' => 8],
            ['name' => 'Satisfactory', 'description' => '', 'points' => 6], ['name' => 'Needs improvement', 'description' => '', 'points' => 4]];
    }

    protected function resetForm(): void
    {
        $this->reset('criteriaTitle', 'criteriaDescription', 'criteriaType', 'points', 'isRequired', 'fileTypes', 'maxFileSize', 'minFiles', 'maxFiles', 'editingCriteria');
        $this->rubricLevels = $this->defaultLevels(); $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.course-management.course-builder.project-criteria-manager', ['serverLimitKb' => app(AssessmentSubmissionService::class)->serverLimitKb(), 'supportedExtensions' => app(AssessmentSubmissionService::class)->extensions()]);
    }
}
