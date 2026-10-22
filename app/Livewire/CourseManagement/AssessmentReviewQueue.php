<?php

namespace App\Livewire\CourseManagement;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Assessment\StudentAnswer;
use App\Services\AssessmentReviewService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[Layout('layouts.dashboard', ['title' => 'Assessment reviews'])]
class AssessmentReviewQueue extends Component
{
    use WithPagination;

    #[Locked] public ?int $sessionId = null;
    public bool $showReview = false;
    public array $marks = [];
    public array $feedback = [];
    public string $message = '';
    public string $status = 'pending';
    #[Locked] public ?int $correctionAnswerId = null;
    #[Locked] public string $reviewRevision = '';
    #[Locked] public string $operationId = '';
    public string $correctionPoints = '';
    public string $correctionFeedback = '';
    public string $correctionReason = '';
    public array $decisionReasons = [];

    public function updatedStatus(): void
    {
        $this->validate(['status' => 'required|in:pending,finalized,requested']);
        $this->resetPage();
    }

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'academy_admin', 'instructor']), 403);
        if ($this->sessionId) $this->entry($this->sessionId);
    }

    private function entry(int $id): LearningSession
    {
        $entry = LearningSession::where('activity_type', 'assessment')->whereNotNull('ended_at')->findOrFail($id);
        app(AssessmentReviewService::class)->authorize(auth()->user(), Assessment::findOrFail($entry->metadata['assessment_id'] ?? 0));
        abort_if((int) $entry->user_id === (int) auth()->id(), 403);
        return $entry;
    }

    public function review(int $id): void
    {
        $this->entry($id);
        $this->sessionId = $id;
        $this->reset('marks', 'feedback', 'correctionAnswerId', 'correctionPoints', 'correctionFeedback', 'correctionReason', 'decisionReasons', 'message');
        $this->resetValidation();
        $this->refreshRevision();
        $this->showReview = true;
    }

    private function refreshRevision(): void
    {
        $this->reviewRevision = app(AssessmentReviewService::class)->revision($this->entry($this->sessionId));
        $this->operationId = (string) Str::uuid();
    }

    public function beginCorrection(int $answerId): void
    {
        abort_unless($this->sessionId, 422);
        $entry = $this->entry($this->sessionId);
        abort_if($entry->metadata['results']['pending_review'] ?? true, 422, 'Complete the initial review first.');
        $answer = StudentAnswer::where('user_id', $entry->user_id)->where('assessment_id', $entry->metadata['assessment_id'])
            ->where('attempt_number', $entry->metadata['attempt'])->whereNotNull('graded_at')->findOrFail($answerId);
        abort_if(in_array($entry->metadata['grading'][$answer->question_id]['question_type'] ?? '', ['multiple_choice', 'true_false', ...\App\Services\StructuredQuestionService::TYPES], true), 422);
        $this->correctionAnswerId = $answer->id;
        $this->correctionPoints = (string) $answer->points_earned;
        $this->correctionFeedback = $answer->feedback ?? '';
        $this->correctionReason = '';
        $this->resetValidation();
        $this->refreshRevision();
        $this->dispatch('bootkode-assessment-focus', id: 'correction-points');
    }

    public function cancelCorrection(): void
    {
        if ($this->correctionAnswerId) $this->dispatch('bootkode-assessment-focus', id: 'correct-mark-'.$this->correctionAnswerId);
        $this->reset('correctionAnswerId', 'correctionPoints', 'correctionFeedback', 'correctionReason');
        $this->resetValidation();
    }

    public function publishCorrection(): void
    {
        abort_unless($this->sessionId && $this->correctionAnswerId, 422);
        $this->entry($this->sessionId);
        try {
            app(AssessmentReviewService::class)->correct(auth()->user(), $this->sessionId, $this->correctionAnswerId,
                $this->correctionPoints, $this->correctionFeedback, $this->correctionReason, $this->reviewRevision, $this->operationId);
        } catch (ValidationException $exception) {
            foreach ($exception->validator->errors()->messages() as $field => $messages) {
                $this->addError(['points' => 'correctionPoints', 'feedback' => 'correctionFeedback', 'reason' => 'correctionReason'][$field] ?? 'correctionReason', $messages[0]);
            }
            return;
        } catch (HttpException $exception) {
            if (!in_array($exception->getStatusCode(), [409, 422], true)) throw $exception;
            $this->addError('correctionReason', $exception->getMessage());
            return;
        }
        $this->cancelCorrection();
        $this->refreshRevision();
        $this->message = 'Correction published. The previous mark and result remain in history. Existing rewards, completion records and issued certificates are retained.';
    }

    public function upholdMark(string $requestId): void
    {
        abort_unless($this->sessionId, 422);
        $this->entry($this->sessionId);
        try {
            $decision = app(AssessmentReviewService::class)->upholdMark(auth()->user(), $this->sessionId, $requestId,
                $this->decisionReasons[$requestId] ?? '', $this->reviewRevision, $this->operationId);
        } catch (ValidationException $exception) {
            $this->addError('decisionReasons.'.$requestId, $exception->validator->errors()->first());
            return;
        } catch (HttpException $exception) {
            if (!in_array($exception->getStatusCode(), [409, 422], true)) throw $exception;
            $this->addError('decisionReasons.'.$requestId, $exception->getMessage());
            return;
        }
        unset($this->decisionReasons[$requestId]);
        $this->refreshRevision();
        $this->message = 'Review decision recorded. The learner can read the reason in their result history.';
        $this->dispatch('bootkode-assessment-focus', id: 'mark-section-'.$decision['answer_id']);
    }

    public function grade(int $answerId): void
    {
        abort_unless($this->sessionId, 422);
        $this->entry($this->sessionId);
        $this->validate(['marks.'.$answerId => 'required|numeric|min:0', 'feedback.'.$answerId => 'nullable|string|max:4000']);
        try {
            $results = app(AssessmentReviewService::class)->grade(auth()->user(), $this->sessionId, $answerId, $this->marks[$answerId], $this->feedback[$answerId] ?? '');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->addError('marks.'.$answerId, $exception->validator->errors()->first());
            return;
        }
        $this->message = $results['pending_review'] ? 'Mark saved. Other written answers still need review.' : 'Review complete. The final result and feedback are now available to the learner.';
        if (!$results['pending_review']) {
            $this->showReview = false;
            $this->dispatch('bootkode-assessment-focus', id: 'review-status');
        }
    }

    public function render()
    {
        abort_unless(in_array($this->status, ['pending', 'finalized', 'requested'], true), 422);
        $assessments = Assessment::query();
        if (!auth()->user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $assessments->where(fn ($query) => $query->whereHas('course', fn ($course) => $course->where('instructor_id', auth()->id()))
                ->orWhere(fn ($legacy) => $legacy->whereNull('course_id')->where(fn ($parent) => $parent
                    ->whereHas('lesson.section.course', fn ($course) => $course->where('instructor_id', auth()->id()))
                    ->orWhereHas('section.course', fn ($course) => $course->where('instructor_id', auth()->id())))));
        }
        $ids = $assessments->pluck('id')->map(fn ($id) => (int) $id)->all();
        $query = LearningSession::where('activity_type', 'assessment')->whereNotNull('ended_at')->where('user_id', '!=', auth()->id())
            ->whereIn('metadata->assessment_id', $ids)->with('user');
        if ($this->status === 'requested') $query->where('metadata->open_mark_reviews', '>', 0);
        else $query->where('metadata->results->pending_review', $this->status === 'pending');
        $entries = $query->orderBy('ended_at', $this->status === 'finalized' ? 'desc' : 'asc')->paginate(10);
        $titles = Assessment::whereIn('id', $entries->pluck('metadata.assessment_id'))->pluck('title', 'id');
        $review = $this->showReview && $this->sessionId ? $this->entry($this->sessionId) : null;
        $responses = $review ? StudentAnswer::where('user_id', $review->user_id)->where('assessment_id', $review->metadata['assessment_id'])->where('attempt_number', $review->metadata['attempt'])->get()->keyBy('question_id') : collect();
        return view('livewire.course-management.assessment-review-queue', compact('entries', 'titles', 'review', 'responses'));
    }
}
