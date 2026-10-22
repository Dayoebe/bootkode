<?php

namespace App\Livewire\StudentManagement\CourseView;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Learning\Lesson;
use App\Services\CbtAttemptService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.dashboard', ['title' => 'Lesson assessments'])]
class StudentAssessmentTaker extends Component
{
    use \App\Livewire\Concerns\RequiresAuthenticatedAccount;
    use \App\Livewire\Concerns\RequestsAssessmentMarkReview;

    #[Locked] public Lesson $lesson;
    #[Locked] public bool $standalone = false;
    #[Locked] public ?int $assessmentId = null;
    #[Locked] public ?int $examSessionId = null;
    #[Locked] public array $questions = [];
    #[Locked] public ?array $results = null;
    #[Locked] public ?string $deadline = null;
    public array $answers = [];
    public bool $showSubmitModal = false;
    public string $message = '';

    public function boot(): void
    {
        if (isset($this->lesson)) app(CbtAttemptService::class)->authorizeLesson(auth()->user(), $this->lesson);
    }

    public function mount(Lesson $lesson, bool $standalone = false): void
    {
        $this->lesson = $lesson;
        $this->standalone = $standalone;
        app(CbtAttemptService::class)->authorizeLesson(auth()->user(), $lesson);
    }

    private function assessment(int $id): Assessment
    {
        $assessment = $this->lesson->assessments()->find($id);
        abort_unless($assessment, 404);
        return $assessment;
    }

    protected function markReviewContext(): array
    {
        return [$this->assessmentId ? $this->assessment($this->assessmentId) : null, $this->results['attempt_number'] ?? null];
    }

    public function startAssessment(int $id): void
    {
        $assessment = $this->assessment($id);
        $session = app(CbtAttemptService::class)->start(auth()->user(), $assessment, true);
        $this->assessmentId = $id;
        $this->examSessionId = $session->id;
        $this->questions = $session->metadata['questions'];
        $this->answers = array_replace(collect($this->questions)->mapWithKeys(fn ($question) => [$question['id'] => (in_array($question['question_type'], ['matching', 'ordering', 'drag_drop']) || $question['allows_multiple'] ? [] : null)])->all(), $session->metadata['answers'] ?? []);
        $this->deadline = $session->started_at->addSeconds($session->metadata['duration_seconds'])->toISOString();
        $this->results = null;
        $this->message = 'Attempt started. Saved answers survive a page reload; use Resume to continue.';
    }

    public function retakeAssessment(int $id): void { $this->startAssessment($id); }

    public function updatedAnswers($value, $key): void
    {
        abort_unless($this->assessmentId && $this->examSessionId && !$this->results, 422);
        $questionId = (int) explode('.', $key)[0];
        $value = $this->answers[$questionId] ?? null;
        // Unchecking the final option clears the saved answer.
        if ($value === []) $value = null;
        app(CbtAttemptService::class)->saveAnswer(auth()->user(), $this->assessment($this->assessmentId), $this->examSessionId, $questionId, $value);
        $this->message = 'Answer saved.';
    }

    public function moveOrderingItem(int $questionId, int $position, int $direction): void
    {
        abort_unless($this->assessmentId && $this->examSessionId && !$this->results && in_array($direction, [-1, 1], true), 422);
        $question = collect($this->questions)->firstWhere('id', $questionId);
        abort_unless($question && $question['question_type'] === 'ordering', 422);
        $order = !empty($this->answers[$questionId]) ? $this->answers[$questionId] : range(0, count($question['options']) - 1);
        app(\App\Services\StructuredQuestionService::class)->validate($question, $order);
        abort_unless(isset($order[$position]) && isset($order[$position + $direction]), 422);
        [$order[$position], $order[$position + $direction]] = [$order[$position + $direction], $order[$position]];
        $this->answers[$questionId] = $order;
        $this->saveOrdering($questionId);
        $this->dispatch('bootkode-list-moved', list: 'student-order-'.$questionId, item: $order[$position + $direction], direction: $direction > 0 ? 'down' : 'up');
    }

    public function saveOrdering(int $questionId): void
    {
        abort_unless($this->assessmentId && $this->examSessionId && !$this->results, 422);
        $question = collect($this->questions)->firstWhere('id', $questionId);
        abort_unless($question && $question['question_type'] === 'ordering', 422);
        $order = !empty($this->answers[$questionId]) ? $this->answers[$questionId] : range(0, count($question['options']) - 1);
        app(CbtAttemptService::class)->saveAnswer(auth()->user(), $this->assessment($this->assessmentId), $this->examSessionId, $questionId, $order);
        $this->answers[$questionId] = $order;
        $this->message = 'Sequence saved.';
    }

    public function submitAssessment(): void
    {
        abort_unless($this->assessmentId && $this->examSessionId, 422, 'Start an attempt first.');
        $answers = array_map(fn ($answer) => $answer === [] ? null : $answer, $this->answers);
        $this->results = app(CbtAttemptService::class)->submit(auth()->user(), $this->assessment($this->assessmentId), $this->examSessionId, $answers);
        $this->questions = [];
        $this->showSubmitModal = false;
        $this->message = $this->results['pending_review'] ? 'Submitted for instructor review. Your grade will appear here when all written answers are marked.' : 'Your result has been recorded.';
        $this->dispatch('assessment-completed');
    }

    public function showAssessmentResults(int $id): void
    {
        $this->reset('reviewReasons', 'reviewMessage');
        $assessment = $this->assessment($id);
        $result = $assessment->getStudentResults(auth()->id());
        abort_unless($result, 404);
        $this->assessmentId = $id;
        $this->results = \Illuminate\Support\Arr::except($result, ['answers', 'submitted_at']);
        $this->questions = [];
        $this->examSessionId = null;
    }

    public function backToAssessmentList(): void
    {
        $this->reset('assessmentId', 'examSessionId', 'questions', 'answers', 'results', 'deadline', 'showSubmitModal', 'reviewReasons', 'reviewMessage');
    }

    public function clearPreviousAttempts(int $id): void
    {
        $this->assessment($id);
        $this->message = 'Attempt history is retained. Additional attempts follow the assessment limit.';
    }

    public function render()
    {
        if ($this->assessmentId && $this->results) {
            $this->results = \Illuminate\Support\Arr::except($this->assessment($this->assessmentId)->getStudentResults(auth()->id(), $this->results['attempt_number']), ['answers', 'submitted_at']);
        }
        $assessments = $this->lesson->assessments()->orderBy('order')->get();
        $statuses = $assessments->mapWithKeys(fn ($assessment) => [$assessment->id => $assessment->getStudentResults(auth()->id())]);
        $activeIds = LearningSession::where('user_id', auth()->id())->where('activity_type', 'assessment')->whereNull('ended_at')->get()->pluck('metadata.assessment_id')->all();
        $responses = $this->assessmentId && $this->results ? app(\App\Services\AssessmentResultService::class)->responses($this->assessment($this->assessmentId), auth()->user(), $this->results['attempt_number']) : [];
        return view('livewire.student-management.course-view.student-assessment-taker', compact('assessments', 'statuses', 'activeIds', 'responses'));
    }
}
