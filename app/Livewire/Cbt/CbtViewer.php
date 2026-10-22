<?php

namespace App\Livewire\Cbt;

use App\Models\Assessment\Assessment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', ['title' => 'Assessment results'])]
class CbtViewer extends Component
{
    use WithPagination;
    use \App\Livewire\Concerns\RequiresAuthenticatedAccount;
    use \App\Livewire\Concerns\RequestsAssessmentMarkReview;

    #[Locked] public ?int $selectedAssessment = null;
    #[Locked] public ?array $selectedAttempt = null;
    public bool $viewDetails = false;

    public function boot(): void
    {
        if ($this->selectedAssessment) $this->ownedAssessment($this->selectedAssessment);
    }

    public function mount($assessmentId = null): void
    {
        if ($assessmentId) $this->viewAssessmentDetails((int) $assessmentId);
    }

    private function ownedAssessment(int $id): Assessment
    {
        $assessment = Assessment::whereHas('studentAnswers', fn ($answers) => $answers->where('user_id', auth()->id())->whereNotNull('submitted_at'))->find($id);
        abort_unless($assessment, 404);
        return $assessment;
    }

    protected function markReviewContext(): array
    {
        return [$this->selectedAssessment ? $this->ownedAssessment($this->selectedAssessment) : null, $this->selectedAttempt['attempt_number'] ?? null];
    }

    public function viewAssessmentDetails(int $assessmentId): void
    {
        $this->ownedAssessment($assessmentId);
        $this->selectedAssessment = $assessmentId;
        $this->selectedAttempt = null;
        $this->viewDetails = true;
    }

    public function viewAttemptDetails(int $attemptNumber): void
    {
        $this->reset('reviewReasons', 'reviewMessage');
        abort_unless($this->selectedAssessment, 422);
        $results = $this->ownedAssessment($this->selectedAssessment)->getStudentResults(auth()->id(), $attemptNumber);
        abort_unless($results, 404);
        $this->selectedAttempt = \Illuminate\Support\Arr::except($results, ['answers']);
    }

    public function closeDetails(): void
    {
        $this->reset('viewDetails', 'selectedAssessment', 'selectedAttempt', 'reviewReasons', 'reviewMessage');
    }

    public function getAttemptsForAssessment(Assessment $assessment)
    {
        $assessment = $this->ownedAssessment($assessment->id);
        return $assessment->studentAnswers()->where('user_id', auth()->id())->whereNotNull('submitted_at')->distinct()->orderByDesc('attempt_number')->pluck('attempt_number')
            ->map(fn ($number) => \Illuminate\Support\Arr::except($assessment->getStudentResults(auth()->id(), $number), ['answers']));
    }

    public function render()
    {
        $userAssessments = Assessment::whereHas('studentAnswers', fn ($answers) => $answers->where('user_id', auth()->id())->whereNotNull('submitted_at'))->latest()->paginate(10);
        $assessment = $this->selectedAssessment ? $this->ownedAssessment($this->selectedAssessment) : null;
        if ($assessment && $this->selectedAttempt) {
            $this->selectedAttempt = \Illuminate\Support\Arr::except($assessment->getStudentResults(auth()->id(), $this->selectedAttempt['attempt_number']), ['answers']);
        }
        $attempts = $assessment ? $this->getAttemptsForAssessment($assessment) : collect();
        $responses = $assessment && $this->selectedAttempt ? app(\App\Services\AssessmentResultService::class)->responses($assessment, auth()->user(), $this->selectedAttempt['attempt_number']) : [];
        return view('livewire.cbt.cbt-viewer', compact('userAssessments', 'assessment', 'attempts', 'responses'));
    }
}
