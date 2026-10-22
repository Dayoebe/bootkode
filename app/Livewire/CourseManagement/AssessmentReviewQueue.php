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

#[Layout('layouts.dashboard', ['title' => 'Assessment reviews'])]
class AssessmentReviewQueue extends Component
{
    use WithPagination;

    #[Locked] public ?int $sessionId = null;
    public bool $showReview = false;
    public array $marks = [];
    public array $feedback = [];
    public string $message = '';

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
        $this->reset('marks', 'feedback');
        $this->resetValidation();
        $this->showReview = true;
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
        if (!$results['pending_review']) $this->showReview = false;
    }

    public function render()
    {
        $assessments = Assessment::query();
        if (!auth()->user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $assessments->where(fn ($query) => $query->whereHas('course', fn ($course) => $course->where('instructor_id', auth()->id()))
                ->orWhere(fn ($legacy) => $legacy->whereNull('course_id')->whereHas('lesson.section.course', fn ($course) => $course->where('instructor_id', auth()->id()))));
        }
        $ids = $assessments->pluck('id')->map(fn ($id) => (int) $id)->all();
        $entries = LearningSession::where('activity_type', 'assessment')->whereNotNull('ended_at')->where('user_id', '!=', auth()->id())
            ->where('metadata->results->pending_review', true)->whereIn('metadata->assessment_id', $ids)->with('user')->oldest('ended_at')->paginate(10);
        $titles = Assessment::whereIn('id', $entries->pluck('metadata.assessment_id'))->pluck('title', 'id');
        $review = $this->showReview && $this->sessionId ? $this->entry($this->sessionId) : null;
        $responses = $review ? StudentAnswer::where('user_id', $review->user_id)->where('assessment_id', $review->metadata['assessment_id'])->where('attempt_number', $review->metadata['attempt'])->get()->keyBy('question_id') : collect();
        return view('livewire.course-management.assessment-review-queue', compact('entries', 'titles', 'review', 'responses'));
    }
}
