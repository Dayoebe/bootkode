<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Assessment\Question;
use App\Models\Assessment\Assessment;
use Livewire\Component;

class AssignmentCriteriaManager extends Component
{
    use \App\Livewire\Concerns\AuthorizesCourseAuthoring;

    #[\Livewire\Attributes\Locked]
    public $assessmentId;
    public $assessment;
    #[\Livewire\Attributes\Locked]
    public $questions = [];
    public $showCreateForm = false;
    #[\Livewire\Attributes\Locked]
    public $editingQuestion = null;
    
    // Question form fields
    public $questionText = '';
    public $questionType = 'essay';
    public $points = 10;
    public $explanation = '';
    public $isRequired = true;
    public $wordLimit = null;
    public $timeLimit = null;
    
    // Assignment specific fields
    public $rubricCriteria = [];
    public $sampleAnswer = '';
    public $gradingNotes = '';
    public $allowFileUpload = false;
    public $fileTypes = [];
    public $maxFileSize = 10;
    
    protected $listeners = [
        'questionCreated' => 'loadQuestions',
        'questionUpdated' => 'loadQuestions',
        'questionDeleted' => 'loadQuestions',
    ];

    protected function rules()
    {
        return [
            'questionText' => 'required|string|max:1000',
            'questionType' => 'required|in:essay,short_answer,analysis,reflection,research',
            'points' => 'required|numeric|decimal:0,2|min:1|max:100',
            'explanation' => 'nullable|string|max:500',
            'isRequired' => 'boolean',
            'wordLimit' => 'nullable|integer|min:10|max:10000',
            'timeLimit' => 'nullable|integer|min:1|max:300',
            'sampleAnswer' => 'nullable|string|max:2000',
            'gradingNotes' => 'nullable|string|max:1000',
            'allowFileUpload' => 'boolean',
            'maxFileSize' => 'required|integer|min:1|max:100',
            'fileTypes' => 'array|max:30',
            'fileTypes.*' => ['required', 'string', \Illuminate\Validation\Rule::in(app(\App\Services\AssessmentSubmissionService::class)->extensions())],
            'rubricCriteria' => 'required|array|min:1|max:20',
            'rubricCriteria.*' => 'array:name,weight,description',
            'rubricCriteria.*.name' => 'required|string|max:100',
            'rubricCriteria.*.weight' => 'required|integer|min:1|max:100',
            'rubricCriteria.*.description' => 'nullable|string|max:1000',
        ];
    }

    public function mount($assessmentId)
    {
        $this->assessmentId = $assessmentId;
        $this->authorizeAuthoringContext();
        $this->assessment = Assessment::findOrFail($assessmentId);
        abort_unless($this->assessment->type === 'assignment', 403);
        $this->loadQuestions();
        
        // Initialize default rubric criteria
        $this->rubricCriteria = [
            ['name' => 'Content Knowledge', 'weight' => 40, 'description' => 'Understanding of key concepts'],
            ['name' => 'Critical Thinking', 'weight' => 30, 'description' => 'Analysis and evaluation of ideas'],
            ['name' => 'Communication', 'weight' => 20, 'description' => 'Clarity and organization of response'],
            ['name' => 'Evidence/Sources', 'weight' => 10, 'description' => 'Use of supporting materials'],
        ];
    }

