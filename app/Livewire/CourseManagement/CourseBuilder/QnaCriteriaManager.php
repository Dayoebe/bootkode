<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Assessment\Question;
use App\Services\AssessmentAuthoringService;
use App\Services\CourseAuthoringAccess;
use App\Services\QnaResponseService;
use App\Services\StructuredQuestionService;
use Livewire\Attributes\Locked;
use Livewire\Component;

class QnaCriteriaManager extends Component
{
    use \App\Livewire\Concerns\AuthorizesCourseAuthoring;

    #[Locked] public int $assessmentId;
    #[Locked] public array $topics = [];
    #[Locked] public ?int $editingTopic = null;
    public bool $showCreateForm = false;
    public string $topicTitle = '';
    public string $topicDescription = '';
    public string $topicType = 'discussion';
    public $points = 5;
    public bool $isRequired = true;
    public $timeLimit = null;
    public $minResponses = 1;
    public $minResponseLength = 50;
    public bool $allowPeerReview = false;
    public bool $moderatorApproval = true;
    public array $discussionPrompts = [''];
    public array $evaluationCriteria = [['name' => 'Quality of response', 'weight' => 100, 'description' => 'Relevance, reasoning and supporting evidence']];

    protected function rules(): array
    {
        return [
            'topicTitle' => 'required|string|max:255', 'topicDescription' => 'required|string|max:2000',
            'topicType' => 'required|in:'.implode(',', QnaResponseService::TOPICS), 'points' => 'required|numeric|decimal:0,2|between:1,100',
            'isRequired' => 'boolean', 'timeLimit' => 'nullable|integer|between:1,10080',
            'minResponses' => 'required|integer|between:1,10', 'minResponseLength' => 'required|integer|between:10,1000',
            'allowPeerReview' => 'boolean', 'moderatorApproval' => 'boolean',
            'discussionPrompts' => 'array|max:10', 'discussionPrompts.*' => 'nullable|string|max:1000',
            'evaluationCriteria' => 'required|array|min:1|max:20', 'evaluationCriteria.*' => 'array:name,weight,description',
            'evaluationCriteria.*.name' => 'required|string|max:100', 'evaluationCriteria.*.weight' => 'required|integer|between:1,100',
            'evaluationCriteria.*.description' => 'nullable|string|max:1000',
        ];
    }

    public function mount(int $assessmentId): void
    {
        $this->assessmentId = $assessmentId;
        $this->loadTopics();
    }

    public function loadTopics(): void
    {
        $assessment = app(CourseAuthoringAccess::class)->assessment($this->assessmentId);
        abort_unless($assessment->type === 'qna', 403);
        $this->topics = $assessment->questions()->where('question_type', 'qna_topic')->orderBy('order')->orderBy('id')->get()->toArray();
    }

    public function toggleCreateForm(): void
    {
        $wasOpen = $this->showCreateForm;
        $this->resetForm();
        $this->showCreateForm = !$wasOpen;
    }

    public function addDiscussionPrompt(): void { if (count($this->discussionPrompts) < 10) $this->discussionPrompts[] = ''; }
    public function removeDiscussionPrompt(int $index): void { unset($this->discussionPrompts[$index]); $this->discussionPrompts = array_values($this->discussionPrompts); }
    public function addEvaluationCriteria(): void { if (count($this->evaluationCriteria) < 20) $this->evaluationCriteria[] = ['name' => '', 'weight' => 1, 'description' => '']; }
    public function removeEvaluationCriteria(int $index): void { if (count($this->evaluationCriteria) > 1) unset($this->evaluationCriteria[$index]); $this->evaluationCriteria = array_values($this->evaluationCriteria); }

