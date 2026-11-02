<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\Question;
use App\Services\StructuredQuestionService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;

class QuestionManager extends Component
{
    use \App\Livewire\Concerns\AuthorizesCourseAuthoring;

    #[Locked] public $assessmentId;
    #[Locked] public $assessment;
    #[Locked] public array $questions = [];
    #[Locked] public ?array $editingQuestion = null;
    public bool $showCreateForm = false;
    public string $questionText = '';
    public string $questionType = 'multiple_choice';
    public $points = 1;
    public string $explanation = '';
    public bool $isRequired = true;
    public $timeLimit = null;
    public array $options = ['', '', '', ''];
    public array $correctAnswers = [];
    public string $trueAnswerText = 'True';
    public string $falseAnswerText = 'False';
    public string $correctAnswer = '';
    public array $pairs = [['prompt' => '', 'choice' => ''], ['prompt' => '', 'choice' => '']];
    public array $items = ['', ''];

    protected $listeners = ['questionCreated' => 'loadQuestions', 'questionUpdated' => 'loadQuestions', 'questionDeleted' => 'loadQuestions'];

    public function mount($assessmentId): void
    {
        $this->assessmentId = $assessmentId;
        $this->authorizeAuthoringContext();
        $this->assessment = Assessment::findOrFail($assessmentId);
        $this->loadQuestions();
    }

    private function context(): Assessment
    {
        $this->authorizeAuthoringContext();
        $assessment = Assessment::findOrFail($this->assessmentId);
        abort_unless($assessment->type === 'quiz', 422, 'Use the assessment criteria editor for this assessment type.');
        return $assessment;
    }

    public function loadQuestions(): void
    {
        $this->questions = $this->context()->questions()->get()->toArray();
    }

    protected function rules(): array
    {
        $rules = ['questionText' => 'required|string|max:1000', 'questionType' => 'required|in:multiple_choice,true_false,short_answer,essay,fill_blank,matching,ordering,drag_drop',
            'points' => 'required|numeric|min:0.1|max:100', 'explanation' => 'nullable|string|max:4000', 'isRequired' => 'boolean', 'timeLimit' => 'nullable|integer|min:1|max:300'];
        if ($this->questionType === 'multiple_choice') {
            $rules += ['options' => 'required|array|min:2|max:12', 'options.*' => 'required|string|max:255', 'correctAnswers' => 'required|array|min:1|max:12', 'correctAnswers.*' => 'required|integer|distinct|min:0|max:'.max(0, count($this->options) - 1)];
        } elseif ($this->questionType === 'true_false') {
            $rules += ['correctAnswer' => 'required|in:true,false', 'trueAnswerText' => 'required|string|max:255', 'falseAnswerText' => 'required|string|max:255|different:trueAnswerText'];
        } elseif (in_array($this->questionType, ['short_answer', 'fill_blank'], true)) {
            $rules['correctAnswer'] = 'required|string|max:1000';
        } elseif (in_array($this->questionType, ['matching', 'drag_drop'], true)) {
            $rules += ['pairs' => 'required|array|min:2|max:12', 'pairs.*.prompt' => 'required|string|max:1000|distinct', 'pairs.*.choice' => 'required|string|max:1000|distinct'];
        } elseif ($this->questionType === 'ordering') {
            $rules += ['items' => 'required|array|min:2|max:12', 'items.*' => 'required|string|max:1000|distinct'];
        }
        return $rules;
    }

    private function data(): array
    {
        $this->validate();
        $data = ['question_text' => $this->questionText, 'question_type' => $this->questionType, 'points' => $this->points, 'explanation' => $this->explanation,
            'is_required' => $this->isRequired, 'time_limit' => $this->timeLimit, 'options' => null, 'correct_answers' => []];
        if ($this->questionType === 'multiple_choice') {
            abort_unless(array_is_list($this->options), 422, 'Options must be a complete list.');
            $data['options'] = array_values($this->options);
            $data['correct_answers'] = array_values(array_map('intval', $this->correctAnswers));
        } elseif ($this->questionType === 'true_false') {
            $data['options'] = [$this->trueAnswerText, $this->falseAnswerText];
            $data['correct_answers'] = [$this->correctAnswer === 'true' ? 0 : 1];
        } elseif (in_array($this->questionType, ['short_answer', 'fill_blank'], true)) {
            $data['correct_answers'] = [$this->correctAnswer];
        } elseif (in_array($this->questionType, ['matching', 'drag_drop'], true)) {
            $data['options'] = ['version' => 1, 'prompts' => array_values(array_column($this->pairs, 'prompt')), 'choices' => array_values(array_column($this->pairs, 'choice'))];
            $data['correct_answers'] = range(0, count($this->pairs) - 1);
        } elseif ($this->questionType === 'ordering') {
            $data['options'] = ['version' => 1, 'items' => array_values($this->items)];
            $data['correct_answers'] = range(0, count($this->items) - 1);
        }
        return $data;
    }

    public function createQuestion(): void
    {
        $assessment = $this->context(); $data = $this->data();
        DB::transaction(function () use ($assessment, $data) {
            Assessment::whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            $assessment->questions()->create($data + ['order' => (int) $assessment->questions()->max('order') + 1]);
        });
        $this->finish('Question created.');
    }