    public function loadQuestions()
    {
        $this->questions = Question::where('assessment_id', $this->assessmentId)->where('question_type', 'assignment_question')
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

    public function selectQuestionType($type)
    {
        abort_unless(in_array($type, ['essay', 'short_answer', 'analysis', 'reflection', 'research'], true), 422);
        $this->questionType = $type;
        $this->adjustDefaultsForType();
    }

    public function updatedQuestionType($type): void { $this->selectQuestionType($type); }

    protected function adjustDefaultsForType()
    {
        switch ($this->questionType) {
            case 'essay':
                $this->wordLimit = $this->wordLimit ?? 500;
                $this->timeLimit = $this->timeLimit ?? 30;
                break;
            case 'short_answer':
                $this->wordLimit = $this->wordLimit ?? 150;
                $this->timeLimit = $this->timeLimit ?? 10;
                break;
            case 'research':
                $this->wordLimit = $this->wordLimit ?? 1000;
                $this->timeLimit = null; // Research assignments typically don't have time limits
                $this->allowFileUpload = true;
                break;
        }
    }

    public function addRubricCriteria()
    {
        $this->rubricCriteria[] = [
            'name' => '',
            'weight' => 10,
            'description' => ''
        ];
    }

    public function removeRubricCriteria($index)
    {
        if (count($this->rubricCriteria) > 1) {
            array_splice($this->rubricCriteria, $index, 1);
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

    public function createQuestion()
    {
        $this->validateRubric();

        $questionData = [
            'assessment_id' => $this->assessmentId,
            'question_text' => $this->questionText,
            'question_type' => 'assignment_question',
            'points' => $this->points,
            'explanation' => $this->explanation,
            'is_required' => $this->isRequired,
            'time_limit' => $this->timeLimit,
            'order' => count($this->questions) + 1,
        ];

        // Store assignment-specific data
        $assignmentData = [
            'assignment_type' => $this->questionType,
            'word_limit' => $this->wordLimit,
            'rubric_criteria' => $this->rubricCriteria,
            'sample_answer' => $this->sampleAnswer,
            'grading_notes' => $this->gradingNotes,
            'allow_file_upload' => $this->allowFileUpload,
            'file_types' => array_filter($this->fileTypes),
            'max_file_size' => $this->maxFileSize,
        ];

        $questionData['options'] = $assignmentData;

        app(\App\Services\AssessmentAuthoringService::class)->saveCriteria((int) $this->assessmentId, 'assignment_question', $questionData);

        $this->loadQuestions();
        $this->resetForm();
        $this->showCreateForm = false;
        
        session()->flash('success', 'Assignment question created successfully!');
    }

    public function editQuestion($questionId)
    {
        $question = Question::where('assessment_id', $this->assessmentId)->where('question_type', 'assignment_question')->findOrFail($questionId)->toArray();
        if ($question) {
            $this->editingQuestion = $question;
            $this->fillFormFromQuestion($question);
            $this->showCreateForm = true;
        }
    }

    protected function fillFormFromQuestion($question)
    {
        $this->questionText = $question['question_text'];
        $this->points = $question['points'];
        $this->explanation = $question['explanation'] ?? '';
        $this->isRequired = $question['is_required'] ?? true;
        $this->timeLimit = $question['time_limit'];

        $assignmentData = $this->normalizeToArray($question['options']);
        $this->questionType = $assignmentData['assignment_type'] ?? 'essay';
        $this->wordLimit = $assignmentData['word_limit'] ?? null;
        $this->rubricCriteria = $assignmentData['rubric_criteria'] ?? $this->rubricCriteria;
        $this->sampleAnswer = $assignmentData['sample_answer'] ?? '';
        $this->gradingNotes = $assignmentData['grading_notes'] ?? '';
        $this->allowFileUpload = $assignmentData['allow_file_upload'] ?? false;
        $this->fileTypes = $assignmentData['file_types'] ?? [];
        $this->maxFileSize = $assignmentData['max_file_size'] ?? 10;
    }

    protected function normalizeToArray($value, array $default = []): array
    {
        return app(\App\Services\StructuredQuestionService::class)->array($value);
    }

    protected function prepareForValidation($attributes)
    {
        if (is_array($attributes['fileTypes'] ?? null) && collect($attributes['fileTypes'])->every(fn ($value) => is_string($value))) {
            $attributes['fileTypes'] = $this->fileTypes = app(\App\Services\AssessmentSubmissionService::class)->normalizeExtensions($attributes['fileTypes']);
        }
        return $attributes;
    }

    private function validateRubric(): void
    {
        $this->validate();
        if (array_sum(array_column($this->rubricCriteria, 'weight')) !== 100) {
            throw \Illuminate\Validation\ValidationException::withMessages(['rubricCriteria' => 'Rubric weights must total 100%.']);
        }
    }

    public function updateQuestion()
    {
        $this->validateRubric();

        if ($this->editingQuestion) {
            $question = Question::where('assessment_id', $this->assessmentId)->where('question_type', 'assignment_question')->findOrFail($this->editingQuestion['id']);
            
            $assignmentData = [
                'assignment_type' => $this->questionType,
                'word_limit' => $this->wordLimit,
                'rubric_criteria' => $this->rubricCriteria,
                'sample_answer' => $this->sampleAnswer,
                'grading_notes' => $this->gradingNotes,
                'allow_file_upload' => $this->allowFileUpload,
                'file_types' => array_filter($this->fileTypes),
                'max_file_size' => $this->maxFileSize,
            ];

            app(\App\Services\AssessmentAuthoringService::class)->saveCriteria((int) $this->assessmentId, 'assignment_question', [
                'question_text' => $this->questionText,
                'points' => $this->points,
                'explanation' => $this->explanation,
                'is_required' => $this->isRequired,
                'time_limit' => $this->timeLimit,
                'options' => $assignmentData,
            ], $question->id);

            $this->loadQuestions();
            $this->resetForm();
            $this->showCreateForm = false;
            
            session()->flash('success', 'Assignment question updated successfully!');
        }
    }

    public function deleteQuestion($questionId)
    {
        Question::where('assessment_id', $this->assessmentId)->where('question_type', 'assignment_question')->findOrFail($questionId)->delete();
        $this->loadQuestions();
        
        session()->flash('success', 'Assignment question deleted successfully!');
    }

    public function duplicateQuestion($questionId)
    {
        $originalQuestion = Question::where('assessment_id', $this->assessmentId)->where('question_type', 'assignment_question')->findOrFail($questionId);
        app(\App\Services\AssessmentAuthoringService::class)->duplicateQuestion((int) $this->assessmentId, (int) $questionId);

        $this->loadQuestions();
        session()->flash('success', 'Assignment question duplicated successfully!');
    }

    public function reorderQuestions($orderedIds)
    {
        app(\App\Services\AssessmentAuthoringService::class)->reorderQuestions((int) $this->assessmentId, $orderedIds);

        $this->loadQuestions();
    }

    public function moveQuestion(int $id, int $direction): void
    {
        Question::where('assessment_id', $this->assessmentId)->where('question_type', 'assignment_question')->findOrFail($id);
        app(\App\Services\AssessmentAuthoringService::class)->moveQuestion((int) $this->assessmentId, $id, $direction);
        $this->loadQuestions();
        $this->dispatch('bootkode-list-moved', list: 'assignment-questions-'.$this->assessmentId, item: $id, direction: $direction > 0 ? 'down' : 'up');
    }

    protected function resetForm()
    {
        $this->questionText = '';
        $this->questionType = 'essay';
        $this->points = 10;
        $this->explanation = '';
        $this->isRequired = true;
        $this->wordLimit = null;
        $this->timeLimit = null;
        $this->rubricCriteria = [
            ['name' => 'Content Knowledge', 'weight' => 40, 'description' => 'Understanding of key concepts'],
            ['name' => 'Critical Thinking', 'weight' => 30, 'description' => 'Analysis and evaluation of ideas'],
            ['name' => 'Communication', 'weight' => 20, 'description' => 'Clarity and organization of response'],
            ['name' => 'Evidence/Sources', 'weight' => 10, 'description' => 'Use of supporting materials'],
        ];
        $this->sampleAnswer = '';
        $this->gradingNotes = '';
        $this->allowFileUpload = false;
        $this->fileTypes = [];
        $this->maxFileSize = 10;
        $this->editingQuestion = null;
    }

    public function render()
    {
        return view('livewire.course-management.course-builder.assignment-criteria-manager', ['serverLimitKb' => app(\App\Services\AssessmentSubmissionService::class)->serverLimitKb(), 'supportedExtensions' => app(\App\Services\AssessmentSubmissionService::class)->extensions()]);
    }
}