    private function attributes(): array
    {
        $this->validate();
        if (array_sum(array_column($this->evaluationCriteria, 'weight')) !== 100) {
            throw \Illuminate\Validation\ValidationException::withMessages(['evaluationCriteria' => 'Evaluation weights must total 100%.']);
        }
        return ['question_text' => $this->topicTitle, 'explanation' => $this->topicDescription, 'points' => $this->points,
            'is_required' => $this->isRequired, 'time_limit' => $this->timeLimit,
            'options' => ['version' => 1, 'topic_type' => $this->topicType, 'min_responses' => (int) $this->minResponses,
                'min_response_length' => (int) $this->minResponseLength, 'allow_peer_review' => $this->allowPeerReview,
                'moderator_approval' => $this->moderatorApproval,
                'discussion_prompts' => array_values(array_filter(array_map('trim', $this->discussionPrompts), fn ($text) => $text !== '')),
                'evaluation_criteria' => $this->evaluationCriteria]];
    }

    public function createTopic(): void
    {
        app(AssessmentAuthoringService::class)->saveCriteria($this->assessmentId, 'qna_topic', $this->attributes());
        $this->saved('Q&A topic created.');
    }

    public function updateTopic(): void
    {
        abort_unless($this->editingTopic, 422);
        app(AssessmentAuthoringService::class)->saveCriteria($this->assessmentId, 'qna_topic', $this->attributes(), $this->editingTopic);
        $this->saved('Q&A topic updated. Existing attempts retain their original requirements.');
    }

    private function topic(int $id): Question
    {
        return app(CourseAuthoringAccess::class)->assessment($this->assessmentId)->questions()->where('question_type', 'qna_topic')->findOrFail($id);
    }

    public function editTopic(int $id): void
    {
        $topic = $this->topic($id);
        $data = app(StructuredQuestionService::class)->array($topic->options);
        $this->resetForm();
        $this->editingTopic = $topic->id;
        $this->topicTitle = $topic->question_text;
        $this->topicDescription = $topic->explanation ?? '';
        $this->points = $topic->points;
        $this->isRequired = (bool) $topic->is_required;
        $this->timeLimit = $topic->time_limit;
        foreach (['topicType' => 'topic_type', 'minResponses' => 'min_responses', 'minResponseLength' => 'min_response_length',
            'allowPeerReview' => 'allow_peer_review', 'moderatorApproval' => 'moderator_approval', 'discussionPrompts' => 'discussion_prompts', 'evaluationCriteria' => 'evaluation_criteria'] as $property => $key) {
            if (isset($data[$key])) $this->$property = $data[$key];
        }
        $this->showCreateForm = true;
    }

    public function deleteTopic(int $id): void { $this->topic($id)->delete(); $this->saved('Q&A topic deleted.'); }
    public function duplicateTopic(int $id): void { $this->topic($id); app(AssessmentAuthoringService::class)->duplicateQuestion($this->assessmentId, $id); $this->saved('Q&A topic copied.'); }
    public function reorderTopics(array $ids): void { app(AssessmentAuthoringService::class)->reorderQuestions($this->assessmentId, $ids); $this->loadTopics(); }
    public function moveTopic(int $id, int $direction): void
    {
        $this->topic($id); app(AssessmentAuthoringService::class)->moveQuestion($this->assessmentId, $id, $direction); $this->loadTopics();
        $this->dispatch('bootkode-list-moved', list: 'qna-topics-'.$this->assessmentId, item: $id, direction: $direction > 0 ? 'down' : 'up');
    }
    private function saved(string $message): void { $this->loadTopics(); $this->resetForm(); session()->flash('success', $message); }
    private function resetForm(): void
    {
        $this->resetValidation();
        $this->reset('editingTopic', 'showCreateForm', 'topicTitle', 'topicDescription', 'topicType', 'points', 'isRequired', 'timeLimit', 'minResponses', 'minResponseLength', 'allowPeerReview', 'moderatorApproval', 'discussionPrompts', 'evaluationCriteria');
    }
    public function render() { return view('livewire.course-management.course-builder.qna-criteria-manager'); }
}