    public function editQuestion(int $id): void
    {
        $question = $this->context()->questions()->findOrFail($id);
        $this->resetForm();
        $this->editingQuestion = ['id' => $question->id];
        $this->questionText = $question->question_text; $this->questionType = $question->question_type;
        $this->points = $question->points; $this->explanation = $question->explanation ?? '';
        $this->isRequired = (bool) $question->is_required; $this->timeLimit = $question->time_limit;
        $codec = app(StructuredQuestionService::class);
        $options = $codec->array($question->options); $correct = $codec->array($question->correct_answers);
        if ($question->question_type === 'multiple_choice') { $this->options = $options; $this->correctAnswers = array_map('intval', $correct); }
        elseif ($question->question_type === 'true_false') { $this->trueAnswerText = $options[0] ?? 'True'; $this->falseAnswerText = $options[1] ?? 'False'; $this->correctAnswer = (int) ($correct[0] ?? 0) === 0 ? 'true' : 'false'; }
        elseif (in_array($question->question_type, ['short_answer', 'fill_blank'], true)) $this->correctAnswer = (string) ($correct[0] ?? '');
        elseif ($question->question_type === 'ordering') $this->items = array_map(fn ($index) => $options['items'][$index] ?? '', $correct) ?: ['', ''];
        elseif (in_array($question->question_type, ['matching', 'drag_drop'], true)) $this->pairs = collect($options['prompts'] ?? [])->map(fn ($prompt, $index) => ['prompt' => $prompt, 'choice' => $options['choices'][$correct[$index] ?? $index] ?? ''])->values()->all() ?: [['prompt' => '', 'choice' => ''], ['prompt' => '', 'choice' => '']];
        $this->showCreateForm = true;
    }

    public function updateQuestion(): void
    {
        $assessment = $this->context(); abort_unless($this->editingQuestion, 422);
        $data = $this->data();
        DB::transaction(function () use ($assessment, $data) {
            Assessment::whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            $assessment->questions()->findOrFail($this->editingQuestion['id'])->update($data);
        });
        $this->finish('Question updated. Existing attempts retain their marking snapshots.');
    }

    public function duplicateQuestion(int $id): void
    {
        $this->context();
        app(\App\Services\AssessmentAuthoringService::class)->duplicateQuestion((int) $this->assessmentId, $id);
        $this->loadQuestions(); session()->flash('success', 'Question duplicated.');
    }

    public function deleteQuestion(int $id): void
    {
        try { $this->context()->questions()->findOrFail($id)->delete(); }
        catch (\DomainException $exception) { session()->flash('error', $exception->getMessage()); return; }
        $this->loadQuestions(); session()->flash('success', 'Question deleted.');
    }

    public function reorderQuestions(array $ids): void
    {
        $this->context();
        app(\App\Services\AssessmentAuthoringService::class)->reorderQuestions((int) $this->assessmentId, $ids);
        $this->loadQuestions();
    }

    public function moveQuestion(int $id, int $direction): void
    {
        abort_unless(in_array($direction, [-1, 1], true), 422);
        $ids = $this->context()->questions()->pluck('id')->all(); $index = array_search($id, $ids, true);
        abort_unless($index !== false && isset($ids[$index + $direction]), 422);
        [$ids[$index], $ids[$index + $direction]] = [$ids[$index + $direction], $ids[$index]];
        $this->reorderQuestions($ids);
        $this->dispatch('bootkode-list-moved', list: 'author-questions-'.$this->assessmentId, item: $id, direction: $direction > 0 ? 'down' : 'up');
    }

    public function toggleCreateForm(): void { $this->resetForm(); $this->showCreateForm = !$this->showCreateForm; }
    public function selectQuestionType(string $type): void { $this->questionType = $type; $this->correctAnswers = []; $this->correctAnswer = ''; $this->resetValidation(); }
    public function addOption(): void { if (count($this->options) < 12) $this->options[] = ''; }
    public function removeOption(int $index): void
    {
        abort_unless(isset($this->options[$index]) && count($this->options) > 2, 422);
        array_splice($this->options, $index, 1);
        $this->correctAnswers = array_values(array_map(fn ($value) => (int) $value > $index ? (int) $value - 1 : (int) $value, array_filter($this->correctAnswers, fn ($value) => (int) $value !== $index)));
    }
    public function addPair(): void { if (count($this->pairs) < 12) $this->pairs[] = ['prompt' => '', 'choice' => '']; }
    public function removePair(int $index): void { abort_unless(isset($this->pairs[$index]) && count($this->pairs) > 2, 422); array_splice($this->pairs, $index, 1); }
    public function addItem(): void { if (count($this->items) < 12) $this->items[] = ''; }
    public function removeItem(int $index): void { abort_unless(isset($this->items[$index]) && count($this->items) > 2, 422); array_splice($this->items, $index, 1); }
    public function cancelEdit(): void { $this->resetForm(); $this->showCreateForm = false; }
    private function resetForm(): void { $this->reset('questionText', 'questionType', 'points', 'explanation', 'isRequired', 'timeLimit', 'options', 'correctAnswers', 'correctAnswer', 'trueAnswerText', 'falseAnswerText', 'pairs', 'items', 'editingQuestion'); $this->resetValidation(); }
    private function finish(string $message): void { $this->loadQuestions(); $this->cancelEdit(); session()->flash('success', $message); $this->dispatch('questionsUpdated'); }
    public function render() { return view('livewire.course-management.course-builder.question-manager'); }
}
